<?php
// ============================================================
// MODÈLE : Association_membre_model
// DESCRIPTION : Gestion des adhérents de l'association
// TABLES : association_membres, association_categories, association_membres_logs
// ============================================================

defined('BASEPATH') OR exit('No direct script access allowed');

class Association_membre_model extends CI_Model {

    protected $table_membres = 'association_membres';
    protected $table_categories = 'association_categories';
    protected $table_logs = 'association_membres_logs';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Récupère tous les adhérents avec filtres
     */
    public function get_all_membres($filters = [], $limit = null, $offset = null) {
        $this->db->select('m.*, c.nom as categorie_nom, c.couleur as categorie_couleur');
        $this->db->from($this->table_membres . ' m');
        $this->db->join($this->table_categories . ' c', 'c.id = m.categorie_id', 'left');
        $this->db->where('m.deleted_at IS NULL');

        // Filtres
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $this->db->group_start();
            $this->db->like('m.nom', $search);
            $this->db->or_like('m.prenom', $search);
            $this->db->or_like('m.email', $search);
            $this->db->or_like('m.telephone', $search);
            $this->db->or_like('m.matricule', $search);
            $this->db->group_end();
        }

        if (!empty($filters['type_membre'])) {
            $this->db->where('m.type_membre', $filters['type_membre']);
        }

        if (isset($filters['statut']) && $filters['statut'] !== '') {
            $this->db->where('m.statut', $filters['statut']);
        }

        if (!empty($filters['categorie_id'])) {
            $this->db->where('m.categorie_id', $filters['categorie_id']);
        }

        if (!empty($filters['date_debut'])) {
            $this->db->where('m.date_adhesion >=', $filters['date_debut']);
        }

        if (!empty($filters['date_fin'])) {
            $this->db->where('m.date_adhesion <=', $filters['date_fin']);
        }

        $this->db->order_by('m.nom', 'ASC');

        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Compte le nombre total d'adhérents
     */
    public function count_membres($filters = []) {
        $this->db->from($this->table_membres . ' m');
        $this->db->where('m.deleted_at IS NULL');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $this->db->group_start();
            $this->db->like('m.nom', $search);
            $this->db->or_like('m.prenom', $search);
            $this->db->or_like('m.email', $search);
            $this->db->or_like('m.telephone', $search);
            $this->db->or_like('m.matricule', $search);
            $this->db->group_end();
        }

        if (!empty($filters['type_membre'])) {
            $this->db->where('m.type_membre', $filters['type_membre']);
        }

        if (isset($filters['statut']) && $filters['statut'] !== '') {
            $this->db->where('m.statut', $filters['statut']);
        }

