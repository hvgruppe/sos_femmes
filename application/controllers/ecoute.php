<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

session_start();

class Ecoute extends CI_Controller {

    function __construct() {
        parent::__construct();

        $this->load->database();
        $this->load->helper('url');
        $this->load->library('grocery_CRUD');
        $this->load->library('gc_dependent_select');
        $this->check_isvalidated();
        $this->load->model('navigation');
    }

    function _example_output($output = null) {
        $this->load->view('template', $output);
    }

    public function index() {
        try {

// General
            $crud = new grocery_CRUD();
            $crud->set_language("french");
            $crud->unset_bootstrap();
            //$crud->set_theme('datatables');
            $crud->set_theme('twitter-bootstrap');
            $crud->set_table('sos_ecoute');
            $crud->order_by('premier_contact', 'desc');
            $crud->set_subject('Ecoute téléphonique');
//relations
            $crud->set_relation('situation_familiale', 'sos_gen_situation_familiale_parrent', 'name_situation_familiale_parrent');
            $crud->set_relation('emplois', 'sos_gen_emplois_parrent', 'name_emplois');
            $crud->set_relation('age', 'sos_gen_femme_age', 'name_femme_age');
            $crud->set_relation('duree_de_la_relation', 'sos_gen_duree_de_la_relation', 'name_duree_de_la_relation', null, 'id_duree_de_la_relation ASC');
            $crud->set_relation('ville', 'sos_gen_villes', '{nom_ville}, {code_postal}', null, 'nom_ville ASC');
            $crud->set_relation('nationalite', 'sos_gen_nationalite', 'name_nationalite');
            $crud->set_relation('ressources', 'sos_gen_ressources', 'name_ressources');
            $crud->set_relation('allocations_familiales', 'sos_gen_allocations_familiales', 'name_allocations_familiales');
            $crud->set_relation('logement', 'sos_gen_logement_parent', 'name_logement');
            $crud->set_relation('situation_actuelle_depuis', 'sos_gen_situation_actuelle_depuis', 'name_situation_actuelle_depuis', null, 'id_situation_actuelle_depuis ASC');
            $crud->set_relation('situation_actuelle', 'sos_gen_situation_actuelle', 'name_situation_actuelle');
            $crud->set_relation('situation_actuelle_detailles', 'sos_gen_situation_actuelle_detailles', 'name_situation_actuelle_detailles');
            $crud->set_relation('partenaire', 'sos_gen_partenaire', 'name_partenaire');
            $crud->set_relation('informations', 'sos_gen_informations', 'name_informations');
            $crud->set_relation('rdv', 'sos_gen_rdv', 'name_rdv');
            $crud->set_relation('hebergement', 'sos_gen_hebergement', 'name_hebergement');
            $crud->set_relation('depuis', 'sos_gen_depuis', 'name_depuis');
            $crud->set_relation('temps_ecoute', 'sos_gen_temps_ecoute', 'name_temps_ecoute', null, 'id_temps_ecoute ASC');
            $crud->set_relation('interlocuteur', 'sos_gen_interlocuteur', 'name_interlocuteur', null, 'id_interlocuteur ASC');
            $crud->set_relation('appel', 'sos_gen_appel', 'name_appel', null, 'id_appel ASC');

            $crud->set_relation('frequence', 'sos_gen_frequence', 'name_frequence');
            $crud->set_relation('commencement', 'sos_gen_commencement', 'name_commencement');
            $crud->set_relation_n_n('violences_physiques', 'sos_relation_violences_physiques_ecoute', 'sos_gen_violences_physiques', 'id_from_violences', 'id_from_violences_physiques', 'name_violences_physiques', 'priority');
            $crud->set_relation_n_n('violences_psychologiques', 'sos_relation_violences_psychologiques_ecoute', 'sos_gen_violences_psychologiques', 'id_from_violences', 'id_from_violences_psychologiques', 'name_violences_psychologiques', 'priority');
            $crud->set_relation_n_n('violences_sexuelles', 'sos_relation_violences_sexuelles_ecoute', 'sos_gen_violences_sexuelles', 'id_from_violences', 'id_from_violences_sexuelles', 'name_violences_sexuelles', 'priority');
            $crud->set_relation_n_n('violences_economiques', 'sos_relation_violences_economiques_ecoute', 'sos_gen_violences_economiques', 'id_from_violences', 'id_from_violences_economiques', 'name_violences_economiques', 'priority');
            $crud->set_relation_n_n('violences_administratives', 'sos_relation_violences_administratives_ecoute', 'sos_gen_violences_administratives', 'id_from_violences', 'id_from_violences_administratives', 'name_violences_administratives', 'priority');
            $crud->set_relation_n_n('violences_sociales', 'sos_relation_violences_sociales_ecoute', 'sos_gen_violences_sociales', 'id_from_violences', 'id_from_violences_sociales', 'name_violences_sociales', 'priority');
            $crud->set_relation_n_n('violences_privations', 'sos_relation_violences_privations_ecoute', 'sos_gen_violences_privations', 'id_from_violences', 'id_from_violences_privations', 'name_violences_privations', 'priority');
            $crud->set_relation_n_n('violences_juridiques', 'sos_relation_violences_juridiques_ecoute', 'sos_gen_violences_juridiques', 'id_from_violences', 'id_from_violences_juridiques', 'name_violences_juridiques', 'priority');
            $crud->set_relation_n_n('de_la_part', 'sos_relation_de_la_part_ecoute', 'sos_gen_de_la_part', 'id_from_violences', 'id_from_de_la_part', 'name_de_la_part');
            $crud->set_relation_n_n('raisons', 'sos_relation_raisons_ecoute', 'sos_gen_raisons', 'id_from_violences', 'id_from_raisons', 'name_raisons');
            $crud->set_relation_n_n('violences_enfants_directes', 'sos_relation_violences_enfants_directes_ecoute', 'sos_gen_violences_enfants_directes', 'id_from_violences', 'id_from_violences_enfants_directes', 'name_violences_enfants_directes', 'priority');
            $crud->set_relation_n_n('violences_enfants_indirectes', 'sos_relation_violences_enfants_indirectes_ecoute', 'sos_gen_violences_enfants_indirectes', 'id_from_violences', 'id_from_violences_enfants_indirectes', 'name_violences_enfants_indirectes', 'priority');
            $crud->set_relation_n_n('de_la_part_enfants', 'sos_relation_de_la_part_enfants_ecoute', 'sos_gen_de_la_part_enfants', 'id_from_violences', 'id_from_de_la_part_enfants', 'name_de_la_part_enfants');
            $crud->set_relation_n_n('consequences_physiques', 'sos_relation_consequences_physiques_ecoute', 'sos_gen_consequences_physiques', 'id_from_violences', 'id_from_consequences_physiques', 'name_consequences_physiques', 'priority');
            $crud->set_relation_n_n('consequences_psychologiques', 'sos_relation_consequences_psychologiques_ecoute', 'sos_gen_consequences_psychologiques', 'id_from_violences', 'id_from_consequences_psychologiques', 'name_consequences_psychologiques', 'priority');
            $crud->set_relation_n_n('consequences_administratives', 'sos_relation_consequences_administratives_ecoute', 'sos_gen_consequences_administratives', 'id_from_violences', 'id_from_consequences_administratives', 'name_consequences_administratives', 'priority');
//Master/child relations

            $fields_situation_actuelle = array(
                'situation_actuelle' => array(
                    'table_name' => 'sos_gen_situation_actuelle',
                    'title' => 'name_situation_actuelle',
                    'relate' => null
                ),
                'situation_actuelle_detailles' => array(
                    'table_name' => 'sos_gen_situation_actuelle_detailles',
                    'title' => 'name_situation_actuelle_detailles',
                    'id_field' => 'id_situation_actuelle_detailles',
                    'relate' => 'id_from_situation_actuelle',
                    'data-placeholder' => 'Préciser'
                )
            );
            $config_situation_actuelle = array(
                'main_table' => 'sos_ecoute',
                'main_table_primary' => 'id_femme',
                "url" => base_url() . 'index.php/home/index/',
                'ajax_loader' => base_url() . 'img/ajax-loader.gif',
                'segment_name' => 'situation_actuelle'
            );
            $categories_situation_actuelle = new gc_dependent_select($crud, $fields_situation_actuelle, $config_situation_actuelle);
            $js_situation_actuelle = $categories_situation_actuelle->get_js();


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
                'main_table' => 'sos_ecoute',
                'main_table_primary' => 'id_femme',
                "url" => base_url() . 'index.php/ecoute/index/',
                'ajax_loader' => base_url() . 'img/ajax-loader.gif',
                'segment_name' => 'first'
            );
            $categories_demarche = new gc_dependent_select($crud, $fields_demarche, $config_demarche);
            $js_demarche = $categories_demarche->get_js();




            $fields_demarche1 = array(
                'first1' => array(
                    'table_name' => 'sos_gen_demarche_first',
                    'title' => 'name_demarche_first',
                    'relate' => null
                ),
                'second1' => array(
                    'table_name' => 'sos_gen_demarche_second',
                    'title' => 'name_demarche_second',
                    'id_field' => 'id_demarche_second',
                    'relate' => 'id_from_demarche_first',
                    'data-placeholder' => 'Préciser'
                ),
                'third1' => array(
                    'table_name' => 'sos_gen_demarche_third',
                    'title' => 'name_demarche_third',
                    'id_field' => 'id_demarche_third',
                    'relate' => 'id_from_demarche_second',
                    'data-placeholder' => 'Préciser'
                )
            );
            // 'url' => base_url() . 'index.php/demarche/window_demarche/'. $this->id_femme.'/',
            $config_demarche1 = array(
                'main_table' => 'sos_ecoute',
                'main_table_primary' => 'id_femme',
                "url" => base_url() . 'index.php/ecoute/index/',
                'ajax_loader' => base_url() . 'img/ajax-loader.gif',
                'segment_name' => 'first1'
            );
            $categories_demarche1 = new gc_dependent_select($crud, $fields_demarche1, $config_demarche1);
            $js_demarche1 = $categories_demarche1->get_js();


            $fields_demarche2 = array(
                'first2' => array(
                    'table_name' => 'sos_gen_demarche_first',
                    'title' => 'name_demarche_first',
                    'relate' => null
                ),
                'second2' => array(
                    'table_name' => 'sos_gen_demarche_second',
                    'title' => 'name_demarche_second',
                    'id_field' => 'id_demarche_second',
                    'relate' => 'id_from_demarche_first',
                    'data-placeholder' => 'Préciser'
                ),
                'third2' => array(
                    'table_name' => 'sos_gen_demarche_third',
                    'title' => 'name_demarche_third',
                    'id_field' => 'id_demarche_third',
                    'relate' => 'id_from_demarche_second',
                    'data-placeholder' => 'Préciser'
                )
            );
            // 'url' => base_url() . 'index.php/demarche/window_demarche/'. $this->id_femme.'/',
            $config_demarche2 = array(
                'main_table' => 'sos_ecoute',
                'main_table_primary' => 'id_femme',
                "url" => base_url() . 'index.php/ecoute/index/',
                'ajax_loader' => base_url() . 'img/ajax-loader.gif',
                'segment_name' => 'first2'
            );
            $categories_demarche2 = new gc_dependent_select($crud, $fields_demarche2, $config_demarche2);
            $js_demarche2 = $categories_demarche2->get_js();

            $crud->set_relation('first', 'sos_gen_demarche_first', 'name_demarche_first');
            $crud->set_relation('second', 'sos_gen_demarche_second', 'name_demarche_second');
            $crud->set_relation('third', 'sos_gen_demarche_third', 'name_demarche_third');
            $crud->set_relation('first1', 'sos_gen_demarche_first', 'name_demarche_first');
            $crud->set_relation('second1', 'sos_gen_demarche_second', 'name_demarche_second');
            $crud->set_relation('third1', 'sos_gen_demarche_third', 'name_demarche_third');
            $crud->set_relation('first2', 'sos_gen_demarche_first', 'name_demarche_first');
            $crud->set_relation('second2', 'sos_gen_demarche_second', 'name_demarche_second');
            $crud->set_relation('third2', 'sos_gen_demarche_third', 'name_demarche_third');

//Visual

            $crud->display_as('nationalite', 'Nationalité')
                    ->display_as('premier_contact', 'Date')
                    ->display_as('duree_de_la_relation', 'Durée de la relation')
                    ->display_as('emplois_detailles', 'Détails de l\'emploi')
                    ->display_as('emplois_more_detailles', 'Plus de détails de l\'emploi')
                    ->display_as('ouvertures', 'Dernière modification')
                    ->display_as('hebergement', 'Demande d\'hébergement')
                    ->display_as('aide_materielle', 'Demande d\'aide matérielle')
                    ->display_as('accueil', 'Demande d\'accueil')
                    ->display_as('informations', 'Demande d\'informations')
                    ->display_as('conseil', 'Demande de conseil')
                    ->display_as('orientation', 'Demande d\'orientation')
                    ->display_as('rdv', 'Demande de rdv')
                    ->display_as('hebergement', 'Demande d\'hébergement')
                    ->display_as('situation_actuelle_detailles', 'Situation actuelle détaillée')
                    ->display_as('emplois', 'Situation professionnelle')
                    ->display_as('commentaire', 'Commentaires')
                    ->display_as('partenaire', 'Orienteur')
                    ->display_as('rep_accueil', 'Réponse')
                    ->display_as('rep_informations', 'Réponse')
                    ->display_as('rep_conseil', 'Réponse')
                    ->display_as('rep_orientation', 'Réponse')
                    ->display_as('rep_rdv', 'Réponse')
                    ->display_as('rep_hebergement', 'Réponse')
                    ->display_as('rep_soutien', 'Réponse')
                    ->display_as('rep_aide_materielle', 'Réponse')
                    ->display_as('rep_ecoute', 'Réponse')
                    ->display_as('consequences', 'Conséquences')
                    ->display_as('consequences_administratives', 'Conséquences administratives et économiques')
                    ->display_as('violences_economiques', 'Violences économiques')
                    ->display_as('consequences_physiques', 'Conséquences physiques')
                    ->display_as('consequences_psychologiques', 'Conséquences psychologiques')
                    ->display_as('consequences_administratives', 'Conséquences administratives')
                    ->display_as('de_la_part_enfants', 'Violences sur enfants par')
                    ->display_as('temps_ecoute', "Temps d'écoute")
                    ->display_as('age', 'Age de la femme')
                    ->display_as('logement_dem', 'Demande de logement')
                    ->display_as('rep_logement_dem', 'Réponse')
                    ->display_as('violences_enfants_directes', 'ENFANTS - Violences directes')
                    ->display_as('violences_enfants_indirectes', 'ENFANTS - Violences indirectes')
                    ->display_as('frequence', 'Fréquence')
                    ->display_as('demandes', 'Demandes/Réponses');
            $crud->display_as('first', 'Type de démarche 1');
            $crud->display_as('second', 'Type d\'intervention 1');
            $crud->display_as('third', 'Suites 1');
            $crud->display_as('first1', 'Type de démarche 2');
            $crud->display_as('second1', 'Type d\'intervention 2');
            $crud->display_as('third1', 'Suites 2');
            $crud->display_as('first2', 'Type de démarche 3');
            $crud->display_as('second2', 'Type d\'intervention 3');
            $crud->display_as('third2', 'Suites 3');
            $crud->display_as('demarche', 'Démarches');


//unsets





            if ($this->session->userdata('status') == '1' OR $this->session->userdata('status') == '2' OR $this->session->userdata('status') == '3') {
                $crud->fields('premier_contact', 'interlocuteur', 'appel', 'partenaire', 'temps_ecoute', 'age', 'enfants', 'enceinte', 'ville', 'nationalite', 'situation_familiale', 'depuis', 'duree_de_la_relation', 'emplois', 'logement', 'situation_actuelle', 'situation_actuelle_detailles', 'situation_actuelle_depuis', 'ressources', 'allocations_familiales', 'dettes', 'soutien', 'rep_soutien', 'ecoute', 'rep_ecoute', 'accueil', 'rep_accueil', 'informations', 'rep_informations', 'conseil', 'rep_conseil', 'orientation', 'rep_orientation', 'rdv', 'rep_rdv', 'hebergement', 'rep_hebergement', 'logement_dem', 'rep_logement_dem', 'aide_materielle', 'rep_aide_materielle', 'violences_physiques', 'violences_psychologiques', 'violences_sexuelles', 'violences_economiques', 'violences_administratives', 'violences_sociales', 'violences_privations', 'violences_juridiques', 'de_la_part', 'raisons', 'violences_enfants_directes', 'violences_enfants_indirectes', 'de_la_part_enfants', 'consequences_physiques', 'consequences_psychologiques', 'consequences_administratives', 'frequence', 'commencement', 'first', 'second', 'third', 'first1', 'second1', 'third1', 'first2', 'second2', 'third2', 'commentaire');
            } else {
                $crud->fields('interlocuteur', 'appel', 'partenaire', 'temps_ecoute', 'age', 'enfants', 'enceinte', 'ville', 'nationalite', 'situation_familiale', 'depuis', 'duree_de_la_relation', 'emplois', 'logement', 'situation_actuelle', 'situation_actuelle_detailles', 'situation_actuelle_depuis', 'ressources', 'allocations_familiales', 'dettes', 'soutien', 'rep_soutien', 'ecoute', 'rep_ecoute', 'accueil', 'rep_accueil', 'informations', 'rep_informations', 'conseil', 'rep_conseil', 'orientation', 'rep_orientation', 'rdv', 'rep_rdv', 'hebergement', 'rep_hebergement', 'logement_dem', 'rep_logement_dem', 'aide_materielle', 'rep_aide_materielle', 'violences_physiques', 'violences_psychologiques', 'violences_sexuelles', 'violences_economiques', 'violences_administratives', 'violences_sociales', 'violences_privations', 'violences_juridiques', 'de_la_part', 'raisons', 'violences_enfants_directes', 'violences_enfants_indirectes', 'de_la_part_enfants', 'consequences_physiques', 'consequences_psychologiques', 'consequences_administratives', 'frequence', 'commencement', 'first', 'second', 'third', 'first1', 'second1', 'third1', 'first2', 'second2', 'third2', 'commentaire');
            }
            $crud->unset_add_fields('par');
            if ($this->session->userdata('status') == '0' OR $this->session->userdata('status') == '4') {
                $crud->unset_delete();
                $crud->unset_export();
                $crud->unset_print();
            }

//Requireds
            $crud->required_fields('premier_contact', 'appel', 'temps_ecoute', 'interlocuteur');
            $crud->columns('premier_contact', 'infos', 'situation', 'demandes', 'violences', 'de_la_part', 'commencement', 'consequences', 'demarche');

//Callbacks
            $crud->callback_column('ouvertures', array($this, 'ouvertures'));
            $crud->callback_column('premier_contact', array($this, 'premier_contact'));
            $crud->callback_column('infos', array($this, 'infos'));
            $crud->callback_column('situation', array($this, 'situation'));
            $crud->callback_column('demandes', array($this, 'demandes'));
            $crud->callback_column('commentaire', array($this, 'commentaire'));
            $crud->callback_column('demarche', array($this, 'demarche'));
            $crud->callback_after_update(array($this, 'insert_after_update'));
            $crud->callback_after_insert(array($this, 'insert_after_insert'));
            $crud->callback_before_update(array($this, 'before_update'));
            $crud->callback_column('de_la_part', array($this, 'de_la_part'));
            $crud->callback_column('raisons', array($this, 'raisons'));
            $crud->callback_column('violences', array($this, 'violences'));
            $crud->callback_column('consequences', array($this, 'consequences'));
//field Types
            //$crud->callback_edit_field('accueil', array($this, 'edit_field_callback_1'));
            //$crud->callback_add_field('accueil', array($this, 'add_field_callback_1'));
//field Types

            $crud->field_type('dettes', 'enum', array('OUI', 'NON'));
            $crud->field_type('enceinte', 'enum', array('OUI', 'NON'));
            // $crud->field_type('appel', 'dropdown', array('0' => '', '1' => '1er appel', '2' => 'Appel suivi', '3' => 'Autres appels'));
            $crud->field_type('enfants', 'enum', array('Aucun', '1 enfant', '2 enfants', '3 enfants', '4 enfants', '5 et +'));
            // $crud->field_type('interlocuteur', 'dropdown', array('0' => '', '1' => 'Elle-même', '2' => 'Professionnel', '3' => 'Entourage'));
// Actions
// Renders
            $output = $crud->render();
            $output->output.= $js_situation_actuelle . $js_demarche . $js_demarche1 . $js_demarche2;
            $menu = new stdClass;
            $menu->status = $this->session->userdata('status');
            if ($menu->status != 5) {
                $menu->n1 = true;
            } else {
                $menu->n0 = true;
            }
            $header = $this->navigation->home_f($menu);
            $data = array('output' => $output, 'header' => $header);
            $this->_example_output($data);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
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


        $row_first_str1 = "";
        if ($row->first1 > 0) {
            $this->db->where('id_demarche_first', $row->first1);
            $query_first = $this->db->get('sos_gen_demarche_first');
            $row_first = $query_first->row();
            $row_first_str1 = $row_first->name_demarche_first;
        }

        $row_second_str1 = "";
        if ($row->second1 > 0) {
            $this->db->where('id_demarche_second', $row->second1);
            $query_second = $this->db->get('sos_gen_demarche_second');
            $row_second = $query_second->row();
            $row_second_str1 = $row_second->name_demarche_second;
        }
        $row_third_str1 = "";
        if ($row->third1 > 0) {
            $this->db->where('id_demarche_third', $row->third1);
            $query_third = $this->db->get('sos_gen_demarche_third');
            $row_third = $query_third->row();
            $row_third_str1 = $row_third->name_demarche_third;
        }

        $row_first_str2 = "";
        if ($row->first2 > 0) {
            $this->db->where('id_demarche_first', $row->first2);
            $query_first = $this->db->get('sos_gen_demarche_first');
            $row_first = $query_first->row();
            $row_first_str2 = $row_first->name_demarche_first;
        }

        $row_second_str2 = "";
        if ($row->second2 > 0) {
            $this->db->where('id_demarche_second', $row->second2);
            $query_second = $this->db->get('sos_gen_demarche_second');
            $row_second = $query_second->row();
            $row_second_str2 = $row_second->name_demarche_second;
        }
        $row_third_str2 = "";
        if ($row->third2 > 0) {
            $this->db->where('id_demarche_third', $row->third2);
            $query_third = $this->db->get('sos_gen_demarche_third');
            $row_third = $query_third->row();
            $row_third_str2 = $row_third->name_demarche_third;
        }



        return '<b>Démarche 1</b><br>' . $row_first_str . '->' . $row_second_str . '->' . $row_third_str . '<br><b>Démarche 2</b><br>' . $row_first_str1 . '->' . $row_second_str1 . '->' . $row_third_str1 . '<br><b>Démarche 3</b><br>' . $row_first_str2 . '->' . $row_second_str2 . '->' . $row_third_str2;
    }

