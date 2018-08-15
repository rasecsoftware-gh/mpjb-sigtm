<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rep_FTT extends MX_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_rep_ftt', 'model');
    }

    public function index() {
        $data['p_contribuyente_id'] = $this->input->get('contribuyente_id'); 
        $data['p_execute'] = $this->input->get('execute');

        if ($data['p_execute'] == '1') {
            $data['contrib'] = $this->model->get_contribuyente_row($data['p_contribuyente_id']);
            $data['lc_list'] = $this->model->get_lc_list($data['p_contribuyente_id']);
            $data['papeleta_list'] = $this->model->get_papeleta_list($data['p_contribuyente_id']);
            $data['psp_list'] = $this->model->get_psp_list($data['p_contribuyente_id']);
        } else {
            $data['contribuyente'] = null;
        }

        $this->load->view('v_rep_ftt', $data);
    }
    public function getContribuyenteList() {
        $search_text = $this->input->get('term');
        $rows = $this->model->get_contribuyente_list($search_text);
        echo json_encode($rows);
    }
  
}
?>
