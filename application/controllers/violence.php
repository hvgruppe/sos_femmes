<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/* Author: Jorge Torres
 * Description: Home controller class
 * This is only viewable to those members that are logged in
 */
session_start();

class Violence extends CI_Controller {

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

    public function violence_window($id) {
        $this->id_femme = $id;
        try {


// General
            $crud = new grocery_CRUD();
            $crud->set_language("french");
            $crud->set_theme('datatables');
            //$crud->set_theme('twitter-bootstrap');
            $crud->where('id_from_femme', $this->id_femme);
            $crud->set_table('sos_violences');
            $crud->set_subject('Violence');

//relations
            $crud->set_relation_n_n('violences_physiques', 'sos_relation_violences_physiques', 'sos_gen_violences_physiques', 'id_from_violences', 'id_from_violences_physiques', 'name_violences_physiques', 'priority');
            $crud->set_relation_n_n('violences_psychologiques', 'sos_relation_violences_psychologiques', 'sos_gen_violences_psychologiques', 'id_from_violences', 'id_from_violences_psychologiques', 'name_violences_psychologiques', 'priority');
            $crud->set_relation_n_n('violences_sexuelles', 'sos_relation_violences_sexuelles', 'sos_gen_violences_sexuelles', 'id_from_violences', 'id_from_violences_sexuelles', 'name_violences_sexuelles', 'priority');
            $crud->set_relation_n_n('violences_economiques', 'sos_relation_violences_economiques', 'sos_gen_violences_economiques', 'id_from_violences', 'id_from_violences_economiques', 'name_violences_economiques', 'priority');
            $crud->set_relation_n_n('violences_administratives', 'sos_relation_violences_administratives', 'sos_gen_violences_administratives', 'id_from_violences', 'id_from_violences_administratives', 'name_violences_administratives', 'priority');
            $crud->set_relation_n_n('violences_sociales', 'sos_relation_violences_sociales', 'sos_gen_violences_sociales', 'id_from_violences', 'id_from_violences_sociales', 'name_violences_sociales', 'priority');
            $crud->set_relation_n_n('violences_privations', 'sos_relation_violences_privations', 'sos_gen_violences_privations', 'id_from_violences', 'id_from_violences_privations', 'name_violences_privations', 'priority');
            $crud->set_relation_n_n('violences_juridiques', 'sos_relation_violences_juridiques', 'sos_gen_violences_juridiques', 'id_from_violences', 'id_from_violences_juridiques', 'name_violences_juridiques', 'priority');
            $crud->set_relation_n_n('de_la_part', 'sos_relation_de_la_part', 'sos_gen_de_la_part', 'id_from_violences', 'id_from_de_la_part', 'name_de_la_part');





            $crud->set_relation('frequence', 'sos_gen_frequence', 'name_frequence');
            $crud->set_relation('commencement', 'sos_gen_commencement', 'name_commencement');





            $crud->set_relation_n_n('raisons', 'sos_relation_raisons', 'sos_gen_raisons', 'id_from_violences', 'id_from_raisons', 'name_raisons');



            $crud->set_relation_n_n('violences_enfants_directes', 'sos_relation_violences_enfants_directes', 'sos_gen_violences_enfants_directes', 'id_from_violences', 'id_from_violences_enfants_directes', 'name_violences_enfants_directes', 'priority');
            $crud->set_relation_n_n('violences_enfants_indirectes', 'sos_relation_violences_enfants_indirectes', 'sos_gen_violences_enfants_indirectes', 'id_from_violences', 'id_from_violences_enfants_indirectes', 'name_violences_enfants_indirectes', 'priority');

            $crud->set_relation_n_n('de_la_part_enfants', 'sos_relation_de_la_part_enfants', 'sos_gen_de_la_part_enfants', 'id_from_violences', 'id_from_de_la_part_enfants', 'name_de_la_part_enfants');





            $crud->set_relation_n_n('consequences_physiques', 'sos_relation_consequences_physiques', 'sos_gen_consequences_physiques', 'id_from_violences', 'id_from_consequences_physiques', 'name_consequences_physiques', 'priority');
            $crud->set_relation_n_n('consequences_psychologiques', 'sos_relation_consequences_psychologiques', 'sos_gen_consequences_psychologiques', 'id_from_violences', 'id_from_consequences_psychologiques', 'name_consequences_psychologiques', 'priority');
            $crud->set_relation_n_n('consequences_administratives', 'sos_relation_consequences_administratives', 'sos_gen_consequences_administratives', 'id_from_violences', 'id_from_consequences_administratives', 'name_consequences_administratives', 'priority');
//Visual
            $crud->columns('violences', 'de_la_part', 'frequence', 'commencement', 'raisons', 'consequences');

            $crud->display_as('frequence', 'Fréquence')
                    ->display_as('consequences', 'Conséquences')
                    ->display_as('consequences_administratives', 'Conséquences administratives et économiques')
                    ->display_as('violences_economiques', 'Violences économiques')
                    ->display_as('consequences_physiques', 'Conséquences physiques')
                    ->display_as('consequences_psychologiques', 'Conséquences psychologiques')
                    ->display_as('consequences_administratives', 'Conséquences administratives');


//unsets
            $crud->unset_delete();
            $crud->unset_add();
//Requireds
//Callbacks


            $crud->callback_column('de_la_part', array($this, 'de_la_part'));
            $crud->callback_column('raisons', array($this, 'raisons'));


            $crud->callback_column('violences', array($this, 'violences'));
            $crud->callback_column('consequences', array($this, 'consequences'));
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

    function de_la_part($value, $row) {


        $html = '';
        $row_query_de_la_part = $this->db->query('SELECT c.name_de_la_part
          FROM sos_violences AS a 
          JOIN sos_relation_de_la_part AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_de_la_part AS c ON b.id_from_de_la_part=c.id_de_la_part 
          WHERE a.id_from_femme = ' . $row->id_from_femme)->result_array();
        if (count($row_query_de_la_part) > 0) {
            $html = '<ul><b>Femme</b><ul>';
            foreach ($row_query_de_la_part as $items) {
                $html.='<li>' . $items['name_de_la_part'] . '</li>';
            }
            $html.='</ul></ul>';
        }

        $row_query_de_la_part_enfants = $this->db->query('SELECT c.name_de_la_part_enfants
          FROM sos_violences AS a 
          JOIN sos_relation_de_la_part_enfants AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_de_la_part_enfants AS c ON b.id_from_de_la_part_enfants=c.id_de_la_part_enfants 
          WHERE a.id_from_femme = ' . $row->id_from_femme)->result_array();
        if (count($row_query_de_la_part_enfants) > 0) {
            $html.= '<ul><b>Enfants</b><ul>';
            foreach ($row_query_de_la_part_enfants as $items) {
                $html.='<li>' . $items['name_de_la_part_enfants'] . '</li>';
            }
            $html.='</ul></ul>';
        }

        return $html;
    }

    function raisons($value, $row) {
        $html = '<ul>';
        $row_query_raisons = $this->db->query('SELECT c.name_raisons
          FROM sos_violences AS a 
          JOIN sos_relation_raisons AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_raisons AS c ON b.id_from_raisons=c.id_raisons 
          WHERE a.id_from_femme = ' . $row->id_from_femme)->result_array();
        foreach ($row_query_raisons as $items) {
            $html.='<li>' . $items['name_raisons'] . '</li>';
        }
        $html.='</ul>';
        return $html;
    }

    function consequences_physiques($value, $row) {
        $html = '<ul>';
        $row_query_consequences_physiques = $this->db->query('SELECT c.name_consequences_physiques
          FROM sos_violences AS a 
          JOIN sos_relation_consequences_physiques AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_consequences_physiques AS c ON b.id_from_consequences_physiques=c.id_consequences_physiques 
          WHERE a.id_from_femme = ' . $row->id_from_femme)->result_array();
        foreach ($row_query_consequences_physiques as $items) {
            $html.='<li>' . $items['name_consequences_physiques'] . '</li>';
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

    function violences($value, $row) {

        $html = '';
        $row_query_violences_physiques = $this->db->query('SELECT c.name_violences_physiques
          FROM sos_violences AS a 
          JOIN sos_relation_violences_physiques AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_violences_physiques AS c ON b.id_from_violences_physiques=c.id_violences_physiques 
          WHERE a.id_from_femme = ' . $row->id_from_femme . ' ORDER BY b.priority ASC')->result_array();
        if (count($row_query_violences_physiques) > 0) {
            $html.= '<ul><li><b>Physiques</b><ul>';
            foreach ($row_query_violences_physiques as $items) {
                $html.='<li>' . $items['name_violences_physiques'] . '</li>';
            }
            $html.='</ul></li></ul>';
        }

        $row_query_violences_psychologiques = $this->db->query('SELECT c.name_violences_psychologiques
          FROM sos_violences AS a 
          JOIN sos_relation_violences_psychologiques AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_violences_psychologiques AS c ON b.id_from_violences_psychologiques=c.id_violences_psychologiques 
          WHERE a.id_from_femme = ' . $row->id_from_femme . ' ORDER BY b.priority ASC')->result_array();
        if (count($row_query_violences_psychologiques) > 0) {
            $html.= '<ul><li><b>Psychologiques</b><ul>';
            foreach ($row_query_violences_psychologiques as $items) {
                $html.='<li>' . $items['name_violences_psychologiques'] . '</li>';
            }
            $html.='</ul></li></ul>';
        }


        $row_query_violences_sexuelles = $this->db->query('SELECT c.name_violences_sexuelles
          FROM sos_violences AS a 
          JOIN sos_relation_violences_sexuelles AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_violences_sexuelles AS c ON b.id_from_violences_sexuelles=c.id_violences_sexuelles 
          WHERE a.id_from_femme = ' . $row->id_from_femme . ' ORDER BY b.priority ASC')->result_array();
        if (count($row_query_violences_sexuelles) > 0) {
            $html.= '<ul><li><b>Sexuelles</b><ul>';
            foreach ($row_query_violences_sexuelles as $items) {
                $html.='<li>' . $items['name_violences_sexuelles'] . '</li>';
            }
            $html.='</ul></li></ul>';
        }


        $row_query_violences_economiques = $this->db->query('SELECT c.name_violences_economiques
          FROM sos_violences AS a 
          JOIN sos_relation_violences_economiques AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_violences_economiques AS c ON b.id_from_violences_economiques=c.id_violences_economiques 
          WHERE a.id_from_femme = ' . $row->id_from_femme . ' ORDER BY b.priority ASC')->result_array();
        if (count($row_query_violences_economiques) > 0) {
            $html.= '<ul><li><b>Economiques</b><ul>';
            foreach ($row_query_violences_economiques as $items) {
                $html.='<li>' . $items['name_violences_economiques'] . '</li>';
            }
            $html.='</ul></li></ul>';
        }

        $row_query_violences_administratives = $this->db->query('SELECT c.name_violences_administratives
          FROM sos_violences AS a 
          JOIN sos_relation_violences_administratives AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_violences_administratives AS c ON b.id_from_violences_administratives=c.id_violences_administratives 
          WHERE a.id_from_femme = ' . $row->id_from_femme . ' ORDER BY b.priority ASC')->result_array();
        if (count($row_query_violences_administratives) > 0) {
            $html.= '<ul><li><b>Administratives</b><ul>';
            foreach ($row_query_violences_administratives as $items) {
                $html.='<li>' . $items['name_violences_administratives'] . '</li>';
            }
            $html.='</ul></li></ul>';
        }

        $row_query_violences_sociales = $this->db->query('SELECT c.name_violences_sociales
          FROM sos_violences AS a 
          JOIN sos_relation_violences_sociales AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_violences_sociales AS c ON b.id_from_violences_sociales=c.id_violences_sociales 
          WHERE a.id_from_femme = ' . $row->id_from_femme . ' ORDER BY b.priority ASC')->result_array();
        if (count($row_query_violences_sociales) > 0) {
            $html.= '<ul><li><b>Sociales</b><ul>';
            foreach ($row_query_violences_sociales as $items) {
                $html.='<li>' . $items['name_violences_sociales'] . '</li>';
            }
            $html.='</ul></li></ul>';
        }

        $row_query_violences_privations = $this->db->query('SELECT c.name_violences_privations
          FROM sos_violences AS a 
          JOIN sos_relation_violences_privations AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_violences_privations AS c ON b.id_from_violences_privations=c.id_violences_privations 
          WHERE a.id_from_femme = ' . $row->id_from_femme . ' ORDER BY b.priority ASC')->result_array();
        if (count($row_query_violences_privations) > 0) {
            $html.= '<ul><li><b>Privations</b><ul>';
            foreach ($row_query_violences_privations as $items) {

                $html.='<li>' . $items['name_violences_privations'] . '</li>';
            }
            $html.='</ul></li></ul>';
        }

        $row_query_violences_juridiques = $this->db->query('SELECT c.name_violences_juridiques
          FROM sos_violences AS a 
          JOIN sos_relation_violences_juridiques AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_violences_juridiques AS c ON b.id_from_violences_juridiques=c.id_violences_juridiques 
          WHERE a.id_from_femme = ' . $row->id_from_femme . ' ORDER BY b.priority ASC')->result_array();
        if (count($row_query_violences_juridiques) > 0) {
            $html.= '<ul><li><b>Juridiques</b><ul>';
            foreach ($row_query_violences_juridiques as $items) {

                $html.='<li>' . $items['name_violences_juridiques'] . '</li>';
            }
            $html.='</ul></li></ul>';
        }

        $pour_les_enfants = '';
        $row_query_violences_enfants_directes = $this->db->query('SELECT c.name_violences_enfants_directes
          FROM sos_violences AS a 
          JOIN sos_relation_violences_enfants_directes AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_violences_enfants_directes AS c ON b.id_from_violences_enfants_directes=c.id_violences_enfants 
          WHERE a.id_from_femme = ' . $row->id_from_femme . ' ORDER BY b.priority ASC')->result_array();
        if (count($row_query_violences_enfants_directes) > 0) {
            $pour_les_enfants.= '<ul><li>Directes<ul>';
            foreach ($row_query_violences_enfants_directes as $items) {

                $pour_les_enfants.='<li>' . $items['name_violences_enfants_directes'] . '</li>';
            }
            $pour_les_enfants.='</ul></li></ul>';
        }
        $row_query_violences_enfants_indirectes = $this->db->query('SELECT c.name_violences_enfants_indirectes
          FROM sos_violences AS a 
          JOIN sos_relation_violences_enfants_indirectes AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_violences_enfants_indirectes AS c ON b.id_from_violences_enfants_indirectes=c.id_violences_enfants 
          WHERE a.id_from_femme = ' . $row->id_from_femme . ' ORDER BY b.priority ASC')->result_array();
        if (count($row_query_violences_enfants_indirectes) > 0) {
            $pour_les_enfants.= '<ul><li>Indirectes<ul>';
            foreach ($row_query_violences_enfants_indirectes as $items) {

                $pour_les_enfants.='<li>' . $items['name_violences_enfants_indirectes'] . '</li>';
            }
            $pour_les_enfants.='</ul></li></ul>';
        }

        if ($pour_les_enfants != '') {
            $pour_les_enfants = '<ul><li><b>Sur les enfants</b>' . $pour_les_enfants . '</li></ul>';
        }
        $html.=$pour_les_enfants;
        return $html;
    }

    function consequences($value, $row) {

        $html = '';
        $row_query_consequences_physiques = $this->db->query('SELECT c.name_consequences_physiques
          FROM sos_violences AS a 
          JOIN sos_relation_consequences_physiques AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_consequences_physiques AS c ON b.id_from_consequences_physiques=c.id_consequences_physiques 
          WHERE a.id_from_femme = ' . $row->id_from_femme)->result_array();
        if (count($row_query_consequences_physiques) > 0) {
            $html.= '<ul><li><b>Physiques</b><ul>';
            foreach ($row_query_consequences_physiques as $items) {
                $html.='<li>' . $items['name_consequences_physiques'] . '</li>';
            }
            $html.='</ul></li></ul>';
        }

        $row_query_consequences_psychologiques = $this->db->query('SELECT c.name_consequences_psychologiques
          FROM sos_violences AS a 
          JOIN sos_relation_consequences_psychologiques AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_consequences_psychologiques AS c ON b.id_from_consequences_psychologiques=c.id_consequences_psychologiques 
          WHERE a.id_from_femme = ' . $row->id_from_femme)->result_array();
        if (count($row_query_consequences_psychologiques) > 0) {
            $html.= '<ul><li><b>Psychologiques</b><ul>';
            foreach ($row_query_consequences_psychologiques as $items) {
                $html.='<li>' . $items['name_consequences_psychologiques'] . '</li>';
            }
            $html.='</ul></li></ul>';
        }

        $row_query_consequences_administratives = $this->db->query('SELECT c.name_consequences_administratives
          FROM sos_violences AS a 
          JOIN sos_relation_consequences_administratives AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_consequences_administratives AS c ON b.id_from_consequences_administratives=c.id_consequences_administratives 
          WHERE a.id_from_femme = ' . $row->id_from_femme)->result_array();
        if (count($row_query_consequences_administratives) > 0) {
            $html.= '<ul><li><b>Administratives</b><ul>';
            foreach ($row_query_consequences_administratives as $items) {
                $html.='<li>' . $items['name_consequences_administratives'] . '</li>';
            }
            $html.='</ul></li></ul>';
        }


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
