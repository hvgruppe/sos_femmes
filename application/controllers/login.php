<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/* Author: Jorge Torres
 * Description: Login controller class
 */

class Login extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->helper(array('form'));
    }

    function _example_output($output = null) {
        $this->load->view('login_view', $output);
    }

    public function index($msg = NULL) {
        // Load our view to be displayed
        // to the user

        $this->_example_output((object) array('msg' => $msg, 'output' => '', 'js_files' => array(), 'css_files' => array()));
    }

    public function process() {
        // Load the model
        $this->load->model('login_model');
        // Validate the user can login
        $result = $this->login_model->validate();
        // Now we verify the result
        if (!$result) {
            // If user did not validate, then show them login page again
            $msg = 'Votre mal ou votre mot de passe est incorect !';
            $this->index($msg);
        } else {
             if ($this->session->userdata('status')==5) {
                redirect('ecoute');
            }else{
                redirect('home');
            }
            // If user did validate, 
            // Send them to members area
            
        }
    }

}

?>