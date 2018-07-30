<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GEB extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_geb','model');
	}

	public function index() {
		$data = array();
		$this->load->view('v_geb', $data);
	}

	public function getList () {
		$search_by = $this->input->get('search_by');
		$search_text = $this->input->get('search_text');
		$pagination_size = $this->input->get('limit');
    	$pagination_start = $this->input->get('start');
		$ret = $this->model->get_list($search_by, $search_text, $pagination_size, $pagination_start);

		$rows = $ret['data'];
		foreach($rows as $i=>$r) {
			$r->nemo_cod_desc = $r->nemo_anio.'-'.$r->nemo_cod.': '.$r->nemo_desc;
			$gdcount = $this->db->select('COUNT(*) AS value')->where('geb_id', $r->geb_id)->get('alm.geb_det')->row();
			$r->geb_det_count = $gdcount->value;
		}
		$ret['data'] = $rows;
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

	public function Add() {
		$data = array(
  			'geb_fecha'=>$this->input->post('geb_fecha'),
			'geb_solicitante'=>$this->input->post('geb_solicitante'),
			'nemo_anio'=>$this->input->post('nemo_anio'),
			'nemo_cod'=>$this->input->post('nemo_cod'),
			'nemo_desc'=>$this->input->post('nemo_desc'),
			'nemo_secfun'=>$this->input->post('nemo_secfun'),
			'nemo_meta'=>$this->input->post('nemo_meta'),
			'area_cod'=>$this->input->post('area_cod'),
      		'area_desc'=>$this->input->post('area_desc'),
			'geb_desc'=>$this->input->post('geb_desc')
		);

		if (trim($data['nemo_cod'])=='') {
			die (json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Centro de costo",
				'target_id'=>'geb_form_nemo_desc'
			)));
		}

		if (trim($data['area_cod'])=='') {
			die (json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Area de destino",
				'target_id'=>'geb_form_area_desc'
			)));
		}

		if (trim($data['geb_fecha'])=='') {
			die (json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la fecha",
				'target_id'=>'geb_form_geb_fecha'
			)));
		}

		if (trim($data['geb_solicitante'])=='') {
			die (json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el solicitante",
				'target_id'=>'geb_form_geb_solicitante'
			)));
		}

		$from = 'áéíóúñ';
		$to = 'ÁÉÍÓÚÑ';
		$data['geb_solicitante'] = strtr(strtoupper($data['geb_solicitante']), $from, $to);
		
		$result = $this->model->add($data);

		if ($result !== false) {
			$ret = array (
				'success'=>true,
				'msg'=>"Se registro satisfactoriamente",
				'rowid'=>$result
			);
			
		} else {
			$ret = array (
				'success'=>false,
				'msg'=>"Error al registrar el proceso",
				'rowid'=>0
			);
		}
		echo json_encode($ret);
	}

	public function Update() {
		$data = array(
			'geb_id'=>$this->input->post('geb_id'),
  			'geb_fecha'=>$this->input->post('geb_fecha'),
			'geb_solicitante'=>$this->input->post('geb_solicitante'),
			'nemo_anio'=>$this->input->post('nemo_anio'),
			'nemo_cod'=>$this->input->post('nemo_cod'),
			'nemo_desc'=>$this->input->post('nemo_desc'),
			'nemo_secfun'=>$this->input->post('nemo_secfun'),
			'nemo_meta'=>$this->input->post('nemo_meta'),
			'area_cod'=>$this->input->post('area_cod'),
      		'area_desc'=>$this->input->post('area_desc'),
			'geb_desc'=>$this->input->post('geb_desc')
		);

		$geb = $this->model->get_row($data['geb_id']);
		if ($geb->geb_estado == 'APROBADO' || $geb->geb_estado == 'ANULADO') {
			die (json_encode(array(
				'success'=>false,
				'msg'=>"No es posible modificar un documento Aprobado o Anulado."
			)));
		}

		if (trim($data['geb_fecha'])=='') {
			die (json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la fecha",
				'target_id'=>'geb_form_geb_fecha'
			)));
		}

		if (trim($data['geb_solicitante'])=='') {
			die (json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el solicitante",
				'target_id'=>'geb_form_geb_solicitante'
			)));
		}

		$from = 'áéíóúñ';
		$to = 'ÁÉÍÓÚÑ';
		$data['geb_solicitante'] = strtr(strtoupper($data['geb_solicitante']), $from, $to);

		$result = $this->model->update($data);

		if ($result !== false) {
			die (json_encode(array(
				'success'=>true,
				'msg'=>"Se actualizo satisfactoriamente",
				'row_id'=>$result
			)));
			
		} else {
			die (json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion",
				'row_id'=>0
			)));
		}
	}

	public function Aprobar() {
		sys_session_hasRoleOrDie(array('sa', 'geb-aprobador'));

      	$geb_id = $this->input->post('geb_id');

		$r = $this->model->get_row($geb_id);

		if ($r->geb_estado == 'ANULADO') {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"No es posible Aprobar un documento anulado."
			)));
		}
		if ($r->geb_estado == 'APROBADO') {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"El documento ya se encuentra aprobado."
			)));
		}

		$result = $this->model->aprobar($geb_id);

		if ($result !== false) {
			die(json_encode(array (
				'success'=>true,
				'msg'=>"Se aprobo satisfactoriamente.",
				'rowid'=>$geb_id
			)));
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion."
			)));
		}
  	}

  	public function cancelarAprobado() {
  		sys_session_hasRoleOrDie(array('sa', 'geb-aprobador'));

		$geb_id = $this->input->post('geb_id');

		$r = $this->model->get_row($geb_id);

		if ($r->geb_estado == 'ANULADO') {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"No es posible modificar un documento anulado."
			)));
		}
		if ($r->geb_estado == 'GENERADO') {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"El documento no se encuentra aprobado."
			)));
		}

		$result = $this->model->cancelar_aprobado($geb_id);

		if ($result !== false) {
			die(json_encode(array (
				'success'=>true,
				'msg'=>"Se cancelo la aprobacion satisfactoriamente.",
				'rowid'=>$geb_id
			)));
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion."
			)));
		}
  	}

	public function Anular() {
		sys_session_hasRoleOrDie(array('sa', 'geb-anulador'));

		$geb_id = $this->input->post('geb_id');

		$r = $this->model->get_row($geb_id);

		if ($r->geb_estado == 'ANULADO') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Ya se encuentra anulado, no es posible realizar la operacion.",
				'rowid'=>$geb_id
			)));
		}

		$result = $this->model->anular($geb_id);

		if ($result !== false) {
			die(json_encode(array(
				'success'=>true,
				'msg'=>"Se anulo satisfactoriamente.",
				'rowid'=>$geb_id
			)));
		} else {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Error al realizar la operacion.",
				'rowid'=>0
			)));
		}
  	}

  	public function Activar() {
  		sys_session_hasRoleOrDie(array('sa', 'geb-anulador'));

		$geb_id = $this->input->post('geb_id');

		$r = $this->model->get_row($geb_id);

		if ($r->geb_estado == 'GENERADO') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Ya se encuentra activo, no es posible realizar la operacion.",
				'rowid'=>$geb_id
			)));
		}

		$result = $this->model->activar($geb_id);

		if ($result !== false) {
			die(json_encode(array(
				'success'=>true,
				'msg'=>"Se activo satisfactoriamente.",
				'rowid'=>$geb_id
			)));
		} else {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Error al realizar la operacion.",
				'rowid'=>0
			)));
		}
  	}


	public function getSys2009NemoList () {
		$anio = $this->input->get('anio');
		$filter = $this->input->get('query');
		$rows = $this->model->get_sys2009_nemo_list($anio, strtoupper($filter));
		echo '{"data":'.json_encode($rows).'}'; 
	}

	public function getSys2009AreaList () {
		$rows = $this->model->get_sys2009_area_list();
		echo '{"data":'.json_encode($rows).'}';	
	}

	public function getSolicitanteList () {
		$filter = $this->input->get('query');
		$rows = $this->model->get_solicitante_list($filter);
		echo '{"data":'.json_encode($rows).'}';	
	}

	public function getNewGebDetRow (){
		$row = $this->model->get_new_get_det_row();
		echo json_encode(array(
			'data'=>array($row)
		));
	}

	public function getGebDetRow ($id){
		$row = $this->model->get_geb_det_row($id);
		echo json_encode(array(
			'data'=>array($row)
		));
	}

	public function getGebDetList ($id) {
		$rows = $this->model->get_geb_det_list($id);
		foreach ($rows as $i=>$row) {
			$row->orden_anio_numero = $row->oc_anio.'-'.$row->oc_numero;
		}
		echo json_encode(array(
			'data'=>$rows
		));
	}

	public function importGebDetFromOC() {
		$geb_id = $this->input->post('geb_id');

		$geb = $this->model->get_row($geb_id);
		if ($geb->geb_estado == 'APROBADO' || $geb->geb_estado == 'ANULADO') {
			die (json_encode(array(
				'success'=>false,
				'msg'=>"No es posible modificar un documento Aprobado o Anulado."
			)));
		}


		$strlist = $this->input->post('strlist');
		$import_list = explode(',', $strlist);
		$total = count($import_list);
  		$result = $this->model->import_geb_det_from_oc($geb_id, $import_list);

  		if ($result !== false) {
  			$extra = '';
  			if ($result < $total) {
  				$extra = "y se actualizaron ".($total-$result)." ";
  			}
  			$ret = array (
  				'success'=>true,
  				'msg'=>"Se crearon $result {$extra} registros satisfactoriamente.",
  				'count'=>$result
  			);
  			
  		} else {
  			$ret = array (
  				'success'=>false,
  				'msg'=>"Error al realizar la operacion",
  				'count'=>0
  			);
  		}
  		echo json_encode($ret);
	}

	public function importGebDetFromSaldo() {
		$geb_id = $this->input->post('geb_id');

		$geb = $this->model->get_row($geb_id);
		if ($geb->geb_estado == 'APROBADO' || $geb->geb_estado == 'ANULADO') {
			die (json_encode(array(
				'success'=>false,
				'msg'=>"No es posible modificar un documento Aprobado o Anulado."
			)));
		}


		$strlist = $this->input->post('strlist');
		$import_list = explode(',', $strlist);
		$total = count($import_list);
  		$result = $this->model->import_geb_det_from_saldo($geb_id, $import_list);

  		if ($result !== false) {
  			$extra = '';
  			if ($result < $total) {
  				$extra = "y se actualizaron ".($total-$result)." ";
  			}
  			$ret = array (
  				'success'=>true,
  				'msg'=>"Se importaron $result {$extra} registros satisfactoriamente.",
  				'count'=>$result
  			);
  			
  		} else {
  			$ret = array (
  				'success'=>false,
  				'msg'=>"Error al realizar la operacion",
  				'count'=>0
  			);
  		}
  		echo json_encode($ret);
	}

	public function updateGebDet() {
  		$data = array(
  			'geb_det_id'=>$this->input->post('geb_det_id'),
			'geb_det_cantidad'=>$this->input->post('geb_det_cantidad')
		);

  		$result = $this->model->update_geb_det($data);

  		if ($result !== false) {
  			$ret = array (
  				'success'=>true,
  				'msg'=>"Se actualizo satisfactoriamente",
  				'rowid'=>$result
  			);
  			
  		} else {
  			$ret = array (
  				'success'=>false,
  				'msg'=>"Error al realizar la operacion",
  				'rowid'=>0
  			);
  		}
  		echo json_encode($ret);
	}

	public function deleteGebDet() {
		$id = $this->input->post('geb_det_id');
  		
  		$result = $this->model->delete_geb_det($id);

  		if ($result !== false) {
  			die(json_encode(array (
  				'success'=>true,
  				'msg'=>"Se elimino satisfactoriamente"
  			)));
  		} else {
  			die(json_encode(array(
  				'success'=>false,
  				'msg'=>"Error al realizar la operacion"
  			)));
  		}
	}

	public function getSys2009DetOCList ($geb_id) {
		$prefix = 'geb_det_import';
		$orden_anio = $this->input->get('orden_anio');
		$orden_numero = $this->input->get('orden_numero');
		$filter = $this->input->get('query');

		if (is_null($orden_anio)) {
			$orden_anio = $this->session->userdata("{$prefix}_orden_anio");	
		}
		if (is_null($orden_numero)) {
			$orden_numero = $this->session->userdata("{$prefix}_orden_numero");
		}
		if (is_null($filter)) {
			$filter = $this->session->userdata("{$prefix}_query");
		}

		$filter = strtoupper($filter);

		$this->session->set_userdata("{$prefix}_orden_anio", $orden_anio);
		$this->session->set_userdata("{$prefix}_orden_numero", $orden_numero);
		$this->session->set_userdata("{$prefix}_query", $filter);
		
		$rows = $this->model->get_sys2009_det_oc_list($geb_id, $orden_anio, $orden_numero, $filter);

		foreach ($rows as $i=>$row) {
			$rows[$i]['importado'] = '';
			$rows[$i]['saldo'] = '';
			$gdcount = $this->db->select('COUNT(*) AS value')->where('oc_anio', $orden_anio)->where('oc_numero', $orden_numero)->where('bs_cod', $row['bs_cod'])->where('oc_det_obs', $row['obs'])->get('alm.v_geb_det')->row();
			if ($gdcount->value > 0) {
				$rows[$i]['importado'] = 'Si';
				$gdsaldo = $this->db->select('oc_det_saldo AS value')->where('oc_anio', $orden_anio)->where('oc_numero', $orden_numero)->where('bs_cod', $row['bs_cod'])->where('oc_det_obs', $row['obs'])->get('alm.oc_det')->row();
				$rows[$i]['saldo'] = round($gdsaldo->value, 2);
			}
			$rows[$i]['importar'] = false;
		}
		echo json_encode(array(
			'data'=>$rows
		));
	}
	public function getSys2009OCList ($geb_id) {
		$filter = $this->input->get('query');
		$rows = $this->model->get_sys2009_oc_list($geb_id, $filter);
		foreach ($rows as $i=>$row) {
			//$row->importar = false;
		}
		echo json_encode(array(
			'data'=>$rows
		));
	}

	public function getOCDetSaldoList ($geb_id) {
		$prefix = 'geb_det_import_saldo';
		$filter = $this->input->get('query');

		if (is_null($filter)) {
			$filter = $this->session->userdata("{$prefix}_query");
		}

		$filter = strtoupper($filter);

		$this->session->set_userdata("{$prefix}_query", $filter);
		
		$rows = $this->model->get_oc_det_saldo_list($filter);

		foreach ($rows as $i=>$row) {
			$rows[$i]->importado = '';
			$gdcount = $this->db->select('COUNT(*) AS value')->where('oc_det_id', $row->oc_det_id)->get('alm.v_geb_det')->row();
			if ($gdcount->value > 0) {
				$rows[$i]->importado = 'Si';
			}
			$rows[$i]->importar = false;
		}
		echo json_encode(array(
			'data'=>$rows
		));
	}
}
?>