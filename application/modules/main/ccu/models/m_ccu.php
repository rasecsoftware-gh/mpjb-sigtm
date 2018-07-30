<?php
class M_CCU extends CI_Model{
	
	public function __construct() { 
		parent::__construct();
	}

	public function get_list ($search_by='all', $search_text='', $size=100, $start=0) {
		$rows = $this->db->select("u.*")
		->from('sys.usuario AS u')
		->get()->result();
		
		$ret = array(
			'data'=>$rows,
			'total'=>count($rows)
		);
		return $ret;
	}

	public function get_new_row () {
		$row = array();
		$row['usuario_login'] = '';
		$row['usuario_desc'] = '';
		$row['usuario_pw'] = 'thepasswd';
		$row['usuario_sa'] = 'N';
		$row['usuario_estado'] = 'A';
		return $row;
	}

	public function get_row ($id) {
		$row = $this->db
		->select("*")
		->from("sys.usuario")
		->where('usuario_id', $id)
		->get()->row();
		return $row;
	}

	public function add ($data) {
		$table = 'sys.usuario';
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

	public function update ($data) {
		$table = 'sys.usuario';
		$usuario = $this->get_row($data['usuario_id']);
		//$data['syslog'] = sys_session_syslog('modificar', $c->syslog);
		
		$this->db->trans_begin();
		$this->db->where('usuario_id', $data['usuario_id']);
		$this->db->update($table, $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return true;
		}
	}

	public function delete ($usuario_id) {
		$table = 'sys.usuario';
		
		$this->db->trans_begin();
		$this->db->where('usuario_id', $usuario_id);
		$this->db->delete($table);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return true;
		}
	}

	public function get_permiso_list ($filter, $psize, $pstart) {
		$rows = $this->db
		->select("*")
		->from('sys.permiso')
		->like('permiso_accion', $filter)
		->or_like('permiso_desc', $filter)
		->order_by('permiso_accion', 'ASC')
		->get()->result();

		$total_count = count($rows);
		
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count
		);
		return $ret;
	}

	public function get_permiso_new_row () {
		$row = array();
		$row['permiso_desc'] = '';
		$row['permiso_accion'] = 'module.form.action';
		$row['permiso_estado'] = 'A';
		return $row;
	}

	public function permiso_add ($data) {
		$table = 'sys.permiso';
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

	public function permiso_update ($data) {
		$table = 'sys.permiso';
		//$data['syslog'] = sys_session_syslog('modificar', $c->syslog);
		$this->db->trans_begin();
		$this->db->where('permiso_id', $data['permiso_id']);
		$this->db->update($table, $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return true;
		}
	}

	public function get_usuario_permiso_list ($usuario_id) {
		$rows = $this->db->select("
			up.*,
			p.permiso_accion, p.permiso_desc
		")
		->from('sys.usuario_permiso AS up')
		->join('sys.permiso AS p', 'p.permiso_id = up.permiso_id', 'inner')
		->where('up.usuario_id', $usuario_id)
		->order_by('p.permiso_accion', 'ASC')
		->get()->result();
		
		$total_count = count($rows);
		
		return array(
			'data'=>$rows,
			'total'=>$total_count
		);
	}

	public function usuario_permiso_add ($data) {
		$table = 'sys.usuario_permiso';
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
}
?>