<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CCU extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_ccu','model');
		//$this->load->library('upload');
		//ini_set('com.autoregister_casesensitive', 1); // Optional. When set wdPropertyWords does NOT equal WDPROPERTYWORDS
		//ini_set('com.autoregister_typelib', 1); // Auto registry the loaded typelibrary - allows access to constants.
		//ini_set('com.autoregister_verbose', 0); // Suppress Warning: com::com(): Type library constant emptyenum is already defined in $s on line %d messages.
	}

	public function index() {
		$data = array();
		$this->load->view('v_ccu', $data);
	}

	public function getList() {
		$search_by = $this->input->get('search_by');
		$search_text = $this->input->get('search_text');
		$pagination_size = $this->input->get('limit');
		$pagination_start = $this->input->get('start');
		$ret = $this->model->get_list($search_by, $search_text, $pagination_size, $pagination_start);
		/*$rows = $ret['data'];
		foreach ($rows as $i=>$r) {
			//$ret['data'][$i]->oc_anio_numero = $r->oc_anio.'-'.$r->oc_numero;
		}*/
		echo json_encode($ret);
	}

	public function getNewRow () {
		$row = $this->model->get_new_row();
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

	public function Add() {
		$data = array(
			'usuario_login'=>$this->input->post('usuario_login'),
  			'usuario_desc'=>$this->input->post('usuario_desc'),
  			'usuario_pw'=>$this->input->post('usuario_pw'),
			'usuario_sa'=>$this->input->post('usuario_sa'),
			'usuario_estado'=>$this->input->post('usuario_estado')
		);

		if (trim($data['usuario_login'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique un login valido para el usuario",
				'target_id'=>'ccu_usuario_login_field'
			)));
		}

		if (trim($data['usuario_desc'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la descripcion del usuario",
				'target_id'=>'ccu_usuario_desc_field'
			)));
		}

		if (trim($data['usuario_pw'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique una clave porfa...",
				'target_id'=>'ccu_usuario_pw_field'
			)));
		}

		if (trim($data['usuario_estado'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el estado (A o I)",
				'target_id'=>'ccu_usuario_estado_field'
			)));
		}

		$login_count = $this->db->select("COUNT(*) AS value")->where('usuario_login', $data['usuario_login'])->get('sys.usuario')->row();
		if ($login_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Lo siento pero el login especificado ya existe, prueba con otro porfa...",
				'target_id'=>'ccu_usuario_login_field'
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
				'msg'=>"Error al registrar el proceso.".(isset($error)?'<br>$error':'')
			)));
		}
		echo json_encode($ret);
	}

	public function Update() {
		$data = array(
			'usuario_id'=>$this->input->post('usuario_id'),
			'usuario_login'=>$this->input->post('usuario_login'),
  			'usuario_desc'=>$this->input->post('usuario_desc'),
  			'usuario_pw'=>$this->input->post('usuario_pw'),
			'usuario_sa'=>$this->input->post('usuario_sa'),
			'usuario_estado'=>$this->input->post('usuario_estado')
		);

		$usuario = $this->db->where('usuario_id', $data['usuario_id'])->get('sys.usuario')->row();

		if (is_null($usuario)) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El usuario con ID: {$p_usuario_id} no existe."
			)));
		}

		if (intval($data['usuario_id']) === 1) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Hey!. No es posible modificar el super usuario admin."
			)));	
		}

		if (trim($data['usuario_login'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique un login valido para el usuario",
				'target_id'=>'ccu_usuario_login_field'
			)));
		}

		if (trim($data['usuario_desc'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la descripcion del usuario",
				'target_id'=>'ccu_usuario_desc_field'
			)));
		}

		if (trim($data['usuario_pw'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique una clave porfa...",
				'target_id'=>'ccu_usuario_pw_field'
			)));
		}

		if (trim($data['usuario_estado'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el estado (A o I)",
				'target_id'=>'ccu_usuario_estado_field'
			)));
		}

		$login_count = $this->db->select("COUNT(*) AS value")->where('usuario_login', $data['usuario_login'])->where('usuario_id <>', $data['usuario_id'])->get('sys.usuario')->row();
		if ($login_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Lo siento pero el login especificado ya existe, prueba con otro porfa...",
				'target_id'=>'ccu_usuario_login_field'
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
		$p_usuario_id = $this->input->post('usuario_id');
		$usuario = $this->db->where('usuario_id', $p_usuario_id)->get('sys.usuario')->row();
		if (is_null($usuario)) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El usuario con ID: {$p_usuario_id} no existe."
			)));
		}

		if (intval($p_usuario_id) === 1) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Hey!. No es posible eliminar el super usuario admin."
			)));	
		}

		$usuario_permiso_count = $this->db->select("COUNT(*) AS value")->where('usuario_id', $p_usuario_id)->get('sys.usuario_permiso')->row();
		if ($usuario_permiso_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Lo siento, no es posible eliminar este usuario, tiene registrado permisos."
			)));
		}
		try {
			$result = $this->model->delete($p_usuario_id);
		} catch (Exception $ex) {
			$error = $ex->getMessage();
		}

		if ($result !== false) {
			die(json_encode(array(
				'success'=>true,
				'msg'=>"Se elimino satisfactoriamente"
			)));
			
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion.".(isset($error)?'<br>$error':'')
			)));
		}
	}

	public function getUsuarioPermisoList () {
		$p_usuario_id = $this->input->get('usuario_id');
		$ret = $this->model->get_usuario_permiso_list($p_usuario_id);
		echo json_encode($ret);
	}

	public function getPermisoList () {
		$filter = $this->input->get('query');
		$pagination_size = $this->input->get('limit');
		$pagination_start = $this->input->get('start');
		$ret = $this->model->get_permiso_list($filter, $pagination_size, $pagination_start);
		echo json_encode($ret);
	}

	public function getPermisoNewRow () {
		$row = $this->model->get_permiso_new_row();
		echo json_encode(array(
			'data'=>array($row)
		));
	}

	public function permisoAdd() {
		sys_session_hasRoleOrDie('sa');
		$data = array(
			'permiso_desc'=>trim($this->input->post('permiso_desc')),
			'permiso_accion'=>strtolower(trim($this->input->post('permiso_accion'))),
			'permiso_estado'=>$this->input->post('permiso_estado')
		);

		if ($data['permiso_desc'] == '') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la descripcion del permiso.",
			)));
		}

		if ($data['permiso_accion'] == '') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el nombre de la accion en el formato valido (app.module.form[.action]).",
			)));
		}

		if ($data['permiso_accion'] == 'sa') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"'sa', es una accion reservada por el sistema.",
			)));
		}

		$desc_count = $this->db->select('COUNT(*) AS value')->where('LOWER(permiso_desc)', strtolower($data['permiso_desc']))->get('sys.permiso')->row();
		if ($desc_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"La descripcion del permiso ya existe.",
			)));	
		}

		$accion_count = $this->db->select('COUNT(*) AS value')->where('LOWER(permiso_accion)', $data['permiso_accion'])->get('sys.permiso')->row();
		if ($accion_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"La accion del permiso ya existe.",
			)));	
		}

		try {
			$result = $this->model->permiso_add($data);
		} catch (Exception $ex) {
			$error = $ex->getMessage();
		}

		if ($result !== false) {
			die(json_encode(array(
				'success'=>true,
				'msg'=>"Se agrego satisfactoriamente.",
				'rowid'=>$result
			)));
			
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion.".(isset($error)?'<br>$error':'')
			)));
		}
	}

	public function permisoUpdate() {
		sys_session_hasRoleOrDie('sa');
		$data = array(
			'permiso_id'=>$this->input->post('permiso_id'),
			'permiso_desc'=>trim($this->input->post('permiso_desc')),
			'permiso_accion'=>strtolower(trim($this->input->post('permiso_accion'))),
			'permiso_estado'=>$this->input->post('permiso_estado')
		);

		$permiso = $this->db->where('permiso_id', $data['permiso_id'])->get('sys.permiso')->row();
		if (is_null($permiso)) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El permiso especificado no existe ({$data['permiso_id']})."
			)));
		}

		if ($data['permiso_desc'] == '') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la descripcion del permiso."
			)));
		}

		if ($data['permiso_accion'] == '') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el nombre de la accion en el formato valido (app.module.form[.action])."
			)));
		}

		if ($data['permiso_accion'] == 'sa') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"'sa', es una accion reservada por el sistema.",
			)));
		}

		$desc_count = $this->db->select('COUNT(*) AS value')->where('LOWER(permiso_desc)', strtolower($data['permiso_desc']))->where('permiso_id<>', $data['permiso_id'])->get('sys.permiso')->row();
		if ($desc_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"La descripcion del permiso ya existe."
			)));
		}

		$accion_count = $this->db->select('COUNT(*) AS value')->where('LOWER(permiso_accion)', $data['permiso_accion'])->where('permiso_id<>', $data['permiso_id'])->get('sys.permiso')->row();
		if ($accion_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"La accion del permiso ya existe."
			)));
		}

		try {
			$result = $this->model->permiso_update($data);
		} catch (Exception $ex) {
			$error = $ex->getMessage();
		}

		if ($result !== false) {
			die(json_encode(array(
				'success'=>true,
				'msg'=>"Se guardo satisfactoriamente.",
				'rowid'=>$result
			)));
			
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion.".(isset($error)?'<br>$error':'')
			)));
		}
	}

	public function permisoDelete() {
		sys_session_hasRoleOrDie('sa');
		$p_permiso_id = $this->input->post('permiso_id');
		$permiso = $this->db->where('permiso_id', $p_permiso_id)->get('sys.permiso')->row();
		if (is_null($permiso)) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El registro no existe ({$p_permiso_id})."
			)));
		}

		$usuario_permiso_count = $this->db->select('COUNT(*) AS value')->where('permiso_id', $p_permiso_id)->get('sys.usuario_permiso')->row();
		if ($usuario_permiso_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"No es posible eliminar el permiso, porque esta siendo usado todavia por algun usuario."
			)));
		}

		try {
			$this->db->where('permiso_id', $p_permiso_id)->delete('sys.permiso');
			die(json_encode(array(
				'success'=>true,
				'msg'=>"Se elimino satisfactoriamente"
			)));
		} catch (Exception $ex) {
			$error = $ex->getMessage();
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion.".(isset($error)?'<br>$error':'')
			)));
		}
	}

	public function usuarioPermisoAdd() {
		$data = array(
			'usuario_id'=>$this->input->post('usuario_id'),
			'permiso_id'=>$this->input->post('permiso_id')
		);

		if (!($data['usuario_id'] > 0)) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique un Id de usuario valido.",
			)));
		}

		if (!($data['permiso_id'] > 0)) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el permiso.",
			)));
		}

		$exists_count = $this->db->select('COUNT(*) AS value')->where('usuario_id', $data['usuario_id'])->where('permiso_id', $data['permiso_id'])->get('sys.usuario_permiso')->row();
		if ($exists_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El permiso ya se encuentra agregado.",
			)));	
		}

		try {
			$result = $this->model->usuario_permiso_add($data);
		} catch (Exception $ex) {
			$error = $ex->getMessage();
		}

		if ($result !== false) {
			die(json_encode(array(
				'success'=>true,
				'msg'=>"Se agrego satisfactoriamente.",
				'rowid'=>$result
			)));
			
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion.".(isset($error)?'<br>$error':'')
			)));
		}
	}

	public function usuarioPermisoDelete() {
		$p_usuario_permiso_id = $this->input->post('usuario_permiso_id');
		$usuario_permiso = $this->db->where('usuario_permiso_id', $p_usuario_permiso_id)->get('sys.usuario_permiso')->row();
		if (is_null($usuario_permiso)) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El registro no existe ({$p_usuario_permiso_id})."
			)));
		}

		try {
			$this->db->where('usuario_permiso_id', $p_usuario_permiso_id)->delete('sys.usuario_permiso');
			die(json_encode(array(
				'success'=>true,
				'msg'=>"Se quito satisfactoriamente"
			)));
		} catch (Exception $ex) {
			$error = $ex->getMessage();
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion.".(isset($error)?'<br>$error':'')
			)));
		}
	}
}
?>