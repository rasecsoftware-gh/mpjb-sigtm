<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Session extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_session', 'model');
	}

	public function index()
	{
		$this->load->view('v_session');
	}

	private function ValidateLogin($username, $pwd) {
		$user = sys_session_getUserInfo($username);
		if (!is_null($user)) {
			if ($user->usuario_pw==$pwd) {
				return true;
			}
		}
		return false;
	}

	public function Login() {
		$u = $this->input->post('u');
		$p = $this->input->post('p');
		$result = $this->ValidateLogin($u, $p);
		if ($result) {
			$data  = array(
				"logged" => true,
				"username" => $u,
				"ip"=>$this->get_client_ip()
			);
			$this->session->set_userdata($data);
			echo '{"msg":"true"}';
		} else {
			echo '{"msg":"Usuario o Contraseña invalida"}';
		}
	}

	public function Logout() {
		$data  = array(
			"logged" => false,
			"username" => ''
		);
		$this->session->set_userdata($data);
		redirect('session');
	}

	private function get_client_ip () {
	   	if (@$_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
	      $client_ip = ( !empty($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : ( ( !empty($_ENV['REMOTE_ADDR']) ) ? $_ENV['REMOTE_ADDR'] : "unknown" );
	
	      $entries = split('[, ]', $_SERVER['HTTP_X_FORWARDED_FOR']);
	      reset($entries);
	      while (list(, $entry) = each($entries))
	      {
	         $entry = trim($entry);
	         if ( preg_match("/^([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/", $entry, $ip_list) )
	         {
	            $private_ip = array(
	                  '/^0\./',
	                  '/^127\.0\.0\.1/' //,
	                  //'/^192\.168\..*/',
	                  //'/^172\.((1[6-9])|(2[0-9])|(3[0-1]))\..*/',
	                  //'/^10\..*/'
	                  );
	   
	            $found_ip = preg_replace($private_ip, $client_ip, $ip_list[1]);
	   
	            if ($client_ip != $found_ip)
	            {
	               $client_ip = $found_ip;
	               break;
	            }
	         }
	      }
	   } else {
	      $client_ip = ( !empty($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : ( ( !empty($_ENV['REMOTE_ADDR']) ) ? $_ENV['REMOTE_ADDR'] : "unknown" );
	   }
	   
	   return $client_ip;
	}
	public function showClientIP() {
		echo $this->get_client_ip();
	}
	public function showSessionIP() {
		echo $this->session->userdata('ip');
	}
}
?>