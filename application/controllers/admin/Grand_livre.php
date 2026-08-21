<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Grand_livre extends Ohada_Admin_Controller
{
    protected $page_title = 'Grand livre';
    protected $page_subtitle = 'Consultation detaillee des mouvements par compte.';

    public function index()
    {
        $this->setMenuState('admin/frontoffice/grand_livre');
        $date_debut = $this->inputDate('date_debut', date('Y-m-01'));
        $date_fin = $this->inputDate('date_fin', date('Y-m-t'));
        $compte = trim((string) $this->input->get('compte'));
        $report = $this->ohada_model->get_grand_livre_report($date_debut, $date_fin, $compte);
        $rows = $report['rows'];

        $table_rows = array();
        foreach ($rows as $row) {
            $table_rows[] = array(
                html_escape($row['account']),
                html_escape($row['account_label']),
                html_escape($row['date']),
                html_escape($row['journal']),
                html_escape($row['counterpart']),
                html_escape($row['libelle']),
                html_escape($row['piece']),
                number_format($row['debit'], 2, ',', ' '),
                number_format($row['credit'], 2, ',', ' '),
                number_format($row['running_balance'], 2, ',', ' '),
                $row['type'] === 'movement' ? '<a href="' . site_url('admin/frontoffice/grand_livre/detail/' . rawurlencode($row['account'])) . '" class="btn btn-xs btn-default">Voir</a>' : '',
            );
        }

        $filters_html = form_open(current_url(), array('method' => 'get', 'class' => 'form-inline'));
        $filters_html .= '<div class="form-group" style="margin-right:10px;"><label style="margin-right:6px;">Du</label><input type="date" name="date_debut" class="form-control" value="' . html_escape($date_debut) . '"></div>';
        $filters_html .= '<div class="form-group" style="margin-right:10px;"><label style="margin-right:6px;">Au</label><input type="date" name="date_fin" class="form-control" value="' . html_escape($date_fin) . '"></div>';
        $filters_html .= '<div class="form-group" style="margin-right:10px;"><label style="margin-right:6px;">Compte</label><input type="text" name="compte" class="form-control" value="' . html_escape($compte) . '" placeholder="512"></div>';
        $filters_html .= '<button type="submit" class="btn btn-primary">Filtrer</button>';
        $filters_html .= form_close();

        $this->renderOhadaPage(array(
            'cards' => array(
                array('label' => 'Lignes', 'value' => count($rows)),
                array('label' => 'Comptes concernes', 'value' => $report['account_count']),
                array('label' => 'Debit periode', 'value' => number_format($report['totals']['debit'], 2, ',', ' ')),
                array('label' => 'Credit periode', 'value' => number_format($report['totals']['credit'], 2, ',', ' ')),
                array('label' => 'Debut', 'value' => date('d/m/Y', strtotime($date_debut))),
                array('label' => 'Fin', 'value' => date('d/m/Y', strtotime($date_fin))),
            ),
            'actions_html' => '<a href="' . site_url('admin/frontoffice/grand_livre/export_excel?date_debut=' . $date_debut . '&date_fin=' . $date_fin . '&compte=' . rawurlencode($compte)) . '" class="btn btn-default">Export CSV</a> <a href="' . site_url('admin/frontoffice/grand_livre/export_pdf?date_debut=' . $date_debut . '&date_fin=' . $date_fin . '&compte=' . rawurlencode($compte)) . '" class="btn btn-default">Export PDF</a>',
            'filters_html' => $filters_html,
            'table_headers' => array('Compte', 'Libelle compte', 'Date', 'Journal', 'Contrepartie', 'Libelle ecriture', 'Piece', 'Debit', 'Credit', 'Solde progressif', 'Detail'),
            'table_rows' => $table_rows,
            'empty_message' => 'Aucun mouvement comptable sur la periode selectionnee.',
            'info_message' => 'Le grand livre integre un solde d ouverture par compte, puis calcule le solde progressif ligne par ligne.',
        ));
    }

    public function detail($compte)
    {
        $_GET['compte'] = $compte;
        $this->index();
    }

    public function export_excel()
    {
        $date_debut = $this->inputDate('date_debut', date('Y-m-01'));
        $date_fin = $this->inputDate('date_fin', date('Y-m-t'));
        $compte = trim((string) $this->input->get('compte'));
        $rows = $this->ohada_model->get_grand_livre_report($date_debut, $date_fin, $compte)['rows'];
        $csv = array();
        foreach ($rows as $row) {
            $csv[] = array($row['account'], $row['account_label'], $row['date'], $row['journal'], $row['counterpart'], $row['libelle'], $row['piece'], $row['debit'], $row['credit'], $row['running_balance']);
        }
        $this->streamCsv('grand_livre_' . date('Ymd') . '.csv', array('Compte', 'Libelle compte', 'Date', 'Journal', 'Contrepartie', 'Libelle', 'Piece', 'Debit', 'Credit', 'Solde progressif'), $csv);
    }

    public function export_pdf()
    {
        $date_debut = $this->inputDate('date_debut', date('Y-m-01'));
        $date_fin = $this->inputDate('date_fin', date('Y-m-t'));
        $compte = trim((string) $this->input->get('compte'));
        $rows = $this->ohada_model->get_grand_livre_report($date_debut, $date_fin, $compte)['rows'];
        $flat = array();
        foreach ($rows as $row) {
            $flat[] = array($row['account'], $row['account_label'], $row['date'], $row['journal'], $row['counterpart'], $row['libelle'], $row['piece'], $row['debit'], $row['credit'], $row['running_balance']);
        }
        $html = $this->buildSimplePdfHtml('Grand livre', array('Compte', 'Libelle compte', 'Date', 'Journal', 'Contrepartie', 'Libelle', 'Piece', 'Debit', 'Credit', 'Solde progressif'), $flat, 'Periode du ' . $date_debut . ' au ' . $date_fin);
        $this->streamPdfLike('grand_livre_' . date('Ymd') . '.pdf', 'Grand livre', $html);
    }
}
