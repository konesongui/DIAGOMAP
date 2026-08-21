<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Caisse_model extends My_Model
{
    // Spécifier la table principale (optionnel)
    protected $ma_table = 'caisse';

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    /**
     * ============================================
     * GESTION DES CAISSES
     * ============================================
     */

    /**
     * Créer une nouvelle caisse
     * @param array $data Données de la caisse
     * @return int|bool ID de la caisse créée ou false en cas d'erreur
     */
    public function create_caisse($data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        // Générer une référence unique si non fournie
        if (!isset($data['reference']) || empty($data['reference'])) {
            $data['reference'] = 'CAISSE-' . strtoupper(uniqid());
        }

        // S'assurer que les dates sont correctes
        if (!isset($data['date_creation'])) {
            $data['date_creation'] = date('Y-m-d H:i:s');
        }

        // Initialiser le solde actuel au solde initial
        if (!isset($data['solde_actuel']) && isset($data['solde_initial'])) {
            $data['solde_actuel'] = $data['solde_initial'];
        } elseif (!isset($data['solde_actuel'])) {
            $data['solde_actuel'] = 0.00;
        }

        // Valeurs par défaut
        $data['est_actif'] = isset($data['est_actif']) ? $data['est_actif'] : 1;
        $data['statut'] = isset($data['statut']) ? $data['statut'] : 'ACTIVE';
        $data['deleted'] = 0;

        // Insérer la caisse
        $this->db->insert('caisse', $data);
        $caisse_id = $this->db->insert_id();

        // Journalisation
        $message = "Création de la caisse: " . $data['nom'] . " (ID: $caisse_id)";
        $action = "Insert";
        $this->log($message, $caisse_id, $action);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }

        return $caisse_id;
    }

    /**
     * Récupérer toutes les caisses actives
     * @param int|null $id ID de la caisse spécifique
     * @return array|object Résultat de la requête
     */
    public function get_all_caisses($id = null)
    {
        $this->db->select('
            c.id, c.nom, c.reference, c.description, 
            c.solde_initial, c.solde_actuel, 
            c.date_creation, c.cree_par, 
            c.statut, c.est_actif, c.deleted,
            c.created_at, c.updated_at,
            COUNT(DISTINCT oc.id) as total_operations,
            COALESCE(SUM(CASE WHEN oc.type_operation = "ENTREE" THEN oc.montant ELSE 0 END), 0) as total_entrees,
            COALESCE(SUM(CASE WHEN oc.type_operation = "SORTIE" THEN oc.montant ELSE 0 END), 0) as total_sorties
        ');
        $this->db->from('caisse c');
        $this->db->join('operation_caisse oc', 'c.id = oc.caisse_id AND oc.deleted = 0', 'left');
        $this->db->where('c.deleted', 0);

        if ($id !== null) {
            $this->db->where('c.id', $id);
            $this->db->group_by('c.id');
            $query = $this->db->get();
            return $query->row_array();
        } else {
            $this->db->group_by('c.id');
            $this->db->order_by('c.date_creation', 'DESC');
            $query = $this->db->get();
            return $query->result_array();
        }
    }

    /**
     * Récupérer une caisse par son ID
     * @param int $id ID de la caisse
     * @return array|bool Données de la caisse ou false si non trouvée
     */
    public function get_caisse_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('caisse');
        $this->db->where('id', $id);
        $this->db->where('deleted', 0);
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() === 1) {
            return $query->row_array();
        }

        return false;
    }

    /**
     * Mettre à jour une caisse
     * @param int $id ID de la caisse
     * @param array $data Données à mettre à jour
     * @return bool Succès de l'opération
     */
    public function update_caisse($id, $data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        // Ne pas permettre la mise à jour de certaines colonnes
        unset($data['id'], $data['created_at'], $data['solde_actuel']);

        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $this->db->where('deleted', 0);
        $success = $this->db->update('caisse', $data);

        // Journalisation
        if ($success) {
            $message = "Mise à jour de la caisse ID: $id";
            $action = "Update";
            $this->log($message, $id, $action);
        }

        $this->db->trans_complete();

        return $success && $this->db->trans_status() !== false;
    }

    /**
     * Supprimer (désactiver) une caisse
     * @param int $id ID de la caisse
     * @return bool Succès de l'opération
     */
    public function delete_caisse($id)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        // Vérifier si la caisse a un solde à zéro
        $caisse = $this->get_caisse_by_id($id);
        if ($caisse && $caisse['solde_actuel'] != 0) {
            return false; // Ne peut pas supprimer une caisse avec un solde non nul
        }

        // Marquer comme supprimée (soft delete)
        $this->db->where('id', $id);
        $success = $this->db->update('caisse', [
            'est_actif' => 0,
            'statut' => 'INACTIVE',
            'deleted' => 1,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Journalisation
        if ($success) {
            $message = "Suppression de la caisse ID: $id";
            $action = "Delete";
            $this->log($message, $id, $action);
        }

        $this->db->trans_complete();

        return $success && $this->db->trans_status() !== false;
    }

    /**
     * Vérifier si une caisse existe par son nom
     * @param string $nom Nom de la caisse
     * @param int|null $exclude_id ID à exclure de la recherche
     * @return bool
     */
    public function caisse_exists($nom, $exclude_id = null)
    {
        $this->db->select('id');
        $this->db->from('caisse');
        $this->db->where('nom', $nom);
        $this->db->where('deleted', 0);

        if ($exclude_id !== null) {
            $this->db->where('id !=', $exclude_id);
        }

        $this->db->limit(1);
        $query = $this->db->get();

        return $query->num_rows() > 0;
    }

    /**
     * ============================================
     * GESTION DES OPÉRATIONS DE CAISSE
     * ============================================
     */

    /**
     * Créer une opération de caisse
     * @param array $data Données de l'opération
     * @return int|bool ID de l'opération ou false en cas d'erreur
     */
    public function create_operation($data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        // Vérifier que la caisse existe et est active
        $caisse = $this->get_caisse_by_id($data['caisse_id']);
        if (!$caisse || $caisse['est_actif'] == 0) {
            $this->db->trans_rollback();
            return false;
        }

        // Déterminer le type d'opération
        $type_operation = isset($data['type_operation']) ? $data['type_operation'] : 'ENTREE';

        // Calculer les montants
        if ($type_operation == 'ENTREE') {
            $entree = $data['montant'];
            $sortie = 0.00;
        } else {
            $entree = 0.00;
            $sortie = $data['montant'];

            // Vérifier que le solde est suffisant pour une sortie
            if ($caisse['solde_actuel'] < $data['montant']) {
                $this->db->trans_rollback();
                return false; // Solde insuffisant
            }
        }

        // Générer une référence unique
        $prefix = ($type_operation == 'ENTREE') ? 'RECU-' : 'DEP-';
        $reference = $prefix . strtolower(uniqid());

        // Préparer les données de l'opération
        $operation_data = [
            'reference' => $reference,
            'caisse_id' => $data['caisse_id'],
            'type_operation' => $type_operation,
            'montant' => $data['montant'],
            'designation' => $data['designation'],
            'categorie_id' => isset($data['categorie_id']) ? $data['categorie_id'] : null,
            'user' => isset($data['user']) ? $data['user'] : 'Super Admin',
            'date' => isset($data['date']) ? $data['date'] : date('Y-m-d H:i:s'),
            'entree' => $entree,
            'sortie' => $sortie,
            'solde_avant_operation' => $caisse['solde_actuel'],
            'solde_apres_operation' => $type_operation == 'ENTREE'
                ? $caisse['solde_actuel'] + $data['montant']
                : $caisse['solde_actuel'] - $data['montant'],
            'documents' => isset($data['documents']) ? $data['documents'] : null,
            'note' => isset($data['note']) ? $data['note'] : null,
            'est_actif' => 1,
            'deleted' => 0
        ];

        // Insérer l'opération
        $this->db->insert('operation_caisse', $operation_data);
        $operation_id = $this->db->insert_id();

        // Mettre à jour le solde de la caisse
        $nouveau_solde = $operation_data['solde_apres_operation'];
        $this->db->where('id', $data['caisse_id']);
        $this->db->update('caisse', ['solde_actuel' => $nouveau_solde]);

        // Journalisation
        $message = "Opération $type_operation sur caisse ID: {$data['caisse_id']} - Montant: {$data['montant']}";
        $action = "Insert";
        $this->log($message, $operation_id, $action);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }

        return $operation_id;
    }

    /**
     * Récupérer les opérations d'une caisse
     * @param int|null $caisse_id ID de la caisse (null pour toutes)
     * @param string|null $start_date Date de début
     * @param string|null $end_date Date de fin
     * @param array $options Options supplémentaires
     * @return array Liste des opérations
     */
    public function get_operations($caisse_id = null, $start_date = null, $end_date = null, $options = [])
    {
        $default_options = [
            'limit' => null,
            'offset' => 0,
            'order_by' => 'oc.date DESC, oc.id DESC',
            'include_caisse_info' => true,
            'include_categorie_info' => true
        ];

        $options = array_merge($default_options, $options);

        $this->db->select('
            oc.id, oc.reference, oc.caisse_id, oc.type_operation, 
            oc.montant, oc.designation, oc.categorie_id, oc.user, 
            oc.date, oc.entree, oc.sortie, 
            oc.solde_avant_operation, oc.solde_apres_operation,
            oc.documents, oc.note, oc.est_actif, oc.deleted,
            oc.created_at, oc.updated_at
        ');

        if ($options['include_caisse_info']) {
            $this->db->select('c.nom as caisse_nom, c.reference as caisse_reference');
        }

        if ($options['include_categorie_info'] && $options['include_caisse_info']) {
            $this->db->select('cat.nom as categorie_nom, cat.code as categorie_code');
        }

        $this->db->from('operation_caisse oc');

        if ($options['include_caisse_info']) {
            $this->db->join('caisse c', 'oc.caisse_id = c.id AND c.deleted = 0', 'left');
        }

        if ($options['include_categorie_info'] && $options['include_caisse_info']) {
            $this->db->join('categorie_operation cat', 'oc.categorie_id = cat.id', 'left');
        }

        $this->db->where('oc.deleted', 0);

        // Filtres
        if ($caisse_id !== null) {
            $this->db->where('oc.caisse_id', $caisse_id);
        }

        if ($start_date !== null) {
            $this->db->where('DATE(oc.date) >=', $start_date);
        }

        if ($end_date !== null) {
            $this->db->where('DATE(oc.date) <=', $end_date);
        }

        // Ordre
        $this->db->order_by($options['order_by']);

        // Limite et offset
        if ($options['limit'] !== null) {
            $this->db->limit($options['limit'], $options['offset']);
        }

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Récupérer une opération par son ID
     * @param int $id ID de l'opération
     * @return array|bool Données de l'opération ou false
     */
    public function get_operation_by_id($id)
    {
        $this->db->select('
            oc.*, 
            c.nom as caisse_nom, c.reference as caisse_reference,
            cat.nom as categorie_nom, cat.code as categorie_code
        ');
        $this->db->from('operation_caisse oc');
        $this->db->join('caisse c', 'oc.caisse_id = c.id AND c.deleted = 0', 'left');
        $this->db->join('categorie_operation cat', 'oc.categorie_id = cat.id', 'left');
        $this->db->where('oc.id', $id);
        $this->db->where('oc.deleted', 0);
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() === 1) {
            return $query->row_array();
        }

        return false;
    }

    /**
     * Récupérer une opération par sa référence
     * @param string $reference Référence de l'opération
     * @return array|bool Données de l'opération ou false
     */
    public function get_operation_by_reference($reference)
    {
        $this->db->select('*');
        $this->db->from('operation_caisse');
        $this->db->where('reference', $reference);
        $this->db->where('deleted', 0);
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() === 1) {
            return $query->row_array();
        }

        return false;
    }

    /**
     * Mettre à jour une opération
     * @param int $id ID de l'opération
     * @param array $data Données à mettre à jour
     * @return bool Succès de l'opération
     */
    public function update_operation($id, $data)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        // Récupérer l'opération actuelle
        $operation = $this->get_operation_by_id($id);
        if (!$operation) {
            $this->db->trans_rollback();
            return false;
        }

        // Ne pas permettre la mise à jour de certaines colonnes
        unset($data['id'], $data['reference'], $data['caisse_id'], $data['created_at']);

        $data['updated_at'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $this->db->where('deleted', 0);
        $success = $this->db->update('operation_caisse', $data);

        // Journalisation
        if ($success) {
            $message = "Mise à jour de l'opération ID: $id";
            $action = "Update";
            $this->log($message, $id, $action);
        }

        $this->db->trans_complete();

        return $success && $this->db->trans_status() !== false;
    }

    /**
     * Supprimer une opération
     * @param int $id ID de l'opération
     * @return bool Succès de l'opération
     */
    public function delete_operation($id)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        // Récupérer l'opération
        $operation = $this->get_operation_by_id($id);
        if (!$operation) {
            $this->db->trans_rollback();
            return false;
        }

        // Annuler l'effet de l'opération sur le solde de la caisse
        $caisse_id = $operation['caisse_id'];
        $montant = $operation['montant'];
        $type_operation = $operation['type_operation'];

        // Récupérer la caisse
        $caisse = $this->get_caisse_by_id($caisse_id);

        // Calculer le nouveau solde
        if ($type_operation == 'ENTREE') {
            $nouveau_solde = $caisse['solde_actuel'] - $montant;
        } else {
            $nouveau_solde = $caisse['solde_actuel'] + $montant;
        }

        // Mettre à jour le solde de la caisse
        $this->db->where('id', $caisse_id);
        $this->db->update('caisse', ['solde_actuel' => $nouveau_solde]);

        // Marquer l'opération comme supprimée
        $this->db->where('id', $id);
        $success = $this->db->update('operation_caisse', [
            'est_actif' => 0,
            'deleted' => 1,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Journalisation
        if ($success) {
            $message = "Suppression de l'opération ID: $id (Caisse: $caisse_id)";
            $action = "Delete";
            $this->log($message, $id, $action);
        }

        $this->db->trans_complete();

        return $success && $this->db->trans_status() !== false;
    }

    /**
     * ============================================
     * TRANSFERTS ENTRE CAISSES
     * ============================================
     */

    /**
     * Effectuer un transfert entre deux caisses
     * @param int $from_caisse_id Caisse source
     * @param int $to_caisse_id Caisse destination
     * @param float $montant Montant à transférer
     * @param string $user Utilisateur effectuant le transfert
     * @param string|null $designation Description du transfert
     * @return int|bool ID du transfert ou false en cas d'erreur
     */
    public function transfert_caisse($from_caisse_id, $to_caisse_id, $montant, $user, $designation = null)
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        // Vérifier que les caisses sont différentes
        if ($from_caisse_id == $to_caisse_id) {
            $this->db->trans_rollback();
            return false;
        }

        // Vérifier que la caisse source existe et a suffisamment de fonds
        $caisse_source = $this->get_caisse_by_id($from_caisse_id);
        $caisse_destination = $this->get_caisse_by_id($to_caisse_id);

        if (!$caisse_source || !$caisse_destination) {
            $this->db->trans_rollback();
            return false;
        }

        if ($caisse_source['solde_actuel'] < $montant) {
            $this->db->trans_rollback();
            return false; // Solde insuffisant
        }

        // Générer une référence unique pour le transfert
        $reference_transfert = 'TRF-' . strtoupper(uniqid());

        // Préparer la désignation par défaut
        if ($designation === null) {
            $designation = "Transfert de {$caisse_source['nom']} vers {$caisse_destination['nom']}";
        }

        // Créer l'opération de sortie sur la caisse source
        $operation_sortie = [
            'caisse_id' => $from_caisse_id,
            'type_operation' => 'SORTIE',
            'montant' => $montant,
            'designation' => $designation . " [Transfert vers: {$caisse_destination['nom']}]",
            'categorie_id' => $this->get_categorie_transfert_id(),
            'user' => $user,
            'date' => date('Y-m-d H:i:s')
        ];

        $operation_sortie_id = $this->create_operation($operation_sortie);

        if (!$operation_sortie_id) {
            $this->db->trans_rollback();
            return false;
        }

        // Créer l'opération d'entrée sur la caisse destination
        $operation_entree = [
            'caisse_id' => $to_caisse_id,
            'type_operation' => 'ENTREE',
            'montant' => $montant,
            'designation' => $designation . " [Transfert depuis: {$caisse_source['nom']}]",
            'categorie_id' => $this->get_categorie_reception_id(),
            'user' => $user,
            'date' => date('Y-m-d H:i:s')
        ];

        $operation_entree_id = $this->create_operation($operation_entree);

        if (!$operation_entree_id) {
            $this->db->trans_rollback();
            return false;
        }

        // Enregistrer le transfert dans la table dédiée
        $transfert_data = [
            'reference' => $reference_transfert,
            'from_caisse_id' => $from_caisse_id,
            'to_caisse_id' => $to_caisse_id,
            'montant' => $montant,
            'user_id' => $user,
            'date_transfert' => date('Y-m-d H:i:s'),
            'designation' => $designation,
            'operation_sortie_id' => $operation_sortie_id,
            'operation_entree_id' => $operation_entree_id
        ];

        $this->db->insert('transfert_caisse', $transfert_data);
        $transfert_id = $this->db->insert_id();

        // Journalisation
        $message = "Transfert de $montant FCFA de {$caisse_source['nom']} vers {$caisse_destination['nom']}";
        $action = "Transfert";
        $this->log($message, $transfert_id, $action);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            return false;
        }

        return $transfert_id;
    }

    /**
     * Récupérer l'historique des transferts
     * @param array $filters Filtres de recherche
     * @return array Liste des transferts
     */
    public function get_transferts($filters = [])
    {
        $default_filters = [
            'start_date' => null,
            'end_date' => null,
            'caisse_id' => null,
            'limit' => null,
            'offset' => 0
        ];

        $filters = array_merge($default_filters, $filters);

        $this->db->select('
            tc.*,
            fc.nom as from_caisse_nom,
            fc.reference as from_caisse_reference,
            tc.nom as to_caisse_nom,
            tc.reference as to_caisse_reference,
            u.username as user_name
        ');

        $this->db->from('transfert_caisse tc');
        $this->db->join('caisse fc', 'tc.from_caisse_id = fc.id', 'left');
        $this->db->join('caisse tc', 'tc.to_caisse_id = tc.id', 'left');
        $this->db->join('users u', 'tc.user_id = u.id', 'left');

        // Appliquer les filtres
        if ($filters['start_date'] !== null) {
            $this->db->where('DATE(tc.date_transfert) >=', $filters['start_date']);
        }

        if ($filters['end_date'] !== null) {
            $this->db->where('DATE(tc.date_transfert) <=', $filters['end_date']);
        }

        if ($filters['caisse_id'] !== null) {
            $this->db->group_start();
            $this->db->where('tc.from_caisse_id', $filters['caisse_id']);
            $this->db->or_where('tc.to_caisse_id', $filters['caisse_id']);
            $this->db->group_end();
        }

        $this->db->order_by('tc.date_transfert', 'DESC');
        $this->db->order_by('tc.id', 'DESC');

        if ($filters['limit'] !== null) {
            $this->db->limit($filters['limit'], $filters['offset']);
        }

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * ============================================
     * RAPPORTS ET STATISTIQUES
     * ============================================
     */

    /**
     * Calculer le total centralisé (somme de tous les soldes)
     * @return float Total centralisé
     */
    public function get_total_centralisation()
    {
        $this->db->select('SUM(solde_actuel) as total_centralisation');
        $this->db->from('caisse');
        $this->db->where('est_actif', 1);
        $this->db->where('deleted', 0);

        $query = $this->db->get();
        $result = $query->row();

        return $result ? floatval($result->total_centralisation) : 0.00;
    }

    /**
     * Générer le livre de caisse (journal complet)
     * @param array $caisse_ids IDs des caisses (vide pour toutes)
     * @param string|null $start_date Date de début
     * @param string|null $end_date Date de fin
     * @return array Journal des opérations
     */
    public function get_livre_caisse($caisse_ids = [], $start_date = null, $end_date = null)
    {
        $this->db->select('
            oc.reference,
            oc.date,
            CONCAT(c.nom, " - ", oc.designation) as designation,
            COALESCE(cat.nom, "Non catégorisé") as categorie,
            oc.user,
            oc.entree,
            oc.sortie,
            oc.solde_apres_operation,
            c.nom as caisse_nom,
            c.reference as caisse_reference
        ');

        $this->db->from('operation_caisse oc');
        $this->db->join('caisse c', 'oc.caisse_id = c.id AND c.deleted = 0', 'left');
        $this->db->join('categorie_operation cat', 'oc.categorie_id = cat.id', 'left');
        $this->db->where('oc.deleted', 0);

        // Filtrer par caisses
        if (!empty($caisse_ids)) {
            $this->db->where_in('oc.caisse_id', $caisse_ids);
        }

        // Filtrer par date
        if ($start_date !== null) {
            $this->db->where('DATE(oc.date) >=', $start_date);
        }

        if ($end_date !== null) {
            $this->db->where('DATE(oc.date) <=', $end_date);
        }

        $this->db->order_by('oc.date', 'DESC');
        $this->db->order_by('oc.id', 'DESC');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Obtenir les statistiques par période
     * @param string $start_date Date de début
     * @param string $end_date Date de fin
     * @param int|null $caisse_id ID de la caisse (optionnel)
     * @return array Statistiques
     */
    public function get_statistiques_periode($start_date, $end_date, $caisse_id = null)
    {
        $this->db->select('
            c.id as caisse_id,
            c.nom as caisse_nom,
            COALESCE(SUM(oc.entree), 0) as total_entrees,
            COALESCE(SUM(oc.sortie), 0) as total_sorties,
            COALESCE(SUM(oc.entree - oc.sortie), 0) as solde_net,
            COUNT(oc.id) as nombre_operations
        ');

        $this->db->from('caisse c');
        $this->db->join('operation_caisse oc', 'c.id = oc.caisse_id AND oc.deleted = 0', 'left');
        $this->db->where('c.deleted', 0);
        $this->db->where('c.est_actif', 1);

        if ($caisse_id !== null) {
            $this->db->where('c.id', $caisse_id);
        }

        $this->db->where('DATE(oc.date) >=', $start_date);
        $this->db->where('DATE(oc.date) <=', $end_date);

        $this->db->group_by('c.id');
        $this->db->order_by('c.nom', 'ASC');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Obtenir le solde de début et de fin pour une période
     * @param int $caisse_id ID de la caisse
     * @param string $start_date Date de début
     * @param string $end_date Date de fin
     * @return array Solde de début et de fin
     */
    public function get_solde_periode($caisse_id, $start_date, $end_date)
    {
        // Solde avant la période (dernière opération avant start_date)
        $this->db->select('solde_apres_operation');
        $this->db->from('operation_caisse');
        $this->db->where('caisse_id', $caisse_id);
        $this->db->where('DATE(date) <', $start_date);
        $this->db->where('deleted', 0);
        $this->db->order_by('date', 'DESC');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);

        $query = $this->db->get();
        $solde_debut = $query->num_rows() > 0 ? $query->row()->solde_apres_operation : 0.00;

        // Solde à la fin de la période
        $this->db->select('solde_apres_operation');
        $this->db->from('operation_caisse');
        $this->db->where('caisse_id', $caisse_id);
        $this->db->where('DATE(date) <=', $end_date);
        $this->db->where('deleted', 0);
        $this->db->order_by('date', 'DESC');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);

        $query = $this->db->get();
        $solde_fin = $query->num_rows() > 0 ? $query->row()->solde_apres_operation : $solde_debut;

        return [
            'solde_debut' => floatval($solde_debut),
            'solde_fin' => floatval($solde_fin)
        ];
    }

    /**
     * ============================================
     * FONCTIONS UTILITAIRES
     * ============================================
     */

    /**
     * Obtenir l'ID de la catégorie "Transfert"
     * @return int ID de la catégorie
     */
    private function get_categorie_transfert_id()
    {
        $this->db->select('id');
        $this->db->from('categorie_operation');
        $this->db->where('code', 'TRANSFERT');
        $this->db->or_where('nom LIKE', '%transfert%');
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->id;
        }

        return 12; // Valeur par défaut (comme dans votre exemple)
    }

    /**
     * Obtenir l'ID de la catégorie "Réception"
     * @return int ID de la catégorie
     */
    private function get_categorie_reception_id()
    {
        $this->db->select('id');
        $this->db->from('categorie_operation');
        $this->db->where('code', 'RECEPTION');
        $this->db->or_where('nom LIKE', '%réception%');
        $this->db->or_where('nom LIKE', '%reception%');
        $this->db->limit(1);

        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row()->id;
        }

        return 10; // Valeur par défaut (comme dans votre exemple)
    }

    /**
     * Rechercher dans les opérations
     * @param string $search_term Terme de recherche
     * @param array $filters Filtres supplémentaires
     * @return array Résultats de la recherche
     */
    public function search_operations($search_term, $filters = [])
    {
        $this->db->select('
            oc.*,
            c.nom as caisse_nom,
            cat.nom as categorie_nom
        ');

        $this->db->from('operation_caisse oc');
        $this->db->join('caisse c', 'oc.caisse_id = c.id AND c.deleted = 0', 'left');
        $this->db->join('categorie_operation cat', 'oc.categorie_id = cat.id', 'left');
        $this->db->where('oc.deleted', 0);

        // Recherche dans plusieurs champs
        $this->db->group_start();
        $this->db->like('oc.reference', $search_term);
        $this->db->or_like('oc.designation', $search_term);
        $this->db->or_like('oc.note', $search_term);
        $this->db->or_like('c.nom', $search_term);
        $this->db->group_end();

        // Appliquer les filtres
        if (isset($filters['start_date']) && $filters['start_date']) {
            $this->db->where('DATE(oc.date) >=', $filters['start_date']);
        }

        if (isset($filters['end_date']) && $filters['end_date']) {
            $this->db->where('DATE(oc.date) <=', $filters['end_date']);
        }

        if (isset($filters['caisse_id']) && $filters['caisse_id']) {
            $this->db->where('oc.caisse_id', $filters['caisse_id']);
        }

        if (isset($filters['type_operation']) && $filters['type_operation']) {
            $this->db->where('oc.type_operation', $filters['type_operation']);
        }

        $this->db->order_by('oc.date', 'DESC');
        $this->db->order_by('oc.id', 'DESC');

        if (isset($filters['limit']) && $filters['limit']) {
            $offset = isset($filters['offset']) ? $filters['offset'] : 0;
            $this->db->limit($filters['limit'], $offset);
        }

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Vérifier le solde d'une caisse
     * @param int $caisse_id ID de la caisse
     * @return float Solde actuel
     */
    public function get_solde_caisse($caisse_id)
    {
        $caisse = $this->get_caisse_by_id($caisse_id);

        if ($caisse) {
            return floatval($caisse['solde_actuel']);
        }

        return 0.00;
    }

    /**
     * Générer un rapport PDF du livre de caisse
     * @param array $data Données à exporter
     * @param array $options Options d'export
     * @return string Chemin du fichier PDF généré
     */
    public function generate_pdf_report($data, $options = [])
    {
        // Cette fonction serait implémentée avec une bibliothèque PDF
        // comme TCPDF ou mPDF

        $default_options = [
            'title' => 'Livre de Caisse',
            'filename' => 'livre_caisse_' . date('Ymd_His') . '.pdf',
            'orientation' => 'L', // Landscape
            'author' => 'Système de Caisse'
        ];

        $options = array_merge($default_options, $options);

        // Implémentation PDF à ajouter ici
        // return $pdf_file_path;

        return null; // À implémenter
    }

    /**
     * Exporter les données en Excel
     * @param array $data Données à exporter
     * @param array $options Options d'export
     * @return string Chemin du fichier Excel généré
     */
    public function generate_excel_report($data, $options = [])
    {
        // Cette fonction serait implémentée avec PHPExcel ou PhpSpreadsheet

        $default_options = [
            'title' => 'Export Caisse',
            'filename' => 'export_caisse_' . date('Ymd_His') . '.xlsx',
            'sheet_name' => 'Caisse'
        ];

        $options = array_merge($default_options, $options);

        // Implémentation Excel à ajouter ici
        // return $excel_file_path;

        return null; // À implémenter
    }

    /**
     * Obtenir les caisses avec leur solde pour un select
     * @return array Caisses formatées pour un dropdown
     */
    public function get_caisses_for_dropdown()
    {
        $this->db->select('id, nom, solde_actuel');
        $this->db->from('caisse');
        $this->db->where('est_actif', 1);
        $this->db->where('deleted', 0);
        $this->db->order_by('nom', 'ASC');

        $query = $this->db->get();
        $caisses = $query->result_array();

        $dropdown = [];
        foreach ($caisses as $caisse) {
            $dropdown[$caisse['id']] = $caisse['nom'] . ' (' . number_format($caisse['solde_actuel'], 2, ',', ' ') . ' FCFA)';
        }

        return $dropdown;
    }

    /**
     * Obtenir les catégories d'opérations pour un select
     * @param string|null $type Type de catégorie (ENTREE, SORTIE, MIXTE)
     * @return array Catégories formatées
     */
    public function get_categories_for_dropdown($type = null)
    {
        $this->db->select('id, nom, code, type');
        $this->db->from('categorie_operation');
        $this->db->where('est_actif', 1);

        if ($type !== null) {
            $this->db->group_start();
            $this->db->where('type', $type);
            $this->db->or_where('type', 'MIXTE');
            $this->db->group_end();
        }

        $this->db->order_by('nom', 'ASC');

        $query = $this->db->get();
        $categories = $query->result_array();

        $dropdown = [];
        foreach ($categories as $categorie) {
            $dropdown[$categorie['id']] = $categorie['nom'] . ' (' . $categorie['code'] . ')';
        }

        return $dropdown;
    }

    /**
     * Valider les données d'une opération
     * @param array $data Données à valider
     * @return array|bool Données validées ou false en cas d'erreur
     */
    public function validate_operation_data($data)
    {
        // Vérifications de base
        if (!isset($data['caisse_id']) || !is_numeric($data['caisse_id'])) {
            return false;
        }

        if (!isset($data['montant']) || !is_numeric($data['montant']) || $data['montant'] <= 0) {
            return false;
        }

        if (!isset($data['designation']) || empty(trim($data['designation']))) {
            return false;
        }

        // Vérifier que la caisse existe
        $caisse = $this->get_caisse_by_id($data['caisse_id']);
        if (!$caisse) {
            return false;
        }

        // Vérifier le solde pour une sortie
        if (isset($data['type_operation']) && $data['type_operation'] == 'SORTIE') {
            if ($caisse['solde_actuel'] < $data['montant']) {
                return false;
            }
        }

        return $data;
    }

    /**
     * ============================================
     * FONCTIONS DE MIGRATION (si nécessaire)
     * ============================================
     */

    /**
     * Migrer les données de l'ancienne table income vers les nouvelles tables
     * @return bool Succès de la migration
     */
    public function migrate_from_income()
    {
        $this->db->trans_start();
        $this->db->trans_strict(false);

        try {
            // Étape 1: Migrer les caisses (où inc_head_id est NULL ou 0)
            $this->db->query("
                INSERT INTO caisse (id, nom, reference, description, solde_initial, solde_actuel, 
                                  date_creation, cree_par, statut, est_actif, deleted, created_at)
                SELECT 
                    id,
                    name as nom,
                    invoice_no as reference,
                    note as description,
                    amount as solde_initial,
                    amount_re as solde_actuel,
                    date as date_creation,
                    user as cree_par,
                    CASE 
                        WHEN est_actif = 1 THEN 'ACTIVE' 
                        ELSE 'INACTIVE' 
                    END as statut,
                    est_actif,
                    deleted,
                    created_at
                FROM income
                WHERE (inc_head_id IS NULL OR inc_head_id = 0)
                AND deleted = 0
            ");

            // Étape 2: Migrer les opérations (où inc_head_id n'est pas NULL ou 0)
            $this->db->query("
                INSERT INTO operation_caisse (reference, caisse_id, type_operation, montant, 
                                            designation, user, date, entree, sortie, 
                                            documents, note, est_actif, deleted, created_at)
                SELECT 
                    invoice_no as reference,
                    inc_head_id as caisse_id,
                    CASE 
                        WHEN amount > 0 THEN 'ENTREE'
                        ELSE 'SORTIE'
                    END as type_operation,
                    ABS(amount) as montant,
                    name as designation,
                    user,
                    date,
                    CASE 
                        WHEN amount > 0 THEN amount 
                        ELSE 0 
                    END as entree,
                    CASE 
                        WHEN amount < 0 THEN ABS(amount) 
                        ELSE 0 
                    END as sortie,
                    documents,
                    note,
                    est_actif,
                    deleted,
                    created_at
                FROM income
                WHERE inc_head_id IS NOT NULL 
                AND inc_head_id != 0
                AND deleted = 0
            ");

            // Étape 3: Mettre à jour les soldes des caisses basés sur les opérations
            $this->db->query("
                UPDATE caisse c
                SET solde_actuel = COALESCE((
                    SELECT c2.solde_initial + COALESCE(SUM(
                        CASE 
                            WHEN oc.type_operation = 'ENTREE' THEN oc.montant 
                            ELSE -oc.montant 
                        END
                    ), 0)
                    FROM caisse c2
                    LEFT JOIN operation_caisse oc ON c2.id = oc.caisse_id AND oc.deleted = 0
                    WHERE c2.id = c.id
                    GROUP BY c2.id
                ), c.solde_initial)
            ");

            $this->db->trans_complete();

            return $this->db->trans_status() !== false;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Migration error: ' . $e->getMessage());
            return false;
        }
    }
}