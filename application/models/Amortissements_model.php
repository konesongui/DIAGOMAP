<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Amortissements_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // ========================================== //
    // MÉTHODE PRIVÉE POUR APPLIQUER LE FILTRE    //
    // ========================================== //
    private function _apply_entreprise_filter($table_alias = null) {
        $entreprise_id = $this->session->userdata('entreprise_id');
        $columns = $this->db->list_fields('immobilisations');
        $hasEntrepriseId = in_array('entreprise_id', $columns);

        if ($hasEntrepriseId && !empty($entreprise_id)) {
            $prefix = $table_alias ? $table_alias . '.' : '';
            $this->db->where($prefix . 'entreprise_id', $entreprise_id);
        }
    }

    // ========================================== //
    // RÉCUPÉRER TOUS LES AMORTISSEMENTS          //
    // ========================================== //
    public function get_all($id = null) {
        $this->db->select('a.*, i.nom as immobilisation_nom, i.code as immobilisation_code, i.categorie');
        $this->db->from('amortissements a');
        $this->db->join('immobilisations i', 'a.immobilisation_id = i.id', 'left');
        $this->db->where('i.deleted', 0);

        // Application du filtre entreprise sur la table immobilisations
        $this->_apply_entreprise_filter('i');

        if ($id != null) {
            $this->db->where('a.id', $id);
            $query = $this->db->get();
            return $query->row_array();
        }

        $this->db->order_by('a.periode_debut', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES AMORTISSEMENTS FILTRÉS       //
    // ========================================== //
    public function get_filtered($date_from = null, $date_to = null, $categorie = null, $statut = null) {
        $this->db->select('a.*, i.nom as immobilisation_nom, i.code as immobilisation_code, i.categorie, i.statut as immobilisation_statut');
        $this->db->from('amortissements a');
        $this->db->join('immobilisations i', 'a.immobilisation_id = i.id', 'left');
        $this->db->where('i.deleted', 0);

        // Application du filtre entreprise sur la table immobilisations
        $this->_apply_entreprise_filter('i');

        if (!empty($date_from)) {
            $this->db->where('a.periode_debut >=', $date_from);
        }

        if (!empty($date_to)) {
            $this->db->where('a.periode_fin <=', $date_to);
        }

        if (!empty($categorie)) {
            $this->db->where('i.categorie', $categorie);
        }

        if (!empty($statut)) {
            $this->db->where('i.statut', $statut);
        }

        $this->db->order_by('a.periode_debut', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // STATISTIQUES DES AMORTISSEMENTS            //
    // ========================================== //
    public function get_stats() {
        $stats = array();

        // Récupérer l'entreprise_id
        $entreprise_id = $this->session->userdata('entreprise_id');
        $columns = $this->db->list_fields('immobilisations');
        $hasEntrepriseId = in_array('entreprise_id', $columns);

        // Total amortissement
        $this->db->select('COUNT(a.id) as total, SUM(a.montant) as total_montant');
        $this->db->from('amortissements a');
        $this->db->join('immobilisations i', 'a.immobilisation_id = i.id', 'left');
        $this->db->where('i.deleted', 0);
        if ($hasEntrepriseId && !empty($entreprise_id)) {
            $this->db->where('i.entreprise_id', $entreprise_id);
        }
        $query = $this->db->get();
        $row = $query->row();
        $stats['total'] = (int)$row->total;
        $stats['total_montant'] = (float)$row->total_montant;

        // Par type
        $types = ['effectif', 'previsionnel'];
        foreach ($types as $type) {
            $this->db->select('COUNT(a.id) as total, SUM(a.montant) as montant');
            $this->db->from('amortissements a');
            $this->db->join('immobilisations i', 'a.immobilisation_id = i.id', 'left');
            $this->db->where('i.deleted', 0);
            $this->db->where('a.type', $type);
            if ($hasEntrepriseId && !empty($entreprise_id)) {
                $this->db->where('i.entreprise_id', $entreprise_id);
            }
            $query = $this->db->get();
            $row = $query->row();
            $stats[$type] = (int)$row->total;
            $stats[$type . '_montant'] = (float)$row->montant;
        }

        // Par catégorie
        $this->db->select('i.categorie, COUNT(a.id) as total, SUM(a.montant) as montant');
        $this->db->from('amortissements a');
        $this->db->join('immobilisations i', 'a.immobilisation_id = i.id', 'left');
        $this->db->where('i.deleted', 0);
        if ($hasEntrepriseId && !empty($entreprise_id)) {
            $this->db->where('i.entreprise_id', $entreprise_id);
        }
        $this->db->group_by('i.categorie');
        $query = $this->db->get();
        $stats['categories'] = $query->result_array();

        // Par année
        $this->db->select('YEAR(a.periode_debut) as annee, COUNT(a.id) as total, SUM(a.montant) as montant');
        $this->db->from('amortissements a');
        $this->db->join('immobilisations i', 'a.immobilisation_id = i.id', 'left');
        $this->db->where('i.deleted', 0);
        if ($hasEntrepriseId && !empty($entreprise_id)) {
            $this->db->where('i.entreprise_id', $entreprise_id);
        }
        $this->db->group_by('YEAR(a.periode_debut)');
        $this->db->order_by('annee', 'DESC');
        $query = $this->db->get();
        $stats['par_annee'] = $query->result_array();

        return $stats;
    }

    // ========================================== //
    // RÉCUPÉRER LES AMORTISSEMENTS PAR IMMO      //
    // ========================================== //
    public function get_by_immobilisation($immobilisation_id) {
        // Vérifier que l'immobilisation appartient bien à l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        $columns = $this->db->list_fields('immobilisations');
        $hasEntrepriseId = in_array('entreprise_id', $columns);

        if ($hasEntrepriseId && !empty($entreprise_id)) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where('id', $immobilisation_id);
        $this->db->where('deleted', 0);
        $check = $this->db->get('immobilisations')->row_array();

        if (!$check) {
            return array(); // Immobilisation non trouvée ou accès non autorisé
        }

        $this->db->select('*');
        $this->db->from('amortissements');
        $this->db->where('immobilisation_id', $immobilisation_id);
        $this->db->order_by('periode_debut', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // CALCULER LE PLAN D'AMORTISSEMENT           //
    // ========================================== //
    public function get_plan_amortissement($immobilisation_id) {
        // Vérifier que l'immobilisation appartient bien à l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        $columns = $this->db->list_fields('immobilisations');
        $hasEntrepriseId = in_array('entreprise_id', $columns);

        if ($hasEntrepriseId && !empty($entreprise_id)) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where('id', $immobilisation_id);
        $this->db->where('deleted', 0);
        $immobilisation = $this->db->get('immobilisations')->row_array();

        if (!$immobilisation) return array();

        $plan = array();
        $valeur_originale = $immobilisation['valeur_originale'];
        $duree = $immobilisation['duree_amortissement'];

        if (empty($duree) || $duree <= 0) return array();

        $amortissement_annuel = $valeur_originale / $duree;
        $date_acquisition = new DateTime($immobilisation['date_acquisition']);

        for ($i = 1; $i <= $duree; $i++) {
            $annee = $date_acquisition->format('Y') + $i;
            $plan[] = array(
                'annee' => $annee,
                'montant' => $amortissement_annuel,
                'cumul' => $amortissement_annuel * $i,
                'valeur_residuelle' => $valeur_originale - ($amortissement_annuel * $i)
            );
        }

        return $plan;
    }

    // ========================================== //
    // EXPORTER EN CSV                            //
    // ========================================== //
    public function export_csv($date_from = null, $date_to = null, $categorie = null) {
        $data = $this->get_filtered($date_from, $date_to, $categorie);
        return $data;
    }

    // ========================================== //
    // AJOUTER UN AMORTISSEMENT                   //
    // ========================================== //
    public function add($data) {
        // Vérifier que l'immobilisation appartient bien à l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        $columns = $this->db->list_fields('immobilisations');
        $hasEntrepriseId = in_array('entreprise_id', $columns);

        if ($hasEntrepriseId && !empty($entreprise_id)) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where('id', $data['immobilisation_id']);
        $this->db->where('deleted', 0);
        $check = $this->db->get('immobilisations')->row_array();

        if (!$check) {
            return false; // Immobilisation non trouvée ou accès non autorisé
        }

        $this->db->insert('amortissements', $data);
        return $this->db->insert_id();
    }

    // ========================================== //
    // METTRE À JOUR UN AMORTISSEMENT             //
    // ========================================== //
    public function update($id, $data) {
        // Vérifier que l'amortissement appartient à une immobilisation de l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        $columns = $this->db->list_fields('immobilisations');
        $hasEntrepriseId = in_array('entreprise_id', $columns);

        $this->db->select('a.*');
        $this->db->from('amortissements a');
        $this->db->join('immobilisations i', 'a.immobilisation_id = i.id', 'left');
        $this->db->where('a.id', $id);
        if ($hasEntrepriseId && !empty($entreprise_id)) {
            $this->db->where('i.entreprise_id', $entreprise_id);
        }
        $this->db->where('i.deleted', 0);
        $check = $this->db->get()->row_array();

        if (!$check) {
            return false; // Amortissement non trouvé ou accès non autorisé
        }

        $this->db->where('id', $id);
        $this->db->update('amortissements', $data);
        return $this->db->affected_rows();
    }

    // ========================================== //
    // SUPPRIMER UN AMORTISSEMENT                 //
    // ========================================== //
    public function delete($id) {
        // Vérifier que l'amortissement appartient à une immobilisation de l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        $columns = $this->db->list_fields('immobilisations');
        $hasEntrepriseId = in_array('entreprise_id', $columns);

        $this->db->select('a.*');
        $this->db->from('amortissements a');
        $this->db->join('immobilisations i', 'a.immobilisation_id = i.id', 'left');
        $this->db->where('a.id', $id);
        if ($hasEntrepriseId && !empty($entreprise_id)) {
            $this->db->where('i.entreprise_id', $entreprise_id);
        }
        $this->db->where('i.deleted', 0);
        $check = $this->db->get()->row_array();

        if (!$check) {
            return false; // Amortissement non trouvé ou accès non autorisé
        }

        $this->db->where('id', $id);
        $this->db->delete('amortissements');
        return $this->db->affected_rows();
    }

    // ========================================== //
    // SUPPRESSION MULTIPLE                       //
    // ========================================== //
    public function delete_multiple($ids) {
        if (empty($ids)) {
            return false;
        }

        // Vérifier que tous les amortissements appartiennent à des immobilisations de l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        $columns = $this->db->list_fields('immobilisations');
        $hasEntrepriseId = in_array('entreprise_id', $columns);

        $this->db->select('a.id');
        $this->db->from('amortissements a');
        $this->db->join('immobilisations i', 'a.immobilisation_id = i.id', 'left');
        $this->db->where_in('a.id', $ids);
        if ($hasEntrepriseId && !empty($entreprise_id)) {
            $this->db->where('i.entreprise_id', $entreprise_id);
        }
        $this->db->where('i.deleted', 0);
        $valid_ids = $this->db->get()->result_array();

        if (count($valid_ids) != count($ids)) {
            return false; // Certains amortissements ne sont pas accessibles
        }

        $this->db->where_in('id', $ids);
        $this->db->delete('amortissements');
        return $this->db->affected_rows();
    }

    // ========================================== //
    // RÉCUPÉRER LES AMORTISSEMENTS PAR PÉRIODE   //
    // ========================================== //
    public function get_by_periode($date_debut, $date_fin) {
        $this->db->select('a.*, i.nom as immobilisation_nom, i.code as immobilisation_code');
        $this->db->from('amortissements a');
        $this->db->join('immobilisations i', 'a.immobilisation_id = i.id', 'left');
        $this->db->where('i.deleted', 0);
        $this->db->where('a.periode_debut >=', $date_debut);
        $this->db->where('a.periode_fin <=', $date_fin);

        // Application du filtre entreprise
        $this->_apply_entreprise_filter('i');

        $this->db->order_by('a.periode_debut', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LE TOTAL DES AMORTISSEMENTS      //
    // ========================================== //
    public function get_total_amortissement($date_from = null, $date_to = null) {
        $this->db->select('SUM(a.montant) as total');
        $this->db->from('amortissements a');
        $this->db->join('immobilisations i', 'a.immobilisation_id = i.id', 'left');
        $this->db->where('i.deleted', 0);

        if (!empty($date_from)) {
            $this->db->where('a.periode_debut >=', $date_from);
        }
        if (!empty($date_to)) {
            $this->db->where('a.periode_fin <=', $date_to);
        }

        // Application du filtre entreprise
        $this->_apply_entreprise_filter('i');

        $query = $this->db->get();
        $row = $query->row();
        return (float)$row->total;
    }
}