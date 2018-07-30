<?php
class M_Cheque extends CI_Model{
	
	public function __construct() { 
		parent::__construct();
	}

	public function get_list ($ano_eje, $search_by='all', $search_text='', $size=100, $start=0) {
		$filter = $search_text;
		switch ($search_by) {
			case 'all':
				$filter= strtoupper($search_text);
			break;
			case 'expediente':
				$filter = str_pad(intval($search_text), 10, '0', STR_PAD_LEFT);
			break;
		}

		$rows = siaf_expediente_documento_cheque_list($ano_eje, $filter);
		$ret = array(
			'data'=>$rows,
			'total'=>count($rows)
		);
		return $ret;
	}

	public function get_new_row () {
		$row = array();
		return $row;
	}

	public function get_row ($id) {
		return array();
	}
	public function print_preview($expediente_documento_id) {
		$row = siaf_expediente_documento_row($expediente_documento_id);
		if (!is_null($row)) {
			$fecha_cheque = $row['fecha_doc'];
			$fdb_list = explode('/', $fecha_cheque);
			$cheque = array(
				'cheque_expediente'=>$row['expediente'],
				'cheque_cp'=>$row['num_doc_cp'],
				'cheque_numero'=>$row['num_doc'],
				'cheque_fecha'=>$row['fecha_doc'],
				'cheque_lugar'=>'LOCUMBA',
				'cheque_dia'=>$fdb_list[0],
				'cheque_mes'=>$fdb_list[1],
				'cheque_anio'=>$fdb_list[2],
				'cheque_monto'=>$row['monto'],
				'cheque_monto_letras'=>num2letras(floatval($row['monto'])),
				'cheque_ruc'=>$row['ruc'],
				'cheque_proveedor'=>$row['proveedor'],
				'cheque_nombre_girado'=>$row['nombre']
			);
			$this->db->insert('tes.siaf_cheque', $cheque);
			return $this->db->insert_id();
		} else {
			return false;
		}
	}
	
}
?>