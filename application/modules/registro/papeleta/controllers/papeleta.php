<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpWord\TemplateProcessor;

class Papeleta extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_papeleta','model');
		$this->load->library('upload');
		//sys_session_hasRoleOrDie('papeleta');
	}

	public function index() {
		$data = array();
		$this->load->view('v_papeleta', $data);
	}

	public function getList() {
		$search_by = $this->input->get('search_by');
		$search_text = $this->input->get('search_text');
		$pagination_size = $this->input->get('limit');
		$pagination_start = $this->input->get('start');
		$ret = $this->model->get_list($search_by, $search_text, $pagination_size, $pagination_start);
		foreach ($ret['data'] as $i=>$r) {
			$r->contribuyente_nomape = $r->contribuyente_nombres.' '.$r->contribuyente_apellidos;
		}
		echo json_encode($ret);
	}

	public function getNewRow () {
		$row = $this->model->get_new_row();
		$row['operation'] = 'new';
		echo json_encode(array(
			'data'=>array($row)
		));
	}

	public function getRow ($id){
		$row = $this->model->get_row($id);
		$row->operation = 'edit';
		die(json_encode(array(
			'data'=>array($row)
		)));
	}
	public function AddOrUpdate() {
		if ($this->input->post('operation') == 'new') {
			$this->Add();
		} else {
			$this->Update();
		}
	}

	public function Add() {
		//sys_session_hasRoleOrDie('rh.papeleta.add, rh.papeleta.update');
		$data = array(
			'papeleta_numero'=>trim($this->input->post('papeleta_numero')),
			'contribuyente_id'=>$this->input->post('contribuyente_id'),
			'papeleta_fecha'=>$this->input->post('papeleta_fecha'),
			'tipo_infraccion_id'=>$this->input->post('tipo_infraccion_id'),
			'papeleta_infraccion_codigo'=>$this->input->post('papeleta_infraccion_codigo'),
			'medida_preventiva_id'=>$this->input->post('medida_preventiva_id'),
			'estado_papeleta_id'=>$this->input->post('estado_papeleta_id')
		);

		if ($data['papeleta_numero']=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Numero de Papelta",
				'target_id'=>'papeleta_form_papeleta_numero_field'
			)));
		}

		/*$numero_doc_count = $this->db
		->select('COUNT(*) AS value')
		->from('public.papeleta')
		->where('papeleta_numero_doc', $data['papeleta_numero_doc'])
		->get()->row();
		if ($numero_doc_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El Numero de Documento ya existe.",
				'target_id'=>'papeleta_form_papeleta_numero_doc_field'
			)));
		}*/

		if ( !($data['contribuyente_id'] > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el contribuyente infractor",
				'target_id'=>'papeleta_form_contribuyente_id_field'
			)));
		}

		/*if ( $data['papeleta_fecha'] == '' ) {
			$data['papeleta_fecha_nac'] = null;
		} elseif ( strlen($data['papeleta_fecha']) != 10 ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique una fecha valida",
				'target_id'=>'papeleta_form_papeleta_fecha_nac_field'
			)));
		}*/

		try {
			$result = $this->model->add($data);
		} catch (Exception $ex) {
			$error = $ex->getMessage();
		}

		if ($result !== false) {
			die(json_encode(array(
				'success'=>true,
				'msg'=>"Se registro satisfactoriamente",
				'rowid'=>$result
			)));
		} else {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Error al realizar la operacion.".(isset($error)?'<br>$error':'')
			)));
		}
		echo json_encode($ret);
	}

	public function Update() {
		//sys_session_hasRoleOrDie('papeleta.update');
		$data = array(
			'papeleta_id'=>$this->input->post('papeleta_id'),
			'papeleta_numero'=>trim($this->input->post('papeleta_numero')),
			'contribuyente_id'=>$this->input->post('contribuyente_id'),
			'papeleta_fecha'=>$this->input->post('papeleta_fecha'),
			'tipo_infraccion_id'=>$this->input->post('tipo_infraccion_id'),
			'papeleta_infraccion_codigo'=>$this->input->post('papeleta_infraccion_codigo'),
			'medida_preventiva_id'=>$this->input->post('medida_preventiva_id'),
			'estado_papeleta_id'=>$this->input->post('estado_papeleta_id')
		);

		if ($data['papeleta_numero']=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Numero de Papelta",
				'target_id'=>'papeleta_form_papeleta_numero_field'
			)));
		}

		if ( !($data['contribuyente_id'] > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el contribuyente infractor",
				'target_id'=>'papeleta_form_contribuyente_id_field'
			)));
		}


		try {
			$result = $this->model->update($data);
		} catch (Exception $ex) {
			$error = $ex->getMessage();
		}

		if ($result !== false) {
			die(json_encode(array(
				'success'=>true,
				'msg'=>"Se actualizo satisfactoriamente",
				'rowid'=>$data['papeleta_id']
			)));
			
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion.".(isset($error)?'<br>$error':'')
			)));
		}
	}

	public function Delete() {
		sys_session_hasRoleOrDie('registro.papeleta.delete');
		$p_papeleta_id = $this->input->post('papeleta_id');

		$result = $this->model->delete($p_papeleta_id);

		if ($result !== false) {
			die(json_encode(array(
				'success'=>true,
				'msg'=>"Se elimino satisfactoriamente."
			)));
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion."
			)));
		}
	}

	public function getContribuyenteList () {
		$filter = trim($this->input->get_post('query'));
		$ret = $this->model->get_contribuyente_list($filter);
		foreach ($ret['data'] as $i=>$r) {
			$r->contribuyente_nomape = $r->contribuyente_nombres.' '.$r->contribuyente_apellidos;
		}
		echo json_encode($ret);
	}

	public function getTipoInfraccionList () {
		$ret = $this->model->get_tipo_infraccion_list();
		echo json_encode($ret);
	}

	public function getMedidaPreventivaList () {
		$ret = $this->model->get_medida_preventiva_list();
		echo json_encode($ret);
	}

	public function getEstadoPapeletaList () {
		$ret = $this->model->get_estado_papeleta_list();
		echo json_encode($ret);
	}
}
?>