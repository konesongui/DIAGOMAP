<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Immobilisations_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // ========================================== //
    // MÉTHODE PRIVÉE POUR APPLIQUER LE FILTRE    //
    // ========================================== //
    private function _apply_entreprise_filter() {
        $entreprise_id = $this->session->userdata('entreprise_id');
        $columns = $this->db->list_fields('immobilisations');
        $hasEntrepriseId = in_array('entreprise_id', $columns);

        if ($hasEntrepriseId && !empty($entreprise_id)) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
    }

    // ========================================== //
    // RÉCUPÉRER TOUTES LES IMMOBILISATIONS       //
    // ========================================== //
    public function get_all($id = null) {
        $this->db->select('*');
        $this->db->from('immobilisations');
        $this->db->where('deleted', 0);

        // Application du filtre entreprise
        $this->_apply_entreprise_filter();

        if ($id != null) {
            $this->db->where('id', $id);
            $query = $this->db->get();
            return $query->row_array();
        }

        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER UNE IMMOBILISATION PAR ID        //
    // ========================================== //
    public function get_by_id($id) {
        $this->db->select('*');
        $this->db->from('immobilisations');
        $this->db->where('id', $id);
        $this->db->where('deleted', 0);

        // Application du filtre entreprise
        $this->_apply_entreprise_filter();

        $query = $this->db->get();
        return $query->row_array();
    }

    // ========================================== //
    // AJOUTER UNE IMMOBILISATION                 //
    // ========================================== //
    public function add($data) {
        // Générer un code unique si non fourni
        if (empty($data['code'])) {
            $data['code'] = $this->generate_code();
        }

        // Définir la valeur nette initiale
        if (isset($data['valeur_originale']) && !isset($data['valeur_nette'])) {
            $data['valeur_nette'] = $data['valeur_originale'];
        }

        // Définir le statut par défaut
        if (empty($data['statut'])) {
            $data['statut'] = 'actif';
        }

        // Ajout de l'entreprise_id si non fourni
        if (!isset($data['entreprise_id']) || empty($data['entreprise_id'])) {
            $entreprise_id = $this->session->userdata('entreprise_id');
            $columns = $this->db->list_fields('immobilisations');
            $hasEntrepriseId = in_array('entreprise_id', $columns);

            if ($hasEntrepriseId && !empty($entreprise_id)) {
                $data['entreprise_id'] = $entreprise_id;
            }
        }

        $this->db->insert('immobilisations', $data);
        return $this->db->insert_id();
    }

    // ========================================== //
    // METTRE À JOUR UNE IMMOBILISATION           //
    // ========================================== //
    public function update($id, $data) {
        // Vérifier que l'immobilisation appartient bien à l'entreprise
        $columns = $this->db->list_fields('immobilisations');
        $hasEntrepriseId = in_array('entreprise_id', $columns);
        $entreprise_id = $this->session->userdata('entreprise_id');

        if ($hasEntrepriseId && !empty($entreprise_id)) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where('id', $id);
        $check = $this->db->get('immobilisations')->row_array();

        if (!$check) {
            return false; // Immobilisation non trouvée ou accès non autorisé
        }

        // Recalculer la valeur nette
        if (isset($data['valeur_originale']) && isset($data['amortissement_cumule'])) {
            $data['valeur_nette'] = $data['valeur_originale'] - $data['amortissement_cumule'];
        }

        $this->db->where('id', $id);
        $this->db->update('immobilisations', $data);
        return $this->db->affected_rows();
    }

    // ========================================== //
    // SUPPRIMER UNE IMMOBILISATION               //
    // ========================================== //
    public function delete($id) {
        // Vérifier que l'immobilisation appartient bien à l'entreprise
        $columns = $this->db->list_fields('immobilisations');
        $hasEntrepriseId = in_array('entreprise_id', $columns);
        $entreprise_id = $this->session->userdata('entreprise_id');

        if ($hasEntrepriseId && !empty($entreprise_id)) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where('id', $id);
        $check = $this->db->get('immobilisations')->row_array();

        if (!$check) {
            return false; // Immobilisation non trouvée ou accès non autorisé
        }

        $this->db->where('id', $id);
        $this->db->update('immobilisations', array('deleted' => 1));
        return $this->db->affected_rows();
    }

    // ========================================== //
    // GÉNÉRER UN CODE UNIQUE                     //
    // ========================================== //
    public function generate_code() {
        $prefix = 'IMM';
        $year = date('Y');

        // Appliquer le filtre entreprise pour le compteur
        $columns = $this->db->list_fields('immobilisations');
        $hasEntrepriseId = in_array('entreprise_id', $columns);
        $entreprise_id = $this->session->userdata('entreprise_id');

        $this->db->like('code', $prefix . '-' . $year, 'after');
        if ($hasEntrepriseId && !empty($entreprise_id)) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $query = $this->db->get('immobilisations');

        $last = 0;
        foreach ($query->result() as $row) {
            $parts = explode('-', $row->code);
            if (isset($parts[2])) {
                $num = intval($parts[2]);
                if ($num > $last) $last = $num;
            }
        }

        $next = str_pad(($last + 1), 4, '0', STR_PAD_LEFT);
        return $prefix . '-' . $year . '-' . $next;
    }

    // ========================================== //
    // STATISTIQUES DES IMMOBILISATIONS           //
    // ========================================== //
    public function get_stats() {
        $stats = array();

        // Total
        $this->db->select('COUNT(*) as total, SUM(valeur_originale) as total_valeur');
        $this->db->from('immobilisations');
        $this->db->where('deleted', 0);
        $this->_apply_entreprise_filter();
        $query = $this->db->get();
        $row = $query->row();
        $stats['total'] = (int)$row->total;
        $stats['total_valeur'] = (float)$row->total_valeur;

        // Par statut
        $statuses = ['actif', 'amorti', 'ceder', 'sortie'];
        foreach ($statuses as $status) {
            $this->db->select('COUNT(*) as total');
            $this->db->from('immobilisations');
            $this->db->where('deleted', 0);
            $this->db->where('statut', $status);
            $this->_apply_entreprise_filter();
            $query = $this->db->get();
            $row = $query->row();
            $stats[$status] = (int)$row->total;
        }

        // Amortissement total
        $this->db->select('SUM(amortissement_cumule) as total_amortissement');
        $this->db->from('immobilisations');
        $this->db->where('deleted', 0);
        $this->_apply_entreprise_filter();
        $query = $this->db->get();
        $stats['total_amortissement'] = (float)$query->row()->total_amortissement;

        return $stats;
    }

    // ========================================== //
    // RÉCUPÉRER LES CATÉGORIES                   //
    // ========================================== //
    public function get_categories() {
        $this->db->distinct();
        $this->db->select('categorie');
        $this->db->from('immobilisations');
        $this->db->where('deleted', 0);
        $this->db->where('categorie !=', '');
        $this->_apply_entreprise_filter();
        $this->db->order_by('categorie', 'ASC');
        $query = $this->db->get();
        $result = $query->result_array();
        return array_column($result, 'categorie');
    }

    // ========================================== //
    // RÉCUPÉRER LES IMMOBILISATIONS FILTRÉES     //
    // ========================================== //
    public function get_filtered($categorie = null, $statut = null) {
        $this->db->select('*');
        $this->db->from('immobilisations');
        $this->db->where('deleted', 0);
        $this->_apply_entreprise_filter();

        if (!empty($categorie)) {
            $this->db->where('categorie', $categorie);
        }

        if (!empty($statut)) {
            $this->db->where('statut', $statut);
        }

        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES AMORTISSEMENTS               //
    // ========================================== //
    public function get_amortissements($immobilisation_id) {
        // Vérifier que l'immobilisation appartient bien à l'entreprise
        $check = $this->get_by_id($immobilisation_id);
        if (!$check) {
            return array();
        }

        $this->db->select('*');
        $this->db->from('amortissements');
        $this->db->where('immobilisation_id', $immobilisation_id);
        $this->db->order_by('periode_debut', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // RÉCUPÉRER LES CESSIONS                     //
    // ========================================== //
    public function get_cessions($immobilisation_id) {
        // Vérifier que l'immobilisation appartient bien à l'entreprise
        $check = $this->get_by_id($immobilisation_id);
        if (!$check) {
            return array();
        }

        $this->db->select('*');
        $this->db->from('cessions');
        $this->db->where('immobilisation_id', $immobilisation_id);
        $this->db->order_by('date_cession', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // AJOUTER UN AMORTISSEMENT                   //
    // ========================================== //
    public function add_amortissement($data) {
        // Vérifier que l'immobilisation appartient bien à l'entreprise
        $check = $this->get_by_id($data['immobilisation_id']);
        if (!$check) {
            return false;
        }

        $this->db->insert('amortissements', $data);
        return $this->db->insert_id();
    }

    // ========================================== //
    // AJOUTER UNE CESSION                        //
    // ========================================== //
    public function add_cession($data) {
        // Vérifier que l'immobilisation appartient bien à l'entreprise
        $check = $this->get_by_id($data['immobilisation_id']);
        if (!$check) {
            return false;
        }

        $this->db->insert('cessions', $data);
        return $this->db->insert_id();
    }

    // ========================================== //
    // CALCULER L'AMORTISSEMENT                   //
    // ========================================== //
    public function calculer_amortissement($id) {
        $immobilisation = $this->get_by_id($id);
        if (!$immobilisation) return false;

        // Vérifier si déjà amorti
        if ($immobilisation['statut'] == 'amorti' || $immobilisation['statut'] == 'ceder') {
            return false;
        }

        $valeur_originale = $immobilisation['valeur_originale'];
        $duree = $immobilisation['duree_amortissement'];

        if (empty($duree) || $duree <= 0) {
            return false;
        }

        // Amortissement linéaire
        $amortissement_annuel = $valeur_originale / $duree;
        $amortissement_mensuel = $amortissement_annuel / 12;

        // Calculer l'amortissement cumulé depuis la date d'acquisition
        $date_acquisition = new DateTime($immobilisation['date_acquisition']);
        $now = new DateTime();
        $interval = $date_acquisition->diff($now);
        $mois = ($interval->y * 12) + $interval->m;

        if ($mois > 0) {
            $amortissement_cumule = $amortissement_mensuel * $mois;
            if ($amortissement_cumule > $valeur_originale) {
                $amortissement_cumule = $valeur_originale;
            }

            // Mettre à jour
            $valeur_nette = $valeur_originale - $amortissement_cumule;
            $data = array(
                'amortissement_cumule' => $amortissement_cumule,
                'valeur_nette' => $valeur_nette
            );

            if ($valeur_nette <= 0) {
                $data['statut'] = 'amorti';
            }

            $this->update($id, $data);

            // Ajouter une ligne d'amortissement
            $this->add_amortissement(array(
                'immobilisation_id' => $id,
                'periode_debut' => $date_acquisition->format('Y-m-d'),
                'periode_fin' => $now->format('Y-m-d'),
                'montant' => $amortissement_cumule,
                'type' => 'effectif',
                'description' => 'Amortissement calculé automatiquement'
            ));

            return array(
                'amortissement_cumule' => $amortissement_cumule,
                'valeur_nette' => $valeur_nette,
                'mensuel' => $amortissement_mensuel,
                'annuel' => $amortissement_annuel
            );
        }

        return false;
    }

    // ========================================== //
    // CÉDER UNE IMMOBILISATION                   //
    // ========================================== //
    public function ceder($id, $montant, $acheteur, $motif = null) {
        $immobilisation = $this->get_by_id($id);
        if (!$immobilisation) return false;

        if ($immobilisation['statut'] == 'ceder') {
            return false;
        }

        // Enregistrer la cession
        $cession_data = array(
            'immobilisation_id' => $id,
            'date_cession' => date('Y-m-d'),
            'montant_cession' => $montant,
            'acheteur' => $acheteur,
            'motif' => $motif
        );
        $this->add_cession($cession_data);

        // Mettre à jour le statut
        $this->update($id, array('statut' => 'ceder'));
        return true;
    }

    // ========================================== //
    // RECHERCHER DES IMMOBILISATIONS             //
    // ========================================== //
    public function search($keyword) {
        $this->db->select('*');
        $this->db->from('immobilisations');
        $this->db->where('deleted', 0);
        $this->_apply_entreprise_filter();

        $this->db->group_start();
        $this->db->like('nom', $keyword);
        $this->db->or_like('code', $keyword);
        $this->db->or_like('categorie', $keyword);
        $this->db->or_like('description', $keyword);
        $this->db->or_like('num_serie', $keyword);
        $this->db->group_end();

        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }

    // ========================================== //
    // SUPPRESSION MULTIPLE                       //
    // ========================================== //
    public function delete_multiple($ids) {
        if (empty($ids)) {
            return false;
        }

        // Vérifier que toutes les immobilisations appartiennent à l'entreprise
        $columns = $this->db->list_fields('immobilisations');
        $hasEntrepriseId = in_array('entreprise_id', $columns);
        $entreprise_id = $this->session->userdata('entreprise_id');

        if ($hasEntrepriseId && !empty($entreprise_id)) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where_in('id', $ids);
        $this->db->update('immobilisations', array('deleted' => 1));
        return $this->db->affected_rows();
    }

    // ========================================== //
    // RÉCUPÉRER LES IMMOBILISATIONS PAR ENTREPRISE //
    // ========================================== //
    public function get_by_entreprise($entreprise_id) {
        $this->db->select('*');
        $this->db->from('immobilisations');
        $this->db->where('deleted', 0);

        $columns = $this->db->list_fields('immobilisations');
        if (in_array('entreprise_id', $columns) && !empty($entreprise_id)) {
            $this->db->where('entreprise_id', $entreprise_id);
        }

        $this->db->order_by('id', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }
}