<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpWord\TemplateProcessor;

class LC extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_lc', 'model');
		$this->load->library('upload');
		//sys_session_hasRoleOrDie('LC');
	}

	public function index() {
		$data = array();
		$this->load->view('v_lc', $data);
	}

	public function getList() {
		$search_by = $this->input->get('search_by');
		$search_text = $this->input->get('search_text');
		$p_anio = date('Y'); //$this->input->get('anio');
		$pagination_size = $this->input->get('limit');
		$pagination_start = $this->input->get('start');
		$ret = $this->model->get_list($search_by, $search_text, $p_anio, $pagination_size, $pagination_start);
		foreach ($ret['data'] as $i=>$r) {
			$ret['data'][$i]->contribuyente_nomape = $r->contribuyente_nombres.' '.$r->contribuyente_apellidos;
		}
		echo json_encode($ret);
	}

	public function getNewRow () {
		$row = $this->model->get_new_row();
		$row['operation'] = 'new';
		
		// SET first default template
		$plantilla = $this->db
		->where('tipo_doc_id', 'LC')
		->where('plantilla_estado', 'A')
		->order_by('plantilla_id', 'ASC')
		->get('public.plantilla')->row();

		if ( is_null($plantilla) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"No existe una plantilla para este tipo de documento, revise la configuracion de plantillas de documentos por favor."
			)));
		}
		$row['plantilla_id'] = $plantilla->plantilla_id;

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
		//sys_session_hasRoleOrDie('rh.lc.add, rh.lc.update');
		$data = array(
			'tipo_doc_id'=>'LC',
  			'lc_anio'=>trim($this->input->post('lc_anio')),
			'lc_numero'=>trim($this->input->post('lc_numero')),
			'contribuyente_id'=>$this->input->post('contribuyente_id'),
			'lc_fecha'=>$this->input->post('lc_fecha'),
			'lc_clase'=>to_upper(trim($this->input->post('lc_clase'))),
			'lc_categoria'=>to_upper(trim($this->input->post('lc_categoria'))),
			'lc_resolucion'=>to_upper(trim($this->input->post('lc_resolucion'))),
			'lc_fecha_exp'=>trim($this->input->post('lc_fecha_exp')),
			'lc_fecha_ven'=>trim($this->input->post('lc_fecha_ven')),
			'lc_codigo'=>trim($this->input->post('lc_codigo')),
			'lc_grupo_s'=>trim($this->input->post('lc_grupo_s')),
			'lc_restricciones'=>to_upper(trim($this->input->post('lc_restricciones'))),
			'lc_observacion'=>trim($this->input->post('lc_observacion')),
			'plantilla_id'=>$this->input->post('plantilla_id')
		);

		if ($data['lc_anio']=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Año de Documento",
				'target_id'=>'lc_form_lc_anio_field'
			)));
		}

		if ($data['lc_numero']=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Numero de Documento",
				'target_id'=>'lc_form_lc_numero_field'
			)));
		}

		$anio_numero_count = $this->db
		->select('COUNT(*) AS value')
		->from('public.lc')
		->where('lc_anio', $data['lc_anio'])
		->where('lc_numero', $data['lc_numero'])
		->get()->row();
		if ($anio_numero_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El Numero de Registro ya existe.",
				'target_id'=>'lc_form_lc_numero_field'
			)));
		}

		if ( !($data['contribuyente_id'] > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Contribuyente",
				'target_id'=>'lc_form_contribuyente_id_field'
			)));
		}

		if (trim($data['lc_fecha'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la fecha",
				'target_id'=>'lc_form_lc_fecha_field'
			)));
		}

		if ( !($data['plantilla_id'] > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique una plantilla para la generacion del PDF."
			)));
		}

		if ( trim($data['lc_clase'])=='' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la Clase",
				'target_id'=>'lc_form_lc_clase_field'
			)));
		}

		if ( trim($data['lc_categoria'])=='' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la Categoria",
				'target_id'=>'lc_form_lc_categoria_field'
			)));
		}

		if ( trim($data['lc_fecha_exp'])=='' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la Fecha de Expedicion",
				'target_id'=>'lc_form_lc_fecha_exp_field'
			)));
		}

		if ( trim($data['lc_fecha_ven'])=='' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la fecha de vencimiento",
				'target_id'=>'lc_form_lc_fecha_ven_field'
			)));
		}

		// SET first default state
		$estado_doc = $this->db
		->where('tipo_doc_id', 'LC')
		->order_by('estado_doc_index', 'ASC')
		->get('public.estado_doc')->row();

		if ( is_null($estado_doc) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"No existe un estado definido para este tipo de documento, revise la configuracion de estados de documentos por favor."
			)));
		}

		try {
			$result = $this->model->add($data, $estado_doc->estado_doc_id);
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
	}

	public function Update() {
		//sys_session_hasRoleOrDie('lc.update');
		$data = array(
			'lc_id'=>$this->input->post('lc_id'),
  			'lc_anio'=>trim($this->input->post('lc_anio')),
			'lc_numero'=>trim($this->input->post('lc_numero')),
			'contribuyente_id'=>$this->input->post('contribuyente_id'),
			'lc_fecha'=>$this->input->post('lc_fecha'),
			'lc_clase'=>to_upper(trim($this->input->post('lc_clase'))),
			'lc_categoria'=>to_upper(trim($this->input->post('lc_categoria'))),
			'lc_resolucion'=>to_upper(trim($this->input->post('lc_resolucion'))),
			'lc_fecha_exp'=>trim($this->input->post('lc_fecha_exp')),
			'lc_fecha_ven'=>trim($this->input->post('lc_fecha_ven')),
			'lc_codigo'=>trim($this->input->post('lc_codigo')),
			'lc_grupo_s'=>trim($this->input->post('lc_grupo_s')),
			'lc_restricciones'=>to_upper(trim($this->input->post('lc_restricciones'))),
			'lc_observacion'=>trim($this->input->post('lc_observacion')),
			'plantilla_id'=>$this->input->post('plantilla_id')
		);
		
		$doc = $this->model->get_row($data['lc_id']);

		if ( $doc->estado_doc_modificar_flag == 'N' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"No es posible modificar el documento en el estado actual."
			)));
		}

		if ($data['lc_anio']=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Año de Documento",
				'target_id'=>'lc_form_lc_anio_field'
			)));
		}

		if ($data['lc_numero']=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Numero de Documento",
				'target_id'=>'lc_form_lc_numero_field'
			)));
		}

		$anio_numero_count = $this->db
		->select('COUNT(*) AS value')
		->from('public.lc')
		->where('lc_anio', $data['lc_anio'])
		->where('lc_numero', $data['lc_numero'])
		->where('lc_id <>', $data['lc_id'])
		->get()->row();
		if ($anio_numero_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El Numero de Registro ya existe.",
				'target_id'=>'lc_form_lc_numero_field'
			)));
		}

		if ( !($data['contribuyente_id'] > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Contribuyente",
				'target_id'=>'lc_form_contribuyente_id_field'
			)));
		}

		if (trim($data['lc_fecha'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la fecha",
				'target_id'=>'lc_form_lc_fecha_field'
			)));
		}

		if ( !($data['plantilla_id'] > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique una plantilla para la generacion del PDF."
			)));
		}

		if ( trim($data['lc_clase'])=='' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la Clase",
				'target_id'=>'lc_form_lc_clase_field'
			)));
		}

		if ( trim($data['lc_categoria'])=='' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la Categoria",
				'target_id'=>'lc_form_lc_categoria_field'
			)));
		}

		/*if ( trim($data['lc_resolucion'])=='' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la resolucion",
				'target_id'=>'lc_form_lc_resolucion_field'
			)));
		}*/

		if ( trim($data['lc_fecha_exp'])=='' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la Fecha de Expedicion",
				'target_id'=>'lc_form_lc_fecha_exp_field'
			)));
		}

		if ( trim($data['lc_fecha_ven'])=='' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la fecha de vencimiento",
				'target_id'=>'lc_form_lc_fecha_ven_field'
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
				'msg'=>"Se guardo satisfactoriamente"
			)));
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion.".(isset($error)?'<br>$error':'')
			)));
		}
	}

	public function Delete () {
		//sys_session_hasRoleOrDie('rh.lc.modify');
		$p_lc_id = $this->input->post('lc_id');
		/*
		$cat_count = $this->db->select('COUNT(*) AS value')->where('lc_id', $p_lc_id)->get('public.cat')->row();
		if ($cat_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El lc tiene Constancias de Autorizacion Temporal registrado(s)."
			)));
		}
		*/

		$result = $this->model->delete($p_lc_id);

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
			$ret['data'][$i]->contribuyente_nomape = $r->contribuyente_nombres.' '.$r->contribuyente_apellidos;
		}
		echo json_encode($ret);
	}

	public function getPlantillaList () {
		$ret = $this->model->get_plantilla_list();
		echo json_encode($ret);
	}

	public function getEstadoDocList () {
		$ret = $this->model->get_estado_doc_list();
		echo json_encode($ret);
	}

	public function getDocRequisitoList () {
		$doc_id = $this->input->get('doc_id');
		$ret = $this->model->get_doc_requisito_list($doc_id);
		echo json_encode($ret);
	}

	public function addDocRequisito() {
		//sys_session_hasRoleOrDie('rh.contrato.modify');
		//var_dump($_FILES);
		$upload_path = 'dbfiles/public.doc_requisito/';
		$data = array(
			'tipo_doc_id'=>'LC',
			'doc_id'=>$this->input->post('doc_id'),
			'tipo_doc_requisito_id'=>$this->input->post('tipo_doc_requisito_id'),
			'doc_requisito_fecha'=>$this->input->post('doc_requisito_fecha'),
			'doc_requisito_numero'=>to_upper($this->input->post('doc_requisito_numero'))
		);

		if ( !($data['tipo_doc_requisito_id'] > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Tipo de Documento a Adjuntar."
			)));
		}
		
		$tipo_doc_requisito = $this->db->where('tipo_doc_requisito_id', $data['tipo_doc_requisito_id'])->get('public.tipo_doc_requisito')->row();
		
		// revisar si el tipo_doc_requisito ya existe
		$tdr_count = $this->db
		->select('COUNT(*) AS value')
		->where('tipo_doc_id', $data['tipo_doc_id'])
		->where('doc_id', $data['doc_id'])
		->where('tipo_doc_requisito_id', $data['tipo_doc_requisito_id'])
		->get('public.doc_requisito')->row();

		if ( $tdr_count->value > 0 ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Ya existe un(a) {$tipo_doc_requisito->tipo_doc_requisito_desc} registrado."
			)));
		}

		$uploaded = false;
		if (isset($_FILES['doc_requisito_file']) && $_FILES['doc_requisito_file']['name'] != '') {
			$config['upload_path'] = $upload_path;
			$config['allowed_types'] = 'pdf'; //gif|jpg|png
			$this->upload->initialize($config);
			if ($this->upload->do_upload('doc_requisito_file')) {
				$data['doc_requisito_pdf'] = $this->upload->data('file_name');
				$uploaded = true;
			} else {
				die(json_encode(array(
					'success'=>false,
					'msg'=>'UPLOAD: '.$this->upload->display_errors()
				)));
			}	
		} 
		
		if ( $tipo_doc_requisito->tipo_doc_requisito_pdf_flag == 'S' && !$uploaded) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Este documento requiere que se adjunte el escaneado en formato PDF."
			)));			
		}

		if ( $data['doc_requisito_fecha'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la fecha",
				'target_id'=>'lc_doc_requisito_form_doc_requisito_fecha_field'
			)));
		}

		if ( $data['doc_requisito_numero'] == '' && $tipo_doc_requisito->tipo_doc_requisito_numero_flag == 'S' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Numero",
				'target_id'=>'lc_doc_requisito_form_doc_requisito_numero_field'
			)));
		}

		try {
			//unset($data['plantilla_id']);
			$result = $this->model->add_doc_requisito($data);
		} catch (Exception $ex) {
			$error = $ex->getMessage();
		}

		if ($result !== false) {
			die(json_encode(array(
				'success'=>true,
				'msg'=>"Se guardo satisfactoriamente",
				'rowid'=>$result
			)));
			
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion.".(isset($error)?'<br>$error':'')
			)));
		}
	}

	public function updateDocRequisito() {
		//sys_session_hasRoleOrDie('rh.contrato.modify');
		//var_dump($_FILES);
		$upload_path = 'dbfiles/public.doc_requisito/';
		$data = array(
			'doc_requisito_id'=>$this->input->post('doc_requisito_id'),
			'doc_requisito_fecha'=>$this->input->post('doc_requisito_fecha'),
			'doc_requisito_numero'=>to_upper($this->input->post('doc_requisito_numero'))
		);
		$doc_requisito = $this->db->where('doc_requisito_id', $data['doc_requisito_id'])->get('public.doc_requisito')->row();
		$p_tipo_doc_requisito_id = $this->input->post('tipo_doc_requisito_id');
		if ( !($p_tipo_doc_requisito_id > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Tipo de Documento a Adjuntar."
			)));
		}

		$uploaded = false;
		if (isset($_FILES['doc_requisito_file']) && $_FILES['doc_requisito_file']['name'] != '') {
			$config['upload_path'] = $upload_path;
			$config['allowed_types'] = 'pdf'; //gif|jpg|png
			$this->upload->initialize($config);
			if ($this->upload->do_upload('doc_requisito_file')) {
				$data['doc_requisito_pdf'] = $this->upload->data('file_name');
				$uploaded = true;
			} else {
				die(json_encode(array(
					'success'=>false,
					'msg'=>'UPLOAD: '.$this->upload->display_errors()
				)));
			}	
		}
		
		$tipo_doc_requisito = $this->db->where('tipo_doc_requisito_id', $p_tipo_doc_requisito_id)->get('public.tipo_doc_requisito')->row();
		if ( $tipo_doc_requisito->tipo_doc_requisito_pdf_flag == 'S' && !$uploaded && $doc_requisito->doc_requisito_pdf == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El archivo PDF es requerido para este documento."
			)));		
		}
		

		if ( $data['doc_requisito_fecha'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la fecha",
				'target_id'=>'lc_doc_requisito_form_doc_requisito_fecha_field'
			)));
		}

		if ( $data['doc_requisito_numero'] == '' && $tipo_doc_requisito->tipo_doc_requisito_numero_flag == 'S' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Numero",
				'target_id'=>'lc_doc_requisito_form_doc_requisito_numero_field'
			)));
		}

		try {
			//unset($data['plantilla_id']);
			$result = $this->model->update_doc_requisito($data);
		} catch (Exception $ex) {
			$error = $ex->getMessage();
		}

		if ($result !== false) {
			die(json_encode(array(
				'success'=>true,
				'msg'=>"Se guardo satisfactoriamente",
				'rowid'=>$result
			)));
			
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion.".(isset($error)?'<br>$error':'')
			)));
		}
	}

	public function deleteDocRequisito() {
		//sys_session_hasRoleOrDie('rh.lc.modify');
		$p_doc_requisito_id = intval($this->input->post('doc_requisito_id'));

		if ( !($p_doc_requisito_id > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"No se ha especificado un id valido del documento."
			)));
		}

		$doc_requisito = $this->db->where('doc_requisito_id', $p_doc_requisito_id)->get('public.doc_requisito')->row();
		
		$doc = $this->model->get_row($doc_requisito->doc_id);

		if (to_upper($doc->estado_doc_desc) != 'REGISTRADO') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"No es posible modificar el documento en el estado '{$doc->estado_doc_desc}' actual."
			)));	
		}

		$result = $this->model->delete_doc_requisito($p_doc_requisito_id);

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

	public function getDocEstadoList () {
		$p_doc_id = $this->input->get('doc_id');
		$ret = $this->model->get_doc_estado_list($p_doc_id);
		echo json_encode($ret);
	}

	public function addDocEstado() {
		//sys_session_hasRoleOrDie('rh.contrato.modify');
		//var_dump($_FILES);
		$upload_path = 'dbfiles/public.doc_requisito/';
		$data = array(
			'tipo_doc_id'=>'LC',
			'doc_id'=>$this->input->post('doc_id'),
			'estado_doc_id'=>$this->input->post('estado_doc_id'),
			'doc_estado_fecha'=>$this->input->post('doc_estado_fecha'),
			'doc_estado_obs'=>to_upper($this->input->post('doc_estado_obs'))
		);

		if ( !($data['doc_id'] > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el id del documento."
			)));
		}
		$doc = $this->model->get_row($data['doc_id']);

		if ( !($data['estado_doc_id'] > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el nuevo estado del documento."
			)));
		}
		
		$estado_doc = $this->db->where('estado_doc_id', $data['estado_doc_id'])->get('public.estado_doc')->row();
		
		// revisar si el estado_doc ya existe
		$estado_doc_count = $this->db
		->select('COUNT(*) AS value')
		->where('tipo_doc_id', $data['tipo_doc_id'])
		->where('doc_id', $data['doc_id'])
		->where('estado_doc_id', $data['estado_doc_id'])
		->get('public.doc_estado')->row();

		if ( $estado_doc_count->value > 0 ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El estado {$estado_doc->estado_doc_desc}, ya se encuentra registrado."
			)));
		}

		/*if ( $data['doc_requisito_fecha'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la fecha",
				'target_id'=>'lc_doc_requisito_form_doc_requisito_fecha_field'
			)));
		}*/
		$data['doc_estado_fecha'] = date('d/m/Y H:i:s');

		if ($estado_doc->estado_doc_requisito_requerido_flag == 'S') {
			$tipo_doc_requisito_requerido_count = $this->db
			->select('COUNT(*) AS value')
			->where('tipo_doc_id', $data['tipo_doc_id'])
			->where('tipo_doc_requisito_estado', 'A')
			->where('tipo_doc_requisito_requerido_flag', 'S')
			->get('public.tipo_doc_requisito')->row();

			$doc_requisito_requerido_count = $this->db
			->select('COUNT(*) AS value')
			->from('public.doc_requisito AS dr')
			->join('public.tipo_doc_requisito AS tdr', 'tdr.tipo_doc_requisito_id = dr.tipo_doc_requisito_id')
			->where('dr.tipo_doc_id', $data['tipo_doc_id'])
			->where('dr.doc_id', $data['doc_id'])
			->where('tdr.tipo_doc_requisito_requerido_flag', 'S')
			->get()->row();

			if ( $doc_requisito_requerido_count->value < $tipo_doc_requisito_requerido_count->value ) {
				die(json_encode(array(
					'success'=>false,
					'msg'=>"No se han registrado todos los documentos requeridos."
				)));	
			}
		}

		try {
			//unset($data['plantilla_id']);
			$result = $this->model->add_doc_estado($data);
			$this->model->update(
				array(
					'lc_id'=>$data['doc_id'],
					'doc_estado_id'=>$result // nuevo id de estado
				)
			);
		} catch (Exception $ex) {
			$error = $ex->getMessage();
		}

		if ($result !== false) {
			die(json_encode(array(
				'success'=>true,
				'msg'=>"Se guardo satisfactoriamente",
				'rowid'=>$result
			)));
			
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion.".(isset($error)?'<br>$error':'')
			)));
		}
	}	

	public function deleteDocEstado() {
		//sys_session_hasRoleOrDie('rh.lc.modify');
		$p_doc_estado_id = intval($this->input->post('doc_estado_id'));

		if ( !($p_doc_estado_id > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"No se ha especificado un id valido del estado a revertir."
			)));
		}

		$doc_estado = $this->db->where('doc_estado_id', $p_doc_estado_id)->get('public.doc_estado')->row();

		$result = $this->model->delete_doc_estado($p_doc_estado_id);
		if ($result) {
			$doc_estado_anterior = $this->db
			->from('public.doc_estado AS de')
			->join('public.estado_doc AS ed', 'ed.estado_doc_id = de.estado_doc_id', 'inner')
			->where('de.tipo_doc_id', $doc_estado->tipo_doc_id)
			->where('de.doc_id', $doc_estado->doc_id)
			->order_by('ed.estado_doc_index', 'DESC')
			->get()->row();
			$this->model->update(
				array(
					'lc_id'=>$doc_estado->doc_id,
					'doc_estado_id'=>$doc_estado_anterior->doc_estado_id
				)
			);
		}

		if ($result !== false) {
			die(json_encode(array(
				'success'=>true,
				'msg'=>"Se cancelar el estado satisfactoriamente."
			)));
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion."
			)));
		}
	}

	private function _generarPDF($doc_id) {
		$c = $this->model->get_row($doc_id, 'array');
		$td = $this->db->where('tipo_doc_id', $c['tipo_doc_id'])->get('public.tipo_doc')->row();
		$p = $this->db->where('plantilla_id', $c['plantilla_id'])->get('public.plantilla')->row();

		$config_list = $this->db->select('config_id, config_valor')->get('sys.config')->result();
		$config = array();
		foreach ($config_list as $r) {
			$config[$r->config_id] = $r->config_valor;
		}

		
		$c['tipo_doc_desc'] = to_upper($c['tipo_doc_desc']);

		// 02/06/2018
		$c['lc_fecha_dia_numero'] = substr($c['lc_fecha'], 0, 2);
		$c['lc_fecha_mes_nombre'] = month_name(intval(substr($c['lc_fecha'], 3, 2)));
		$c['lc_fecha_anio'] = substr($c['lc_fecha'], 6, 4);
		// resolucion
		$c['lc_resolucion_fecha_dia'] = substr($c['lc_resolucion_fecha'], 0, 2);
		$c['lc_resolucion_fecha_mes'] = substr($c['lc_resolucion_fecha'], 3, 2);
		$c['lc_resolucion_fecha_anio'] = substr($c['lc_resolucion_fecha'], 6, 4);
		$c['lc_resolucion_fecha_desc'] = $c['lc_resolucion_fecha_dia'].' de '.month_name(intval($c['lc_resolucion_fecha_mes'])).' del '.$c['lc_resolucion_fecha_anio'];

		// documentos adjuntos requeridos con keyname
		$doc_requisito_list = $this->model->get_doc_requisito_list($doc_id)['data'];
		foreach ($doc_requisito_list as $dr) {
			if ($dr->tipo_doc_requisito_keyname != '') {
				$prefijo = 'dr_'.$dr->tipo_doc_requisito_keyname;
				$c["{$prefijo}_fecha_dia"] = substr($dr->doc_requisito_fecha, 0, 2);
				$c["{$prefijo}_fecha_mes"] = substr($dr->doc_requisito_fecha, 3, 2);
				$c["{$prefijo}_fecha_anio"] = substr($dr->doc_requisito_fecha, 6, 4);
				$c["{$prefijo}_fecha_desc"] = $c["{$prefijo}_fecha_dia"].' de '.month_name(intval($c["{$prefijo}_fecha_mes"])).' del '.$c["{$prefijo}_fecha_anio"];
				$c["{$prefijo}_fecha"] = $dr->doc_requisito_fecha;
				$c["{$prefijo}_numero"] = $dr->doc_requisito_numero;
			}
		}
		
		
		$path_archivo = FCPATH.'dbfiles/public.plantilla/'.$p->plantilla_archivo;
		if ( !(file_exists($path_archivo) && $p->plantilla_archivo != '') ) {
			throw new Exception("El archivo de la plantilla ('{$p->plantilla_archivo}'), no existe o no es valido!.");
		}

		$t = new TemplateProcessor(FCPATH.'dbfiles/public.plantilla/'.$p->plantilla_archivo);
		$var_list = $t->getVariables();
	    foreach ($var_list as $key => $value) {
	        if (array_key_exists($value, $c)) {
	            $t->setValue($value, $c[$value]);
	        } elseif (array_key_exists($value, $config)) {
	        	$t->setValue($value, $config[$value]);
	        } elseif ($value=='no-tiene') {
	        	$t->setValue($value, '');
	        } else {
	        	throw new Exception("D{$doc_id}: Falta el parametro $value.");
	        }
	    }
	    // --- Guardamos el documento
	    $filename = strtolower($td->tipo_doc_id).'_'.$c['lc_anio'].'_'.$c['lc_numero'].'_'.microtime(true);
	    $t->saveAs("tmp/{$filename}.docx");
	    // to PDF
	    $word = new COM("Word.Application") or die ("MS Word: Could not initialise Object.");
	    $word->Visible = 0;
	    $word->DisplayAlerts = 0;
	    $r = $word->Documents->Open(FCPATH."tmp/{$filename}.docx");
	    $word->ActiveDocument->ExportAsFixedFormat(FCPATH."dbfiles/public.lc/{$filename}.pdf", 17, false, 0, 0, 0, 0, 7, true, true, 2, true, true, false);
	    $word->Quit(false);
	    unset($word);
	    return $filename.'.pdf';
	}

	public function generarPDF() {
		$p_doc_id = $this->input->post('doc_id');
		$doc = $this->model->get_row($p_doc_id);
		if ( $doc->estado_doc_generar_pdf_flag == 'S' ) {
			try {
				$filename = $this->_generarPDF($p_doc_id);	
				$this->model->update(
					array(
						'lc_id'=>$p_doc_id,
						'lc_pdf'=>$filename
					)
				);
				die(json_encode(array (
					'success'=>true,
					'msg'=>"Se genero el  PDF satisfactoriamente.",
					'filename'=>$filename
				)));
			} catch (Exception $ex) {
				die(json_encode(array (
					'success'=>false,
					'msg'=>$ex->getMessage()
				)));
			}
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"No es posible generar el PDF en el estado actual."
			)));
		}
	}

	public function printPreview() {
		//die($this->config->item('base_url'));
		//if (file_exists('tmp/archivo.txt')) { die(file_get_contents('tmp/archivo.txt')); } else { die('no'); }
		//die(FCPATH);
		$p_doc_id = $this->input->post('doc_id');
		$doc = $this->model->get_row($p_doc_id);
		$filename = $doc->lc_pdf;
		if (file_exists(FCPATH."dbfiles/public.lc/".$filename) && $filename != '') {
			$url = $this->config->item('base_url')."dbfiles/public.lc/{$filename}";
			echo "<embed src=\"{$url}\" type=\"application/pdf\" width=\"100%\" height=\"100%\"></embed>";
		} else {
			echo "No es posible mostrar el archivo '{$filename}'.";
		}
	}
}
?>