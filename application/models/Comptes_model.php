<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Comptes_model extends MY_Model {

    public function __construct() {
        parent::__construct();
        $this->table = 'compte_entreprise';
    }

    public function getDatatableExpenseHead() {
        $this->datatables
            ->select('*')
            ->from('compte_entreprise');
        return $this->datatables->generate('json');
    }

    public function get($id = null)
    {
        $select = 'ce.*';
        if ($this->db->field_exists('parent_entreprise_id', 'compte_entreprise')) {
            $select .= ', parent.nom as parent_nom';
        }
        if ($this->db->table_exists('entreprise_succursales')) {
            $select .= ', rel.siege_entreprise_id, rel.relation_status, rel.inherit_settings, rel.inherit_roles, rel.inherit_ohada';
        }
        $this->db->select($select, false);
        $this->db->from('compte_entreprise ce');
        if ($this->db->table_exists('entreprise_succursales')) {
            $this->db->join('entreprise_succursales rel', 'rel.succursale_entreprise_id = ce.id', 'left');
        }
        if ($this->db->field_exists('parent_entreprise_id', 'compte_entreprise')) {
            $this->db->join('compte_entreprise parent', 'parent.id = ce.parent_entreprise_id', 'left');
        }

        if ($id != null) {
            $this->db->where('ce.id', $id);
            $query = $this->db->get();
            return $query->row();
        } else {
            $this->db->order_by('ce.id', 'DESC');
            $query = $this->db->get();
            return $query->result();
        }
    }

    // ... (le reste du code reste identique à votre version précédente)

    /**
     * Alternative method for DataTables without using the datatables library
     */
    public function get_datatables_data() {
        $this->apply_company_listing_query();

        // Handle search
        if (isset($_POST['search']['value'])) {
            $search = trim((string) $_POST['search']['value']);
            $this->db->group_start();
            $this->db->like('ce.nom', $search);
            $this->db->or_like('ce.email', $search);
            $this->db->or_like('ce.telephone', $search);
            $this->db->or_like('ce.forfait', $search);
            $this->db->or_like('ce.statut', $search);
            if ($this->db->field_exists('type_structure', 'compte_entreprise')) {
                $this->db->or_like('ce.type_structure', $search);
                $this->db->or_like('ce.code_succursale', $search);
            }
            if ($this->db->field_exists('can_manage_succursales', 'compte_entreprise')) {
                $this->db->or_like('ce.can_manage_succursales', $search);
            }
            if ($this->db->field_exists('parent_entreprise_id', 'compte_entreprise')) {
                $this->db->or_like('parent.nom', $search);
            }
            $this->db->group_end();
        }

        // Handle order
        if (isset($_POST['order'])) {
            $column = $_POST['order'][0]['column'];
            $dir = $_POST['order'][0]['dir'];
            $columns = ['ce.nom', 'ce.email', 'ce.telephone', 'ce.adresse', 'structure_label', 'parent_nom', 'ce.forfait', 'ce.date_debut', 'ce.date_expiration', 'ce.statut'];

            if (isset($columns[$column])) {
                $this->db->order_by($columns[$column], $dir);
            }
        } else {
            $this->db->order_by('ce.id', 'DESC');
        }

        // Handle pagination
        if ($_POST['length'] != -1) {
            $this->db->limit($_POST['length'], $_POST['start']);
        }

        return $this->db->get()->result_array();
    }

    public function count_all() {
        return (int) $this->db->count_all($this->table);
    }

    public function count_filtered() {
        $this->apply_company_listing_query(false);

        if (isset($_POST['search']['value'])) {
            $search = trim((string) $_POST['search']['value']);
            $this->db->group_start();
            $this->db->like('ce.nom', $search);
            $this->db->or_like('ce.email', $search);
            $this->db->or_like('ce.telephone', $search);
            $this->db->or_like('ce.forfait', $search);
            $this->db->or_like('ce.statut', $search);
            if ($this->db->field_exists('type_structure', 'compte_entreprise')) {
                $this->db->or_like('ce.type_structure', $search);
                $this->db->or_like('ce.code_succursale', $search);
            }
            if ($this->db->field_exists('parent_entreprise_id', 'compte_entreprise')) {
                $this->db->or_like('parent.nom', $search);
            }
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }

    protected function apply_company_listing_query($with_select = true)
    {
        if ($with_select) {
            $select = 'ce.*';
            if ($this->db->field_exists('type_structure', 'compte_entreprise')) {
                $select .= ", COALESCE(ce.type_structure, 'siege') as structure_label";
            }
            if ($this->db->field_exists('can_manage_succursales', 'compte_entreprise')) {
                $select .= ', ce.can_manage_succursales';
            }
            if ($this->db->field_exists('parent_entreprise_id', 'compte_entreprise')) {
                $select .= ', parent.nom as parent_nom';
            }
            if ($this->db->table_exists('entreprise_succursales')) {
                $select .= ', rel.relation_status, rel.inherit_settings, rel.inherit_roles, rel.inherit_ohada';
            }
            $this->db->select($select, false);
        }

        $this->db->from('compte_entreprise ce');
        if ($this->db->field_exists('parent_entreprise_id', 'compte_entreprise')) {
            $this->db->join('compte_entreprise parent', 'parent.id = ce.parent_entreprise_id', 'left');
        }
        if ($this->db->table_exists('entreprise_succursales')) {
            $this->db->join('entreprise_succursales rel', 'rel.succursale_entreprise_id = ce.id', 'left');
        }
    }

    public function getHeadOfficeOptions($exclude_id = null)
    {
        $select = 'id, nom';
        if ($this->db->field_exists('code_succursale', 'compte_entreprise')) {
            $select .= ', code_succursale';
        }
        if ($this->db->field_exists('type_structure', 'compte_entreprise')) {
            $select .= ', type_structure';
        }
        if ($this->db->field_exists('can_manage_succursales', 'compte_entreprise')) {
            $select .= ', can_manage_succursales';
        }
        $this->db->select($select);
        $this->db->from('compte_entreprise');
        if ($this->db->field_exists('type_structure', 'compte_entreprise')) {
            $this->db->where('type_structure', 'siege');
        }
        if ($this->db->field_exists('can_manage_succursales', 'compte_entreprise')) {
            $this->db->where('can_manage_succursales', 1);
        }
        if ($exclude_id !== null) {
            $this->db->where('id !=', (int) $exclude_id);
        }
        $this->db->where('statut !=', 'expiré');
        $this->db->order_by('nom', 'ASC');
        return $this->db->get()->result_array();
    }

    public function getBranchRelation($branch_id)
    {
        if (!$this->db->table_exists('entreprise_succursales')) {
            return null;
        }

        return $this->db->where('succursale_entreprise_id', (int) $branch_id)
            ->get('entreprise_succursales')
            ->row_array();
    }

    public function getBranchesByHeadOffice($head_office_id)
    {
        if (!$this->db->table_exists('entreprise_succursales')) {
            return array();
        }

        $this->db->select('es.*, ce.nom, ce.email, ce.telephone, ce.statut, ce.code_succursale');
        $this->db->from('entreprise_succursales es');
        $this->db->join('compte_entreprise ce', 'ce.id = es.succursale_entreprise_id', 'inner');
        $this->db->where('es.siege_entreprise_id', (int) $head_office_id);
        $this->db->order_by('ce.nom', 'ASC');
        return $this->db->get()->result_array();
    }

    public function saveBranchRelation($branch_id, array $relation_data)
    {
        if (!$this->db->table_exists('entreprise_succursales')) {
            return false;
        }

        $existing = $this->getBranchRelation($branch_id);
        if ($existing) {
            $this->db->where('succursale_entreprise_id', (int) $branch_id);
            return $this->db->update('entreprise_succursales', $relation_data);
        }

        $relation_data['succursale_entreprise_id'] = (int) $branch_id;
        return $this->db->insert('entreprise_succursales', $relation_data);
    }

    public function removeBranchRelation($branch_id)
    {
        if (!$this->db->table_exists('entreprise_succursales')) {
            return true;
        }

        $this->db->where('succursale_entreprise_id', (int) $branch_id);
        return $this->db->delete('entreprise_succursales');
    }

    /**
     * Déconnecter une entreprise (réinitialiser la session)
     */
    public function deconnecter_entreprise($entreprise_id) {
        // Log de déconnexion
        $message = "Entreprise déconnectée - ID: " . $entreprise_id;
        $this->log($message, $entreprise_id, "Déconnexion");

        return true;
    }

    /**
     * Forcer la déconnexion d'une entreprise (pour super admin)
     */
    public function forcer_deconnexion_entreprise($entreprise_id) {
        // Ici vous pouvez :
        // 1. Réinitialiser le token de session de l'entreprise
        // 2. Supprimer les sessions actives
        // 3. Logger l'action

        $this->db->where('id', $entreprise_id);
        $this->db->update('compte_entreprise', [
            'derniere_deconnexion' => date('Y-m-d H:i:s'),
            'session_active' => 0 // Si vous avez un champ pour suivre les sessions
        ]);

        $message = "Déconnexion forcée de l'entreprise - ID: " . $entreprise_id;
        $this->log($message, $entreprise_id, "Déconnexion Forcée");

        return $this->db->affected_rows() > 0;
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('compte_entreprise', $data);
    }

    /**
     * Vérifier si l'entreprise est connectée
     */
    public function est_connectee($entreprise_id) {
        $this->db->select('derniere_connexion, session_active');
        $this->db->where('id', $entreprise_id);
        $entreprise = $this->db->get('compte_entreprise')->row_array();

        if (!$entreprise) return false;

        // Vérifier si connectée récemment (dans les 30 dernières minutes)
        $derniere_connexion = strtotime($entreprise['derniere_connexion']);
        $maintenant = time();
        $delai_connection = 30 * 60; // 30 minutes en secondes

        return ($maintenant - $derniere_connexion) < $delai_connection;
    }

    /**
     * Obtenir les entreprises actuellement connectées
     */
    public function get_entreprises_connectees() {
        $delai = date('Y-m-d H:i:s', strtotime('-30 minutes'));

        $this->db->where('derniere_connexion >=', $delai);
        $this->db->where('statut', 'actif');
        $this->db->order_by('derniere_connexion', 'DESC');

        return $this->db->get('compte_entreprise')->result_array();
    }
    /**
     * Formater la durée en texte lisible
     */
    public function format_duree($seconds) {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'min';
        } else {
            return $minutes . 'min';
        }
    }

    public function remove($id) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        $this->db->where('id', $id);
        $this->db->delete('compte_entreprise');
        $message = DELETE_RECORD_CONSTANT . " On item supplier id " . $id;
        $action = "Delete";
        $record_id = $id;
        $this->log($message, $record_id, $action);
        //======================Code End==============================
        $this->db->trans_complete(); # Completing transaction
        /* Optional */
        if ($this->db->trans_status() === false) {
            # Something went wrong.
            $this->db->trans_rollback();
            return false;
        } else {
            //return $return_value;
        }
    }

    public function add($data) {
        $this->db->trans_start(); # Starting Transaction
        $this->db->trans_strict(false); # See Note 01. If you wish can remove as well
        //=======================Code Start===========================
        if (isset($data['id'])) {
            $this->db->where('id', $data['id']);
            $this->db->update('compte_entreprise', $data);
            $message = UPDATE_RECORD_CONSTANT . " On  caisse supplier id " . $data['id'];
            $action = "Update";
            $record_id = $data['id'];
            $this->log($message, $record_id, $action);
            //======================Code End==============================

            $this->db->trans_complete(); # Completing transaction
            /* Optional */

            if ($this->db->trans_status() === false) {
                # Something went wrong.
                $this->db->trans_rollback();
                return false;
            } else {
                //return $return_value;
            }
        } else {
            $this->db->insert('compte_entreprise', $data);
            $insert_id = $this->db->insert_id();
            $message = INSERT_RECORD_CONSTANT . " On caisse supplier id " . $insert_id;
            $action = "Insert";
            $record_id = $insert_id;
            $this->log($message, $record_id, $action);

            //======================Code End==============================

            $this->db->trans_complete(); # Completing transaction
            /* Optional */

            if ($this->db->trans_status() === false) {
                # Something went wrong.
                $this->db->trans_rollback();
                return false;
            } else {
                //return $return_value;
            }
            return $insert_id;
        }
    }

}