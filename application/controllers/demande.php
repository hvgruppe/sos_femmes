<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

session_start();

class Demande extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->load->helper('string');
        $this->load->library('grocery_CRUD');
        $this->check_isvalidated();
        $this->load->model('navigation');
    }

    function _example_output($output = null) {

        $this->load->view('template', $output);
    }

    public function demande_window($id) {
        $this->id_femme = $id;
        try {
// General
            $crud = new grocery_CRUD();
            $crud->set_language("french");


            $crud->set_theme('bootstrap');
            $crud->where('id_from_femme', $id);
            $crud->set_table('sos_demande');
            $crud->set_subject('Intervention');
            $crud->display_as('date_entry', 'Date de saisie');
            $crud->display_as('accueil_dem', 'Accueil');
            $crud->display_as('accompagnement_specialise', 'Accompagnement spécialisé');
//relations

            $crud->set_relation('femme', 'sos_gen_demande_femme', 'name_demande_femme');
            $crud->set_relation('accueil_dem', 'sos_gen_demande_accueil', 'name_demande_accueil');
            $crud->set_relation_n_n('lieu_ressource', 'sos_relation_demande_lieu_ressource', 'sos_gen_demande_lieu_ressource', 'id_from_demande', 'id_from_lieu_ressource', 'name_demande_lieu_ressource');
            $crud->set_relation_n_n('accompagnement_specialise', 'sos_relation_demande_accompagnement_specialise', 'sos_gen_demande_accompagnement_specialise', 'id_from_demande', 'id_from_accompagnement_specialise', 'name_demande_accompagnement_specialise');
            $crud->set_relation('hbgt', 'sos_gen_demande_hbgt', 'name_demande_hbgt');
            $crud->set_relation('service', 'sos_gen_service', 'nom_service', null, 'nom_service DESC');

//Master/child relations
//Visual
            $crud->columns('date_entry',  'visite', 'accompagnement_specialise', 'lieu_ressource', 'commentaire');
//unsets
            $crud->unset_fields('id_demande', 'date_entry', 'dump');

            if (!$this->session->userdata('status')) {
                $crud->unset_export();
                $crud->unset_print();
            }
//Requireds
            $crud->required_fields('visite', 'accueil_dem', 'femme', 'service');
//Callbacks
            $crud->callback_column('date_entry', array($this, 'date_entry'));
            $crud->callback_column('visite', array($this, 'visite'));

            $crud->callback_column('commentaire', array($this, 'commentaire'));
            $crud->callback_column('commentaire_psy', array($this, 'commentaire_psy'));
            $crud->callback_column('lieu_ressource', array($this, 'lieu_ressource'));
            $crud->callback_column('accompagnement_specialise', array($this, 'accompagnement_specialise'));
            $crud->callback_after_update(array($this, 'insert_after'));
            $crud->callback_after_insert(array($this, 'insert_after_insert'));

//field Types
            $crud->field_type('id_from_femme', 'hidden', $this->id_femme);
            $crud->field_type('id_user', 'hidden', $this->session->userdata('userid'));

// Actions
            $crud->add_action('Enfants', '', 'enfants/enfants_window', 'ui-icon-person');
            //$crud->add_action('Enfants', base_url('img/pictos/enfants.png'), 'kids/kids_window', 'ui-icon-person');
            // $crud->add_action('Enfants', base_url('img/pictos/enfants.png'), 'enfants/enfants_window', 'ui-icon-person');
// Renders
            $output = $crud->render();

            $menu = new stdClass;
            $menu->n1 = true;
            $menu->status = $this->session->userdata('status');
            $this->db->where('id_femme', $this->id_femme);
            $query = $this->db->get('sos_femme');

            if ($query->num_rows == 1) {
                $row_femme = $query->row();
            }

            $header = $this->navigation->home_f($menu) . ' ' . '<p><pre>' . $row_femme->prenom . ' ' . $row_femme->nom . ' ' . $row_femme->nom_marital . '</pre></p>';
            $data = array('output' => $output, 'header' => $header);
            $this->_example_output($data);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    function lieu_ressource($value, $row) {
        $row_query_lieu_ressource = $this->db->query('SELECT c.name_demande_lieu_ressource
          FROM sos_demande AS a
          JOIN sos_relation_demande_lieu_ressource AS b ON a.id_demande=b.id_from_demande
          JOIN sos_gen_demande_lieu_ressource AS c ON b.id_from_lieu_ressource=c.id_demande_lieu_ressource
          WHERE a.id_demande = ' . $row->id_demande)->result_array();

        $html = '<ul>';
        foreach ($row_query_lieu_ressource as $keys => $values) {
            $html.='<li>' . $values['name_demande_lieu_ressource'] . '</li>';
        }
        $html.='</ul>';
        return $html;
    }

    function accompagnement_specialise($value, $row) {
        $row_query_accompagnement_specialise = $this->db->query('SELECT c.name_demande_accompagnement_specialise
          FROM sos_demande AS a
          JOIN sos_relation_demande_accompagnement_specialise AS b ON a.id_demande=b.id_from_demande
          JOIN sos_gen_demande_accompagnement_specialise AS c ON b.id_from_accompagnement_specialise=c.id_demande_accompagnement_specialise
          WHERE a.id_demande = ' . $row->id_demande)->result_array();

        $html = '<ul>';
        foreach ($row_query_accompagnement_specialise as $keys => $values) {
            $html.='<li>' . $values['name_demande_accompagnement_specialise'] . '</li>';
        }
        $html.='</ul>';
        return $html;
    }

    function commentaire($value, $row) {
        return $row->commentaire;
    }

    function commentaire_psy($value, $row) {
        return $row->commentaire_psy;
    }

    function date_entry($value, $row) {
        $this->db->where('id_utilisateur', $row->id_user);
        $query = $this->db->get('sos_utilisateur');
        if ($query->num_rows == 1) {
            $row_user = $query->row();
            return $row->date_entry . ' par ' . $row_user->prenom_utilisateur . ' ' . $row_user->nom_utilisateur . '</li>';
        }
    }

    function visite($value, $row) {
        $this->db->where('id_service', $row->service);
        $query = $this->db->get('sos_gen_service');
        $service = '***';
        if ($query->num_rows == 1) {
         $row_service = $query->row();
          $service =$row_service->nom_service;
        }
        return $row->visite . '<br> ' . $service;
    }

    function insert_after($post_array, $primary_key) {

        $data = array(
            'id_femme' => $post_array['id_from_femme'],
            'id_utilisateur' => $this->session->userdata('userid')
        );

        $this->db->insert('sos_ouverture', $data);


        $this->db->where('id_demande', $primary_key);
        $query = $this->db->get('sos_demande');
        if ($query->num_rows == 1) {   // echo var_dump($user).'<br />';
            $row_demande = $query->row();
            $data = array(
                'dump' => random_string('unique')
            );
            $this->db->where('id_demande', $row_demande->id_demande);
            $this->db->update('sos_demande', $data);
        }



        return true;
    }

    function insert_after_insert($post_array, $primary_key) {

        $data = array(
            'id_femme' => $post_array['id_from_femme'],
            'id_utilisateur' => $this->session->userdata('userid')
        );

        $this->db->insert('sos_ouverture', $data);


        $this->db->where('id_femme', $post_array['id_from_femme']);
        $query = $this->db->get('sos_kids');
        $row_kids = $query->result_array();

        foreach ($row_kids as $key => $value) {
            $data = array(
                'id_from_kids' => $value["id_kid"],
                'id_from_demande' => $primary_key
            );

            $this->db->insert('sos_enfants', $data);
        }

        return true;
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
