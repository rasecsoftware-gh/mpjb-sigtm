<?php
class M_Tdr extends CI_Model{
	
	public function __construct() { 
		parent::__construct();
	}

	public function get_list ($search_by='all', $search_text='', $tipo_doc_id, $size=100, $start=0) {
		$this->db->start_cache();
		
		$this->db->select("
			tdr.*,
			td.tipo_doc_desc,
			tp.tipo_permiso_desc
		")
		->from('public.tipo_doc_requisito AS tdr')
		->join('public.tipo_doc AS td', 'td.tipo_doc_id = tdr.tipo_doc_id', 'inner')
		->join('public.tipo_permiso AS tp', 'tp.tipo_permiso_id = tdr.tipo_permiso_id', 'left');

		if ( $tipo_doc_id != '') {
			$this->db->where('tdr.tipo_doc_id', $tipo_doc_id);	
		}
		
		switch ($search_by) {
			case 'all':
				$terms = explode(' ', $search_text);
				foreach ($terms as $i=>$t) {
					if (trim($t)!='') {
						$this->db->like(
							"UPPER(tdr.tipo_doc_requisito_desc||' '||
							tdr.tipo_doc_requisito_keyname)", 
							to_upper($t)
						);
					}
				}
			break;
			case 'keyname':
				$this->db->like('tdr.tipo_doc_requisito_keyname', $search_text);	
			break;
			case 'estado':
				$this->db->like('tdr.tipo_doc_requisito_estado', strtoupper($search_text));
			break;
		}

		$this->db->stop_cache();
		$total_count = $this->db->count_all_results();

		$this->db->order_by('tdr.tipo_doc_id', 'asc');
		$this->db->order_by('tdr.tipo_permiso_id', 'asc');
		$this->db->order_by('tdr.tipo_doc_requisito_index', 'asc');
		$this->db->order_by('tdr.tipo_doc_requisito_desc', 'asc');
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
			'tipo_doc_requisito_requerido_flag'=>'S', 
			'tipo_doc_requisito_pdf_flag'=>'S', 
			'tipo_doc_requisito_numero_flag'=>'S',
			'tipo_doc_requisito_index'=>1,
			'tipo_doc_requisito_estado'=>'A'
		);
		return $row;
	}

	public function get_row ($id) {
		$this->db
		->select("
			tdr.*, 
			td.tipo_doc_desc,
			tp.tipo_permiso_desc
		")
		->from("public.tipo_doc_requisito AS tdr")
		->join("public.tipo_doc AS td", "td.tipo_doc_id = tdr.tipo_doc_id", "inner")
		->join("public.tipo_permiso AS tp", "tp.tipo_permiso_id = tdr.tipo_permiso_id", "left")
		->where('tdr.tipo_doc_requisito_id', $id);
		return $this->db->get()->row();
	}

	public function add ($data) {
		$table = 'public.tipo_doc_requisito';
		
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
		$c = $this->get_row($data['tipo_doc_requisito_id']);
		$data['syslog'] = sys_session_syslog('modificar', $c->syslog);
		
		$this->db->trans_begin();
		$this->db->where('tipo_doc_requisito_id', $data['tipo_doc_requisito_id']);
		$this->db->update('public.tipo_doc_requisito', $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return true;
		}
	}	

	public function delete ($id) {
		$this->db->trans_begin();
		$this->db->where('tipo_doc_requisito_id', $id);
		$this->db->delete('public.tipo_doc_requisito');

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

	public function get_tipo_permiso_list () {
		$rows = $this->db->get('public.tipo_permiso')->result();
		$total_count = count($rows);
		
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count
		);
		return $ret;
	}

}
?>