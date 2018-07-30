<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ClaPre extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_clapre','model');
	}

	public function index() {
    $data = array();
		$this->load->view('v_clapre', $data);
	}

	public function getList() {
    $cod_obra = $this->input->get('cod_obra');
		$rows = $this->model->get_list($cod_obra);
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
  		'cod_clapre'=>$this->input->post('cod_clapre'),
			'desc_clapre'=>$this->input->post('desc_clapre'),
			'cod_obra'=>$this->input->post('cod_obra')
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
          'msg'=>"Se registro el clapre satisfactoriamente",
          'rowid'=>$result
        );
        
      } else {
        $ret = array (
          'success'=>false,
          'msg'=>"Error al registrar el clapre",
          'rowid'=>0
        );
      } 
    }
  	echo json_encode($ret);
	}

	public function Update() {
  	$data = array(
			'id_clapre'=>$this->input->post('id_clapre'),
			'cod_clapre'=>$this->input->post('cod_clapre'),
			'desc_clapre'=>$this->input->post('desc_clapre'),
			'estado_clapre'=>$this->input->post('estado_clapre')
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

	public function Delete() {
		$id = $this->input->post('id');
  		
  	$result = $this->model->delete($id);

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
  public function getObraList () {
    $rows = $this->model->get_obra_list();
    echo json_encode(array(
      'data'=>$rows
    ));
  }
}
?>