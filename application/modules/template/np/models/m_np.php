<?php
class M_NP extends CI_Model{
	
	public function __construct() { 
		parent::__construct();
	}

	public function get_list ($search_by='general', $search_text='') {
		//$this->db->where('anio_eje', '2015');
		if ($search_by == 'all') {
			$this->db->like('row_filter', strtoupper($search_text));	
		} else if ($search_by == 'nro_np') {
			$this->db->like('nro_np', strtoupper($search_text));	
		} else if ($search_by == 'id_frente') {
			$this->db->like('desc_obra', strtoupper($search_text));
			$this->db->or_like('cod_obra', strtoupper($search_text));
			$this->db->or_like('desc_frente', strtoupper($search_text));
		} else if ($search_by == 'id_comprador') {
			$this->db->or_like('cod_comprador', strtoupper($search_text));
			$this->db->or_like('desc_comprador', strtoupper($search_text));
		} else if ($search_by == 'desc_np') {
			$this->db->like('desc_np', strtoupper($search_text));	
		}
		
		$query = $this->db->get('log.v_np');
		return $query->result();	
	}

	public function get_anio_activo(){
		$this->db->where('est_anio','A');
		$result = $this->db->get('sys.anio');
		$row = $result->row();
		return $row->anio_eje;
	}

	public function get_new_row () {
		$row = array(
			'id_np'=>0,
			'anio_eje'=>date('Y'),
			'nro_np'=>'00000',
			'id_frente'=>0,
			'id_comprador'=>0,
			'fecha_np'=>date('d/m/Y'),
			'desc_np'=>'',
			'id_estado_np'=>'1'
		);
		return $row;
	}

	public function get_row ($id) {
		$this->db->where('id_np', $id);
		//$this->db->like('row_filter', strtoupper($filter));
		$query = $this->db->get('log.v_np');
		$rows = $query->result();
		return $rows[0];	
	}

	public function get_frente_list ($filter) {
		//$this->db->where('anio_eje', '2015');
		$this->db->like('row_filter', strtoupper($filter));
		$query = $this->db->get('log.v_frente');
		return $query->result();	
	}

	public function get_comprador_list ($filter) {
		$this->db->where('estado_comprador', 'A');
		$this->db->like('row_filter', strtoupper($filter));
		$query = $this->db->get('log.v_comprador');
		return $query->result();	
	}

	public function get_proveedor_list ($filter, $id_proveedor=0) {
		if ($id_proveedor>0) {
			$this->db->where('id_proveedor', $id_proveedor);	
		}
		$this->db->where('estado_proveedor', 'A');
		$this->db->like('row_filter', strtoupper($filter));
		$query = $this->db->get('log.v_proveedor');
		return $query->result();	
	}

	public function add ($data){
		$this->db->select("nextval(pg_get_serial_sequence('log.np', 'id_np')) AS value");
		$result_id = $this->db->get()->result();

		$cod_obra = $data['cod_obra'];
		unset($data['cod_obra']);
		$result_num = $this->db->select("sys.f_semilla_next('', 'log.np', '', '{$cod_obra}') AS value")->get()->result();
		
		$data['id_np'] = $result_id[0]->value;
		$data['anio_eje'] =  date('Y');
		$data['nro_np'] = STR_PAD($result_num[0]->value, 5, '0', STR_PAD_LEFT);
		$data['id_estado_np'] =  '1'; // generado
		$data['syslog'] = 'sys';

		$this->db->trans_begin();

		$this->db->insert('log.np',$data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['id_np'];
		}
	}

	public function update ($data) {
		$data['syslog'] = 'sys';
		
		$this->db->trans_begin();
		$this->db->where('id_np', $data['id_np']);
		$this->db->update('log.np', $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['id_np'];
		}
	}

