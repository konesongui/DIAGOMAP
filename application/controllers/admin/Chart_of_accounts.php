<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Chart_of_accounts extends Ohada_Admin_Controller
{
    protected $page_title = 'Plan comptable';
    protected $page_subtitle = 'Gestion des comptes SYSCOHADA par entreprise.';
    protected $sub_menu = 'admin/admin/comptabilite';

    public function index()
    {
        $this->setMenuState();

        $search = trim((string) $this->input->get('search'));
        $classe = trim((string) $this->input->get('classe'));
        $date_debut = $this->inputDate('date_debut', date('Y-m-01'));
        $date_fin = $this->inputDate('date_fin', date('Y-m-t'));
        $accounts = $this->ohada_model->get_accounts($search, $classe);
        $counts = $this->ohada_model->get_dashboard_counts();
        $report = $this->ohada_model->get_balance_generale_report($date_debut, $date_fin, $classe, true);
        $report_map = array();
        foreach ($report['rows'] as $row) {
            $report_map[$row['compte']] = $row;
        }

        $filters_html = form_open(current_url(), array('method' => 'get', 'class' => 'form-inline'));
        $filters_html .= '<div class="form-group" style="margin-right:10px;"><label style="margin-right:6px;">Recherche</label><input type="text" name="search" class="form-control" value="' . html_escape($search) . '" placeholder="Numero ou libelle"></div>';
        $filters_html .= '<div class="form-group" style="margin-right:10px;"><label style="margin-right:6px;">Classe</label><select name="classe" class="form-control"><option value="">Toutes</option>';
        foreach ($this->ohada_model->get_default_classes() as $code => $label) {
            $selected = $classe === $code ? ' selected' : '';
            $filters_html .= '<option value="' . html_escape($code) . '"' . $selected . '>' . html_escape($code . ' - ' . $label) . '</option>';
        }
        $filters_html .= '</select></div>';
        $filters_html .= '<div class="form-group" style="margin-right:10px;"><label style="margin-right:6px;">Du</label><input type="date" name="date_debut" class="form-control" value="' . html_escape($date_debut) . '"></div>';
        $filters_html .= '<div class="form-group" style="margin-right:10px;"><label style="margin-right:6px;">Au</label><input type="date" name="date_fin" class="form-control" value="' . html_escape($date_fin) . '"></div>';
        $filters_html .= '<button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Filtrer</button> ';
        $filters_html .= '<a href="' . site_url('admin/chart_of_accounts') . '" class="btn btn-default">Reinitialiser</a>';
        $filters_html .= form_close();

        $rows = array();
        foreach ($accounts as $account) {
            $summary = isset($report_map[$account['numero_compte']]) ? $report_map[$account['numero_compte']] : null;
            $rows[] = array(
                html_escape($account['numero_compte']),
                html_escape($account['libelle_compte']),
                html_escape($account['classe']),
                html_escape($account['type_compte']),
                html_escape($account['nature']),
                $summary ? number_format((float) $summary['solde_ouverture_debit'] + (float) $summary['solde_ouverture_credit'], 2, ',', ' ') : '0,00',
                $summary ? number_format((float) $summary['mouvement_debit'], 2, ',', ' ') : '0,00',
                $summary ? number_format((float) $summary['mouvement_credit'], 2, ',', ' ') : '0,00',
                $summary ? number_format((float) $summary['solde_cloture_debit'] + (float) $summary['solde_cloture_credit'], 2, ',', ' ') : '0,00',
                !empty($account['allow_posting']) ? 'Oui' : 'Non',
                html_escape(isset($account['status']) ? $account['status'] : 'active'),
                '<a class="btn btn-xs btn-primary" href="' . site_url('admin/chart_of_accounts/edit/' . $account['id']) . '"><i class="fa fa-pencil"></i></a> ' .
                '<a class="btn btn-xs btn-danger" href="' . site_url('admin/chart_of_accounts/delete/' . $account['id']) . '" onclick="return confirm(\'Supprimer ce compte ?\')"><i class="fa fa-trash"></i></a>',
            );
        }

        $actions = '<a href="' . site_url('admin/chart_of_accounts/add') . '" class="btn btn-primary"><i class="fa fa-plus"></i> Nouveau compte</a> ' .
            '<a href="' . site_url('admin/syscohada/import') . '" class="btn btn-default"><i class="fa fa-download"></i> Charger la base SYSCOHADA</a> ' .
            '<a href="' . site_url('admin/chart_of_accounts/export_excel?search=' . rawurlencode($search) . '&classe=' . rawurlencode($classe) . '&date_debut=' . $date_debut . '&date_fin=' . $date_fin) . '" class="btn btn-default">Export CSV</a> ' .
            '<a href="' . site_url('admin/chart_of_accounts/export_pdf?search=' . rawurlencode($search) . '&classe=' . rawurlencode($classe) . '&date_debut=' . $date_debut . '&date_fin=' . $date_fin) . '" class="btn btn-default">Export PDF</a>';

        $this->renderOhadaPage(array(
            'cards' => array(
                array('label' => 'Comptes', 'value' => count($accounts)),
                array('label' => 'Mouvement debit', 'value' => number_format($report['totals']['mouvement_debit'], 2, ',', ' ')),
                array('label' => 'Mouvement credit', 'value' => number_format($report['totals']['mouvement_credit'], 2, ',', ' ')),
                array('label' => 'Solde cloture', 'value' => number_format($report['totals']['cloture_debit'] + $report['totals']['cloture_credit'], 2, ',', ' ')),
            ),
            'actions_html' => $actions,
            'filters_html' => $filters_html,
            'table_headers' => array('Compte', 'Libelle', 'Classe', 'Type', 'Nature', 'Ouverture', 'Debit periode', 'Credit periode', 'Cloture', 'Saisie', 'Statut', 'Actions'),
            'table_rows' => $rows,
            'empty_message' => 'Aucun compte comptable disponible. Chargez le referentiel SYSCOHADA ou ajoutez vos comptes.',
            'info_message' => 'Le plan comptable sert de referentiel aux balances et etats financiers. Les colonnes d ouverture, mouvements et cloture sont calculees sur la periode choisie.',
        ));
    }

    public function export_excel()
    {
        $payload = $this->buildExportRows();
        $this->streamCsv('plan_comptable_' . date('Ymd') . '.csv', $payload['headers'], $payload['rows']);
    }

    public function export_pdf()
    {
        $payload = $this->buildExportRows();
        $html = $this->buildSimplePdfHtml('Plan comptable OHADA', $payload['headers'], $payload['rows'], $payload['subtitle']);
        $this->streamPdfLike('plan_comptable_' . date('Ymd') . '.pdf', 'Plan comptable OHADA', $html);
    }

    public function add()
    {
        $this->save();
    }

    public function edit($id)
    {
        $this->save((int) $id);
    }

    protected function save($id = null)
    {
        $record = $id ? $this->ohada_model->get_account($id) : array();
        if ($id && empty($record)) {
            show_404();
        }

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('numero_compte', 'Numero compte', 'required|trim');
            $this->form_validation->set_rules('libelle_compte', 'Libelle compte', 'required|trim');
            $this->form_validation->set_rules('classe', 'Classe', 'required|trim');
            $this->form_validation->set_rules('type_compte', 'Type compte', 'required|trim');
            $this->form_validation->set_rules('nature', 'Nature', 'required|trim');

            if ($this->form_validation->run()) {
                $payload = array(
                    'numero_compte' => $this->input->post('numero_compte', true),
                    'libelle_compte' => $this->input->post('libelle_compte', true),
                    'classe' => $this->input->post('classe', true),
                    'type_compte' => $this->input->post('type_compte', true),
                    'compte_parent' => $this->input->post('compte_parent', true),
                    'nature' => $this->input->post('nature', true),
                    'allow_posting' => $this->input->post('allow_posting') ? 1 : 0,
                    'status' => $this->input->post('status', true),
                );
                $this->ohada_model->save_account($payload, $id);
                $this->redirectBackToModule('admin/chart_of_accounts', 'Compte enregistre avec succes.');
                return;
            }
        }

        $values = array_merge(array(
            'numero_compte' => '',
            'libelle_compte' => '',
            'classe' => '',
            'type_compte' => 'bilan',
            'compte_parent' => '',
            'nature' => 'debit',
            'allow_posting' => 1,
            'status' => 'active',
        ), $record);

        $fields = '<div class="row">';
        $fields .= '<div class="col-md-4"><div class="form-group"><label>Numero compte</label><input type="text" name="numero_compte" class="form-control" value="' . html_escape(set_value('numero_compte', $values['numero_compte'])) . '"></div></div>';
        $fields .= '<div class="col-md-8"><div class="form-group"><label>Libelle compte</label><input type="text" name="libelle_compte" class="form-control" value="' . html_escape(set_value('libelle_compte', $values['libelle_compte'])) . '"></div></div>';
        $fields .= '<div class="col-md-3"><div class="form-group"><label>Classe</label><select name="classe" class="form-control">';
        foreach ($this->ohada_model->get_default_classes() as $code => $label) {
            $selected = set_value('classe', $values['classe']) === $code ? ' selected' : '';
            $fields .= '<option value="' . html_escape($code) . '"' . $selected . '>' . html_escape($code) . '</option>';
        }
        $fields .= '</select></div></div>';
        $fields .= '<div class="col-md-3"><div class="form-group"><label>Type</label><select name="type_compte" class="form-control">';
        foreach (array('bilan' => 'Bilan', 'resultat' => 'Resultat', 'analytique' => 'Analytique') as $key => $label) {
            $selected = set_value('type_compte', $values['type_compte']) === $key ? ' selected' : '';
            $fields .= '<option value="' . html_escape($key) . '"' . $selected . '>' . html_escape($label) . '</option>';
        }
        $fields .= '</select></div></div>';
        $fields .= '<div class="col-md-3"><div class="form-group"><label>Compte parent</label><input type="text" name="compte_parent" class="form-control" value="' . html_escape(set_value('compte_parent', $values['compte_parent'])) . '"></div></div>';
        $fields .= '<div class="col-md-3"><div class="form-group"><label>Nature</label><select name="nature" class="form-control">';
        foreach (array('debit' => 'Debit', 'credit' => 'Credit', 'mixte' => 'Mixte') as $key => $label) {
            $selected = set_value('nature', $values['nature']) === $key ? ' selected' : '';
            $fields .= '<option value="' . html_escape($key) . '"' . $selected . '>' . html_escape($label) . '</option>';
        }
        $fields .= '</select></div></div>';
        $checked = set_value('allow_posting', $values['allow_posting']) ? ' checked' : '';
        $fields .= '<div class="col-md-6"><div class="checkbox"><label><input type="checkbox" name="allow_posting" value="1"' . $checked . '> Autoriser la saisie directe</label></div></div>';
        $fields .= '<div class="col-md-6"><div class="form-group"><label>Statut</label><select name="status" class="form-control">';
        foreach (array('active' => 'Actif', 'inactive' => 'Inactif') as $key => $label) {
            $selected = set_value('status', $values['status']) === $key ? ' selected' : '';
            $fields .= '<option value="' . html_escape($key) . '"' . $selected . '>' . html_escape($label) . '</option>';
        }
        $fields .= '</select></div></div>';
        $fields .= '</div>';

        if (validation_errors()) {
            $fields = '<div class="alert alert-danger">' . validation_errors() . '</div>' . $fields;
        }

        $this->renderOhadaForm(array(
            'title' => $id ? 'Modifier un compte' : 'Nouveau compte comptable',
            'subtitle' => 'Enregistrez un compte du plan comptable OHADA.',
            'form_action' => $id ? site_url('admin/chart_of_accounts/edit/' . $id) : site_url('admin/chart_of_accounts/add'),
            'cancel_url' => site_url('admin/chart_of_accounts'),
            'fields_html' => $fields,
            'submit_label' => $id ? 'Mettre a jour' : 'Creer le compte',
        ));
    }

    protected function buildExportRows()
    {
        $search = trim((string) $this->input->get('search'));
        $classe = trim((string) $this->input->get('classe'));
        $date_debut = $this->inputDate('date_debut', date('Y-m-01'));
        $date_fin = $this->inputDate('date_fin', date('Y-m-t'));
        $accounts = $this->ohada_model->get_accounts($search, $classe);
        $report = $this->ohada_model->get_balance_generale_report($date_debut, $date_fin, $classe, true);
        $report_map = array();
        foreach ($report['rows'] as $row) {
            $report_map[$row['compte']] = $row;
        }

        $rows = array();
        foreach ($accounts as $account) {
            $summary = isset($report_map[$account['numero_compte']]) ? $report_map[$account['numero_compte']] : null;
            $rows[] = array(
                $account['numero_compte'],
                $account['libelle_compte'],
                $account['classe'],
                $account['type_compte'],
                $account['nature'],
                $summary ? $summary['opening_balance'] : 0,
                $summary ? $summary['mouvement_debit'] : 0,
                $summary ? $summary['mouvement_credit'] : 0,
                $summary ? $summary['closing_balance'] : 0,
                !empty($account['allow_posting']) ? 'Oui' : 'Non',
                isset($account['status']) ? $account['status'] : 'active',
            );
        }

        return array(
            'headers' => array('Compte', 'Libelle', 'Classe', 'Type', 'Nature', 'Ouverture', 'Debit periode', 'Credit periode', 'Cloture', 'Saisie', 'Statut'),
            'rows' => $rows,
            'subtitle' => 'Periode du ' . $date_debut . ' au ' . $date_fin,
        );
    }

    public function delete($id)
    {
        $this->ohada_model->soft_delete('chart_of_accounts', (int) $id);
        $this->redirectBackToModule('admin/chart_of_accounts', 'Compte supprime avec succes.');
    }
}
