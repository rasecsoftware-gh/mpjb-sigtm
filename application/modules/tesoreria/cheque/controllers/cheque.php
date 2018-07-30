<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cheque extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_cheque','model');
	}

	public function index() {
		$data = array();
		$this->load->view('v_cheque', $data);
	}

	public function getList () {
		$ano_eje = $this->input->get('ano_eje');
		$search_by = $this->input->get('search_by');
		$search_text = $this->input->get('search_text');
		$pagination_size = $this->input->get('limit');
    	$pagination_start = $this->input->get('start');
		$ret = $this->model->get_list($ano_eje, $search_by, $search_text, $pagination_size, $pagination_start);
		echo json_encode($ret);
	}

	public function getNewRow (){
		$row = $this->model->get_new_row();
		echo json_encode(array(
			'data'=>array($row)
		));
	}

	public function getRow ($id){
		$row = $this->model->get_row($id);
		echo json_encode(array(
			'data'=>array($row)
		));
	}

	public function printPreview() {
		$expediente_documento_id = $this->input->post('expediente_documento_id');
		$cheque_id = $this->model->print_preview($expediente_documento_id);
		if ($cheque_id > 0) {
			$url = $this->config->item('rpt_server')."/rpt_cheque_pdf.jsp?id={$cheque_id}";
			echo "<embed src=\"{$url}\" type=\"application/pdf\" width=\"100%\" height=\"100%\"></embed>";
		}
	}

}
?>