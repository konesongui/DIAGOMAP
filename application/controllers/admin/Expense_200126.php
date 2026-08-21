<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Expense extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('Customlib');
        $this->config->load('app-config');
        $this->load->library("datatables");
    }

    public function index()
    {
        // ... code existant inchangé ...
    }

    public function bank()
    {
        if (!$this->rbac->hasPrivilege('depense', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Caisse');
        $this->session->set_userdata('sub_menu', 'expense/bank');
        $data['title']      = 'Transactions Bancaires';
        $data['title_list'] = 'Transactions Récentes';

        // CORRECTION : CHARGER LES BANQUES POUR LA VUE
        $data['banks'] = $this->expense_model->get_banks(); // ← AJOUTÉ !

        // REMOVED: Validation for exp_head_id
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('documents', $this->lang->line('documents'), 'callback_handle_upload');
        $this->form_validation->set_rules('bank_id', 'Banque', 'trim|required|xss_clean'); // ← AJOUTÉ

        if ($this->form_validation->run() == true) {
            // Check if inc_head_id is provided and has sufficient balance
            $incomeId = (int)$this->input->post('inc_head_id') ?? 0;
            $amount = $this->input->post('amount') ? floatval($this->input->post('amount')) : 0;

            // Only check balance if a caisse is selected
            if ($incomeId > 0) {
                $oldData = $this->income_model->read('*', ['id' => $incomeId]);
                $incomeAmountAvailable = (float)$oldData->amount_re ?? 0;

                if ($incomeAmountAvailable < $amount) {
                    $this->session->set_flashdata('msg', '<div class="alert alert-warning text-left">Le solde de la caisse sélectionnée est insuffisant</div>');
                    redirect('admin/expense/bank');
                }

                // Update caisse balance if caisse was selected
                $newIncomeAmountAvailable = $incomeAmountAvailable - $amount;
                $this->income_model->updateP(['id' => $incomeId], [
                    'amount_re' => $newIncomeAmountAvailable
                ]);

                $this->Income_processing_model->createP([
                    'income_id' => $incomeId,
                    'amount'    => -$amount,
                    'reason'    => "Expense"
                ]);
            }

            // Prepare bank transaction data
            $data = [
                'bank_id' => $this->input->post('bank_id'), // ← AJOUTÉ
                'name' => $this->input->post('name'),
                'nom' => $this->input->post('nom'),
                'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'amount' => $this->input->post('amount'),
                'transaction_type' => $this->input->post('transaction_type'),
                'designation' => $this->input->post('designation'),
                'reference' => $this->input->post('reference'),
                'payment_mode' => $this->input->post('payment_mode'),
                'note' => $this->input->post('description'),
                'created_at' => date('Y-m-d H:i:s'),
            ];

            $insert_id = $this->expense_model->addbank($data);

            // Mettre à jour le solde de la banque
            $this->update_bank_balance($this->input->post('bank_id'));

            // ... reste du code existant pour les écritures comptables ...

            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/expense/bank');
        }

        // ... reste du code existant ...

        $this->load->view('layout/header', $data);
        $this->load->view('admin/expense/bank', $data);
        $this->load->view('layout/footer', $data);
    }
    /**
     * Édition d'une transaction bancaire
     */
    public function edit_bank_transaction($id)
    {
        if (!$this->rbac->hasPrivilege('depense', 'can_edit')) {
            access_denied();
        }

        $data['title'] = 'Modifier Transaction Bancaire';
        $data['transaction'] = $this->expense_model->get_bank_transaction($id);
        $data['banks'] = $this->expense_model->get_banks();

        // Si le formulaire est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->form_validation->set_rules('bank_id', 'Banque', 'trim|required|xss_clean');
            $this->form_validation->set_rules('name', 'Libellé', 'trim|required|xss_clean');
            $this->form_validation->set_rules('amount', 'Montant', 'trim|required|numeric|xss_clean');
            $this->form_validation->set_rules('date', 'Date', 'trim|required|xss_clean');
            $this->form_validation->set_rules('transaction_type', 'Type de transaction', 'trim|required|xss_clean');
            $this->form_validation->set_rules('designation', 'Désignation', 'trim|required|xss_clean');

            if ($this->form_validation->run() == true) {
                $update_data = [
                    'bank_id' => $this->input->post('bank_id'),
                    'name' => $this->input->post('name'),
                    'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                    'amount' => $this->input->post('amount'),
                    'transaction_type' => $this->input->post('transaction_type'),
                    'designation' => $this->input->post('designation'),
                    'reference' => $this->input->post('reference'),
                    'payment_mode' => $this->input->post('payment_mode'),
                    'note' => $this->input->post('description'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                // Mettre à jour la transaction
                $this->db->where('id', $id);
                $this->db->update('bank', $update_data);

                // Mettre à jour le solde de la banque
                $this->update_bank_balance($this->input->post('bank_id'));

                $this->session->set_flashdata('msg', '<div class="alert alert-success">Transaction mise à jour avec succès</div>');
                redirect('admin/expense/bank');
            }
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/expense/edit_bank_transaction', $data);
        $this->load->view('layout/footer', $data);
    }
    // AJOUTER CETTE MÉTHODE POUR METTRE À JOUR LE SOLDE DE LA BANQUE
    private function update_bank_balance_($bank_id)
    {
        // Calculer le nouveau solde basé sur toutes les transactions
        $this->db->select('SUM(CASE 
            WHEN designation = "Crédit" OR transaction_type IN ("Dépôt", "Virement entrant") THEN amount 
            ELSE -amount 
        END) as balance');
        $this->db->from('bank');
        $this->db->where('bank_id', $bank_id);
        $result = $this->db->get()->row();

        $new_balance = $result->balance ?? 0;

        // Mettre à jour la table banks
        $this->db->where('id', $bank_id);
        $this->db->update('banks', ['balance' => $new_balance, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    // AJOUTER CES NOUVELLES MÉTHODES POUR L'AJAX :

    /**
     * Retourne le HTML de la liste des banques (pour rafraîchissement AJAX)
     */
    public function get_bank_list()
    {
        $banks = $this->expense_model->get_banks_with_balance();
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();

        $html = '';

        if (!empty($banks)) {
            $total_balance = 0;

            foreach ($banks as $bank) {
                $balance = $bank->balance ?? 0;
                $total_balance += $balance;

                $html .= '<tr data-bank-id="' . $bank->id . '" ';
                $html .= 'data-bank-name="' . htmlspecialchars($bank->name) . '" ';
                $html .= 'data-bank-code="' . htmlspecialchars($bank->code) . '" ';
                $html .= 'data-bank-balance="' . $balance . '">';
                $html .= '<td>';
                $html .= '<strong style="color: #3498db;">' . htmlspecialchars($bank->name) . '</strong>';
                $html .= '<br><small class="text-muted">';
                $html .= '<i class="fa fa-hashtag"></i> ' . htmlspecialchars($bank->code);
                $html .= '</small>';
                $html .= '</td>';
                $html .= '<td>';
                $html .= '<span class="label label-primary">' . htmlspecialchars($bank->code) . '</span>';
                $html .= '</td>';
                $html .= '<td class="text-center">';
                $html .= '<span class="badge ' . ($balance >= 0 ? 'badge-success' : 'badge-danger') . '" ';
                $html .= 'style="font-size: 12px; padding: 5px 10px;">';
                $html .= number_format($balance, 2, ',', ' ') . ' ' . $currency_symbol;
                $html .= '</span>';
                $html .= '</td>';
                $html .= '<td class="text-center">';
                $html .= '<div class="btn-group btn-group-xs" role="group">';
                $html .= '<button class="btn btn-success add-transaction-btn" ';
                $html .= 'data-bank-id="' . $bank->id . '" ';
                $html .= 'data-bank-name="' . htmlspecialchars($bank->name) . '" ';
                $html .= 'title="Ajouter transaction">';
                $html .= '<i class="fa fa-plus-circle"></i>';
                $html .= '</button>';
                $html .= '<button class="btn btn-info view-transactions-btn" ';
                $html .= 'data-bank-id="' . $bank->id . '" ';
                $html .= 'data-bank-name="' . htmlspecialchars($bank->name) . '" ';
                $html .= 'title="Voir transactions">';
                $html .= '<i class="fa fa-eye"></i>';
                $html .= '</button>';
                $html .= '<button class="btn btn-warning edit-bank" ';
                $html .= 'data-id="' . $bank->id . '" ';
                $html .= 'data-name="' . htmlspecialchars($bank->name) . '" ';
                $html .= 'data-code="' . htmlspecialchars($bank->code) . '" ';
                $html .= 'title="Modifier">';
                $html .= '<i class="fa fa-edit"></i>';
                $html .= '</button>';
                $html .= '<button class="btn btn-danger delete-bank" ';
                $html .= 'data-id="' . $bank->id . '" ';
                $html .= 'title="Supprimer">';
                $html .= '<i class="fa fa-trash"></i>';
                $html .= '</button>';
                $html .= '</div>';
                $html .= '</td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr>';
            $html .= '<td colspan="4" class="text-center">';
            $html .= '<div class="alert alert-info" style="margin: 10px 0;">';
            $html .= '<i class="fa fa-info-circle"></i> Aucune banque enregistrée';
            $html .= '</div>';
            $html .= '<button type="button" class="btn btn-success" data-toggle="modal" data-target="#bankModal">';
            $html .= '<i class="fa fa-plus"></i> Créer une banque';
            $html .= '</button>';
            $html .= '</td>';
            $html .= '</tr>';
        }

        echo $html;
    }

    /**
     * Retourne les banques au format JSON pour les dropdowns
     */
    public function get_banks_dropdown()
    {
        $banks = $this->expense_model->get_banks();
        $data = [];

        foreach ($banks as $bank) {
            $data[] = [
                'id' => $bank->id,
                'name' => $bank->name . ' (' . $bank->code . ')',
                'code' => $bank->code
            ];
        }

        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Alternative pour get_banks_for_dropdown
     */
    public function get_banks_for_dropdown()
    {
        $this->get_banks_dropdown();
    }

    /**
     * Retourne les détails d'une transaction spécifique
     */
    public function get_transaction()
    {
        $transaction_id = $this->input->post('transaction_id');

        if (!$transaction_id) {
            echo json_encode(['status' => 'error', 'message' => 'ID transaction manquant']);
            return;
        }

        $this->db->where('id', $transaction_id);
        $transaction = $this->db->get('bank')->row();

        if (!$transaction) {
            echo json_encode(['status' => 'error', 'message' => 'Transaction non trouvée']);
            return;
        }

        // Formater les dates pour l'affichage
        if ($transaction->date) {
            $transaction->date = date($this->customlib->getSchoolDateFormat(), strtotime($transaction->date));
        }

        echo json_encode(['status' => 'success', 'data' => $transaction]);
    }


    public function get_transaction_details($transaction_id)
    {
        if (!$transaction_id) {
            echo '<div class="alert alert-danger">ID transaction manquant</div>';
            return;
        }

        // Récupérer la transaction avec les informations de la banque
        $this->db->select('bank.*, banks.name as bank_name, banks.code as bank_code, banks.account_number');
        $this->db->from('bank');
        $this->db->join('banks', 'banks.id = bank.bank_id', 'left');
        $this->db->where('bank.id', $transaction_id);
        $transaction = $this->db->get()->row();

        if (!$transaction) {
            echo '<div class="alert alert-danger">Transaction non trouvée</div>';
            return;
        }

        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();

        // Générer le HTML des détails
        $html = '<div class="container-fluid">';

        // Informations principales
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12">';
        $html .= '<h4><i class="fa fa-exchange"></i> Détails de la Transaction</h4>';
        $html .= '<hr>';
        $html .= '</div>';
        $html .= '</div>';

        // Informations de base
        $html .= '<div class="row">';
        $html .= '<div class="col-md-6">';
        $html .= '<div class="panel panel-default">';
        $html .= '<div class="panel-heading"><strong>Informations Générales</strong></div>';
        $html .= '<div class="panel-body">';
        $html .= '<table class="table table-condensed">';
        $html .= '<tr><td width="40%"><strong>Libellé :</strong></td><td>' . htmlspecialchars($transaction->name) . '</td></tr>';
        $html .= '<tr><td width="40%"><strong>Nom :</strong></td><td>' . htmlspecialchars($transaction->nom) . '</td></tr>';
        $html .= '<tr><td><strong>Date :</strong></td><td>' . date('d/m/Y', strtotime($transaction->date)) . '</td></tr>';
        $html .= '<tr><td><strong>Type :</strong></td><td><span class="label ' .
            (in_array($transaction->transaction_type, ['Dépôt', 'Virement entrant']) ? 'label-success' : 'label-danger') . '">' .
            htmlspecialchars($transaction->transaction_type) . '</span></td></tr>';
        $html .= '<tr><td><strong>Désignation :</strong></td><td><span class="label ' .
            ($transaction->designation == 'Crédit' ? 'label-success' : 'label-danger') . '">' .
            htmlspecialchars($transaction->designation) . '</span></td></tr>';
        $html .= '<tr><td><strong>Montant :</strong></td><td><span class="' .
            ($transaction->designation == 'Crédit' ? 'text-success' : 'text-danger') . '" style="font-size: 16px; font-weight: bold;">' .
            number_format($transaction->amount, 2, ',', ' ') . ' ' . $currency_symbol . '</span></td></tr>';
        $html .= '</table>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        // Informations complémentaires
        $html .= '<div class="col-md-6">';
        $html .= '<div class="panel panel-default">';
        $html .= '<div class="panel-heading"><strong>Informations Complémentaires</strong></div>';
        $html .= '<div class="panel-body">';
        $html .= '<table class="table table-condensed">';
        $html .= '<tr><td width="40%"><strong>Référence :</strong></td><td>' .
            ($transaction->reference ? htmlspecialchars($transaction->reference) : '<em class="text-muted">Non spécifié</em>') . '</td></tr>';
        $html .= '<tr><td><strong>Mode paiement :</strong></td><td>' .
            ($transaction->payment_mode ? htmlspecialchars($transaction->payment_mode) : '<em class="text-muted">Non spécifié</em>') . '</td></tr>';
        $html .= '<tr><td><strong>Date création :</strong></td><td>' .
            date('d/m/Y H:i:s', strtotime($transaction->created_at)) . '</td></tr>';
        $html .= '</table>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        // Informations de la banque
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12">';
        $html .= '<div class="panel panel-info">';
        $html .= '<div class="panel-heading"><strong><i class="fa fa-bank"></i> Informations Bancaires</strong></div>';
        $html .= '<div class="panel-body">';
        $html .= '<table class="table table-condensed">';
        $html .= '<tr><td width="30%"><strong>Banque :</strong></td><td>' . htmlspecialchars($transaction->bank_name) . '</td></tr>';
        $html .= '<tr><td><strong>Code banque :</strong></td><td>' . htmlspecialchars($transaction->bank_code) . '</td></tr>';
        $html .= '<tr><td><strong>N° compte :</strong></td><td>' .
            ($transaction->account_number ? htmlspecialchars($transaction->account_number) : '<em class="text-muted">Non spécifié</em>') . '</td></tr>';
        $html .= '</table>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';

        // Description
        if ($transaction->note) {
            $html .= '<div class="row">';
            $html .= '<div class="col-md-12">';
            $html .= '<div class="panel panel-default">';
            $html .= '<div class="panel-heading"><strong>Description</strong></div>';
            $html .= '<div class="panel-body">';
            $html .= nl2br(htmlspecialchars($transaction->note));
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }

        // Actions
        $html .= '<div class="row">';
        $html .= '<div class="col-md-12 text-right">';

        if ($this->rbac->hasPrivilege('depense', 'can_edit')) {
            $html .= '<a href="' . base_url() . 'admin/expense/edit_bank_transaction/' . $transaction->id . '" class="btn btn-warning">';
            $html .= '<i class="fa fa-edit"></i> Modifier</a> ';
        }

        if ($this->rbac->hasPrivilege('depense', 'can_delete')) {
            $html .= '<button class="btn btn-danger delete-transaction-btn" data-id="' . $transaction->id . '">';
            $html .= '<i class="fa fa-trash"></i> Supprimer</button>';
        }

        $html .= '</div>';
        $html .= '</div>';

        $html .= '</div>';

        // Script pour la suppression
        $html .= '<script>
    $(document).ready(function() {
        $(".delete-transaction-btn").click(function() {
            var transactionId = $(this).data("id");
            if (confirm("Êtes-vous sûr de vouloir supprimer cette transaction ? Cette action est irréversible.")) {
                $.ajax({
                    url: "' . base_url() . 'admin/expense/delete_bank_transaction_ajax",
                    type: "POST",
                    data: {
                        transaction_id: transactionId,
                        "' . $this->security->get_csrf_token_name() . '": "' . $this->security->get_csrf_hash() . '"
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.status == "success") {
                            alert(response.message);
                            $("#transactionDetailsModal").modal("hide");
                            // Rafraîchir la liste des transactions si nécessaire
                            if (typeof reloadBankTransactions === "function") {
                                reloadBankTransactions();
                            }
                        } else {
                            alert("Erreur: " + response.message);
                        }
                    }
                });
            }
        });
    });
    </script>';

        echo $html;
    }

    /**
     * Suppression d'une transaction bancaire via AJAX
     */
    public function delete_bank_transaction_ajax()
    {
        if (!$this->rbac->hasPrivilege('depense', 'can_delete')) {
            echo json_encode(['status' => 'error', 'message' => 'Accès refusé']);
            return;
        }

        $transaction_id = $this->input->post('transaction_id');

        if (!$transaction_id) {
            echo json_encode(['status' => 'error', 'message' => 'ID transaction manquant']);
            return;
        }

        // Récupérer la transaction avant suppression
        $this->db->where('id', $transaction_id);
        $transaction = $this->db->get('bank')->row();

        if (!$transaction) {
            echo json_encode(['status' => 'error', 'message' => 'Transaction non trouvée']);
            return;
        }

        // Démarrer une transaction pour assurer l'intégrité
        $this->db->trans_start();

        // Supprimer la transaction
        $this->db->where('id', $transaction_id);
        $this->db->delete('bank');

        // Mettre à jour le solde de la banque
        if ($transaction->bank_id) {
            $this->update_bank_balance($transaction->bank_id);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la suppression']);
        } else {
            echo json_encode(['status' => 'success', 'message' => 'Transaction supprimée avec succès']);
        }
    }

    /**
     * Méthode helper pour mettre à jour le solde de la banque
     * (Déjà ajoutée précédemment)
     */
    private function update_bank_balance($bank_id)
    {
        // Calculer le nouveau solde
        $this->db->select('SUM(CASE 
        WHEN designation = "Crédit" OR transaction_type IN ("Dépôt", "Virement entrant") THEN amount 
        ELSE -amount 
    END) as balance');
        $this->db->from('bank');
        $this->db->where('bank_id', $bank_id);
        $result = $this->db->get()->row();

        $new_balance = $result->balance ?? 0;

        // Mettre à jour la table banks
        $this->db->where('id', $bank_id);
        $this->db->update('banks', [
            'balance' => $new_balance,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $new_balance;
    }

    /**
     * Version corrigée de save_bank
     */

    // Dans votre contrôleur expense.php
    // Dans votre contrôleur expense.php
    public function save_bank()
    {
        $this->load->library('upload');

        $bank_id = $this->input->post('bank_id');
        $data = [
            'name' => $this->input->post('bank_name'),
            'code' => $this->input->post('bank_code'),
            'account_number' => $this->input->post('account_number'),
            'balance' => floatval($this->input->post('initial_balance') ?: 0),
            'balance_re' => floatval($this->input->post('initial_balance') ?: 0),
        ];

        // Gérer l'upload du logo
        $logo_path = $this->handle_logo_upload($bank_id);
        if ($logo_path !== false) { // Note: peut être null si pas de fichier
            $data['logo'] = $logo_path;
        }

        if ($bank_id) {
            $this->expense_model->update_bank($bank_id, $data);
            $message = 'Banque mise à jour avec succès';
        } else {
            $bank_id = $this->expense_model->add_bank($data);
            $message = 'Banque créée avec succès';
        }

        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'bank_id' => $bank_id
        ]);
    }

    private function handle_logo_upload($bank_id = null)
    {
        // Vérifier si un fichier a été uploadé
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {

            // Configuration de l'upload
            $config['upload_path'] = './uploads/bank_logos/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif|svg';
            $config['max_size'] = 2048; // 2MB
            $config['encrypt_name'] = true;
            $config['overwrite'] = false;

            // Créer le dossier s'il n'existe pas
            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
                // Ajouter un fichier index.html pour la sécurité
                file_put_contents($config['upload_path'] . 'index.html', '<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body><p>Directory access is forbidden.</p></body></html>');
            }

            $this->upload->initialize($config);

            if ($this->upload->do_upload('logo')) {
                $upload_data = $this->upload->data();

                // Supprimer l'ancien logo si on met à jour une banque existante
                if ($bank_id) {
                    $old_bank = $this->expense_model->get_bank($bank_id);
                    if (!empty($old_bank->logo) && file_exists('.' . $old_bank->logo)) {
                        unlink('.' . $old_bank->logo);
                    }
                }

                return 'uploads/bank_logos/' . $upload_data['file_name'];
            } else {
                // Log l'erreur d'upload
                error_log('Erreur upload logo: ' . $this->upload->display_errors());
                return null;
            }
        }

        return null; // Aucun fichier uploadé
    }
    // Dans expense.php
    public function change_bank_logo()
    {
        $bank_id = $this->input->post('bank_id');
        $logo_path = $this->handle_logo_upload($bank_id);

        if ($logo_path) {
            $this->expense_model->update_bank($bank_id, ['logo' => $logo_path]);
            echo json_encode(['status' => 'success', 'message' => 'Logo mis à jour']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'upload du logo']);
        }
    }

    public function refresh_bank_balance($bank_id)
    {
        // Calculer le solde à partir des transactions
        $balance = $this->expense_model->calculate_bank_balance($bank_id);

        // Mettre à jour la base de données
        $this->expense_model->update_bank($bank_id, ['balance' => $balance]);

        $formatted_balance = number_format($balance, 2, ',', ' ') . ' ' . $this->customlib->getSchoolCurrencyFormat();

        echo json_encode([
            'status' => 'success',
            'new_balance' => $balance,
            'formatted_balance' => $formatted_balance
        ]);
    }


    /**
     * Suppression d'une banque avec vérification
     */

    public function get_bank_list_ajax()
    {
        $banks = $this->expense_model->get_banks();
        $data = array();

        foreach ($banks as $bank) {
            $data[] = array(
                'id' => $bank->id,
                'name' => $bank->name,
                'code' => $bank->code,
                'balance' => $bank->balance
            );
        }

        echo json_encode(array(
            'status' => 'success',
            'data' => $data
        ));
    }
    public function getbanklist()
    {
        $m       = $this->expense_model->getbanklist();
        $m       = json_decode($m);
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();

        // var_dump($m);
        // exit;

        $dt_data = array();
        if (!empty($m->data)) {
            foreach ($m->data as $key => $value) {
                $editbtn   = '';
                $deletebtn = '';
                $documents = '' ;

                if ($this->rbac->hasPrivilege('depense', 'can_edit')) {
                    $editbtn = "<a href='".base_url()."admin/expense/edit_bank/".$value->id."'   class='btn btn-default btn-xs'  data-toggle='tooltip' data-placement='left' title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                }
                if ($this->rbac->hasPrivilege('depense', 'can_delete')) {
                    $deletebtn = "<a onclick=\"return confirm('".$this->lang->line('delete_confirm')."')\" 
                    href='".base_url()."admin/expense/delete_bank/".$value->id."' 
                    class='btn btn-default btn-xs' 
                    data-placement='left' 
                    title='".$this->lang->line('delete')."' 
                    data-toggle='tooltip'>
                    <i class='fa fa-trash'></i>
                </a>";
                }



                if($value->documents){
                    $documents="<a data-placement='left' href='".base_url()."admin/expense/download/".$value->documents."' class='btn btn-default btn-xs'  data-toggle='tooltip' title='".$this->lang->line('download')."'>
                         <i class='fa fa-download'></i> </a>" ;
                }
                $row       = array();
                // $row[]     = $value->exp_category;
                $row[]     = $value->name;



                /* $row[]     = $value->invoice_no; */
                $row[]     = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value->date));

                //$row[]     = $value->income_name;
                $row[]     = $value->amount .$currency_symbol;
                $row[]     = $value->transaction_type;
                $row[]     = $value->category;

                if ($value->note == "") {
                    $row[]     = $this->lang->line('no_description');
                }else{
                    $row[]     = $value->note;
                }

                $row[]     = $documents.' ' .$editbtn . ' ' . $deletebtn;
                $dt_data[] = $row;

                //Ajouter sous total
            }
        }

        $json_data = array(
            "draw"            => intval($m->draw),
            "recordsTotal"    => intval($m->recordsTotal),
            "recordsFiltered" => intval($m->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);
    }
    public function get_bank_transactions()
    {
        $bank_id = $this->input->post('bank_id');

        if (!$bank_id) {
            echo '<div class="alert alert-danger">ID banque manquant</div>';
            return;
        }

        // Récupérer le nom de la banque
        $bank = $this->db->get_where('banks', ['id' => $bank_id])->row();
        $bank_name = $bank ? $bank->name : 'Inconnue';

        // Récupérer les transactions
        $this->db->select('*');
        $this->db->from('bank');
        $this->db->where('bank_id', $bank_id);
        $this->db->order_by('date', 'DESC');
        $this->db->order_by('created_at', 'DESC');
        $transactions = $this->db->get()->result();

        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();

        // Calculer le solde total
        $total_balance = 0;
        foreach ($transactions as $trans) {
            if ($trans->designation == 'Crédit' ||
                $trans->transaction_type == 'Dépôt' ||
                $trans->transaction_type == 'Virement entrant') {
                $total_balance += floatval($trans->amount);
            } else {
                $total_balance -= floatval($trans->amount);
            }
        }

        // Générer le HTML
        echo '<div class="transaction-container" id="transactionContainer">';

        // En-tête
        echo '<div class="transaction-header mb-4">';
        echo '<h4><i class="fa fa-university"></i> Transactions de la banque: ' . htmlspecialchars($bank_name) . '</h4>';
        echo '<div class="transaction-stats d-flex gap-3 mt-2">';
        echo '<span style="background-color: deepskyblue; color: white; padding: 5px 10px; border-radius: 5px;" id="transactionCount">Total: ' . count($transactions) . ' transaction(s)</span>';

        echo '<span style="background-color: red; color: white; padding: 5px 10px; border-radius: 5px;">';
        echo 'Solde: ' . number_format($total_balance, 2, ',', ' ') . ' ' . $currency_symbol;
        echo '</span>';  echo '</span>';
        echo '<button id="resetFiltersBtn" class="btn btn-sm btn-outline-secondary ml-2" style="display: none;">';
        echo '<i class="fa fa-refresh"></i> Réinitialiser filtres';
        echo '</button>';
        echo '</div>';
        echo '</div>';

        if (empty($transactions)) {
            echo '<div class="alert alert-info">';
            echo '<i class="fa fa-info-circle"></i> Aucune transaction trouvée pour cette banque.';
            echo '</div>';
        } else {
            // Récupérer les types uniques pour le filtre
            $unique_types = [];
            foreach ($transactions as $trans) {
                if ($trans->transaction_type && !in_array($trans->transaction_type, $unique_types)) {
                    $unique_types[] = $trans->transaction_type;
                }
            }

            echo '<div class="table-responsive">';
            echo '<table class="table table-hover table-bordered" id="transactionsTable">';
            echo '<thead class="thead-dark">';

            // Première ligne : titres des colonnes
            echo '<tr>';
            echo '<th width="120">Date</th>';
            echo '<th width="150">Nom</th>';
            echo '<th width="200">Libellé</th>';
            echo '<th width="120">Type</th>';
            echo '<th width="100">Désignation</th>';
            echo '<th width="120">Montant</th>';
            echo '<th width="120">Référence</th>';
            echo '<th width="100">Actions</th>';
            echo '</tr>';

            // Deuxième ligne : filtres
            echo '<tr class="table-filter-header">';
            echo '<td><input type="text" class="filter-input" data-column="0" placeholder="Date..." style="width: 100%; padding: 5px; font-size: 12px;"></td>';
            echo '<td><input type="text" class="filter-input" data-column="1" placeholder="Filtrer par nom..." style="width: 100%; padding: 5px; font-size: 12px;"></td>';
            echo '<td><input type="text" class="filter-input" data-column="2" placeholder="Filtrer par libellé..." style="width: 100%; padding: 5px; font-size: 12px;"></td>';
            echo '<td>';
            echo '<select class="filter-input" data-column="3" style="width: 100%; padding: 5px; font-size: 12px;">';
            echo '<option value="">Tous les types</option>';
            foreach ($unique_types as $type) {
                echo '<option value="' . htmlspecialchars($type) . '">' . htmlspecialchars($type) . '</option>';
            }
            echo '</select>';
            echo '</td>';
            echo '<td>';
            echo '<select class="filter-input" data-column="4" style="width: 100%; padding: 5px; font-size: 12px;">';
            echo '<option value="">Toutes</option>';
            echo '<option value="Crédit">Crédit</option>';
            echo '<option value="Débit">Débit</option>';
            echo '</select>';
            echo '</td>';
            echo '<td><input type="text" class="filter-input" data-column="5" placeholder="Montant..." style="width: 100%; padding: 5px; font-size: 12px;"></td>';
            echo '<td><input type="text" class="filter-input" data-column="6" placeholder="Filtrer référence..." style="width: 100%; padding: 5px; font-size: 12px;"></td>';
            echo '<td></td>';
            echo '</tr>';

            echo '</thead>';
            echo '<tbody id="transactionsBody">';

            foreach ($transactions as $transaction) {
                $is_credit = ($transaction->designation == 'Crédit' ||
                    $transaction->transaction_type == 'Dépôt' ||
                    $transaction->transaction_type == 'Virement entrant');

                echo '<tr class="transaction-item" data-transaction-id="' . $transaction->id . '">';

                // Date
                echo '<td class="transaction-date">';
                echo date('d/m/Y', strtotime($transaction->date));
                echo '</td>';

                // Nom
                echo '<td class="transaction-name">';
                echo htmlspecialchars($transaction->nom);
                echo '</td>';

                // Libellé
                echo '<td class="transaction-description">';
                echo htmlspecialchars($transaction->name);
                echo '</td>';

                // Type
                echo '<td class="transaction-type">';
                echo htmlspecialchars($transaction->transaction_type);
                echo '</td>';

                // Désignation
                echo '<td class="transaction-designation">';
                echo '<span class="badge ' . ($is_credit ? 'badge-success' : 'badge-danger') . '">';
                echo htmlspecialchars($transaction->designation);
                echo '</span>';
                echo '</td>';

                // Montant
                $amount_display = ($is_credit ? '+' : '-') . number_format($transaction->amount, 2, ',', ' ') . ' ' . $currency_symbol;
                echo '<td class="transaction-amount ' . ($is_credit ? 'text-success' : 'text-danger') . '">';
                echo $amount_display;
                echo '</td>';

                // Référence
                echo '<td class="transaction-reference">';
                echo htmlspecialchars($transaction->reference ?: '-');
                echo '</td>';

                // Actions
                echo '<td class="transaction-actions">';

                // Bouton Éditer
                if ($this->rbac->hasPrivilege('depense', 'can_edit')) {
                    echo '<button class="btn btn-sm btn-warning edit-transaction" ';
                    echo 'data-id="' . $transaction->id . '" ';
                    echo 'title="Modifier">';
                    echo '<i class="fa fa-edit"></i>';
                    echo '</button> ';
                }

                // Bouton Supprimer
                if ($this->rbac->hasPrivilege('depense', 'can_delete')) {
                    echo '<button class="btn btn-sm btn-danger delete-transaction" ';
                    echo 'data-id="' . $transaction->id . '" ';
                    echo 'title="Supprimer">';
                    echo '<i class="fa fa-trash"></i>';
                    echo '</button>';
                }

                echo '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        }

        echo '</div>';

        // CSS seulement
        echo '<style>
    .transaction-container {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .transaction-header {
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 15px;
    }
    .transaction-stats {
        font-size: 14px;
    }
    .transaction-item:hover {
        background-color: #f8fafc;
        transition: background-color 0.2s;
    }
    .transaction-actions {
        white-space: nowrap;
    }
    .transaction-actions .btn {
        margin: 2px;
        padding: 4px 8px;
        font-size: 12px;
    }
    .transaction-amount {
        font-weight: bold;
        font-size: 14px;
    }
    .table-filter-header {
        background-color: #f8f9fa;
    }
    .table-filter-header td {
        padding: 5px 8px !important;
        border-top: 2px solid #dee2e6;
        vertical-align: middle;
    }
    .filter-input {
        border: 1px solid #ced4da;
        border-radius: 4px;
        transition: all 0.15s ease-in-out;
        font-size: 12px;
        padding: 5px 8px;
        width: 100%;
        box-sizing: border-box;
    }
    .filter-input:focus {
        border-color: #4dabf7;
        outline: 0;
        box-shadow: 0 0 0 0.2rem rgba(77, 171, 247, 0.25);
    }
    select.filter-input {
        height: 32px;
    }
    </style>';
    }


    public function update_transaction()
    {
        if (!$this->rbac->hasPrivilege('depense', 'can_edit')) {
            echo json_encode(['status' => 'error', 'message' => 'Accès refusé']);
            return;
        }

        $transaction_id = $this->input->post('transaction_id');

        if (!$transaction_id) {
            echo json_encode(['status' => 'error', 'message' => 'ID transaction manquant']);
            return;
        }

        // Récupérer l'ancienne transaction pour calculer la différence
        $old_transaction = $this->db->get_where('bank', ['id' => $transaction_id])->row();
        $old_bank_id = $old_transaction->bank_id;

        // Préparer les données de mise à jour
        $update_data = [
            'name' => $this->input->post('name'),
            'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
            'amount' => floatval($this->input->post('amount')),
            'transaction_type' => $this->input->post('transaction_type'),
            'designation' => $this->input->post('designation'),
            'reference' => $this->input->post('reference'),
            'payment_mode' => $this->input->post('payment_mode'),
            'note' => $this->input->post('description'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Si la banque a changé
        $new_bank_id = $this->input->post('bank_id');
        if ($new_bank_id && $new_bank_id != $old_bank_id) {
            $update_data['bank_id'] = $new_bank_id;
        }

        // Démarrer la transaction
        $this->db->trans_start();

        // Mettre à jour la transaction
        $this->db->where('id', $transaction_id);
        $this->db->update('bank', $update_data);

        // Mettre à jour les soldes des banques
        if ($old_bank_id) {
            $this->update_bank_balance($old_bank_id);
        }
        if ($new_bank_id && $new_bank_id != $old_bank_id) {
            $this->update_bank_balance($new_bank_id);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la mise à jour']);
        } else {
            echo json_encode(['status' => 'success', 'message' => 'Transaction mise à jour avec succès']);
        }
    }


    public function delete_bank()
    {
        if (!$this->rbac->hasPrivilege('depense', 'can_delete')) {
            echo json_encode(['status' => 'error', 'message' => 'Accès refusé']);
            return;
        }

        $bank_id = $this->input->post('bank_id');

        if (!$bank_id) {
            echo json_encode(['status' => 'error', 'message' => 'ID banque manquant']);
            return;
        }

        // Vérifier s'il y a des transactions
        $this->db->where('bank_id', $bank_id);
        $transaction_count = $this->db->count_all_results('bank');

        if ($transaction_count > 0) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Impossible de supprimer : cette banque a ' . $transaction_count . ' transaction(s) associée(s)'
            ]);
            return;
        }

        // Supprimer la banque
        $this->db->where('id', $bank_id);
        $deleted = $this->db->delete('banks');

        if ($deleted) {
            echo json_encode(['status' => 'success', 'message' => 'Banque supprimée avec succès']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la suppression']);
        }
    }

    public function delete_transaction()
    {
        if (!$this->rbac->hasPrivilege('depense', 'can_delete')) {
            echo json_encode(['status' => 'error', 'message' => 'Accès refusé']);
            return;
        }

        $transaction_id = $this->input->post('transaction_id');

        if (!$transaction_id) {
            echo json_encode(['status' => 'error', 'message' => 'ID transaction manquant']);
            return;
        }

        // Récupérer la transaction avant suppression
        $this->db->where('id', $transaction_id);
        $transaction = $this->db->get('bank')->row();

        if (!$transaction) {
            echo json_encode(['status' => 'error', 'message' => 'Transaction non trouvée']);
            return;
        }

        $bank_id = $transaction->bank_id;

        // Démarrer une transaction pour assurer l'intégrité
        $this->db->trans_start();

        // Supprimer la transaction
        $this->db->where('id', $transaction_id);
        $this->db->delete('bank');

        // Mettre à jour le solde de la banque
        if ($bank_id) {
            $this->update_bank_balance($bank_id);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la suppression']);
        } else {
            echo json_encode(['status' => 'success', 'message' => 'Transaction supprimée avec succès']);
        }
    }


    public function download($documents)
    {
        $this->load->helper('download');
        $filepath = "./uploads/school_expense/" . $this->uri->segment(6);
        $data     = file_get_contents($filepath);
        $name     = $this->uri->segment(6);
        force_download($name, $data);
    }

    public function handle_upload()
    {

        $image_validate = $this->config->item('file_validate');
        $result = $this->filetype_model->get();
        if (isset($_FILES["documents"]) && !empty($_FILES['documents']['name'])) {
            $file_type         = $_FILES["documents"]['type'];
            $file_size         = $_FILES["documents"]["size"];
            $file_name         = $_FILES["documents"]["name"];
            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->file_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->file_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($files = filesize($_FILES['documents']['tmp_name'])) {

                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', 'File Type Not Allowed');
                    return false;
                }

                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', 'Extension Not Allowed');
                    return false;
                }
                if ($file_size > $result->file_size) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', "File Type / Extension Error Uploading  Image");
                return false;
            }

            return true;
        }
        return true;
    }

    public function view($id)
    {
        if (!$this->rbac->hasPrivilege('depense', 'can_view')) {
            access_denied();
        }
        $data['title']   = 'Fees Master List';
        $expense         = $this->expense_model->get($id);
        $data['expense'] = $expense;
        $this->load->view('layout/header', $data);
        $this->load->view('expense/expenseShow', $data);
        $this->load->view('layout/footer', $data);
    }

    // ... reste des méthodes existantes (handle_upload, view, getByFeecategory, etc.) ...
}