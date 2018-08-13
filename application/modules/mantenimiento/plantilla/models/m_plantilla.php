<?php
class M_Plantilla extends CI_Model{
	
	public function __construct() { 
		parent::__construct();
	}

	public function get_list ($search_by='all', $search_text='', $tipo_doc_id, $size=100, $start=0) {
		$this->db->start_cache();
		
		$this->db->select("
			p.*,
			td.tipo_doc_desc
		")
		->from('public.plantilla AS p')
		->join('public.tipo_doc AS td', 'td.tipo_doc_id = p.tipo_doc_id', 'inner');

		if ( $tipo_doc_id != '') {
			$this->db->where('p.tipo_doc_id', $tipo_doc_id);	
		}
		
		switch ($search_by) {
			case 'all':
				$terms = explode(' ', $search_text);
				foreach ($terms as $i=>$t) {
					if (trim($t)!='') {
						$this->db->like(
							"UPPER(p.plantilla_desc)", 
							to_upper($t)
						);
					}
				}
			break;
			
			case 'estado':
				$this->db->like('p.plantilla_estado', strtoupper($search_text));
			break;
		}

		$this->db->stop_cache();
		$total_count = $this->db->count_all_results();

		$this->db->order_by('p.tipo_doc_id', 'asc');
		//$this->db->order_by('p.plantilla_index', 'asc');
		$this->db->order_by('p.plantilla_desc', 'asc');
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
			'tipo_doc_id'=>'CLIT',
			'plantilla_original_flag'=>'N',
			'plantilla_estado'=>'A'
		);
		return $row;
	}

	public function get_row ($id) {
		$this->db
		->select("
			p.*, 
			td.tipo_doc_desc
		")
		->from("public.plantilla AS p")
		->join("public.tipo_doc AS td", "td.tipo_doc_id = p.tipo_doc_id", "inner")
		->where('p.plantilla_id', $id);
		return $this->db->get()->row();
	}

	public function add ($data) {
		$table = 'public.plantilla';
		
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
		$c = $this->get_row($data['plantilla_id']);
		$data['syslog'] = sys_session_syslog('modificar', $c->syslog);
		
		$this->db->trans_begin();
		$this->db->where('plantilla_id', $data['plantilla_id']);
		$this->db->update('public.plantilla', $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['plantilla_id'];
		}
	}	

	public function delete ($id) {
		$this->db->trans_begin();
		$this->db->where('plantilla_id', $id);
		$this->db->delete('public.plantilla');

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return true;
		}
	}
	
	public function get_tipo_doc_list () {
		$rows = $this->db
		->order_by('tipo_doc_id', 'ASC')
		->get('public.tipo_doc')->result();
		$total_count = count($rows);
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count
		);
		return $ret;
	}

}
?>