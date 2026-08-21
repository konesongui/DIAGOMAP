<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Income_model extends My_Model
{
    protected $ma_table = 'income';
    protected $entreprise_id;

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
        // Récupération de l'entreprise connectée
        $userdata = $this->customlib->getUserData();
        $this->entreprise_id = $userdata['entreprise_id'] ?? 0;
    }

    /**
     * Applique le filtre entreprise sur une table donnée
     * @param string $table_prefix Préfixe de table (ex: 'income', 'ip')
     */
    private function _filter_entreprise($table_prefix = 'income')
    {
        if ($this->entreprise_id > 0) {
            $this->db->where($table_prefix . '.entreprise_id', $this->entreprise_id);
        }
    }

    // ==================== MÉTHODES EXISTANTES AVEC FILTRE ====================

    public function GetIncomeValueId($id)
    {
        $this->db->select('income.*, income_head.*');
        $this->db->from('income');
        $this->db->join('income_head', 'income.inc_head_id = income_head.id', 'left');
        $this->db->where('income.id', $id);
        $this->_filter_entreprise('income');
        $query = $this->db->get();
        return $query->row();
    }

    public function getIncomeQty($logid)
    {
        $this->db->select('*');
        $this->db->from('income');
        $this->db->where('id', $logid);
        $this->_filter_entreprise('income');
        $query = $this->db->get();
        return $query->row();
    }

    public function transfer($text = null, $start_date = null, $end_date = null)
    {
        $this->datatables
            ->select('transfert_caisse.id, transfert_caisse.date, transfert_caisse.from_id, transfert_caisse.to_id, transfert_caisse.amount')
            ->searchable('income.name,income.status,income.invoice_no,income.date,income.amount,income.amount_re')
            ->orderable('income.name,income.invoice_no,income.status,income.date,income.amount_re,income.amount')
            ->from('transfert_caisse');

        if ($this->entreprise_id > 0) {
            $this->datatables->where('transfert_caisse.entreprise_id', $this->entreprise_id);
        }

        if (!empty($text)) {
            $this->datatables->like('income.name', $text);
        } else {
            $this->datatables->where('income.date <=', $end_date);
            $this->datatables->where('income.date >=', $start_date);
        }

        return $this->datatables->generate('json');
    }

    public function search($text = null, $start_date = null, $end_date = null)
    {
        $this->datatables
            ->select('income.id,income.date,income.name,income.user,income.invoice_no,income.amount,income.montant,income.amount_re,income.documents,income.note,income.status')
            ->searchable('income.name,income.status,income.invoice_no,income.date,income.amount,income.montant,income.amount_re')
            ->orderable('income.name,income.invoice_no,income.status,income.date,income.amount_re,income.amount,income.montant')
            ->from('income');

        if ($this->entreprise_id > 0) {
            $this->datatables->where('income.entreprise_id', $this->entreprise_id);
        }

        if (!empty($text)) {
            $this->datatables->like('income.name', $text);
        } else {
            $this->datatables->where('income.date <=', $end_date);
            $this->datatables->where('income.date >=', $start_date);
        }

        return $this->datatables->generate('json');
    }

    public function GetCaissesQty($id)
    {
        $this->db->select('*');
        $this->db->from('income');
        $this->db->where('id', $id);
        $this->_filter_entreprise('income');
        $query = $this->db->get();
        return $query->row();
    }

    public function Update_Caisses($id, $data)
    {
        $this->db->where('id', $id);
        if ($this->entreprise_id > 0) {
            $this->db->where('entreprise_id', $this->entreprise_id);
        }
        $this->db->update('income', $data);
    }

    public function searchincomegroup($start_date = null, $end_date = null, $head_id = null)
    {
        $this->datatables
            ->select('GROUP_CONCAT(income.id,"@",income.name,"@",income.invoice_no,"@",income.date,"@",income.amount,income.amount_re,income.user,income.status,income.amount_re) as income, income_head.income_category,sum(income.amount) as total_amount')
            ->searchable('income_head.income_category,income.id,income.name,income.user,income.date,income.invoice_no,income.status,income.amount,income.amount_re')
            ->orderable('income_head.income_category,income.id,income.name,income.user,income.date,income.invoice_no,income.status,income.amount_re')
            ->join('income_head', 'income.inc_head_id = income_head.id')
            ->where('income.date >=', $start_date)
            ->where('income.date <=', $end_date)
            ->from('income');

        if ($this->entreprise_id > 0) {
            $this->datatables->where('income.entreprise_id', $this->entreprise_id);
        }

        if ($head_id != null) {
            $this->datatables->where('income.inc_head_id', $head_id);
        }
        $this->datatables->group_by('income.inc_head_id');
        return $this->datatables->generate('json');
    }

    public function getIncomeHeadsData($start_date, $end_date)
    {
        $condition = "date_format(date,'%Y-%m-%d') between '" . $start_date . "' and '" . $end_date . "'";
        $this->db->select('sum(amount) as total,income_category');
        $this->db->from('income');
        $this->db->join('income_head', 'income.inc_head_id = income_head.id');
        $this->db->where($condition);
        if ($this->entreprise_id > 0) {
            $this->db->where('income.entreprise_id', $this->entreprise_id);
        }
        $this->db->group_by('income_head.id');
        $r = $this->db->get()->result_array();
        return $r;
    }

    public function getfilesHeadsData($start_date, $end_date)
    {
        $condition = "date_format(date,'%Y-%m-%d') between '" . $start_date . "' and '" . $end_date . "'";
        $this->db->select('sum(amount) as total,income_category');
        $this->db->from('files');
        $this->db->join('income_head', 'income.inc_head_id = income_head.id');
        $this->db->where($condition);
        if ($this->entreprise_id > 0) {
            $this->db->where('files.entreprise_id', $this->entreprise_id);
        }
        $this->db->group_by('income_head.id');
        $r = $this->db->get()->result_array();
        return $r;
    }

    public function getcaisse($id = null)
    {
        $this->db->select('*');
        $this->db->from('income');
        if ($id !== null) {
            $this->db->where('income.id', $id);
        }
        $this->_filter_entreprise('income');
        $query = $this->db->get();
        return ($id !== null) ? $query->row() : $query->result();
    }

    public function get($id = null)
    {
        $this->db->select('income.id, income.date, income.name, income.est_actif, income.user, income.invoice_no, income.amount,income.montant, income.amount_re, income.documents, income.note');
        $this->db->where('income.est_actif', '1');
        $this->db->where('income.deleted', '1');
        $this->db->from('income');
        $this->_filter_entreprise('income');

        if ($id != null) {
            $this->db->where('income.id', $id);
        } else {
            $this->db->order_by('income.id', 'DESC');
        }

        $query = $this->db->get();
        return ($id != null) ? $query->row_array() : $query->result_array();
    }

    public function got($id = null)
    {
        $this->db->select('income.id, income.date, income.name, income.est_actif, income.user, income.invoice_no, income.status, income.amount,income.montant, income.amount_re, income.documents, income.note');
        $this->db->from('income');
        $this->_filter_entreprise('income');

        if ($id != null) {
            $this->db->where('income.id', $id);
        } else {
            $this->db->order_by('income.id', 'DESC');
        }

        $query = $this->db->get();
        return ($id != null) ? $query->row_array() : $query->result_array();
    }

    public function gets($id = null)
    {
        $this->db->select('income.id,income.date,income.name,income.invoice_no,income.amount,income.montant,income.amount_re,income.est_actif,income.documents,income.note,income_head.income_category,income.inc_head_id');
        $this->db->from('files');
        $this->db->join('income_head', 'income.inc_head_id = income_head.id');
        $this->_filter_entreprise('income');

        if ($id != null) {
            $this->db->where('income.id', $id);
        } else {
            $this->db->order_by('income.id', 'DESC');
        }

        $query = $this->db->get();
        return ($id != null) ? $query->row_array() : $query->result_array();
    }

    public function getincomelist()
    {
        $this->datatables
            ->select('income.id, income.date, income.name, income.est_actif, income.user, income.invoice_no, income.status, income.amount,income.montant, income.amount_re, income.documents, income.note')
            ->searchable('income.name, income.user, income.invoice_no, income.status, income.date, income.amount,income.montant, income.amount_re, income.note')
            ->orderable('income.name, income.user, income.note, income.invoice_no, income.status, income.date, income.amount,income.montant, income.amount_re')
            ->sort('income.id', 'desc')
            ->from('income');

        if ($this->entreprise_id > 0) {
            $this->datatables->where('income.entreprise_id', $this->entreprise_id);
        }
        $this->datatables->where('income.deleted', 1);
        return $this->datatables->generate('json');
    }

    public function gettransferlist()
    {
        $this->datatables
            ->select('t.id, t.date, t.amount, fc.name as from_caisse, tc.name as to_caisse')
            ->searchable('fc.name, tc.name, t.amount, t.date')
            ->orderable('fc.name, tc.name, t.amount, t.date')
            ->join('caisses as fc', 't.from_id = fc.id', 'left')
            ->join('caisses as tc', 't.to_id = tc.id', 'left')
            ->from('transfert_caisse as t')
            ->sort('t.date', 'desc');

        if ($this->entreprise_id > 0) {
            $this->datatables->where('t.entreprise_id', $this->entreprise_id);
        }
        return $this->datatables->generate('json');
    }

    public function getfileslist()
    {
        $this->datatables
            ->select('income.id,income.date,income.name,income.user,income.status,income.invoice_no,income.amount,income.amount_re,income.documents,income.note,income_head.income_category,income.inc_head_id')
            ->searchable('income.name,income.user,income.invoice_no,income.status,income.date,income_head.income_category,income.amount,income.amount_re,income.note')
            ->orderable('income.name,income.user,income.note,income.status,income.invoice_no,income.date,income_head.income_category,income.amount,income.amount_re')
            ->join("income_head", "income.inc_head_id = income_head.id")
            ->sort('income.id', 'desc')
            ->from('files');

        if ($this->entreprise_id > 0) {
            $this->datatables->where('income.entreprise_id', $this->entreprise_id);
        }
        return $this->datatables->generate('json');
    }

    public function getIncomesQty($logid)
    {
        $this->db->select('*');
        $this->db->from('income');
        $this->db->where('id', $logid);
        $this->_filter_entreprise('income');
        $query = $this->db->get();
        return $query->row();
    }

    public function getFilesQty($logid)
    {
        $this->db->select('*');
        $this->db->from('files');
        $this->db->where('id', $logid);
        if ($this->entreprise_id > 0) {
            $this->db->where('files.entreprise_id', $this->entreprise_id);
        }
        $query = $this->db->get();
        return $query->row();
    }

    public function remove($id)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        $caisse = $this->db->get_where('income', ['id' => $id])->row();
        if (!$caisse) {
            $this->db->trans_complete();
            return false;
        }

        $this->db->where('id', $id);
        if ($this->entreprise_id > 0) {
            $this->db->where('entreprise_id', $this->entreprise_id);
        }
        $this->db->update('income', [
            'is_deleted' => 'yes',
            'deleted_at' => date('Y-m-d H:i:s'),
            'deleted_by' => $this->session->userdata('username') ?? 'system'
        ]);

        // Marquer les opérations liées
        $this->db->where('caisse_id', $id);
        if ($this->entreprise_id > 0) {
            $this->db->where('entreprise_id', $this->entreprise_id);
        }
        $this->db->update('operation_caisse', [
            'deleted' => 'yes',
            'deleted_at' => date('Y-m-d H:i:s')
        ]);

        $this->log(DELETE_RECORD_CONSTANT . " On Income id " . $id, $id, "Delete");
        $this->db->trans_complete();
        return ($this->db->trans_status() === false) ? false : $id;
    }

    // Méthodes remove_16, removed conservées (similaires avec filtre)
    public function remove_16($id)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db->where('id', $id);
        if ($this->entreprise_id > 0) {
            $this->db->where('entreprise_id', $this->entreprise_id);
        }
        $this->db->delete('income');
        $this->log(DELETE_RECORD_CONSTANT . " On Income id " . $id, $id, "Delete");
        $this->db->trans_complete();
        return ($this->db->trans_status() === false) ? false : $id;
    }

    public function removed($id)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);
        $this->db->where('id', $id);
        if ($this->entreprise_id > 0) {
            $this->db->where('entreprise_id', $this->entreprise_id);
        }
        $this->db->delete('operation_caisse');
        $this->log(DELETE_RECORD_CONSTANT . " On Increase id " . $id, $id, "Delete");
        $this->db->trans_complete();
        return ($this->db->trans_status() === false) ? false : $id;
    }

    // ==================== GESTION DES CAISSES / OPÉRATIONS ====================

    public function create_caisse($data)
    {
        if ($this->entreprise_id > 0 && !isset($data['entreprise_id'])) {
            $data['entreprise_id'] = $this->entreprise_id;
        }
        $this->db->insert('income', $data);
        return $this->db->insert_id();
    }

    public function create_operation($data)
    {
        if ($this->entreprise_id > 0 && !isset($data['entreprise_id'])) {
            $data['entreprise_id'] = $this->entreprise_id;
        }
        $this->db->insert('income', $data);
        return $this->db->insert_id();
    }

    public function get_all_caisses()
    {
        $this->db->select('*');
        $this->db->from('income');
        $this->db->where('inc_head_id IS NULL', null, false);
        $this->db->or_where('inc_head_id', 0);
        $this->_filter_entreprise('income');
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_caisse_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('income');
        $this->db->where('id', $id);
        $this->_filter_entreprise('income');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function update_caisse($id, $data)
    {
        $this->db->where('id', $id);
        if ($this->entreprise_id > 0) {
            $this->db->where('entreprise_id', $this->entreprise_id);
        }
        return $this->db->update('income', $data);
    }

    public function get_solde_caisse($caisse_id)
    {
        $this->db->select('SUM(entree - sortie) as solde', false);
        $this->db->from('income');
        $this->db->where('caisse_id', $caisse_id);
        if ($this->entreprise_id > 0) {
            $this->db->where('entreprise_id', $this->entreprise_id);
        }
        $query = $this->db->get();
        $result = $query->row();
        return $result ? floatval($result->solde) : 0;
    }

    // ==================== MÉTHODE ADD GÉNÉRIQUE ====================

    public function add($data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            if ($this->entreprise_id > 0) {
                $this->db->where('entreprise_id', $this->entreprise_id);
            }
            $this->db->update('income', $data);
            $record_id = $data['id'];
            $action = "Update";
        } else {
            if ($this->entreprise_id > 0 && !isset($data['entreprise_id'])) {
                $data['entreprise_id'] = $this->entreprise_id;
            }
            $this->db->insert('income', $data);
            $record_id = $this->db->insert_id();
            $action = "Insert";
        }

        $this->log(($action == "Update" ? UPDATE_RECORD_CONSTANT : INSERT_RECORD_CONSTANT) . " On Income id " . $record_id, $record_id, $action);
        $this->db->trans_complete();
        return ($this->db->trans_status() === false) ? false : $record_id;
    }

    // ==================== AUTRES MÉTHODES UTILITAIRES ====================

    public function check_Exits_group($data)
    {
        $this->db->select('*');
        $this->db->from('income');
        $this->db->where('session_id', $this->current_session);
        $this->db->where('feetype_id', $data['feetype_id']);
        $this->db->where('class_id', $data['class_id']);
        if ($this->entreprise_id > 0) {
            $this->db->where('entreprise_id', $this->entreprise_id);
        }
        $this->db->limit(1);
        $query = $this->db->get();
        return ($query->num_rows() == 1) ? false : true;
    }

    public function getTypeByFeecategory($type, $class_id)
    {
        $this->db->select('income.id,income.session_id,income.amount,income.invoice_no,income.amount_re,income.documents,income.note,income_head.class,feetype.type');
        $this->db->from('income');
        $this->db->join('income_head', 'income.class_id = income_head.id');
        $this->db->join('feetype', 'income.feetype_id = feetype.id');
        $this->db->where('income.class_id', $class_id);
        $this->db->where('income.feetype_id', $type);
        $this->db->where('income.session_id', $this->current_session);
        if ($this->entreprise_id > 0) {
            $this->db->where('income.entreprise_id', $this->entreprise_id);
        }
        $this->db->order_by('income.id');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getTotalExpenseBydate($date)
    {
        $query = 'SELECT sum(amount) as `amount` FROM `income` where date=' . $this->db->escape($date);
        if ($this->entreprise_id > 0) {
            $query .= ' AND entreprise_id = ' . $this->entreprise_id;
        }
        $query = $this->db->query($query);
        return $query->row();
    }

    public function getTotalExpenseBwdate($date_from, $date_to)
    {
        $query = 'SELECT sum(amount) as `amount` FROM `income` where date between ' . $this->db->escape($date_from) . ' and ' . $this->db->escape($date_to);
        if ($this->entreprise_id > 0) {
            $query .= ' AND entreprise_id = ' . $this->entreprise_id;
        }
        $query = $this->db->query($query);
        return $query->row();
    }

    public function getTotalIncome()
    {
        $query = 'SELECT sum(amount_re) as `amount` FROM `income`';
        if ($this->entreprise_id > 0) {
            $query .= ' WHERE entreprise_id = ' . $this->entreprise_id;
        }
        $query = $this->db->query($query);
        return $query->row();
    }

    public function getIncomeWithReappro($start_date, $end_date)
    {
        $this->db->select('income.*');
        $this->db->from('income');
        $this->db->where('income.deleted', 1);
        $this->db->where('income.est_actif', 1);
        $this->db->where('income.date >=', $start_date);
        $this->db->where('income.date <=', $end_date);
        $this->_filter_entreprise('income');

        $query = $this->db->get();
        $incomes = [];
        foreach ($query->result() as $row) {
            $incomeData = ['income' => $row, 'income_processing' => []];
            $reappro = $this->getReapprovisionnements($row->id);
            foreach ($reappro as $r) {
                $incomeData['income_processing'][] = $r;
            }
            $incomes[] = $incomeData;
        }
        return $incomes;
    }

    public function getReapprovisionnements($incomeID)
    {
        $this->db->select('*');
        $this->db->from('income_processing');
        $this->db->where('income_processing.deleted', 1);
        $this->db->where('income_id', $incomeID);
        $this->db->where('amount >', 0);
        if ($this->entreprise_id > 0) {
            $this->db->where('income_processing.entreprise_id', $this->entreprise_id);
        }
        $query = $this->db->get();
        return $query->result();
    }

    public function get_operation_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('income');
        $this->db->where('id', $id);
        $this->_filter_entreprise('income');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $operation = $query->row_array();
            if ($operation['entree'] > 0) {
                $operation['type'] = 'entree';
                $operation['montant'] = $operation['entree'];
            } else {
                $operation['type'] = 'sortie';
                $operation['montant'] = $operation['sortie'];
            }
            $operation['date'] = date('Y-m-d', strtotime($operation['date']));
            return $operation;
        }
        return false;
    }

    public function update_operation($id, $data)
    {
        $this->db->where('id', $id);
        if ($this->entreprise_id > 0) {
            $this->db->where('entreprise_id', $this->entreprise_id);
        }
        return $this->db->update('income', $data);
    }

    public function delete_operation($id)
    {
        $this->db->where('id', $id);
        if ($this->entreprise_id > 0) {
            $this->db->where('entreprise_id', $this->entreprise_id);
        }
        return $this->db->delete('income');
    }

    // ==================== MÉTHODES BANKS ET SOUS-COMPTES ====================
    // (non filtrées car elles concernent des tables différentes,
    // mais on peut ajouter un filtre si elles ont une colonne entreprise_id)

    public function get_all_banks()
    {
        $this->db->select('*');
        $this->db->from('banks');
        if ($this->entreprise_id > 0) {
            $this->db->where('banks.entreprise_id', $this->entreprise_id);
        }
        return $this->db->get()->result();
    }

    public function get_bank($id)
    {
        $this->db->where('id', $id);
        if ($this->entreprise_id > 0) {
            $this->db->where('banks.entreprise_id', $this->entreprise_id);
        }
        return $this->db->get('banks')->row();
    }

    public function update_bank($id, $data)
    {
        $this->db->where('id', $id);
        if ($this->entreprise_id > 0) {
            $this->db->where('banks.entreprise_id', $this->entreprise_id);
        }
        return $this->db->update('banks', $data);
    }

    public function get_sous_comptes($caisse_id)
    {
        $this->db->select('*');
        $this->db->from('caisse_sous_comptes');
        $this->db->where('caisse_id', $caisse_id);
        $this->db->where('est_actif', 1);
        if ($this->entreprise_id > 0) {
            $this->db->where('caisse_sous_comptes.entreprise_id', $this->entreprise_id);
        }
        $this->db->order_by('nom', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_sous_compte($id)
    {
        $this->db->select('*');
        $this->db->from('caisse_sous_comptes');
        $this->db->where('id', $id);
        if ($this->entreprise_id > 0) {
            $this->db->where('caisse_sous_comptes.entreprise_id', $this->entreprise_id);
        }
        return $this->db->get()->row_array();
    }

    public function create_sous_compte($data)
    {
        if ($this->entreprise_id > 0 && !isset($data['entreprise_id'])) {
            $data['entreprise_id'] = $this->entreprise_id;
        }
        $this->db->insert('caisse_sous_comptes', $data);
        return $this->db->insert_id();
    }

    public function update_solde_sous_compte($id, $montant, $type = 'entree')
    {
        $sous_compte = $this->get_sous_compte($id);
        if ($type == 'entree') {
            $nouveau_solde = $sous_compte['solde_actuel'] + $montant;
        } else {
            $nouveau_solde = $sous_compte['solde_actuel'] - $montant;
        }
        $this->db->where('id', $id);
        if ($this->entreprise_id > 0) {
            $this->db->where('caisse_sous_comptes.entreprise_id', $this->entreprise_id);
        }
        return $this->db->update('caisse_sous_comptes', [
            'solde_actuel' => $nouveau_solde,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}