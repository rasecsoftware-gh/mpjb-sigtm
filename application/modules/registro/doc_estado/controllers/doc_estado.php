<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpWord\TemplateProcessor;

class DocEstado extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_doc_estado','model');
		$this->load->library('upload');
		//sys_session_hasRoleOrDie('doc_estado');
	}

	public function index() {
		$data = array();
		$this->load->view('v_doc_estado', $data);
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
		//sys_session_hasRoleOrDie('rh.doc_estado.add, rh.doc_estado.update');
		$data = array(
			'tipo_persona_id'=>$this->input->post('tipo_persona_id'),
  			'tipo_doc_identidad_id'=>$this->input->post('tipo_doc_identidad_id'),
			'doc_estado_numero_doc'=>trim($this->input->post('doc_estado_numero_doc')),
			'doc_estado_nombres'=>to_upper($this->input->post('doc_estado_nombres')),
			'doc_estado_apellidos'=>to_upper($this->input->post('doc_estado_apellidos')),
			'ubigeo_id'=>$this->input->post('ubigeo_id'),
			'doc_estado_direccion'=>$this->input->post('doc_estado_direccion'),
			'doc_estado_telefono'=>$this->input->post('doc_estado_telefono'),
			'doc_estado_email'=>$this->input->post('doc_estado_email'),
			'doc_estado_observacion'=>$this->input->post('doc_estado_observacion'),
			
			'doc_estado_estado'=>'A',
		);

		if ($data['doc_estado_numero_doc']=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Numero de Documento",
				'target_id'=>'doc_estado_form_doc_estado_numero_doc_field'
			)));
		}

		$numero_doc_count = $this->db
		->select('COUNT(*) AS value')
		->from('public.doc_estado')
		->where('doc_estado_numero_doc', $data['doc_estado_numero_doc'])
		->get()->row();
		if ($numero_doc_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El Numero de Documento ya existe.",
				'target_id'=>'doc_estado_form_doc_estado_numero_doc_field'
			)));
		}

		if (trim($data['doc_estado_nombres'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique los Nombres o Razon social",
				'target_id'=>'doc_estado_form_doc_estado_nombres_field'
			)));
		}

		if (trim($data['doc_estado_apellidos'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique los Apelllidos",
				'target_id'=>'doc_estado_form_doc_estado_apellidos_field'
			)));
		}

		if (trim($data['ubigeo_id'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la ubicacion.",
				'target_id'=>'doc_estado_form_doc_estado_nombres_field'
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
		//sys_session_hasRoleOrDie('doc_estado.update');
		$data = array(
			'doc_estado_id'=>$this->input->post('doc_estado_id'),
			'tipo_persona_id'=>$this->input->post('tipo_persona_id'),
			'tipo_doc_identidad_id'=>$this->input->post('tipo_doc_identidad_id'),
			'doc_estado_numero_doc'=>trim($this->input->post('doc_estado_numero_doc')),
			'doc_estado_nombres'=>to_upper($this->input->post('doc_estado_nombres')),
			'doc_estado_apellidos'=>to_upper($this->input->post('doc_estado_apellidos')),

			'ubigeo_id'=>$this->input->post('ubigeo_id'),
			'doc_estado_direccion'=>$this->input->post('doc_estado_direccion'),
			'doc_estado_telefono'=>$this->input->post('doc_estado_telefono'),
			'doc_estado_email'=>$this->input->post('doc_estado_email'),
			'doc_estado_observacion'=>$this->input->post('doc_estado_observacion')
			//'doc_estado_estado'=>$this->input->post('doc_estado_estado'),
		);

		if (trim($data['doc_estado_numero_doc'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Numero del documento",
				'target_id'=>'doc_estado_form_doc_estado_numero_doc_field'
			)));
		}

		$numero_doc_count = $this->db
		->select('COUNT(*) AS value')
		->from('public.doc_estado')
		->where('doc_estado_numero_doc', $data['doc_estado_numero_doc'])
		->where('doc_estado_id <>', $data['doc_estado_id'])
		->get()->row();
		if ($numero_doc_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El Numero de Documento ya existe.",
				'target_id'=>'doc_estado_form_doc_estado_numero_doc_field'
			)));
		}

		if (trim($data['doc_estado_nombres'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique los nombres p rason social",
				'target_id'=>'doc_estado_form_doc_estado_nombres_field'
			)));
		}

		if (trim($data['doc_estado_apellidos'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique los apellidos",
				'target_id'=>'doc_estado_form_doc_estado_apellidos_field'
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
		//sys_session_hasRoleOrDie('rh.doc_estado.modify');
		$data = array(
			'doc_estado_id'=>$this->input->post('doc_estado_id'),
			'doc_estado_estado'=>'A'
		);

		$r = $this->model->get_row($data['doc_estado_id']);
		if ($r->doc_estado_estado == 'A') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El doc_estado ya se encuentra Activo."
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
		//sys_session_hasRoleOrDie('rh.doc_estado.modify');
		$data = array(
			'doc_estado_id'=>$this->input->post('doc_estado_id'),
			'doc_estado_estado'=>'I'
		);
		$r = $this->model->get_row($data['doc_estado_id']);
		if ($r->doc_estado_estado == 'I') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El doc_estado ya se encuentra Inactivo."
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
		//sys_session_hasRoleOrDie('rh.doc_estado.modify');
		$p_doc_estado_id = $this->input->post('doc_estado_id');

		$clit_count = $this->db->select('COUNT(*) AS value')->where('doc_estado_id', $p_doc_estado_id)->get('public.clit')->row();
		if ($clit_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El doc_estado tiene Constancias de Libre Infraccion de Transito registrado(s)."
			)));
		}

		$psp_count = $this->db->select('COUNT(*) AS value')->where('doc_estado_id', $p_doc_estado_id)->get('public.psp')->row();
		if ($psp_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El doc_estado tiene Permisos de Servicio Publico registrado(s)."
			)));
		}

		$lc_count = $this->db->select('COUNT(*) AS value')->where('doc_estado_id', $p_doc_estado_id)->get('public.lc')->row();
		if ($lc_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El doc_estado tiene Licencias de Conducir registrado(s)."
			)));
		}

		$cat_count = $this->db->select('COUNT(*) AS value')->where('doc_estado_id', $p_doc_estado_id)->get('public.cat')->row();
		if ($cat_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El doc_estado tiene Constancias de Autorizacion Temporal registrado(s)."
			)));
		}

		$result = $this->model->delete($p_doc_estado_id);

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
		$p_doc_estado_id = $this->input->post('doc_estado_id');
		$doc_estado = $this->db->where('doc_estado_id', $p_doc_estado_id)->get('rh.doc_estado')->row();
		$filename = $doc_estado->doc_estado_pdf;
		if ($doc_estado->doc_estado_estado == 'REGISTRADO') {
			$filename = $this->generarPDF($p_doc_estado_id);	
			$this->db
			->set('doc_estado_pdf', $filename)
			->set('doc_estado_estado', 'GENERADO')
			->where('doc_estado_id', $p_doc_estado_id)
			->update('rh.doc_estado');
			//$reload_list = "<script type=\"text/javascript\">doc_estado.reload_list({$p_doc_estado_id})</script>";
		} 
		if (file_exists(FCPATH."dbfiles/rh.doc_estado/".$filename) && $filename != '') {
			$url = $this->config->item('base_url')."dbfiles/rh.doc_estado/{$filename}";
			echo "<embed src=\"{$url}\" type=\"application/pdf\" width=\"100%\" height=\"100%\"></embed>";
		} else {
			echo "No es posible mostrar el archivo '{$filename}'.";
		}
	}
}
?>