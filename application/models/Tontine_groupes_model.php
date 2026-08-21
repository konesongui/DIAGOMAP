<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tontine_groupes_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // ========================================== //
    // RÉCUPÉRER TOUS LES GROUPES                 //
    // ========================================== //
    public function get_all() {
        try {
            $this->db->where('deleted', 0);
            $this->db->order_by('nom', 'ASC');
            $query = $this->db->get('tontine_groupes');
            return $query->result_array();
        } catch (Exception $e) {
            log_message('error', 'Erreur dans get_all: ' . $e->getMessage());
            return array();
        }
    }

    // ========================================== //
    // RÉCUPÉRER UN GROUPE PAR ID                 //
    // ========================================== //
    public function get($id) {
        try {
            $this->db->where('id', $id);
            $this->db->where('deleted', 0);
            $query = $this->db->get('tontine_groupes');
            return $query->row_array();
        } catch (Exception $e) {
            log_message('error', 'Erreur dans get: ' . $e->getMessage());
            return null;
        }
    }

    // ========================================== //
    // AJOUTER UN GROUPE                          //
    // ========================================== //
    public function ajouter($data) {
        try {
            $this->db->insert('tontine_groupes', $data);
            return $this->db->insert_id();
        } catch (Exception $e) {
            log_message('error', 'Erreur dans ajouter: ' . $e->getMessage());
            return false;
        }
    }

    // ========================================== //
    // METTRE À JOUR UN GROUPE                    //
    // ========================================== //
    public function mettre_a_jour($id, $data) {
        try {
            $this->db->where('id', $id);
            return $this->db->update('tontine_groupes', $data);
        } catch (Exception $e) {
            log_message('error', 'Erreur dans mettre_a_jour: ' . $e->getMessage());
            return false;
        }
    }
}