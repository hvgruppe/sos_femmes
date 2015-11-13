<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/* Author: Jorge Torres
 * Description: Home controller class
 * This is only viewable to those members that are logged in
 */
session_start();

class Savedb extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->load->helper('string');

        $this->check_isvalidated();
        $this->load->model('navigation');
    }

    public function index() {
        $this->load->dbutil();
        $this->load->helper('download');

        $backup = & $this->dbutil->backup();
        force_download('mybackup.gz', $backup);
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
