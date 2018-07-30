<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RBS extends MX_Controller {

  public function __construct()
  {
		parent::__construct();
		$this->load->model('m_rbs','model');
	}

	public function index() {
    $data = array();
		$this->load->view('v_rbs', $data);
	}

	public function getList() {
		$search_by = $this->input->get('search_by');
		$search_text = $this->input->get('search_text');
    $pagination_size = $this->input->get('limit');
    $pagination_start = $this->input->get('start');
		$ret = $this->model->get_list($search_by, $search_text, $pagination_size, $pagination_start);
    $rows = $ret['data'];
    foreach ($rows as $i=>$r) {
      $ret['data'][$i]->oc_anio_numero = $r->oc_anio.'-'.$r->oc_numero;
    }
		echo json_encode($ret);
	}

	public function getNewRow () {
    $row = $this->model->get_new_row();
    die(json_encode(array(
      'data'=>array($row)
    )));  
	}

	public function getRow ($id){
		$row = $this->model->get_row($id);
		die(json_encode(array(
			'data'=>array($row)
		)));
	}

	public function Add() {
		$data = array(
      'oc_anio'=>'0000',
      'oc_numero'=>'00000000',
      'bs_cod'=>$this->input->post('bs_cod'),
			'bs_desc'=>strtoupper($this->input->post('bs_desc')),
      'bs_unimed'=>strtoupper($this->input->post('bs_unimed')),
      'oc_det_cantidad'=>$this->input->post('oc_det_cantidad'),
      'oc_det_obs'=>strtoupper($this->input->post('oc_det_obs')),
			'oc_det_saldo'=>$this->input->post('oc_det_cantidad')
		);

    if ($data['oc_det_cantidad'] < 0) {
      die(json_encode(array(
        'success'=>false,
        'msg'=>"La cantidad o saldo no pueden ser negativos."  
      )));
    }

    $result = $this->model->add($data);
    if ($result !== false) {
      die(json_encode(array(
        'success'=>true,
        'msg'=>"Se registro el bien satisfactoriamente",
        'rowid'=>$result
      )));
    } else {
      die(json_encode(array(
        'success'=>false,
        'msg'=>"Error al registrar el bien"
      )));
    } 
	}

	public function Update() {
    $data = array(
      'oc_det_id'=>$this->input->post('oc_det_id'),
      'bs_cod'=>$this->input->post('bs_cod'),
      'bs_desc'=>$this->input->post('bs_desc'),
      'bs_unimed'=>$this->input->post('bs_unimed'),
      'oc_det_obs'=>strtoupper($this->input->post('oc_det_obs')),
      'oc_det_cantidad'=>$this->input->post('oc_det_cantidad'),
      'oc_det_saldo'=>$this->input->post('oc_det_cantidad')
    );

    if ($data['oc_det_cantidad'] < 0) {
      die(json_encode(array(
        'success'=>false,
        'msg'=>"La cantidad o saldo no pueden ser negativo."  
      )));
    }
  	
    $result = $this->model->update($data);
    if ($result !== false) {
      die(json_encode(array(
        'success'=>true,
        'msg'=>"Se actualizo satisfactoriamente",
        'rowid'=>$result
      )));
    } else {
      die(json_encode(array(
        'success'=>false,
        'msg'=>"Error al realizar la operacion"
      )));
    }
	}

  public function getSys2009BienList () {
    $filter = $this->input->get('query');
    $rows = $this->model->get_sys2009_bien_list(strtoupper($filter));
    echo '{"data":'.json_encode($rows).'}'; 
  }

  public function activarClaseBS() {
    $data = array(
      'id_clase_bs'=>$this->input->post('id_clase_bs'),
      'estado_clase_bs'=>'A'
    );

    $r = $this->model->get_clase_bs_row($data['id_clase_bs']);

    $error = '';
    if ($r->estado_clase_bs == 'A') {
      $error = "El registro ya se encuentra activade.";
    }

    if ($error != '') {
      $ret = array (
        'success'=>false,
        'msg'=>$error,
        'rowid'=>$data['id_clase_bs']
      );
      echo json_encode($ret);
      exit;
    }

    $result = $this->model->update_clase_bs($data);

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

	public function deletebsCB() {
		$id = $this->input->post('id');
  		
  		$result = $this->model->delete_bs_cb($id);

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
  
}
?>