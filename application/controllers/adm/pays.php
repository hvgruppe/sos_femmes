<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

session_start();

class Pays extends CI_Controller {

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


            $crud->set_theme('bootstrap');
            $crud->set_table('sos_gen_pays');
            $crud->set_subject('Pays');

//relations
//Master/child relations
//Visual

            $crud->columns('nom_pays', 'continent');
            $crud->display_as('nom_pays', 'Pays');
            $crud->display_as('continent', 'Continants');
            $crud->order_by('nom_pays', 'desc');
//unsets
//Requireds
            $crud->required_fields('nom_pays', 'continent');
//Callbacks
//field Types
            $crud->field_type('continent', 'enum', array('Afrique', 'Amérique', 'Asie', 'Europe', 'Océanie'));
// Actions
// Renders
            $output = $crud->render();
            $menu = new stdClass;
            $menu->n1 = true;
            $menu->lien3_1_1_3 = false;
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