	public function update_as_rendido ($data) {
		$data['syslog'] = 'sys';
		
		$this->db->trans_begin();
		$this->db->where('id_np', $data['id_np']);
		$this->db->update('log.np', $data);

		$details = $this->get_detail_list($data['id_np']);
		foreach ($details as $d) {
			$this->update_det_requer_estado_from_saldo ($d->id_det_requer);
		}

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['id_np'];
		}
	}

	public function cancelar_rendicion ($data) {
		$data['syslog'] = 'sys';
		
		$this->db->trans_begin();
		$this->db->where('id_np', $data['id_np']);
		$this->db->update('log.np', $data);

		$details = $this->get_detail_list($data['id_np']);
		foreach ($details as $d) {
			$this->update_det_requer_estado_from_saldo($d->id_det_requer);
		}

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['id_np'];
		}
	}

	// unused
	public function get_new_detail_row () {
		$row = array(
			'id_det_requer'=>0,
			'id_bs'=>0,
			'cant_det_requer'=>1,
			'preuni_det_requer'=>0,
			'tot_det_requer'=>0,
			'obs_det_requer'=>'',
			'syslog'=>'',
			'id_unimed'=>0
		);
		return $row;
	}

	public function get_detail_row ($id) {
		$this->db->where('id_det_np', $id);
		//$this->db->like('row_filter', strtoupper($filter));
		$query = $this->db->get('log.v_det_np');
		$rows = $query->result();
		return $rows[0];	
	}

	public function get_detail_list ($id) {
		$this->db->where('id_np', $id);
		$query = $this->db->get('log.v_det_np');
		return $query->result();	
	}

	public function import_details ($id, $list) {

		$this->db->where_in('id_det_requer', $list);
		$rows = $this->db->get('log.v_det_requer_saldo')->result();

		$this->db->trans_begin();
/*
  id_det_np,
  id_np,
  id_bs,
  id_unimed,
  cant_det_np,
  preuni_det_np,
  tot_det_np,
  igv_det_np,
  obs_det_np,
  id_det_requer,
  syslog
*/
  		$count = 0;
		foreach ($rows as $r) {
			$total = $r->saldo * $r->preuni_det_requer;
			$data = array(
				'id_np'=>$id,
				'id_bs'=>$r->id_bs,
				'id_unimed'=>$r->id_unimed,
				'id_clapre'=>$r->id_clapre,
				'cant_det_np'=>$r->saldo,
				'preuni_det_np'=>$r->preuni_det_requer,
				'tot_det_np'=>$total,
				'obs_det_np'=>$r->obs_det_requer,
	  			'id_det_requer'=>$r->id_det_requer,
				'syslog'=>''
			);
			$this->db->insert('log.det_np', $data);

			$this->db->where('id_det_requer', $r->id_det_requer)->update('log.det_requer', array(
				'id_estado_det_requer'=>'5'
			));

			$count++;
		}

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $count;
		}
	}

	public function update_det_requer_estado_from_saldo ($id_det_requer) {
		$dr_rows = $this->db->where('id_det_requer', $id_det_requer)->get('log.v_det_requer_saldo')->result();
		if ($dr_rows[0]->saldo == 0) {
			$this->db->where('id_det_requer', $id_det_requer)->update('log.det_requer', array('id_estado_det_requer'=>'5'));
		} elseif ($dr_rows[0]->saldo == $dr_rows[0]->cant_det_requer) {
			$this->db->where('id_det_requer', $id_det_requer)->update('log.det_requer', array('id_estado_det_requer'=>'1'));
		} else {
			$this->db->where('id_det_requer', $id_det_requer)->update('log.det_requer', array('id_estado_det_requer'=>'4'));
		}
	}

	public function update_detail ($data) {
		$data['syslog'] = 'sys';
		
		$this->db->trans_begin();
		$this->db->where('id_det_np', $data['id_det_np']);
		$this->db->update('log.det_np', $data);

		$dnp_rows = $this->db->where('id_det_np', $data['id_det_np'])->get('log.det_np')->result();
		$id_det_requer = $dnp_rows[0]->id_det_requer;

		$this->update_det_requer_estado_from_saldo ($id_det_requer);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['id_det_np'];
		}
	}

	public function delete_detail ($id) {
		$this->db->trans_begin();

		$dnp_rows = $this->db->where('id_det_np', $id)->get('log.det_np')->result();
		$id_det_requer = $dnp_rows[0]->id_det_requer;

		$this->db->where('id_det_np', $id);
		$this->db->delete('log.det_np');

		$this->update_det_requer_estado_from_saldo ($id_det_requer);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $id;
		}
	}

	public function get_det_requer_list ($filter, $id_np) {
		$this->db->select('id_frente');
		$this->db->where('id_np', $id_np);
		$rows = $this->db->get('log.np')->result();

		$id_frente = 0;
		if (count($rows)>0) {
			$id_frente = $rows[0]->id_frente;
		}
		$this->db->where('id_frente', $id_frente);
		//$this->db->like('cod_bs', 'B');
		$this->db->like('row_filter', strtoupper($filter));
		$this->db->limit(200);
		$query = $this->db->get('log.v_det_requer_for_import');
		return $query->result();	
	}

	public function get_tipo_documento_list () {
		$query = $this->db->get('log.v_rendicion_np_tipo_documento');
		return $query->result();	
	}

	public function get_det_np_comprobante ($id_det_np) {
		$this->db->select("td.abrev_tipo_documento||' '||rnp.numdoc_rendicion_np AS comprobante");
	    $this->db->from('log.det_rendicion_np AS drnp');
	    $this->db->join('log.rendicion_np AS rnp', 'rnp.id_rendicion_np = drnp.id_rendicion_np');
	    $this->db->join('log.tipo_documento AS td', 'td.id_tipo_documento = rnp.id_tipo_documento');
	    $this->db->where('drnp.id_det_np', $id_det_np);
	    $rows = $this->db->get()->result();
	    $items = array();
	    foreach ($rows as $r) {
	    	$items[] = $r->comprobante;
	    }
	    return implode(',', $items);
	}

	public function get_det_np_for_import_list ($id) {
		$this->db->where('id_np', $id);
		$query = $this->db->get('log.v_det_np_for_import');
		return $query->result();	
	}

	public function get_det_np_for_import_row ($id) {
		$this->db->where('id_det_np', $id);
		$query = $this->db->get('log.v_det_np_for_import');
		$rows = $query->result();
		return $rows[0];	
	}

	public function get_rendicion_np_list ($id_np) {
		$this->db->where('id_np', $id_np);
		$query = $this->db->get('log.v_rendicion_np');
		return $query->result();	
	}

	public function get_rendicion_np_new_row () {
		$row = array(
			'id_rendicion_np'=>0,
			'id_tipo_documento'=>'01',
			'fechadoc_rendicion_np'=>date('d/m/Y'),
			'numdoc_rendicion_np'=>'',
		);
		return $row;
	}

	public function get_rendicion_np_row ($id) {
		$this->db->where('id_rendicion_np', $id);
		$query = $this->db->get('log.v_rendicion_np');
		$rows = $query->result();
		return $rows[0];	
	}

	public function add_rendicion_np ($data){
		$this->db->select("nextval(pg_get_serial_sequence('log.rendicion_np', 'id_rendicion_np')) AS value");
		$result_id = $this->db->get()->result();
		
		$data['id_rendicion_np'] = $result_id[0]->value;
		$data['syslog'] = 'system';

		$this->db->trans_begin();

		$this->db->insert('log.rendicion_np', $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['id_rendicion_np'];
		}
	}

	public function update_rendicion_np ($data){
		
		$data['syslog'] = 'system';

		$this->db->trans_begin();
		$this->db->where('id_rendicion_np', $data['id_rendicion_np']);
		$this->db->update('log.rendicion_np', $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['id_rendicion_np'];
		}
	}

	public function delete_rendicion_np ($id) {
		$this->db->trans_begin();

		$this->db->where('id_rendicion_np', $id);
		$this->db->delete('log.det_rendicion_np');

		$this->db->where('id_rendicion_np', $id);
		$this->db->delete('log.rendicion_np');

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $id;
		}
	}

	public function get_det_rendicion_np_list ($id_rendicion_np) {
		$this->db->where('id_rendicion_np', $id_rendicion_np);
		$query = $this->db->get('log.v_det_rendicion_np');
		return $query->result();	
	}

	public function get_det_rendicion_np_row ($id) {
		$rows = $this->db->where('id_det_rendicion_np', $id)->get('log.v_det_rendicion_np')->result();
		return $rows[0];	
	}

	public function validate_add_det_rendicion_np ($data) {
		/*$rows = $this->db->where('id_det_np', $data['id_det_np'])->get('log.det_rendicion_np')->result();
		if (count($rows) > 0 ) {
			return 'El detalle ya se encuentra registrado en un comprobante';
		}*/
		// aki hay que validar solo la cantidad por item de la nota de pedido
		// con respecto a la sumatoria de detalles de rendicion
		return true;
	}

	public function add_det_rendicion_np ($data){
		$this->db->select("nextval(pg_get_serial_sequence('log.det_rendicion_np', 'id_det_rendicion_np')) AS value");
		$result_id = $this->db->get()->result();
		
		$data['id_det_rendicion_np'] = $result_id[0]->value;
		$data['syslog'] = 'system';

		$this->db->trans_begin();

		$this->db->insert('log.det_rendicion_np', $data);

		$dnp = $this->get_detail_row($data['id_det_np']);
		$total = $this->db->select('SUM(saldo) AS saldo, SUM(cant_det_np) AS cantidad')->where('id_np', $dnp->id_np)->get('log.v_det_np_for_import')->result();
		$np = array('id_np'=>$dnp->id_np);
		if ($total[0]->saldo == $total[0]->cantidad) {
			$np['id_estado_np'] = '1'; // vuelve a generado
		} elseif ($total[0]->saldo > 0) {
			$np['id_estado_np'] = '4'; // rendicion parcial
		} else {
			$np['id_estado_np'] = '5'; // rendido
		}
		$this->update($np);


		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['id_det_rendicion_np'];
		}
	}

	public function update_det_rendicion_np ($data) {
		$data['syslog'] = 'system';

		$this->db->trans_begin();

		$this->db->where('id_det_rendicion_np', $data['id_det_rendicion_np']);
		$this->db->update('log.det_rendicion_np', $data);

		$drnp = $this->get_det_rendicion_np_row($data['id_det_rendicion_np']);
		$rnp = $this->get_rendicion_np_row($drnp->id_rendicion_np);
		$total = $this->db->select('SUM(saldo) AS saldo, SUM(cant_det_np) AS cantidad')->where('id_np', $rnp->id_np)->get('log.v_det_np_for_import')->result();
		$np = array('id_np'=>$rnp->id_np);
		if ($total[0]->saldo == $total[0]->cantidad) {
			$np['id_estado_np'] = '1'; // vuelve a generado
		} elseif ($total[0]->saldo > 0) {
			$np['id_estado_np'] = '4'; // rendicion parcial
		} else {
			$np['id_estado_np'] = '5'; // rendicion parcial
		}
		$this->update($np);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['id_det_rendicion_np'];
		}
	}

	public function delete_det_rendicion_np ($id) {
		$this->db->trans_begin();

		$drnp = $this->get_det_rendicion_np_row($id);

		$this->db->where('id_det_rendicion_np', $id);
		$this->db->delete('log.det_rendicion_np');


		$rnp = $this->get_rendicion_np_row($drnp->id_rendicion_np);
		$total = $this->db->select('SUM(saldo) AS saldo, SUM(cant_det_np) AS cantidad')->where('id_np', $rnp->id_np)->get('log.v_det_np_for_import')->result();
		$np = array('id_np'=>$rnp->id_np);
		if ($total[0]->saldo == $total[0]->cantidad) {
			$np['id_estado_np'] = '1'; // vuelve a generado
		} elseif ($total[0]->saldo > 0) {
			$np['id_estado_np'] = '4'; // rendicion parcial
		} else {
			$np['id_estado_np'] = '5'; // rendicion parcial
		}
		$this->update($np);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $id;
		}
	}
}
?>