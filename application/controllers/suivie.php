<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/* Author: Jorge Torres
 * Description: Home controller class
 * This is only viewable to those members that are logged in
 */
session_start();

class Suivie extends CI_Controller {

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

    public function suivie_window($id) {
        $this->id_psy = $id;

        $this->db->select('sos_psy.id_from_femme');
        $this->db->where('id_psy', $this->id_psy);

        $query = $this->db->get('sos_psy');
        $row_psy = $query->row();
        $this->id_femme = $row_psy->id_from_femme;

        try {
// General
            $crud = new grocery_CRUD();
            $crud->set_language("french");
            $crud->set_theme('datatables');
            $crud->where('id_from_femme', $this->id_femme);
            $crud->where('rdv', 4);
            $crud->set_table('sos_demande');
            $crud->set_subject('Demande');

//relations
//Master/child relations
//Visual
            $crud->columns('date_entry', 'visite', 'commentaire_psy');
//unsets         


            $crud->unset_fields('id_demande', 'date_entry', 'dump', 'visite', 'accueil', 'informations', 'conseil', 'orientation', 'rdv', 'hebergement', 'logement', 'aide_materielle', 'adresse_postale', 'accompagnement_exterieur', 'commentaire');
            $crud->unset_delete();
            $crud->unset_add();
            if (!$this->session->userdata('status')) {
                $crud->unset_export();
                $crud->unset_print();
            }
//Requireds
            //$crud->required_fields('visite');
//Callbacks
            $crud->callback_column('date_entry', array($this, 'date_entry'));
            $crud->callback_column('commentaire_psy', array($this, 'commentaire_psy'));

            $crud->callback_after_update(array($this, 'insert_after'));
            $crud->callback_after_insert(array($this, 'insert_after_insert'));

//field Types
            $crud->field_type('id_from_femme', 'hidden', $this->id_femme);
            $crud->field_type('id_user', 'hidden', $this->session->userdata('userid'));

// Actions
// Renders
            $output = $crud->render();



            $menu = new stdClass;
            $menu->id = $this->id_femme;
            if ($this->session->userdata('status') != '0') {
                $menu->lien1 = true;
                $menu->lien1_2 = true;
                $menu->lien2 = true;
                $menu->lien2_1 = true;
                $menu->lien3 = true;
                $menu->lien3_1 = true;
                $menu->lien3_1_1 = true;
                $menu->lien3_1_2 = true;
                $menu->lien3_1_3 = true;
                $menu->lien3_1_4 = true;
                $menu->lien3_1_5 = true;
                $menu->lien3_2 = true;
                $menu->lien3_2_1 = true;
                $menu->lien3_2_2 = true;
                $menu->lien3_2_3 = true;
            } else {
                $menu->lien1 = true;
                $menu->lien1_2 = true;
                $menu->lien2 = true;
                $menu->lien2_1 = true;
            }
            $header = $this->navigation->home_f($menu);




            $data = array('output' => $output, 'header' => $header);
            $this->_example_output($data);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    function before_delete($primary_key, $empty = FALSE) {

        $this->db->where('id_demande', $primary_key);
        $uploads = $this->db->get('sos_demande')->row();

        $path = 'assets/uploads/files/image_' . $uploads->id_from_femme . '/' . $primary_key;
        if (is_dir($path) === true) {
            $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::CHILD_FIRST);

            foreach ($files as $file) {
                if (in_array($file->getBasename(), array('.', '..')) !== true) {
                    if ($file->isDir() === true) {
                        rmdir($file->getPathName());
                    } else if (($file->isFile() === true) || ($file->isLink() === true)) {
                        unlink($file->getPathname());
                    }
                }
            }

            return rmdir($path);
        } else if ((is_file($path) === true) || (is_link($path) === true)) {
            return unlink($path);
        }

        return false;
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

        $data = array(
            'id_from_demande' => $primary_key
        );

        $this->db->insert('sos_activite', $data);
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
