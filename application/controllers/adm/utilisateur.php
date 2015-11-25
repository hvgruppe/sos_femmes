<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

session_start();

class Utilisateur extends CI_Controller {

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
            $crud->set_table('sos_utilisateur');
            $crud->set_subject('Utilisateurs');

//relations
//Master/child relations
//Visual

            $crud->columns('nom_utilisateur', 'prenom_utilisateur', 'identifiant_utilisateur', 'motdepass_utilisateur', 'status', 'titre');
            $crud->display_as('nom_utilisateur', 'Nom')
                    ->display_as('prenom_utilisateur', 'Prénom')
                    ->display_as('identifiant_utilisateur', 'Id')
                    ->display_as('motdepass_utilisateur', 'Mot de pass');
            $crud->order_by('prenom_utilisateur', 'desc');
//unsets
//Requireds
            $crud->required_fields('identifiant_utilisateur', 'motdepass_utilisateur','status');
//Callbacks
//field Types
            $crud->field_type('status', 'dropdown', array('0' => 'Utilisateur','1' => 'Super Administrateur', '2' => 'Administrateur', '3' => 'Web Master',  '4' => 'Psychologue', '5' => 'Ecoute téléphonique'));
// Actions
            if ($this->session->userdata('status')=='0' OR $this->session->userdata('status')=='2' OR $this->session->userdata('status')=='4' OR $this->session->userdata('status')=='5'){
                $crud->unset_delete();
            }

// Renders
            $output = $crud->render();
            $menu = new stdClass;
            $menu->n1 = true;
            $menu->lien3_7 = false;
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
