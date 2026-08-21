<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Journal_entries extends Ohada_Admin_Controller
{
    protected $page_title = 'Ecritures comptables';
    protected $page_subtitle = 'Saisie et consultation des ecritures comptables OHADA.';

    public function index()
    {
        $this->setMenuState();

        $filters = array(
            'date_debut' => $this->inputDate('date_debut', date('Y-m-01')),
            'date_fin' => $this->inputDate('date_fin', date('Y-m-t')),
            'journal_id' => $this->input->get('journal_id'),
            'compte' => $this->input->get('compte'),
        );

        $entries = $this->ohada_model->get_journal_entries($filters);
        $journals = $this->ohada_model->get_journals();
        $totals = $this->ohada_model->get_entries_totals($filters);

        $filters_html = form_open(current_url(), array('method' => 'get', 'class' => 'form-inline'));
        $filters_html .= '<div class="form-group" style="margin-right:10px;"><label style="margin-right:6px;">Du</label><input type="date" name="date_debut" class="form-control" value="' . html_escape($filters['date_debut']) . '"></div>';
        $filters_html .= '<div class="form-group" style="margin-right:10px;"><label style="margin-right:6px;">Au</label><input type="date" name="date_fin" class="form-control" value="' . html_escape($filters['date_fin']) . '"></div>';
        $filters_html .= '<div class="form-group" style="margin-right:10px;"><label style="margin-right:6px;">Journal</label><select name="journal_id" class="form-control"><option value="">Tous</option>';
        foreach ($journals as $journal) {
            $selected = (string) $filters['journal_id'] === (string) $journal['id'] ? ' selected' : '';
            $filters_html .= '<option value="' . (int) $journal['id'] . '"' . $selected . '>' . html_escape($journal['code'] . ' - ' . $journal['libelle']) . '</option>';
        }
        $filters_html .= '</select></div>';
        $filters_html .= '<div class="form-group" style="margin-right:10px;"><label style="margin-right:6px;">Compte</label><input type="text" name="compte" class="form-control" value="' . html_escape((string) $filters['compte']) . '" placeholder="601, 512..."></div>';
        $filters_html .= '<button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> Filtrer</button> ';
        $filters_html .= '<a href="' . site_url('admin/journal_entries') . '" class="btn btn-default">Reinitialiser</a>';
        $filters_html .= form_close();

        $rows = array();
        foreach ($entries as $entry) {
            $amount = number_format((float) $entry['montant'], 2, ',', ' ');
            $rows[] = array(
                html_escape($entry['date_ecriture']),
                html_escape(isset($entry['journal_code']) ? $entry['journal_code'] : ''),
                html_escape($entry['compte_debit']),
                html_escape($entry['compte_credit']),
                html_escape($entry['libelle']),
                html_escape(isset($entry['reference_piece']) ? $entry['reference_piece'] : ''),
                html_escape($amount),
                html_escape(isset($entry['tiers_code']) ? $entry['tiers_code'] : ''),
                html_escape(isset($entry['analytique_code']) ? $entry['analytique_code'] : ''),
            );
        }

        $this->renderOhadaPage(array(
            'cards' => array(
                array('label' => 'Ecritures', 'value' => count($entries)),
                array('label' => 'Debit', 'value' => number_format($totals['debit'], 2, ',', ' ')),
                array('label' => 'Credit', 'value' => number_format($totals['credit'], 2, ',', ' ')),
                array('label' => 'Equilibre', 'value' => abs($totals['debit'] - $totals['credit']) < 0.0001 ? 'Oui' : 'Verifier'),
            ),
            'actions_html' => '<a href="' . site_url('admin/journal_entries/add') . '" class="btn btn-primary"><i class="fa fa-plus"></i> Nouvelle ecriture</a>',
            'filters_html' => $filters_html,
            'table_headers' => array('Date', 'Journal', 'Compte debit', 'Compte credit', 'Libelle', 'Piece', 'Montant', 'Tiers', 'Analytique'),
            'table_rows' => $rows,
            'empty_message' => 'Aucune ecriture pour les criteres selectionnes.',
            'info_message' => 'Chaque ecriture cree automatiquement un montant debit et credit identiques pour garantir l equilibre de la piece.',
        ));
    }

    public function add()
    {
        $this->setMenuState();

        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('date_ecriture', 'Date ecriture', 'required');
            $this->form_validation->set_rules('journal_id', 'Journal', 'required|integer');
            $this->form_validation->set_rules('compte_debit', 'Compte debit', 'required|trim');
            $this->form_validation->set_rules('compte_credit', 'Compte credit', 'required|trim');
            $this->form_validation->set_rules('montant', 'Montant', 'required|numeric');
            $this->form_validation->set_rules('libelle', 'Libelle', 'required|trim');

            if ($this->form_validation->run()) {
                $payload = array(
                    'date_ecriture' => $this->input->post('date_ecriture', true),
                    'journal_id' => $this->input->post('journal_id', true),
                    'compte_debit' => $this->input->post('compte_debit', true),
                    'compte_credit' => $this->input->post('compte_credit', true),
                    'montant' => $this->input->post('montant', true),
                    'libelle' => $this->input->post('libelle', true),
                    'reference_piece' => $this->input->post('reference_piece', true),
                    'piece_justificative' => $this->input->post('piece_justificative', true),
                    'tier_id' => $this->input->post('tier_id', true),
                    'analytic_id' => $this->input->post('analytic_id', true),
                    'exercice_id' => $this->input->post('exercice_id', true),
                    'status' => $this->input->post('status', true),
                );
                $this->ohada_model->add_journal_entry($payload);
                $this->redirectBackToModule('admin/journal_entries', 'Ecriture comptable enregistree avec succes.');
                return;
            }
        }

        $journals = $this->ohada_model->get_journals();
        $accounts = $this->ohada_model->get_accounts();
        $tiers = $this->ohada_model->get_tiers();
        $analytics = $this->ohada_model->get_analytics();
        $active_exercise = $this->ohada_model->get_active_exercise();

        $fields = validation_errors() ? '<div class="alert alert-danger">' . validation_errors() . '</div>' : '';
        $fields .= '<div class="row">';
        $fields .= '<div class="col-md-3"><div class="form-group"><label>Date</label><input type="date" name="date_ecriture" class="form-control" value="' . html_escape(set_value('date_ecriture', date('Y-m-d'))) . '"></div></div>';
        $fields .= '<div class="col-md-3"><div class="form-group"><label>Journal</label><select name="journal_id" class="form-control">';
        foreach ($journals as $journal) {
            $selected = set_value('journal_id') == $journal['id'] ? ' selected' : '';
            $fields .= '<option value="' . (int) $journal['id'] . '"' . $selected . '>' . html_escape($journal['code'] . ' - ' . $journal['libelle']) . '</option>';
        }
        $fields .= '</select></div></div>';
        $fields .= '<div class="col-md-3"><div class="form-group"><label>Exercice</label><input type="text" class="form-control" value="' . html_escape($active_exercise ? $active_exercise['libelle'] : 'Aucun exercice actif') . '" readonly></div></div>';
        $fields .= '<div class="col-md-3"><div class="form-group"><label>Montant</label><input type="number" step="0.01" min="0" name="montant" class="form-control" value="' . html_escape(set_value('montant')) . '"></div></div>';
        $fields .= '<div class="col-md-6"><div class="form-group"><label>Compte debit</label><select name="compte_debit" class="form-control">';
        foreach ($accounts as $account) {
            $selected = set_value('compte_debit') === $account['numero_compte'] ? ' selected' : '';
            $fields .= '<option value="' . html_escape($account['numero_compte']) . '"' . $selected . '>' . html_escape($account['numero_compte'] . ' - ' . $account['libelle_compte']) . '</option>';
        }
        $fields .= '</select></div></div>';
        $fields .= '<div class="col-md-6"><div class="form-group"><label>Compte credit</label><select name="compte_credit" class="form-control">';
        foreach ($accounts as $account) {
            $selected = set_value('compte_credit') === $account['numero_compte'] ? ' selected' : '';
            $fields .= '<option value="' . html_escape($account['numero_compte']) . '"' . $selected . '>' . html_escape($account['numero_compte'] . ' - ' . $account['libelle_compte']) . '</option>';
        }
        $fields .= '</select></div></div>';
        $fields .= '<div class="col-md-12"><div class="form-group"><label>Libelle</label><input type="text" name="libelle" class="form-control" value="' . html_escape(set_value('libelle')) . '"></div></div>';
        $fields .= '<div class="col-md-4"><div class="form-group"><label>Reference piece</label><input type="text" name="reference_piece" class="form-control" value="' . html_escape(set_value('reference_piece')) . '"></div></div>';
        $fields .= '<div class="col-md-4"><div class="form-group"><label>Tiers</label><select name="tier_id" class="form-control"><option value="">Aucun</option>';
        foreach ($tiers as $tier) {
            $selected = set_value('tier_id') == $tier['id'] ? ' selected' : '';
            $fields .= '<option value="' . (int) $tier['id'] . '"' . $selected . '>' . html_escape($tier['code'] . ' - ' . $tier['libelle']) . '</option>';
        }
        $fields .= '</select></div></div>';
        $fields .= '<div class="col-md-4"><div class="form-group"><label>Analytique</label><select name="analytic_id" class="form-control"><option value="">Aucun</option>';
        foreach ($analytics as $analytic) {
            $selected = set_value('analytic_id') == $analytic['id'] ? ' selected' : '';
            $fields .= '<option value="' . (int) $analytic['id'] . '"' . $selected . '>' . html_escape($analytic['code'] . ' - ' . $analytic['libelle']) . '</option>';
        }
        $fields .= '</select></div></div>';
        $fields .= '<div class="col-md-6"><div class="form-group"><label>Piece justificative</label><input type="text" name="piece_justificative" class="form-control" value="' . html_escape(set_value('piece_justificative')) . '"></div></div>';
        $fields .= '<div class="col-md-6"><div class="form-group"><label>Statut</label><select name="status" class="form-control"><option value="posted">Postee</option><option value="draft">Brouillon</option></select></div></div>';
        $fields .= '</div>';
        $fields .= '<input type="hidden" name="exercice_id" value="' . html_escape($active_exercise ? $active_exercise['id'] : '') . '">';

        $this->renderOhadaForm(array(
            'title' => 'Nouvelle ecriture comptable',
            'subtitle' => 'Saisissez une ecriture simple et equilibree dans le journal selectionne.',
            'form_action' => site_url('admin/journal_entries/add'),
            'cancel_url' => site_url('admin/journal_entries'),
            'fields_html' => $fields,
            'submit_label' => 'Enregistrer l ecriture',
            'info_message' => empty($journals) ? 'Aucun journal disponible. Commencez par configurer vos journaux auxiliaires.' : '',
        ));
    }
}
