<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/* Author: Jorge Torres
 * Description: Home controller class
 * This is only viewable to those members that are logged in
 */
session_start();

class Show extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->load->helper('string');
        $this->load->library('grocery_CRUD');
        $this->check_isvalidated();
        $this->pdf = false;
        $this->attestation = false;
        $this->load->model('navigation');
        $this->load->library('tbswrapper');
    }

    function _example_output($output = null) {

        $this->load->view('template_show', $output);
    }

    public function make_first($id) {
        $query = $this->db->get_where('sos_femme_premier', array('id_femme' => $id));
        if ($query->num_rows == 0) {
            $query_femme = $this->db->get_where('sos_femme', array('id_femme' => $id));
            $query_first = $query_femme->result_array();
            $this->db->insert('sos_femme_premier', $query_first[0]);
        }
        $this->show_window($id);
    }

    public function delete_first($id) {
        $query = $this->db->get_where('sos_femme_premier', array('id_femme' => $id));
        if ($query->num_rows != 0) {
            $this->db->delete('sos_femme_premier', array('id_femme' => $id));
        }
        $this->show_window($id);
    }

    public function show_window($id, $action = '', $id_attestations = null) {
        $this->action = $action;
        $this->id_attestations = $id_attestations;
        $ouvertures = $this->db->order_by('ouverture_time', 'asc')->get_where('sos_ouverture', array('id_femme' => $id), 1)->result_array();
        if ($ouvertures) {
            foreach ($ouvertures as $items) {
                $this->db->where('id_utilisateur', $items['id_utilisateur']);
                $query = $this->db->get('sos_utilisateur');
                if ($query->num_rows == 1) {   // echo var_dump($user).'<br />';
                    $row_user = $query->row();
                    $nom_user = $row_user->prenom_utilisateur . ' ' . $row_user->nom_utilisateur;
                }
            }
        }



        $this->db->select('sos_femme.prenom, sos_femme.nom, sos_femme.nom_marital , sos_femme.enceinte, sos_femme.telephone, sos_femme.rue,sos_femme.premier_contact, sos_femme.oriente_par_SMS,sos_femme.situation_actuelle_depuis,sos_femme.departs_anterieurs,sos_femme.depuis,sos_femme.profession,sos_femme.dettes, sos_femme.accueil, sos_femme.conseil,sos_femme.orientation,sos_femme.logement_dem,sos_femme.aide_materielle,sos_femme.adresse_postale,sos_femme.accompagnement_exterieur,sos_femme.commentaire,
          sos_gen_depuis.name_depuis,
          sos_gen_departs_anterieurs.name_departs_anterieurs,
          sos_gen_femme_age.name_femme_age,
          sos_gen_service.nom_service,
          sos_gen_villes.nom_ville,sos_gen_villes.code_postal,
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
          sos_gen_hebergement.name_hebergement,
          sos_gen_situation_actuelle_depuis.name_situation_actuelle_depuis');





        $this->db->join('sos_gen_service', 'sos_gen_service.id_service = sos_femme.service', 'left');
        $this->db->join('sos_gen_femme_age', 'sos_gen_femme_age.id_femme_age = sos_femme.age', 'left');
        $this->db->join('sos_gen_departs_anterieurs', 'sos_gen_departs_anterieurs.id_departs_anterieurs = sos_femme.departs_anterieurs', 'left');
        $this->db->join('sos_gen_depuis', 'sos_gen_depuis.id_depuis = sos_femme.depuis', 'left');
        $this->db->join('sos_gen_villes', 'sos_gen_villes.id_ville = sos_femme.ville', 'left');
        $this->db->join('sos_gen_pays', 'sos_gen_pays.id = sos_femme.pays', 'left');
        $this->db->join('sos_gen_nationalite', 'sos_gen_nationalite.id_nationalite = sos_femme.nationalite', 'left');
        $this->db->join('sos_gen_nationalite_detailles', 'sos_gen_nationalite_detailles.id_nationalite_detailles = sos_femme.nationalite_detailles', 'left');
        $this->db->join('sos_gen_situation_familiale_parrent', 'sos_gen_situation_familiale_parrent.id_situation_familiale_parrent = sos_femme.situation_familiale', 'left');
        $this->db->join('sos_gen_situation_familiale_child', 'sos_gen_situation_familiale_child.id_situation_familiale_child = sos_femme.detailles', 'left');
        $this->db->join('sos_gen_emplois_parrent', 'sos_gen_emplois_parrent.id_emplois = sos_femme.emplois', 'left');
        $this->db->join('sos_gen_emplois_child', 'sos_gen_emplois_child.id_emplois_detailles = sos_femme.emplois_detailles', 'left');
        $this->db->join('sos_gen_emplois_child_child', 'sos_gen_emplois_child_child.id_emplois_child_child = sos_femme.emplois_more_detailles', 'left');
        $this->db->join('sos_gen_ressources', 'sos_gen_ressources.id_ressources = sos_femme.ressources', 'left');
        $this->db->join('sos_gen_provenance', 'sos_gen_provenance.id_provenance = sos_femme.provenance', 'left');
        $this->db->join('sos_gen_allocations_familiales', 'sos_gen_allocations_familiales.id_allocations_familiales = sos_femme.allocations_familiales', 'left');
        $this->db->join('sos_gen_percues_par', 'sos_gen_percues_par.id_percues_par = sos_femme.percues_par', 'left');
        $this->db->join('sos_gen_partenaire', 'sos_gen_partenaire.id_partenaire = sos_femme.partenaire', 'left');
        $this->db->join('sos_gen_logement_parent', 'sos_gen_logement_parent.id_logement_parent = sos_femme.logement', 'left');
        $this->db->join('sos_gen_logement_child', 'sos_gen_logement_child.id_logement_child = sos_femme.logement_detailles', 'left');
        $this->db->join('sos_gen_situation_actuelle_depuis', 'sos_gen_situation_actuelle_depuis.id_situation_actuelle_depuis = sos_femme.situation_actuelle_depuis', 'left');


        $this->db->join('sos_gen_situation_actuelle', 'sos_gen_situation_actuelle.id_situation_actuelle = sos_femme.situation_actuelle', 'left');
        $this->db->join('sos_gen_situation_actuelle_detailles', 'sos_gen_situation_actuelle_detailles.id_situation_actuelle_detailles = sos_femme.situation_actuelle_detailles', 'left');

        $this->db->join('sos_gen_informations', 'sos_gen_informations.id_informations = sos_femme.informations', 'left');
        $this->db->join('sos_gen_rdv', 'sos_gen_rdv.id_rdv = sos_femme.rdv', 'left');
        $this->db->join('sos_gen_hebergement', 'sos_gen_hebergement.id_hebergement = sos_femme.hebergement', 'left');

        $this->db->where('sos_femme.id_femme', $id);
        $query = $this->db->get('sos_femme');

        $row_femme = $query->row();
        $row_femme->nom_utilisateur = $nom_user;



        $query = $this->db->get_where('sos_femme_premier', array('id_femme' => $id));
        if ($query->num_rows != 0) {
            $this->db->select('sos_femme_premier.prenom, sos_femme_premier.nom, sos_femme_premier.nom_marital , sos_femme_premier.enceinte, sos_femme_premier.telephone, sos_femme_premier.rue,sos_femme_premier.premier_contact, sos_femme_premier.oriente_par_SMS,sos_femme_premier.situation_actuelle_depuis,sos_femme_premier.departs_anterieurs,sos_femme_premier.depuis,sos_femme_premier.profession,sos_femme_premier.dettes, sos_femme_premier.accueil, sos_femme_premier.conseil,sos_femme_premier.orientation,sos_femme_premier.logement_dem,sos_femme_premier.aide_materielle,sos_femme_premier.adresse_postale,sos_femme_premier.accompagnement_exterieur,sos_femme_premier.commentaire,
          sos_utilisateur.prenom_utilisateur, sos_utilisateur.nom_utilisateur,
          sos_gen_depuis.name_depuis,
          sos_gen_departs_anterieurs.name_departs_anterieurs,
          sos_gen_femme_age.name_femme_age,
          sos_gen_service.nom_service,
          sos_gen_villes.nom_ville,sos_gen_villes.code_postal,
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
          sos_gen_situation_actuelle_depuis.name_situation_actuelle_depuis,
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
            $this->db->join('sos_gen_situation_actuelle_depuis', 'sos_gen_situation_actuelle_depuis.id_situation_actuelle_depuis = sos_femme_premier.situation_actuelle_depuis', 'left');


            $this->db->join('sos_gen_situation_actuelle', 'sos_gen_situation_actuelle.id_situation_actuelle = sos_femme_premier.situation_actuelle', 'left');
            $this->db->join('sos_gen_situation_actuelle_detailles', 'sos_gen_situation_actuelle_detailles.id_situation_actuelle_detailles = sos_femme_premier.situation_actuelle_detailles', 'left');

            $this->db->join('sos_gen_informations', 'sos_gen_informations.id_informations = sos_femme_premier.informations', 'left');
            $this->db->join('sos_gen_rdv', 'sos_gen_rdv.id_rdv = sos_femme_premier.rdv', 'left');
            $this->db->join('sos_gen_hebergement', 'sos_gen_hebergement.id_hebergement = sos_femme_premier.hebergement', 'left');

            $this->db->where('sos_femme_premier.id_femme', $id);
            $query = $this->db->get('sos_femme_premier');
            $row_femme->first = $query->row();
        }




        $this->db->select('sos_kids.prenom,sos_kids.nom,sos_kids.sex,sos_gen_kids_age.name_kids_age,sos_kids.commentaire');
        $this->db->join('sos_gen_kids_age', 'sos_gen_kids_age.id_kids_age = sos_kids.age', 'left');
        $this->db->where('id_femme', $id);
        $query = $this->db->get('sos_kids');
        $row_kids = $query->result_array();
        $row_femme->kids = $row_kids;


        $this->db->select('sos_upload.type_uploads, sos_upload.detailles,sos_upload.date_entry,
            sos_gen_type_uploads_parrent.name_type_uploads_parrent,
            sos_gen_type_uploads_child.name_type_uploads_child');
        $this->db->join('sos_gen_type_uploads_parrent', 'sos_gen_type_uploads_parrent.id_type_uploads_parrent = sos_upload.type_uploads', 'left');
        $this->db->join('sos_gen_type_uploads_child', 'sos_gen_type_uploads_child.id_type_uploads_child = sos_upload.detailles', 'left');
        $this->db->where('id_from_femme', $id);
        $query = $this->db->get('sos_upload');
        $row_uploads = $query->result_array();
        $row_femme->uploads = $row_uploads;


        $this->db->select('sos_demarche.date_evenement, sos_demarche.commentaire,sos_demarche.id_demarche,
            sos_upload.date_entry,
            sos_gen_type_uploads_parrent.name_type_uploads_parrent,
            sos_gen_type_uploads_child.name_type_uploads_child,
            sos_gen_demarche_first.name_demarche_first,
            sos_gen_demarche_second.name_demarche_second,
            sos_gen_demarche_third.name_demarche_third'
        );
        $this->db->join('sos_upload', 'sos_upload.id_upload = sos_demarche.upload', 'left');
        $this->db->join('sos_gen_type_uploads_parrent', 'sos_gen_type_uploads_parrent.id_type_uploads_parrent = sos_upload.type_uploads', 'left');
        $this->db->join('sos_gen_type_uploads_child', 'sos_gen_type_uploads_child.id_type_uploads_child = sos_upload.detailles', 'left');

        $this->db->join('sos_gen_demarche_first', 'sos_gen_demarche_first.id_demarche_first = sos_demarche.first', 'left');
        $this->db->join('sos_gen_demarche_second', 'sos_gen_demarche_second.id_demarche_second = sos_demarche.second', 'left');
        $this->db->join('sos_gen_demarche_third', 'sos_gen_demarche_third.id_demarche_third = sos_demarche.third', 'left');




        $this->db->where('id_from_femme_demarche', $id);



        $query = $this->db->get('sos_demarche');
        $row_demarches = $query->result_array();

        foreach ($row_demarches as $key => $value) {

            $row_query_ordonnance_de_protection = $this->db->query('SELECT c.name_ordonnance_de_protection
          FROM sos_demarche AS a 
          JOIN sos_relation_ordonnance_de_protection AS b ON a.id_demarche=b.id_from_demarche
          JOIN sos_gen_ordonnance_de_protection AS c ON b.id_from_ordonnance_de_protection=c.id_ordonnance_de_protection 
          WHERE a.id_demarche = ' . $value["id_demarche"])->result_array();
            $row_query_ordonnance_de_protection_array = array();
            foreach ($row_query_ordonnance_de_protection as $keys => $values) {
                array_push($row_query_ordonnance_de_protection_array, $values['name_ordonnance_de_protection']);
            }
            $row_demarches[$key]['ordonnance_de_protection'] = $row_query_ordonnance_de_protection_array;



            $row_query_suites_de_plainte = $this->db->query('SELECT c.name_suites_de_plainte
          FROM sos_demarche AS a 
          JOIN sos_relation_suites_de_plainte AS b ON a.id_demarche=b.id_from_demarche
          JOIN sos_gen_suites_de_plainte AS c ON b.id_from_suites_de_plainte=c.id_suites_de_plainte 
          WHERE a.id_demarche = ' . $value["id_demarche"] . ' ORDER BY b.priority ASC')->result_array();
            $row_query_suites_de_plainte_array = array();
            foreach ($row_query_suites_de_plainte as $keys => $values) {
                array_push($row_query_suites_de_plainte_array, $values['name_suites_de_plainte']);
            }
            $row_demarches[$key]['suites_de_plainte'] = $row_query_suites_de_plainte_array;
        }



        $row_femme->demarches = $row_demarches;





        $this->db->select('sos_demande.id_demande,sos_demande.visite,sos_demande.accompagnatrice ,sos_demande.commentaire,sos_demande.commentaire_psy,
          sos_gen_demande_femme.name_demande_femme,
          sos_gen_demande_accueil.name_demande_accueil,
          sos_gen_demande_hbgt.name_demande_hbgt,sos_gen_service.nom_service,');
        $this->db->join('sos_gen_demande_femme', 'sos_gen_demande_femme.id_demande_femme = sos_demande.femme', 'left');
        $this->db->join('sos_gen_service', 'sos_gen_service.id_service = sos_demande.service', 'left');
        $this->db->join('sos_gen_demande_accueil', 'sos_gen_demande_accueil.id_demande_accueil = sos_demande.accueil_dem', 'left');
        $this->db->join('sos_gen_demande_hbgt', 'sos_gen_demande_hbgt.id_demande_hbgt = sos_demande.hbgt', 'left');

        $this->db->where('id_from_femme', $id);
        $this->db->order_by("sos_demande.visite", "desc");
        $query = $this->db->get('sos_demande');
        $row_demande = $query->result_array();

        foreach ($row_demande as $key => $value) {
//$row_demande[$key]['nom_service']=$value["service"];
            $row_query_lieu_ressource = $this->db->query('SELECT c.name_demande_lieu_ressource
          FROM sos_demande AS a 
          JOIN sos_relation_demande_lieu_ressource AS b ON a.id_demande=b.id_from_demande
          JOIN sos_gen_demande_lieu_ressource AS c ON b.id_from_lieu_ressource=c.id_demande_lieu_ressource 
          WHERE a.id_demande = ' . $value["id_demande"])->result_array();

            $row_query_lieu_ressource_array = array();
            foreach ($row_query_lieu_ressource as $keys => $values) {
                array_push($row_query_lieu_ressource_array, $values['name_demande_lieu_ressource']);
            }

            $row_demande[$key]['lieu_ressource'] = $row_query_lieu_ressource_array;

            $row_demande[$key]['accompagnement_specialise'] = array();
            $row_query_accompagnement_specialise = $this->db->query('SELECT c.name_demande_accompagnement_specialise
          FROM sos_demande AS a 
          JOIN sos_relation_demande_accompagnement_specialise AS b ON a.id_demande=b.id_from_demande
          JOIN sos_gen_demande_accompagnement_specialise AS c ON b.id_from_accompagnement_specialise=c.id_demande_accompagnement_specialise
          WHERE a.id_demande = ' . $value["id_demande"])->result_array();
            $row_query_accompagnement_specialise_array = array();
            foreach ($row_query_accompagnement_specialise as $keys => $values) {
                array_push($row_query_accompagnement_specialise_array, $values['name_demande_accompagnement_specialise']);
            }
            $row_demande[$key]['accompagnement_specialise'] = $row_query_accompagnement_specialise_array;

            $this->db->select('sos_upload.file_url ,sos_gen_type_uploads_parrent.name_type_uploads_parrent ,sos_gen_type_uploads_child.name_type_uploads_child');
            $this->db->join('sos_gen_type_uploads_parrent', 'sos_upload.type_uploads = sos_gen_type_uploads_parrent.id_type_uploads_parrent', 'left');
            $this->db->join('sos_gen_type_uploads_child', 'sos_upload.detailles = sos_gen_type_uploads_child.id_type_uploads_child', 'left');
            $this->db->where('id_from_femme', $id);
            $query = $this->db->get('sos_upload');
            $row_update = $query->result_array();
            $row_demande[$key]['update'] = $row_update;

            $query = $this->db->query('SELECT * FROM sos_enfants,sos_kids WHERE id_from_demande = ' . $value["id_demande"] . ' AND sos_enfants.id_from_kids = sos_kids.id_kid');
            $row_enfants = $query->result_array();
            foreach ($row_enfants as $keys => $values) {
                $this->db->select('sos_kids.prenom,sos_kids.nom, sos_kids.age,sos_kids.sex');
                $this->db->where('id_kid', $values['id_from_kids']);
                $query = $this->db->get('sos_kids');
                $row_kids = $query->row();
                $this->db->select('sos_gen_kids_age.name_kids_age');
                $this->db->where('id_kids_age', $row_kids->age);
                $query = $this->db->get('sos_gen_kids_age');
                $row_kids_age = $query->row();
                if (count($row_kids_age) != 0) {
                    $age_enfant = $row_kids_age->name_kids_age;
                } else {
                    $age_enfant = '';
                }
                $row_enfants[$keys]['name'] = $row_kids->prenom . ' ' . $row_kids->nom . ' - ' . $age_enfant . ' - ' . $row_kids->sex;
                $this->db->select('sos_gen_recu.name_recu');
                $this->db->where('id_recu', $values['recu']);
                $query = $this->db->get('sos_gen_recu');
                $row_recu = $query->row();
                $row_enfants[$keys]['recus'] = '';

                if ($row_recu) {
                    $row_enfants[$keys]['recus'] = $row_recu->name_recu;
                }
                $row_query_accompagniement_kid = $this->db->query('SELECT c.name_accompagniement_kid
          FROM sos_enfants AS a 
          JOIN sos_relation_accompagniement_kid AS b ON a.id_enfants=b.id_from_enfants
          JOIN sos_gen_accompagniement_kid AS c ON b.id_from_accompagniement_kid=c.id_accompagniement_kid    
          WHERE a.id_from_kids = ' . $values['id_from_kids'] . ' AND a.id_from_demande =' . $value["id_demande"])->result_array();
                $row_query_accompagniement_kid_array = array();
                foreach ($row_query_accompagniement_kid as $keyss => $valuess) {
                    array_push($row_query_accompagniement_kid_array, $valuess['name_accompagniement_kid']);
                }
                $row_enfants[$keys]['accompagniement_kid'] = $row_query_accompagniement_kid_array;

                $row_query_activite_kid = $this->db->query('SELECT c.name_activite_kid
          FROM sos_enfants AS a 
          JOIN sos_relation_activite_kid AS b ON a.id_enfants=b.id_from_enfants
          JOIN sos_gen_activite_kid AS c ON b.id_from_activite_kid=c.id_activite_kid    
          WHERE a.id_from_kids = ' . $values['id_from_kids'] . ' AND a.id_from_demande =' . $value["id_demande"])->result_array();
                $row_query_activite_kid_array = array();
                foreach ($row_query_activite_kid as $keyss => $valuess) {
                    array_push($row_query_activite_kid_array, $valuess['name_activite_kid']);
                }
                $row_enfants[$keys]['activite_kid'] = $row_query_activite_kid_array;

                $row_enfants[$keys]['commentaire'] = $values['commentaire_enfant'];

                $row_demande[$key]['enfants'] = $row_enfants;
            }
        }
        $row_femme->demande = $row_demande;

        $row_femme->violences = new stdClass;
        $row_query_violences_physiques = $this->db->query('SELECT c.name_violences_physiques
          FROM sos_violences AS a 
          JOIN sos_relation_violences_physiques AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_violences_physiques AS c ON b.id_from_violences_physiques=c.id_violences_physiques 
          WHERE a.id_from_femme = ' . $id . ' ORDER BY b.priority ASC')->result_array();
        $row_query_violences_physiques_array = array();
        foreach ($row_query_violences_physiques as $key => $value) {
            array_push($row_query_violences_physiques_array, $value['name_violences_physiques']);
        }
        $row_femme->violences->violences_physiques = $row_query_violences_physiques_array;

        $row_query_violences_psychologiques = $this->db->query('SELECT c.name_violences_psychologiques
          FROM sos_violences AS a 
          JOIN sos_relation_violences_psychologiques AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_violences_psychologiques AS c ON b.id_from_violences_psychologiques=c.id_violences_psychologiques 
          WHERE a.id_from_femme = ' . $id . ' ORDER BY b.priority ASC')->result_array();
        $row_query_violences_psychologiques_array = array();
        foreach ($row_query_violences_psychologiques as $key => $value) {
            array_push($row_query_violences_psychologiques_array, $value['name_violences_psychologiques']);
        }
        $row_femme->violences->violences_psychologiques = $row_query_violences_psychologiques_array;

        $row_query_violences_sexuelles = $this->db->query('SELECT c.name_violences_sexuelles
          FROM sos_violences AS a 
          JOIN sos_relation_violences_sexuelles AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_violences_sexuelles AS c ON b.id_from_violences_sexuelles=c.id_violences_sexuelles 
          WHERE a.id_from_femme = ' . $id . ' ORDER BY b.priority ASC')->result_array();
        $row_query_violences_sexuelles_array = array();
        foreach ($row_query_violences_sexuelles as $key => $value) {
            array_push($row_query_violences_sexuelles_array, $value['name_violences_sexuelles']);
        }
        $row_femme->violences->violences_sexuelles = $row_query_violences_sexuelles_array;

        $row_query_violences_economiques = $this->db->query('SELECT c.name_violences_economiques
          FROM sos_violences AS a 
          JOIN sos_relation_violences_economiques AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_violences_economiques AS c ON b.id_from_violences_economiques=c.id_violences_economiques 
          WHERE a.id_from_femme = ' . $id . ' ORDER BY b.priority ASC')->result_array();
        $row_query_violences_economiques_array = array();
        foreach ($row_query_violences_economiques as $key => $value) {
            array_push($row_query_violences_economiques_array, $value['name_violences_economiques']);
        }
        $row_femme->violences->violences_economiques = $row_query_violences_economiques_array;

        $row_query_violences_administratives = $this->db->query('SELECT c.name_violences_administratives
          FROM sos_violences AS a 
          JOIN sos_relation_violences_administratives AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_violences_administratives AS c ON b.id_from_violences_administratives=c.id_violences_administratives 
          WHERE a.id_from_femme = ' . $id . ' ORDER BY b.priority ASC')->result_array();
        $row_query_violences_administratives_array = array();
        foreach ($row_query_violences_administratives as $key => $value) {
            array_push($row_query_violences_administratives_array, $value['name_violences_administratives']);
        }
        $row_femme->violences->violences_administratives = $row_query_violences_administratives_array;



        $row_query_violences_sociales = $this->db->query('SELECT c.name_violences_sociales
          FROM sos_violences AS a 
          JOIN sos_relation_violences_sociales AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_violences_sociales AS c ON b.id_from_violences_sociales=c.id_violences_sociales 
          WHERE a.id_from_femme = ' . $id . ' ORDER BY b.priority ASC')->result_array();

        $row_query_violences_sociales_array = array();
        foreach ($row_query_violences_sociales as $key => $value) {
            array_push($row_query_violences_sociales_array, $value['name_violences_sociales']);
        }
        $row_femme->violences->violences_sociales = $row_query_violences_sociales_array;

        $row_query_violences_privations = $this->db->query('SELECT c.name_violences_privations
          FROM sos_violences AS a 
          JOIN sos_relation_violences_privations AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_violences_privations AS c ON b.id_from_violences_privations=c.id_violences_privations 
          WHERE a.id_from_femme = ' . $id . ' ORDER BY b.priority ASC')->result_array();

        $row_query_violences_privations_array = array();
        foreach ($row_query_violences_privations as $key => $value) {
            array_push($row_query_violences_privations_array, $value['name_violences_privations']);
        }
        $row_femme->violences->violences_privations = $row_query_violences_privations_array;


        $row_query_violences_juridiques = $this->db->query('SELECT c.name_violences_juridiques
          FROM sos_violences AS a 
          JOIN sos_relation_violences_juridiques AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_violences_juridiques AS c ON b.id_from_violences_juridiques=c.id_violences_juridiques 
          WHERE a.id_from_femme = ' . $id . ' ORDER BY b.priority ASC')->result_array();
        $row_query_violences_juridiques_array = array();
        foreach ($row_query_violences_juridiques as $key => $value) {
            array_push($row_query_violences_juridiques_array, $value['name_violences_juridiques']);
        }
        $row_femme->violences->violences_juridiques = $row_query_violences_juridiques_array;



        $row_query_violences_enfants_directes = $this->db->query('SELECT c.name_violences_enfants_directes
          FROM sos_violences AS a 
          JOIN sos_relation_violences_enfants_directes AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_violences_enfants_directes AS c ON b.id_from_violences_enfants_directes=c.id_violences_enfants 
          WHERE a.id_from_femme = ' . $id . ' ORDER BY b.priority ASC')->result_array();
        $row_query_violences_enfants_directes_array = array();
        foreach ($row_query_violences_enfants_directes as $key => $value) {
            array_push($row_query_violences_enfants_directes_array, $value['name_violences_enfants_directes']);
        }
        $row_femme->violences->violences_enfants_directes = $row_query_violences_enfants_directes_array;


        $row_query_violences_enfants_indirectes = $this->db->query('SELECT c.name_violences_enfants_indirectes
          FROM sos_violences AS a 
          JOIN sos_relation_violences_enfants_indirectes AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_violences_enfants_indirectes AS c ON b.id_from_violences_enfants_indirectes=c.id_violences_enfants 
          WHERE a.id_from_femme = ' . $id . ' ORDER BY b.priority ASC')->result_array();
        $row_query_violences_enfants_indirectes_array = array();
        foreach ($row_query_violences_enfants_indirectes as $key => $value) {
            array_push($row_query_violences_enfants_indirectes_array, $value['name_violences_enfants_indirectes']);
        }
        $row_femme->violences->violences_enfants_indirectes = $row_query_violences_enfants_indirectes_array;



        $this->db->select('sos_gen_frequence.name_frequence');
        $this->db->join('sos_gen_frequence', 'sos_gen_frequence.id_frequence = sos_violences.frequence', 'left');
        $this->db->where('sos_violences.id_from_femme', $id);
        $query = $this->db->get('sos_violences');
        $row_frequence = $query->row();
        $row_femme->violences->frequence = $row_frequence->name_frequence;

        $this->db->select('sos_gen_commencement.name_commencement');
        $this->db->join('sos_gen_commencement', 'sos_gen_commencement.id_commencement = sos_violences.commencement', 'left');
        $this->db->where('sos_violences.id_from_femme', $id);
        $query = $this->db->get('sos_violences');
        $row_commencement = $query->row();
        $row_femme->violences->commencement = $row_commencement->name_commencement;


        $row_query_de_la_part = $this->db->query('SELECT c.name_de_la_part
          FROM sos_violences AS a 
          JOIN sos_relation_de_la_part AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_de_la_part AS c ON b.id_from_de_la_part=c.id_de_la_part 
          WHERE a.id_from_femme = ' . $id)->result_array();
        $row_query_de_la_part_array = array();
        foreach ($row_query_de_la_part as $key => $value) {
            array_push($row_query_de_la_part_array, $value['name_de_la_part']);
        }
        $row_femme->violences->de_la_part = $row_query_de_la_part_array;



        $row_query_de_la_part_enfants = $this->db->query('SELECT c.name_de_la_part_enfants
          FROM sos_violences AS a 
          JOIN sos_relation_de_la_part_enfants AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_de_la_part_enfants AS c ON b.id_from_de_la_part_enfants=c.id_de_la_part_enfants 
          WHERE a.id_from_femme = ' . $id)->result_array();
        $row_query_de_la_part_enfants_array = array();
        foreach ($row_query_de_la_part_enfants as $key => $value) {
            array_push($row_query_de_la_part_enfants_array, $value['name_de_la_part_enfants']);
        }
        $row_femme->violences->de_la_part_enfants = $row_query_de_la_part_enfants_array;


        $row_query_raisons = $this->db->query('SELECT c.name_raisons
          FROM sos_violences AS a 
          JOIN sos_relation_raisons AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_raisons AS c ON b.id_from_raisons=c.id_raisons 
          WHERE a.id_from_femme = ' . $id)->result_array();

        $row_query_raisons_array = array();
        foreach ($row_query_raisons as $key => $value) {
            array_push($row_query_raisons_array, $value['name_raisons']);
        }
        $row_femme->violences->raisons = $row_query_raisons_array;



        $row_femme->consequences = new stdClass;
        $row_query_consequences_physiques = $this->db->query('SELECT c.name_consequences_physiques
          FROM sos_violences AS a 
          JOIN sos_relation_consequences_physiques AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_consequences_physiques AS c ON b.id_from_consequences_physiques=c.id_consequences_physiques 
          WHERE a.id_from_femme = ' . $id)->result_array();

        $row_query_consequences_physiques_array = array();
        foreach ($row_query_consequences_physiques as $key => $value) {
            array_push($row_query_consequences_physiques_array, $value['name_consequences_physiques']);
        }
        $row_femme->consequences->consequences_physiques = $row_query_consequences_physiques_array;



        $row_query_consequences_psychologiques = $this->db->query('SELECT c.name_consequences_psychologiques
          FROM sos_violences AS a 
          JOIN sos_relation_consequences_psychologiques AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_consequences_psychologiques AS c ON b.id_from_consequences_psychologiques=c.id_consequences_psychologiques 
          WHERE a.id_from_femme = ' . $id)->result_array();
        $row_query_consequences_psychologiques_array = array();
        foreach ($row_query_consequences_psychologiques as $key => $value) {
            array_push($row_query_consequences_psychologiques_array, $value['name_consequences_psychologiques']);
        }
        $row_femme->consequences->consequences_psychologiques = $row_query_consequences_psychologiques_array;



        $row_query_consequences_administratives = $this->db->query('SELECT c.name_consequences_administratives
          FROM sos_violences AS a 
          JOIN sos_relation_consequences_administratives AS b ON a.id_violences=b.id_from_violences
          JOIN sos_gen_consequences_administratives AS c ON b.id_from_consequences_administratives=c.id_consequences_administratives 
          WHERE a.id_from_femme = ' . $id)->result_array();
        $row_query_consequences_administratives_array = array();
        foreach ($row_query_consequences_administratives as $key => $value) {
            array_push($row_query_consequences_administratives_array, $value['name_consequences_administratives']);
        }
        $row_femme->consequences->consequences_administratives = $row_query_consequences_administratives_array;



        $this->db->where('sos_violences.id_from_femme', $id);
        $query = $this->db->get('sos_violences');
        $row_violences = $query->row();
        $row_femme->psy = new stdClass;

        $row_query_troubles_physiologiques = $this->db->query('SELECT c.name_troubles_physiologiques
          FROM sos_psy AS a 
          JOIN sos_relation_troubles_physiologiques AS b ON a.id_psy=b.id_from_psy
          JOIN sos_gen_troubles_physiologiques AS c ON b.id_from_troubles_physiologiques=c.id_troubles_physiologiques 
          WHERE a.id_from_femme = ' . $id)->result_array();
        $row_query_troubles_physiologiques_array = array();
        foreach ($row_query_troubles_physiologiques as $key => $value) {
            array_push($row_query_troubles_physiologiques_array, $value['name_troubles_physiologiques']);
        }
        $row_femme->psy->troubles_physiologiques = $row_query_troubles_physiologiques_array;


        $row_query_troubles_cognitifs = $this->db->query('SELECT c.name_troubles_cognitifs
          FROM sos_psy AS a 
          JOIN sos_relation_troubles_cognitifs AS b ON a.id_psy=b.id_from_psy
          JOIN sos_gen_troubles_cognitifs AS c ON b.id_from_troubles_cognitifs=c.id_troubles_cognitifs 
          WHERE a.id_from_femme = ' . $id)->result_array();
        $row_query_troubles_cognitifs_array = array();
        foreach ($row_query_troubles_cognitifs as $key => $value) {
            array_push($row_query_troubles_cognitifs_array, $value['name_troubles_cognitifs']);
        }
        $row_femme->psy->troubles_cognitifs = $row_query_troubles_cognitifs_array;


        $row_query_troubles_emotionnels = $this->db->query('SELECT c.name_troubles_emotionnels
          FROM sos_psy AS a 
          JOIN sos_relation_troubles_emotionnels AS b ON a.id_psy=b.id_from_psy
          JOIN sos_gen_troubles_emotionnels AS c ON b.id_from_troubles_emotionnels=c.id_troubles_emotionnels 
          WHERE a.id_from_femme = ' . $id)->result_array();
        $row_query_troubles_emotionnels_array = array();
        foreach ($row_query_troubles_emotionnels as $key => $value) {
            array_push($row_query_troubles_emotionnels_array, $value['name_troubles_emotionnels']);
        }
        $row_femme->psy->troubles_emotionnels = $row_query_troubles_emotionnels_array;

        $menu = new stdClass;
        $menu->n1 = true;
        $menu->id = $id;
        $query = $this->db->get_where('sos_femme_premier', array('id_femme' => $id));
        if ($query->num_rows == 0) {
            $menu->first = true;
        } else {
            $menu->first = false;
        }



        $menu->status = $this->session->userdata('status');
        $header = $this->navigation->home_f($menu);
        $data = array('header' => $header, 'row_femme' => $row_femme, 'pdf' => $this->pdf, 'attestation' => $this->attestation);

        if ($this->action == 'pdf') {
            $data['pdf'] = true;
            $html = $this->load->view('template_show', $data, true);
            $filename = $row_femme->prenom . '_' . $row_femme->nom . '_' . $row_femme->nom_marital . '.pdf';
            ini_set('memory_limit', '64M');
            $this->load->library('pdf');
            $pdf = $this->pdf->load();
            $pdf->WriteHTML($html);
            $pdf->Output($filename, 'D');
        }

        if ($this->action == 'attestations') {

            $this->db->where('id_editor', $this->id_attestations);
            $query = $this->db->get('sos_editor');
            if ($query->num_rows == 1) {
                $row_attestation = $query->row();
                //$html = $row_attestation->name_editor;
                $file = $row_attestation->file_url;
                //$jour = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
                //$mois = array("", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
                // $dateDuJour = $jour[date("w")] . " " . date("d") . " " . $mois[date("n")] . " " . date("Y");



                /* $find = array(
                  htmlentities('*|Nom|*', ENT_COMPAT, 'UTF-8'),
                  htmlentities('*|Prénom|*', ENT_COMPAT, 'UTF-8'),
                  htmlentities('*|Nom marital|*', ENT_COMPAT, 'UTF-8'),
                  htmlentities('*|Date actuelle|*', ENT_COMPAT, 'UTF-8'),
                  htmlentities('*|Age|*', ENT_COMPAT, 'UTF-8'),
                  htmlentities('*|Adresse|*', ENT_COMPAT, 'UTF-8'));
                  $replace = array(
                  $row_femme->nom,
                  $row_femme->prenom,
                  $row_femme->nom_marital,
                  $dateDuJour,
                  $row_femme->name_femme_age,
                  $row_femme->rue . ', ' . $row_femme->code_postal . ', ' . $row_femme->nom_ville);


                  $html = str_replace($find, $replace, $html);
                  $filename = $row_attestation->abrev . '_' . $row_femme->prenom . '_' . $row_femme->nom . '_' . $row_femme->nom_marital . '.pdf'; */




                /* $this->tbswrapper->TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
                  $this->tbswrapper->TBS->ResetVarRef(false);

                  $this->tbswrapper->TBS->VarRef['yourname'] = 'Efthymios Pavlidis';
                  $data = array();
                  $data[] = array('rank' => 'A', 'firstname' => 'Sandra', 'name' => 'Hill', 'number' => '1523d', 'score' => 200, 'email_1' => 'sh@tbs.com', 'email_2' => 'sandra@tbs.com', 'email_3' => 's.hill@tbs.com');
                  $data[] = array('rank' => 'A', 'firstname' => 'Roger', 'name' => 'Smith', 'number' => '1234f', 'score' => 800, 'email_1' => 'rs@tbs.com', 'email_2' => 'robert@tbs.com', 'email_3' => 'r.smith@tbs.com');
                  $data[] = array('rank' => 'A', 'firstname' => 'Roger', 'name' => 'Smith', 'number' => '1234f', 'score' => 800, 'email_1' => 'rs@tbs.com', 'email_2' => 'robert@tbs.com', 'email_3' => 'r.smith@tbs.com');
                  $data[] = array('rank' => 'B', 'firstname' => 'William', 'name' => 'Mac Dowell', 'number' => '5491y', 'score' => 130, 'email_1' => 'wmc@tbs.com', 'email_2' => 'william@tbs.com', 'email_3' => 'w.m.dowell@tbs.com');

                  // Other single data items
                  $x_num = 3152.456;
                  $x_pc = 0.2567;
                  $x_dt = mktime(13, 0, 0, 2, 15, 2010);
                  $x_bt = true;
                  $x_bf = false;
                  $this->tbswrapper->TBS->VarRef['x_delete'] = 1;
                  //$x_delete = 1;
                  $filepath = base_url() . 'assets/uploads/models/';
                  $filename = 'demo_ms_word.docx';
                  $template = 'assets/uploads/models/demo_ms_word.docx';
                  $this->tbswrapper->TBS->LoadTemplate($template);

                  $this->tbswrapper->TBS->MergeBlock('a,b', $data);

                  // Merge data in colmuns
                  $data = array(
                  array('date' => '2013-10-13', 'thin' => 156, 'heavy' => 128, 'total' => 284),
                  array('date' => '2013-10-14', 'thin' => 233, 'heavy' => 25, 'total' => 284),
                  array('date' => '2013-10-15', 'thin' => 110, 'heavy' => 412, 'total' => 130),
                  array('date' => '2013-10-16', 'thin' => 258, 'heavy' => 522, 'total' => 258),
                  );
                  $this->tbswrapper->TBS->MergeBlock('c', $data);
                  $ChartNameOrNum = 'a nice chart'; // Title of the shape that embeds the chart
                  $SeriesNameOrNum = 'Series 2';
                  $NewValues = array(array('Category A', 'Category B', 'Category C', 'Category D'), array(3, 1.1, 4.0, 3.3));
                  $NewLegend = "Updated series 2";
                  $this->tbswrapper->TBS->PlugIn(OPENTBS_CHART, $ChartNameOrNum, $SeriesNameOrNum, $NewValues, $NewLegend);

                  // Delete comments
                  $this->tbswrapper->TBS->PlugIn(OPENTBS_DELETE_COMMENTS);
                  $this->tbswrapper->TBS->Show(OPENTBS_DOWNLOAD, 'done.docx'); */






                $this->tbswrapper->TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
                $this->tbswrapper->TBS->ResetVarRef(false);

                if ($row_femme->prenom == null) {
                    $this->tbswrapper->TBS->VarRef['prenom'] = '';
                } else {
                    $this->tbswrapper->TBS->VarRef['prenom'] = $row_femme->prenom;
                }
                if ($row_femme->nom == null) {
                    $this->tbswrapper->TBS->VarRef['nom'] = '';
                } else {
                    $this->tbswrapper->TBS->VarRef['nom'] = $row_femme->nom;
                }
                if ($row_femme->nom_marital == null) {
                    $this->tbswrapper->TBS->VarRef['nom_marital'] = '';
                } else {
                    $this->tbswrapper->TBS->VarRef['nom_marital'] = $row_femme->nom_marital;
                }


                $this->tbswrapper->TBS->VarRef['utilisateur'] = $this->session->userdata('fname') . ' ' . $this->session->userdata('lname');
                $this->tbswrapper->TBS->VarRef['titre'] = $this->session->userdata('titre');
                $this->tbswrapper->TBS->VarRef['premier_contact'] = date("d-m-Y", strtotime($row_femme->premier_contact));
                $this->tbswrapper->TBS->VarRef['violences_physiques'] = implode(", ", $row_femme->violences->violences_physiques);
                $this->tbswrapper->TBS->VarRef['violences_psychologiques'] = implode(", ", $row_femme->violences->violences_psychologiques);
                $this->tbswrapper->TBS->VarRef['violences_sexuelles'] = implode(", ", $row_femme->violences->violences_sexuelles);




                $template = 'assets/uploads/models/' . $file;
                $this->tbswrapper->TBS->LoadTemplate($template, OPENTBS_ALREADY_UTF8);


                $filename = $row_attestation->abrev . '_' . $row_femme->prenom . '_' . $row_femme->nom . '_' . $row_femme->nom_marital . '.docx';
                $this->tbswrapper->TBS->Show(OPENTBS_DOWNLOAD, $filename);



                //$TBS = new Tbswrapper();
                //$TBS->LoadTemplate($filepath . $filename, OPENTBS_ALREADY_UTF8);
                //$TB->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);


                /* ini_set('memory_limit', '32M');
                  $this->load->library('pdf');
                  $pdf = $this->pdf->load();
                  $pdf->WriteHTML($html);
                  $pdf->Output($filename, 'D'); */
            }
        }

        $this->_example_output($data);
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
