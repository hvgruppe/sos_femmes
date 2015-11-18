<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

session_start();

class Statistiques extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->check_isvalidated();
        $this->load->model('navigation');
        $this->load->helper('form');
        $this->load->library('form_builder');
    }

    function _example_output($output = null) {
        $this->load->view('template_stats', $output);
    }

    public function index($mesage = NULL) {
        try {
            $menu = new stdClass;
            $menu->n1 = true;
            $menu->status = $this->session->userdata('status');
            $header = $this->navigation->home_f($menu);


            $output = $this->makeScript();

            $input_span = (isset($input_span)) ? $input_span . ' ' : '';

            /* Begin building form */

            $yearRange = 5;
            $thisYear = date('Y');
            $startYear = ($thisYear - 5);



            $exp_year_options = array();
            foreach (range($thisYear, $startYear) as $year) {
                $exp_year_options[$year] = $year;
            }
            $thisMonth = date('m');
            $cc_exp_month = $thisMonth;



            $query = $this->db->query('SELECT * FROM sos_editor WHERE type=2');

            $list_rapports = array();
            foreach ($query->result() as $row) {
                $list_rapports[$row->id_editor] = $row->abrev;
            }


           /* $form_options = array(
                array(
                    'id' => 'rapport',
                    'label' => 'Rapport',
                    'type' => 'dropdown',
                    'options' => $list_rapports,
                    'autocomplete' => 'cc-type',
                    'class' => $input_span . 'required input-medium',
                    'required' => '',
                    'value' => isset($cc_type) ? $cc_type : ''
                ),
                array(
                    'id' => 'de',
                    'label' => 'Plage dans anuelle',
                    'type' => 'combine',
                    'elements' => array(
                        array(
                            'id' => 'premier_mois',
                            'label' => 'Du',
                            'autocomplete' => 'premier_mois',
                            'type' => 'dropdown',
                            'options' => array(
                                '01' => 'JAN',
                                '02' => 'FEV',
                                '03' => 'MAR',
                                '04' => 'AVR',
                                '05' => 'MAI',
                                '06' => 'JUIN',
                                '07' => 'JUIL',
                                '08' => 'AOUT',
                                '09' => 'SEPT',
                                '10' => 'OCT',
                                '11' => 'NOV',
                                '12' => 'DEC'
                            ),
                            'class' => $input_span . 'required input-small',
                            'required' => '',
                            'data-items' => '4',
                            'pattern' => '\d{1,2}',
                            'style' => 'width: auto;',
                            'value' => 01
                        ),
                        array(
                            'id' => 'dernier_mois',
                            'label' => 'Au',
                            'autocomplete' => 'dernier_mois',
                            'type' => 'dropdown',
                            'options' => array(
                                '01' => 'JAN',
                                '02' => 'FEV',
                                '03' => 'MAR',
                                '04' => 'AVR',
                                '05' => 'MAI',
                                '06' => 'JUIN',
                                '07' => 'JUIL',
                                '08' => 'AOUT',
                                '09' => 'SEPT',
                                '10' => 'OCT',
                                '11' => 'NOV',
                                '12' => 'DEC'
                            ),
                            'class' => $input_span . 'required input-small',
                            'required' => '',
                            'data-items' => '4',
                            'pattern' => '\d{1,2}',
                            'style' => 'width: auto;',
                            'value' => (isset($cc_exp_month) ? $cc_exp_month : '')
                        ),
                        array(
                            'id' => 'anee',
                            'autocomplete' => 'anee',
                            'type' => 'dropdown',
                            'options' => $exp_year_options,
                            'class' => $input_span . 'required input-small',
                            'required' => '',
                            'data-items' => '4',
                            'pattern' => '\d{4}',
                            'style' => 'width: auto; margin-left: 5px;',
                            'value' => (isset($cc_exp_year) ? $cc_exp_year : '')
                        )
                    )
                ),
                array(
                    'id' => 'Envoyer',
                    'type' => 'submit'
                ),
            );*/
            $form_options = array(
                array(
                    'id' => 'rapport',
                    'label' => 'Rapport',
                    'type' => 'dropdown',
                    'options' => $list_rapports,
                    'autocomplete' => 'cc-type',
                    'class' => $input_span . 'required input-medium',
                    'required' => '',
                    'value' => isset($cc_type) ? $cc_type : ''
                ),
                array(
                    'id' => 'de',
                    'label' => 'Année',
                    'type' => 'combine',
                    'elements' => array(

                        array(
                            'id' => 'anee',
                            'autocomplete' => 'anee',
                            'type' => 'dropdown',
                            'options' => $exp_year_options,
                            'class' => $input_span . 'required input-small',
                            'required' => '',
                            'data-items' => '4',
                            'pattern' => '\d{4}',
                            'style' => 'width: auto; margin-left: 5px;',
                            'value' => (isset($cc_exp_year) ? $cc_exp_year : '')
                        )
                    )
                ),
                array(
                    'id' => 'Envoyer',
                    'type' => 'submit'
                ),
            );

            $output['output'] = form_open('statistiques/process') . '<div class="container">' . $this->form_builder->build_form_horizontal($form_options) . '</div><br>' . form_close();
            $data = array('output' => $output, 'header' => $header, 'mesage' => $mesage);

            $this->_example_output($data);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    private function makeScript($output = null) {
        $output['js_files'] = array(base_url() . "assets/grocery_crud/js/jquery-1.10.2.min.js",
            base_url() . "assets/grocery_crud/js/jquery_plugins/jquery.noty.js",
            base_url() . "assets/grocery_crud/js/jquery_plugins/config/jquery.noty.config.js",
            base_url() . "assets/grocery_crud/js/common/lazyload-min.js",
            base_url() . "assets/grocery_crud/js/common/list.js",
            base_url() . "assets/grocery_crud/js/jquery_plugins/ui/jquery-ui-1.10.3.custom.min.js",
            base_url() . "assets/grocery_crud/themes/datatables/js/jquery.dataTables.min.js",
            base_url() . "assets/grocery_crud/themes/datatables/js/datatables-extras.js",
            base_url() . "assets/grocery_crud/themes/datatables/js/datatables.js",
            base_url() . "assets/grocery_crud/themes/datatables/extras/TableTools/media/js/ZeroClipboard.js",
            base_url() . "assets/grocery_crud/themes/datatables/extras/TableTools/media/js/TableTools.min.js",
            base_url() . "assets/grocery_crud/js/jquery_plugins/jquery.fancybox-1.3.4.js",
            base_url() . "assets/grocery_crud/js/jquery_plugins/jquery.easing-1.3.pack.js");
        $output['js_lib_files'] = array(base_url() . "assets/grocery_crud/js/jquery-1.10.2.min.js",
            base_url() . "assets/grocery_crud/js/jquery_plugins/jquery.noty.js",
            base_url() . "assets/grocery_crud/js/jquery_plugins/config/jquery.noty.config.js",
            base_url() . "assets/grocery_crud/js/common/lazyload-min.js",
            base_url() . "assets/grocery_crud/js/common/list.js",
            base_url() . "assets/grocery_crud/js/jquery_plugins/ui/jquery-ui-1.10.3.custom.min.js",
            base_url() . "assets/grocery_crud/themes/datatables/js/jquery.dataTables.min.js");
        $output['css_files'] = array(base_url() . "assets/grocery_crud/themes/datatables/css/demo_table_jui.css",
            base_url() . "grocery_crud/css/ui/simple/jquery-ui-1.10.1.custom.min.css",
            base_url() . "assets/grocery_crud/themes/datatables/css/datatables.css",
            base_url() . "assets/grocery_crud/themes/datatables/css/jquery.dataTables.css",
            base_url() . "assets/grocery_crud/themes/datatables/extras/TableTools/media/css/TableTools.css",
            base_url() . "assets/grocery_crud/css/jquery_plugins/fancybox/jquery.fancybox.css");
        return $output;
    }

    public function process() {

        // Load the model
        $this->load->model('statistiques_model');

        $result = $this->statistiques_model->validate();

        // Now we verify the result
        if (!$result) {

            $this->index('Incompatibilité des dates');
        } else {






            $this->index();
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
