<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Rep_PSP extends CI_Model{
    public function __construct() { 
        parent::__construct();
    }

    public function get_list ($data) {
        $this->db->select("
            ps.*,
            c.contribuyente_numero_doc,
            c.contribuyente_nombres,
            c.contribuyente_apellidos,
            tp.tipo_persona_id,
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
            tpe.tipo_permiso_desc
        ")
        ->from('public.psp AS ps')
        ->join('public.contribuyente AS c', 'c.contribuyente_id = ps.contribuyente_id', 'inner')
        ->join('public.tipo_persona AS tp', 'tp.tipo_persona_id = c.tipo_persona_id', 'inner')
        ->join('public.tipo_doc_identidad AS tdi', 'tdi.tipo_doc_identidad_id = c.tipo_doc_identidad_id', 'inner')
        ->join('public.ubigeo AS u', 'u.ubigeo_id = c.ubigeo_id', 'left')
        ->join('public.tipo_permiso AS tpe', 'tpe.tipo_permiso_id = ps.tipo_permiso_id', 'inner')
        ->join('public.plantilla AS p', 'p.plantilla_id = ps.plantilla_id', 'left')
        ->join('public.doc_estado AS de', 'de.doc_estado_id = ps.doc_estado_id', 'left')
        ->join('public.estado_doc AS ed', 'ed.estado_doc_id = de.estado_doc_id', 'left');

        if ( $data['p_anio'] != '' ) {
            $this->db->where('ps.psp_anio', $data['p_anio']);
        }
        if ($data['p_filter'] != '') {
            $terms = explode(' ', $data['p_filter']);
            foreach ($terms as $i=>$t) {
                if (trim($t)!='') {
                    $this->db->like(
                        "UPPER(ps.psp_anio||' '||ps.psp_numero||' '||
                        c.contribuyente_numero_doc||' '||
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
        if ( $data['p_contribuyente_desc'] != '' ) {
            //$ubigeo_id = rtrim($data['p_ubigeo_id'], '0');
            $this->db->where("c.contribuyente_nombres||' - '||c.contribuyente_apellidos||' - '||c.contribuyente_numero_doc ILIKE '{$data['p_contribuyente_desc']}%'");
        }
        if ( $data['p_ubigeo_desc'] != '' ) {
            //$ubigeo_id = rtrim($data['p_ubigeo_id'], '0');
            $this->db->where("u.ubigeo_departamento||' - '||u.ubigeo_provincia||' - '||u.ubigeo_distrito ILIKE '{$data['p_ubigeo_desc']}%'");
        }
        if ( $data['p_tipo_permiso_id'] > 0 ) {
            $this->db->where('ps.tipo_permiso_id', $data['p_tipo_permiso_id']);
        }
        if ( $data['p_resolucion'] != '' ) {
            $this->db->like('ps.psp_resolucion', $data['p_resolucion']);
        }
        if ( $data['p_ruta'] != '' ) {
            $this->db->like('ps.psp_ruta', $data['p_ruta']);
        }
        if ( $data['p_estado_doc_id'] > 0 ) {
            $this->db->where('ps.estado_doc_id', $data['p_estado_doc_id']);
        }
        if ( $data['p_fecha_flag'] == '1' && $data['p_fecha_from'] != '' && $data['p_fecha_to'] != '' ) {
            $this->db->where("ps.psp_fecha BETWEEN '{$data['p_fecha_from']}' AND '{$data['p_fecha_to']}'");
        }

        $this->db->order_by('ps.psp_anio', 'desc');
        $this->db->order_by('ps.psp_numero', 'desc');
        $this->db->order_by('ps.psp_id', 'asc');

        $rows = $this->db->get()->result();
        return $rows;
    }

    public function get_doc_row ($id) {
        $this->db->select("
            ps.*,
            td.tipo_doc_desc,
            c.contribuyente_numero_doc,
            c.contribuyente_nombres,
            c.contribuyente_apellidos,
            tp.tipo_persona_id,
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
            tpe.tipo_permiso_desc
        ")
        ->from('public.psp AS ps')
        ->join('public.tipo_doc AS td', 'td.tipo_doc_id = ps.tipo_doc_id', 'inner')
        ->join('public.contribuyente AS c', 'c.contribuyente_id = ps.contribuyente_id', 'inner')
        ->join('public.tipo_persona AS tp', 'tp.tipo_persona_id = c.tipo_persona_id', 'inner')
        ->join('public.tipo_doc_identidad AS tdi', 'tdi.tipo_doc_identidad_id = c.tipo_doc_identidad_id', 'inner')
        ->join('public.ubigeo AS u', 'u.ubigeo_id = c.ubigeo_id', 'left')
        ->join('public.tipo_permiso AS tpe', 'tpe.tipo_permiso_id = ps.tipo_permiso_id', 'inner')
        ->join('public.plantilla AS p', 'p.plantilla_id = ps.plantilla_id', 'left')
        ->join('public.doc_estado AS de', 'de.doc_estado_id = ps.doc_estado_id', 'left')
        ->join('public.estado_doc AS ed', 'ed.estado_doc_id = de.estado_doc_id', 'left')
        ->where('ps.psp_id', $id);

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

    public function get_tipo_permiso_list () {
        $rows = $this->db
        ->from('public.tipo_permiso')
        ->order_by('tipo_permiso_desc')
        ->get()->result();

        return array_merge(
            array( (object) array('tipo_permiso_id'=>0, 'tipo_permiso_desc'=>'- todo -') ),
            $rows
        );
    }

    public function get_estado_doc_list () {
        $rows = $this->db
        ->from('public.estado_doc')
        ->where('tipo_doc_id', 'PSP')
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
        ->where('dr.tipo_doc_id', 'PSP')
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
        ->where('ed.tipo_doc_id', 'PSP')
        ->where('de.doc_id', $doc_id)
        ->order_by('ed.estado_doc_index', 'ASC')
        ->get()->result();
        return $rows;
    }

    public function get_vehiculo_list ($doc_id) {
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