        return $this->db->count_all_results();
    }

    /**
     * Récupère un adhérent par son ID
     */
    public function get_membre_by_id($id) {
        $this->db->select('m.*, c.nom as categorie_nom, c.couleur as categorie_couleur, c.montant_defaut');
        $this->db->from($this->table_membres . ' m');
        $this->db->join($this->table_categories . ' c', 'c.id = m.categorie_id', 'left');
        $this->db->where('m.id', $id);
        $this->db->where('m.deleted_at IS NULL');
        $query = $this->db->get();
        return $query->row();
    }

    /**
     * Récupère un adhérent par email
     */
    public function get_membre_by_email($email) {
        $this->db->where('email', $email);
        $this->db->where('deleted_at IS NULL');
        $query = $this->db->get($this->table_membres);
        return $query->row();
    }

    /**
     * Récupère un adhérent par matricule
     */
    public function get_membre_by_matricule($matricule) {
        $this->db->where('matricule', $matricule);
        $this->db->where('deleted_at IS NULL');
        $query = $this->db->get($this->table_membres);
        return $query->row();
    }

    /**
     * Crée un nouvel adhérent
     */
    public function create_membre($data) {
        // Générer un matricule unique
        $data['matricule'] = $this->generate_matricule();

        if (empty($data['date_adhesion'])) {
            $data['date_adhesion'] = date('Y-m-d');
        }

        $data['created_at'] = date('Y-m-d H:i:s');

        $this->db->insert($this->table_membres, $data);
        $insert_id = $this->db->insert_id();

        // Journaliser la création
        $this->log_action($insert_id, 'creation', null, $data);

        return $insert_id;
    }

    /**
     * Met à jour un adhérent
     */
    public function update_membre($id, $data) {
        // Récupérer l'ancien adhérent pour le journal
        $old_membre = $this->get_membre_by_id($id);

        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        $this->db->update($this->table_membres, $data);

        // Journaliser la modification
        $this->log_action($id, 'modification', $old_membre, $data);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Supprime un adhérent (soft delete)
     */
    public function delete_membre($id) {
        $old_membre = $this->get_membre_by_id($id);

        $data = ['deleted_at' => date('Y-m-d H:i:s')];
        $this->db->where('id', $id);
        $this->db->update($this->table_membres, $data);

        $this->log_action($id, 'suppression', $old_membre, null);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Active ou désactive un adhérent
     */
    public function toggle_statut($id, $statut) {
        $old_membre = $this->get_membre_by_id($id);

        $data = ['statut' => $statut, 'updated_at' => date('Y-m-d H:i:s')];
        $this->db->where('id', $id);
        $this->db->update($this->table_membres, $data);

        $action = $statut == 1 ? 'reactivation' : 'desactivation';
        $this->log_action($id, $action, $old_membre, $data);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Génère un matricule unique
     */
    private function generate_matricule() {
        $annee = date('Y');
        $prefix = 'MEM-' . $annee . '-';

        $this->db->select('matricule');
        $this->db->from($this->table_membres);
        $this->db->like('matricule', $prefix, 'after');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        $last = $query->row();

        if ($last && !empty($last->matricule)) {
            $parts = explode('-', $last->matricule);
            $num = intval(end($parts)) + 1;
        } else {
            $num = 1;
        }

        return $prefix . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Journalise une action
     */
    private function log_action($membre_id, $action, $old_data = null, $new_data = null) {
        $log = [
            'membre_id' => $membre_id,
            'action' => $action,
            'user_id' => $this->session->userdata('staff_id') ?? 0,
            'ip_address' => $this->input->ip_address()
        ];

        if ($old_data) {
            $log['anciennes_valeurs'] = json_encode($old_data);
        }

        if ($new_data) {
            $log['nouvelles_valeurs'] = json_encode($new_data);

            // Identifier les champs modifiés
            if ($old_data) {
                $changed = [];
                foreach ($new_data as $key => $value) {
                    if (isset($old_data->$key) && $old_data->$key != $value) {
                        $changed[] = $key;
                    }
                }
                $log['champs_modifies'] = implode(', ', $changed);
            }
        }

        $this->db->insert($this->table_logs, $log);
    }

    /**
     * Récupère toutes les catégories
     */
    public function get_all_categories() {
        $this->db->where('statut', 1);
        $this->db->order_by('nom', 'ASC');
        $query = $this->db->get($this->table_categories);
        return $query->result();
    }

    /**
     * Récupère une catégorie par ID
     */
    public function get_categorie_by_id($id) {
        $this->db->where('id', $id);
        $query = $this->db->get($this->table_categories);
        return $query->row();
    }

    /**
     * Statistiques rapides
     */
    public function get_stats() {
        $stats = [];

        // Total des adhérents
        $this->db->where('deleted_at IS NULL');
        $stats['total'] = $this->db->count_all_results($this->table_membres);

        // Adhérents actifs
        $this->db->where('statut', 1);
        $this->db->where('deleted_at IS NULL');
        $stats['actifs'] = $this->db->count_all_results($this->table_membres);

        // Adhérents inactifs
        $this->db->where('statut', 0);
        $this->db->where('deleted_at IS NULL');
        $stats['inactifs'] = $this->db->count_all_results($this->table_membres);

        // Nouveaux ce mois-ci
        $this->db->where('MONTH(date_adhesion)', date('m'));
        $this->db->where('YEAR(date_adhesion)', date('Y'));
        $this->db->where('deleted_at IS NULL');
        $stats['nouveaux_mois'] = $this->db->count_all_results($this->table_membres);

        return $stats;
    }

    /**
     * Récupère l'historique d'un adhérent
     */
    public function get_historique($membre_id, $limit = 20) {
        $this->db->where('membre_id', $membre_id);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get($this->table_logs);
        return $query->result();
    }
}