<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Proveedor extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_proveedor','model');
	}

	public function index() {
    $data = array();
		$this->load->view('v_proveedor', $data);
	}

	public function getList() {
		$search_by = $this->input->get('search_by');
		$search_text = $this->input->get('search_text');
    $pagination_size = $this->input->get('limit');
    $pagination_start = $this->input->get('start');
		$ret = $this->model->get_list($search_by, $search_text, $pagination_size, $pagination_start);
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
  		'ruc_proveedor'=>$this->input->post('ruc_proveedor'),
			'desc_proveedor'=>$this->input->post('desc_proveedor'),
			'repleg_proveedor'=>$this->input->post('repleg_proveedor'),
			'dir_proveedor'=>$this->input->post('dir_proveedor'),
			'telefono_proveedor'=>$this->input->post('telefono_proveedor'),
			'correo_proveedor'=>$this->input->post('correo_proveedor')
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
          'msg'=>"Se registro el Proveedor satisfactoriamente",
          'rowid'=>$result
        );
        
      } else {
        $ret = array (
          'success'=>false,
          'msg'=>"Error al registrar el proveedor",
          'rowid'=>0
        );
      } 
    }
  	echo json_encode($ret);
	}

	public function Update() {
  	$data = array(
			'id_proveedor'=>$this->input->post('id_proveedor'),
			'ruc_proveedor'=>$this->input->post('ruc_proveedor'),
			'desc_proveedor'=>$this->input->post('desc_proveedor'),
			'repleg_proveedor'=>$this->input->post('repleg_proveedor'),
			'dir_proveedor'=>$this->input->post('dir_proveedor'),
			'telefono_proveedor'=>$this->input->post('telefono_proveedor'),
			'correo_proveedor'=>$this->input->post('correo_proveedor'),
      'estado_proveedor'=>$this->input->post('estado_proveedor')
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

	public function updateEstado() {

  		$data = array(
  			'id_proveedor'=>$this->input->post('id_proveedor'),
  			'estado_proveedor'=>$this->input->post('estado_proveedor')
		);

  		$result = $this->model->update($data);

  		if ($data['estado_proveedor']=='I') {
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

	public function getNewProveedorCBRow (){
		$row = $this->model->get_new_proveedor_cb_row();
		echo json_encode(array(
			'data'=>array($row)
		));
	}

	public function getProveedorCBRow ($id){
		$row = $this->model->get_proveedor_cb_row($id);
		echo json_encode(array(
			'data'=>array($row)
		));
	}

	public function getProveedorCBList ($id_proveedor) {
		$rows = $this->model->get_proveedor_cb_list($id_proveedor);
		echo json_encode(array(
			'data'=>$rows
		));
	}

	public function addProveedorCB() {
		$data = array(
  		'id_proveedor_cb'=>$this->input->post('id_proveedor_cb'),
  		'id_proveedor'=>$this->input->post('id_proveedor'),
			'nro_proveedor_cb'=>$this->input->post('nro_proveedor_cb'),
			'id_moneda'=>$this->input->post('id_moneda'),
			'id_banco'=>$this->input->post('id_banco')
		);

    $result = $this->model->add_proveedor_cb($data);
    if ($result !== false) {
      $ret = array (
        'success'=>true,
        'msg'=>"Se registro la Cuenta del Proveedor satisfactoriamente",
        'rowid'=>$result
      );
    } else {
      $ret = array (
        'success'=>false,
        'msg'=>"Error al registrar el proveedor",
        'rowid'=>0
      );
    }
		echo json_encode($ret);
	}

	public function updateProveedorCB() {

  	$data = array(
  		'id_proveedor_cb'=>$this->input->post('id_proveedor_cb'),
			'nro_proveedor_cb'=>$this->input->post('nro_proveedor_cb'),
			'id_moneda'=>$this->input->post('id_moneda'),
			'id_banco'=>$this->input->post('id_banco'),
			'estado_proveedor_cb'=>$this->input->post('estado_proveedor_cb')
		);

		$result = $this->model->update_proveedor_cb($data);


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

	public function deleteProveedorCB() {
		$id = $this->input->post('id');
  		
  		$result = $this->model->delete_proveedor_cb($id);

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