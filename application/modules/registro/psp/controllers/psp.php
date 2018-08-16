<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpWord\TemplateProcessor;

class PSP extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_psp', 'model');
		$this->load->library('upload');
		//sys_session_hasRoleOrDie('psp');
	}

	public function index() {
		$data = array();
		$this->load->view('v_psp', $data);
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
		->where('tipo_doc_id', 'PSP')
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
		//sys_session_hasRoleOrDie('rh.psp.add, rh.psp.update');
		$data = array(
			'tipo_doc_id'=>'PSP',
  			'psp_anio'=>trim($this->input->post('psp_anio')),
			'psp_numero'=>trim($this->input->post('psp_numero')),
			'tipo_permiso_id'=>$this->input->post('tipo_permiso_id'),
			'contribuyente_id'=>$this->input->post('contribuyente_id'),
			'psp_fecha'=>$this->input->post('psp_fecha'),
			'psp_fecha_inicio'=>$this->input->post('psp_fecha_inicio'),
			'psp_fecha_fin'=>$this->input->post('psp_fecha_fin'),
			'psp_resolucion'=>$this->input->post('psp_resolucion'),
			'psp_ruta'=>to_upper($this->input->post('psp_ruta')),
			'plantilla_id'=>$this->input->post('plantilla_id')
		);

		if ($data['psp_anio']=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Año de Documento",
				'target_id'=>'psp_form_psp_anio_field'
			)));
		}

		if ($data['psp_numero']=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Numero de Documento",
				'target_id'=>'psp_form_psp_numero_field'
			)));
		}

		$anio_numero_count = $this->db
		->select('COUNT(*) AS value')
		->from('public.psp')
		->where('psp_anio', $data['psp_anio'])
		->where('psp_numero', $data['psp_numero'])
		->get()->row();
		if ($anio_numero_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El Numero del documento ya existe.",
				'target_id'=>'psp_form_psp_numero_field'
			)));
		}

		if ( !($data['tipo_permiso_id'] > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Tipo de Permiso",
				'target_id'=>'psp_form_tipo_permiso_id_field'
			)));
		}

		if ( !($data['contribuyente_id'] > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Contribuyente",
				'target_id'=>'psp_form_contribuyente_id_field'
			)));
		}

		if (trim($data['psp_fecha'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la fecha",
				'target_id'=>'psp_form_psp_fecha_field'
			)));
		}

		if (trim($data['psp_fecha_inicio'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la fecha de inicio",
				'target_id'=>'psp_form_psp_fecha_inicio_field'
			)));
		}

		if (trim($data['psp_fecha_fin'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la fecha de termino",
				'target_id'=>'psp_form_psp_fecha_fin_field'
			)));
		}

		if ( !($data['plantilla_id'] > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique una plantilla para la generacion del PDF.",
				'target_id'=>'psp_form_plantilla_id_field'
			)));
		}

		// SET first default state
		$estado_doc = $this->db
		->where('tipo_doc_id', 'PSP')
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
		//sys_session_hasRoleOrDie('psp.update');
		$data = array(
			'psp_id'=>$this->input->post('psp_id'),
  			'psp_anio'=>trim($this->input->post('psp_anio')),
			'psp_numero'=>trim($this->input->post('psp_numero')),
			'contribuyente_id'=>$this->input->post('contribuyente_id'),
			'tipo_permiso_id'=>$this->input->post('tipo_permiso_id'),
			'psp_fecha'=>$this->input->post('psp_fecha'),
			'psp_fecha_inicio'=>$this->input->post('psp_fecha_inicio'),
			'psp_fecha_fin'=>$this->input->post('psp_fecha_fin'),
			'psp_resolucion'=>$this->input->post('psp_resolucion'),
			'psp_ruta'=>to_upper($this->input->post('psp_ruta')),
			'plantilla_id'=>$this->input->post('plantilla_id')
		);

		$doc = $this->model->get_row($data['psp_id']);

		if ( $doc->estado_doc_modificar_flag == 'N' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"No es posible modificar el documento en el estado actual."
			)));
		}

		if ($data['psp_anio']=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Año de Documento",
				'target_id'=>'psp_form_psp_anio_field'
			)));
		}

		if ($data['psp_numero']=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Numero de Documento",
				'target_id'=>'psp_form_psp_numero_field'
			)));
		}

		$anio_numero_count = $this->db
		->select('COUNT(*) AS value')
		->from('public.psp')
		->where('psp_anio', $data['psp_anio'])
		->where('psp_numero', $data['psp_numero'])
		->where('psp_id <>', $data['psp_id'])
		->get()->row();
		if ($anio_numero_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El Numero del Documento ya existe.",
				'target_id'=>'psp_form_psp_numero_field'
			)));
		}

		if ( !($data['contribuyente_id'] > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Contribuyente",
				'target_id'=>'psp_form_contribuyente_id_field'
			)));
		}

		if ( trim($data['psp_fecha'])=='' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la fecha",
				'target_id'=>'psp_form_psp_fecha_field'
			)));
		}

		if (trim($data['psp_fecha_inicio'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la fecha de inicio",
				'target_id'=>'psp_form_psp_fecha_inicio_field'
			)));
		}

		if (trim($data['psp_fecha_fin'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la fecha de termino",
				'target_id'=>'psp_form_psp_fecha_fin_field'
			)));
		}

		if ( !($data['plantilla_id'] > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique una plantilla para la generacion del PDF.",
				'target_id'=>'psp_form_plantilla_id_field'
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
				'msg'=>"Se actualizo satisfactoriamente"
			)));
			
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion.".(isset($error)?'<br>$error':'')
			)));
		}
	}

	public function Delete() {
		//sys_session_hasRoleOrDie('rh.psp.modify');
		$p_psp_id = $this->input->post('psp_id');

		/*$psp_count = $this->db->select('COUNT(*) AS value')->where('psp_id', $p_psp_id)->get('public.psp')->row();
		if ($psp_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El psp tiene Constancias de Libre Infraccion de Transito registrado(s)."
			)));
		}*/

		$result = $this->model->delete($p_psp_id);

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

	public function getTipoPermisoList () {
		$ret = $this->model->get_tipo_permiso_list();
		echo json_encode($ret);
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
		$doc = $this->db->where('psp_id', $doc_id)->get('public.psp')->row();
		$ret = $this->model->get_doc_requisito_list($doc_id, $doc->tipo_permiso_id);
		echo json_encode($ret);
	}

	public function addDocRequisito() {
		//sys_session_hasRoleOrDie('rh.contrato.modify');
		//var_dump($_FILES);
		$upload_path = 'dbfiles/public.doc_requisito/';
		$data = array(
			'tipo_doc_id'=>'PSP',
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
				'target_id'=>'psp_doc_requisito_form_doc_requisito_fecha_field'
			)));
		}

		if ( $data['doc_requisito_numero'] == ''  && $tipo_doc_requisito->tipo_doc_requisito_numero_flag == 'S' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Numero",
				'target_id'=>'psp_doc_requisito_form_doc_requisito_numero_field'
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
				'target_id'=>'psp_doc_requisito_form_doc_requisito_fecha_field'
			)));
		}

		if ( $data['doc_requisito_numero'] == ''  && $tipo_doc_requisito->tipo_doc_requisito_numero_flag == 'S' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Numero",
				'target_id'=>'psp_doc_requisito_form_doc_requisito_numero_field'
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
		//sys_session_hasRoleOrDie('rh.psp.modify');
		$p_doc_requisito_id = intval($this->input->post('doc_requisito_id'));

		if ( !($p_doc_requisito_id > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"No se ha especificado un id valido del documento."
			)));
		}

		$doc_requisito = $this->db->where('doc_requisito_id', $p_doc_requisito_id)->get('public.doc_requisito')->row();
		
		$doc = $this->model->get_row($doc_requisito->doc_id);

		if ( $doc->estado_doc_modificar_flag == 'N') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"No es posible modificar el documento en el estado actual."
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
		//$upload_path = 'dbfiles/public.doc_requisito/';
		$data = array(
			'tipo_doc_id'=>'PSP',
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
				'target_id'=>'psp_doc_requisito_form_doc_requisito_fecha_field'
			)));
		}*/
		$data['doc_estado_fecha'] = date('d/m/Y H:i:s');

		if ($estado_doc->estado_doc_requisito_requerido_flag == 'S') {
			$tipo_doc_requisito_requerido_count = $this->db
			->select('COUNT(*) AS value')
			->where('tipo_doc_id', $data['tipo_doc_id'])
			->where('tipo_permiso_id', $doc->tipo_permiso_id)
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
					'psp_id'=>$data['doc_id'],
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
		//sys_session_hasRoleOrDie('rh.psp.modify');
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
					'psp_id'=>$doc_estado->doc_id,
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

	public function getVehiculoList () {
		$p_doc_id = $this->input->get('doc_id');
		$ret = $this->model->get_vehiculo_list($p_doc_id);
		echo json_encode($ret);
	}

	public function getVehiculoNewRow () {
		$row['operation'] = 'new';
		$row['psp_vehiculo_estado'] = 'CORRECTO';

		echo json_encode(array(
			'data'=>array($row)
		));
	}

	public function getVehiculoRow ($id){
		$row = $this->model->get_vehiculo_row($id);
		$row->operation = 'edit';
		die(json_encode(array(
			'data'=>array($row)
		)));
	}

	public function addOrUpdateVehiculo() {
		if ($this->input->post('operation') == 'new') {
			$this->addVehiculo();
		} else {
			$this->updateVehiculo();
		}
	}

	public function addVehiculo() {
		$data = array(
			'psp_id'=>$this->input->post('psp_id'),
			'psp_vehiculo_categoria'=>to_upper($this->input->post('psp_vehiculo_categoria')),
			'psp_vehiculo_marca'=>to_upper($this->input->post('psp_vehiculo_marca')),
			'psp_vehiculo_modelo'=>to_upper($this->input->post('psp_vehiculo_modelo')),
			'psp_vehiculo_color'=>to_upper($this->input->post('psp_vehiculo_color')),
			'psp_vehiculo_placa'=>to_upper($this->input->post('psp_vehiculo_placa')),
			'psp_vehiculo_ntp'=>to_upper($this->input->post('psp_vehiculo_ntp')),
			'psp_vehiculo_estado'=>$this->input->post('psp_vehiculo_estado'),
			'psp_vehiculo_observacion'=>to_upper($this->input->post('psp_vehiculo_observacion')),
			'psp_vehiculo_conductor_nomape'=>to_upper($this->input->post('psp_vehiculo_conductor_nomape')),
			'psp_vehiculo_conductor_dni'=>to_upper($this->input->post('psp_vehiculo_conductor_dni')),
			'psp_vehiculo_conductor_nlc'=>to_upper($this->input->post('psp_vehiculo_conductor_nlc'))
		);

		if ( !($data['psp_id'] > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Id del permiso de servicio publico."
			)));
		}

		if ( $data['psp_vehiculo_categoria'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la Categoria",
				'target_id'=>'psp_vehiculo_form_psp_vehiculo_categoria_field'
			)));
		}

		if ( $data['psp_vehiculo_marca'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la Marca",
				'target_id'=>'psp_vehiculo_form_psp_vehiculo_marca_field'
			)));
		}

		if ( $data['psp_vehiculo_modelo'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la Modelo",
				'target_id'=>'psp_vehiculo_form_psp_vehiculo_modelo_field'
			)));
		}

		if ( $data['psp_vehiculo_color'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Color",
				'target_id'=>'psp_vehiculo_form_psp_vehiculo_color_field'
			)));
		}

		if ( $data['psp_vehiculo_placa'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la Placa",
				'target_id'=>'psp_vehiculo_form_psp_vehiculo_placa_field'
			)));
		}

		if ( $data['psp_vehiculo_ntp'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Numero de tarjeta de propiedad",
				'target_id'=>'psp_vehiculo_form_psp_vehiculo_ntp_field'
			)));
		}
		// conductor
		if ( $data['psp_vehiculo_conductor_nomape'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique los nombres y apellidos del conductor",
				'target_id'=>'psp_vehiculo_form_psp_vehiculo_conductor_nomape_field'
			)));
		}
		
		if ( $data['psp_vehiculo_conductor_dni'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el DNI del conductor",
				'target_id'=>'psp_vehiculo_form_psp_vehiculo_conductor_dni_field'
			)));
		}

		if ( $data['psp_vehiculo_conductor_nlc'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique Numero de licencia de conducir",
				'target_id'=>'psp_vehiculo_form_psp_vehiculo_conductor_nlc_field'
			)));
		}

		try {
			//unset($data['plantilla_id']);
			$result = $this->model->add_vehiculo($data);
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

	public function updateVehiculo() {
		$data = array(
			'psp_vehiculo_id'=>$this->input->post('psp_vehiculo_id'),
			'psp_id'=>$this->input->post('psp_id'),
			'psp_vehiculo_categoria'=>to_upper($this->input->post('psp_vehiculo_categoria')),
			'psp_vehiculo_marca'=>to_upper($this->input->post('psp_vehiculo_marca')),
			'psp_vehiculo_modelo'=>to_upper($this->input->post('psp_vehiculo_modelo')),
			'psp_vehiculo_color'=>to_upper($this->input->post('psp_vehiculo_color')),
			'psp_vehiculo_placa'=>to_upper($this->input->post('psp_vehiculo_placa')),
			'psp_vehiculo_ntp'=>to_upper($this->input->post('psp_vehiculo_ntp')),
			'psp_vehiculo_estado'=>$this->input->post('psp_vehiculo_estado'),
			'psp_vehiculo_observacion'=>to_upper($this->input->post('psp_vehiculo_observacion')),
			'psp_vehiculo_conductor_nomape'=>to_upper($this->input->post('psp_vehiculo_conductor_nomape')),
			'psp_vehiculo_conductor_dni'=>to_upper($this->input->post('psp_vehiculo_conductor_dni')),
			'psp_vehiculo_conductor_nlc'=>to_upper($this->input->post('psp_vehiculo_conductor_nlc'))
		);

		if ( !($data['psp_id'] > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Id del permiso de servicio publico."
			)));
		}

		if ( $data['psp_vehiculo_categoria'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la Categoria",
				'target_id'=>'psp_vehiculo_form_psp_vehiculo_categoria_field'
			)));
		}

		if ( $data['psp_vehiculo_marca'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la Marca",
				'target_id'=>'psp_vehiculo_form_psp_vehiculo_marca_field'
			)));
		}

		if ( $data['psp_vehiculo_modelo'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la Modelo",
				'target_id'=>'psp_vehiculo_form_psp_vehiculo_modelo_field'
			)));
		}

		if ( $data['psp_vehiculo_color'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Color",
				'target_id'=>'psp_vehiculo_form_psp_vehiculo_color_field'
			)));
		}

		if ( $data['psp_vehiculo_placa'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la Placa",
				'target_id'=>'psp_vehiculo_form_psp_vehiculo_placa_field'
			)));
		}

		if ( $data['psp_vehiculo_ntp'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Numero de tarjeta de propiedad",
				'target_id'=>'psp_vehiculo_form_psp_vehiculo_ntp_field'
			)));
		}
		// conductor
		if ( $data['psp_vehiculo_conductor_nomape'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique los nombres y apellidos del conductor",
				'target_id'=>'psp_vehiculo_form_psp_vehiculo_conductor_nomape_field'
			)));
		}
		
		if ( $data['psp_vehiculo_conductor_dni'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el DNI del conductor",
				'target_id'=>'psp_vehiculo_form_psp_vehiculo_conductor_dni_field'
			)));
		}

		if ( $data['psp_vehiculo_conductor_nlc'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique Numero de licencia de conducir",
				'target_id'=>'psp_vehiculo_form_psp_vehiculo_conductor_nlc_field'
			)));
		}

		try {
			//unset($data['plantilla_id']);
			$result = $this->model->update_vehiculo($data);
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

	private function _generarPDF($doc_id) {
		$c = $this->model->get_row($doc_id, 'array');
		$td = $this->db->where('tipo_doc_id', $c['tipo_doc_id'])->get('public.tipo_doc')->row();
		$p = $this->db->where('plantilla_id', $c['plantilla_id'])->get('public.plantilla')->row();

		$cfg = array();
		//$cfg = $this->db->where('config_id', 1)->get('sys.config')->row(0, 'array');
		//$cfg['entidad_nombre_mayus'] = strtoupper($cfg['entidad_nombre']);
		
		$c['tipo_doc_desc'] = to_upper($c['tipo_doc_desc']);

		// 02/06/2018
		$c['psp_fecha_dia_numero'] = substr($c['psp_fecha'], 0, 2);
		$meses = array('', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
		$c['psp_fecha_mes_nombre'] = $meses[intval(substr($c['psp_fecha'], 3, 2))];
		$c['psp_fecha_anio'] = substr($c['psp_fecha'], 6, 4);
		
		$path_archivo = FCPATH.'dbfiles/public.plantilla/'.$p->plantilla_archivo;
		if ( !(file_exists($path_archivo) && $p->plantilla_archivo != '') ) {
			throw new Exception("El archivo de la plantilla ('{$p->plantilla_archivo}'), no existe o no es valido!.");
		}

		$t = new TemplateProcessor($path_archivo);
		$var_list = $t->getVariables();
	    foreach ($var_list as $key => $value) {
	        if (array_key_exists($value, $c)) {
	            $t->setValue($value, $c[$value]);
	        } elseif (array_key_exists($value, $cfg)) {
	        	$t->setValue($value, $cfg[$value]);
	        } elseif ($value=='no-tiene') {
	        	$t->setValue($value, '');
	        } else {
	        	throw new Exception("D{$doc_id}: Falta el parametro $value.");
	        }
	    }
	    // --- Guardamos el documento
	    $filename = strtolower($td->tipo_doc_id).'_'.$c['psp_anio'].'_'.$c['psp_numero'].'_res'.microtime(true);
	    $t->saveAs("tmp/{$filename}.docx");
	    // to PDF
	    $word = new COM("Word.Application") or die ("MS Word: Could not initialise Object.");
	    $word->Visible = 0;
	    $word->DisplayAlerts = 0;
	    $r = $word->Documents->Open(FCPATH."tmp/{$filename}.docx");
	    $word->ActiveDocument->ExportAsFixedFormat(FCPATH."dbfiles/public.psp/{$filename}.pdf", 17, false, 0, 0, 0, 0, 7, true, true, 2, true, true, false);
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
						'psp_id'=>$p_doc_id,
						'psp_pdf_resolucion'=>$filename
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
		$filename = $doc->psp_pdf;
		if (file_exists(FCPATH."dbfiles/public.psp/".$filename) && $filename != '') {
			$url = $this->config->item('base_url')."dbfiles/public.psp/{$filename}";
			echo "<embed src=\"{$url}\" type=\"application/pdf\" width=\"100%\" height=\"100%\"></embed>";
		} else {
			echo "No es posible mostrar el archivo '{$filename}'.";
		}
	}
}
?>