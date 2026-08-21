<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Bilan_comptable extends Ohada_Admin_Controller
{
    protected $page_title = 'Bilan comptable';
    protected $page_subtitle = 'Presentation actif / passif consolidee sur les comptes de bilan.';

    public function index()
    {
        $this->setMenuState('admin/frontoffice/bilan_comptable');
        $date_fin = $this->inputDate('date_fin', date('Y-m-t'));
        $report = $this->ohada_model->get_bilan_report($date_fin);
        $rows = $report['rows'];
        $table_rows = array();
        foreach ($rows as $row) {
            $table_rows[] = array(
                html_escape($row['section']),
                html_escape(ucfirst($row['side'])),
                html_escape($row['compte']),
                html_escape($row['libelle']),
                html_escape($row['classe']),
                number_format($row['ouverture'], 2, ',', ' '),
                number_format($row['mouvement'], 2, ',', ' '),
                number_format($row['cloture'], 2, ',', ' '),
                number_format($row['actif'], 2, ',', ' '),
                number_format($row['passif'], 2, ',', ' '),
            );
        }

        $filters_html = form_open(current_url(), array('method' => 'get', 'class' => 'form-inline'));
        $filters_html .= '<div class="form-group" style="margin-right:10px;"><label style="margin-right:6px;">Date de fin</label><input type="date" name="date_fin" class="form-control" value="' . html_escape($date_fin) . '"></div>';
        $filters_html .= '<button type="submit" class="btn btn-primary">Generer</button>';
        $filters_html .= form_close();

        $this->renderOhadaPage(array(
            'cards' => array(
                array('label' => 'Lignes', 'value' => count($rows)),
                array('label' => 'Actif', 'value' => number_format($report['totals']['actif'], 2, ',', ' ')),
                array('label' => 'Passif', 'value' => number_format($report['totals']['passif'], 2, ',', ' ')),
                array('label' => 'Ecart', 'value' => number_format($report['totals']['actif'] - $report['totals']['passif'], 2, ',', ' ')),
                array('label' => 'Date', 'value' => date('d/m/Y', strtotime($date_fin))),
            ),
            'actions_html' => '<a href="' . site_url('admin/frontoffice/bilan_comptable/export_excel?date_fin=' . $date_fin) . '" class="btn btn-default">Export CSV</a> <a href="' . site_url('admin/frontoffice/bilan_comptable/export_pdf?date_fin=' . $date_fin) . '" class="btn btn-default">Export PDF</a>',
            'filters_html' => $filters_html,
            'table_headers' => array('Section', 'Sens', 'Compte', 'Libelle', 'Classe', 'Ouverture annuelle', 'Variation', 'Cloture', 'Actif', 'Passif'),
            'table_rows' => $table_rows,
            'empty_message' => 'Aucun compte de bilan avec mouvement.',
            'info_message' => 'Le bilan classe les soldes de cloture par grandes masses OHADA et affiche la variation sur l exercice en cours.',
        ));
    }

    public function generer()
    {
        $this->index();
    }

    public function export_excel()
    {
        $date_fin = $this->inputDate('date_fin', date('Y-m-t'));
        $rows = $this->ohada_model->get_bilan_report($date_fin)['rows'];
        $csv = array();
        foreach ($rows as $row) {
            $csv[] = array($row['section'], $row['side'], $row['compte'], $row['libelle'], $row['classe'], $row['ouverture'], $row['mouvement'], $row['cloture'], $row['actif'], $row['passif']);
        }
        $this->streamCsv('bilan_comptable_' . date('Ymd') . '.csv', array('Section', 'Sens', 'Compte', 'Libelle', 'Classe', 'Ouverture', 'Variation', 'Cloture', 'Actif', 'Passif'), $csv);
    }

    public function export_pdf()
    {
        $date_fin = $this->inputDate('date_fin', date('Y-m-t'));
        $rows = $this->ohada_model->get_bilan_report($date_fin)['rows'];
        $flat = array();
        foreach ($rows as $row) {
            $flat[] = array($row['section'], $row['side'], $row['compte'], $row['libelle'], $row['classe'], $row['ouverture'], $row['mouvement'], $row['cloture'], $row['actif'], $row['passif']);
        }
        $html = $this->buildSimplePdfHtml('Bilan comptable', array('Section', 'Sens', 'Compte', 'Libelle', 'Classe', 'Ouverture', 'Variation', 'Cloture', 'Actif', 'Passif'), $flat, 'Arrete au ' . $date_fin);
        $this->streamPdfLike('bilan_comptable_' . date('Ymd') . '.pdf', 'Bilan comptable', $html);
    }

    public function compare()
    {
        $date_fin = $this->inputDate('date_fin', date('Y-m-t'));
        $report = $this->ohada_model->get_bilan_report($date_fin);
        $actif = $report['totals']['actif'];
        $passif = $report['totals']['passif'];

        header('Content-Type: application/json');
        echo json_encode(array(
            'actif' => $actif,
            'passif' => $passif,
            'equilibre' => abs($actif - $passif) < 0.0001,
            'message' => abs($actif - $passif) < 0.0001 ? 'Bilan equilibre' : 'Verifier le bilan',
        ));
    }
}