    function commentaire($value, $row) {
        return $row->commentaire;
    }

    function situation($value, $row) {
        $this->db->select('sos_gen_situation_familiale_parrent.name_situation_familiale_parrent,
sos_gen_depuis.name_depuis,
sos_gen_situation_actuelle_depuis.name_situation_actuelle_depuis,
sos_gen_duree_de_la_relation.name_duree_de_la_relation,
sos_gen_emplois_parrent.name_emplois,
sos_gen_logement_parent.name_logement,
sos_gen_situation_actuelle.name_situation_actuelle,
sos_gen_situation_actuelle_detailles.name_situation_actuelle_detailles,
sos_gen_ressources.name_ressources,
sos_gen_allocations_familiales.name_allocations_familiales');
        $this->db->join('sos_gen_situation_familiale_parrent', 'sos_gen_situation_familiale_parrent.id_situation_familiale_parrent = sos_ecoute.situation_familiale', 'left');
        $this->db->join('sos_gen_depuis', 'sos_gen_depuis.id_depuis = sos_ecoute.depuis', 'left');
        $this->db->join('sos_gen_situation_actuelle_depuis', 'sos_gen_situation_actuelle_depuis.id_situation_actuelle_depuis = sos_ecoute.situation_actuelle_depuis', 'left');
        $this->db->join('sos_gen_duree_de_la_relation', 'sos_gen_duree_de_la_relation.id_duree_de_la_relation = sos_ecoute.duree_de_la_relation', 'left');
        $this->db->join('sos_gen_emplois_parrent', 'sos_gen_emplois_parrent.id_emplois = sos_ecoute.emplois', 'left');
        $this->db->join('sos_gen_logement_parent', 'sos_gen_logement_parent.id_logement_parent = sos_ecoute.logement', 'left');
        $this->db->join('sos_gen_situation_actuelle', 'sos_gen_situation_actuelle.id_situation_actuelle = sos_ecoute.situation_actuelle', 'left');
        $this->db->join('sos_gen_situation_actuelle_detailles', 'sos_gen_situation_actuelle_detailles.id_situation_actuelle_detailles = sos_ecoute.situation_actuelle_detailles', 'left');
        $this->db->join('sos_gen_ressources', 'sos_gen_ressources.id_ressources = sos_ecoute.ressources', 'left');
        $this->db->join('sos_gen_allocations_familiales', 'sos_gen_allocations_familiales.id_allocations_familiales = sos_ecoute.allocations_familiales', 'left');

        $this->db->where('sos_ecoute.id_femme', $row->id_femme);


        $query = $this->db->get('sos_ecoute')->row();
        $dettes = "";
        switch ($row->dettes) {
            case 'OUI':
                $dettes .= 'OUI';
                break;
            case 'NON':
                $dettes .= 'NON';
                break;
        }
        $html = 'Situation actuelle :<b>' . $query->name_situation_actuelle . '</b><br>' .
                'Situation actuelle détaillée :<b>' . $query->name_situation_actuelle_detailles . '</b><br>' .
                'Situation actuelle depuis :<b>' . $query->name_situation_actuelle_depuis . '</b><br>' .
                'Situation familiale :<b>' . $query->name_situation_familiale_parrent . '</b><br>' .
                'Depuis  :<b>' . $query->name_depuis . '</b><br>' .
                'Durée de la relation :<b>' . $query->name_duree_de_la_relation . '</b><br>' .
                'Situation professionnelle :<b>' . $query->name_emplois . '</b><br>' .
                'Logement :<b>' . $query->name_logement . '</b><br>' .
                'Ressources :<b>' . $query->name_ressources . '</b><br>' .
                'Allocations familiales :<b>' . $query->name_allocations_familiales . '</b><br>' .
                'Dettes :<b>' . $dettes . '</b>';
        return $html;
    }

    function infos($value, $row) {
        $this->db->select('name_femme_age');
        $this->db->where('id_femme_age', $row->age);
        $query = $this->db->get('sos_gen_femme_age')->row();
        $age = "<b>Age : </b>";
        if ($query != null) {
            $age .= $query->name_femme_age;
        };
        $enfants = "<b>Enfants : </b>" . $row->enfants;


        $enceinte = "<b>Enceinte : </b>";
        switch ($row->enceinte) {
            case 'OUI':
                $enceinte .= 'OUI';
                break;
            case 'NON':
                $enceinte .= 'NON';
                break;
        }


        $this->db->select('sos_gen_villes.nom_ville,sos_gen_villes.code_postal,
          sos_gen_nationalite.name_nationalite');

        $this->db->join('sos_gen_villes', 'sos_gen_villes.id_ville = sos_ecoute.ville', 'left');
        $this->db->join('sos_gen_nationalite', 'sos_gen_nationalite.id_nationalite = sos_ecoute.nationalite', 'left');

        $this->db->where('sos_ecoute.id_femme', $row->id_femme);
        $query = $this->db->get('sos_ecoute')->row();


        $html = '<b>Ville : </b>' . $query->nom_ville . ' ' . $query->code_postal . '<br>';

        $html .= '<b>Nationalité : </b>' . $query->name_nationalite;
        return $age . '<br>' . $enfants . '<br>' . $enceinte . '<br>' . $html;
    }

    function demandes($value, $row) {
        $line1 = '';
        if ($row->accueil || $row->rep_accueil) {
            $dem = '&#10060;';
            if ($row->accueil) {
                $dem = '&#10062;';
            }
            $rep = '&#10060;';
            if ($row->rep_accueil) {
                $rep = '&#10062;';
            }

            $line1 = '<TR><TH> Accueil </TH><TD> ' . $dem . ' ' . $rep . ' </TD></TR> ';
        }

        $line2 = '';
        $this->db->select('name_informations');
        $this->db->where('id_informations', $row->informations);
        $query = $this->db->get('sos_gen_informations');
        if ($query->num_rows() > 0) {
            $query = $query->row();

            if ($row->rep_informations) {
                $line2 = '<TR> <TH> Informations ' . $query->name_informations . ' </TH> <TD> &#10062; &#10062; </TD> </TR> ';
            } else {
                $line2 = '<TR> <TH> Informations ' . $query->name_informations . ' </TH> <TD> &#10062; &#10060; </TD> </TR> ';
            }
        } else {
            if ($row->rep_informations) {
                $line2 = '<TR> <TH> Informations  </TH> <TD> &#10060; &#10062; </TD> </TR> ';
            }
        }



        $line3 = '';
        if ($row->conseil || $row->rep_conseil) {
            $dem = '&#10060;';
            if ($row->conseil) {
                $dem = '&#10062;';
            }
            $rep = '&#10060;';
            if ($row->rep_conseil) {
                $rep = '&#10062;';
            }

            $line3 = '<TR><TH> Conseil </TH><TD> ' . $dem . ' ' . $rep . ' </TD></TR> ';
        }


        $line4 = '';
        if ($row->orientation || $row->rep_orientation) {
            $dem = '&#10060;';
            if ($row->orientation) {
                $dem = '&#10062;';
            }
            $rep = '&#10060;';
            if ($row->rep_orientation) {
                $rep = '&#10062;';
            }

            $line4 = '<TR><TH> Orientation </TH><TD> ' . $dem . ' ' . $rep . ' </TD></TR> ';
        }

        $line5 = '';
        $this->db->select('name_rdv');
        $this->db->where('id_rdv', $row->rdv);
        $query = $this->db->get('sos_gen_rdv');
        if ($query->num_rows() > 0) {
            $query = $query->row();
            if ($row->rep_rdv) {
                $line5 = '<TR> <TH> RDV ' . $query->name_rdv . ' </TH> <TD> &#10062; &#10062; </TD> </TR> ';
            } else {
                $line5 = '<TR> <TH> RDV ' . $query->name_rdv . ' </TH> <TD> &#10062; &#10060; </TD> </TR> ';
            }
        } else {
            if ($row->rep_rdv) {
                $line5 = '<TR> <TH> RDV  </TH> <TD> &#10060; &#10062; </TD> </TR> ';
            }
        }

        $line6 = '';
        $this->db->select('name_hebergement');
        $this->db->where('id_hebergement', $row->hebergement);
        $query = $this->db->get('sos_gen_hebergement');
        if ($query->num_rows() > 0) {
            $query = $query->row();
            if ($row->rep_hebergement) {
                $line6 = '<TR> <TH> Hébergement ' . $query->name_hebergement . ' </TH> <TD> &#10062; &#10062; </TD> </TR> ';
            } else {
                $line6 = '<TR> <TH> Hébergement ' . $query->name_hebergement . ' </TH> <TD> &#10062; &#10060; </TD> </TR> ';
            }
        } else {
            if ($row->rep_hebergement) {
                $line6 = '<TR> <TH> Hébergement  </TH> <TD> &#10060; &#10062; </TD> </TR> ';
            }
        }



        $line7 = '';
        if ($row->soutien || $row->rep_soutien) {
            $dem = '&#10060;';
            if ($row->soutien) {
                $dem = '&#10062;';
            }
            $rep = '&#10060;';
            if ($row->rep_soutien) {
                $rep = '&#10062;';
            }

            $line7 = '<TR><TH> Soutien </TH><TD> ' . $dem . ' ' . $rep . ' </TD></TR> ';
        }

        $line8 = '';
        if ($row->aide_materielle || $row->rep_aide_materielle) {
            $dem = '&#10060;';
            if ($row->aide_materielle) {
                $dem = '&#10062;';
            }
            $rep = '&#10060;';
            if ($row->rep_aide_materielle) {
                $rep = '&#10062;';
            }

            $line8 = '<TR><TH> Aide matérielle </TH><TD> ' . $dem . ' ' . $rep . ' </TD></TR> ';
        }

        $line9 = '';
        if ($row->ecoute || $row->rep_ecoute) {
            $dem = '&#10060;';
            if ($row->ecoute) {
                $dem = '&#10062;';
            }
            $rep = '&#10060;';
            if ($row->rep_ecoute) {
                $rep = '&#10062;';
            }

            $line9 = '<TR><TH> Ecoute </TH><TD> ' . $dem . ' ' . $rep . ' </TD></TR> ';
        }

        $html = '';
        $html_ret = $line1 . $line2 . $line3 . $line4 . $line5 . $line6 . $line7 . $line8 . $line9;
        if (strlen($html_ret)) {
            $html = '<TABLE> ' . $html_ret . '</TABLE> ';
        }
        return $html;
    }

    function premier_contact($value, $row) {

        $html = '<h6>';
        $this->db->select('name_appel');
        $this->db->where('id_appel', $row->appel);
        $query = $this->db->get('sos_gen_appel')->row();

        if ($query != null) {
            $html .= '<h6>' . $query->name_appel . '</h6>' . '<br>';
        }
        $html.='</h6><br>';
        $this->db->select('name_interlocuteur');
        $this->db->where('id_interlocuteur', $row->interlocuteur);
        $query = $this->db->get('sos_gen_interlocuteur')->row();

        if ($query != null) {
            $html .= '<h6>' . $query->name_interlocuteur . '</h6>' . '<br>';
        }


        $this->db->select('name_temps_ecoute');
        $this->db->where('id_temps_ecoute', $row->temps_ecoute);
        $query = $this->db->get('sos_gen_temps_ecoute')->row();
        $temps_ecoute = "<b>Temps d'écoute : </b>";
        if ($query != null) {
            $temps_ecoute .= $query->name_temps_ecoute;
        }

        $html.=$temps_ecoute . '<br>';
        $ret = strtok($row->premier_contact, " ");

        $ouvertures = $this->db->order_by('ouverture_time', 'asc')->get_where('sos_ouverture_ecoute', array('id_femme' => $row->id_femme), 1)->result_array();
        if ($ouvertures) {
            foreach ($ouvertures as $items) {
                $this->db->where('id_utilisateur', $items['id_utilisateur']);
                $query = $this->db->get('sos_utilisateur');
                if ($query->num_rows == 1) {   // echo var_dump($user).'<br />';
                    $row_user = $query->row();

                    $html = $ret . ' par<br>' . $row_user->prenom_utilisateur . ' ' . $row_user->nom_utilisateur . $html;
                }
            }
        }
        //$html.='</ul>';
        return $html;
    }

    function ouvertures($value, $row) {
        // $html = '<ul>';
        $html = '';
        $ouvertures = $this->db->order_by('ouverture_time', 'desc')->get_where('sos_ouverture_ecoute', array('id_femme' => $row->id_femme), 1)->result_array();
        if ($ouvertures) {
            foreach ($ouvertures as $items) {
                $this->db->where('id_utilisateur', $items['id_utilisateur']);
                $query = $this->db->get('sos_utilisateur');
                if ($query->num_rows == 1) {   // echo var_dump($user).'<br />';
                    $row_user = $query->row();
                    $html.= strtok($items['ouverture_time'], " ") . ' par<br>' . $row_user->prenom_utilisateur . ' ' . $row_user->nom_utilisateur;
                }
            }
        }
        //$html.='</ul>';
        return $html;
    }

    function de_la_part($value, $row) {
        $html = '';
        $row_query_de_la_part = $this->db->query('SELECT c.name_de_la_part
          FROM sos_ecoute AS a
          JOIN sos_relation_de_la_part_ecoute AS b ON a.id_femme=b.id_from_violences
          JOIN sos_gen_de_la_part AS c ON b.id_from_de_la_part=c.id_de_la_part
          WHERE a.id_femme = ' . $row->id_femme)->result_array();
        if (count($row_query_de_la_part) > 0) {
            $html = '<ul><b>Femme</b><ul>';
            foreach ($row_query_de_la_part as $items) {
                $html.='<li>' . $items['name_de_la_part'] . '</li>';
            }
            $html.='</ul></ul>';
        }

        $row_query_de_la_part_enfants = $this->db->query('SELECT c.name_de_la_part_enfants
          FROM sos_ecoute AS a
          JOIN sos_relation_de_la_part_enfants_ecoute AS b ON a.id_femme=b.id_from_violences
          JOIN sos_gen_de_la_part_enfants AS c ON b.id_from_de_la_part_enfants=c.id_de_la_part_enfants
          WHERE a.id_femme = ' . $row->id_femme)->result_array();
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
        $html = '';
        $row_query_raisons = $this->db->query('SELECT c.name_raisons
          FROM sos_ecoute AS a
          JOIN sos_relation_raisons_ecoute AS b ON a.id_femme=b.id_from_violences
          JOIN sos_gen_raisons AS c ON b.id_from_raisons=c.id_raisons
          WHERE a.id_femme = ' . $row->id_femme)->result_array();
        foreach ($row_query_raisons as $items) {
            $html.= $items['name_raisons'] . '<br>';
        }
        $html.='';
        return $html;
    }

    function consequences_physiques($value, $row) {
        $html = '<ul>';
        $row_query_consequences_physiques = $this->db->query('SELECT c.name_consequences_physiques
          FROM sos_ecoute AS a
          JOIN sos_relation_consequences_physiques_ecoute AS b ON a.id_femme=b.id_from_violences
          JOIN sos_gen_consequences_physiques AS c ON b.id_from_consequences_physiques=c.id_consequences_physiques
          WHERE a.id_femme = ' . $row->id_femme)->result_array();
        foreach ($row_query_consequences_physiques as $items) {
            $html.='<li>' . $items['name_consequences_physiques'] . '</li>';
        }
        $html.='</ul>';
        return $html;
    }

    function consequences_psychologiques($value, $row) {
        $html = '<ul>';

        $row_query_consequences_psychologiques = $this->db->query('SELECT c.name_consequences_psychologiques
          FROM sos_ecoute AS a
          JOIN sos_relation_consequences_psychologiques_ecoute AS b ON a.id_femme=b.id_from_violences
          JOIN sos_gen_consequences_psychologiques AS c ON b.id_from_consequences_psychologiques=c.id_consequences_psychologiques
          WHERE a.id_femme = ' . $row->id_femme)->result_array();
        foreach ($row_query_consequences_psychologiques as $items) {
            $html.='<li>' . $items['name_consequences_psychologiques'] . '</li>';
        }

        $html.='</ul>';
        return $html;
    }

    function violences($value, $row) {

        $html = '';
        $row_query_violences_physiques = $this->db->query('SELECT c.name_violences_physiques
          FROM sos_ecoute AS a
          JOIN sos_relation_violences_physiques_ecoute AS b ON a.id_femme=b.id_from_violences
          JOIN sos_gen_violences_physiques AS c ON b.id_from_violences_physiques=c.id_violences_physiques
          WHERE a.id_femme = ' . $row->id_femme . ' ORDER BY b.priority ASC')->result_array();
        if (count($row_query_violences_physiques) > 0) {
            $html.= '<ul><li><b>Physiques</b><ul>';
            foreach ($row_query_violences_physiques as $items) {
                $html.='<li>' . $items['name_violences_physiques'] . '</li>';
            }
            $html.='</ul></li></ul>';
        }

        $row_query_violences_psychologiques = $this->db->query('SELECT c.name_violences_psychologiques
          FROM sos_ecoute AS a
          JOIN sos_relation_violences_psychologiques_ecoute AS b ON a.id_femme=b.id_from_violences
          JOIN sos_gen_violences_psychologiques AS c ON b.id_from_violences_psychologiques=c.id_violences_psychologiques
          WHERE a.id_femme = ' . $row->id_femme . ' ORDER BY b.priority ASC')->result_array();
        if (count($row_query_violences_psychologiques) > 0) {
            $html.= '<ul><li><b>Psychologiques</b><ul>';
            foreach ($row_query_violences_psychologiques as $items) {
                $html.='<li>' . $items['name_violences_psychologiques'] . '</li>';
            }
            $html.='</ul></li></ul>';
        }


        $row_query_violences_sexuelles = $this->db->query('SELECT c.name_violences_sexuelles
          FROM sos_ecoute AS a
          JOIN sos_relation_violences_sexuelles_ecoute AS b ON a.id_femme=b.id_from_violences
          JOIN sos_gen_violences_sexuelles AS c ON b.id_from_violences_sexuelles=c.id_violences_sexuelles
          WHERE a.id_femme = ' . $row->id_femme . ' ORDER BY b.priority ASC')->result_array();
        if (count($row_query_violences_sexuelles) > 0) {
            $html.= '<ul><li><b>Sexuelles</b><ul>';
            foreach ($row_query_violences_sexuelles as $items) {
                $html.='<li>' . $items['name_violences_sexuelles'] . '</li>';
            }
            $html.='</ul></li></ul>';
        }


        $row_query_violences_economiques = $this->db->query('SELECT c.name_violences_economiques
          FROM sos_ecoute AS a
          JOIN sos_relation_violences_economiques_ecoute AS b ON a.id_femme=b.id_from_violences
          JOIN sos_gen_violences_economiques AS c ON b.id_from_violences_economiques=c.id_violences_economiques
          WHERE a.id_femme = ' . $row->id_femme . ' ORDER BY b.priority ASC')->result_array();
        if (count($row_query_violences_economiques) > 0) {
            $html.= '<ul><li><b>Economiques</b><ul>';
            foreach ($row_query_violences_economiques as $items) {
                $html.='<li>' . $items['name_violences_economiques'] . '</li>';
            }
            $html.='</ul></li></ul>';
        }

        $row_query_violences_administratives = $this->db->query('SELECT c.name_violences_administratives
          FROM sos_ecoute AS a
          JOIN sos_relation_violences_administratives_ecoute AS b ON a.id_femme=b.id_from_violences
          JOIN sos_gen_violences_administratives AS c ON b.id_from_violences_administratives=c.id_violences_administratives
          WHERE a.id_femme = ' . $row->id_femme . ' ORDER BY b.priority ASC')->result_array();
        if (count($row_query_violences_administratives) > 0) {
            $html.= '<ul><li><b>Administratives</b><ul>';
            foreach ($row_query_violences_administratives as $items) {
                $html.='<li>' . $items['name_violences_administratives'] . '</li>';
            }
            $html.='</ul></li></ul>';
        }

        $row_query_violences_sociales = $this->db->query('SELECT c.name_violences_sociales
          FROM sos_ecoute AS a
          JOIN sos_relation_violences_sociales_ecoute AS b ON a.id_femme=b.id_from_violences
          JOIN sos_gen_violences_sociales AS c ON b.id_from_violences_sociales=c.id_violences_sociales
          WHERE a.id_femme = ' . $row->id_femme . ' ORDER BY b.priority ASC')->result_array();
        if (count($row_query_violences_sociales) > 0) {
            $html.= '<ul><li><b>Sociales</b><ul>';
            foreach ($row_query_violences_sociales as $items) {
                $html.='<li>' . $items['name_violences_sociales'] . '</li>';
            }
            $html.='</ul></li></ul>';
        }

        $row_query_violences_privations = $this->db->query('SELECT c.name_violences_privations
          FROM sos_ecoute AS a
          JOIN sos_relation_violences_privations_ecoute AS b ON a.id_femme=b.id_from_violences
          JOIN sos_gen_violences_privations AS c ON b.id_from_violences_privations=c.id_violences_privations
          WHERE a.id_femme = ' . $row->id_femme . ' ORDER BY b.priority ASC')->result_array();
        if (count($row_query_violences_privations) > 0) {
            $html.= '<ul><li><b>Privations</b><ul>';
            foreach ($row_query_violences_privations as $items) {

                $html.='<li>' . $items['name_violences_privations'] . '</li>';
            }
            $html.='</ul></li></ul>';
        }

        $row_query_violences_juridiques = $this->db->query('SELECT c.name_violences_juridiques
          FROM sos_ecoute AS a
          JOIN sos_relation_violences_juridiques_ecoute AS b ON a.id_femme=b.id_from_violences
          JOIN sos_gen_violences_juridiques AS c ON b.id_from_violences_juridiques=c.id_violences_juridiques
          WHERE a.id_femme = ' . $row->id_femme . ' ORDER BY b.priority ASC')->result_array();
        if (count($row_query_violences_juridiques) > 0) {
            $html.= '<ul><li><b>Juridiques</b><ul>';
            foreach ($row_query_violences_juridiques as $items) {

                $html.='<li>' . $items['name_violences_juridiques'] . '</li>';
            }
            $html.='</ul></li></ul>';
        }

        $pour_les_enfants = '';
        $row_query_violences_enfants_directes = $this->db->query('SELECT c.name_violences_enfants_directes
          FROM sos_ecoute AS a
          JOIN sos_relation_violences_enfants_directes_ecoute AS b ON a.id_femme=b.id_from_violences
          JOIN sos_gen_violences_enfants_directes AS c ON b.id_from_violences_enfants_directes=c.id_violences_enfants
          WHERE a.id_femme = ' . $row->id_femme . ' ORDER BY b.priority ASC')->result_array();
        if (count($row_query_violences_enfants_directes) > 0) {
            $pour_les_enfants.= '<ul><li>Directes<ul>';
            foreach ($row_query_violences_enfants_directes as $items) {

                $pour_les_enfants.='<li>' . $items['name_violences_enfants_directes'] . '</li>';
            }
            $pour_les_enfants.='</ul></li></ul>';
        }
        $row_query_violences_enfants_indirectes = $this->db->query('SELECT c.name_violences_enfants_indirectes
          FROM sos_ecoute AS a
          JOIN sos_relation_violences_enfants_indirectes_ecoute AS b ON a.id_femme=b.id_from_violences
          JOIN sos_gen_violences_enfants_indirectes AS c ON b.id_from_violences_enfants_indirectes=c.id_violences_enfants
          WHERE a.id_femme = ' . $row->id_femme . ' ORDER BY b.priority ASC')->result_array();
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
          FROM sos_ecoute AS a
          JOIN sos_relation_consequences_physiques_ecoute AS b ON a.id_femme=b.id_from_violences
          JOIN sos_gen_consequences_physiques AS c ON b.id_from_consequences_physiques=c.id_consequences_physiques
          WHERE a.id_femme = ' . $row->id_femme)->result_array();
        if (count($row_query_consequences_physiques) > 0) {
            $html.= '<ul><li><b>Physiques</b><ul>';
            foreach ($row_query_consequences_physiques as $items) {
                $html.='<li>' . $items['name_consequences_physiques'] . '</li>';
            }
            $html.='</ul></li></ul>';
        }

        $row_query_consequences_psychologiques = $this->db->query('SELECT c.name_consequences_psychologiques
          FROM sos_ecoute AS a
          JOIN sos_relation_consequences_psychologiques_ecoute AS b ON a.id_femme=b.id_from_violences
          JOIN sos_gen_consequences_psychologiques AS c ON b.id_from_consequences_psychologiques=c.id_consequences_psychologiques
          WHERE a.id_femme = ' . $row->id_femme)->result_array();
        if (count($row_query_consequences_psychologiques) > 0) {
            $html.= '<ul><li><b>Psychologiques</b><ul>';
            foreach ($row_query_consequences_psychologiques as $items) {
                $html.='<li>' . $items['name_consequences_psychologiques'] . '</li>';
            }
            $html.='</ul></li></ul>';
        }

        $row_query_consequences_administratives = $this->db->query('SELECT c.name_consequences_administratives
          FROM sos_ecoute AS a
          JOIN sos_relation_consequences_administratives_ecoute AS b ON a.id_femme=b.id_from_violences
          JOIN sos_gen_consequences_administratives AS c ON b.id_from_consequences_administratives=c.id_consequences_administratives
          WHERE a.id_femme = ' . $row->id_femme)->result_array();
        if (count($row_query_consequences_administratives) > 0) {
            $html.= '<ul><li><b>Administratives</b><ul>';
            foreach ($row_query_consequences_administratives as $items) {
                $html.='<li>' . $items['name_consequences_administratives'] . '</li>';
            }
            $html.='</ul></li></ul>';
        }


        return $html;
    }

    function before_update($post_array, $primary_key) {
        if ($post_array['situation_actuelle'] == '') {
            $post_array['situation_actuelle_detailles'] = '';
        }
        if ($post_array['first'] == ''):
            $post_array['second'] = '';
            $post_array['third'] = '';
        elseif ($post_array['second'] == ''):
            $post_array['third'] = '';

        endif;
        if ($post_array['first1'] == ''):
            $post_array['second1'] = '';
            $post_array['third1'] = '';
        elseif ($post_array['second1'] == ''):
            $post_array['third1'] = '';

        endif;
        if ($post_array['first2'] == ''):
            $post_array['second2'] = '';
            $post_array['third2'] = '';
        elseif ($post_array['second2'] == ''):
            $post_array['third2'] = '';

        endif;


        return $post_array;
    }

    function insert_after_update($post_array, $primary_key) {
        $data = array(
            'id_femme' => $primary_key,
            'id_utilisateur' => $this->session->userdata('userid')
        );
        $this->db->insert('sos_ouverture_ecoute', $data);
        $data = array(
            'par' => $this->session->userdata('userid')
        );

        $this->db->where('id_femme', $primary_key);
        $this->db->update('sos_ecoute', $data);

        return true;
    }

    function insert_after_insert($post_array, $primary_key) {
        $data = array(
            'id_femme' => $primary_key,
            'id_utilisateur' => $this->session->userdata('userid')
        );
        $this->db->insert('sos_ouverture_ecoute', $data);

        $data = array(
            'par' => $this->session->userdata('userid')
        );
        $this->db->where('id_femme', $primary_key);
        $this->db->update('sos_ecoute', $data);

        return true;
    }

    function edit_field_callback_1($value, $primary_key) {
        $this->db->where('id_femme', $primary_key);
        $lead = $this->db->get('sos_ecoute')->row();
        $oui = '';
        $non = '';
        if ($lead->accueil) {
            $oui = 'checked';
        } else {
            $non = 'checked';
        }

        $htmp = '
	OUI  <INPUT type=radio name="accueil" value="1" ' . $oui . '>
	<br>
        <br>
        NON  <INPUT type=radio name="accueil" value="0" ' . $non . '>
	';

        //$htmp = "<input type='radio' name='accueil' value='" . $lead->accueil . "'> <input type='radio' name='rep_accueil' value='" .$lead->rep_accueil. "'>";
        return $htmp;
    }

    function add_field_callback_1() {

        return "<input type='radio' name='accueil' >";
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
