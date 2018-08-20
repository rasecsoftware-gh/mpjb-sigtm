<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_PSP extends MX_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_rep_psp', 'model');
    }

    public function index() {
        $data['p_filter'] = $this->input->get('filter'); 
        $data['p_anio'] = $this->input->get('anio'); 
        $data['p_tipo_persona_id'] = $this->input->get('tipo_persona_id'); 
        $data['p_contribuyente_desc'] = $this->input->get('contribuyente_desc'); 
        $data['p_ubigeo_desc'] = $this->input->get('ubigeo_desc'); 
        $data['p_tipo_permiso_id'] = $this->input->get('tipo_permiso_id'); 
        $data['p_fecha_flag'] = $this->input->get('fecha_flag'); 
        $data['p_fecha_from'] = $this->input->get('fecha_from'); 
        $data['p_fecha_to'] = $this->input->get('fecha_to'); 
        $data['p_resolucion'] = $this->input->get('resolucion'); 
        $data['p_ruta'] = $this->input->get('ruta'); 
        $data['p_estado_doc_id'] = $this->input->get('estado_doc_id'); 
        $data['p_execute'] = $this->input->get('execute');

        $data['tipo_persona_list'] = $this->model->get_tipo_persona_list();
        $data['tipo_permiso_list'] = $this->model->get_tipo_permiso_list();
        $data['estado_doc_list'] = $this->model->get_estado_doc_list();

        if ($data['p_execute'] == '1') {
            $data['list'] = $this->model->get_list($data);
        } else {
            $data['list'] = array();
            
        }
       
        $this->load->view('v_rep_psp', $data);
    }
    public function getView() {
        $data['p_psp_id'] = $this->input->get('psp_id'); 

        $data['doc'] = $this->model->get_doc_row($data['p_psp_id']);
        $data['doc_requisito_list'] = $this->model->get_doc_requisito_list($data['p_psp_id']);
        $data['doc_estado_list'] = $this->model->get_doc_estado_list($data['p_psp_id']);
        $data['vehiculo_list'] = $this->model->get_vehiculo_list($data['p_psp_id']);
       
        $this->load->view('v_rep_psp_view', $data);
    }
    public function getContribuyenteList() {
        $search_text = $this->input->get('term');
        $rows = $this->model->get_contribuyente_list($search_text);
        echo json_encode($rows);
    }
    public function getUbigeoList() {
        $filter = $this->input->get('term');
        $rows = $this->model->get_ubigeo_list($filter);
        echo json_encode($rows);
    }
  
}
?>
