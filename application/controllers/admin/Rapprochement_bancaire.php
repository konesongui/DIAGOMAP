<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Rapprochement_bancaire extends Ohada_Admin_Controller
{
    protected $page_title = 'Rapprochement bancaire';
    protected $page_subtitle = 'Gestion des ecarts et pointage des operations bancaires.';

    public function index()
    {
        $this->setMenuState('admin/frontoffice/rapprochement_bancaire');
        $items = $this->ohada_model->get_bank_reconciliations();
        $rows = array();
        foreach ($items as $item) {
            $rows[] = array(
                html_escape($item['date_operation']),
                html_escape($item['reference']),
                html_escape($item['libelle']),
                number_format((float) $item['montant'], 2, ',', ' '),
                html_escape($item['statut']),
                '<a class="btn btn-xs btn-success" href="' . site_url('admin/frontoffice/rapprochement_bancaire/lettrage?id=' . $item['id']) . '">Lettrer</a> ' .
                '<a class="btn btn-xs btn-danger" href="' . site_url('admin/frontoffice/rapprochement_bancaire/delettrage/' . $item['id']) . '">Annuler</a>',
            );
        }

        $action_form = form_open(site_url('admin/frontoffice/rapprochement_bancaire/importer'), array('method' => 'post', 'class' => 'form-inline'));
        $action_form .= '<input type="date" name="date_operation" class="form-control" required style="margin-right:8px;">';
        $action_form .= '<input type="text" name="reference" class="form-control" required placeholder="Reference" style="margin-right:8px;">';
        $action_form .= '<input type="text" name="libelle" class="form-control" required placeholder="Libelle" style="margin-right:8px;">';
        $action_form .= '<input type="number" step="0.01" min="0" name="montant" class="form-control" required placeholder="Montant" style="margin-right:8px;">';
        $action_form .= '<button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Ajouter une ligne</button>';
        $action_form .= form_close();

        $this->renderOhadaPage(array(
            'cards' => array(array('label' => 'Lignes bancaires', 'value' => count($items))),
            'actions_html' => '<a href="' . site_url('admin/frontoffice/rapprochement_bancaire/export_pdf') . '" class="btn btn-default">Export PDF</a>',
            'filters_html' => $action_form,
            'table_headers' => array('Date', 'Reference', 'Libelle', 'Montant', 'Statut', 'Actions'),
            'table_rows' => $rows,
            'empty_message' => 'Aucune ligne de rapprochement disponible.',
        ));
    }

    public function lettrage()
    {
        $id = (int) $this->input->get('id');
        $this->db->where('id', $id);
        $this->db->where('entreprise_id', $this->ohada_model->get_entreprise_id());
        $this->db->update('ohada_rapprochements', array('statut' => 'lettre', 'updated_at' => date('Y-m-d H:i:s')));
        $this->redirectBackToModule('admin/frontoffice/rapprochement_bancaire', 'Ligne lettragee avec succes.');
    }

    public function delettrage($id)
    {
        $this->db->where('id', (int) $id);
        $this->db->where('entreprise_id', $this->ohada_model->get_entreprise_id());
        $this->db->update('ohada_rapprochements', array('statut' => 'en_attente', 'updated_at' => date('Y-m-d H:i:s')));
        $this->redirectBackToModule('admin/frontoffice/rapprochement_bancaire', 'Lettrage annule avec succes.');
    }

    public function importer()
    {
        $this->ohada_model->save_bank_reconciliation(array(
            'date_operation' => $this->input->post('date_operation', true),
            'reference' => $this->input->post('reference', true),
            'libelle' => $this->input->post('libelle', true),
            'montant' => $this->input->post('montant', true),
            'statut' => 'en_attente',
        ));
        $this->redirectBackToModule('admin/frontoffice/rapprochement_bancaire', 'Ligne bancaire importee avec succes.');
    }

    public function export_pdf()
    {
        $items = $this->ohada_model->get_bank_reconciliations();
        $rows = array();
        foreach ($items as $item) {
            $rows[] = array($item['date_operation'], $item['reference'], $item['libelle'], $item['montant'], $item['statut']);
        }
        $html = $this->buildSimplePdfHtml('Rapprochement bancaire', array('Date', 'Reference', 'Libelle', 'Montant', 'Statut'), $rows, 'Etat des rapprochements');
        $this->streamPdfLike('rapprochement_bancaire_' . date('Ymd') . '.pdf', 'Rapprochement bancaire', $html);
    }
}

