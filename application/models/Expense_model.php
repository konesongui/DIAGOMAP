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
     * Récupère l'entreprise_id de l'utilisateur connecté
     * @return int
     */
    private function get_entreprise_id()
    {
        $userdata = $this->customlib->getUserData();
        $entreprise_id = $userdata['entreprise_id'] ?? 0;

        if ($entreprise_id == 0) {
            $entreprise_id = $this->session->userdata('entreprise_id') ?? 0;
        }

        return $entreprise_id;
    }

    /**
     * This funtion takes id as a parameter and will fetch the record.
     * If id is not provided, then it will fetch all the records form the table.
     * @param int $id
     * @return mixed
     */
    public function search($text = null, $start_date = null, $end_date = null)
    {
        $entreprise_id = $this->get_entreprise_id();

        if (!empty($text)) {
            $this->datatables
                ->select('expenses.id,expenses.date,expenses.invoice_no,expenses.name,expenses.user,expenses.amount,expenses.documents,expenses.note')
                ->searchable('expenses.name,expenses.user,expenses.invoice_no,expenses.date,expenses.amount')
                ->orderable('expenses.name,expenses.user,expenses.invoice_no,expenses.date,expenses.amount')
                ->like('expenses.name', $text);

            if ($entreprise_id > 0) {
                $this->datatables->where('expenses.entreprise_id', $entreprise_id);
            }

            $this->db->where('expenses.deleted', 1)
                ->from('expenses');
        } else {
            $this->datatables
                ->select('expenses.id,expenses.date,expenses.invoice_no,expenses.name,expenses.user,expenses.amount,expenses.documents,expenses.note')
                ->searchable('expenses.name,expenses.user,expenses.invoice_no,expenses.date,expenses.amount')
                ->orderable('expenses.name,expenses.user,expenses.invoice_no,expenses.date,expenses.amount')
                ->where('expenses.date <=', $end_date)
                ->where('expenses.date >=', $start_date);

            if ($entreprise_id > 0) {
                $this->datatables->where('expenses.entreprise_id', $entreprise_id);
            }

            $this->db->where('expenses.deleted', 1)
                ->from('expenses');
        }

        return $this->datatables->generate('json');
    }

    public function search_old($text = null, $start_date = null, $end_date = null)
    {
        $entreprise_id = $this->get_entreprise_id();

        if (!empty($text)) {
            $this->datatables
                ->select('expenses.id,expenses.date,expenses.invoice_no,expenses.name,expenses.user, income.name as income_name,income.amount_re as income_amount_re,income.amount as income_amount expenses.amount,expenses.documents,expenses.note,expense_head.exp_category,expenses.exp_head_id')
                ->searchable('expenses.name,expenses.user,expenses.invoice_no,exp_category,date,expenses.amount')
                ->orderable('expenses.name,expenses.user,expenses.invoice_no,exp_category,date,expenses.amount')
                ->join('expense_head', 'expenses.exp_head_id = expense_head.id')
                ->join('income', 'expenses.inc_head_id = income.id')
                ->like('expenses.name', $text);

            if ($entreprise_id > 0) {
                $this->db->where('expenses.entreprise_id', $entreprise_id);
            }

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

            if ($entreprise_id > 0) {
                $this->db->where('expenses.entreprise_id', $entreprise_id);
            }

            $this->db->where('expenses.deleted', 1)
                ->from('expenses');
        }
        return $this->datatables->generate('json');
    }

    public function getbank($id = null)
    {
        $entreprise_id = $this->get_entreprise_id();

        $this->db->select('bank.id,bank.date,bank.name,bank.user,bank.invoice_no,bank.amount,bank.documents,bank.note');
        $this->db->where('bank.deleted', 1);
        $this->db->from('bank');

        if ($entreprise_id > 0) {
            $this->db->where('bank.entreprise_id', $entreprise_id);
        }

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
        $entreprise_id = $this->get_entreprise_id();

        $this->db->select('expenses.id,expenses.date,expenses.name,expenses.user,expenses.invoice_no,expenses.amount,expenses.documents,expenses.note');
        $this->db->where('expenses.deleted', 1);
        $this->db->from('expenses');

        if ($entreprise_id > 0) {
            $this->db->where('expenses.entreprise_id', $entreprise_id);
        }

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
        $entreprise_id = $this->get_entreprise_id();

        $this->db->select('expenses.id,expenses.date,expenses.name,expenses.user,expenses.invoice_no,expenses.amount,expenses.documents,expenses.note,expense_head.exp_category,expenses.exp_head_id');
        $this->db->where('expenses.deleted', 1)
            ->from('expenses');
        $this->db->join('expense_head', 'expenses.exp_head_id = expense_head.id');

        if ($entreprise_id > 0) {
            $this->db->where('expenses.entreprise_id', $entreprise_id);
        }

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
        $entreprise_id = $this->get_entreprise_id();

        $this->datatables
            ->select('expenses.id,expenses.date,expenses.name,expenses.user, income.name as income_name,income.est_actif as income_est_actif,income.amount as income_amount, income.amount_re as income_amount_re, expenses.invoice_no,expenses.user,expenses.amount,expenses.documents,expenses.note,expense_head.exp_category,expenses.exp_head_id')
            ->searchable('expenses.id,expenses.date,expenses.name,expenses.user,expenses.invoice_no,expenses.amount,expenses.documents,expenses.note,expense_head.exp_category,expenses.exp_head_id')
            ->orderable('expenses.name,expenses.user,expenses.note,expenses.invoice_no,expenses.date,expense_head.exp_category,expenses.amount')
            ->join("expense_head", "expenses.exp_head_id = expense_head.id")
            ->join("income", "expenses.inc_head_id = income.id")
            ->sort('expenses.id', 'desc');

        if ($entreprise_id > 0) {
            $this->datatables->where('expenses.entreprise_id', $entreprise_id);
        }

        $this->db->where('expenses.deleted', 1)
            ->from('expenses');
        return $this->datatables->generate('json');
    }

    public function getexpenselist($id = null)
    {
        $entreprise_id = $this->get_entreprise_id();

        $this->datatables
            ->select('expenses.id, expenses.date, expenses.name, expenses.user, expenses.invoice_no, expenses.amount,expenses.category, expenses.documents, expenses.note')
            ->searchable('expenses.id, expenses.date, expenses.name, expenses.user, expenses.invoice_no, expenses.amount,expenses.category, expenses.documents, expenses.note')
            ->orderable('expenses.name, expenses.user, expenses.note, expenses.invoice_no, expenses.date, expenses.amount')
            ->sort('expenses.id', 'desc');

        if ($entreprise_id > 0) {
            $this->datatables->where('expenses.entreprise_id', $entreprise_id);
        }

        $this->db->where('expenses.deleted', 1)
            ->from('expenses');

        return $this->datatables->generate('json');
    }

    public function getbanklist($id = null)
    {
        $entreprise_id = $this->get_entreprise_id();

        $this->datatables
            ->select('bank.id, bank.date, bank.name, bank.user,bank.nom, bank.transaction_type, bank.invoice_no, bank.amount,bank.category, bank.documents, bank.note')
            ->searchable('bank.id, bank.date, bank.name,bank.nom, bank.user, bank.transaction_type, bank.invoice_no, bank.amount,bank.category, bank.documents, bank.note')
            ->orderable('bank.name, bank.user, bank.note,bank.nom, bank.transaction_type, bank.invoice_no, bank.date, bank.amount')
            ->sort('bank.id', 'desc');

        if ($entreprise_id > 0) {
            $this->datatables->where('bank.entreprise_id', $entreprise_id);
        }

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
        $entreprise_id = $this->get_entreprise_id();

        $this->db->trans_start();
        $this->db->trans_strict(false);

        $this->db->where('id', $id);
        if ($entreprise_id > 0) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->set('deleted', '0');
        $this->db->update('expenses');

        $message = DELETE_RECORD_CONSTANT . " On expenses id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return true;
        }
    }

    public function remove_bank($id)
    {
        $entreprise_id = $this->get_entreprise_id();

        $this->db->trans_start();
        $this->db->trans_strict(false);

        $this->db->where('id', $id);
        if ($entreprise_id > 0) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->set('deleted', '0');
        $this->db->update('bank');

        $message = DELETE_RECORD_CONSTANT . " On bank id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return true;
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
        $entreprise_id = $this->get_entreprise_id();

        $this->db->trans_start();
        $this->db->trans_strict(false);

        if (isset($data['id']) && $data['id'] != '') {
            $this->db->where('id', $data['id']);
            if ($entreprise_id > 0) {
                $this->db->where('entreprise_id', $entreprise_id);
            }
            $this->db->update('expenses', $data);

            $message = UPDATE_RECORD_CONSTANT . " On expenses id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
        } else {
            if ($entreprise_id > 0 && !isset($data['entreprise_id'])) {
                $data['entreprise_id'] = $entreprise_id;
            }
            $this->db->insert('expenses', $data);
            $record_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On expenses id " . $record_id;
            $action = "Insert";
        }

        $this->log($message, $record_id, $action);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        } else {
            return $record_id;
        }
    }

    public function addbank($data)
    {
        $entreprise_id = $this->get_entreprise_id();

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

        return $insert_id;
    }

    public function addbank17($data)
    {
        $entreprise_id = $this->get_entreprise_id();

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

        $this->db->insert('bank', $bank_data);
        $insert_id = $this->db->insert_id();

        if (isset($data['bank_id'])) {
            $this->update_bank_balance($data['bank_id']);
        }

        return $insert_id;
    }

    public function check_Exits_group($data)
    {
        $entreprise_id = $this->get_entreprise_id();

        $this->db->select('*');
        $this->db->from('expenses');
        $this->db->where('session_id', $this->current_session);
        $this->db->where('feetype_id', $data['feetype_id']);
        $this->db->where('class_id', $data['class_id']);

        if ($entreprise_id > 0) {
            $this->db->where('entreprise_id', $entreprise_id);
        }

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
        $entreprise_id = $this->get_entreprise_id();

        $this->db->select('expenses.id,expenses.session_id,expenses.invoice_no,expenses.amount,expenses.documents,expenses.note,expense_head.class,feetype.type')->from('expenses');
        $this->db->join('expense_head', 'expenses.class_id = expense_head.id');
        $this->db->join('feetype', 'expenses.feetype_id = feetype.id');
        $this->db->where('expenses.class_id', $class_id);
        $this->db->where('expenses.feetype_id', $type);
        $this->db->where('expenses.session_id', $this->current_session);

        if ($entreprise_id > 0) {
            $this->db->where('expenses.entreprise_id', $entreprise_id);
        }

        $this->db->order_by('expenses.id');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getTotalExpenseBydate($date)
    {
        $entreprise_id = $this->get_entreprise_id();

        $query = 'SELECT sum(amount) as `amount` FROM `expenses` where date=' . $this->db->escape($date);
        if ($entreprise_id > 0) {
            $query .= ' AND entreprise_id = ' . $entreprise_id;
        }
        $query = $this->db->query($query);
        return $query->row();
    }

    public function getTotalExpenseBwdate($date_from, $date_to)
    {
        $entreprise_id = $this->get_entreprise_id();

        $query = 'SELECT sum(amount) as `amount` FROM `expenses` where date between ' . $this->db->escape($date_from) . ' and ' . $this->db->escape($date_to);
        if ($entreprise_id > 0) {
            $query .= ' AND entreprise_id = ' . $entreprise_id;
        }
        $query = $this->db->query($query);
        return $query->row();
    }

    public function getExpenseHeadData($start_date, $end_date)
    {
        $entreprise_id = $this->get_entreprise_id();

        $condition = "date_format(date,'%Y-%m-%d') between '" . $start_date . "' and '" . $end_date . "'";

        $this->db->select('sum(amount) as total,exp_category');
        $this->db->from('expenses');
        $this->db->join('expense_head', 'expenses.exp_head_id = expense_head.id');
        $this->db->where($condition);

        if ($entreprise_id > 0) {
            $this->db->where('expenses.entreprise_id', $entreprise_id);
        }

        $this->db->group_by('expense_head.id');
        return $this->db->get()->result_array();
    }

    // ==================== MÉTHODES BANQUES AVEC FILTRE ENTREPRISE ====================

    public function get_banks()
    {
        $entreprise_id = $this->get_entreprise_id();

        $this->db->order_by('name', 'asc');

        if ($entreprise_id > 0) {
            $this->db->where('banks.entreprise_id', $entreprise_id);
        }

        return $this->db->get('banks')->result();
    }

    public function add_bank($data)
    {
        $this->db->trans_start();

        $entreprise_id = $this->get_entreprise_id();

        if ($entreprise_id > 0 && !isset($data['entreprise_id'])) {
            $data['entreprise_id'] = $entreprise_id;
        }

        $this->db->insert('banks', $data);
        $insert_id = $this->db->insert_id();

        $message = INSERT_RECORD_CONSTANT . " On bank id " . $insert_id;
        $action = "Insert";
        $record_id = $insert_id;
        $this->log($message, $record_id, $action);

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
        $entreprise_id = $this->get_entreprise_id();

        $this->db->where('id', $id);
        if ($entreprise_id > 0) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->update('banks', $data);
    }

    public function delete_bank($id)
    {
        $entreprise_id = $this->get_entreprise_id();

        $this->db->where('id', $id);
        if ($entreprise_id > 0) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->delete('banks');
    }

    public function bank_has_transactions($bank_id)
    {
        $entreprise_id = $this->get_entreprise_id();

        $this->db->where('bank_id', $bank_id);
        if ($entreprise_id > 0) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->limit(1);
        $query = $this->db->get('bank_transactions');
        return $query->num_rows() > 0;
    }

    public function get_bank_transaction($id)
    {
        $entreprise_id = $this->get_entreprise_id();

        $this->db->select('bank.*, banks.name as bank_name, banks.code as bank_code');
        $this->db->from('bank');
        $this->db->join('banks', 'banks.id = bank.bank_id', 'left');
        $this->db->where('bank.id', $id);

        if ($entreprise_id > 0) {
            $this->db->where('bank.entreprise_id', $entreprise_id);
        }

        return $this->db->get()->row();
    }

    public function delete_bank_transaction($id)
    {
        $entreprise_id = $this->get_entreprise_id();

        $this->db->where('id', $id);
        if ($entreprise_id > 0) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->delete('expenses');
    }

    public function get_bank($id)
    {
        $entreprise_id = $this->get_entreprise_id();

        $this->db->where('id', $id);
        if ($entreprise_id > 0) {
            $this->db->where('banks.entreprise_id', $entreprise_id);
        }
        return $this->db->get('banks')->row();
    }

    public function update_bank_balance($bank_id)
    {
        $entreprise_id = $this->get_entreprise_id();

        // Calculer le nouveau solde
        $this->db->select("SUM(CASE 
            WHEN designation = 'Crédit' OR transaction_type IN ('Dépôt', 'Virement entrant') THEN amount 
            ELSE -amount 
        END) as balance");
        $this->db->from('bank');
        $this->db->where('bank_id', $bank_id);

        if ($entreprise_id > 0) {
            $this->db->where('bank.entreprise_id', $entreprise_id);
        }

        $result = $this->db->get()->row();
        $new_balance = $result->balance ?? 0;

        // Mettre à jour la table banks
        $this->db->where('id', $bank_id);
        if ($entreprise_id > 0) {
            $this->db->where('banks.entreprise_id', $entreprise_id);
        }
        $this->db->update('banks', [
            'balance' => $new_balance,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $new_balance;
    }

    public function getTotalExpense()
    {
        $entreprise_id = $this->get_entreprise_id();

        $query = 'SELECT sum(amount) as `amount` FROM `expenses`';
        if ($entreprise_id > 0) {
            $query .= ' WHERE entreprise_id = ' . $entreprise_id;
        }
        $query = $this->db->query($query);
        return $query->row();
    }

    /**
     * Récupère une transaction bancaire par ID
     */
    public function get_bank_transaction_by_id($id)
    {
        $entreprise_id = $this->get_entreprise_id();

        $this->db->where('id', $id);
        if ($entreprise_id > 0) {
            $this->db->where('bank.entreprise_id', $entreprise_id);
        }
        $query = $this->db->get('bank');
        return $query->row();
    }

    /**
     * Récupère les transactions bancaires filtrées
     */
    public function get_bank_transactions($bank_id = null)
    {
        $entreprise_id = $this->get_entreprise_id();

        $this->db->select('bank.*, banks.name as bank_name');
        $this->db->from('bank');
        $this->db->join('banks', 'banks.id = bank.bank_id', 'left');
        $this->db->order_by('bank.date', 'DESC');

        if ($bank_id != null) {
            $this->db->where('bank.bank_id', $bank_id);
        }

        if ($entreprise_id > 0) {
            $this->db->where('bank.entreprise_id', $entreprise_id);
            $this->db->where('banks.entreprise_id', $entreprise_id);
        }

        return $this->db->get()->result();
    }

    /**
     * Met à jour une transaction bancaire
     */
    public function update_bank_transaction($id, $data)
    {
        $entreprise_id = $this->get_entreprise_id();

        $this->db->where('id', $id);
        if ($entreprise_id > 0) {
            $this->db->where('bank.entreprise_id', $entreprise_id);
        }
        $this->db->update('bank', $data);
        return $this->db->affected_rows();
    }

    /**
     * Recalcule le solde d'une banque
     */
    public function recalculate_bank_balance($bank_id)
    {
        if (!$bank_id) return 0;

        $entreprise_id = $this->get_entreprise_id();

        // Récupérer le solde initial
        $this->db->where('id', $bank_id);
        if ($entreprise_id > 0) {
            $this->db->where('banks.entreprise_id', $entreprise_id);
        }
        $bank = $this->db->get('banks')->row();
        if (!$bank) return 0;

        $initial_balance = floatval($bank->balance_re ?? 0);

        // Calculer le total des transactions
        $this->db->select("SUM(CASE 
            WHEN designation = 'Crédit' THEN amount
            WHEN designation = 'Débit' THEN -amount
            ELSE 0
        END) as transaction_total");
        $this->db->from('bank');
        $this->db->where('bank_id', $bank_id);

        if ($entreprise_id > 0) {
            $this->db->where('bank.entreprise_id', $entreprise_id);
        }

        $result = $this->db->get()->row();
        $transaction_total = floatval($result->transaction_total ?? 0);

        // Nouveau solde
        $new_balance = $initial_balance + $transaction_total;

        // Mettre à jour
        $this->db->where('id', $bank_id);
        if ($entreprise_id > 0) {
            $this->db->where('banks.entreprise_id', $entreprise_id);
        }
        $this->db->update('banks', [
            'balance' => $new_balance,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return $new_balance;
    }

    public function get_all_banks()
    {
        $entreprise_id = $this->get_entreprise_id();

        $this->db->select('*');
        $this->db->from('banks');
        $this->db->order_by('name', 'asc');

        if ($entreprise_id > 0) {
            $this->db->where('banks.entreprise_id', $entreprise_id);
        }

        return $this->db->get()->result();
    }

}