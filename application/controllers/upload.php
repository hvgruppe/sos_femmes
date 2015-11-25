<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/* Author: Jorge Torres
 * Description: Home controller class
 * This is only viewable to those members that are logged in
 */
session_start();

class Upload extends CI_Controller {

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

    public function upload_window($id) {

        $this->id_femme = $id;

        try {
// General
            $crud = new grocery_CRUD();
            $crud->set_language("french");


            $crud->set_theme('bootstrap');
            $crud->where('id_from_femme', $id);
            $crud->set_table('sos_upload');
            $crud->set_subject('Document');
            $crud->field_type('id_from_femme', 'hidden', $this->id_femme);
//relations

            $crud->set_relation('type_uploads', 'sos_gen_type_uploads_parrent', 'name_type_uploads_parrent');
            $crud->set_relation('detailles', 'sos_gen_type_uploads_child', 'name_type_uploads_child');
//Master/child relations
//Visual
            $crud->columns('type_uploads', 'detailles', 'file_url', 'date_entry');
//unsets

            $crud->unset_fields('date_entry');
            $crud->unset_export();
            $crud->unset_print();
//Requireds
            $crud->required_fields('type_uploads', 'file_url');
//Visual
            $crud->display_as('type_uploads', 'Type de document')
                    ->display_as('detailles', 'Détail')
                    ->display_as('date_entry', 'Date d\'envoi')
                    ->display_as('file_url', 'Document');
//Callbacks
            $crud->callback_after_update(array($this, 'after_update'));
            $crud->callback_before_upload(array($this, 'before_upload'));
            $crud->callback_before_delete(array($this, 'before_delete'));
            $crud->callback_after_insert(array($this, 'insert_after'));
            $crud->callback_before_update(array($this, 'before_update'));
//field Types
             if (!is_dir($path = 'assets/uploads/files/image_' . $this->id_femme)) {
                mkdir($path = 'assets/uploads/files/image_' . $this->id_femme);
            }
            $crud->set_field_upload('file_url', 'assets/uploads/files/image_' . $this->id_femme);
            $fields_type_uploads = array(
                'type_uploads' => array(
                    'table_name' => 'sos_gen_type_uploads_parrent',
                    'title' => 'name_type_uploads_parrent',
                    'relate' => null
                ),
                'detailles' => array(
                    'table_name' => 'sos_gen_type_uploads_child',
                    'title' => 'name_type_uploads_child',
                    'id_field' => 'id_type_uploads_child',
                    'relate' => 'id_parrent_from_type_uploads_parrent',
                    'data-placeholder' => 'Precisé'
                )
            );
            $config_type_uploads = array(
                'main_table' => 'sos_upload',
                'main_table_primary' => 'id_upload',
                "url" => base_url() . 'index.php/upload/upload_window/',
                'segment_name' => "type_uploads"
            );
            $categories_type_uploads = new gc_dependent_select($crud, $fields_type_uploads, $config_type_uploads);
            $js_type_uploads = $categories_type_uploads->get_js();
// Actions

            $this->db->where('id_femme', $this->id_femme);
            $query = $this->db->get('sos_femme');

            if ($query->num_rows == 1) {
                $row_femme = $query->row();
            }

// Renders
            $output = $crud->render();
            $output->output.= $js_type_uploads;
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
        $this->db->where('id_activite', $primary_key);
        $query = $this->db->get('sos_activite');
        if ($query->num_rows == 1) {   // echo var_dump($user).'<br />';
            $row_femme = $query->row();
            $data = array(
                'dump' => random_string('unique')
            );
            $this->db->where('id_femme', $row_femme->id_from_femme);
            $this->db->update('sos_femme', $data);
        }
        return true;
    }

    function insert_after($post_array, $primary_key) {
        $data = array(
            'id_from_femme' => $this->id_femme
        );
        $this->db->where('id_upload', $primary_key);
        $this->db->update('sos_upload', $data);

        return true;
    }

    function before_delete($primary_key) {
        $this->db->where('id_upload', $primary_key);
        $upload = $this->db->get('sos_upload')->row();

        $this->db->where('id_femme', $upload->id_from_femme);
        $query = $this->db->get('sos_femme');
        if ($query->num_rows == 1) {
            $row_femme = $query->row();
        }
        $filepath = 'assets/uploads/files/image_' . $this->id_femme;
        $filename = $upload->file_url;

        if (file_exists($filepath . $filename) && $filename != "" && $filename != "n/a") {
            unlink($filepath . $filename);
            $success = TRUE;
        }
        return true;
    }

    function before_upload($files_to_upload, $field_info) {
        if (!is_dir($field_info->upload_path)) {
            mkdir($field_info->upload_path);
        }
    }

    function before_update($post_array, $primary_key) {

        if ($post_array['type_uploads'] == '') {
            $post_array['detailles'] = '';
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
