<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Comprador extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_comprador','model');
	}

	public function index() {
    $data = array();
		$this->load->view('v_comprador', $data);
	}

	public function getList() {
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
  		'cod_comprador'=>$this->input->post('cod_comprador'),
			'desc_comprador'=>$this->input->post('desc_comprador')
		);
		
    $validation = $this->model->valid_add($data);
    if ($validation!==true) {
      $ret = array (
        'success'=>false,
        'msg'=>$validation,
        'rowid'=>0
      );
    } else {
      $result = $this->model->add($data);
      if ($result !== false) {
        $ret = array (
          'success'=>true,
          'msg'=>"Se registro el Comprador satisfactoriamente",
          'rowid'=>$result
        );
        
      } else {
        $ret = array (
          'success'=>false,
          'msg'=>"Error al registrar el Comprador",
          'rowid'=>0
        );
      } 
    }
  	echo json_encode($ret);
	}

	public function Update() {
  	$data = array(
			'id_comprador'=>$this->input->post('id_comprador'),
			'cod_comprador'=>$this->input->post('cod_comprador'),
			'desc_comprador'=>$this->input->post('desc_comprador'),
			'estado_comprador'=>$this->input->post('estado_comprador')
		);
    $validation = $this->model->valid_update($data);
    if ($validation!==true) {
      $ret = array (
        'success'=>false,
        'msg'=>$validation,
        'rowid'=>0
      );
    } else {
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
    }
  	echo json_encode($ret);
	}

	public function getNewCompradorCBRow (){
		$row = $this->model->get_new_comprador_cb_row();
		echo json_encode(array(
			'data'=>array($row)
		));
	}

	public function getCompradorCBRow ($id){
		$row = $this->model->get_comprador_cb_row($id);
		echo json_encode(array(
			'data'=>array($row)
		));
	}

	public function getCompradorCBList ($id_comprador) {
		$rows = $this->model->get_comprador_cb_list($id_comprador);
		echo json_encode(array(
			'data'=>$rows
		));
	}

	public function addCompradorCB() {
		$data = array(
  		'id_comprador'=>$this->input->post('id_comprador'),
			'nro_comprador_cb'=>$this->input->post('nro_comprador_cb'),
			'id_moneda'=>$this->input->post('id_moneda'),
			'id_banco'=>$this->input->post('id_banco')
		);

    $result = $this->model->add_comprador_cb($data);
    if ($result !== false) {
      $ret = array (
        'success'=>true,
        'msg'=>"Se registro la Cuenta del Comprador satisfactoriamente",
        'rowid'=>$result
      );
    } else {
      $ret = array (
        'success'=>false,
        'msg'=>"Error al registrar la CB",
        'rowid'=>0
      );
    }
		echo json_encode($ret);
	}

	public function updateCompradorCB() {

  	$data = array(
  		'id_comprador_cb'=>$this->input->post('id_comprador_cb'),
			'nro_comprador_cb'=>$this->input->post('nro_comprador_cb'),
			'id_moneda'=>$this->input->post('id_moneda'),
			'id_banco'=>$this->input->post('id_banco'),
			'estado_comprador_cb'=>$this->input->post('estado_comprador_cb')
		);

		$result = $this->model->update_comprador_cb($data);


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

	public function deleteCompradorCB() {
		$id = $this->input->post('id');
  		
		$result = $this->model->delete_comprador_cb($id);

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
  public function getBancoList () {
    $rows = $this->model->get_banco_list();
    echo json_encode(array(
      'data'=>$rows
    ));
  }
  public function getMonedaList () {
    $rows = $this->model->get_moneda_list();
    echo json_encode(array(
      'data'=>$rows
    ));
  }
}
?>