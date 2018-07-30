<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NP extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_np','model');
	}

	public function index(){
		$data['anio_eje']  = $this->model->get_anio_activo();
		$this->load->view('v_np', $data);
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
  			'fecha_np'=>$this->input->post('fecha_np'),
  			'desc_np'=>$this->input->post('desc_np'),
  			'id_comprador'=>$this->input->post('id_comprador')
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
    		'id_np'=>$this->input->post('id_np'),
    		'id_frente'=>$this->input->post('id_frente'),
  			'fecha_np'=>$this->input->post('fecha_np'),
  			'desc_np'=>$this->input->post('desc_np'),
  			'id_comprador'=>$this->input->post('id_comprador')
  		);

      $r = $this->model->get_row($data['id_np']);

      $error = '';
      if ($r->id_estado_np == '0') {
        $error = "La NP se encuentra Anulada, no es posible realizar la operacion.";
      }
      if ($r->id_estado_np == '5') {
        $error = "La NP se encuentra Rendida, no es posible realizar la operacion.";
      }
      if ($error != '') {
        $ret = array (
          'success'=>false,
          'msg'=>$error,
          'rowid'=>$data['id_np']
        );
        echo json_encode($ret);
        exit;
      }

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
  			'id_np'=>$this->input->post('id_np'),
  			'estado_np'=>$this->input->post('estado_np')
		  );

  		$result = $this->model->update($data);

  		if ($data['estado_np']=='I') {
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
        'id_np'=>$this->input->post('id_np'),
        'id_estado_np'=>'0'
      );

      $r = $this->model->get_row($data['id_np']);

      $error = '';
      if ($r->id_estado_np == '0') {
        $error = "La NP esta anulada, no es posible realizar la operacion.";
      }
      if ($r->id_estado_np == '5') {
        $error = "La NP esta rendida, no es posible realizar la operacion.";
      }

      if ($error != '') {
        $ret = array (
          'success'=>false,
          'msg'=>$error,
          'rowid'=>$data['id_np']
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
        'id_np'=>$this->input->post('id_np'),
        'id_estado_np'=>'1'
      );

      $r = $this->model->get_row($data['id_np']);

      $error = '';
      if ($r->id_estado_np == '1') {
        $error = "La NP ya se encuentra activada, no es posible realizar la operacion.";
      }
      if ($r->id_estado_np == '5') {
        $error = "La NP esta rendida, no es posible realizar la operacion.";
      }

      if ($error != '') {
        $ret = array (
          'success'=>false,
          'msg'=>$error,
          'rowid'=>$data['id_np']
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

	public function getFrenteList () {
		$filter = $this->input->get('query');
		$rows = $this->model->get_frente_list($filter);
		echo '{"data":'.json_encode($rows).'}';	
	}

	public function getCompradorList () {
		$filter = $this->input->get('query');
		$rows = $this->model->get_comprador_list($filter);
		echo '{"data":'.json_encode($rows).'}';	
	}

	public function getProveedorList () {
		$filter = $this->input->get('query');
		$id_proveedor = $this->input->get('id_proveedor');
		$rows = $this->model->get_proveedor_list($filter, $id_proveedor);
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
			$row->tot_det_np = floatval($row->tot_det_np);
		}
		echo json_encode(array(
			'data'=>$rows
		));
	}

	public function importDetails() {
		$id_np = $this->input->post('id_np');
		$strlist = $this->input->post('strlist');
		$import_list = explode(',', $strlist);
		
  		$result = $this->model->import_details($id_np, $import_list);

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
  			'id_det_np'=>$this->input->post('id_det_np'),
			'id_np'=>$this->input->post('id_np'),
			'cant_det_np'=>$this->input->post('cant_det_np'),
			'preuni_det_np'=>$this->input->post('preuni_det_np'),
			'obs_det_np'=>$this->input->post('obs_det_np')
		);
		$data['tot_det_np'] = floatval($data['cant_det_np']) * floatval($data['preuni_det_np']);

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

	public function getDetRequerList ($id_np) {
		$filter = $this->input->get('query');
		$rows = $this->model->get_det_requer_list($filter, $id_np);
		foreach ($rows as $i=>$row) {
			$row->tot_det_requer = floatval($row->tot_det_requer);
			$row->importar = false;
		}
		echo json_encode(array(
			'data'=>$rows,
			'id_np'=>$id_np
		));
	}

  public function updateRDetail() {

      $data = array(
        'id_det_np'=>$this->input->post('id_det_np'),
      'id_np'=>$this->input->post('id_np'),
      'cant_final_det_np'=>$this->input->post('cant_final_det_np'),
      'preuni_final_det_np'=>$this->input->post('preuni_final_det_np'),
      'id_proveedor'=>$this->input->post('id_proveedor')
    );
    $data['tot_final_det_np'] = floatval($data['cant_final_det_np']) * floatval($data['preuni_final_det_np']);

    if ($data['id_proveedor'] == '') {
      $ret = array (
          'success'=>false,
          'msg'=>"Especifique el Proveedor",
          'rowid'=>0
        );
        echo json_encode($ret);
        exit;
    }
      
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

	public function updateAsRendido () {

  		$data = array(
  			'id_np'=>$this->input->post('id_np')
		  );
  		$r = $this->model->get_row($data['id_np']);

  		if ($r->id_estado_np == '0') {
  			$ret = array (
  				'success'=>false,
  				'msg'=>"La NP esta anulada, no es posible realizar la operacion.",
  				'rowid'=>$data['id_np']
  			);
  			echo json_encode($ret);
  			exit;
  		}

  		if ($r->id_estado_np == '5') {
  			$ret = array (
  				'success'=>false,
  				'msg'=>"La NP ya esta rendida, no es posible realizar la operacion.",
  				'rowid'=>$data['id_np']
  			);
  			echo json_encode($ret);
  			exit;
  		}

  		$data['id_estado_np'] = '5';

  		$result = $this->model->update_as_rendido($data);

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
  public function cancelarRendicion () {

      $data = array(
        'id_np'=>$this->input->post('id_np')
      );
      $r = $this->model->get_row($data['id_np']);

      if ($r->id_estado_np != '5') {
        $ret = array (
          'success'=>false,
          'msg'=>"La NP no esta rendida, no es posible realizar la operacion.",
          'rowid'=>$data['id_np']
        );
        echo json_encode($ret);
        exit;
      }

      if ($r->id_estado_np == '0') {
        $ret = array (
          'success'=>false,
          'msg'=>"La NP esta anulada, no es posible realizar la operacion.",
          'rowid'=>$data['id_np']
        );
        echo json_encode($ret);
        exit;
      }

      $data['id_estado_np'] = '1';

      $result = $this->model->cancelar_rendicion($data);

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
          'rowid'=>$data['id_np']
        );
      }
      echo json_encode($ret);
  }

  public function getTipoDocumentoList () {
    $rows = $this->model->get_tipo_documento_list();
    echo '{"data":'.json_encode($rows).'}'; 
  }

  public function getDetNPForImportList ($id_np) {
    $rows = $this->model->get_det_np_for_import_list($id_np);
    foreach ( $rows as $r) {
      $r->comprobante = $this->model->get_det_np_comprobante($r->id_det_np);
      $r->cant_det_np = floatval($r->cant_det_np);
      $r->saldo = floatval($r->saldo);
      $r->tot_det_np = floatval($r->tot_det_np);
    }
    echo '{"data":'.json_encode($rows).'}'; 
  }

  public function getDetNPForImportRow ($id_det_np) {
    $row = $this->model->get_det_np_for_import_row($id_det_np);
    echo json_encode(array(
      'data'=>array($row)
    ));
  }

  public function getRendicionNPList ($id_np) {
    $rows = $this->model->get_rendicion_np_list($id_np);
    echo '{"data":'.json_encode($rows).'}'; 
  }

  public function getRendicionNPNewRow (){
    $row = $this->model->get_rendicion_np_new_row();
    echo json_encode(array(
      'data'=>array($row)
    ));
  }

  public function getRendicionNPRow ($id){
    $row = $this->model->get_rendicion_np_row($id);
    echo json_encode(array(
      'data'=>array($row)
    ));
  }

  public function addRendicionNP() {

    $data = array(
      'id_np'=>$this->input->post('id_np'),
      'id_tipo_documento'=>$this->input->post('id_tipo_documento'),
      'id_proveedor'=>$this->input->post('id_proveedor'),
      'fechadoc_rendicion_np'=>$this->input->post('fechadoc_rendicion_np'),
      'numdoc_rendicion_np'=>$this->input->post('numdoc_rendicion_np')
    );

    if ($data['id_tipo_documento'] == '') {
      $ret = array (
        'success'=>false,
        'msg'=>"Especifique el Tipo de documento",
        'rowid'=>0
      );
      echo json_encode($ret);
      exit;
    }

    if ($data['id_proveedor'] == '') {
      $ret = array (
        'success'=>false,
        'msg'=>"Especifique el Proveedor",
        'rowid'=>0
      );
      echo json_encode($ret);
      exit;
    }
      
    $result = $this->model->add_rendicion_np($data);

    if ($result !== false) {
      $ret = array (
        'success'=>true,
        'msg'=>"Se agrego satisfactoriamente",
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

  public function updateRendicionNP() {

    $data = array(
      'id_rendicion_np'=>$this->input->post('id_rendicion_np'),
      'id_tipo_documento'=>$this->input->post('id_tipo_documento'),
      'id_proveedor'=>$this->input->post('id_proveedor'),
      'fechadoc_rendicion_np'=>$this->input->post('fechadoc_rendicion_np'),
      'numdoc_rendicion_np'=>$this->input->post('numdoc_rendicion_np')
    );

    if ($data['id_tipo_documento'] == '') {
      $ret = array (
        'success'=>false,
        'msg'=>"Especifique el Tipo de documento",
        'rowid'=>0
      );
      echo json_encode($ret);
      exit;
    }

    if ($data['id_proveedor'] == '') {
      $ret = array (
        'success'=>false,
        'msg'=>"Especifique el Proveedor",
        'rowid'=>0
      );
      echo json_encode($ret);
      exit;
    }
      
    $result = $this->model->update_rendicion_np($data);

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

  public function deleteRendicionNP() {
    $id = $this->input->post('id');
      
    $result = $this->model->delete_rendicion_np($id);

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

  public function getDetRendicionNPList () {
    $id_rendicion_np = $this->input->get('id_rendicion_np');
    $rows = $this->model->get_det_rendicion_np_list($id_rendicion_np);
    foreach ($rows as $i=>$row) {
      $row->tot_det_rendicion_np = floatval($row->tot_det_rendicion_np);
    }
    echo json_encode(array(
      'data'=>$rows
    ));
  }

  public function getDetRendicionNPRow ($id_det_rendicion_np) {
    $row = $this->model->get_det_rendicion_np_row($id_det_rendicion_np);
    echo json_encode(array(
      'data'=>array($row)
    ));
  }

  private function validateAddDetRendicionNP($data) {
    return $this->model->validate_add_det_rendicion_np($data);
  }

  public function addDetRendicionNP() {

    $data = array(
      'id_rendicion_np'=>$this->input->post('id_rendicion_np'),
      'id_det_np'=>$this->input->post('id_det_np'),
      'cant_det_rendicion_np'=>$this->input->post('cant_det_rendicion_np'),
      'preuni_det_rendicion_np'=>$this->input->post('preuni_det_rendicion_np')
    );
    $data['tot_det_rendicion_np'] = floatval($data['cant_det_rendicion_np']) * floatval($data['preuni_det_rendicion_np']);

    $valid = $this->validateAddDetRendicionNP($data);
    if ($valid !== true) {
      $ret = array (
        'success'=>false,
        'msg'=>$valid,
        'rowid'=>0
      );
      echo json_encode($ret);
      return;
    }

      
    $result = $this->model->add_det_rendicion_np($data);

    if ($result !== false) {
      $ret = array (
        'success'=>true,
        'msg'=>"Se agrego satisfactoriamente",
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

  public function updateDetRendicionNP() {

    $data = array(
      'id_det_rendicion_np'=>$this->input->post('id_det_rendicion_np'),
      'cant_det_rendicion_np'=>$this->input->post('cant_det_rendicion_np'),
      'preuni_det_rendicion_np'=>$this->input->post('preuni_det_rendicion_np')
    );
    $data['tot_det_rendicion_np'] = floatval($data['cant_det_rendicion_np']) * floatval($data['preuni_det_rendicion_np']);
      
    $result = $this->model->update_det_rendicion_np($data);

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

  public function deleteDetRendicionNP() {
    $id = $this->input->post('id');
      
    $result = $this->model->delete_det_rendicion_np($id);

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