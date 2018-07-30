<?php
class M_COMPRADOR extends CI_Model{
	
	public function __construct() { 
		parent::__construct();
	}

	public function get_list ($search_by='general', $search_text='') {
		if ($search_by == 'all') {
			$this->db->like('row_filter', strtoupper($search_text));	
		} else if ($search_by == 'cod_comprador') {
			$this->db->like('cod_comprador', strtoupper($search_text));	
		} else if ($search_by == 'desc_comprador') {
			$this->db->like('desc_comprador', strtoupper($search_text));
		} else if ($search_by == 'estado_comprador') {
			$this->db->like('estado_comprador', strtoupper($search_text));	
		}
		
		$query = $this->db->get('log.v_comprador');
		return $query->result();	
	}

	public function get_new_row () {
		$row = array(
			'id_comprador'=>0,
			'cod_comprador'=>'',
			'desc_comprador'=>''
		);
		return $row;
	}

	public function get_row ($id) {
		$this->db->where('id_comprador', $id);
		$query = $this->db->get('log.v_comprador');
		$rows = $query->result();
		return $rows[0];	
	}

	public function valid_add($data) {
		$this->db->select("COUNT(*) AS value");
		$this->db->where('cod_comprador', $data['cod_comprador']);
		$result_count = $this->db->get('log.comprador')->result();

		if ($result_count[0]->value > 0) {
			return "El CODIGO del comprador ya esistes.";
		} 
		return true;
	}
	
	public function add ($data){
		$this->db->select("nextval(pg_get_serial_sequence('log.comprador', 'id_comprador')) AS value");
		$result_id = $this->db->get()->result();
		
		$data['id_comprador'] = $result_id[0]->value;
		$data['syslog'] = 'sys';

		$this->db->trans_begin();

		$this->db->insert('log.comprador',$data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['id_comprador'];
		}
	}
	
	public function valid_update ($data) {
		$this->db->select("COUNT(*) AS value");
		$this->db->where('cod_comprador', $data['cod_comprador']);
		$this->db->where("id_comprador <> {$data['id_comprador']}");
		$result_count = $this->db->get('log.comprador')->result();

		if ($result_count[0]->value > 0) {
			return "El CODIGO del comprador ya esiste.";
		} 
		return true;
	}
	
	public function update ($data) {
		$data['syslog'] = 'sys';
		
		$this->db->trans_begin();
		$this->db->where('id_comprador', $data['id_comprador']);
		$this->db->update('log.comprador', $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['id_comprador'];
		}
	}

	// unused
	public function get_new_comprador_cb_row () {
		$row = array(
			'id_comprador_cb'=>0,
			'nro_comprador_cb'=>'',
			'id_moneda'=>1,
			'id_banco'=>1
		);
		return $row;
	}

	public function get_comprador_cb_row ($id) {
		$this->db->where('id_comprador_cb', $id);
		//$this->db->like('row_filter', strtoupper($filter));
		$query = $this->db->get('log.v_comprador_cb');
		$rows = $query->result();
		return $rows[0];	
	}

	public function get_comprador_cb_list ($id_comprador) {
		$this->db->where('id_comprador', $id_comprador);
		$query = $this->db->get('log.v_comprador_cb');
		return $query->result();	
	}

	public function add_comprador_cb ($data) {
		$this->db->select("nextval(pg_get_serial_sequence('log.comprador_cb', 'id_comprador_cb')) AS value");
		$result_id = $this->db->get()->result();

		$data['id_comprador_cb'] = $result_id[0]->value;
		$data['syslog'] = 'sys';
		
		$this->db->trans_begin();
		$this->db->insert('log.comprador_cb', $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['id_comprador_cb'];
		}
	}

	public function update_comprador_cb ($data) {
		$data['syslog'] = 'sys';
		
		$this->db->trans_begin();
		$this->db->where('id_comprador_cb', $data['id_comprador_cb']);
		$this->db->update('log.comprador_cb', $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['id_comprador_cb'];
		}
	}

	public function delete_comprador_cb ($id) {
		$this->db->trans_begin();
		$this->db->where('id_comprador_cb', $id);
		$this->db->delete('log.comprador_cb');

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $id;
		}
	}

	public function get_banco_list () {
		$this->db->order_by('desc_banco', 'asc');
		$query = $this->db->get('log.banco');
		return $query->result();	
	}

	public function get_moneda_list () {
		$this->db->order_by('desc_moneda', 'asc');
		$query = $this->db->get('log.moneda');
		return $query->result();	
	}
}
?>