<?php
class M_NEA extends CI_Model{
	
	public function __construct() { 
		parent::__construct();
	}

	public function get_anio () {
		$anio = $this->session->userdata('nea_anio');
		if (is_null($anio)) {
			$anio = date('Y');
			$this->session->set_userdata('nea_anio', $anio);
		}
		return $anio;
	}

	public function get_list ($search_by='all', $search_text='', $size=100, $start=0) {
		$anio = $this->get_anio();
		$this->db->start_cache();
		$this->db->select("
			ne.*, 
			t.tipo_nea_desc, t.tipo_nea_abrev,
			n.nemo_anio, n.nemo_cod, n.nemo_desc, n.nemo_secfun, n.nemo_meta
		")->from(
			"alm.nea AS ne"
		)->join(
			"alm.tipo_nea AS t", "t.tipo_nea_id = ne.tipo_nea_id", "inner"
		)->join(
			"pp.nemo AS n", "n.nemo_id = ne.nemo_id", "left"
		)->where('nea_anio', $anio);
		switch ($search_by) {
			case 'all':
				$this->db->like("ne.nea_anio||' '||ne.nea_numero||' '||UPPER(ne.nea_observacion)", strtoupper($search_text));
			break;
			case 'numero':
				$numero = str_pad(intval($search_text), 5, '0', STR_PAD_LEFT);
				$this->db->like('ne.nea_numero', strtoupper($numero));	
			break;
			case 'nemonico':
				$this->db->like("n.nemo_cod||n.nemo_desc", strtoupper($search_text));
			break;
			case 'observacion':
				$this->db->like('UPPER(ne.nea_observacion)', strtoupper($search_text));
			break;
			case 'estado':
				$this->db->like('UPPER(ne.nea_estado)', strtoupper($search_text));
			break;
		}

		$this->db->stop_cache();
		$total_count = $this->db->count_all_results();

		$this->db->order_by('ne.nea_numero', 'desc');
		$this->db->limit($size, $start);

		$rows = $this->db->get()->result();

		$this->db->flush_cache();
		
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count,
			'anio'=>$anio
		);
		return $ret;
	}

	public function get_new_row () {
		$row = array(
			'nea_anio'=>$this->get_anio(),
			'nea_numero'=>'nuevo',
			'nea_fecha'=>date('d/m/Y'),
			'tipo_nea_id'=>'1',
			'nea_procedencia'=>'MP JORGE BASADRE',
			'nea_observacion'=>''
		);
		return $row;
	}

	public function get_row ($id) {
		$this->db->select("
			ne.*, 
			t.tipo_nea_desc, t.tipo_nea_abrev,
			n.nemo_anio, n.nemo_cod, n.nemo_desc, n.nemo_secfun, n.nemo_meta
		")->from(
			"alm.nea AS ne"
		)->join(
			"alm.tipo_nea AS t", "t.tipo_nea_id = ne.tipo_nea_id", "inner"
		)->join(
			"pp.nemo AS n", "n.nemo_id = ne.nemo_id", "left"
		)->where('nea_id', $id);
		return $this->db->get()->row();
	}

	public function get_sys2009_nemo_list ($nemo_anio, $filter) {
		$rows = sys2009_nemo_list($nemo_anio, $filter);
		return $rows;
	}

	public function get_sys2009_area_list () {
		$rows = sys2009_area_list();
		return $rows;
	}

	public function get_tipo_nea_list () {
		$rows = $this->db->order_by('tipo_nea_id', 'ASC')->get('alm.tipo_nea')->result();
		return $rows;
	}

	public function get_procedencia_list ($filter) {
		$this->db->select('nea_procedencia');
		$this->db->where("nea_procedencia ILIKE '%$filter%'")->group_by('nea_procedencia');
		$rows = $this->db->get('alm.nea')->result();
		return $rows;
	}

	public function add ($data) {
		$table = 'alm.nea';
		$anio = $this->get_anio();
		$row_num = $this->db->select("sys.f_numerador_next('{$anio}', '$table', '', '') AS value")->get()->row();
		
		//$data['nea_id'] = $row_id->value;  // autogenerate and get with insert_id()
		$data['nea_anio'] =  $anio;
		$data['nea_numero'] = STR_PAD($row_num->value, 5, '0', STR_PAD_LEFT);
		$data['nea_estado'] =  'GENERADO'; // generado
		$data['syslog'] = sys_session_syslog();

		// si no tiene centro de costo, sus campos son vacios ''
		if ($data['nemo_cod'] == '') {
			$data['nemo_id'] = null;
		} else {
			$nemo = $this->db->where('nemo_anio', $data['nemo_anio'])->where('nemo_cod', $data['nemo_cod'])->get('pp.nemo')->row();
			if (is_null($nemo)) {
				$data['nemo_id'] = $this->nemo_add($data['nemo_anio'], $data['nemo_cod'], $data['nemo_desc'], $data['nemo_secfun'], $data['nemo_meta']);
			} else {
				$data['nemo_id'] = $nemo->nemo_id;
			}
		}
		

		unset($data['nemo_anio']);
		unset($data['nemo_cod']);
		unset($data['nemo_desc']);
		unset($data['nemo_secfun']);
		unset($data['nemo_meta']);

		$this->db->trans_begin();

		$this->db->insert($table, $data);
		$data['nea_id'] = $this->db->insert_id(); // get serial generated on insert.

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['nea_id'];
		}
	}

	public function update ($data) {
		$nea = $this->get_row($data['nea_id']);
		$data['syslog'] = sys_session_syslog('modificar', $nea->syslog);

		$nemo = $this->db->where('nemo_anio', $data['nemo_anio'])->where('nemo_cod', $data['nemo_cod'])->get('pp.nemo')->row();
		if (is_null($nemo)) {
			$data['nemo_id'] = $this->nemo_add($data['nemo_anio'], $data['nemo_cod'], $data['nemo_desc'], $data['nemo_secfun'], $data['nemo_meta']);
		} else {
			$data['nemo_id'] = $nemo->nemo_id;
			$this->nemo_update($nemo->nemo_id, $data['nemo_anio'], $data['nemo_cod'], $data['nemo_desc'], $data['nemo_secfun'], $data['nemo_meta']);
		}

		$area = $this->db->where('area_cod', $data['area_cod'])->get('pp.area')->row();
		if (is_null($area)) {
			$data['area_id'] = $this->area_add($data['area_cod'], $data['area_desc']);
		} else {
			$data['area_id'] = $area->area_id;
		}

		unset($data['nemo_anio']);
		unset($data['nemo_cod']);
		unset($data['nemo_desc']);
		unset($data['nemo_secfun']);
		unset($data['nemo_meta']);

		unset($data['area_cod']);
		unset($data['area_desc']);
		
		$this->db->trans_begin();
		$this->db->where('nea_id', $data['nea_id']);
		$this->db->update('alm.nea', $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['nea_id'];
		}
	}

	public function change_nemo ($data) {
		$nea = $this->get_row($data['nea_id']);
		$data['syslog'] = sys_session_syslog('modificar', $nea->syslog);

		// si no tiene centro de costo, sus campos son vacios ''
		if ($data['nemo_cod'] == '') {
			$data['nemo_id'] = null;
		} else {
			$nemo = $this->db->where('nemo_anio', $data['nemo_anio'])->where('nemo_cod', $data['nemo_cod'])->get('pp.nemo')->row();
			if (is_null($nemo)) {
				$data['nemo_id'] = $this->nemo_add($data['nemo_anio'], $data['nemo_cod'], $data['nemo_desc'], $data['nemo_secfun'], $data['nemo_meta']);
			} else {
				$data['nemo_id'] = $nemo->nemo_id;
				$this->nemo_update($nemo->nemo_id, $data['nemo_anio'], $data['nemo_cod'], $data['nemo_desc'], $data['nemo_secfun'], $data['nemo_meta']);
			}
		}

		unset($data['nemo_anio']);
		unset($data['nemo_cod']);
		unset($data['nemo_desc']);
		unset($data['nemo_secfun']);
		unset($data['nemo_meta']);
		
		$this->db->trans_begin();
		$this->db->where('nea_id', $data['nea_id']);
		$this->db->update('alm.nea', $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return true;
		}
	}

	public function nemo_add($anio, $cod, $desc, $secfun, $meta) {
		$data = array(
			'nemo_anio'=>$anio,
			'nemo_cod'=>$cod,
			'nemo_desc'=>$desc,
			'nemo_secfun'=>$secfun,
			'nemo_meta'=>$meta,
			'nemo_estado'=>'A'
		);
		$this->db->insert('pp.nemo', $data);
		return $this->db->insert_id();
	}

	public function nemo_update($nemo_id, $anio, $cod, $desc, $secfun, $meta) {
		$data = array(
			'nemo_anio'=>$anio,
			'nemo_cod'=>$cod,
			'nemo_desc'=>$desc,
			'nemo_secfun'=>$secfun,
			'nemo_meta'=>$meta
		);
		$this->db->where('nemo_id', $nemo_id);
		$this->db->update('pp.nemo', $data);
		return true;
	}

	public function area_add($cod, $desc) {
		//$area_id = $this->db->select("nextval(pg_get_serial_sequence('pp.area', 'area_id')) AS value")->get()->row();
		$data = array(
			//'area_id'=>$area_id->value,
			'area_cod'=>$cod,
			'area_desc'=>$desc,
			'area_estado'=>'A'
		);
		$this->db->insert('pp.area', $data);
		return $this->db->insert_id();
	}

	public function get_new_nea_det_row () {
		$row = array(
			'bs_cod'=>'',
			'bs_desc'=>'',
			'nea_det_unimed'=>'',
			'nea_det_cantidad'=>0,
			'nea_det_obs'=>'',
		);
		return $row;
	}

	public function get_nea_det_row ($id) {
		$row = $this->db->select(
			'gd.*, ocd.oc_anio, ocd.oc_numero'
		)->from(
			'alm.nea_det AS gd'
		)->join(
			'alm.oc_det AS ocd', 'ocd.oc_det_id = gd.oc_det_id', 'left'
		)->where(
			'gd.nea_det_id', $id
		)->order_by(
			'gd.bs_desc', 'ASC'
		)->get()->row();
		return $row;	
	}

	public function get_nea_det_list ($id) {
		$rows = $this->db->select(
			'gd.*, ocd.oc_anio, ocd.oc_numero'
		)->from(
			'alm.nea_det AS gd'
		)->join(
			'alm.oc_det AS ocd', 'ocd.oc_det_id = gd.oc_det_id', 'left'
		)->where(
			'gd.nea_id', $id
		)->order_by(
			'ocd.oc_anio', 'ASC'
		)->order_by(
			'ocd.oc_numero', 'ASC'
		)->order_by(
			'gd.bs_desc', 'ASC'
		)->get()->result();
		return $rows;
	}

	private function get_or_create_oc_det($anio, $numero, $bs_cod, $bs_desc, $bs_unimed, $cantidad, $precio, $obs) {
		$row = $this->db->where('oc_anio', $anio)->where('oc_numero', $numero)->where('bs_cod', $bs_cod)->where('oc_det_obs', $obs)->get('alm.oc_det')->row();
		if (is_null($row)) {
			$this->db->insert('alm.oc_det', array(
				'oc_anio'=>$anio,
				'oc_numero'=>$numero,
				'bs_cod'=>$bs_cod,
				'bs_desc'=>$bs_desc,
				'bs_unimed'=>$bs_unimed,
				'oc_det_cantidad'=>$cantidad,
				'oc_det_precio'=>$precio,
				'oc_det_total'=>($cantidad*$precio),
				'oc_det_obs'=>$obs,
				'oc_det_saldo'=>$cantidad
			));
			return $this->db->where('oc_det_id', $this->db->insert_id())->get('alm.oc_det')->row();
		} else {
			$data = array(
				'oc_det_cantidad'=>$cantidad,
				'oc_det_precio'=>$precio,
				'oc_det_total'=>($cantidad*$precio),
				'oc_det_obs'=>$obs,
			);
			$this->db->where('oc_det_id', $row->oc_det_id);
			$this->db->update('alm.oc_det', $data);
			$row = $this->db->where('oc_det_id', $row->oc_det_id)->get('alm.oc_det')->row();
			return $row;
		}
	}

	private function oc_det_sync($oc_det_id, $anterior, $nuevo) {
		$row = $this->db->where('oc_det_id', $oc_det_id)->get('alm.oc_det')->row();
		$saldo = ($row->oc_det_saldo + floatval($anterior)) - floatval($nuevo);
		$this->db->where('oc_det_id', $oc_det_id)->update('alm.oc_det', array('oc_det_saldo'=>$saldo));
		return true;
	}
	
	public function import_nea_det_from_oc ($nea_id, $list) {
		$this->db->trans_begin();
		$icount = $ucount = 0;
		foreach ($list as $key => $value) {
			list($orden_anio, $orden_numero, $bs_cod, $obs) = explode('.', $value);
			$obs = trim(base64_decode($obs));
			$r = sys2009_det_oc_row ($orden_anio, $orden_numero, $bs_cod, $obs);
			if (is_null($r)) {
				die("$orden_anio, $orden_numero, $bs_cod, $obs");
			}
			$oc_det = $this->get_or_create_oc_det($orden_anio, $orden_numero, $bs_cod, $r['bs_desc'], $r['bs_unimed'], $r['cantidad'], $r['precio'], $obs);
			$data = array(
				'nea_id'=>$nea_id,
				'oc_det_id'=>$oc_det->oc_det_id,
				'bs_cod'=>$r['bs_cod'],
				'bs_desc'=>$r['bs_desc'],
				'bs_unimed'=>$r['bs_unimed'],
				'nea_det_cantidad'=>$oc_det->oc_det_cantidad,
				'nea_det_precio'=>$oc_det->oc_det_precio,
				'nea_det_total'=>$oc_det->oc_det_total,
				'nea_det_obs'=>$obs,
				'syslog'=>''
			);
			
			$nea_det = $this->db->where('nea_id', $nea_id)->where('oc_det_id', $oc_det->oc_det_id)->get('alm.nea_det')->row();
			if (is_null($nea_det)) {
				$data['syslog'] = sys_session_syslog();

				$this->db->insert('alm.nea_det', $data);

				$icount++;
			} else {
				//unset($data['nea_det_cantidad']);
				//unset($data['nea_det_obs']);

				$data['syslog'] = sys_session_syslog('modificar', $nea_det->syslog);

				$this->db->where('nea_det_id', $nea_det->nea_det_id)->update('alm.nea_det', $data);
				// si es Update, no se syncroniza, pote no se actualiza cantidad
				$ucount++;
			}
		}

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $icount;
		}
	}

	public function import_nea_det_from_cb ($nea_id, $list) {
		$this->db->trans_begin();
		$icount = $ucount = 0;
		foreach ($list as $key => $value) {
			$b = sys2009_bien_row($value);
			$data = array(
				'nea_id'=>$nea_id,
				'oc_det_id'=>NULL,
				'bs_cod'=>$b['bs_cod'],
				'bs_desc'=>$b['bs_desc'],
				'bs_unimed'=>$b['bs_unimed'],
				'nea_det_cantidad'=>0,
				'nea_det_obs'=>'',
				'syslog'=>''
			);
			
			$nea_det = $this->db->where('nea_id', $nea_id)->where('bs_cod', $b['bs_cod'])->where('oc_det_id IS NULL')->get('alm.nea_det')->row();
			if (is_null($nea_det)) {
				$data['syslog'] = sys_session_syslog();

				$this->db->insert('alm.nea_det', $data);
				$icount++;
			} else {
				$ucount++;
			}
		}

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $icount;
		}
	}

	public function update_nea_det ($data) {
		$nea_det = $this->db->where('nea_det_id', $data['nea_det_id'])->get('alm.nea_det')->row();

		$data['syslog'] = sys_session_syslog('modificar', $nea_det->syslog);

		$this->db->trans_begin();

		$this->db->where('nea_det_id', $data['nea_det_id']);
		$this->db->update('alm.nea_det', $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['nea_det_id'];
		}
	}

	public function delete_nea_det ($id) {
		//$nea_det = $this->db->where('nea_det_id', $id)->get('alm.nea_det')->row();

		$this->db->trans_begin();

		$this->db->where('nea_det_id', $id);
		$this->db->delete('alm.nea_det');

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $id;
		}
	}

	public function get_sys2009_det_oc_list ($nea_id, $filter) {
		$nea = $this->get_row($nea_id);
		$ctarea = ($nea->nemo_anio=='')?'- no tiene meta -':($nea->nemo_anio.$nea->nemo_meta);

		$rows = sys2009_det_oc_by_meta_list($ctarea, $filter);
		return $rows;
	}

	public function get_sys2009_bien_list ($filter) {
		$rows = sys2009_bien_list($filter);
		return $rows;
	}

	public function aprobar ($nea_id) {
		$nea = $this->get_row($nea_id);

		$this->db->trans_begin();

		$this->db->where('nea_id', $nea_id)->update('alm.nea', array(
			'nea_estado'=>'APROBADO',
			'syslog'=>sys_session_syslog('aprobar', $nea->syslog)
		));

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $nea_id;
		}
	}

	public function cancelar_aprobado ($nea_id) {
		$nea = $this->get_row($nea_id);

		$this->db->trans_begin();

		$this->db->where('nea_id', $nea_id)->update('alm.nea', array(
			'nea_estado'=>'GENERADO',
			'syslog'=>sys_session_syslog('cancelar_aprobado', $nea->syslog)
		));

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $nea_id;
		}
	}

	public function anular ($nea_id) {
		$nea = $this->get_row($nea_id);

		$this->db->trans_begin();

		$this->db->where('nea_id', $nea_id)->update('alm.nea', array(
			'nea_estado'=>'ANULADO',
			'syslog'=>sys_session_syslog('anular', $nea->syslog)
		));

		$nea_det_list = $this->get_nea_det_list($nea_id);
		foreach ($nea_det_list as $i => $r) {
			if (!is_null($r->oc_det_id)) {
				$this->oc_det_sync($r->oc_det_id, $r->nea_det_cantidad, 0);	
			}
		}

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $nea_id;
		}
	}

	public function cancelar_anulado ($nea_id) {
		$nea = $this->get_row($nea_id);

		$this->db->trans_begin();

		$this->db->where('nea_id', $nea_id)->update('alm.nea', array(
			'nea_estado'=>'GENERADO',
			'syslog'=>sys_session_syslog('activar', $nea->syslog)
		));

		$nea_det_list = $this->get_nea_det_list($nea_id);
		foreach ($nea_det_list as $i => $r) {
			if (!is_null($r->oc_det_id)) {
				$this->oc_det_sync($r->oc_det_id, 0, $r->nea_det_cantidad);	
			}
		}

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $nea_id;
		}
	}
}
?>