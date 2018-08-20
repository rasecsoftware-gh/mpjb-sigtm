<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Rep_Contribuyente extends CI_Model{
    public function __construct() { 
        parent::__construct();
    }

    public function get_list ($data) {
        
        return array();
    }

    public function get_contribuyente_list ($data) {
        $this->db->select("
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
        ->join('public.ubigeo AS u', 'u.ubigeo_id = c.ubigeo_id', 'left');
        if ($data['p_filter'] != '') {
            $terms = explode(' ', $data['p_filter']);
            foreach ($terms as $i=>$t) {
                if (trim($t)!='') {
                    $this->db->like(
                        "UPPER(c.contribuyente_numero_doc||' '||
                        c.contribuyente_nombres||' '||
                        c.contribuyente_apellidos)", 
                        to_upper($t)
                    );
                }
            }
        }
        if ( $data['p_tipo_persona_id'] != '0' ) {
            $this->db->where('c.tipo_persona_id', $data['p_tipo_persona_id']);
        }
        if ( $data['p_tipo_doc_identidad_id'] > 0 ) {
            $this->db->where('c.tipo_doc_identidad_id', $data['p_tipo_doc_identidad_id']);
        }
        if ( $data['p_ubigeo_desc'] != '' ) {
            //$ubigeo_id = rtrim($data['p_ubigeo_id'], '0');
            $this->db->where("u.ubigeo_departamento||' - '||u.ubigeo_provincia||' - '||u.ubigeo_distrito ILIKE '{$data['p_ubigeo_desc']}%'");
        }
        $rows = $this->db->get()->result();
        return $rows;
    }

    public function get_contribuyente_row ($id) {
        $this->db
        ->select("
            c.*, 
            tp.tipo_persona_desc, 
            tdi.tipo_doc_identidad_desc,
            u.ubigeo_departamento,
            u.ubigeo_provincia,
            u.ubigeo_distrito
        ")
        ->from("public.contribuyente AS c")
        ->join("public.tipo_persona AS tp", "tp.tipo_persona_id = c.tipo_persona_id", "inner")
        ->join("public.tipo_doc_identidad AS tdi", "tdi.tipo_doc_identidad_id = c.tipo_doc_identidad_id", "inner")
        ->join('public.ubigeo AS u', 'u.ubigeo_id = c.ubigeo_id', 'left')
        ->where('contribuyente_id', $id);
        return $this->db->get()->row();
    }

    public function get_tipo_persona_list () {
        $rows = $this->db
        ->from('public.tipo_persona')
        ->order_by('tipo_persona_desc')
        ->get()->result();

        return array_merge(
            array( (object) array('tipo_persona_id'=>0, 'tipo_persona_desc'=>'- todo -') ),
            $rows
        );
    }

    public function get_tipo_doc_identidad_list () {
        $rows = $this->db
        ->from('public.tipo_doc_identidad')
        ->order_by('tipo_doc_identidad_desc')
        ->get()->result();
        return array_merge(
            array( (object) array('tipo_doc_identidad_id'=>0, 'tipo_doc_identidad_desc'=>'- todo -') ),
            $rows
        );
    }

    public function get_ubigeo_list ($filter) {
        $this->db->select("
            *,
            ubigeo_departamento||' - '||ubigeo_provincia||' - '||ubigeo_distrito AS label
        ")
        ->from('public.ubigeo');
        if ($filter != '') {
            $terms = explode(' ', $filter);
            foreach ($terms as $i=>$t) {
                if (trim($t)!='') {
                    $this->db->like(
                        "UPPER(ubigeo_departamento||' - '||
                        ubigeo_provincia||' - '||
                        ubigeo_distrito)", 
                        to_upper($t)
                    );
                }
            }
        }
        $rows = $this->db->get()->result();
        return $rows;
    }

    public function get_ubigeo_row ($id) {
        $this->db
        ->select("
            *, 
            ubigeo_departamento||' - '||ubigeo_provincia||' - '||ubigeo_distrito AS ubigeo_desc
        ")
        ->where('ubigeo_id', $id);
        return $this->db->get('public.ubigeo')->row();
    }
}
?>
