<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpWord\TemplateProcessor;

class Tdr extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_tdr','model');
		$this->load->library('upload');
		//sys_session_hasRoleOrDie('tdr');
	}

	public function index() {
		$data = array();
		$this->load->view('v_tdr', $data);
	}

	public function getList() {
		$search_by = $this->input->get('search_by');
		$search_text = $this->input->get('search_text');
		$p_tipo_doc_id = $this->input->get('tipo_doc_id');
		$pagination_size = $this->input->get('limit');
		$pagination_start = $this->input->get('start');
		$ret = $this->model->get_list($search_by, $search_text, $p_tipo_doc_id, $pagination_size, $pagination_start);
		/*$rows = $ret['data'];
		foreach ($rows as $i=>$r) {
			//$ret['data'][$i]->oc_anio_numero = $r->oc_anio.'-'.$r->oc_numero;
		}*/
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
		//sys_session_hasRoleOrDie('rh.tdr.add, rh.tdr.update');
		$data = array(
			'tipo_doc_id'=>$this->input->post('tipo_doc_id'),
  			'tipo_permiso_id'=>$this->input->post('tipo_permiso_id'),
			'tipo_doc_requisito_desc'=>(trim($this->input->post('tipo_doc_requisito_desc'))),
			'tipo_doc_requisito_requerido_flag'=>$this->input->post('tipo_doc_requisito_requerido_flag'),
			'tipo_doc_requisito_pdf_flag'=>$this->input->post('tipo_doc_requisito_pdf_flag'),
			'tipo_doc_requisito_numero_flag'=>to_upper($this->input->post('tipo_doc_requisito_numero_flag')),
			'tipo_doc_requisito_keyname'=>strtolower($this->input->post('tipo_doc_requisito_keyname')),
			'tipo_doc_requisito_index'=>$this->input->post('tipo_doc_requisito_index'),
			'tipo_doc_requisito_estado'=>$this->input->post('tipo_doc_requisito_estado')
		);

		if ( $data['tipo_doc_id']=='' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Tipo de Documento",
				'target_id'=>'tdr_form_tipo_doc_id_field'
			)));
		}

		if ( !($data['tipo_permiso_id'] > 0) ) {
			$data['tipo_permiso_id'] = null;
		}

		$keyname_count = $this->db
		->select('COUNT(*) AS value')
		->from('public.tipo_doc_requisito')
		->where('tipo_doc_id', $data['tipo_doc_id'])
		->where('tipo_permiso_id', $data['tipo_permiso_id'])
		->where('tipo_doc_requisito_keyname <>', '')
		->where('tipo_doc_requisito_keyname', $data['tipo_doc_requisito_keyname'])
		->get()->row();
		if ($keyname_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El nombre clave ya existe. Los nombres claves no se deben repetir por tipo de documento.",
				'target_id'=>'tdr_form_tipo_doc_requisito_keyname_field'
			)));
		}

		if ( $data['tipo_doc_requisito_desc'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la descripcion",
				'target_id'=>'tdr_form_tipo_doc_requisio_desc_field'
			)));
		}

		if ( $data['tipo_doc_requisito_requerido_flag'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique un valor valido (Si o No)",
				'target_id'=>'tdr_form_tipo_doc_requisio_requerido_flag_field'
			)));
		}

		if ( $data['tipo_doc_requisito_pdf_flag'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique un valor valido (Si o No)",
				'target_id'=>'tdr_form_tipo_doc_requisito_pdf_flag_field'
			)));
		}
		
		if ( $data['tipo_doc_requisito_numero_flag'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique un valor valido (Si o No)",
				'target_id'=>'tdr_form_tipo_doc_requisito_numero_flag_field'
			)));
		}

		if ( !($data['tipo_doc_requisito_index'] > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el valor del indice mayor que zero.",
				'target_id'=>'tdr_form_tipo_doc_requisito_index_field'
			)));
		}

		if ( $data['tipo_doc_requisito_estado'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el estado",
				'target_id'=>'tdr_form_tipo_doc_requisito_estado_field'
			)));
		}

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
		//sys_session_hasRoleOrDie('tdr.update');
		$data = array(
			'tipo_doc_requisito_id'=>$this->input->post('tipo_doc_requisito_id'),
			'tipo_doc_id'=>$this->input->post('tipo_doc_id'),
  			'tipo_permiso_id'=>$this->input->post('tipo_permiso_id'),
			'tipo_doc_requisito_desc'=>(trim($this->input->post('tipo_doc_requisito_desc'))),
			'tipo_doc_requisito_requerido_flag'=>$this->input->post('tipo_doc_requisito_requerido_flag'),
			'tipo_doc_requisito_pdf_flag'=>$this->input->post('tipo_doc_requisito_pdf_flag'),
			'tipo_doc_requisito_numero_flag'=>to_upper($this->input->post('tipo_doc_requisito_numero_flag')),
			'tipo_doc_requisito_keyname'=>strtolower($this->input->post('tipo_doc_requisito_keyname')),
			'tipo_doc_requisito_index'=>$this->input->post('tipo_doc_requisito_index'),
			'tipo_doc_requisito_estado'=>$this->input->post('tipo_doc_requisito_estado')
		);

		if ( $data['tipo_doc_id']=='' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Tipo de Documento",
				'target_id'=>'tdr_form_tipo_doc_id_field'
			)));
		}

		if ( !($data['tipo_permiso_id'] > 0) ) {
			$data['tipo_permiso_id'] = null;
		}

		$keyname_count = $this->db
		->select('COUNT(*) AS value')
		->from('public.tipo_doc_requisito')
		->where('tipo_doc_id', $data['tipo_doc_id'])
		->where('tipo_permiso_id', $data['tipo_permiso_id'])
		->where('tipo_doc_requisito_keyname <>', '')
		->where('tipo_doc_requisito_keyname', $data['tipo_doc_requisito_keyname'])
		->where('tipo_doc_requisito_id <>', $data['tipo_doc_requisito_id'])
		->get()->row();
		if ($keyname_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El nombre clave ya existe. Los nombres claves no se deben repetir por tipo de documento.",
				'target_id'=>'tdr_form_tipo_doc_requisito_keyname_field'
			)));
		}

		if ( $data['tipo_doc_requisito_desc'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la descripcion",
				'target_id'=>'tdr_form_tipo_doc_requisio_desc_field'
			)));
		}

		if ( $data['tipo_doc_requisito_requerido_flag'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique un valor valido (Si o No)",
				'target_id'=>'tdr_form_tipo_doc_requisio_requerido_flag_field'
			)));
		}

		if ( $data['tipo_doc_requisito_pdf_flag'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique un valor valido (Si o No)",
				'target_id'=>'tdr_form_tipo_doc_requisito_pdf_flag_field'
			)));
		}
		
		if ( $data['tipo_doc_requisito_numero_flag'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique un valor valido (Si o No)",
				'target_id'=>'tdr_form_tipo_doc_requisito_numero_flag_field'
			)));
		}

		if ( !($data['tipo_doc_requisito_index'] > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el valor del indice mayor que zero.",
				'target_id'=>'tdr_form_tipo_doc_requisito_index_field'
			)));
		}

		if ( $data['tipo_doc_requisito_estado'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el estado",
				'target_id'=>'tdr_form_tipo_doc_requisito_estado_field'
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
				'rowid'=>$data['tipo_doc_requisito_id']
			)));
			
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion.".(isset($error)?'<br>$error':'')
			)));
		}
	}

	public function Delete() {
		//sys_session_hasRoleOrDie('rh.tdr.modify');
		$p_tipo_doc_requisito_id = $this->input->post('tipo_doc_requisito_id');

		$doc_requisito_count = $this->db->select('COUNT(*) AS value')->where('tipo_doc_requisito_id', $p_tipo_doc_requisito_id)->get('public.doc_requisito')->row();
		if ($doc_requisito_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"No es posible eliminar este registro porque se esta usando ({$doc_requisito_count->value})."
			)));
		}

		$result = $this->model->delete($p_tipo_doc_requisito_id);

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

	public function getTipoDocList () {
		$ret = $this->model->get_tipo_doc_list();
		echo json_encode($ret);
	}

	public function getTipoPermisoList () {
		$ret = $this->model->get_tipo_permiso_list();
		echo json_encode($ret);
	}

	public function getKeynameList () {
		echo json_encode(
			array(
				'data'=>array(

					array('id'=>'solicitud', 'desc'=>'solicitud'),
					array('id'=>'dni', 'desc'=>'dni'),
					array('id'=>'recibo', 'desc'=>'recibo')
				)
			)
		);
	}
}
?>