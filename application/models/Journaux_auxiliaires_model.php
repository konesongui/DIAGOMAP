<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Journaux_auxiliaires_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // ========================================== //
    // MÉTHODE PRIVÉE POUR APPLIQUER LE FILTRE    //
    // ========================================== //
    private function _apply_entreprise_filter() {
        $entreprise_id = $this->session->userdata('entreprise_id');
        $columns = $this->db->list_fields('journaux_auxiliaires');
        $hasEntrepriseId = in_array('entreprise_id', $columns);

        if ($hasEntrepriseId && !empty($entreprise_id)) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
    }

    // ========================================== //
    // RÉCUPÉRER TOUS LES JOURNAUX                //
    // ========================================== //
    public function get_all() {
        $this->db->select('*');
        $this->db->from('journaux_auxiliaires');
        $this->db->where('deleted', 0);
        $this->_apply_entreprise_filter();
        $this->db->order_by('code', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER UN JOURNAL PAR ID                //
    // ========================================== //
    public function get_by_id($id) {
        $this->db->select('*');
        $this->db->from('journaux_auxiliaires');
        $this->db->where('id', $id);
        $this->db->where('deleted', 0);
        $this->_apply_entreprise_filter();
        $query = $this->db->get();
        return $query->row_array();
    }

    // ========================================== //
    // RÉCUPÉRER UN JOURNAL PAR CODE              //
    // ========================================== //
    public function get_by_code($code) {
        $this->db->select('*');
        $this->db->from('journaux_auxiliaires');
        $this->db->where('code', strtoupper($code));
        $this->db->where('deleted', 0);
        $this->_apply_entreprise_filter();
        $query = $this->db->get();
        return $query->row_array();
    }

    // ========================================== //
    // AJOUTER UN JOURNAL                         //
    // ========================================== //
    public function add($data) {
        // Ajout de l'entreprise_id si non fourni
        if (!isset($data['entreprise_id']) || empty($data['entreprise_id'])) {
            $entreprise_id = $this->session->userdata('entreprise_id');
            $columns = $this->db->list_fields('journaux_auxiliaires');
            $hasEntrepriseId = in_array('entreprise_id', $columns);

            if ($hasEntrepriseId && !empty($entreprise_id)) {
                $data['entreprise_id'] = $entreprise_id;
            }
        }

        $this->db->insert('journaux_auxiliaires', $data);
        return $this->db->insert_id();
    }

    // ========================================== //
    // METTRE À JOUR UN JOURNAL                   //
    // ========================================== //
    public function update($id, $data) {
        // Vérifier que le journal appartient bien à l'entreprise
        $columns = $this->db->list_fields('journaux_auxiliaires');
        $hasEntrepriseId = in_array('entreprise_id', $columns);
        $entreprise_id = $this->session->userdata('entreprise_id');

        if ($hasEntrepriseId && !empty($entreprise_id)) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where('id', $id);
        $check = $this->db->get('journaux_auxiliaires')->row_array();

        if (!$check) {
            return false;
        }

        $this->db->where('id', $id);
        $this->db->update('journaux_auxiliaires', $data);
        return $this->db->affected_rows();
    }

    // ========================================== //
    // SUPPRIMER UN JOURNAL                       //
    // ========================================== //
    public function delete($id) {
        // Vérifier que le journal appartient bien à l'entreprise
        $columns = $this->db->list_fields('journaux_auxiliaires');
        $hasEntrepriseId = in_array('entreprise_id', $columns);
        $entreprise_id = $this->session->userdata('entreprise_id');

        if ($hasEntrepriseId && !empty($entreprise_id)) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where('id', $id);
        $check = $this->db->get('journaux_auxiliaires')->row_array();

        if (!$check) {
            return false;
        }

        $this->db->where('id', $id);
        $this->db->update('journaux_auxiliaires', array('deleted' => 1));
        return $this->db->affected_rows();
    }

    // ========================================== //
    // VÉRIFIER SI UN CODE EXISTE DÉJÀ           //
    // ========================================== //
    public function code_exists($code, $id = null) {
        $this->db->where('code', strtoupper($code));
        $this->db->where('deleted', 0);
        $this->_apply_entreprise_filter();

        if ($id) {
            $this->db->where('id !=', $id);
        }

        $query = $this->db->get('journaux_auxiliaires');
        return $query->num_rows() > 0;
    }

    // ========================================== //
    // STATISTIQUES DES JOURNAUX                  //
    // ========================================== //
    public function get_stats() {
        $stats = array();

        // Total
        $this->db->select('COUNT(*) as total');
        $this->db->from('journaux_auxiliaires');
        $this->db->where('deleted', 0);
        $this->_apply_entreprise_filter();
        $query = $this->db->get();
        $stats['total'] = (int)$query->row()->total;

        // Actifs
        $this->db->select('COUNT(*) as total');
        $this->db->from('journaux_auxiliaires');
        $this->db->where('deleted', 0);
        $this->db->where('actif', 1);
        $this->_apply_entreprise_filter();
        $query = $this->db->get();
        $stats['actifs'] = (int)$query->row()->total;

        // Par type
        $types = ['ACHATS', 'VENTES', 'BANQUE', 'CAISSE', 'PAIE', 'OPD', 'A-NOUVEAUX', 'AUTRE'];
        foreach ($types as $type) {
            $this->db->select('COUNT(*) as total');
            $this->db->from('journaux_auxiliaires');
            $this->db->where('deleted', 0);
            $this->db->where('type', $type);
            $this->_apply_entreprise_filter();
            $query = $this->db->get();
            $stats['type_' . strtolower($type)] = (int)$query->row()->total;
        }

        return $stats;
    }

    // ========================================== //
    // RÉCUPÉRER LES TYPES DE JOURNAUX           //
    // ========================================== //
    public function get_types() {
        $this->db->distinct();
        $this->db->select('type');
        $this->db->from('journaux_auxiliaires');
        $this->db->where('deleted', 0);
        $this->_apply_entreprise_filter();
        $this->db->order_by('type', 'ASC');
        $query = $this->db->get();
        $result = $query->result_array();
        return array_column($result, 'type');
    }

    // ========================================== //
    // RÉCUPÉRER LES ÉCRITURES D'UN JOURNAL      //
    // ========================================== //
    public function get_ecritures($journal_id) {
        $this->db->select('*');
        $this->db->from('ecritures_comptables');
        $this->db->where('journal_id', $journal_id);
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && in_array('entreprise_id', $this->db->list_fields('ecritures_comptables'))) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->order_by('date_ecriture', 'DESC');
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // SOMME DES DÉBITS D'UN JOURNAL              //
    // ========================================== //
    public function sum_debit($journal_id) {
        $this->db->select_sum('montant_debit');
        $this->db->from('ecritures_comptables');
        $this->db->where('journal_id', $journal_id);
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && in_array('entreprise_id', $this->db->list_fields('ecritures_comptables'))) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $query = $this->db->get();
        return $query->row()->montant_debit ?? 0;
    }

    // ========================================== //
    // SOMME DES CRÉDITS D'UN JOURNAL             //
    // ========================================== //
    public function sum_credit($journal_id) {
        $this->db->select_sum('montant_credit');
        $this->db->from('ecritures_comptables');
        $this->db->where('journal_id', $journal_id);
        $entreprise_id = $this->session->userdata('entreprise_id');
        if (!empty($entreprise_id) && in_array('entreprise_id', $this->db->list_fields('ecritures_comptables'))) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $query = $this->db->get();
        return $query->row()->montant_credit ?? 0;
    }

    // ========================================== //
    // RÉCUPÉRER LES JOURNAUX FILTRÉS            //
    // ========================================== //
    public function get_filtered($type = null, $actif = null) {
        $this->db->select('*');
        $this->db->from('journaux_auxiliaires');
        $this->db->where('deleted', 0);
        $this->_apply_entreprise_filter();

        if (!empty($type)) {
            $this->db->where('type', $type);
        }

        if ($actif !== null && $actif !== '') {
            $this->db->where('actif', $actif);
        }

        $this->db->order_by('code', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }
}