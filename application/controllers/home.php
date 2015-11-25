<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

session_start();

class Home extends CI_Controller {

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
            $crud->set_theme('bootstrap');

            $crud->set_table('sos_femme');
            if (!$this->session->userdata('status')) {
                $crud->where('archiver', FALSE);
            }
            $crud->set_subject('Femme');

//relations
            $crud->set_relation('situation_familiale', 'sos_gen_situation_familiale_parrent', 'name_situation_familiale_parrent');
            $crud->set_relation('detailles', 'sos_gen_situation_familiale_child', 'name_situation_familiale_child');

            $crud->set_relation('emplois', 'sos_gen_emplois_parrent', 'name_emplois');
            $crud->set_relation('emplois_detailles', 'sos_gen_emplois_child', 'name_emplois_detaille');
            $crud->set_relation('emplois_more_detailles', 'sos_gen_emplois_child_child', 'name_emplois_child_child');


            $crud->set_relation('age', 'sos_gen_femme_age', 'name_femme_age');

            $crud->set_relation('duree_de_la_relation', 'sos_gen_duree_de_la_relation', 'name_duree_de_la_relation', null, 'id_duree_de_la_relation ASC');


            $crud->set_relation('service', 'sos_gen_service', 'nom_service', null, 'nom_service DESC');
            $crud->set_relation('ville', 'sos_gen_villes', '{nom_ville}, {code_postal}', null, 'nom_ville ASC');
            $crud->set_relation('pays', 'sos_gen_pays', '{nom_pays} - {continent}', null, 'nom_pays ASC');

            $crud->set_relation('nationalite', 'sos_gen_nationalite', 'name_nationalite');
            $crud->set_relation('nationalite_detailles', 'sos_gen_nationalite_detailles', 'name_nationalite_detailles');

            $crud->set_relation('ressources', 'sos_gen_ressources', 'name_ressources');
            $crud->set_relation('provenance', 'sos_gen_provenance', 'name_provenance');




            $crud->set_relation('allocations_familiales', 'sos_gen_allocations_familiales', 'name_allocations_familiales');
            $crud->set_relation('percues_par', 'sos_gen_percues_par', 'name_percues_par');

            $crud->set_relation('logement', 'sos_gen_logement_parent', 'name_logement');
            $crud->set_relation('logement_detailles', 'sos_gen_logement_child', 'name_logement_child');
            $crud->set_relation('situation_actuelle_depuis', 'sos_gen_situation_actuelle_depuis', 'name_situation_actuelle_depuis', null, 'id_situation_actuelle_depuis ASC');
            $crud->set_relation('situation_actuelle', 'sos_gen_situation_actuelle', 'name_situation_actuelle');
            $crud->set_relation('situation_actuelle_detailles', 'sos_gen_situation_actuelle_detailles', 'name_situation_actuelle_detailles');

            $crud->set_relation('partenaire', 'sos_gen_partenaire', 'name_partenaire');

            $crud->set_relation('informations', 'sos_gen_informations', 'name_informations');
            $crud->set_relation('rdv', 'sos_gen_rdv', 'name_rdv');
            $crud->set_relation('hebergement', 'sos_gen_hebergement', 'name_hebergement');
            $crud->set_relation('departs_anterieurs', 'sos_gen_departs_anterieurs', 'name_departs_anterieurs');
            $crud->set_relation('depuis', 'sos_gen_depuis', 'name_depuis');
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
                'main_table' => 'sos_femme',
                'main_table_primary' => 'id_femme',
                "url" => base_url() . 'index.php/home/index/',
                'segment_name' => "situation_familialle"
            );
            $categories_situation_familialle = new gc_dependent_select($crud, $fields_situation_familialle, $config_situation_familialle);
            $js_situation_familialle = $categories_situation_familialle->get_js();


            $fields_nationalite = array(
                'nationalite' => array(
                    'table_name' => 'sos_gen_nationalite',
                    'title' => 'name_nationalite',
                    'relate' => null
                ),
                'nationalite_detailles' => array(
                    'table_name' => 'sos_gen_nationalite_detailles',
                    'title' => 'name_nationalite_detailles',
                    'id_field' => 'id_nationalite_detailles',
                    'relate' => 'id_from_nationalite',
                    'data-placeholder' => 'Préciser'
                )
            );
            $config_nationalite = array(
                'main_table' => 'sos_femme',
                'main_table_primary' => 'id_femme',
                "url" => base_url() . 'index.php/home/index/',
                'segment_name' => "nationalite"
            );
            $categories_nationalite = new gc_dependent_select($crud, $fields_nationalite, $config_nationalite);
            $js_nationalite = $categories_nationalite->get_js();


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
                'main_table' => 'sos_femme',
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
                'main_table' => 'sos_femme',
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
                'main_table' => 'sos_femme',
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
//Visual
            if ($this->session->userdata('status') == '0' OR $this->session->userdata('status') == '4') {
                $crud->columns('prenom', 'nom', 'nom_marital', 'age', 'date_naissance', 'telephone', 'ville', 'nombre_d\'enfants', 'ouvertures', 'premier_contact', 'nombre_passages');
            } else {
                $crud->columns('archiver', 'prenom', 'nom', 'nom_marital', 'age', 'date_naissance', 'telephone', 'ville', 'nombre_d\'enfants', 'ouvertures', 'premier_contact', 'nombre_passages');
            }
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
                    ->display_as('partenaire', 'Orienteur')
                    ->display_as('nombre_passages', 'Nombre passages')
                    ->display_as('arrivee_en_france', 'Arrivée en France')
                    ->display_as('parle_pas_francais', 'Parle pas français')
                    ->display_as('ism', 'Utilisation ISM');

            $crud->order_by('prenom', 'asc');

