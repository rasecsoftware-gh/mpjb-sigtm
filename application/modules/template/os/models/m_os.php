<?php
class M_OS extends CI_Model{
	
	public function __construct() { 
		parent::__construct();
	}

	public function get_list ($search_by='general', $search_text='') {
		//$this->db->where('anio_eje', '2015');
		$this->db->where('id_tipo_orden', 'OS');
		if ($search_by == 'all') {
			$this->db->like('row_filter', strtoupper($search_text));	
		} else if ($search_by == 'nro_orden') {
			$this->db->like('nro_orden', strtoupper($search_text));	
		} else if ($search_by == 'id_frente') {
			$this->db->like('desc_obra', strtoupper($search_text));
			$this->db->or_like('cod_obra', strtoupper($search_text));
			$this->db->or_like('desc_frente', strtoupper($search_text));
		} else if ($search_by == 'id_proveedor') {
			$this->db->like('ruc_proveedor', strtoupper($search_text));
			$this->db->or_like('desc_proveedor', strtoupper($search_text));
		} else if ($search_by == 'desc_orden') {
			$this->db->like('desc_orden', strtoupper($search_text));	
		}
		
		$query = $this->db->get('log.v_orden');
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
			'id_orden'=>0,
			'anio_eje'=>date('Y'),
			'nro_orden'=>'00000',
			'id_frente'=>0,
			'id_proveedor'=>0,
			'fecha_orden'=>date('d/m/Y'),
			'desc_orden'=>'',
			'id_estado_orden'=>'1',
			'id_regimen_igv'=>'N',
			'val_regimen_igv_det'=>0
		);
		return $row;
	}

	public function get_row ($id) {
		$this->db->where('id_orden', $id);
		//$this->db->like('row_filter', strtoupper($filter));
		$query = $this->db->get('log.v_orden');
		$rows = $query->result();
		return $rows[0];	
	}

	public function get_obra_list ($filter, $cod_obra = '') {
		//$this->db->where('anio_eje', '2015');
		$this->db->where('est_obra', 'A');
		// build smart filter
		if ($cod_obra!='') {
			$this->db->where('cod_obra', $cod_obra);
		} else {
			$terms = explode(' ', $filter);
			foreach ($terms as $i=>$t) {
				if (trim($t)!='') {
					$this->db->like('row_filter', strtoupper($t));
				}
			}
		}
		$this->db->order_by('desc_obra','asc');
		$query = $this->db->get('log.v_obra');
		return $query->result();	
	}

	public function get_frente_list ($cod_obra) {
		//$this->db->where('anio_eje', '2015');
		$this->db->where('cod_obra', $cod_obra);
		$query = $this->db->get('log.v_frente');
		return $query->result();	
	}

	public function get_proveedor_list ($filter) {
		$this->db->where('estado_proveedor', 'A');
		$this->db->like('row_filter', strtoupper($filter));
		$query = $this->db->get('log.v_proveedor');
		return $query->result();	
	}

	public function get_regimen_igv_list () {
		$rows = $this->db->order_by('desc_regimen_igv')->get('log.regimen_igv')->result();
		return $rows;
	}

	public function get_regimen_igv_det_list () {
		$rows = $this->db->order_by('desc_regimen_igv_det')->get('log.regimen_igv_det')->result();
		return $rows;
	}

	public function add ($data){
		$this->db->select("nextval(pg_get_serial_sequence('log.orden', 'id_orden')) AS value");
		$result_id = $this->db->get()->result();

		$cod_obra = $data['cod_obra'];
		unset($data['cod_obra']);
		$result_num = $this->db->select("sys.f_semilla_next('2015', 'log.orden', 'os', '{$cod_obra}') AS value")->get()->result();
		
		$data['id_orden'] = $result_id[0]->value;
		$data['anio_eje'] =  date('Y');
		$data['id_tipo_orden'] =  'OS';
		$data['nro_orden'] = STR_PAD($result_num[0]->value, 5, '0', STR_PAD_LEFT);
		$data['id_estado_orden'] = '1';
		$data['id_regimen_igv'] =  'N';
		$data['val_regimen_igv_det'] =  0;
		$data['igv_orden'] =  18;
		$data['syslog'] = 'sys';

		$this->db->trans_begin();

		$this->db->insert('log.orden',$data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['id_orden'];
		}
	}

	public function update ($data) {
		$data['syslog'] = 'sys';

		$data['igv_orden'] =  18;
		
		$this->db->trans_begin();
		$this->db->where('id_orden', $data['id_orden']);
		$this->db->update('log.orden', $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['id_orden'];
		}
	}


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
		$this->db->where('id_det_orden', $id);
		//$this->db->like('row_filter', strtoupper($filter));
		$query = $this->db->get('log.v_det_orden');
		$rows = $query->result();
		return $rows[0];	
	}

	public function get_detail_list ($id) {
		$this->db->where('id_orden', $id);
		$query = $this->db->get('log.v_det_orden');
		return $query->result();	
	}

	public function import_details ($id, $list) {

		$this->db->where_in('id_det_requer', $list);
		$rows = $this->db->get('log.v_det_requer_for_import')->result();

		$this->db->trans_begin();
/*
  id_det_orden,
  id_orden,
  id_bs,
  id_unimed,
  cant_det_orden,
  preuni_det_orden,
  tot_det_orden,
  igv_det_orden,
  obs_det_orden,
  id_det_requer,
  syslog
*/
  		$count = 0;
		foreach ($rows as $r) {
			$data = array(
				'id_orden'=>$id,
				'id_bs'=>$r->id_bs,
				'id_unimed'=>$r->id_unimed,
				'id_clapre'=>$r->id_clapre,
				'cant_det_orden'=>$r->saldo,
				'preuni_det_orden'=>$r->preuni_det_requer,
				'tot_det_orden'=>$r->tot_det_requer,
				'igv_det_orden'=>'1',
				'obs_det_orden'=>$r->obs_det_requer,
	  			'id_det_requer'=>$r->id_det_requer,
				'syslog'=>''
			);
			$this->db->insert('log.det_orden', $data);

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

	public function update_detail ($data) {
		$data['syslog'] = 'sys';
		
		$this->db->trans_begin();

		$this->db->where('id_det_orden', $data['id_det_orden']);
		$this->db->update('log.det_orden', $data);

		$do_rows = $this->db->where('id_det_orden', $data['id_det_orden'])->get('log.det_orden')->result();
		$id_det_requer = $do_rows[0]->id_det_requer;

		$dr_rows = $this->db->where('id_det_requer', $id_det_requer)->get('log.v_det_requer_saldo')->result();
		
		if ($dr_rows[0]->saldo == 0) {
			$this->db->where('id_det_requer', $id_det_requer)->update('log.det_requer', array('id_estado_det_requer'=>'5'));
		} elseif ($dr_rows[0]->saldo == $dr_rows[0]->cant_det_requer) {
			$this->db->where('id_det_requer', $id_det_requer)->update('log.det_requer', array('id_estado_det_requer'=>'1'));
		} else {
			$this->db->where('id_det_requer', $id_det_requer)->update('log.det_requer', array('id_estado_det_requer'=>'4'));
		}

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['id_det_orden'];
		}
	}

	public function delete_detail ($id) {
		$this->db->trans_begin();
		
		$do_rows = $this->db->where('id_det_orden', $id)->get('log.det_orden')->result();
		$id_det_requer = $do_rows[0]->id_det_requer;

		$this->db->where('id_det_orden', $id);
		$this->db->delete('log.det_orden');

		

		$dr_rows = $this->db->where('id_det_requer', $id_det_requer)->get('log.v_det_requer_saldo')->result();
		
		if ($dr_rows[0]->saldo == 0) {
			$this->db->where('id_det_requer', $id_det_requer)->update('log.det_requer', array('id_estado_det_requer'=>'5'));
		} elseif ($dr_rows[0]->saldo == $dr_rows[0]->cant_det_requer) {
			$this->db->where('id_det_requer', $id_det_requer)->update('log.det_requer', array('id_estado_det_requer'=>'1'));
		} else {
			$this->db->where('id_det_requer', $id_det_requer)->update('log.det_requer', array('id_estado_det_requer'=>'4'));
		}

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $id;
		}
	}

	public function get_det_requer_list ($filter, $id_orden) {
		$this->db->select('id_frente');
		$this->db->where('id_orden', $id_orden);
		$rows = $this->db->get('log.orden')->result();

		$id_frente = 0;
		if (count($rows)>0) {
			$id_frente = $rows[0]->id_frente;
		}
		$this->db->where('id_frente', $id_frente);
		$this->db->like('cod_bs', 'S');
		$this->db->like('row_filter', strtoupper($filter));
		$this->db->limit(200);
		$query = $this->db->get('log.v_det_requer_for_import');
		return $query->result();	
	}
}
?>