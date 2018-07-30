<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CRLS extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_crls','model');
	}

	public function index() {
		$data = array();
		$this->load->view('v_crls', $data);
	}

	public function getList() {
		$search_by = $this->input->get('search_by');
		$search_text = $this->input->get('search_text');
		$search_ubig = $this->input->get('search_ubig');
		$pagination_size = $this->input->get('limit');
		$pagination_start = $this->input->get('start');
		$ret = $this->model->get_list($search_by, $search_text, $search_ubig, $pagination_size, $pagination_start);
		$rows = $ret['data'];
		foreach ($rows as $i=>$r) {
			//$ret['data'][$i]->oc_anio_numero = $r->oc_anio.'-'.$r->oc_numero;
		}
		echo json_encode($ret);
	}

	public function getPlanillaList () {
		$traba_cod = $this->input->get('traba_cod');
		$ret = $this->model->get_planilla_list($traba_cod);
		echo json_encode($ret);
	}

	public function getPlaniIngresoList () {
		$plani_anio = $this->input->get('plani_anio');
		$plani_cod = $this->input->get('plani_cod');
		$traba_cod = $this->input->get('traba_cod');
		$ret = $this->model->get_plani_ingreso_list($plani_anio, $plani_cod, $traba_cod);
		echo json_encode($ret);
	}

	public function getPlaniDescuentoList () {
		$plani_anio = $this->input->get('plani_anio');
		$plani_cod = $this->input->get('plani_cod');
		$traba_cod = $this->input->get('traba_cod');
		$ret = $this->model->get_plani_descuento_list($plani_anio, $plani_cod, $traba_cod);
		echo json_encode($ret);
	}

	public function getPlaniAporteList () {
		$plani_anio = $this->input->get('plani_anio');
		$plani_cod = $this->input->get('plani_cod');
		$traba_cod = $this->input->get('traba_cod');
		$ret = $this->model->get_plani_aporte_list($plani_anio, $plani_cod, $traba_cod);
		echo json_encode($ret);
	}

	public function getServicioList () {
		$traba_cod = $this->input->get('traba_cod');
		$ret = $this->model->get_servicio_list($traba_cod);
		echo json_encode($ret);
	}

	public function printResumen() {
		$traba_cod = $this->input->post('traba_cod');
		$report_key = base64_encode(microtime(true));
		if ($this->model->prepare_print_resumen($traba_cod, $report_key)) {
			$url = $this->config->item('rpt_server')."/rpt_trep_rls_pdf.jsp?id={$report_key}";
			echo "<embed src=\"{$url}\" type=\"application/pdf\" width=\"100%\" height=\"100%\"></embed>";
		}
	}
}
?>