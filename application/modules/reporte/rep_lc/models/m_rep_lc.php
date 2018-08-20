<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Rep_LC extends CI_Model{
    public function __construct() { 
        parent::__construct();
    }

    public function get_list ($data) {
       $this->db->select("
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
            p.plantilla_desc,
            (CASE 
                WHEN ed.estado_doc_final_flag = 'N' THEN 'EN TRAMITE'
                WHEN NOW()::DATE BETWEEN lc.lc_fecha_exp AND lc.lc_fecha_ven THEN 'VIGENTE'
                ELSE 'VENCIDO'
            END) AS estado
        ")
        ->from('public.lc AS lc')
        ->join('public.contribuyente AS c', 'c.contribuyente_id = lc.contribuyente_id', 'inner')
        ->join('public.tipo_persona AS tp', 'tp.tipo_persona_id = c.tipo_persona_id', 'inner')
        ->join('public.tipo_doc_identidad AS tdi', 'tdi.tipo_doc_identidad_id = c.tipo_doc_identidad_id', 'inner')
        ->join('public.ubigeo AS u', 'u.ubigeo_id = c.ubigeo_id', 'left')
        ->join('public.plantilla AS p', 'p.plantilla_id = lc.plantilla_id', 'left')
        ->join('public.doc_estado AS de', 'de.doc_estado_id = lc.doc_estado_id', 'left')
        ->join('public.estado_doc AS ed', 'ed.estado_doc_id = de.estado_doc_id', 'left');

        if ( $data['p_anio'] != '' ) {
            $this->db->where('lc.lc_anio', $data['p_anio']);
        }
        if ($data['p_filter'] != '') {
            $terms = explode(' ', $data['p_filter']);
            foreach ($terms as $i=>$t) {
                if (trim($t)!='') {
                    $this->db->like(
                        "UPPER(lc.lc_anio||' '||lc.lc_numero||' '||
                        c.contribuyente_numero_doc||' '||
                        c.contribuyente_nombres||' '||
                        c.contribuyente_apellidos)", 
                        to_upper($t)
                    );
                }
            }
        }
        if ( $data['p_contribuyente_desc'] != '' ) {
            //$ubigeo_id = rtrim($data['p_ubigeo_id'], '0');
            $this->db->where("c.contribuyente_nombres||' - '||c.contribuyente_apellidos||' - '||c.contribuyente_numero_doc ILIKE '{$data['p_contribuyente_desc']}%'");
        }
        if ( $data['p_ubigeo_desc'] != '' ) {
            //$ubigeo_id = rtrim($data['p_ubigeo_id'], '0');
            $this->db->where("u.ubigeo_departamento||' - '||u.ubigeo_provincia||' - '||u.ubigeo_distrito ILIKE '{$data['p_ubigeo_desc']}%'");
        }
        if ( $data['p_categoria'] != '' ) {
            $this->db->where('lc.lc_categoria', $data['p_categoria']);
        }
        if ( $data['p_restricciones'] != '' ) {
            $this->db->where('lc.lc_restricciones', $data['p_restricciones']);
        }
        if ( $data['p_resolucion'] != '' ) {
            $this->db->where('lc.lc_resolucion', $data['p_resolucion']);
        }
        if ( $data['p_estado_doc_id'] > 0 ) {
            $this->db->where('lc.estado_doc_id', $data['p_estado_doc_id']);
        }
        if ( $data['p_fecha_flag'] == '1' && $data['p_fecha_from'] != '' && $data['p_fecha_to'] != '' ) {
            $this->db->where("lc.lc_fecha BETWEEN '{$data['p_fecha_from']}' AND '{$data['p_fecha_to']}'");
        }
        if ( $data['p_estado'] != '' ) {
            switch ($data['p_estado']) {
                case 'EN TRAMITE':
                    $this->db->where('lc.estado_doc_final_flag', 'N');
                    break;
                case 'VIGENTE':
                    $this->db->where("NOW()::DATE BETWEEN lc.lc_fecha_exp AND lc.lc_fecha_ven");
                    break;
                case 'VENCIDO':
                    $this->db->where("NOT (NOW()::DATE BETWEEN lc.lc_fecha_exp AND lc.lc_fecha_ven)");
                    break;
            }
        }

        $this->db->order_by('lc.lc_anio', 'desc');
        $this->db->order_by('lc.lc_numero', 'desc');
        $this->db->order_by('lc.lc_id', 'asc');

        $rows = $this->db->get()->result();
        return $rows;
    }

    public function get_doc_row ($id) {
        $this->db->select("
            lc.*,
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
            p.plantilla_desc,
            (CASE 
                WHEN ed.estado_doc_final_flag = 'N' THEN 'EN TRAMITE'
                WHEN NOW()::DATE BETWEEN lc.lc_fecha_exp AND lc.lc_fecha_ven THEN 'VIGENTE'
                ELSE 'VENCIDO'
            END) AS estado
        ")
        ->from('public.lc AS lc')
        ->join('public.tipo_doc AS td', 'td.tipo_doc_id = lc.tipo_doc_id', 'inner')
        ->join('public.contribuyente AS c', 'c.contribuyente_id = lc.contribuyente_id', 'inner')
        ->join('public.tipo_persona AS tp', 'tp.tipo_persona_id = c.tipo_persona_id', 'inner')
        ->join('public.tipo_doc_identidad AS tdi', 'tdi.tipo_doc_identidad_id = c.tipo_doc_identidad_id', 'inner')
        ->join('public.ubigeo AS u', 'u.ubigeo_id = c.ubigeo_id', 'left')
        ->join('public.plantilla AS p', 'p.plantilla_id = lc.plantilla_id', 'left')
        ->join('public.doc_estado AS de', 'de.doc_estado_id = lc.doc_estado_id', 'left')
        ->join('public.estado_doc AS ed', 'ed.estado_doc_id = de.estado_doc_id', 'left')
        ->where('lc.lc_id', $id);

        return $this->db->get()->row();
    }

    public function get_contribuyente_list ($filter) {
        $this->db->select("
            c.*,
            tp.tipo_persona_desc,
            tdi.tipo_doc_identidad_desc,
            u.ubigeo_departamento,
            u.ubigeo_provincia,
            u.ubigeo_distrito,
            c.contribuyente_nombres||' '||c.contribuyente_apellidos AS label
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

    public function get_restricciones_list () {
        $rows = $this->db
        ->select('lc_restricciones')
        ->from('public.lc')
        ->where('lc_restricciones <>', '')
        ->group_by('lc_restricciones')
        ->order_by('lc_restricciones')
        ->get()->result();

        return array_merge(
            array( (object) array('lc_restricciones'=>'') ),
            $rows
        );
    }

    public function get_estado_doc_list () {
        $rows = $this->db
        ->from('public.estado_doc')
        ->where('tipo_doc_id', 'LC')
        ->order_by('estado_doc_index')
        ->get()->result();
        return array_merge(
            array( (object) array('estado_doc_id'=>0, 'estado_doc_desc'=>'- todo -') ),
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

    public function get_doc_requisito_list ($doc_id) {
        $rows = $this->db
        ->select("
            dr.*,
            tdr.tipo_doc_requisito_desc
        ")
        ->from('public.doc_requisito AS dr')
        ->join('public.tipo_doc_requisito AS tdr', "tdr.tipo_doc_requisito_id = dr.tipo_doc_requisito_id", 'inner')
        ->where('dr.tipo_doc_id', 'LC')
        ->where('dr.doc_id', $doc_id)
        ->order_by('tdr.tipo_doc_requisito_index', 'ASC')
        ->get()->result();
        return $rows;
    }

    public function get_doc_estado_list ($doc_id) {
        $rows = $this->db
        ->select("
            de.*,
            ed.estado_doc_desc
        ")
        ->from('public.doc_estado AS de')
        ->join('public.estado_doc AS ed', "ed.estado_doc_id = de.estado_doc_id", 'inner')
        ->where('ed.tipo_doc_id', 'LC')
        ->where('de.doc_id', $doc_id)
        ->order_by('ed.estado_doc_index', 'ASC')
        ->get()->result();
        return $rows;
    }
}
?>
