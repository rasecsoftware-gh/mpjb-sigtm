<?php
class M_Session extends CI_Model{
	
	public function __construct(){
		parent::__construct();
	}

	public function verifica_usuario($user,$pass){
		/*$this->db->where('cod_usu',$user);
		$this->db->where('pass_usu',$pass);
		$result = $this->db->get('sys.usuario');*/
		return true;
	}
}
?>