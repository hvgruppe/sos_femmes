<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/* Author: Jorge Torres
 * Description: Home controller class
 * This is only viewable to those members that are logged in
 */
session_start();

class Kids extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->load->library('grocery_CRUD');
        $this->check_isvalidated();
        $this->load->model('navigation');
    }

    function _example_output($output = null) {

        $this->load->view('template', $output);
    }

    public function kids_window($id) {
        $this->id_femme = $id;
        try {

            $crud = new grocery_CRUD();
            $crud->set_language("french");
            //$crud->set_theme('datatables');
            $crud->unset_bootstrap();
            $crud->set_theme('twitter-bootstrap');
            $crud->where('id_femme', $id);
            $crud->set_table('sos_kids');
            $crud->set_subject('Enfant(s)');
            $crud->unset_edit_fields('id_kid');
            $crud->unset_add_fields('id_kid');
            $crud->field_type('id_femme', 'hidden', $this->id_femme);
            $crud->columns('prenom', 'nom', 'age', 'sex', 'commentaire');
            $crud->display_as('prenom', 'Prénom');
            $crud->display_as('sex', 'Sexe');
            $crud->display_as('commentaire', 'Commentaires');
            $crud->set_relation('age', 'sos_gen_kids_age', 'name_kids_age');
            if (!$this->session->userdata('status')) {

                $crud->unset_export();
                $crud->unset_print();
            }
            $crud->callback_after_update(array($this, 'after_update'));
            $crud->callback_after_insert(array($this, 'insert_after'));

            $crud->field_type('sex', 'enum', array('Fille', 'Garçon'));


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

    function after_update($post_array, $primary_key) {

        $data = array(
            'id_femme' => $post_array['id_femme'],
            'id_utilisateur' => $this->session->userdata('userid')
        );
        $this->db->insert('sos_ouverture', $data);

        return true;
    }

    function insert_after($post_array, $primary_key) {


        $this->db->where('id_from_femme', $post_array['id_femme']);
        $query = $this->db->get('sos_demande');
        $row_demande = $query->result_array();

        foreach ($row_demande as $key => $value) {

            $data = array(
                'id_from_kids' => $primary_key,
                'id_from_demande' => $value['id_demande']
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
