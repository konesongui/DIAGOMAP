<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Ohada_model extends CI_Model
{
    protected $schema_db;
    protected $table_exists_cache = array();
    protected $table_columns_cache = array();
    protected $current_user_data = array();
    protected $report_lines_cache = array();
    protected $known_account_numbers_cache = null;
    protected $default_classes = array(
        '1' => 'Comptes de ressources durables',
        '2' => 'Comptes d actifs immobilises',
        '3' => 'Comptes de stocks',
        '4' => 'Comptes de tiers',
        '5' => 'Comptes de tresorerie',
        '6' => 'Comptes de charges',
        '7' => 'Comptes de produits',
        '8' => 'Comptes des autres charges et autres produits',
        '9' => 'Comptes analytiques',
    );

    public function __construct()
    {
        parent::__construct();
        $this->schema_db = $this->load->database('default', true);
        $this->current_user_data = $this->resolveCurrentUserData();
    }

    public function get_entreprise_id()
    {
        return isset($this->current_user_data['entreprise_id']) ? (int) $this->current_user_data['entreprise_id'] : 0;
    }

    public function get_user_id()
    {
        return isset($this->current_user_data['id']) ? (int) $this->current_user_data['id'] : 0;
    }

    public function table_exists($table)
    {
        if (!array_key_exists($table, $this->table_exists_cache)) {
            $this->table_exists_cache[$table] = $this->schema_db->table_exists($table);
        }

        return $this->table_exists_cache[$table];
    }

    public function column_exists($table, $column)
    {
        if (!$this->table_exists($table)) {
            return false;
        }

        if (!array_key_exists($table, $this->table_columns_cache)) {
            $this->table_columns_cache[$table] = $this->schema_db->list_fields($table);
        }

        return in_array($column, $this->table_columns_cache[$table], true);
    }

    protected function scope_table($table, $alias = '')
    {
        $entreprise_id = $this->get_entreprise_id();
        if ($entreprise_id > 0 && $this->column_exists($table, 'entreprise_id')) {
            $prefix = $alias !== '' ? $alias . '.' : '';
            $this->db->where($prefix . 'entreprise_id', $entreprise_id);
        }
    }

    protected function deleted_clause($table, $alias = '')
    {
        if ($this->column_exists($table, 'deleted')) {
            $prefix = $alias !== '' ? $alias . '.' : '';
            $this->db->where($prefix . 'deleted', 0);
        }
    }

    protected function resolveCurrentUserData()
    {
        $userdata = $this->customlib->getUserData();
        return is_array($userdata) ? $userdata : array();
    }

    public function get_default_classes()
    {
        return $this->default_classes;
    }

    public function get_dashboard_counts()
    {
        return array(
            'accounts' => $this->count_table('chart_of_accounts'),
            'entries' => $this->count_table('ecritures_comptables'),
            'journals' => $this->count_table('journaux_auxiliaires'),
            'tiers' => $this->count_table('ohada_tiers'),
        );
    }

    public function count_table($table)
    {
        if (!$this->table_exists($table)) {
            return 0;
        }

        $this->db->from($table);
        $this->scope_table($table);
        $this->deleted_clause($table);
        return (int) $this->db->count_all_results();
    }

    public function get_accounts($search = '', $classe = '')
    {
        if (!$this->table_exists('chart_of_accounts')) {
            return array();
        }

        $this->db->from('chart_of_accounts');
        $this->scope_table('chart_of_accounts');
        $this->deleted_clause('chart_of_accounts');

        if ($search !== '') {
            $this->db->group_start();
            $this->db->like('numero_compte', $search);
            $this->db->or_like('libelle_compte', $search);
            $this->db->group_end();
        }

        if ($classe !== '') {
            $this->db->where('classe', $classe);
        }

        $this->db->order_by('numero_compte', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_account($id)
    {
        $this->db->from('chart_of_accounts');
        $this->db->where('id', (int) $id);
        $this->scope_table('chart_of_accounts');
        $this->deleted_clause('chart_of_accounts');
        return $this->db->get()->row_array();
    }

    public function save_account(array $data, $id = null)
    {
        $payload = array(
            'entreprise_id' => $this->get_entreprise_id(),
            'numero_compte' => trim($data['numero_compte']),
            'libelle_compte' => trim($data['libelle_compte']),
            'classe' => trim($data['classe']),
            'type_compte' => trim($data['type_compte']),
            'compte_parent' => trim($data['compte_parent']),
            'nature' => trim($data['nature']),
            'allow_posting' => !empty($data['allow_posting']) ? 1 : 0,
            'status' => !empty($data['status']) ? $data['status'] : 'active',
            'updated_at' => date('Y-m-d H:i:s'),
        );

        if ($id) {
            $this->db->where('id', (int) $id);
            $this->scope_table('chart_of_accounts');
            return $this->db->update('chart_of_accounts', $payload);
        }

        $payload['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('chart_of_accounts', $payload);
    }

    public function soft_delete($table, $id)
    {
        if (!$this->table_exists($table)) {
            return false;
        }

        $data = array('updated_at' => date('Y-m-d H:i:s'));
        if ($this->column_exists($table, 'deleted')) {
            $data['deleted'] = 1;
        }

        $this->db->where('id', (int) $id);
        $this->scope_table($table);
        return $this->db->update($table, $data);
    }

    public function get_journals()
    {
        if (!$this->table_exists('journaux_auxiliaires')) {
            return array();
        }

        $this->db->from('journaux_auxiliaires');
        $this->scope_table('journaux_auxiliaires');
        $this->deleted_clause('journaux_auxiliaires');
        $this->db->order_by('code', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_journal_entries(array $filters = array())
    {
        return $this->build_report_entries($filters);
    }

    public function add_journal_entry(array $data)
    {
        $montant = (float) $data['montant'];
        $payload = array(
            'entreprise_id' => $this->get_entreprise_id(),
            'journal_id' => (int) $data['journal_id'],
            'exercice_id' => !empty($data['exercice_id']) ? (int) $data['exercice_id'] : null,
            'tier_id' => !empty($data['tier_id']) ? (int) $data['tier_id'] : null,
            'analytic_id' => !empty($data['analytic_id']) ? (int) $data['analytic_id'] : null,
            'compte_debit' => trim($data['compte_debit']),
            'compte_credit' => trim($data['compte_credit']),
            'montant' => $montant,
            'montant_debit' => $montant,
            'montant_credit' => $montant,
            'date_ecriture' => $data['date_ecriture'],
            'libelle' => trim($data['libelle']),
            'reference_piece' => trim($data['reference_piece']),
            'piece_justificative' => trim($data['piece_justificative']),
            'user_id' => $this->get_user_id(),
            'status' => !empty($data['status']) ? $data['status'] : 'posted',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );

        return $this->db->insert('ecritures_comptables', $payload);
    }

    public function get_entries_totals(array $filters = array())
    {
        return $this->get_entry_totals(
            isset($filters['date_debut']) ? $filters['date_debut'] : null,
            isset($filters['date_fin']) ? $filters['date_fin'] : null,
            isset($filters['journal_id']) ? $filters['journal_id'] : null,
            isset($filters['compte']) ? (string) $filters['compte'] : ''
        );
    }

    public function get_balance_generale_report($date_debut, $date_fin, $classe = '', $include_zero = false)
    {
        $rows = $this->build_account_summary_rows($date_debut, $date_fin, $classe, $include_zero);
        $totals = $this->summarize_account_rows($rows);

        return array(
            'rows' => $rows,
            'totals' => $totals,
            'stats' => array(
                'total_comptes' => count($rows),
                'total_mouvements' => $this->count_mouvements($date_debut, $date_fin),
                'total_ecritures' => $this->count_ecritures($date_debut, $date_fin),
                'equilibre' => abs($totals['mouvement_debit'] - $totals['mouvement_credit']) < 0.0001,
            ),
        );
    }

    public function get_balance_generale_data($date_debut, $date_fin, $classe = '')
    {
        $report = $this->get_balance_generale_report($date_debut, $date_fin, $classe);
        return $report['rows'];
    }

    public function get_balance_auxiliaire_data($date_debut, $date_fin)
    {
        if (!$this->table_exists('ohada_tiers')) {
            return array();
        }

        $tiers = $this->get_tiers();
        $tier_rows = array();
        foreach ($tiers as $tier) {
            $tier_rows[(int) $tier['id']] = array(
                'id' => (int) $tier['id'],
                'code' => $tier['code'],
                'libelle' => $tier['libelle'],
                'type' => $tier['type'],
                'debit' => 0.0,
                'credit' => 0.0,
                'solde' => 0.0,
            );
        }

        $lines = $this->get_report_lines(array(
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'sort' => 'ASC',
        ));

        foreach ($lines as $line) {
            $tier_id = isset($line['tier_id']) ? (int) $line['tier_id'] : 0;
            if ($tier_id === 0 || !isset($tier_rows[$tier_id])) {
                continue;
            }

            $tier_rows[$tier_id]['debit'] += isset($line['debit']) ? (float) $line['debit'] : 0.0;
            $tier_rows[$tier_id]['credit'] += isset($line['credit']) ? (float) $line['credit'] : 0.0;
            $tier_rows[$tier_id]['solde'] = $tier_rows[$tier_id]['debit'] - $tier_rows[$tier_id]['credit'];
        }

        $rows = array();
        foreach ($tier_rows as $row) {
            if (abs($row['debit']) < 0.0001 && abs($row['credit']) < 0.0001) {
                continue;
            }
            $rows[] = $row;
        }

        return $rows;
    }

    public function get_grand_livre_data($date_debut, $date_fin, $compte = '')
    {
        $report = $this->get_grand_livre_report($date_debut, $date_fin, $compte);
        return $report['rows'];
    }

    public function get_grand_livre_report($date_debut, $date_fin, $compte = '')
    {
        $lines = $this->get_report_lines(array(
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'compte' => $compte,
            'sort' => 'ASC',
        ));
        $summaries = $this->build_account_summary_rows($date_debut, $date_fin, '', true);
        $accounts = array();
        foreach ($summaries as $summary) {
            $accounts[$summary['compte']] = $summary;
        }

        $running_balances = array();
        $rows = array();
        foreach ($lines as $line) {
            $account_number = isset($line['account']) ? (string) $line['account'] : '';
            if ($account_number === '') {
                continue;
            }

            if (!isset($running_balances[$account_number])) {
                $opening_balance = isset($accounts[$account_number]['opening_balance']) ? (float) $accounts[$account_number]['opening_balance'] : 0.0;
                $running_balances[$account_number] = $opening_balance;
                $rows[] = array(
                    'type' => 'opening',
                    'account' => $account_number,
                    'account_label' => isset($accounts[$account_number]['libelle']) ? $accounts[$account_number]['libelle'] : 'Compte',
                    'date' => $date_debut,
                    'journal' => 'AN',
                    'libelle' => 'Solde d ouverture',
                    'counterpart' => '',
                    'piece' => '',
                    'debit' => $opening_balance > 0 ? $opening_balance : 0.0,
                    'credit' => $opening_balance < 0 ? abs($opening_balance) : 0.0,
                    'running_balance' => $opening_balance,
                );
            }

            $signed_amount = ((float) (isset($line['debit']) ? $line['debit'] : 0.0)) - ((float) (isset($line['credit']) ? $line['credit'] : 0.0));
            $running_balances[$account_number] += $signed_amount;
            $rows[] = array(
                'type' => 'movement',
                'account' => $account_number,
                'account_label' => isset($accounts[$account_number]['libelle']) ? $accounts[$account_number]['libelle'] : 'Compte',
                'date' => isset($line['date_ecriture']) ? $line['date_ecriture'] : '',
                'journal' => isset($line['journal_code']) ? $line['journal_code'] : '',
                'libelle' => isset($line['libelle']) ? $line['libelle'] : '',
                'counterpart' => isset($line['counterpart']) ? $line['counterpart'] : '',
                'piece' => isset($line['reference_piece']) ? $line['reference_piece'] : '',
                'debit' => isset($line['debit']) ? (float) $line['debit'] : 0.0,
                'credit' => isset($line['credit']) ? (float) $line['credit'] : 0.0,
                'running_balance' => $running_balances[$account_number],
            );
        }

        return array(
            'rows' => $rows,
            'totals' => $this->get_entry_totals($date_debut, $date_fin, null, $compte),
            'account_count' => count($running_balances),
        );
    }

    public function get_bilan_data($date_fin)
    {
        $report = $this->get_bilan_report($date_fin);
        return $report['rows'];
    }

    public function get_bilan_report($date_fin)
    {
        $date_debut = date('Y-01-01', strtotime($date_fin));
        $summaries = $this->build_account_summary_rows($date_debut, $date_fin, '', false);
        $sections = array(
            'actif_immobilise' => array('label' => 'Actif immobilise', 'total' => 0.0),
            'stocks' => array('label' => 'Stocks et en-cours', 'total' => 0.0),
            'creances' => array('label' => 'Creances et emplois assimiles', 'total' => 0.0),
            'tresorerie_actif' => array('label' => 'Tresorerie actif', 'total' => 0.0),
            'capitaux' => array('label' => 'Capitaux propres et ressources durables', 'total' => 0.0),
            'dettes' => array('label' => 'Dettes et passifs circulants', 'total' => 0.0),
            'tresorerie_passif' => array('label' => 'Tresorerie passif', 'total' => 0.0),
        );
        $totals = array('actif' => 0.0, 'passif' => 0.0);
        $rows = array();

        foreach ($summaries as $row) {
            $classe = isset($row['classe']) ? (string) $row['classe'] : '';
            if (!in_array($classe, array('1', '2', '3', '4', '5'), true)) {
                continue;
            }

            $classification = $this->classify_balance_sheet_account($row);
            if ($classification === null) {
                continue;
            }

            $amount = abs((float) $row['closing_balance']);
            if ($amount < 0.0001) {
                continue;
            }

            $sections[$classification['section']]['total'] += $amount;
            $totals[$classification['side']] += $amount;
            $rows[] = array(
                'section' => $sections[$classification['section']]['label'],
                'side' => $classification['side'],
                'compte' => $row['compte'],
                'libelle' => $row['libelle'],
                'classe' => $row['classe'],
                'ouverture' => $row['opening_balance'],
                'mouvement' => $row['net_movement'],
                'cloture' => $row['closing_balance'],
                'actif' => $classification['side'] === 'actif' ? $amount : 0.0,
                'passif' => $classification['side'] === 'passif' ? $amount : 0.0,
            );
        }

        return array(
            'rows' => $rows,
            'sections' => $sections,
            'totals' => $totals,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
        );
    }

    public function get_compte_resultat_data($date_debut, $date_fin)
    {
        $report = $this->get_compte_resultat_report($date_debut, $date_fin);
        return $report['rows'];
    }

    public function get_compte_resultat_report($date_debut, $date_fin)
    {
        $summaries = $this->build_account_summary_rows($date_debut, $date_fin, '', false);
        $rows = array();
        $sections = array();
        $totals = array('charges' => 0.0, 'produits' => 0.0);

        foreach ($summaries as $row) {
            $classe = isset($row['classe']) ? (string) $row['classe'] : '';
            if (!in_array($classe, array('6', '7', '8'), true)) {
                continue;
            }

            $classification = $this->classify_income_statement_account($row);
            if ($classification['amount'] < 0.0001) {
                continue;
            }

            if (!isset($sections[$classification['section']])) {
                $sections[$classification['section']] = array(
                    'label' => $classification['label'],
                    'type' => $classification['type'],
                    'total' => 0.0,
                );
            }

            $sections[$classification['section']]['total'] += $classification['amount'];
            $totals[$classification['type'] === 'charge' ? 'charges' : 'produits'] += $classification['amount'];
            $rows[] = array(
                'section' => $classification['label'],
                'compte' => $row['compte'],
                'libelle' => $row['libelle'],
                'charges' => $classification['type'] === 'charge' ? $classification['amount'] : 0.0,
                'produits' => $classification['type'] === 'produit' ? $classification['amount'] : 0.0,
                'solde' => $classification['type'] === 'produit' ? $classification['amount'] : -$classification['amount'],
            );
        }

        return array(
            'rows' => $rows,
            'sections' => $sections,
            'totals' => $totals,
            'sig' => $this->build_sig_indicators($rows),
        );
    }

    public function get_tafire_data($date_debut, $date_fin)
    {
        $report = $this->get_tafire_report($date_debut, $date_fin);
        return $report['rows'];
    }

    public function get_tafire_report($date_debut, $date_fin)
    {
        $summaries = $this->build_account_summary_rows($date_debut, $date_fin, '', false);
        $resultat = $this->get_compte_resultat_report($date_debut, $date_fin);
        $lines = array(
            'ressources_durables' => array('nature' => 'Ressources durables mobilisees', 'emploi' => 0.0, 'ressource' => 0.0),
            'investissements' => array('nature' => 'Investissements et immobilisations', 'emploi' => 0.0, 'ressource' => 0.0),
            'stocks' => array('nature' => 'Variation des stocks', 'emploi' => 0.0, 'ressource' => 0.0),
            'creances' => array('nature' => 'Variation des creances', 'emploi' => 0.0, 'ressource' => 0.0),
            'dettes' => array('nature' => 'Variation des dettes', 'emploi' => 0.0, 'ressource' => 0.0),
            'tresorerie' => array('nature' => 'Variation nette de tresorerie', 'emploi' => 0.0, 'ressource' => 0.0),
            'resultat' => array('nature' => 'Resultat net de la periode', 'emploi' => 0.0, 'ressource' => 0.0),
        );

        foreach ($summaries as $row) {
            $delta = (float) $row['net_movement'];
            if (abs($delta) < 0.0001) {
                continue;
            }

            $classe = isset($row['classe']) ? (string) $row['classe'] : '';
            if ($classe === '1') {
                $this->add_delta_to_tafire_line($lines['ressources_durables'], -$delta);
            } elseif ($classe === '2') {
                $this->add_delta_to_tafire_line($lines['investissements'], $delta);
            } elseif ($classe === '3') {
                $this->add_delta_to_tafire_line($lines['stocks'], $delta);
            } elseif ($classe === '4') {
                $closing_balance = (float) $row['closing_balance'];
                $this->add_delta_to_tafire_line($closing_balance >= 0 ? $lines['creances'] : $lines['dettes'], $closing_balance >= 0 ? $delta : -$delta);
            } elseif ($classe === '5') {
                $this->add_delta_to_tafire_line($lines['tresorerie'], $delta);
            }
        }

        $this->add_delta_to_tafire_line($lines['resultat'], -(float) $resultat['sig']['resultat_net']);

        $rows = array_values(array_filter($lines, function ($line) {
            return abs($line['emploi']) >= 0.0001 || abs($line['ressource']) >= 0.0001;
        }));

        return array(
            'rows' => $rows,
            'totals' => array(
                'emplois' => array_sum(array_map(function ($line) {
                    return $line['emploi'];
                }, $rows)),
                'ressources' => array_sum(array_map(function ($line) {
                    return $line['ressource'];
                }, $rows)),
            ),
        );
    }

    protected function build_account_summary_rows($date_debut, $date_fin, $classe = '', $include_zero = false)
    {
        $accounts = $this->build_report_account_catalog($date_fin, $classe);
        $opening_cutoff = $this->get_previous_date($date_debut);
        $opening_amounts = $this->aggregate_report_line_amounts(null, $opening_cutoff);
        $period_amounts = $this->aggregate_report_line_amounts($date_debut, $date_fin);
        $rows = array();

        foreach ($accounts as $account_number => $account) {
            if ($classe !== '' && isset($account['classe']) && (string) $account['classe'] !== (string) $classe) {
                continue;
            }

            $opening_debit_total = isset($opening_amounts[$account_number]['debit']) ? (float) $opening_amounts[$account_number]['debit'] : 0.0;
            $opening_credit_total = isset($opening_amounts[$account_number]['credit']) ? (float) $opening_amounts[$account_number]['credit'] : 0.0;
            $period_debit_total = isset($period_amounts[$account_number]['debit']) ? (float) $period_amounts[$account_number]['debit'] : 0.0;
            $period_credit_total = isset($period_amounts[$account_number]['credit']) ? (float) $period_amounts[$account_number]['credit'] : 0.0;
            $opening_balance = $opening_debit_total - $opening_credit_total;
            $closing_balance = $opening_balance + $period_debit_total - $period_credit_total;

            if (
                !$include_zero
                && abs($opening_balance) < 0.0001
                && abs($period_debit_total) < 0.0001
                && abs($period_credit_total) < 0.0001
                && abs($closing_balance) < 0.0001
            ) {
                continue;
            }

            $rows[] = array(
                'compte' => $account_number,
                'libelle' => $account['libelle'],
                'classe' => $account['classe'],
                'classe_label' => $this->get_class_label($account['classe']),
                'type_compte' => $account['type_compte'],
                'nature' => $account['nature'],
                'opening_balance' => $opening_balance,
                'solde_ouverture_debit' => $opening_balance > 0 ? $opening_balance : 0.0,
                'solde_ouverture_credit' => $opening_balance < 0 ? abs($opening_balance) : 0.0,
                'mouvement_debit' => $period_debit_total,
                'mouvement_credit' => $period_credit_total,
                'total_debit' => $period_debit_total,
                'total_credit' => $period_credit_total,
                'net_movement' => $period_debit_total - $period_credit_total,
                'closing_balance' => $closing_balance,
                'solde_cloture_debit' => $closing_balance > 0 ? $closing_balance : 0.0,
                'solde_cloture_credit' => $closing_balance < 0 ? abs($closing_balance) : 0.0,
                'solde_debiteur' => $closing_balance > 0 ? $closing_balance : 0.0,
                'solde_crediteur' => $closing_balance < 0 ? abs($closing_balance) : 0.0,
            );
        }

        usort($rows, function ($left, $right) {
            return strnatcmp($left['compte'], $right['compte']);
        });

        return $rows;
    }

    protected function summarize_account_rows(array $rows)
    {
        $totals = array(
            'ouverture_debit' => 0.0,
            'ouverture_credit' => 0.0,
            'mouvement_debit' => 0.0,
            'mouvement_credit' => 0.0,
            'cloture_debit' => 0.0,
            'cloture_credit' => 0.0,
        );

        foreach ($rows as $row) {
            $totals['ouverture_debit'] += (float) $row['solde_ouverture_debit'];
            $totals['ouverture_credit'] += (float) $row['solde_ouverture_credit'];
            $totals['mouvement_debit'] += (float) $row['mouvement_debit'];
            $totals['mouvement_credit'] += (float) $row['mouvement_credit'];
            $totals['cloture_debit'] += (float) $row['solde_cloture_debit'];
            $totals['cloture_credit'] += (float) $row['solde_cloture_credit'];
        }

        return $totals;
    }

    protected function build_report_account_catalog($date_fin = null, $classe = '')
    {
        $catalog = array();
        foreach ($this->get_accounts('', $classe) as $account) {
            $catalog[$account['numero_compte']] = array(
                'libelle' => $account['libelle_compte'],
                'classe' => $account['classe'],
                'type_compte' => isset($account['type_compte']) ? $account['type_compte'] : '',
                'nature' => isset($account['nature']) ? $account['nature'] : $this->default_nature_for_class($account['classe']),
            );
        }

        foreach ($this->get_distinct_entry_accounts($date_fin) as $account_number) {
            if (isset($catalog[$account_number])) {
                continue;
            }

            $derived_class = substr((string) $account_number, 0, 1);
            if ($classe !== '' && $derived_class !== (string) $classe) {
                continue;
            }

            $catalog[$account_number] = array(
                'libelle' => 'Compte non parametre',
                'classe' => $derived_class,
                'type_compte' => $derived_class >= '6' ? 'resultat' : 'bilan',
                'nature' => $this->default_nature_for_class($derived_class),
            );
        }

        return $catalog;
    }

    protected function get_distinct_entry_accounts($date_fin = null)
    {
        $accounts = array();
        foreach ($this->get_report_lines(array('date_fin' => $date_fin)) as $line) {
            if (!empty($line['account'])) {
                $accounts[$line['account']] = $line['account'];
            }
        }

        return array_values($accounts);
    }

    protected function aggregate_account_amounts($field, $date_debut = null, $date_fin = null, $journal_id = null, $compte_filter = '')
    {
        $map = array();
        $side = $field === 'compte_credit' ? 'credit' : 'debit';
        foreach ($this->get_report_lines(array(
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'journal_id' => $journal_id,
            'compte' => $compte_filter,
        )) as $line) {
            $account_number = isset($line['account']) ? (string) $line['account'] : '';
            $amount = isset($line[$side]) ? (float) $line[$side] : 0.0;
            if ($account_number === '' || $amount <= 0) {
                continue;
            }

            if (!isset($map[$account_number])) {
                $map[$account_number] = 0.0;
            }
            $map[$account_number] += $amount;
        }

        return $map;
    }

    protected function get_entry_totals($date_debut = null, $date_fin = null, $journal_id = null, $compte_filter = '')
    {
        return array(
            'debit' => array_sum($this->aggregate_account_amounts('compte_debit', $date_debut, $date_fin, $journal_id, $compte_filter)),
            'credit' => array_sum($this->aggregate_account_amounts('compte_credit', $date_debut, $date_fin, $journal_id, $compte_filter)),
        );
    }

    protected function get_entry_amount_expression($field, $alias = 'e')
    {
        if ($field === 'compte_debit' && $this->column_exists('ecritures_comptables', 'montant_debit')) {
            return 'COALESCE(NULLIF(' . $alias . '.montant_debit, 0), ' . $alias . '.montant, 0)';
        }
        if ($field === 'compte_credit' && $this->column_exists('ecritures_comptables', 'montant_credit')) {
            return 'COALESCE(NULLIF(' . $alias . '.montant_credit, 0), ' . $alias . '.montant, 0)';
        }
        return 'COALESCE(' . $alias . '.montant, 0)';
    }

    protected function apply_entry_report_scope($alias = 'e')
    {
        $this->scope_table('ecritures_comptables', $alias);
        $this->deleted_clause('ecritures_comptables', $alias);
        $this->apply_report_status_filter('ecritures_comptables', $alias);
    }

    protected function apply_report_status_filter($table, $alias = '')
    {
        if (!$this->column_exists($table, 'status')) {
            return;
        }

        $prefix = $alias !== '' ? $alias . '.' : '';
        $this->db->group_start();
        $this->db->where($prefix . 'status IS NULL', null, false);
        $this->db->or_where_not_in($prefix . 'status', array('draft', 'brouillon', 'annule', 'cancelled'));
        $this->db->group_end();
    }

    protected function build_report_entries(array $filters = array())
    {
        $entry_filters = $filters;
        $entry_filters['compte'] = '';
        $groups = $this->group_report_lines($this->get_report_lines($entry_filters));
        $entries = array();
        $compte_filter = isset($filters['compte']) ? trim((string) $filters['compte']) : '';
        foreach ($groups as $group) {
            if ($compte_filter !== '') {
                $matches_filter = false;
                foreach (array_merge(array_keys($group['debit_accounts']), array_keys($group['credit_accounts'])) as $account) {
                    if (strpos((string) $account, $compte_filter) === 0) {
                        $matches_filter = true;
                        break;
                    }
                }
                if (!$matches_filter) {
                    continue;
                }
            }

            $entries[] = array(
                'date_ecriture' => $group['date_ecriture'],
                'journal_code' => $group['journal_code'],
                'journal_libelle' => $group['journal_libelle'],
                'compte_debit' => implode(', ', array_keys($group['debit_accounts'])),
                'compte_credit' => implode(', ', array_keys($group['credit_accounts'])),
                'libelle' => $group['libelle'],
                'reference_piece' => $group['reference_piece'],
                'montant' => max($group['total_debit'], $group['total_credit']),
                'montant_debit' => $group['total_debit'],
                'montant_credit' => $group['total_credit'],
                'tier_id' => $group['tier_id'],
                'tiers_code' => $group['tiers_code'],
                'tiers_libelle' => $group['tiers_libelle'],
                'analytic_id' => $group['analytic_id'],
                'analytique_code' => $group['analytique_code'],
                'source' => $group['source'],
            );
        }

        $sort_direction = (!empty($filters['sort']) && strtoupper((string) $filters['sort']) === 'ASC') ? 'ASC' : 'DESC';
        usort($entries, function ($left, $right) use ($sort_direction) {
            $compare = strcmp((string) $left['date_ecriture'], (string) $right['date_ecriture']);
            if ($compare === 0) {
                $compare = strcmp((string) $left['reference_piece'], (string) $right['reference_piece']);
            }
            return $sort_direction === 'ASC' ? $compare : -$compare;
        });

        return $entries;
    }

    protected function get_report_lines(array $filters = array())
    {
        $normalized_filters = array(
            'date_debut' => !empty($filters['date_debut']) ? $filters['date_debut'] : null,
            'date_fin' => !empty($filters['date_fin']) ? $filters['date_fin'] : null,
            'journal_id' => !empty($filters['journal_id']) ? (int) $filters['journal_id'] : 0,
            'compte' => isset($filters['compte']) ? trim((string) $filters['compte']) : '',
            'sort' => (!empty($filters['sort']) && strtoupper((string) $filters['sort']) === 'ASC') ? 'ASC' : 'DESC',
        );
        $cache_key = md5(serialize($normalized_filters));
        if (isset($this->report_lines_cache[$cache_key])) {
            return $this->report_lines_cache[$cache_key];
        }

        $native_lines = $this->fetch_native_ohada_report_lines($normalized_filters);
        $legacy_lines = $this->fetch_legacy_accounting_report_lines($normalized_filters);
        $existing_groups = $this->group_report_lines(array_merge($native_lines, $legacy_lines));

        $synthetic_lines = array_merge(
            $this->fetch_cash_opening_report_lines($normalized_filters, $existing_groups),
            $this->fetch_cash_operation_report_lines($normalized_filters, $existing_groups),
            $this->fetch_bank_opening_report_lines($normalized_filters),
            $this->fetch_bank_transaction_report_lines($normalized_filters, $existing_groups)
        );

        $lines = $this->attach_counterparts_to_lines(array_merge($native_lines, $legacy_lines, $synthetic_lines));
        $selected_journal_code = $normalized_filters['journal_id'] > 0 ? $this->get_journal_code_by_id($normalized_filters['journal_id']) : '';
        if ($selected_journal_code !== '') {
            $lines = array_values(array_filter($lines, function ($line) use ($selected_journal_code) {
                return isset($line['journal_code']) && $line['journal_code'] === $selected_journal_code;
            }));
        }
        if ($normalized_filters['compte'] !== '') {
            $lines = array_values(array_filter($lines, function ($line) use ($normalized_filters) {
                return isset($line['account']) && strpos((string) $line['account'], $normalized_filters['compte']) === 0;
            }));
        }

        usort($lines, function ($left, $right) use ($normalized_filters) {
            $compare = strcmp((string) $left['date_ecriture'], (string) $right['date_ecriture']);
            if ($compare === 0) {
                $compare = strcmp((string) $left['group_key'], (string) $right['group_key']);
            }
            if ($compare === 0) {
                $compare = strcmp((string) $left['account'], (string) $right['account']);
            }
            return $normalized_filters['sort'] === 'ASC' ? $compare : -$compare;
        });

        $this->report_lines_cache[$cache_key] = $lines;
        return $lines;
    }

    protected function fetch_native_ohada_report_lines(array $filters)
    {
        if (!$this->table_exists('ecritures_comptables')) {
            return array();
        }

        $this->db->select('e.*, j.code as journal_code, j.libelle as journal_libelle, t.code as tiers_code, t.libelle as tiers_libelle, a.code as analytique_code');
        $this->db->from('ecritures_comptables e');
        if ($this->table_exists('journaux_auxiliaires')) {
            $this->db->join('journaux_auxiliaires j', 'j.id = e.journal_id', 'left');
        }
        if ($this->table_exists('ohada_tiers')) {
            $this->db->join('ohada_tiers t', 't.id = e.tier_id', 'left');
        }
        if ($this->table_exists('ohada_analytique')) {
            $this->db->join('ohada_analytique a', 'a.id = e.analytic_id', 'left');
        }

        $this->apply_entry_report_scope('e');
        if (!empty($filters['date_debut'])) {
            $this->db->where('e.date_ecriture >=', $filters['date_debut']);
        }
        if (!empty($filters['date_fin'])) {
            $this->db->where('e.date_ecriture <=', $filters['date_fin']);
        }
        if (!empty($filters['journal_id'])) {
            $this->db->where('e.journal_id', (int) $filters['journal_id']);
        }
        if ($filters['compte'] !== '') {
            $this->db->group_start();
            $this->db->like('e.compte_debit', $filters['compte'], 'after');
            $this->db->or_like('e.compte_credit', $filters['compte'], 'after');
            $this->db->group_end();
        }

        $rows = $this->db->get()->result_array();
        $lines = array();
        foreach ($rows as $row) {
            $debit_amount = $this->extract_entry_debit_amount($row);
            $credit_amount = $this->extract_entry_credit_amount($row);
            $lines[] = $this->make_report_line(array(
                'group_key' => 'ohada|' . (int) $row['id'],
                'date_ecriture' => $row['date_ecriture'],
                'journal_code' => isset($row['journal_code']) ? $row['journal_code'] : 'OH',
                'journal_libelle' => isset($row['journal_libelle']) ? $row['journal_libelle'] : 'Journal OHADA',
                'account' => $this->normalize_account_number(isset($row['compte_debit']) ? $row['compte_debit'] : ''),
                'debit' => $debit_amount,
                'credit' => 0.0,
                'libelle' => isset($row['libelle']) ? $row['libelle'] : '',
                'reference_piece' => isset($row['reference_piece']) ? $row['reference_piece'] : '',
                'tier_id' => isset($row['tier_id']) ? (int) $row['tier_id'] : 0,
                'tiers_code' => isset($row['tiers_code']) ? $row['tiers_code'] : '',
                'tiers_libelle' => isset($row['tiers_libelle']) ? $row['tiers_libelle'] : '',
                'analytic_id' => isset($row['analytic_id']) ? (int) $row['analytic_id'] : 0,
                'analytique_code' => isset($row['analytique_code']) ? $row['analytique_code'] : '',
                'source' => 'ecritures_comptables',
                'source_id' => (int) $row['id'],
            ));
            $lines[] = $this->make_report_line(array(
                'group_key' => 'ohada|' . (int) $row['id'],
                'date_ecriture' => $row['date_ecriture'],
                'journal_code' => isset($row['journal_code']) ? $row['journal_code'] : 'OH',
                'journal_libelle' => isset($row['journal_libelle']) ? $row['journal_libelle'] : 'Journal OHADA',
                'account' => $this->normalize_account_number(isset($row['compte_credit']) ? $row['compte_credit'] : ''),
                'debit' => 0.0,
                'credit' => $credit_amount,
                'libelle' => isset($row['libelle']) ? $row['libelle'] : '',
                'reference_piece' => isset($row['reference_piece']) ? $row['reference_piece'] : '',
                'tier_id' => isset($row['tier_id']) ? (int) $row['tier_id'] : 0,
                'tiers_code' => isset($row['tiers_code']) ? $row['tiers_code'] : '',
                'tiers_libelle' => isset($row['tiers_libelle']) ? $row['tiers_libelle'] : '',
                'analytic_id' => isset($row['analytic_id']) ? (int) $row['analytic_id'] : 0,
                'analytique_code' => isset($row['analytique_code']) ? $row['analytique_code'] : '',
                'source' => 'ecritures_comptables',
                'source_id' => (int) $row['id'],
            ));
        }

        return $lines;
    }

    protected function fetch_legacy_accounting_report_lines(array $filters)
    {
        if (!$this->table_exists('accounting_entries')) {
            return array();
        }

        $this->db->from('accounting_entries');
        $this->scope_table('accounting_entries');
        if (!empty($filters['date_debut'])) {
            $this->db->where('date >=', $filters['date_debut']);
        }
        if (!empty($filters['date_fin'])) {
            $this->db->where('date <=', $filters['date_fin']);
        }
        if ($filters['compte'] !== '') {
            $this->db->like('account', $filters['compte'], 'after');
        }

        $rows = $this->db->get()->result_array();
        $grouped_rows = array();
        foreach ($rows as $row) {
            $group_key = 'legacy|' . $row['date'] . '|' . (isset($row['invoice_id']) ? (string) $row['invoice_id'] : '0');
            if (!isset($grouped_rows[$group_key])) {
                $grouped_rows[$group_key] = array();
            }
            $grouped_rows[$group_key][] = $row;
        }

        $lines = array();
        foreach ($grouped_rows as $group_key => $group_rows) {
            $journal = $this->resolve_legacy_group_journal($group_rows);
            foreach ($group_rows as $row) {
                $account = $this->normalize_account_number(isset($row['account']) ? $row['account'] : '');
                if ($account === '') {
                    continue;
                }
                $lines[] = $this->make_report_line(array(
                    'group_key' => $group_key,
                    'date_ecriture' => $row['date'],
                    'journal_code' => $journal['code'],
                    'journal_libelle' => $journal['libelle'],
                    'account' => $account,
                    'debit' => isset($row['debit']) ? (float) $row['debit'] : 0.0,
                    'credit' => isset($row['credit']) ? (float) $row['credit'] : 0.0,
                    'libelle' => isset($row['description']) ? $row['description'] : '',
                    'reference_piece' => !empty($row['invoice_id']) ? 'LEG-' . $row['invoice_id'] : 'LEG-' . md5($group_key),
                    'source' => 'accounting_entries',
                    'source_id' => (int) $row['id'],
                    'invoice_id' => isset($row['invoice_id']) ? (int) $row['invoice_id'] : 0,
                ));
            }
        }

        return $lines;
    }

    protected function fetch_cash_opening_report_lines(array $filters, array $existing_groups)
    {
        if (!$this->table_exists('income')) {
            return array();
        }

        $cashboxes = $this->get_cashboxes();
        $operation_stats = $this->get_cash_operation_stats();
        $lines = array();
        foreach ($cashboxes as $cashbox) {
            $cashbox_id = (int) $cashbox['id'];
            $stats = isset($operation_stats[$cashbox_id]) ? $operation_stats[$cashbox_id] : array('net' => 0.0, 'first_date' => null);
            $current_balance = $this->to_float(isset($cashbox['amount_re']) ? $cashbox['amount_re'] : 0);
            $opening_amount = $current_balance - (float) $stats['net'];
            if (abs($opening_amount) < 0.0001) {
                $opening_amount = $this->to_float(isset($cashbox['amount']) ? $cashbox['amount'] : 0);
            }

            if (abs($opening_amount) < 0.0001 || $this->cash_opening_already_posted($cashbox_id, $existing_groups)) {
                continue;
            }

            $opening_date = $this->resolve_opening_date(
                isset($cashbox['date']) ? $cashbox['date'] : null,
                isset($cashbox['created_at']) ? $cashbox['created_at'] : null,
                isset($stats['first_date']) ? $stats['first_date'] : null
            );
            if (!empty($filters['date_debut']) && $opening_date < $filters['date_debut']) {
                continue;
            }
            if (!empty($filters['date_fin']) && $opening_date > $filters['date_fin']) {
                continue;
            }

            $treasury_account = !empty($cashbox['est_mobile_money']) ? $this->pick_preferred_account(array('5712', '571')) : $this->pick_preferred_account(array('5711', '571'));
            $lines = array_merge($lines, $this->build_synthetic_double_entry_lines(array(
                'group_key' => 'cash-opening|' . $cashbox_id,
                'date_ecriture' => $opening_date,
                'journal_code' => 'AN',
                'journal_libelle' => 'A nouveaux',
                'debit_account' => $treasury_account,
                'credit_account' => '101',
                'amount' => abs($opening_amount),
                'libelle' => 'Ouverture caisse - ' . (isset($cashbox['name']) ? $cashbox['name'] : ('Caisse ' . $cashbox_id)),
                'reference_piece' => 'CAI-OPEN-' . $cashbox_id,
                'source' => 'cash_opening',
                'source_id' => $cashbox_id,
            )));
        }

        return $lines;
    }

    protected function fetch_cash_operation_report_lines(array $filters, array $existing_groups)
    {
        if (!$this->table_exists('operation_caisse')) {
            return array();
        }

        $cashboxes = $this->index_rows_by_id($this->get_cashboxes());
        $this->db->from('operation_caisse');
        $this->scope_table('operation_caisse');
        if ($this->column_exists('operation_caisse', 'deleted')) {
            $this->db->where('deleted', 0);
        }
        if (!empty($filters['date_debut'])) {
            $this->db->where('date >=', $filters['date_debut'] . ' 00:00:00');
        }
        if (!empty($filters['date_fin'])) {
            $this->db->where('date <=', $filters['date_fin'] . ' 23:59:59');
        }
        $rows = $this->db->get()->result_array();

        $lines = array();
        foreach ($rows as $row) {
            $operation_id = (int) $row['id'];
            if ($this->cash_operation_already_posted($operation_id, $existing_groups)) {
                continue;
            }

            $is_entry = $this->is_cash_entry_operation($row);
            $amount = $this->to_float(isset($row['montant']) ? $row['montant'] : ($is_entry ? (isset($row['entree']) ? $row['entree'] : 0) : (isset($row['sortie']) ? $row['sortie'] : 0)));
            if ($amount <= 0) {
                continue;
            }

            $cashbox = isset($cashboxes[(int) $row['caisse_id']]) ? $cashboxes[(int) $row['caisse_id']] : array();
            $treasury_account = !empty($cashbox['est_mobile_money']) ? $this->pick_preferred_account(array('5712', '571')) : $this->pick_preferred_account(array('571', '5711'));
            $lines = array_merge($lines, $this->build_synthetic_double_entry_lines(array(
                'group_key' => 'cash-op|' . $operation_id,
                'date_ecriture' => $this->normalize_date_string(isset($row['date']) ? $row['date'] : null),
                'journal_code' => 'CAI',
                'journal_libelle' => 'Journal de caisse',
                'debit_account' => $is_entry ? $treasury_account : '6',
                'credit_account' => $is_entry ? '7' : $treasury_account,
                'amount' => $amount,
                'libelle' => ($is_entry ? 'Entree de caisse - ' : 'Sortie de caisse - ') . trim((string) (isset($row['designation']) ? $row['designation'] : 'Operation de caisse')),
                'reference_piece' => !empty($row['reference']) ? $row['reference'] : 'CAI-' . $operation_id,
                'source' => 'operation_caisse',
                'source_id' => $operation_id,
                'invoice_id' => $operation_id,
            )));
        }

        return $lines;
    }

    protected function fetch_bank_opening_report_lines(array $filters)
    {
        if (!$this->table_exists('banks')) {
            return array();
        }

        $banks = $this->get_bank_accounts();
        $bank_stats = $this->get_bank_transaction_stats();
        $lines = array();
        foreach ($banks as $bank) {
            $bank_id = (int) $bank['id'];
            $stats = isset($bank_stats[$bank_id]) ? $bank_stats[$bank_id] : array('net' => 0.0, 'first_date' => null);
            $current_balance = $this->to_float(isset($bank['balance']) ? $bank['balance'] : 0);
            $balance_re = $this->to_float(isset($bank['balance_re']) ? $bank['balance_re'] : 0);
            $opening_amount = abs($balance_re) > 0.0001 ? $balance_re : ($current_balance - (float) $stats['net']);
            if (abs($opening_amount) < 0.0001) {
                continue;
            }

            $opening_date = $this->resolve_opening_date(
                isset($bank['created_at']) ? $bank['created_at'] : null,
                isset($bank['created_at']) ? $bank['created_at'] : null,
                isset($stats['first_date']) ? $stats['first_date'] : null
            );
            if (!empty($filters['date_debut']) && $opening_date < $filters['date_debut']) {
                continue;
            }
            if (!empty($filters['date_fin']) && $opening_date > $filters['date_fin']) {
                continue;
            }

            $bank_account = $this->resolve_bank_account_number($bank_id, $bank['name']);
            $lines = array_merge($lines, $this->build_synthetic_double_entry_lines(array(
                'group_key' => 'bank-opening|' . $bank_id,
                'date_ecriture' => $opening_date,
                'journal_code' => 'AN',
                'journal_libelle' => 'A nouveaux',
                'debit_account' => $bank_account,
                'credit_account' => '101',
                'amount' => abs($opening_amount),
                'libelle' => 'Ouverture banque - ' . (isset($bank['name']) ? $bank['name'] : ('Banque ' . $bank_id)),
                'reference_piece' => 'BNK-OPEN-' . $bank_id,
                'source' => 'bank_opening',
                'source_id' => $bank_id,
            )));
        }

        return $lines;
    }

    protected function fetch_bank_transaction_report_lines(array $filters, array $existing_groups)
    {
        if (!$this->table_exists('bank')) {
            return array();
        }

        $banks = $this->index_rows_by_id($this->get_bank_accounts());
        $this->db->from('bank');
        $this->scope_table('bank');
        if ($this->column_exists('bank', 'deleted')) {
            $this->db->where('deleted', 1);
        }
        if (!empty($filters['date_debut'])) {
            $this->db->where('date >=', $filters['date_debut']);
        }
        if (!empty($filters['date_fin'])) {
            $this->db->where('date <=', $filters['date_fin']);
        }
        $rows = $this->db->get()->result_array();

        $lines = array();
        foreach ($rows as $row) {
            if ($this->bank_transaction_already_posted($row, $existing_groups)) {
                continue;
            }

            $amount = $this->to_float(isset($row['amount']) ? $row['amount'] : 0);
            if ($amount <= 0) {
                continue;
            }

            $is_credit = $this->is_bank_credit_transaction($row);
            $bank_id = isset($row['bank_id']) ? (int) $row['bank_id'] : 0;
            $bank = isset($banks[$bank_id]) ? $banks[$bank_id] : array();
            $bank_account = $this->resolve_bank_account_number($bank_id, isset($bank['name']) ? $bank['name'] : '');
            $transaction_label = trim((string) (isset($row['transaction_type']) ? $row['transaction_type'] : (isset($row['designation']) ? $row['designation'] : 'Mouvement')));
            $lines = array_merge($lines, $this->build_synthetic_double_entry_lines(array(
                'group_key' => 'bank-op|' . (int) $row['id'],
                'date_ecriture' => $this->normalize_date_string(isset($row['date']) ? $row['date'] : null),
                'journal_code' => 'BQ',
                'journal_libelle' => 'Journal de banque',
                'debit_account' => $is_credit ? $bank_account : '58',
                'credit_account' => $is_credit ? '58' : $bank_account,
                'amount' => $amount,
                'libelle' => 'Mouvement bancaire - ' . $transaction_label . ' - ' . trim((string) (isset($row['name']) ? $row['name'] : 'Banque')),
                'reference_piece' => !empty($row['reference']) ? $row['reference'] : 'BNK-' . (int) $row['id'],
                'source' => 'bank',
                'source_id' => (int) $row['id'],
            )));
        }

        return $lines;
    }

    protected function aggregate_report_line_amounts($date_debut = null, $date_fin = null)
    {
        $totals = array();
        foreach ($this->get_report_lines(array(
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
        )) as $line) {
            $account = isset($line['account']) ? (string) $line['account'] : '';
            if ($account === '') {
                continue;
            }
            if (!isset($totals[$account])) {
                $totals[$account] = array('debit' => 0.0, 'credit' => 0.0);
            }
            $totals[$account]['debit'] += isset($line['debit']) ? (float) $line['debit'] : 0.0;
            $totals[$account]['credit'] += isset($line['credit']) ? (float) $line['credit'] : 0.0;
        }

        return $totals;
    }

    protected function group_report_lines(array $lines)
    {
        $groups = array();
        foreach ($lines as $line) {
            $group_key = isset($line['group_key']) ? (string) $line['group_key'] : md5(serialize($line));
            if (!isset($groups[$group_key])) {
                $groups[$group_key] = array(
                    'group_key' => $group_key,
                    'date_ecriture' => isset($line['date_ecriture']) ? $line['date_ecriture'] : '',
                    'journal_code' => isset($line['journal_code']) ? $line['journal_code'] : '',
                    'journal_libelle' => isset($line['journal_libelle']) ? $line['journal_libelle'] : '',
                    'libelle' => isset($line['libelle']) ? $line['libelle'] : '',
                    'reference_piece' => isset($line['reference_piece']) ? $line['reference_piece'] : '',
                    'debit_accounts' => array(),
                    'credit_accounts' => array(),
                    'total_debit' => 0.0,
                    'total_credit' => 0.0,
                    'tier_id' => isset($line['tier_id']) ? (int) $line['tier_id'] : 0,
                    'tiers_code' => isset($line['tiers_code']) ? $line['tiers_code'] : '',
                    'tiers_libelle' => isset($line['tiers_libelle']) ? $line['tiers_libelle'] : '',
                    'analytic_id' => isset($line['analytic_id']) ? (int) $line['analytic_id'] : 0,
                    'analytique_code' => isset($line['analytique_code']) ? $line['analytique_code'] : '',
                    'source' => isset($line['source']) ? $line['source'] : '',
                    'invoice_ids' => array(),
                );
            }

            if (!empty($line['account']) && (float) $line['debit'] > 0) {
                $groups[$group_key]['debit_accounts'][$line['account']] = true;
            }
            if (!empty($line['account']) && (float) $line['credit'] > 0) {
                $groups[$group_key]['credit_accounts'][$line['account']] = true;
            }
            $groups[$group_key]['total_debit'] += isset($line['debit']) ? (float) $line['debit'] : 0.0;
            $groups[$group_key]['total_credit'] += isset($line['credit']) ? (float) $line['credit'] : 0.0;
            if (!empty($line['invoice_id'])) {
                $groups[$group_key]['invoice_ids'][(int) $line['invoice_id']] = true;
            }
        }

        return $groups;
    }

    protected function attach_counterparts_to_lines(array $lines)
    {
        $groups = array();
        foreach ($lines as $index => $line) {
            $group_key = isset($line['group_key']) ? (string) $line['group_key'] : (string) $index;
            if (!isset($groups[$group_key])) {
                $groups[$group_key] = array('debit' => array(), 'credit' => array());
            }
            if (!empty($line['account']) && (float) $line['debit'] > 0) {
                $groups[$group_key]['debit'][$line['account']] = true;
            }
            if (!empty($line['account']) && (float) $line['credit'] > 0) {
                $groups[$group_key]['credit'][$line['account']] = true;
            }
        }

        foreach ($lines as $index => $line) {
            $group_key = isset($line['group_key']) ? (string) $line['group_key'] : (string) $index;
            $counterpart_accounts = (float) $line['debit'] > 0 ? array_keys($groups[$group_key]['credit']) : array_keys($groups[$group_key]['debit']);
            $lines[$index]['counterpart'] = implode(', ', $counterpart_accounts);
        }

        return $lines;
    }

    protected function make_report_line(array $data)
    {
        return array(
            'group_key' => isset($data['group_key']) ? $data['group_key'] : md5(serialize($data)),
            'date_ecriture' => $this->normalize_date_string(isset($data['date_ecriture']) ? $data['date_ecriture'] : null),
            'journal_code' => isset($data['journal_code']) ? $data['journal_code'] : '',
            'journal_libelle' => isset($data['journal_libelle']) ? $data['journal_libelle'] : '',
            'account' => isset($data['account']) ? $this->normalize_account_number($data['account']) : '',
            'debit' => isset($data['debit']) ? (float) $data['debit'] : 0.0,
            'credit' => isset($data['credit']) ? (float) $data['credit'] : 0.0,
            'libelle' => isset($data['libelle']) ? $data['libelle'] : '',
            'reference_piece' => isset($data['reference_piece']) ? $data['reference_piece'] : '',
            'tier_id' => isset($data['tier_id']) ? (int) $data['tier_id'] : 0,
            'tiers_code' => isset($data['tiers_code']) ? $data['tiers_code'] : '',
            'tiers_libelle' => isset($data['tiers_libelle']) ? $data['tiers_libelle'] : '',
            'analytic_id' => isset($data['analytic_id']) ? (int) $data['analytic_id'] : 0,
            'analytique_code' => isset($data['analytique_code']) ? $data['analytique_code'] : '',
            'source' => isset($data['source']) ? $data['source'] : '',
            'source_id' => isset($data['source_id']) ? (int) $data['source_id'] : 0,
            'invoice_id' => isset($data['invoice_id']) ? (int) $data['invoice_id'] : 0,
        );
    }

    protected function build_synthetic_double_entry_lines(array $data)
    {
        $amount = isset($data['amount']) ? (float) $data['amount'] : 0.0;
        if ($amount <= 0) {
            return array();
        }

        return array(
            $this->make_report_line(array(
                'group_key' => $data['group_key'],
                'date_ecriture' => $data['date_ecriture'],
                'journal_code' => $data['journal_code'],
                'journal_libelle' => $data['journal_libelle'],
                'account' => $data['debit_account'],
                'debit' => $amount,
                'credit' => 0.0,
                'libelle' => $data['libelle'],
                'reference_piece' => $data['reference_piece'],
                'source' => $data['source'],
                'source_id' => isset($data['source_id']) ? (int) $data['source_id'] : 0,
                'invoice_id' => isset($data['invoice_id']) ? (int) $data['invoice_id'] : 0,
            )),
            $this->make_report_line(array(
                'group_key' => $data['group_key'],
                'date_ecriture' => $data['date_ecriture'],
                'journal_code' => $data['journal_code'],
                'journal_libelle' => $data['journal_libelle'],
                'account' => $data['credit_account'],
                'debit' => 0.0,
                'credit' => $amount,
                'libelle' => $data['libelle'],
                'reference_piece' => $data['reference_piece'],
                'source' => $data['source'],
                'source_id' => isset($data['source_id']) ? (int) $data['source_id'] : 0,
                'invoice_id' => isset($data['invoice_id']) ? (int) $data['invoice_id'] : 0,
            )),
        );
    }

    protected function get_cashboxes()
    {
        if (!$this->table_exists('income')) {
            return array();
        }

        $this->db->from('income');
        $this->scope_table('income');
        if ($this->column_exists('income', 'deleted')) {
            $this->db->where('deleted', 1);
        }
        if ($this->column_exists('income', 'type_operation')) {
            $this->db->where('type_operation', 'caisse');
        }
        return $this->db->get()->result_array();
    }

    protected function get_cash_operation_stats()
    {
        if (!$this->table_exists('operation_caisse')) {
            return array();
        }

        $this->db->select('caisse_id, MIN(DATE(date)) as first_date, SUM(COALESCE(entree, 0) - COALESCE(sortie, 0)) as net', false);
        $this->db->from('operation_caisse');
        $this->scope_table('operation_caisse');
        if ($this->column_exists('operation_caisse', 'deleted')) {
            $this->db->where('deleted', 0);
        }
        $this->db->group_by('caisse_id');

        $stats = array();
        foreach ($this->db->get()->result_array() as $row) {
            $stats[(int) $row['caisse_id']] = array(
                'first_date' => isset($row['first_date']) ? $row['first_date'] : null,
                'net' => isset($row['net']) ? (float) $row['net'] : 0.0,
            );
        }

        return $stats;
    }

    protected function get_bank_accounts()
    {
        if (!$this->table_exists('banks')) {
            return array();
        }

        $this->db->from('banks');
        $this->scope_table('banks');
        if ($this->column_exists('banks', 'status')) {
            $this->db->where('status', 1);
        }
        return $this->db->get()->result_array();
    }

    protected function get_bank_transaction_stats()
    {
        if (!$this->table_exists('bank')) {
            return array();
        }

        $this->db->select('bank_id, MIN(date) as first_date, SUM(CASE WHEN designation = "Crédit" OR transaction_type IN ("Dépôt", "Virement entrant") THEN amount ELSE -amount END) as net', false);
        $this->db->from('bank');
        $this->scope_table('bank');
        if ($this->column_exists('bank', 'deleted')) {
            $this->db->where('deleted', 1);
        }
        $this->db->group_by('bank_id');

        $stats = array();
        foreach ($this->db->get()->result_array() as $row) {
            $stats[(int) $row['bank_id']] = array(
                'first_date' => isset($row['first_date']) ? $row['first_date'] : null,
                'net' => isset($row['net']) ? (float) $row['net'] : 0.0,
            );
        }

        return $stats;
    }

    protected function resolve_legacy_group_journal(array $rows)
    {
        $has_cash = false;
        $has_bank = false;
        foreach ($rows as $row) {
            $account = $this->normalize_account_number(isset($row['account']) ? $row['account'] : '');
            if (strpos($account, '57') === 0) {
                $has_cash = true;
            }
            if (strpos($account, '51') === 0 || strpos($account, '52') === 0) {
                $has_bank = true;
            }
        }

        if ($has_cash) {
            return array('code' => 'CAI', 'libelle' => 'Journal de caisse');
        }
        if ($has_bank) {
            return array('code' => 'BQ', 'libelle' => 'Journal de banque');
        }

        return array('code' => 'LEG', 'libelle' => 'Ecritures legacy');
    }

    protected function normalize_account_number($value)
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }

        if (preg_match('/^([0-9A-Za-z]+)/', $value, $matches)) {
            return strtoupper($matches[1]);
        }

        return strtoupper($value);
    }

    protected function normalize_date_string($value)
    {
        if (empty($value)) {
            return '';
        }

        $timestamp = strtotime((string) $value);
        if ($timestamp === false) {
            return (string) $value;
        }

        return date('Y-m-d', $timestamp);
    }

    protected function to_float($value)
    {
        return (float) str_replace(',', '.', (string) $value);
    }

    protected function resolve_opening_date($primary_date = null, $created_at = null, $first_operation_date = null)
    {
        $candidate = $primary_date;
        if (empty($candidate)) {
            $candidate = $created_at;
        }
        if (!empty($first_operation_date)) {
            $candidate = date('Y-m-d', strtotime($this->normalize_date_string($first_operation_date) . ' -1 day'));
        }

        return $this->normalize_date_string($candidate ?: date('Y-m-d'));
    }

    protected function pick_preferred_account(array $candidates)
    {
        $known_accounts = $this->get_known_account_numbers();
        foreach ($candidates as $candidate) {
            if (isset($known_accounts[$candidate])) {
                return $candidate;
            }
        }

        return reset($candidates);
    }

    protected function get_known_account_numbers()
    {
        if ($this->known_account_numbers_cache !== null) {
            return $this->known_account_numbers_cache;
        }

        $accounts = array();
        if ($this->table_exists('chart_of_accounts')) {
            foreach ($this->get_accounts() as $account) {
                $number = $this->normalize_account_number(isset($account['numero_compte']) ? $account['numero_compte'] : '');
                if ($number !== '') {
                    $accounts[$number] = true;
                }
            }
        }
        if ($this->table_exists('accounting_entries')) {
            $this->db->distinct();
            $this->db->select('account');
            $this->db->from('accounting_entries');
            $this->scope_table('accounting_entries');
            foreach ($this->db->get()->result_array() as $row) {
                $number = $this->normalize_account_number(isset($row['account']) ? $row['account'] : '');
                if ($number !== '') {
                    $accounts[$number] = true;
                }
            }
        }

        $this->known_account_numbers_cache = $accounts;
        return $this->known_account_numbers_cache;
    }

    protected function resolve_bank_account_number($bank_id, $bank_name = '')
    {
        static $cache = array();
        $cache_key = (int) $bank_id . '|' . strtolower((string) $bank_name);
        if (isset($cache[$cache_key])) {
            return $cache[$cache_key];
        }

        $candidate = '512';
        if ($this->table_exists('bank') && $this->table_exists('accounting_entries') && $bank_id > 0) {
            $this->db->select('b.date, b.amount');
            $this->db->from('bank b');
            $this->db->where('b.bank_id', $bank_id);
            $this->scope_table('bank', 'b');
            if ($this->column_exists('bank', 'deleted')) {
                $this->db->where('b.deleted', 1);
            }
            $bank_rows = $this->db->get()->result_array();

            $scores = array();
            foreach ($bank_rows as $bank_row) {
                $this->db->select('account');
                $this->db->from('accounting_entries');
                $this->scope_table('accounting_entries');
                $this->db->where('date', $bank_row['date']);
                $this->db->group_start();
                $this->db->where('debit', $bank_row['amount']);
                $this->db->or_where('credit', $bank_row['amount']);
                $this->db->group_end();
                foreach ($this->db->get()->result_array() as $entry_row) {
                    $account = $this->normalize_account_number($entry_row['account']);
                    if (strpos($account, '51') === 0 || strpos($account, '52') === 0) {
                        if (!isset($scores[$account])) {
                            $scores[$account] = 0;
                        }
                        $scores[$account]++;
                    }
                }
            }

            if (!empty($scores)) {
                arsort($scores);
                $candidate = (string) key($scores);
            }
        }

        if ($candidate === '512') {
            $candidate = $this->pick_preferred_account(array('512', '521', '511', '52'));
        }

        $cache[$cache_key] = $candidate;
        return $candidate;
    }

    protected function cash_opening_already_posted($cashbox_id, array $existing_groups)
    {
        foreach ($existing_groups as $group) {
            if (empty($group['invoice_ids'][(int) $cashbox_id])) {
                continue;
            }
            $has_treasury = $this->group_has_account_prefix($group, array('57'));
            $has_equity = $this->group_has_account_prefix($group, array('10', '1'));
            if ($has_treasury && $has_equity) {
                return true;
            }
        }

        return false;
    }

    protected function cash_operation_already_posted($operation_id, array $existing_groups)
    {
        foreach ($existing_groups as $group) {
            if (!empty($group['invoice_ids'][(int) $operation_id]) && $this->group_has_account_prefix($group, array('57'))) {
                return true;
            }
        }

        return false;
    }

    protected function bank_transaction_already_posted(array $bank_row, array $existing_groups)
    {
        $date = $this->normalize_date_string(isset($bank_row['date']) ? $bank_row['date'] : null);
        $amount = $this->to_float(isset($bank_row['amount']) ? $bank_row['amount'] : 0);
        $is_credit = $this->is_bank_credit_transaction($bank_row);

        foreach ($existing_groups as $group) {
            if ($group['date_ecriture'] !== $date) {
                continue;
            }
            $treasury_accounts = $is_credit ? $group['debit_accounts'] : $group['credit_accounts'];
            foreach (array_keys($treasury_accounts) as $account) {
                if (strpos($account, '51') !== 0 && strpos($account, '52') !== 0) {
                    continue;
                }
                $group_amount = $is_credit ? (float) $group['total_debit'] : (float) $group['total_credit'];
                if (abs($group_amount - $amount) < 0.0001) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function group_has_account_prefix(array $group, array $prefixes)
    {
        foreach (array_merge(array_keys($group['debit_accounts']), array_keys($group['credit_accounts'])) as $account) {
            foreach ($prefixes as $prefix) {
                if (strpos((string) $account, (string) $prefix) === 0) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function is_cash_entry_operation(array $row)
    {
        $type = strtoupper(trim((string) (isset($row['type_operation']) ? $row['type_operation'] : '')));
        if ($type !== '') {
            return $type === 'ENTREE' || $type === 'ENTRÉE' || $type === 'ENTREE';
        }

        return $this->to_float(isset($row['entree']) ? $row['entree'] : 0) >= $this->to_float(isset($row['sortie']) ? $row['sortie'] : 0);
    }

    protected function is_bank_credit_transaction(array $row)
    {
        $designation = trim((string) (isset($row['designation']) ? $row['designation'] : ''));
        $transaction_type = trim((string) (isset($row['transaction_type']) ? $row['transaction_type'] : ''));

        return in_array($designation, array('Crédit', 'Credit'), true)
            || in_array($transaction_type, array('Dépôt', 'Depot', 'Virement entrant'), true);
    }

    protected function get_journal_code_by_id($journal_id)
    {
        static $cache = array();
        $journal_id = (int) $journal_id;
        if ($journal_id <= 0) {
            return '';
        }
        if (isset($cache[$journal_id])) {
            return $cache[$journal_id];
        }
        if (!$this->table_exists('journaux_auxiliaires')) {
            return '';
        }

        $this->db->select('code');
        $this->db->from('journaux_auxiliaires');
        $this->db->where('id', $journal_id);
        $this->scope_table('journaux_auxiliaires');
        $this->deleted_clause('journaux_auxiliaires');
        $row = $this->db->get()->row_array();
        $cache[$journal_id] = isset($row['code']) ? $row['code'] : '';
        return $cache[$journal_id];
    }

    protected function index_rows_by_id(array $rows)
    {
        $indexed = array();
        foreach ($rows as $row) {
            if (isset($row['id'])) {
                $indexed[(int) $row['id']] = $row;
            }
        }

        return $indexed;
    }

    protected function explode_entry_to_ledger_rows(array $entry, $compte_filter, array $accounts)
    {
        $rows = array();
        $debit_account = isset($entry['compte_debit']) ? (string) $entry['compte_debit'] : '';
        $credit_account = isset($entry['compte_credit']) ? (string) $entry['compte_credit'] : '';
        $debit_amount = $this->extract_entry_debit_amount($entry);
        $credit_amount = $this->extract_entry_credit_amount($entry);

        if ($compte_filter === '' || strpos($debit_account, $compte_filter) === 0) {
            $rows[] = array(
                'type' => 'movement',
                'account' => $debit_account,
                'account_label' => isset($accounts[$debit_account]['libelle']) ? $accounts[$debit_account]['libelle'] : 'Compte',
                'date' => $entry['date_ecriture'],
                'journal' => isset($entry['journal_code']) ? $entry['journal_code'] : '',
                'libelle' => $entry['libelle'],
                'counterpart' => $credit_account,
                'piece' => isset($entry['reference_piece']) ? $entry['reference_piece'] : '',
                'debit' => $debit_amount,
                'credit' => 0.0,
                'signed_amount' => $debit_amount,
            );
        }

        if ($credit_account !== $debit_account && ($compte_filter === '' || strpos($credit_account, $compte_filter) === 0)) {
            $rows[] = array(
                'type' => 'movement',
                'account' => $credit_account,
                'account_label' => isset($accounts[$credit_account]['libelle']) ? $accounts[$credit_account]['libelle'] : 'Compte',
                'date' => $entry['date_ecriture'],
                'journal' => isset($entry['journal_code']) ? $entry['journal_code'] : '',
                'libelle' => $entry['libelle'],
                'counterpart' => $debit_account,
                'piece' => isset($entry['reference_piece']) ? $entry['reference_piece'] : '',
                'debit' => 0.0,
                'credit' => $credit_amount,
                'signed_amount' => -$credit_amount,
            );
        }

        return $rows;
    }

    protected function extract_entry_debit_amount(array $entry)
    {
        if (isset($entry['montant_debit']) && (float) $entry['montant_debit'] > 0) {
            return (float) $entry['montant_debit'];
        }

        return isset($entry['montant']) ? (float) $entry['montant'] : 0.0;
    }

    protected function extract_entry_credit_amount(array $entry)
    {
        if (isset($entry['montant_credit']) && (float) $entry['montant_credit'] > 0) {
            return (float) $entry['montant_credit'];
        }

        return isset($entry['montant']) ? (float) $entry['montant'] : 0.0;
    }

    protected function classify_balance_sheet_account(array $row)
    {
        $classe = isset($row['classe']) ? (string) $row['classe'] : '';
        $closing_balance = (float) $row['closing_balance'];

        if ($classe === '1') {
            return array('side' => 'passif', 'section' => 'capitaux');
        }
        if ($classe === '2') {
            return array('side' => 'actif', 'section' => 'actif_immobilise');
        }
        if ($classe === '3') {
            return array('side' => 'actif', 'section' => 'stocks');
        }
        if ($classe === '4') {
            return $closing_balance >= 0 ? array('side' => 'actif', 'section' => 'creances') : array('side' => 'passif', 'section' => 'dettes');
        }
        if ($classe === '5') {
            return $closing_balance >= 0 ? array('side' => 'actif', 'section' => 'tresorerie_actif') : array('side' => 'passif', 'section' => 'tresorerie_passif');
        }

        return null;
    }

    protected function classify_income_statement_account(array $row)
    {
        $account_number = isset($row['compte']) ? (string) $row['compte'] : '';
        $classe = isset($row['classe']) ? (string) $row['classe'] : '';
        $net = (float) $row['mouvement_debit'] - (float) $row['mouvement_credit'];
        $type = $net >= 0 ? 'charge' : 'produit';

        if ($classe === '6') {
            $type = 'charge';
        } elseif ($classe === '7') {
            $type = 'produit';
        }

        $amount = $type === 'charge' ? max($net, 0.0) : max(-$net, 0.0);
        if ($classe === '7') {
            $amount = max((float) $row['mouvement_credit'] - (float) $row['mouvement_debit'], 0.0);
        } elseif ($classe === '6') {
            $amount = max((float) $row['mouvement_debit'] - (float) $row['mouvement_credit'], 0.0);
        }

        $prefix2 = substr($account_number, 0, 2);
        $section = 'autres';
        $label = 'Autres charges et produits';

        if ($type === 'charge') {
            if (in_array($prefix2, array('60', '61', '62', '63', '64', '65'), true)) {
                $section = 'charges_exploitation';
                $label = 'Charges d exploitation';
            } elseif ($prefix2 === '66') {
                $section = 'charges_personnel';
                $label = 'Charges de personnel';
            } elseif ($prefix2 === '67') {
                $section = 'charges_financieres';
                $label = 'Charges financieres';
            } elseif (in_array($prefix2, array('68', '69'), true)) {
                $section = 'charges_risques';
                $label = 'Dotations, provisions et charges HAO';
            }
        } else {
            if (in_array($prefix2, array('70', '71', '72'), true)) {
                $section = 'produits_activite';
                $label = 'Produits des activites ordinaires';
            } elseif (in_array($prefix2, array('73', '74', '75', '76'), true)) {
                $section = 'autres_produits_exploitation';
                $label = 'Autres produits d exploitation';
            } elseif ($prefix2 === '77') {
                $section = 'produits_financiers';
                $label = 'Produits financiers';
            } elseif (in_array($prefix2, array('78', '79'), true)) {
                $section = 'produits_hao';
                $label = 'Produits HAO et reprises';
            }
        }

        return array(
            'section' => $section,
            'label' => $label,
            'type' => $type,
            'amount' => $amount,
        );
    }

    protected function build_sig_indicators(array $rows)
    {
        $section_totals = array();
        foreach ($rows as $row) {
            $section = isset($row['section']) ? $row['section'] : 'autres';
            if (!isset($section_totals[$section])) {
                $section_totals[$section] = array('charges' => 0.0, 'produits' => 0.0);
            }
            $section_totals[$section]['charges'] += (float) $row['charges'];
            $section_totals[$section]['produits'] += (float) $row['produits'];
        }

        $chiffre_affaires = $this->safe_section_value($section_totals, 'produits_activite', 'produits');
        $autres_produits = $this->safe_section_value($section_totals, 'autres_produits_exploitation', 'produits');
        $charges_exploitation = $this->safe_section_value($section_totals, 'charges_exploitation', 'charges');
        $charges_personnel = $this->safe_section_value($section_totals, 'charges_personnel', 'charges');
        $charges_financieres = $this->safe_section_value($section_totals, 'charges_financieres', 'charges');
        $produits_financiers = $this->safe_section_value($section_totals, 'produits_financiers', 'produits');
        $dotations = $this->safe_section_value($section_totals, 'charges_risques', 'charges');
        $produits_hao = $this->safe_section_value($section_totals, 'produits_hao', 'produits');
        $autres = $this->safe_section_value($section_totals, 'autres', 'produits') - $this->safe_section_value($section_totals, 'autres', 'charges');

        $marge_brute = $chiffre_affaires - $charges_exploitation;
        $valeur_ajoutee = $marge_brute + $autres_produits - $charges_personnel;
        $resultat_exploitation = $valeur_ajoutee - $dotations;
        $resultat_financier = $produits_financiers - $charges_financieres;
        $resultat_hao = $produits_hao + max($autres, 0.0);
        $resultat_net = 0.0;
        foreach ($rows as $row) {
            $resultat_net += (float) $row['produits'] - (float) $row['charges'];
        }

        return array(
            'chiffre_affaires' => $chiffre_affaires,
            'marge_brute' => $marge_brute,
            'valeur_ajoutee' => $valeur_ajoutee,
            'resultat_exploitation' => $resultat_exploitation,
            'resultat_financier' => $resultat_financier,
            'resultat_hao' => $resultat_hao,
            'resultat_net' => $resultat_net,
        );
    }

    protected function safe_section_value(array $section_totals, $section, $column)
    {
        return isset($section_totals[$section][$column]) ? (float) $section_totals[$section][$column] : 0.0;
    }

    protected function add_delta_to_tafire_line(array &$line, $delta)
    {
        if ($delta >= 0) {
            $line['emploi'] += abs($delta);
        } else {
            $line['ressource'] += abs($delta);
        }
    }

    protected function get_previous_date($date)
    {
        return date('Y-m-d', strtotime($date . ' -1 day'));
    }

    protected function get_class_label($classe)
    {
        return isset($this->default_classes[$classe]) ? $this->default_classes[$classe] : 'Classe ' . $classe;
    }

    protected function default_nature_for_class($classe)
    {
        if (in_array((string) $classe, array('1', '7', '8'), true)) {
            return 'credit';
        }

        if ((string) $classe === '4') {
            return 'mixte';
        }

        return 'debit';
    }

    public function count_mouvements($date_debut, $date_fin)
    {
        return count($this->build_report_entries(array(
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
        )));
    }

    public function count_ecritures($date_debut, $date_fin)
    {
        return $this->count_mouvements($date_debut, $date_fin);
    }

    public function get_tiers($type = '')
    {
        if (!$this->table_exists('ohada_tiers')) {
            return array();
        }

        $this->db->from('ohada_tiers');
        $this->scope_table('ohada_tiers');
        $this->deleted_clause('ohada_tiers');
        if ($type !== '') {
            $this->db->where('type', $type);
        }
        $this->db->order_by('code', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_tier($id)
    {
        $this->db->from('ohada_tiers');
        $this->db->where('id', (int) $id);
        $this->scope_table('ohada_tiers');
        $this->deleted_clause('ohada_tiers');
        return $this->db->get()->row_array();
    }

    public function save_tier(array $data, $id = null)
    {
        $payload = array(
            'entreprise_id' => $this->get_entreprise_id(),
            'code' => trim($data['code']),
            'libelle' => trim($data['libelle']),
            'type' => trim($data['type']),
            'compte_collectif' => trim($data['compte_collectif']),
            'telephone' => trim($data['telephone']),
            'email' => trim($data['email']),
            'adresse' => trim($data['adresse']),
            'updated_at' => date('Y-m-d H:i:s'),
        );

        if ($id) {
            $this->db->where('id', (int) $id);
            $this->scope_table('ohada_tiers');
            return $this->db->update('ohada_tiers', $payload);
        }

        $payload['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('ohada_tiers', $payload);
    }

    public function get_analytics()
    {
        if (!$this->table_exists('ohada_analytique')) {
            return array();
        }

        $this->db->from('ohada_analytique');
        $this->scope_table('ohada_analytique');
        $this->deleted_clause('ohada_analytique');
        $this->db->order_by('code', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_analytic($id)
    {
        $this->db->from('ohada_analytique');
        $this->db->where('id', (int) $id);
        $this->scope_table('ohada_analytique');
        $this->deleted_clause('ohada_analytique');
        return $this->db->get()->row_array();
    }

    public function save_analytic(array $data, $id = null)
    {
        $payload = array(
            'entreprise_id' => $this->get_entreprise_id(),
            'code' => trim($data['code']),
            'libelle' => trim($data['libelle']),
            'type' => trim($data['type']),
            'description' => trim($data['description']),
            'updated_at' => date('Y-m-d H:i:s'),
        );

        if ($id) {
            $this->db->where('id', (int) $id);
            $this->scope_table('ohada_analytique');
            return $this->db->update('ohada_analytique', $payload);
        }

        $payload['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('ohada_analytique', $payload);
    }

    public function get_notes()
    {
        if (!$this->table_exists('ohada_notes_annexes')) {
            return array();
        }

        $this->db->from('ohada_notes_annexes');
        $this->scope_table('ohada_notes_annexes');
        $this->deleted_clause('ohada_notes_annexes');
        $this->db->order_by('ordre_affichage', 'ASC');
        $this->db->order_by('id', 'ASC');
        return $this->db->get()->result_array();
    }

    public function get_note($id)
    {
        $this->db->from('ohada_notes_annexes');
        $this->db->where('id', (int) $id);
        $this->scope_table('ohada_notes_annexes');
        $this->deleted_clause('ohada_notes_annexes');
        return $this->db->get()->row_array();
    }

    public function save_note(array $data, $id = null)
    {
        $payload = array(
            'entreprise_id' => $this->get_entreprise_id(),
            'titre' => trim($data['titre']),
            'contenu' => trim($data['contenu']),
            'ordre_affichage' => (int) $data['ordre_affichage'],
            'updated_at' => date('Y-m-d H:i:s'),
        );

        if ($id) {
            $this->db->where('id', (int) $id);
            $this->scope_table('ohada_notes_annexes');
            return $this->db->update('ohada_notes_annexes', $payload);
        }

        $payload['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('ohada_notes_annexes', $payload);
    }

    public function get_exercises()
    {
        if (!$this->table_exists('ohada_exercices')) {
            return array();
        }

        $this->db->from('ohada_exercices');
        $this->scope_table('ohada_exercices');
        $this->deleted_clause('ohada_exercices');
        $this->db->order_by('date_debut', 'DESC');
        return $this->db->get()->result_array();
    }

    public function get_exercise($id)
    {
        $this->db->from('ohada_exercices');
        $this->db->where('id', (int) $id);
        $this->scope_table('ohada_exercices');
        $this->deleted_clause('ohada_exercices');
        return $this->db->get()->row_array();
    }

    public function get_active_exercise()
    {
        $this->db->from('ohada_exercices');
        $this->scope_table('ohada_exercices');
        $this->deleted_clause('ohada_exercices');
        $this->db->where('is_active', 1);
        return $this->db->get()->row_array();
    }

    public function save_exercise(array $data, $id = null)
    {
        $payload = array(
            'entreprise_id' => $this->get_entreprise_id(),
            'libelle' => trim($data['libelle']),
            'date_debut' => $data['date_debut'],
            'date_fin' => $data['date_fin'],
            'statut' => !empty($data['statut']) ? $data['statut'] : 'ouvert',
            'updated_at' => date('Y-m-d H:i:s'),
        );

        if ($id) {
            $this->db->where('id', (int) $id);
            $this->scope_table('ohada_exercices');
            return $this->db->update('ohada_exercices', $payload);
        }

        $payload['is_active'] = !empty($data['is_active']) ? 1 : 0;
        $payload['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('ohada_exercices', $payload);
    }

    public function activate_exercise($id)
    {
        $this->db->where('entreprise_id', $this->get_entreprise_id());
        $this->db->update('ohada_exercices', array(
            'is_active' => 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ));

        $this->db->where('id', (int) $id);
        $this->db->where('entreprise_id', $this->get_entreprise_id());
        return $this->db->update('ohada_exercices', array(
            'is_active' => 1,
            'updated_at' => date('Y-m-d H:i:s'),
        ));
    }

    public function update_exercise_status($id, $status)
    {
        $this->db->where('id', (int) $id);
        $this->db->where('entreprise_id', $this->get_entreprise_id());
        return $this->db->update('ohada_exercices', array(
            'statut' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ));
    }

    public function get_settings()
    {
        if (!$this->table_exists('ohada_parametres')) {
            return array();
        }

        $this->db->from('ohada_parametres');
        $this->scope_table('ohada_parametres');
        return $this->db->get()->row_array();
    }

    public function save_settings(array $data)
    {
        $payload = array(
            'entreprise_id' => $this->get_entreprise_id(),
            'referentiel' => trim($data['referentiel']),
            'devise' => trim($data['devise']),
            'pays' => trim($data['pays']),
            'longueur_compte' => (int) $data['longueur_compte'],
            'utiliser_analytique' => !empty($data['utiliser_analytique']) ? 1 : 0,
            'utiliser_tiers' => !empty($data['utiliser_tiers']) ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s'),
        );

        $existing = $this->get_settings();
        if ($existing) {
            $this->db->where('id', (int) $existing['id']);
            return $this->db->update('ohada_parametres', $payload);
        }

        $payload['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('ohada_parametres', $payload);
    }

    public function reset_settings()
    {
        $this->db->where('entreprise_id', $this->get_entreprise_id());
        return $this->db->delete('ohada_parametres');
    }

    public function get_journal_configs()
    {
        if (!$this->table_exists('ohada_journaux_config')) {
            return array();
        }

        $this->db->from('ohada_journaux_config');
        $this->scope_table('ohada_journaux_config');
        $this->db->order_by('module_code', 'ASC');
        return $this->db->get()->result_array();
    }

    public function save_journal_config(array $rows)
    {
        $this->db->where('entreprise_id', $this->get_entreprise_id());
        $this->db->delete('ohada_journaux_config');

        foreach ($rows as $row) {
            $payload = array(
                'entreprise_id' => $this->get_entreprise_id(),
                'module_code' => $row['module_code'],
                'journal_code' => $row['journal_code'],
                'libelle' => $row['libelle'],
                'updated_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
            );
            $this->db->insert('ohada_journaux_config', $payload);
        }

        return true;
    }

    public function get_bank_reconciliations()
    {
        if (!$this->table_exists('ohada_rapprochements')) {
            return array();
        }

        $this->db->from('ohada_rapprochements');
        $this->scope_table('ohada_rapprochements');
        $this->deleted_clause('ohada_rapprochements');
        $this->db->order_by('date_operation', 'DESC');
        return $this->db->get()->result_array();
    }

    public function save_bank_reconciliation(array $data)
    {
        $payload = array(
            'entreprise_id' => $this->get_entreprise_id(),
            'date_operation' => $data['date_operation'],
            'reference' => trim($data['reference']),
            'libelle' => trim($data['libelle']),
            'montant' => (float) $data['montant'],
            'statut' => !empty($data['statut']) ? $data['statut'] : 'en_attente',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        );
        return $this->db->insert('ohada_rapprochements', $payload);
    }

    public function get_cloture_summary()
    {
        $active = $this->get_active_exercise();
        $entries = $this->count_table('ecritures_comptables');
        $notes = $this->count_table('ohada_notes_annexes');

        return array(
            'active_exercise' => $active ? $active['libelle'] : 'Aucun exercice actif',
            'entries' => $entries,
            'notes' => $notes,
        );
    }

    public function seed_syscohada_accounts()
    {
        if (!$this->table_exists('chart_of_accounts')) {
            return false;
        }

        $entreprise_id = $this->get_entreprise_id();
        $defaults = array(
            array('numero_compte' => '101', 'libelle_compte' => 'Capital social', 'classe' => '1', 'type_compte' => 'bilan', 'nature' => 'credit'),
            array('numero_compte' => '201', 'libelle_compte' => 'Frais de constitution', 'classe' => '2', 'type_compte' => 'bilan', 'nature' => 'debit'),
            array('numero_compte' => '311', 'libelle_compte' => 'Stocks de marchandises', 'classe' => '3', 'type_compte' => 'bilan', 'nature' => 'debit'),
            array('numero_compte' => '401', 'libelle_compte' => 'Fournisseurs', 'classe' => '4', 'type_compte' => 'bilan', 'nature' => 'credit'),
            array('numero_compte' => '411', 'libelle_compte' => 'Clients', 'classe' => '4', 'type_compte' => 'bilan', 'nature' => 'debit'),
            array('numero_compte' => '512', 'libelle_compte' => 'Banques', 'classe' => '5', 'type_compte' => 'bilan', 'nature' => 'debit'),
            array('numero_compte' => '571', 'libelle_compte' => 'Caisse', 'classe' => '5', 'type_compte' => 'bilan', 'nature' => 'debit'),
            array('numero_compte' => '601', 'libelle_compte' => 'Achats de marchandises', 'classe' => '6', 'type_compte' => 'resultat', 'nature' => 'debit'),
            array('numero_compte' => '701', 'libelle_compte' => 'Ventes de marchandises', 'classe' => '7', 'type_compte' => 'resultat', 'nature' => 'credit'),
            array('numero_compte' => '902', 'libelle_compte' => 'Centre de cout principal', 'classe' => '9', 'type_compte' => 'analytique', 'nature' => 'debit'),
        );

        foreach ($defaults as $item) {
            $this->db->from('chart_of_accounts');
            $this->db->where('entreprise_id', $entreprise_id);
            $this->db->where('numero_compte', $item['numero_compte']);
            if ($this->db->count_all_results() > 0) {
                continue;
            }

            $payload = array_merge($item, array(
                'entreprise_id' => $entreprise_id,
                'compte_parent' => '',
                'allow_posting' => 1,
                'status' => 'active',
                'deleted' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ));
            $this->db->insert('chart_of_accounts', $payload);
        }

        return true;
    }
}
