<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Offrandes_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // ========================================== //
    // RÉCUPÉRER TOUTES LES OFFRANDES             //
    // ========================================== //
    public function get_all($id = null) {
        $this->db->select('o.*, m.nom as membre_nom_complet, m.prenom as membre_prenom');
        $this->db->from('offrandes_dimes o');
        $this->db->join('membres m', 'o.membre_id = m.id', 'left');
        $this->db->where('o.deleted', 0);

        if ($id != null) {
            $this->db->where('o.id', $id);
            $query = $this->db->get();
            return $query->row_array();
        }

        $this->db->order_by('o.id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER UNE OFFERANDE PAR ID             //
    // ========================================== //
    public function get_by_id($id) {
        $this->db->select('o.*, m.nom as membre_nom_complet, m.prenom as membre_prenom');
        $this->db->from('offrandes_dimes o');
        $this->db->join('membres m', 'o.membre_id = m.id', 'left');
        $this->db->where('o.id', $id);
        $this->db->where('o.deleted', 0);
        $query = $this->db->get();
        return $query->row_array();
    }

    // ========================================== //
    // GÉNÉRER UN CODE DE TRANSACTION             //
    // ========================================== //
    public function generate_code() {
        $prefix = 'TRX';
        $year = date('Y');
        $month = date('m');
        $last = $this->db->query("SELECT MAX(CAST(SUBSTRING(code_transaction, 10) AS UNSIGNED)) as last FROM offrandes_dimes WHERE code_transaction LIKE '$prefix-$year$month-%'")->row()->last;
        $next = str_pad(($last + 1), 4, '0', STR_PAD_LEFT);
        return $prefix . '-' . $year . $month . '-' . $next;
    }

    // ========================================== //
    // AJOUTER UNE OFFERANDE                      //
    // ========================================== //
    public function add($data) {
        $data['code_transaction'] = $this->generate_code();
        $this->db->insert('offrandes_dimes', $data);
        return $this->db->insert_id();
    }

    // ========================================== //
    // METTRE À JOUR UNE OFFERANDE                //
    // ========================================== //
    public function update($id, $data) {
        $this->db->where('id', $id);
        $this->db->update('offrandes_dimes', $data);
        return $this->db->affected_rows();
    }

    // ========================================== //
    // SUPPRIMER UNE OFFERANDE (SOFT DELETE)      //
    // ========================================== //
    public function delete($id) {
        $this->db->where('id', $id);
        $this->db->update('offrandes_dimes', array('deleted' => 1));
        return $this->db->affected_rows();
    }

    // ========================================== //
    // STATISTIQUES DES OFFRANDES                 //
    // ========================================== //
    public function get_stats() {
        $stats = array();

        // Total
        $this->db->select('COUNT(*) as total, SUM(montant) as total_montant');
        $this->db->from('offrandes_dimes');
        $this->db->where('deleted', 0);
        $this->db->where('statut', 'valide');
        $query = $this->db->get();
        $row = $query->row();
        $stats['total'] = (int)$row->total;
        $stats['total_montant'] = (float)$row->total_montant;

        // Par type
        $types = ['offrande', 'dime', 'action_de_grace', 'autre'];
        foreach ($types as $type) {
            $this->db->select('COUNT(*) as total, SUM(montant) as montant');
            $this->db->from('offrandes_dimes');
            $this->db->where('deleted', 0);
            $this->db->where('statut', 'valide');
            $this->db->where('type', $type);
            $query = $this->db->get();
            $row = $query->row();
            $stats['type_' . $type] = (int)$row->total;
            $stats['type_' . $type . '_montant'] = (float)$row->montant;
        }

        // Par mode de paiement
        $modes = ['especes', 'cheque', 'virement', 'mobile_money', 'carte'];
        foreach ($modes as $mode) {
            $this->db->select('COUNT(*) as total, SUM(montant) as montant');
            $this->db->from('offrandes_dimes');
            $this->db->where('deleted', 0);
            $this->db->where('statut', 'valide');
            $this->db->where('mode_paiement', $mode);
            $query = $this->db->get();
            $row = $query->row();
            $stats['mode_' . $mode] = (int)$row->total;
            $stats['mode_' . $mode . '_montant'] = (float)$row->montant;
        }

        // Aujourd'hui
        $today = date('Y-m-d');
        $this->db->select('COUNT(*) as total, SUM(montant) as montant');
        $this->db->from('offrandes_dimes');
        $this->db->where('deleted', 0);
        $this->db->where('statut', 'valide');
        $this->db->where('date_transaction', $today);
        $query = $this->db->get();
        $row = $query->row();
        $stats['today'] = (int)$row->total;
        $stats['today_montant'] = (float)$row->montant;

        // Ce mois
        $month = date('Y-m');
        $this->db->select('COUNT(*) as total, SUM(montant) as montant');
        $this->db->from('offrandes_dimes');
        $this->db->where('deleted', 0);
        $this->db->where('statut', 'valide');
        $this->db->where('DATE_FORMAT(date_transaction, "%Y-%m")', $month);
        $query = $this->db->get();
        $row = $query->row();
        $stats['month'] = (int)$row->total;
        $stats['month_montant'] = (float)$row->montant;

        return $stats;
    }

    // ========================================== //
    // RÉCUPÉRER LES OFFRANDES FILTRÉES           //
    // ========================================== //
    public function get_filtered($type = null, $date_from = null, $date_to = null, $statut = null, $mode = null) {
        $this->db->select('o.*, m.nom as membre_nom_complet, m.prenom as membre_prenom');
        $this->db->from('offrandes_dimes o');
        $this->db->join('membres m', 'o.membre_id = m.id', 'left');
        $this->db->where('o.deleted', 0);

        if (!empty($type)) {
            $this->db->where('o.type', $type);
        }

        if (!empty($date_from)) {
            $this->db->where('o.date_transaction >=', $date_from);
        }

        if (!empty($date_to)) {
            $this->db->where('o.date_transaction <=', $date_to);
        }

        if (!empty($statut)) {
            $this->db->where('o.statut', $statut);
        }

        if (!empty($mode)) {
            $this->db->where('o.mode_paiement', $mode);
        }

        $this->db->order_by('o.id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES TYPES                        //
    // ========================================== //
    public function get_types() {
        return [
            'offrande' => 'Offrande',
            'dime' => 'Dîme',
            'action_de_grace' => 'Action de grâce',
            'autre' => 'Autre'
        ];
    }

    // ========================================== //
    // RÉCUPÉRER LES MODES DE PAIEMENT            //
    // ========================================== //
    public function get_modes() {
        return [
            'especes' => 'Espèces',
            'cheque' => 'Chèque',
            'virement' => 'Virement',
            'mobile_money' => 'Mobile Money',
            'carte' => 'Carte bancaire'
        ];
    }

    // ========================================== //
    // RÉCUPÉRER LES STATUTS                      //
    // ========================================== //
    public function get_statuses() {
        return [
            'valide' => 'Validé',
            'en_attente' => 'En attente',
            'annule' => 'Annulé'
        ];
    }

    // ========================================== //
    // GET STATUS LABEL                           //
    // ========================================== //
    public function get_status_label($statut) {
        $labels = [
            'valide' => 'Validé',
            'en_attente' => 'En attente',
            'annule' => 'Annulé'
        ];
        return $labels[$statut] ?? $statut;
    }

    // ========================================== //
    // GET STATUS BADGE CLASS                     //
    // ========================================== //
    public function get_status_badge($statut) {
        $badges = [
            'valide' => 'valide',
            'en_attente' => 'en-attente',
            'annule' => 'annule'
        ];
        return $badges[$statut] ?? 'valide';
    }

    // ========================================== //
    // GET TYPE LABEL                             //
    // ========================================== //
    public function get_type_label($type) {
        $labels = [
            'offrande' => 'Offrande',
            'dime' => 'Dîme',
            'action_de_grace' => 'Action de grâce',
            'autre' => 'Autre'
        ];
        return $labels[$type] ?? $type;
    }

    // ========================================== //
    // GET TYPE BADGE CLASS                       //
    // ========================================== //
    public function get_type_badge($type) {
        $badges = [
            'offrande' => 'offrande',
            'dime' => 'dime',
            'action_de_grace' => 'action',
            'autre' => 'autre'
        ];
        return $badges[$type] ?? 'autre';
    }

    // ========================================== //
    // RECHERCHER DES OFFRANDES                   //
    // ========================================== //
    public function search($keyword) {
        $this->db->select('o.*, m.nom as membre_nom_complet, m.prenom as membre_prenom');
        $this->db->from('offrandes_dimes o');
        $this->db->join('membres m', 'o.membre_id = m.id', 'left');
        $this->db->where('o.deleted', 0);
        $this->db->group_start();
        $this->db->like('o.code_transaction', $keyword);
        $this->db->or_like('o.membre_nom', $keyword);
        $this->db->or_like('o.description', $keyword);
        $this->db->or_like('o.reference_paiement', $keyword);
        $this->db->group_end();
        $this->db->order_by('o.id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // STATISTIQUES PAR MOIS                      //
    // ========================================== //
    public function get_stats_mensuelles($mois, $annee) {
        $this->db->select('type, COUNT(*) as total, SUM(montant) as montant');
        $this->db->from('offrandes_dimes');
        $this->db->where('deleted', 0);
        $this->db->where('statut', 'valide');
        $this->db->where('MONTH(date_transaction)', $mois);
        $this->db->where('YEAR(date_transaction)', $annee);
        $this->db->group_by('type');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // COMPTER PAR TYPE                           //
    // ========================================== //
    public function count_by_type($type) {
        $this->db->select('COUNT(*) as total, SUM(montant) as montant');
        $this->db->from('offrandes_dimes');
        $this->db->where('deleted', 0);
        $this->db->where('statut', 'valide');
        $this->db->where('type', $type);
        $query = $this->db->get();
        $row = $query->row();
        return ['total' => (int)$row->total, 'montant' => (float)$row->montant];
    }
}