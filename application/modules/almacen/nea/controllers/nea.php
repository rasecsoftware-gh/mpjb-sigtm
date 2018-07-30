<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NEA extends MX_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('m_nea','model');
	}

	public function index() {
		$data = array();
		$this->load->view('v_nea', $data);
	}

	public function setAnio() {
		$anio = $this->input->post('anio');
		$data  = array(
			"nea_anio" => $anio
		);
		$this->session->set_userdata($data);
		die(json_encode(array(
			'success'=>true
		)));
	}

	public function getList () {
		$search_by = $this->input->get('search_by');
		$search_text = $this->input->get('search_text');
		$pagination_size = $this->input->get('limit');
    	$pagination_start = $this->input->get('start');
		$ret = $this->model->get_list($search_by, $search_text, $pagination_size, $pagination_start);

		$rows = $ret['data'];
		foreach($rows as $i=>$r) {
			$r->nemo_cod_desc = $r->nemo_anio.'-'.$r->nemo_cod.': '.$r->nemo_desc;
			$nd_count = $this->db->select('COUNT(*) AS value')->where('nea_id', $r->nea_id)->get('alm.nea_det')->row();
			$r->nea_det_count = $nd_count->value;
			$n_total = $this->db->select('SUM(nea_det_total) AS value')->where('nea_id', $r->nea_id)->get('alm.nea_det')->row();
			$r->nea_total = $n_total->value;
		}
		$ret['data'] = $rows;
		echo json_encode($ret);
	}

	public function getNewRow (){
		$row = $this->model->get_new_row();
		echo json_encode(array(
			'data'=>array($row)
		));
	}

	public function getRow ($id){
		$row = $this->model->get_row($id);
		if ($row->nemo_cod == '') {
			$row->nemo_anio_cod_desc = '';
		} else {
			$row->nemo_anio_cod_desc = $row->nemo_anio." - ".$row->nemo_cod." | ".$row->nemo_desc;
		}
		echo json_encode(array(
			'data'=>array($row)
		));
	}

	public function Add() {
		sys_session_hasRoleOrDie(array('sa', 'nea'));

		$data = array(
  			'nea_fecha'=>$this->input->post('nea_fecha'),
  			'tipo_nea_id'=>$this->input->post('tipo_nea_id'),
			'nea_procedencia'=>$this->input->post('nea_procedencia'),
			'nemo_anio'=>$this->input->post('nemo_anio'),
			'nemo_cod'=>$this->input->post('nemo_cod'),
			'nemo_desc'=>$this->input->post('nemo_desc'),
			'nemo_secfun'=>$this->input->post('nemo_secfun'),
			'nemo_meta'=>$this->input->post('nemo_meta'),
			'nea_observacion'=>$this->input->post('nea_observacion')
		);

		// commented why nemo_id can be null!
		/*if (trim($data['nemo_cod'])=='') {
			die (json_encode(array(
				'success'=>false,
				'msg'=>"Especifique el Centro de costo",
				'target_id'=>'nea_form_nemo_desc'
			)));
		}*/

		if (trim($data['nea_fecha'])=='') {
			die (json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la fecha",
				'target_id'=>'nea_form_nea_fecha'
			)));
		}

		if (trim($data['nea_procedencia'])=='') {
			die (json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la procedencia",
				'target_id'=>'nea_form_nea_procedencia'
			)));
		}

		$from = 'áéíóúñ';
		$to = 'ÁÉÍÓÚÑ';
		$data['nea_procedencia'] = strtr(strtoupper($data['nea_procedencia']), $from, $to);
		$data['nea_observacion'] = strtr(strtoupper($data['nea_observacion']), $from, $to);
		
		$result = $this->model->add($data);

		if ($result !== false) {
			$ret = array (
				'success'=>true,
				'msg'=>"Se registro satisfactoriamente",
				'rowid'=>$result
			);
			
		} else {
			$ret = array (
				'success'=>false,
				'msg'=>"Error al registrar el proceso",
				'rowid'=>0
			);
		}
		echo json_encode($ret);
	}

	public function Update() {
		sys_session_hasRoleOrDie(array('sa', 'nea'));

		$data = array(
			'nea_id'=>$this->input->post('nea_id'),
  			'nea_fecha'=>$this->input->post('nea_fecha'),
			'nea_observacion'=>$this->input->post('nea_observacion')
		);

		$nea = $this->model->get_row($data['nea_id']);
		if ($nea->nea_estado == 'APROBADO' || $nea->nea_estado == 'ANULADO') {
			die (json_encode(array(
				'success'=>false,
				'msg'=>"No es posible modificar un documento Aprobado o Anulado."
			)));
		}

		if (trim($data['nea_fecha'])=='') {
			die (json_encode(array(
				'success'=>false,
				'msg'=>"Especifique la fecha",
				'target_id'=>'nea_form_nea_fecha'
			)));
		}

		$from = 'áéíóúñ';
		$to = 'ÁÉÍÓÚÑ';
		$data['nea_observacion'] = strtr(strtoupper($data['nea_observacion']), $from, $to);

		$result = $this->model->update($data);

		if ($result !== false) {
			die (json_encode(array(
				'success'=>true,
				'msg'=>"Se actualizo satisfactoriamente",
				'row_id'=>$result
			)));
			
		} else {
			die (json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion",
				'row_id'=>0
			)));
		}
	}

	public function changeNemo() {
		sys_session_hasRoleOrDie(array('sa', 'nea'));

      	$nea_id = $this->input->post('nea_id');

      	$data = array(
  			'nea_id'=>$this->input->post('nea_id'),
			'nemo_anio'=>$this->input->post('nemo_anio'),
			'nemo_cod'=>$this->input->post('nemo_cod'),
			'nemo_desc'=>$this->input->post('nemo_desc'),
			'nemo_secfun'=>$this->input->post('nemo_secfun'),
			'nemo_meta'=>$this->input->post('nemo_meta')
		);

		$nea = $this->model->get_row($nea_id);

		if ($nea->nea_estado == 'ANULADO') {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"No es posible modificar un documento anulado."
			)));
		}
		if ($nea->nea_estado == 'APROBADO') {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"No es posible modificar un documento aprobado."
			)));
		}

		$nd_list = $this->db->query("SELECT nea_det_id FROM alm.nea_det WHERE nea_id = $nea_id AND NOT oc_det_id IS NULL")->result();

		if (count($nd_list) > 0) {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"No es posible cambiar el Centro de costo porque la NEA tiene detalles de Ordenes de Compra, elimine esos detalles e intente denuevo."
			)));
		}

		$result = $this->model->change_nemo($data);

		if ($result !== false) {
			die(json_encode(array (
				'success'=>true,
				'msg'=>"Se cambio y guardo el centro de costo satisfactoriamente."
			)));
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion."
			)));
		}
  	}

	public function Aprobar() {
		sys_session_hasRoleOrDie(array('sa', 'nea-aprobador'));

      	$nea_id = $this->input->post('nea_id');

		$r = $this->model->get_row($nea_id);

		if ($r->nea_estado == 'ANULADO') {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"No es posible Aprobar un documento anulado."
			)));
		}
		if ($r->nea_estado == 'APROBADO') {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"El documento ya se encuentra aprobado."
			)));
		}

		$result = $this->model->aprobar($nea_id);

		if ($result !== false) {
			die(json_encode(array (
				'success'=>true,
				'msg'=>"Se aprobo satisfactoriamente.",
				'rowid'=>$nea_id
			)));
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion."
			)));
		}
  	}

  	public function cancelarAprobado() {
  		sys_session_hasRoleOrDie(array('sa', 'nea-aprobador'));

		$nea_id = $this->input->post('nea_id');

		$r = $this->model->get_row($nea_id);

		if ($r->nea_estado == 'ANULADO') {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"No es posible modificar un documento anulado."
			)));
		}
		if ($r->nea_estado == 'GENERADO') {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"El documento no se encuentra aprobado."
			)));
		}

		$result = $this->model->cancelar_aprobado($nea_id);

		if ($result !== false) {
			die(json_encode(array (
				'success'=>true,
				'msg'=>"Se cancelo la aprobacion satisfactoriamente.",
				'rowid'=>$nea_id
			)));
		} else {
			die(json_encode(array (
				'success'=>false,
				'msg'=>"Error al realizar la operacion."
			)));
		}
  	}

	public function Anular() {
		sys_session_hasRoleOrDie(array('sa', 'nea-anulador'));

		$nea_id = $this->input->post('nea_id');

		$r = $this->model->get_row($nea_id);

		if ($r->nea_estado == 'ANULADO') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Ya se encuentra anulado, no es posible realizar la operacion.",
				'rowid'=>$nea_id
			)));
		}

		$result = $this->model->anular($nea_id);

		if ($result !== false) {
			die(json_encode(array(
				'success'=>true,
				'msg'=>"Se anulo satisfactoriamente.",
				'rowid'=>$nea_id
			)));
		} else {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Error al realizar la operacion.",
				'rowid'=>0
			)));
		}
  	}

  	public function cancelarAnulado() {
  		sys_session_hasRoleOrDie(array('sa', 'nea-anulador'));

		$nea_id = $this->input->post('nea_id');

		$r = $this->model->get_row($nea_id);

		if ($r->nea_estado == 'GENERADO') {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"El documento no esta anulado, no es posible realizar la operacion.",
				'rowid'=>$nea_id
			)));
		}

		$result = $this->model->cancelar_anulado($nea_id);

		if ($result !== false) {
			die(json_encode(array(
				'success'=>true,
				'msg'=>"Se cancelo la anulacion satisfactoriamente.",
				'rowid'=>$nea_id
			)));
		} else {
			die(json_encode(array(
				'success'=>false,
				'msg'=>"Error al realizar la operacion.",
				'rowid'=>0
			)));
		}
  	}


	public function getSys2009NemoList () {
		$anio = $this->input->get('anio');
		$filter = $this->input->get('query');
		$rows = $this->model->get_sys2009_nemo_list($anio, strtoupper($filter));
		echo '{"data":'.json_encode($rows).'}'; 
	}

	public function getSys2009AreaList () {
		$rows = $this->model->get_sys2009_area_list();
		echo '{"data":'.json_encode($rows).'}';	
	}

	public function getTipoNeaList () {
		$rows = $this->model->get_tipo_nea_list();
		echo '{"data":'.json_encode($rows).'}';	
	}

	public function getProcedenciaList () {
		$filter = $this->input->get('query');
		$rows = $this->model->get_procedencia_list($filter);
		echo '{"data":'.json_encode($rows).'}';	
	}

	public function getNewNeaDetRow (){
		$row = $this->model->get_new_get_det_row();
		echo json_encode(array(
			'data'=>array($row)
		));
	}

	public function getNeaDetRow ($id){
		$row = $this->model->get_nea_det_row($id);
		echo json_encode(array(
			'data'=>array($row)
		));
	}

	public function getNeaDetList ($id) {
		$rows = $this->model->get_nea_det_list($id);
		foreach ($rows as $i=>$row) {
			$row->bs_desc_obs = $row->bs_desc;
			if (trim($row->nea_det_obs)!='') {
				$row->bs_desc_obs .= ' - '.trim($row->nea_det_obs);
			}
			$row->oc_anio_numero = $row->oc_anio.'-'.substr($row->oc_numero, 3, 5);
		}
		echo json_encode(array(
			'data'=>$rows
		));
	}

	public function importNeaDetFromOC() {
		sys_session_hasRoleOrDie(array('sa', 'nea'));

		$nea_id = $this->input->post('nea_id');

		$nea = $this->model->get_row($nea_id);
		if ($nea->nea_estado == 'APROBADO' || $nea->nea_estado == 'ANULADO') {
			die (json_encode(array(
				'success'=>false,
				'msg'=>"No es posible modificar un documento Aprobado o Anulado."
			)));
		}


		$strlist = $this->input->post('strlist');
		$import_list = explode(',', $strlist);
		$total = count($import_list);
  		$result = $this->model->import_nea_det_from_oc($nea_id, $import_list);

  		if ($result !== false) {
  			$extra = '';
  			if ($result < $total) {
  				$extra = "y se actualizaron ".($total-$result)." ";
  			}
  			$ret = array (
  				'success'=>true,
  				'msg'=>"Se importaron $result {$extra} registros satisfactoriamente.",
  				'count'=>$result
  			);
  			
  		} else {
  			$ret = array (
  				'success'=>false,
  				'msg'=>"Error al realizar la operacion",
  				'count'=>0
  			);
  		}
  		echo json_encode($ret);
	}

	public function importNeaDetFromCB() {
		sys_session_hasRoleOrDie(array('sa', 'nea'));

		$nea_id = $this->input->post('nea_id');

		$nea = $this->model->get_row($nea_id);
		if ($nea->nea_estado == 'APROBADO' || $nea->nea_estado == 'ANULADO') {
			die (json_encode(array(
				'success'=>false,
				'msg'=>"No es posible modificar un documento Aprobado o Anulado."
			)));
		}


		$strlist = $this->input->post('strlist');
		$import_list = explode(',', $strlist);
		$total = count($import_list);
  		$result = $this->model->import_nea_det_from_cb($nea_id, $import_list);

  		if ($result !== false) {
  			$extra = '';
  			if ($result < $total) {
  				$extra = "y se actualizaron ".($total-$result)." ";
  			}
  			$ret = array (
  				'success'=>true,
  				'msg'=>"Se importaron $result {$extra} registros satisfactoriamente.",
  				'count'=>$result
  			);
  			
  		} else {
  			$ret = array (
  				'success'=>false,
  				'msg'=>"Error al realizar la operacion",
  				'count'=>0
  			);
  		}
  		echo json_encode($ret);
	}

	public function updateNeaDet() {
		sys_session_hasRoleOrDie(array('sa', 'nea'));

		$nea_det = $this->db->where('nea_det_id', $this->input->post('nea_det_id'))->get('alm.nea_det')->row();
		$nea = $this->model->get_row($nea_det->nea_id);
		if ($nea->nea_estado == 'APROBADO' || $nea->nea_estado == 'ANULADO') {
			die (json_encode(array(
				'success'=>false,
				'msg'=>"No es posible modificar un documento Aprobado o Anulado."
			)));
		}

  		$data = array(
  			'nea_det_id'=>$this->input->post('nea_det_id'),
			'nea_det_cantidad'=>$this->input->post('nea_det_cantidad'),
			'nea_det_precio'=>$this->input->post('nea_det_precio'),
			'nea_det_obs'=>$this->input->post('nea_det_obs')
		);

		$data['nea_det_total'] = floatval($data['nea_det_cantidad'])*floatval($data['nea_det_precio']);

  		$result = $this->model->update_nea_det($data);

  		if ($result !== false) {
  			$ret = array (
  				'success'=>true,
  				'msg'=>"Se actualizo satisfactoriamente",
  				'rowid'=>$result
  			);
  			
  		} else {
  			$ret = array (
  				'success'=>false,
  				'msg'=>"Error al realizar la operacion",
  				'rowid'=>0
  			);
  		}
  		echo json_encode($ret);
	}

	public function deleteNeaDet() {
		sys_session_hasRoleOrDie(array('sa', 'nea'));

		$nea_det_id = $this->input->post('nea_det_id');

		$nea_det = $this->db->where('nea_det_id', $nea_det_id)->get('alm.nea_det')->row();
		$nea = $this->model->get_row($nea_det->nea_id);
		if ($nea->nea_estado == 'APROBADO' || $nea->nea_estado == 'ANULADO') {
			die (json_encode(array(
				'success'=>false,
				'msg'=>"No es posible modificar un documento Aprobado o Anulado."
			)));
		}
  		
  		$result = $this->model->delete_nea_det($nea_det_id);

  		if ($result !== false) {
  			die(json_encode(array (
  				'success'=>true,
  				'msg'=>"Se elimino satisfactoriamente"
  			)));
  		} else {
  			die(json_encode(array(
  				'success'=>false,
  				'msg'=>"Error al realizar la operacion"
  			)));
  		}
	}

	public function getSys2009DetOCList ($nea_id) {
		$prefix = 'nea_det_import_from_oc';
		$filter = strtoupper($this->input->get('query'));
		
		$rows = $this->model->get_sys2009_det_oc_list($nea_id, $filter);

		foreach ($rows as $i=>$row) {
			$rows[$i]['importado'] = '';
			
			$this->db
			->select('COUNT(*) AS value')
			->from('alm.nea_det AS nd')
			->join('alm.oc_det AS ocd', 'ocd.oc_det_id = nd.oc_det_id', 'inner')
			->where('ocd.oc_anio', $row['oc_anio'])
			->where('ocd.oc_numero', $row['oc_numero'])
			->where('ocd.bs_cod', $row['bs_cod'])
			->where('ocd.oc_det_obs', $row['oc_det_obs']);
			$gdcount = $this->db->get()->row();

			if ($gdcount->value > 0) {
				$rows[$i]['importado'] = 'Si';
			}
			$rows[$i]['importar'] = false; // sirve para mostrar o no el check de seleccion en el GRID
			// calculate field bs_desc_obs
			$rows[$i]['bs_desc_obs'] = $row['bs_desc'];
			if ($row['oc_det_obs']!='') {
				$rows[$i]['bs_desc_obs'] .= " - {$row['oc_det_obs']}";
			}
			// calculate field oc_anio_num
			$rows[$i]['oc_anio_numero'] = $row['oc_anio']."-".substr($row['oc_numero'], -5, 5);
		}
		echo json_encode(array(
			'data'=>$rows
		));
	}

	public function getSys2009OCList ($nea_id) {
		$filter = strtoupper($this->input->get('query'));
		
		$rows = $this->model->get_sys2009_det_oc_list($nea_id, $filter);
		// consolidar las OC afectadas
		$oc_list = array();
		foreach ($rows as $oc) {
			$oc_anio_numero = $oc['oc_anio']."-".substr($oc['oc_numero'], -5, 5);
			$oc_list[$oc_anio_numero] = $oc;
		}
		// ordenar por claves (Key), por anio y numero
		ksort($oc_list);
		$oc_rows = array(
			array('oc_anio'=>'-', 'oc_numero'=>'Todo', 'oc_fecha'=>'')
		);
		foreach ($oc_list as $oc) {
			$oc_rows[] = array('oc_anio'=>$oc['oc_anio'], 'oc_numero'=>substr($oc['oc_numero'], -5, 5), 'oc_fecha'=>$oc['oc_fecha']);
		}

		echo json_encode(array(
			'data'=>$oc_rows
		));
	}

	public function getSys2009BienList ($nea_id) {
		$prefix = 'nea_det_import_from_cb';
		$filter = strtoupper($this->input->get('query'));
		
		$rows = $this->model->get_sys2009_bien_list($filter);

		foreach ($rows as $i=>$row) {
			$rows[$i]['importado'] = '';
			
			$this->db
			->select('COUNT(*) AS value')
			->from('alm.nea_det AS nd')
			->where('nd.bs_cod', $row['bs_cod']);
			$gdcount = $this->db->get()->row();

			if ($gdcount->value > 0) {
				$rows[$i]['importado'] = 'Si';
			}
			$rows[$i]['importar'] = false; // sirve para mostrar o no el check de seleccion en el GRID
		}
		echo json_encode(array(
			'data'=>$rows
		));
	}

	public function getPrintPreview() {
		sys_session_hasRoleOrDie(array('sa', 'nea'));

		$nea_id = $this->input->post('nea_id');
		$version = $this->input->post('version');
		$url = $this->config->item('rpt_server')."/rpt_nea_pdf.jsp?id={$nea_id}&v={$version}";
		echo "<embed src=\"{$url}\" type=\"application/pdf\" width=\"100%\" height=\"100%\"></embed>";
	}

}
?>