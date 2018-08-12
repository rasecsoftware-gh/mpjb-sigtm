<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Viewport extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$logged = $this->session->userdata('logged');
		if($logged != true){
			redirect('session');
		}
	}
	
	public function index()
	{
		
		$this->load->view('v_viewport');
	}
}
?>