<?php
class M_RBS extends CI_Model{
	
	public function __construct() { 
		parent::__construct();
	}

	public function get_list ($search_by='all', $search_text='', $size=100, $start=0) {
		$this->db->start_cache();
		if ($search_by == 'all') {
			$terms = explode(' ', $search_text);
			foreach ($terms as $i=>$t) {
				if (trim($t)!='') {
					$this->db->like('row_filter', strtoupper($t));
				}
			}
		} else if ($search_by == 'bs_cod') {
			$this->db->like('bs_cod', strtoupper($search_text));	
		} else if ($search_by == 'bs_desc') {
			$this->db->like('bs_desc', strtoupper($search_text));
		} 

		$this->db->where('oc_numero', '00000000');
		$this->db->stop_cache();
		$total_count = $this->db->count_all_results('alm.oc_det');

		$this->db->order_by('bs_desc', 'asc');
		$this->db->limit($size, $start);

		$query = $this->db->get('alm.oc_det');
		$ret = array(
			'data'=>$query->result(),
			'total'=>$total_count
		);
		return $ret;
	}

	public function get_new_row () {
		$row = array(
			'oc_anio'=>'0000',
			'oc_numero'=>'00000000',
			'oc_det_cantidad'=>0,
			'oc_det_obs'=>'',
			'oc_det_saldo'=>0
		);
		return $row;
	}

	public function get_row ($id) {
		$this->db->where('oc_det_id', $id);
		$row = $this->db->get('alm.oc_det')->row();
		return $row;	
	}
	
	public function add ($data){
		$data['syslog'] = 'sys';

		$this->db->trans_begin();

		$this->db->insert('alm.oc_det', $data);
		$id = $this->db->insert_id();

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $id;
		}
	}
	
	public function update ($data) {
		$data['syslog'] = 'sys';
		
		$this->db->trans_begin();
		$this->db->where('oc_det_id', $data['oc_det_id']);
		$this->db->update('alm.oc_det', $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return true;
		}
	}

	public function get_sys2009_bien_list ($filter) {
		$rows = sys2009_bien_list($filter);
		return $rows;
	}
	
	public function delete ($id) {
		$this->db->trans_begin();
		$this->db->where('oc_det_id', $id);
		$this->db->delete('alm.oc_det');

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $id;
		}
	}
}
?>