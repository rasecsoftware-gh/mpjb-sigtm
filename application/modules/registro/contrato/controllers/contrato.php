<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use PhpOffice\PhpWord\TemplateProcessor;

class Contrato extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_contrato','model');
		$this->load->library('upload');
		sys_session_hasRoleOrDie('rh.contrato');
		//ini_set('com.autoregister_casesensitive', 1); // Optional. When set wdPropertyWords does NOT equal WDPROPERTYWORDS
		//ini_set('com.autoregister_typelib', 1); // Auto registry the loaded typelibrary - allows access to constants.
		//ini_set('com.autoregister_verbose', 0); // Suppress Warning: com::com(): Type library constant emptyenum is already defined in $s on line %d messages.
	}

	public function index() {
		$data = array();
		$this->load->view('v_contrato', $data);
	}

	public function getList() {
		$search_by = $this->input->get('search_by');
		$search_text = $this->input->get('search_text');
		$p_tipo_contrato_id = $this->input->get('tipo_contrato_id');
		$pagination_size = $this->input->get('limit');
		$pagination_start = $this->input->get('start');
		$ret = $this->model->get_list($search_by, $search_text, $p_tipo_contrato_id, $pagination_size, $pagination_start);
		/*$rows = $ret['data'];
		foreach ($rows as $i=>$r) {
			//$ret['data'][$i]->oc_anio_numero = $r->oc_anio.'-'.$r->oc_numero;
		}*/
		echo json_encode($ret);
	}

	public function getNewRow ($parent_id) {
		$p_tipo_contrato_id = '01';//$this->input->get('tipo_contrato_id');
		if ($parent_id > 0) {
			$row['tipo_contrato_id'] = '03'; // adenda
		} else {
			$row['tipo_contrato_id'] = $p_tipo_contrato_id;
		}
		
		$row = $this->model->get_new_row($row['tipo_contrato_id']);

		$entidad = $this->db->get('sys.config')->row();
		if (is_null($entidad)) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"No hay datos de la Entidad"
			)));
		}
		$repre_list = $this->db->where('repre_contrato_estado', 'ACTIVO')->order_by('repre_contrato_fecha', 'DESC')->get('rh.repre_contrato')->result();
		if (count($repre_list) == 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"No hay un representante activo para la Entidad",
				'total'=>333
			)));	
		} else {
			$repre = $repre_list[0];
		}

		$row['entidad_nombre'] = strtoupper($entidad->entidad_nombre);
		$row['entidad_ruc'] = $entidad->entidad_ruc;
		$row['entidad_direccion'] = $entidad->entidad_direccion;

		$row['repre_contrato_id'] = $repre->repre_contrato_id;
		$row['repre_contrato_dni'] = $repre->repre_contrato_dni;
		$row['repre_contrato_descripcion'] = $repre->repre_contrato_gradoacad.' '.$repre->repre_contrato_apenom;
		$row['repre_contrato_documento'] = $repre->repre_contrato_docref;
		$row['repre_contrato_cargo'] = $repre->repre_contrato_cargo;

		if ($parent_id > 0) { // adenda!
			$a = $this->model->get_row($parent_id);
			$row['p_contrato_tipo_numero_anio'] = $a->tipo_contrato_desc.' '.$a->contrato_numero.'-'.$a->contrato_anio;
			$row['p_contrato_fecha_emision'] = $a->contrato_fecha_emision;
			$row['p_contrato_fecha_inicio'] = $a->contrato_fecha_inicio;
			$row['p_contrato_fecha_fin'] = $a->contrato_fecha_fin;
			$row['p_contrato_cargo'] = $a->contrato_cargo;
			$row['p_contrato_dependencia'] = $a->contrato_dependencia;
			
			$row['contrato_id_parent'] = $parent_id;
			$row['tipo_adenda_id'] = 1; // prorroga por defecto

			//$row['traba_id'] = $a->traba_id;
			$row['contrato_traba_cod'] = $a->contrato_traba_cod;
			$row['contrato_traba_dni'] = $a->contrato_traba_dni;
			$row['contrato_traba_apenom'] = $a->contrato_traba_apenom;
			$row['contrato_traba_ruc'] = $a->contrato_traba_ruc;
			$row['contrato_traba_direccion'] = $a->contrato_traba_direccion;

		} 
		
		$row['contrato_fecha_inicio'] = date('d/m/Y');
		$row['contrato_fecha_fin'] = date('d/m/Y');
		$row['contrato_fecha_emision'] = date('d/m/Y');

		echo json_encode(array(
			'data'=>array($row)
		));
	}
	
	public function getRepreContratoNewRow () {
		$row = array();
		$row['repre_contrato_id'] = 0;
		$row['repre_contrato_dni'] = '';
		$row['repre_contrato_apenom'] = '';
		$row['repre_contrato_gradoacad'] = '';
		$row['repre_contrato_docref'] = '';
		$row['repre_contrato_cargo'] = strtoupper('GERENTE DE ADMINISTRACION Y FINANZAS');
		$row['repre_contrato_fecha'] = date('d/m/Y');
		$row['repre_contrato_estado'] = 'ACTIVO';

		echo json_encode(array(
			'data'=>array($row)
		));
	}

	public function getNumero () {
		$p_anio = $this->input->post('anio');
		$p_tipo_contrato_id = $this->input->post('tipo_contrato_id');

		$ultimo_numero_x_tipo_anio = $this->db
		->select('contrato_numero AS value')
		->where('tipo_contrato_id', $p_tipo_contrato_id)
		->where('contrato_anio', $p_anio)
		->order_by('contrato_numero', 'DESC')
		->get('rh.contrato')->row();
		
		if (is_null($ultimo_numero_x_tipo_anio)) {
			$numero = '0001';
		} else {
			$numero = str_pad(intval($ultimo_numero_x_tipo_anio->value)+1, 4, '0', STR_PAD_LEFT);
		}

		die(json_encode(array(
			'success'=>true,
			'numero'=>$numero
		)));
	}

	public function getRow ($id){
		$row = $this->model->get_row($id);
		if ($row->contrato_id_parent > 0) { // adenda!
			$a = $this->model->get_row($row->contrato_id_parent);
			$row->p_contrato_tipo_numero_anio = $a->tipo_contrato_desc.' '.$a->contrato_numero.'-'.$a->contrato_anio;
			$row->p_contrato_fecha_emision = $a->contrato_fecha_emision;
			$row->p_contrato_fecha_inicio = $a->contrato_fecha_inicio;
			$row->p_contrato_fecha_fin = $a->contrato_fecha_fin;
			$row->p_contrato_cargo = $a->contrato_cargo;
			$row->p_contrato_dependencia = $a->contrato_dependencia;
		}

		$entidad = $this->db->get('sys.config')->row();
		if (is_null($entidad)) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"No hay datos de la Entidad"
			)));
		}

		$row->entidad_nombre = strtoupper($entidad->entidad_nombre);
		$row->entidad_ruc = $entidad->entidad_ruc;
		$row->entidad_direccion = $entidad->entidad_direccion;

		die(json_encode(array(
			'data'=>array($row)
		)));
	}

	public function Add() {
		sys_session_hasRoleOrDie('rh.contrato.add, rh.contrato.update');
		$data = array(
			'tipo_contrato_id'=>$this->input->post('tipo_contrato_id'),
  			'contrato_anio'=>$this->input->post('contrato_anio'),
			'contrato_numero'=>$this->input->post('contrato_numero'),
			'repre_contrato_id'=>$this->input->post('repre_contrato_id'),

			'contrato_traba_cod'=>$this->input->post('contrato_traba_cod'),
			'contrato_traba_dni'=>$this->input->post('contrato_traba_dni'),
			'contrato_traba_apenom'=>strtoupper($this->input->post('contrato_traba_apenom')),
			'contrato_traba_ruc'=>$this->input->post('contrato_traba_ruc'),
			'contrato_traba_direccion'=>strtoupper($this->input->post('contrato_traba_direccion')),

			'contrato_fecha_inicio'=>$this->input->post('contrato_fecha_inicio'),
			'contrato_fecha_fin'=>$this->input->post('contrato_fecha_fin'),
			'contrato_fecha_emision'=>$this->input->post('contrato_fecha_emision'),
			'plantilla_id'=>$this->input->post('plantilla_id'),
			'contrato_estado'=>'REGISTRADO',
		);

		if (trim($data['contrato_numero'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Numero del Contrato",
				'target_id'=>'contrato_form_contrato_numero_field'
			)));
		}

		$data['contrato_numero'] = substr('0000'.$data['contrato_numero'], -4);

		if (trim($data['contrato_fecha_inicio'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la Fecha de Inicio",
				'target_id'=>'contrato_form_contrato_fecha_inicio_field'
			)));
		}

		if (trim($data['contrato_fecha_fin'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la Fecha de Fin",
				'target_id'=>'contrato_form_contrato_fecha_fin_field'
			)));
		}

		if (trim($data['contrato_fecha_emision'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la Fecha de Emision",
				'target_id'=>'contrato_form_contrato_fecha_emision_field'
			)));
		}

		/*$from = 'áéíóúñ';
		$to = 'ÁÉÍÓÚÑ';
		$data['geb_solicitante'] = strtr(strtoupper($data['geb_solicitante']), $from, $to);*/

		switch ($data['tipo_contrato_id']) {
			case '01':
				$data['contrato_nivel_ocupacional'] = strtoupper($this->input->post('contrato_nivel_ocupacional'));
				$data['contrato_categoria'] = strtoupper($this->input->post('contrato_categoria'));

				if (trim($data['contrato_nivel_ocupacional'])=='') {
					die(json_encode(array(
						'success'=>false,
						'msg'=>"Especifique el Nivel Ocupacional",
						'target_id'=>'contrato_form_contrato_nivel_ocupacional_field'
					)));
				}

				if (trim($data['contrato_categoria'])=='') {
					die(json_encode(array(
						'success'=>false,
						'msg'=>"Especifique la Categoria",
						'target_id'=>'contrato_form_contrato_categoria_field'
					)));
				}
				break;
			case '02': // CAS
				$data['contrato_cargo'] = strtoupper($this->input->post('contrato_cargo'));
				$data['contrato_dependencia'] = strtoupper($this->input->post('contrato_dependencia'));
				$data['contrato_area'] = strtoupper($this->input->post('contrato_area'));
				$data['contrato_convocatoria'] = $this->input->post('contrato_convocatoria');
				$data['contrato_monto'] = trim($this->input->post('contrato_monto'));
				if ($data['contrato_monto']=='') {
					die(json_encode(array(
						'success'=>false,
						'msg'=>"Especifique el Monto.",
						'target_id'=>'contrato_form_contrato_monto_field'
					)));	
				} elseif(!is_numeric($data['contrato_monto'])) {
					die(json_encode(array(
						'success'=>false,
						'msg'=>"Especifique un valor numerico para el Monto.",
						'target_id'=>'contrato_form_contrato_monto_field'
					)));	
				}
				$data['contrato_monto_letras'] = num2letras($this->input->post('contrato_monto'));

				if (trim($data['contrato_cargo'])=='') {
					die(json_encode(array(
						'success'=>false,
						'msg'=>"Especifique el Cargo",
						'target_id'=>'contrato_form_contrato_cargo_field'
					)));
				}
				if (trim($data['contrato_dependencia'])=='') {
					die(json_encode(array(
						'success'=>false,
						'msg'=>"Especifique la Dependencia",
						'target_id'=>'contrato_form_contrato_dependencia_field'
					)));
				}
				if (trim($data['contrato_area'])=='') {
					die(json_encode(array(
						'success'=>false,
						'msg'=>"Especifique el Area",
						'target_id'=>'contrato_form_contrato_area_field'
					)));
				}

				break;
			case '03'; // ADENDA
				$data['contrato_id_parent'] = $this->input->post('contrato_id_parent');
				$data['tipo_adenda_id'] = $this->input->post('tipo_adenda_id');
				$data['contrato_docref'] = $this->input->post('contrato_docref');
				$data['contrato_tiempo'] = $this->input->post('contrato_tiempo');

				if (!($data['contrato_id_parent'] > 0)) {
					die(json_encode(array(
						'success'=>false,
						'msg'=>"Falta el Contrato Original de la Adenda"
					)));
				}

				if (!($data['tipo_adenda_id'] > 0)) {
					die(json_encode(array(
						'success'=>false,
						'msg'=>"Especifique el Tipo de Adenda"
					)));
				}

				if (trim($data['contrato_docref'])=='') {
					die(json_encode(array(
						'success'=>false,
						'msg'=>"Especifique el Documento de Referencia de la Adenda",
						'target_id'=>'contrato_form_contrato_docref_field'
					)));
				}

				if (trim($data['contrato_tiempo'])=='') {
					die(json_encode(array(
						'success'=>false,
						'msg'=>"Especifique el Tiempo de prorroga",
						'target_id'=>'contrato_form_contrato_tiempo_field'
					)));
				}

				break;
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
		sys_session_hasRoleOrDie('rh.contrato.update');
		$data = array(
			'contrato_id'=>$this->input->post('contrato_id'),
			'tipo_contrato_id'=>$this->input->post('tipo_contrato_id'),
			'plantilla_id'=>$this->input->post('plantilla_id'),
  			//'contrato_anio'=>date('Y'), //$this->input->post('contrato_anio'),
			'contrato_numero'=>$this->input->post('contrato_numero'),
			'repre_contrato_id'=>$this->input->post('repre_contrato_id'),

			'contrato_traba_cod'=>$this->input->post('contrato_traba_cod'),
			'contrato_traba_dni'=>$this->input->post('contrato_traba_dni'),
			'contrato_traba_apenom'=>strtoupper($this->input->post('contrato_traba_apenom')),
			'contrato_traba_ruc'=>$this->input->post('contrato_traba_ruc'),
			'contrato_traba_direccion'=>strtoupper($this->input->post('contrato_traba_direccion')),

			'contrato_fecha_inicio'=>$this->input->post('contrato_fecha_inicio'),
			'contrato_fecha_fin'=>$this->input->post('contrato_fecha_fin'),
			'contrato_fecha_emision'=>$this->input->post('contrato_fecha_emision')
			//'contrato_estado'=>'GENERADO',
		);

		$contrato = $this->db->where('contrato_id', $data['contrato_id'])->get('rh.contrato')->row();

		if ($contrato->contrato_estado != 'REGISTRADO') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Solo es posible modificar un contrato en estado REGISTRADO."
			)));
		}

		if (trim($data['contrato_numero'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Numero del Contrato",
				'target_id'=>'contrato_form_contrato_numero_field'
			)));
		}

		$data['contrato_numero'] = substr('0000'.$data['contrato_numero'], -4);

		if (trim($data['contrato_fecha_inicio'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la Fecha de Inicio",
				'target_id'=>'contrato_form_contrato_fecha_inicio_field'
			)));
		}

		if (trim($data['contrato_fecha_fin'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la Fecha de Fin",
				'target_id'=>'contrato_form_contrato_fecha_fin_field'
			)));
		}

		if (trim($data['contrato_fecha_emision'])=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la Fecha de Emision",
				'target_id'=>'contrato_form_contrato_fecha_emision_field'
			)));
		}

		/*$from = 'áéíóúñ';
		$to = 'ÁÉÍÓÚÑ';
		$data['geb_solicitante'] = strtr(strtoupper($data['geb_solicitante']), $from, $to);*/

		switch ($data['tipo_contrato_id']) {
			case '01':
				$data['contrato_nivel_ocupacional'] = strtoupper($this->input->post('contrato_nivel_ocupacional'));
				$data['contrato_categoria'] = strtoupper($this->input->post('contrato_categoria'));

				if (trim($data['contrato_nivel_ocupacional'])=='') {
					die(json_encode(array(
						'success'=>false,
						'msg'=>"Especifique el Nivel Ocupacional",
						'target_id'=>'contrato_form_contrato_nivel_ocupacional_field'
					)));
				}

				if (trim($data['contrato_categoria'])=='') {
					die(json_encode(array(
						'success'=>false,
						'msg'=>"Especifique la Categoria",
						'target_id'=>'contrato_form_contrato_categoria_field'
					)));
				}
				break;
			case '02': // CAS
				$data['contrato_cargo'] = strtoupper($this->input->post('contrato_cargo'));
				$data['contrato_dependencia'] = strtoupper($this->input->post('contrato_dependencia'));
				$data['contrato_area'] = strtoupper($this->input->post('contrato_area'));
				$data['contrato_convocatoria'] = $this->input->post('contrato_convocatoria');
				$data['contrato_monto'] = $this->input->post('contrato_monto');
				$data['contrato_monto_letras'] = num2letras($this->input->post('contrato_monto'));

				if (trim($data['contrato_cargo'])=='') {
					die(json_encode(array(
						'success'=>false,
						'msg'=>"Especifique el Cargo",
						'target_id'=>'contrato_form_contrato_cargo_field'
					)));
				}
				if (trim($data['contrato_dependencia'])=='') {
					die(json_encode(array(
						'success'=>false,
						'msg'=>"Especifique la Dependencia",
						'target_id'=>'contrato_form_contrato_dependencia_field'
					)));
				}
				if (trim($data['contrato_area'])=='') {
					die(json_encode(array(
						'success'=>false,
						'msg'=>"Especifique el Area",
						'target_id'=>'contrato_form_contrato_area_field'
					)));
				}

				break;
			case '03'; // ADENDA
				$data['contrato_id_parent'] = $this->input->post('contrato_id_parent');
				$data['tipo_adenda_id'] = $this->input->post('tipo_adenda_id');
				$data['contrato_docref'] = $this->input->post('contrato_docref');
				$data['contrato_tiempo'] = $this->input->post('contrato_tiempo');

				if (!($data['contrato_id_parent'] > 0)) {
					die(json_encode(array(
						'success'=>false,
						'msg'=>"Falta el Contrato Original de la Adenda"
					)));
				}

				if (!($data['tipo_adenda_id'] > 0)) {
					die(json_encode(array(
						'success'=>false,
						'msg'=>"Especifique el Tipo de Adenda"
					)));
				}

				if (trim($data['contrato_docref'])=='') {
					die(json_encode(array(
						'success'=>false,
						'msg'=>"Especifique el Documento de Referencia de la Adenda",
						'target_id'=>'contrato_form_contrato_docref_field'
					)));
				}

				if (trim($data['contrato_tiempo'])=='') {
					die(json_encode(array(
						'success'=>false,
						'msg'=>"Especifique el Tiempo de prorroga",
						'target_id'=>'contrato_form_contrato_tiempo_field'
					)));
				}

				break;
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

	public function repreContratoUpdate() {
		sys_session_hasRoleOrDie('rh.contrato.modify');
		$data = array(
			'repre_contrato_id'=>$this->input->post('repre_contrato_id'),
			'repre_contrato_dni'=>$this->input->post('repre_contrato_dni'),
			'repre_contrato_apenom'=>$this->input->post('repre_contrato_apenom'),
			'repre_contrato_gradoacad'=>$this->input->post('repre_contrato_gradoacad'),

			'repre_contrato_cargo'=>$this->input->post('repre_contrato_cargo'),
			'repre_contrato_fecha'=>$this->input->post('repre_contrato_fecha'),
			'repre_contrato_estado'=>$this->input->post('repre_contrato_estado')
		);

		$from = 'áéíóúñ';
		$to = 'ÁÉÍÓÚÑ';
		foreach ($data as $i=>$v) {
			$data[$i] = strtr(trim(strtoupper($v)), $from, $to);
		}
		$data['repre_contrato_docref'] = trim($this->input->post('repre_contrato_docref'));

		if ($data['repre_contrato_dni']=='' || strlen($data['repre_contrato_dni'])!=8) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique un numero valido para el DNI",
				'target_id'=>'repre_contrato_dni_field'
			)));
		}

		if ($data['repre_contrato_apenom']=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique los apellidos y nombres",
				'target_id'=>'repre_contrato_apenom_field'
			)));
		}

		if ($data['repre_contrato_gradoacad']=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la abreviacion del grado academico.",
				'target_id'=>'repre_contrato_gradoacad_field'
			)));
		}

		if ($data['repre_contrato_cargo']=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el cargo.",
				'target_id'=>'repre_contrato_cargo_field'
			)));
		}

		if ($data['repre_contrato_docref']=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el documento de referencia del nombramiento o encargatura.",
				'target_id'=>'repre_contrato_docref_field'
			)));
		}

		if ($data['repre_contrato_fecha']=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la fecha de inicio del cargo.",
				'target_id'=>'repre_contrato_fecha_field'
			)));
		}

		if ($data['repre_contrato_id'] > 0) {
			$contrato_count = $this->db->select('COUNT(*) AS value')->where('repre_contrato_id', $data['repre_contrato_id'])->get('rh.contrato')->row();
			if ($contrato_count->value > 0) {
				die(json_encode(array(
					'success'=>false,
					'msg'=>"No es posible modificar los valores de este registro porque ya esta siendo usado. Contactese con el administrador del sistema."
				)));		
			}
		}

		try {
			if ($data['repre_contrato_id'] > 0) {
				$result = $this->model->repre_contrato_update($data);
			} else {
				unset($data['repre_contrato_id']);
				$result = $this->model->repre_contrato_add($data);
			}
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

	public function repreContratoDelete() {
		sys_session_hasRoleOrDie('rh.contrato.modify');
		$repre_contrato_id = $this->input->post('repre_contrato_id');
		$contrato_count = $this->db->select('COUNT(*) AS value')->where('repre_contrato_id', $repre_contrato_id)->get('rh.contrato')->row();
		if ($contrato_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"No es posible eliminar el representante porque ya esta siendo usado. Contactese con el administrador del sistema."
			)));		
		}

		try {
			$this->db->where('repre_contrato_id', $repre_contrato_id)->delete('rh.repre_contrato');
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

	public function Emitir() {
		sys_session_hasRoleOrDie('rh.contrato.modify');
		$data = array(
			'contrato_id'=>$this->input->post('contrato_id'),
			'contrato_estado'=>'EMITIDO'
		);

		$r = $this->model->get_row($data['contrato_id']);
		if ($r->contrato_estado != 'GENERADO') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Solo se puede emitir un contrato con estado 'GENERADO'."
			)));
		}
		$fe = explode('/', $r->contrato_fecha_emision);
		$ts_fecha_emision = strtotime("{$fe[2]}-{$fe[1]}-{$fe[0]}");
		$ff = explode('/', $r->contrato_fecha_fin);
		$ts_fecha_fin = strtotime("{$ff[2]}-{$ff[1]}-{$ff[0]}");
		//var_dump($ts_fecha_emision); var_dump($ts_fecha_inicio);
		if ($ts_fecha_emision > $ts_fecha_fin) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"La fecha de emision del contrato debe ser menor o igual a la fecha de termino del contrato."
			)));
		}
		
		$result = $this->model->update($data);

		if ($result !== false) {
			die(json_encode(array(
				'success'=>true,
				'msg'=>"Se actualizo satisfactoriamente"
			)));
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion."
			)));
		}
	}

	public function CancelarEmitido() {
		sys_session_hasRoleOrDie('rh.contrato.modify');
		$data = array(
			'contrato_id'=>$this->input->post('contrato_id'),
			'contrato_estado'=>'GENERADO'
		);

		$r = $this->model->get_row($data['contrato_id']);
		if ($r->contrato_estado != 'EMITIDO') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Solo se puede cancelar la emision de un contrato 'EMITIDO'."
			)));
		}

		$result = $this->model->update($data);

		if ($result !== false) {
			die(json_encode(array(
				'success'=>true,
				'msg'=>"Se cancelo satisfactoriamente"
			)));
			
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion"
			)));
		}
	}

	public function Entregar() {
		sys_session_hasRoleOrDie('rh.contrato.modify');
		$data = array(
			'contrato_id'=>$this->input->post('contrato_id'),
			'contrato_fecha_entrega'=>$this->input->post('contrato_fecha_entrega'),
			'contrato_estado'=>'ENTREGADO'
		);

		$r = $this->model->get_row($data['contrato_id']);
		if ($r->contrato_estado != 'EMITIDO') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Solo se puede entregar un contrato con estado 'EMITIDO'."
			)));
		}
		$fe = explode('/', $data['contrato_fecha_entrega']);
		$ts_fecha_entrega = strtotime("{$fe[2]}-{$fe[1]}-{$fe[0]}");
		$fem = explode('/', $r->contrato_fecha_emision);
		$ts_fecha_emision = strtotime("{$fem[2]}-{$fem[1]}-{$fem[0]}");

		if ($ts_fecha_entrega < $ts_fecha_emision) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"La fecha de entrega del contrato no pueder ser menor que la fecha de emision."
			)));
		}

		$result = $this->model->update($data);

		if ($result !== false) {
			die(json_encode(array(
				'success'=>true,
				'msg'=>"Se actualizo satisfactoriamente"
			)));
			
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion"
			)));
		}
	}

	public function CancelarEntregado() {
		sys_session_hasRoleOrDie('rh.contrato.modify');
		$data = array(
			'contrato_id'=>$this->input->post('contrato_id'),
			'contrato_estado'=>'EMITIDO',
			'contrato_fecha_entrega'=>null
		);

		$r = $this->model->get_row($data['contrato_id']);
		if ($r->contrato_estado != 'ENTREGADO') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Solo se puede cancelar la entrega de un contrato 'ENTREGADO'."
			)));
		}

		$result = $this->model->update($data);

		if ($result !== false) {
			die(json_encode(array(
				'success'=>true,
				'msg'=>"Se cancelo satisfactoriamente"
			)));
			
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion"
			)));
		}
	}

	public function Anular() {
		sys_session_hasRoleOrDie('rh.contrato.modify');
		$data = array(
			'contrato_id'=>$this->input->post('contrato_id'),
			'contrato_estado'=>'ANULADO'
		);

		$r = $this->model->get_row($data['contrato_id']);
		if ($r->contrato_estado != 'REGISTRADO' && $r->contrato_estado != 'GENERADO') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Solo se puede anular un contrato REGISTRADO o GENERADO ."
			)));
		}

		$result = $this->model->update($data);

		if ($result !== false) {
			die(json_encode(array(
				'success'=>true,
				'msg'=>"Se anulo satisfactoriamente"
			)));
			
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion"
			)));
		}
	}

	public function CancelarAnulado() {
		sys_session_hasRoleOrDie('rh.contrato.modify');
		$data = array(
			'contrato_id'=>$this->input->post('contrato_id'),
			'contrato_estado'=>'REGISTRADO'
		);
		$r = $this->model->get_row($data['contrato_id']);
		if ($r->contrato_pdf != '') {
			$data['contrato_estado'] = 'GENERADO';
		}

		if ($r->contrato_estado != 'ANULADO') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Solo se puede cancelar la anulacion de un contrato 'ANULADO'."
			)));
		}

		$result = $this->model->update($data);

		if ($result !== false) {
			die(json_encode(array(
				'success'=>true,
				'msg'=>"Se cancelo satisfactoriamente"
			)));
			
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion"
			)));
		}
	}

	public function getAdendaList () {
		$contrato_id_parent = $this->input->get('contrato_id_parent');
		$ret = $this->model->get_adenda_list($contrato_id_parent);
		echo json_encode($ret);
	}

	public function getTipoAdendaList () {
		$ret = $this->model->get_tipo_adenda_list();
		echo json_encode($ret);
	}

	public function getTipoContratoList () {
		$ret = $this->model->get_tipo_contrato_list();
		echo json_encode($ret);
	}

	public function getRepreContratoList () {
		$ret = $this->model->get_repre_contrato_list();
		echo json_encode($ret);
	}

	public function getRepreContratoFullList () {
		$ret = $this->model->get_repre_contrato_flist();
		echo json_encode($ret);
	}

	public function getTipoContratoParentList () {
		$ret = $this->model->get_tipo_contrato_parent_list();
		echo json_encode($ret);
	}

	public function getTrabaList () {
		$filter = $this->input->get('query');
		$pagination_size = $this->input->get('limit');
		$pagination_start = $this->input->get('start');
		$ret = $this->model->get_traba_list($filter, $pagination_size, $pagination_start);
		echo json_encode($ret);
	}

	public function getPlantillaList () {
		$p_tipo_contrato_id =  $this->input->get('tipo_contrato_id');
		$p_plantilla_estado =  $this->input->get('plantilla_estado');
		$ret = $this->model->get_plantilla_list($p_tipo_contrato_id, $p_plantilla_estado);
		echo json_encode($ret);
	}
	public function getPlantillaFullList () {
		$ret = $this->model->get_plantilla_full_list();
		$base_url = $this->config->item('base_url');
		foreach ($ret['data'] as $i=>$r) {
			$filename = "/dbfiles/rh.plantilla/{$r->plantilla_archivo}";
			if (file_exists(FCPATH.$filename)) {
				$r->plantilla_archivo_link = "<a href=\"{$base_url}.{$filename}\"><img src=\"{$base_url}/tools/img/word_32.png\" border=\"0\" width=\"24\"/>";	
			} else {
				$r->plantilla_archivo_link = "No existe.";
			}
			
		}
		echo json_encode($ret);
	}

	public function getPlantillaNewRow () {
		$row = array();
		$row['plantilla_id'] = 0;
		$row['plantilla_desc'] = '';
		$row['plantilla_archivo'] = '';
		$row['tipo_contrato_id'] = '01';
		$row['plantilla_estado'] = 'A';

		echo json_encode(array(
			'data'=>array($row)
		));
	}

	public function plantillaUpdate() {
		sys_session_hasRoleOrDie('rh.contrato.modify');
		//var_dump($_FILES);
		$upload_path = 'dbfiles/rh.plantilla/';
		$data = array(
			'plantilla_id'=>$this->input->post('plantilla_id'),
			'plantilla_desc'=>$this->input->post('plantilla_desc'),
			'tipo_contrato_id'=>$this->input->post('tipo_contrato_id'),
			'plantilla_estado'=>$this->input->post('plantilla_estado')
		);
		$uploaded = false;
		if (isset($_FILES['plantilla_file']) && $_FILES['plantilla_file']['name'] != '') {
			$config['upload_path'] = $upload_path;
			$config['allowed_types'] = 'docx'; //gif|jpg|png
			$this->upload->initialize($config);
			if ($this->upload->do_upload('plantilla_file')) {
				$uploaded = true;
				$data['plantilla_archivo'] = $this->upload->data('file_name');
			} else {
				die(json_encode(array(
					'success'=>false,
					'msg'=>'UPLOAD: '.$this->upload->display_errors()
				)));
			}	
		}
		

		if ($data['plantilla_desc']=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la descripcion",
				'target_id'=>'plantilla_desc_field'
			)));
		}

		if ($data['tipo_contrato_id']=='') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Tipo de contrato.",
				'target_id'=>'plantilla_tipo_contrato_id_field'
			)));
		}

		if ($data['plantilla_id'] > 0) {
			$plantilla = $this->db->where('plantilla_id', $data['plantilla_id'])->get('rh.plantilla')->row();
			$plantilla_count = $this->db->select('COUNT(*) AS value')->where('plantilla_id', $data['plantilla_id'])->get('rh.contrato')->row();
			if ($plantilla_count->value > 0) {
				if (
					$data['tipo_contrato_id'] != $plantilla->tipo_contrato_id
					//|| $uploaded // el archivo si. porke ahora se generan los pdfs
				) {
					if ($uploaded) {
						unlink($upload_path.$data['plantilla_archivo']);
					}
					die(json_encode(array(
						'success'=>false,
						//'msg'=>"No es posible modificar el los valores del Tipo de contrato ni del Archivo, porque el registro ya esta siendo usado. Contactese con el administrador del sistema."
						'msg'=>"No es posible modificar el Tipo de contrato, porque el registro ya esta siendo usado. Contactese con el administrador del sistema."
					)));		
				}
			}
		}

		try {
			if ($data['plantilla_id'] > 0) {
				$plantilla = $this->db->where('plantilla_id', $data['plantilla_id'])->get('rh.plantilla')->row(); // before
				$result = $this->model->plantilla_update($data);
				// file changed?
				if ($uploaded && $data['plantilla_archivo'] != $plantilla->plantilla_archivo) {
					$old_file = $upload_path.$plantilla->plantilla_archivo;
					if (file_exists($old_file)) {
						// delete old file
						unlink($old_file);
					}
				}
			} else {
				unset($data['plantilla_id']);
				$result = $this->model->plantilla_add($data);
			}
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

	public function plantillaDelete() {
		sys_session_hasRoleOrDie('rh.contrato.modify');
		$plantilla_id = $this->input->post('plantilla_id');
		$plantilla_count = $this->db->select('COUNT(*) AS value')->where('plantilla_id', $plantilla_id)->get('rh.contrato')->row();
		if ($plantilla_count->value > 0) {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"No es posible eliminar la plantilla porque ya esta siendo usado. Contactese con el administrador del sistema."
			)));		
		}

		try {
			$this->db->where('plantilla_id', $plantilla_id)->delete('rh.plantilla');
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

	public function printPreview_old() {
		$contrato_id = $this->input->post('contrato_id');
		$c = $this->model->get_row($contrato_id);
		switch ($c->tipo_contrato_id) {
			case '01':
				$url = $this->config->item('rpt_server')."/rpt_contrato_ctct_pdf.jsp?id={$contrato_id}";	
			break;
			case '02':
				$url = $this->config->item('rpt_server')."/rpt_contrato_cas_pdf.jsp?id={$contrato_id}";	
			break;
			case '03':
				$url = $this->config->item('rpt_server')."/rpt_contrato_adenda_pdf.jsp?id={$contrato_id}";	
			break;
		}
		echo "<embed src=\"{$url}\" type=\"application/pdf\" width=\"100%\" height=\"100%\"></embed>";
		die(FCPATH);
		die(BASEPATH);
		die(dirname(__FILE__));
	}

	private function generarPDF($contrato_id) {
		$c = $this->db->where('contrato_id', $contrato_id)->get('rh.v_contrato_4_print')->row(0, 'array');
		$tc = $this->db->where('tipo_contrato_id', $c['tipo_contrato_id'])->get('rh.tipo_contrato')->row();
		$p = $this->db->where('plantilla_id', $c['plantilla_id'])->get('rh.plantilla')->row();
		$cfg = $this->db->where('config_id', 1)->get('sys.config')->row(0, 'array');
		$cfg['entidad_nombre_mayus'] = strtoupper($cfg['entidad_nombre']);
		$adenda_list = $this->db
		->where('contrato_id_parent', $c['contrato_id_parent'])
		->where('contrato_fecha_fin <', $c['contrato_fecha_inicio'])
		->where('tipo_contrato_id', '03') // adenda
		->get('rh.contrato')->result();
		$adendas_anteriores = '';
		foreach ($adenda_list as $i => $r) {
			$adendas_anteriores .= "-  {$r->contrato_docref}, prorroga de contrato N° {$r->contrato_numero}-{$r->contrato_anio} desde el {$r->contrato_fecha_inicio} al {$r->contrato_fecha_fin}".'<w:br/>';
		}
		if (count($adenda_list) > 0) {
			$adendas_anteriores = '<w:br/>'.$adendas_anteriores;
		}
		$t = new TemplateProcessor(FCPATH.'dbfiles/rh.plantilla/'.$p->plantilla_archivo);
		$var_list = $t->getVariables();
	    foreach ($var_list as $key => $value) {
	        if (array_key_exists($value, $c)) {
	            $t->setValue($value, $c[$value]);
	        } elseif (array_key_exists($value, $cfg)) {
	        	$t->setValue($value, $cfg[$value]);
	        } elseif ($value=='adendas_anteriores_info') {
	        	$t->setValue($value, $adendas_anteriores);
	        } else {
	            die("C{$contrato_id}: Falta parametro $value.");
	        }
	    }
	    // --- Guardamos el documento
	    $filename = strtolower($tc->tipo_contrato_abrev).'_'.$c['contrato_anio'].'_'.$c['contrato_numero'].'_'.microtime(true);
	    $t->saveAs("tmp/{$filename}.docx");
	    // to PDF
	    $word = new COM("Word.Application") or die ("MS Word: Could not initialise Object.");
	    $word->Visible = 0;
	    $word->DisplayAlerts = 0;
	    $r = $word->Documents->Open(FCPATH."tmp/{$filename}.docx");
	    $word->ActiveDocument->ExportAsFixedFormat(FCPATH."dbfiles/rh.contrato/{$filename}.pdf", 17, false, 0, 0, 0, 0, 7, true, true, 2, true, true, false);
	    $word->Quit(false);
	    unset($word);
	    return $filename.'.pdf';
	}

	public function getListForGenPDF() {
		$p_contrato_anio = $this->input->get('contrato_anio');
		$p_tipo_contrato_id = $this->input->get('tipo_contrato_id');
		//$search_text = $this->input->get('search_text');
		//$p_tipo_contrato_id = $this->input->get('tipo_contrato_id');
		$ret = $this->model->get_list_for_gen_pdf($p_contrato_anio, $p_tipo_contrato_id);
		echo json_encode($ret);
	}
	// ya no se usa
	public function generarPDFs() {
		sys_session_hasRoleOrDie('sa');
		$rows = $this->db
		->where('contrato_pdf', '')
		//->where_in('contrato_estado', array('REGISTRADO', 'GENERADO'))
		->order_by('contrato_anio', 'DESC')
		->order_by('tipo_contrato_id', 'ASC')
		->order_by('contrato_numero', 'DESC')
		->order_by('contrato_id', 'DESC')
		->limit(50)
		->get('rh.contrato')->result();
		foreach ($rows as $i => $r) {
			$filename = $this->generarPDF($r->contrato_id);	
			$this->db
			->set('contrato_pdf', $filename)
			//->set('contrato_estado', 'GENERADO')
			->where('contrato_id', $r->contrato_id)
			->update('rh.contrato');
		}
		$count = count($rows);
		die(json_encode(array(
			'success'=>true,
			'msg'=>"Se han generado {$count} PDFs satisfactoriamente."
		)));		
	}

	public function regenerarPDF() {
		sys_session_hasRoleOrDie('sa, rh.contrato.regenerar_pdf');
		$p_contrato_id = $this->input->post('contrato_id');
		$contrato = $this->db->where('contrato_id', $p_contrato_id)->get('rh.contrato')->row();
		$filename = $this->generarPDF($contrato->contrato_id);	
		$this->db
		->set('contrato_pdf', $filename)
		->where('contrato_id', $contrato->contrato_id)
		->update('rh.contrato');
		//die("Se han generado el PDF satisfactoriamente.");
		die(json_encode(array(
			'success'=>true,
			'msg'=>"Se han generado el PDF satisfactoriamente."
		)));		
	}

	public function printPreview() {
		//die($this->config->item('base_url'));
		//if (file_exists('tmp/archivo.txt')) { die(file_get_contents('tmp/archivo.txt')); } else { die('no'); }
		//die(FCPATH);
		$p_contrato_id = $this->input->post('contrato_id');
		$contrato = $this->db->where('contrato_id', $p_contrato_id)->get('rh.contrato')->row();
		$filename = $contrato->contrato_pdf;
		if ($contrato->contrato_estado == 'REGISTRADO') {
			$filename = $this->generarPDF($p_contrato_id);	
			$this->db
			->set('contrato_pdf', $filename)
			->set('contrato_estado', 'GENERADO')
			->where('contrato_id', $p_contrato_id)
			->update('rh.contrato');
			//$reload_list = "<script type=\"text/javascript\">contrato.reload_list({$p_contrato_id})</script>";
		} 
		if (file_exists(FCPATH."dbfiles/rh.contrato/".$filename) && $filename != '') {
			$url = $this->config->item('base_url')."dbfiles/rh.contrato/{$filename}";
			echo "<embed src=\"{$url}\" type=\"application/pdf\" width=\"100%\" height=\"100%\"></embed>";
		} else {
			echo "No es posible mostrar el archivo '{$filename}'.";
		}
	}

	public function cancelarGenerado() {
		sys_session_hasRoleOrDie('rh.contrato.modify');
		$data = array(
			'contrato_id'=>$this->input->post('contrato_id'),
			'contrato_estado'=>'REGISTRADO',
			'contrato_pdf'=>''
		);
		$r = $this->model->get_row($data['contrato_id']);
		if ($r->contrato_estado != 'GENERADO') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Solo se puede cancelar un contrato 'GENERADO'."
			)));
		}

		$result = $this->model->update($data);

		if ($result !== false) {
			if (file_exists(FCPATH."dbfiles/rh.contrato/".$r->contrato_pdf) && $r->contrato_pdf != '') {
				unlink(FCPATH."dbfiles/rh.contrato/".$r->contrato_pdf);
			}
			die(json_encode(array(
				'success'=>true,
				'msg'=>"Se cancelo satisfactoriamente"
			)));
			
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion"
			)));
		}
	}
}
?>