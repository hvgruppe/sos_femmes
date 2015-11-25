<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

session_start();

class Premier_acceuil extends CI_Controller {

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

    public function first($id) {
        try {

// General
            $crud = new grocery_CRUD();

            $crud->set_language("french");


            $crud->set_theme('bootstrap');
            $crud->set_table('sos_femme_premier');
            if (!$this->session->userdata('status')) {
                $crud->where('archiver', FALSE);
            }
            $crud->set_subject('Femme');
            $crud->where('id_femme', $id);
            $crud->unset_add();
//relations
            $crud->set_relation('situation_familiale', 'sos_gen_situation_familiale_parrent', 'name_situation_familiale_parrent');
            $crud->set_relation('detailles', 'sos_gen_situation_familiale_child', 'name_situation_familiale_child');

            $crud->set_relation('emplois', 'sos_gen_emplois_parrent', 'name_emplois');
            $crud->set_relation('emplois_detailles', 'sos_gen_emplois_child', 'name_emplois_detaille');
            $crud->set_relation('emplois_more_detailles', 'sos_gen_emplois_child_child', 'name_emplois_child_child');


            $crud->set_relation('logement', 'sos_gen_logement_parent', 'name_logement');
            $crud->set_relation('logement_detailles', 'sos_gen_logement_child', 'name_logement_child');

            $crud->set_relation('ressources', 'sos_gen_ressources', 'name_ressources');
            $crud->set_relation('provenance', 'sos_gen_provenance', 'name_provenance');

            $crud->set_relation('allocations_familiales', 'sos_gen_allocations_familiales', 'name_allocations_familiales');
            $crud->set_relation('percues_par', 'sos_gen_percues_par', 'name_percues_par');
            $crud->set_relation('depuis', 'sos_gen_depuis', 'name_depuis');
            $crud->set_relation('situation_actuelle_depuis', 'sos_gen_situation_actuelle_depuis', 'name_situation_actuelle_depuis', null, 'id_situation_actuelle_depuis ASC');
            $crud->set_relation('situation_actuelle', 'sos_gen_situation_actuelle', 'name_situation_actuelle');
            $crud->set_relation('situation_actuelle_detailles', 'sos_gen_situation_actuelle_detailles', 'name_situation_actuelle_detailles');
            $crud->set_relation('departs_anterieurs', 'sos_gen_departs_anterieurs', 'name_departs_anterieurs');

//Master/child relations
            $fields_situation_familialle = array(
                'situation_familiale' => array(
                    'table_name' => 'sos_gen_situation_familiale_parrent',
                    'title' => 'name_situation_familiale_parrent',
                    'relate' => null
                ),
                'detailles' => array(
                    'table_name' => 'sos_gen_situation_familiale_child',
                    'title' => 'name_situation_familiale_child',
                    'id_field' => 'id_situation_familiale_child',
                    'relate' => 'id_parrent_from_situation_familiale_parrent',
                    'data-placeholder' => 'Préciser'
                )
            );
            $config_situation_familialle = array(
                'main_table' => 'sos_femme_premier',
                'main_table_primary' => 'id_femme',
                "url" => base_url() . 'index.php/home/index/',
                'segment_name' => "situation_familialle"
            );
            $categories_situation_familialle = new gc_dependent_select($crud, $fields_situation_familialle, $config_situation_familialle);
            $js_situation_familialle = $categories_situation_familialle->get_js();


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
                'main_table' => 'sos_femme',
                'main_table_primary' => 'id_femme',
                "url" => base_url() . 'index.php/home/index/',
                'segment_name' => "situation_actuelle"
            );
            $categories_situation_actuelle = new gc_dependent_select($crud, $fields_situation_actuelle, $config_situation_actuelle);
            $js_situation_actuelle = $categories_situation_actuelle->get_js();


            $fields_emplois = array(
                'emplois' => array(
                    'table_name' => 'sos_gen_emplois_parrent',
                    'title' => 'name_emplois',
                    'relate' => null
                ),
                'emplois_detailles' => array(
                    'table_name' => 'sos_gen_emplois_child',
                    'title' => 'name_emplois_detaille',
                    'id_field' => 'id_emplois_detailles',
                    'relate' => 'id_from_emplois',
                    'data-placeholder' => 'Préciser'
                ),
                'emplois_more_detailles' => array(
                    'table_name' => 'sos_gen_emplois_child_child',
                    'title' => 'name_emplois_child_child',
                    'id_field' => 'id_emplois_child_child',
                    'relate' => 'id_emplois_from_child',
                    'data-placeholder' => 'Préciser'
                )
            );
            $config_emplois = array(
                'main_table' => 'sos_femme_premier',
                'main_table_primary' => 'id_femme',
                "url" => base_url() . 'index.php/home/index/',
                'segment_name' => "emplois"
            );
            $categories_emplois = new gc_dependent_select($crud, $fields_emplois, $config_emplois);
            $js_emplois = $categories_emplois->get_js();


            $fields_ressources = array(
                'ressources' => array(
                    'table_name' => 'sos_gen_ressources',
                    'title' => 'name_ressources',
                    'relate' => null
                ),
                'provenance' => array(
                    'table_name' => 'sos_gen_provenance',
                    'title' => 'name_provenance',
                    'id_field' => 'id_provenance',
                    'relate' => 'id_from_ressources',
                    'data-placeholder' => 'Préciser'
                )
            );
            $config_ressources = array(
                'main_table' => 'sos_femme_premier',
                'main_table_primary' => 'id_femme',
                "url" => base_url() . 'index.php/home/index/',
                'segment_name' => "ressources"
            );
            $categories_ressources = new gc_dependent_select($crud, $fields_ressources, $config_ressources);
            $js_ressources = $categories_ressources->get_js();


            $fields_allocations_familiales = array(
                'allocations_familiales' => array(
                    'table_name' => 'sos_gen_allocations_familiales',
                    'title' => 'name_allocations_familiales',
                    'relate' => null
                ),
                'percues_par' => array(
                    'table_name' => 'sos_gen_percues_par',
                    'title' => 'name_percues_par',
                    'id_field' => 'id_percues_par',
                    'relate' => 'id_from_allocations_familiales',
                    'data-placeholder' => 'Préciser'
                )
            );
            $config_allocations_familiales = array(
                'main_table' => 'sos_femme_premier',
                'main_table_primary' => 'id_femme',
                "url" => base_url() . 'index.php/home/index/',
                'segment_name' => "allocations_familiales"
            );
            $categories_allocations_familiales = new gc_dependent_select($crud, $fields_allocations_familiales, $config_allocations_familiales);
            $js_allocations_familiales = $categories_allocations_familiales->get_js();


            $fields_logement = array(
                'logement' => array(
                    'table_name' => 'sos_gen_logement_parent',
                    'title' => 'name_logement',
                    'relate' => null
                ),
                'logement_detailles' => array(
                    'table_name' => 'sos_gen_logement_child',
                    'title' => 'name_logement_child',
                    'id_field' => 'id_logement_child',
                    'relate' => 'id_from_logement_parent',
                    'data-placeholder' => 'Préciser'
                )
            );
            $config_logement = array(
                'main_table' => 'sos_femme',
                'main_table_primary' => 'id_femme',
                "url" => base_url() . 'index.php/home/index/',
                'segment_name' => "logement"
            );
            $categories_logement = new gc_dependent_select($crud, $fields_logement, $config_logement);
            $js_logement = $categories_logement->get_js();


//Visual
            $crud->columns('Premier Acceuil', 'enceinte', 'profession', 'Situation Actuelle - Emlpoi', 'Appartenance - Logement', 'Situation Actuelle - Logement', 'departs_anterieurs', 'ressources', 'provenance', 'Allocations familiales', 'dettes');

            $crud->display_as('pays', 'Originaire de')
                    ->display_as('archiver', 'Archivage')
                    ->display_as('prenom', 'Prénom')
                    ->display_as('telephone', 'Téléphone')
                    ->display_as('nationalite', 'Nationalité')
                    ->display_as('rue', 'Adresse')
                    ->display_as('oriente_par_SMS', 'Orientée par SMS')
                    ->display_as('departs_anterieurs', 'Départs antérieurs')
                    ->display_as('nationalite_detailles', 'Situation administrative')
                    ->display_as('detailles', 'Situation familiale en détail')
                    ->display_as('duree_de_la_relation', 'Durée de la relation')
                    ->display_as('emplois_detailles', 'Détails de l\'emploi')
                    ->display_as('emplois_more_detailles', 'Plus de détails de l\'emploi')
                    ->display_as('ouvertures', 'Dernière modification')
                    ->display_as('hebergement', 'Demande d\'hébergement')
                    ->display_as('aide_materielle', 'Demande d\'aide matérielle')
                    ->display_as('accompagnement_exterieur', 'Demande d\'accompagnement extérieur')
                    ->display_as('adresse_postale', 'Demande d\'adresse postale')
                    ->display_as('accueil', 'Demande d\'accueil')
                    ->display_as('informations', 'Demande d\'informations')
                    ->display_as('conseil', 'Demande de conseil')
                    ->display_as('orientation', 'Demande d\'orientation')
                    ->display_as('rdv', 'Demande de rdv')
                    ->display_as('hebergement', 'Demande d\'hébergement')
                    ->display_as('logement_dem', 'Demande de logement')
                    ->display_as('logement_detailles', 'Logement détaillé')
                    ->display_as('situation_actuelle_detailles', 'Situation actuelle détaillée')
                    ->display_as('emplois', 'Situation professionnelle')
                    ->display_as('emplois_detailles', 'Situation professionnelle détaillée')
                    ->display_as('emplois_more_detailles', 'Autres infos emploi')
                    ->display_as('percues_par', 'Perçues par')
                    ->display_as('commentaire', 'Commentaires')
                    ->display_as('partenaire', 'Orienteur');

            $crud->order_by('prenom', 'desc');

//unsets


            if (!$this->session->userdata('status')) {
                $crud->unset_delete();
                $crud->unset_export();
                $crud->unset_print();
            }

//Requireds
            //$crud->required_fields('service', 'premier_contact', 'prenom');
//Callbacks
            $crud->callback_column('Premier Acceuil', array($this, 'premier_acceuil'));
            $crud->callback_column('Situation Actuelle - Emlpoi', array($this, 'situation_actuelle'));
            $crud->callback_column('Appartenance - Logement', array($this, 'appartenance'));
            $crud->callback_column('Situation Actuelle - Logement', array($this, 'situation_actuelle_logement'));
            $crud->callback_column('Allocations familiales', array($this, 'allocations_familiales'));
            //$crud->callback_column('premier_contact', array($this, 'premier_contact'));
            //$crud->callback_column('nombre_d\'enfants', array($this, 'enfants'));
            //$crud->callback_after_update(array($this, 'insert_after_update'));
            //$crud->callback_after_insert(array($this, 'insert_after_insert'));
            //$crud->callback_after_delete(array($this, 'after_delete'));
            //$crud->callback_column('archiver', array($this, 'archiver'));
            $crud->callback_before_update(array($this, 'before_update'));
//field Types
            $crud->field_type('enceinte', 'dropdown', array('1' => '1 mois', '2' => '2 mois', '3' => '3 mois', '4' => '4 mois', '5' => '5 mois', '6' => '6 mois', '7' => '7 mois', '8' => '8 mois', '9' => '9 mois'));
            $crud->field_type('dettes', 'enum', array('OUI', 'NON'));
// Actions
// Renders
            $crud->unset_fields('id_femme', 'prenom', 'nom', 'age', 'telephone', 'nom_marital', 'rue', 'premier_contact', 'par', 'service', 'oriente_par_SMS', 'partenaire', 'ville', 'pays', 'nationalite', 'nationalite_detailles', 'accueil', 'informations', 'conseil', 'orientation', 'rdv', 'hebergement', 'logement_dem', 'aide_materielle', 'adresse_postale', 'accompagnement_exterieur', 'commentaire', 'archiver', 'duree_de_la_relation');
            $output = $crud->render();
            $output->output.= $js_situation_familialle . $js_emplois . $js_ressources . $js_allocations_familiales . $js_logement . $js_situation_actuelle;

            $menu = new stdClass;
            $menu->n1 = true;
            $menu->status = $this->session->userdata('status');
            $header = $this->navigation->home_f($menu);

            $data = array('output' => $output, 'header' => $header);

            $this->_example_output($data);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    function allocations_familiales($value, $row) {

        $query = $this->db->get_where('sos_femme_premier', array('id_femme' => $row->id_femme));
        if ($query->num_rows != 0) {
            $this->db->select('sos_femme_premier.prenom, sos_femme_premier.nom, sos_femme_premier.nom_marital , sos_femme_premier.enceinte, sos_femme_premier.telephone, sos_femme_premier.rue,sos_femme_premier.premier_contact, sos_femme_premier.oriente_par_SMS,sos_femme_premier.situation_actuelle_depuis,sos_femme_premier.departs_anterieurs,sos_femme_premier.depuis,sos_femme_premier.profession,sos_femme_premier.dettes, sos_femme_premier.accueil, sos_femme_premier.conseil,sos_femme_premier.orientation,sos_femme_premier.logement_dem,sos_femme_premier.aide_materielle,sos_femme_premier.adresse_postale,sos_femme_premier.accompagnement_exterieur,sos_femme_premier.commentaire,
          sos_utilisateur.prenom_utilisateur, sos_utilisateur.nom_utilisateur,
          sos_gen_depuis.name_depuis,
          sos_gen_departs_anterieurs.name_departs_anterieurs,
          sos_gen_femme_age.name_femme_age,
          sos_gen_service.nom_service,
          sos_gen_villes.nom_ville,
          sos_gen_pays.nom_pays, sos_gen_pays.continent,
          sos_gen_nationalite.name_nationalite,
          sos_gen_nationalite_detailles.name_nationalite_detailles,
          sos_gen_situation_familiale_parrent.name_situation_familiale_parrent,
          sos_gen_situation_familiale_child.name_situation_familiale_child,
          sos_gen_emplois_parrent.name_emplois,
          sos_gen_emplois_child.name_emplois_detaille,
          sos_gen_emplois_child_child.name_emplois_child_child,
          sos_gen_ressources.name_ressources,
          sos_gen_provenance.name_provenance,
          sos_gen_allocations_familiales.name_allocations_familiales,
          sos_gen_percues_par.name_percues_par,
          sos_gen_partenaire.name_partenaire,
          sos_gen_logement_parent.name_logement,
          sos_gen_logement_child.name_logement_child,
          sos_gen_situation_actuelle.name_situation_actuelle,
          sos_gen_situation_actuelle_detailles.name_situation_actuelle_detailles,
          sos_gen_informations.name_informations,
          sos_gen_rdv.name_rdv,
          sos_gen_hebergement.name_hebergement');
            $this->db->join('sos_utilisateur', 'sos_utilisateur.id_utilisateur = sos_femme_premier.par', 'left');
            $this->db->join('sos_gen_service', 'sos_gen_service.id_service = sos_femme_premier.service', 'left');
            $this->db->join('sos_gen_femme_age', 'sos_gen_femme_age.id_femme_age = sos_femme_premier.age', 'left');
            $this->db->join('sos_gen_departs_anterieurs', 'sos_gen_departs_anterieurs.id_departs_anterieurs = sos_femme_premier.departs_anterieurs', 'left');
            $this->db->join('sos_gen_depuis', 'sos_gen_depuis.id_depuis = sos_femme_premier.depuis', 'left');
            $this->db->join('sos_gen_villes', 'sos_gen_villes.id_ville = sos_femme_premier.ville', 'left');
            $this->db->join('sos_gen_pays', 'sos_gen_pays.id = sos_femme_premier.pays', 'left');
            $this->db->join('sos_gen_nationalite', 'sos_gen_nationalite.id_nationalite = sos_femme_premier.nationalite', 'left');
            $this->db->join('sos_gen_nationalite_detailles', 'sos_gen_nationalite_detailles.id_nationalite_detailles = sos_femme_premier.nationalite_detailles', 'left');
            $this->db->join('sos_gen_situation_familiale_parrent', 'sos_gen_situation_familiale_parrent.id_situation_familiale_parrent = sos_femme_premier.situation_familiale', 'left');
            $this->db->join('sos_gen_situation_familiale_child', 'sos_gen_situation_familiale_child.id_situation_familiale_child = sos_femme_premier.detailles', 'left');
            $this->db->join('sos_gen_emplois_parrent', 'sos_gen_emplois_parrent.id_emplois = sos_femme_premier.emplois', 'left');
            $this->db->join('sos_gen_emplois_child', 'sos_gen_emplois_child.id_emplois_detailles = sos_femme_premier.emplois_detailles', 'left');
            $this->db->join('sos_gen_emplois_child_child', 'sos_gen_emplois_child_child.id_emplois_child_child = sos_femme_premier.emplois_more_detailles', 'left');
            $this->db->join('sos_gen_ressources', 'sos_gen_ressources.id_ressources = sos_femme_premier.ressources', 'left');
            $this->db->join('sos_gen_provenance', 'sos_gen_provenance.id_provenance = sos_femme_premier.provenance', 'left');
            $this->db->join('sos_gen_allocations_familiales', 'sos_gen_allocations_familiales.id_allocations_familiales = sos_femme_premier.allocations_familiales', 'left');
            $this->db->join('sos_gen_percues_par', 'sos_gen_percues_par.id_percues_par = sos_femme_premier.percues_par', 'left');
            $this->db->join('sos_gen_partenaire', 'sos_gen_partenaire.id_partenaire = sos_femme_premier.partenaire', 'left');
            $this->db->join('sos_gen_logement_parent', 'sos_gen_logement_parent.id_logement_parent = sos_femme_premier.logement', 'left');
            $this->db->join('sos_gen_logement_child', 'sos_gen_logement_child.id_logement_child = sos_femme_premier.logement_detailles', 'left');



            $this->db->join('sos_gen_situation_actuelle', 'sos_gen_situation_actuelle.id_situation_actuelle = sos_femme_premier.situation_actuelle', 'left');
            $this->db->join('sos_gen_situation_actuelle_detailles', 'sos_gen_situation_actuelle_detailles.id_situation_actuelle_detailles = sos_femme_premier.situation_actuelle_detailles', 'left');

            $this->db->join('sos_gen_informations', 'sos_gen_informations.id_informations = sos_femme_premier.informations', 'left');
            $this->db->join('sos_gen_rdv', 'sos_gen_rdv.id_rdv = sos_femme_premier.rdv', 'left');
            $this->db->join('sos_gen_hebergement', 'sos_gen_hebergement.id_hebergement = sos_femme_premier.hebergement', 'left');

            $this->db->where('sos_femme_premier.id_femme', $row->id_femme);
            $query = $this->db->get('sos_femme_premier');
            $row_femme = $query->row();

            return $row_femme->name_allocations_familiales . ' - ' . '- Perçues par ' . $row_femme->name_percues_par;
        }

        return '';
    }

    function situation_actuelle_logement($value, $row) {

        $query = $this->db->get_where('sos_femme_premier', array('id_femme' => $row->id_femme));
        if ($query->num_rows != 0) {
            $this->db->select('sos_femme_premier.prenom, sos_femme_premier.nom, sos_femme_premier.nom_marital , sos_femme_premier.enceinte, sos_femme_premier.telephone, sos_femme_premier.rue,sos_femme_premier.premier_contact, sos_femme_premier.oriente_par_SMS,sos_femme_premier.situation_actuelle_depuis,sos_femme_premier.departs_anterieurs,sos_femme_premier.depuis,sos_femme_premier.profession,sos_femme_premier.dettes, sos_femme_premier.accueil, sos_femme_premier.conseil,sos_femme_premier.orientation,sos_femme_premier.logement_dem,sos_femme_premier.aide_materielle,sos_femme_premier.adresse_postale,sos_femme_premier.accompagnement_exterieur,sos_femme_premier.commentaire,
          sos_utilisateur.prenom_utilisateur, sos_utilisateur.nom_utilisateur,
          sos_gen_depuis.name_depuis,
          sos_gen_departs_anterieurs.name_departs_anterieurs,
          sos_gen_femme_age.name_femme_age,
          sos_gen_service.nom_service,
          sos_gen_villes.nom_ville,
          sos_gen_pays.nom_pays, sos_gen_pays.continent,
          sos_gen_nationalite.name_nationalite,
          sos_gen_nationalite_detailles.name_nationalite_detailles,
          sos_gen_situation_familiale_parrent.name_situation_familiale_parrent,
          sos_gen_situation_familiale_child.name_situation_familiale_child,
          sos_gen_emplois_parrent.name_emplois,
          sos_gen_emplois_child.name_emplois_detaille,
          sos_gen_emplois_child_child.name_emplois_child_child,
          sos_gen_ressources.name_ressources,
          sos_gen_provenance.name_provenance,
          sos_gen_allocations_familiales.name_allocations_familiales,
          sos_gen_percues_par.name_percues_par,
          sos_gen_partenaire.name_partenaire,
          sos_gen_logement_parent.name_logement,
          sos_gen_logement_child.name_logement_child,
          sos_gen_situation_actuelle.name_situation_actuelle,
          sos_gen_situation_actuelle_detailles.name_situation_actuelle_detailles,
          sos_gen_informations.name_informations,
          sos_gen_rdv.name_rdv,
          sos_gen_hebergement.name_hebergement');
            $this->db->join('sos_utilisateur', 'sos_utilisateur.id_utilisateur = sos_femme_premier.par', 'left');
            $this->db->join('sos_gen_service', 'sos_gen_service.id_service = sos_femme_premier.service', 'left');
            $this->db->join('sos_gen_femme_age', 'sos_gen_femme_age.id_femme_age = sos_femme_premier.age', 'left');
            $this->db->join('sos_gen_departs_anterieurs', 'sos_gen_departs_anterieurs.id_departs_anterieurs = sos_femme_premier.departs_anterieurs', 'left');
            $this->db->join('sos_gen_depuis', 'sos_gen_depuis.id_depuis = sos_femme_premier.depuis', 'left');
            $this->db->join('sos_gen_villes', 'sos_gen_villes.id_ville = sos_femme_premier.ville', 'left');
            $this->db->join('sos_gen_pays', 'sos_gen_pays.id = sos_femme_premier.pays', 'left');
            $this->db->join('sos_gen_nationalite', 'sos_gen_nationalite.id_nationalite = sos_femme_premier.nationalite', 'left');
            $this->db->join('sos_gen_nationalite_detailles', 'sos_gen_nationalite_detailles.id_nationalite_detailles = sos_femme_premier.nationalite_detailles', 'left');
            $this->db->join('sos_gen_situation_familiale_parrent', 'sos_gen_situation_familiale_parrent.id_situation_familiale_parrent = sos_femme_premier.situation_familiale', 'left');
            $this->db->join('sos_gen_situation_familiale_child', 'sos_gen_situation_familiale_child.id_situation_familiale_child = sos_femme_premier.detailles', 'left');
            $this->db->join('sos_gen_emplois_parrent', 'sos_gen_emplois_parrent.id_emplois = sos_femme_premier.emplois', 'left');
            $this->db->join('sos_gen_emplois_child', 'sos_gen_emplois_child.id_emplois_detailles = sos_femme_premier.emplois_detailles', 'left');
            $this->db->join('sos_gen_emplois_child_child', 'sos_gen_emplois_child_child.id_emplois_child_child = sos_femme_premier.emplois_more_detailles', 'left');
            $this->db->join('sos_gen_ressources', 'sos_gen_ressources.id_ressources = sos_femme_premier.ressources', 'left');
            $this->db->join('sos_gen_provenance', 'sos_gen_provenance.id_provenance = sos_femme_premier.provenance', 'left');
            $this->db->join('sos_gen_allocations_familiales', 'sos_gen_allocations_familiales.id_allocations_familiales = sos_femme_premier.allocations_familiales', 'left');
            $this->db->join('sos_gen_percues_par', 'sos_gen_percues_par.id_percues_par = sos_femme_premier.percues_par', 'left');
            $this->db->join('sos_gen_partenaire', 'sos_gen_partenaire.id_partenaire = sos_femme_premier.partenaire', 'left');
            $this->db->join('sos_gen_logement_parent', 'sos_gen_logement_parent.id_logement_parent = sos_femme_premier.logement', 'left');
            $this->db->join('sos_gen_logement_child', 'sos_gen_logement_child.id_logement_child = sos_femme_premier.logement_detailles', 'left');



            $this->db->join('sos_gen_situation_actuelle', 'sos_gen_situation_actuelle.id_situation_actuelle = sos_femme_premier.situation_actuelle', 'left');
            $this->db->join('sos_gen_situation_actuelle_detailles', 'sos_gen_situation_actuelle_detailles.id_situation_actuelle_detailles = sos_femme_premier.situation_actuelle_detailles', 'left');

            $this->db->join('sos_gen_informations', 'sos_gen_informations.id_informations = sos_femme_premier.informations', 'left');
            $this->db->join('sos_gen_rdv', 'sos_gen_rdv.id_rdv = sos_femme_premier.rdv', 'left');
            $this->db->join('sos_gen_hebergement', 'sos_gen_hebergement.id_hebergement = sos_femme_premier.hebergement', 'left');

            $this->db->where('sos_femme_premier.id_femme', $row->id_femme);
            $query = $this->db->get('sos_femme_premier');
            $row_femme = $query->row();


            return $row_femme->name_situation_actuelle . ' - ' . $row_femme->name_situation_actuelle_detailles . ' depuis ' . $row_femme->situation_actuelle_depuis . 'moi(s)';
        }

        return '';
    }

    function appartenance($value, $row) {

        $query = $this->db->get_where('sos_femme_premier', array('id_femme' => $row->id_femme));
        if ($query->num_rows != 0) {
            $this->db->select('sos_femme_premier.prenom, sos_femme_premier.nom, sos_femme_premier.nom_marital , sos_femme_premier.enceinte, sos_femme_premier.telephone, sos_femme_premier.rue,sos_femme_premier.premier_contact, sos_femme_premier.oriente_par_SMS,sos_femme_premier.situation_actuelle_depuis,sos_femme_premier.departs_anterieurs,sos_femme_premier.depuis,sos_femme_premier.profession,sos_femme_premier.dettes, sos_femme_premier.accueil, sos_femme_premier.conseil,sos_femme_premier.orientation,sos_femme_premier.logement_dem,sos_femme_premier.aide_materielle,sos_femme_premier.adresse_postale,sos_femme_premier.accompagnement_exterieur,sos_femme_premier.commentaire,
          sos_utilisateur.prenom_utilisateur, sos_utilisateur.nom_utilisateur,
          sos_gen_depuis.name_depuis,
          sos_gen_departs_anterieurs.name_departs_anterieurs,
          sos_gen_femme_age.name_femme_age,
          sos_gen_service.nom_service,
          sos_gen_villes.nom_ville,
          sos_gen_pays.nom_pays, sos_gen_pays.continent,
          sos_gen_nationalite.name_nationalite,
          sos_gen_nationalite_detailles.name_nationalite_detailles,
          sos_gen_situation_familiale_parrent.name_situation_familiale_parrent,
          sos_gen_situation_familiale_child.name_situation_familiale_child,
          sos_gen_emplois_parrent.name_emplois,
          sos_gen_emplois_child.name_emplois_detaille,
          sos_gen_emplois_child_child.name_emplois_child_child,
          sos_gen_ressources.name_ressources,
          sos_gen_provenance.name_provenance,
          sos_gen_allocations_familiales.name_allocations_familiales,
          sos_gen_percues_par.name_percues_par,
          sos_gen_partenaire.name_partenaire,
          sos_gen_logement_parent.name_logement,
          sos_gen_logement_child.name_logement_child,
          sos_gen_situation_actuelle.name_situation_actuelle,
          sos_gen_situation_actuelle_detailles.name_situation_actuelle_detailles,
          sos_gen_informations.name_informations,
          sos_gen_rdv.name_rdv,
          sos_gen_hebergement.name_hebergement');
            $this->db->join('sos_utilisateur', 'sos_utilisateur.id_utilisateur = sos_femme_premier.par', 'left');
            $this->db->join('sos_gen_service', 'sos_gen_service.id_service = sos_femme_premier.service', 'left');
            $this->db->join('sos_gen_femme_age', 'sos_gen_femme_age.id_femme_age = sos_femme_premier.age', 'left');
            $this->db->join('sos_gen_departs_anterieurs', 'sos_gen_departs_anterieurs.id_departs_anterieurs = sos_femme_premier.departs_anterieurs', 'left');
            $this->db->join('sos_gen_depuis', 'sos_gen_depuis.id_depuis = sos_femme_premier.depuis', 'left');
            $this->db->join('sos_gen_villes', 'sos_gen_villes.id_ville = sos_femme_premier.ville', 'left');
            $this->db->join('sos_gen_pays', 'sos_gen_pays.id = sos_femme_premier.pays', 'left');
            $this->db->join('sos_gen_nationalite', 'sos_gen_nationalite.id_nationalite = sos_femme_premier.nationalite', 'left');
            $this->db->join('sos_gen_nationalite_detailles', 'sos_gen_nationalite_detailles.id_nationalite_detailles = sos_femme_premier.nationalite_detailles', 'left');
            $this->db->join('sos_gen_situation_familiale_parrent', 'sos_gen_situation_familiale_parrent.id_situation_familiale_parrent = sos_femme_premier.situation_familiale', 'left');
            $this->db->join('sos_gen_situation_familiale_child', 'sos_gen_situation_familiale_child.id_situation_familiale_child = sos_femme_premier.detailles', 'left');
            $this->db->join('sos_gen_emplois_parrent', 'sos_gen_emplois_parrent.id_emplois = sos_femme_premier.emplois', 'left');
            $this->db->join('sos_gen_emplois_child', 'sos_gen_emplois_child.id_emplois_detailles = sos_femme_premier.emplois_detailles', 'left');
            $this->db->join('sos_gen_emplois_child_child', 'sos_gen_emplois_child_child.id_emplois_child_child = sos_femme_premier.emplois_more_detailles', 'left');
            $this->db->join('sos_gen_ressources', 'sos_gen_ressources.id_ressources = sos_femme_premier.ressources', 'left');
            $this->db->join('sos_gen_provenance', 'sos_gen_provenance.id_provenance = sos_femme_premier.provenance', 'left');
            $this->db->join('sos_gen_allocations_familiales', 'sos_gen_allocations_familiales.id_allocations_familiales = sos_femme_premier.allocations_familiales', 'left');
            $this->db->join('sos_gen_percues_par', 'sos_gen_percues_par.id_percues_par = sos_femme_premier.percues_par', 'left');
            $this->db->join('sos_gen_partenaire', 'sos_gen_partenaire.id_partenaire = sos_femme_premier.partenaire', 'left');
            $this->db->join('sos_gen_logement_parent', 'sos_gen_logement_parent.id_logement_parent = sos_femme_premier.logement', 'left');
            $this->db->join('sos_gen_logement_child', 'sos_gen_logement_child.id_logement_child = sos_femme_premier.logement_detailles', 'left');



            $this->db->join('sos_gen_situation_actuelle', 'sos_gen_situation_actuelle.id_situation_actuelle = sos_femme_premier.situation_actuelle', 'left');
            $this->db->join('sos_gen_situation_actuelle_detailles', 'sos_gen_situation_actuelle_detailles.id_situation_actuelle_detailles = sos_femme_premier.situation_actuelle_detailles', 'left');

            $this->db->join('sos_gen_informations', 'sos_gen_informations.id_informations = sos_femme_premier.informations', 'left');
            $this->db->join('sos_gen_rdv', 'sos_gen_rdv.id_rdv = sos_femme_premier.rdv', 'left');
            $this->db->join('sos_gen_hebergement', 'sos_gen_hebergement.id_hebergement = sos_femme_premier.hebergement', 'left');

            $this->db->where('sos_femme_premier.id_femme', $row->id_femme);
            $query = $this->db->get('sos_femme_premier');
            $row_femme = $query->row();


            return $row_femme->name_logement . ' - ' . $row_femme->name_logement_child;
        }

        return '';
    }

    function premier_acceuil($value, $row) {

        $query = $this->db->get_where('sos_femme_premier', array('id_femme' => $row->id_femme));
        if ($query->num_rows != 0) {
            $this->db->select('sos_femme_premier.prenom, sos_femme_premier.nom, sos_femme_premier.nom_marital , sos_femme_premier.enceinte, sos_femme_premier.telephone, sos_femme_premier.rue,sos_femme_premier.premier_contact, sos_femme_premier.oriente_par_SMS,sos_femme_premier.situation_actuelle_depuis,sos_femme_premier.departs_anterieurs,sos_femme_premier.depuis,sos_femme_premier.profession,sos_femme_premier.dettes, sos_femme_premier.accueil, sos_femme_premier.conseil,sos_femme_premier.orientation,sos_femme_premier.logement_dem,sos_femme_premier.aide_materielle,sos_femme_premier.adresse_postale,sos_femme_premier.accompagnement_exterieur,sos_femme_premier.commentaire,
          sos_utilisateur.prenom_utilisateur, sos_utilisateur.nom_utilisateur,
          sos_gen_depuis.name_depuis,
          sos_gen_departs_anterieurs.name_departs_anterieurs,
          sos_gen_femme_age.name_femme_age,
          sos_gen_service.nom_service,
          sos_gen_villes.nom_ville,
          sos_gen_pays.nom_pays, sos_gen_pays.continent,
          sos_gen_nationalite.name_nationalite,
          sos_gen_nationalite_detailles.name_nationalite_detailles,
          sos_gen_situation_familiale_parrent.name_situation_familiale_parrent,
          sos_gen_situation_familiale_child.name_situation_familiale_child,
          sos_gen_emplois_parrent.name_emplois,
          sos_gen_emplois_child.name_emplois_detaille,
          sos_gen_emplois_child_child.name_emplois_child_child,
          sos_gen_ressources.name_ressources,
          sos_gen_provenance.name_provenance,
          sos_gen_allocations_familiales.name_allocations_familiales,
          sos_gen_percues_par.name_percues_par,
          sos_gen_partenaire.name_partenaire,
          sos_gen_logement_parent.name_logement,
          sos_gen_logement_child.name_logement_child,
          sos_gen_situation_actuelle.name_situation_actuelle,
          sos_gen_situation_actuelle_detailles.name_situation_actuelle_detailles,
          sos_gen_informations.name_informations,
          sos_gen_rdv.name_rdv,
          sos_gen_hebergement.name_hebergement');
            $this->db->join('sos_utilisateur', 'sos_utilisateur.id_utilisateur = sos_femme_premier.par', 'left');
            $this->db->join('sos_gen_service', 'sos_gen_service.id_service = sos_femme_premier.service', 'left');
            $this->db->join('sos_gen_femme_age', 'sos_gen_femme_age.id_femme_age = sos_femme_premier.age', 'left');
            $this->db->join('sos_gen_departs_anterieurs', 'sos_gen_departs_anterieurs.id_departs_anterieurs = sos_femme_premier.departs_anterieurs', 'left');
            $this->db->join('sos_gen_depuis', 'sos_gen_depuis.id_depuis = sos_femme_premier.depuis', 'left');
            $this->db->join('sos_gen_villes', 'sos_gen_villes.id_ville = sos_femme_premier.ville', 'left');
            $this->db->join('sos_gen_pays', 'sos_gen_pays.id = sos_femme_premier.pays', 'left');
            $this->db->join('sos_gen_nationalite', 'sos_gen_nationalite.id_nationalite = sos_femme_premier.nationalite', 'left');
            $this->db->join('sos_gen_nationalite_detailles', 'sos_gen_nationalite_detailles.id_nationalite_detailles = sos_femme_premier.nationalite_detailles', 'left');
            $this->db->join('sos_gen_situation_familiale_parrent', 'sos_gen_situation_familiale_parrent.id_situation_familiale_parrent = sos_femme_premier.situation_familiale', 'left');
            $this->db->join('sos_gen_situation_familiale_child', 'sos_gen_situation_familiale_child.id_situation_familiale_child = sos_femme_premier.detailles', 'left');
            $this->db->join('sos_gen_emplois_parrent', 'sos_gen_emplois_parrent.id_emplois = sos_femme_premier.emplois', 'left');
            $this->db->join('sos_gen_emplois_child', 'sos_gen_emplois_child.id_emplois_detailles = sos_femme_premier.emplois_detailles', 'left');
            $this->db->join('sos_gen_emplois_child_child', 'sos_gen_emplois_child_child.id_emplois_child_child = sos_femme_premier.emplois_more_detailles', 'left');
            $this->db->join('sos_gen_ressources', 'sos_gen_ressources.id_ressources = sos_femme_premier.ressources', 'left');
            $this->db->join('sos_gen_provenance', 'sos_gen_provenance.id_provenance = sos_femme_premier.provenance', 'left');
            $this->db->join('sos_gen_allocations_familiales', 'sos_gen_allocations_familiales.id_allocations_familiales = sos_femme_premier.allocations_familiales', 'left');
            $this->db->join('sos_gen_percues_par', 'sos_gen_percues_par.id_percues_par = sos_femme_premier.percues_par', 'left');
            $this->db->join('sos_gen_partenaire', 'sos_gen_partenaire.id_partenaire = sos_femme_premier.partenaire', 'left');
            $this->db->join('sos_gen_logement_parent', 'sos_gen_logement_parent.id_logement_parent = sos_femme_premier.logement', 'left');
            $this->db->join('sos_gen_logement_child', 'sos_gen_logement_child.id_logement_child = sos_femme_premier.logement_detailles', 'left');



            $this->db->join('sos_gen_situation_actuelle', 'sos_gen_situation_actuelle.id_situation_actuelle = sos_femme_premier.situation_actuelle', 'left');
            $this->db->join('sos_gen_situation_actuelle_detailles', 'sos_gen_situation_actuelle_detailles.id_situation_actuelle_detailles = sos_femme_premier.situation_actuelle_detailles', 'left');

            $this->db->join('sos_gen_informations', 'sos_gen_informations.id_informations = sos_femme_premier.informations', 'left');
            $this->db->join('sos_gen_rdv', 'sos_gen_rdv.id_rdv = sos_femme_premier.rdv', 'left');
            $this->db->join('sos_gen_hebergement', 'sos_gen_hebergement.id_hebergement = sos_femme_premier.hebergement', 'left');

            $this->db->where('sos_femme_premier.id_femme', $row->id_femme);
            $query = $this->db->get('sos_femme_premier');
            $row_femme = $query->row();
            return $row_femme->name_situation_familiale_parrent . ' - ' . $row_femme->name_situation_familiale_child . ' depuis ' . $row_femme->name_depuis;
        }

        return '';
    }

    function situation_actuelle($value, $row) {
        $query = $this->db->get_where('sos_femme_premier', array('id_femme' => $row->id_femme));
        if ($query->num_rows != 0) {
            $this->db->select('sos_femme_premier.prenom, sos_femme_premier.nom, sos_femme_premier.nom_marital , sos_femme_premier.enceinte, sos_femme_premier.telephone, sos_femme_premier.rue,sos_femme_premier.premier_contact, sos_femme_premier.oriente_par_SMS,sos_femme_premier.situation_actuelle_depuis,sos_femme_premier.departs_anterieurs,sos_femme_premier.depuis,sos_femme_premier.profession,sos_femme_premier.dettes, sos_femme_premier.accueil, sos_femme_premier.conseil,sos_femme_premier.orientation,sos_femme_premier.logement_dem,sos_femme_premier.aide_materielle,sos_femme_premier.adresse_postale,sos_femme_premier.accompagnement_exterieur,sos_femme_premier.commentaire,
          sos_utilisateur.prenom_utilisateur, sos_utilisateur.nom_utilisateur,
          sos_gen_depuis.name_depuis,
          sos_gen_departs_anterieurs.name_departs_anterieurs,
          sos_gen_femme_age.name_femme_age,
          sos_gen_service.nom_service,
          sos_gen_villes.nom_ville,
          sos_gen_pays.nom_pays, sos_gen_pays.continent,
          sos_gen_nationalite.name_nationalite,
          sos_gen_nationalite_detailles.name_nationalite_detailles,
          sos_gen_situation_familiale_parrent.name_situation_familiale_parrent,
          sos_gen_situation_familiale_child.name_situation_familiale_child,
          sos_gen_emplois_parrent.name_emplois,
          sos_gen_emplois_child.name_emplois_detaille,
          sos_gen_emplois_child_child.name_emplois_child_child,
          sos_gen_ressources.name_ressources,
          sos_gen_provenance.name_provenance,
          sos_gen_allocations_familiales.name_allocations_familiales,
          sos_gen_percues_par.name_percues_par,
          sos_gen_partenaire.name_partenaire,
          sos_gen_logement_parent.name_logement,
          sos_gen_logement_child.name_logement_child,
          sos_gen_situation_actuelle.name_situation_actuelle,
          sos_gen_situation_actuelle_detailles.name_situation_actuelle_detailles,
          sos_gen_informations.name_informations,
          sos_gen_rdv.name_rdv,
          sos_gen_hebergement.name_hebergement');
            $this->db->join('sos_utilisateur', 'sos_utilisateur.id_utilisateur = sos_femme_premier.par', 'left');
            $this->db->join('sos_gen_service', 'sos_gen_service.id_service = sos_femme_premier.service', 'left');
            $this->db->join('sos_gen_femme_age', 'sos_gen_femme_age.id_femme_age = sos_femme_premier.age', 'left');
            $this->db->join('sos_gen_departs_anterieurs', 'sos_gen_departs_anterieurs.id_departs_anterieurs = sos_femme_premier.departs_anterieurs', 'left');
            $this->db->join('sos_gen_depuis', 'sos_gen_depuis.id_depuis = sos_femme_premier.depuis', 'left');
            $this->db->join('sos_gen_villes', 'sos_gen_villes.id_ville = sos_femme_premier.ville', 'left');
            $this->db->join('sos_gen_pays', 'sos_gen_pays.id = sos_femme_premier.pays', 'left');
            $this->db->join('sos_gen_nationalite', 'sos_gen_nationalite.id_nationalite = sos_femme_premier.nationalite', 'left');
            $this->db->join('sos_gen_nationalite_detailles', 'sos_gen_nationalite_detailles.id_nationalite_detailles = sos_femme_premier.nationalite_detailles', 'left');
            $this->db->join('sos_gen_situation_familiale_parrent', 'sos_gen_situation_familiale_parrent.id_situation_familiale_parrent = sos_femme_premier.situation_familiale', 'left');
            $this->db->join('sos_gen_situation_familiale_child', 'sos_gen_situation_familiale_child.id_situation_familiale_child = sos_femme_premier.detailles', 'left');
            $this->db->join('sos_gen_emplois_parrent', 'sos_gen_emplois_parrent.id_emplois = sos_femme_premier.emplois', 'left');
            $this->db->join('sos_gen_emplois_child', 'sos_gen_emplois_child.id_emplois_detailles = sos_femme_premier.emplois_detailles', 'left');
            $this->db->join('sos_gen_emplois_child_child', 'sos_gen_emplois_child_child.id_emplois_child_child = sos_femme_premier.emplois_more_detailles', 'left');
            $this->db->join('sos_gen_ressources', 'sos_gen_ressources.id_ressources = sos_femme_premier.ressources', 'left');
            $this->db->join('sos_gen_provenance', 'sos_gen_provenance.id_provenance = sos_femme_premier.provenance', 'left');
            $this->db->join('sos_gen_allocations_familiales', 'sos_gen_allocations_familiales.id_allocations_familiales = sos_femme_premier.allocations_familiales', 'left');
            $this->db->join('sos_gen_percues_par', 'sos_gen_percues_par.id_percues_par = sos_femme_premier.percues_par', 'left');
            $this->db->join('sos_gen_partenaire', 'sos_gen_partenaire.id_partenaire = sos_femme_premier.partenaire', 'left');
            $this->db->join('sos_gen_logement_parent', 'sos_gen_logement_parent.id_logement_parent = sos_femme_premier.logement', 'left');
            $this->db->join('sos_gen_logement_child', 'sos_gen_logement_child.id_logement_child = sos_femme_premier.logement_detailles', 'left');



            $this->db->join('sos_gen_situation_actuelle', 'sos_gen_situation_actuelle.id_situation_actuelle = sos_femme_premier.situation_actuelle', 'left');
            $this->db->join('sos_gen_situation_actuelle_detailles', 'sos_gen_situation_actuelle_detailles.id_situation_actuelle_detailles = sos_femme_premier.situation_actuelle_detailles', 'left');

            $this->db->join('sos_gen_informations', 'sos_gen_informations.id_informations = sos_femme_premier.informations', 'left');
            $this->db->join('sos_gen_rdv', 'sos_gen_rdv.id_rdv = sos_femme_premier.rdv', 'left');
            $this->db->join('sos_gen_hebergement', 'sos_gen_hebergement.id_hebergement = sos_femme_premier.hebergement', 'left');

            $this->db->where('sos_femme_premier.id_femme', $row->id_femme);
            $query = $this->db->get('sos_femme_premier');
            $row_femme = $query->row();
            return $row_femme->name_emplois . ' - ' . $row_femme->name_emplois_detaille . ' - ' . $row_femme->name_emplois_child_child;
        }

        return '';
    }

    function after_delete($primary_key, $empty = FALSE) {
        $path = 'assets/uploads/files/image_' . $primary_key;
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

    function premier_contact($value, $row) {
        $this->db->where('id_service', $row->service);
        $query = $this->db->get('sos_gen_service');
        if ($query->num_rows == 1) {
            $row_user = $query->row();

            $ret = $row_user->nom_service . '<br />' . strtok($row->premier_contact, " ");
            $this->db->where('id_utilisateur', $row->par);
            $query = $this->db->get('sos_utilisateur');
            if ($query->num_rows == 1) {   // echo var_dump($user).'<br />';
                $row_user = $query->row();
                return $ret . ' par ' . $row_user->prenom_utilisateur . ' ' . $row_user->nom_utilisateur . '</li>';
            }
        }
    }

    function ouvertures($value, $row) {
        // $html = '<ul>';
        $html = '';
        $ouvertures = $this->db->order_by('ouverture_time', 'desc')->get_where('sos_ouverture', array('id_femme' => $row->id_femme), 1)->result_array();
        if ($ouvertures) {
            foreach ($ouvertures as $items) {
                $this->db->where('id_utilisateur', $items['id_utilisateur']);
                $query = $this->db->get('sos_utilisateur');
                if ($query->num_rows == 1) {   // echo var_dump($user).'<br />';
                    $row_user = $query->row();

                    $html.= strtok($items['ouverture_time'], " ") . ' par ' . $row_user->prenom_utilisateur . ' ' . $row_user->nom_utilisateur;
                }
            }
        }
        //$html.='</ul>';
        return $html;
    }

    function enfants($value, $row) {
        $html = '';
        $enfants = $this->db->get_where('sos_kids', array('id_femme' => $row->id_femme))->result_array();
        if (count($enfants) > 0) {
            $html = count($enfants);
        }
        if ($row->enceinte != NULL) {
            $html = $html . '<br />Enceinte de : ' . $row->enceinte . ' mois';
        }

        return $html;
    }

    function before_update($post_array, $primary_key) {

        if ($post_array['nationalite'] == '') {
            $post_array['nationalite_detailles'] = '';
        }
        if ($post_array['situation_familiale'] == '') {
            $post_array['detailles'] = '';
        }

        if ($post_array['situation_familiale'] == '') {
            $post_array['detailles'] = '';
        }

        if ($post_array['emplois'] == ''):
            $post_array['emplois_detailles'] = '';
            $post_array['emplois_more_detailles'] = '';
        elseif ($post_array['emplois_detailles'] == ''):
            $post_array['emplois_more_detailles'] = '';

        endif;

        if ($post_array['logement'] == '') {
            $post_array['logement_detailles'] = '';
        }

        if ($post_array['situation_actuelle'] == '') {
            $post_array['situation_actuelle_detailles'] = '';
        }
        if ($post_array['allocations_familiales'] == '') {
            $post_array['percues_par'] = '';
        }
        if ($post_array['ressources'] == '') {
            $post_array['provenance'] = '';
        }
        return $post_array;
    }

    function insert_after_update($post_array, $primary_key) {
        $data = array(
            'id_femme' => $primary_key,
            'id_utilisateur' => $this->session->userdata('userid')
        );
        $this->db->insert('sos_ouverture', $data);
        $data = array(
            'par' => $this->session->userdata('userid')
        );

        $this->db->where('id_femme', $primary_key);
        $this->db->update('sos_femme_premier', $data);

        return true;
    }

    function insert_after_insert($post_array, $primary_key) {
        $data = array(
            'id_femme' => $primary_key,
            'id_utilisateur' => $this->session->userdata('userid')
        );
        $this->db->insert('sos_ouverture', $data);

        $data = array(
            'par' => $this->session->userdata('userid')
        );
        $this->db->where('id_femme', $primary_key);
        $this->db->update('sos_femme_premier', $data);

        $data = array(
            'id_from_femme' => $primary_key
        );
        $this->db->insert('sos_violences', $data);

        $this->db->insert('sos_psy', $data);

        if (!is_dir($path = 'assets/uploads/files/image_' . $primary_key)) {
            mkdir($path = 'assets/uploads/files/image_' . $primary_key);
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
