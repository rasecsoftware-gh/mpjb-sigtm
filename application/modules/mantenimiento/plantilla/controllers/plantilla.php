<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpWord\TemplateProcessor;

class Plantilla extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_plantilla','model');
		$this->load->library('upload');
		//sys_session_hasRoleOrDie('plantilla');
	}

	public function index() {
		$data = array();
		$this->load->view('v_plantilla', $data);
	}

	public function getList() {
		$search_by = $this->input->get('search_by');
		$search_text = $this->input->get('search_text');
		$p_tipo_doc_id = $this->input->get('tipo_doc_id');
		$pagination_size = $this->input->get('limit');
		$pagination_start = $this->input->get('start');
		$ret = $this->model->get_list($search_by, $search_text, $p_tipo_doc_id, $pagination_size, $pagination_start);
		$base_url = $this->config->item('base_url');
		foreach ($ret['data'] as $i=>$r) {
			$filename = "/dbfiles/public.plantilla/{$r->plantilla_archivo}";
			if (file_exists(FCPATH.$filename)) {
				$r->plantilla_archivo_link = "<a href=\"{$base_url}.{$filename}\"><img src=\"{$base_url}/tools/img/word_32.png\" border=\"0\" width=\"24\"/>";	
			} else {
				$r->plantilla_archivo_link = "No existe.";
			}
			
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
		//sys_session_hasRoleOrDie('rh.plantilla.add, rh.plantilla.update');
		$data = array(
			'tipo_doc_id'=>$this->input->post('tipo_doc_id'),
			'plantilla_desc'=>$this->input->post('plantilla_desc'),
			'plantilla_nota'=>$this->input->post('plantilla_nota'),
			'plantilla_estado'=>$this->input->post('plantilla_estado')
		);

		if ( $data['tipo_doc_id']=='' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Tipo de Documento",
				'target_id'=>'plantilla_form_tipo_doc_id_field'
			)));
		}

		if ( $data['plantilla_desc'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la descripcion",
				'target_id'=>'plantilla_form_plantilla_desc_field'
			)));
		}

		$desc_count = $this->db
		->select('COUNT(*) AS value')
		->from('public.plantilla')
		->where('tipo_doc_id', $data['tipo_doc_id'])
		->where('UPPER(plantilla_desc)', to_upper($data['plantilla_desc']))
		->get()->row();
		if ($desc_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"La descripcion de la estadoplantilla ya existe. Los nombres de plantilla deben ser diferenciados por algun parametro adcional (fecha, version, año, etc).",
				'target_id'=>'plantilla_form_plantilla_desc_field'
			)));
		}

		if ( $data['plantilla_estado'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el estado",
				'target_id'=>'plantilla_form_plantilla_estado_field'
			)));
		}

		$upload_path = 'dbfiles/public.plantilla/';
		$uploaded = false;
		if (isset($_FILES['plantilla_file']) && $_FILES['plantilla_file']['name'] != '') {
			$config['upload_path'] = $upload_path;
			$config['allowed_types'] = 'docx'; //gif|jpg|png
			$this->upload->initialize($config);
			if ($this->upload->do_upload('plantilla_file')) {
				$data['plantilla_archivo'] = $this->upload->data('file_name');
				$uploaded = true;
			} else {
				die(json_encode(array(
					'success'=>false,
					'msg'=>'UPLOAD: '.$this->upload->display_errors()
				)));
			}	
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
		//sys_session_hasRoleOrDie('plantilla.update');
		$data = array(
			'plantilla_id'=>$this->input->post('plantilla_id'),
			'tipo_doc_id'=>$this->input->post('tipo_doc_id'),
			'plantilla_desc'=>$this->input->post('plantilla_desc'),
			'plantilla_nota'=>$this->input->post('plantilla_nota'),
			'plantilla_estado'=>$this->input->post('plantilla_estado')
		);

		if ( $data['tipo_doc_id']=='' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Tipo de Documento",
				'target_id'=>'plantilla_form_tipo_doc_id_field'
			)));
		}

		if ( $data['plantilla_desc'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la descripcion",
				'target_id'=>'plantilla_form_plantilla_desc_field'
			)));
		}

		$desc_count = $this->db
		->select('COUNT(*) AS value')
		->from('public.plantilla')
		->where('tipo_doc_id', $data['tipo_doc_id'])
		->where('UPPER(plantilla_desc)', to_upper($data['plantilla_desc']))
		->where('plantilla_id <>', $data['plantilla_id'])
		->get()->row();
		if ($desc_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"La descripcion de la estadoplantilla ya existe. Los nombres de plantilla deben ser diferenciados por algun parametro adcional (fecha, version, año, etc).",
				'target_id'=>'plantilla_form_plantilla_desc_field'
			)));
		}

		if ( $data['plantilla_estado'] == '' ) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el estado",
				'target_id'=>'plantilla_form_plantilla_estado_field'
			)));
		}

		$plantilla = $this->model->get_row($data['plantilla_id']);

		$upload_path = 'dbfiles/public.plantilla/';
		$uploaded = false;
		if (isset($_FILES['plantilla_file']) && $_FILES['plantilla_file']['name'] != '') {
			$config['upload_path'] = $upload_path;
			$config['allowed_types'] = 'docx'; //gif|jpg|png
			$this->upload->initialize($config);
			if ($this->upload->do_upload('plantilla_file')) {
				$data['plantilla_archivo'] = $this->upload->data('file_name');
				$uploaded = true;
				// move to deleted
				if (file_exists($upload_path.$plantilla->plantilla_archivo) && trim($plantilla->plantilla_archivo)!='') {
					rename($upload_path.$plantilla->plantilla_archivo, $upload_path.'deleted/'.$plantilla->plantilla_archivo);
				}
			} else {
				die(json_encode(array(
					'success'=>false,
					'msg'=>'UPLOAD: '.$this->upload->display_errors()
				)));
			}	
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
				'rowid'=>$data['plantilla_id']
			)));
			
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion.".(isset($error)?'<br>$error':'')
			)));
		}
	}

	public function Delete() {
		//sys_session_hasRoleOrDie('rh.plantilla.modify');
		$p_plantilla_id = $this->input->post('plantilla_id');

		$clit_count = $this->db->select('COUNT(*) AS value')->where('plantilla_id', $p_plantilla_id)->get('public.clit')->row();
		if ($clit_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"No es posible eliminar este registro porque se esta usando en CLIT ({$clit_count->value})."
			)));
		}

		$psp_count = $this->db->select('COUNT(*) AS value')->where('plantilla_id', $p_plantilla_id)->get('public.psp')->row();
		if ($psp_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"No es posible eliminar este registro porque se esta usando en PSP ({$psp_count->value})."
			)));
		}

		$lc_count = $this->db->select('COUNT(*) AS value')->where('plantilla_id', $p_plantilla_id)->get('public.lc')->row();
		if ($lc_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"No es posible eliminar este registro porque se esta usando en LC ({$lc_count->value})."
			)));
		}

		$cat_count = $this->db->select('COUNT(*) AS value')->where('plantilla_id', $p_plantilla_id)->get('public.cat')->row();
		if ($cat_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"No es posible eliminar este registro porque se esta usando en CAT ({$cat_count->value})."
			)));
		}

		$plantilla = $this->model->get_row($data['plantilla_id']);

		$result = $this->model->delete($p_plantilla_id);

		if ($result !== false) {
			// move to deleted
			if (file_exists($upload_path.$plantilla->plantilla_archivo) && trim($plantilla->plantilla_archivo)!='') {
				rename($upload_path.$plantilla->plantilla_archivo, $upload_path.'deleted/'.$plantilla->plantilla_archivo);
			}
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
}
?>