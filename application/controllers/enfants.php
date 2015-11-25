<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

session_start();

class Enfants extends CI_Controller {

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

    public function enfants_window($id) {
        $this->id_demande = $id;
        try {

            $crud = new grocery_CRUD();
            $crud->set_language("french");


            $crud->set_theme('bootstrap');
            $crud->where('id_from_demande', $this->id_demande);
            $crud->set_table('sos_enfants');
            $crud->set_subject('Enfant');

            $crud->unset_add();
            $crud->unset_delete();

            $crud->field_type('id_from_demande', 'hidden', $this->id_demande);

            $crud->unset_edit_fields('id_from_kids');
            $crud->set_relation('id_from_kids', 'sos_kids', "id_kid");
            $crud->set_relation('recu', 'sos_gen_recu', "name_recu", null, 'name_recu ASC');

            $crud->set_relation_n_n('accompagniement', 'sos_relation_accompagniement_kid', 'sos_gen_accompagniement_kid', 'id_from_enfants', 'id_from_accompagniement_kid', 'name_accompagniement_kid');
            $crud->set_relation_n_n('activite', 'sos_relation_activite_kid', 'sos_gen_activite_kid', 'id_from_enfants', 'id_from_activite_kid', 'name_activite_kid');

            $crud->columns('enfant', 'recu', 'activite', 'accompagniement', 'commentaire_enfant');
            //$crud->display_as('id_from_kids', 'Enfants');
            $crud->display_as('accompagniement', 'Accompagnement');

            $crud->display_as('activite', 'Activité');
            $crud->display_as('recu', 'Reçu');
            $crud->display_as('id_from_kids', 'Enfant(s)');
            $crud->display_as('commentaire_enfant', 'Commentaire enfant');
            $crud->callback_column('accompagniement', array($this, 'accompagniement'));
            $crud->callback_column('activite', array($this, 'activite'));
            $crud->callback_column('enfant', array($this, 'enfant'));
            $crud->required_fields('recu');
            $output = $crud->render();

            $this->db->where('id_demande', $this->id_demande);
            $query = $this->db->get('sos_demande');
            if ($query->num_rows == 1) {
                $row_demande = $query->row();
            }
            //   $my_demande = $row_demande->id_from_demande;



            $menu = new stdClass;
            $menu->n3 = true;

            //$menu->id_1 = $my_demande;
            $menu->id = $row_demande->id_from_femme;
            $menu->status = $this->session->userdata('status');
            $this->db->where('id_femme', $menu->id);
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

    function accompagniement($value, $row) {
        $html = '<ul>';
        $row_query_accompagniement = $this->db->query('SELECT c.name_accompagniement_kid
          FROM sos_enfants AS a
          JOIN sos_relation_accompagniement_kid AS b ON a.id_enfants=b.id_from_enfants
          JOIN sos_gen_accompagniement_kid AS c ON b.id_from_accompagniement_kid=c.id_accompagniement_kid
          WHERE a.id_from_kids = ' . $row->id_from_kids . ' AND a.id_from_demande =' . $this->id_demande)->result_array();
        foreach ($row_query_accompagniement as $items) {
            $html.='<li>' . $items['name_accompagniement_kid'] . '</li>';
        }
        $html.= '</ul>';
        return $html;
    }

    function enfant($value, $row) {

        $html = '';
        $row_query_id_from_kids = $this->db->query('SELECT a.prenom,a.nom,a.age,a.sex,b.name_kids_age
          FROM sos_kids AS a
          JOIN sos_gen_kids_age AS b ON a.age=b.id_kids_age
          WHERE a.id_kid = ' . $row->id_from_kids)->result_array();
        foreach ($row_query_id_from_kids as $items) {
            $html.= $items['prenom'] . ' ' . $items['nom'] . ' ' . $items['name_kids_age'] . ' ' . $items['sex'];
        }

        return $html;
    }

    function activite($value, $row) {
        $html = '<ul>';
        $row_query_activite = $this->db->query('SELECT c.name_activite_kid
          FROM sos_enfants AS a
          JOIN sos_relation_activite_kid AS b ON a.id_enfants=b.id_from_enfants
          JOIN sos_gen_activite_kid AS c ON b.id_from_activite_kid=c.id_activite_kid
          WHERE a.id_from_kids = ' . $row->id_from_kids . ' AND a.id_from_demande =' . $this->id_demande)->result_array();
        foreach ($row_query_activite as $items) {
            $html.='<li>' . $items['name_activite_kid'] . '</li>';
        }
        $html.='</ul>';
        return $html;
    }

    function insert_after($post_array, $primary_key) {

        $data = array(
            'id_femme' => $post_array['id_femme'],
            'id_utilisateur' => $this->session->userdata('userid')
        );

        $this->db->insert('sos_ouverture', $data);

        return true;
    }

    private function check_isvalidated() {
        if (!$this->session->userdata('validated')) {
            redirect('login');
        }
    }

    public
            function do_logout() {
        $this->session->sess_destroy();
        redirect('login');
    }

}

?>