//unsets
            if ($this->session->userdata('status') == '1' OR $this->session->userdata('status') == '2' OR $this->session->userdata('status') == '3') {
                $crud->unset_edit_fields('service', 'par', 'archiver');
            } else {
                $crud->unset_edit_fields('service', 'premier_contact', 'par');
            }
            $crud->unset_add_fields('par', 'archiver');
            if ($this->session->userdata('status') == '0' OR $this->session->userdata('status') == '4') {
                $crud->unset_delete();
                $crud->unset_export();
                $crud->unset_print();
            }

//Requireds
            $crud->required_fields('service', 'premier_contact', 'prenom');

//Callbacks
            $crud->callback_column('ouvertures', array($this, 'ouvertures'));
            $crud->callback_column('premier_contact', array($this, 'premier_contact'));
            $crud->callback_column('nombre_d\'enfants', array($this, 'enfants'));
            $crud->callback_after_update(array($this, 'insert_after_update'));
            $crud->callback_after_insert(array($this, 'insert_after_insert'));
            $crud->callback_after_delete(array($this, 'after_delete'));
            $crud->callback_column('archiver', array($this, 'archiver'));
            $crud->callback_column('nombre_passages', array($this, 'nombre_passages'));

            $crud->callback_before_update(array($this, 'before_update'));

//field Types


            $crud->field_type('enceinte', 'dropdown', array('1' => '1 mois', '2' => '2 mois', '3' => '3 mois', '4' => '4 mois', '5' => '5 mois', '6' => '6 mois', '7' => '7 mois', '8' => '8 mois', '9' => '9 mois'));

            $crud->field_type('dettes', 'enum', array('OUI', 'NON'));

            $crud->field_type('arrivee_en_france', 'dropdown', range(date("Y"), date("Y") - 50));
            $crud->field_type('parle_pas_francais', 'enum', array('OUI', 'NON'));
            $crud->field_type('ism', 'multiselect', array("1" => "Ponctuel", "2" => "Systématique", "3" => "Venu physiquement"));

// Actions


            $crud->add_action('Enfants', '', 'kids/kids_window', 'ui-icon-person');
            $crud->add_action('Intervention', '', 'demande/demande_window', 'ui-icon-home');
            $crud->add_action('Profil', '', 'show/show_window', 'ui-icon-print');
            $crud->add_action('Violences', '', 'violence/violence_window', 'ui-icon-alert');
            $crud->add_action('Démarches', '', 'demarche/demarche_window', 'ui-icon-gear');
            if ($this->session->userdata('status') != '0') {
                $crud->add_action('Psy', '', 'psy/psy_window', 'ui-icon-comment');
            }
            $crud->add_action('Documents', '', 'upload/upload_window', 'ui-icon-document');


// Renders
            $output = $crud->render();
            $output->output.= $js_situation_familialle . $js_emplois . $js_nationalite . $js_ressources . $js_allocations_familiales . $js_logement . $js_situation_actuelle;

            $menu = new stdClass;
            $menu->n0 = true;
            $menu->status = $this->session->userdata('status');
            $header = $this->navigation->home_f($menu);

            $data = array('output' => $output, 'header' => $header);

            $this->_example_output($data);
        } catch (Exception $e) {
            show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }

    function archiver($value, $row) {
        if ($row->archiver) {   // echo var_dump($user).'<br />';
            return 'OUI';
        }
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
            $ret = $row_user->nom_service . ' ' . strtok($row->premier_contact, " ");
            $html = '';
            $ouvertures = $this->db->order_by('ouverture_time', 'asc')->get_where('sos_ouverture', array('id_femme' => $row->id_femme), 1)->result_array();
            if ($ouvertures) {
                foreach ($ouvertures as $items) {
                    $this->db->where('id_utilisateur', $items['id_utilisateur']);
                    $query = $this->db->get('sos_utilisateur');
                    if ($query->num_rows == 1) {   // echo var_dump($user).'<br />';
                        $row_user = $query->row();

                        $html.= $ret . ' par<br>' . $row_user->prenom_utilisateur . ' ' . $row_user->nom_utilisateur;
                    }
                }
            }
            //$html.='</ul>';
            return $html;
        }
    }

    function nombre_passages($value, $row) {
        // $html = '<ul>';
        $html = '';
        $html = $this->db->query('SELECT * FROM `sos_demande` WHERE `id_from_femme` = ' . $row->id_femme)->num_rows;
        return $html;
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

                    $html.= strtok($items['ouverture_time'], " ") . ' par<br>' . $row_user->prenom_utilisateur . ' ' . $row_user->nom_utilisateur;
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
        $this->db->update('sos_femme', $data);

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
        $this->db->update('sos_femme', $data);

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

