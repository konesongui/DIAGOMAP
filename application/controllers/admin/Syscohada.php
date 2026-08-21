<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Syscohada extends Ohada_Admin_Controller
{
    protected $page_title = 'Plan SYSCOHADA';
    protected $page_subtitle = 'Referentiel OHADA des classes et comptes de base.';

    public function index()
    {
        $this->setMenuState('admin/frontoffice/syscohada');
        $accounts = $this->ohada_model->get_accounts();
        $classes = $this->ohada_model->get_default_classes();
        $counts = array();
        foreach ($accounts as $account) {
            $class_code = isset($account['classe']) ? $account['classe'] : '';
            if (!isset($counts[$class_code])) {
                $counts[$class_code] = 0;
            }
            $counts[$class_code]++;
        }
        $rows = array();
        foreach ($classes as $code => $label) {
            $rows[] = array(
                html_escape($code),
                html_escape($label),
                isset($counts[$code]) ? (int) $counts[$code] : 0,
                '<a class="btn btn-xs btn-default" href="' . site_url('admin/frontoffice/syscohada/compte/' . $code) . '">Voir</a>',
            );
        }

        $this->renderOhadaPage(array(
            'cards' => array(array('label' => 'Comptes', 'value' => count($accounts)), array('label' => 'Classes', 'value' => count($classes))),
            'actions_html' => '<a href="' . site_url('admin/frontoffice/syscohada/import') . '" class="btn btn-primary"><i class="fa fa-download"></i> Charger le referentiel</a> <a href="' . site_url('admin/frontoffice/syscohada/export') . '" class="btn btn-default">Export CSV</a>',
            'table_headers' => array('Classe', 'Libelle', 'Comptes', 'Action'),
            'table_rows' => $rows,
            'empty_message' => 'Aucune classe SYSCOHADA disponible.',
        ));
    }

    public function classes()
    {
        $this->index();
    }

    public function comptes()
    {
        redirect('admin/chart_of_accounts');
    }

    public function compte($classe)
    {
        $_GET['classe'] = $classe;
        redirect('admin/chart_of_accounts?classe=' . rawurlencode($classe));
    }

    public function import()
    {
        $this->ohada_model->seed_syscohada_accounts();
        $this->redirectBackToModule('admin/frontoffice/syscohada', 'Referentiel SYSCOHADA charge avec succes.');
    }

    public function export()
    {
        $accounts = $this->ohada_model->get_accounts();
        $rows = array();
        foreach ($accounts as $account) {
            $rows[] = array($account['numero_compte'], $account['libelle_compte'], $account['classe'], $account['type_compte'], $account['nature']);
        }
        $this->streamCsv('syscohada_' . date('Ymd') . '.csv', array('Numero', 'Libelle', 'Classe', 'Type', 'Nature'), $rows);
    }
}
