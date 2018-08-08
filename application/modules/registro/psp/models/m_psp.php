<?php
class M_Clit extends CI_Model{
	
	public function __construct() { 
		parent::__construct();
	}

	public function get_list ($search_by='all', $search_text='', $p_anio, $size=100, $start=0) {
		$this->db->start_cache();
		
		$this->db->select("
			cl.*,
			c.contribuyente_numero_doc,
			c.contribuyente_nombres,
			c.contribuyente_apellidos,
			tp.tipo_persona_desc,
			tdi.tipo_doc_identidad_desc,
			u.ubigeo_departamento,
			u.ubigeo_provincia,
			u.ubigeo_distrito,
			de.doc_estado_usuario,
			de.doc_estado_fecha,
			ed.estado_doc_desc,
			ed.estado_doc_color,
			ed.estado_doc_index,
			ed.estado_doc_requisito_requerido_flag,
			ed.estado_doc_final_flag,
			ed.estado_doc_generar_pdf_flag,
			ed.estado_doc_modificar_flag,
			p.plantilla_desc
		")
		->from('public.clit AS cl')
		->join('public.contribuyente AS c', 'c.contribuyente_id = cl.contribuyente_id', 'inner')
		->join('public.tipo_persona AS tp', 'tp.tipo_persona_id = c.tipo_persona_id', 'inner')
		->join('public.tipo_doc_identidad AS tdi', 'tdi.tipo_doc_identidad_id = c.tipo_doc_identidad_id', 'inner')
		->join('public.ubigeo AS u', 'u.ubigeo_id = c.ubigeo_id', 'left')
		->join('public.plantilla AS p', 'p.plantilla_id = cl.plantilla_id', 'left')
		->join('public.doc_estado AS de', 'de.doc_estado_id = cl.doc_estado_id', 'left')
		->join('public.estado_doc AS ed', 'ed.estado_doc_id = de.estado_doc_id', 'left')
		->where('cl.clit_anio', $p_anio);

		switch ($search_by) {
			case 'all':
				$terms = explode(' ', $search_text);
				foreach ($terms as $i=>$t) {
					if (trim($t)!='') {
						$this->db->like(
							"UPPER(cl.clit_anio||' '||
							cl.clit_numero||' '||
							c.contribuyente_nombres||' '||
							c.contribuyente_apellidos)", 
							to_upper($t)
						);
					}
				}
			break;
			case 'numero':
				$this->db->like('c.clit_numero', $search_text);	
			break;
			case 'estado':
				$this->db->like('ed.estado_doc_desc', strtoupper($search_text));
			break;
		}

		$this->db->stop_cache();
		$total_count = $this->db->count_all_results();


		$this->db->order_by('cl.clit_anio', 'desc');
		$this->db->order_by('cl.clit_numero', 'desc');
		$this->db->order_by('cl.clit_id', 'asc');
		$this->db->limit($size, $start);

		$rows = $this->db->get()->result();

		$this->db->flush_cache();
		
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count
		);
		return $ret;
	}

	private function get_next_numero($anio) {
		$n_max = $this->db->select('MAX(clit_numero) AS value')->where('clit_anio', $anio)->get('public.clit')->row();
		if (is_null($n_max)) {
			return 1;
		} else {
			return intval($n_max->value) + 1;
		}
	}

	public function get_new_row () {
		$row = array(
			'tipo_doc_id'=>'CLIT',
			'clit_anio'=>date('Y'), 
			'clit_numero'=>$this->get_next_numero(date('Y')), 
			'clit_fecha'=>date('d/m/Y'), 
			'clit_resultado'=>'PENDIENTE'
		);
		return $row;
	}

	public function get_row ($id, $format = 'object') {
		$this->db->select("
			cl.*,
			td.tipo_doc_desc,
			c.contribuyente_numero_doc,
			c.contribuyente_nombres,
			c.contribuyente_apellidos,
			tp.tipo_persona_desc,
			tdi.tipo_doc_identidad_desc,
			u.ubigeo_departamento,
			u.ubigeo_provincia,
			u.ubigeo_distrito,
			de.doc_estado_usuario,
			de.doc_estado_fecha,
			ed.estado_doc_desc,
			ed.estado_doc_color,
			ed.estado_doc_index,
			ed.estado_doc_requisito_requerido_flag,
			ed.estado_doc_final_flag,
			ed.estado_doc_generar_pdf_flag,
			ed.estado_doc_modificar_flag,
			p.plantilla_desc
		")
		->from('public.clit AS cl')
		->join('public.tipo_doc AS td', 'td.tipo_doc_id = cl.tipo_doc_id', 'inner')
		->join('public.contribuyente AS c', 'c.contribuyente_id = cl.contribuyente_id', 'inner')
		->join('public.tipo_persona AS tp', 'tp.tipo_persona_id = c.tipo_persona_id', 'inner')
		->join('public.tipo_doc_identidad AS tdi', 'tdi.tipo_doc_identidad_id = c.tipo_doc_identidad_id', 'inner')
		->join('public.ubigeo AS u', 'u.ubigeo_id = c.ubigeo_id', 'left')
		->join('public.plantilla AS p', 'p.plantilla_id = cl.plantilla_id', 'left')
		->join('public.doc_estado AS de', 'de.doc_estado_id = cl.doc_estado_id', 'left')
		->join('public.estado_doc AS ed', 'ed.estado_doc_id = de.estado_doc_id', 'left')
		->where('cl.clit_id', $id);

		return $this->db->get()->row(0, $format);
	}

	public function add ($data, $estado_doc_id) {
		$table = 'public.clit';
		$data['syslog'] = sys_session_syslog();
		$this->db->trans_begin();
		
		$this->db->insert($table, $data);
		$row_id = $this->db->insert_id();
		// $row_id, $estado_doc_id
		$doc_estado_id = $this->model->add_doc_estado(
			array(
				'tipo_doc_id'=>$data['tipo_doc_id'],
				'doc_id'=>$row_id,
				'estado_doc_id'=>$estado_doc_id
			)
		);

		$this->db->where('clit_id', $row_id)->update($table, array('doc_estado_id'=>$doc_estado_id));

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return $row_id;
		}
	}

	public function update ($data) {
		$c = $this->get_row($data['clit_id']);
		$data['syslog'] = sys_session_syslog('modificar', $c->syslog);
		
		$this->db->trans_begin();
		$this->db->where('clit_id', $data['clit_id']);
		$this->db->update('public.clit', $data);

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
		$this->db->where('clit_id', $id);
		$this->db->delete('public.clit');

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

	public function get_plantilla_list () {
		$rows = $this->db
		->where('tipo_doc_id', 'CLIT')
		->order_by('plantilla_id')
		->get('public.plantilla')->result();
		
		$ret = array(
			'data'=>$rows
		);
		return $ret;
	}

	public function get_estado_doc_list () {
		$rows = $this->db
		->where('tipo_doc_id', 'CLIT')
		->order_by('estado_doc_id')
		->get('public.estado_doc')->result();
		
		$ret = array(
			'data'=>$rows
		);
		return $ret;
	}

	public function get_tipo_doc_requisito_list () {
		$rows = $this->db
		->where('tipo_doc_id', 'CLIT')
		->where('tipo_doc_requisito_estado', 'A')
		->order_by('tipo_doc_requisito_id', 'ASC')
		->get('public.tipo_doc_requisito')->result();
		
		$ret = array(
			'data'=>$rows
		);
		return $ret;
	}
	// consulta que se cruza con tipo_doc_requisito para tener el listado completo de lo que HAY y SE TIENE ya registrado
	public function get_doc_requisito_list ($doc_id) {
		$rows = $this->db
		->select("
			tdr.*,
			dr.doc_requisito_id,
			dr.doc_id,
			dr.doc_requisito_pdf,
			dr.doc_requisito_fecha,
			dr.doc_requisito_numero
		")
		->from('public.tipo_doc_requisito AS tdr')
		->join('public.doc_requisito AS dr', "dr.doc_id = {$doc_id} AND dr.tipo_doc_requisito_id = tdr.tipo_doc_requisito_id", 'left')
		->where('tdr.tipo_doc_id', 'CLIT')
		->where('tdr.tipo_doc_requisito_estado', 'A')
		->order_by('tdr.tipo_doc_requisito_id', 'ASC')
		->get()->result();
		
		$ret = array(
			'data'=>$rows
		);
		return $ret;
	}

	public function add_doc_requisito ($data) {
		$table = 'public.doc_requisito';
		
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

	public function update_doc_requisito ($data) {
		$table = 'public.doc_requisito';

		$dr = $this->db->where('doc_requisito_id', $data['doc_requisito_id'])->get($table)->row();
		$data['syslog'] = sys_session_syslog('modificar', $dr->syslog);

		$this->db->trans_begin();
		$this->db
		->where('doc_requisito_id', $data['doc_requisito_id'])
		->update($table, $data);

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return true;
		}
	}

	public function delete_doc_requisito ($doc_requisito_id) {
		$this->db->trans_begin();
		$this->db->where('doc_requisito_id', $doc_requisito_id);
		$this->db->delete('public.doc_requisito');

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return true;
		}
	}

	public function get_doc_estado_list ($doc_id) {
		$rows = $this->db
		->select("
			ed.*,
			de.doc_estado_id,
			de.doc_id,
			de.doc_estado_fecha,
			de.doc_estado_usuario
		")
		->from('public.estado_doc AS ed')
		->join('public.doc_estado AS de', "de.doc_id = {$doc_id} AND de.estado_doc_id = ed.estado_doc_id", 'left')
		->where('ed.tipo_doc_id', 'CLIT')
		->order_by('ed.estado_doc_index', 'ASC')
		->get()->result();
		
		$ret = array(
			'data'=>$rows
		);
		return $ret;
	}

	public function add_doc_estado ($_data = array()) {
		$table = 'public.doc_estado';
		$user = sys_session_getUserInfo();
		$data = array(
			'doc_estado_usuario'=>$user->usuario_login,
			'doc_estado_fecha'=>date('d/m/Y H:i:s')
		);
		foreach ($_data as $field=>$value) {
			$data[$field] = $value;
		}
		$this->db->insert($table, $data);
       	return $this->db->insert_id();
	}

	public function delete_doc_estado ($doc_estado_id) {
		$this->db->trans_begin();
		$this->db->where('doc_estado_id', $doc_estado_id);
		$this->db->delete('public.doc_estado');

		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return true;
		}
	}



	public function get_list_for_gen_pdf ($clit_anio='2018', $tipo_clit_id) {
		$rows = $this->db
		->select("c.clit_id")
		->from('rh.clit AS c')
		->where('c.clit_anio', $clit_anio)	
		->where('c.tipo_clit_id', $tipo_clit_id)
		->where('c.clit_estado <>', 'ANULADO')
		->where('c.clit_pdf', '')
		->order_by('c.clit_anio', 'desc')
		->order_by('c.tipo_clit_id', 'asc')
		->order_by('c.clit_numero', 'desc')
		->order_by('c.clit_id', 'desc')
		->get()->result();
		
		$ret = array(
			'data'=>$rows,
			'total'=>count($rows)
		);
		return $ret;
	}
	

}
?>