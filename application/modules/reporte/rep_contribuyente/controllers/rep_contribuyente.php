<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_Contribuyente extends MX_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_rep_contribuyente', 'model');
    }

    public function index() {
        $data['p_filter'] = $this->input->get('filter'); 
        $data['p_tipo_persona_id'] = $this->input->get('tipo_persona_id'); 
        $data['p_tipo_doc_identidad_id'] = $this->input->get('tipo_doc_identidad_id'); 
        $data['p_ubigeo_desc'] = $this->input->get('ubigeo_desc'); 
        $data['p_execute'] = $this->input->get('execute');

        $data['tipo_persona_list'] = $this->model->get_tipo_persona_list();
        $data['tipo_doc_identidad_list'] = $this->model->get_tipo_doc_identidad_list();

        if ($data['p_execute'] == '1') {
            $data['contrib_list'] = $this->model->get_contribuyente_list($data);
        } else {
            $data['contrib_list'] = array();
            
        }
        /*
        if ( $data['p_ubigeo_id'] > 0) {
            $data['ubigeo'] = $this->model->get_ubigeo_row($data['p_ubigeo_id']);
            $data['ubigeo_desc'] = $data['ubigeo']->ubigeo_desc;
        } else {
            $data['ubigeo_desc'] = '';
        }
        */
        $this->load->view('v_rep_contribuyente', $data);
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
