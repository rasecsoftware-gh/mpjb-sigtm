<?php
class M_PROVEEDOR extends CI_Model{
	
	public function __construct() { 
		parent::__construct();
	}

	public function get_list ($search_by='general', $search_text='', $size=30, $start=0) {
		if ($search_by == 'all') {
			$this->db->like('row_filter', strtoupper($search_text));	
		} else if ($search_by == 'ruc_proveedor') {
			$this->db->like('ruc_proveedor', strtoupper($search_text));	
		} else if ($search_by == 'desc_proveedor') {
			$this->db->like('desc_proveedor', strtoupper($search_text));
		} else if ($search_by == 'repleg_proveedor') {
			$this->db->like('repleg_proveedor', strtoupper($search_text));
		} else if ($search_by == 'estado_proveedor') {
			$this->db->like('estado_proveedor', strtoupper($search_text));	
		}
		$total_count = $this->db->count_all_results('log.v_proveedor');

		$this->db->limit($size, $start);

		$query = $this->db->get('log.v_proveedor');
		$ret = array(
			'data'=>$query->result(),
			'total'=>$total_count
		);
		return $ret;
	}

	public function get_new_row () {
		$row = array(
			'id_proveedor'=>0,
			'ruc_proveedor'=>'',
			'desc_proveedor'=>'',
			'repleg_proveedor'=>'',
			'telefono_proveedor'=>'',
			'correo_proveedor'=>''
		);
		return $row;
	}

	public function get_row ($id) {
		$this->db->where('id_proveedor', $id);
		$query = $this->db->get('log.v_proveedor');
		$rows = $query->result();
		return $rows[0];	
	}

	public function valid_add($data) {
		$this->db->select("COUNT(*) AS value");
		$this->db->where('ruc_proveedor', $data['ruc_proveedor']);
		$result_count = $this->db->get('log.proveedor')->result();

		if ($result_count[0]->value > 0) {
			return "El RUC del proveedor ya esiste.";
		} 
		return true;
	}
	
	public function add ($data){
		$this->db->select("nextval(pg_get_serial_sequence('log.proveedor', 'id_proveedor')) AS value");
		$result_id = $this->db->get()->result();
		
		$data['id_proveedor'] = $result_id[0]->value;
		$data['syslog'] = 'sys';

		$this->db->trans_begin();

		$this->db->insert('log.proveedor',$data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['id_proveedor'];
		}
	}
	
	public function valid_update ($data) {
		$this->db->select("COUNT(*) AS value");
		$this->db->where('ruc_proveedor', $data['ruc_proveedor']);
		$this->db->where("id_proveedor <> {$data['id_proveedor']}");
		$result_count = $this->db->get('log.proveedor')->result();

		if ($result_count[0]->value > 0) {
			return "El RUC del proveedor ya esiste.";
		} 
		return true;
	}
	
	public function update ($data) {
		$data['syslog'] = 'sys';
		
		$this->db->trans_begin();
		$this->db->where('id_proveedor', $data['id_proveedor']);
		$this->db->update('log.proveedor', $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['id_proveedor'];
		}
	}

	// unused
	public function get_new_proveedor_cb_row () {
		$row = array(
			'id_proveedor_cb'=>0,
			'nro_proveedor_cb'=>'',
			'id_moneda'=>1,
			'id_banco'=>1
		);
		return $row;
	}

	public function get_proveedor_cb_row ($id) {
		$this->db->where('id_proveedor_cb', $id);
		//$this->db->like('row_filter', strtoupper($filter));
		$query = $this->db->get('log.v_proveedor_cb');
		$rows = $query->result();
		return $rows[0];	
	}

	public function get_proveedor_cb_list ($id_proveedor) {
		$this->db->where('id_proveedor', $id_proveedor);
		$query = $this->db->get('log.v_proveedor_cb');
		return $query->result();	
	}

	public function add_proveedor_cb ($data) {
		$this->db->select("nextval(pg_get_serial_sequence('log.proveedor_cb', 'id_proveedor_cb')) AS value");
		$result_id = $this->db->get()->result();

		$data['id_proveedor_cb'] = $result_id[0]->value;
		$data['syslog'] = 'sys';
		
		$this->db->trans_begin();
		$this->db->insert('log.proveedor_cb', $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['id_proveedor_cb'];
		}
	}

	public function update_proveedor_cb ($data) {
		$data['syslog'] = 'sys';
		
		$this->db->trans_begin();
		$this->db->where('id_proveedor_cb', $data['id_proveedor_cb']);
		$this->db->update('log.proveedor_cb', $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['id_proveedor_cb'];
		}
	}

	public function delete_proveedor_cb ($id) {
		$this->db->trans_begin();
		$this->db->where('id_proveedor_cb', $id);
		$this->db->delete('log.proveedor_cb');

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