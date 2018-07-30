<?php
class M_GEB extends CI_Model{
	
	public function __construct() { 
		parent::__construct();
	}

	public function get_list ($search_by='general', $search_text='', $size=100, $start=0) {
		$this->db->start_cache();
		switch ($search_by) {
			case 'all':
				$this->db->like('row_filter', strtoupper($search_text));
			break;
			case 'numero':
				$numero = str_pad(intval($search_text), 5, '0', STR_PAD_LEFT);
				$this->db->like('geb_numero', strtoupper($numero));	
			break;
			case 'nemonico':
				$this->db->like('nemo_cod', strtoupper($search_text));
				$this->db->or_like('nemo_desc', strtoupper($search_text));
			break;
			case 'area':
				$this->db->like('area_desc', strtoupper($search_text));
			break;
			case 'solicitante':
				$this->db->like('UPPER(geb_solicitante)', strtoupper($search_text));
			break;
			case 'estado':
				$this->db->like('UPPER(geb_estado)', strtoupper($search_text));
			break;
		}

		$this->db->stop_cache();
		$total_count = $this->db->count_all_results('alm.v_geb');

		$this->db->order_by('geb_numero', 'desc');
		$this->db->limit($size, $start);

		$rows = $this->db->get('alm.v_geb')->result();

		$this->db->flush_cache();
		
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count
		);
		return $ret;
	}

	public function get_new_row () {
		$row = array(
			'geb_anio'=>date('Y'),
			'geb_numero'=>'00000',
			'geb_fecha'=>date('d/m/Y'),
			'geb_solicitante'=>'',
			//'nemo_id'=>0,
			//'area_id'=>0,
			'geb_desc'=>''
		);
		return $row;
	}

	public function get_row ($id) {
		return $this->db->where('geb_id', $id)->get('alm.v_geb')->row();
	}

	public function get_sys2009_nemo_list ($nemo_anio, $filter) {
		$rows = sys2009_nemo_list($nemo_anio, $filter);
		return $rows;
	}

	public function get_sys2009_area_list () {
		$rows = sys2009_area_list();
		return $rows;
	}

	public function get_solicitante_list ($filter) {
		$this->db->select('geb_solicitante');
		$this->db->where("geb_solicitante ILIKE '%$filter%'")->group_by('geb_solicitante');
		$rows = $this->db->get('alm.v_geb')->result();
		return $rows;
	}

	public function add ($data) {
		$table = 'alm.geb';
		$row_id = $this->db->select("nextval(pg_get_serial_sequence('$table', 'geb_id')) AS value")->get()->row();
		$anio = date('Y');
		$row_num = $this->db->select("sys.f_numerador_next('{$anio}', '$table', '', '') AS value")->get()->row();
		
		$data['geb_id'] = $row_id->value;
		$data['geb_anio'] =  $anio;
		$data['geb_numero'] = STR_PAD($row_num->value, 5, '0', STR_PAD_LEFT);
		$data['geb_estado'] =  'GENERADO'; // generado
		$data['syslog'] = sys_session_syslog();

		$nemo = $this->db->where('nemo_anio', $data['nemo_anio'])->where('nemo_cod', $data['nemo_cod'])->get('pp.nemo')->row();
		if (is_null($nemo)) {
			$data['nemo_id'] = $this->nemo_add($data['nemo_anio'], $data['nemo_cod'], $data['nemo_desc'], $data['nemo_secfun'], $data['nemo_meta']);
		} else {
			$data['nemo_id'] = $nemo->nemo_id;
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

		$this->db->insert($table, $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['geb_id'];
		}
	}

	public function update ($data) {
		$geb = $this->get_row($data['geb_id']);
		$data['syslog'] = sys_session_syslog('modificar', $geb->syslog);

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
		$this->db->where('geb_id', $data['geb_id']);
		$this->db->update('alm.geb', $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['geb_id'];
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

	public function get_new_geb_det_row () {
		$row = array(
			'bs_cod'=>'',
			'bs_desc'=>'',
			'geb_det_unimed'=>'',
			'geb_det_cantidad'=>0,
			'geb_det_obs'=>'',
		);
		return $row;
	}

	public function get_geb_det_row ($id) {
		$row = $this->db->select(
			'gd.*, ocd.oc_anio, ocd.oc_numero'
		)->from(
			'alm.geb_det AS gd'
		)->join(
			'alm.oc_det AS ocd', 'ocd.oc_det_id = gd.oc_det_id', 'left'
		)->where(
			'gd.geb_det_id', $id
		)->order_by(
			'gd.bs_desc', 'ASC'
		)->get()->row();
		return $row;	
	}

	public function get_geb_det_list ($id) {
		$rows = $this->db->select(
			'gd.*, ocd.oc_anio, ocd.oc_numero'
		)->from(
			'alm.geb_det AS gd'
		)->join(
			'alm.oc_det AS ocd', 'ocd.oc_det_id = gd.oc_det_id', 'left'
		)->where(
			'gd.geb_id', $id
		)->order_by(
			'gd.bs_desc', 'ASC'
		)->get()->result();
		return $rows;
	}

	private function get_or_create_oc_det($anio, $numero, $bs_cod, $bs_desc, $bs_unimed, $cantidad, $obs) {
		$row = $this->db->where('oc_anio', $anio)->where('oc_numero', $numero)->where('bs_cod', $bs_cod)->where('oc_det_obs', $obs)->get('alm.oc_det')->row();
		if (is_null($row)) {
			$this->db->insert('alm.oc_det', array(
				'oc_anio'=>$anio,
				'oc_numero'=>$numero,
				'bs_cod'=>$bs_cod,
				'bs_desc'=>$bs_desc,
				'bs_unimed'=>$bs_unimed,
				'oc_det_cantidad'=>$cantidad,
				'oc_det_obs'=>$obs,
				'oc_det_saldo'=>$cantidad
			));
			return $this->db->where('oc_det_id', $this->db->insert_id())->get('alm.oc_det')->row();
		} else {
			return $row;
		}
	}

	private function oc_det_sync($oc_det_id, $anterior, $nuevo) {
		$row = $this->db->where('oc_det_id', $oc_det_id)->get('alm.oc_det')->row();
		$saldo = ($row->oc_det_saldo + floatval($anterior)) - floatval($nuevo);
		$this->db->where('oc_det_id', $oc_det_id)->update('alm.oc_det', array('oc_det_saldo'=>$saldo));
		return true;
	}
	
	public function import_geb_det_from_oc ($geb_id, $list) {
		$this->db->trans_begin();
		$icount = $ucount = 0;
		foreach ($list as $key => $value) {
			list($orden_anio, $orden_numero, $bs_cod, $obs) = explode('.', $value);
			$obs = trim(base64_decode($obs));
			$r = sys2009_det_oc_row ($orden_anio, $orden_numero, $bs_cod, $obs);
			if (is_null($r)) {
				die("$orden_anio, $orden_numero, $bs_cod, $obs");
			}
			$oc_det = $this->get_or_create_oc_det($orden_anio, $orden_numero, $bs_cod, $r['bs_desc'], $r['bs_unimed'], $r['cantidad'], $obs);
			$data = array(
				'geb_id'=>$geb_id,
				'oc_det_id'=>$oc_det->oc_det_id,
				'bs_cod'=>$r['bs_cod'],
				'bs_desc'=>$r['bs_desc'],
				'bs_unimed'=>$r['bs_unimed'],
				'geb_det_cantidad'=>$oc_det->oc_det_saldo,
				'geb_det_obs'=>$obs,
				'syslog'=>''
			);
			
			$geb_det = $this->db->where('geb_id', $geb_id)->where('oc_det_id', $oc_det->oc_det_id)->get('alm.geb_det')->row();
			if (is_null($geb_det)) {
				$data['syslog'] = sys_session_syslog();

				$this->db->insert('alm.geb_det', $data);
				// solo syncroniza si es nuevo
				$this->oc_det_sync($oc_det->oc_det_id, 0, $data['geb_det_cantidad']);
				$icount++;
			} else {
				unset($data['geb_det_cantidad']);
				unset($data['geb_det_obs']);

				$data['syslog'] = sys_session_syslog('modificar', $geb_det->syslog);

				$this->db->where('geb_det_id', $geb_det->geb_det_id)->update('alm.geb_det', $data);
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

	public function import_geb_det_from_saldo ($geb_id, $list) {
		$this->db->trans_begin();
		$icount = $ucount = 0;
		foreach ($list as $key => $value) {
			$oc_det = $this->db->where('oc_det_id', $value)->get('alm.oc_det')->row();
			$data = array(
				'geb_id'=>$geb_id,
				'oc_det_id'=>$oc_det->oc_det_id,
				'bs_cod'=>$oc_det->bs_cod,
				'bs_desc'=>$oc_det->bs_desc,
				'bs_unimed'=>$oc_det->bs_unimed,
				'geb_det_cantidad'=>$oc_det->oc_det_saldo,
				'geb_det_obs'=>$oc_det->oc_det_obs,
				'syslog'=>''
			);
			
			$geb_det = $this->db->where('geb_id', $geb_id)->where('oc_det_id', $oc_det->oc_det_id)->get('alm.geb_det')->row();
			if (is_null($geb_det)) {
				$data['syslog'] = sys_session_syslog();

				$this->db->insert('alm.geb_det', $data);
				// solo syncroniza si es nuevo
				$this->oc_det_sync($oc_det->oc_det_id, 0, $data['geb_det_cantidad']);
				$icount++;
			} else {
				unset($data['geb_det_cantidad']);
				unset($data['geb_det_obs']);

				$data['syslog'] = sys_session_syslog('modificar', $geb_det->syslog);

				$this->db->where('geb_det_id', $geb_det->geb_det_id)->update('alm.geb_det', $data);
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

	public function update_geb_det ($data) {
		$geb_det = $this->db->where('geb_det_id', $data['geb_det_id'])->get('alm.geb_det')->row();

		$data['syslog'] = sys_session_syslog('modificar', $geb_det->syslog);

		$this->db->trans_begin();

		$this->db->where('geb_det_id', $data['geb_det_id']);
		$this->db->update('alm.geb_det', $data);

		$this->oc_det_sync($geb_det->oc_det_id, $geb_det->geb_det_cantidad, $data['geb_det_cantidad']);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['geb_det_id'];
		}
	}

	public function delete_geb_det ($id) {
		$geb_det = $this->db->where('geb_det_id', $id)->get('alm.geb_det')->row();

		$this->db->trans_begin();

		$this->db->where('geb_det_id', $id);
		$this->db->delete('alm.geb_det');

		$this->oc_det_sync($geb_det->oc_det_id, $geb_det->geb_det_cantidad, 0);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $id;
		}
	}

	public function get_sys2009_det_oc_list ($geb_id, $orden_anio, $orden_numero, $filter) {
		//$gep = $this->db->where('geb_id', $geb_id)->get('alm.v_geb')->row(); // nemo_meta
		$rows = sys2009_det_oc_list($orden_anio, $orden_numero, $filter);
		return $rows;
	}

	public function get_sys2009_oc_list ($geb_id, $filter) {
		$geb = $this->db->where('geb_id', $geb_id)->get('alm.v_geb')->row(); // nemo_meta
		$ctarea = $geb->nemo_anio.$geb->nemo_meta;
		//echo "n: $orden_numero, t: $ctareas";
		$rows = sys2009_oc_list($ctarea, $filter);
		return $rows;
	}

	public function get_oc_det_saldo_list ($filter) {
		$filter = str_replace(" ", "%", $filter);
		$this->db->where("bs_cod||' '||bs_desc||' '||bs_unimed ILIKE '%{$filter}%'")->where('oc_numero','00000000')->order_by('bs_desc', 'asc');
		$rows = $this->db->get('alm.oc_det')->result();
		return $rows;
	}

	public function aprobar ($geb_id) {
		$geb = $this->get_row($geb_id);

		$this->db->trans_begin();

		$this->db->where('geb_id', $geb_id)->update('alm.geb', array(
			'geb_estado'=>'APROBADO',
			'syslog'=>sys_session_syslog('aprobar', $geb->syslog)
		));

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $geb_id;
		}
	}

	public function cancelar_aprobado ($geb_id) {
		$geb = $this->get_row($geb_id);

		$this->db->trans_begin();

		$this->db->where('geb_id', $geb_id)->update('alm.geb', array(
			'geb_estado'=>'GENERADO',
			'syslog'=>sys_session_syslog('cancelar_aprobado', $geb->syslog)
		));

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $geb_id;
		}
	}

	public function anular ($geb_id) {
		$geb = $this->get_row($geb_id);

		$this->db->trans_begin();

		$this->db->where('geb_id', $geb_id)->update('alm.geb', array(
			'geb_estado'=>'ANULADO',
			'syslog'=>sys_session_syslog('anular', $geb->syslog)
		));

		$geb_det_list = $this->get_geb_det_list($geb_id);
		foreach ($geb_det_list as $i => $r) {
			if (!is_null($r->oc_det_id)) {
				$this->oc_det_sync($r->oc_det_id, $r->geb_det_cantidad, 0);	
			}
		}

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $geb_id;
		}
	}

	public function activar ($geb_id) {
		$geb = $this->get_row($geb_id);

		$this->db->trans_begin();

		$this->db->where('geb_id', $geb_id)->update('alm.geb', array(
			'geb_estado'=>'GENERADO',
			'syslog'=>sys_session_syslog('activar', $geb->syslog)
		));

		$geb_det_list = $this->get_geb_det_list($geb_id);
		foreach ($geb_det_list as $i => $r) {
			if (!is_null($r->oc_det_id)) {
				$this->oc_det_sync($r->oc_det_id, 0, $r->geb_det_cantidad);	
			}
		}

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $geb_id;
		}
	}
}
?>