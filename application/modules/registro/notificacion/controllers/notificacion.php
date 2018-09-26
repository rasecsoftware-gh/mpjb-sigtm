<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpWord\TemplateProcessor;

class Notificacion extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_notificacion','model');
		$this->load->library('upload');
		//sys_session_hasRoleOrDie('notificacion');
	}

	public function index() {
		$data = array();
		$this->load->view('v_notificacion', $data);
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
		//sys_session_hasRoleOrDie('rh.notificacion.add, rh.notificacion.update');
		$data = array(
			'contribuyente_id'=>$this->input->post('contribuyente_id'),
			'notificacion_numero'=>trim($this->input->post('notificacion_numero')),
			'notificacion_fecha'=>$this->input->post('notificacion_fecha'),
			'notificacion_hora'=>$this->input->post('notificacion_hora'),
			'notificacion_acto_administrativo'=>trim($this->input->post('notificacion_acto_administrativo')),
			'notificacion_acta_snar'=>$this->input->post('notificacion_acta_snar'),
			'notificacion_acta_snaf'=>$this->input->post('notificacion_acta_snaf'),
			'notificacion_acta_sdbp'=>$this->input->post('notificacion_acta_sdbp'),
			'notificacion_acta_fecha'=>$this->input->post('notificacion_acta_fecha'),
			'papeleta_id'=>$this->input->post('papeleta_id')
		);

		if ($data['notificacion_numero']=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Numero de Notificacion",
				'target_id'=>'notificacion_form_notificacion_numero_field'
			)));
		}

		/*$numero_doc_count = $this->db
		->select('COUNT(*) AS value')
		->from('public.notificacion')
		->where('notificacion_numero_doc', $data['notificacion_numero_doc'])
		->get()->row();
		if ($numero_doc_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El Numero de Documento ya existe.",
				'target_id'=>'notificacion_form_notificacion_numero_doc_field'
			)));
		}*/

		if ( !($data['contribuyente_id'] > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el contribuyente infractor",
				'target_id'=>'notificacion_form_contribuyente_id_field'
			)));
		}

		if ( !($data['papeleta_id'] > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la papeleta",
				'target_id'=>'notificacion_form_papeleta_id_field'
			)));
		}

		$contribuyente_papeleta = $this->db
		->where('contribuyente_id', $data['contribuyente_id'])
		->where('papeleta_id', $data['papeleta_id'])
		->get('public.papeleta')
		->row();
		if (is_null($contribuyente_papeleta)) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"La papeleta no existe o no corresponde al contribuyente.",
				'target_id'=>'notificacion_form_papeleta_id_field'
			)));
		}

		/*if ( $data['notificacion_fecha'] == '' ) {
			$data['notificacion_fecha_nac'] = null;
		} elseif ( strlen($data['notificacion_fecha']) != 10 ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique una fecha valida",
				'target_id'=>'notificacion_form_notificacion_fecha_nac_field'
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
		//sys_session_hasRoleOrDie('notificacion.update');
		$data = array(
			'notificacion_id'=>$this->input->post('notificacion_id'),
			'contribuyente_id'=>$this->input->post('contribuyente_id'),
			'notificacion_numero'=>trim($this->input->post('notificacion_numero')),
			'notificacion_fecha'=>$this->input->post('notificacion_fecha'),
			'notificacion_hora'=>$this->input->post('notificacion_hora'),
			'notificacion_acto_administrativo'=>trim($this->input->post('notificacion_acto_administrativo')),
			'notificacion_acta_snar'=>$this->input->post('notificacion_acta_snar'),
			'notificacion_acta_snaf'=>$this->input->post('notificacion_acta_snaf'),
			'notificacion_acta_sdbp'=>$this->input->post('notificacion_acta_sdbp'),
			'notificacion_acta_fecha'=>$this->input->post('notificacion_acta_fecha'),
			'papeleta_id'=>$this->input->post('papeleta_id')
		);

		if ($data['notificacion_numero']=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Numero de Papelta",
				'target_id'=>'notificacion_form_notificacion_numero_field'
			)));
		}

		if ( !($data['contribuyente_id'] > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el contribuyente infractor",
				'target_id'=>'notificacion_form_contribuyente_id_field'
			)));
		}

		if ( !($data['papeleta_id'] > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la papeleta",
				'target_id'=>'notificacion_form_papeleta_id_field'
			)));
		}

		$contribuyente_papeleta = $this->db
		->where('contribuyente_id', $data['contribuyente_id'])
		->where('papeleta_id', $data['papeleta_id'])
		->get('public.papeleta')
		->row();
		if (is_null($contribuyente_papeleta)) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"La papeleta no existe o no corresponde al contribuyente.",
				'target_id'=>'notificacion_form_papeleta_id_field'
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
				'rowid'=>$data['notificacion_id']
			)));
			
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion.".(isset($error)?'<br>$error':'')
			)));
		}
	}

	public function Delete() {
		sys_session_hasRoleOrDie('registro.notificacion.delete');
		$p_notificacion_id = $this->input->post('notificacion_id');

		$result = $this->model->delete($p_notificacion_id);

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

	public function getPapeletaList () {
		$contribuyente_id = $this->input->get_post('contribuyente_id');
		if ( !($contribuyente_id > 0) ) {
			$contribuyente_id = 0;
		}
		$filter = trim($this->input->get_post('query'));
		$ret = $this->model->get_papeleta_list($filter, $contribuyente_id);	
		echo json_encode($ret);
	}

}
?>