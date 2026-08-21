<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tontine_membres_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // ========================================== //
    // RÉCUPÉRER TOUS LES MEMBRES                 //
    // ========================================== //
    public function get_membres($search = null, $statut = null, $groupe = null, $date_adhesion = null) {
        try {
            $this->db->select('m.*, g.nom as groupe_nom');
            $this->db->from('tontine_membres m');
            $this->db->join('tontine_groupes g', 'm.groupe_id = g.id', 'left');
            $this->db->where('m.deleted', 0);

            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('m.nom', $search);
                $this->db->or_like('m.prenom', $search);
                $this->db->or_like('m.telephone', $search);
                $this->db->or_like('m.email', $search);
                $this->db->group_end();
            }

            if (!empty($statut)) {
                $this->db->where('m.statut', $statut);
            }

            if (!empty($groupe)) {
                $this->db->where('m.groupe_id', $groupe);
            }

            if (!empty($date_adhesion)) {
                $this->db->where('DATE(m.date_adhesion)', $date_adhesion);
            }

            $this->db->order_by('m.id', 'DESC');
            $query = $this->db->get();
            return $query->result_array();

        } catch (Exception $e) {
            log_message('error', 'Erreur dans get_membres: ' . $e->getMessage());
            return array();
        }
    }

    // ========================================== //
    // COMPTER LES MEMBRES                        //
    // ========================================== //
    public function count_membres($search = null, $statut = null, $groupe = null, $date_adhesion = null) {
        try {
            $this->db->from('tontine_membres');
            $this->db->where('deleted', 0);

            if (!empty($search)) {
                $this->db->group_start();
                $this->db->like('nom', $search);
                $this->db->or_like('prenom', $search);
                $this->db->or_like('telephone', $search);
                $this->db->group_end();
            }

            if (!empty($statut)) {
                $this->db->where('statut', $statut);
            }

            if (!empty($groupe)) {
                $this->db->where('groupe_id', $groupe);
            }

            if (!empty($date_adhesion)) {
                $this->db->where('DATE(date_adhesion)', $date_adhesion);
            }

            return $this->db->count_all_results();

        } catch (Exception $e) {
            log_message('error', 'Erreur dans count_membres: ' . $e->getMessage());
            return 0;
        }
    }

    // ========================================== //
    // RÉCUPÉRER UN MEMBRE PAR ID                 //
    // ========================================== //
    public function get_membre($id) {
        try {
            $this->db->select('m.*, g.nom as groupe_nom');
            $this->db->from('tontine_membres m');
            $this->db->join('tontine_groupes g', 'm.groupe_id = g.id', 'left');
            $this->db->where('m.id', $id);
            $this->db->where('m.deleted', 0);
            $query = $this->db->get();
            return $query->row_array();

        } catch (Exception $e) {
            log_message('error', 'Erreur dans get_membre: ' . $e->getMessage());
            return null;
        }
    }

    // ========================================== //
    // AJOUTER UN MEMBRE                          //
    // ========================================== //
    public function ajouter($data) {
        try {
            $this->db->insert('tontine_membres', $data);
            return $this->db->insert_id();
        } catch (Exception $e) {
            log_message('error', 'Erreur dans ajouter: ' . $e->getMessage());
            return false;
        }
    }

    // ========================================== //
    // METTRE À JOUR UN MEMBRE                    //
    // ========================================== //
    public function mettre_a_jour($id, $data) {
        try {
            $this->db->where('id', $id);
            return $this->db->update('tontine_membres', $data);
        } catch (Exception $e) {
            log_message('error', 'Erreur dans mettre_a_jour: ' . $e->getMessage());
            return false;
        }
    }

    // ========================================== //
    // SUPPRIMER UN MEMBRE                        //
    // ========================================== //
    public function supprimer($id) {
        try {
            $this->db->where('id', $id);
            return $this->db->update('tontine_membres', array('deleted' => 1));
        } catch (Exception $e) {
            log_message('error', 'Erreur dans supprimer: ' . $e->getMessage());
            return false;
        }
    }

    // ========================================== //
    // STATISTIQUES D'UN MEMBRE                   //
    // ========================================== //
    public function get_statistiques_membre($id) {
        try {
            $stats = array();

            // Total cotisations
            $this->db->select('SUM(montant) as total_cotisations');
            $this->db->from('tontine_cotisations');
            $this->db->where('membre_id', $id);
            $this->db->where('deleted', 0);
            $this->db->where('statut', 'paye');
            $query = $this->db->get();
            $stats['total_cotisations'] = (float)$query->row()->total_cotisations;

            // Nombre de cotisations
            $this->db->select('COUNT(*) as total');
            $this->db->from('tontine_cotisations');
            $this->db->where('membre_id', $id);
            $this->db->where('deleted', 0);
            $query = $this->db->get();
            $stats['nb_cotisations'] = (int)$query->row()->total;

            // Cotisations en attente
            $this->db->select('COUNT(*) as total');
            $this->db->from('tontine_cotisations');
            $this->db->where('membre_id', $id);
            $this->db->where('deleted', 0);
            $this->db->where('statut', 'en_attente');
            $query = $this->db->get();
            $stats['cotisations_attente'] = (int)$query->row()->total;

            // Participations aux collectes
            $this->db->select('COUNT(*) as total');
            $this->db->from('tontine_collectes');
            $this->db->where('membre_id', $id);
            $this->db->where('deleted', 0);
            $this->db->where('statut', 'effectue');
            $query = $this->db->get();
            $stats['nb_collectes'] = (int)$query->row()->total;

            return $stats;

        } catch (Exception $e) {
            log_message('error', 'Erreur dans get_statistiques_membre: ' . $e->getMessage());
            return array(
                'total_cotisations' => 0,
                'nb_cotisations' => 0,
                'cotisations_attente' => 0,
                'nb_collectes' => 0
            );
        }
    }
}