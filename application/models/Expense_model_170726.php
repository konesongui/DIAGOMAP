<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Expense_model extends MY_Model
{
    protected $ma_table = 'expenses';

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    /**
     * This funtion takes id as a parameter and will fetch the record.
     * If id is not provided, then it will fetch all the records form the table.
     * @param int $id
     * @return mixed
     */
    public function search($text = null, $start_date = null, $end_date = null)
    {
        if (!empty($text)) {
            $this->datatables
                ->select('expenses.id,expenses.date,expenses.invoice_no,expenses.name,expenses.user,expenses.amount,expenses.documents,expenses.note')
                ->searchable('expenses.name,expenses.user,expenses.invoice_no,expenses.date,expenses.amount')
                ->orderable('expenses.name,expenses.user,expenses.invoice_no,expenses.date,expenses.amount')
                // ❌ Supprimé les join
                // ->join('expense_head', 'expenses.exp_head_id = expense_head.id')
                // ->join('income', 'expenses.inc_head_id = income.id')
                ->like('expenses.name', $text);

            $this->db->where('expenses.deleted', 1)
                ->from('expenses');
        } else {
            $this->datatables
                ->select('expenses.id,expenses.date,expenses.invoice_no,expenses.name,expenses.user,expenses.amount,expenses.documents,expenses.note')
                ->searchable('expenses.name,expenses.user,expenses.invoice_no,expenses.date,expenses.amount')
                ->orderable('expenses.name,expenses.user,expenses.invoice_no,expenses.date,expenses.amount')
                // ❌ Supprimé les join
                // ->join('expense_head', 'expenses.exp_head_id = expense_head.id')
                // ->join('income', 'expenses.inc_head_id = income.id')
                ->where('expenses.date <=', $end_date)
                ->where('expenses.date >=', $start_date);

            $this->db->where('expenses.deleted', 1)
                ->from('expenses');
        }

        return $this->datatables->generate('json');
    }


    public function search_old($text = null, $start_date = null, $end_date = null)

    {
        if (!empty($text)) {

            $this->datatables
                ->select('expenses.id,expenses.date,expenses.invoice_no,expenses.name,expenses.user, income.name as income_name,income.amount_re as income_amount_re,income.amount as income_amount expenses.amount,expenses.documents,expenses.note,expense_head.exp_category,expenses.exp_head_id')
                ->searchable('expenses.name,expenses.user,expenses.invoice_no,exp_category,date,expenses.amount')
                ->orderable('expenses.name,expenses.user,expenses.invoice_no,exp_category,date,expenses.amount')
                ->join('expense_head', 'expenses.exp_head_id = expense_head.id')
                ->join('income', 'expenses.inc_head_id = income.id')
                ->like('expenses.name', $text);
                 $this->db->where('expenses.deleted', 1)
                ->from('expenses');

        } else {

            $this->datatables
                ->select('expenses.id,expenses.date,expenses.invoice_no,expenses.name,expenses.user, income.name as income_name, income.amount_re as income_amount_re,income.amount as income_amount, expenses.amount,expenses.documents,expenses.note,expense_head.exp_category,expenses.exp_head_id')
                ->searchable('expenses.name,expenses.user,expenses.invoice_no,exp_category,date,expenses.amount')
                ->orderable('expenses.name,expenses.user,expenses.invoice_no,exp_category,date,expenses.amount')
                ->join('expense_head', 'expenses.exp_head_id = expense_head.id')
                ->join('income', 'expenses.inc_head_id = income.id')
                ->where('expenses.date <=', $end_date)
                ->where('expenses.date >=', $start_date);
                 $this->db->where('expenses.deleted', 1)
                ->from('expenses');
        }
        return $this->datatables->generate('json');

    }

    public function getbank($id = null)
    {
        $this->db->select('bank.id,bank.date,bank.name,bank.user,bank.invoice_no,bank.amount,bank.documents,bank.note');
        $this->db->where('bank.deleted', 1);
        $this->db->from('bank');

        // ❌ Supprimé : jointure avec expense_head
        // $this->db->join('expense_head', 'expenses.exp_head_id = expense_head.id');

        if ($id != null) {
            $this->db->where('bank.id', $id);
        } else {
            $this->db->order_by('bank.id', 'DESC');
        }

        $query = $this->db->get();

        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }


    public function get($id = null)
    {
        $this->db->select('expenses.id,expenses.date,expenses.name,expenses.user,expenses.invoice_no,expenses.amount,expenses.documents,expenses.note');
        $this->db->where('expenses.deleted', 1);
        $this->db->from('expenses');

        // ❌ Supprimé : jointure avec expense_head
        // $this->db->join('expense_head', 'expenses.exp_head_id = expense_head.id');

        if ($id != null) {
            $this->db->where('expenses.id', $id);
        } else {
            $this->db->order_by('expenses.id', 'DESC');
        }

        $query = $this->db->get();

        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }


    public function get_old($id = null)
    {
        $this->db->select('expenses.id,expenses.date,expenses.name,expenses.user,expenses.invoice_no,expenses.amount,expenses.documents,expenses.note,expense_head.exp_category,expenses.exp_head_id');
             $this->db->where('expenses.deleted', 1)
            ->from('expenses');
        $this->db->join('expense_head', 'expenses.exp_head_id = expense_head.id');
        if ($id != null) {
            $this->db->where('expenses.id', $id);
        } else {
            $this->db->order_by('expenses.id', 'DESC');
        }


        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }


    public function getexpenselist_old($id = null)
    {
        $this->datatables
            ->select('expenses.id,expenses.date,expenses.name,expenses.user, income.name as income_name,income.est_actif as income_est_actif,income.amount as income_amount, income.amount_re as income_amount_re, expenses.invoice_no,expenses.user,expenses.amount,expenses.documents,expenses.note,expense_head.exp_category,expenses.exp_head_id')
            ->searchable('expenses.id,expenses.date,expenses.name,expenses.user,expenses.invoice_no,expenses.amount,expenses.documents,expenses.note,expense_head.exp_category,expenses.exp_head_id')
            ->orderable('expenses.name,expenses.user,expenses.note,expenses.invoice_no,expenses.date,expense_head.exp_category,expenses.amount')
            ->join("expense_head", "expenses.exp_head_id = expense_head.id")
            ->join("income", "expenses.inc_head_id = income.id")
            ->sort('expenses.id', 'desc');
        $this->db->where('expenses.deleted', 1)

            ->from('expenses');
        return $this->datatables->generate('json');
    }

    public function getexpenselist($id = null)
    {
        $this->datatables
            ->select('expenses.id, expenses.date, expenses.name, expenses.user, expenses.invoice_no, expenses.amount,expenses.category, expenses.documents, expenses.note')
            ->searchable('expenses.id, expenses.date, expenses.name, expenses.user, expenses.invoice_no, expenses.amount,expenses.category, expenses.documents, expenses.note')
            ->orderable('expenses.name, expenses.user, expenses.note, expenses.invoice_no, expenses.date, expenses.amount')
            // ❌ Suppression des jointures non nécessaires
            // ->join("expense_head", "expenses.exp_head_id = expense_head.id")
            // ->join("income", "expenses.inc_head_id = income.id")
            ->sort('expenses.id', 'desc');

        $this->db->where('expenses.deleted', 1)
            ->from('expenses');

        return $this->datatables->generate('json');
    }

    public function getbanklist($id = null)
    {
        $this->datatables
            ->select('bank.id, bank.date, bank.name, bank.user,bank.nom, bank.transaction_type, bank.invoice_no, bank.amount,bank.category, bank.documents, bank.note')
            ->searchable('bank.id, bank.date, bank.name,bank.nom, bank.user, bank.transaction_type, bank.invoice_no, bank.amount,bank.category, bank.documents, bank.note')
            ->orderable('bank.name, bank.user, bank.note,bank.nom, bank.transaction_type, bank.invoice_no, bank.date, bank.amount')
            // ❌ Suppression des jointures non nécessaires
            // ->join("expense_head", "expenses.exp_head_id = expense_head.id")
            // ->join("income", "expenses.inc_head_id = income.id")
            ->sort('bank.id', 'desc');

        $this->db->where('bank.deleted', 1)
            ->from('bank');

        return $this->datatables->generate('json');
    }





    /**
     * This function will delete the record based on the id
     * @param $id
     */
    public function remove($id)
    {

        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id)
            ->set('deleted', '0');
        $this->db->update('expenses');
        /* $this->db->delete('expenses');*/

        $message   = DELETE_RECORD_CONSTANT . " On  expenses   id " . $id;
        $action    = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);

        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {

            return $return_value;
        }
    }

    public function remove_bank($id)
    {

        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id)
            ->set('deleted', '0');
        $this->db->update('bank');
        /* $this->db->delete('expenses');*/

        $message   = DELETE_RECORD_CONSTANT . " On  expenses   id " . $id;
        $action    = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);

        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {

            return $return_value;
        }
    }

    /**
     * This function will take the post data passed from the controller
     * If id is present, then it will do an update
     * else an insert. One function doing both add and edit.
     * @param $data
     */
    public function add($data)
    {

        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================

        if (isset($data['id']) && $data['id'] != '') {

            $this->db->where('id', $data['id']);
            $this->db->update('expenses', $data);

            $message   = UPDATE_RECORD_CONSTANT . " On  expenses   id " . $data['id'];
            $action    = "Update";
            $record_id = $data['id'];
        } else {
            $this->db->insert('expenses', $data);

            $record_id = $this->db->insert_id();
            $message   = INSERT_RECORD_CONSTANT . " On  expenses   id " . $record_id;
            $action    = "Insert";
        }

        $this->log($message, $record_id, $action);

        //======================Code End==============================

        $this->db->trans_complete(); # Completing transaction
        /* Optional */

        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {

            return $record_id;
        }
    }

    public function addbank($data)
    {
        // Récupérer l'entreprise de l'utilisateur connecté
        $userdata = $this->customlib->getUserData();
        $entreprise_id = $userdata['entreprise_id'] ?? 0;

        if ($entreprise_id == 0) {
            $entreprise_id = $this->session->userdata('entreprise_id') ?? 0;
        }

        // Prépare les données pour l'insertion
        $bank_data = [
            'bank_id' => isset($data['bank_id']) ? $data['bank_id'] : null,
            'name' => isset($data['name']) ? $data['name'] : null,
            'nom' => isset($data['nom']) ? $data['nom'] : null,
            'date' => isset($data['date']) ? $data['date'] : date('Y-m-d'),
            'amount' => isset($data['amount']) ? floatval($data['amount']) : 0.00,
            'transaction_type' => isset($data['transaction_type']) ? $data['transaction_type'] : null,
            'designation' => isset($data['designation']) ? $data['designation'] : null,
            'reference' => isset($data['reference']) ? $data['reference'] : null,
            'payment_mode' => isset($data['payment_mode']) ? $data['payment_mode'] : null,
            'note' => isset($data['note']) ? $data['note'] : null,
            'created_at' => date('Y-m-d H:i:s'),
            'entreprise_id' => $entreprise_id
        ];

        // Insère dans la table bank
        $this->db->insert('bank', $bank_data);
        $insert_id = $this->db->insert_id();

        // Met à jour le solde de la banque
        if (isset($data['bank_id'])) {
            $this->update_bank_balance($data['bank_id']);
        }

        // Retourner une réponse JSON pour AJAX
        if ($this->input->is_ajax_request()) {
            if ($insert_id) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Opération bancaire enregistrée avec succès !',
                    'insert_id' => $insert_id
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de l\'enregistrement de l\'opération bancaire.'
                ]);
            }
            exit;
        }

        return $insert_id;
    }   

    public function addbank17($data)
    {
        // Prépare les données pour l'insertion
        $bank_data = [
            'bank_id' => isset($data['bank_id']) ? $data['bank_id'] : null,
            'name' => isset($data['name']) ? $data['name'] : null,
            'nom' => isset($data['nom']) ? $data['nom'] : null,
            'date' => isset($data['date']) ? $data['date'] : date('Y-m-d'),
            'amount' => isset($data['amount']) ? floatval($data['amount']) : 0.00,
            'transaction_type' => isset($data['transaction_type']) ? $data['transaction_type'] : null,
            'designation' => isset($data['designation']) ? $data['designation'] : null,
            'reference' => isset($data['reference']) ? $data['reference'] : null,
            'payment_mode' => isset($data['payment_mode']) ? $data['payment_mode'] : null,
            'note' => isset($data['note']) ? $data['note'] : null,
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Insère dans la table bank
        $this->db->insert('bank', $bank_data);
        $insert_id = $this->db->insert_id();

        // Met à jour le solde de la banque
        if (isset($data['bank_id'])) {
            $this->update_bank_balance($data['bank_id']);
        }

        return $insert_id;
    }

    public function check_Exits_group($data)
    {
        $this->db->select('*');
        $this->db->from('expenses');
        $this->db->where('session_id', $this->current_session);
        $this->db->where('feetype_id', $data['feetype_id']);
        $this->db->where('class_id', $data['class_id']);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return false;
        } else {
            return true;
        }
    }

    public function getTypeByFeecategory($type, $class_id)
    {
        $this->db->select('expenses.id,expenses.session_id,expenses.invoice_no,expenses.amount,expenses.documents,expenses.note,expense_head.class,feetype.type')->from('expenses');
        $this->db->join('expense_head', 'expenses.class_id = expense_head.id');
        $this->db->join('feetype', 'expenses.feetype_id = feetype.id');
        $this->db->where('expenses.class_id', $class_id);
        $this->db->where('expenses.feetype_id', $type);
        $this->db->where('expenses.session_id', $this->current_session);
        $this->db->order_by('expenses.id');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getTotalExpenseBydate($date)
    {
        $query = 'SELECT sum(amount) as `amount` FROM `expenses` where date=' . $this->db->escape($date);
        $query = $this->db->query($query);
        return $query->row();
    }

    public function getTotalExpenseBwdate($date_from, $date_to)
    {
        $query = 'SELECT sum(amount) as `amount` FROM `expenses` where date between ' . $this->db->escape($date_from) . ' and ' . $this->db->escape($date_to);

        $query = $this->db->query($query);
        return $query->row();
    }

    public function getExpenseHeadData($start_date, $end_date)
    {
        $condition = "date_format(date,'%Y-%m-%d') between '" . $start_date . "' and '" . $end_date . "'";

        $recorddata = $this->db->select('sum(amount) as total,exp_category')->from('expenses');
        $this->db->join('expense_head', 'expenses.exp_head_id = expense_head.id');
        $this->db->where($condition)->group_by('expense_head.id');
        $r = $this->db->get()->result_array();
        return $r;
    }


// Ajoutez ces méthodes au modèle

    public function get_banks_17()
    {
        $this->db->order_by('name', 'asc');
        return $this->db->get('banks')->result();
    }

    public function add_bank_17($data)
    {
        $this->db->insert('banks', $data);
        return $this->db->insert_id();
    }

    public function get_banks()
    {
        // Récupérer l'entreprise de l'utilisateur connecté
        $userdata = $this->customlib->getUserData();
        $entreprise_id = $userdata['entreprise_id'] ?? 0;

        // Si l'entreprise_id est 0, essayer de le récupérer depuis la session
        if ($entreprise_id == 0) {
            $entreprise_id = $this->session->userdata('entreprise_id') ?? 0;
        }

        $this->db->order_by('name', 'asc');

        // Filtrer par entreprise_id si disponible
        if ($entreprise_id > 0) {
            $this->db->where('banks.entreprise_id', $entreprise_id);
        }

        return $this->db->get('banks')->result();
    }

    public function add_bank($data)
    {
        // Démarrer la transaction
        $this->db->trans_start();

        // Récupérer l'entreprise de l'utilisateur connecté
        $userdata = $this->customlib->getUserData();
        $entreprise_id = $userdata['entreprise_id'] ?? 0;

        // Si l'entreprise_id est 0, essayer de le récupérer depuis la session
        if ($entreprise_id == 0) {
            $entreprise_id = $this->session->userdata('entreprise_id') ?? 0;
        }

        // Ajouter l'entreprise_id aux données si elle n'existe pas déjà
        if ($entreprise_id > 0 && !isset($data['entreprise_id'])) {
            $data['entreprise_id'] = $entreprise_id;
        }

        // Insertion
        $this->db->insert('banks', $data);
        $insert_id = $this->db->insert_id();

        // Journalisation
        $message = INSERT_RECORD_CONSTANT . " On bank id " . $insert_id;
        $action = "Insert";
        $record_id = $insert_id;
        $this->log($message, $record_id, $action);

        // Compléter la transaction
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return $insert_id;
        }
    }

    public function update_bank($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('banks', $data);
    }

    public function delete_bank($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('banks');
    }

    public function bank_has_transactions($bank_id)
    {
        $this->db->where('bank_id', $bank_id);
        $this->db->limit(1);
        $query = $this->db->get('bank_transactions');
        return $query->num_rows() > 0;
    }
    public function get_bank_transaction($id)
    {
        $this->db->select('bank.*, banks.name as bank_name, banks.code as bank_code');
        $this->db->from('bank');
        $this->db->join('banks', 'banks.id = bank.bank_id', 'left');
        $this->db->where('bank.id', $id);
        return $this->db->get()->row();
    }
    public function delete_bank_transaction($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('expenses');
    }

    // Dans expense_model.php
    public function get_bank($id)
    {
        return $this->db->where('id', $id)->get('banks')->row();
    }




    // Ajoute cette méthode pour mettre à jour le solde
    public function update_bank_balance($bank_id)
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

    public function getTotalExpense(){
        $query = 'SELECT sum(amount) as `amount` FROM `expenses`';
        $query = $this->db->query($query);
        return $query->row();
    }

    // Dans Expense_model.php

    /**
     * Récupère une transaction bancaire par ID
     */
    public function get_bank_transaction_by_id($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('bank');
        return $query->row();
    }

    /**
     * Met à jour une transaction bancaire
     */
    public function update_bank_transaction($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('bank', $data);
        return $this->db->affected_rows();
    }

    /**
     * Recalcule le solde d'une banque
     */
    public function recalculate_bank_balance($bank_id)
    {
        if (!$bank_id) return 0;

        // Récupérer le solde initial
        $bank = $this->db->get_where('banks', ['id' => $bank_id])->row();
        if (!$bank) return 0;

        $initial_balance = floatval($bank->balance_re ?? 0);

        // Calculer le total des transactions
        $this->db->select("
        SUM(CASE 
            WHEN designation = 'Crédit' THEN amount
            WHEN designation = 'Débit' THEN -amount
            ELSE 0
        END) as transaction_total
    ");
        $this->db->from('bank');
        $this->db->where('bank_id', $bank_id);
        $result = $this->db->get()->row();

        $transaction_total = floatval($result->transaction_total ?? 0);

        // Nouveau solde
        $new_balance = $initial_balance + $transaction_total;

        // Mettre à jour
        $this->db->where('id', $bank_id);
        $this->db->update('banks', [
            'balance' => $new_balance,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $new_balance;
    }

}
