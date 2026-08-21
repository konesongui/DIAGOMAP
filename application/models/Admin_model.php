<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_model extends CI_Model {

    protected function getCurrentEntrepriseId() {
        $entreprise_id = 0;

        if (isset($this->session)) {
            $admin_session = $this->session->userdata('admin');
            if (is_array($admin_session) && isset($admin_session['entreprise_id'])) {
                $entreprise_id = (int) $admin_session['entreprise_id'];
            }

            if ($entreprise_id <= 0) {
                $entreprise_id = (int) ($this->session->userdata('entreprise_id') ?? 0);
            }
        }

        if ($entreprise_id <= 0 && isset($this->customlib) && method_exists($this->customlib, 'getUserData')) {
            $userdata = $this->customlib->getUserData();
            $entreprise_id = (int) ($userdata['entreprise_id'] ?? 0);
        }

        return $entreprise_id;
    }

    public function __construct() {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
        $this->current_session_name = $this->setting_model->getCurrentSessionName();
        $this->start_month = $this->setting_model->getStartMonth();
    }

    /**
     * Récupère les revenus du mois courant
     */
    public function getMonthlyRevenue() {
        $entreprise_id = $this->getCurrentEntrepriseId();
        $revenus = [
            'total' => 0,
            'creation_caisse' => 0,
            'reappro' => 0,
            'operations' => 0
        ];

        // Table income (création de caisse)
        $this->db->select('COALESCE(SUM(amount), 0) as total');
        $this->db->from('income');
        $this->db->where('MONTH(date)', 'MONTH(CURDATE())', FALSE);
        $this->db->where('YEAR(date)', 'YEAR(CURDATE())', FALSE);
        $this->db->where('deleted', 1);
        $this->db->where('est_actif', 1);
        if ($entreprise_id > 0) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $query = $this->db->get();
        $result = $query->row();
        $revenus['creation_caisse'] = $result->total ?: 0;

        // Table income_processing (réapprovisionnements)
        $this->db->select('COALESCE(SUM(amount), 0) as total');
        $this->db->from('income_processing');
        $this->db->where('MONTH(date)', 'MONTH(CURDATE())', FALSE);
        $this->db->where('YEAR(date)', 'YEAR(CURDATE())', FALSE);
        $this->db->where('deleted', 1);
        if ($entreprise_id > 0) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $query = $this->db->get();
        $result = $query->row();
        $revenus['reappro'] = $result->total ?: 0;

        // Table operation_caisse (entrées d'argent)
        $this->db->select('COALESCE(SUM(montant), 0) as total');
        $this->db->from('operation_caisse');
        $this->db->where('MONTH(date)', 'MONTH(CURDATE())', FALSE);
        $this->db->where('YEAR(date)', 'YEAR(CURDATE())', FALSE);
        $this->db->group_start();
        $this->db->where('est_active', 1);
        $this->db->or_where('est_active IS NULL', NULL, FALSE);
        $this->db->group_end();
        $this->db->where('deleted', 0);
        $this->db->where('type_operation', 'ENTREE');
        if ($entreprise_id > 0) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $query = $this->db->get();
        $result = $query->row();
        $revenus['operations'] = $result->total ?: 0;

        $revenus['total'] = $revenus['creation_caisse'] + $revenus['reappro'] + $revenus['operations'];

        return $revenus;
    }

    /**
     * Récupère les dépenses du mois courant
     */
    public function getMonthlyExpenses() {
        $entreprise_id = $this->getCurrentEntrepriseId();
        $total_expenses = 0;

        // Dépenses de operation_caisse
        $this->db->select('COALESCE(SUM(montant), 0) as total');
        $this->db->from('operation_caisse');
        $this->db->where('MONTH(date)', 'MONTH(CURDATE())', FALSE);
        $this->db->where('YEAR(date)', 'YEAR(CURDATE())', FALSE);

        // Vérifier quelle colonne utiliser (est_active ou est_actif)
        $fields = $this->db->list_fields('operation_caisse');
        if (in_array('est_active', $fields)) {
            $this->db->group_start();
            $this->db->where('est_active', 1);
            $this->db->or_where('est_active IS NULL', NULL, FALSE);
            $this->db->group_end();
        } else {
            $this->db->where('est_actif', 1);
        }

        $this->db->where('deleted', 0);
        $this->db->where('type_operation', 'SORTIE');
        if ($entreprise_id > 0) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $query = $this->db->get();
        $result = $query->row();
        $total_expenses = $result->total ?: 0;

        // Vérifier aussi les anciennes dépenses si la table existe
        if ($this->db->table_exists('expenses')) {
            $this->db->select('COALESCE(SUM(amount), 0) as total');
            $this->db->from('expenses');
            $this->db->where('MONTH(date)', 'MONTH(CURDATE())', FALSE);
            $this->db->where('YEAR(date)', 'YEAR(CURDATE())', FALSE);
            $this->db->where('deleted', 0);
            if ($entreprise_id > 0) {
                $this->db->where('entreprise_id', $entreprise_id);
            }
            $query = $this->db->get();
            if ($query->num_rows() > 0) {
                $result = $query->row();
                $total_expenses += $result->total ?: 0;
            }
        }

        return $total_expenses;
    }

    /**
     * Récupère le solde actuel de la caisse
     */
    public function getCurrentBalance() {
        $entreprise_id = $this->getCurrentEntrepriseId();
        $revenus = $this->getMonthlyRevenue();

        // Dernier solde_apres_operation
        $this->db->select('solde_apres_operation');
        $this->db->from('operation_caisse');
        $this->db->group_start();
        $this->db->where('est_active', 1);
        $this->db->or_where('est_actif', 1);
        $this->db->or_where('est_active IS NULL', NULL, FALSE);
        $this->db->group_end();
        $this->db->where('deleted', 0);
        if ($entreprise_id > 0) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->order_by('date DESC, created_at DESC');
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->solde_apres_operation ?: 0;
        }

        // Si pas de solde récent, calculer manuellement
        // Total des entrées
        $this->db->select('COALESCE(SUM(montant), 0) as total');
        $this->db->from('operation_caisse');
        $this->db->group_start();
        $this->db->where('est_active', 1);
        $this->db->or_where('est_actif', 1);
        $this->db->or_where('est_active IS NULL', NULL, FALSE);
        $this->db->group_end();
        $this->db->where('deleted', 0);
        $this->db->where('type_operation', 'ENTREE');
        if ($entreprise_id > 0) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $query = $this->db->get();
        $row = $query->row();
        $total_entrees = $row->total ?: 0;

        // Total des sorties
        $this->db->select('COALESCE(SUM(montant), 0) as total');
        $this->db->from('operation_caisse');
        $this->db->group_start();
        $this->db->where('est_active', 1);
        $this->db->or_where('est_actif', 1);
        $this->db->or_where('est_active IS NULL', NULL, FALSE);
        $this->db->group_end();
        $this->db->where('deleted', 0);
        $this->db->where('type_operation', 'SORTIE');
        if ($entreprise_id > 0) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $query = $this->db->get();
        $row = $query->row();
        $total_sorties = $row->total ?: 0;

        return $total_entrees - $total_sorties + $revenus['creation_caisse'] + $revenus['reappro'];
    }

    /**
     * Récupère le nombre de transactions du mois
     */
    public function getMonthlyTransactionsCount() {
        $entreprise_id = $this->getCurrentEntrepriseId();
        $sql = "SELECT COUNT(*) as total
                FROM (
                    SELECT id FROM operation_caisse 
                    WHERE MONTH(date) = MONTH(CURDATE()) 
                    AND YEAR(date) = YEAR(CURDATE())
                    AND (est_active = 1 OR est_actif = 1 OR est_active IS NULL)
                    AND deleted = 0";
        if ($entreprise_id > 0) {
            $sql .= " AND entreprise_id = " . (int) $entreprise_id;
        }
        $sql .= "
                    UNION ALL
                    
                    SELECT id FROM income 
                    WHERE MONTH(date) = MONTH(CURDATE()) 
                    AND YEAR(date) = YEAR(CURDATE())
                    AND est_actif = 1
                    AND deleted = 1";
        if ($entreprise_id > 0) {
            $sql .= " AND entreprise_id = " . (int) $entreprise_id;
        }
        $sql .= "
                    UNION ALL
                    
                    SELECT income_id FROM income_processing 
                    WHERE MONTH(date) = MONTH(CURDATE()) 
                    AND YEAR(date) = YEAR(CURDATE())
                    AND deleted = 1";
        if ($entreprise_id > 0) {
            $sql .= " AND entreprise_id = " . (int) $entreprise_id;
        }
        $sql .= "
                ) as transactions";

        $query = $this->db->query($sql);
        $result = $query->row();
        return $result->total ?: 0;
    }

    /**
     * Récupère les données pour le graphique des dépenses par catégorie
     */
    public function getExpensesByCategory() {
        $entreprise_id = $this->getCurrentEntrepriseId();
        $fields = $this->db->list_fields('operation_caisse');
        $condition_actif = in_array('est_active', $fields)
            ? "(oc.est_active = 1 OR oc.est_active IS NULL)"
            : "oc.est_actif = 1";

        $sql = "SELECT 
            COALESCE(eh.exp_category, oc.category, 'Non catégorisé') as categorie,
            SUM(oc.montant) as total
            FROM operation_caisse oc
            LEFT JOIN expense_head eh ON oc.exp_head_id = eh.id
            WHERE MONTH(oc.date) = MONTH(CURDATE()) 
            AND YEAR(oc.date) = YEAR(CURDATE())
            AND $condition_actif
            AND oc.deleted = 0
            AND oc.type_operation = 'SORTIE'";
        if ($entreprise_id > 0) {
            $sql .= " AND oc.entreprise_id = " . (int) $entreprise_id;
        }
        $sql .= "
            GROUP BY COALESCE(eh.exp_category, oc.category, 'Non catégorisé')
            ORDER BY total DESC
            LIMIT 10";

        $query = $this->db->query($sql);
        $labels = [];
        $data = [];

        // Debug : voir ce que retourne la requête
        log_message('debug', 'SQL Expenses by Category: ' . $sql);
        log_message('debug', 'Number of rows: ' . $query->num_rows());

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                $labels[] = $row->categorie;
                $data[] = floatval($row->total);
                log_message('debug', 'Category: ' . $row->categorie . ' - Total: ' . $row->total);
            }
        } else {
            $labels = ['Aucune dépense'];
            $data = [0];
            log_message('debug', 'No expenses found for current month');
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Récupère les données financières par semaine
     */
    public function getWeeklyFinancialData() {
        $entreprise_id = $this->getCurrentEntrepriseId();
        $current_month = date('n');
        $current_year = date('Y');

        // Initialiser les tableaux pour 5 semaines
        $revenus_semaines = [0, 0, 0, 0, 0];
        $depenses_semaines = [0, 0, 0, 0, 0];

        $fields = $this->db->list_fields('operation_caisse');
        $condition_actif = in_array('est_active', $fields)
            ? "(est_active = 1 OR est_active IS NULL)"
            : "est_actif = 1";

        // Revenus par semaine
        $sql_revenus = "SELECT 
                        WEEK(date, 1) as semaine,
                        COALESCE(SUM(amount), 0) as total
                        FROM income 
                        WHERE MONTH(date) = $current_month 
                        AND YEAR(date) = $current_year
                        AND deleted = 1
                        AND est_actif = 1";
        if ($entreprise_id > 0) {
            $sql_revenus .= " AND entreprise_id = " . (int) $entreprise_id;
        }
        $sql_revenus .= " GROUP BY semaine
                        
                        UNION ALL
                        
                        SELECT 
                        WEEK(date, 1) as semaine,
                        COALESCE(SUM(amount), 0) as total
                        FROM income_processing 
                        WHERE MONTH(date) = $current_month 
                        AND YEAR(date) = $current_year
                        AND deleted = 1";
        if ($entreprise_id > 0) {
            $sql_revenus .= " AND entreprise_id = " . (int) $entreprise_id;
        }
        $sql_revenus .= " GROUP BY semaine
                        
                        UNION ALL
                        
                        SELECT 
                        WEEK(date, 1) as semaine,
                        COALESCE(SUM(montant), 0) as total
                        FROM operation_caisse 
                        WHERE MONTH(date) = $current_month 
                        AND YEAR(date) = $current_year
                        AND $condition_actif
                        AND deleted = 0
                        AND type_operation = 'ENTREE'";
        if ($entreprise_id > 0) {
            $sql_revenus .= " AND entreprise_id = " . (int) $entreprise_id;
        }
        $sql_revenus .= " GROUP BY semaine";

        $query = $this->db->query($sql_revenus);
        if ($query->num_rows() > 0) {
            $first_day_of_month = strtotime(date('Y-m-01'));
            $first_week = date('W', $first_day_of_month);

            foreach ($query->result() as $row) {
                $week_in_month = $row->semaine - $first_week + 1;
                if ($week_in_month >= 1 && $week_in_month <= 5) {
                    $revenus_semaines[$week_in_month - 1] += floatval($row->total);
                }
            }
        }

        // Dépenses par semaine
        $sql_depenses = "SELECT 
                         WEEK(date, 1) as semaine,
                         COALESCE(SUM(montant), 0) as total
                         FROM operation_caisse 
                         WHERE MONTH(date) = $current_month 
                         AND YEAR(date) = $current_year
                         AND $condition_actif
                         AND deleted = 0
                         AND type_operation = 'SORTIE'";
        if ($entreprise_id > 0) {
            $sql_depenses .= " AND entreprise_id = " . (int) $entreprise_id;
        }
        $sql_depenses .= " GROUP BY semaine";

        $query = $this->db->query($sql_depenses);
        if ($query->num_rows() > 0) {
            $first_day_of_month = strtotime(date('Y-m-01'));
            $first_week = date('W', $first_day_of_month);

            foreach ($query->result() as $row) {
                $week_in_month = $row->semaine - $first_week + 1;
                if ($week_in_month >= 1 && $week_in_month <= 5) {
                    $depenses_semaines[$week_in_month - 1] = floatval($row->total);
                }
            }
        }

        return [
            'revenus' => $revenus_semaines,
            'depenses' => $depenses_semaines
        ];
    }

    /**
     * Récupère les statistiques des employés
     */
    public function getEmployeeStatistics() {
        $entreprise_id = $this->getCurrentEntrepriseId();
        $stats = [];

        // Total employés
        $this->db->select('COUNT(*) as total');
        $this->db->from('staff');
        $this->db->where('is_active', 1);
        $this->db->where('deleted', 1);
        if ($entreprise_id > 0) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $query = $this->db->get();
        $stats['total'] = $query->row()->total ?? 0;

        // Femmes
        $this->db->select('COUNT(*) as total');
        $this->db->from('staff');
        $this->db->where('gender', 'Female');
        $this->db->where('is_active', 1);
        $this->db->where('deleted', 1);
        if ($entreprise_id > 0) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $query = $this->db->get();
        $stats['femmes'] = $query->row()->total ?? 0;

        // Hommes
        $this->db->select('COUNT(*) as total');
        $this->db->from('staff');
        $this->db->where('gender', 'Male');
        $this->db->where('is_active', 1);
        $this->db->where('deleted', 1);
        if ($entreprise_id > 0) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $query = $this->db->get();
        $stats['hommes'] = $query->row()->total ?? 0;

        // CDI (permanent)
        $this->db->select('COUNT(*) as total');
        $this->db->from('staff');
        $this->db->where('contract_type', 'permanent');
        $this->db->where('is_active', 1);
        $this->db->where('deleted', 1);
        if ($entreprise_id > 0) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $query = $this->db->get();
        $stats['cdi'] = $query->row()->total ?? 0;

        // CDD (probation)
        $this->db->select('COUNT(*) as total');
        $this->db->from('staff');
        $this->db->where('contract_type', 'probation');
        $this->db->where('is_active', 1);
        $this->db->where('deleted', 1);
        if ($entreprise_id > 0) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $query = $this->db->get();
        $stats['cdd'] = $query->row()->total ?? 0;

        // STAGE (stage)
        $this->db->select('COUNT(*) as total');
        $this->db->from('staff');
        $this->db->where('contract_type', 'stage');
        $this->db->where('is_active', 1);
        $this->db->where('deleted', 1);
        if ($entreprise_id > 0) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $query = $this->db->get();
        $stats['stage'] = $query->row()->total ?? 0;

        // Autres
        $this->db->select('COUNT(*) as total');
        $this->db->from('staff');
        $this->db->where("(contract_type IS NULL OR contract_type NOT IN ('permanent', 'probation', 'stage'))");
        $this->db->where('is_active', 1);
        $this->db->where('deleted', 1);
        if ($entreprise_id > 0) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $query = $this->db->get();
        $stats['autre'] = $query->row()->total ?? 0;

        // Actifs
        $this->db->select('COUNT(*) as total');
        $this->db->from('staff');
        $this->db->where('is_active', 1);
        $this->db->where('deleted', 1);
        if ($entreprise_id > 0) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $query = $this->db->get();
        $stats['actifs'] = $query->row()->total ?? 0;

        // Inactifs
        $this->db->select('COUNT(*) as total');
        $this->db->from('staff');
        $this->db->where('is_active', 0);
        $this->db->where('deleted', 1);
        if ($entreprise_id > 0) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $query = $this->db->get();
        $stats['inactifs'] = $query->row()->total ?? 0;

        return $stats;
    }

    /**
     * Récupère les données pour le tableau de bord
     */
    public function getDashboardData() {
        $data = [];
        $data['revenus'] = $this->getMonthlyRevenue();
        $data['depenses'] = $this->getMonthlyExpenses();
        $data['solde'] = $this->getCurrentBalance();
        $data['transactions'] = $this->getMonthlyTransactionsCount();
        $data['expenses_category'] = $this->getExpensesByCategory();
        $data['weekly'] = $this->getWeeklyFinancialData();
        $data['employees'] = $this->getEmployeeStatistics();

        return $data;
    }

    // Vos méthodes existantes...
    public function get($id = null) {
        $this->db->select()->from('admin');
        if ($id != null) {
            $this->db->where('id', $id);
        } else {
            $this->db->order_by('id');
        }
        $query = $this->db->get();
        if ($id != null) {
            return $query->row_array();
        } else {
            return $query->result_array();
        }
    }

    public function remove($id) {
        $this->db->where('id', $id);
        $this->db->delete('admin');
    }

    public function add($data) {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('admin', $data);
        } else {
            $this->db->insert('admin', $data);
        }
    }

    public function checkLogin($data) {
        $this->db->select('id, username, password');
        $this->db->from('admin');
        $this->db->where('email', $data['username']);
        $this->db->where('password', MD5($data['password']));
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function read_user_information($email) {
        $condition = "email =" . "'" . $email . "'";
        $this->db->select('*');
        $this->db->from('admin');
        $this->db->where($condition);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function readByEmail($email) {
        $condition = "email =" . "'" . $email . "'";
        $this->db->select('*');
        $this->db->from('admin');
        $this->db->where($condition);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function updateVerCode($data) {
        $this->db->where('id', $data['id']);
        $query = $this->db->update('admin', $data);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function getAdminByCode($ver_code) {
        $condition = "verification_code =" . "'" . $ver_code . "'";
        $this->db->select('*');
        $this->db->from('admin');
        $this->db->where($condition);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function change_password($data) {
        $condition = "id =" . "'" . $data['id'] . "'";
        $this->db->select('password');
        $this->db->from('admin');
        $this->db->where($condition);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function checkOldPass($data) {
        $this->db->where('id', $data['user_id']);
        $this->db->where('email', $data['user_email']);
        $query = $this->db->get('staff');
        if ($query->num_rows() > 0)
            return TRUE;
        else
            return FALSE;
    }

    public function saveNewPass($data) {
        $this->db->where('id', $data['id']);
        $query = $this->db->update('staff', $data);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function saveForgotPass($data) {
        $this->db->where('email', $data['email']);
        $query = $this->db->update('admin', $data);
        if ($query) {
            return true;
        } else {
            return false;
        }
    }

    public function addReceipt($data) {
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('fee_receipt_no', $data);
        } else {
            $this->db->insert('fee_receipt_no', $data);
            $insert_id = $this->db->insert_id();
            return $insert_id;
        }
    }

    public function getMonthlyCollection() {
        $data = explode("-", $this->current_session_name);
        $data_first = $data[0];
        $data_second = substr($data_first, 0, 2) . $data[1];
        $this->start_month;
        $sql = "SELECT SUM(amount+amount_fine-amount_discount) as amount,MONTH(date) as month ,YEAR(date) as year FROM student_fees where YEAR(date) BETWEEN " . $this->db->escape($data_first) . " and " . $this->db->escape($data_second) . " GROUP BY MONTH(date)";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getMonthlyExpense() {
        $data = explode("-", $this->current_session_name);
        $data_first = $data[0];
        $data_second = substr($data_first, 0, 2) . $data[1];
        $this->start_month;
        $sql = "SELECT SUM(amount) as amount,MONTH(date) as month ,YEAR(date) as year FROM expenses where YEAR(date) BETWEEN " . $this->db->escape($data_first) . " and " . $this->db->escape($data_second) . " GROUP BY MONTH(date)";
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getCollectionbyDay($date) {
        $sql = 'SELECT SUM(amount+amount_fine-amount_discount) as amount FROM student_fees where date=' . $this->db->escape($date);
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getExpensebyDay($date) {
        $sql = 'SELECT SUM(amount) as amount FROM expenses where date=' . $this->db->escape($date);
        $query = $this->db->query($sql);
        return $query->result_array();
    }

    public function getAllEnquiryCount($start_date, $end_date) {
        $condition = " date_format(date,'%Y-%m-%d') between '" . $start_date . "' and '" . $end_date . "'";
        return $this->db->select("SUM(CASE WHEN status = 'won' THEN 1  ELSE 0 END) AS 'complete',SUM(CASE WHEN status = 'active' THEN 1  ELSE 0 END) AS 'active',SUM(CASE WHEN status = 'passive' THEN 1  ELSE 0 END) AS 'passive',SUM(CASE WHEN status = 'dead' THEN 1  ELSE 0 END) AS 'dead',SUM(CASE WHEN status = 'lost' THEN 1  ELSE 0 END) AS 'lost',count(*) as total")->from('enquiry')->where($condition)->get()->row_array();
    }
}