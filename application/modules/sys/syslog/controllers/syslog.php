<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpWord\TemplateProcessor;

class SysLog extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_syslog','model');
		$this->load->library('upload');
		//sys_session_hasRoleOrDie('syslog');
	}

	public function index() {
		$data = array();
		$this->load->view('v_syslog', $data);
	}

	public function View () {
		$p_tablename = $this->input->post_get('tablename');
		$p_rowid = $this->input->post_get('rowid');

		$parts = explode('.', $p_tablename);
		$pk = array_pop($parts).'_id';
		
		
		$row = $this->db->select('*')->where($pk, $p_rowid)->get($p_tablename)->row();
		if (!is_null($row)) {
			$line_list = explode(';', $row->syslog);
			foreach ($line_list as $line) {
				echo "<div>{$line}</div>";
			}
		}
	}
	
}
?>