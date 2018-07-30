<?php
class M_Contrato extends CI_Model{
	
	public function __construct() { 
		parent::__construct();
	}

	public function get_anio () {
		$anio = $this->session->userdata('contrato_anio');
		if (is_null($anio)) {
			$anio = date('Y');
			$this->session->set_userdata('contrato_anio', $anio);
		}
		return $anio;
	}

	public function get_list ($search_by='all', $search_text='', $tipo_contrato_id, $size=100, $start=0) {
		if ($tipo_contrato_id == '') {
			$tc_in_list = array('01','02'); // todos
		} else {
			$tc_in_list = array($tipo_contrato_id);
		}
		$this->db->start_cache();
		
		$this->db->select("
			c.*,
			tc.tipo_contrato_desc, tc.tipo_contrato_abrev,
			
			r.repre_contrato_dni,
			r.repre_contrato_gradoacad||' '||r.repre_contrato_apenom AS repre_contrato_descripcion,
			r.repre_contrato_docref,
			r.repre_contrato_cargo,

			cp.contrato_fecha_emision AS p_contrato_fecha_emision,
			(tc.tipo_contrato_desc || ' ' || cp.contrato_numero || ' - ' || cp.contrato_anio) AS p_contrato_tipo_numero_anio,
			cp.contrato_cargo AS p_contrato_cargo,
			cp.contrato_fecha_inicio AS p_contrato_fecha_inicio,
			cp.contrato_fecha_fin AS p_contrato_fecha_fin,
			cp.contrato_dependencia AS p_contrato_dependencia
		")
		->from('rh.contrato AS c')
		->join('rh.tipo_contrato AS tc', 'tc.tipo_contrato_id = c.tipo_contrato_id', 'inner')
		->join('rh.repre_contrato AS r', 'r.repre_contrato_id = c.repre_contrato_id', 'inner')
		->join('rh.contrato AS cp', 'cp.contrato_id = c.contrato_id_parent', 'left')
		->where_in('c.tipo_contrato_id', $tc_in_list);

		switch ($search_by) {
			case 'all':
				$terms = explode(' ', $search_text);
				foreach ($terms as $i=>$t) {
					if (trim($t)!='') {
						$this->db->like(
							"c.contrato_anio||' '||c.contrato_numero||' '||
							tc.tipo_contrato_desc||' '||' '||tc.tipo_contrato_abrev||' '||
							c.contrato_traba_dni||' '||c.contrato_traba_apenom", 
							strtoupper($t)
						);
					}
				}
				/*$this->db->like(
					"c.contrato_anio||' '||c.contrato_numero||' '||
					tc.tipo_contrato_desc||' '||' '||tc.tipo_contrato_abrev||' '||
					c.contrato_traba_dni||' '||c.contrato_traba_apenom", 
					strtoupper($search_text)
				);*/
			break;
			case 'anio':
				$this->db->where('c.contrato_anio', $search_text);	
			break;
			case 'numero':
				$numero = str_pad(intval($search_text), 4, '0', STR_PAD_LEFT);
				$this->db->like('c.contrato_numero', strtoupper($numero));	
			break;
			/*case 'dependencia':
				$this->db->like("c.contrato_dependencia||' '||c.contrato_area", strtoupper($search_text));
				$this->db->or_like('nemo_desc', strtoupper($search_text));
			break;
			case 'trabajador':
				$this->db->like("c.contrato_traba_dni||' '||c.contrato_traba_apenom", strtoupper($search_text));
			break;
			case 'tipo_contrato':
				$this->db->like("tc.tipo_contrato_desc||' '||tc.tipo_contrato_abrev", strtoupper($search_text));
			break;*/
			case 'estado':
				$this->db->like('c.contrato_estado', strtoupper($search_text));
			break;
		}

		$this->db->stop_cache();
		$total_count = $this->db->count_all_results();


		$this->db->order_by('c.contrato_anio', 'desc');
		$this->db->order_by('c.tipo_contrato_id', 'asc');
		$this->db->order_by('c.contrato_numero', 'desc');
		$this->db->order_by('c.contrato_id', 'desc');
		$this->db->limit($size, $start);

		$rows = $this->db->get()->result();

		$this->db->flush_cache();
		
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count
		);
		return $ret;
	}

