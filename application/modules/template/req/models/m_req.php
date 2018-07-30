<?php
class M_Req extends CI_Model{
	
	public function __construct() { 
		parent::__construct();
	}

	public function get_list ($search_by='general', $search_text='') {
		//$this->db->where('anio_eje', '2015');
		if ($search_by == 'all') {
			$this->db->like('row_filter', strtoupper($search_text));	
		} else if ($search_by == 'nro_requer') {
			$this->db->like('nro_requer', strtoupper($search_text));	
		} else if ($search_by == 'cod_obra') {
			$this->db->like('desc_obra', strtoupper($search_text));
			$this->db->or_like('cod_obra', strtoupper($search_text));
			$this->db->or_like('desc_frente', strtoupper($search_text));
		} else if ($search_by == 'desc_requer') {
			$this->db->like('desc_requer', strtoupper($search_text));	
		} else if ($search_by == 'id_estado_requer') {
			$this->db->where('id_estado_requer', strtoupper($search_text));	
		}

		$query = $this->db->get('log.v_requer');
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
			'id_requer'=>0,
			'nro_requer'=>'00000',
			'id_frente'=>0,
			'anio_eje'=>date('Y'),
			'fecha_requer'=>date('d/m/Y'),
			'desc_requer'=>'',
			'id_estado_requer'=>'1'
		);
		return $row;
	}

	public function get_row ($id) {
		$this->db->where('id_requer', $id);
		//$this->db->like('row_filter', strtoupper($filter));
		$query = $this->db->get('log.v_requer');
		$rows = $query->result();
		return $rows[0];	
	}
	
	public function get_estado_for_search_list () {
		$this->db->select('id_estado_requer AS search_text_id, desc_estado_requer AS search_text_desc');
		$query = $this->db->from('log.estado_requer')->order_by('id_estado_requer', 'asc')->get();
		return $query->result();	
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

	public function add ($cod_obra, $id_frente, $fecha_requer, $desc_requer){
		$this->db->select("nextval(pg_get_serial_sequence('log.requer', 'id_requer')) AS value");
		$result_id = $this->db->get()->result();

		$this->db->select("sys.f_semilla_next('', 'log.requer', '', '{$cod_obra}') AS value");
		$result_num = $this->db->get()->result();
		
		$data = array(
			'id_requer'=>$result_id[0]->value,
			'nro_requer'=>STR_PAD($result_num[0]->value, 5, '0', STR_PAD_LEFT),
			'anio_eje'=>date('Y'),
			'id_frente' => $id_frente,
  			'fecha_requer' => $fecha_requer, 
  			'desc_requer' => $desc_requer,
  			'id_estado_requer'=> '1',
  			'syslog' => 'sys'
		);
		$msg = "";
		$this->db->trans_begin();

		$this->db->insert('log.requer',$data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['id_requer'];
		}
	}

	public function update ($data) {
		$data['syslog'] = 'sys';
		
		$this->db->trans_begin();
		$this->db->where('id_requer', $data['id_requer']);
		$this->db->update('log.requer', $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['id_requer'];
		}
	}


	public function get_new_detail_row () {
		$row = array(
			'id_det_requer'=>0,
			'id_bieser'=>0,
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
		$this->db->where('id_det_requer', $id);
		//$this->db->like('row_filter', strtoupper($filter));
		$query = $this->db->get('log.v_det_requer');
		$rows = $query->result();
		return $rows[0];	
	}

	public function get_detail_list ($id) {
		$this->db->where('id_requer', $id);
		$query = $this->db->get('log.v_det_requer');
		return $query->result();	
	}

	public function increment_bs_hits ($id_bs) {
		$result_hits = $this->db->select('hits_bs AS value')->where('id_bs', $id_bs)->get('log.bs')->result();
		$data = array(
			'hits_bs'=>($result_hits[0]->value+1)
		);
		$this->db->where('id_bs', $id_bs)->update('log.bs', $data);
	}

	public function add_detail ($data) {
		$this->db->select("nextval(pg_get_serial_sequence('log.det_requer', 'id_det_requer')) AS value");
		$result_id = $this->db->get()->result();

		$data['id_det_requer'] = $result_id[0]->value;
		$data['syslog'] = 'sys';

		$msg = "";
		$this->db->trans_begin();

		$this->increment_bs_hits($data['id_bs']);

		$this->db->insert('log.det_requer', $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['id_det_requer'];
		}
	}

	public function update_detail ($data) {
		$data['syslog'] = 'sys';
		
		$msg = "";
		$this->db->trans_begin();
		$this->db->where('id_det_requer', $data['id_det_requer']);
		$this->db->update('log.det_requer', $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $data['id_det_requer'];
		}
	}

	public function delete_detail ($id) {
		$this->db->trans_begin();
		$this->db->where('id_det_requer', $id);
		$this->db->delete('log.det_requer');

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $id;
		}
	}

	public function get_bs_list ($filter, $id=0) {
		if ($id>0) {
			$this->db->where('id_bs', $id);
		} else {
			$terms = explode(' ', $filter);
			foreach ($terms as $i=>$t) {
				if (trim($t)!='') {
					$this->db->like('row_filter', strtoupper($t));
				}
			}
		}
		$this->db->order_by('hits_bs','desc');
		$this->db->order_by('desc_bs','asc');
		$this->db->limit(200);
		$query = $this->db->get('log.v_bs');
		return $query->result();	
	}

	public function get_clapre_list ($id_requer) {
		$r = $this->get_row($id_requer);
		//$this->db->where('anio_eje', $r->anio_eje);
		$this->db->where('cod_obra', $r->cod_obra);
		$this->db->order_by('desc_clapre','asc');
		$query = $this->db->get('pre.clapre');
		return $query->result();	
	}
}
?>