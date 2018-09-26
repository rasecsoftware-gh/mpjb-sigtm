<?php
class M_Notificacion extends CI_Model{
	
	public function __construct() { 
		parent::__construct();
	}

	public function get_list ($search_by='all', $search_text='', $tipo_persona_id, $tipo_doc_identidad_id, $size=100, $start=0) {
		$this->db->start_cache();
		
		$this->db->select("
			n.*,
			c.contribuyente_numero_doc,
			c.contribuyente_nombres,
			c.contribuyente_apellidos,
			c.contribuyente_fecha_nac,
			tp.tipo_persona_desc,
			tdi.tipo_doc_identidad_desc,
			u.ubigeo_departamento,
			u.ubigeo_provincia,
			u.ubigeo_distrito,
			p.papeleta_numero
		")
		->from('public.notificacion AS n')
		->join('public.contribuyente AS c', 'c.contribuyente_id = n.contribuyente_id', 'inner')
		->join('public.tipo_persona AS tp', 'tp.tipo_persona_id = c.tipo_persona_id', 'inner')
		->join('public.tipo_doc_identidad AS tdi', 'tdi.tipo_doc_identidad_id = c.tipo_doc_identidad_id', 'inner')
		->join('public.ubigeo AS u', 'u.ubigeo_id = c.ubigeo_id', 'left')
		->join('public.papeleta AS p', 'p.papeleta_id = n.papeleta_id', 'inner');

		switch ($search_by) {
			case 'all':
				$terms = explode(' ', $search_text);
				foreach ($terms as $i=>$t) {
					if (trim($t)!='') {
						$this->db->like(
							"UPPER(n.notificacion_numero||' '||
							c.contribuyente_numero_doc||' '||
							c.contribuyente_nombres||' '||
							c.contribuyente_apellidos)", 
							to_upper($t)
						);
					}
				}
			break;
			case 'acto':
				$this->db->like('n.notificacion_acto_administrativo', strtoupper($search_text));
			break;
		}

		$this->db->stop_cache();
		$total_count = $this->db->count_all_results();


		$this->db->order_by('n.notificacion_numero', 'desc');
		$this->db->order_by('n.notificacion_id', 'asc');
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
			'notificacion_fecha'=>date('d/m/Y'),
			'notificacion_acta_snar'=>'NO',
			'notificacion_acta_snaf'=>'NO',
			'notificacion_acta_sdbp'=>'NO'
		);
		return $row;
	}

	public function get_row ($id) {
		$this->db
		->select("
			n.*,
			c.contribuyente_numero_doc,
			c.contribuyente_nombres,
			c.contribuyente_apellidos,
			c.contribuyente_fecha_nac,
			tp.tipo_persona_desc,
			tdi.tipo_doc_identidad_desc,
			u.ubigeo_departamento,
			u.ubigeo_provincia,
			u.ubigeo_distrito,
			p.papeleta_numero
		")
		->from('public.notificacion AS n')
		->join('public.contribuyente AS c', 'c.contribuyente_id = n.contribuyente_id', 'inner')
		->join('public.tipo_persona AS tp', 'tp.tipo_persona_id = c.tipo_persona_id', 'inner')
		->join('public.tipo_doc_identidad AS tdi', 'tdi.tipo_doc_identidad_id = c.tipo_doc_identidad_id', 'inner')
		->join('public.ubigeo AS u', 'u.ubigeo_id = c.ubigeo_id', 'left')
		->join('public.papeleta AS p', 'p.papeleta_id = n.papeleta_id', 'inner')
		->where('notificacion_id', $id);
		return $this->db->get()->row();
	}

	public function add ($data) {
		$table = 'public.notificacion';
		
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
		$c = $this->get_row($data['notificacion_id']);
		$data['syslog'] = sys_session_syslog('modificar', $c->syslog);
		
		$this->db->trans_begin();
		$this->db->where('notificacion_id', $data['notificacion_id']);
		$this->db->update('public.notificacion', $data);

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
		$this->db->where('notificacion_id', $id);
		$this->db->delete('public.notificacion');

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

	public function get_papeleta_list ($filter, $contribuyente_id) {
		$this->db
		->select("
			p.papeleta_id,
			p.papeleta_numero,
			p.papeleta_numero||' - '||p.papeleta_fecha::text AS papeleta_desc
		")
		->from('public.papeleta AS p')
		->like('p.papeleta_numero', $filter)
		->where('p.contribuyente_id', $contribuyente_id);
		/*if ( $filter != '' ) {
			$terms = explode(' ', $filter);
			foreach ($terms as $i=>$t) {
				if (trim($t)!='') {
					$this->db->where("c.contribuyente_numero_doc||' '||c.contribuyente_nombres||' '||c.contribuyente_apellidos ILIKE '%{$t}%'");
				}
			}
			
		}*/
		$rows = $this->db
		->order_by('p.papeleta_numero', 'DESC')
		->limit(50)
		->get()->result();
		$total_count = count($rows);
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count
		);
		return $ret;
	}
	
}
?>