<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Tafire extends Ohada_Admin_Controller
{
    protected $page_title = 'TAFIRE';
    protected $page_subtitle = 'Tableau financier simplifie des emplois et ressources.';

    public function index()
    {
        $this->setMenuState('admin/frontoffice/tafire');
        $date_debut = $this->inputDate('date_debut', date('Y-m-01'));
        $date_fin = $this->inputDate('date_fin', date('Y-m-t'));
        $report = $this->ohada_model->get_tafire_report($date_debut, $date_fin);
        $rows = $report['rows'];
        $table_rows = array();
        foreach ($rows as $row) {
            $table_rows[] = array(
                html_escape($row['nature']),
                number_format($row['emploi'], 2, ',', ' '),
                number_format($row['ressource'], 2, ',', ' '),
                number_format($row['ressource'] - $row['emploi'], 2, ',', ' '),
            );
        }

        $filters_html = form_open(current_url(), array('method' => 'get', 'class' => 'form-inline'));
        $filters_html .= '<div class="form-group" style="margin-right:10px;"><label style="margin-right:6px;">Du</label><input type="date" name="date_debut" class="form-control" value="' . html_escape($date_debut) . '"></div>';
        $filters_html .= '<div class="form-group" style="margin-right:10px;"><label style="margin-right:6px;">Au</label><input type="date" name="date_fin" class="form-control" value="' . html_escape($date_fin) . '"></div>';
        $filters_html .= '<button type="submit" class="btn btn-primary">Generer</button>';
        $filters_html .= form_close();

        $this->renderOhadaPage(array(
            'cards' => array(
                array('label' => 'Emplois', 'value' => number_format($report['totals']['emplois'], 2, ',', ' ')),
                array('label' => 'Ressources', 'value' => number_format($report['totals']['ressources'], 2, ',', ' ')),
                array('label' => 'Variation', 'value' => number_format($report['totals']['ressources'] - $report['totals']['emplois'], 2, ',', ' ')),
                array('label' => 'Lignes', 'value' => count($rows)),
            ),
            'actions_html' => '<a href="' . site_url('admin/frontoffice/tafire/export_excel?date_debut=' . $date_debut . '&date_fin=' . $date_fin) . '" class="btn btn-default">Export CSV</a> <a href="' . site_url('admin/frontoffice/tafire/export_pdf?date_debut=' . $date_debut . '&date_fin=' . $date_fin) . '" class="btn btn-default">Export PDF</a>',
            'filters_html' => $filters_html,
            'table_headers' => array('Nature', 'Emploi', 'Ressource', 'Variation nette'),
            'table_rows' => $table_rows,
            'empty_message' => 'Aucune donnee TAFIRE sur la periode.',
            'info_message' => 'Le TAFIRE rapproche les variations de ressources durables, emplois, creances, dettes et tresorerie sur la periode choisie.',
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
        $rows = $this->ohada_model->get_tafire_report($date_debut, $date_fin)['rows'];
        $csv = array();
        foreach ($rows as $row) {
            $csv[] = array($row['nature'], $row['emploi'], $row['ressource'], $row['ressource'] - $row['emploi']);
        }
        $this->streamCsv('tafire_' . date('Ymd') . '.csv', array('Nature', 'Emploi', 'Ressource', 'Variation nette'), $csv);
    }

    public function export_pdf()
    {
        $date_debut = $this->inputDate('date_debut', date('Y-m-01'));
        $date_fin = $this->inputDate('date_fin', date('Y-m-t'));
        $rows = $this->ohada_model->get_tafire_report($date_debut, $date_fin)['rows'];
        $flat = array();
        foreach ($rows as $row) {
            $flat[] = array($row['nature'], $row['emploi'], $row['ressource'], $row['ressource'] - $row['emploi']);
        }
        $html = $this->buildSimplePdfHtml('TAFIRE', array('Nature', 'Emploi', 'Ressource', 'Variation nette'), $flat, 'Periode du ' . $date_debut . ' au ' . $date_fin);
        $this->streamPdfLike('tafire_' . date('Ymd') . '.pdf', 'TAFIRE', $html);
    }
}
