<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_LC extends MX_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_rep_lc', 'model');
    }

    public function index() {
        $data['p_filter'] = $this->input->get('filter'); 
        $data['p_anio'] = $this->input->get('anio'); 
        $data['p_contribuyente_desc'] = $this->input->get('contribuyente_desc'); 
        $data['p_ubigeo_desc'] = $this->input->get('ubigeo_desc'); 
        $data['p_categoria'] = $this->input->get('categoria'); 
        $data['p_fecha_flag'] = $this->input->get('fecha_flag'); 
        $data['p_fecha_from'] = $this->input->get('fecha_from'); 
        $data['p_fecha_to'] = $this->input->get('fecha_to'); 
        $data['p_resolucion'] = $this->input->get('resolucion'); 
        $data['p_restricciones'] = $this->input->get('restricciones'); 
        $data['p_estado_doc_id'] = $this->input->get('estado_doc_id'); 
        $data['p_estado'] = $this->input->get('estado'); 
        $data['p_execute'] = $this->input->get('execute');

        $data['tipo_persona_list'] = $this->model->get_tipo_persona_list();
        $data['categoria_list'] = array('','I', 'II-A', 'II-B', 'II-C');
        $data['estado_doc_list'] = $this->model->get_estado_doc_list();
        $data['restricciones_list'] = $this->model->get_restricciones_list();
        $data['estado_list'] = array('','EN TRAMITE', 'VIGENTE', 'VENCIDO');

        if ($data['p_execute'] == '1') {
            $data['list'] = $this->model->get_list($data);
        } else {
            $data['list'] = array();
            
        }
       
        $this->load->view('v_rep_lc', $data);
    }
    public function getView() {
        $data['p_lc_id'] = $this->input->get('lc_id'); 

        $data['doc'] = $this->model->get_doc_row($data['p_lc_id']);
        $data['doc_requisito_list'] = $this->model->get_doc_requisito_list($data['p_lc_id']);
        $data['doc_estado_list'] = $this->model->get_doc_estado_list($data['p_lc_id']);
       
        $this->load->view('v_rep_lc_view', $data);
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
