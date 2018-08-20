<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_CLIT extends MX_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_rep_clit', 'model');
    }

    public function index() {
        $data['p_filter'] = $this->input->get('filter'); 
        $data['p_anio'] = $this->input->get('anio'); 
        $data['p_tipo_persona_id'] = $this->input->get('tipo_persona_id'); 
        $data['p_contribuyente_desc'] = $this->input->get('contribuyente_desc'); 
        $data['p_ubigeo_desc'] = $this->input->get('ubigeo_desc'); 
        $data['p_resultado'] = $this->input->get('resultado'); 
        $data['p_fecha_flag'] = $this->input->get('fecha_flag'); 
        $data['p_fecha_from'] = $this->input->get('fecha_from'); 
        $data['p_fecha_to'] = $this->input->get('fecha_to'); 
        $data['p_estado_doc_id'] = $this->input->get('estado_doc_id'); 
        $data['p_execute'] = $this->input->get('execute');

        $data['tipo_persona_list'] = $this->model->get_tipo_persona_list();
        $data['resultado_list'] = array('','PENDIENTE', 'SI', 'NO');
        $data['estado_doc_list'] = $this->model->get_estado_doc_list();

        if ($data['p_execute'] == '1') {
            $data['list'] = $this->model->get_list($data);
        } else {
            $data['list'] = array();
            
        }
       
        $this->load->view('v_rep_clit', $data);
    }
    public function getView() {
        $data['p_clit_id'] = $this->input->get('clit_id'); 

        $data['doc'] = $this->model->get_doc_row($data['p_clit_id']);
        $data['doc_requisito_list'] = $this->model->get_doc_requisito_list($data['p_clit_id']);
        $data['doc_estado_list'] = $this->model->get_doc_estado_list($data['p_clit_id']);
       
        $this->load->view('v_rep_clit_view', $data);
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
