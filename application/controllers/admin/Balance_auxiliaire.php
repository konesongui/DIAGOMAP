<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Balance_auxiliaire extends Ohada_Admin_Controller
{
    protected $page_title = 'Balance auxiliaire';
    protected $page_subtitle = 'Suivi des soldes par tiers sur la periode selectionnee.';

    public function index()
    {
        $this->setMenuState('admin/frontoffice/balance_auxiliaire');
        $date_debut = $this->inputDate('date_debut', date('Y-m-01'));
        $date_fin = $this->inputDate('date_fin', date('Y-m-t'));
        $rows = $this->ohada_model->get_balance_auxiliaire_data($date_debut, $date_fin);

        $table_rows = array();
        $total_debit = 0;
        $total_credit = 0;
        foreach ($rows as $row) {
            $total_debit += $row['debit'];
            $total_credit += $row['credit'];
            $table_rows[] = array(
                html_escape($row['code']),
                html_escape($row['libelle']),
                html_escape($row['type']),
                number_format($row['debit'], 2, ',', ' '),
                number_format($row['credit'], 2, ',', ' '),
                number_format($row['solde'], 2, ',', ' '),
            );
        }

        $filters_html = form_open(current_url(), array('method' => 'get', 'class' => 'form-inline'));
        $filters_html .= '<div class="form-group" style="margin-right:10px;"><label style="margin-right:6px;">Du</label><input type="date" name="date_debut" class="form-control" value="' . html_escape($date_debut) . '"></div>';
        $filters_html .= '<div class="form-group" style="margin-right:10px;"><label style="margin-right:6px;">Au</label><input type="date" name="date_fin" class="form-control" value="' . html_escape($date_fin) . '"></div>';
        $filters_html .= '<button type="submit" class="btn btn-primary">Filtrer</button>';
        $filters_html .= form_close();

        $actions = '<a href="' . site_url('admin/frontoffice/balance_auxiliaire/export_excel?date_debut=' . $date_debut . '&date_fin=' . $date_fin) . '" class="btn btn-default"><i class="fa fa-file-excel-o"></i> Export CSV</a> ';
        $actions .= '<a href="' . site_url('admin/frontoffice/balance_auxiliaire/export_pdf?date_debut=' . $date_debut . '&date_fin=' . $date_fin) . '" class="btn btn-default"><i class="fa fa-file-pdf-o"></i> Export PDF</a>';

        $this->renderOhadaPage(array(
            'cards' => array(
                array('label' => 'Tiers mouvementes', 'value' => count($rows)),
                array('label' => 'Debit', 'value' => number_format($total_debit, 2, ',', ' ')),
                array('label' => 'Credit', 'value' => number_format($total_credit, 2, ',', ' ')),
                array('label' => 'Periode', 'value' => date('d/m/Y', strtotime($date_debut)) . ' - ' . date('d/m/Y', strtotime($date_fin))),
            ),
            'actions_html' => $actions,
            'filters_html' => $filters_html,
            'table_headers' => array('Code', 'Libelle', 'Type', 'Debit', 'Credit', 'Solde'),
            'table_rows' => $table_rows,
            'empty_message' => 'Aucun tiers avec mouvement sur la periode.',
        ));
    }

    public function export_excel()
    {
        $date_debut = $this->inputDate('date_debut', date('Y-m-01'));
        $date_fin = $this->inputDate('date_fin', date('Y-m-t'));
        $rows = $this->ohada_model->get_balance_auxiliaire_data($date_debut, $date_fin);
        $csv = array();
        foreach ($rows as $row) {
            $csv[] = array($row['code'], $row['libelle'], $row['type'], $row['debit'], $row['credit'], $row['solde']);
        }
        $this->streamCsv('balance_auxiliaire_' . date('Ymd') . '.csv', array('Code', 'Libelle', 'Type', 'Debit', 'Credit', 'Solde'), $csv);
    }

    public function export_pdf()
    {
        $date_debut = $this->inputDate('date_debut', date('Y-m-01'));
        $date_fin = $this->inputDate('date_fin', date('Y-m-t'));
        $rows = $this->ohada_model->get_balance_auxiliaire_data($date_debut, $date_fin);
        $flat = array();
        foreach ($rows as $row) {
            $flat[] = array($row['code'], $row['libelle'], $row['type'], $row['debit'], $row['credit'], $row['solde']);
        }
        $html = $this->buildSimplePdfHtml('Balance auxiliaire', array('Code', 'Libelle', 'Type', 'Debit', 'Credit', 'Solde'), $flat, 'Periode du ' . $date_debut . ' au ' . $date_fin);
        $this->streamPdfLike('balance_auxiliaire_' . date('Ymd') . '.pdf', 'Balance auxiliaire', $html);
    }
}

