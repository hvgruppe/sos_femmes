<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

session_start();

class V_enfants_indirectes extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->load->library('grocery_CRUD');
        $this->load->library('gc_dependent_select');
        $this->check_isvalidated();
        $this->load->model('navigation');
    }

    function _example_output($output = null) {
        $this->load->view('template', $output);
    }

    public function index() {
        try {

// General
            $crud = new grocery_CRUD();
            $crud->set_language("french");
            //$crud->set_theme('datatables');
            $crud->unset_bootstrap();
            $crud->set_theme('twitter-bootstrap');
            $crud->set_table('sos_gen_violences_enfants_indirectes');
            $crud->set_subject('Violences indirectes sur les enfants');

//relations
//Master/child relations
//Visual

            $crud->columns('name_violences_enfants_indirectes');
            $crud->display_as('name_violences_enfants_indirectes', 'Violences indirectes sur les enfants');
            $crud->order_by('name_violences_enfants_indirectes', 'desc');
//unsets
//Requireds
            $crud->required_fields('name_violences_enfants_indirectes');
//Callbacks
//field Types
// Actions
// Renders
            $output = $crud->render();
            $menu = new stdClass;
            $menu->n1 = true;
            $menu->lien3_3_5_10 = false;
            $menu->status = $this->session->userdata('status');
            $header = $this->navigation->home_f($menu);

            $data = array('output' => $output, 'header' => $header);

            $this->_example_output($data);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    private function check_isvalidated() {
        if (!$this->session->userdata('validated')) {
            redirect('login');
        }
    }

    public function do_logout() {
        $this->session->sess_destroy();
        redirect('login');
    }

}

?>