	public function get_new_row ($default_tipo_contrato_id='01') {
		/*if ($default_tipo_contrato_id == '01') {
			$default_contrato_anio = '2017';	
		} else {
			$default_contrato_anio = $this->get_anio();
		}*/
		$default_contrato_anio = $this->get_anio();

		$ultimo_numero_x_tipo_anio = $this->db
			->select('contrato_numero AS value')
			->where('tipo_contrato_id', $default_tipo_contrato_id)
			->where('contrato_anio', $default_contrato_anio)
			->order_by('contrato_numero', 'DESC')
			->get('rh.contrato')->row();
		
		if (is_null($ultimo_numero_x_tipo_anio)) {
			$default_contrato_numero = '0001';
		} else {
			$default_contrato_numero = str_pad(intval($ultimo_numero_x_tipo_anio->value)+1, 4, '0', STR_PAD_LEFT);
		}
		
		$row = array(
			'tipo_contrato_id'=>$default_tipo_contrato_id,
			'contrato_anio'=>$default_contrato_anio,
			'contrato_numero'=>$default_contrato_numero,

			'contrato_fecha_inicio'=>date('01/m/Y'),
			'contrato_fecha_fin'=>date('01/m/Y'),
			'contrato_fecha_emision'=>date('d/m/Y')
		);
		return $row;
	}

	public function get_row ($id) {
		$this->db
		->select("
			c.*, 
			tc.tipo_contrato_desc, tc.tipo_contrato_abrev,
			r.repre_contrato_dni, 
			r.repre_contrato_gradoacad||' '||r.repre_contrato_apenom AS repre_contrato_descripcion,
			r.repre_contrato_docref,
			r.repre_contrato_cargo")
		->from("rh.contrato AS c")
		->join("rh.tipo_contrato AS tc", "tc.tipo_contrato_id = c.tipo_contrato_id", "inner")
		->join("rh.repre_contrato AS r", "r.repre_contrato_id = c.repre_contrato_id", "inner")
		->where('contrato_id', $id);
		return $this->db->get()->row();
	}

	public function add ($data) {
		$table = 'rh.contrato';
		//$row_num = $this->db->select("sys.f_numerador_next('{$anio}', '$table', '', '') AS value")->get()->row();
		
		$data['syslog'] = sys_session_syslog();

		$this->db->trans_begin();

		$this->db->insert($table, $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $this->db->insert_id();
		}
	}

	public function update ($data) {
		$c = $this->get_row($data['contrato_id']);
		$data['syslog'] = sys_session_syslog('modificar', $c->syslog);
		
		$this->db->trans_begin();
		$this->db->where('contrato_id', $data['contrato_id']);
		$this->db->update('rh.contrato', $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return true;
		}
	}

	public function repre_contrato_add ($data) {
		$table = 'rh.repre_contrato';
		
		$data['syslog'] = sys_session_syslog();

		$this->db->trans_begin();

		$this->db->insert($table, $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $this->db->insert_id();
		}
	}

	public function repre_contrato_update ($data) {
		$table = 'rh.repre_contrato';
		//$c = $this->db->where('repre_contrato_id', $data['repre_contrato_id'])->get($table)->row();
		//$data['syslog'] = sys_session_syslog('modificar', $c->syslog);
		
		$this->db->trans_begin();
		$this->db->where('repre_contrato_id', $data['repre_contrato_id']);
		$this->db->update($table, $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return true;
		}
	}

	public function get_adenda_list ($contrato_id_parent) {
		$rows = $this->db->select("
			c.*,
			tc.tipo_contrato_desc, tc.tipo_contrato_abrev
		")
		->from('rh.contrato AS c')
		->join('rh.tipo_contrato AS tc', 'tc.tipo_contrato_id = c.tipo_contrato_id', 'inner')
		->where('c.contrato_id_parent', $contrato_id_parent)
		->order_by('c.contrato_id', 'ASC')
		->get()->result();
		
		$total_count = count($rows);
		
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count
		);
		return $ret;
	}
	
	public function get_tipo_adenda_list () {
		$rows = $this->db
		->order_by('tipo_adenda_id', 'ASC')
		->get('rh.tipo_adenda')->result();
		
		$total_count = count($rows);
		
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count
		);
		return $ret;
	}

