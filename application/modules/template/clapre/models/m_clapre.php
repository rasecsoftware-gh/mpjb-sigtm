<?php
class M_clapre extends CI_Model{
	
	public function __construct() { 
		parent::__construct();
	}

	public function get_list ($cod_obra) {
		//$this->db->where('anio_eje', date('Y'));
		$this->db->where('cod_obra', $cod_obra)->order_by('cod_clapre', 'asc');
		$query = $this->db->get('pre.clapre');
		return $query->result();	
	}

	public function get_new_row () {
		$row = array(
			'id_clapre'=>0,
			'cod_clapre'=>'',
			'desc_clapre'=>''
		);
		return $row;
	}

	public function get_row ($id) {
		$this->db->where('id_clapre', $id);
		$query = $this->db->get('pre.clapre');
		$rows = $query->result();
		return $rows[0];	
	}

	public function valid_add($data) {
		$this->db->select("COUNT(*) AS value");
		$this->db->where('anio_eje', date('Y'));
		$this->db->where('cod_obra', $data['cod_obra']);
		$this->db->where('cod_clapre', $data['cod_clapre']);
		$result_count = $this->db->get('pre.clapre')->result();

		if ($result_count[0]->value > 0) {
			return "El Codigo del clasificador ya esiste.";
		} 
		return true;
	}
	
	public function add ($data){
		$this->db->select("nextval(pg_get_serial_sequence('pre.clapre', 'id_clapre')) AS value");
		$result_id = $this->db->get()->result();
		
		$data['id_clapre'] = $result_id[0]->value;
		$data['anio_eje'] = date('Y');
		$data['syslog'] = 'sys';

		$this->db->trans_begin();

		$this->db->insert('pre.clapre',$data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['id_clapre'];
		}
	}
	
	public function valid_update ($data) {
		$r = $this->get_row($data['id_clapre']);

		$this->db->select("COUNT(*) AS value");
		$this->db->where('anio_eje', date('Y'));
		$this->db->where('cod_obra', $r->cod_obra);
		$this->db->where('cod_clapre', $data['cod_clapre']);
		$this->db->where("id_clapre <> {$data['id_clapre']}");
		$result_count = $this->db->get('pre.clapre')->result();

		if ($result_count[0]->value > 0) {
			return "El Codigo del clasificador ya esiste.";
		} 
		return true;
	}
	
	public function update ($data) {
		$data['syslog'] = 'sys';
		
		$this->db->trans_begin();
		$this->db->where('id_clapre', $data['id_clapre']);
		$this->db->update('pre.clapre', $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['id_clapre'];
		}
	}

	public function delete ($id) {
		$this->db->trans_begin();
		$this->db->where('id_clapre', $id);
		$this->db->delete('pre.clapre');

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $id;
		}
	}


	public function get_obra_list () {
		$this->db->select("anio_eje, cod_obra, desc_obra, cod_obra||' - '||desc_obra AS cod_desc_obra");
		//$this->db->where('anio_eje', date('Y'));
		$this->db->order_by('cod_obra', 'asc');
		$query = $this->db->get('log.obra');
		return $query->result();	
	}
}
?>