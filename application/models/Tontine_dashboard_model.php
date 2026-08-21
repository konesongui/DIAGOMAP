<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Tontine_membres_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // Récupérer tous les membres avec filtres
    public function get_membres($search = null, $statut = null, $groupe = null, $date_adhesion = null) {
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
    }

    // Compter les membres
    public function count_membres($search = null, $statut = null, $groupe = null, $date_adhesion = null) {
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
    }

    // Récupérer un membre
    public function get_membre($id) {
        $this->db->select('m.*, g.nom as groupe_nom');
        $this->db->from('tontine_membres m');
        $this->db->join('tontine_groupes g', 'm.groupe_id = g.id', 'left');
        $this->db->where('m.id', $id);
        $this->db->where('m.deleted', 0);
        $query = $this->db->get();
        return $query->row_array();
    }

    // Ajouter un membre
    public function ajouter($data) {
        $this->db->insert('tontine_membres', $data);
        return $this->db->insert_id();
    }

    // Mettre à jour un membre
    public function mettre_a_jour($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('tontine_membres', $data);
    }

    // Supprimer un membre
    public function supprimer($id) {
        $this->db->where('id', $id);
        return $this->db->update('tontine_membres', array('deleted' => 1));
    }
}