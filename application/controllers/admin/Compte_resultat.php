<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Compte_resultat extends Ohada_Admin_Controller
{
    protected $page_title = 'Compte de resultat';
    protected $page_subtitle = 'Charges, produits et lecture simplifiee des SIG.';

    public function index()
    {
        $this->setMenuState('admin/frontoffice/compte_resultat');
        $date_debut = $this->inputDate('date_debut', date('Y-m-01'));
        $date_fin = $this->inputDate('date_fin', date('Y-m-t'));
        $report = $this->ohada_model->get_compte_resultat_report($date_debut, $date_fin);
        $rows = $report['rows'];
        $table_rows = array();
        foreach ($rows as $row) {
            $table_rows[] = array(
                html_escape($row['section']),
                html_escape($row['compte']),
                html_escape($row['libelle']),
                number_format($row['charges'], 2, ',', ' '),
                number_format($row['produits'], 2, ',', ' '),
                number_format($row['solde'], 2, ',', ' '),
            );
        }

        $filters_html = form_open(current_url(), array('method' => 'get', 'class' => 'form-inline'));
        $filters_html .= '<div class="form-group" style="margin-right:10px;"><label style="margin-right:6px;">Du</label><input type="date" name="date_debut" class="form-control" value="' . html_escape($date_debut) . '"></div>';
        $filters_html .= '<div class="form-group" style="margin-right:10px;"><label style="margin-right:6px;">Au</label><input type="date" name="date_fin" class="form-control" value="' . html_escape($date_fin) . '"></div>';
        $filters_html .= '<button type="submit" class="btn btn-primary">Generer</button>';
        $filters_html .= form_close();

        $this->renderOhadaPage(array(
            'cards' => array(
                array('label' => 'Charges', 'value' => number_format($report['totals']['charges'], 2, ',', ' ')),
                array('label' => 'Produits', 'value' => number_format($report['totals']['produits'], 2, ',', ' ')),
                array('label' => 'Resultat net', 'value' => number_format($report['sig']['resultat_net'], 2, ',', ' ')),
                array('label' => 'Lignes', 'value' => count($rows)),
            ),
            'actions_html' => '<a href="' . site_url('admin/frontoffice/compte_resultat/export_excel?date_debut=' . $date_debut . '&date_fin=' . $date_fin) . '" class="btn btn-default">Export CSV</a> <a href="' . site_url('admin/frontoffice/compte_resultat/export_pdf?date_debut=' . $date_debut . '&date_fin=' . $date_fin) . '" class="btn btn-default">Export PDF</a>',
            'filters_html' => $filters_html,
            'table_headers' => array('Section', 'Compte', 'Libelle', 'Charges', 'Produits', 'Impact net'),
            'table_rows' => $table_rows,
            'empty_message' => 'Aucun mouvement de resultat sur la periode.',
            'info_message' => 'Le compte de resultat regroupe les comptes par grandes familles OHADA et calcule un SIG simplifie a partir des mouvements nets.',
        ));
    }

    public function generer()
    {
        $this->index();
    }

    public function export_excel()
    {
        $date_debut = $this->inputDate('date_debut', date('Y-m-01'));
        $date_fin = $this->inputDate('date_fin', date('Y-m-t'));
        $rows = $this->ohada_model->get_compte_resultat_report($date_debut, $date_fin)['rows'];
        $csv = array();
        foreach ($rows as $row) {
            $csv[] = array($row['section'], $row['compte'], $row['libelle'], $row['charges'], $row['produits'], $row['solde']);
        }
        $this->streamCsv('compte_resultat_' . date('Ymd') . '.csv', array('Section', 'Compte', 'Libelle', 'Charges', 'Produits', 'Impact net'), $csv);
    }

    public function export_pdf()
    {
        $date_debut = $this->inputDate('date_debut', date('Y-m-01'));
        $date_fin = $this->inputDate('date_fin', date('Y-m-t'));
        $rows = $this->ohada_model->get_compte_resultat_report($date_debut, $date_fin)['rows'];
        $flat = array();
        foreach ($rows as $row) {
            $flat[] = array($row['section'], $row['compte'], $row['libelle'], $row['charges'], $row['produits'], $row['solde']);
        }
        $html = $this->buildSimplePdfHtml('Compte de resultat', array('Section', 'Compte', 'Libelle', 'Charges', 'Produits', 'Impact net'), $flat, 'Periode du ' . $date_debut . ' au ' . $date_fin);
        $this->streamPdfLike('compte_resultat_' . date('Ymd') . '.pdf', 'Compte de resultat', $html);
    }

    public function sig()
    {
        $date_debut = $this->inputDate('date_debut', date('Y-m-01'));
        $date_fin = $this->inputDate('date_fin', date('Y-m-t'));
        $sig = $this->ohada_model->get_compte_resultat_report($date_debut, $date_fin)['sig'];
        header('Content-Type: application/json');
        echo json_encode($sig);
    }
}
