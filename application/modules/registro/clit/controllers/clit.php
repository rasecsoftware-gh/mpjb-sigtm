<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpWord\TemplateProcessor;

class Clit extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_clit','model');
		$this->load->library('upload');
		//sys_session_hasRoleOrDie('clit');
	}

	public function index() {
		$data = array();
		$this->load->view('v_clit', $data);
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
		//sys_session_hasRoleOrDie('rh.clit.add, rh.clit.update');
		$data = array(
			'tipo_doc_id'=>'CLIT',
  			'clit_anio'=>trim($this->input->post('clit_anio')),
			'clit_numero'=>trim($this->input->post('clit_numero')),
			'contribuyente_id'=>$this->input->post('contribuyente_id'),
			'clit_fecha'=>$this->input->post('clit_fecha'),
			'clit_resultado'=>trim($this->input->post('clit_resultado'))
		);

		if ($data['clit_anio']=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Año de Documento",
				'target_id'=>'clit_form_clit_anio_field'
			)));
		}

		if ($data['clit_numero']=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Numero de Documento",
				'target_id'=>'clit_form_clit_numero_field'
			)));
		}

		$anio_numero_count = $this->db
		->select('COUNT(*) AS value')
		->from('public.clit')
		->where('clit_anio', $data['clit_anio'])
		->where('clit_numero', $data['clit_numero'])
		->get()->row();
		if ($anio_numero_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El Numero del Documento ya existe.",
				'target_id'=>'clit_form_clit_numero_field'
			)));
		}

		if ( !($data['contribuyente_id'] > 0) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Contribuyente",
				'target_id'=>'clit_form_contribuyente_id_field'
			)));
		}

		if (trim($data['clit_fecha'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la fecha",
				'target_id'=>'clit_form_clit_fecha_field'
			)));
		}

		// SET first default template
		$plantilla = $this->db
		->where('tipo_doc_id', 'CLIT')
		->where('plantilla_estado', 'A')
		->order_by('plantilla_id', 'ASC')
		->get('public.plantilla')->row();

		if ( is_null($plantilla) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"No existe una plantilla para este tipo de documento, revise la configuracion de plantillas de documentos por favor."
			)));
		}
		$data['plantilla_id'] = $plantilla->plantilla_id;

		// SET first default state
		$estado_doc = $this->db
		->where('tipo_doc_id', 'CLIT')
		->order_by('estado_doc_id', 'ASC')
		->get('public.estado_doc')->row();

		if ( is_null($estado_doc) ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"No existe Estado definido para este tipo de documento, revise la configuracion de Estados por documentos por favor."
			)));
		}
		$data['estado_doc_id'] = $estado_doc->estado_doc_id;

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
	}

	public function Update() {
		//sys_session_hasRoleOrDie('clit.update');
		$data = array(
			'clit_id'=>$this->input->post('clit_id'),
  			'clit_anio'=>trim($this->input->post('clit_anio')),
			'clit_numero'=>trim($this->input->post('clit_numero')),
			'contribuyente_id'=>$this->input->post('contribuyente_id'),
			'clit_fecha'=>$this->input->post('clit_fecha'),
			'clit_resultado'=>trim($this->input->post('clit_resultado')),
			'plantilla_id'=>$this->input->post('plantilla_id')
		);

		if ($data['clit_anio']=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Año de Documento",
				'target_id'=>'clit_form_clit_anio_field'
			)));
		}

		if ($data['clit_numero']=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Numero de Documento",
				'target_id'=>'clit_form_clit_numero_field'
			)));
		}

		$anio_numero_count = $this->db
		->select('COUNT(*) AS value')
		->from('public.clit')
		->where('clit_anio', $data['clit_anio'])
		->where('clit_numero', $data['clit_numero'])
		->where('clit_id <>', $data['clit_id'])
		->get()->row();
		if ($anio_numero_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El Numero del Documento ya existe.",
				'target_id'=>'clit_form_clit_numero_field'
			)));
		}

		if ($data['contribuyente_id'] > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Contribuyente",
				'target_id'=>'clit_form_contribuyente_id_field'
			)));
		}

		if (trim($data['clit_fecha'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la fecha",
				'target_id'=>'clit_form_clit_fecha_field'
			)));
		}

		if ($data['plantilla_id'] == 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique una plantilla para la generacion del PDF.",
				'target_id'=>'clit_form_plantilla_field'
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

	public function Activar() {
		//sys_session_hasRoleOrDie('rh.clit.modify');
		$data = array(
			'clit_id'=>$this->input->post('clit_id'),
			'clit_estado'=>'A'
		);

		$r = $this->model->get_row($data['clit_id']);
		if ($r->clit_estado == 'A') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El clit ya se encuentra Activo."
			)));
		}

		$result = $this->model->update($data);

		if ($result !== false) {
			die(json_encode(array(
				'success'=>true,
				'msg'=>"Se Activo satisfactoriamente."
			)));
			
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion."
			)));
		}
	}

	public function Inactivar() {
		//sys_session_hasRoleOrDie('rh.clit.modify');
		$data = array(
			'clit_id'=>$this->input->post('clit_id'),
			'clit_estado'=>'I'
		);
		$r = $this->model->get_row($data['clit_id']);
		if ($r->clit_estado == 'I') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El clit ya se encuentra Inactivo."
			)));
		}

		$result = $this->model->update($data);

		if ($result !== false) {
			die(json_encode(array(
				'success'=>true,
				'msg'=>"Se inactivo satisfactoriamente."
			)));
			
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion."
			)));
		}
	}

	public function Delete() {
		//sys_session_hasRoleOrDie('rh.clit.modify');
		$p_clit_id = $this->input->post('clit_id');

		$clit_count = $this->db->select('COUNT(*) AS value')->where('clit_id', $p_clit_id)->get('public.clit')->row();
		if ($clit_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El clit tiene Constancias de Libre Infraccion de Transito registrado(s)."
			)));
		}

		$psp_count = $this->db->select('COUNT(*) AS value')->where('clit_id', $p_clit_id)->get('public.psp')->row();
		if ($psp_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El clit tiene Permisos de Servicio Publico registrado(s)."
			)));
		}

		$lc_count = $this->db->select('COUNT(*) AS value')->where('clit_id', $p_clit_id)->get('public.lc')->row();
		if ($lc_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El clit tiene Licencias de Conducir registrado(s)."
			)));
		}

		$cat_count = $this->db->select('COUNT(*) AS value')->where('clit_id', $p_clit_id)->get('public.cat')->row();
		if ($cat_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El clit tiene Constancias de Autorizacion Temporal registrado(s)."
			)));
		}

		$result = $this->model->delete($p_clit_id);

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
			'tipo_doc_id'=>'CLIT',
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
		->where('doc_id', $data['doc_id'])
		->where('tipo_doc_requisito_id', $data['tipo_doc_requisito_id'])
		->get('public.doc_requisito')->row();

		if ( $tdr_count->value > 0 ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Ya existe un(a) {$tipo_doc_requisito->tipo_doc_requisito_desc} regiatrado."
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
				'target_id'=>'clit_doc_requisito_form_doc_requisito_fecha_field'
			)));
		}

		if ( $data['doc_requisito_numero'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Numero",
				'target_id'=>'clit_doc_requisito_form_doc_requisito_numero_field'
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
				'target_id'=>'clit_doc_requisito_form_doc_requisito_fecha_field'
			)));
		}

		if ( $data['doc_requisito_numero'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Numero",
				'target_id'=>'clit_doc_requisito_form_doc_requisito_numero_field'
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

	public function printPreview() {
		//die($this->config->item('base_url'));
		//if (file_exists('tmp/archivo.txt')) { die(file_get_contents('tmp/archivo.txt')); } else { die('no'); }
		//die(FCPATH);
		$p_clit_id = $this->input->post('clit_id');
		$clit = $this->db->where('clit_id', $p_clit_id)->get('rh.clit')->row();
		$filename = $clit->clit_pdf;
		if ($clit->clit_estado == 'REGISTRADO') {
			$filename = $this->generarPDF($p_clit_id);	
			$this->db
			->set('clit_pdf', $filename)
			->set('clit_estado', 'GENERADO')
			->where('clit_id', $p_clit_id)
			->update('rh.clit');
			//$reload_list = "<script type=\"text/javascript\">clit.reload_list({$p_clit_id})</script>";
		} 
		if (file_exists(FCPATH."dbfiles/rh.clit/".$filename) && $filename != '') {
			$url = $this->config->item('base_url')."dbfiles/rh.clit/{$filename}";
			echo "<embed src=\"{$url}\" type=\"application/pdf\" width=\"100%\" height=\"100%\"></embed>";
		} else {
			echo "No es posible mostrar el archivo '{$filename}'.";
		}
	}
}
?>