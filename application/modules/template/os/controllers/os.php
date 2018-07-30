<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OS extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_os','model');
	}

	public function index(){
		$data['anio_eje']  = $this->model->get_anio_activo();
		$this->load->view('v_os', $data);
	}

	public function getList () {
		$search_by = $this->input->get('search_by');
		$search_text = $this->input->get('search_text');
		$rows = $this->model->get_list($search_by, $search_text);
		echo json_encode(array(
			'data'=>$rows
		));
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
			'cod_obra'=>$this->input->post('cod_obra'),
  			'id_frente'=>$this->input->post('id_frente'),
			'fecha_orden'=>$this->input->post('fecha_orden'),
			'desc_orden'=>$this->input->post('desc_orden'),
			'id_proveedor'=>$this->input->post('id_proveedor'),
			'id_regimen_igv'=>$this->input->post('id_regimen_igv'),
			'val_regimen_igv_det'=>$this->input->post('val_regimen_igv_det')
		);
		
  		$result = $this->model->add($data);

  		if ($result !== false) {
  			$ret = array (
  				'success'=>true,
  				'msg'=>"Se registro la Orden satisfactoriamente",
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
  			'id_orden'=>$this->input->post('id_orden'),
  			'id_frente'=>$this->input->post('id_frente'),
			'fecha_orden'=>$this->input->post('fecha_orden'),
			'desc_orden'=>$this->input->post('desc_orden'),
			'id_proveedor'=>$this->input->post('id_proveedor'),
			'id_regimen_igv'=>$this->input->post('id_regimen_igv'),
			'val_regimen_igv_det'=>$this->input->post('val_regimen_igv_det')
		);

  		$result = $this->model->update($data);

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

	public function updateEstado() {

  		$data = array(
  			'id_orden'=>$this->input->post('id_orden'),
  			'estado_orden'=>$this->input->post('estado_orden')
		);

  		$result = $this->model->update($data);

  		if ($data['estado_orden']=='I') {
  			$operation = 'Anulo';
  		} else {
  			$operation = 'Activo';
  		}

  		if ($result !== false) {
  			$ret = array (
  				'success'=>true,
  				'msg'=>"Se $operation satisfactoriamente",
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

	public function Anular() {
      $data = array(
        'id_orden'=>$this->input->post('id_orden'),
        'id_estado_orden'=>'0'
      );

      $r = $this->model->get_row($data['id_orden']);

      $error = '';
      if ($r->id_estado_orden == '0') {
        $error = "La ORDEN esta anulada, no es posible realizar la operacion.";
      }
      if ($r->id_estado_orden == '5') {
        $error = "La ORDEN esta liquidada, no es posible realizar la operacion.";
      }

      if ($error != '') {
        $ret = array (
          'success'=>false,
          'msg'=>$error,
          'rowid'=>$data['id_orden']
        );
        echo json_encode($ret);
        exit;
      }

      $result = $this->model->update($data);

      if ($result !== false) {
        $ret = array (
          'success'=>true,
          'msg'=>"Se anulo satisfactoriamente",
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

  public function Activar() {
      $data = array(
        'id_orden'=>$this->input->post('id_orden'),
        'id_estado_orden'=>'1'
      );

      $r = $this->model->get_row($data['id_orden']);

      $error = '';
      if ($r->id_estado_orden == '1') {
        $error = "La ORDEN ya se encuentra activada, no es posible realizar la operacion.";
      }
      if ($r->id_estado_orden == '5') {
        $error = "La ORDEN esta liquidada, no es posible realizar la operacion.";
      }

      if ($error != '') {
        $ret = array (
          'success'=>false,
          'msg'=>$error,
          'rowid'=>$data['id_orden']
        );
        echo json_encode($ret);
        exit;
      }

      $result = $this->model->update($data);

      if ($result !== false) {
        $ret = array (
          'success'=>true,
          'msg'=>"Se activo satisfactoriamente",
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

	public function getObraList () {
	    $filter = $this->input->get('query');
	    $cod_obra = $this->input->get('cod_obra');
	    $rows = $this->model->get_obra_list($filter, $cod_obra);
	    echo '{"data":'.json_encode($rows).'}'; 
	  }

	public function getFrenteList () {
		$cod_obra = $this->input->get('cod_obra');
		$rows = $this->model->get_frente_list($cod_obra);
		echo '{"data":'.json_encode($rows).'}';	
	}

	public function getProveedorList () {
		$filter = $this->input->get('query');
		$rows = $this->model->get_proveedor_list($filter);
		echo '{"data":'.json_encode($rows).'}';	
	}

	public function getRegimenIgvList () {
		$rows = $this->model->get_regimen_igv_list();
		echo '{"data":'.json_encode($rows).'}';	
	}

	public function getRegimenIgvDetList () {
		$id_regimen_igv = $this->input->get('id_regimen_igv');
		$rows = $this->model->get_regimen_igv_det_list($id_regimen_igv);
		echo '{"data":'.json_encode($rows).'}';	
	}

	public function getNewDetailRow (){
		$row = $this->model->get_new_detail_row();
		echo json_encode(array(
			'data'=>array($row)
		));
	}

	public function getDetailRow ($id){
		$row = $this->model->get_detail_row($id);
		echo json_encode(array(
			'data'=>array($row)
		));
	}

	public function getDetailList ($id) {
		$rows = $this->model->get_detail_list($id);
		foreach ($rows as $i=>$row) {
			$row->tot_det_orden = floatval($row->tot_det_orden);
		}
		echo json_encode(array(
			'data'=>$rows
		));
	}

	public function importDetails() {
		$id_orden = $this->input->post('id_orden');
		$strlist = $this->input->post('strlist');
		$import_list = explode(',', $strlist);
		
  		$result = $this->model->import_details($id_orden, $import_list);

  		if ($result !== false) {
  			$ret = array (
  				'success'=>true,
  				'msg'=>"Se importo los registros satisfactoriamente",
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

	public function updateDetail() {

  		$data = array(
  			'id_det_orden'=>$this->input->post('id_det_orden'),
			'id_orden'=>$this->input->post('id_orden'),
			'cant_det_orden'=>$this->input->post('cant_det_orden'),
			'preuni_det_orden'=>$this->input->post('preuni_det_orden'),
			'obs_det_orden'=>$this->input->post('obs_det_orden')
		);
		$data['tot_det_orden'] = floatval($data['cant_det_orden']) * floatval($data['preuni_det_orden']);

  		$result = $this->model->update_detail($data);


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

	public function deleteDetail() {
		$id = $this->input->post('id');
  		
  		$result = $this->model->delete_detail($id);

  		if ($result !== false) {
  			$ret = array (
  				'success'=>true,
  				'msg'=>"Se elimino satisfactoriamente",
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

	public function getDetRequerList ($id_orden) {
		$filter = $this->input->get('query');
		$rows = $this->model->get_det_requer_list($filter, $id_orden);
		foreach ($rows as $i=>$row) {
			$row->tot_det_requer = floatval($row->tot_det_requer);
			$row->importar = false;
		}
		echo json_encode(array(
			'data'=>$rows,
			'id_orden'=>$id_orden
		));
	}
}
?>