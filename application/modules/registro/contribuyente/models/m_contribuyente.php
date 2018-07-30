<?php
class M_Contribuyente extends CI_Model{
	
	public function __construct() { 
		parent::__construct();
	}

	public function get_list ($search_by='all', $search_text='', $tipo_persona_id, $tipo_doc_identidad_id, $size=100, $start=0) {
		$this->db->start_cache();
		
		$this->db->select("
			c.*,
			tp.tipo_persona_desc,
			tdi.tipo_doc_identidad_desc
		")
		->from('public.contribuyente AS c')
		->join('public.tipo_persona AS tp', 'tp.tipo_persona_id = c.tipo_persona_id', 'inner')
		->join('public.tipo_doc_identidad AS tdi', 'tdi.tipo_doc_identidad_id = c.tipo_doc_identidad_id', 'inner');
		//->where_in('c.tipo_persona_id', $tc_in_list);

		switch ($search_by) {
			case 'all':
				$terms = explode(' ', $search_text);
				foreach ($terms as $i=>$t) {
					if (trim($t)!='') {
						$this->db->like(
							"UPPER(c.contribuyente_numero_doc||' '||
							c.contribuyente_nombres||' '||
							c.contribuyente_apellidos)", 
							to_upper($t)
						);
					}
				}
			break;
			case 'tp':
				$this->db->where('tp.tipo_persona_desc', $search_text);	
			break;
			case 'tdi':
				$this->db->where('tdi.tipo_doc_identidad_desc', $search_text);	
			break;
			case 'numero_doc':
				$this->db->like('c.contribuyente_numero_doc', $search_text);	
			break;
			case 'estado':
				$this->db->like('c.contribuyente_estado', strtoupper($search_text));
			break;
		}

		$this->db->stop_cache();
		$total_count = $this->db->count_all_results();


		$this->db->order_by('c.contribuyente_apellidos', 'asc');
		$this->db->order_by('c.contribuyente_nombres', 'asc');
		$this->db->order_by('c.contribuyente_id', 'asc');
		$this->db->limit($size, $start);

		$rows = $this->db->get()->result();

		$this->db->flush_cache();
		
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count
		);
		return $ret;
	}

	public function get_new_row () {
		$row = array(
			'tipo_persona_id'=>'N',
			'tipo_doc_identidad_id'=>1, // dni
			'ubigeo_id'=>'230301', // locumba
			'contribuyente_estado'=>'A'
		);
		return $row;
	}

	public function get_row ($id) {
		$this->db
		->select("
			c.*, 
			tp.tipo_persona_desc, 
			tdi.tipo_doc_identidad_desc
		")
		->from("public.contribuyente AS c")
		->join("public.tipo_persona AS tp", "tp.tipo_persona_id = c.tipo_persona_id", "inner")
		->join("public.tipo_doc_identidad AS tdi", "tdi.tipo_doc_identidad_id = c.tipo_doc_identidad_id", "inner")
		->where('contribuyente_id', $id);
		return $this->db->get()->row();
	}

	public function add ($data) {
		$table = 'public.contribuyente';
		
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
		$c = $this->get_row($data['contribuyente_id']);
		$data['syslog'] = sys_session_syslog('modificar', $c->syslog);
		
		$this->db->trans_begin();
		$this->db->where('contribuyente_id', $data['contribuyente_id']);
		$this->db->update('public.contribuyente', $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return true;
		}
	}	
	
	public function get_tipo_persona_list () {
		$rows = $this->db
		->order_by('tipo_persona_id', 'ASC')
		->get('public.tipo_persona')->result();
		$total_count = count($rows);
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count
		);
		return $ret;
	}

	public function get_tipo_doc_identidad_list () {
		$rows = $this->db->get('public.tipo_doc_identidad')->result();
		$total_count = count($rows);
		
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count
		);
		return $ret;
	}

	public function get_ubigeo_list ($filter) {
		$rows = $this->db
		->like('ubigeo_id', strtoupper($filter))
		->or_like('UPPER(ubigeo_distrito)', strtoupper($filter))
		->order_by('ubigeo_departamento', 'ASC')
		->order_by('ubigeo_provincia', 'ASC')
		->order_by('ubigeo_distrito', 'ASC')
		->get('public.ubigeo')->result();
		$total_count = count($rows);
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count
		);
		return $ret;
	}

	public function get_list_for_gen_pdf ($contribuyente_anio='2018', $tipo_contribuyente_id) {
		$rows = $this->db
		->select("c.contribuyente_id")
		->from('rh.contribuyente AS c')
		->where('c.contribuyente_anio', $contribuyente_anio)	
		->where('c.tipo_contribuyente_id', $tipo_contribuyente_id)
		->where('c.contribuyente_estado <>', 'ANULADO')
		->where('c.contribuyente_pdf', '')
		->order_by('c.contribuyente_anio', 'desc')
		->order_by('c.tipo_contribuyente_id', 'asc')
		->order_by('c.contribuyente_numero', 'desc')
		->order_by('c.contribuyente_id', 'desc')
		->get()->result();
		
		$ret = array(
			'data'=>$rows,
			'total'=>count($rows)
		);
		return $ret;
	}
	

}
?>