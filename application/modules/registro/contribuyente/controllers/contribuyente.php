<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpWord\TemplateProcessor;

class Contribuyente extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_contribuyente','model');
		$this->load->library('upload');
		//sys_session_hasRoleOrDie('contribuyente');
	}

	public function index() {
		$data = array();
		$this->load->view('v_contribuyente', $data);
	}

	public function getList() {
		$search_by = $this->input->get('search_by');
		$search_text = $this->input->get('search_text');
		$p_tipo_persona_id = $this->input->get('tipo_persona_id');
		$p_tipo_doc_identidad_id = $this->input->get('tipo_doc_identidad_id');
		$pagination_size = $this->input->get('limit');
		$pagination_start = $this->input->get('start');
		$ret = $this->model->get_list($search_by, $search_text, $p_tipo_persona_id, $p_tipo_doc_identidad_id, $pagination_size, $pagination_start);
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
		//sys_session_hasRoleOrDie('rh.contribuyente.add, rh.contribuyente.update');
		$data = array(
			'tipo_persona_id'=>$this->input->post('tipo_persona_id'),
  			'tipo_doc_identidad_id'=>$this->input->post('tipo_doc_identidad_id'),
			'contribuyente_numero_doc'=>$this->input->post('contribuyente_numero_doc'),
			'contribuyente_nombres'=>$this->input->post('contribuyente_nombres'),
			'contribuyente_apellidos'=>$this->input->post('contribuyente_apellidos'),
			'ubigeo_id'=>$this->input->post('ubigeo_id'),
			'contribuyente_direccion'=>$this->input->post('contribuyente_direccion'),
			'contribuyente_telefono'=>$this->input->post('contribuyente_telefono'),
			'contribuyente_email'=>$this->input->post('contribuyente_email'),
			'contribuyente_observacion'=>$this->input->post('contribuyente_observacion'),
			
			'contribuyente_estado'=>'A',
		);

		if (trim($data['contribuyente_numero_doc'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Numero de Documento",
				'target_id'=>'contribuyente_form_contribuyente_numero_doc_field'
			)));
		}

		if (trim($data['contribuyente_nombres'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique los Nombres o Razon social",
				'target_id'=>'contribuyente_form_contribuyente_nombres_field'
			)));
		}

		if (trim($data['contribuyente_apellidos'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique los Apelllidos",
				'target_id'=>'contribuyente_form_contribuyente_apellidos_field'
			)));
		}

		if (trim($data['ubigeo_id'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la ubicacion.",
				'target_id'=>'contribuyente_form_contribuyente_nombres_field'
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
		//sys_session_hasRoleOrDie('contribuyente.update');
		$data = array(
			'contribuyente_id'=>$this->input->post('contribuyente_id'),
			'tipo_persona_id'=>$this->input->post('tipo_persona_id'),
			'tipo_doc_identidad_id'=>$this->input->post('tipo_doc_identidad_id'),
			'contribuyente_numero_doc'=>$this->input->post('contribuyente_numero_doc'),
			'contribuyente_nombres'=>$this->input->post('contribuyente_nombres'),
			'contribuyente_apellidos'=>$this->input->post('contribuyente_apellidos'),

			'ubigeo_id'=>$this->input->post('ubigeo_id'),
			'contribuyente_direccion'=>$this->input->post('contribuyente_direccion'),
			'contribuyente_telefono'=>$this->input->post('contribuyente_telefono'),
			'contribuyente_email'=>$this->input->post('contribuyente_email'),
			'contribuyente_observacion'=>$this->input->post('contribuyente_observacion'),
			'contribuyente_estado'=>$this->input->post('contribuyente_estado'),
		);

		if (trim($data['contribuyente_numero_doc'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Numero del documento",
				'target_id'=>'contribuyente_form_contribuyente_numero_doc_field'
			)));
		}

		if (trim($data['contribuyente_nombres'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique los nombres p rason social",
				'target_id'=>'contribuyente_form_contribuyente_nombres_field'
			)));
		}

		if (trim($data['contribuyente_apellidos'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique los apellidos",
				'target_id'=>'contribuyente_form_contribuyente_apellidos_field'
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

	public function Anular() {
		//sys_session_hasRoleOrDie('rh.contribuyente.modify');
		$data = array(
			'contribuyente_id'=>$this->input->post('contribuyente_id'),
			'contribuyente_estado'=>'I'
		);

		$r = $this->model->get_row($data['contribuyente_id']);
		if ($r->contribuyente_estado == 'I') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El contribuyente ya se encuentra Inactivo."
			)));
		}

		$result = $this->model->update($data);

		if ($result !== false) {
			die(json_encode(array(
				'success'=>true,
				'msg'=>"Se inactivo satisfactoriamente"
			)));
			
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion"
			)));
		}
	}

	public function CancelarAnulado() {
		//sys_session_hasRoleOrDie('rh.contribuyente.modify');
		$data = array(
			'contribuyente_id'=>$this->input->post('contribuyente_id'),
			'contribuyente_estado'=>'A'
		);
		$r = $this->model->get_row($data['contribuyente_id']);
		if ($r->contribuyente_estado == 'A') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El contribuyente ya se encuentra Activo."
			)));
		}

		$result = $this->model->update($data);

		if ($result !== false) {
			die(json_encode(array(
				'success'=>true,
				'msg'=>"Se activo satisfactoriamente"
			)));
			
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion"
			)));
		}
	}

	public function getTipoPersonaList () {
		$ret = $this->model->get_tipo_persona_list();
		echo json_encode($ret);
	}

	public function getTipoDocIdentidadList () {
		$ret = $this->model->get_tipo_doc_identidad_list();
		echo json_encode($ret);
	}

	public function getUbigeoList () {
		$filter = $this->input->get('query');
		$ret = $this->model->get_ubigeo_list($filter);
		echo json_encode($ret);
	}

	public function printPreview() {
		//die($this->config->item('base_url'));
		//if (file_exists('tmp/archivo.txt')) { die(file_get_contents('tmp/archivo.txt')); } else { die('no'); }
		//die(FCPATH);
		$p_contribuyente_id = $this->input->post('contribuyente_id');
		$contribuyente = $this->db->where('contribuyente_id', $p_contribuyente_id)->get('rh.contribuyente')->row();
		$filename = $contribuyente->contribuyente_pdf;
		if ($contribuyente->contribuyente_estado == 'REGISTRADO') {
			$filename = $this->generarPDF($p_contribuyente_id);	
			$this->db
			->set('contribuyente_pdf', $filename)
			->set('contribuyente_estado', 'GENERADO')
			->where('contribuyente_id', $p_contribuyente_id)
			->update('rh.contribuyente');
			//$reload_list = "<script type=\"text/javascript\">contribuyente.reload_list({$p_contribuyente_id})</script>";
		} 
		if (file_exists(FCPATH."dbfiles/rh.contribuyente/".$filename) && $filename != '') {
			$url = $this->config->item('base_url')."dbfiles/rh.contribuyente/{$filename}";
			echo "<embed src=\"{$url}\" type=\"application/pdf\" width=\"100%\" height=\"100%\"></embed>";
		} else {
			echo "No es posible mostrar el archivo '{$filename}'.";
		}
	}
}
?>