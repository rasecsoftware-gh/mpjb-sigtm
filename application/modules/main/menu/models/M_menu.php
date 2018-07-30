<?php
class M_menu extends CI_Model{
	
	public function __construct(){
		parent::__construct();
	}

	public function get_acceso($user,$parent) {
		$menu = array(
			array(
				"text"=>"Usuarios", "leaf"=>true, "name"=>"appUserInfo", "controller"=>"userinfo/userinfo"
			),
			array(
				"text"=>"Listas de Acceso", "leaf"=>true, "name"=>"appACL", "controller"=>"acl/acl"
			),
			array(
				"text"=>"Mantenimiento", "leaf"=>false, 
				"children"=>array(
					array("text"=>"Areas Administrativas", "leaf"=>true, "name"=>"appGroup", "controller"=>"group/group"),
					array("text"=>"Cargos", "leaf"=>true, "name"=>"appProfile", "controller"=>"profile/profile")
				)
			)
		);
		// convert asoc array to object
		$result = json_decode(json_encode($menu), FALSE);
		/*$this->db->where('id_padre',$parent);
		$this->db->where('cod_usuario',$user);
		$result = $this->db->get('sys.v_accesos');*/
		return $result;
	}
}
?>