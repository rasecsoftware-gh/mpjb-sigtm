<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Menu extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_menu','mm');
	}

	public function index() {
		$menu = $this->constructMenu();
		echo json_encode($menu);	
	}

	private function constructMenu() {
		$user = $this->session->userdata('username');
		$rows = $this->mm->get_acceso($user,0);
		/*$menu = "";
		foreach ($rows as $row) {
			$menu .= "{text:'".$row->text."', leaf:".$row->leaf;
			$smenus = $this->subMenu($user,$row->id_menu);
			$menu .= ",children:[";
			foreach ($smenus->result() as $smenu) {
				$menu .= "{text:'".$smenu->desc_menu."',leaf:".$smenu->form.",controller:'".$smenu->ctrl_menu."',name:'".$smenu->app_menu."'";
				$menu .= ",children:[";
				$smenu1s = $this->subMenu($user,$smenu->id_menu);
				foreach ($smenu1s->result() as $smenu1) {
					$menu .= "{text:'".$smenu1->desc_menu."',leaf:".$smenu1->form.",controller:'".$smenu1->ctrl_menu."',name:'".$smenu1->app_menu."'},";
				}
				$menu .= "]},";
			};
			$menu .= "]},";
		}*/
		return $rows;
	}

	public function subMenu($user,$parent){
		$rows = $this->mm->get_acceso($user,$parent);
		return $rows;
	}
}
?>