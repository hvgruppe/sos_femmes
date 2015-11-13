<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/* Author: Jorge Torres
 * Description: Login model class
 */

class Navigation extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->database();
    }

    public function home_f($menu) {
        $lien1 = array("disabled", "#");
        $lien1_1 = array("disabled", "#");
        $lien1_2 = array("disabled", "#");
        $lien1_3 = array("disabled", "#");
        $lien1_4 = array("disabled", "#");
        $lien1_5 = array("disabled", "#");


        $lien1_5_1 = array("disabled", "#");
        $lien1_5_2 = array("disabled", "#");
        $lien1_6 = array("disabled", "#");
        $lien1_7 = array("disabled", "#");

        $query = $this->db->query('SELECT * FROM sos_editor WHERE type=1');
        $putit = '<ul class="dropdown-menu">';

        foreach ($query->result() as $row) {
            $putit.='<li class="disabled"><a href="#">' . $row->abrev . '</a></li>';
        }
        $putit.='</ul>';


        $lien2 = array("disabled", "#");
        $lien2_1 = array("disabled", "#");
        $lien2_2 = array("disabled", "#");
        $lien3 = array("disabled", "#");
        $lien3_1 = array("disabled", "#");
        $lien3_1_1 = array("disabled", "#");
        $lien3_1_1_1 = array("disabled", "#");
        $lien3_1_1_2 = array("disabled", "#");
        $lien3_1_1_3 = array("disabled", "#");
        $lien3_1_1_4 = array("disabled", "#");
        $lien3_1_2 = array("disabled", "#");
        $lien3_1_2_1 = array("disabled", "#");
        $lien3_1_2_2 = array("disabled", "#");
        $lien3_1_2_3 = array("disabled", "#");
        $lien3_1_3 = array("disabled", "#");
        $lien3_1_3_1 = array("disabled", "#");
        $lien3_1_3_2 = array("disabled", "#");
        $lien3_1_4 = array("disabled", "#");
        $lien3_1_4_1 = array("disabled", "#");
        $lien3_1_4_2 = array("disabled", "#");
        $lien3_1_5 = array("disabled", "#");
        $lien3_1_6 = array("disabled", "#");
        $lien3_1_7 = array("disabled", "#");
        $lien3_1_8 = array("disabled", "#");
        $lien3_1_9 = array("disabled", "#");
        $lien3_1_10 = array("disabled", "#");
        $lien3_1_11 = array("disabled", "#");
        $lien3_1_12 = array("disabled", "#");
        $lien3_1_13 = array("disabled", "#");
        $lien3_2 = array("disabled", "#");
        $lien3_2_1 = array("disabled", "#");
        $lien3_2_2 = array("disabled", "#");
        $lien3_2_3 = array("disabled", "#");
        $lien3_2_4 = array("disabled", "#");
        $lien3_2_5 = array("disabled", "#");
        $lien3_2_6 = array("disabled", "#");
        $lien3_2_6_1 = array("disabled", "#");
        $lien3_2_6_2 = array("disabled", "#");
        $lien3_3 = array("disabled", "#");
        $lien3_3_1 = array("disabled", "#");
        $lien3_3_2 = array("disabled", "#");
        $lien3_3_3 = array("disabled", "#");
        $lien3_3_4 = array("disabled", "#");
        $lien3_3_5 = array("disabled", "#");
        $lien3_3_5_1 = array("disabled", "#");
        $lien3_3_5_2 = array("disabled", "#");
        $lien3_3_5_3 = array("disabled", "#");
        $lien3_3_5_4 = array("disabled", "#");
        $lien3_3_5_5 = array("disabled", "#");
        $lien3_3_5_6 = array("disabled", "#");
        $lien3_3_5_7 = array("disabled", "#");
        $lien3_3_5_8 = array("disabled", "#");
        $lien3_3_5_9 = array("disabled", "#");
        $lien3_3_5_10 = array("disabled", "#");
        $lien3_3_5_11 = array("disabled", "#");
        $lien3_3_6 = array("disabled", "#");
        $lien3_3_6_1 = array("disabled", "#");
        $lien3_3_6_2 = array("disabled", "#");
        $lien3_3_6_3 = array("disabled", "#");
        $lien3_4 = array("disabled", "#");
        $lien3_4_1 = array("disabled", "#");
        $lien3_4_2 = array("disabled", "#");
        $lien3_4_3 = array("disabled", "#");
        $lien3_4_4 = array("disabled", "#");
        $lien3_4_5 = array("disabled", "#");
        $lien3_5 = array("disabled", "#");
        $lien3_5_1 = array("disabled", "#");
        $lien3_5_2 = array("disabled", "#");
        $lien3_5_3 = array("disabled", "#");
        $lien3_6 = array("disabled", "#");
        $lien3_7 = array("disabled", "#");
        $lien3_8 = array("disabled", "#");
        $lien3_9 = array("disabled", "#");
        $lien3_9_1 = array("disabled", "#");
        $lien3_9_2 = array("disabled", "#");
        $lien4 = array("", "#");
        $lien4 = array("", base_url('index.php/ecoute'));
        if (property_exists($menu, 'status')) {
            if ($menu->status == '1' OR $menu->status == '2' OR $menu->status == '3') {
                $lien1 = array("", "#");
                $lien1_3 = array("", base_url('index.php/savedb'));
                //$lien1_4 = array("", base_url('index.php/myform'));
                $lien1_4 = array("", 'http://www.efthymios.fr/contact/formualaire_contact.php');
                $lien1_7 = array("", base_url('index.php/statistiques'));
                $lien3 = array("", "#");
                $lien3_1 = array("", "#");
                $lien3_1_1 = array("", "#");
                $lien3_1_1_1 = array("", base_url('index.php/adm/partenaire'));
                $lien3_1_1_2 = array("", base_url('index.php/adm/villes'));
                $lien3_1_1_3 = array("", base_url('index.php/adm/pays'));
                $lien3_1_1_4 = array("", base_url('index.php/adm/nationalite'));
                $lien3_1_2 = array("", "#");

                $lien3_1_2_1 = array("", base_url('index.php/adm/emplois'));
                $lien3_1_2_2 = array("", base_url('index.php/adm/detaills_emploi'));
                $lien3_1_2_3 = array("", base_url('index.php/adm/plus_detaills_emploi'));

                $lien3_1_3 = array("", "#");

                $lien3_1_3_1 = array("", base_url('index.php/adm/logement'));
                $lien3_1_3_2 = array("", base_url('index.php/adm/detaills_logement'));

                $lien3_1_4 = array("", "#");


                $lien3_1_4_1 = array("", base_url('index.php/adm/situation_actuelle'));
                $lien3_1_4_2 = array("", base_url('index.php/adm/situation_actuelle_detailles'));

                $lien3_1_5 = array("", base_url('index.php/adm/provenance'));
                $lien3_1_6 = array("", base_url('index.php/adm/informations'));
                $lien3_1_7 = array("", base_url('index.php/adm/rdv'));
                $lien3_1_8 = array("", base_url('index.php/adm/hebergement'));
                $lien3_1_9 = array("", base_url('index.php/adm/age_enfants'));
                $lien3_1_10 = array("", base_url('index.php/adm/age_femme'));
                $lien3_1_11 = array("", base_url('index.php/adm/departs_anterieurs'));
                $lien3_1_12 = array("", base_url('index.php/adm/depuis'));
                $lien3_1_13 = array("", base_url('index.php/adm/duree_de_la_relation'));
                $lien3_2 = array("", "#");
                $lien3_2_1 = array("", base_url('index.php/adm/femme'));
                $lien3_2_2 = array("", base_url('index.php/adm/accueil'));
                $lien3_2_3 = array("", base_url('index.php/adm/hbgt'));
                $lien3_2_4 = array("", base_url('index.php/adm/accompagnement_specialise'));
                $lien3_2_5 = array("", base_url('index.php/adm/lieu_ressource'));
                $lien3_2_6 = array("", "#");

                $lien3_2_6_1 = array("", base_url('index.php/adm/accompagnement_kid'));
                $lien3_2_6_2 = array("", base_url('index.php/adm/activite_kid'));
                $lien3_3 = array("", "#");

                $lien3_3_1 = array("", base_url('index.php/adm/frequence'));
                $lien3_3_2 = array("", base_url('index.php/adm/commencement'));
                $lien3_3_3 = array("", base_url('index.php/adm/de_la_part'));
                $lien3_3_4 = array("", base_url('index.php/adm/raisons'));
                $lien3_3_5 = array("", "#");

                $lien3_3_5_1 = array("", base_url('index.php/adm/physiques'));
                $lien3_3_5_2 = array("", base_url('index.php/adm/psychologiques'));
                $lien3_3_5_3 = array("", base_url('index.php/adm/sexuelles'));
                $lien3_3_5_4 = array("", base_url('index.php/adm/economiques'));
                $lien3_3_5_5 = array("", base_url('index.php/adm/administratives'));
                $lien3_3_5_6 = array("", base_url('index.php/adm/sociales'));
                $lien3_3_5_7 = array("", base_url('index.php/adm/privations'));
                $lien3_3_5_8 = array("", base_url('index.php/adm/juridiques'));
                $lien3_3_5_9 = array("", base_url('index.php/adm/v_enfants_directes'));
                $lien3_3_5_10 = array("", base_url('index.php/adm/v_enfants_indirectes'));
                $lien3_3_5_11 = array("", base_url('index.php/adm/de_la_part_enfants'));
                $lien3_3_6 = array("", "#");
                $lien3_3_6_1 = array("", base_url('index.php/adm/c_physiques'));
                $lien3_3_6_2 = array("", base_url('index.php/adm/c_psychologiques'));
                $lien3_3_6_3 = array("", base_url('index.php/adm/c_administratives'));
                $lien3_4 = array("", "#");
                $lien3_4_1 = array("", base_url('index.php/adm/niv1'));
                $lien3_4_2 = array("", base_url('index.php/adm/niv2'));
                $lien3_4_3 = array("", base_url('index.php/adm/niv3'));
                $lien3_4_4 = array("", base_url('index.php/adm/o_d_p'));
                $lien3_4_5 = array("", base_url('index.php/adm/s_d_p'));
                $lien3_5 = array("", "#");
                $lien3_5_1 = array("", base_url('index.php/adm/t_psychologiques'));
                $lien3_5_2 = array("", base_url('index.php/adm/t_cognitifs'));
                $lien3_5_3 = array("", base_url('index.php/adm/t_emotionnels'));
                $lien3_6 = array("", base_url('index.php/adm/service'));
                $lien3_7 = array("", base_url('index.php/adm/utilisateur'));
                $lien3_8 = array("", base_url('index.php/adm/editor'));
                $lien3_9 = array("", "#");
                $lien3_9_1 = array("", base_url('index.php/adm/interlocuteur'));
                $lien3_9_2 = array("", base_url('index.php/adm/appel'));



                $query = $this->db->query('SELECT * FROM sos_editor WHERE type=2');
                $putit_stats = '<ul class="dropdown-menu">';

                foreach ($query->result() as $row) {
                    $putit_stats.='<li class=""><a href="#">' . $row->abrev . '</a></li>';
                }
                $putit_stats.='</ul>';
            }
            if ($menu->status == '4') {
                $lien3 = array("", "#");
                $lien3_5 = array("", "#");
                $lien3_5_1 = array("", base_url('index.php/adm/t_psychologiques'));
                $lien3_5_2 = array("", base_url('index.php/adm/t_cognitifs'));
                $lien3_5_3 = array("", base_url('index.php/adm/t_emotionnels'));
                $lien4 = array("disabled", "#");
            }
        }




        if (property_exists($menu, 'n0')) {
            if ($menu->n0) {
                $lien1 = array("", "#");
                $lien1_2 = array("", base_url('index.php/home/do_logout'));
            }
        }
        if (property_exists($menu, 'n1')) {
            if ($menu->n1) {
                $lien1 = array("", "#");
                if (property_exists($menu, 'id')) {
                    $lien1_1 = array("", base_url('index.php/show/show_window/' . $menu->id . '/pdf'));
                    $lien1_5 = array("", "#");
                    if (property_exists($menu, 'first')) {
                        if ($menu->first) {
                            $lien1_5_1 = array("", base_url('index.php/show/make_first/' . $menu->id));
                        } else {
                            if ($menu->status == '1' OR $menu->status == '2' OR $menu->status == '3') {
                                $lien1_5_2 = array("", base_url('index.php/premier_acceuil/first/' . $menu->id));
                            }
                        }
                    }
                    $lien1_6 = array("", "#");
                    $query = $this->db->query('SELECT * FROM sos_editor WHERE type=1');
                    $putit = '<ul class="dropdown-menu">';

                    foreach ($query->result() as $row) {
                        $putit.='<li class=""><a href="' . base_url('index.php/show/show_window/' . $menu->id) . '/attestations/' . $row->id_editor . '">' . $row->abrev . '</a></li>';
                    }
                    $putit.='</ul>';
                }
                $lien1_2 = array("", base_url('index.php/home/do_logout'));
                $lien2 = array("", "#");
                $lien2_1 = array("", base_url('index.php/home'));
            }
        }

        if (property_exists($menu, 'n2')) {
            if ($menu->n2) {
                $lien1 = array("", "#");
                $lien1_2 = array("", base_url('index.php/home/do_logout'));
                $lien2 = array("", "#");
                $lien2_1 = array("", base_url('index.php/home'));
                if (property_exists($menu, 'id')) {
                    $lien2_2 = array("", base_url('index.php/demande/demande_window/' . $menu->id));
                }
            }
        }
        if (property_exists($menu, 'n2_bis')) {
            if ($menu->n2_bis) {
                $lien1 = array("", "#");
                $lien1_2 = array("", base_url('index.php/home/do_logout'));
                $lien2 = array("", "#");
                $lien2_1 = array("", base_url('index.php/home'));
                if (property_exists($menu, 'id')) {
                    $lien2_2 = array("", base_url('index.php/demarche/demarche_window/' . $menu->id));
                }
            }
        }
        if (property_exists($menu, 'n3')) {
            if ($menu->n3) {
                $lien1 = array("", "#");
                $lien1_2 = array("", base_url('index.php/home/do_logout'));
                $lien2 = array("", "#");
                $lien2_1 = array("", base_url('index.php/home'));
                if (property_exists($menu, 'id')) {
                    $lien2_2 = array("", base_url('index.php/demande/demande_window/' . $menu->id));
                }
            }
        }

        if (property_exists($menu, 'lien1')) {
            if (!$menu->lien1) {
                $lien1 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien1_1')) {
            if (!$menu->lien1_1) {
                $lien1_1 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien1_2')) {
            if (!$menu->lien1_2) {
                $lien1_2 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien1_3')) {
            if (!$menu->lien1_3) {
                $lien1_3 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien1_4')) {
            if (!$menu->lien1_4) {
                $lien1_4 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien1_5')) {
            if (!$menu->lien1_5) {
                $lien1_5 = array("disabled", "#");
                $lien1_5_1 = array("disabled", "#");
                $lien1_5_2 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien2')) {
            if (!$menu->lien2) {
                $lien2 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien2_1')) {
            if (!$menu->lien2_1) {
                $lien2_1 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien2_2')) {
            if (!$menu->lien2_2) {
                $lien2_2 = array("disabled", "#");
            }
        }

        if (property_exists($menu, 'lien3')) {
            if (!$menu->lien3) {
                $lien3 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_1')) {
            if (!$menu->lien3_1) {
                $lien3_1 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_1_1')) {
            if (!$menu->lien3_1_1) {
                $lien3_1_1 = array("disabled", "#");
            }
        }

        if (property_exists($menu, 'lien3_1_1_1')) {
            if (!$menu->lien3_1_1_1) {
                $lien3_1_1_1 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_1_1_2')) {
            if (!$menu->lien3_1_1_2) {
                $lien3_1_1_2 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_1_1_3')) {
            if (!$menu->lien3_1_1_3) {
                $lien3_1_1_3 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_1_1_4')) {
            if (!$menu->lien3_1_1_4) {
                $lien3_1_1_4 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_1_2')) {
            if (!$menu->lien3_1_2) {
                $lien3_1_2 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_1_2_1')) {
            if (!$menu->lien3_1_2_1) {
                $lien3_1_2_1 = array("disabled", "#");
            }
        }

        if (property_exists($menu, 'lien3_1_2_2')) {
            if (!$menu->lien3_1_2_2) {
                $lien3_1_2_2 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_1_2_3')) {
            if (!$menu->lien3_1_2_3) {
                $lien3_1_2_3 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_1_3')) {
            if (!$menu->lien3_1_3) {
                $lien3_1_3 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_1_3_1')) {
            if (!$menu->lien3_1_3_1) {
                $lien3_1_3_1 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_1_3_2')) {
            if (!$menu->lien3_1_3_2) {
                $lien3_1_3_2 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_1_4')) {
            if (!$menu->lien3_1_4) {
                $lien3_1_4 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_1_4_1')) {
            if (!$menu->lien3_1_4_1) {
                $lien3_1_4_1 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_1_4_2')) {
            if (!$menu->lien3_1_4_2) {
                $lien3_1_4_2 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_1_5')) {
            if (!$menu->lien3_1_5) {
                $lien3_1_5 = array("disabled", "#");
            }
        }

        if (property_exists($menu, 'lien3_1_6')) {
            if (!$menu->lien3_1_6) {
                $lien3_1_6 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_1_7')) {
            if (!$menu->lien3_1_7) {
                $lien3_1_7 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_1_8')) {
            if (!$menu->lien3_1_8) {
                $lien3_1_8 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_1_9')) {
            if (!$menu->lien3_1_9) {
                $lien3_1_9 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_1_10')) {
            if (!$menu->lien3_1_10) {
                $lien3_1_10 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_1_11')) {
            if (!$menu->lien3_1_11) {
                $lien3_1_11 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_1_12')) {
            if (!$menu->lien3_1_12) {
                $lien3_1_12 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_1_13')) {
            if (!$menu->lien3_1_13) {
                $lien3_1_13 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_2')) {
            if (!$menu->lien3_2) {
                $lien3_2 = array("disabled", "#");
            }
        }


        if (property_exists($menu, 'lien3_2_1')) {
            if (!$menu->lien3_2_1) {
                $lien3_2_1 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_2_2')) {
            if (!$menu->lien3_2_2) {
                $lien3_2_2 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_2_3')) {
            if (!$menu->lien3_2_3) {
                $lien3_2_3 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_2_4')) {
            if (!$menu->lien3_2_4) {
                $lien3_2_4 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_2_5')) {
            if (!$menu->lien3_2_5) {
                $lien3_2_5 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_2_6')) {
            if (!$menu->lien3_2_6) {
                $lien3_2_6 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_2_6_1')) {
            if (!$menu->lien3_2_6_1) {
                $lien3_2_6_1 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_2_6_2')) {
            if (!$menu->lien3_2_6_2) {
                $lien3_2_6_2 = array("disabled", "#");
            }
        }

        if (property_exists($menu, 'lien3_3')) {
            if (!$menu->lien3_3) {
                $lien3_3 = array("disabled", "#");
            }
        }

        if (property_exists($menu, 'lien3_3_1')) {
            if (!$menu->lien3_3_1) {
                $lien3_3_1 = array("disabled", "#");
            }
        }

        if (property_exists($menu, 'lien3_3_2')) {
            if (!$menu->lien3_3_2) {
                $lien3_3_2 = array("disabled", "#");
            }
        }

        if (property_exists($menu, 'lien3_3_3')) {
            if (!$menu->lien3_3_3) {
                $lien3_3_3 = array("disabled", "#");
            }
        }

        if (property_exists($menu, 'lien3_3_4')) {
            if (!$menu->lien3_3_4) {
                $lien3_3_4 = array("disabled", "#");
            }
        }

        if (property_exists($menu, 'lien3_3_5')) {
            if (!$menu->lien3_3_5) {
                $lien3_3_5 = array("disabled", "#");
            }
        }

        if (property_exists($menu, 'lien3_3_5_1')) {
            if (!$menu->lien3_3_5_1) {
                $lien3_3_5_1 = array("disabled", "#");
            }
        }

        if (property_exists($menu, 'lien3_3_5_2')) {
            if (!$menu->lien3_3_5_2) {
                $lien3_3_5_2 = array("disabled", "#");
            }
        }

        if (property_exists($menu, 'lien3_3_5_3')) {
            if (!$menu->lien3_3_5_3) {
                $lien3_3_5_3 = array("disabled", "#");
            }
        }

        if (property_exists($menu, 'lien3_3_5_4')) {
            if (!$menu->lien3_3_5_4) {
                $lien3_3_5_4 = array("disabled", "#");
            }
        }

        if (property_exists($menu, 'lien3_3_5_5')) {
            if (!$menu->lien3_3_5_5) {
                $lien3_3_5_5 = array("disabled", "#");
            }
        }

        if (property_exists($menu, 'lien3_3_5_6')) {
            if (!$menu->lien3_3_5_6) {
                $lien3_3_5_6 = array("disabled", "#");
            }
        }

        if (property_exists($menu, 'lien3_3_5_7')) {
            if (!$menu->lien3_3_5_7) {
                $lien3_3_5_7 = array("disabled", "#");
            }
        }

        if (property_exists($menu, 'lien3_3_5_8')) {
            if (!$menu->lien3_3_5_8) {
                $lien3_3_5_8 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_3_5_9')) {
            if (!$menu->lien3_3_5_9) {
                $lien3_3_5_9 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_3_5_10')) {
            if (!$menu->lien3_3_5_10) {
                $lien3_3_5_10 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_3_5_11')) {
            if (!$menu->lien3_3_5_11) {
                $lien3_3_5_11 = array("disabled", "#");
            }
        }



        if (property_exists($menu, 'lien3_3_6')) {
            if (!$menu->lien3_3_6) {
                $lien3_3_6 = array("disabled", "#");
            }
        }

        if (property_exists($menu, 'lien3_3_6_1')) {
            if (!$menu->lien3_3_6_1) {
                $lien3_3_6_1 = array("disabled", "#");
            }
        }

        if (property_exists($menu, 'lien3_3_6_2')) {
            if (!$menu->lien3_3_6_2) {
                $lien3_3_6_2 = array("disabled", "#");
            }
        }

        if (property_exists($menu, 'lien3_3_6_3')) {
            if (!$menu->lien3_3_6_3) {
                $lien3_3_6_3 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_4')) {
            if (!$menu->lien3_4) {
                $lien3_4 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_4_1')) {
            if (!$menu->lien3_4_1) {
                $lien3_4_1 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_4_2')) {
            if (!$menu->lien3_4_2) {
                $lien3_4_2 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_4_3')) {
            if (!$menu->lien3_4_3) {
                $lien3_4_3 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_4_4')) {
            if (!$menu->lien3_4_4) {

                $lien3_4_4 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_4_5')) {
            if (!$menu->lien3_4_5) {

                $lien3_4_5 = array("disabled", "#");
            }
        }

        if (property_exists($menu, 'lien3_5')) {
            if (!$menu->lien3_5) {
                $lien3_5 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_5_1')) {
            if (!$menu->lien3_5_1) {
                $lien3_5_1 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_5_2')) {
            if (!$menu->lien3_5_2) {
                $lien3_5_2 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_5_3')) {
            if (!$menu->lien3_5_3) {
                $lien3_5_3 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_6')) {
            if (!$menu->lien3_6) {
                $lien3_6 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_7')) {
            if (!$menu->lien3_7) {
                $lien3_7 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_8')) {
            if (!$menu->lien3_8) {
                $lien3_8 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_9')) {
            if (!$menu->lien3_9) {
                $lien3_9 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_9_1')) {
            if (!$menu->lien3_9_1) {
                $lien3_9_1 = array("disabled", "#");
            }
        }
        if (property_exists($menu, 'lien3_9_2')) {
            if (!$menu->lien3_9_2) {
                $lien3_9_2 = array("disabled", "#");
            }
        }

        /* return '<div class="dropdown">
          <nav class="nav-menu clearfix">
          <ul>
          <li class="current-page-ancestor has-subpages"><a href="#">Parent page</a>
          <ul class="children">
          <li><a href="#">Child page 1</a></li>
          <li><a href="#">Child page 2</a></li>
          <li class="has-subpages"><a href="#">Level 1</a>
          <ul class="children">
          <li class="has-subpages"><a href="#">Level 2</a>
          <ul class="children">
          <li><a href="#">Level 3</a></li>
          </ul>
          </li>
          </ul>
          </li>
          <li class="current-page-ancestor has-subpages"><a href="#">Level 1 a</a>
          <ul class="children">
          <li class="current-page-ancestor has-subpages"><a href="#">Level 2 a</a>
          <ul class="children">
          <li class="current-page-item"><a href="#">Level 3 a</a></li>
          <li><a href="#">Level 3 c</a></li>
          </ul>
          </li>
          <li><a href="#">Level 2 c</a></li>
          </ul>
          </li>
          <li><a href="#">Level 1 b</a></li>
          </ul>
          </li>
          <li class="has-subpages"><span class="menu-item">Not link item</span>
          <ul class="children">
          <li class="has-subpages"><a href="#">Level 2</a>
          <ul class="children">
          <li><a href="#">Level 3</a></li>
          </ul>
          </li>
          </ul>
          </li>
          <li><a href="#">Sample Page</a></li>
          </ul>

          <ul class="pull-right">
          <li class="has-subpages"><a href="#">new menu item</a>
          <ul>
          <li class="has-subpages"><a href="#">sub item 1</a>
          <ul>
          <li><a href="#">sub sub item 1</a></li>
          <li><a href="#">sub sub item 2</a></li>
          </ul>
          </li>
          <li><a href="#">sub item 2</a></li>
          </ul>
          </li>
          <li class="has-subpages"><span class="menu-item">Hello, username</span>
          <ul>
          <li class="has-subpages"><a href="#">sub item 1</a>
          <ul>
          <li><a href="#">sub sub item 1</a></li>
          <li><a href="#">sub sub item 2</a></li>
          </ul>
          </li>
          <li><a href="#">sub item 2</a></li>
          </ul>
          </li>
          </ul>
          </nav>
          </div>'; */

        return '
<ul class="nav nav-pills">
  <li class="dropdown">
    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
      Actions <span class="caret"></span>
    </a>
   <ul class="dropdown-menu">
                <li class="' . $lien1_1[0] . '"><a href="' . $lien1_1[1] . '">Exporter dossier PDF</a></li>
                <li class="' . $lien1_3[0] . '"><a href="' . $lien1_3[1] . '">Exporter base de données</a></li>      
                <li class="divider"></li>                
                <li class="dropdown-submenu ' . $lien1_5[0] . '">
                    <a tabindex="-1" href="' . $lien1_5[1] . '">Premier acceuil</a>
                    <ul class="dropdown-menu">
                        <li class="' . $lien1_5_1[0] . '"><a href="' . $lien1_5_1[1] . '">Valider</a></li>
                        <li class="' . $lien1_5_2[0] . '"><a href="' . $lien1_5_2[1] . '">Modifier premier accueil</a></li>
                    </ul>
                </li>
                <li class="dropdown-submenu ' . $lien1_6[0] . '">
                    <a tabindex="-1" href="' . $lien1_6[1] . '">Attestations</a>' . $putit .
                '</li>
                     <li class="' . $lien1_7[0] . '"><a href="' . $lien1_7[1] . '">Statistiques</a></li>
                <li class="divider"></li>
                <li class="' . $lien1_4[0] . '"><a href="' . $lien1_4[1] . '" target="_blank">Contact</a></li>
                <li class="' . $lien1_2[0] . '"><a href="' . $lien1_2[1] . '">Sortir</a></li>
            </ul>
  </li>
  <li class="dropdown">
    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
      Navigation <span class="caret"></span>
    </a>
     <ul class="dropdown-menu">
               <li class="' . $lien2_1[0] . '"><a href="' . $lien2_1[1] . '">Liste femmes</a></li>
               <li class="' . $lien2_2[0] . '"><a href="' . $lien2_2[1] . '">Liste passages</a></li>
            </ul>
  </li>
  <li class="dropdown ' . $lien4[0] . '"><a href="' . $lien4[1] . '">Ecoute téléphonique </a></li>
  <li class="dropdown">
    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
      Administrateur <span class="caret"></span>
    </a>
     <ul class="dropdown-menu">
                <li class="dropdown-submenu ' . $lien3_1[0] . '">
                    <a tabindex="-1" href="' . $lien3_1[1] . '">Menu Femme</a>
                    <ul class="dropdown-menu">
                        <li class="dropdown-submenu ' . $lien3_1_1[0] . '">
                            <a tabindex="-1" href="' . $lien3_1_1[1] . '">Infos</a>
                            <ul class="dropdown-menu">
                                <li class="' . $lien3_1_1_1[0] . '"><a href="' . $lien3_1_1_1[1] . '">Orienteurs</a></li>
                                <li class="' . $lien3_1_1_2[0] . '"><a href="' . $lien3_1_1_2[1] . '">Villes</a></li>
                                <li class="' . $lien3_1_1_3[0] . '"><a href="' . $lien3_1_1_3[1] . '">Pays</a></li>
                                <li class="' . $lien3_1_1_4[0] . '"><a href="' . $lien3_1_1_4[1] . '">Situation administrative</a></li>
                            </ul>
                        </li>
                        <li class="dropdown-submenu ' . $lien3_1_2[0] . '">
                            <a tabindex="-1" href="' . $lien3_1_2[1] . '">Emploi</a>
                            <ul class="dropdown-menu">
                                <li class="' . $lien3_1_2_1[0] . '"><a href="' . $lien3_1_2_1[1] . '">Situation professionnelle</a></li>
                                <li class="' . $lien3_1_2_2[0] . '"><a href="' . $lien3_1_2_2[1] . '">Situation professionnelle détaillée</a></li>
                                <li class="' . $lien3_1_2_3[0] . '"><a href="#' . $lien3_1_2_3[1] . '">Autres infos emploi</a></li>
                            </ul>
                        </li>
                        <li class="dropdown-submenu ' . $lien3_1_3[0] . '">
                            <a tabindex="-1" href="' . $lien3_1_3[1] . '">Logement</a>
                            <ul class="dropdown-menu">
                                <li class="' . $lien3_1_3_1[0] . '"><a href="' . $lien3_1_3_1[1] . '">Logement</a></li>
                                <li class="' . $lien3_1_3_2[0] . '"><a href="' . $lien3_1_3_2[1] . '">Logement détaillé</a></li>
                            </ul>
                        </li>
                        <li class="dropdown-submenu ' . $lien3_1_4[0] . '">
                            <a tabindex="-1" href="' . $lien3_1_4[1] . '">Situation</a>
                            <ul class="dropdown-menu">
                                <li class="' . $lien3_1_4_1[0] . '"><a href="' . $lien3_1_4_1[1] . '">Situation Actuelle</a></li>
                                <li class="' . $lien3_1_4_2[0] . '"><a href="' . $lien3_1_4_2[1] . '">Situation actuelle détaillée</a></li>
                            </ul>
                        </li>
                        <li class="' . $lien3_1_5[0] . '"><a href="' . $lien3_1_5[1] . '">Provenance ressources</a></li>
                        <li class="' . $lien3_1_6[0] . '"><a href="' . $lien3_1_6[1] . '">Demande d\'informations</a></li>
                        <li class="' . $lien3_1_7[0] . '"><a href="' . $lien3_1_7[1] . '">Demande de Rdv</a></li>
                        <li class="' . $lien3_1_8[0] . '"><a href="' . $lien3_1_8[1] . '">Demande d\'hébergement</a></li>
                        <li class="' . $lien3_1_9[0] . '"><a href="' . $lien3_1_9[1] . '">Age des enfants</a></li>
                        <li class="' . $lien3_1_10[0] . '"><a href="' . $lien3_1_10[1] . '">Age de la femme</a></li>
                        <li class="' . $lien3_1_11[0] . '"><a href="' . $lien3_1_11[1] . '">Départs antérieurs</a></li>
                        <li class="' . $lien3_1_12[0] . '"><a href="' . $lien3_1_12[1] . '">Depuis</a></li>
                        <li class="' . $lien3_1_13[0] . '"><a href="' . $lien3_1_13[1] . '">Durée de la relation</a></li>
                    </ul>
                </li>
                <li class="dropdown-submenu ' . $lien3_2[0] . '">
                    <a tabindex="-1" href="' . $lien3_2[1] . '">Menu Passage</a>
                    <ul class="dropdown-menu">
                        <li class="' . $lien3_2_1[0] . '"><a href="' . $lien3_2_1[1] . '">Femme</a></li>
                        <li class="' . $lien3_2_2[0] . '"><a href="' . $lien3_2_2[1] . '">Accueil</a></li>
                        <li class="' . $lien3_2_3[0] . '"><a href="' . $lien3_2_3[1] . '">Hbgt</a></li>
                        <li class="' . $lien3_2_4[0] . '"><a href="' . $lien3_2_4[1] . '">Accompagnement spécialisé</a></li>
                        <li class="' . $lien3_2_5[0] . '"><a href="' . $lien3_2_5[1] . '">Lieu ressource</a></li>                            
                        <li class="dropdown-submenu ' . $lien3_2_6[0] . '">
                            <a tabindex="-1" href="' . $lien3_2_6[1] . '">Menu Enfants</a>
                            <ul class="dropdown-menu">
                                <li class="' . $lien3_2_6_1[0] . '"><a href="' . $lien3_2_6_1[1] . '">Accompagnement</a></li>
                                <li class="' . $lien3_2_6_2[0] . '"><a href="' . $lien3_2_6_2[1] . '">Activité</a></li>
                            </ul>
                        </li>
                    </ul>   
                </li>
                <li class="dropdown-submenu ' . $lien3_3[0] . '">
                    <a tabindex="-1" href="' . $lien3_3[1] . '">Menu Violence</a>
                    <ul class="dropdown-menu">
                        <li class="' . $lien3_3_1[0] . '"><a href="' . $lien3_3_1[1] . '">Fréquence</a></li>
                        <li class="' . $lien3_3_2[0] . '"><a href="' . $lien3_3_2[1] . '">Commencement</a></li>
                        <li class="' . $lien3_3_3[0] . '"><a href="' . $lien3_3_3[1] . '">De la part</a></li>
                        <li class="' . $lien3_3_4[0] . '"><a href="' . $lien3_3_4[1] . '">Raisons</a></li>                         
                        <li class="dropdown-submenu ' . $lien3_3_5[0] . '">
                            <a tabindex="-1" href="' . $lien3_3_5[1] . '">Violences</a>
                            <ul class="dropdown-menu">
                                <li class="' . $lien3_3_5_1[0] . '"><a href="' . $lien3_3_5_1[1] . '">Physiques</a></li>
                                <li class="' . $lien3_3_5_2[0] . '"><a href="' . $lien3_3_5_2[1] . '">Psychologiques</a></li>
                                <li class="' . $lien3_3_5_3[0] . '"><a href="' . $lien3_3_5_3[1] . '">Sexuelles</a></li>
                                <li class="' . $lien3_3_5_4[0] . '"><a href="' . $lien3_3_5_4[1] . '">Economiques</a></li>
                                <li class="' . $lien3_3_5_5[0] . '"><a href="' . $lien3_3_5_5[1] . '">Administratives</a></li>
                                <li class="' . $lien3_3_5_6[0] . '"><a href="' . $lien3_3_5_6[1] . '">Sociales</a></li>
                                <li class="' . $lien3_3_5_7[0] . '"><a href="' . $lien3_3_5_7[1] . '">Privations</a></li>
                                <li class="' . $lien3_3_5_8[0] . '"><a href="' . $lien3_3_5_8[1] . '">Juridiques</a></li>
                                <li class="' . $lien3_3_5_9[0] . '"><a href="' . $lien3_3_5_9[1] . '">Directes sur les enfants</a></li>
                                <li class="' . $lien3_3_5_10[0] . '"><a href="' . $lien3_3_5_10[1] . '">Indirectes sur les enfants</a></li>
                                <li class="' . $lien3_3_5_11[0] . '"><a href="' . $lien3_3_5_11[1] . '">De la part - enfants</a></li>
                            </ul>
                        </li>
                        <li class="dropdown-submenu ' . $lien3_3_6[0] . '">
                            <a tabindex="-1" href="' . $lien3_3_6[1] . '">Conséquences</a>
                            <ul class="dropdown-menu">
                                <li class="' . $lien3_3_6_1[0] . '"><a href="' . $lien3_3_6_1[1] . '">Physiques</a></li>
                                <li class="' . $lien3_3_6_2[0] . '"><a href="' . $lien3_3_6_2[1] . '">Psychologiques</a></li>
                                <li class="' . $lien3_3_6_3[0] . '"><a href="' . $lien3_3_6_3[1] . '">Administratives</a></li>
                            </ul>   
                        </li>
                    </ul>   
                </li>
                <li class="dropdown-submenu ' . $lien3_4[0] . '">
                    <a tabindex="-1" href="' . $lien3_4[1] . '">Menu Démarches</a>
                    <ul class="dropdown-menu">
                        <li class="' . $lien3_4_1[0] . '"><a href="' . $lien3_4_1[1] . '">Type de démarche</a></li>
                        <li class="' . $lien3_4_2[0] . '"><a href="' . $lien3_4_2[1] . '">Type d\'intervention</a></li>
                        <li class="' . $lien3_4_3[0] . '"><a href="' . $lien3_4_3[1] . '">Suites</a></li>
                        <li class="' . $lien3_4_4[0] . '"><a href="' . $lien3_4_4[1] . '">Ordonnance de protection</a></li>
                        <li class="' . $lien3_4_5[0] . '"><a href="' . $lien3_4_5[1] . '">Suites de plainte</a></li>
                    </ul>   
                </li>
                <li class="dropdown-submenu ' . $lien3_5[0] . '">
                    <a tabindex="-1" href="' . $lien3_5[1] . '">Menu Psy</a>
                    <ul class="dropdown-menu">
                        <li class="' . $lien3_5_1[0] . '"><a href="' . $lien3_5_1[1] . '">Troubles physiologiques</a></li>
                        <li class="' . $lien3_5_2[0] . '"><a href="' . $lien3_5_2[1] . '">Troubles cognitifs</a></li>
                        <li class="' . $lien3_5_3[0] . '"><a href="' . $lien3_5_3[1] . '">Troubles émotionnels</a></li>
                    </ul>   
                </li>
                 <li class="' . $lien3_6[0] . '"><a href="' . $lien3_6[1] . '">Service</a></li>
                 <li class="' . $lien3_7[0] . '"><a href="' . $lien3_7[1] . '">Utilisateurs</a></li>
                 <li class="' . $lien3_8[0] . '"><a href="' . $lien3_8[1] . '">Ecrits</a></li>
                 <li class="dropdown-submenu ' . $lien3_9[0] . '">
                    <a tabindex="-1" href="' . $lien3_9[1] . '">Menu Ecoute</a>
                    <ul class="dropdown-menu">
                        <li class="' . $lien3_9_1[0] . '"><a href="' . $lien3_9_1[1] . '">Interlocuteur</a></li>
                        <li class="' . $lien3_9_2[0] . '"><a href="' . $lien3_9_2[1] . '">Appel</a></li>
                                    
                    </ul>   
                </li>
            </ul>
  </li>

</ul>';
        /* return '<ul class="sf-menu">' .
          '<li>' .
          $lien1 .
          '<ul>' .
          '<li>' . $lien1_1 . '</li>' .
          '<li>' . $lien1_5 . '<ul>' . '<li>' . $lien1_5_1 . '</li>' . '<li>' . $lien1_5_2 . '</li>' . '</ul>' .
          '</li>' .
          '<li>' . $lien1_4 . '</li>' .
          '<li>' . $lien1_3 . '</li>' .
          '<li>' . $lien1_2 . '</li>' .
          '</ul>' .
          '</li>' .
          '<li>' .
          $lien2 .
          '<ul>' .
          '<li>' . $lien2_1 . '</li>' .
          '<li>' . $lien2_2 . '</li>' .
          '</ul>' .
          '</li>' .
          '<li>' .
          $lien3 .
          '<ul>' .
          '<li>' .
          $lien3_1 .
          '<ul>' .
          '<li>' . $lien3_1_1 .
          '<ul>' .
          '<li>' . $lien3_1_1_1 . '</li>' .
          '<li>' . $lien3_1_1_2 . '</li>' .
          '<li>' . $lien3_1_1_3 . '</li>' .
          '<li>' . $lien3_1_1_4 . '</li>' .
          '</ul>' .
          '</li>' .
          '<li>' . $lien3_1_2 .
          '<ul>' .
          '<li>' . $lien3_1_2_1 . '</li>' .
          '<li>' . $lien3_1_2_2 . '</li>' .
          '<li>' . $lien3_1_2_3 . '</li>' .
          '</ul>' .
          '</li>' .
          '<li>' . $lien3_1_3 .
          '<ul>' .
          '<li>' . $lien3_1_3_1 . '</li>' .
          '<li>' . $lien3_1_3_2 . '</li>' .
          '</ul>' .
          '</li>' .
          '<li>' . $lien3_1_4 .
          '<ul>' .
          '<li>' . $lien3_1_4_1 . '</li>' .
          '<li>' . $lien3_1_4_2 . '</li>' .
          '</ul>' .
          '</li>' .
          '<li>' . $lien3_1_5 . '</li>' .
          '<li>' . $lien3_1_6 . '</li>' .
          '<li>' . $lien3_1_7 . '</li>' .
          '<li>' . $lien3_1_8 . '</li>' .
          '<li>' . $lien3_1_9 . '</li>' .
          '<li>' . $lien3_1_10 . '</li>' .
          '<li>' . $lien3_1_11 . '</li>' .
          '<li>' . $lien3_1_12 . '</li>' .
          '<li>' . $lien3_1_13 . '</li>' .
          '</ul>' .
          '<li>' .
          $lien3_2 .
          '<ul>' .
          '<li>' . $lien3_2_1 .
          '<li>' . $lien3_2_2 . '</li>' .
          '<li>' . $lien3_2_3 . '</li>' .
          '<li>' . $lien3_2_4 . '</li>' .
          '<li>' . $lien3_2_5 . '</li>' .
          '<li>' . $lien3_2_6 .
          '<ul>' .
          '<li>' . $lien3_2_6_1 . '</li>' .
          '<li>' . $lien3_2_6_2 . '</li>' .
          '</ul>' .
          '</li>' .
          '</li>' .
          '</ul>' .
          '</li>' .
          '<li>' .
          $lien3_3 .
          '<ul>' .
          '<li>' . $lien3_3_1 . '</li>' .
          '<li>' . $lien3_3_2 . '</li>' .
          '<li>' . $lien3_3_3 . '</li>' .
          '<li>' . $lien3_3_4 . '</li>' .
          '<li>' . $lien3_3_5 .
          '<ul>' .
          '<li>' . $lien3_3_5_1 . '</li>' .
          '<li>' . $lien3_3_5_2 . '</li>' .
          '<li>' . $lien3_3_5_3 . '</li>' .
          '<li>' . $lien3_3_5_4 . '</li>' .
          '<li>' . $lien3_3_5_5 . '</li>' .
          '<li>' . $lien3_3_5_6 . '</li>' .
          '<li>' . $lien3_3_5_7 . '</li>' .
          '<li>' . $lien3_3_5_8 . '</li>' .
          '<li>' . $lien3_3_5_9 . '</li>' .
          '<li>' . $lien3_3_5_10 . '</li>' .
          '<li>' . $lien3_3_5_11 . '</li>' .
          '</ul>' .
          '</li>' .
          '<li>' . $lien3_3_6 .
          '<ul>' .
          '<li>' . $lien3_3_6_1 . '</li>' .
          '<li>' . $lien3_3_6_2 . '</li>' .
          '<li>' . $lien3_3_6_3 . '</li>' .
          '</ul>' .
          '</li>' .
          '</ul>' .
          '<li>' . $lien3_4 .
          '<ul>' .
          '<li>' . $lien3_4_1 . '</li>' .
          '<li>' . $lien3_4_2 . '</li>' .
          '<li>' . $lien3_4_3 . '</li>' .
          '<li>' . $lien3_4_4 . '</li>' .
          '<li>' . $lien3_4_5 . '</li>' .
          '</ul>' .
          '</li>' .
          '<li>' . $lien3_5 .
          '<ul>' .
          '<li>' . $lien3_5_1 . '</li>' .
          '<li>' . $lien3_5_2 . '</li>' .
          '<li>' . $lien3_5_3 . '</li>' .
          '</ul>' .
          '</li>' .
          '</li>' .
          '</li>' .
          '<li>' . $lien3_6 . '</li>' .
          '<li>' . $lien3_7 . '</li>' .
          '</ul>' .
          '</li>' .
          '</ul>'; */
    }

}

?>
