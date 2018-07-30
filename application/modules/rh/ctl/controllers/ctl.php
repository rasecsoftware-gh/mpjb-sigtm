<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CTL extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_ctl','model');
	}

	public function index() {
		$data = array();
		$this->load->view('v_ctl', $data);
	}

	public function getList() {
		$search_ubigeo = $this->input->get('search_ubigeo');
		$search_opcion = $this->input->get('search_opcion');
		$search_estado = $this->input->get('search_estado');
		$pagination_size = $this->input->get('limit');
		$pagination_start = $this->input->get('start');
		$ret = $this->model->get_list($search_opcion, $search_estado, $search_ubigeo, $pagination_size, $pagination_start);
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

	public function getPrintPreview() {
		$report_key = $this->input->post('rkey');
		$url = $this->config->item('rpt_server')."/rpt_tl_pdf.jsp?id={$report_key}";
		echo "<embed src=\"{$url}\" type=\"application/pdf\" width=\"100%\" height=\"100%\"></embed>";
	}

	public function getPrintDetallePreview() {
		$report_key = $this->input->post('rkey');
		if ($this->model->prepare_print_detalle($report_key)) {
			$url = $this->config->item('rpt_server')."/rpt_tl_det_pdf.jsp?id={$report_key}";
			echo "<embed src=\"{$url}\" type=\"application/pdf\" width=\"100%\" height=\"100%\"></embed>";
		}
	}
	
	public function getExcel() {
		$report_key = $this->input->post('rkey');
		$qtlh = $this->db->select('*')->from('rpt.tl_header')->where('report_key', $report_key)->get();
		$qtl = $this->db
			->select('rpt.tl.*, rh.reglab.reglab_abrev')
			->from('rpt.tl')
			->join('rh.reglab','rpt.tl.reglab_cod=rh.reglab.reglab_cod')
			->where('report_key', $report_key)
			->get();
		
		$excelTab = array(array(
			'RECORD DE TIEMPO LABORADO'
		));
    	$excelRow = array(
    	  	'CÓDIGO',
      		'DNI',
			'APELLIDOS Y NOMBRES',
			'UBIGEO',
			'REG',
			'ACTIVO'
		);
		foreach ($qtlh->result() as $row) {
			//for($i=1; $i<=$qtlh->num_fields()-3; $i++){
			for($i=1; $i<=14; $i++){
				$excelRow = array_merge($excelRow, array($row->{"hm{$i}"}) );
			}
		}
		$excelRow = array_merge($excelRow, array('MESES LAB'));
		$excelTab[]=$excelRow;
		unset($excelRow);
		foreach ($qtl->result() as $row) {
			$excelRow = array(
				$row->traba_cod,
				$row->traba_dni,
				$row->traba_nomape,
				$row->traba_ubigeo,
				$row->reglab_abrev,
				$row->traba_activo,
			);
			//for($i=1;$i<$qtl->num_fields()-10;$i++){
			for($i=1;$i<=14;$i++){
				$excelRow = array_merge($excelRow, array($row->{"m{$i}"}) );
			}
			$excelRow = array_merge($excelRow, array($row->meses_lab) );
			$excelTab[]=$excelRow;
			unset($excelRow);
		}
		
		$date = new Datetime();
		$fec = $date->format('d-m-Y_h-i-s');
		$file = "ctl_{$fec}.xlsx";
		toExcel($excelTab, 'tmp/'.$file, array(1,2));
		$json = array(
			'file' => 'tmp/'.$file,
			'name' => $file,
		);
		echo json_encode($json);
	}
	public function siga_servicios() {
		$rows = $this->model->siga_servicio_list('00487109');
		echo "<table style=\"border: 1px solid silver; border-collapse: collapse;\" >";
		if (count($rows)>0) {
			$fields = array_keys(($rows[0]));
			echo "<tr>";
			foreach ($fields  as $h) {
				echo "<th style=\"border: 1px solid silver;\">{$h}</th>";
			}
			echo "</tr>";
			foreach ($rows as $r) {
				echo "<tr>";
				foreach ($fields as $f) {
					echo "<td style=\"border: 1px solid silver;\">{$r[$f]}</td>";
				}
				echo "</tr>";
			}
		}
		echo "</table>";
	}

	public function siga_connect() {
		$serverName = "10.100.100.9"; //serverName\instanceName
		$connectionInfo = array( "Database"=>"SIGA_301793", "UID"=>"sa", "PWD"=>"sqlserversape!");
		$conn = sqlsrv_connect( $serverName, $connectionInfo);

		if( $conn ) {
			 echo "Connection established.<br />";
		}else{
			 echo "Connection could not be established.<br />";
			 die( print_r( sqlsrv_errors(), true));
		}
	}
}
?>