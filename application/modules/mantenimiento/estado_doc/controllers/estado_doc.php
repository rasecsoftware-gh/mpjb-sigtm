<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpWord\TemplateProcessor;

class Estado_Doc extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_estado_doc','model');
		$this->load->library('upload');
		//sys_session_hasRoleOrDie('estado_doc');
	}

	public function index() {
		$data = array();
		$this->load->view('v_estado_doc', $data);
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
		//sys_session_hasRoleOrDie('rh.estado_doc.add, rh.estado_doc.update');
		$data = array(
			'tipo_doc_id'=>$this->input->post('tipo_doc_id'),
			'estado_doc_desc'=>(trim($this->input->post('estado_doc_desc'))),
			'estado_doc_requisito_requerido_flag'=>$this->input->post('estado_doc_requisito_requerido_flag'),
			'estado_doc_color'=>$this->input->post('estado_doc_color'),
			'estado_doc_correlativo_flag'=>to_upper($this->input->post('estado_doc_correlativo_flag')),
			'estado_doc_final_flag'=>$this->input->post('estado_doc_final_flag'),
			'estado_doc_generar_pdf_flag'=>$this->input->post('estado_doc_generar_pdf_flag'),
			'estado_doc_modificar_flag'=>$this->input->post('estado_doc_modificar_flag'),
			'estado_doc_index'=>$this->input->post('estado_doc_index'),
			'estado_doc_estado'=>$this->input->post('estado_doc_estado')
		);

		if ( $data['tipo_doc_id']=='' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Tipo de Documento",
				'target_id'=>'estado_doc_form_tipo_doc_id_field'
			)));
		}

		if ( $data['estado_doc_desc'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la descripcion",
				'target_id'=>'estado_doc_form_estado_doc_desc_field'
			)));
		}

		$desc_count = $this->db
		->select('COUNT(*) AS value')
		->from('public.estado_doc')
		->where('tipo_doc_id', $data['tipo_doc_id'])
		->where('UPPER(estado_doc_desc)', to_upper($data['estado_doc_desc']))
		->get()->row();
		if ($desc_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"La descripcion del estado ya existe. Los nombres de estado no se deben repetir por tipo de documento.",
				'target_id'=>'estado_doc_form_estado_doc_desc_field'
			)));
		}

		if ( $data['estado_doc_color'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el color del estado",
				'target_id'=>'estado_doc_form_estado_doc_color_field'
			)));
		}

		if ( !($data['estado_doc_index'] > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el numero de orden mayor que zero.",
				'target_id'=>'estado_doc_form_estado_doc_index_field'
			)));
		}

		if ( $data['estado_doc_estado'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el estado",
				'target_id'=>'estado_doc_form_estado_doc_estado_field'
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
		//sys_session_hasRoleOrDie('estado_doc.update');
		$data = array(
			'estado_doc_id'=>$this->input->post('estado_doc_id'),
			'tipo_doc_id'=>$this->input->post('tipo_doc_id'),
			'estado_doc_desc'=>(trim($this->input->post('estado_doc_desc'))),
			'estado_doc_requisito_requerido_flag'=>$this->input->post('estado_doc_requisito_requerido_flag'),
			'estado_doc_color'=>$this->input->post('estado_doc_color'),
			'estado_doc_correlativo_flag'=>to_upper($this->input->post('estado_doc_correlativo_flag')),
			'estado_doc_final_flag'=>$this->input->post('estado_doc_final_flag'),
			'estado_doc_generar_pdf_flag'=>$this->input->post('estado_doc_generar_pdf_flag'),
			'estado_doc_modificar_flag'=>$this->input->post('estado_doc_modificar_flag'),
			'estado_doc_index'=>$this->input->post('estado_doc_index'),
			'estado_doc_estado'=>$this->input->post('estado_doc_estado')
		);

		if ( $data['tipo_doc_id']=='' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Tipo de Documento",
				'target_id'=>'estado_doc_form_tipo_doc_id_field'
			)));
		}

		if ( !($data['tipo_permiso_id'] > 0) ) {
			$data['tipo_permiso_id'] = null;
		}

		$keyname_count = $this->db
		->select('COUNT(*) AS value')
		->from('public.estado_doc')
		->where('tipo_doc_id', $data['tipo_doc_id'])
		->where('tipo_permiso_id', $data['tipo_permiso_id'])
		->where('estado_doc_keyname <>', '')
		->where('estado_doc_keyname', $data['estado_doc_keyname'])
		->where('estado_doc_id <>', $data['estado_doc_id'])
		->get()->row();
		if ($keyname_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El nombre clave ya existe. Los nombres claves no se deben repetir por tipo de documento.",
				'target_id'=>'estado_doc_form_estado_doc_keyname_field'
			)));
		}

		if ( $data['estado_doc_desc'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la descripcion",
				'target_id'=>'estado_doc_form_estado_doc_desc_field'
			)));
		}

		if ( $data['estado_doc_requerido_flag'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique un valor valido (Si o No)",
				'target_id'=>'estado_doc_form_estado_doc_requerido_flag_field'
			)));
		}

		if ( $data['estado_doc_pdf_flag'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique un valor valido (Si o No)",
				'target_id'=>'estado_doc_form_estado_doc_pdf_flag_field'
			)));
		}
		
		if ( $data['estado_doc_numero_flag'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique un valor valido (Si o No)",
				'target_id'=>'estado_doc_form_estado_doc_numero_flag_field'
			)));
		}

		if ( !($data['estado_doc_index'] > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el valor del indice mayor que zero.",
				'target_id'=>'estado_doc_form_estado_doc_index_field'
			)));
		}

		if ( $data['estado_doc_estado'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el estado",
				'target_id'=>'estado_doc_form_estado_doc_estado_field'
			)));
		}if ( $data['estado_doc_desc'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la descripcion",
				'target_id'=>'estado_doc_form_estado_doc_desc_field'
			)));
		}

		$desc_count = $this->db
		->select('COUNT(*) AS value')
		->from('public.estado_doc')
		->where('tipo_doc_id', $data['tipo_doc_id'])
		->where('UPPER(estado_doc_desc)', to_upper($data['estado_doc_desc']))
		->where('estado_doc_id <>', $data['estado_doc_id'])
		->get()->row();
		if ($desc_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"La descripcion del estado ya existe. Los nombres de estado no se deben repetir por tipo de documento.",
				'target_id'=>'estado_doc_form_estado_doc_desc_field'
			)));
		}

		if ( $data['estado_doc_color'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el color del estado",
				'target_id'=>'estado_doc_form_estado_doc_color_field'
			)));
		}

		if ( !($data['estado_doc_index'] > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el numero de orden mayor que zero.",
				'target_id'=>'estado_doc_form_estado_doc_index_field'
			)));
		}

		if ( $data['estado_doc_estado'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el estado",
				'target_id'=>'estado_doc_form_estado_doc_estado_field'
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
				'rowid'=>$data['estado_doc_id']
			)));
			
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion.".(isset($error)?'<br>$error':'')
			)));
		}
	}

	public function Delete() {
		//sys_session_hasRoleOrDie('rh.estado_doc.modify');
		$p_estado_doc_id = $this->input->post('estado_doc_id');

		$doc_estado_count = $this->db->select('COUNT(*) AS value')->where('estado_doc_id', $p_estado_doc_id)->get('public.doc_estado')->row();
		if ($doc_requisito_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"No es posible eliminar este registro porque se esta usando ({$doc_estado_count->value})."
			)));
		}

		$result = $this->model->delete($p_estado_doc_id);

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

	public function getColorList () {
		echo json_encode(
			array(
				'data'=>array(
					array('id'=>'black', 'desc'=>'Negro'),
					array('id'=>'gray', 'desc'=>'Gris'),
					array('id'=>'blue', 'desc'=>'Azul'),
					array('id'=>'orange', 'desc'=>'Naranja'),
					array('id'=>'green', 'desc'=>'Verde'),
					array('id'=>'red', 'desc'=>'Rojo'),
					array('id'=>'purple', 'desc'=>'Morado')
				)
			)
		);
	}
}
?>