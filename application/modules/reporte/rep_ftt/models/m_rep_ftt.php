<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Rep_FTT extends CI_Model{
    public function __construct() { 
        parent::__construct();
    }

    public function get_list ($data) {
        
        return array();
    }

    public function get_contribuyente_list ($filter) {
        $this->db->select("
            c.contribuyente_nombres||' '||c.contribuyente_apellidos AS label,
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
        if ($filter != '') {
            $terms = explode(' ', $filter);
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

    public function get_lc_list ($contribuyente_id) {
        $rows = $this->db->select("
            lc.*,
            c.contribuyente_numero_doc,
            c.contribuyente_nombres,
            c.contribuyente_apellidos,
            c.contribuyente_fecha_nac,
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
        ->from('public.lc AS lc')
        ->join('public.contribuyente AS c', 'c.contribuyente_id = lc.contribuyente_id', 'inner')
        ->join('public.tipo_persona AS tp', 'tp.tipo_persona_id = c.tipo_persona_id', 'inner')
        ->join('public.tipo_doc_identidad AS tdi', 'tdi.tipo_doc_identidad_id = c.tipo_doc_identidad_id', 'inner')
        ->join('public.ubigeo AS u', 'u.ubigeo_id = c.ubigeo_id', 'left')
        ->join('public.plantilla AS p', 'p.plantilla_id = lc.plantilla_id', 'left')
        ->join('public.doc_estado AS de', 'de.doc_estado_id = lc.doc_estado_id', 'left')
        ->join('public.estado_doc AS ed', 'ed.estado_doc_id = de.estado_doc_id', 'left')
        ->where('lc.contribuyente_id', $contribuyente_id)
        ->get()->result();
        return $rows;
    }

    public function get_papeleta_list ($contribuyente_id) {
        return array();
    }

    public function get_psp_list ($contribuyente_id) {
        $rows = $this->db->select("
            ps.*,
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
        ->from('public.psp AS ps')
        ->join('public.contribuyente AS c', 'c.contribuyente_id = ps.contribuyente_id', 'inner')
        ->join('public.tipo_persona AS tp', 'tp.tipo_persona_id = c.tipo_persona_id', 'inner')
        ->join('public.tipo_doc_identidad AS tdi', 'tdi.tipo_doc_identidad_id = c.tipo_doc_identidad_id', 'inner')
        ->join('public.ubigeo AS u', 'u.ubigeo_id = c.ubigeo_id', 'left')
        ->join('public.plantilla AS p', 'p.plantilla_id = ps.plantilla_id', 'left')
        ->join('public.doc_estado AS de', 'de.doc_estado_id = ps.doc_estado_id', 'left')
        ->join('public.estado_doc AS ed', 'ed.estado_doc_id = de.estado_doc_id', 'left')
        ->where('ps.contribuyente_id', $contribuyente_id)
        ->get()->result();
        foreach ($rows as $r) {
            $r->vehiculo_list = $this->get_psp_vehiculo_list($r->psp_id);
        }
        return $rows;
    }

    public function get_psp_vehiculo_list ($doc_id) {
        $rows = $this->db
        ->select("
            pv.*
        ")
        ->from('public.psp_vehiculo AS pv')
        ->where('pv.psp_id', $doc_id)
        ->order_by('pv.psp_vehiculo_id', 'ASC')
        ->get()->result();

        return $rows;
    }
}
?>
