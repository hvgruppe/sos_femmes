<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/* Author: Jorge Torres
 * Description: Login model class
 */

class Login_model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    public function validate() {
        // grab user input
        $username = $this->security->xss_clean($this->input->post('username'));
        $password = $this->security->xss_clean($this->input->post('password'));

        // Prep the query
        $this->db->where('identifiant_utilisateur', $username);
        $this->db->where('motdepass_utilisateur', $password);

        // Run the query
        $query = $this->db->get('sos_utilisateur');
        // Let's check if there are any results
        if ($query->num_rows == 1) {
            // If there is a user, then create session data
            $row = $query->row();
            $data = array(
                'userid' => $row->id_utilisateur,
                'fname' => $row->prenom_utilisateur,
                'lname' => $row->nom_utilisateur,
                'status' => $row->status,
                'titre' =>  $row->titre,
                'validated' => true
            );
            $this->session->set_userdata($data);
            return true;
        }
        // If the previous process did not validate
        // then return false.
        return false;
    }

}

?>