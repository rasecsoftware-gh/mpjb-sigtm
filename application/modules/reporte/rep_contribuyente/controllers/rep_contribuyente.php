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
        $data['p_ubigeo_id'] = $this->input->get('ubigeo_id'); 
        $data['p_execute'] = $this->input->get('execute');

        if ($data['p_execute'] == '1') {
            $data['contrib_list'] = $this->model->get_contribuyente_list($data);
            $data['lc_list'] = $this->model->get_lc_list($data['p_contribuyente_id']);
            $data['papeleta_list'] = $this->model->get_papeleta_list($data['p_contribuyente_id']);
            $data['psp_list'] = $this->model->get_psp_list($data['p_contribuyente_id']);

            $data['contribuyente_desc'] = $data['contrib']->contribuyente_nombres.' '.$data['contrib']->contribuyente_apellidos;
        } else {
            $data['contrib_list'] = array();
            $data['lc_list'] = array();
            $data['papeleta_list'] = array();
            $data['psp_list'] = array();

            $data['contribuyente_desc'] = '';
        }

        $this->load->view('v_rep_contribuyente', $data);
    }
    public function getContribuyenteList() {
        $search_text = $this->input->get('term');
        $rows = $this->model->get_contribuyente_list($search_text);
        echo json_encode($rows);
    }
  
}
?>
