<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/* Author: Jorge Torres
 * Description: Login model class
 */

class Statistiques_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->load->helper('string');
        $this->load->library('grocery_CRUD');
        $this->load->library('tbswrapper');
    }

    public function validate() {

        $rapport = $this->input->post('rapport');
        /* $start = $this->input->post('anee') . '-' . $this->input->post('premier_mois');
          $date = new DateTime($start);
          $timestamp_start = $date->getTimestamp();
          $end = $this->input->post('anee') . '-' . $this->input->post('dernier_mois');
          $date = new DateTime($end);
          $timestamp_end = $date->getTimestamp();
          if ($timestamp_start <= $timestamp_end) { */
        $this->db->where('id_editor', $rapport);
        $query = $this->db->get('sos_editor');
        $row_rapport = $query->row();
        $file = $row_rapport->file_url;
        $this->tbswrapper->TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN);
        $this->tbswrapper->TBS->ResetVarRef(false);
        $template = 'assets/uploads/models/' . $file;
        $this->tbswrapper->TBS->SetOption('noerr', true);
        $this->tbswrapper->TBS->VarRef['yourname'] = 'Efthymios Pavlidis';
        $this->tbswrapper->TBS->VarRef['x_delete'] = 1;
        $this->tbswrapper->TBS->LoadTemplate($template, OPENTBS_ALREADY_UTF8);
        //$this->tbswrapper->TBS->PlugIn(OPENTBS_DEBUG_INFO );
        $this->r_service = '';
        $this->r_p_service = '';
        $this->s_service = '';
        $n_service = '';
        $this->r_femme_service = '';
        $this->tbswrapper->TBS->VarRef['service'] = '';
        $this->tbswrapper->TBS->VarRef['chart'] = '';
        $this->tbswrapper->TBS->VarRef['ouvrables'] = '';
        $this->tbswrapper->TBS->VarRef['ouvrables_enfants'] = '';
        $string = $this->tbswrapper->TBS->Source;
        $pattern = "/{[^}]*}/";
        $subject = $string;
        $table_array = array();
        $chart_array = array();
        $this->ouvrables_array = array();
        $this->ouvrables_enfants_array = array();
        preg_match_all($pattern, $subject, $matches);
        foreach ($matches[0] as $value) {
            if (strpos($value, 'service')) {
                $service = ltrim(stristr($this->findText('{', '}', $value), ':'), ':');
                $this->db->where('id_service', $service);
                $query = $this->db->get('sos_gen_service');
                if ($query->num_rows == 1) {
                    $row_service = $query->row();
                    $this->r_femme_service = ' AND sos_femme.service=' . $row_service->id_service . ' ';
                    $this->r_p_service = ' AND sos_femme_premier.service=' . $row_service->id_service . ' ';
                    //a voir               
                    $this->r_service = ' AND sos_demande.service=' . $row_service->id_service . ' ';
                    $this->r_p_service = ' AND sos_femme_premier.service=' . $row_service->id_service . ' ';
                    $this->tbswrapper->TBS->VarRef['service'] = $row_service->nom_service;
                    $n_service = '_' . $service;
                    $this->s_service = $service;
                }
            }
            if (strpos($value, 'chart')) {
                $chart = ltrim(stristr($this->findText('{', '}', $value), ':'), ':');
                $chart_array = explode(",", $chart);
            }
            if (strpos($value, 'table')) {
                $chart = ltrim(stristr($this->findText('{', '}', $value), ':'), ':');
                array_push($table_array, $chart);
            }
            if (strpos($value, 'ouvrables')) {
                $ouvrables = ltrim(stristr($this->findText('{', '}', $value), ':'), ':');
                $this->ouvrables_array = explode(",", $ouvrables);
            }
            if (strpos($value, 'ouvrables_enfants')) {
                $ouvrables_enfants = ltrim(stristr($this->findText('{', '}', $value), ':'), ':');
                $this->ouvrables_enfants_array = explode(",", $ouvrables_enfants);
            }
        }
        $startmonth = intval($this->input->post('premier_mois'));
        $endmonth = intval($this->input->post('dernier_mois'));
        $this->year = intval($this->input->post('anee'));
        //always performe
        $total_femmes = $this->db->query('SELECT count(*) as `num`
            FROM `sos_femme`
            WHERE ( (year(`sos_femme`.`premier_contact`)=' . $this->year . ')' . $this->r_femme_service . ')')->result();
        if (count($total_femmes) > 0) {
            $this->total_femmes_var = $total_femmes[0]->num;
        } else {
            $this->total_femmes_var = 0;
        }
        //always performe

        $this->total_femmes_var_visites = intval($this->db->query('select count(*) as num from (SELECT count(*) FROM sos_demande WHERE 
year(sos_demande.visite)=' . $this->year . $this->r_service . ' group by sos_demande.id_from_femme) groups')->first_row()->num);

        $this->total_femmes_var_visites_avec_enfant = intval($this->db->query('SELECT count(*) as num
FROM (select count(*) from sos_demande 
INNER JOIN sos_femme ON sos_demande.id_from_femme = sos_femme.id_femme 
INNER JOIN sos_kids ON sos_femme.id_femme = sos_kids.id_femme 
where year(sos_demande.visite)=' . $this->year . $this->r_service .
                        ' group by sos_demande.id_from_femme) groups')->first_row()->num);

// gros variables
        $this->total_femmes_acc = intval($this->db->query('select count(*) as num from (SELECT count(*) FROM sos_femme WHERE 
year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service . ' group by sos_femme.id_femme) groups')->first_row()->num);
        $this->total_femmes_visites = intval($this->db->query('select count(*) as num from (SELECT count(*) FROM sos_demande WHERE 
year(sos_demande.visite)=' . $this->year . $this->r_service . ' group by sos_demande.id_from_femme) groups')->first_row()->num);
        $this->total_femmes_acc_avec_enfant = intval($this->db->query('select sum(num) numm from '
                        . '(SELECT count(*) as num FROM sos_femme '
                        . 'inner JOIN sos_kids  ON sos_femme.id_femme = sos_kids.id_femme '
                        . 'where year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service . ' group by sos_kids.id_femme) groups')->first_row()->numm);

        $this->total_femmes_visites_avec_enfant = intval($this->db->query('SELECT count(*) as num
FROM (select count(*) from sos_demande 
INNER JOIN sos_femme ON sos_demande.id_from_femme = sos_femme.id_femme 
INNER JOIN sos_kids ON sos_femme.id_femme = sos_kids.id_femme 
where year(sos_demande.visite)=' . $this->year . $this->r_service .
                        ' group by sos_demande.id_from_femme) groups')->first_row()->num);

        $this->total_visites_enfant = intval($this->db->query('SELECT count(*) as num'
                        . ' FROM sos_demande,sos_enfants where '
                        . 'year(sos_demande.visite) = ' . $this->year . $this->r_service .
                        ' and sos_enfants.id_from_demande= sos_demande.id_demande '
                        . 'and sos_enfants.recu is not null ORDER BY `id_from_femme` ASC ')->first_row()->num);

//always performe
        $data = array();
        $query = $this->db->get('sos_gen_demande_femme');
        $row_gen = $query->result_array();

        foreach ($row_gen as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`visite`) AS `Year`,
            COUNT(CASE WHEN MONTH(`visite`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`visite`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`visite`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`visite`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`visite`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`visite`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`visite`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`visite`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`visite`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`visite`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`visite`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`visite`) = 12  THEN 1 END) AS `dec`
    FROM sos_demande,sos_femme WHERE femme = ' . $value['id_demande_femme'] . ' ' . $this->r_service . ' AND sos_femme.id_femme=sos_demande.id_from_femme AND YEAR(`visite`)=' . $this->year . ' GROUP BY YEAR(`visite`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'femmes' => $value['name_demande_femme'],
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            } else {
                $data[] = array(
                    'femmes' => $value['name_demande_femme'],
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                );
            }
        }
        $this->data_total = array();
        $this->data_total[0] = $this->array_add($data[0], $data[1], $data[2], $data[3]);
        $this->data_total[0]['totals'] = 'Total femmes';
        $this->data_total_femmes = array();
        $this->data_total_femmes[0] = $this->data_total[0];
        $data[1] = array_map(function($el) {
            return $el * 1;
        }, $data[1]);
        $data[2] = array_map(function($el) {
            return $el * 2;
        }, $data[2]);
        $data[3] = array_map(function($el) {
            return $el * 3;
        }, $data[3]);
        $data[4] = array_map(function($el) {
            return $el * 4;
        }, $data[4]);
        $this->data_total[1] = $this->array_add($data[1], $data[2], $data[3], $data[4]);
        $this->data_total[1]['totals'] = 'Total enfants';
        $this->data_totals_totals = array();
        $this->data_totals_totals[] = $this->array_add($this->data_total[0], $this->data_total[1]);



        $data = array();
        $result = $this->db->query('SELECT  YEAR(`visite`) AS `Year`,
            COUNT(CASE WHEN MONTH(`visite`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`visite`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`visite`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`visite`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`visite`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`visite`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`visite`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`visite`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`visite`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`visite`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`visite`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`visite`) = 12  THEN 1 END) AS `dec`
   FROM sos_demande,sos_femme WHERE accompagnatrice !="" ' . $this->r_service . ' AND sos_femme.id_femme=sos_demande.id_from_femme AND YEAR(`visite`)=' . $this->year . ' GROUP BY YEAR(`visite`)')->result();

        $data[] = array(
            'jan' => $result[0]->jan,
            'fev' => $result[0]->fev,
            'mar' => $result[0]->mar,
            'avr' => $result[0]->avr,
            'mai' => $result[0]->mai,
            'juin' => $result[0]->juin,
            'juil' => $result[0]->juil,
            'aout' => $result[0]->aout,
            'sept' => $result[0]->sept,
            'oct' => $result[0]->oct,
            'nov' => $result[0]->nov,
            'dec' => $result[0]->dec,
            'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
        );

        $this->data_totals_persones = array();
        $this->data_totals_persones[] = $this->array_add($data[0], $this->data_totals_totals[0]);
// always performe
        $data = array();
        $this->total_data_personnes_accueillies = array();
        $result = $this->db->query('select distinct `sos_demande`.`visite` AS `visite`, count(0) AS `nombre` 
            from `sos_demande`,sos_femme where year(`sos_demande`.`visite`)=' . $this->year . ' and sos_femme.id_femme =sos_demande.id_from_femme ' . $this->r_service . ' group by sos_demande.visite order by nombre DESC ')->result();
        if (count($result) > 0) {
            $max = intval($result[0]->nombre);
        } else {
            $max = 0;
        }
        foreach (range(1, $max) as $number) {
            $list_months = array();
            for ($i = 1; $i <= 12; $i++) {
                $month_num = 0;
                foreach ($result as $key => $value) {
                    if (intval(substr($value->visite, 5, 2)) == $i) {
                        $month_num = $month_num + 1;
                    }
                }
                $list_months[] = $month_num;
            }
            $data[] = array(
                'nombre' => $number,
                'jan' => $list_months[0],
                'fev' => $list_months[1],
                'mar' => $list_months[2],
                'avr' => $list_months[3],
                'mai' => $list_months[4],
                'juin' => $list_months[5],
                'juil' => $list_months[6],
                'aout' => $list_months[7],
                'sept' => $list_months[8],
                'oct' => $list_months[9],
                'nov' => $list_months[10],
                'dec' => $list_months[11],
                'total' => ($list_months[0] + $list_months[1] + $list_months[2] + $list_months[3] + $list_months[4] + $list_months[5] + $list_months[6] + $list_months[7] + $list_months[8] + $list_months[9] + $list_months[10] + $list_months[11])
            );
            $c_data = count($data) - 1;
            array_push($this->total_data_personnes_accueillies, intval($data[$c_data]['total']));
        }
        //***************

        foreach ($table_array as $value) {
            $funcname = "table_" . $value;
            $this->$funcname();
        }

        // Other single data items
        $x_num = 3152.456;
        $x_pc = 0.2567;
        $x_dt = mktime(13, 0, 0, 2, 15, 2010);
        $x_bt = true;
        $x_bf = false;
        // Merge data in colmuns
        $data = array(
            array(
                'date' => '2013-10-13',
                'thin' => 156,
                'heavy' => 128,
                'total' => 284
            ),
            array(
                'date' => '2013-10-14',
                'thin' => 233,
                'heavy' => 25,
                'total' => 284
            ),
            array(
                'date' => '2013-10-15',
                'thin' => 110,
                'heavy' => 412,
                'total' => 130
            ),
            array(
                'date' => '2013-10-16',
                'thin' => 258,
                'heavy' => 522,
                'total' => 258
            )
        );
        $this->tbswrapper->TBS->MergeBlock('c', $data);
        $ChartNameOrNum = 'Total de femmes lines'; // Title of the shape that embeds the chart
        $SeriesNameOrNum = 'Frequantation';
        $NewValues = array(
            array(
                'JAN',
                'FEV',
                'MAR',
                'AVR',
                'MAI',
                'JUIN',
                'JUIL',
                'AOUT',
                'SEPT',
                'OCT',
                'NOV',
                'DEC'
            ),
            array(
                $this->data_total[0]['jan'],
                $this->data_total[0]['fev'],
                $this->data_total[0]['mar'],
                $this->data_total[0]['avr'],
                $this->data_total[0]['mai'],
                $this->data_total[0]['juin'],
                $this->data_total[0]['juil'],
                $this->data_total[0]['aout'],
                $this->data_total[0]['sept'],
                $this->data_total[0]['oct'],
                $this->data_total[0]['nov'],
                $this->data_total[0]['dec']
            )
        );
        $this->tbswrapper->TBS->PlugIn(OPENTBS_CHART, $ChartNameOrNum, $SeriesNameOrNum, $NewValues);
        $ChartNameOrNum = 'Total de femmes bars'; // Title of the shape that embeds the chart
        $SeriesNameOrNum = 'Frequantation';
        $NewValues = array(
            array(
                'JAN',
                'FEV',
                'MAR',
                'AVR',
                'MAI',
                'JUIN',
                'JUIL',
                'AOUT',
                'SEPT',
                'OCT',
                'NOV',
                'DEC'
            ),
            array(
                $this->data_total[0]['jan'],
                $this->data_total[0]['fev'],
                $this->data_total[0]['mar'],
                $this->data_total[0]['avr'],
                $this->data_total[0]['mai'],
                $this->data_total[0]['juin'],
                $this->data_total[0]['juil'],
                $this->data_total[0]['aout'],
                $this->data_total[0]['sept'],
                $this->data_total[0]['oct'],
                $this->data_total[0]['nov'],
                $this->data_total[0]['dec']
            )
        );
        $this->tbswrapper->TBS->PlugIn(OPENTBS_CHART, $ChartNameOrNum, $SeriesNameOrNum, $NewValues);
        $villes_array = array();
        $totalvisits = array();
        $totalvisits_demande = array();
        $data = array();
        $query = $this->db->get('sos_gen_villes');
        $row_gen_villes = $query->result_array();
        foreach ($row_gen_villes as $key => $value) {
            if (in_array($value['id_ville'], $chart_array)) {
                // $result = $this->db->query('SELECT count(*) as `num`
                // FROM `sos_femme`
                //WHERE ((`sos_femme`.`ville` =' . $value['id_ville'] . ') AND (year(`sos_femme`.`premier_contact`)=' . $this->year . ')' . $this->r_femme_service . ')')->result();
                $result = $this->db->query('select  count(*) AS `num` 
            from `sos_demande`,sos_femme where year(`sos_demande`.`visite`)=' . $this->year . ' and sos_femme.id_femme =sos_demande.id_from_femme ' . $this->r_service . ' and `sos_femme`.`ville` =' . $value['id_ville'] . ' group by sos_demande.id_from_femme')->result();
                if (count($result) > 0) {
                    array_push($villes_array, $value['nom_ville']);
                    array_push($totalvisits, count($result));
                } else {
                    array_push($villes_array, $value['nom_ville']);
                    array_push($totalvisits, 0);
                }
                $result_new = $this->db->query('select  count(*) AS `num` 
            from `sos_demande`,sos_femme where year(`sos_demande`.`visite`)=' . $this->year . ' and sos_femme.id_femme =sos_demande.id_from_femme ' . $this->r_service . ' and `sos_femme`.`ville` =' . $value['id_ville'])->result();
                if (count($result_new) > 0) {
                    array_push($totalvisits_demande, $result_new[0]->num);
                } else {
                    array_push($totalvisits_demande, 0);
                }
            }
        }
        $ChartNameOrNum = '4'; // Title of the shape that embeds the chart
        $SeriesNameOrNum = 'Total visites';
        $NewValues = array(
            $villes_array,
            $totalvisits_demande
        );
        $this->tbswrapper->TBS->PlugIn(OPENTBS_CHART, $ChartNameOrNum, $SeriesNameOrNum, $NewValues);
        $ChartNameOrNum = '4'; // Title of the shape that embeds the chart
        $SeriesNameOrNum = 'Total femmes';
        $NewValues = array(
            $villes_array,
            $totalvisits
        );
        $this->tbswrapper->TBS->PlugIn(OPENTBS_CHART, $ChartNameOrNum, $SeriesNameOrNum, $NewValues);
        $result = $this->db->query('select distinct `sos_demande`.`visite` AS `visite`, count(0) AS `nombre` 
            from `sos_demande`,sos_femme where year(`sos_demande`.`visite`)=' . $this->year . ' and sos_femme.id_femme =sos_demande.id_from_femme ' . $this->r_service . ' group by sos_demande.visite order by nombre DESC ')->result();
        if (count($result) > 0) {
            $max = intval($result[0]->nombre);
        } else {
            $max = 0;
        }
        $ChartNameOrNum = 'Nombre de personnes accueillies par jour'; // Title of the shape that embeds the chart
        $SeriesNameOrNum = 'Frequantation';
        $NewValues = array(
            range(1, $max),
            $this->total_data_personnes_accueillies
        );
        $this->tbswrapper->TBS->PlugIn(OPENTBS_CHART, $ChartNameOrNum, $SeriesNameOrNum, $NewValues);
        //$this->tbswrapper->TBS->PlugIn(OPENTBS_DEBUG_XML_CURRENT );
        // $crud->field_type('interlocuteur', 'dropdown', array('0' => '', '1' => 'Elle-même', '2' => 'Professionnel', '3' => 'Entourage'));
        //***********************************************************************************************************
        $filename = $row_rapport->abrev . '.docx';
        $this->tbswrapper->TBS->PlugIn(OPENTBS_DELETE_COMMENTS);
        $this->tbswrapper->TBS->Show(OPENTBS_DOWNLOAD, $filename);
        return true;
        /* } else {
          return false;
          } */
    }

    private function table_1() {
        // PERSONNES RECUES DANS LE CADRE DU LIEU D'ACCUEIL 
        $data = array();
        $query = $this->db->get('sos_gen_demande_femme');
        $row_gen = $query->result_array();

        foreach ($row_gen as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`visite`) AS `Year`,
            COUNT(CASE WHEN MONTH(`visite`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`visite`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`visite`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`visite`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`visite`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`visite`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`visite`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`visite`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`visite`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`visite`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`visite`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`visite`) = 12  THEN 1 END) AS `dec`
    FROM sos_demande,sos_femme WHERE femme = ' . $value['id_demande_femme'] . ' ' . $this->r_service . ' AND sos_femme.id_femme=sos_demande.id_from_femme AND YEAR(`visite`)=' . $this->year . ' GROUP BY YEAR(`visite`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'femmes' => $value['name_demande_femme'],
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            } else {
                $data[] = array(
                    'femmes' => $value['name_demande_femme'],
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                );
            }
        }

        $this->tbswrapper->TBS->MergeBlock('femmes', $data);

        $this->tbswrapper->TBS->MergeBlock('totals', $this->data_total);
        $this->tbswrapper->TBS->MergeBlock('totals_acc', $this->data_totals_totals);
        /* Accompagniatrice
         * 
         */
        $data = array();
        $result = $this->db->query('SELECT  YEAR(`visite`) AS `Year`,
            COUNT(CASE WHEN MONTH(`visite`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`visite`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`visite`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`visite`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`visite`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`visite`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`visite`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`visite`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`visite`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`visite`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`visite`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`visite`) = 12  THEN 1 END) AS `dec`
   FROM sos_demande,sos_femme WHERE accompagnatrice !="" ' . $this->r_service . ' AND sos_femme.id_femme=sos_demande.id_from_femme AND YEAR(`visite`)=' . $this->year . ' GROUP BY YEAR(`visite`)')->result();
        $data[] = array(
            'jan' => $result[0]->jan,
            'fev' => $result[0]->fev,
            'mar' => $result[0]->mar,
            'avr' => $result[0]->avr,
            'mai' => $result[0]->mai,
            'juin' => $result[0]->juin,
            'juil' => $result[0]->juil,
            'aout' => $result[0]->aout,
            'sept' => $result[0]->sept,
            'oct' => $result[0]->oct,
            'nov' => $result[0]->nov,
            'dec' => $result[0]->dec,
            'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
        );

        $this->tbswrapper->TBS->MergeBlock('accompagnatrices', $data);
        /* Total persones
         * 
         */
        $this->tbswrapper->TBS->MergeBlock('total_persones', $this->data_totals_persones);
    }

    private function table_2() {


// //Nombre de personnes accueillies
        $data = array();
        $total_data_personnes_accueillies = array();
        $result = $this->db->query('select distinct `sos_demande`.`visite` AS `visite`, count(0) AS `nombre` 
            from `sos_demande`,sos_femme where year(`sos_demande`.`visite`)=' . $this->year . ' and sos_femme.id_femme =sos_demande.id_from_femme ' . $this->r_service . ' group by sos_demande.visite order by nombre DESC ')->result();
        if (count($result) > 0) {
            $max = intval($result[0]->nombre);
        } else {
            $max = 0;
        }
        foreach (range(1, $max) as $number) {
            $list_months = array();
            for ($i = 1; $i <= 12; $i++) {
                $month_num = 0;
                foreach ($result as $key => $value) {
                    if (intval(substr($value->visite, 5, 2)) == $i) {
                        $month_num = $month_num + 1;
                    }
                }
                $list_months[] = $month_num;
            }
            $data[] = [
                'nombre' => $number,
                'jan' => $list_months[0],
                'fev' => $list_months[1],
                'mar' => $list_months[2],
                'avr' => $list_months[3],
                'mai' => $list_months[4],
                'juin' => $list_months[5],
                'juil' => $list_months[6],
                'aout' => $list_months[7],
                'sept' => $list_months[8],
                'oct' => $list_months[9],
                'nov' => $list_months[10],
                'dec' => $list_months[11],
                'total' => ($list_months[0] + $list_months[1] + $list_months[2] + $list_months[3] + $list_months[4] + $list_months[5] + $list_months[6] + $list_months[7] + $list_months[8] + $list_months[9] + $list_months[10] + $list_months[11])
            ];
            $c_data = count($data) - 1;
            array_push($total_data_personnes_accueillies, intval($data[$c_data]['total']));
        }
        $data_ouvrables = array();
        $data_ouvrables[] = [
            'jan' => $this->ouvrables_array[0],
            'fev' => $this->ouvrables_array[1],
            'mar' => $this->ouvrables_array[2],
            'avr' => $this->ouvrables_array[3],
            'mai' => $this->ouvrables_array[4],
            'juin' => $this->ouvrables_array[5],
            'juil' => $this->ouvrables_array[6],
            'aout' => $this->ouvrables_array[7],
            'sept' => $this->ouvrables_array[8],
            'oct' => $this->ouvrables_array[9],
            'nov' => $this->ouvrables_array[10],
            'dec' => $this->ouvrables_array[11],
            'total' => ($this->ouvrables_array[0] + $this->ouvrables_array[1] + $this->ouvrables_array[2] + $this->ouvrables_array[3] + $this->ouvrables_array[4] + $this->ouvrables_array[5] + $this->ouvrables_array[6] + $this->ouvrables_array[7] + $this->ouvrables_array[8] + $this->ouvrables_array[9] + $this->ouvrables_array[10] + $this->ouvrables_array[11])
        ];
        $this->tbswrapper->TBS->MergeBlock('nombre_jours', $data_ouvrables);
        $data_mayenne = array();
        $data_mayenne[] = ['jan' => $this->mydivide($this->data_totals_persones[0]['jan'], $data_ouvrables[0]['jan']),
            'fev' => $this->mydivide($this->data_totals_persones[0]['fev'], $data_ouvrables[0]['fev']),
            'mar' => $this->mydivide($this->data_totals_persones[0]['mar'], $data_ouvrables[0]['mar']),
            'avr' => $this->mydivide($this->data_totals_persones[0]['avr'], $data_ouvrables[0]['avr']),
            'mai' => $this->mydivide($this->data_totals_persones[0]['mai'], $data_ouvrables[0]['mai']),
            'juin' => $this->mydivide($this->data_totals_persones[0]['juin'], $data_ouvrables[0]['juin']),
            'juil' => $this->mydivide($this->data_totals_persones[0]['juil'], $data_ouvrables[0]['juil']),
            'aout' => $this->mydivide($this->data_totals_persones[0]['aout'], $data_ouvrables[0]['aout']),
            'sept' => $this->mydivide($this->data_totals_persones[0]['sept'], $data_ouvrables[0]['sept']),
            'oct' => $this->mydivide($this->data_totals_persones[0]['oct'], $data_ouvrables[0]['oct']),
            'nov' => $this->mydivide($this->data_totals_persones[0]['nov'], $data_ouvrables[0]['nov']),
            'dec' => $this->mydivide($this->data_totals_persones[0]['dec'], $data_ouvrables[0]['dec']),
            'total' => $this->mydivide($this->data_totals_persones[0]['total'], $data_ouvrables[0]['total'])
        ];
        $this->tbswrapper->TBS->MergeBlock('moyenne_jour', $data_mayenne);
        $this->tbswrapper->TBS->MergeBlock('total_persones', $this->data_totals_persones);
    }

    private function table_3() {
        // TYPES D’ACCUEIL
        $data = [];
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
   FROM sos_femme WHERE YEAR(`premier_contact`)=' . $this->year . ' ' . $this->r_femme_service . ' GROUP BY YEAR(`premier_contact`)')->result();

        $data[] = ['jan' => $result[0]->jan,
            'fev' => $result[0]->fev,
            'mar' => $result[0]->mar,
            'avr' => $result[0]->avr,
            'mai' => $result[0]->mai,
            'juin' => $result[0]->juin,
            'juil' => $result[0]->juil,
            'aout' => $result[0]->aout,
            'sept' => $result[0]->sept,
            'oct' => $result[0]->oct,
            'nov' => $result[0]->nov,
            'dec' => $result[0]->dec,
            'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
        ];

        $this->tbswrapper->TBS->MergeBlock('premier_contact', $data);
        $data_accompagnement[] = $this->array_subtract($this->data_total_femmes[0], $data[0]);
        $this->tbswrapper->TBS->MergeBlock('accompagnement', $data_accompagnement);
        $this->tbswrapper->TBS->MergeBlock('total_femmes', $this->data_total_femmes);
    }

    private function table_4() {
        // TRAVAIL SOCIAL
        $data = [];
        $query = $this->db->get('sos_gen_demande_accompagnement_specialise');
        $row_gen_demande_accompagnement_specialise = $query->result_array();
        foreach ($row_gen_demande_accompagnement_specialise as $key => $value) {
            $list_months = [];
            for ($i = 1; $i <= 12; $i++) {
                $this->db->select('sos_demande.visite,sos_demande.service,sos_demande.id_from_femme,sos_demande.id_demande,sos_relation_demande_accompagnement_specialise.id_from_accompagnement_specialise');
                $this->db->join('sos_relation_demande_accompagnement_specialise', 'sos_relation_demande_accompagnement_specialise.id_from_demande =sos_demande.id_demande', 'left');
                //$this->db->join('sos_femme', 'sos_femme.id_femme =sos_demande.id_from_femme', 'left');
                $this->db->where('YEAR(sos_demande.visite)', $this->year);
                $this->db->where('MONTH(sos_demande.visite)', $i);
                $this->db->where('sos_relation_demande_accompagnement_specialise.id_from_accompagnement_specialise', $value['id_demande_accompagnement_specialise']);
                if ($this->s_service != '') {
                    $this->db->where('sos_demande.service', $this->s_service);
                }
                $query = $this->db->get('sos_demande');
                $row = $query->result_array();
                $row_num = count($row);
                $list_months[] = $row_num;
            }
            $data[] = ['travail_social' => $value['name_demande_accompagnement_specialise'],
                'jan' => $list_months[0],
                'fev' => $list_months[1],
                'mar' => $list_months[2],
                'avr' => $list_months[3],
                'mai' => $list_months[4],
                'juin' => $list_months[5],
                'juil' => $list_months[6],
                'aout' => $list_months[7],
                'sept' => $list_months[8],
                'oct' => $list_months[9],
                'nov' => $list_months[10],
                'dec' => $list_months[11],
                'total' => ($list_months[0] + $list_months[1] + $list_months[2] + $list_months[3] + $list_months[4] + $list_months[5] + $list_months[6] + $list_months[7] + $list_months[8] + $list_months[9] + $list_months[10] + $list_months[11])
            ];
        }
        $this->tbswrapper->TBS->MergeBlock('travail_social', $data);
    }

    private function table_5() {
        // MODES D’ACCES
        $data = [];
        $query = $this->db->get('sos_gen_demande_accueil');
        $row_gen_demande_accueil = $query->result_array();
        foreach ($row_gen_demande_accueil as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`visite`) AS `Year`,
              COUNT(CASE WHEN MONTH(`visite`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`visite`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`visite`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`visite`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`visite`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`visite`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`visite`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`visite`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`visite`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`visite`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`visite`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`visite`) = 12  THEN 1 END) AS `dec`
              FROM sos_demande,sos_femme WHERE YEAR(`visite`)=' . $this->year . $this->r_service . ' AND sos_femme.id_femme=sos_demande.id_from_femme AND `accueil_dem`=' . $value['id_demande_accueil'] . ' GROUP BY YEAR(`visite`)')->result();

            $data[] = ['accuils' => $value['name_demande_accueil'],
                'jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            ];
        }
        $this->tbswrapper->TBS->MergeBlock('accuils', $data);
    }

    private function table_6() {
        // acceuil personnes
        $data = [];
        $total_data_personnes_accueillies = [];
        $result = $this->db->query('select distinct `sos_demande`.`visite` AS `visite`, count(0) AS `nombre` 
            from `sos_demande`,sos_femme where year(`sos_demande`.`visite`)=' . $this->year . ' and sos_femme.id_femme =sos_demande.id_from_femme ' . $this->r_service . ' group by sos_demande.visite order by nombre DESC ')->result();
        if (count($result) > 0) {
            $max = intval($result[0]->nombre);
        } else {
            $max = 0;
        }
        foreach (range(1, $max) as $number) {
            $list_months = [];
            for ($i = 1; $i <= 12; $i++) {
                $month_num = 0;
                foreach ($result as $key => $value) {
                    if ($value->nombre == $number && intval(substr($value->visite, 5, 2)) == $i) {
                        $month_num = $month_num + 1;
                    }
                }
                $list_months[] = $month_num;
            }
            $data[] = ['nombre' => $number,
                'jan' => $list_months[0],
                'fev' => $list_months[1],
                'mar' => $list_months[2],
                'avr' => $list_months[3],
                'mai' => $list_months[4],
                'juin' => $list_months[5],
                'juil' => $list_months[6],
                'aout' => $list_months[7],
                'sept' => $list_months[8],
                'oct' => $list_months[9],
                'nov' => $list_months[10],
                'dec' => $list_months[11],
                'total' => ($list_months[0] + $list_months[1] + $list_months[2] + $list_months[3] + $list_months[4] + $list_months[5] + $list_months[6] + $list_months[7] + $list_months[8] + $list_months[9] + $list_months[10] + $list_months[11])
            ];
            $c_data = count($data) - 1;
            array_push($total_data_personnes_accueillies, intval($data[$c_data]['total']));
        }
        $this->tbswrapper->TBS->MergeBlock('nombre', $data);
    }

    private function table_7() {
        /* accuil kids ------------------------------------------------------------------------------------- */
        $data = [];
        $total_data_personnes_accueillies = [];
        $result = $this->db->query('select distinct `sos_demande`.`visite` AS `visite`, count(0) AS `nombre` 
            from (`sos_demande` join `sos_enfants` on `sos_demande`.`id_demande` = `sos_enfants`.`id_from_demande`) where 
            `sos_enfants`.`recu`!=""
            and  year(`sos_demande`.`visite`)=' . $this->year . $this->r_service . ' group by sos_demande.visite order by nombre DESC ')->result();

        if (count($result) > 0) {
            $max = intval($result[0]->nombre);
        } else {
            $max = 0;
        }
        foreach (range(1, $max) as $number) {
            $list_months = [];
            for ($i = 1; $i <= 12; $i++) {
                $month_num = 0;
                foreach ($result as $key => $value) {
                    if ($value->nombre == $number && intval(substr($value->visite, 5, 2)) == $i) {
                        $month_num = $month_num + 1;
                    }
                }
                $list_months[] = $month_num;
            }
            $data[] = ['nombre' => $number,
                'jan' => $list_months[0],
                'fev' => $list_months[1],
                'mar' => $list_months[2],
                'avr' => $list_months[3],
                'mai' => $list_months[4],
                'juin' => $list_months[5],
                'juil' => $list_months[6],
                'aout' => $list_months[7],
                'sept' => $list_months[8],
                'oct' => $list_months[9],
                'nov' => $list_months[10],
                'dec' => $list_months[11],
                'total' => ($list_months[0] + $list_months[1] + $list_months[2] + $list_months[3] + $list_months[4] + $list_months[5] + $list_months[6] + $list_months[7] + $list_months[8] + $list_months[9] + $list_months[10] + $list_months[11])
            ];
            $c_data = count($data) - 1;
            array_push($total_data_personnes_accueillies, intval($data[$c_data]['total']));
        }
        $this->tbswrapper->TBS->MergeBlock('nombre_enfants', $data);
        $data_ouvrables_enfants = array();
        $data_ouvrables_enfants[] = [
            'jan' => $this->ouvrables_enfants_array[0],
            'fev' => $this->ouvrables_enfants_array[1],
            'mar' => $this->ouvrables_enfants_array[2],
            'avr' => $this->ouvrables_enfants_array[3],
            'mai' => $this->ouvrables_enfants_array[4],
            'juin' => $this->ouvrables_enfants_array[5],
            'juil' => $this->ouvrables_enfants_array[6],
            'aout' => $this->ouvrables_enfants_array[7],
            'sept' => $this->ouvrables_enfants_array[8],
            'oct' => $this->ouvrables_enfants_array[9],
            'nov' => $this->ouvrables_enfants_array[10],
            'dec' => $this->ouvrables_enfants_array[11],
            'total' => (string) ($this->ouvrables_enfants_array[0] + $this->ouvrables_enfants_array[1] + $this->ouvrables_enfants_array[2] + $this->ouvrables_enfants_array[3] + $this->ouvrables_enfants_array[4] + $this->ouvrables_enfants_array[5] + $this->ouvrables_enfants_array[6] + $this->ouvrables_enfants_array[7] + $this->ouvrables_enfants_array[8] + $this->ouvrables_enfants_array[9] + $this->ouvrables_enfants_array[10] + $this->ouvrables_enfants_array[11])];

        $this->tbswrapper->TBS->MergeBlock('nombre_jours_enfants', $data_ouvrables_enfants);
        $jan_sum = 0;
        $fev_sum = 0;
        $mar_sum = 0;
        $avr_sum = 0;
        $mai_sum = 0;
        $juin_sum = 0;
        $juil_sum = 0;
        $aout_sum = 0;
        $sept_sum = 0;
        $oct_sum = 0;
        $nov_sum = 0;
        $dec_sum = 0;
        $total_sum = 0;

        $jan = $this->array_column($data, 'jan');
        $arr_length = count($jan);
        for ($i = 0; $i < $arr_length; $i++) {
            $jan_sum = $jan_sum + $jan[$i] * ($i + 1);
        }
        $fev = $this->array_column($data, 'fev');
        $arr_length = count($fev);
        for ($i = 0; $i < $arr_length; $i++) {
            $fev_sum = $fev_sum + $fev[$i] * ($i + 1);
        }
        $mar = $this->array_column($data, 'mar');
        $arr_length = count($mar);
        for ($i = 0; $i < $arr_length; $i++) {
            $mar_sum = $mar_sum + $mar[$i] * ($i + 1);
        }
        $avr = $this->array_column($data, 'avr');
        $arr_length = count($avr);
        for ($i = 0; $i < $arr_length; $i++) {
            $avr_sum = $avr_sum + $avr[$i] * ($i + 1);
        }
        $mai = $this->array_column($data, 'mai');
        $arr_length = count($mai);
        for ($i = 0; $i < $arr_length; $i++) {
            $mai_sum = $mai_sum + $mai[$i] * ($i + 1);
        }
        $juin = $this->array_column($data, 'juin');
        $arr_length = count($juin);
        for ($i = 0; $i < $arr_length; $i++) {
            $juin_sum = $juin_sum + $juin[$i] * ($i + 1);
        }
        $juil = $this->array_column($data, 'juil');
        $arr_length = count($juil);
        for ($i = 0; $i < $arr_length; $i++) {
            $juil_sum = $juil_sum + $juil[$i] * ($i + 1);
        }
        $aout = $this->array_column($data, 'aout');
        $arr_length = count($aout);
        for ($i = 0; $i < $arr_length; $i++) {
            $aout_sum = $aout_sum + $aout[$i] * ($i + 1);
        }
        $sept = $this->array_column($data, 'sept');
        $arr_length = count($sept);
        for ($i = 0; $i < $arr_length; $i++) {
            $sept_sum = $sept_sum + $sept[$i] * ($i + 1);
        }
        $oct = $this->array_column($data, 'oct');
        $arr_length = count($oct);
        for ($i = 0; $i < $arr_length; $i++) {
            $oct_sum = $oct_sum + $oct[$i] * ($i + 1);
        }
        $nov = $this->array_column($data, 'nov');
        $arr_length = count($nov);
        for ($i = 0; $i < $arr_length; $i++) {
            $nov_sum = $nov_sum + $nov[$i] * ($i + 1);
        }
        $dec = $this->array_column($data, 'dec');
        $arr_length = count($dec);
        for ($i = 0; $i < $arr_length; $i++) {
            $dec_sum = $dec_sum + $dec[$i] * ($i + 1);
        }
        $total = $this->array_column($data, 'total');
        $arr_length = count($total);
        for ($i = 0; $i < $arr_length; $i++) {
            $total_sum = $total_sum + $total[$i] * ($i + 1);
        }
        $data_total_persones_enfants = array();
        $data_total_persones_enfants[] = [
            'jan' => $jan_sum,
            'fev' => $fev_sum,
            'mar' => $mar_sum,
            'avr' => $avr_sum,
            'mai' => $mai_sum,
            'juin' => $juin_sum,
            'juil' => $juil_sum,
            'aout' => $aout_sum,
            'sept' => $sept_sum,
            'oct' => $oct_sum,
            'nov' => $nov_sum,
            'dec' => $dec_sum,
            'total' => $total_sum];

        $this->tbswrapper->TBS->MergeBlock('total_persones_enfants', $data_total_persones_enfants);
    }

    private function table_8() {
        /* Lieu Ressources
         * 
         */
        $data = [];
        $query = $this->db->get('sos_gen_demande_lieu_ressource');
        $row_gen_demande_lieu_ressource = $query->result_array();
        foreach ($row_gen_demande_lieu_ressource as $key => $value) {
            $list_months = [];
            for ($i = 1; $i <= 12; $i++) {
                $this->db->select('sos_demande.id_demande,sos_relation_demande_lieu_ressource.id_from_lieu_ressource,sos_relation_demande_lieu_ressource.id_from_demande,sos_demande.service');
                $this->db->join('sos_relation_demande_lieu_ressource', 'sos_relation_demande_lieu_ressource.id_from_demande =sos_demande.id_demande', 'left');
                //$this->db->join('sos_femme', 'sos_femme.id_femme =sos_demande.id_from_femme', 'left');
                $this->db->where('YEAR(sos_demande.visite)', $this->year);
                $this->db->where('MONTH(sos_demande.visite)', $i);
                $this->db->where('sos_relation_demande_lieu_ressource.id_from_lieu_ressource', $value['id_demande_lieu_ressource']);
                if ($this->s_service != '') {
                    $this->db->where('sos_demande.service', $this->s_service);
                }
                $query = $this->db->get('sos_demande');
                $row = $query->result_array();
                $row_num = count($row);
                $list_months[] = $row_num;
            }
            $data[] = ['lieu_ressource' => $value['name_demande_lieu_ressource'],
                'jan' => $list_months[0],
                'fev' => $list_months[1],
                'mar' => $list_months[2],
                'avr' => $list_months[3],
                'mai' => $list_months[4],
                'juin' => $list_months[5],
                'juil' => $list_months[6],
                'aout' => $list_months[7],
                'sept' => $list_months[8],
                'oct' => $list_months[9],
                'nov' => $list_months[10],
                'dec' => $list_months[11],
                'total' => ($list_months[0] + $list_months[1] + $list_months[2] + $list_months[3] + $list_months[4] + $list_months[5] + $list_months[6] + $list_months[7] + $list_months[8] + $list_months[9] + $list_months[10] + $list_months[11])
            ];
        }
        $this->tbswrapper->TBS->MergeBlock('lieu_ressource', $data);
    }

    private function table_9() {
        /* DEMANDES D’HEBERGEMENT
         * 
         */
        $data = [];
        $query = $this->db->get('sos_gen_demande_hbgt');
        $row_gen_demande_hbgt = $query->result_array();
        foreach ($row_gen_demande_hbgt as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`visite`) AS `Year`,
              COUNT(CASE WHEN MONTH(`visite`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`visite`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`visite`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`visite`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`visite`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`visite`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`visite`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`visite`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`visite`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`visite`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`visite`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`visite`) = 12  THEN 1 END) AS `dec`
              FROM sos_demande,sos_femme WHERE YEAR(`visite`)=' . $this->year . ' ' . $this->r_service . ' AND sos_femme.id_femme=sos_demande.id_from_femme  AND `hbgt`=' . $value['id_demande_hbgt'] . ' GROUP BY YEAR(`visite`)')->result();
            if (count($result) > 0) {
                $data[] = ['hebergement' => $value['name_demande_hbgt'],
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                ];
            } else {
                $data[] = ['hebergement' => $value['name_demande_hbgt'],
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                ];
            }
        }
        $this->tbswrapper->TBS->MergeBlock('hebergement', $data);
    }

    private function table_10() {
        //CONTACTS
        $data = [];
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
    FROM sos_femme_premier WHERE YEAR(`premier_contact`)=' . $this->year . ' ' . $this->r_p_service . ' GROUP BY YEAR(`premier_contact`)')->result();
        if (count($result) > 0) {
            $data[] = ['jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            ];
        } else {
            $data[] = ['jan' => 0,
                'fev' => 0,
                'mar' => 0,
                'avr' => 0,
                'mai' => 0,
                'juin' => 0,
                'juil' => 0,
                'aout' => 0,
                'sept' => 0,
                'oct' => 0,
                'nov' => 0,
                'dec' => 0,
                'total' => 0
            ];
        }
        $this->tbswrapper->TBS->MergeBlock('1_acc', $data);
    }

    private function table_11() {
        /* orienteur
         * 
         */
        $data = [];
        $query = $this->db->get('sos_gen_partenaire');
        $row_gen_partenaire = $query->result_array();
        $tot = 0;
        foreach ($row_gen_partenaire as $key => $value) {
            $result = $this->db->query('SELECT `sos_femme`.`partenaire`,count(*) as `num`
            FROM `sos_femme`
            WHERE ((`sos_femme`.`partenaire` =' . $value['id_partenaire'] . ') AND (year(`sos_femme`.`premier_contact`)=' . $this->year . ')' . ' ' . $this->r_femme_service . ')')->result();
            if (count($result) > 0) {
                $data[] = ['orienteur' => $value['name_partenaire'],
                    'num' => $result[0]->num
                ];
                $tot = $tot + $result[0]->num;
            } else {
                $data[] = ['orienteur' => $value['name_partenaire'],
                    'num' => 0
                ];
            }
        }
        $data[] = ['orienteur' => 'TOTAL',
            'num' => $tot
        ];
        $this->tbswrapper->TBS->MergeBlock('orienteur', $data);
    }

    private function table_12() {
        /* demande
         * 
         */
        $data = [];
        $result = $this->db->query('SELECT `sos_femme`.`accueil`,count(*) as `num`
            FROM `sos_femme`
            WHERE ((`sos_femme`.`accueil` =' . 1 . ') AND (year(`sos_femme`.`premier_contact`)=' . $this->year . ')' . $this->r_femme_service . ')')->result();
        if (count($result) > 0) {
            $data[] = ['demande' => 'Accueil',
                'num' => $result[0]->num
            ];
        } else {
            $data[] = ['demande' => 'Accueil',
                'num' => 0
            ];
        }
        $query = $this->db->get('sos_gen_informations');
        $row_gen_informations = $query->result_array();
        $tot = 0;
        foreach ($row_gen_informations as $key => $value) {
            $result = $this->db->query('SELECT `sos_femme`.`informations`,count(*) as `num`
            FROM `sos_femme`
            WHERE ((`sos_femme`.`informations` =' . $value['id_informations'] . ') AND (year(`sos_femme`.`premier_contact`)=' . $this->year . ')' . ' ' . $this->r_femme_service . ')')->result();
            if (count($result) > 0) {
                $data[] = ['demande' => '--- ' . $value['name_informations'],
                    'num' => $result[0]->num
                ];
                $tot = $tot + $result[0]->num;
            } else {
                $data[] = ['demande' => '--- ' . $value['name_informations'],
                    'num' => 0
                ];
            }
        }
        $data[] = ['demande' => 'Total Informations',
            'num' => $tot
        ];
        $result = $this->db->query('SELECT `sos_femme`.`conseil`,count(*) as `num`
            FROM `sos_femme`
            WHERE ((`sos_femme`.`conseil` =' . 1 . ') AND (year(`sos_femme`.`premier_contact`)=' . $this->year . ')' . $this->r_femme_service . ')')->result();
        if (count($result) > 0) {
            $data[] = ['demande' => 'Conseil',
                'num' => $result[0]->num
            ];
        } else {
            $data[] = ['demande' => 'Conseil',
                'num' => 0
            ];
        }
        $result = $this->db->query('SELECT `sos_femme`.`orientation`,count(*) as `num`
            FROM `sos_femme`
            WHERE ((`sos_femme`.`orientation` =' . 1 . ') AND (year(`sos_femme`.`premier_contact`)=' . $this->year . ')' . $this->r_femme_service . ')')->result();
        if (count($result) > 0) {
            $data[] = ['demande' => 'Orientation',
                'num' => $result[0]->num
            ];
        } else {
            $data[] = ['demande' => 'Orientation',
                'num' => 0
            ];
        }
        $query = $this->db->get('sos_gen_rdv');
        $row_gen_rdv = $query->result_array();
        $tot = 0;
        foreach ($row_gen_rdv as $key => $value) {
            $result = $this->db->query('SELECT `sos_femme`.`rdv`,count(*) as `num`
            FROM `sos_femme`
            WHERE ((`sos_femme`.`rdv` =' . $value['id_rdv'] . ') AND (year(`sos_femme`.`premier_contact`)=' . $this->year . ')' . ' ' . $this->r_femme_service . ')')->result();
            if (count($result) > 0) {
                $data[] = ['demande' => '--- ' . $value['name_rdv'],
                    'num' => $result[0]->num
                ];
                $tot = $tot + $result[0]->num;
            } else {
                $data[] = ['demande' => '--- ' . $value['name_rdv'],
                    'num' => 0
                ];
            }
        }
        $data[] = ['demande' => 'Total RDV',
            'num' => $tot
        ];
        $query = $this->db->get('sos_gen_hebergement');
        $row_gen_hebergement = $query->result_array();
        $tot = 0;
        foreach ($row_gen_hebergement as $key => $value) {
            $result = $this->db->query('SELECT `sos_femme`.`hebergement`,count(*) as `num`
            FROM `sos_femme`
            WHERE ((`sos_femme`.`hebergement` =' . $value['id_hebergement'] . ') AND (year(`sos_femme`.`premier_contact`)=' . $this->year . ')' . $this->r_femme_service . ')')->result();
            if (count($result) > 0) {
                $data[] = ['demande' => '--- ' . $value['name_hebergement'],
                    'num' => $result[0]->num
                ];
                $tot = $tot + $result[0]->num;
            } else {
                $data[] = ['demande' => '--- ' . $value['name_hebergement'],
                    'num' => 0
                ];
            }
        }
        $data[] = ['demande' => 'Total Hébergement',
            'num' => $tot
        ];
        $result = $this->db->query('SELECT `sos_femme`.`logement_dem`,count(*) as `num`
            FROM `sos_femme`
            WHERE ((`sos_femme`.`logement_dem` =' . 1 . ') AND (year(`sos_femme`.`premier_contact`)=' . $this->year . ')' . ' ' . $this->r_femme_service . ')')->result();
        if (count($result) > 0) {
            $data[] = ['demande' => 'Logement',
                'num' => $result[0]->num
            ];
        } else {
            $data[] = ['demande' => 'Logement',
                'num' => 0
            ];
        }
        $result = $this->db->query('SELECT `sos_femme`.`aide_materielle`,count(*) as `num`
            FROM `sos_femme`
            WHERE ((`sos_femme`.`aide_materielle` =' . 1 . ') AND (year(`sos_femme`.`premier_contact`)=' . $this->year . ')' . ' ' . $this->r_femme_service . ')')->result();
        if (count($result) > 0) {
            $data[] = ['demande' => 'Matérielle',
                'num' => $result[0]->num
            ];
        } else {
            $data[] = ['demande' => 'Matérielle',
                'num' => 0
            ];
        }
        $result = $this->db->query('SELECT `sos_femme`.`adresse_postale`,count(*) as `num`
            FROM `sos_femme`
            WHERE ((`sos_femme`.`adresse_postale` =' . 1 . ') AND (year(`sos_femme`.`premier_contact`)=' . $this->year . ')' . ' ' . $this->r_femme_service . ')')->result();
        if (count($result) > 0) {
            $data[] = ['demande' => 'Adresse Postale',
                'num' => $result[0]->num
            ];
        } else {
            $data[] = ['demande' => 'Adresse Postale',
                'num' => 0
            ];
        }
        $result = $this->db->query('SELECT `sos_femme`.`accompagnement_exterieur`,count(*) as `num`
            FROM `sos_femme`
            WHERE ((`sos_femme`.`accompagnement_exterieur` =' . 1 . ') AND (year(`sos_femme`.`premier_contact`)=' . $this->year . ')' . ' ' . $this->r_femme_service . ')')->result();
        if (count($result) > 0) {
            $data[] = ['demande' => 'Accompagnement Extérieur',
                'num' => $result[0]->num
            ];
        } else {
            $data[] = ['demande' => 'Accompagnement Extérieur',
                'num' => 0
            ];
        }
        $this->tbswrapper->TBS->MergeBlock('demande', $data);
    }

    private function table_13() {
        /* residence
         * 
         */
        $data = [];
        $query = $this->db->get('sos_gen_villes');
        $row_gen = $query->result_array();
        $tot = 0;
        $tot_vis = 0;
        $tot_pass = 0;

        foreach ($row_gen as $key => $value) {
            $result = $this->db->query('SELECT count(*) as num
            FROM sos_femme
            WHERE sos_femme.ville =' . $value['id_ville'] . ' AND year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service)->result();
            $data[] = ['residence' => $value['nom_ville'],
                'num' => $result[0]->num
            ];
            $tot = $tot + $result[0]->num;
            $result = $this->db->query('select count(*) as num from '
                            . '(select ville from sos_femme inner join sos_demande on sos_demande.id_from_femme = sos_femme.id_femme'
                            . ' where sos_femme.ville=' . $value['id_ville'] . ' and year(sos_demande.visite)=' . $this->year . $this->r_service . ' group BY id_from_femme) as raq')->result();
            $data[count($data) - 1] = array_merge($data[count($data) - 1], [
                'num_vis' => $result[0]->num
            ]);
            $tot_vis = $tot_vis + $result[0]->num;
            $result = $this->db->query('select count(*) as num from '
                            . '(select ville from sos_femme inner join sos_demande on sos_demande.id_from_femme = sos_femme.id_femme'
                            . ' where sos_femme.ville=' . $value['id_ville'] . ' and year(sos_demande.visite)=' . $this->year . $this->r_service . ') as raq')->result();
            $data[count($data) - 1] = array_merge($data[count($data) - 1], [
                'num_pass' => $result[0]->num
            ]);
            $tot_pass = $tot_pass + $result[0]->num;
        }
        $result = $this->db->query('select count(*) as num from '
                        . '(select ville from sos_femme inner join sos_demande on sos_demande.id_from_femme = sos_femme.id_femme'
                        . ' where year(sos_demande.visite)=' . $this->year . $this->r_service . ') as raq')->result();
        $data[] = ['residence' => 'Non dit',
            'num' => ($this->total_femmes_var - $tot) . ' sur ' . $this->total_femmes_var . ' (' . (round(($this->total_femmes_var - $tot) * 100 / $this->total_femmes_var)) . '%)',
            'num_vis' => ($this->total_femmes_var_visites - $tot_vis) . ' sur ' . $this->total_femmes_var_visites . ' (' . @((round(($this->total_femmes_var_visites - $tot_vis) * 100 / $this->total_femmes_var_visites))) . '%)',
            'num_pass' => ($result[0]->num - $tot_pass) . ' sur ' . $result[0]->num . ' (' . (round(($result[0]->num - $tot_pass) * 100 / $result[0]->num)) . '%)'
        ];
        $this->tbswrapper->TBS->MergeBlock('residence', $data);


        /* foreach ($row_gen_villes as $key => $value) {
          $result = $this->db->query('SELECT `sos_femme`.`ville`,count(*) as `num`
          FROM `sos_femme`
          WHERE ((`sos_femme`.`ville` =' . $value['id_ville'] . ') AND (year(`sos_femme`.`premier_contact`)=' . $this->year . ')' . $this->r_femme_service . ')')->result();
          if (count($result) > 0) {
          $data[] = ['residence' => $value['nom_ville'],
          'num' => $result[0]->num
          ];
          $tot = $tot + $result[0]->num;
          } else {
          $data[] = ['residence' => $value['nom_ville'],
          'num' => 0
          ];
          }
          $result = $this->db->query('SELECT `sos_femme`.`ville`,count(*) as `num`
          FROM `sos_femme`,sos_demande
          WHERE ((`sos_femme`.`ville` =' . $value['id_ville'] . ') AND (year(`sos_demande`.`visite`)=' . $this->year . ')' . $this->r_service . ') group by sos_demande.id_from_femme')->result();
          if (count($result) > 0) {
          $data[count($data) - 1] = array_merge($data[count($data) - 1], [
          'num_vis' => $result[0]->num
          ]);

          $tot_vis = $tot_vis + $result[0]->num;
          } else {
          $data[count($data) - 1] = array_merge($data[count($data) - 1], [
          'num_vis' => 0
          ]);
          }

          $result = $this->db->query('SELECT `sos_femme`.`ville`,count(*) as `num`
          FROM `sos_femme`,sos_demande
          WHERE ((`sos_femme`.`ville` =' . $value['id_ville'] . ') AND (year(`sos_demande`.`visite`)=' . $this->year . ')' . $this->r_service . ') and (sos_femme.id_femme=sos_demande.id_from_femme)')->result();
          if (count($result) > 0) {
          $data[count($data) - 1] = array_merge($data[count($data) - 1], [
          'num_pass' => $result[0]->num
          ]);

          $tot_pass = $tot_pass + $result[0]->num;
          } else {
          $data[count($data) - 1] = array_merge($data[count($data) - 1], [
          'num_pass' => 0
          ]);
          }
          }
          $result = $this->db->query('SELECT count(*) as `num`
          FROM `sos_femme`,sos_demande
          WHERE ((year(`sos_demande`.`visite`)=' . $this->year . ')' . $this->r_service . ') and (sos_femme.id_femme=sos_demande.id_from_femme)')->result();
         */
    }

    private function table_14() {
        //Ages
        $data = [];
        $query = $this->db->get('sos_gen_femme_age');
        $row_gen = $query->result_array();
        $tot = 0;
        $tot_vis = 0;
        foreach ($row_gen as $key => $value) {
            $result = $this->db->query('SELECT count(*) as num
            FROM sos_femme
            WHERE sos_femme.age =' . $value['id_femme_age'] . ' AND year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service)->result();
            $data[] = ['ages' => $value['name_femme_age'],
                'num' => $result[0]->num
            ];
            $tot = $tot + $result[0]->num;
            $result = $this->db->query('select count(*) as num from '
                            . '(select age from sos_femme inner join sos_demande on sos_demande.id_from_femme = sos_femme.id_femme'
                            . ' where sos_femme.age=' . $value['id_femme_age'] . ' and year(sos_demande.visite)=' . $this->year . $this->r_service . ' group BY id_from_femme) as raq')->result();
            $data[count($data) - 1] = array_merge($data[count($data) - 1], [
                'num_vis' => $result[0]->num
            ]);
            $tot_vis = $tot_vis + $result[0]->num;
        }

        $data[] = ['ages' => 'Non dit',
            'num' => ($this->total_femmes_var - $tot) . ' sur ' . $this->total_femmes_var . '(' . (round(($this->total_femmes_var - $tot) * 100 / $this->total_femmes_var)) . '%)',
            'num_vis' => ($this->total_femmes_var_visites - $tot_vis) . ' sur ' . $this->total_femmes_var_visites . ' (' . @((round(($this->total_femmes_var_visites - $tot_vis) * 100 / $this->total_femmes_var_visites))) . '%)'
        ];
        $this->tbswrapper->TBS->MergeBlock('ages', $data);
    }

    private function table_15() {
        /* matrimonial
         * 
         */
        $data = [];
        $query = $this->db->get('sos_gen_situation_familiale_parrent');
        $row_gen = $query->result_array();
        $tot = 0;
        $tot_vis = 0;
        foreach ($row_gen as $key => $value) {
            $result = $this->db->query('SELECT count(*) as num
            FROM sos_femme
            WHERE sos_femme.situation_familiale =' . $value['id_situation_familiale_parrent'] . ' AND year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service)->result();
            $data[] = ['matrimonial' => $value['name_situation_familiale_parrent'],
                'num' => $result[0]->num
            ];
            $tot = $tot + $result[0]->num;
            $result = $this->db->query('select count(*) as num from '
                            . '(select situation_familiale from sos_femme inner join sos_demande on sos_demande.id_from_femme = sos_femme.id_femme'
                            . ' where sos_femme.situation_familiale=' . $value['id_situation_familiale_parrent'] . ' and year(sos_demande.visite)=' . $this->year . $this->r_service . ' group BY id_from_femme) as raq')->result();
            $data[count($data) - 1] = array_merge($data[count($data) - 1], [
                'num_vis' => $result[0]->num
            ]);
            $tot_vis = $tot_vis + $result[0]->num;
            $query_child = $this->db->get_where('sos_gen_situation_familiale_child', ['id_parrent_from_situation_familiale_parrent' => $value['id_situation_familiale_parrent']
            ]);
            $row_gen_child = $query_child->result_array();
            foreach ($row_gen_child as $key => $value_child) {
                $result_child = $this->db->query('SELECT count(*) as `num`
            FROM sos_femme
            WHERE sos_femme.detailles =' . $value_child['id_situation_familiale_child'] . ' and sos_femme.situation_familiale =' . $value['id_situation_familiale_parrent'] . ' AND year(`sos_femme`.`premier_contact`)=' . $this->year . $this->r_femme_service)->result();
                $data[] = ['matrimonial' => '      ' . $value_child['name_situation_familiale_child'],
                    'num' => $result_child[0]->num
                ];
                $result_child = $this->db->query('select count(*) as num from '
                                . '(select nationalite_detailles from sos_femme inner join sos_demande on sos_demande.id_from_femme = sos_femme.id_femme'
                                . ' where sos_femme.detailles =' . $value_child['id_situation_familiale_child'] . ' and sos_femme.situation_familiale =' . $value['id_situation_familiale_parrent'] . ' and year(sos_demande.visite)=' . $this->year . $this->r_service . ' group BY id_from_femme) as raq')->result();
                $data[count($data) - 1] = array_merge($data[count($data) - 1], [
                    'num_vis' => $result_child[0]->num
                ]);
            }
        }
        $data[] = ['matrimonial' => 'Non dit',
            'num' => ($this->total_femmes_var - $tot) . ' sur ' . $this->total_femmes_var . '(' . (round(($this->total_femmes_var - $tot) * 100 / $this->total_femmes_var)) . '%)',
            'num_vis' => ($this->total_femmes_var_visites - $tot_vis) . ' sur ' . $this->total_femmes_var_visites . ' (' . @((round(($this->total_femmes_var_visites - $tot_vis) * 100 / $this->total_femmes_var_visites))) . '%)',
        ];
        $this->tbswrapper->TBS->MergeBlock('matrimonial', $data);
    }

    private function table_16() {
        //AGES D’ENFANTS
        $data = [];
        $query = $this->db->get('sos_gen_kids_age');
        $row_gen_kids_age = $query->result_array();
        $tot = 0;
        $tot_vis = 0;
        foreach ($row_gen_kids_age as $key => $value) {
            $result = $this->db->query('select sum(num) as numm from '
                            . '(SELECT count(*) as num FROM sos_femme '
                            . 'inner JOIN sos_kids ON sos_femme.id_femme = sos_kids.id_femme '
                            . 'where sos_kids.age =' . $value['id_kids_age'] . ' and year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service . ' group by sos_kids.id_femme) groups')->result();
            $data[] = ['enfants_ages' => $value['name_kids_age'],
                'num' => $result[0]->numm
            ];
            $tot = $tot + $result[0]->numm;
            $result = $this->db->query('select count(*) as numm from '
                            . '(SELECT sos_demande.id_from_femme '
                            . 'fROM sos_demande INNER JOIN sos_femme ON sos_demande.id_from_femme = sos_femme.id_femme '
                            . 'INNER JOIN sos_kids ON sos_kids.id_femme = sos_demande.id_from_femme'
                            . ' where year(sos_demande.visite)=' . $this->year . $this->r_femme_service . ' and sos_kids.age=' . $value['id_kids_age'] . ' group by sos_demande.id_from_femme) groups')->result();
            $data[count($data) - 1] = array_merge($data[count($data) - 1], [
                'num_vis' => $result[0]->numm
            ]);
            $tot_vis = $tot_vis + $result[0]->numm;
        }

        $data[] = ['enfants_ages' => 'Non dit',
            'num' => ($this->total_femmes_acc_avec_enfant - $tot) . ' sur ' . $this->total_femmes_acc_avec_enfant . ' (' . (round(($this->total_femmes_acc_avec_enfant - $tot) * 100 / $this->total_femmes_acc_avec_enfant)) . '%)',
            'num_vis' => ($this->total_femmes_var_visites_avec_enfant - $tot_vis) . ' sur ' . $this->total_femmes_var_visites_avec_enfant . ' (' . (round(($this->total_femmes_var_visites_avec_enfant - $tot_vis) * 100 / $this->total_femmes_var_visites_avec_enfant)) . '%)'
        ];
        $this->tbswrapper->TBS->MergeBlock('enfants_ages', $data);
    }

    private function table_17() {
        //NOMBRE D’ENFANTS
        $data = [];
        $counter = 0;
        $result = $this->db->query('select num,count(*) as numm from '
                        . '(SELECT count(*) as num FROM sos_kids,sos_femme '
                        . 'WHERE sos_kids.id_femme=sos_femme.id_femme '
                        . 'and year(sos_femme.premier_contact)=' . $this->year . ' ' . $this->r_femme_service . ' group by sos_kids.id_femme ) as raq group by num')->result_array();
        foreach ($result as $key => $value) {
            $data[] = ['enfants_nombre' => $value['num'],
                'num' => $value['numm']
            ];
        }

        $result = $this->db->query('select num,count(*) as numm '
                        . 'from (SELECT count(*) as num '
                        . 'FROM (select * '
                        . 'from (SELECT * FROM sos_demande WHERE year(sos_demande.visite)=' . $this->year . ' ' . $this->r_service . ' group by sos_demande.id_from_femme) groups) as f,sos_kids,sos_femme WHERE sos_kids.id_femme=f.id_from_femme and sos_femme.id_femme = f.id_from_femme group by sos_kids.id_femme ) as raq group by num')->result_array();
        $counter = 0;
        foreach ($result as $key => $value) {
            if (isset($data[$counter])) {
                $data[$counter] = array_merge($data[$counter], [
                    'num_vis' => $value['numm']
                ]);
                $counter = $counter + 1;
            } else {
                $data[] = ['enfants_nombre' => $value['num'],
                    'num_vis' => $value['numm']
                ];
            }
        }
        $this->tbswrapper->TBS->MergeBlock('enfants_nombre', $data);
    }

    private function table_18() {
        /*
         * logement
         */
        $data = [];
        $query = $this->db->get('sos_gen_logement_parent');
        $row_gen_logement_parent = $query->result_array();
        $tot = 0;
        foreach ($row_gen_logement_parent as $key => $value) {
            $result = $this->db->query('SELECT `sos_femme`.`logement`,count(*) as `num`
            FROM `sos_femme`
            WHERE ((`sos_femme`.`logement` =' . $value['id_logement_parent'] . ') AND (year(`sos_femme`.`premier_contact`)=' . $this->year . ')' . $this->r_femme_service . ')')->result();
            if (count($result) > 0) {
                $data[] = ['logement_stat' => $value['name_logement'],
                    'num' => $result[0]->num
                ];
                $tot = $tot + $result[0]->num;
                $query_child = $this->db->get_where('sos_gen_logement_child', ['id_from_logement_parent' => $value['id_logement_parent']
                ]);
                $row_gen_logement_child = $query_child->result_array();
                foreach ($row_gen_logement_child as $key => $value_child) {
                    $result_child = $this->db->query('SELECT `sos_femme`.`logement`,`sos_femme`.`logement_detailles`,count(*) as `num`
            FROM `sos_femme`
            WHERE (`sos_femme`.`logement_detailles` =' . $value_child['id_logement_child'] . ' and (`sos_femme`.`logement` =' . $value['id_logement_parent'] . ') AND (year(`sos_femme`.`premier_contact`)=' . $this->year . ')' . $this->r_femme_service . ')')->result();
                    if (count($result_child) > 0) {
                        $data[] = ['logement_stat' => '     ' . $value_child['name_logement_child'],
                            'num' => $result_child[0]->num
                        ];
                    } else {
                        $data[] = ['logement_stat' => '     ' . $value_child['name_logement_child'],
                            'num' => 0
                        ];
                    }
                }
            } else {
                $data[] = ['logement_stat' => $value['name_logement'],
                    'num' => 0
                ];
            }
        }
        $data[] = ['logement_stat' => 'Non dit : ' . ($this->total_femmes_var - $tot) . ' sur ' . $this->total_femmes_var,
            'num' => (round(($this->total_femmes_var - $tot) * 100 / $this->total_femmes_var)) . '%'
        ];
        $this->tbswrapper->TBS->MergeBlock('logement_stat', $data);
    }

    private function table_19() {
        /*
         * situation actuelle
         */
        $data = [];
        $query = $this->db->get('sos_gen_situation_actuelle');
        $row_gen_situation_actuelle = $query->result_array();
        $tot = 0;
        $tot_vis = 0;
        foreach ($row_gen_situation_actuelle as $key => $value) {
            $result = $this->db->query('SELECT count(*) as num
            FROM sos_femme
            WHERE sos_femme.situation_actuelle =' . $value['id_situation_actuelle'] . ' AND year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service)->result();
            $data[] = ['situation_act' => $value['name_situation_actuelle'],
                'num' => $result[0]->num
            ];
            $tot = $tot + $result[0]->num;
            $result = $this->db->query('select count(*) as num from '
                            . '(select situation_actuelle from sos_femme inner join sos_demande on sos_demande.id_from_femme = sos_femme.id_femme'
                            . ' where sos_femme.situation_actuelle=' . $value['id_situation_actuelle'] . ' and year(sos_demande.visite)=' . $this->year . $this->r_service . ' group BY id_from_femme) as raq')->result();
            $data[count($data) - 1] = array_merge($data[count($data) - 1], [
                'num_vis' => $result[0]->num
            ]);
            $tot_vis = $tot_vis + $result[0]->num;
            $query_child = $this->db->get_where('sos_gen_situation_actuelle_detailles', ['id_from_situation_actuelle' => $value['id_situation_actuelle']
            ]);
            $row_gen_situation_actuelle_detailles = $query_child->result_array();
            foreach ($row_gen_situation_actuelle_detailles as $key => $value_child) {
                $result_child = $this->db->query('SELECT count(*) as `num`
            FROM sos_femme
            WHERE sos_femme.situation_actuelle_detailles =' . $value_child['id_situation_actuelle_detailles'] . ' and sos_femme.situation_actuelle =' . $value['id_situation_actuelle'] . ' AND year(`sos_femme`.`premier_contact`)=' . $this->year . $this->r_femme_service)->result();
                $data[] = ['situation_act' => '        ' . $value_child['name_situation_actuelle_detailles'],
                    'num' => $result_child[0]->num
                ];
                $result_child = $this->db->query('select count(*) as num from '
                                . '(select situation_actuelle_detailles from sos_femme inner join sos_demande on sos_demande.id_from_femme = sos_femme.id_femme'
                                . ' where sos_femme.situation_actuelle_detailles =' . $value_child['id_situation_actuelle_detailles'] . ' and sos_femme.situation_actuelle =' . $value['id_situation_actuelle'] . ' and year(sos_demande.visite)=' . $this->year . $this->r_service . ' group BY id_from_femme) as raq')->result();
                $data[count($data) - 1] = array_merge($data[count($data) - 1], [
                    'num_vis' => $result_child[0]->num
                ]);
            }
        }
        $data[] = ['situation_act' => 'Non dit',
            'num' => ($this->total_femmes_var - $tot) . ' sur ' . $this->total_femmes_var . '(' . (round(($this->total_femmes_var - $tot) * 100 / $this->total_femmes_var)) . '%)',
            'num_vis' => ($this->total_femmes_var_visites - $tot_vis) . ' sur ' . $this->total_femmes_var_visites . ' (' . @((round(($this->total_femmes_var_visites - $tot_vis) * 100 / $this->total_femmes_var_visites))) . '%)'
        ];
        $this->tbswrapper->TBS->MergeBlock('situation_act', $data);
    }

    private function table_20() {
        /*
         * situation actuelle depuis
         */
        $data = [];
        $query = $this->db->get('sos_gen_situation_actuelle_depuis');
        $row_gen_situation_actuelle_depuis = $query->result_array();
        $tot = 0;
        $tot_vis = 0;
        foreach ($row_gen_situation_actuelle_depuis as $key => $value) {
            $result = $this->db->query('SELECT count(*) as num
            FROM sos_femme
            WHERE sos_femme.situation_actuelle_depuis =' . $value['id_situation_actuelle_depuis'] . ' AND year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service)->result();
            $data[] = ['situation_act_depuis' => $value['name_situation_actuelle_depuis'],
                'num' => $result[0]->num
            ];
            $tot = $tot + $result[0]->num;
            $result = $this->db->query('select count(*) as num from '
                            . '(select situation_actuelle_depuis from sos_femme inner join sos_demande on sos_demande.id_from_femme = sos_femme.id_femme'
                            . ' where sos_femme.situation_actuelle_depuis=' . $value['id_situation_actuelle_depuis'] . ' and year(sos_demande.visite)=' . $this->year . $this->r_service . ' group BY id_from_femme) as raq')->result();
            $data[count($data) - 1] = array_merge($data[count($data) - 1], [
                'num_vis' => $result[0]->num
            ]);
            $tot_vis = $tot_vis + $result[0]->num;
        }
        $data[] = ['situation_act_depuis' => 'Non dit',
            'num' => ($this->total_femmes_var - $tot) . ' sur ' . $this->total_femmes_var . '(' . (round(($this->total_femmes_var - $tot) * 100 / $this->total_femmes_var)) . '%)',
            'num_vis' => ($this->total_femmes_var_visites - $tot_vis) . ' sur ' . $this->total_femmes_var_visites . ' (' . @((round(($this->total_femmes_var_visites - $tot_vis) * 100 / $this->total_femmes_var_visites))) . '%)'
        ];

        $this->tbswrapper->TBS->MergeBlock('situation_act_depuis', $data);
    }

    private function table_21() {
        /*
         * depars anterieurs
         */
        $data = [];
        $query = $this->db->get('sos_gen_departs_anterieurs');
        $row_gen_departs_anterieurs = $query->result_array();
        $tot = 0;
        $tot_vis = 0;
        foreach ($row_gen_departs_anterieurs as $key => $value) {
            $result = $this->db->query('SELECT count(*) as num
            FROM sos_femme
            WHERE sos_femme.departs_anterieurs =' . $value['id_departs_anterieurs'] . ' AND year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service)->result();
            $data[] = ['departs_anterieurs' => $value['name_departs_anterieurs'],
                'num' => $result[0]->num
            ];
            $tot = $tot + $result[0]->num;
            $result = $this->db->query('select count(*) as num from '
                            . '(select departs_anterieurs from sos_femme inner join sos_demande on sos_demande.id_from_femme = sos_femme.id_femme'
                            . ' where sos_femme.departs_anterieurs=' . $value['id_departs_anterieurs'] . ' and year(sos_demande.visite)=' . $this->year . $this->r_service . ' group BY id_from_femme) as raq')->result();
            $data[count($data) - 1] = array_merge($data[count($data) - 1], [
                'num_vis' => $result[0]->num
            ]);
            $tot_vis = $tot_vis + $result[0]->num;
        }
        $data[] = ['departs_anterieurs' => 'Non dit',
            'num' => ($this->total_femmes_var - $tot) . ' sur ' . $this->total_femmes_var . '(' . (round(($this->total_femmes_var - $tot) * 100 / $this->total_femmes_var)) . '%)',
            'num_vis' => ($this->total_femmes_var_visites - $tot_vis) . ' sur ' . $this->total_femmes_var_visites . ' (' . @((round(($this->total_femmes_var_visites - $tot_vis) * 100 / $this->total_femmes_var_visites))) . '%)'
        ];

        $this->tbswrapper->TBS->MergeBlock('departs_anterieurs', $data);
    }

    private function table_22() {
        /*
         * pays
         */
        $data = [];
        $query = $this->db->get('sos_gen_pays');
        $row_gen = $query->result_array();
        $tot = 0;
        $tot_vis = 0;
        foreach ($row_gen as $key => $value) {
            $result = $this->db->query('SELECT count(*) as num
            FROM sos_femme
            WHERE sos_femme.pays =' . $value['id'] . ' AND year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service)->result();
            $data[] = ['pays' => $value['nom_pays'] . ' - ' . $value['continent'],
                'num' => $result[0]->num
            ];
            $tot = $tot + $result[0]->num;
            $result = $this->db->query('select count(*) as num from '
                            . '(select pays from sos_femme inner join sos_demande on sos_demande.id_from_femme = sos_femme.id_femme'
                            . ' where sos_femme.pays=' . $value['id'] . ' and year(sos_demande.visite)=' . $this->year . $this->r_service . ' group BY id_from_femme) as raq')->result();
            $data[count($data) - 1] = array_merge($data[count($data) - 1], [
                'num_vis' => $result[0]->num
            ]);
            $tot_vis = $tot_vis + $result[0]->num;
        }
        $data[] = ['pays' => 'Non dit',
            'num' => ($this->total_femmes_var - $tot) . ' sur ' . $this->total_femmes_var . '(' . (round(($this->total_femmes_var - $tot) * 100 / $this->total_femmes_var)) . '%)',
            'num_vis' => ($this->total_femmes_var_visites - $tot_vis) . ' sur ' . $this->total_femmes_var_visites . ' (' . @((round(($this->total_femmes_var_visites - $tot_vis) * 100 / $this->total_femmes_var_visites))) . '%)',
        ];
        $this->tbswrapper->TBS->MergeBlock('pays', $data);
    }

    private function table_23() {
        /*
         * nationalite
         */
        $data = [];
        $query = $this->db->get('sos_gen_nationalite');
        $row_gen = $query->result_array();
        $tot = 0;
        $tot_vis = 0;
        foreach ($row_gen as $key => $value) {
            $result = $this->db->query('SELECT count(*) as num
            FROM sos_femme
            WHERE sos_femme.nationalite =' . $value['id_nationalite'] . ' AND year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service)->result();
            $data[] = ['nationalite_stat' => $value['name_nationalite'],
                'num' => $result[0]->num
            ];
            $tot = $tot + $result[0]->num;
            $result = $this->db->query('select count(*) as num from '
                            . '(select nationalite from sos_femme inner join sos_demande on sos_demande.id_from_femme = sos_femme.id_femme'
                            . ' where sos_femme.nationalite=' . $value['id_nationalite'] . ' and year(sos_demande.visite)=' . $this->year . $this->r_service . ' group BY id_from_femme) as raq')->result();
            $data[count($data) - 1] = array_merge($data[count($data) - 1], [
                'num_vis' => $result[0]->num
            ]);
            $tot_vis = $tot_vis + $result[0]->num;
            $query_child = $this->db->get_where('sos_gen_nationalite_detailles', ['id_from_nationalite' => $value['id_nationalite']
            ]);
            $row_gen_child = $query_child->result_array();
            foreach ($row_gen_child as $key => $value_child) {
                $result_child = $this->db->query('SELECT count(*) as `num`
            FROM sos_femme
            WHERE sos_femme.nationalite_detailles =' . $value_child['id_nationalite_detailles'] . ' and sos_femme.nationalite =' . $value['id_nationalite'] . ' AND year(`sos_femme`.`premier_contact`)=' . $this->year . $this->r_femme_service)->result();
                $data[] = ['nationalite_stat' => '        ' . $value_child['name_nationalite_detailles'],
                    'num' => $result_child[0]->num
                ];
                $result_child = $this->db->query('select count(*) as num from '
                                . '(select nationalite_detailles from sos_femme inner join sos_demande on sos_demande.id_from_femme = sos_femme.id_femme'
                                . ' where sos_femme.nationalite_detailles =' . $value_child['id_nationalite_detailles'] . ' and sos_femme.nationalite =' . $value['id_nationalite'] . ' and year(sos_demande.visite)=' . $this->year . $this->r_service . ' group BY id_from_femme) as raq')->result();
                $data[count($data) - 1] = array_merge($data[count($data) - 1], [
                    'num_vis' => $result_child[0]->num
                ]);
            }
        }
        $data[] = ['nationalite_stat' => 'Non dit',
            'num' => ($this->total_femmes_var - $tot) . ' sur ' . $this->total_femmes_var . '(' . (round(($this->total_femmes_var - $tot) * 100 / $this->total_femmes_var)) . '%)',
            'num_vis' => ($this->total_femmes_var_visites - $tot_vis) . ' sur ' . $this->total_femmes_var_visites . ' (' . @((round(($this->total_femmes_var_visites - $tot_vis) * 100 / $this->total_femmes_var_visites))) . '%)'
        ];
        $this->tbswrapper->TBS->MergeBlock('nationalite_stat', $data);
    }

    private function table_24() {
        /*
         * emplois
         */
        $data = [];
        $query = $this->db->get('sos_gen_emplois_parrent');
        $row_gen = $query->result_array();
        $tot = 0;
        $tot_vis = 0;
        foreach ($row_gen as $key => $value) {
            $result = $this->db->query('SELECT count(*) as num
            FROM sos_femme
            WHERE sos_femme.emplois =' . $value['id_emplois'] . ' AND year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service)->result();
            $data[] = ['emplois_stat' => $value['name_emplois'],
                'num' => $result[0]->num
            ];
            $tot = $tot + $result[0]->num;
            $result = $this->db->query('select count(*) as num from '
                            . '(select emplois from sos_femme inner join sos_demande on sos_demande.id_from_femme = sos_femme.id_femme'
                            . ' where sos_femme.emplois=' . $value['id_emplois'] . ' and year(sos_demande.visite)=' . $this->year . $this->r_service . ' group BY id_from_femme) as raq')->result();
            $data[count($data) - 1] = array_merge($data[count($data) - 1], [
                'num_vis' => $result[0]->num
            ]);
            $tot_vis = $tot_vis + $result[0]->num;
            $query_child = $this->db->get_where('sos_gen_emplois_child', ['id_from_emplois' => $value['id_emplois']
            ]);
            $row_gen_child = $query_child->result_array();
            foreach ($row_gen_child as $key => $value_child) {
                $result_child = $this->db->query('SELECT count(*) as `num`
            FROM sos_femme
            WHERE sos_femme.emplois_detailles =' . $value_child['id_emplois_detailles'] . ' and sos_femme.emplois =' . $value['id_emplois'] . ' AND year(`sos_femme`.`premier_contact`)=' . $this->year . $this->r_femme_service)->result();
                $data[] = ['emplois_stat' => '        ' . $value_child['name_emplois_detaille'],
                    'num' => $result_child[0]->num
                ];
                $result_child = $this->db->query('select count(*) as num from '
                                . '(select emplois_detailles from sos_femme inner join sos_demande on sos_demande.id_from_femme = sos_femme.id_femme'
                                . ' where sos_femme.emplois_detailles =' . $value_child['id_emplois_detailles'] . ' and sos_femme.emplois =' . $value['id_emplois'] . ' and year(sos_demande.visite)=' . $this->year . $this->r_service . ' group BY id_from_femme) as raq')->result();
                $data[count($data) - 1] = array_merge($data[count($data) - 1], [
                    'num_vis' => $result_child[0]->num
                ]);
                $query_child_child = $this->db->get_where('sos_gen_emplois_child_child', ['id_emplois_from_child' => $value_child['id_emplois_detailles']
                ]);
                $row_gen_child_child = $query_child_child->result_array();
                foreach ($row_gen_child_child as $key => $value_child_child) {
                    $result_child_child = $this->db->query('SELECT count(*) as `num`
            FROM sos_femme
            WHERE sos_femme.emplois_more_detailles =' . $value_child_child['id_emplois_child_child'] . ' and sos_femme.emplois_detailles =' . $value_child['id_emplois_detailles'] . ' and sos_femme.emplois =' . $value['id_emplois'] . ' AND year(`sos_femme`.`premier_contact`)=' . $this->year . $this->r_femme_service)->result();
                    $data[] = ['emplois_stat' => '              ' . $value_child_child['name_emplois_child_child'],
                        'num' => $result_child_child[0]->num
                    ];
                    $result_child_child = $this->db->query('select count(*) as num from '
                                    . '(select emplois_more_detailles from sos_femme inner join sos_demande on sos_demande.id_from_femme = sos_femme.id_femme'
                                    . ' where sos_femme.emplois_more_detailles =' . $value_child_child['id_emplois_child_child'] . ' and sos_femme.emplois_detailles =' . $value_child['id_emplois_detailles'] . ' and sos_femme.emplois =' . $value['id_emplois'] . ' and year(sos_demande.visite)=' . $this->year . $this->r_service . ' group BY id_from_femme) as raq')->result();
                    $data[count($data) - 1] = array_merge($data[count($data) - 1], [
                        'num_vis' => $result_child_child[0]->num
                    ]);
                }
            }
        }
        $data[] = ['emplois_stat' => 'Non dit',
            'num' => ($this->total_femmes_var - $tot) . ' sur ' . $this->total_femmes_var . '(' . (round(($this->total_femmes_var - $tot) * 100 / $this->total_femmes_var)) . '%)',
            'num_vis' => ($this->total_femmes_var_visites - $tot_vis) . ' sur ' . $this->total_femmes_var_visites . ' (' . @((round(($this->total_femmes_var_visites - $tot_vis) * 100 / $this->total_femmes_var_visites))) . '%)',
        ];
        $this->tbswrapper->TBS->MergeBlock('emplois_stat', $data);
    }

    private function table_25() {
        /*
         * violences phisiques
         */
        $result = $this->db->query('select * from sos_relation_violences_physiques,sos_violences,sos_femme ' . ' where sos_relation_violences_physiques.id_from_violences=sos_violences.id_violences and ' . ' sos_femme.id_femme = sos_violences.id_from_femme' . ' and year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service . ' group BY sos_relation_violences_physiques.id_from_violences ASC')->result();
        $total_femmes_vic = count($result);
        $data = [];
        $query = $this->db->get('sos_gen_violences_physiques');
        $row_gen_violences_physiques = $query->result_array();
        $tot = 0;
        foreach ($row_gen_violences_physiques as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_violences_physiques 
                 left join sos_violences on sos_violences.id_violences=sos_relation_violences_physiques.id_from_violences 
                 left join sos_femme on sos_violences.id_from_femme=sos_femme.id_femme 
                 where sos_relation_violences_physiques.id_from_violences_physiques = ' . $value['id_violences_physiques'] . ' and year(`sos_femme`.`premier_contact`)= ' . $this->year . $this->r_femme_service)->result();
            if (count($result) > 0) {
                $data[] = ['violences_physiques_stat' => $value['name_violences_physiques'],
                    'num' => $result[0]->num
                ];
                $tot = $tot + 1;
            } else {
                $data[] = ['violences_physiques_stat' => $value['name_violences_physiques'],
                    'num' => 0
                ];
            }
        }
        $data[] = ['violences_physiques_stat' => 'Total des femmes victimes des violences physiques : ' . $total_femmes_vic,
            'num' => (round(($total_femmes_vic) * 100 / $this->total_femmes_var)) . '%'
        ];
        $this->tbswrapper->TBS->MergeBlock('violences_physiques_stat', $data);
    }

    private function table_251() {
        /*
         * violences phisiques
         */

        $result = $this->db->query('select * from sos_relation_violences_physiques,sos_violences,sos_demande where sos_relation_violences_physiques.id_from_violences=sos_violences.id_violences and  
sos_demande.id_from_femme = sos_violences.id_from_femme and 
year(sos_demande.visite)=' . $this->year
                        . $this->r_service .
                        ' group BY sos_relation_violences_physiques.id_from_violences ASC')->result();
        $total_femmes_vic = count($result);
        $data = [];
        $query = $this->db->get('sos_gen_violences_physiques');
        $row_gen_violences_physiques = $query->result_array();
        $tot = 0;
        foreach ($row_gen_violences_physiques as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_violences_physiques 
                 left join sos_violences on sos_violences.id_violences=sos_relation_violences_physiques.id_from_violences 
                 left join sos_demande on sos_violences.id_from_femme=sos_demande.id_from_femme 
                 where sos_relation_violences_physiques.id_from_violences_physiques = ' . $value['id_violences_physiques'] . ' and year(`sos_demande`.`visite`)= ' . $this->year . $this->r_service . ' group by sos_demande.id_from_femme')->result();
            $data[] = ['violences_physiques_stat_visites' => $value['name_violences_physiques'],
                'num' => count($result)
            ];
        }
        $data[] = ['violences_physiques_stat_visites' => 'Total des femmes victimes des violences physiques : ' . $total_femmes_vic,
            'num' => @((round(($total_femmes_vic) * 100 / $this->total_femmes_var_visites))) . '%'
        ];
        $this->tbswrapper->TBS->MergeBlock('violences_physiques_stat_visites', $data);
    }

    private function table_26() {
        /*
         * violences psy
         */
        $result = $this->db->query('select * from sos_relation_violences_psychologiques,sos_violences,sos_femme ' . ' where sos_relation_violences_psychologiques.id_from_violences=sos_violences.id_violences and ' . ' sos_femme.id_femme = sos_violences.id_from_femme' . ' and year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service . ' group BY sos_relation_violences_psychologiques.id_from_violences ASC')->result();
        $total_femmes_vic = count($result);
        $data = [];
        $query = $this->db->get('sos_gen_violences_psychologiques');
        $row_gen_violences_psychologiques = $query->result_array();
        $tot = 0;
        foreach ($row_gen_violences_psychologiques as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_violences_psychologiques 
                 left join sos_violences on sos_violences.id_violences=sos_relation_violences_psychologiques.id_from_violences 
                 left join sos_femme on sos_violences.id_from_femme=sos_femme.id_femme 
                 where sos_relation_violences_psychologiques.id_from_violences_psychologiques = ' . $value['id_violences_psychologiques'] . ' and year(`sos_femme`.`premier_contact`)= ' . $this->year . $this->r_femme_service)->result();
            if (count($result) > 0) {
                $data[] = ['violences_psychologiques_stat' => $value['name_violences_psychologiques'],
                    'num' => $result[0]->num
                ];
                $tot = $tot + 1;
            } else {
                $data[] = ['violences_psychologiques_stat' => $value['name_violences_psychologiques'],
                    'num' => 0
                ];
            }
        }
        $data[] = ['violences_psychologiques_stat' => 'Total des femmes victimes des violences psychologiques : ' . $total_femmes_vic,
            'num' => (round(($total_femmes_vic) * 100 / $this->total_femmes_var)) . '%'
        ];
        $this->tbswrapper->TBS->MergeBlock('violences_psychologiques_stat', $data);
    }

    private function table_261() {
        /*
         * violences psy
         */
        $result = $this->db->query('select * from sos_relation_violences_psychologiques,sos_violences,sos_demande where sos_relation_violences_psychologiques.id_from_violences=sos_violences.id_violences and  
sos_demande.id_from_femme = sos_violences.id_from_femme and 
year(sos_demande.visite)=' . $this->year
                        . $this->r_service .
                        ' group BY sos_relation_violences_psychologiques.id_from_violences ASC')->result();
        $total_femmes_vic = count($result);
        $data = [];
        $query = $this->db->get('sos_gen_violences_psychologiques');
        $row_gen_violences_psychologiques = $query->result_array();
        $tot = 0;
        foreach ($row_gen_violences_psychologiques as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_violences_psychologiques 
                 left join sos_violences on sos_violences.id_violences=sos_relation_violences_psychologiques.id_from_violences 
                 left join sos_demande on sos_violences.id_from_femme=sos_demande.id_from_femme 
                 where sos_relation_violences_psychologiques.id_from_violences_psychologiques = ' . $value['id_violences_psychologiques'] . ' and year(`sos_demande`.`visite`)= ' . $this->year . $this->r_service . ' group by sos_demande.id_from_femme')->result();
            $data[] = ['violences_psychologiques_stat_visites' => $value['name_violences_psychologiques'],
                'num' => count($result)
            ];
        }
        $data[] = ['violences_psychologiques_stat_visites' => 'Total des femmes victimes des violences psychologiques : ' . $total_femmes_vic,
            'num' => @((round(($total_femmes_vic) * 100 / $this->total_femmes_var_visites))) . '%'
        ];
        $this->tbswrapper->TBS->MergeBlock('violences_psychologiques_stat_visites', $data);
    }

    private function table_27() {
        /*
         * violences sex
         */
        $result = $this->db->query('select * from sos_relation_violences_sexuelles,sos_violences,sos_femme ' . ' where sos_relation_violences_sexuelles.id_from_violences=sos_violences.id_violences and ' . ' sos_femme.id_femme = sos_violences.id_from_femme' . ' and year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service . ' group BY sos_relation_violences_sexuelles.id_from_violences ASC')->result();
        $total_femmes_vic = count($result);
        $data = [];
        $query = $this->db->get('sos_gen_violences_sexuelles');
        $row_gen_violences_sexuelles = $query->result_array();
        $tot = 0;
        foreach ($row_gen_violences_sexuelles as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_violences_sexuelles 
                 left join sos_violences on sos_violences.id_violences=sos_relation_violences_sexuelles.id_from_violences 
                 left join sos_femme on sos_violences.id_from_femme=sos_femme.id_femme 
                 where sos_relation_violences_sexuelles.id_from_violences_sexuelles = ' . $value['id_violences_sexuelles'] . ' and year(`sos_femme`.`premier_contact`)= ' . $this->year . $this->r_femme_service)->result();
            if (count($result) > 0) {
                $data[] = ['violences_sexuelles_stat' => $value['name_violences_sexuelles'],
                    'num' => $result[0]->num
                ];
                $tot = $tot + 1;
            } else {
                $data[] = ['violences_sexuelles_stat' => $value['name_violences_sexuelles'],
                    'num' => 0
                ];
            }
        }
        $data[] = ['violences_sexuelles_stat' => 'Total des femmes victimes des violences sexuelles : ' . $total_femmes_vic,
            'num' => (round(($total_femmes_vic) * 100 / $this->total_femmes_var)) . '%'
        ];
        $this->tbswrapper->TBS->MergeBlock('violences_sexuelles_stat', $data);
    }

    private function table_271() {
        /*
         * violences sex
         */


        $result = $this->db->query('select * from sos_relation_violences_sexuelles,sos_violences,sos_demande where sos_relation_violences_sexuelles.id_from_violences=sos_violences.id_violences and  
sos_demande.id_from_femme = sos_violences.id_from_femme and 
year(sos_demande.visite)=' . $this->year
                        . $this->r_service .
                        ' group BY sos_relation_violences_sexuelles.id_from_violences ASC')->result();
        $total_femmes_vic = count($result);
        $data = [];
        $query = $this->db->get('sos_gen_violences_sexuelles');
        $row_gen_violences_sexuelles = $query->result_array();
        $tot = 0;
        foreach ($row_gen_violences_sexuelles as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_violences_sexuelles 
                 left join sos_violences on sos_violences.id_violences=sos_relation_violences_sexuelles.id_from_violences 
                 left join sos_demande on sos_violences.id_from_femme=sos_demande.id_from_femme 
                 where sos_relation_violences_sexuelles.id_from_violences_sexuelles = ' . $value['id_violences_sexuelles'] . ' and year(`sos_demande`.`visite`)= ' . $this->year . $this->r_service . ' group by sos_demande.id_from_femme')->result();
            $data[] = ['violences_sexuelles_stat_visites' => $value['name_violences_sexuelles'],
                'num' => count($result)
            ];
        }
        $data[] = ['violences_sexuelles_stat_visites' => 'Total des femmes victimes des violences sexuelles : ' . $total_femmes_vic,
            'num' => @((round(($total_femmes_vic) * 100 / $this->total_femmes_var_visites))) . '%'
        ];
        $this->tbswrapper->TBS->MergeBlock('violences_sexuelles_stat_visites', $data);
    }

    private function table_28() {
        /*
         * violences eco
         */
        $result = $this->db->query('select * from sos_relation_violences_economiques,sos_violences,sos_femme ' . ' where sos_relation_violences_economiques.id_from_violences=sos_violences.id_violences and ' . ' sos_femme.id_femme = sos_violences.id_from_femme' . ' and year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service . ' group BY sos_relation_violences_economiques.id_from_violences ASC')->result();
        $total_femmes_vic = count($result);
        $data = [];
        $query = $this->db->get('sos_gen_violences_economiques');
        $row_gen_violences_economiques = $query->result_array();
        $tot = 0;
        foreach ($row_gen_violences_economiques as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_violences_economiques 
                 left join sos_violences on sos_violences.id_violences=sos_relation_violences_economiques.id_from_violences 
                 left join sos_femme on sos_violences.id_from_femme=sos_femme.id_femme 
                 where sos_relation_violences_economiques.id_from_violences_economiques = ' . $value['id_violences_economiques'] . ' and year(`sos_femme`.`premier_contact`)= ' . $this->year . $this->r_femme_service)->result();
            if (count($result) > 0) {
                $data[] = ['violences_economiques_stat' => $value['name_violences_economiques'],
                    'num' => $result[0]->num
                ];
                $tot = $tot + 1;
            } else {
                $data[] = ['violences_economiques_stat' => $value['name_violences_economiques'],
                    'num' => 0
                ];
            }
        }
        $data[] = ['violences_economiques_stat' => 'Total des femmes victimes des violences economiques : ' . $total_femmes_vic,
            'num' => (round(($total_femmes_vic) * 100 / $this->total_femmes_var)) . '%'
        ];
        $this->tbswrapper->TBS->MergeBlock('violences_economiques_stat', $data);
    }

    private function table_281() {
        /*
         * violences eco
         */


        $result = $this->db->query('select * from sos_relation_violences_economiques,sos_violences,sos_demande where sos_relation_violences_economiques.id_from_violences=sos_violences.id_violences and  
sos_demande.id_from_femme = sos_violences.id_from_femme and 
year(sos_demande.visite)=' . $this->year
                        . $this->r_service .
                        ' group BY sos_relation_violences_economiques.id_from_violences ASC')->result();
        $total_femmes_vic = count($result);
        $data = [];
        $query = $this->db->get('sos_gen_violences_economiques');
        $row_gen_violences_economiques = $query->result_array();
        $tot = 0;
        foreach ($row_gen_violences_economiques as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_violences_economiques 
                 left join sos_violences on sos_violences.id_violences=sos_relation_violences_economiques.id_from_violences 
                 left join sos_demande on sos_violences.id_from_femme=sos_demande.id_from_femme 
                 where sos_relation_violences_economiques.id_from_violences_economiques = ' . $value['id_violences_economiques'] . ' and year(`sos_demande`.`visite`)= ' . $this->year . $this->r_service . ' group by sos_demande.id_from_femme')->result();
            $data[] = ['violences_economiques_stat_visites' => $value['name_violences_economiques'],
                'num' => count($result)
            ];
        }
        $data[] = ['violences_economiques_stat_visites' => 'Total des femmes victimes des violences economiques : ' . $total_femmes_vic,
            'num' => @((round(($total_femmes_vic) * 100 / $this->total_femmes_var_visites))) . '%'
        ];
        $this->tbswrapper->TBS->MergeBlock('violences_economiques_stat_visites', $data);
    }

    private function table_29() {
        /*
         * violences admin
         */
        $result = $this->db->query('select * from sos_relation_violences_administratives,sos_violences,sos_femme ' . ' where sos_relation_violences_administratives.id_from_violences=sos_violences.id_violences and ' . ' sos_femme.id_femme = sos_violences.id_from_femme' . ' and year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service . ' group BY sos_relation_violences_administratives.id_from_violences ASC')->result();
        $total_femmes_vic = count($result);
        $data = [];
        $query = $this->db->get('sos_gen_violences_administratives');
        $row_gen_violences_administratives = $query->result_array();
        $tot = 0;
        foreach ($row_gen_violences_administratives as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_violences_administratives 
                 left join sos_violences on sos_violences.id_violences=sos_relation_violences_administratives.id_from_violences 
                 left join sos_femme on sos_violences.id_from_femme=sos_femme.id_femme 
                 where sos_relation_violences_administratives.id_from_violences_administratives = ' . $value['id_violences_administratives'] . ' and year(`sos_femme`.`premier_contact`)= ' . $this->year . $this->r_femme_service)->result();
            if (count($result) > 0) {
                $data[] = ['violences_administratives_stat' => $value['name_violences_administratives'],
                    'num' => $result[0]->num
                ];
                $tot = $tot + 1;
            } else {
                $data[] = ['violences_administratives_stat' => $value['name_violences_administratives'],
                    'num' => 0
                ];
            }
        }
        $data[] = ['violences_administratives_stat' => 'Total des femmes victimes des violences administratives : ' . $total_femmes_vic,
            'num' => (round(($total_femmes_vic) * 100 / $this->total_femmes_var)) . '%'
        ];
        $this->tbswrapper->TBS->MergeBlock('violences_administratives_stat', $data);
    }

    private function table_291() {
        /*
         * violences admin
         */
        $result = $this->db->query('select * from sos_relation_violences_administratives,sos_violences,sos_demande where sos_relation_violences_administratives.id_from_violences=sos_violences.id_violences and  
sos_demande.id_from_femme = sos_violences.id_from_femme and 
year(sos_demande.visite)=' . $this->year
                        . $this->r_service .
                        ' group BY sos_relation_violences_administratives.id_from_violences ASC')->result();
        $total_femmes_vic = count($result);
        $data = [];
        $query = $this->db->get('sos_gen_violences_administratives');
        $row_gen_violences_administratives = $query->result_array();
        $tot = 0;
        foreach ($row_gen_violences_administratives as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_violences_administratives 
                 left join sos_violences on sos_violences.id_violences=sos_relation_violences_administratives.id_from_violences 
                 left join sos_demande on sos_violences.id_from_femme=sos_demande.id_from_femme 
                 where sos_relation_violences_administratives.id_from_violences_administratives = ' . $value['id_violences_administratives'] . ' and year(`sos_demande`.`visite`)= ' . $this->year . $this->r_service . ' group by sos_demande.id_from_femme')->result();
            $data[] = ['violences_administratives_stat_visites' => $value['name_violences_administratives'],
                'num' => count($result)
            ];
        }
        $data[] = ['violences_administratives_stat_visites' => 'Total des femmes victimes des violences administratives : ' . $total_femmes_vic,
            'num' => @((round(($total_femmes_vic) * 100 / $this->total_femmes_var_visites))) . '%'
        ];
        $this->tbswrapper->TBS->MergeBlock('violences_administratives_stat_visites', $data);
    }

    private function table_30() {
        /*
         * violences soc
         */
        $result = $this->db->query('select * from sos_relation_violences_sociales,sos_violences,sos_femme ' . ' where sos_relation_violences_sociales.id_from_violences=sos_violences.id_violences and ' . ' sos_femme.id_femme = sos_violences.id_from_femme' . ' and year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service . ' group BY sos_relation_violences_sociales.id_from_violences ASC')->result();
        $total_femmes_vic = count($result);
        $data = [];
        $query = $this->db->get('sos_gen_violences_sociales');
        $row_gen_violences_sociales = $query->result_array();
        $tot = 0;
        foreach ($row_gen_violences_sociales as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_violences_sociales 
                 left join sos_violences on sos_violences.id_violences=sos_relation_violences_sociales.id_from_violences 
                 left join sos_femme on sos_violences.id_from_femme=sos_femme.id_femme 
                 where sos_relation_violences_sociales.id_from_violences_sociales = ' . $value['id_violences_sociales'] . ' and year(`sos_femme`.`premier_contact`)= ' . $this->year . $this->r_femme_service)->result();
            if (count($result) > 0) {
                $data[] = ['violences_sociales_stat' => $value['name_violences_sociales'],
                    'num' => $result[0]->num
                ];
                $tot = $tot + 1;
            } else {
                $data[] = ['violences_sociales_stat' => $value['name_violences_sociales'],
                    'num' => 0
                ];
            }
        }
        $data[] = ['violences_sociales_stat' => 'Total des femmes victimes des violences sociales : ' . $total_femmes_vic,
            'num' => (round(($total_femmes_vic) * 100 / $this->total_femmes_var)) . '%'
        ];
        $this->tbswrapper->TBS->MergeBlock('violences_sociales_stat', $data);
    }

    private function table_301() {
        /*
         * violences soc
         */

        $result = $this->db->query('select * from sos_relation_violences_sociales,sos_violences,sos_demande where sos_relation_violences_sociales.id_from_violences=sos_violences.id_violences and  
sos_demande.id_from_femme = sos_violences.id_from_femme and 
year(sos_demande.visite)=' . $this->year
                        . $this->r_service .
                        ' group BY sos_relation_violences_sociales.id_from_violences ASC')->result();
        $total_femmes_vic = count($result);
        $data = [];
        $query = $this->db->get('sos_gen_violences_sociales');
        $row_gen_violences_sociales = $query->result_array();
        $tot = 0;
        foreach ($row_gen_violences_sociales as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_violences_sociales 
                 left join sos_violences on sos_violences.id_violences=sos_relation_violences_sociales.id_from_violences 
                 left join sos_demande on sos_violences.id_from_femme=sos_demande.id_from_femme 
                 where sos_relation_violences_sociales.id_from_violences_sociales = ' . $value['id_violences_sociales'] . ' and year(`sos_demande`.`visite`)= ' . $this->year . $this->r_service . ' group by sos_demande.id_from_femme')->result();
            $data[] = ['violences_sociales_stat_visites' => $value['name_violences_sociales'],
                'num' => count($result)
            ];
        }
        $data[] = ['violences_sociales_stat_visites' => 'Total des femmes victimes des violences sociales : ' . $total_femmes_vic,
            'num' => @((round(($total_femmes_vic) * 100 / $this->total_femmes_var_visites))) . '%'
        ];
        $this->tbswrapper->TBS->MergeBlock('violences_sociales_stat_visites', $data);
    }

    private function table_31() {
        /*
         * violences priv
         */
        $result = $this->db->query('select * from sos_relation_violences_privations,sos_violences,sos_femme ' . ' where sos_relation_violences_privations.id_from_violences=sos_violences.id_violences and ' . ' sos_femme.id_femme = sos_violences.id_from_femme' . ' and year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service . ' group BY sos_relation_violences_privations.id_from_violences ASC')->result();
        $total_femmes_vic = count($result);
        $data = [];
        $query = $this->db->get('sos_gen_violences_privations');
        $row_gen_violences_privations = $query->result_array();
        $tot = 0;
        foreach ($row_gen_violences_privations as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_violences_privations 
                 left join sos_violences on sos_violences.id_violences=sos_relation_violences_privations.id_from_violences 
                 left join sos_femme on sos_violences.id_from_femme=sos_femme.id_femme 
                 where sos_relation_violences_privations.id_from_violences_privations = ' . $value['id_violences_privations'] . ' and year(`sos_femme`.`premier_contact`)= ' . $this->year . $this->r_femme_service)->result();
            if (count($result) > 0) {
                $data[] = ['violences_privations_stat' => $value['name_violences_privations'],
                    'num' => $result[0]->num
                ];
                $tot = $tot + 1;
            } else {
                $data[] = ['violences_privations_stat' => $value['name_violences_privations'],
                    'num' => 0
                ];
            }
        }
        $data[] = ['violences_privations_stat' => 'Total des femmes victimes des violences privations : ' . $total_femmes_vic,
            'num' => (round(($total_femmes_vic) * 100 / $this->total_femmes_var)) . '%'
        ];
        $this->tbswrapper->TBS->MergeBlock('violences_privations_stat', $data);
    }

    private function table_311() {
        /*
         * violences priv
         */

        $result = $this->db->query('select * from sos_relation_violences_privations,sos_violences,sos_demande where sos_relation_violences_privations.id_from_violences=sos_violences.id_violences and  
sos_demande.id_from_femme = sos_violences.id_from_femme and 
year(sos_demande.visite)=' . $this->year
                        . $this->r_service .
                        ' group BY sos_relation_violences_privations.id_from_violences ASC')->result();
        $total_femmes_vic = count($result);
        $data = [];
        $query = $this->db->get('sos_gen_violences_privations');
        $row_gen_violences_privations = $query->result_array();
        $tot = 0;
        foreach ($row_gen_violences_privations as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_violences_privations 
                 left join sos_violences on sos_violences.id_violences=sos_relation_violences_privations.id_from_violences 
                 left join sos_demande on sos_violences.id_from_femme=sos_demande.id_from_femme 
                 where sos_relation_violences_privations.id_from_violences_privations = ' . $value['id_violences_privations'] . ' and year(`sos_demande`.`visite`)= ' . $this->year . $this->r_service . ' group by sos_demande.id_from_femme')->result();
            $data[] = ['violences_privations_stat_visites' => $value['name_violences_privations'],
                'num' => count($result)
            ];
        }
        $data[] = array(
            'violences_privations_stat_visites' => 'Total des femmes victimes des violences privations : ' . $total_femmes_vic,
            'num' => @((round(($total_femmes_vic) * 100 / $this->total_femmes_var_visites))) . '%'
        );
        $this->tbswrapper->TBS->MergeBlock('violences_privations_stat_visites', $data);
    }

    private function table_32() {
        /*
         * violences juri
         */
        $result = $this->db->query('select * from sos_relation_violences_juridiques,sos_violences,sos_femme ' . ' where sos_relation_violences_juridiques.id_from_violences=sos_violences.id_violences and ' . ' sos_femme.id_femme = sos_violences.id_from_femme' . ' and year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service . ' group BY sos_relation_violences_juridiques.id_from_violences ASC')->result();
        $total_femmes_vic = count($result);
        $data = [];
        $query = $this->db->get('sos_gen_violences_juridiques');
        $row_gen_violences_juridiques = $query->result_array();
        $tot = 0;
        foreach ($row_gen_violences_juridiques as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_violences_juridiques 
                 left join sos_violences on sos_violences.id_violences=sos_relation_violences_juridiques.id_from_violences 
                 left join sos_femme on sos_violences.id_from_femme=sos_femme.id_femme 
                 where sos_relation_violences_juridiques.id_from_violences_juridiques = ' . $value['id_violences_juridiques'] . ' and year(`sos_femme`.`premier_contact`)= ' . $this->year . $this->r_femme_service)->result();
            if (count($result) > 0) {
                $data[] = array(
                    'violences_juridiques_stat' => $value['name_violences_juridiques'],
                    'num' => $result[0]->num
                );
                $tot = $tot + 1;
            } else {
                $data[] = array(
                    'violences_juridiques_stat' => $value['name_violences_juridiques'],
                    'num' => 0
                );
            }
        }
        $data[] = ['violences_juridiques_stat' => 'Total des femmes victimes des violences juridiques : ' . $total_femmes_vic,
            'num' => (round(($total_femmes_vic) * 100 / $this->total_femmes_var)) . '%'
        ];
        $this->tbswrapper->TBS->MergeBlock('violences_juridiques_stat', $data);
    }

    private function table_321() {
        /*
         * violences juri
         */

        $result = $this->db->query('select * from sos_relation_violences_juridiques,sos_violences,sos_demande where sos_relation_violences_juridiques.id_from_violences=sos_violences.id_violences and  
sos_demande.id_from_femme = sos_violences.id_from_femme and 
year(sos_demande.visite)=' . $this->year
                        . $this->r_service .
                        ' group BY sos_relation_violences_juridiques.id_from_violences ASC')->result();
        $total_femmes_vic = count($result);
        $data = [];
        $query = $this->db->get('sos_gen_violences_juridiques');
        $row_gen_violences_juridiques = $query->result_array();
        $tot = 0;
        foreach ($row_gen_violences_juridiques as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_violences_juridiques 
                 left join sos_violences on sos_violences.id_violences=sos_relation_violences_juridiques.id_from_violences 
                 left join sos_demande on sos_violences.id_from_femme=sos_demande.id_from_femme 
                 where sos_relation_violences_juridiques.id_from_violences_juridiques = ' . $value['id_violences_juridiques'] . ' and year(`sos_demande`.`visite`)= ' . $this->year . $this->r_service . ' group by sos_demande.id_from_femme')->result();
            $data[] = ['violences_juridiques_stat_visites' => $value['name_violences_juridiques'],
                'num' => count($result)
            ];
        }
        $data[] = ['violences_juridiques_stat_visites' => 'Total des femmes victimes des violences juridiques : ' . $total_femmes_vic,
            'num' => @((round(($total_femmes_vic) * 100 / $this->total_femmes_var_visites))) . '%'
        ];
        $this->tbswrapper->TBS->MergeBlock('violences_juridiques_stat_visites', $data);
    }

    private function table_33() {
        /*
         * consequances admi
         */
        $result = $this->db->query('select * from sos_relation_consequences_administratives,sos_violences,sos_femme ' . ' where sos_relation_consequences_administratives.id_from_violences=sos_violences.id_violences and ' . ' sos_femme.id_femme = sos_violences.id_from_femme' . ' and year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service . ' group BY sos_relation_consequences_administratives.id_from_violences ASC')->result();
        $total_femmes_vic = count($result);
        $data = [];
        $query = $this->db->get('sos_gen_consequences_administratives');
        $row_gen_consequences_administratives = $query->result_array();
        $tot = 0;
        foreach ($row_gen_consequences_administratives as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_consequences_administratives 
                 left join sos_violences on sos_violences.id_violences=sos_relation_consequences_administratives.id_from_violences 
                 left join sos_femme on sos_violences.id_from_femme=sos_femme.id_femme 
                 where sos_relation_consequences_administratives.id_from_consequences_administratives = ' . $value['id_consequences_administratives'] . ' and year(`sos_femme`.`premier_contact`)= ' . $this->year . $this->r_femme_service)->result();
            if (count($result) > 0) {
                $data[] = ['consequences_administratives_stat' => $value['name_consequences_administratives'],
                    'num' => $result[0]->num
                ];
                $tot = $tot + 1;
            } else {
                $data[] = ['consequences_administratives_stat' => $value['name_consequences_administratives'],
                    'num' => 0
                ];
            }
        }
        $data[] = ['consequences_administratives_stat' => 'Total des femmes avec concequences administratives : ' . $total_femmes_vic,
            'num' => (round(($total_femmes_vic) * 100 / $this->total_femmes_var)) . '%'
        ];
        $this->tbswrapper->TBS->MergeBlock('consequences_administratives_stat', $data);
    }

    private function table_331() {
        /*
         *  consequances admi
         */

        $result = $this->db->query('select * from sos_relation_consequences_administratives,sos_violences,sos_demande where sos_relation_consequences_administratives.id_from_violences=sos_violences.id_violences and  
sos_demande.id_from_femme = sos_violences.id_from_femme and 
year(sos_demande.visite)=' . $this->year
                        . $this->r_service .
                        ' group BY sos_relation_consequences_administratives.id_from_violences ASC')->result();
        $total_femmes_vic = count($result);
        $data = [];
        $query = $this->db->get('sos_gen_consequences_administratives');
        $row_gen_consequences_administratives = $query->result_array();
        $tot = 0;
        foreach ($row_gen_consequences_administratives as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_consequences_administratives 
                 left join sos_violences on sos_violences.id_violences=sos_relation_consequences_administratives.id_from_violences 
                 left join sos_demande on sos_violences.id_from_femme=sos_demande.id_from_femme 
                 where sos_relation_consequences_administratives.id_from_consequences_administratives = ' . $value['id_consequences_administratives'] . ' and year(`sos_demande`.`visite`)= ' . $this->year . $this->r_service . ' group by sos_demande.id_from_femme')->result();
            $data[] = ['consequences_administratives_stat' => $value['name_consequences_administratives'],
                'num' => count($result)
            ];
        }
        $data[] = ['consequences_administratives_stat' => 'Total des femmes victimes des consequences administratives : ' . $total_femmes_vic . ' sur ' . $this->total_femmes_var_visites . ' reçu',
            'num' => @((round(($total_femmes_vic) * 100 / $this->total_femmes_var_visites))) . '%'
        ];
        $this->tbswrapper->TBS->MergeBlock('consequences_administratives_stat', $data);
    }

    private function table_34() {
        /*
         * consequances phy
         */
        $result = $this->db->query('select * from sos_relation_consequences_physiques,sos_violences,sos_femme ' . ' where sos_relation_consequences_physiques.id_from_violences=sos_violences.id_violences and ' . ' sos_femme.id_femme = sos_violences.id_from_femme' . ' and year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service . ' group BY sos_relation_consequences_physiques.id_from_violences ASC')->result();
        $total_femmes_vic = count($result);
        $data = [];
        $query = $this->db->get('sos_gen_consequences_physiques');
        $row_gen_consequences_physiques = $query->result_array();
        $tot = 0;
        foreach ($row_gen_consequences_physiques as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_consequences_physiques 
                 left join sos_violences on sos_violences.id_violences=sos_relation_consequences_physiques.id_from_violences 
                 left join sos_femme on sos_violences.id_from_femme=sos_femme.id_femme 
                 where sos_relation_consequences_physiques.id_from_consequences_physiques = ' . $value['id_consequences_physiques'] . ' and year(`sos_femme`.`premier_contact`)= ' . $this->year . $this->r_femme_service)->result();
            if (count($result) > 0) {
                $data[] = ['consequences_physiques_stat' => $value['name_consequences_physiques'],
                    'num' => $result[0]->num
                ];
                $tot = $tot + 1;
            } else {
                $data[] = ['consequences_physiques_stat' => $value['name_consequences_physiques'],
                    'num' => 0
                ];
            }
        }
        $data[] = ['consequences_physiques_stat' => 'Total des femmes avec concequences physiques : ' . $total_femmes_vic,
            'num' => (round(($total_femmes_vic) * 100 / $this->total_femmes_var)) . '%'
        ];
        $this->tbswrapper->TBS->MergeBlock('consequences_physiques_stat', $data);
    }

    private function table_341() {
        /*
         *  consequances phy
         */

        $result = $this->db->query('select * from sos_relation_consequences_physiques,sos_violences,sos_demande where sos_relation_consequences_physiques.id_from_violences=sos_violences.id_violences and  
sos_demande.id_from_femme = sos_violences.id_from_femme and 
year(sos_demande.visite)=' . $this->year
                        . $this->r_service .
                        ' group BY sos_relation_consequences_physiques.id_from_violences ASC')->result();
        $total_femmes_vic = count($result);
        $data = [];
        $query = $this->db->get('sos_gen_consequences_physiques');
        $row_gen_consequences_physiques = $query->result_array();
        $tot = 0;
        foreach ($row_gen_consequences_physiques as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_consequences_physiques 
                 left join sos_violences on sos_violences.id_violences=sos_relation_consequences_physiques.id_from_violences 
                 left join sos_demande on sos_violences.id_from_femme=sos_demande.id_from_femme 
                 where sos_relation_consequences_physiques.id_from_consequences_physiques = ' . $value['id_consequences_physiques'] . ' and year(`sos_demande`.`visite`)= ' . $this->year . $this->r_service . ' group by sos_demande.id_from_femme')->result();
            $data[] = ['consequences_physiques_stat' => $value['name_consequences_physiques'],
                'num' => count($result)
            ];
        }
        $data[] = ['consequences_physiques_stat' => 'Total des femmes victimes des consequences physiques : ' . $total_femmes_vic . ' sur ' . $this->total_femmes_var_visites . ' reçu',
            'num' => @((round(($total_femmes_vic) * 100 / $this->total_femmes_var_visites))) . '%'
        ];
        $this->tbswrapper->TBS->MergeBlock('consequences_physiques_stat', $data);
    }

    private function table_35() {
        /*
         * consequances psy
         */
        $result = $this->db->query('select * from sos_relation_consequences_psychologiques,sos_violences,sos_femme ' . ' where sos_relation_consequences_psychologiques.id_from_violences=sos_violences.id_violences and ' . ' sos_femme.id_femme = sos_violences.id_from_femme' . ' and year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service . ' group BY sos_relation_consequences_psychologiques.id_from_violences ASC')->result();
        $total_femmes_vic = count($result);
        $data = [];
        $query = $this->db->get('sos_gen_consequences_psychologiques');
        $row_gen_consequences_psychologiques = $query->result_array();
        $tot = 0;
        foreach ($row_gen_consequences_psychologiques as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_consequences_psychologiques 
                 left join sos_violences on sos_violences.id_violences=sos_relation_consequences_psychologiques.id_from_violences 
                 left join sos_femme on sos_violences.id_from_femme=sos_femme.id_femme 
                 where sos_relation_consequences_psychologiques.id_from_consequences_psychologiques = ' . $value['id_consequences_psychologiques'] . ' and year(`sos_femme`.`premier_contact`)= ' . $this->year . $this->r_femme_service)->result();
            if (count($result) > 0) {
                $data[] = ['consequences_psychologiques_stat' => $value['name_consequences_psychologiques'],
                    'num' => $result[0]->num
                ];
                $tot = $tot + 1;
            } else {
                $data[] = ['consequences_psychologiques_stat' => $value['name_consequences_psychologiques'],
                    'num' => 0
                ];
            }
        }
        $data[] = ['consequences_psychologiques_stat' => 'Total des femmes avec concequences psychologiques : ' . $total_femmes_vic,
            'num' => (round(($total_femmes_vic) * 100 / $this->total_femmes_var)) . '%'
        ];
        $this->tbswrapper->TBS->MergeBlock('consequences_psychologiques_stat', $data);
    }

    private function table_351() {
        /*
         * violences juri
         */

        $result = $this->db->query('select * from sos_relation_consequences_psychologiques,sos_violences,sos_demande where sos_relation_consequences_psychologiques.id_from_violences=sos_violences.id_violences and  
sos_demande.id_from_femme = sos_violences.id_from_femme and 
year(sos_demande.visite)=' . $this->year
                        . $this->r_service .
                        ' group BY sos_relation_consequences_psychologiques.id_from_violences ASC')->result();
        $total_femmes_vic = count($result);
        $data = [];
        $query = $this->db->get('sos_gen_consequences_psychologiques');
        $row_gen_consequences_psychologiques = $query->result_array();
        $tot = 0;
        foreach ($row_gen_consequences_psychologiques as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_consequences_psychologiques 
                 left join sos_violences on sos_violences.id_violences=sos_relation_consequences_psychologiques.id_from_violences 
                 left join sos_demande on sos_violences.id_from_femme=sos_demande.id_from_femme 
                 where sos_relation_consequences_psychologiques.id_from_consequences_psychologiques = ' . $value['id_consequences_psychologiques'] . ' and year(`sos_demande`.`visite`)= ' . $this->year . $this->r_service . ' group by sos_demande.id_from_femme')->result();
            $data[] = ['consequences_psychologiques_stat' => $value['name_consequences_psychologiques'],
                'num' => count($result)
            ];
        }
        $data[] = ['consequences_psychologiques_stat' => 'Total des femmes victimes des consequences psychologiques : ' . $total_femmes_vic . ' sur ' . $this->total_femmes_var_visites . ' reçu',
            'num' => @((round(($total_femmes_vic) * 100 / $this->total_femmes_var_visites))) . '%'
        ];
        $this->tbswrapper->TBS->MergeBlock('consequences_psychologiques_stat', $data);
    }

    private function table_36() {


        /*
         * violences enfans INDIRECTES
         */
        $result = $this->db->query('select * from sos_relation_violences_enfants_indirectes,sos_violences,sos_femme ' . ' where sos_relation_violences_enfants_indirectes.id_from_violences=sos_violences.id_violences and ' . ' sos_femme.id_femme = sos_violences.id_from_femme' . ' and year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service . ' group BY sos_relation_violences_enfants_indirectes.id_from_violences ASC')->result();
        $total_femmes_vic = count($result);
        $data = [];
        $query = $this->db->get('sos_gen_violences_enfants_indirectes');
        $row_gen_violences_enfants_indirectes = $query->result_array();
        $tot = 0;
        foreach ($row_gen_violences_enfants_indirectes as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_violences_enfants_indirectes 
                 left join sos_violences on sos_violences.id_violences=sos_relation_violences_enfants_indirectes.id_from_violences 
                 left join sos_femme on sos_violences.id_from_femme=sos_femme.id_femme 
                 where sos_relation_violences_enfants_indirectes.id_from_violences_enfants_indirectes = ' . $value['id_violences_enfants'] . ' and year(`sos_femme`.`premier_contact`)= ' . $this->year . $this->r_femme_service)->result();
            if (count($result) > 0) {
                $data[] = ['violences_enfants_indirectes_stat' => $value['name_violences_enfants_indirectes'],
                    'num' => $result[0]->num
                ];
                $tot = $tot + 1;
            } else {
                $data[] = ['violences_enfants_indirectes_stat' => $value['name_violences_enfants_indirectes'],
                    'num' => 0
                ];
            }
        }
        $data[] = ['violences_enfants_indirectes_stat' => 'Total des femmes avec concequences enfants_indirectes : ' . $total_femmes_vic,
            'num' => (round(($total_femmes_vic) * 100 / $this->total_femmes_var)) . '%'
        ];
        $this->tbswrapper->TBS->MergeBlock('violences_enfants_indirectes_stat', $data);
    }

    private function table_361() {
        /*
         * violences enfans INDIRECTES
         */

        $result = $this->db->query('select * from sos_relation_violences_enfants_indirectes,sos_violences,sos_demande where sos_relation_violences_enfants_indirectes.id_from_violences=sos_violences.id_violences and  
sos_demande.id_from_femme = sos_violences.id_from_femme and 
year(sos_demande.visite)=' . $this->year
                        . $this->r_service .
                        ' group BY sos_relation_violences_enfants_indirectes.id_from_violences ASC')->result();
        $total_femmes_vic_enfants = count($result);

        $data = [];
        $query = $this->db->get('sos_gen_violences_enfants_indirectes');
        $row_gen_violences_enfants_indirectes = $query->result_array();
        $tot = 0;
        foreach ($row_gen_violences_enfants_indirectes as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_violences_enfants_indirectes 
                 left join sos_violences on sos_violences.id_violences=sos_relation_violences_enfants_indirectes.id_from_violences 
                 left join sos_demande on sos_violences.id_from_femme=sos_demande.id_from_femme 
                 where sos_relation_violences_enfants_indirectes.id_from_violences_enfants_indirectes = ' . $value['id_violences_enfants'] . ' and year(`sos_demande`.`visite`)= ' . $this->year . $this->r_service . ' group by sos_demande.id_from_femme')->result();
            $data[] = ['violences_enfants_indirectes_stat' => $value['name_violences_enfants_indirectes'],
                'num' => count($result)
            ];
        }
        $data[] = ['violences_enfants_indirectes_stat' => 'Total des femmes déclarant avoir des enfants victimes de violences directes : ' . $total_femmes_vic_enfants . ' sur ' . $this->total_femmes_var_visites_avec_enfant . ' femmes déclarant avoir des enfants',
            'num' => @((round(($total_femmes_vic_enfants) * 100 / $this->total_femmes_var_visites_avec_enfant))) . '%'
        ];
        $this->tbswrapper->TBS->MergeBlock('violences_enfants_indirectes_stat', $data);
    }

    private function table_371() {
        /*
         * violences enfans DIRECTES
         */

        $result = $this->db->query('select * from sos_relation_violences_enfants_directes,sos_violences,sos_demande where sos_relation_violences_enfants_directes.id_from_violences=sos_violences.id_violences and  
sos_demande.id_from_femme = sos_violences.id_from_femme and 
year(sos_demande.visite)=' . $this->year
                        . $this->r_service .
                        ' group BY sos_relation_violences_enfants_directes.id_from_violences ASC')->result();
        $total_femmes_vic_enfants = count($result);

        $data = [];
        $query = $this->db->get('sos_gen_violences_enfants_directes');
        $row_gen_violences_enfants_directes = $query->result_array();
        $tot = 0;
        foreach ($row_gen_violences_enfants_directes as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_violences_enfants_directes 
                 left join sos_violences on sos_violences.id_violences=sos_relation_violences_enfants_directes.id_from_violences 
                 left join sos_demande on sos_violences.id_from_femme=sos_demande.id_from_femme 
                 where sos_relation_violences_enfants_directes.id_from_violences_enfants_directes = ' . $value['id_violences_enfants'] . ' and year(`sos_demande`.`visite`)= ' . $this->year . $this->r_service . ' group by sos_demande.id_from_femme')->result();
            $data[] = ['violences_enfants_directes_stat' => $value['name_violences_enfants_directes'],
                'num' => count($result)
            ];
        }
        $data[] = ['violences_enfants_directes_stat' => 'Total des femmes déclarant avoir des enfants victimes de violences directes : ' . $total_femmes_vic_enfants . ' sur ' . $this->total_femmes_var_visites_avec_enfant . ' femmes déclarant avoir des enfants',
            'num' => @((round(($total_femmes_vic_enfants) * 100 / $this->total_femmes_var_visites_avec_enfant))) . '%'
        ];
        $this->tbswrapper->TBS->MergeBlock('violences_enfants_directes_stat', $data);
    }

    private function table_38() {
        /*
         * FREQUENCE DES VIOLENCES
         */
        $data = array();
        $query = $this->db->get('sos_gen_frequence');
        $row_gen_frequence = $query->result_array();
        $tot = 0;
        foreach ($row_gen_frequence as $key => $value) {
            $result = $this->db->query('SELECT count(*) as `num` FROM sos_violences,
                sos_femme where 
                sos_violences.id_from_femme = sos_femme.id_femme and 
                sos_violences.frequence =' . $value['id_frequence'] . ' AND (year(`sos_femme`.`premier_contact`)=' . $this->year . ')' . $this->r_femme_service)->result();
            if (count($result) > 0) {
                $data[] = array(
                    'frequence_violences' => $value['name_frequence'],
                    'num' => $result[0]->num
                );
                $tot = $tot + $result[0]->num;
            } else {
                $data[] = array(
                    'frequence_violences' => $value['name_frequence'],
                    'num' => 0
                );
            }
        }
        $data[] = array(
            'frequence_violences' => 'Non dit : ' . ($this->total_femmes_var - $tot) . ' sur ' . $this->total_femmes_var,
            'num' => (round(($this->total_femmes_var - $tot) * 100 / $this->total_femmes_var)) . '%'
        );
        $this->tbswrapper->TBS->MergeBlock('frequence_violences', $data);
    }

    private function table_39() {
        /*
         * COMMENCEMENT DES VIOLENCES
         */
        $data = array();
        $query = $this->db->get('sos_gen_commencement');
        $row_gen_commencement = $query->result_array();
        $tot = 0;
        foreach ($row_gen_commencement as $key => $value) {
            $result = $this->db->query('SELECT count(*) as `num` FROM sos_violences,
                sos_femme where 
                sos_violences.id_from_femme = sos_femme.id_femme and 
                sos_violences.commencement =' . $value['id_commencement'] . ' AND (year(`sos_femme`.`premier_contact`)=' . $this->year . ')' . $this->r_femme_service)->result();
            if (count($result) > 0) {
                $data[] = array(
                    'commencement_violences' => $value['name_commencement'],
                    'num' => $result[0]->num
                );
                $tot = $tot + $result[0]->num;
            } else {
                $data[] = array(
                    'commencement_violences' => $value['name_commencement'],
                    'num' => 0
                );
            }
        }
        $data[] = array(
            'commencement_violences' => 'Non dit : ' . ($this->total_femmes_var - $tot) . ' sur ' . $this->total_femmes_var,
            'num' => (round(($this->total_femmes_var - $tot) * 100 / $this->total_femmes_var)) . '%'
        );
        $this->tbswrapper->TBS->MergeBlock('commencement_violences', $data);
    }

    private function table_391() {
        /*
         * COMMENCEMENT DES VIOLENCES
         */
        $data = [];
        $query = $this->db->get('sos_gen_commencement');
        $row_gen_commencement = $query->result_array();
        $tot = 0;
        foreach ($row_gen_commencement as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_violences 
                  left join sos_demande on sos_violences.id_from_femme=sos_demande.id_from_femme 
                  left join sos_femme on sos_femme.id_femme=sos_demande.id_from_femme
                 where  year(`sos_demande`.`visite`)= ' . $this->year . $this->r_service . ' and sos_violences.commencement =' . $value['id_commencement'] . ' group by  sos_femme.id_femme')->result();
            $data[] = ['commencement_violences' => $value['name_commencement'],
                'num' => count($result)
            ];
            $tot = $tot + count($result);
        }
        $data[] = ['commencement_violences' => 'Total des femmes déclarant le commencement : ' . $tot . ' sur ' . $this->total_femmes_var_visites . ' femmes visite',
            'num' => @((round(($tot) * 100 / $this->total_femmes_var_visites))) . '%'
        ];
        $this->tbswrapper->TBS->MergeBlock('commencement_violences', $data);
    }

    private function table_40() {
        /*
         * DE LA PART
         */
        $result = $this->db->query('select * from sos_relation_de_la_part,sos_violences,sos_femme ' . ' where sos_relation_de_la_part.id_from_violences=sos_violences.id_violences and ' . ' sos_femme.id_femme = sos_violences.id_from_femme' . ' and year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service . ' group BY sos_relation_de_la_part.id_from_violences ASC')->result();
        $total_femmes_vic = count($result);
        $data = array();
        $query = $this->db->get('sos_gen_de_la_part');
        $row_gen_de_la_part = $query->result_array();
        $tot = 0;
        foreach ($row_gen_de_la_part as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_de_la_part 
                 left join sos_violences on sos_violences.id_violences=sos_relation_de_la_part.id_from_violences 
                 left join sos_femme on sos_violences.id_from_femme=sos_femme.id_femme 
                 where sos_relation_de_la_part.id_from_de_la_part = ' . $value['id_de_la_part'] . ' and year(`sos_femme`.`premier_contact`)= ' . $this->year . $this->r_femme_service)->result();
            if (count($result) > 0) {
                $data[] = array(
                    'de_la_part_stat' => $value['name_de_la_part'],
                    'num' => $result[0]->num
                );
                $tot = $tot + 1;
            } else {
                $data[] = array(
                    'de_la_part_stat' => $value['name_de_la_part'],
                    'num' => 0
                );
            }
        }
        $data[] = array(
            'de_la_part_stat' => 'Non dit : ' . ($this->total_femmes_var - $total_femmes_vic) . ' sur ' . $this->total_femmes_var,
            'num' => (round(($this->total_femmes_var - $total_femmes_vic) * 100 / $this->total_femmes_var)) . '%'
        );
        $this->tbswrapper->TBS->MergeBlock('de_la_part_stat', $data);
    }

    private function table_41() {
        /*
         * TROUBLE PHYSIOLOGIQUES
         */
        $result = $this->db->query('select * from  sos_relation_troubles_physiologiques,sos_femme,sos_psy ' . ' where  sos_relation_troubles_physiologiques.id_from_psy=sos_psy.id_psy and ' . ' sos_femme.id_femme = sos_psy.id_from_femme' . ' and year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service . ' group BY sos_relation_troubles_physiologiques.id_from_psy ASC')->result();
        $total_femmes_vic = count($result);
        $data = array();
        $query = $this->db->get('sos_gen_troubles_physiologiques');
        $row_gen_troubles_physiologiques = $query->result_array();
        $tot = 0;
        foreach ($row_gen_troubles_physiologiques as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_troubles_physiologiques 
                 left join sos_psy on sos_psy.id_psy=sos_relation_troubles_physiologiques.id_from_psy 
                 left join sos_femme on sos_psy.id_from_femme=sos_femme.id_femme 
                 where sos_relation_troubles_physiologiques.id_from_troubles_physiologiques = ' . $value['id_troubles_physiologiques'] . ' and year(`sos_femme`.`premier_contact`)= ' . $this->year . $this->r_femme_service)->result();
            if (count($result) > 0) {
                $data[] = array(
                    'troubles_physiologiques' => $value['name_troubles_physiologiques'],
                    'num' => $result[0]->num
                );
                $tot = $tot + 1;
            } else {
                $data[] = array(
                    'troubles_physiologiques' => $value['name_troubles_physiologiques'],
                    'num' => 0
                );
            }
        }
        $data[] = array(
            'troubles_physiologiques' => 'Non dit : ' . ($this->total_femmes_var - $total_femmes_vic) . ' sur ' . $this->total_femmes_var,
            'num' => (round(($this->total_femmes_var - $total_femmes_vic) * 100 / $this->total_femmes_var)) . '%'
        );
        $this->tbswrapper->TBS->MergeBlock('troubles_physiologiques', $data);
    }

    private function table_42() {
        /*
         * TROUBLE COGNITIFS
         */
        $result = $this->db->query('select * from  sos_relation_troubles_cognitifs,sos_femme,sos_psy ' . ' where  sos_relation_troubles_cognitifs.id_from_psy=sos_psy.id_psy and ' . ' sos_femme.id_femme = sos_psy.id_from_femme' . ' and year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service . ' group BY sos_relation_troubles_cognitifs.id_from_psy ASC')->result();
        $total_femmes_vic = count($result);
        $data = array();
        $query = $this->db->get('sos_gen_troubles_cognitifs');
        $row_gen_troubles_cognitifs = $query->result_array();
        $tot = 0;
        foreach ($row_gen_troubles_cognitifs as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_troubles_cognitifs 
                 left join sos_psy on sos_psy.id_psy=sos_relation_troubles_cognitifs.id_from_psy 
                 left join sos_femme on sos_psy.id_from_femme=sos_femme.id_femme 
                 where sos_relation_troubles_cognitifs.id_from_troubles_cognitifs = ' . $value['id_troubles_cognitifs'] . ' and year(`sos_femme`.`premier_contact`)= ' . $this->year . $this->r_femme_service)->result();
            if (count($result) > 0) {
                $data[] = array(
                    'troubles_cognitifs' => $value['name_troubles_cognitifs'],
                    'num' => $result[0]->num
                );
                $tot = $tot + 1;
            } else {
                $data[] = array(
                    'troubles_cognitifs' => $value['name_troubles_cognitifs'],
                    'num' => 0
                );
            }
        }
        $data[] = array(
            'troubles_cognitifs' => 'Non dit : ' . ($this->total_femmes_var - $total_femmes_vic) . ' sur ' . $this->total_femmes_var,
            'num' => (round(($this->total_femmes_var - $total_femmes_vic) * 100 / $this->total_femmes_var)) . '%'
        );
        $this->tbswrapper->TBS->MergeBlock('troubles_cognitifs', $data);
    }

    private function table_43() {
        /*
         * TROUBLE EMOTIONNELS
         */
        $result = $this->db->query('select * from  sos_relation_troubles_emotionnels,sos_femme,sos_psy ' . ' where  sos_relation_troubles_emotionnels.id_from_psy=sos_psy.id_psy and ' . ' sos_femme.id_femme = sos_psy.id_from_femme' . ' and year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service . ' group BY sos_relation_troubles_emotionnels.id_from_psy ASC')->result();
        $total_femmes_vic = count($result);
        $data = array();
        $query = $this->db->get('sos_gen_troubles_emotionnels');
        $row_gen_troubles_emotionnels = $query->result_array();
        $tot = 0;
        foreach ($row_gen_troubles_emotionnels as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_troubles_emotionnels 
                 left join sos_psy on sos_psy.id_psy=sos_relation_troubles_emotionnels.id_from_psy 
                 left join sos_femme on sos_psy.id_from_femme=sos_femme.id_femme 
                 where sos_relation_troubles_emotionnels.id_from_troubles_emotionnels = ' . $value['id_troubles_emotionnels'] . ' and year(`sos_femme`.`premier_contact`)= ' . $this->year . $this->r_femme_service)->result();
            if (count($result) > 0) {
                $data[] = array(
                    'troubles_emotionnels' => $value['name_troubles_emotionnels'],
                    'num' => $result[0]->num
                );
                $tot = $tot + 1;
            } else {
                $data[] = array(
                    'troubles_emotionnels' => $value['name_troubles_emotionnels'],
                    'num' => 0
                );
            }
        }
        $data[] = array(
            'troubles_emotionnels' => 'Non dit : ' . ($this->total_femmes_var - $total_femmes_vic) . ' sur ' . $this->total_femmes_var,
            'num' => (round(($this->total_femmes_var - $total_femmes_vic) * 100 / $this->total_femmes_var)) . '%'
        );
        $this->tbswrapper->TBS->MergeBlock('troubles_emotionnels', $data);
    }

    private function table_44() {
        /*
         * DEMARCHES
         */
        $data = array();
        $query = $this->db->get('sos_gen_demarche_first');
        $row_gen_demarche_first = $query->result_array();
        $tot = 0;
        foreach ($row_gen_demarche_first as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_demarche , sos_femme where 
   sos_demarche.id_from_femme_demarche = sos_femme.id_femme and 
   year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service . ' and 
   year(sos_demarche.date_evenement)=' . $this->year . ' and sos_demarche.first=' . $value['id_demarche_first'])->result();
            if (count($result) > 0) {
                $data[] = array(
                    'demarches' => $value['name_demarche_first'],
                    'num' => $result[0]->num
                );
                $tot = $tot + $result[0]->num;
                $query_child = $this->db->get_where('sos_gen_demarche_second', array(
                    'id_from_demarche_first' => $value['id_demarche_first']
                ));
                $row_gen_demarche_second = $query_child->result_array();
                foreach ($row_gen_demarche_second as $key => $value_child) {
                    $result_child = $this->db->query('select count(*) as `num` from sos_demarche , sos_femme where 
   sos_demarche.id_from_femme_demarche = sos_femme.id_femme and 
   year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service . ' and 
   year(sos_demarche.date_evenement)=' . $this->year . ' and sos_demarche.first=' . $value['id_demarche_first'] . ' and sos_demarche.second=' . $value_child['id_demarche_second'])->result();
                    if (count($result_child) > 0) {
                        $data[] = array(
                            'demarches' => '      ' . $value_child['name_demarche_second'],
                            'num' => $result_child[0]->num
                        );
                        $query_child_child = $this->db->get_where('sos_gen_demarche_third', array(
                            'id_from_demarche_second' => $value_child['id_demarche_second']
                        ));
                        $row_gen_demarche_third = $query_child_child->result_array();
                        foreach ($row_gen_demarche_third as $key => $value_child_child) {
                            $result_child_child = $this->db->query('select count(*) as `num` from sos_demarche , sos_femme where 
   sos_demarche.id_from_femme_demarche = sos_femme.id_femme and 
   year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service . ' and 
   year(sos_demarche.date_evenement)=' . $this->year . ' and sos_demarche.first=' . $value['id_demarche_first'] . ' and sos_demarche.second=' . $value_child['id_demarche_second'] . ' and sos_demarche.third=' . $value_child_child['id_demarche_third'])->result();
                            if (count($result_child_child) > 0) {
                                $data[] = array(
                                    'demarches' => '              ' . $value_child_child['name_demarche_third'],
                                    'num' => $result_child_child[0]->num
                                );
                            } else {
                                $data[] = array(
                                    'demarches' => '              ' . $value_child_child['name_demarche_third'],
                                    'num' => 0
                                );
                            }
                        }
                    } else {
                        $data[] = array(
                            'demarches' => '      ' . $value_child['name_demarche_second'],
                            'num' => 0
                        );
                    }
                }
            } else {
                $data[] = array(
                    'demarches' => $value['name_demarche_first'],
                    'num' => 0
                );
            }
        }
        $data[] = array(
            'demarches' => 'Non dit : ' . ($this->total_femmes_var - $tot) . ' sur ' . $this->total_femmes_var,
            'num' => (round(($this->total_femmes_var - $tot) * 100 / $this->total_femmes_var)) . '%'
        );
        $this->tbswrapper->TBS->MergeBlock('demarches', $data);
    }

    private function table_45() {
        /*
         * SUITES DE PLAINTE
         */
        $result = $this->db->query('select * from sos_relation_suites_de_plainte,sos_demarche,sos_femme ' . ' where sos_relation_suites_de_plainte.id_from_demarche=sos_demarche.id_demarche and ' . ' sos_femme.id_femme = sos_demarche.id_from_femme_demarche' . ' and year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service . ' and year(sos_demarche.date_evenement)=' . $this->year . ' group BY ' . ' sos_relation_suites_de_plainte.id_from_demarche ASC')->result();
        $total_femmes_vic = count($result);
        $data = array();
        $query = $this->db->get('sos_gen_suites_de_plainte');
        $row_gen_suites_de_plainte = $query->result_array();
        $tot = 0;
        foreach ($row_gen_suites_de_plainte as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_suites_de_plainte 
                 left join sos_demarche on sos_demarche.id_demarche=sos_relation_suites_de_plainte.id_from_demarche 
                 left join sos_femme on sos_demarche.id_from_femme_demarche=sos_femme.id_femme 
                 where sos_relation_suites_de_plainte.id_from_suites_de_plainte = ' . $value['id_suites_de_plainte'] . ' and year(`sos_femme`.`premier_contact`)= ' . $this->year . $this->r_femme_service . ' and year(sos_demarche.date_evenement)=' . $this->year)->result();
            if (count($result) > 0) {
                $data[] = array(
                    'suites_de_plainte' => $value['name_suites_de_plainte'],
                    'num' => $result[0]->num
                );
                $tot = $tot + 1;
            } else {
                $data[] = array(
                    'suites_de_plainte' => $value['name_suites_de_plainte'],
                    'num' => 0
                );
            }
        }
        $data[] = array(
            'suites_de_plainte' => 'Total des femmes avec suites des plantes : ' . $total_femmes_vic . ' sur' . $this->total_femmes_var . 'on premier contact dans l\'anee',
            'num' => (round(($total_femmes_vic) * 100 / $this->total_femmes_var)) . '%'
        );
        $this->tbswrapper->TBS->MergeBlock('suites_de_plainte', $data);
    }

    private function table_46() {
        /*
         * ORDONNANCE DE PROTECTION
         */
        $result = $this->db->query('select * from sos_relation_ordonnance_de_protection,sos_demarche,sos_femme ' . ' where sos_relation_ordonnance_de_protection.id_from_demarche=sos_demarche.id_demarche and ' . ' sos_femme.id_femme = sos_demarche.id_from_femme_demarche' . ' and year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service . ' and year(sos_demarche.date_evenement)=' . $this->year . ' group BY ' . ' sos_relation_ordonnance_de_protection.id_from_demarche ASC')->result();
        $total_femmes_vic = count($result);
        $data = array();
        $query = $this->db->get('sos_gen_ordonnance_de_protection');
        $row_gen_ordonnance_de_protection = $query->result_array();
        $tot = 0;
        foreach ($row_gen_ordonnance_de_protection as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_ordonnance_de_protection 
                 left join sos_demarche on sos_demarche.id_demarche=sos_relation_ordonnance_de_protection.id_from_demarche 
                 left join sos_femme on sos_demarche.id_from_femme_demarche=sos_femme.id_femme 
                 where sos_relation_ordonnance_de_protection.id_from_ordonnance_de_protection = ' . $value['id_ordonnance_de_protection'] . ' and year(`sos_femme`.`premier_contact`)= ' . $this->year . $this->r_femme_service . ' and year(sos_demarche.date_evenement)=' . $this->year)->result();
            if (count($result) > 0) {
                $data[] = array(
                    'ordonnance_de_protection' => $value['name_ordonnance_de_protection'],
                    'num' => $result[0]->num
                );
                $tot = $tot + 1;
            } else {
                $data[] = array(
                    'ordonnance_de_protection' => $value['name_ordonnance_de_protection'],
                    'num' => 0
                );
            }
        }
        $data[] = array(
            'ordonnance_de_protection' => 'Total des femmes avec ordonnance de protection : ' . $total_femmes_vic . ' sur' . $this->total_femmes_var . 'on premier contact dans l\'anee',
            'num' => (round(($total_femmes_vic) * 100 / $this->total_femmes_var)) . '%'
        );
        $this->tbswrapper->TBS->MergeBlock('ordonnance_de_protection', $data);
    }

    private function table_47() {
        /*
         * DE LA PART enfants
         */
        $result = $this->db->query('select * from sos_relation_de_la_part_enfants,sos_violences,sos_femme where sos_relation_de_la_part_enfants.id_from_violences=sos_violences.id_violences and sos_femme.id_femme = sos_violences.id_from_femme and year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service . ' group BY sos_relation_de_la_part_enfants.id_from_violences ASC')->result();
        $total_femmes_vic = count($result);
        $data = array();
        $query = $this->db->get('sos_gen_de_la_part_enfants');
        $row_gen_de_la_part_enfants = $query->result_array();
        $tot = 0;
        foreach ($row_gen_de_la_part_enfants as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_de_la_part_enfants 
                 left join sos_violences on sos_violences.id_violences=sos_relation_de_la_part_enfants.id_from_violences 
                 left join sos_femme on sos_violences.id_from_femme=sos_femme.id_femme 
                 where sos_relation_de_la_part_enfants.id_from_de_la_part_enfants = ' . $value['id_de_la_part_enfants'] . ' and year(`sos_femme`.`premier_contact`)= ' . $this->year . $this->r_femme_service)->result();
            if (count($result) > 0) {
                $data[] = array(
                    'de_la_part_stat_enfants' => $value['name_de_la_part_enfants'],
                    'num' => $result[0]->num
                );
                $tot = $tot + 1;
            } else {
                $data[] = array(
                    'de_la_part_stat_enfants' => $value['name_de_la_part_enfants'],
                    'num' => 0
                );
            }
        }
        $data[] = array(
            'de_la_part_stat_enfants' => 'Non dit : ' . ($this->total_femmes_var_visites_avec_enfant - $total_femmes_vic) . ' sur ' . $this->total_femmes_var_visites_avec_enfant,
            'num' => (round(($this->total_femmes_var_visites_avec_enfant - $total_femmes_vic) * 100 / $this->total_femmes_var_visites_avec_enfant)) . '%'
        );
        $this->tbswrapper->TBS->MergeBlock('de_la_part_stat_enfants', $data);
    }

    private function table_48() {
        $data[] = array(
            'total' => $this->data_total[1]['total']
        );
        $this->tbswrapper->TBS->MergeBlock('passage_enfants', $data);
    }

    private function table_4900() {
        /*
         * DE LA PART ENFANTS
         */
        $result = $this->db->query('select * from sos_relation_de_la_part_enfants,sos_violences,sos_femme ' . ' where sos_relation_de_la_part_enfants.id_from_violences=sos_violences.id_violences and ' . ' sos_femme.id_femme = sos_violences.id_from_femme' . ' and year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service . ' group BY sos_relation_de_la_part_enfants.id_from_violences ASC')->result();
        $total_femmes_vic = count($result);
        $data = array();
        $query = $this->db->get('sos_gen_de_la_part_enfants');
        $row_gen_de_la_part_enfants = $query->result_array();
        $tot = 0;
        foreach ($row_gen_de_la_part_enfants as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_de_la_part_enfants 
                 left join sos_violences on sos_violences.id_violences=sos_relation_de_la_part_enfants.id_from_violences 
                 left join sos_femme on sos_violences.id_from_femme=sos_femme.id_femme 
                 where sos_relation_de_la_part_enfants.id_from_de_la_part_enfants = ' . $value['id_de_la_part_enfants'] . ' and year(`sos_femme`.`premier_contact`)= ' . $this->year . $this->r_femme_service)->result();
            if (count($result) > 0) {
                $data[] = array(
                    'de_la_part_enfants_stat' => $value['name_de_la_part_enfants'],
                    'num' => $result[0]->num
                );
                $tot = $tot + 1;
            } else {
                $data[] = array(
                    'de_la_part_enfants_stat' => $value['name_de_la_part_enfants'],
                    'num' => 0
                );
            }
        }

        $this->tbswrapper->TBS->MergeBlock('de_la_part_enfants_stat', $data);
    }

    private function table_49() {
        /*
         * Auteur
         */

        $data = array();
        $query = $this->db->get('sos_gen_de_la_part_enfants');
        $row_gen_de_la_part_enfants = $query->result_array();

        foreach ($row_gen_de_la_part_enfants as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_de_la_part_enfants 
                 left join sos_violences on sos_violences.id_violences=sos_relation_de_la_part_enfants.id_from_violences 
                 left join sos_femme on sos_violences.id_from_femme=sos_femme.id_femme 
                 where sos_relation_de_la_part_enfants.id_from_de_la_part_enfants = ' . $value['id_de_la_part_enfants'] . ' and year(`sos_femme`.`premier_contact`)= ' . $this->year . $this->r_femme_service)->result();

            $data[] = array(
                'de_la_part_enfants_stat' => $value['name_de_la_part_enfants'],
                'num' => $result[0]->num
            );
            $result = $this->db->query('select count(*) as `num_vis` from sos_relation_de_la_part_enfants 
                 left join sos_violences on sos_violences.id_violences=sos_relation_de_la_part_enfants.id_from_violences 
                 left join sos_demande on sos_violences.id_from_femme=sos_demande.id_from_femme 
                 where sos_relation_de_la_part_enfants.id_from_de_la_part_enfants = ' . $value['id_de_la_part_enfants'] . ' and year(`sos_demande`.`visite`)= ' . $this->year . $this->r_service . ' group by sos_demande.id_from_femme')->result();

            $data[count($data) - 1] = array_merge($data[count($data) - 1], [
                'num_vis' => $result[0]->num_vis
            ]);
        }

        $this->tbswrapper->TBS->MergeBlock('de_la_part_enfants_stat', $data);
    }

    private function table_491() {
        /*
         * DE LA PART ENFANTS
         */

        $result = $this->db->query('select * from sos_relation_de_la_part_enfants,sos_violences,sos_demande where sos_relation_de_la_part_enfants.id_from_violences=sos_violences.id_violences and  
sos_demande.id_from_femme = sos_violences.id_from_femme and 
year(sos_demande.visite)=' . $this->year
                        . $this->r_service .
                        ' group BY sos_relation_de_la_part_enfants.id_from_violences ASC')->result();
        $row_gen_de_la_part_enfants = count($result);

        $data = [];
        $query = $this->db->get('sos_gen_de_la_part_enfants');
        $row_gen_de_la_part_enfants = $query->result_array();
        $tot = 0;
        foreach ($row_gen_de_la_part_enfants as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_de_la_part_enfants 
                 left join sos_violences on sos_violences.id_violences=sos_relation_de_la_part_enfants.id_from_violences 
                 left join sos_demande on sos_violences.id_from_femme=sos_demande.id_from_femme 
                 where sos_relation_de_la_part_enfants.id_from_de_la_part_enfants = ' . $value['id_de_la_part_enfants'] . ' and year(`sos_demande`.`visite`)= ' . $this->year . $this->r_service . ' group by sos_demande.id_from_femme')->result();
            $data[] = ['de_la_part_enfants_stat' => $value['name_de_la_part_enfants'],
                'num' => count($result)
            ];
        }

        $this->tbswrapper->TBS->MergeBlock('de_la_part_enfants_stat', $data);
    }

    private function table_50() {
        /*
         * vtotal femmes enfants victime
         */
        $result = $this->db->query('select * from sos_relation_violences_enfants_directes, sos_relation_violences_enfants_indirectes,sos_violences,sos_demande 
            where sos_relation_violences_enfants_directes.id_from_violences=sos_violences.id_violences and  
sos_demande.id_from_femme = sos_violences.id_from_femme and 
 sos_relation_violences_enfants_indirectes.id_from_violences=sos_violences.id_violences and
year(sos_demande.visite)=' . $this->year
                        . $this->r_service .
                        ' group BY sos_demande.id_from_femme ASC')->result();
        $total_femmes_vic_enfants = count($result);
        $data = [];
        $data[] = array(
            'total' => $total_femmes_vic_enfants
        );
        $this->tbswrapper->TBS->MergeBlock('femmes_avec_enfants_vic', $data);
    }

    private function table_51() {
        /*
         * DE LA PART 
         */
        $result = $this->db->query('select * from sos_relation_de_la_part,sos_violences,sos_femme ' . ' where sos_relation_de_la_part.id_from_violences=sos_violences.id_violences and ' . ' sos_femme.id_femme = sos_violences.id_from_femme' . ' and year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service . ' group BY sos_relation_de_la_part.id_from_violences ASC')->result();
        $total_femmes_vic = count($result);
        $data = array();
        $query = $this->db->get('sos_gen_de_la_part');
        $row_gen_de_la_part = $query->result_array();
        $tot = 0;
        foreach ($row_gen_de_la_part as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_de_la_part 
                 left join sos_violences on sos_violences.id_violences=sos_relation_de_la_part.id_from_violences 
                 left join sos_femme on sos_violences.id_from_femme=sos_femme.id_femme 
                 where sos_relation_de_la_part.id_from_de_la_part = ' . $value['id_de_la_part'] . ' and year(`sos_femme`.`premier_contact`)= ' . $this->year . $this->r_femme_service)->result();
            if (count($result) > 0) {
                $data[] = array(
                    'violences_femme_de_la_part' => $value['name_de_la_part'],
                    'num' => $result[0]->num
                );
                $tot = $tot + 1;
            } else {
                $data[] = array(
                    'violences_femme_de_la_part' => $value['name_de_la_part'],
                    'num' => 0
                );
            }
        }

        $this->tbswrapper->TBS->MergeBlock('violences_femme_de_la_part', $data);
    }

    private function table_511() {
        /*
         * DE LA PART visites
         */

        $result = $this->db->query('select * from sos_relation_de_la_part,sos_violences,sos_demande where sos_relation_de_la_part.id_from_violences=sos_violences.id_violences and  
sos_demande.id_from_femme = sos_violences.id_from_femme and 
year(sos_demande.visite)=' . $this->year
                        . $this->r_service .
                        ' group BY sos_relation_de_la_part.id_from_violences ASC')->result();
        $row_gen_de_la_part = count($result);

        $data = [];
        $query = $this->db->get('sos_gen_de_la_part');
        $row_gen_de_la_part = $query->result_array();
        $tot = 0;
        foreach ($row_gen_de_la_part as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_de_la_part 
                 left join sos_violences on sos_violences.id_violences=sos_relation_de_la_part.id_from_violences 
                 left join sos_demande on sos_violences.id_from_femme=sos_demande.id_from_femme 
                 where sos_relation_de_la_part.id_from_de_la_part = ' . $value['id_de_la_part'] . ' and year(`sos_demande`.`visite`)= ' . $this->year . $this->r_service . ' group by sos_demande.id_from_femme')->result();
            $data[] = ['violences_femme_de_la_part' => $value['name_de_la_part'],
                'num' => count($result)
            ];
        }

        $this->tbswrapper->TBS->MergeBlock('violences_femme_de_la_part', $data);
    }

    private function table_52() {
        /*
         * violences RAISONS
         */
        $result = $this->db->query('select * from sos_relation_raisons,sos_violences,sos_femme ' . ' where sos_relation_raisons.id_from_violences=sos_violences.id_violences and ' . ' sos_femme.id_femme = sos_violences.id_from_femme' . ' and year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service . ' group BY sos_relation_raisons.id_from_violences ASC')->result();
        $total_femmes_vic = count($result);
        $data = array();
        $query = $this->db->get('sos_gen_raisons');
        $row_gen_raisons = $query->result_array();
        $tot = 0;
        foreach ($row_gen_raisons as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_raisons 
                 left join sos_violences on sos_violences.id_violences=sos_relation_raisons.id_from_violences 
                 left join sos_femme on sos_violences.id_from_femme=sos_femme.id_femme 
                 where sos_relation_raisons.id_from_raisons = ' . $value['id_raisons'] . ' and year(`sos_femme`.`premier_contact`)= ' . $this->year . $this->r_femme_service)->result();
            if (count($result) > 0) {
                $data[] = array(
                    'raisons_stat' => $value['name_raisons'],
                    'num' => $result[0]->num
                );
                $tot = $tot + 1;
            } else {
                $data[] = array(
                    'raisons_stat' => $value['name_raisons'],
                    'num' => 0
                );
            }
        }
        $data[] = array(
            'raisons_stat' => 'Total des femmes avec raison : ' . $total_femmes_vic,
            'num' => (round(($total_femmes_vic) * 100 / $this->total_femmes_var)) . '%'
        );
        $this->tbswrapper->TBS->MergeBlock('raisons_stat', $data);
    }

    private function table_521() {
        /*
         * violences RAISONS
         */

        $result = $this->db->query('select * from sos_relation_raisons,sos_violences,sos_demande where sos_relation_raisons.id_from_violences=sos_violences.id_violences and  
sos_demande.id_from_femme = sos_violences.id_from_femme and 
year(sos_demande.visite)=' . $this->year
                        . $this->r_service .
                        ' group BY sos_relation_raisons.id_from_violences ASC')->result();
        $total_femmes_vic_enfants = count($result);

        $data = [];
        $query = $this->db->get('sos_gen_raisons');
        $row_gen_raisons = $query->result_array();
        $tot = 0;
        foreach ($row_gen_raisons as $key => $value) {
            $result = $this->db->query('select count(*) as `num` from sos_relation_raisons 
                 left join sos_violences on sos_violences.id_violences=sos_relation_raisons.id_from_violences 
                 left join sos_demande on sos_violences.id_from_femme=sos_demande.id_from_femme 
                 where sos_relation_raisons.id_from_raisons = ' . $value['id_raisons'] . ' and year(`sos_demande`.`visite`)= ' . $this->year . $this->r_service . ' group by sos_demande.id_from_femme')->result();
            $data[] = ['raisons_stat' => $value['name_raisons'],
                'num' => count($result)
            ];
        }
        $data[] = ['raisons_stat' => 'Total des femmes avec raison : ' . $total_femmes_vic_enfants . ' sur ' . $this->total_femmes_var_visites . ' femmes venus',
            'num' => @((round(($total_femmes_vic_enfants) * 100 / $this->total_femmes_var_visites))) . '%'
        ];
        $this->tbswrapper->TBS->MergeBlock('raisons_stat', $data);
    }

    private function table_53() {
        /*
         * violences 
         */


        $result_physiques = $this->db->query('select sos_demande.id_from_femme from sos_violences,sos_demande,sos_relation_violences_physiques
    where sos_relation_violences_physiques.id_from_violences=sos_violences.id_violences and
 sos_demande.id_from_femme = sos_violences.id_from_femme and 
year(sos_demande.visite)=' . $this->year
                        . $this->r_service .
                        ' group BY sos_demande.id_from_femme ASC')->result();
        $result_psychologiques = $this->db->query('select sos_demande.id_from_femme from sos_violences,sos_demande,sos_relation_violences_psychologiques
    where sos_relation_violences_psychologiques.id_from_violences=sos_violences.id_violences and
 sos_demande.id_from_femme = sos_violences.id_from_femme and 
year(sos_demande.visite)=' . $this->year
                        . $this->r_service .
                        ' group BY sos_demande.id_from_femme ASC')->result();

        $result_economiques = $this->db->query('select sos_demande.id_from_femme from sos_violences,sos_demande,sos_relation_violences_economiques
    where sos_relation_violences_economiques.id_from_violences=sos_violences.id_violences and
 sos_demande.id_from_femme = sos_violences.id_from_femme and 
year(sos_demande.visite)=' . $this->year
                        . $this->r_service .
                        ' group BY sos_demande.id_from_femme ASC')->result();

        $result_privations = $this->db->query('select sos_demande.id_from_femme from sos_violences,sos_demande,sos_relation_violences_privations
    where sos_relation_violences_privations.id_from_violences=sos_violences.id_violences and
 sos_demande.id_from_femme = sos_violences.id_from_femme and 
year(sos_demande.visite)=' . $this->year
                        . $this->r_service .
                        ' group BY sos_demande.id_from_femme ASC')->result();
        $result_sociales = $this->db->query('select sos_demande.id_from_femme from sos_violences,sos_demande,sos_relation_violences_sociales
    where sos_relation_violences_sociales.id_from_violences=sos_violences.id_violences and
 sos_demande.id_from_femme = sos_violences.id_from_femme and 
year(sos_demande.visite)=' . $this->year
                        . $this->r_service .
                        ' group BY sos_demande.id_from_femme ASC')->result();
        $result_administratives = $this->db->query('select sos_demande.id_from_femme from sos_violences,sos_demande,sos_relation_violences_administratives
    where sos_relation_violences_administratives.id_from_violences=sos_violences.id_violences and
 sos_demande.id_from_femme = sos_violences.id_from_femme and 
year(sos_demande.visite)=' . $this->year
                        . $this->r_service .
                        ' group BY sos_demande.id_from_femme ASC')->result();
        $result_juridiques = $this->db->query('select sos_demande.id_from_femme from sos_violences,sos_demande,sos_relation_violences_juridiques
    where sos_relation_violences_juridiques.id_from_violences=sos_violences.id_violences and
 sos_demande.id_from_femme = sos_violences.id_from_femme and 
year(sos_demande.visite)=' . $this->year
                        . $this->r_service .
                        ' group BY sos_demande.id_from_femme ASC')->result();
        $result = array_merge($result_physiques, $result_psychologiques, $result_economiques, $result_privations, $result_sociales, $result_administratives, $result_juridiques);

        $final = array();

        foreach ($result as $current) {
            if (!in_array($current, $final)) {
                $final[] = $current;
            }
        }
        $total_femmes_vic = count($final);
        $data = [];
        $data[] = array(
            'total' => $total_femmes_vic
        );
        $this->tbswrapper->TBS->MergeBlock('total_femmes_violences', $data);
    }

    private function table_54() {

        $data = [];
        $query = $this->db->get('sos_gen_duree_de_la_relation');
        $row_gen = $query->result_array();
        $tot = 0;
        $tot_vis = 0;
        foreach ($row_gen as $key => $value) {
            $result = $this->db->query('SELECT count(*) as num
            FROM sos_femme
            WHERE sos_femme.duree_de_la_relation =' . $value['id_duree_de_la_relation'] . ' AND year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service)->result();
            $data[] = ['duree_de_la_relation' => $value['name_duree_de_la_relation'],
                'num' => $result[0]->num
            ];
            $tot = $tot + $result[0]->num;
            $result = $this->db->query('select count(*) as num from '
                            . '(select duree_de_la_relation from sos_femme inner join sos_demande on sos_demande.id_from_femme = sos_femme.id_femme'
                            . ' where sos_femme.duree_de_la_relation=' . $value['id_duree_de_la_relation'] . ' and year(sos_demande.visite)=' . $this->year . $this->r_service . ' group BY id_from_femme) as raq')->result();
            $data[count($data) - 1] = array_merge($data[count($data) - 1], [
                'num_vis' => $result[0]->num
            ]);
            $tot_vis = $tot_vis + $result[0]->num;
        }
        $data[] = ['duree_de_la_relation' => 'Non dit',
            'num' => ($this->total_femmes_var - $tot) . ' sur ' . $this->total_femmes_var . '(' . (round(($this->total_femmes_var - $tot) * 100 / $this->total_femmes_var)) . '%)',
            'num_vis' => ($this->total_femmes_var_visites - $tot_vis) . ' sur ' . $this->total_femmes_var_visites . ' (' . @((round(($this->total_femmes_var_visites - $tot_vis) * 100 / $this->total_femmes_var_visites))) . '%)'
        ];
        $this->tbswrapper->TBS->MergeBlock('duree_de_la_relation', $data);
    }

    private function table_55() {
        /* ressources
         * 
         */
        $data = [];
        $query = $this->db->get('sos_gen_ressources');
        $row_gen = $query->result_array();
        $tot = 0;
        $tot_vis = 0;
        foreach ($row_gen as $key => $value) {
            $result = $this->db->query('SELECT count(*) as num
            FROM sos_femme
            WHERE sos_femme.ressources =' . $value['id_ressources'] . ' AND year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service)->result();
            $data[] = ['ressources' => $value['name_ressources'],
                'num' => $result[0]->num
            ];
            $tot = $tot + $result[0]->num;
            $result = $this->db->query('select count(*) as num from '
                            . '(select ressources from sos_femme inner join sos_demande on sos_demande.id_from_femme = sos_femme.id_femme'
                            . ' where sos_femme.ressources=' . $value['id_ressources'] . ' and year(sos_demande.visite)=' . $this->year . $this->r_service . ' group BY id_from_femme) as raq')->result();
            $data[count($data) - 1] = array_merge($data[count($data) - 1], [
                'num_vis' => $result[0]->num
            ]);
            $tot_vis = $tot_vis + $result[0]->num;
            $query_child = $this->db->get_where('sos_gen_provenance', ['id_from_ressources' => $value['id_ressources']
            ]);
            $row_gen_child = $query_child->result_array();
            foreach ($row_gen_child as $key => $value_child) {
                $result_child = $this->db->query('SELECT count(*) as `num`
            FROM sos_femme
            WHERE sos_femme.provenance =' . $value_child['id_provenance'] . ' and sos_femme.ressources =' . $value['id_ressources'] . ' AND year(`sos_femme`.`premier_contact`)=' . $this->year . $this->r_femme_service)->result();
                $data[] = ['ressources' => '        ' . $value_child['name_provenance'],
                    'num' => $result_child[0]->num
                ];
                $result_child = $this->db->query('select count(*) as num from '
                                . '(select situation_actuelle_detailles from sos_femme inner join sos_demande on sos_demande.id_from_femme = sos_femme.id_femme'
                                . ' where sos_femme.provenance =' . $value_child['id_provenance'] . ' and sos_femme.ressources =' . $value['id_ressources'] . ' and year(sos_demande.visite)=' . $this->year . $this->r_service . ' group BY id_from_femme) as raq')->result();
                $data[count($data) - 1] = array_merge($data[count($data) - 1], [
                    'num_vis' => $result_child[0]->num
                ]);
            }
        }
        $data[] = ['ressources' => 'Non dit',
            'num' => ($this->total_femmes_var - $tot) . ' sur ' . $this->total_femmes_var . '(' . (round(($this->total_femmes_var - $tot) * 100 / $this->total_femmes_var)) . '%)',
            'num_vis' => ($this->total_femmes_var_visites - $tot_vis) . ' sur ' . $this->total_femmes_var_visites . ' (' . @((round(($this->total_femmes_var_visites - $tot_vis) * 100 / $this->total_femmes_var_visites))) . '%)',
        ];
        $this->tbswrapper->TBS->MergeBlock('ressources', $data);
    }

    private function table_56() {
        /* dettes
         * 
         */
        $result = $this->db->query('SELECT `sos_femme`.`dettes`,count(*) as `num`
            FROM `sos_femme`
            WHERE year(`sos_femme`.`premier_contact`)=' . $this->year . $this->r_femme_service . ' group by `sos_femme`.`dettes`')->result();
        if (count($result) > 0) {
            foreach ($result as $key => $value) {
                if ($value->dettes == "") {
                    $value->dettes = "Non dit";
                }
                $data[] = ['dettes' => $value->dettes,
                    'num' => $value->num
                ];
            }
        }
        $result = $this->db->query('select count(*) as num from 
    (select dettes from sos_femme inner join sos_demande 
    on sos_demande.id_from_femme = sos_femme.id_femme where 
    sos_femme.dettes="NON" and year(sos_demande.visite)=' . $this->year . $this->r_service .
                        ' group BY id_from_femme) as raq')->result();
        $counter = 0;
        foreach ($data as $item) {
            if ($item['dettes'] == 'NON') {
                if (count($result) > 0) {
                    $data[$counter] = array_merge($data[$counter], [
                        'num_vis' => $result[0]->num
                    ]);
                } else {
                    $data[$counter] = array_merge($data[$counter], [
                        'num_vis' => 0
                    ]);
                }
            }
            $counter = $counter + 1;
        }


        $result = $this->db->query('select count(*) as num from 
    (select dettes from sos_femme inner join sos_demande 
    on sos_demande.id_from_femme = sos_femme.id_femme where 
    sos_femme.dettes="OUI" and year(sos_demande.visite)=' . $this->year . $this->r_service .
                        ' group BY id_from_femme) as raq')->result();
        $counter = 0;
        foreach ($data as $item) {
            if ($item['dettes'] == 'OUI') {
                if (count($result) > 0) {
                    $data[$counter] = array_merge($data[$counter], [
                        'num_vis' => $result[0]->num
                    ]);
                } else {
                    $data[$counter] = array_merge($data[$counter], [
                        'num_vis' => 0
                    ]);
                }
            }
            $counter = $counter + 1;
        }
        $result = $this->db->query('select count(*) as num from 
    (select dettes from sos_femme inner join sos_demande 
    on sos_demande.id_from_femme = sos_femme.id_femme where 
    sos_femme.dettes="" and year(sos_demande.visite)=' . $this->year . $this->r_service .
                        ' group BY id_from_femme) as raq')->result();
        $counter = 0;
        foreach ($data as $item) {
            if ($item['dettes'] == 'Non dit') {
                if (count($result) > 0) {
                    $data[$counter] = array_merge($data[$counter], [
                        'num_vis' => $result[0]->num
                    ]);
                } else {
                    $data[$counter] = array_merge($data[$counter], [
                        'num_vis' => 0
                    ]);
                }
            }
            $counter = $counter + 1;
        }

        $this->tbswrapper->TBS->MergeBlock('dettes', $data);
    }

    private function table_57() {
        /* enfants recu
         * 
         */
        $data = [];
        $query = $this->db->get('sos_gen_recu');
        $row_gen = $query->result_array();
        $tot = 0;
        foreach ($row_gen as $key => $value) {
            $result = $this->db->query('SELECT count(*) as num'
                            . ' FROM sos_demande,sos_enfants where '
                            . 'year(sos_demande.visite) = ' . $this->year . $this->r_service .
                            ' and sos_enfants.id_from_demande= sos_demande.id_demande '
                            . 'and sos_enfants.recu=' . $value['id_recu'] . ' ORDER BY `id_from_femme` ASC ')->result();
            $data[] = ['enfants_recu' => $value['name_recu'],
                'num' => $result[0]->num
            ];
            $tot = $tot + $result[0]->num;
        }
        $this->tbswrapper->TBS->MergeBlock('enfants_recu', $data);
    }

    private function table_58() {
        /* enfants accompagniement
         * 
         */
        $data = [];
        $query = $this->db->get('sos_gen_accompagniement_kid');
        $row_gen = $query->result_array();
        $tot = 0;
        foreach ($row_gen as $key => $value) {
            $result = $this->db->query('SELECT count(*) as num'
                            . ' FROM sos_demande,sos_enfants,sos_relation_accompagniement_kid where '
                            . 'year(sos_demande.visite) = ' . $this->year . $this->r_service .
                            ' and sos_enfants.id_from_demande= sos_demande.id_demande '
                            . 'and sos_relation_accompagniement_kid.id_from_enfants=sos_enfants.id_enfants and sos_relation_accompagniement_kid.id_from_accompagniement_kid=' . $value['id_accompagniement_kid'] . ' ORDER BY `id_from_femme` ASC ')->result();
            $data[] = ['enfants_accompagniement' => $value['name_accompagniement_kid'],
                'num' => $result[0]->num
            ];
            $tot = $tot + $result[0]->num;
        }
        $this->tbswrapper->TBS->MergeBlock('enfants_accompagniement', $data);
    }

    private function table_59() {
        /* enfants activite
         * 
         */
        $data = [];
        $query = $this->db->get('sos_gen_activite_kid');
        $row_gen = $query->result_array();
        $tot = 0;
        foreach ($row_gen as $key => $value) {
            $result = $this->db->query('SELECT count(*) as num'
                            . ' FROM sos_demande,sos_enfants,sos_relation_activite_kid where '
                            . 'year(sos_demande.visite) = ' . $this->year . $this->r_service .
                            ' and sos_enfants.id_from_demande= sos_demande.id_demande '
                            . 'and sos_relation_activite_kid.id_from_enfants=sos_enfants.id_enfants and sos_relation_activite_kid.id_from_activite_kid=' . $value['id_activite_kid'] . ' ORDER BY `id_from_femme` ASC ')->result();
            $data[] = ['enfants_activite' => $value['name_activite_kid'],
                'num' => $result[0]->num
            ];
            $tot = $tot + $result[0]->num;
        }
        $this->tbswrapper->TBS->MergeBlock('enfants_activite', $data);
    }

    private function table_60() {
        $data = [];
        $row_gen = $this->db->query('select groups.num as fois,count(*) as femmes 
            from (select sos_femme.id_femme,count(sos_demande.id_from_femme) as num 
            from sos_femme join sos_demande on sos_demande.id_from_femme=sos_femme.id_femme 
            where year(sos_femme.premier_contact)= ' . $this->year . ' and year(sos_demande.visite)=' . $this->year . $this->r_femme_service .
                        ' group by sos_femme.id_femme ORDER BY `num` ASC) groups group by groups.num')->result_array();
        foreach ($row_gen as $key => $value) {
            $data[] = ['nombre_des_passages' => $value['fois'],
                'num' => $value['femmes'], 'num_vis' => 0
            ];
        }
        $row_gen = $this->db->query('select groups.num as fois,count(*) as femmes 
            from (select sos_femme.id_femme,count(sos_demande.id_from_femme) as num 
            from sos_femme join sos_demande on sos_demande.id_from_femme=sos_femme.id_femme 
            where year(sos_demande.visite)=' . $this->year . $this->r_service .
                        ' group by sos_femme.id_femme ORDER BY `num` ASC) groups group by groups.num')->result_array();
        foreach ($row_gen as $key => $value) {
            $fi = $this->objArraySearch($data, $value['fois'], 'nombre_des_passages');
            if (is_null($fi)) {
                $data[] = ['nombre_des_passages' => $value['fois'],
                    'num' => 0, 'num_vis' => $value['femmes']
                ];
            } else {
                $data[$fi]['num_vis'] = $value['femmes'];
            }
        }
        $this->orderBy($data, 'nombre_des_passages');

        $this->tbswrapper->TBS->MergeBlock('nombre_des_passages', $data);
    }

    private function table_61() {
        $total_enfants_1er_acc = intval($this->db->query('SELECT count(*) as num '
                        . 'FROM `sos_kids`join sos_femme on sos_femme.id_femme=sos_kids.id_femme '
                        . 'where year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service)->first_row()->num);
        $total_enfants_1er_acc_fille = intval($this->db->query('SELECT count(*) as num '
                        . 'FROM `sos_kids`join sos_femme on sos_femme.id_femme=sos_kids.id_femme '
                        . 'where sos_kids.sex="Fille" and year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service)->first_row()->num);
        $total_enfants_1er_acc_garcon = intval($this->db->query('SELECT count(*) as num '
                        . 'FROM `sos_kids`join sos_femme on sos_femme.id_femme=sos_kids.id_femme '
                        . 'where sos_kids.sex="Garçon" and year(sos_femme.premier_contact)=' . $this->year . $this->r_femme_service)->first_row()->num);

        $total_enfants_vis = intval($this->db->query('select count(*) as num '
                        . 'from sos_demande join sos_enfants as a on a.id_from_demande=sos_demande.id_demande '
                        . 'join sos_kids as b on b.id_kid=a.id_from_kids '
                        . 'where a.recu is not null and year(sos_demande.visite)=' . $this->year . $this->r_service)->first_row()->num);
        $total_enfants_vis_fillle = intval($this->db->query('select count(*) as num '
                        . 'from sos_demande join sos_enfants as a on a.id_from_demande=sos_demande.id_demande '
                        . 'join sos_kids as b on b.id_kid=a.id_from_kids '
                        . 'where a.recu is not null and b.sex="Fille" and year(sos_demande.visite)=' . $this->year . $this->r_service)->first_row()->num);
        $total_enfants_vis_garcon = intval($this->db->query('select count(*) as num '
                        . 'from sos_demande join sos_enfants as a on a.id_from_demande=sos_demande.id_demande '
                        . 'join sos_kids as b on b.id_kid=a.id_from_kids '
                        . 'where a.recu is not null and b.sex="Garçon" and year(sos_demande.visite)=' . $this->year . $this->r_service)->first_row()->num);
        $data[] = ['sex_enfants' => "Fille",
            'num' => $total_enfants_1er_acc_fille, 'num_vis' => $total_enfants_vis_fillle
        ];
        $data[] = ['sex_enfants' => "Garçon",
            'num' => $total_enfants_1er_acc_garcon, 'num_vis' => $total_enfants_vis_garcon
        ];

        $data[] = ['sex_enfants' => "Non dit",
            'num' => $total_enfants_1er_acc - ($total_enfants_1er_acc_fille + $total_enfants_1er_acc_garcon), 'num_vis' => $total_enfants_vis - ($total_enfants_vis_fillle + $total_enfants_vis_garcon)
        ];
        $this->tbswrapper->TBS->MergeBlock('sex_enfants', $data);
    }

    private function table_62() {
        $data = [];
        $row_gen = $this->db->query('select groups.num as fois, count(*) as enfants '
                        . 'from (SELECT count(*) as num ,sos_enfants.id_from_kids from sos_enfants '
                        . 'join sos_demande on sos_enfants.id_from_demande=sos_demande.id_demande '
                        . 'where year(sos_demande.visite) =' . $this->year . $this->r_service .
                        ' and sos_enfants.recu is not null group by sos_enfants.id_from_kids) groups group by groups.num ')->result_array();
        foreach ($row_gen as $key => $value) {
            $data[] = ['nombre_des_passages_enfants' => $value['fois'],
                'num' => $value['enfants']
            ];
        }
        $this->tbswrapper->TBS->MergeBlock('nombre_des_passages_enfants', $data);
    }

    private function table_63() {

        $total_enfants_vis = intval($this->db->query('select count(*) as num '
                        . 'from sos_demande join sos_enfants as a on a.id_from_demande=sos_demande.id_demande '
                        . 'join sos_kids as b on b.id_kid=a.id_from_kids '
                        . 'where a.recu is not null and year(sos_demande.visite)=' . $this->year . $this->r_service)->first_row()->num);
        $total_fraterie_enfants_femme = intval($this->db->query('select count(*) as num '
                        . 'from (select sos_demande.id_from_femme from sos_demande '
                        . 'join sos_enfants as a on a.id_from_demande=sos_demande.id_demande '
                        . 'join sos_kids as b on b.id_kid=a.id_from_kids where '
                        . 'a.recu is not null and year(sos_demande.visite)=' . $this->year . $this->r_service . ' group by sos_demande.id_from_femme) groups')->first_row()->num);
        $data[] = ['num' => $total_enfants_vis,
            'num_vis' => $total_fraterie_enfants_femme
        ];
        $this->tbswrapper->TBS->MergeBlock('nombre_frateries', $data);
    }

    private function table_64() {
        $data = [];
        $query = $this->db->get('sos_gen_kids_age');
        $row_gen = $query->result_array();
        foreach ($row_gen as $key => $value) {
            $result = $this->db->query('select count(b.age) as num from sos_demande '
                            . 'join sos_enfants as a on a.id_from_demande=sos_demande.id_demande '
                            . 'join sos_kids as b on b.id_kid=a.id_from_kids '
                            . 'where a.recu is not null and year(sos_demande.visite)=' . $this->year . $this->r_service . 'and b.age=' . $value['id_kids_age'] . ' group by b.age  ')->result();
            if (count($result) > 0) {
                $data[] = ['age_des_enfants_recu' => $value['name_kids_age'],
                    'num' => $result[0]->num
                ];
            }
        }
        $this->tbswrapper->TBS->MergeBlock('age_des_enfants_recu', $data);
    }

    private function table_65() {
        $total_rdv_psy = intval($this->db->query('select count(*) as num from '
                        . '(SELECT * FROM sos_demande join sos_relation_demande_accompagnement_specialise '
                        . 'on sos_relation_demande_accompagnement_specialise.id_from_demande=sos_demande.id_demande'
                        . ' where sos_relation_demande_accompagnement_specialise.id_from_accompagnement_specialise=8 '
                        . 'and year(sos_demande.visite)=' . $this->year . $this->r_service . ' group by sos_demande.id_from_femme) groups')->first_row()->num);
        $data[] = ['rdv_psy' => $total_rdv_psy
        ];
        $this->tbswrapper->TBS->MergeBlock('rdv_psy', $data);
    }

    private function table_66() {
        $total_femmes_enceintes_1er_acc = intval($this->db->query('SELECT count(*) as num FROM `sos_femme` '
                        . 'where sos_femme.enceinte is not null and '
                        . 'year(sos_femme.premier_contact) =' . $this->year . $this->r_femme_service)->first_row()->num);
        $total_femmes_enceintes_vic = intval($this->db->query('select count(*) as num from 
            (select count(*) from sos_demande join sos_femme on 
            sos_femme.id_femme=sos_demande.id_from_femme
            where sos_femme.enceinte is not null and 
            year(sos_demande.visite)=' . $this->year . $this->r_service . ' group by sos_demande.id_from_femme) groups')->first_row()->num);

        $data[] = ['num' => $total_femmes_enceintes_1er_acc,
            'num_vis' => $total_femmes_enceintes_vic
        ];
        $this->tbswrapper->TBS->MergeBlock('femmes_enceinte', $data);
    }

    private function table_67() {
        $data = [];
        $row_gen = $this->db->query('select count(*) as num,groups.years as years '
                        . 'from (select year(sos_femme_premier.premier_contact) as years '
                        . 'from sos_demande left join sos_femme_premier on sos_demande.id_from_femme=sos_femme_premier.id_femme'
                        . ' where year(sos_demande.visite)=' . $this->year . $this->r_service . ' group by sos_demande.id_from_femme ) groups group by groups.years ')->result_array();
        foreach ($row_gen as $key => $value) {
            $data[] = ['years' => $value['years'],
                'num' => $value['num']
            ];
        }
        $this->tbswrapper->TBS->MergeBlock('anne_1er_contact', $data);
    }

    private function table_100() {
        $data = array();
        $total_data = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
   FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' GROUP BY YEAR(`premier_contact`)')->result();
        $data[] = array(
            'line' => 'Total appels',
            'jan' => $total_data[0]->jan,
            'fev' => $total_data[0]->fev,
            'mar' => $total_data[0]->mar,
            'avr' => $total_data[0]->avr,
            'mai' => $total_data[0]->mai,
            'juin' => $total_data[0]->juin,
            'juil' => $total_data[0]->juil,
            'aout' => $total_data[0]->aout,
            'sept' => $total_data[0]->sept,
            'oct' => $total_data[0]->oct,
            'nov' => $total_data[0]->nov,
            'dec' => $total_data[0]->dec,
            'total' => $total_data[0]->jan + $total_data[0]->fev + $total_data[0]->mar + $total_data[0]->avr + $total_data[0]->mai + $total_data[0]->juin + $total_data[0]->juil + $total_data[0]->aout + $total_data[0]->sept + $total_data[0]->oct + $total_data[0]->nov + $total_data[0]->dec
        );
        $this->tbswrapper->TBS->MergeBlock('femmes_ecoute', $data);
        $total_data = $total_data[0];
        $data = array();
        $query = $this->db->get('sos_gen_interlocuteur');
        $row_gen_interlocuteur = $query->result_array();
        foreach ($row_gen_interlocuteur as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
   FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `interlocuteur`=' . $value['id_interlocuteur'] . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Interlocuteur',
                    'intelocuteur' => $value['name_interlocuteur'],
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            } else {
                $data[] = array(
                    'line' => 'Interlocuteur',
                    'intelocuteur' => $value['name_interlocuteur'],
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                );
            }
        }
        $this->tbswrapper->TBS->MergeBlock('intelocuteur', $data);
        $data = array();
        $query = $this->db->get('sos_gen_appel');
        $row_gen_appel = $query->result_array();
        foreach ($row_gen_appel as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
   FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `appel`=' . $value['id_appel'] . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Appel',
                    'appel' => $value['name_appel'],
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            } else {
                $data[] = array(
                    'line' => 'Appel',
                    'appel' => $value['name_appel'],
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                );
            }
        }
        $this->tbswrapper->TBS->MergeBlock('appel', $data);
        //$crud->set_relation('partenaire', 'sos_gen_partenaire', 'name_partenaire');
        $data = array();
        $query = $this->db->get('sos_gen_partenaire');
        $row_gen_partenaire = $query->result_array();
        foreach ($row_gen_partenaire as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
   FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `partenaire`=' . $value['id_partenaire'] . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Orienteur',
                    'partenaire' => $value['name_partenaire'],
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            } else {
                $data[] = array(
                    'line' => 'Orienteur',
                    'partenaire' => $value['name_partenaire'],
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                );
            }
        }
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
   FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `partenaire` IS NULL GROUP BY YEAR(`premier_contact`)')->result();
        if (count($result) > 0) {
            $data[] = array(
                'line' => 'Orienteur',
                'partenaire' => 'Non dit',
                'jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            );
        }
        $this->tbswrapper->TBS->MergeBlock('partenaire', $data);
        $data = array();
        $query = $this->db->get('sos_gen_temps_ecoute');
        $row_gen_temps_ecoute = $query->result_array();
        foreach ($row_gen_temps_ecoute as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
   FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `temps_ecoute`=' . $value['id_temps_ecoute'] . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Temps d\'écoute',
                    'temps_ecoute' => $value['name_temps_ecoute'],
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            } else {
                $data[] = array(
                    'line' => 'Temps d\'écoute',
                    'temps_ecoute' => $value['name_temps_ecoute'],
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                );
            }
        }
        $this->tbswrapper->TBS->MergeBlock('temps_ecoute', $data);
        $data = array();
        $query = $this->db->get('sos_gen_femme_age');
        $row_gen_femme_age = $query->result_array();
        foreach ($row_gen_femme_age as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
   FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `age`=' . $value['id_femme_age'] . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Age de la femme',
                    'femme_age' => $value['name_femme_age'],
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            } else {
                $data[] = array(
                    'line' => 'Age de la femme',
                    'femme_age' => $value['name_femme_age'],
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                );
            }
        }
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
   FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `age` IS NULL GROUP BY YEAR(`premier_contact`)')->result();
        if (count($result) > 0) {
            $data[] = array(
                'line' => 'Age de la femme',
                'femme_age' => 'Non dit',
                'jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            );
        }
        $this->tbswrapper->TBS->MergeBlock('femme_age', $data);
        $data = array();
        $appel = array(
            'Aucun',
            '1 enfant',
            '2 enfants',
            '3 enfants',
            '4 enfants',
            '5 et +'
        );
        foreach ($appel as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
   FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `enfants`=' . $key . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Enfants',
                    'enfants' => $value,
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            } else {
                $data[] = array(
                    'line' => 'Enfants',
                    'enfants' => $value,
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                );
            }
        }
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
   FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `enfants` IS NULL GROUP BY YEAR(`premier_contact`)')->result();
        if (count($result) > 0) {
            $data[] = array(
                'line' => 'Enfants',
                'enfants' => 'Non dit',
                'jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            );
        }
        $this->tbswrapper->TBS->MergeBlock('enfants', $data);
        $data = array();
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
   FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `enceinte` LIKE "OUI" GROUP BY YEAR(`premier_contact`)')->result();
        if (count($result) > 0) {
            $data[] = array(
                'line' => 'Enceinte',
                'enceinte' => 'OUI',
                'jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            );
        } else {
            $data[] = array(
                'line' => 'Enceinte',
                'enceinte' => 'OUI',
                'jan' => 0,
                'fev' => 0,
                'mar' => 0,
                'avr' => 0,
                'mai' => 0,
                'juin' => 0,
                'juil' => 0,
                'aout' => 0,
                'sept' => 0,
                'oct' => 0,
                'nov' => 0,
                'dec' => 0,
                'total' => 0
            );
        }
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
   FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `enceinte` LIKE "NON" GROUP BY YEAR(`premier_contact`)')->result();
        if (count($result) > 0) {
            $data[] = array(
                'line' => 'Enceinte',
                'enceinte' => 'NON',
                'jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            );
        } else {
            $data[] = array(
                'line' => 'Enceinte',
                'enceinte' => 'NON',
                'jan' => 0,
                'fev' => 0,
                'mar' => 0,
                'avr' => 0,
                'mai' => 0,
                'juin' => 0,
                'juil' => 0,
                'aout' => 0,
                'sept' => 0,
                'oct' => 0,
                'nov' => 0,
                'dec' => 0,
                'total' => 0
            );
        }
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
   FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `enceinte` LIKE "" GROUP BY YEAR(`premier_contact`)')->result();
        if (count($result) > 0) {
            $data[] = array(
                'line' => 'Enceinte',
                'enceinte' => 'Non dit',
                'jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            );
        }
        $this->tbswrapper->TBS->MergeBlock('enceinte', $data);
        $data = array();
        $query = $this->db->get('sos_gen_villes');
        $row_gen_ville = $query->result_array();
        foreach ($row_gen_ville as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
   FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `ville`=' . $value['id_ville'] . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Ville',
                    'ville' => $value['nom_ville'] . ', ' . $value['code_postal'],
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            } else {
                $data[] = array(
                    'line' => 'Ville',
                    'ville' => $value['nom_ville'] . ', ' . $value['code_postal'],
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                );
            }
        }
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
   FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `ville` IS NULL GROUP BY YEAR(`premier_contact`)')->result();
        if (count($result) > 0) {
            $data[] = array(
                'line' => 'Ville',
                'ville' => 'Non dit',
                'jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            );
        }
        $this->tbswrapper->TBS->MergeBlock('ville', $data);
        $data = array();
        $query = $this->db->get('sos_gen_nationalite');
        $row_gen_nationalite = $query->result_array();
        foreach ($row_gen_nationalite as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
   FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `nationalite`=' . $value['id_nationalite'] . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Nationalite',
                    'nationalite' => $value['name_nationalite'],
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            } else {
                $data[] = array(
                    'line' => 'Nationalite',
                    'nationalite' => $value['name_nationalite'],
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                );
            }
        }
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
   FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `nationalite` IS NULL GROUP BY YEAR(`premier_contact`)')->result();
        if (count($result) > 0) {
            $data[] = array(
                'line' => 'Nationalite',
                'nationalite' => 'Non dit',
                'jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            );
        }
        $this->tbswrapper->TBS->MergeBlock('nationalite', $data);
        $data = array();
        $query = $this->db->get('sos_gen_situation_familiale_parrent');
        $row_gen_situation_familiale_parrent = $query->result_array();
        foreach ($row_gen_situation_familiale_parrent as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
       FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `situation_familiale`=' . $value['id_situation_familiale_parrent'] . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Situation familiale',
                    'situation_familiale' => $value['name_situation_familiale_parrent'],
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            } else {
                $data[] = array(
                    'line' => 'Situation familiale',
                    'situation_familiale' => $value['name_situation_familiale_parrent'],
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                );
            }
        }
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
   FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `situation_familiale` IS NULL GROUP BY YEAR(`premier_contact`)')->result();
        if (count($result) > 0) {
            $data[] = array(
                'line' => 'Situation familiale',
                'situation_familiale' => 'Non dit',
                'jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            );
        }
        $this->tbswrapper->TBS->MergeBlock('situation_familiale', $data);
        $data = array();
        $query = $this->db->get('sos_gen_depuis');
        $row_gen_depuis = $query->result_array();
        foreach ($row_gen_depuis as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
       FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `depuis`=' . $value['id_depuis'] . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Depuis',
                    'depuis' => $value['name_depuis'],
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            } else {
                $data[] = array(
                    'line' => 'Depuis',
                    'depuis' => $value['name_depuis'],
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                );
            }
        }
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
   FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `depuis` IS NULL GROUP BY YEAR(`premier_contact`)')->result();
        if (count($result) > 0) {
            $data[] = array(
                'line' => 'Depuis',
                'depuis' => 'Non dit',
                'jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            );
        }
        $this->tbswrapper->TBS->MergeBlock('depuis', $data);
        $data = array();
        $query = $this->db->get('sos_gen_duree_de_la_relation');
        $row_gen_duree_de_la_relation = $query->result_array();
        foreach ($row_gen_duree_de_la_relation as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
       FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `duree_de_la_relation`=' . $value['id_duree_de_la_relation'] . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Durée de la relation',
                    'duree_de_la_relation' => $value['name_duree_de_la_relation'],
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            } else {
                $data[] = array(
                    'line' => 'Durée de la relation',
                    'duree_de_la_relation' => $value['name_duree_de_la_relation'],
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                );
            }
        }
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
   FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `duree_de_la_relation` IS NULL GROUP BY YEAR(`premier_contact`)')->result();
        if (count($result) > 0) {
            $data[] = array(
                'line' => 'Durée de la relation',
                'duree_de_la_relation' => 'Non dit',
                'jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            );
        }
        $this->tbswrapper->TBS->MergeBlock('duree_de_la_relation', $data);
        $data = array();
        $query = $this->db->get('sos_gen_emplois_parrent');
        $row_gen_emplois_parrent = $query->result_array();
        foreach ($row_gen_emplois_parrent as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
       FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `emplois`=' . $value['id_emplois'] . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Situation professionnelle',
                    'emplois' => $value['name_emplois'],
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            } else {
                $data[] = array(
                    'line' => 'Situation professionnelle',
                    'emplois' => $value['name_emplois'],
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                );
            }
        }
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
   FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `emplois` IS NULL GROUP BY YEAR(`premier_contact`)')->result();
        if (count($result) > 0) {
            $data[] = array(
                'line' => 'Situation professionnelle',
                'emplois' => 'Non dit',
                'jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            );
        }
        $this->tbswrapper->TBS->MergeBlock('emplois', $data);
        $data = array();
        $query = $this->db->get('sos_gen_logement_parent');
        $row_gen_logement_parent = $query->result_array();
        foreach ($row_gen_logement_parent as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
       FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `logement`=' . $value['id_logement_parent'] . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Logement',
                    'logement' => $value['name_logement'],
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            } else {
                $data[] = array(
                    'line' => 'Logement',
                    'logement' => $value['name_logement'],
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                );
            }
        }
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
   FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `emplois` IS NULL GROUP BY YEAR(`premier_contact`)')->result();
        if (count($result) > 0) {
            $data[] = array(
                'line' => 'Logement',
                'logement' => 'Non dit',
                'jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            );
        }
        $this->tbswrapper->TBS->MergeBlock('logement', $data);
        $data = array();
        $query = $this->db->get('sos_gen_situation_actuelle');
        $row_gen_situation_actuelle = $query->result_array();
        foreach ($row_gen_situation_actuelle as $key => $value) {
            $query1 = $this->db->get_where('sos_gen_situation_actuelle_detailles', array(
                'id_from_situation_actuelle' => $value['id_situation_actuelle']
            ));
            $row_gen_situation_actuelle_detailles = $query1->result_array();
            foreach ($row_gen_situation_actuelle_detailles as $key1 => $value1) {
                $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
       FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `situation_actuelle`=' . $value['id_situation_actuelle'] . ' AND `situation_actuelle_detailles`=' . $value1['id_situation_actuelle_detailles'] . ' GROUP BY YEAR(`premier_contact`)')->result();
                if (count($result) > 0) {
                    $data[] = array(
                        'line' => 'Situation actuelle',
                        'situation_actuelle1' => $value['name_situation_actuelle'],
                        'situation_actuelle_detailles' => $value1['name_situation_actuelle_detailles'],
                        'jan' => $result[0]->jan,
                        'fev' => $result[0]->fev,
                        'mar' => $result[0]->mar,
                        'avr' => $result[0]->avr,
                        'mai' => $result[0]->mai,
                        'juin' => $result[0]->juin,
                        'juil' => $result[0]->juil,
                        'aout' => $result[0]->aout,
                        'sept' => $result[0]->sept,
                        'oct' => $result[0]->oct,
                        'nov' => $result[0]->nov,
                        'dec' => $result[0]->dec,
                        'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                    );
                } else {
                    $data[] = array(
                        'line' => 'Situation actuelle',
                        'situation_actuelle1' => $value['name_situation_actuelle'],
                        'situation_actuelle_detailles' => $value1['name_situation_actuelle_detailles'],
                        'jan' => 0,
                        'fev' => 0,
                        'mar' => 0,
                        'avr' => 0,
                        'mai' => 0,
                        'juin' => 0,
                        'juil' => 0,
                        'aout' => 0,
                        'sept' => 0,
                        'oct' => 0,
                        'nov' => 0,
                        'dec' => 0,
                        'total' => 0
                    );
                }
            }
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
       FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `situation_actuelle`=' . $value['id_situation_actuelle'] . ' AND `situation_actuelle_detailles` IS NULL GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Situation actuelle',
                    'situation_actuelle1' => $value['name_situation_actuelle'],
                    'situation_actuelle_detailles' => 'Non dit',
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            }
        }
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
            COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
            COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
   FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `situation_actuelle` IS NULL GROUP BY YEAR(`premier_contact`)')->result();
        if (count($result) > 0) {
            $data[] = array(
                'line' => 'Situation actuelle',
                'situation_actuelle1' => 'Non dit',
                'situation_actuelle_detailles' => '',
                'jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            );
        }
        $this->tbswrapper->TBS->MergeBlock('situation_actuelle', $data);
        $data = array();
        $query = $this->db->get('sos_gen_situation_actuelle_depuis');
        $row_gen_situation_actuelle_depuis = $query->result_array();
        foreach ($row_gen_situation_actuelle_depuis as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `situation_actuelle_depuis`=' . $value['id_situation_actuelle_depuis'] . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Situation actuelle depuis',
                    'situation_actuelle_depuis' => $value['name_situation_actuelle_depuis'],
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            } else {
                $data[] = array(
                    'line' => 'Situation actuelle depuis',
                    'situation_actuelle_depuis' => $value['name_situation_actuelle_depuis'],
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                );
            }
        }
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `situation_actuelle_depuis` IS NULL GROUP BY YEAR(`premier_contact`)')->result();
        if (count($result) > 0) {
            $data[] = array(
                'line' => 'Situation actuelle depuis',
                'situation_actuelle_depuis' => 'Non dit',
                'jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            );
        }
        $this->tbswrapper->TBS->MergeBlock('situation_actuelle_depuis', $data);
        $data = array();
        $query = $this->db->get('sos_gen_ressources');
        $row_gen_ressources = $query->result_array();
        foreach ($row_gen_ressources as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `ressources`=' . $value['id_ressources'] . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Ressources',
                    'ressources' => $value['name_ressources'],
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            } else {
                $data[] = array(
                    'line' => 'Ressources',
                    'ressources' => $value['name_ressources'],
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                );
            }
        }
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `ressources` IS NULL GROUP BY YEAR(`premier_contact`)')->result();
        if (count($result) > 0) {
            $data[] = array(
                'line' => 'Ressources',
                'ressources' => 'Non dit',
                'jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            );
        }
        $this->tbswrapper->TBS->MergeBlock('ressources', $data);
        $data = array();
        $query = $this->db->get('sos_gen_allocations_familiales');
        $row_gen_allocations_familiales = $query->result_array();
        foreach ($row_gen_allocations_familiales as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `allocations_familiales`=' . $value['id_allocations_familiales'] . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Allocations familiales',
                    'allocations_familiales' => $value['name_allocations_familiales'],
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            } else {
                $data[] = array(
                    'line' => 'Allocations familiales',
                    'allocations_familiales' => $value['name_allocations_familiales'],
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                );
            }
        }
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `allocations_familiales` IS NULL GROUP BY YEAR(`premier_contact`)')->result();
        if (count($result) > 0) {
            $data[] = array(
                'line' => 'Allocations familiales',
                'allocations_familiales' => 'Non dit',
                'jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            );
        }
        $this->tbswrapper->TBS->MergeBlock('allocations_familiales', $data);
        $data = array();
        $appel = array(
            '1' => 'OUI',
            '2' => 'NON'
        );
        foreach ($appel as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `dettes`=' . $key . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Dettes',
                    'dettes' => $value,
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            } else {
                $data[] = array(
                    'line' => 'Dettes',
                    'dettes' => $value,
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                );
            }
        }
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `dettes`="" GROUP BY YEAR(`premier_contact`)')->result();
        if (count($result) > 0) {
            $data[] = array(
                'line' => 'Dettes',
                'dettes' => 'Non dit',
                'jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            );
        }
        $this->tbswrapper->TBS->MergeBlock('dettes', $data);
        $data = array();
        $appel = array(
            '1' => 'OUI',
            '0' => 'NON'
        );
        foreach ($appel as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `soutien`=' . $key . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Soutien',
                    'soutien' => $value,
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            }
        }
        $this->tbswrapper->TBS->MergeBlock('soutien', $data);
        $data = array();
        $appel = array(
            '1' => 'OUI',
            '0' => 'NON'
        );
        foreach ($appel as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `rep_soutien`=' . $key . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Réponse Soutien',
                    'rep_soutien' => $value,
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            }
        }
        $this->tbswrapper->TBS->MergeBlock('rep_soutien', $data);
        $data = array();
        $appel = array(
            '1' => 'OUI',
            '0' => 'NON'
        );
        foreach ($appel as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `ecoute`=' . $key . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Ecoute',
                    'ecoute' => $value,
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            }
        }
        $this->tbswrapper->TBS->MergeBlock('ecoute', $data);
        $data = array();
        $appel = array(
            '1' => 'OUI',
            '0' => 'NON'
        );
        foreach ($appel as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `rep_soutien`=' . $key . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Réponse Ecoute',
                    'rep_ecoute' => $value,
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            }
        }
        $this->tbswrapper->TBS->MergeBlock('rep_ecoute', $data);
        $data = array();
        $appel = array(
            '1' => 'OUI',
            '0' => 'NON'
        );
        foreach ($appel as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `accueil`=' . $key . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Accueil',
                    'accueil' => $value,
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            }
        }
        $this->tbswrapper->TBS->MergeBlock('accueil', $data);
        $data = array();
        $appel = array(
            '1' => 'OUI',
            '0' => 'NON'
        );
        foreach ($appel as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `rep_accueil`=' . $key . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Réponse Accueil',
                    'rep_accueil' => $value,
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            }
        }
        $this->tbswrapper->TBS->MergeBlock('rep_accueil', $data);
        $data = array();
        $query = $this->db->get('sos_gen_informations');
        $row_gen_informations = $query->result_array();
        foreach ($row_gen_informations as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `informations`=' . $value['id_informations'] . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Demande d\'informations',
                    'informations' => $value['name_informations'],
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            } else {
                $data[] = array(
                    'line' => 'Demande d\'informations',
                    'informations' => $value['name_informations'],
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                );
            }
        }
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `informations` IS NULL GROUP BY YEAR(`premier_contact`)')->result();
        if (count($result) > 0) {
            $data[] = array(
                'line' => 'Demande d\'informations',
                'informations' => 'NON',
                'jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            );
        }
        $this->tbswrapper->TBS->MergeBlock('informations', $data);
        $data = array();
        $appel = array(
            '1' => 'OUI',
            '0' => 'NON'
        );
        foreach ($appel as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `rep_informations`=' . $key . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Réponse demande d\'informations',
                    'rep_informations' => $value,
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            }
        }
        $this->tbswrapper->TBS->MergeBlock('rep_informations', $data);
        $data = array();
        $appel = array(
            '1' => 'OUI',
            '0' => 'NON'
        );
        foreach ($appel as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `conseil`=' . $key . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Demande de conseil',
                    'conseil' => $value,
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            }
        }
        $this->tbswrapper->TBS->MergeBlock('conseil', $data);
        $data = array();
        $appel = array(
            '1' => 'OUI',
            '0' => 'NON'
        );
        foreach ($appel as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `rep_conseil`=' . $key . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Réponse conseil',
                    'rep_conseil' => $value,
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            }
        }
        $this->tbswrapper->TBS->MergeBlock('rep_conseil', $data);
        $data = array();
        $appel = array(
            '1' => 'OUI',
            '0' => 'NON'
        );
        foreach ($appel as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `orientation`=' . $key . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Demande d\'orientation',
                    'orientation' => $value,
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            }
        }
        $this->tbswrapper->TBS->MergeBlock('orientation', $data);
        $data = array();
        $appel = array(
            '1' => 'OUI',
            '0' => 'NON'
        );
        foreach ($appel as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `rep_orientation`=' . $key . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Réponse demande d\'orientation',
                    'rep_orientation' => $value,
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            }
        }
        $this->tbswrapper->TBS->MergeBlock('rep_orientation', $data);
        $data = array();
        $query = $this->db->get('sos_gen_rdv');
        $row_gen_rdv = $query->result_array();
        foreach ($row_gen_rdv as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `rdv`=' . $value['id_rdv'] . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Demande de rdv',
                    'rdv' => $value['name_rdv'],
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            } else {
                $data[] = array(
                    'line' => 'Demande de rdv',
                    'rdv' => $value['name_rdv'],
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                );
            }
        }
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `rdv` IS NULL GROUP BY YEAR(`premier_contact`)')->result();
        if (count($result) > 0) {
            $data[] = array(
                'line' => 'Demande de rdv',
                'rdv' => 'NON',
                'jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            );
        }
        $this->tbswrapper->TBS->MergeBlock('rdv', $data);
        $data = array();
        $appel = array(
            '1' => 'OUI',
            '0' => 'NON'
        );
        foreach ($appel as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `rep_rdv`=' . $key . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Réponse demande de rdv',
                    'rep_rdv' => $value,
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            }
        }
        $this->tbswrapper->TBS->MergeBlock('rep_rdv', $data);
        $data = array();
        $query = $this->db->get('sos_gen_hebergement');
        $row_gen_hebergement = $query->result_array();
        foreach ($row_gen_hebergement as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `rdv`=' . $value['id_hebergement'] . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Demande d\'hebergement',
                    'hebergement' => $value['name_hebergement'],
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            } else {
                $data[] = array(
                    'line' => 'Demande d\'hebergement',
                    'hebergement' => $value['name_hebergement'],
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                );
            }
        }
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `hebergement` IS NULL GROUP BY YEAR(`premier_contact`)')->result();
        if (count($result) > 0) {
            $data[] = array(
                'line' => 'Demande d\'hebergement',
                'hebergement' => 'NON',
                'jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            );
        }
        $this->tbswrapper->TBS->MergeBlock('hebergement', $data);
        $data = array();
        $appel = array(
            '1' => 'OUI',
            '0' => 'NON'
        );
        foreach ($appel as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `rep_hebergement`=' . $key . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Réponse d\'hebergement',
                    'rep_hebergement' => $value,
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            }
        }
        $this->tbswrapper->TBS->MergeBlock('rep_hebergement', $data);
        $data = array();
        $appel = array(
            '1' => 'OUI',
            '0' => 'NON'
        );
        foreach ($appel as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `logement_dem`=' . $key . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Demande de logement',
                    'logement_dem' => $value,
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            }
        }
        $this->tbswrapper->TBS->MergeBlock('logement_dem', $data);
        $data = array();
        $appel = array(
            '1' => 'OUI',
            '0' => 'NON'
        );
        foreach ($appel as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `rep_logement_dem`=' . $key . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Réponse de logement',
                    'rep_logement_dem' => $value,
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            }
        }
        $this->tbswrapper->TBS->MergeBlock('rep_logement_dem', $data);
        $data = array();
        $appel = array(
            '1' => 'OUI',
            '0' => 'NON'
        );
        foreach ($appel as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `aide_materielle`=' . $key . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Demande d\'aide materielle',
                    'aide_materielle' => $value,
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            }
        }
        $this->tbswrapper->TBS->MergeBlock('aide_materielle', $data);
        $data = array();
        $appel = array(
            '1' => 'OUI',
            '0' => 'NON'
        );
        foreach ($appel as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `rep_aide_materielle`=' . $key . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Réponse demande d\'aide materielle',
                    'rep_aide_materielle' => $value,
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            }
        }
        $this->tbswrapper->TBS->MergeBlock('rep_aide_materielle', $data);
        $data = array();
        $query = $this->db->get('sos_gen_violences_physiques');
        $row_gen_violences_physiques = $query->result_array();
        foreach ($row_gen_violences_physiques as $key => $value) {
            $list_months = array();
            for ($i = 1; $i <= 12; $i++) {
                $this->db->select('sos_ecoute.premier_contact,sos_ecoute.id_femme,sos_relation_violences_physiques_ecoute.id_from_violences_physiques');
                $this->db->join('sos_relation_violences_physiques_ecoute', 'sos_relation_violences_physiques_ecoute.id_from_violences =sos_ecoute.id_femme', 'left');
                $this->db->where('YEAR(sos_ecoute.premier_contact)', $this->year);
                $this->db->where('MONTH(sos_ecoute.premier_contact)', $i);
                $this->db->where('sos_relation_violences_physiques_ecoute.id_from_violences_physiques', $value['id_violences_physiques']);
                $query = $this->db->get('sos_ecoute');
                $row = $query->result_array();
                $row_num = count($row);
                $list_months[] = $row_num;
            }
            $data[] = array(
                'line' => 'Violences physiques',
                'violences_physiques' => $value['name_violences_physiques'],
                'jan' => $list_months[0],
                'fev' => $list_months[1],
                'mar' => $list_months[2],
                'avr' => $list_months[3],
                'mai' => $list_months[4],
                'juin' => $list_months[5],
                'juil' => $list_months[6],
                'aout' => $list_months[7],
                'sept' => $list_months[8],
                'oct' => $list_months[9],
                'nov' => $list_months[10],
                'dec' => $list_months[11],
                'total' => ($list_months[0] + $list_months[1] + $list_months[2] + $list_months[3] + $list_months[4] + $list_months[5] + $list_months[6] + $list_months[7] + $list_months[8] + $list_months[9] + $list_months[10] + $list_months[11])
            );
        }
        $this->tbswrapper->TBS->MergeBlock('violences_physiques', $data);
        $data = array();
        $query = $this->db->get('sos_gen_violences_psychologiques');
        $row_gen_violences_psychologiques = $query->result_array();
        foreach ($row_gen_violences_psychologiques as $key => $value) {
            $list_months = array();
            for ($i = 1; $i <= 12; $i++) {
                $this->db->select('sos_ecoute.premier_contact,sos_ecoute.id_femme,sos_relation_violences_psychologiques_ecoute.id_from_violences_psychologiques');
                $this->db->join('sos_relation_violences_psychologiques_ecoute', 'sos_relation_violences_psychologiques_ecoute.id_from_violences =sos_ecoute.id_femme', 'left');
                $this->db->where('YEAR(sos_ecoute.premier_contact)', $this->year);
                $this->db->where('MONTH(sos_ecoute.premier_contact)', $i);
                $this->db->where('sos_relation_violences_psychologiques_ecoute.id_from_violences_psychologiques', $value['id_violences_psychologiques']);
                $query = $this->db->get('sos_ecoute');
                $row = $query->result_array();
                $row_num = count($row);
                $list_months[] = $row_num;
            }
            $data[] = array(
                'line' => 'Violences psychologiques',
                'violences_psychologiques' => $value['name_violences_psychologiques'],
                'jan' => $list_months[0],
                'fev' => $list_months[1],
                'mar' => $list_months[2],
                'avr' => $list_months[3],
                'mai' => $list_months[4],
                'juin' => $list_months[5],
                'juil' => $list_months[6],
                'aout' => $list_months[7],
                'sept' => $list_months[8],
                'oct' => $list_months[9],
                'nov' => $list_months[10],
                'dec' => $list_months[11],
                'total' => ($list_months[0] + $list_months[1] + $list_months[2] + $list_months[3] + $list_months[4] + $list_months[5] + $list_months[6] + $list_months[7] + $list_months[8] + $list_months[9] + $list_months[10] + $list_months[11])
            );
        }
        $this->tbswrapper->TBS->MergeBlock('violences_psychologiques', $data);
        $data = array();
        $query = $this->db->get('sos_gen_violences_sexuelles');
        $row_gen_violences_sexuelles = $query->result_array();
        foreach ($row_gen_violences_sexuelles as $key => $value) {
            $list_months = array();
            for ($i = 1; $i <= 12; $i++) {
                $this->db->select('sos_ecoute.premier_contact,sos_ecoute.id_femme,sos_relation_violences_sexuelles_ecoute.id_from_violences_sexuelles');
                $this->db->join('sos_relation_violences_sexuelles_ecoute', 'sos_relation_violences_sexuelles_ecoute.id_from_violences =sos_ecoute.id_femme', 'left');
                $this->db->where('YEAR(sos_ecoute.premier_contact)', $this->year);
                $this->db->where('MONTH(sos_ecoute.premier_contact)', $i);
                $this->db->where('sos_relation_violences_sexuelles_ecoute.id_from_violences_sexuelles', $value['id_violences_sexuelles']);
                $query = $this->db->get('sos_ecoute');
                $row = $query->result_array();
                $row_num = count($row);
                $list_months[] = $row_num;
            }
            $data[] = array(
                'line' => 'Violences sexuelles',
                'violences_sexuelles' => $value['name_violences_sexuelles'],
                'jan' => $list_months[0],
                'fev' => $list_months[1],
                'mar' => $list_months[2],
                'avr' => $list_months[3],
                'mai' => $list_months[4],
                'juin' => $list_months[5],
                'juil' => $list_months[6],
                'aout' => $list_months[7],
                'sept' => $list_months[8],
                'oct' => $list_months[9],
                'nov' => $list_months[10],
                'dec' => $list_months[11],
                'total' => ($list_months[0] + $list_months[1] + $list_months[2] + $list_months[3] + $list_months[4] + $list_months[5] + $list_months[6] + $list_months[7] + $list_months[8] + $list_months[9] + $list_months[10] + $list_months[11])
            );
        }
        $this->tbswrapper->TBS->MergeBlock('violences_sexuelles', $data);
        $data = array();
        $query = $this->db->get('sos_gen_violences_economiques');
        $row_gen_violences_economiques = $query->result_array();
        foreach ($row_gen_violences_economiques as $key => $value) {
            $list_months = array();
            for ($i = 1; $i <= 12; $i++) {
                $this->db->select('sos_ecoute.premier_contact,sos_ecoute.id_femme,sos_relation_violences_economiques_ecoute.id_from_violences_economiques');
                $this->db->join('sos_relation_violences_economiques_ecoute', 'sos_relation_violences_economiques_ecoute.id_from_violences =sos_ecoute.id_femme', 'left');
                $this->db->where('YEAR(sos_ecoute.premier_contact)', $this->year);
                $this->db->where('MONTH(sos_ecoute.premier_contact)', $i);
                $this->db->where('sos_relation_violences_economiques_ecoute.id_from_violences_economiques', $value['id_violences_economiques']);
                $query = $this->db->get('sos_ecoute');
                $row = $query->result_array();
                $row_num = count($row);
                $list_months[] = $row_num;
            }
            $data[] = array(
                'line' => 'Violences economiques',
                'violences_economiques' => $value['name_violences_economiques'],
                'jan' => $list_months[0],
                'fev' => $list_months[1],
                'mar' => $list_months[2],
                'avr' => $list_months[3],
                'mai' => $list_months[4],
                'juin' => $list_months[5],
                'juil' => $list_months[6],
                'aout' => $list_months[7],
                'sept' => $list_months[8],
                'oct' => $list_months[9],
                'nov' => $list_months[10],
                'dec' => $list_months[11],
                'total' => ($list_months[0] + $list_months[1] + $list_months[2] + $list_months[3] + $list_months[4] + $list_months[5] + $list_months[6] + $list_months[7] + $list_months[8] + $list_months[9] + $list_months[10] + $list_months[11])
            );
        }
        $this->tbswrapper->TBS->MergeBlock('violences_economiques', $data);
        $data = array();
        $query = $this->db->get('sos_gen_violences_administratives');
        $row_gen_violences_administratives = $query->result_array();
        foreach ($row_gen_violences_administratives as $key => $value) {
            $list_months = array();
            for ($i = 1; $i <= 12; $i++) {
                $this->db->select('sos_ecoute.premier_contact,sos_ecoute.id_femme,sos_relation_violences_administratives_ecoute.id_from_violences_administratives');
                $this->db->join('sos_relation_violences_administratives_ecoute', 'sos_relation_violences_administratives_ecoute.id_from_violences =sos_ecoute.id_femme', 'left');
                $this->db->where('YEAR(sos_ecoute.premier_contact)', $this->year);
                $this->db->where('MONTH(sos_ecoute.premier_contact)', $i);
                $this->db->where('sos_relation_violences_administratives_ecoute.id_from_violences_administratives', $value['id_violences_administratives']);
                $query = $this->db->get('sos_ecoute');
                $row = $query->result_array();
                $row_num = count($row);
                $list_months[] = $row_num;
            }
            $data[] = array(
                'line' => 'Violences administratives',
                'violences_administratives' => $value['name_violences_administratives'],
                'jan' => $list_months[0],
                'fev' => $list_months[1],
                'mar' => $list_months[2],
                'avr' => $list_months[3],
                'mai' => $list_months[4],
                'juin' => $list_months[5],
                'juil' => $list_months[6],
                'aout' => $list_months[7],
                'sept' => $list_months[8],
                'oct' => $list_months[9],
                'nov' => $list_months[10],
                'dec' => $list_months[11],
                'total' => ($list_months[0] + $list_months[1] + $list_months[2] + $list_months[3] + $list_months[4] + $list_months[5] + $list_months[6] + $list_months[7] + $list_months[8] + $list_months[9] + $list_months[10] + $list_months[11])
            );
        }
        $this->tbswrapper->TBS->MergeBlock('violences_administratives', $data);
        $data = array();
        $query = $this->db->get('sos_gen_violences_sociales');
        $row_gen_violences_sociales = $query->result_array();
        foreach ($row_gen_violences_sociales as $key => $value) {
            $list_months = array();
            for ($i = 1; $i <= 12; $i++) {
                $this->db->select('sos_ecoute.premier_contact,sos_ecoute.id_femme,sos_relation_violences_sociales_ecoute.id_from_violences_sociales');
                $this->db->join('sos_relation_violences_sociales_ecoute', 'sos_relation_violences_sociales_ecoute.id_from_violences =sos_ecoute.id_femme', 'left');
                $this->db->where('YEAR(sos_ecoute.premier_contact)', $this->year);
                $this->db->where('MONTH(sos_ecoute.premier_contact)', $i);
                $this->db->where('sos_relation_violences_sociales_ecoute.id_from_violences_sociales', $value['id_violences_sociales']);
                $query = $this->db->get('sos_ecoute');
                $row = $query->result_array();
                $row_num = count($row);
                $list_months[] = $row_num;
            }
            $data[] = array(
                'line' => 'Violences sociales',
                'violences_sociales' => $value['name_violences_sociales'],
                'jan' => $list_months[0],
                'fev' => $list_months[1],
                'mar' => $list_months[2],
                'avr' => $list_months[3],
                'mai' => $list_months[4],
                'juin' => $list_months[5],
                'juil' => $list_months[6],
                'aout' => $list_months[7],
                'sept' => $list_months[8],
                'oct' => $list_months[9],
                'nov' => $list_months[10],
                'dec' => $list_months[11],
                'total' => ($list_months[0] + $list_months[1] + $list_months[2] + $list_months[3] + $list_months[4] + $list_months[5] + $list_months[6] + $list_months[7] + $list_months[8] + $list_months[9] + $list_months[10] + $list_months[11])
            );
        }
        $this->tbswrapper->TBS->MergeBlock('violences_sociales', $data);
        $data = array();
        $query = $this->db->get('sos_gen_violences_privations');
        $row_gen_violences_privations = $query->result_array();
        foreach ($row_gen_violences_privations as $key => $value) {
            $list_months = array();
            for ($i = 1; $i <= 12; $i++) {
                $this->db->select('sos_ecoute.premier_contact,sos_ecoute.id_femme,sos_relation_violences_privations_ecoute.id_from_violences_privations');
                $this->db->join('sos_relation_violences_privations_ecoute', 'sos_relation_violences_privations_ecoute.id_from_violences =sos_ecoute.id_femme', 'left');
                $this->db->where('YEAR(sos_ecoute.premier_contact)', $this->year);
                $this->db->where('MONTH(sos_ecoute.premier_contact)', $i);
                $this->db->where('sos_relation_violences_privations_ecoute.id_from_violences_privations', $value['id_violences_privations']);
                $query = $this->db->get('sos_ecoute');
                $row = $query->result_array();
                $row_num = count($row);
                $list_months[] = $row_num;
            }
            $data[] = array(
                'line' => 'Violences privations',
                'violences_privations' => $value['name_violences_privations'],
                'jan' => $list_months[0],
                'fev' => $list_months[1],
                'mar' => $list_months[2],
                'avr' => $list_months[3],
                'mai' => $list_months[4],
                'juin' => $list_months[5],
                'juil' => $list_months[6],
                'aout' => $list_months[7],
                'sept' => $list_months[8],
                'oct' => $list_months[9],
                'nov' => $list_months[10],
                'dec' => $list_months[11],
                'total' => ($list_months[0] + $list_months[1] + $list_months[2] + $list_months[3] + $list_months[4] + $list_months[5] + $list_months[6] + $list_months[7] + $list_months[8] + $list_months[9] + $list_months[10] + $list_months[11])
            );
        }
        $this->tbswrapper->TBS->MergeBlock('violences_privations', $data);
        $data = array();
        $query = $this->db->get('sos_gen_violences_juridiques');
        $row_gen_violences_juridiques = $query->result_array();
        foreach ($row_gen_violences_juridiques as $key => $value) {
            $list_months = array();
            for ($i = 1; $i <= 12; $i++) {
                $this->db->select('sos_ecoute.premier_contact,sos_ecoute.id_femme,sos_relation_violences_juridiques_ecoute.id_from_violences_juridiques');
                $this->db->join('sos_relation_violences_juridiques_ecoute', 'sos_relation_violences_juridiques_ecoute.id_from_violences =sos_ecoute.id_femme', 'left');
                $this->db->where('YEAR(sos_ecoute.premier_contact)', $this->year);
                $this->db->where('MONTH(sos_ecoute.premier_contact)', $i);
                $this->db->where('sos_relation_violences_juridiques_ecoute.id_from_violences_juridiques', $value['id_violences_juridiques']);
                $query = $this->db->get('sos_ecoute');
                $row = $query->result_array();
                $row_num = count($row);
                $list_months[] = $row_num;
            }
            $data[] = array(
                'line' => 'Violences juridiques',
                'violences_juridiques' => $value['name_violences_juridiques'],
                'jan' => $list_months[0],
                'fev' => $list_months[1],
                'mar' => $list_months[2],
                'avr' => $list_months[3],
                'mai' => $list_months[4],
                'juin' => $list_months[5],
                'juil' => $list_months[6],
                'aout' => $list_months[7],
                'sept' => $list_months[8],
                'oct' => $list_months[9],
                'nov' => $list_months[10],
                'dec' => $list_months[11],
                'total' => ($list_months[0] + $list_months[1] + $list_months[2] + $list_months[3] + $list_months[4] + $list_months[5] + $list_months[6] + $list_months[7] + $list_months[8] + $list_months[9] + $list_months[10] + $list_months[11])
            );
        }
        $this->tbswrapper->TBS->MergeBlock('violences_juridiques', $data);
        $data = array();
        $query = $this->db->get('sos_gen_de_la_part');
        $row_gen_de_la_part = $query->result_array();
        foreach ($row_gen_de_la_part as $key => $value) {
            $list_months = array();
            for ($i = 1; $i <= 12; $i++) {
                $this->db->select('sos_ecoute.premier_contact,sos_ecoute.id_femme,sos_relation_de_la_part_ecoute.id_from_de_la_part');
                $this->db->join('sos_relation_de_la_part_ecoute', 'sos_relation_de_la_part_ecoute.id_from_violences =sos_ecoute.id_femme', 'left');
                $this->db->where('YEAR(sos_ecoute.premier_contact)', $this->year);
                $this->db->where('MONTH(sos_ecoute.premier_contact)', $i);
                $this->db->where('sos_relation_de_la_part_ecoute.id_from_de_la_part', $value['id_de_la_part']);
                $query = $this->db->get('sos_ecoute');
                $row = $query->result_array();
                $row_num = count($row);
                $list_months[] = $row_num;
            }
            $data[] = array(
                'line' => 'De la part',
                'de_la_part' => $value['name_de_la_part'],
                'jan' => $list_months[0],
                'fev' => $list_months[1],
                'mar' => $list_months[2],
                'avr' => $list_months[3],
                'mai' => $list_months[4],
                'juin' => $list_months[5],
                'juil' => $list_months[6],
                'aout' => $list_months[7],
                'sept' => $list_months[8],
                'oct' => $list_months[9],
                'nov' => $list_months[10],
                'dec' => $list_months[11],
                'total' => ($list_months[0] + $list_months[1] + $list_months[2] + $list_months[3] + $list_months[4] + $list_months[5] + $list_months[6] + $list_months[7] + $list_months[8] + $list_months[9] + $list_months[10] + $list_months[11])
            );
        }
        $this->tbswrapper->TBS->MergeBlock('de_la_part', $data);
        $data = array();
        $query = $this->db->get('sos_gen_raisons');
        $row_gen_raisons = $query->result_array();
        foreach ($row_gen_raisons as $key => $value) {
            $list_months = array();
            for ($i = 1; $i <= 12; $i++) {
                $this->db->select('sos_ecoute.premier_contact,sos_ecoute.id_femme,sos_relation_raisons_ecoute.id_from_raisons');
                $this->db->join('sos_relation_raisons_ecoute', 'sos_relation_raisons_ecoute.id_from_violences =sos_ecoute.id_femme', 'left');
                $this->db->where('YEAR(sos_ecoute.premier_contact)', $this->year);
                $this->db->where('MONTH(sos_ecoute.premier_contact)', $i);
                $this->db->where('sos_relation_raisons_ecoute.id_from_raisons', $value['id_raisons']);
                $query = $this->db->get('sos_ecoute');
                $row = $query->result_array();
                $row_num = count($row);
                $list_months[] = $row_num;
            }
            $data[] = array(
                'line' => 'Raisons',
                'raisons' => $value['name_raisons'],
                'jan' => $list_months[0],
                'fev' => $list_months[1],
                'mar' => $list_months[2],
                'avr' => $list_months[3],
                'mai' => $list_months[4],
                'juin' => $list_months[5],
                'juil' => $list_months[6],
                'aout' => $list_months[7],
                'sept' => $list_months[8],
                'oct' => $list_months[9],
                'nov' => $list_months[10],
                'dec' => $list_months[11],
                'total' => ($list_months[0] + $list_months[1] + $list_months[2] + $list_months[3] + $list_months[4] + $list_months[5] + $list_months[6] + $list_months[7] + $list_months[8] + $list_months[9] + $list_months[10] + $list_months[11])
            );
        }
        $this->tbswrapper->TBS->MergeBlock('raisons', $data);
        $data = array();
        $query = $this->db->get('sos_gen_violences_enfants_directes');
        $row_gen_violences_enfants_directes = $query->result_array();
        foreach ($row_gen_violences_enfants_directes as $key => $value) {
            $list_months = array();
            for ($i = 1; $i <= 12; $i++) {
                $this->db->select('sos_ecoute.premier_contact,sos_ecoute.id_femme,sos_relation_violences_enfants_directes_ecoute.id_from_violences_enfants_directes');
                $this->db->join('sos_relation_violences_enfants_directes_ecoute', 'sos_relation_violences_enfants_directes_ecoute.id_from_violences =sos_ecoute.id_femme', 'left');
                $this->db->where('YEAR(sos_ecoute.premier_contact)', $this->year);
                $this->db->where('MONTH(sos_ecoute.premier_contact)', $i);
                $this->db->where('sos_relation_violences_enfants_directes_ecoute.id_from_violences_enfants_directes', $value['id_violences_enfants']);
                $query = $this->db->get('sos_ecoute');
                $row = $query->result_array();
                $row_num = count($row);
                $list_months[] = $row_num;
            }
            $data[] = array(
                'line' => 'ENFANTS - Violences directes',
                'violences_enfants_directes' => $value['name_violences_enfants_directes'],
                'jan' => $list_months[0],
                'fev' => $list_months[1],
                'mar' => $list_months[2],
                'avr' => $list_months[3],
                'mai' => $list_months[4],
                'juin' => $list_months[5],
                'juil' => $list_months[6],
                'aout' => $list_months[7],
                'sept' => $list_months[8],
                'oct' => $list_months[9],
                'nov' => $list_months[10],
                'dec' => $list_months[11],
                'total' => ($list_months[0] + $list_months[1] + $list_months[2] + $list_months[3] + $list_months[4] + $list_months[5] + $list_months[6] + $list_months[7] + $list_months[8] + $list_months[9] + $list_months[10] + $list_months[11])
            );
        }
        $this->tbswrapper->TBS->MergeBlock('violences_enfants_directes', $data);
        $data = array();
        $query = $this->db->get('sos_gen_violences_enfants_indirectes');
        $row_gen_violences_enfants_indirectes = $query->result_array();
        foreach ($row_gen_violences_enfants_indirectes as $key => $value) {
            $list_months = array();
            for ($i = 1; $i <= 12; $i++) {
                $this->db->select('sos_ecoute.premier_contact,sos_ecoute.id_femme,sos_relation_violences_enfants_indirectes_ecoute.id_from_violences_enfants_indirectes');
                $this->db->join('sos_relation_violences_enfants_indirectes_ecoute', 'sos_relation_violences_enfants_indirectes_ecoute.id_from_violences =sos_ecoute.id_femme', 'left');
                $this->db->where('YEAR(sos_ecoute.premier_contact)', $this->year);
                $this->db->where('MONTH(sos_ecoute.premier_contact)', $i);
                $this->db->where('sos_relation_violences_enfants_indirectes_ecoute.id_from_violences_enfants_indirectes', $value['id_violences_enfants']);
                $query = $this->db->get('sos_ecoute');
                $row = $query->result_array();
                $row_num = count($row);
                $list_months[] = $row_num;
            }
            $data[] = array(
                'line' => 'ENFANTS - Violences indirectes',
                'violences_enfants_indirectes' => $value['name_violences_enfants_indirectes'],
                'jan' => $list_months[0],
                'fev' => $list_months[1],
                'mar' => $list_months[2],
                'avr' => $list_months[3],
                'mai' => $list_months[4],
                'juin' => $list_months[5],
                'juil' => $list_months[6],
                'aout' => $list_months[7],
                'sept' => $list_months[8],
                'oct' => $list_months[9],
                'nov' => $list_months[10],
                'dec' => $list_months[11],
                'total' => ($list_months[0] + $list_months[1] + $list_months[2] + $list_months[3] + $list_months[4] + $list_months[5] + $list_months[6] + $list_months[7] + $list_months[8] + $list_months[9] + $list_months[10] + $list_months[11])
            );
        }
        $this->tbswrapper->TBS->MergeBlock('violences_enfants_indirectes', $data);
        $data = array();
        $query = $this->db->get('sos_gen_de_la_part_enfants');
        $row_gen_de_la_part_enfants = $query->result_array();
        foreach ($row_gen_de_la_part_enfants as $key => $value) {
            $list_months = array();
            for ($i = 1; $i <= 12; $i++) {
                $this->db->select('sos_ecoute.premier_contact,sos_ecoute.id_femme,sos_relation_de_la_part_enfants_ecoute.id_from_de_la_part_enfants');
                $this->db->join('sos_relation_de_la_part_enfants_ecoute', 'sos_relation_de_la_part_enfants_ecoute.id_from_violences =sos_ecoute.id_femme', 'left');
                $this->db->where('YEAR(sos_ecoute.premier_contact)', $this->year);
                $this->db->where('MONTH(sos_ecoute.premier_contact)', $i);
                $this->db->where('sos_relation_de_la_part_enfants_ecoute.id_from_de_la_part_enfants', $value['id_de_la_part_enfants']);
                $query = $this->db->get('sos_ecoute');
                $row = $query->result_array();
                $row_num = count($row);
                $list_months[] = $row_num;
            }
            $data[] = array(
                'line' => 'Violences sur enfants par',
                'de_la_part_enfants' => $value['name_de_la_part_enfants'],
                'jan' => $list_months[0],
                'fev' => $list_months[1],
                'mar' => $list_months[2],
                'avr' => $list_months[3],
                'mai' => $list_months[4],
                'juin' => $list_months[5],
                'juil' => $list_months[6],
                'aout' => $list_months[7],
                'sept' => $list_months[8],
                'oct' => $list_months[9],
                'nov' => $list_months[10],
                'dec' => $list_months[11],
                'total' => ($list_months[0] + $list_months[1] + $list_months[2] + $list_months[3] + $list_months[4] + $list_months[5] + $list_months[6] + $list_months[7] + $list_months[8] + $list_months[9] + $list_months[10] + $list_months[11])
            );
        }
        $this->tbswrapper->TBS->MergeBlock('de_la_part_enfants', $data);
        $data = array();
        $query = $this->db->get('sos_gen_consequences_physiques');
        $row_gen_consequences_physiques = $query->result_array();
        foreach ($row_gen_consequences_physiques as $key => $value) {
            $list_months = array();
            for ($i = 1; $i <= 12; $i++) {
                $this->db->select('sos_ecoute.premier_contact,sos_ecoute.id_femme,sos_relation_consequences_physiques_ecoute.id_from_consequences_physiques');
                $this->db->join('sos_relation_consequences_physiques_ecoute', 'sos_relation_consequences_physiques_ecoute.id_from_violences =sos_ecoute.id_femme', 'left');
                $this->db->where('YEAR(sos_ecoute.premier_contact)', $this->year);
                $this->db->where('MONTH(sos_ecoute.premier_contact)', $i);
                $this->db->where('sos_relation_consequences_physiques_ecoute.id_from_consequences_physiques', $value['id_consequences_physiques']);
                $query = $this->db->get('sos_ecoute');
                $row = $query->result_array();
                $row_num = count($row);
                $list_months[] = $row_num;
            }
            $data[] = array(
                'line' => 'Conséquences physiques',
                'consequences_physiques' => $value['name_consequences_physiques'],
                'jan' => $list_months[0],
                'fev' => $list_months[1],
                'mar' => $list_months[2],
                'avr' => $list_months[3],
                'mai' => $list_months[4],
                'juin' => $list_months[5],
                'juil' => $list_months[6],
                'aout' => $list_months[7],
                'sept' => $list_months[8],
                'oct' => $list_months[9],
                'nov' => $list_months[10],
                'dec' => $list_months[11],
                'total' => ($list_months[0] + $list_months[1] + $list_months[2] + $list_months[3] + $list_months[4] + $list_months[5] + $list_months[6] + $list_months[7] + $list_months[8] + $list_months[9] + $list_months[10] + $list_months[11])
            );
        }
        $this->tbswrapper->TBS->MergeBlock('consequences_physiques', $data);
        $data = array();
        $query = $this->db->get('sos_gen_consequences_psychologiques');
        $row_gen_consequences_psychologiques = $query->result_array();
        foreach ($row_gen_consequences_psychologiques as $key => $value) {
            $list_months = array();
            for ($i = 1; $i <= 12; $i++) {
                $this->db->select('sos_ecoute.premier_contact,sos_ecoute.id_femme,sos_relation_consequences_psychologiques_ecoute.id_from_consequences_psychologiques');
                $this->db->join('sos_relation_consequences_psychologiques_ecoute', 'sos_relation_consequences_psychologiques_ecoute.id_from_violences =sos_ecoute.id_femme', 'left');
                $this->db->where('YEAR(sos_ecoute.premier_contact)', $this->year);
                $this->db->where('MONTH(sos_ecoute.premier_contact)', $i);
                $this->db->where('sos_relation_consequences_psychologiques_ecoute.id_from_consequences_psychologiques', $value['id_consequences_psychologiques']);
                $query = $this->db->get('sos_ecoute');
                $row = $query->result_array();
                $row_num = count($row);
                $list_months[] = $row_num;
            }
            $data[] = array(
                'line' => 'Conséquences psychologiques',
                'consequences_psychologiques' => $value['name_consequences_psychologiques'],
                'jan' => $list_months[0],
                'fev' => $list_months[1],
                'mar' => $list_months[2],
                'avr' => $list_months[3],
                'mai' => $list_months[4],
                'juin' => $list_months[5],
                'juil' => $list_months[6],
                'aout' => $list_months[7],
                'sept' => $list_months[8],
                'oct' => $list_months[9],
                'nov' => $list_months[10],
                'dec' => $list_months[11],
                'total' => ($list_months[0] + $list_months[1] + $list_months[2] + $list_months[3] + $list_months[4] + $list_months[5] + $list_months[6] + $list_months[7] + $list_months[8] + $list_months[9] + $list_months[10] + $list_months[11])
            );
        }
        $this->tbswrapper->TBS->MergeBlock('consequences_psychologiques', $data);
        $data = array();
        $query = $this->db->get('sos_gen_consequences_administratives');
        $row_gen_consequences_administratives = $query->result_array();
        foreach ($row_gen_consequences_administratives as $key => $value) {
            $list_months = array();
            for ($i = 1; $i <= 12; $i++) {
                $this->db->select('sos_ecoute.premier_contact,sos_ecoute.id_femme,sos_relation_consequences_administratives_ecoute.id_from_consequences_administratives');
                $this->db->join('sos_relation_consequences_administratives_ecoute', 'sos_relation_consequences_administratives_ecoute.id_from_violences =sos_ecoute.id_femme', 'left');
                $this->db->where('YEAR(sos_ecoute.premier_contact)', $this->year);
                $this->db->where('MONTH(sos_ecoute.premier_contact)', $i);
                $this->db->where('sos_relation_consequences_administratives_ecoute.id_from_consequences_administratives', $value['id_consequences_administratives']);
                $query = $this->db->get('sos_ecoute');
                $row = $query->result_array();
                $row_num = count($row);
                $list_months[] = $row_num;
            }
            $data[] = array(
                'line' => 'Conséquences administratives',
                'consequences_administratives' => $value['name_consequences_administratives'],
                'jan' => $list_months[0],
                'fev' => $list_months[1],
                'mar' => $list_months[2],
                'avr' => $list_months[3],
                'mai' => $list_months[4],
                'juin' => $list_months[5],
                'juil' => $list_months[6],
                'aout' => $list_months[7],
                'sept' => $list_months[8],
                'oct' => $list_months[9],
                'nov' => $list_months[10],
                'dec' => $list_months[11],
                'total' => ($list_months[0] + $list_months[1] + $list_months[2] + $list_months[3] + $list_months[4] + $list_months[5] + $list_months[6] + $list_months[7] + $list_months[8] + $list_months[9] + $list_months[10] + $list_months[11])
            );
        }
        $this->tbswrapper->TBS->MergeBlock('consequences_administratives', $data);
        $data = array();
        $query = $this->db->get('sos_gen_frequence');
        $row_gen_frequence = $query->result_array();
        foreach ($row_gen_frequence as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `frequence`=' . $value['id_frequence'] . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Frequence',
                    'frequence' => $value['name_frequence'],
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            } else {
                $data[] = array(
                    'line' => 'Frequence',
                    'frequence' => $value['name_frequence'],
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                );
            }
        }
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `frequence` IS NULL GROUP BY YEAR(`premier_contact`)')->result();
        if (count($result) > 0) {
            $data[] = array(
                'line' => 'Frequence',
                'frequence' => 'Non dit',
                'jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            );
        }
        $this->tbswrapper->TBS->MergeBlock('frequence', $data);
        $data = array();
        $query = $this->db->get('sos_gen_commencement');
        $row_gen_commencement = $query->result_array();
        foreach ($row_gen_commencement as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `commencement`=' . $value['id_commencement'] . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Commencement',
                    'commencement' => $value['name_commencement'],
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            } else {
                $data[] = array(
                    'line' => 'Commencement',
                    'commencement' => $value['name_commencement'],
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                );
            }
        }
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `commencement` IS NULL GROUP BY YEAR(`premier_contact`)')->result();
        if (count($result) > 0) {
            $data[] = array(
                'line' => 'Commencement',
                'commencement' => 'Non dit',
                'jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            );
        }
        $this->tbswrapper->TBS->MergeBlock('commencement', $data);
        $data = array();
        $query = $this->db->get('sos_gen_commencement');
        $row_gen_commencement = $query->result_array();
        foreach ($row_gen_commencement as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `commencement`=' . $value['id_commencement'] . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Commencement',
                    'commencement' => $value['name_commencement'],
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            } else {
                $data[] = array(
                    'line' => 'Commencement',
                    'commencement' => $value['name_commencement'],
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                );
            }
        }
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `commencement` IS NULL GROUP BY YEAR(`premier_contact`)')->result();
        if (count($result) > 0) {
            $data[] = array(
                'line' => 'Commencement',
                'commencement' => 'Non dit',
                'jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            );
        }
        $this->tbswrapper->TBS->MergeBlock('commencement', $data);
        $data = array();
        $query = $this->db->get('sos_gen_demarche_first');
        $row_gen_demarche_first = $query->result_array();
        foreach ($row_gen_demarche_first as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `first`=' . $value['id_demarche_first'] . ' OR `first1`=' . $value['id_demarche_first'] . ' OR `first2`=' . $value['id_demarche_first'] . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Type de démarche',
                    'demarche_first' => $value['name_demarche_first'],
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            } else {
                $data[] = array(
                    'line' => 'Type de démarche',
                    'demarche_first' => $value['name_demarche_first'],
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                );
            }
        }
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `first` IS NULL AND `first1` IS NULL AND `first2` IS NULL GROUP BY YEAR(`premier_contact`)')->result();
        if (count($result) > 0) {
            $data[] = array(
                'line' => 'Type de démarche',
                'demarche_first' => 'Non dit',
                'jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            );
        } else {
            $data[] = array(
                'line' => 'Type de démarche',
                'demarche_first' => 'Non dit',
                'jan' => 0,
                'fev' => 0,
                'mar' => 0,
                'avr' => 0,
                'mai' => 0,
                'juin' => 0,
                'juil' => 0,
                'aout' => 0,
                'sept' => 0,
                'oct' => 0,
                'nov' => 0,
                'dec' => 0,
                'total' => 0
            );
        }
        $this->tbswrapper->TBS->MergeBlock('demarche_first', $data);
        $data = array();
        $query = $this->db->get('sos_gen_demarche_second');
        $row_gen_demarche_second = $query->result_array();
        foreach ($row_gen_demarche_second as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `second`=' . $value['id_demarche_second'] . ' OR `second1`=' . $value['id_demarche_second'] . ' OR `second2`=' . $value['id_demarche_second'] . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Type d\'intervention',
                    'demarche_second' => $value['name_demarche_second'],
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            } else {
                $data[] = array(
                    'line' => 'Type d\'intervention',
                    'demarche_second' => $value['name_demarche_second'],
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                );
            }
        }
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `second` IS NULL AND `second1` IS NULL AND `second2` IS NULL GROUP BY YEAR(`premier_contact`)')->result();
        if (count($result) > 0) {
            $data[] = array(
                'line' => 'Type d\'intervention',
                'demarche_second' => 'Non dit',
                'jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            );
        } else {
            $data[] = array(
                'line' => 'Type d\'intervention',
                'demarche_second' => 'Non dit',
                'jan' => 0,
                'fev' => 0,
                'mar' => 0,
                'avr' => 0,
                'mai' => 0,
                'juin' => 0,
                'juil' => 0,
                'aout' => 0,
                'sept' => 0,
                'oct' => 0,
                'nov' => 0,
                'dec' => 0,
                'total' => 0
            );
        }
        $my_array = array();
        $keep_array = array();
        foreach ($data as $x => $x_value) {
            if (!in_array($x, $keep_array)) {
                $array_result = $this->filter_by_value($data, 'demarche_second', $x_value['demarche_second']);
                foreach ($array_result as $y => $y_value) {
                    $keep_array[] = $y;
                }
                $jan = 0;
                $fev = 0;
                $mar = 0;
                $avr = 0;
                $mai = 0;
                $juin = 0;
                $juil = 0;
                $aout = 0;
                $sept = 0;
                $oct = 0;
                $nov = 0;
                $dec = 0;
                $total = 0;
                foreach ($array_result as $y => $y_value) {
                    $jan = $jan + $y_value['jan'];
                    $fev = $fev + $y_value['fev'];
                    $mar = $mar + $y_value['mar'];
                    $avr = $avr + $y_value['avr'];
                    $mai = $mai + $y_value['mai'];
                    $juin = $juin + $y_value['juin'];
                    $juil = $juil + $y_value['juil'];
                    $aout = $aout + $y_value['aout'];
                    $sept = $sept + $y_value['sept'];
                    $oct = $oct + $y_value['oct'];
                    $nov = $nov + $y_value['nov'];
                    $dec = $dec + $y_value['dec'];
                    $total = $total + $y_value['total'];
                }
                $my_array[] = array(
                    'line' => 'Type d\'intervention',
                    'demarche_second' => $x_value['demarche_second'],
                    'jan' => $jan,
                    'fev' => $fev,
                    'mar' => $mar,
                    'avr' => $avr,
                    'mai' => $mai,
                    'juin' => $juin,
                    'juil' => $juil,
                    'aout' => $aout,
                    'sept' => $sept,
                    'oct' => $oct,
                    'nov' => $nov,
                    'dec' => $dec,
                    'total' => $total
                );
            }
        }
        $this->tbswrapper->TBS->MergeBlock('demarche_second', $my_array);
        $data = array();
        $query = $this->db->get('sos_gen_demarche_third');
        $row_gen_demarche_third = $query->result_array();
        foreach ($row_gen_demarche_third as $key => $value) {
            $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `third`=' . $value['id_demarche_third'] . ' OR `third1`=' . $value['id_demarche_third'] . ' OR `third2`=' . $value['id_demarche_third'] . ' GROUP BY YEAR(`premier_contact`)')->result();
            if (count($result) > 0) {
                $data[] = array(
                    'line' => 'Suites',
                    'demarche_third' => $value['name_demarche_third'],
                    'jan' => $result[0]->jan,
                    'fev' => $result[0]->fev,
                    'mar' => $result[0]->mar,
                    'avr' => $result[0]->avr,
                    'mai' => $result[0]->mai,
                    'juin' => $result[0]->juin,
                    'juil' => $result[0]->juil,
                    'aout' => $result[0]->aout,
                    'sept' => $result[0]->sept,
                    'oct' => $result[0]->oct,
                    'nov' => $result[0]->nov,
                    'dec' => $result[0]->dec,
                    'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
                );
            } else {
                $data[] = array(
                    'line' => 'Suites',
                    'demarche_third' => $value['name_demarche_third'],
                    'jan' => 0,
                    'fev' => 0,
                    'mar' => 0,
                    'avr' => 0,
                    'mai' => 0,
                    'juin' => 0,
                    'juil' => 0,
                    'aout' => 0,
                    'sept' => 0,
                    'oct' => 0,
                    'nov' => 0,
                    'dec' => 0,
                    'total' => 0
                );
            }
        }
        $result = $this->db->query('SELECT  YEAR(`premier_contact`) AS `Year`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 1 THEN 1 END) AS `jan`,
              COUNT(CASE WHEN MONTH(`premier_contact`) = 2 THEN 1 END) AS `fev`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 3 THEN 1 END) AS `mar`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 4 THEN 1 END) AS `avr`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 5  THEN 1 END) AS `mai`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 6  THEN 1 END) AS `juin`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 7  THEN 1 END) AS `juil`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 8 THEN 1 END) AS `aout`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 9 THEN 1 END) AS `sept`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 10  THEN 1 END) AS `oct`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 11  THEN 1 END) AS `nov`,
              COUNT( CASE WHEN MONTH(`premier_contact`) = 12  THEN 1 END) AS `dec`
              FROM sos_ecoute WHERE YEAR(`premier_contact`)=' . $this->year . ' AND `third` IS NULL AND `third1` IS NULL AND `third2` IS NULL GROUP BY YEAR(`premier_contact`)')->result();
        if (count($result) > 0) {
            $data[] = array(
                'line' => 'Suites',
                'demarche_third' => 'Non dit',
                'jan' => $result[0]->jan,
                'fev' => $result[0]->fev,
                'mar' => $result[0]->mar,
                'avr' => $result[0]->avr,
                'mai' => $result[0]->mai,
                'juin' => $result[0]->juin,
                'juil' => $result[0]->juil,
                'aout' => $result[0]->aout,
                'sept' => $result[0]->sept,
                'oct' => $result[0]->oct,
                'nov' => $result[0]->nov,
                'dec' => $result[0]->dec,
                'total' => $result[0]->jan + $result[0]->fev + $result[0]->mar + $result[0]->avr + $result[0]->mai + $result[0]->juin + $result[0]->juil + $result[0]->aout + $result[0]->sept + $result[0]->oct + $result[0]->nov + $result[0]->dec
            );
        } else {
            $data[] = array(
                'line' => 'Suites',
                'demarche_third' => 'Non dit',
                'jan' => 0,
                'fev' => 0,
                'mar' => 0,
                'avr' => 0,
                'mai' => 0,
                'juin' => 0,
                'juil' => 0,
                'aout' => 0,
                'sept' => 0,
                'oct' => 0,
                'nov' => 0,
                'dec' => 0,
                'total' => 0
            );
        }
        $my_array = array();
        $keep_array = array();
        foreach ($data as $x => $x_value) {
            if (!in_array($x, $keep_array)) {
                $array_result = $this->filter_by_value($data, 'demarche_third', $x_value['demarche_third']);
                foreach ($array_result as $y => $y_value) {
                    $keep_array[] = $y;
                }
                $jan = 0;
                $fev = 0;
                $mar = 0;
                $avr = 0;
                $mai = 0;
                $juin = 0;
                $juil = 0;
                $aout = 0;
                $sept = 0;
                $oct = 0;
                $nov = 0;
                $dec = 0;
                $total = 0;
                foreach ($array_result as $y => $y_value) {
                    $jan = $jan + $y_value['jan'];
                    $fev = $fev + $y_value['fev'];
                    $mar = $mar + $y_value['mar'];
                    $avr = $avr + $y_value['avr'];
                    $mai = $mai + $y_value['mai'];
                    $juin = $juin + $y_value['juin'];
                    $juil = $juil + $y_value['juil'];
                    $aout = $aout + $y_value['aout'];
                    $sept = $sept + $y_value['sept'];
                    $oct = $oct + $y_value['oct'];
                    $nov = $nov + $y_value['nov'];
                    $dec = $dec + $y_value['dec'];
                    $total = $total + $y_value['total'];
                }
                $my_array[] = array(
                    'line' => 'Suites',
                    'demarche_third' => $x_value['demarche_third'],
                    'jan' => $jan,
                    'fev' => $fev,
                    'mar' => $mar,
                    'avr' => $avr,
                    'mai' => $mai,
                    'juin' => $juin,
                    'juil' => $juil,
                    'aout' => $aout,
                    'sept' => $sept,
                    'oct' => $oct,
                    'nov' => $nov,
                    'dec' => $dec,
                    'total' => $total
                );
            }
        }
        $this->tbswrapper->TBS->MergeBlock('demarche_third', $my_array);
    }

    private function filter_by_value($array, $index, $value) {
        if (is_array($array) && count($array) > 0) {
            foreach (array_keys($array) as $key) {
                $temp[$key] = $array[$key][$index];
                if ($temp[$key] == $value) {
                    $newarray[$key] = $array[$key];
                }
            }
        }
        return $newarray;
    }

    private function array_add($a1, $a2) { // ...
        // adds the values at identical keys together
        $aRes = $a1;
        foreach (array_slice(func_get_args(), 1) as $aRay) {
            foreach (array_intersect_key($aRay, $aRes) as $key => $val)
                $aRes[$key] += $val;
            $aRes += $aRay;
        }
        return $aRes;
    }

    private function array_subtract($a1, $a2) { // ...
        // adds the values at identical keys together
        $aRes = $a1;
        foreach (array_slice(func_get_args(), 1) as $aRay) {
            foreach (array_intersect_key($aRay, $aRes) as $key => $val)
                $aRes[$key] = $aRes[$key] - $val;
            foreach (array_diff_key($aRay, $aRes) as $key => $val)
                $aRes[$key] = $aRes[$key] - $val;
        }
        return $aRes;
    }

    private function mydivide($divisior, $div) {
        if (intval($div) != 0) {
            $result = round(intval($divisior) / intval($div)); //is set to number divided by x
        } else {
            $result = 0; //is set to null
        }
        return $result;
    }

    private function findText($start_limiter, $end_limiter, $haystack) {
        $start_pos = strpos($haystack, $start_limiter);
        if ($start_pos === FALSE) {
            return FALSE;
        }
        $end_pos = strpos($haystack, $end_limiter, $start_pos);
        if ($end_pos === FALSE) {
            return FALSE;
        }
        return substr($haystack, $start_pos + 1, ($end_pos - 1) - $start_pos);
    }

    private function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if (!isset($value[$columnKey])) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }

            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            } else {
                if (!isset($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if (!is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }

        return $array;
    }

    private function orderBy(&$data, $field) {
        $code = "return strnatcmp(\$a['$field'], \$b['$field']);";
        usort($data, create_function('$a,$b', $code));
    }

    private function objArraySearch($array, $value, $thekey) {

        foreach ($array as $keys => $arrayInf) {
            if ($arrayInf[$thekey] == $value) {
                return $keys;
            }
        }
        return null;
    }

}

?>