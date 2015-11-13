<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

session_start();

class Demarche extends CI_Controller {

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

    public function demarche_window($id) {
        $this->id_femme = $id;
        try {
// General
            $crud = new grocery_CRUD();
            $crud->set_language("french");
            $crud->set_theme('datatables');
            //$crud->set_theme('twitter-bootstrap');
            $crud->where('id_from_femme_demarche', $id);
            $crud->set_table('sos_demarche');
            $crud->set_subject('Démarche');


//relations
//
            $crud->set_relation('first', 'sos_gen_demarche_first', 'name_demarche_first');

            $crud->set_relation('second', 'sos_gen_demarche_second', 'name_demarche_second');
            $crud->set_relation('third', 'sos_gen_demarche_third', 'name_demarche_third');

            $crud->set_relation_n_n('ordonnance_de_protection', 'sos_relation_ordonnance_de_protection', 'sos_gen_ordonnance_de_protection', 'id_from_demarche', 'id_from_ordonnance_de_protection', 'name_ordonnance_de_protection');
            $crud->set_relation_n_n('suites_de_plainte', 'sos_relation_suites_de_plainte', 'sos_gen_suites_de_plainte', 'id_from_demarche', 'id_from_suites_de_plainte', 'name_suites_de_plainte', 'priority');

//Master/child relations
//



            $fields_demarche = array(
                'first' => array(
                    'table_name' => 'sos_gen_demarche_first',
                    'title' => 'name_demarche_first',
                    'relate' => null
                ),
                'second' => array(
                    'table_name' => 'sos_gen_demarche_second',
                    'title' => 'name_demarche_second',
                    'id_field' => 'id_demarche_second',
                    'relate' => 'id_from_demarche_first',
                    'data-placeholder' => 'Préciser'
                ),
                'third' => array(
                    'table_name' => 'sos_gen_demarche_third',
                    'title' => 'name_demarche_third',
                    'id_field' => 'id_demarche_third',
                    'relate' => 'id_from_demarche_second',
                    'data-placeholder' => 'Préciser'
                )
            );
            // 'url' => base_url() . 'index.php/demarche/window_demarche/'. $this->id_femme.'/',
            $config_demarche = array(
                'main_table' => 'sos_demarche',
                'main_table_primary' => 'id_demarche',
                'url' => base_url() . 'index.php/demarche/demarche_window/'
            );
            $categories_demarche = new gc_dependent_select($crud, $fields_demarche, $config_demarche);
            $js_demarche = $categories_demarche->get_js();

//Visual
            $crud->columns('date_entry', 'date_evenement', 'demarche', 'commentaire', 'upload', 'file_url', 'ordonnance_de_protection', 'suites_de_plainte');
            $crud->display_as('upload', 'Type du Document');
            $crud->display_as('file_url', 'Document');
            $crud->display_as('date_evenement', 'Date évenement');
            $crud->display_as('first', 'Type de démarche');
            $crud->display_as('second', 'Type d\'intervention');
            $crud->display_as('third', 'Suites');
            $crud->display_as('date_entry', 'Date de saisie');

//unsets
            $crud->unset_fields('file_url', 'date_entry');

            if (!$this->session->userdata('status')) {
                $crud->unset_export();
                $crud->unset_print();
            }
//Requireds

            $crud->required_fields('first');
//Callbacks
            $crud->callback_before_update(array($this, 'before_update'));

            $crud->callback_after_update(array($this, 'insert_after_update'));
            $crud->callback_after_insert(array($this, 'insert_after_insert'));
            $crud->callback_column('demarche', array($this, 'demarche'));
            $crud->callback_column('commentaire', array($this, 'commentaire'));
            $crud->callback_column('ordonnance_de_protection', array($this, 'ordonnance_de_protection'));
            $crud->callback_column('suites_de_plainte', array($this, 'suites_de_plainte'));
            $crud->callback_column('date_entry', array($this, 'date_entry'));

//field Types
            $crud->field_type('id_from_femme_demarche', 'hidden', $this->id_femme);
            $crud->field_type('id_user', 'hidden', $this->session->userdata('userid'));
            if (!is_dir($path = 'assets/uploads/files/image_' . $this->id_femme)) {
                mkdir($path = 'assets/uploads/files/image_' . $this->id_femme);
            }

            $crud->set_field_upload('file_url', 'assets/uploads/files/image_' . $this->id_femme);


            $this->db->where('id_from_femme', $this->id_femme);
            $results = $this->db->get('sos_upload')->result();
            $upload_multiselect = array();
            foreach ($results as $result) {
                $text_to_show = "";
                $this->db->where('id_type_uploads_parrent', $result->type_uploads);
                $query_type_uploads = $this->db->get('sos_gen_type_uploads_parrent');
                if ($query_type_uploads->num_rows == 1) {
                    $row_type_uploads = $query_type_uploads->row();
                    $text_to_show.= $row_type_uploads->name_type_uploads_parrent;
                }
                $this->db->where('id_type_uploads_child', $result->detailles);
                $query_type_uploads_child = $this->db->get('sos_gen_type_uploads_child');
                if ($query_type_uploads_child->num_rows == 1) {
                    $row_type_uploads_child = $query_type_uploads_child->row();
                    $text_to_show.= '->' . $row_type_uploads_child->name_type_uploads_child;
                }
                $text_to_show.= ' le ' . $result->date_entry;
                $upload_multiselect[$result->id_upload] = $text_to_show;
            }
            if (count($upload_multiselect)) {
                $crud->field_type('upload', 'dropdown', $upload_multiselect);
            } else {
                $crud->field_type('upload', 'hidden', '');
            }
// Actions
// Renders
            $output = $crud->render();
            $output->output.= $js_demarche;
            $menu = new stdClass;
            $menu->n1 = true;
            $menu->status = false;
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

    function date_entry($value, $row) {
        $this->db->where('id_utilisateur', $row->id_user);
        $query = $this->db->get('sos_utilisateur');
        if ($query->num_rows == 1) {
            $row_user = $query->row();
            return $row->date_entry . ' par ' . $row_user->prenom_utilisateur . ' ' . $row_user->nom_utilisateur . '</li>';
        }
    }

    function commentaire($value, $row) {
        return $row->commentaire;
    }

    function suites_de_plainte($value, $row) {
        $html = '';
        $row_query_suites_de_plainte = $this->db->query('SELECT c.name_suites_de_plainte
          FROM sos_demarche AS a 
          JOIN sos_relation_suites_de_plainte AS b ON a.id_demarche=b.id_from_demarche
          JOIN sos_gen_suites_de_plainte AS c ON b.id_from_suites_de_plainte=c.id_suites_de_plainte 
          WHERE a.id_from_femme_demarche = ' . $row->id_from_femme_demarche . ' AND b.id_from_demarche =' . $row->id_demarche . ' ORDER BY b.priority ASC')->result_array();
        if (count($row_query_suites_de_plainte) > 0) {
            $html = '';
            foreach ($row_query_suites_de_plainte as $items) {
                $html.='<li>' . $items['name_suites_de_plainte'] . '</li>';
            }
            $html.='</ul></ul>';
        }



        return $html;
    }

    function ordonnance_de_protection($value, $row) {

        $html = '';
        $row_query_ordonnance_de_protection = $this->db->query('SELECT c.name_ordonnance_de_protection
          FROM sos_demarche AS a 
          JOIN sos_relation_ordonnance_de_protection AS b ON a.id_demarche=b.id_from_demarche
          JOIN sos_gen_ordonnance_de_protection AS c ON b.id_from_ordonnance_de_protection=c.id_ordonnance_de_protection 
          WHERE a.id_from_femme_demarche = ' . $row->id_from_femme_demarche . ' AND b.id_from_demarche =' . $row->id_demarche)->result_array();
        if (count($row_query_ordonnance_de_protection) > 0) {
            $html = '';
            foreach ($row_query_ordonnance_de_protection as $items) {
                $html.='<li>' . $items['name_ordonnance_de_protection'] . '</li>';
            }
            $html.='</ul></ul>';
        }

        return $html;
    }

    function before_update($post_array, $primary_key) {


        if ($post_array['first'] == ''):
            $post_array['second'] = '';
            $post_array['third'] = '';
        elseif ($post_array['second'] == ''):
            $post_array['third'] = '';

        endif;
        return $post_array;


        /* if ($post_array['first'] == '') {
          $post_array['first'] = 0;
          }
          if ($post_array['second'] == '') {
          $post_array['second'] = 0;
          }
          if ($post_array['third'] == '') {
          $post_array['third'] = 0;
          }

          $row_query_first = $this->db->query('SELECT *
          FROM sos_gen_demarche_second
          WHERE  id_from_demarche_first =' . $post_array['first'] . '
          AND  id_demarche_second =' . $post_array['second'])->row();
          if (!$row_query_first) {
          $post_array['second'] = 0;
          $post_array['third'] = 0;
          }

          $row_query_second = $this->db->query('SELECT *
          FROM   sos_gen_demarche_third
          WHERE  id_from_demarche_second =' . $post_array['second'] . '
          AND  id_demarche_third =' . $post_array['third'])->row();
          if (!$row_query_second) {

          $post_array['third'] = 0;
          } */
    }

    function demarche($value, $row) {
        $row_first_str = "";
        if ($row->first > 0) {
            $this->db->where('id_demarche_first', $row->first);
            $query_first = $this->db->get('sos_gen_demarche_first');
            $row_first = $query_first->row();
            $row_first_str = $row_first->name_demarche_first;
        }

        $row_second_str = "";
        if ($row->second > 0) {
            $this->db->where('id_demarche_second', $row->second);
            $query_second = $this->db->get('sos_gen_demarche_second');
            $row_second = $query_second->row();
            $row_second_str = $row_second->name_demarche_second;
        }
        $row_third_str = "";
        if ($row->third > 0) {
            $this->db->where('id_demarche_third', $row->third);
            $query_third = $this->db->get('sos_gen_demarche_third');
            $row_third = $query_third->row();
            $row_third_str = $row_third->name_demarche_third;
        }

        return $row_first_str . '->' . $row_second_str . '->' . $row_third_str;
    }

    function insert_after_update($post_array, $primary_key) {
        $data = array(
            'id_femme' => $post_array['id_from_femme_demarche'],
            'id_utilisateur' => $this->session->userdata('userid')
        );

        $this->db->insert('sos_ouverture', $data);


        $this->db->where('id_upload', $post_array['upload']);
        $query_upload = $this->db->get('sos_upload');

        if ($query_upload->num_rows == 1) {
            $row_query_upload = $query_upload->row();
            $update_insert = array("id_demarche" => $primary_key, "file_url" => $row_query_upload->file_url);
            $this->db->update('sos_demarche', $update_insert, array('id_demarche' => $primary_key));
        } else {
            $update_insert = array("id_demarche" => $primary_key, "file_url" => "");
            $this->db->update('sos_demarche', $update_insert, array('id_demarche' => $primary_key));
        }


        return true;
    }

    function insert_after_insert($post_array, $primary_key) {

        $data = array(
            'id_femme' => $post_array['id_from_femme_demarche'],
            'id_utilisateur' => $this->session->userdata('userid')
        );

        $this->db->insert('sos_ouverture', $data);


        $this->db->where('id_upload', $post_array['upload']);
        $query_upload = $this->db->get('sos_upload');

        if ($query_upload->num_rows == 1) {
            $row_query_upload = $query_upload->row();
            $update_insert = array("id_demarche" => $primary_key, "file_url" => $row_query_upload->file_url);
            $this->db->update('sos_demarche', $update_insert, array('id_demarche' => $primary_key));
        } else {
            $update_insert = array("id_demarche" => $primary_key, "file_url" => "");
            $this->db->update('sos_demarche', $update_insert, array('id_demarche' => $primary_key));
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
