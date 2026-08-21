<?php
// ============================================================
// MODÈLE : Membre_model
// DESCRIPTION : Gestion des adhérents de l'association
// ============================================================

defined('BASEPATH') OR exit('No direct script access allowed');

class Membre_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->table = 'association_membres';
        $this->table_categorie = 'categorie_membre';
        $this->table_historique = 'historique_adhérents';
    }

    /**
     * Récupère tous les membres avec filtres
     */
    public function get_all_membres($filters = [], $limit = null, $offset = null) {
        $this->db->select('m.*, c.nom as categorie_nom, c.couleur as categorie_couleur');
        $this->db->from($this->table . ' m');
        $this->db->join($this->table_categorie . ' c', 'c.id = m.categorie_id', 'left');
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

        if (!empty($filters['type_adhérent'])) {
            $this->db->where('m.type_adhérent', $filters['type_adhérent']);
        }

        if (!empty($filters['statut']) && $filters['statut'] !== '') {
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
     * Compte le nombre total de membres
     */
    public function count_membres($filters = []) {
        $this->db->from($this->table . ' m');
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

        if (!empty($filters['type_adhérent'])) {
            $this->db->where('m.type_adhérent', $filters['type_adhérent']);
        }

        if (!empty($filters['statut']) && $filters['statut'] !== '') {
            $this->db->where('m.statut', $filters['statut']);
        }

        return $this->db->count_all_results();
    }

    /**
     * Récupère un membre par son ID
     */
    public function get_membre_by_id($id) {
        $this->db->select('m.*, c.nom as categorie_nom, c.couleur as categorie_couleur');
        $this->db->from($this->table . ' m');
        $this->db->join($this->table_categorie . ' c', 'c.id = m.categorie_id', 'left');
        $this->db->where('m.id', $id);
        $this->db->where('m.deleted_at IS NULL');
        $query = $this->db->get();
        return $query->row();
    }

    /**
     * Récupère un membre par email
     */
    public function get_membre_by_email($email) {
        $this->db->where('email', $email);
        $this->db->where('deleted_at IS NULL');
        $query = $this->db->get($this->table);
        return $query->row();
    }

    /**
     * Récupère un membre par matricule
     */
    public function get_membre_by_matricule($matricule) {
        $this->db->where('matricule', $matricule);
        $this->db->where('deleted_at IS NULL');
        $query = $this->db->get($this->table);
        return $query->row();
    }

    /**
     * Récupère les derniers membres inscrits
     */
    public function get_derniers_membres($limit = 5) {
        $this->db->select('m.*, c.nom as categorie_nom');
        $this->db->from($this->table . ' m');
        $this->db->join($this->table_categorie . ' c', 'c.id = m.categorie_id', 'left');
        $this->db->where('m.deleted_at IS NULL');
        $this->db->where('m.statut', 1);
        $this->db->order_by('m.created_at', 'DESC');
        $this->db->limit($limit);
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Statistiques des membres
     */
    public function get_stats_membres() {
        $stats = [];

        // Total des membres
        $this->db->where('deleted_at IS NULL');
        $stats['total'] = $this->db->count_all_results($this->table);

        // Membres actifs
        $this->db->where('deleted_at IS NULL');
        $this->db->where('statut', 1);
        $stats['actifs'] = $this->db->count_all_results($this->table);

        // Membres inactifs
        $this->db->where('deleted_at IS NULL');
        $this->db->where('statut', 0);
        $stats['inactifs'] = $this->db->count_all_results($this->table);

        // Nouveaux ce mois
        $this->db->where('deleted_at IS NULL');
        $this->db->where('MONTH(created_at)', date('m'));
        $this->db->where('YEAR(created_at)', date('Y'));
        $stats['nouveaux_mois'] = $this->db->count_all_results($this->table);

        // Répartition par type
        $this->db->select('type_adhérent, COUNT(*) as count');
        $this->db->where('deleted_at IS NULL');
        $this->db->group_by('type_adhérent');
        $query = $this->db->get($this->table);
        $stats['par_type'] = $query->result();

        // Répartition par catégorie
        $this->db->select('c.nom, COUNT(m.id) as count');
        $this->db->from($this->table . ' m');
        $this->db->join($this->table_categorie . ' c', 'c.id = m.categorie_id', 'left');
        $this->db->where('m.deleted_at IS NULL');
        $this->db->group_by('m.categorie_id');
        $query = $this->db->get();
        $stats['par_categorie'] = $query->result();

        return $stats;
    }

    /**
     * Crée un nouveau membre
     */
    public function create_membre($data) {
        // Générer un matricule unique
        $data['matricule'] = $this->generate_matricule();

        $data['created_at'] = date('Y-m-d H:i:s');
        $data['date_adhesion'] = $data['date_adhesion'] ?? date('Y-m-d');

        $this->db->insert($this->table, $data);
        $insert_id = $this->db->insert_id();

        // Journaliser la création
        $this->log_action($insert_id, 'creation', null, $data);

        return $insert_id;
    }

    /**
     * Met à jour un membre
     */
    public function update_membre($id, $data) {
        // Récupérer l'ancien membre pour le journal
        $old_membre = $this->get_membre_by_id($id);

        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        $this->db->update($this->table, $data);

        // Journaliser la modification
        $this->log_action($id, 'modification', $old_membre, $data);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Supprime un membre (soft delete)
     */
    public function delete_membre($id) {
        $old_membre = $this->get_membre_by_id($id);

        $data = ['deleted_at' => date('Y-m-d H:i:s')];
        $this->db->where('id', $id);
        $this->db->update($this->table, $data);

        $this->log_action($id, 'suppression', $old_membre, null);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Active ou désactive un membre
     */
    public function toggle_statut($id, $statut) {
        $old_membre = $this->get_membre_by_id($id);

        $data = ['statut' => $statut];
        $this->db->where('id', $id);
        $this->db->update($this->table, $data);

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

        $this->db->select_max('id');
        $query = $this->db->get($this->table);
        $row = $query->row();
        $next_id = ($row->id ?? 0) + 1;

        return $prefix . str_pad($next_id, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Journalise une action
     */
    private function log_action($membre_id, $action, $old_data = null, $new_data = null) {
        $log = [
            'membre_id' => $membre_id,
            'action' => $action,
            'user_id' => $this->session->userdata('admin_id') ?? 0,
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent()
        ];

        if ($old_data && $new_data) {
            $diff = [];
            foreach ($new_data as $key => $value) {
                if (isset($old_data->$key) && $old_data->$key != $value) {
                    $diff[$key] = [
                        'ancien' => $old_data->$key,
                        'nouveau' => $value
                    ];
                }
            }
            if (!empty($diff)) {
                $log['champs_modifies'] = json_encode(array_keys($diff));
                $log['anciennes_valeurs'] = json_encode($diff);
            }
        }

        $this->db->insert($this->table_historique, $log);
    }

    /**
     * Récupère l'historique d'un membre
     */
    public function get_historique_membre($membre_id) {
        $this->db->where('membre_id', $membre_id);
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get($this->table_historique);
        return $query->result();
    }

    /**
     * Importe des membres depuis un fichier CSV
     */
    public function import_membres($data) {
        $imported = 0;
        $errors = [];

        foreach ($data as $row) {
            // Vérifier si l'email existe déjà
            if (!empty($row['email']) && $this->get_membre_by_email($row['email'])) {
                $errors[] = "Email '{$row['email']}' existe déjà";
                continue;
            }

            // Ajouter le membre
            $row['created_at'] = date('Y-m-d H:i:s');
            $row['date_adhesion'] = $row['date_adhesion'] ?? date('Y-m-d');
            $row['matricule'] = $this->generate_matricule();

            $this->db->insert($this->table, $row);
            if ($this->db->affected_rows() > 0) {
                $imported++;
            }
        }

        return [
            'imported' => $imported,
            'errors' => $errors
        ];
    }
}
?>