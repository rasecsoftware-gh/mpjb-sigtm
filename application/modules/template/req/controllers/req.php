<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Req extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_req','model');
	}

	public function index(){
		$data['anio_eje']  = $this->model->get_anio_activo();
		$this->load->view('v_req', $data);
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

	public function Add () {
    $cod_obra = $this->input->post('cod_obra');
		$id_frente = $this->input->post('id_frente');
		$fecha_requer = $this->input->post('fecha_requer');
		$desc_requer = $this->input->post('desc_requer');

  		$result = $this->model->add(
        $cod_obra,
  			$id_frente,
  			$fecha_requer,
  			$desc_requer
  		);


  		if ($result !== false) {
  			$ret = array (
  				'success'=>true,
  				'msg'=>"Se registro el proceso",
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
  			'id_requer'=>$this->input->post('id_requer'),
  			'id_frente'=>$this->input->post('id_frente'),
			'fecha_requer'=>$this->input->post('fecha_requer'),
			'desc_requer'=>$this->input->post('desc_requer')
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

	public function UpdateEstado() {
    $id = $operation = $this->input->post('id_requer');
    $operation = $this->input->post('operation');
    $success = true;
    if ($id > 0) {
      $r = $this->model->get_row($id);
      if ($operation=='Anular') {
        if ($r->id_estado_requer == '1') { //generado
          $id_estado_requer = '0';
        } else {
          $success = false;
          $msg = "Solo se pueden Anular requerimientos en estado Generado.";
        }
      } elseif ($operation == 'Activar') {
        if ($r->id_estado_requer == '0') { //generado
          $id_estado_requer = '1';
        } else {
          $success = false;
          $msg = "Solo se pueden activar requerimientos anulados.";
        }
      }
    } else {
      $success = false;
      $msg = 'Requerimiento no valido';
    }

    if (!$success) {
      $ret = array (
        'success'=>false,
        'msg'=>$msg,
        'rowid'=>0
      );
      echo json_encode($ret);
    } else {
      $data = array(
        'id_requer'=>$id,
        'id_estado_requer'=>$id_estado_requer
      );

      $result = $this->model->update($data);

      if ($data['id_estado_requer']=='0') {
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
          'msg'=>"Error al realizar la operacion en la DB",
          'rowid'=>0
        );
      }
      echo json_encode($ret);
    }
	}

  public function Aprobar () {
      $data = array(
        'id_requer'=>$this->input->post('id_requer'),
        'id_estado_requer'=>'3'
      );

      $r = $this->model->get_row($data['id_requer']);

      $error = '';
      if ($r->id_estado_requer == '3') {
        $error = "El Requerimiento ya se encuentra aprobado.";
      }
      if ($r->id_estado_requer == '0') {
        $error = "El Requerimiento esta anulada, no es posible realizar la operacion.";
      }
      if ($r->id_estado_requer == '4' || $r->id_estado_requer == '5') {
        $error = "El Requerimiento esta atendido, no es posible realizar la operacion.";
      }

      if ($error != '') {
        $ret = array (
          'success'=>false,
          'msg'=>$error,
          'rowid'=>$data['id_requer']
        );
        echo json_encode($ret);
        exit;
      }

      $result = $this->model->update($data);

      if ($result !== false) {
        $ret = array (
          'success'=>true,
          'msg'=>"Se aprobo satisfactoriamente",
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
        'id_requer'=>$this->input->post('id_requer'),
        'id_estado_requer'=>'0'
      );

      $r = $this->model->get_row($data['id_requer']);

      $error = '';
      if ($r->id_estado_requer == '0') {
        $error = "El Requerimiento esta anulada, no es posible realizar la operacion.";
      }
      if ($r->id_estado_requer == '4' || $r->id_estado_requer == '5') {
        $error = "El Requerimiento esta atendido o parcialmente atendido. No es posible realizar la operacion.";
      }

      if ($error != '') {
        $ret = array (
          'success'=>false,
          'msg'=>$error,
          'rowid'=>$data['id_requer']
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
        'id_requer'=>$this->input->post('id_requer'),
        'id_estado_requer'=>'1'
      );

      $r = $this->model->get_row($data['id_requer']);

      $error = '';
      if ($r->id_estado_requer != '0') {
        $error = "El Requerimiento no se encuentra anulado para activarlo. No es posible realizar la operacion.";
      }

      if ($error != '') {
        $ret = array (
          'success'=>false,
          'msg'=>$error,
          'rowid'=>$data['id_requer']
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

  public function updateAsAtendido() {
      $data = array(
        'id_requer'=>$this->input->post('id_requer'),
        'id_estado_requer'=>'5'
      );

      $r = $this->model->get_row($data['id_requer']);

      $error = '';
      if ($r->id_estado_requer == '3' || $r->id_estado_requer == '4') {
        $error = "";
      } else {
        $error = "Solo se puede Atender requerimientos aprobados y parcialmente atendidos.";
      }

      if ($error != '') {
        $ret = array (
          'success'=>false,
          'msg'=>$error,
          'rowid'=>$data['id_requer']
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

  public function revaluarAtencion() {
      $data = array(
        'id_requer'=>$this->input->post('id_requer'),
        'id_estado_requer'=>'5'
      );

      $r = $this->model->get_row($data['id_requer']);

      $error = '';
      if ($r->id_estado_requer == '3' || $r->id_estado_requer == '4' || $r->id_estado_requer == '5') {
        $error = "";
      } else {
        $error = "Solo es posible revaluar la tencion si el requermiento esta aprobado o atendido.";
      }

      if ($error != '') {
        $ret = array (
          'success'=>false,
          'msg'=>$error,
          'rowid'=>$data['id_requer']
        );
        echo json_encode($ret);
        exit;
      }

      $rows = $this->model->get_detail_list($data['id_requer']);
      $state_count = array();
      foreach ($rows as $i=>$row) {
        $state_list[$row->id_estado_det_requer] = ':)';
      }
      if (array_key_exists('4', $state_list)) {
        $data['id_estado_requer'] = '4'; // parcialmente atendido
      } elseif (array_key_exists('5', $state_list)) {
        if (array_key_exists('1', $state_list)) {
          $data['id_estado_requer'] = '4'; // parcialmente atendido
        } else {
          $data['id_estado_requer'] = '5'; // atendido
        }
      } else {
        $data['id_estado_requer'] = '3'; // aprobado
      }

      $result = $this->model->update($data);

      if ($result !== false) {
        $ret = array (
          'success'=>true,
          'msg'=>"Se revaluo satisfactoriamente",
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

  public function getEstadoForSearchList () {
    $rows = $this->model->get_estado_for_search_list();
    echo '{"data":'.json_encode($rows).'}'; 
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
			$row->tot_det_requer = floatval($row->tot_det_requer);
		}
		echo json_encode(array(
			'data'=>$rows
		));
	}

	public function addDetail() {
		$data = array(
			'id_requer'=>$this->input->post('id_requer'),
			'id_bs'=>$this->input->post('id_bs'),
			'id_unimed'=>$this->input->post('id_unimed'),
      'id_clapre'=>$this->input->post('id_clapre'),
			'cant_det_requer'=>$this->input->post('cant_det_requer'),
			'preuni_det_requer'=>$this->input->post('preuni_det_requer'),
			'obs_det_requer'=>$this->input->post('obs_det_requer')
		);
		$data['tot_det_requer'] = floatval($data['cant_det_requer']) * floatval($data['preuni_det_requer']);

  		$result = $this->model->add_detail($data);

  		if ($result !== false) {
  			$ret = array (
  				'success'=>true,
  				'msg'=>"Se registro satisfactoriamente",
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

	public function updateDetail() {

  		$data = array(
  			'id_det_requer'=>$this->input->post('id_det_requer'),
			'id_requer'=>$this->input->post('id_requer'),
			'id_bs'=>$this->input->post('id_bs'),
			'id_unimed'=>$this->input->post('id_unimed'),
      'id_clapre'=>$this->input->post('id_clapre'),
			'cant_det_requer'=>$this->input->post('cant_det_requer'),
			'preuni_det_requer'=>$this->input->post('preuni_det_requer'),
			'obs_det_requer'=>$this->input->post('obs_det_requer')
		);
		$data['tot_det_requer'] = floatval($data['cant_det_requer']) * floatval($data['preuni_det_requer']);

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

	public function getBSList () {
		$filter = $this->input->get('query');
		$id_bs = $this->input->get('id_bs');
		$rows = $this->model->get_bs_list($filter, $id_bs);
		echo json_encode(array(
			'data'=>$rows
		));
	}

  public function getClapreList () {
    $id_requer = $this->input->get('id_requer');
    $rows = $this->model->get_clapre_list($id_requer);
    echo json_encode(array(
      'data'=>$rows
    ));
  }
}
?>