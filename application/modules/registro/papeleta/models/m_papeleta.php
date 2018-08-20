<?php
class M_Papeleta extends CI_Model{
	
	public function __construct() { 
		parent::__construct();
	}

	public function get_list ($search_by='all', $search_text='', $tipo_persona_id, $tipo_doc_identidad_id, $size=100, $start=0) {
		$this->db->start_cache();
		
		$this->db->select("
			p.*,
			c.contribuyente_numero_doc,
			c.contribuyente_nombres,
			c.contribuyente_apellidos,
			c.contribuyente_fecha_nac,
			tp.tipo_persona_desc,
			tdi.tipo_doc_identidad_desc,
			u.ubigeo_departamento,
			u.ubigeo_provincia,
			u.ubigeo_distrito,
			ti.tipo_infraccion_desc,
			mp.medida_preventiva_desc,
			ep.estado_papeleta_desc
		")
		->from('public.papeleta AS p')
		->join('public.contribuyente AS c', 'c.contribuyente_id = p.contribuyente_id', 'inner')
		->join('public.tipo_persona AS tp', 'tp.tipo_persona_id = c.tipo_persona_id', 'inner')
		->join('public.tipo_doc_identidad AS tdi', 'tdi.tipo_doc_identidad_id = c.tipo_doc_identidad_id', 'inner')
		->join('public.ubigeo AS u', 'u.ubigeo_id = c.ubigeo_id', 'left')
		->join('public.tipo_infraccion AS ti', 'ti.tipo_infraccion_id = p.tipo_infraccion_id', 'inner')
		->join('public.medida_preventiva AS mp', 'mp.medida_preventiva_id = p.medida_preventiva_id', 'inner')
		->join('public.estado_papeleta AS ep', 'ep.estado_papeleta_id = p.estado_papeleta_id', 'inner');

		switch ($search_by) {
			case 'all':
				$terms = explode(' ', $search_text);
				foreach ($terms as $i=>$t) {
					if (trim($t)!='') {
						$this->db->like(
							"UPPER(p.papeleta_numero||' '||
							c.contribuyente_numero_doc||' '||
							c.contribuyente_nombres||' '||
							c.contribuyente_apellidos)", 
							to_upper($t)
						);
					}
				}
			break;
			case 'estado':
				$this->db->like('ep.estado_papeleta_desc', strtoupper($search_text));
			break;
		}

		$this->db->stop_cache();
		$total_count = $this->db->count_all_results();


		$this->db->order_by('p.papeleta_numero', 'desc');
		$this->db->order_by('p.papeleta_id', 'asc');
		$this->db->limit($size, $start);

		$rows = $this->db->get()->result();

		$this->db->flush_cache();
		
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count
		);
		return $ret;
	}

	public function get_new_row () {
		$row = array(
			'papeleta_fecha'=>date('d/m/Y'),
			'tipo_infraccion_id'=>'L',
			'medida_preventiva_id'=>'00', 
			'estado_papeleta_id'=>1 // registrado
		);
		return $row;
	}

	public function get_row ($id) {
		$this->db
		->select("
			p.*,
			c.contribuyente_numero_doc,
			c.contribuyente_nombres,
			c.contribuyente_apellidos,
			c.contribuyente_fecha_nac,
			tp.tipo_persona_desc,
			tdi.tipo_doc_identidad_desc,
			u.ubigeo_departamento,
			u.ubigeo_provincia,
			u.ubigeo_distrito,
			ti.tipo_infraccion_desc,
			mp.medida_preventiva_desc,
			ep.estado_papeleta_desc
		")
		->from('public.papeleta AS p')
		->join('public.contribuyente AS c', 'c.contribuyente_id = p.contribuyente_id', 'inner')
		->join('public.tipo_persona AS tp', 'tp.tipo_persona_id = c.tipo_persona_id', 'inner')
		->join('public.tipo_doc_identidad AS tdi', 'tdi.tipo_doc_identidad_id = c.tipo_doc_identidad_id', 'inner')
		->join('public.ubigeo AS u', 'u.ubigeo_id = c.ubigeo_id', 'left')
		->join('public.tipo_infraccion AS ti', 'ti.tipo_infraccion_id = p.tipo_infraccion_id', 'inner')
		->join('public.medida_preventiva AS mp', 'mp.medida_preventiva_id = p.medida_preventiva_id', 'inner')
		->join('public.estado_papeleta AS ep', 'ep.estado_papeleta_id = p.estado_papeleta_id', 'inner')
		->where('papeleta_id', $id);
		return $this->db->get()->row();
	}

	public function add ($data) {
		$table = 'public.papeleta';
		
		$data['syslog'] = sys_session_syslog();

		$this->db->trans_begin();

		$this->db->insert($table, $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $this->db->insert_id();
		}
	}

	public function update ($data) {
		$c = $this->get_row($data['papeleta_id']);
		$data['syslog'] = sys_session_syslog('modificar', $c->syslog);
		
		$this->db->trans_begin();
		$this->db->where('papeleta_id', $data['papeleta_id']);
		$this->db->update('public.papeleta', $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return true;
		}
	}	

	public function delete ($id) {
		$this->db->trans_begin();
		$this->db->where('papeleta_id', $id);
		$this->db->delete('public.papeleta');

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return true;
		}
	}

	public function get_contribuyente_list ($filter) {
		$this->db
		->select("
			c.*,
			tp.tipo_persona_desc,
			tdi.tipo_doc_identidad_desc,
			u.ubigeo_departamento,
			u.ubigeo_provincia,
			u.ubigeo_distrito
		")
		->from('public.contribuyente AS c')
		->join('public.tipo_persona AS tp', 'tp.tipo_persona_id = c.tipo_persona_id', 'inner')
		->join('public.tipo_doc_identidad AS tdi', 'tdi.tipo_doc_identidad_id = c.tipo_doc_identidad_id', 'inner')
		->join('public.ubigeo AS u', 'u.ubigeo_id = c.ubigeo_id', 'left')
		->where('c.contribuyente_estado', 'A');
		if ( $filter != '' ) {
			$terms = explode(' ', $filter);
			foreach ($terms as $i=>$t) {
				if (trim($t)!='') {
					$this->db->where("c.contribuyente_numero_doc||' '||c.contribuyente_nombres||' '||c.contribuyente_apellidos ILIKE '%{$t}%'");
				}
			}
			
		}
		$rows = $this->db->order_by('c.contribuyente_nombres')
		->order_by('c.contribuyente_apellidos')
		->order_by('c.contribuyente_id')
		->limit(50)
		->get()->result();
		$total_count = count($rows);
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count
		);
		return $ret;
	}
	
	public function get_tipo_infraccion_list () {
		$rows = $this->db
		->order_by('tipo_infraccion_id', 'ASC')
		->get('public.tipo_infraccion')->result();
		return array(
			'data'=>$rows
		);
	}

	public function get_medida_preventiva_list () {
		$rows = $this->db
		->order_by('medida_preventiva_id', 'ASC')
		->get('public.medida_preventiva')->result();
		return array(
			'data'=>$rows
		);
	}

	public function get_estado_papeleta_list () {
		$rows = $this->db
		->order_by('estado_papeleta_id', 'ASC')
		->get('public.estado_papeleta')->result();
		return array(
			'data'=>$rows
		);
	}

}
?>