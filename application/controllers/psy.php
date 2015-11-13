<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/* Author: Jorge Torres
 * Description: Home controller class
 * This is only viewable to those members that are logged in
 */
session_start();

class Psy extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->load->helper('string');
        $this->load->library('grocery_CRUD');
        $this->load->library('gc_dependent_select');
        $this->check_isvalidated();
        $this->load->model('navigation');
    }

    function _example_output($output = null) {

        $this->load->view('template', $output);
    }

    public function psy_window($id) {
        $this->id_femme = $id;
        try {


// General
            $crud = new grocery_CRUD();
            $crud->set_language("french");
            $crud->set_theme('datatables');
            //$crud->set_theme('twitter-bootstrap');
            $crud->where('id_from_femme', $this->id_femme);
            $crud->set_table('sos_psy');
            $crud->set_subject('Accompagnement Psychologique');

//relations
            $crud->set_relation_n_n('troubles_physiologiques', 'sos_relation_troubles_physiologiques', 'sos_gen_troubles_physiologiques', 'id_from_psy', 'id_from_troubles_physiologiques', 'name_troubles_physiologiques', 'priority');
            $crud->set_relation_n_n('troubles_cognitifs', 'sos_relation_troubles_cognitifs', 'sos_gen_troubles_cognitifs', 'id_from_psy', 'id_from_troubles_cognitifs', 'name_troubles_cognitifs', 'priority');
            $crud->set_relation_n_n('troubles_emotionnels', 'sos_relation_troubles_emotionnels', 'sos_gen_troubles_emotionnels', 'id_from_psy', 'id_from_troubles_emotionnels', 'name_troubles_emotionnels', 'priority');

//Visual
            $crud->columns('troubles_physiologiques', 'troubles_cognitifs', 'troubles_emotionnels');
            $crud->display_as('troubles_emotionnels', 'Troubles émotionnels');
//unsets
            $crud->unset_delete();
            $crud->unset_add();
//Requireds
//Callbacks
            $crud->callback_column('troubles_physiologiques', array($this, 'troubles_physiologiques'));
            $crud->callback_column('troubles_cognitifs', array($this, 'troubles_cognitifs'));
            $crud->callback_column('troubles_emotionnels', array($this, 'troubles_emotionnels'));
//field Types
            $crud->field_type('id_from_femme', 'hidden', $this->id_femme);
// Actions
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

    function troubles_physiologiques($value, $row) {
        $html = '<ul>';
        $row_query_troubles_physiologiques = $this->db->query('SELECT c.name_troubles_physiologiques
          FROM sos_psy AS a 
          JOIN sos_relation_troubles_physiologiques AS b ON a.id_psy=b.id_from_psy
          JOIN sos_gen_troubles_physiologiques AS c ON b.id_from_troubles_physiologiques=c.id_troubles_physiologiques 
          WHERE a.id_from_femme = ' . $row->id_from_femme)->result_array();
        foreach ($row_query_troubles_physiologiques as $items) {
            $html.='<li>' . $items['name_troubles_physiologiques'] . '</li>';
        }
        $html.='</ul>';
        return $html;
    }

    function troubles_cognitifs($value, $row) {
        $html = '<ul>';
        $row_query_troubles_cognitifs = $this->db->query('SELECT c.name_troubles_cognitifs
          FROM sos_psy AS a 
          JOIN sos_relation_troubles_cognitifs AS b ON a.id_psy=b.id_from_psy
          JOIN sos_gen_troubles_cognitifs AS c ON b.id_from_troubles_cognitifs=c.id_troubles_cognitifs 
          WHERE a.id_from_femme = ' . $row->id_from_femme)->result_array();
        foreach ($row_query_troubles_cognitifs as $items) {
            $html.='<li>' . $items['name_troubles_cognitifs'] . '</li>';
        }
        $html.='</ul>';
        return $html;
    }

    function troubles_emotionnels($value, $row) {
        $html = '<ul>';
        $row_query_troubles_emotionnels = $this->db->query('SELECT c.name_troubles_emotionnels
          FROM sos_psy AS a 
          JOIN sos_relation_troubles_emotionnels AS b ON a.id_psy=b.id_from_psy
          JOIN sos_gen_troubles_emotionnels AS c ON b.id_from_troubles_emotionnels=c.id_troubles_emotionnels 
          WHERE a.id_from_femme = ' . $row->id_from_femme)->result_array();
        foreach ($row_query_troubles_emotionnels as $items) {
            $html.='<li>' . $items['name_troubles_emotionnels'] . '</li>';
        }
        $html.='</ul>';
        return $html;
    }

    function consequences_psychologiques($value, $row) {
        $html = '<ul>';

        $row_query_consequences_psychologiques = $this->db->query('SELECT c.name_consequences_psychologiques
          FROM sos_violences AS a 
          JOIN sos_relation_consequences_psychologiques AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_consequences_psychologiques AS c ON b.id_from_consequences_psychologiques=c.id_consequences_psychologiques 
          WHERE a.id_from_femme = ' . $row->id_from_femme)->result_array();
        foreach ($row_query_consequences_psychologiques as $items) {
            $html.='<li>' . $items['name_consequences_psychologiques'] . '</li>';
        }

        $html.='</ul>';
        return $html;
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
