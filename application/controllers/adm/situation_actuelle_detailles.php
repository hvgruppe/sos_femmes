<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

session_start();

class Situation_actuelle_detailles extends CI_Controller {

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
            $crud->set_table('sos_gen_situation_actuelle_detailles');
            $crud->set_subject('Situation actuelle détaillée');

//relations
            $crud->set_relation('id_from_situation_actuelle', 'sos_gen_situation_actuelle', 'name_situation_actuelle');
//Master/child relations
//Visual
            $crud->display_as('name_situation_actuelle_detailles', 'Situation actuelle détaillée');
            $crud->display_as('id_from_situation_actuelle', 'Situation actuelle');
            $crud->columns('name_situation_actuelle_detailles', 'id_from_situation_actuelle');

            $crud->order_by('name_situation_actuelle_detailles', 'desc');
//unsets
//Requireds
            $crud->required_fields('name_situation_actuelle_detailles', 'id_from_situation_actuelle');
//Callbacks
//field Types
// Actions
// Renders
            $output = $crud->render();
            $menu = new stdClass;
            $menu->n1 = true;
            $menu->lien3_1_4_2 = false;
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
