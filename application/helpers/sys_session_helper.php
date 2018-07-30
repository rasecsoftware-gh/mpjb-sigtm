<?php
	if (!defined('BASEPATH')) exit('No direct script access allowed');

	if (!function_exists('sys_session_getUserList')) {
		function sys_session_getUserList() {
			/*$users = array(
				array('name'=>'admin', 'password'=>'siasape!', 'modules'=>array('almacen','rh','tesoreria'), 'roles'=>array('sa')),
				array('name'=>'hwmaquerac', 'password'=>'harryman!', 'modules'=>array('almacen'), 'roles'=>array('geb','geb-aprobador','geb-anulador','nea','nea-aprobador','nea-anulador')),
				array('name'=>'almacenero', 'password'=>'5934', 'modules'=>array('almacen'), 'roles'=>array('geb','nea')),
				array('name'=>'almacenero2', 'password'=>'3519', 'modules'=>array('almacen'), 'roles'=>array('geb','nea')),
				array('name'=>'almacenero3', 'password'=>'5378', 'modules'=>array('almacen'), 'roles'=>array('geb')),
				array('name'=>'jstintayac', 'password'=>'25860396', 'modules'=>array('rh'), 'roles'=>array('crls', array('name'=>'contrato-view'))),
				array('name'=>'asimonic', 'password'=>'00410833', 'modules'=>array('rh'), 'roles'=>array('crls','ctl','contrato')),
				array('name'=>'cllipita', 'password'=>'chepi', 'modules'=>array('rh'), 'roles'=>array('crls','ctl','contrato'))
			);*/
			$ci =& get_instance();
			$users = $ci->db->get('sys.usuario')->result();
			foreach ($users as $i => $user) {
				$user->roles = $ci->db
				->select('up.usuario_permiso_id, p.*')
				->from('sys.usuario_permiso AS up')
				->join('sys.permiso AS p', 'p.permiso_id = up.permiso_id')
				->where('up.usuario_id', $user->usuario_id)
				->where('p.permiso_estado', 'A')
				->get()->result();
			}
			return $users;
		}
	}

	if (!function_exists('sys_session_getUserInfo')) {
		function sys_session_getUserInfo($name='') {
			$ci =& get_instance();
			if ($name=='') {
				$name = $ci->session->userdata('username');
			}
			$users = sys_session_getUserList();
			$search = strtolower($name);
			foreach ($users as $i=>$user) {
				if (strtolower($user->usuario_login)==$search) {
					return $user;
				}
			}
			return null;
		}
	}

	if (!function_exists('sys_session_hasRole')) {
		function sys_session_hasRole($name_or_list) {
			//$list = func_get_args();
			if (is_array($name_or_list)) {
				$list = $name_or_list;
			} else {
				$list = explode(',', $name_or_list);
				//$list = array($name_or_list);
			}
			$user = sys_session_getUserInfo();
			if (!is_null($user)) {
				if ($user->usuario_sa == 'S') {
					return true; // sa has all access
				}
				$roles = $user->roles;
				foreach ($list as $query) {
					foreach ($roles as $rol) {
						if (trim($query) == $rol->permiso_accion) {
							return true;
						}
					}
				}
			}
			return false;
		}
	}

	if (!function_exists('sys_session_hasRoleOrDie')) {
		function sys_session_hasRoleOrDie($role_list, $die_message = null) {
			if (sys_session_hasRole($role_list)) {
				return true;
			} else {
				die(json_encode(array(
					'success'=>false,
					'msg'=>is_null($die_message)?"No esta autorizado para realizar esta operacion.":$die_message
				)));
			}
		}
	}

	if (!function_exists('sys_session_hasRoleToString')) {
		function sys_session_hasRoleToString($role_list) {
			return sys_session_hasRole($role_list)?'true':'false';
		}
	}

	if (!function_exists('sys_session_syslog')) {
		function sys_session_syslog($event='crear', $oldlog = '') {
			$user = sys_session_getUserInfo();
			$ci =& get_instance();
			$info_schema = array(
				'operation'=>$event,
				'user'=>$user->usuario_login,
				'ip'=>$ci->session->userdata('ip'),
				'date'=>date('d/m/Y'),
				'time'=>date('H:i:s')
			);

			foreach ($info_schema as $name=>$value) {
				$info_list[] = "$name: $value";
			}
			
			$info_string = implode(', ', $info_list);

			if ($oldlog!='') {
				$info_string = $oldlog.";\n".$info_string;
			}

			return $info_string;
		}
	}
?>