	public function get_tipo_contrato_list () {
		$rows = $this->db->get('rh.tipo_contrato')->result();

		$total_count = count($rows);
		
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count
		);
		return $ret;
	}

	public function get_repre_contrato_list () {
		$rows = $this->db
		->select("
			*,
			repre_contrato_gradoacad||' '||repre_contrato_apenom AS repre_contrato_desc
		")->where('repre_contrato_estado', 'ACTIVO')->order_by('repre_contrato_apenom')->get('rh.repre_contrato')->result();

		$total_count = count($rows);
		
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count
		);
		return $ret;
	}

	public function get_repre_contrato_flist () {
		$rows = $this->db
		->select("
			*
		")->order_by('repre_contrato_apenom')->get('rh.repre_contrato')->result();

		$total_count = count($rows);
		
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count
		);
		return $ret;
	}

	public function get_tipo_contrato_parent_list () {
		$rows = array();
		$rows[] = (object) array('tipo_contrato_id'=>'', 'tipo_contrato_desc'=>'Todo', 'tipo_contrato_abrev'=>'', 'tipo_contrato_parent_id'=>NULL);

		$rows_db = $this->db->where('tipo_contrato_parent_id', null)->get('rh.tipo_contrato')->result();

		$rows = array_merge($rows, $rows_db);
		
		$total_count = count($rows);
		
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count
		);
		return $ret;
	}

	public function get_traba_list ($filter, $psize, $pstart) {
		$trows = sys2009_trabajador_c_list('all', $filter);

		$total_count = count($trows);
		$rows = array();

		foreach($trows as $i=>$r) {
			if ($pstart <= $i) {
				if ($i < ($pstart + $psize)) {
					$rows[] = $r;
				} else {
					break;
				}
			}
		}

		
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count
		);
		return $ret;
	}

	public function get_plantilla_list ($tipo_contrato_id, $plantilla_estado = '%') {
		$this->db->where('tipo_contrato_id', $tipo_contrato_id);
		if ($plantilla_estado != '%') {
			$this->db->like('plantilla_estado', $plantilla_estado);
		}
		$rows = $this->db->order_by('plantilla_id')->get('rh.plantilla')->result();
		$total_count = count($rows);
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count
		);
		return $ret;
	}

	public function get_plantilla_full_list () {
		$rows = $this->db
		->select('p.*, tc.tipo_contrato_abrev')
		->from('rh.plantilla AS p')
		->join('rh.tipo_contrato AS tc', 'tc.tipo_contrato_id = p.tipo_contrato_id')
		->order_by('tipo_contrato_id')
		->order_by('plantilla_id')
		->get()->result();
		$total_count = count($rows);
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count
		);
		return $ret;
	}

	public function plantilla_add ($data) {
		$table = 'rh.plantilla';
		
		//$data['syslog'] = sys_session_syslog();

		$this->db->trans_begin();

		$this->db->insert($table, $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $this->db->insert_id();
		}
	}

	public function plantilla_update ($data) {
		$table = 'rh.plantilla';
		//$c = $this->db->where('repre_contrato_id', $data['repre_contrato_id'])->get($table)->row();
		//$data['syslog'] = sys_session_syslog('modificar', $c->syslog);
		
		$this->db->trans_begin();
		$this->db->where('plantilla_id', $data['plantilla_id']);
		$this->db->update($table, $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['plantilla_id'];
		}
	}

	public function get_list_for_gen_pdf ($contrato_anio='2018', $tipo_contrato_id) {
		$rows = $this->db
		->select("c.contrato_id")
		->from('rh.contrato AS c')
		->where('c.contrato_anio', $contrato_anio)	
		->where('c.tipo_contrato_id', $tipo_contrato_id)
		->where('c.contrato_estado <>', 'ANULADO')
		->where('c.contrato_pdf', '')
		->order_by('c.contrato_anio', 'desc')
		->order_by('c.tipo_contrato_id', 'asc')
		->order_by('c.contrato_numero', 'desc')
		->order_by('c.contrato_id', 'desc')
		->get()->result();
		
		$ret = array(
			'data'=>$rows,
			'total'=>count($rows)
		);
		return $ret;
	}
	

}
?>