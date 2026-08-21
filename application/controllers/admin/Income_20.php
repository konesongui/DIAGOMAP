<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Income extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->config->load('app-config');
        $this->load->library("datatables");
        $this->load->model('incomehead_model');
        $this->load->model('comptecomptable_model');
        $this->load->model('journal_model');
        $this->load->model('Income_processing_model');
    }

    public function toggle_status()
    {
        $id = $this->input->post('id');
        $current_status = $this->input->post('status');
        $new_status = ($current_status == "1") ? "0" : "1";

        $this->db->where('id', $id)->update('income', [
            'est_actif' => $new_status
        ]);

        echo json_encode([
            'status' => 'success',
            'message' => 'Statut mis à jour avec succès.'
        ]);
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('caisse', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Caisse');
        $this->session->set_userdata('sub_menu', 'income/index');
        $data['title'] = 'Gestion de Caisse';
        $data['title_list'] = 'Livre de Caisse';

        // Récupérer les paramètres de filtrage
        $caisse_id = $this->input->get('caisse_id');
        $date_debut = $this->input->get('date_debut');
        $date_fin = $this->input->get('date_fin');
        $categorie = $this->input->get('categorie');
        $search = $this->input->get('search');
        $mode_paiement = $this->input->get('mode_paiement');
        $data['date_actuelle'] = date('Y-m-d');

        // Filtre pour les totaux généraux
        $date_totaux_debut = $this->input->get('date_totaux_debut');
        $date_totaux_fin = $this->input->get('date_totaux_fin');

        if (empty($date_totaux_debut)) {
            $date_totaux_debut = date('Y-m-01');
        }
        if (empty($date_totaux_fin)) {
            $date_totaux_fin = date('Y-m-d');
        }

        // Définir les dates par défaut si non fournies
        if (empty($date_debut)) {
            $date_debut = date('Y-m-01');
        }
        if (empty($date_fin)) {
            $date_fin = date('Y-m-d');
        }

        // 1. Récupérer toutes les caisses
        $data['caisses'] = $this->get_all_caisses();

        // 2. Calculer les soldes pour chaque caisse
        $data['soldes_caisses'] = array();
        foreach ($data['caisses'] as $caisse) {
            $data['soldes_caisses'][$caisse['id']] = $this->calculer_solde_caisse($caisse['id']);
        }

        // 3. Calculer le total de centralisation
        $data['total_centralisation'] = 0;
        foreach ($data['soldes_caisses'] as $solde) {
            $data['total_centralisation'] += $solde;
        }

        // 4. Si une caisse spécifique est sélectionnée
        if ($caisse_id) {
            $data['caisse_selectionnee'] = $this->get_caisse_by_id($caisse_id);
            $data['solde_initial'] = $this->get_solde_initial($caisse_id, $date_debut);
            $data['operations'] = $this->get_operations_caisse_filtered($caisse_id, $date_debut, $date_fin, $categorie, $search, $mode_paiement);
        } else {
            $data['operations'] = $this->get_all_operations_filtered($date_debut, $date_fin, $categorie, $search, $mode_paiement);
            $data['solde_initial'] = 0;
        }

        // =========================================================
        // CORRECTION APPLIQUÉE ICI
        // =========================================================
        // 5. RÉCUPÉRER LES TOTAUX PAR MODE DE PAIEMENT
        $data['totaux_par_mode_paiement'] = $this->get_totaux_par_mode_paiement(
            $date_totaux_debut,
            $date_totaux_fin,
            $caisse_id
        );

        // 6. Calculer les totaux généraux pour la période (CORRIGÉ !)
        $data['totaux_periode'] = $this->calculer_totaux_periode(
            $date_totaux_debut,
            $date_totaux_fin,   // ← ICI C'ÉTAIT L'ERREUR (date_taux_fin)
            $caisse_id
        );

        // 7. PASSER LES DATES DES TOTAUX À LA VUE
        $data['date_totaux_debut'] = $date_totaux_debut;
        $data['date_totaux_fin'] = $date_totaux_fin;
        // =========================================================

        // 8. Récupérer les autres données
        $data['incomelist'] = $this->income_model->get();
        $data['incomeTotal'] = $this->income_model->getTotalIncome();
        $data['incheadlist'] = $this->incomehead_model->get();
        $data['compte'] = $this->comptecomptable_model->get();
        $expnseHead = $this->expensehead_model->get();
        $data['expheadlist'] = $expnseHead;

        // 9. Récupérer les catégories et modes de paiement
        $data['categories_list'] = $this->get_categories_for_filter();
        $data['modes_paiement_list'] = $this->get_modes_paiement_for_filter();

        // 10. Passer les valeurs des filtres à la vue
        $data['mode_paiement_filter'] = $mode_paiement;
        $data['categorie_filter'] = $categorie;
        $data['search_filter'] = $search;
        $data['date_debut_filter'] = $date_debut;
        $data['date_fin_filter'] = $date_fin;
        $data['caisse_id_filter'] = $caisse_id;

        // 11. Charger la vue
        $this->load->view('layout/header', $data);
        $this->load->view('admin/income/incomeList', $data);
        $this->load->view('layout/footer', $data);
    }

    public function index_110226()
    {
        if (!$this->rbac->hasPrivilege('caisse', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Caisse');
        $this->session->set_userdata('sub_menu', 'income/index');
        $data['title'] = 'Gestion de Caisse';
        $data['title_list'] = 'Livre de Caisse';

        // Récupérer les paramètres de filtrage
        $caisse_id = $this->input->get('caisse_id');
        $date_debut = $this->input->get('date_debut');
        $date_fin = $this->input->get('date_fin');
        $categorie = $this->input->get('categorie');
        $search = $this->input->get('search');
        $mode_paiement = $this->input->get('mode_paiement'); // NOUVEAU FILTRE
        // 12. PASSER LA DATE DU JOUR POUR LE FILTRE DES BOUTONS SUPPRESSION
        $data['date_actuelle'] = date('Y-m-d');


        // NOUVEAU : Filtre pour les totaux généraux
        $date_totaux_debut = $this->input->get('date_totaux_debut');
        $date_totaux_fin = $this->input->get('date_totaux_fin');

        if (empty($date_totaux_debut)) {
            $date_totaux_debut = date('Y-m-01');
        }
        if (empty($date_totaux_fin)) {
            $date_totaux_fin = date('Y-m-d');
        }

        // Définir les dates par défaut si non fournies
        if (empty($date_debut)) {
            $date_debut = date('Y-m-01');
        }
        if (empty($date_fin)) {
            $date_fin = date('Y-m-d');
        }

        // 1. Récupérer toutes les caisses (enregistrements avec name non vide)
        $data['caisses'] = $this->get_all_caisses();

        // 2. Calculer les soldes pour chaque caisse
        $data['soldes_caisses'] = array();
        foreach ($data['caisses'] as $caisse) {
            $data['soldes_caisses'][$caisse['id']] = $this->calculer_solde_caisse($caisse['id']);
        }

        // 3. Calculer le total de centralisation
        $data['total_centralisation'] = 0;
        foreach ($data['soldes_caisses'] as $solde) {
            $data['total_centralisation'] += $solde;
        }

        // 4. Si une caisse spécifique est sélectionnée
        if ($caisse_id) {
            $data['caisse_selectionnee'] = $this->get_caisse_by_id($caisse_id);

            // Récupérer le solde initial (avant la période)
            $data['solde_initial'] = $this->get_solde_initial($caisse_id, $date_debut);

            // Récupérer les opérations pour cette caisse AVEC LES NOUVEAUX FILTRES
            $data['operations'] = $this->get_operations_caisse_filtered($caisse_id, $date_debut, $date_fin, $categorie, $search, $mode_paiement);
        } else {
            // Récupérer toutes les opérations AVEC LES NOUVEAUX FILTRES
            $data['operations'] = $this->get_all_operations_filtered($date_debut, $date_fin, $categorie, $search, $mode_paiement);
            $data['solde_initial'] = 0;
        }

        // 9. RÉCUPÉRER LES TOTAUX PAR MODE DE PAIEMENT (NOUVEAU)
        $data['totaux_par_mode_paiement'] = $this->get_totaux_par_mode_paiement(
            $date_totaux_debut,
            $date_totaux_fin,
            $caisse_id
        );

        // 10. Calculer les totaux généraux pour la période spécifiée
        $data['totaux_periode'] = $this->calculer_totaux_periode($date_totaux_debut, $date_totaux_fin, $caisse_id);

        // 11. PASSER LES DATES DES TOTAUX À LA VUE
        $data['date_totaux_debut'] = $date_totaux_debut;
        $data['date_totaux_fin'] = $date_totaux_fin;

        // 5. Récupérer les autres données nécessaires
        $data['incomelist'] = $this->income_model->get();
        $data['incomeTotal'] = $this->income_model->getTotalIncome();
        $data['incheadlist'] = $this->incomehead_model->get();
        $data['compte'] = $this->comptecomptable_model->get();
        $expnseHead          = $this->expensehead_model->get();
        $data['expheadlist'] = $expnseHead;

        // 6. RÉCUPÉRER LES CATÉGORIES POUR LE FILTRE
        $data['categories_list'] = $this->get_categories_for_filter();

        // 7. RÉCUPÉRER LES MODES DE PAIEMENT POUR LE FILTRE (NOUVEAU)
        $data['modes_paiement_list'] = $this->get_modes_paiement_for_filter();

        // 8. PASSER LES VALEURS DES FILTRES À LA VUE
        $data['mode_paiement_filter'] = $mode_paiement;
        $data['categorie_filter'] = $categorie;
        $data['search_filter'] = $search;
        $data['date_debut_filter'] = $date_debut;
        $data['date_fin_filter'] = $date_fin;
        $data['caisse_id_filter'] = $caisse_id;

        // 9. Charger la vue spécifique pour le livre de caisse
        $this->load->view('layout/header', $data);
        $this->load->view('admin/income/incomeList', $data); // Votre nouvelle vue
        $this->load->view('layout/footer', $data);
    }

    public function index_050226()
    {
        if (!$this->rbac->hasPrivilege('caisse', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Caisse');
        $this->session->set_userdata('sub_menu', 'income/index');
        $data['title'] = 'Gestion de Caisse';
        $data['title_list'] = 'Livre de Caisse';

        // Récupérer les paramètres de filtrage
        $caisse_id = $this->input->get('caisse_id');
        $date_debut = $this->input->get('date_debut');
        $date_fin = $this->input->get('date_fin');
        $categorie = $this->input->get('categorie'); // NOUVEAU FILTRE
        $search = $this->input->get('search'); // NOUVEAU FILTRE

        // Définir les dates par défaut si non fournies
        if (empty($date_debut)) {
            $date_debut = date('Y-m-01');
        }
        if (empty($date_fin)) {
            $date_fin = date('Y-m-d');
        }

        // 1. Récupérer toutes les caisses (enregistrements avec name non vide)
        $data['caisses'] = $this->get_all_caisses();

        // 2. Calculer les soldes pour chaque caisse
        $data['soldes_caisses'] = array();
        foreach ($data['caisses'] as $caisse) {
            $data['soldes_caisses'][$caisse['id']] = $this->calculer_solde_caisse($caisse['id']);
        }

        // 3. Calculer le total de centralisation
        $data['total_centralisation'] = 0;
        foreach ($data['soldes_caisses'] as $solde) {
            $data['total_centralisation'] += $solde;
        }

        // 4. Si une caisse spécifique est sélectionnée
        if ($caisse_id) {
            $data['caisse_selectionnee'] = $this->get_caisse_by_id($caisse_id);

            // Récupérer le solde initial (avant la période)
            $data['solde_initial'] = $this->get_solde_initial($caisse_id, $date_debut);

            // Récupérer les opérations pour cette caisse AVEC LES NOUVEAUX FILTRES
            $data['operations'] = $this->get_operations_caisse_filtered($caisse_id, $date_debut, $date_fin, $categorie, $search);
        } else {
            // Récupérer toutes les opérations AVEC LES NOUVEAUX FILTRES
            $data['operations'] = $this->get_all_operations_filtered($date_debut, $date_fin, $categorie, $search);
            $data['solde_initial'] = 0;
        }

        // 5. Récupérer les autres données nécessaires
        $data['incomelist'] = $this->income_model->get();
        $data['incomeTotal'] = $this->income_model->getTotalIncome();
        $data['incheadlist'] = $this->incomehead_model->get();
        $data['compte'] = $this->comptecomptable_model->get();
        $expnseHead          = $this->expensehead_model->get();
        $data['expheadlist'] = $expnseHead;

        // 6. RÉCUPÉRER LES CATÉGORIES POUR LE FILTRE (NOUVEAU)
        $data['categories_list'] = $this->get_categories_for_filter();

        // 7. PASSER LES VALEURS DES FILTRES À LA VUE
        $data['categorie_filter'] = $categorie;
        $data['search_filter'] = $search;
        $data['date_debut_filter'] = $date_debut;
        $data['date_fin_filter'] = $date_fin;
        $data['caisse_id_filter'] = $caisse_id;

        // 8. Charger la vue spécifique pour le livre de caisse
        $this->load->view('layout/header', $data);
        $this->load->view('admin/income/incomeList', $data); // Votre nouvelle vue
        $this->load->view('layout/footer', $data);
    }

    public function index_240126()
    {
        if (!$this->rbac->hasPrivilege('caisse', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Caisse');
        $this->session->set_userdata('sub_menu', 'income/index');
        $data['title'] = 'Gestion de Caisse';
        $data['title_list'] = 'Livre de Caisse';

        // Récupérer les paramètres de filtrage
        $caisse_id = $this->input->get('caisse_id');
        $date_debut = $this->input->get('date_debut');
        $date_fin = $this->input->get('date_fin');

        // Définir les dates par défaut si non fournies
        if (empty($date_debut)) {
            $date_debut = date('Y-m-01');
        }
        if (empty($date_fin)) {
            $date_fin = date('Y-m-d');
        }

        // 1. Récupérer toutes les caisses (enregistrements avec name non vide)
        $data['caisses'] = $this->get_all_caisses();

        // 2. Calculer les soldes pour chaque caisse
        $data['soldes_caisses'] = array();
        foreach ($data['caisses'] as $caisse) {
            $data['soldes_caisses'][$caisse['id']] = $this->calculer_solde_caisse($caisse['id']);
        }

        // 3. Calculer le total de centralisation
        $data['total_centralisation'] = 0;
        foreach ($data['soldes_caisses'] as $solde) {
            $data['total_centralisation'] += $solde;
        }

        // 4. Si une caisse spécifique est sélectionnée
        if ($caisse_id) {
            $data['caisse_selectionnee'] = $this->get_caisse_by_id($caisse_id);

            // Récupérer le solde initial (avant la période)
            $data['solde_initial'] = $this->get_solde_initial($caisse_id, $date_debut);

            // Récupérer les opérations pour cette caisse
            $data['operations'] = $this->get_operations_caisse_filtered($caisse_id, $date_debut, $date_fin);
        } else {
            // Récupérer toutes les opérations
            $data['operations'] = $this->get_all_operations_filtered($date_debut, $date_fin);
            $data['solde_initial'] = 0;
        }

        // 5. Récupérer les autres données nécessaires
        $data['incomelist'] = $this->income_model->get();
        $data['incomeTotal'] = $this->income_model->getTotalIncome();
        $data['incheadlist'] = $this->incomehead_model->get();
        $data['compte'] = $this->comptecomptable_model->get();
        $expnseHead          = $this->expensehead_model->get();
        $data['expheadlist'] = $expnseHead;

        // 6. Charger la vue spécifique pour le livre de caisse
        $this->load->view('layout/header', $data);
        $this->load->view('admin/income/incomeList', $data); // Votre nouvelle vue
        $this->load->view('layout/footer', $data);
    }

    private function get_categories_for_filter()
    {
        $this->db->distinct(); // Utilisez distinct() au lieu de DISTINCT dans select()
        $this->db->select('exp_category');
        $this->db->from('expense_head');
        $this->db->where('exp_category IS NOT NULL');
        $this->db->where('exp_category !=', '');
        $this->db->where('is_active', 'yes'); // Si vous avez ce champ
        $this->db->where('is_deleted', 'no'); // Si vous avez ce champ
        $this->db->order_by('exp_category', 'ASC');

        $query = $this->db->get();

        $categories = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                if (!empty($row['exp_category'])) {
                    $categories[] = $row['exp_category'];
                }
            }
        }

        return $categories;
    }

    public function finance()
    {
        if (!$this->rbac->hasPrivilege('caisse', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Caisse');
        $this->session->set_userdata('sub_menu', 'income/finance');
        $data['title'] = 'Gestion de Caisse';
        $data['title_list'] = 'Livre de Caisse';

        // Récupérer les paramètres de filtrage
        $caisse_id = $this->input->get('caisse_id');
        $date_debut = $this->input->get('date_debut');
        $date_fin = $this->input->get('date_fin');

        // Définir les dates par défaut si non fournies
        if (empty($date_debut)) {
            $date_debut = date('Y-m-01');
        }
        if (empty($date_fin)) {
            $date_fin = date('Y-m-d');
        }

        // 1. Récupérer toutes les caisses (enregistrements avec name non vide)
        $data['caisses'] = $this->get_all_caisses();

        // 2. Calculer les soldes pour chaque caisse
        $data['soldes_caisses'] = array();
        foreach ($data['caisses'] as $caisse) {
            $data['soldes_caisses'][$caisse['id']] = $this->calculer_solde_caisse($caisse['id']);
        }

        // 3. Calculer le total de centralisation
        $data['total_centralisation'] = 0;
        foreach ($data['soldes_caisses'] as $solde) {
            $data['total_centralisation'] += $solde;
        }

        // 4. Si une caisse spécifique est sélectionnée
        if ($caisse_id) {
            $data['caisse_selectionnee'] = $this->get_caisse_by_id($caisse_id);

            // Récupérer le solde initial (avant la période)
            $data['solde_initial'] = $this->get_solde_initial($caisse_id, $date_debut);

            // Récupérer les opérations pour cette caisse
            $data['operations'] = $this->get_operations_caisse_filtered($caisse_id, $date_debut, $date_fin);
        } else {
            // Récupérer toutes les opérations
            $data['operations'] = $this->get_all_operations_filtered($date_debut, $date_fin);
            $data['solde_initial'] = 0;
        }

        // 5. Récupérer les autres données nécessaires
        $data['incomelist'] = $this->income_model->get();
        $data['incomeTotal'] = $this->income_model->getTotalIncome();
        $data['incheadlist'] = $this->incomehead_model->get();
        $data['compte'] = $this->comptecomptable_model->get();
        $expnseHead          = $this->expensehead_model->get();
        $data['expheadlist'] = $expnseHead;

        // 6. Charger la vue spécifique pour le livre de caisse
        $this->load->view('layout/header', $data);
        $this->load->view('admin/income/finance', $data); // Votre nouvelle vue
        $this->load->view('layout/footer', $data);
    }

    public function global()
    {
        if (!$this->rbac->hasPrivilege('caisse', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Caisse');
        $this->session->set_userdata('sub_menu', 'income/global');
        $data['title'] = 'Gestion de Caisse';
        $data['title_list'] = 'Livre de Caisse';

        // Récupérer les paramètres de filtrage
        $caisse_id = $this->input->get('caisse_id');
        $date_debut = $this->input->get('date_debut');
        $date_fin = $this->input->get('date_fin');

        // Définir les dates par défaut si non fournies
        if (empty($date_debut)) {
            $date_debut = date('Y-m-01');
        }
        if (empty($date_fin)) {
            $date_fin = date('Y-m-d');
        }

        // 1. Récupérer toutes les caisses (enregistrements avec name non vide)
        $data['caisses'] = $this->get_all_caisses();

        // 2. Calculer les soldes pour chaque caisse
        $data['soldes_caisses'] = array();
        foreach ($data['caisses'] as $caisse) {
            $data['soldes_caisses'][$caisse['id']] = $this->calculer_solde_caisse($caisse['id']);
        }

        // 3. Calculer le total de centralisation
        $data['total_centralisation'] = 0;
        foreach ($data['soldes_caisses'] as $solde) {
            $data['total_centralisation'] += $solde;
        }

        // 4. Si une caisse spécifique est sélectionnée
        if ($caisse_id) {
            $data['caisse_selectionnee'] = $this->get_caisse_by_id($caisse_id);

            // Récupérer le solde initial (avant la période)
            $data['solde_initial'] = $this->get_solde_initial($caisse_id, $date_debut);

            // Récupérer les opérations pour cette caisse
            $data['operations'] = $this->get_operations_caisse_filtered($caisse_id, $date_debut, $date_fin);
        } else {
            // Récupérer toutes les opérations
            $data['operations'] = $this->get_all_operations_filtered($date_debut, $date_fin);
            $data['solde_initial'] = 0;
        }

        // 5. Récupérer les autres données nécessaires
        $data['incomelist'] = $this->income_model->get();
        $data['incomeTotal'] = $this->income_model->getTotalIncome();
        $data['incheadlist'] = $this->incomehead_model->get();
        $data['compte'] = $this->comptecomptable_model->get();
        $expnseHead          = $this->expensehead_model->get();
        $data['expheadlist'] = $expnseHead;

        // 6. Charger la vue spécifique pour le livre de caisse
        $this->load->view('layout/header', $data);
        $this->load->view('admin/income/global', $data); // Votre nouvelle vue
        $this->load->view('layout/footer', $data);
    }



    /**
     * Récupérer toutes les caisses (enregistrements avec name non vide)
     */
    private function get_all_caisses()
    {
        $this->db->select('*');
        $this->db->from('income');
        $this->db->where('name IS NOT NULL');
        $this->db->where('name !=', '');
        $this->db->where('is_deleted', 'no');
        $this->db->order_by('est_actif', 'DESC');
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Calculer le solde d'une caisse
     * Solde = Total des entrées (income avec type_operation = 'entrée') - Total des sorties (expenses)
     */
    private function calculer_solde_caisse($caisse_id)
    {
        // Total des entrées depuis la table income
        $this->db->select_sum('montant', 'total_entrees');
        $this->db->from('income');
        $this->db->where('id', $caisse_id);
        $this->db->where('type_operation', 'entrée');
        $this->db->where('is_deleted', 'no');
        $query_entrees = $this->db->get();
        $total_entrees = $query_entrees->row()->total_entrees ?: 0;

        // Total des sorties depuis la table expenses liées à cette caisse
        $this->db->select_sum('amount', 'total_sorties');
        $this->db->from('expenses');
        $this->db->where('inc_head_id', $caisse_id);
        $this->db->where('is_deleted', 'no');
        $query_sorties = $this->db->get();
        $total_sorties = $query_sorties->row()->total_sorties ?: 0;

        // Ajouter les réapprovisionnements (income_processing)
        $this->db->select_sum('amount', 'total_reappro');
        $this->db->from('income_processing');
        $this->db->where('income_id', $caisse_id);
        $query_reappro = $this->db->get();
        $total_reappro = $query_reappro->row()->total_reappro ?: 0;

        return ($total_entrees + $total_reappro) - $total_sorties;
    }

    /**
     * Récupérer une caisse par son ID
     */
    private function get_caisse_by_id($id)
    {
        $this->db->select('*');
        $this->db->from('income');
        $this->db->where('id', $id);
        $this->db->where('is_deleted', 'no');
        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * Récupérer le solde initial d'une caisse avant une date donnée
     */
    private function get_solde_initial($caisse_id, $date_debut)
    {
        // Total des entrées avant la date
        $this->db->select_sum('montant', 'total_entrees');
        $this->db->from('income');
        $this->db->where('id', $caisse_id);
        $this->db->where('type_operation', 'entrée');
        $this->db->where('date <', $date_debut);
        $this->db->where('is_deleted', 'no');
        $query_entrees = $this->db->get();
        $total_entrees = $query_entrees->row()->total_entrees ?: 0;

        // Total des sorties avant la date
        $this->db->select_sum('amount', 'total_sorties');
        $this->db->from('expenses');
        $this->db->where('inc_head_id', $caisse_id);
        $this->db->where('date <', $date_debut);
        $this->db->where('is_deleted', 'no');
        $query_sorties = $this->db->get();
        $total_sorties = $query_sorties->row()->total_sorties ?: 0;

        // Réapprovisionnements avant la date
        $this->db->select_sum('amount', 'total_reappro');
        $this->db->from('income_processing');
        $this->db->where('income_id', $caisse_id);
        $this->db->where('date <', $date_debut);
        $query_reappro = $this->db->get();
        $total_reappro = $query_reappro->row()->total_reappro ?: 0;

        return ($total_entrees + $total_reappro) - $total_sorties;
    }

    /**
     * Récupérer les opérations d'une caisse spécifique avec filtrage par dates
     */
    /**
     * Récupérer les opérations d'une caisse spécifique avec filtrage par dates et autres critères
     */

    // Dans la méthode get_operations_caisse_filtered
    private function get_operations_caisse_filtered($caisse_id, $date_debut, $date_fin, $categorie = null, $search = null, $mode_paiement = null) {
        $this->db->select("
        oc.*,
        c.name AS caisse_nom,
        eh.exp_category AS category_name,
        sc.nom AS sous_compte_nom,
        sc.type AS sous_compte_type
    ");

        $this->db->from('operation_caisse oc');
        $this->db->join('income c', 'c.id = oc.caisse_id', 'left');
        $this->db->join('expense_head eh', 'eh.id = oc.exp_head_id', 'left');
        $this->db->join('caisse_sous_comptes sc', 'sc.id = oc.sous_compte_id', 'left'); // Nouvelle jointure

        // Dates sécurisées
        $this->db->where('DATE(oc.date) >=', $date_debut);
        $this->db->where('DATE(oc.date) <=', $date_fin);

        // Caisse spécifique
        $this->db->where('oc.caisse_id', (int)$caisse_id);

        // FILTRE CATÉGORIE
        if (!empty($categorie)) {
            $this->db->where('eh.exp_category', $categorie);
        }

        // FILTRE RECHERCHE
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('oc.reference', $search);
            $this->db->or_like('oc.designation', $search);
            $this->db->or_like('oc.nom', $search);
            $this->db->group_end();
        }

        // NOUVEAU FILTRE : MODE DE PAIEMENT
        if (!empty($mode_paiement)) {
            $this->db->where('oc.mode_paiement', $mode_paiement);
        }

        $this->db->where('oc.deleted', 'no');

        $this->db->group_start();
        $this->db->where('oc.est_active', 'yes');
        $this->db->or_where('oc.est_actif', 'yes');
        $this->db->or_where('oc.est_active IS NULL');
        $this->db->or_where('oc.est_actif IS NULL');
        $this->db->group_end();

        $this->db->order_by('oc.date', 'ASC');
        $this->db->order_by('oc.created_at', 'ASC');

        $query = $this->db->get();
        return $query->result_array();
    }

    private function get_operations_caisse_filtered_090226($caisse_id, $date_debut, $date_fin, $categorie = null, $search = null, $mode_paiement = null)
    {
        // ===============================
        // VALIDATION DES DATES
        // ===============================
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_debut)) {
            $date_debut = date('Y-m-01');
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_fin)) {
            $date_fin = date('Y-m-d');
        }

        $this->db->select("
        oc.id,
        oc.date,
        oc.designation,
        oc.nom,
        oc.category,
        oc.mode_paiement,
        oc.user,
        oc.entree,
        oc.sortie,
        oc.solde_avant_operation,
        oc.solde_apres_operation,
        oc.reference,
        oc.exp_head_id,
        oc.type_operation,
        oc.created_at,
        c.name AS caisse_nom,
        eh.exp_category AS category_name
    ");

        $this->db->from('operation_caisse oc');
        $this->db->join('income c', 'c.id = oc.caisse_id', 'left');
        $this->db->join('expense_head eh', 'eh.id = oc.exp_head_id', 'left');

        // Dates sécurisées
        $this->db->where('DATE(oc.date) >=', $date_debut);
        $this->db->where('DATE(oc.date) <=', $date_fin);

        // Caisse spécifique
        $this->db->where('oc.caisse_id', (int)$caisse_id);

        // FILTRE CATÉGORIE
        if (!empty($categorie)) {
            $this->db->where('eh.exp_category', $categorie);
        }

        // FILTRE RECHERCHE
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('oc.reference', $search);
            $this->db->or_like('oc.designation', $search);
            $this->db->or_like('oc.nom', $search);
            $this->db->group_end();
        }

        // NOUVEAU FILTRE : MODE DE PAIEMENT
        if (!empty($mode_paiement)) {
            $this->db->where('oc.mode_paiement', $mode_paiement);
        }

        $this->db->where('oc.deleted', 'no');

        $this->db->group_start();
        $this->db->where('oc.est_active', 'yes');
        $this->db->or_where('oc.est_actif', 'yes');
        $this->db->or_where('oc.est_active IS NULL');
        $this->db->or_where('oc.est_actif IS NULL');
        $this->db->group_end();

        $this->db->order_by('oc.date', 'ASC');
        $this->db->order_by('oc.created_at', 'ASC');

        $query = $this->db->get();
        return $query->result_array();
    }

    private function get_operations_caisse_filtered_050226($caisse_id, $date_debut, $date_fin)
    {
        // ===============================
        // VALIDATION DES DATES
        // ===============================
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_debut)) {
            $date_debut = date('Y-m-01');
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_fin)) {
            $date_fin = date('Y-m-d');
        }

        $this->db->select("
        oc.id,
        oc.date,
        oc.designation,
         oc.nom,
        oc.category,
         oc.mode_paiement,
        oc.user,
        oc.entree,
        oc.sortie,
        oc.solde_avant_operation,
        oc.solde_apres_operation,
        oc.reference,
        oc.exp_head_id,
        oc.type_operation,
        oc.created_at,
        c.name AS caisse_nom,
        eh.exp_category AS category_name
    ");

        $this->db->from('operation_caisse oc');
        $this->db->join('income c', 'c.id = oc.caisse_id', 'left');
        $this->db->join('expense_head eh', 'eh.id = oc.exp_head_id', 'left');

        // Dates sécurisées
        $this->db->where('DATE(oc.date) >=', $date_debut);
        $this->db->where('DATE(oc.date) <=', $date_fin);

        // Caisse spécifique
        $this->db->where('oc.caisse_id', (int)$caisse_id);

        $this->db->where('oc.deleted', 'no');

        $this->db->group_start();
        $this->db->where('oc.est_active', 'yes');
        $this->db->or_where('oc.est_actif', 'yes');
        $this->db->or_where('oc.est_active IS NULL');
        $this->db->or_where('oc.est_actif IS NULL');
        $this->db->group_end();

        $this->db->order_by('oc.date', 'ASC');
        $this->db->order_by('oc.created_at', 'ASC');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Récupérer toutes les opérations avec filtrage par dates (toutes les caisses)
     */
    /**
     * Récupérer toutes les opérations avec filtrage par dates (toutes les caisses)
     */
    private function get_all_operations_filtered($date_debut, $date_fin, $categorie = null, $search = null, $mode_paiement = null)
    {
        // ===============================
        // VALIDATION DES DATES
        // ===============================
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_debut)) {
            $date_debut = date('Y-m-01');
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_fin)) {
            $date_fin = date('Y-m-d');
        }

        $this->db->select("
        oc.id,
        oc.date,
        oc.designation,
        oc.nom,
        oc.category,
        oc.mode_paiement,
        oc.user,
        oc.entree,
        oc.sortie,
        oc.solde_avant_operation,
        oc.solde_apres_operation,
        oc.reference,
        oc.exp_head_id,
        oc.type_operation,
        oc.created_at,
        c.name AS caisse_nom,
        eh.exp_category AS category_name
    ");

        $this->db->from('operation_caisse oc');
        $this->db->join('income c', 'c.id = oc.caisse_id', 'left');
        $this->db->join('expense_head eh', 'eh.id = oc.exp_head_id', 'left');

        // Dates sécurisées
        $this->db->where('DATE(oc.date) >=', $date_debut);
        $this->db->where('DATE(oc.date) <=', $date_fin);

        // FILTRE CATÉGORIE
        if (!empty($categorie)) {
            $this->db->where('eh.exp_category', $categorie);
        }

        // FILTRE RECHERCHE
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('oc.reference', $search);
            $this->db->or_like('oc.designation', $search);
            $this->db->or_like('oc.nom', $search);
            $this->db->group_end();
        }

        // NOUVEAU FILTRE : MODE DE PAIEMENT
        if (!empty($mode_paiement)) {
            $this->db->where('oc.mode_paiement', $mode_paiement);
        }

        $this->db->where('oc.deleted', 'no');

        $this->db->group_start();
        $this->db->where('oc.est_active', 'yes');
        $this->db->or_where('oc.est_actif', 'yes');
        $this->db->or_where('oc.est_active IS NULL');
        $this->db->or_where('oc.est_actif IS NULL');
        $this->db->group_end();

        $this->db->order_by('oc.date', 'ASC');
        $this->db->order_by('oc.created_at', 'ASC');

        $query = $this->db->get();
        return $query->result_array();
    }

    private function get_all_operations_filtered_050226($date_debut, $date_fin)
    {
        // ===============================
        // VALIDATION DES DATES
        // ===============================
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_debut)) {
            $date_debut = date('Y-m-01');
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_fin)) {
            $date_fin = date('Y-m-d');
        }

        $this->db->select("
        oc.id,
        oc.date,
        oc.designation,
         oc.nom,
        oc.category,
         oc.mode_paiement,
        oc.user,
        oc.entree,
        oc.sortie,
        oc.solde_avant_operation,
        oc.solde_apres_operation,
        oc.reference,
        oc.exp_head_id,
        oc.type_operation,
        oc.created_at,
        c.name AS caisse_nom,
        eh.exp_category AS category_name
    ");

        $this->db->from('operation_caisse oc');
        $this->db->join('income c', 'c.id = oc.caisse_id', 'left');
        $this->db->join('expense_head eh', 'eh.id = oc.exp_head_id', 'left');

        // Dates sécurisées
        $this->db->where('DATE(oc.date) >=', $date_debut);
        $this->db->where('DATE(oc.date) <=', $date_fin);

        $this->db->where('oc.deleted', 'no');

        $this->db->group_start();
        $this->db->where('oc.est_active', 'yes');
        $this->db->or_where('oc.est_actif', 'yes');
        $this->db->or_where('oc.est_active IS NULL');
        $this->db->or_where('oc.est_actif IS NULL');
        $this->db->group_end();

        $this->db->order_by('oc.date', 'ASC');
        $this->db->order_by('oc.created_at', 'ASC');

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Récupérer les réapprovisionnements pour une période donnée
     */
    private function get_reapprovisionnements($date_debut, $date_fin, $caisse_id = null)
    {
        // ===============================
        // VALIDATION DES DATES
        // ===============================
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_debut)) {
            $date_debut = date('Y-m-01');
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_fin)) {
            $date_fin = date('Y-m-d');
        }

        $this->db->select("
        ip.id,
        ip.date,
        'Réapprovisionnement' as designation,
        'Réapprovisionnement' as category,
        CONCAT(u.firstname, ' ', u.lastname) as user,
        ip.amount as entree,
        0 as sortie,
        0 as solde_avant_operation,
        0 as solde_apres_operation,
        CONCAT('REAPP-', ip.id) as reference,
        ip.exp_head_id,
        'réappro' as type_operation,
        ip.created_at,
        c.name AS caisse_nom,
        'Réapprovisionnement' as category_name
    ");

        $this->db->from('income_processing ip');
        $this->db->join('income c', 'c.id = ip.income_id', 'left');
        $this->db->join('staff u', 'u.id = ip.added_by', 'left');

        // Dates sécurisées
        $this->db->where('DATE(ip.date) >=', $date_debut);
        $this->db->where('DATE(ip.date) <=', $date_fin);

        // Caisse spécifique si fournie
        if (!empty($caisse_id)) {
            $this->db->where('ip.income_id', (int)$caisse_id);
        }

        $this->db->order_by('ip.date', 'ASC');
        $this->db->order_by('ip.created_at', 'ASC');

        $query = $this->db->get();
        $results = $query->result_array();

        // Marquer les réapprovisionnements pour identification facile
        foreach ($results as &$result) {
            $result['is_reappro'] = true;
        }

        return $results;
    }

// Nouvelle fonction pour récupérer les opérations normales

// Nouvelle fonction pour récupérer les réapprovisionnements
    private function get_reapprovisionnements_($date_debut, $date_fin, $caisse_id = null)
    {
        $this->db->select("
        ip.id,
        ip.date,
        ip.reason as designation,
        ip.amount as entree,
        0 as sortie,
        CONCAT('REAPP-', ip.id) as reference,
        'Réappro' as category,
        'Système' as user,
        c.amount_re as solde_avant_operation,
        (c.amount_re + ip.amount) as solde_apres_operation,
        'réappro' as type_operation,
        ip.created_at,
        c.name as caisse_nom,
      
        'réapprovisionnement' as operation_type,
        ip.amount as montant_reappro
    ");

        $this->db->from('income_processing ip');
        $this->db->join('income c', 'c.id = ip.income_id', 'left');
        $this->db->where('ip.date >=', $date_debut);
        $this->db->where('ip.date <=', $date_fin);

        // Filtrer par caisse si spécifiée
        if ($caisse_id && $caisse_id != 'all') {
            $this->db->where('ip.income_id', $caisse_id);
        }

        $this->db->order_by('ip.date', 'ASC');
        $this->db->order_by('ip.created_at', 'ASC');

        $query = $this->db->get();
        $reapprovisionnements = $query->result_array();

        // Formater les réapprovisionnements
        foreach ($reapprovisionnements as &$reappro) {
            if (!empty($reappro['caisse_nom'])) {
                $reappro['designation'] = 'Réappro: ' . $reappro['designation'] . ' - ' . $reappro['caisse_nom'];
            } else {
                $reappro['designation'] = 'Réappro: ' . $reappro['designation'];
            }

            // Formatage des valeurs numériques
            $reappro['entree'] = (float)$reappro['entree'];
            $reappro['sortie'] = (float)$reappro['sortie'];
            $reappro['solde_avant_operation'] = (float)$reappro['solde_avant_operation'];
            $reappro['solde_apres_operation'] = (float)$reappro['solde_apres_operation'];
            $reappro['montant_reappro'] = (float)$reappro['montant_reappro'];

            // Marquer comme réapprovisionnement
            $reappro['operation_type'] = 'reappro';
        }

        return $reapprovisionnements;
    }



    /**
     * Récupérer toutes les opérations (toutes les caisses)
     */


    // Fonction pour mettre à jour une opération
    public function update_operation_() {
        // Vérifier les permissions
        if (!$this->rbac->hasPrivilege('caisse', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission non accordée']);
            return;
        }

        $this->load->library('form_validation');

        // Règles de validation
        $this->form_validation->set_rules('operation_id', 'ID Opération', 'required|numeric');
        $this->form_validation->set_rules('caisse_id', 'Caisse', 'required|numeric');
        $this->form_validation->set_rules('date', 'Date', 'required');
        $this->form_validation->set_rules('type', 'Type', 'required|in_list[entree,sortie]');
        $this->form_validation->set_rules('designation', 'Désignation', 'required');
        $this->form_validation->set_rules('montant', 'Montant', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('exp_head_id', 'Catégorie', 'required|numeric');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'success' => false,
                'errors' => $this->form_validation->error_array()
            ]);
            return;
        }

        $this->load->model('income_model');

        // Préparer les données
        $operation_id = $this->input->post('operation_id');
        $caisse_id = $this->input->post('caisse_id');
        $date = $this->input->post('date');
        $type = $this->input->post('type');
        $designation = $this->input->post('designation');
        $montant = floatval($this->input->post('montant'));
        $exp_head_id = $this->input->post('exp_head_id');
        $reference = $this->input->post('reference');
        $mode_paiement = $this->input->post('mode_paiement');
        $exp_category_name = $this->input->post('exp_category_name');

        // Préparer les données pour le modèle
        $operation_data = [
            'caisse_id' => $caisse_id,
            'date' => $date,
            'designation' => $designation,
            'reference' => $reference,
            'mode_paiement' => $mode_paiement,
            'exp_head_id' => $exp_head_id,
            'exp_category_name' => $exp_category_name,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Déterminer si c'est une entrée ou sortie
        if ($type == 'entree') {
            $operation_data['entree'] = $montant;
            $operation_data['sortie'] = 0;
        } else {
            $operation_data['entree'] = 0;
            $operation_data['sortie'] = $montant;
        }

        // Mettre à jour l'opération
        $success = $this->income_model->update_operation($operation_id, $operation_data);

        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Opération modifiée avec succès'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la modification de l\'opération'
            ]);
        }
    }

    // Fonction pour supprimer une opération
    public function delete_operation_($id) {
        // Vérifier les permissions
        if (!$this->rbac->hasPrivilege('caisse', 'can_delete')) {
            echo json_encode(['success' => false, 'message' => 'Permission non accordée']);
            return;
        }

        $this->load->model('income_model');

        // Vérifier si l'opération existe
        $operation = $this->income_model->get_operation_by_id($id);

        if (!$operation) {
            echo json_encode(['success' => false, 'message' => 'Opération non trouvée']);
            return;
        }

        // Supprimer l'opération
        $success = $this->income_model->delete_operation($id);

        if ($success) {
            echo json_encode([
                'success' => true,
                'message' => 'Opération supprimée avec succès'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'opération'
            ]);
        }
    }
    private function get_all_operationse($date_debut, $date_fin, $caisse_id = null)
    {
        $this->db->select("
        oc.id,
        oc.date,
        oc.designation,
         oc.nom,
        oc.category,
         oc.mode_paiement,
        oc.user,delete_operation
        oc.entree,
        oc.sortie,
        oc.solde_avant_operation,
        oc.solde_apres_operation,
        oc.reference,
        oc.exp_head_id,
        oc.type_operation,
        oc.created_at,
        c.name as caisse_nom,
      
        eh.exp_category as category_name,
        oc.deleted,
        oc.est_active,
        oc.est_actif
    ");

        $this->db->from('operation_caisse oc');
        $this->db->join('income c', 'c.id = oc.caisse_id', 'left');
        $this->db->join('expense_head eh', 'eh.id = oc.exp_head_id', 'left');

        // Filtrer par dates
        $this->db->where('oc.date >=', $date_debut);
        $this->db->where('oc.date <=', $date_fin);

        // Filtrer par caisse si spécifiée
        if ($caisse_id && $caisse_id != 'all') {
            $this->db->where('oc.caisse_id', $caisse_id);
        }

        // TEMPORAIREMENT - Pas de filtre pour voir toutes les opérations
        // $this->db->where('oc.deleted', 'no');
        // $this->db->group_start();
        // $this->db->where('oc.est_active', 'yes');
        // $this->db->or_where('oc.est_actif', 'yes');
        // $this->db->group_end();

        // Trier par date
        $this->db->order_by('oc.date', 'ASC');
        $this->db->order_by('oc.created_at', 'ASC');

        $query = $this->db->get();
        $operations = $query->result_array();

        // Afficher un log pour débogage
        log_message('debug', 'Nombre d\'opérations trouvées: ' . count($operations));
        foreach ($operations as $op) {
            log_message('debug', 'Opération: ' . $op['reference'] . ' | Entrée: ' . $op['entree'] . ' | Sortie: ' . $op['sortie'] . ' | Type: ' . $op['type_operation']);
        }

        // ... reste du code ...
        return $operations;
    }

    /**
     * Créer une nouvelle opération (entrée/sortie)
     */

    public function create_operation()
    {
        if (!$this->rbac->hasPrivilege('caisse', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('caisse_id', 'Caisse', 'required');
        $this->form_validation->set_rules('date', 'Date', 'required');
        $this->form_validation->set_rules('type', 'Type d\'opération', 'required');
        $this->form_validation->set_rules('designation', 'Désignation', 'required');
        $this->form_validation->set_rules('montant', 'Montant', 'required|numeric|greater_than[0]');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('admin/income');
        }

        $caisse_id = $this->input->post('caisse_id');
        $type = $this->input->post('type');
        $date = $this->input->post('date');
        $designation = $this->input->post('designation');
        $montant = $this->input->post('montant');
        $nom = $this->input->post('nom');
        $exp_head_id = $this->input->post('exp_head_id');
        $category = $this->input->post('exp_category_name');
        $reference = $this->input->post('reference');
        $mode_paiement = $this->input->post('mode_paiement');

        // 1. Récupérer les informations actuelles de la caisse
        $this->db->select('amount, amount_re, total_entrees, total_sorties, solde_initial');
        $this->db->from('income');
        $this->db->where('id', $caisse_id);
        $caisse_info = $this->db->get()->row();

        if (!$caisse_info) {
            $this->session->set_flashdata('error', 'Caisse non trouvée.');
            redirect('admin/income');
        }

        $current_amount_re = floatval($caisse_info->amount_re);
        $current_total_entrees = floatval($caisse_info->total_entrees);
        $current_total_sorties = floatval($caisse_info->total_sorties);

        // 2. Calculer le solde avant l'opération
        $solde_avant_operation = $current_amount_re;

        // 3. Calculer le solde après l'opération
        $solde_apres_operation = 0;
        if ($type == 'entree') {
            $solde_apres_operation = $solde_avant_operation + $montant;
        } else {
            // Vérifier si le solde est suffisant pour une sortie
            if ($solde_avant_operation < $montant) {
                $this->session->set_flashdata('error', 'Solde insuffisant dans la caisse. Solde disponible: ' . number_format($solde_avant_operation, 2, ',', ' ') . ' FCFA');
                redirect('admin/income');
            }
            $solde_apres_operation = $solde_avant_operation - $montant;
        }

        // 4. Enregistrer l'opération
        if ($type == 'entree') {
            // Enregistrer comme entrée dans operation_caisse
            $data = [
                'date' => $date,
                'caisse_id' => $caisse_id,
                'designation' => $designation,
                'montant' => $montant,
                'nom' => $nom,
                'entree' => $montant,
                'sortie' => 0,
                'exp_head_id' => $exp_head_id,
                'category' => $category,
                'type_operation' => 'entrée',
                'solde_avant_operation' => $solde_avant_operation,
                'solde_apres_operation' => $solde_apres_operation,
                'user' => $this->customlib->getAdminSessionUserName(),
                'reference' => $reference ?: 'RECU-' . uniqid(),
                'deleted' => 'no',
                'est_active' => 'yes',
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('operation_caisse', $data);
            $operation_id = $this->db->insert_id();

            // 5. Mettre à jour les totaux de la caisse
            $update_data = [
                'amount_re' => $solde_apres_operation,
                'total_entrees' => $current_total_entrees + $montant,
                'last_operation_date' => date('Y-m-d H:i:s')
            ];
            $this->db->where('id', $caisse_id);
            $this->db->update('income', $update_data);

            // Enregistrer dans mouvements
            $mouvement_data = [
                'type_mouvement' => 'entree',
                'montant' => $montant,
                'exp_head_id' => $exp_head_id,
                'description' => $designation,
                'reference' => $reference ?: 'RECU-' . uniqid(),
                'id_employe' => null,
                'date_mouvement' => $date,
                'mode_paiement' => $mode_paiement,
                'solde_avant_operation' => $solde_avant_operation,
                'solde_apres_operation' => $solde_apres_operation,
                'operation_id' => $operation_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->db->insert('mouvements', $mouvement_data);

        } else {
            // Enregistrer comme sortie dans operation_caisse
            $expense_data = [
                'caisse_id' => $caisse_id,
                'designation' => $designation,
                'date' => $date,
                'montant' => $montant,
                'nom' => $nom,
                'entree' => 0,
                'sortie' => $montant,
                'exp_head_id' => $exp_head_id,
                'type_operation' => 'sortie',
                'solde_avant_operation' => $solde_avant_operation,
                'solde_apres_operation' => $solde_apres_operation,
                'reference' => $reference ?: 'DEP-' . uniqid(),
                'deleted' => 'no',
                'est_actif' => 'yes',
                'user' => $this->customlib->getAdminSessionUserName(),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('operation_caisse', $expense_data);
            $operation_id = $this->db->insert_id();

            // 5. Mettre à jour les totaux de la caisse
            $update_data = [
                'amount_re' => $solde_apres_operation,
                'total_sorties' => $current_total_sorties + $montant,
                'last_operation_date' => date('Y-m-d H:i:s')
            ];
            $this->db->where('id', $caisse_id);
            $this->db->update('income', $update_data);

            // Enregistrer dans mouvements
            $mouvement_data = [
                'type_mouvement' => 'sortie',
                'montant' => $montant,
                'exp_head_id' => $exp_head_id,
                'description' => $designation,
                'reference' => $reference ?: 'DEP-' . uniqid(),
                'id_employe' => null,
                'date_mouvement' => $date,
                'mode_paiement' => $mode_paiement,
                'solde_avant_operation' => $solde_avant_operation,
                'solde_apres_operation' => $solde_apres_operation,
                'operation_id' => $operation_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->db->insert('mouvements', $mouvement_data);
        }

        $this->session->set_flashdata('msg', '<div class="alert alert-success">Opération enregistrée avec succès. Solde avant: ' . number_format($solde_avant_operation, 2, ',', ' ') . ' FCFA | Solde après: ' . number_format($solde_apres_operation, 2, ',', ' ') . ' FCFA</div>');
        redirect('admin/income');
    }


    public function create_operation_090226()
    {
        if (!$this->rbac->hasPrivilege('caisse', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('caisse_id', 'Caisse', 'required');
        $this->form_validation->set_rules('date', 'Date', 'required');
        $this->form_validation->set_rules('type', 'Type d\'opération', 'required');
        $this->form_validation->set_rules('designation', 'Désignation', 'required');
        $this->form_validation->set_rules('montant', 'Montant', 'required|numeric|greater_than[0]');

        if ($this->form_validation->run() == false) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('admin/income');
        }

        $caisse_id = $this->input->post('caisse_id');
        $type = $this->input->post('type');
        $date = $this->input->post('date');
        $designation = $this->input->post('designation');
        $montant = $this->input->post('montant');
        $nom = $this->input->post('nom');
        $exp_head_id = $this->input->post('exp_head_id');
        $category = $this->input->post('exp_category_name');
        $reference = $this->input->post('reference');
        $mode_paiement = $this->input->post('mode_paiement');

        // 1. Récupérer les informations actuelles de la caisse
        $this->db->select('amount, amount_re, total_entrees, total_sorties, solde_initial');
        $this->db->from('income');
        $this->db->where('id', $caisse_id);
        $caisse_info = $this->db->get()->row();

        if (!$caisse_info) {
            $this->session->set_flashdata('error', 'Caisse non trouvée.');
            redirect('admin/income');
        }

        $current_amount_re = floatval($caisse_info->amount_re);
        $current_total_entrees = floatval($caisse_info->total_entrees);
        $current_total_sorties = floatval($caisse_info->total_sorties);

        // 2. Calculer le solde avant l'opération
        $solde_avant_operation = $current_amount_re;

        // 3. Calculer le solde après l'opération
        $solde_apres_operation = 0;
        if ($type == 'entree') {
            $solde_apres_operation = $solde_avant_operation + $montant;
        } else {
            // Vérifier si le solde est suffisant pour une sortie
            if ($solde_avant_operation < $montant) {
                $this->session->set_flashdata('error', 'Solde insuffisant dans la caisse. Solde disponible: ' . number_format($solde_avant_operation, 2, ',', ' ') . ' FCFA');
                redirect('admin/income');
            }
            $solde_apres_operation = $solde_avant_operation - $montant;
        }

        // 4. Enregistrer l'opération
        if ($type == 'entree') {
            // Enregistrer comme entrée dans operation_caisse
            $data = [
                'date' => $date,
                'caisse_id' => $caisse_id,
                'designation' => $designation,
                'montant' => $montant,
                'mode_paiement' => $mode_paiement,
                'entree' => $montant,
                 'nom' => $nom,
                'sortie' => 0,
                'exp_head_id' => $exp_head_id,
                'category' => $category,
                'type_operation' => 'entrée',
                'solde_avant_operation' => $solde_avant_operation,
                'solde_apres_operation' => $solde_apres_operation,
                'user' => $this->customlib->getAdminSessionUserName(),
                'reference' => $reference ?: 'RECU-' . uniqid(),
                'deleted' => 'no',
                'est_active' => 'yes',
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('operation_caisse', $data);
            $operation_id = $this->db->insert_id();

            // 5. Mettre à jour les totaux de la caisse
            $update_data = [
                'amount_re' => $solde_apres_operation,
                'total_entrees' => $current_total_entrees + $montant,
                'last_operation_date' => date('Y-m-d H:i:s')
            ];
            $this->db->where('id', $caisse_id);
            $this->db->update('income', $update_data);

            // Enregistrer dans mouvements
            $mouvement_data = [
                'type_mouvement' => 'entree',
                'montant' => $montant,
                'exp_head_id' => $exp_head_id,
                'description' => $designation,
                'reference' => $reference ?: 'RECU-' . uniqid(),
                'id_employe' => null,
                'date_mouvement' => $date,
                'mode_paiement' => $mode_paiement,
                'solde_avant_operation' => $solde_avant_operation,
                'solde_apres_operation' => $solde_apres_operation,
                'operation_id' => $operation_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->db->insert('mouvements', $mouvement_data);

        } else {
            // Enregistrer comme sortie dans operation_caisse
            $expense_data = [
                'caisse_id' => $caisse_id,
                'designation' => $designation,
                'date' => $date,
                'montant' => $montant,
                'mode_paiement' => $mode_paiement,
                'nom' => $nom,
                'entree' => 0,
                'sortie' => $montant,
                'exp_head_id' => $exp_head_id,
                'type_operation' => 'sortie',
                'solde_avant_operation' => $solde_avant_operation,
                'solde_apres_operation' => $solde_apres_operation,
                'reference' => $reference ?: 'DEP-' . uniqid(),
                'deleted' => 'no',
                'est_actif' => 'yes',
                'user' => $this->customlib->getAdminSessionUserName(),
                'created_at' => date('Y-m-d H:i:s')
            ];

            $this->db->insert('operation_caisse', $expense_data);
            $operation_id = $this->db->insert_id();

            // 5. Mettre à jour les totaux de la caisse
            $update_data = [
                'amount_re' => $solde_apres_operation,
                'total_sorties' => $current_total_sorties + $montant,
                'last_operation_date' => date('Y-m-d H:i:s')
            ];
            $this->db->where('id', $caisse_id);
            $this->db->update('income', $update_data);

            // Enregistrer dans mouvements
            $mouvement_data = [
                'type_mouvement' => 'sortie',
                'montant' => $montant,
                'exp_head_id' => $exp_head_id,
                'description' => $designation,
                'reference' => $reference ?: 'DEP-' . uniqid(),
                'id_employe' => null,
                'date_mouvement' => $date,
                'mode_paiement' => $mode_paiement,
                'solde_avant_operation' => $solde_avant_operation,
                'solde_apres_operation' => $solde_apres_operation,
                'operation_id' => $operation_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
            $this->db->insert('mouvements', $mouvement_data);
        }

        $this->session->set_flashdata('msg', '<div class="alert alert-success">Opération enregistrée avec succès. Solde avant: ' . number_format($solde_avant_operation, 2, ',', ' ') . ' FCFA | Solde après: ' . number_format($solde_apres_operation, 2, ',', ' ') . ' FCFA</div>');
        redirect('admin/income');
    }

    public function init_soldes_caisse()
    {
        if (!$this->rbac->hasPrivilege('superadmin', 'can_add')) {
            access_denied();
        }

        // Mettre à jour toutes les caisses pour initialiser amount_re avec amount
        $this->db->set('amount_re', 'amount', false);
        $this->db->set('solde_initial', 'amount', false);
        $this->db->where('is_deleted', 'no');
        $this->db->update('income');

        // Calculer les totaux initiaux depuis les opérations existantes
        $caisses = $this->db->get_where('income', ['is_deleted' => 'no'])->result_array();

        foreach ($caisses as $caisse) {
            // Calculer total des entrées
            $this->db->select('SUM(entree) as total_entrees');
            $this->db->where('caisse_id', $caisse['id']);
            $this->db->where('deleted', 'no');
            $entrees = $this->db->get('operation_caisse')->row();

            // Calculer total des sorties
            $this->db->select('SUM(sortie) as total_sorties');
            $this->db->where('caisse_id', $caisse['id']);
            $this->db->where('deleted', 'no');
            $sorties = $this->db->get('operation_caisse')->row();

            // Mettre à jour les totaux
            $update_data = [
                'total_entrees' => $entrees->total_entrees ?? 0,
                'total_sorties' => $sorties->total_sorties ?? 0,
                'amount_re' => $caisse['amount'] + ($entrees->total_entrees ?? 0) - ($sorties->total_sorties ?? 0)
            ];

            $this->db->where('id', $caisse['id']);
            $this->db->update('income', $update_data);
        }

        $this->session->set_flashdata('msg', '<div class="alert alert-success">Soldes des caisses initialisés avec succès.</div>');
        redirect('admin/income');
    }



    // Les autres méthodes restent inchangées...
    public function download($documents)
    {
        $this->load->helper('download');
        $filepath = "./uploads/school_income/" . $this->uri->segment(6);
        $data     = file_get_contents($filepath);
        $name     = $this->uri->segment(6);
        force_download($name, $data);
    }

    public function view($id)
    {
        if (!$this->rbac->hasPrivilege('caisse', 'can_view')) {
            access_denied();
        }
        $data['title']  = 'Fees Master List';
        $income         = $this->income_model->get($id);
        $data['income'] = $income;
        $this->load->view('layout/header', $data);
        $this->load->view('income/incomeShow', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getByFeecategory()
    {
        $feecategory_id = $this->input->get('feecategory_id');
        $data           = $this->feetype_model->getTypeByFeecategory($feecategory_id);
        echo json_encode($data);
    }

    public function getStudentCategoryFee()
    {
        $type     = $this->input->post('type');
        $class_id = $this->input->post('class_id');
        $data     = $this->income_model->getTypeByFeecategory($type, $class_id);
        if (empty($data)) {
            $status = 'fail';
        } else {
            $status = 'success';
        }
        $array = array('status' => $status, 'data' => $data);
        echo json_encode($array);
    }



    public function delete($id)
    {
        // Vérifier les permissions
        if (!$this->rbac->hasPrivilege('caisse', 'can_delete')) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => false, 'message' => 'Permission non accordée']);
                return;
            } else {
                access_denied();
                return;
            }
        }

        // Vérifier que l'ID est valide
        if (empty($id) || !is_numeric($id)) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(['success' => false, 'message' => 'ID invalide']);
                return;
            } else {
                $this->session->set_flashdata('msg', '<div class="alert alert-danger">ID invalide</div>');
                redirect('admin/income');
                return;
            }
        }

        // Appeler le modèle pour supprimer
        $result = $this->income_model->remove($id);

        if ($this->input->is_ajax_request()) {
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Caisse supprimée avec succès',
                    'id' => $id
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Erreur lors de la suppression'
                ]);
            }
        } else {
            if ($result) {
                $this->session->set_flashdata('msg', '<div class="alert alert-success">Caisse supprimée avec succès</div>');
            } else {
                $this->session->set_flashdata('msg', '<div class="alert alert-danger">Erreur lors de la suppression</div>');
            }
            redirect('admin/income');
        }
    }



    public function create()
    {
        // Vérifier les permissions
        if (!$this->rbac->hasPrivilege('caisse', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('name', 'Nom de la caisse', 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', 'Date', 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', 'Montant initial', 'trim|required|numeric|greater_than_equal_to[0]');
        $this->form_validation->set_rules('est_actif', 'Statut', 'trim|required|in_list[0,1]');

        if ($this->form_validation->run() == false) {
            // Si validation échoue, retourner à la page avec les erreurs
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">' . validation_errors() . '</div>');
            redirect('admin/income/index');
        } else {
            // Préparer les données pour la caisse
            $data = array(
                'name' => $this->input->post('name'),
                'user' => $this->input->post('user') ?: $this->customlib->getAdminSessionUserName(),
                'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'invoice_no' => $this->input->post('invoice_no') ?: 'CAISSE-' . date('Ymd') . '-' . strtoupper(uniqid()),
                'note' => $this->input->post('description'),
                'amount' => $this->input->post('amount'),
                'amount_re' => $this->input->post('amount'), // Montant initial = montant actuel
                'montant' => $this->input->post('amount'), // Montant initial
                'est_actif' => $this->input->post('est_actif'),
                'type_operation' => 'caisse', // Indiquer que c'est une caisse
                'is_active' => 'yes',
                'is_deleted' => 'no',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );

            // Gérer l'upload du document si présent
            if (isset($_FILES["documents"]) && !empty($_FILES['documents']['name'])) {
                $config['upload_path']   = './uploads/school_income/';
                $config['allowed_types'] = 'jpg|jpeg|png|pdf|doc|docx|xls|xlsx';
                $config['max_size']      = 2048; // 2MB
                $config['file_name']     = 'caisse_' . time();

                $this->load->library('upload', $config);

                if ($this->upload->do_upload('documents')) {
                    $upload_data = $this->upload->data();
                    $data['documents'] = 'uploads/school_income/' . $upload_data['file_name'];
                }
            }

            // Insérer la caisse dans la base de données
            $caisse_id = $this->income_model->add($data);

            if ($caisse_id) {
                // Enregistrer le mouvement initial
                $mouvement_data = [
                    'type_mouvement' => 'creation_caisse',
                    'montant' => $this->input->post('amount'),
                    'description' => 'Création de la caisse: ' . $this->input->post('name'),
                    'reference' => $data['invoice_no'],
                    'id_employe' => null,
                    'date_mouvement' => date('Y-m-d H:i:s'),
                    'mode_paiement' => 'espèces',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $this->db->insert('mouvements', $mouvement_data);

                // Enregistrer les écritures comptables (si nécessaire)
                $desc = "Création caisse: " . $this->input->post('name');

                // Débit Caisse (571)
                $this->db->insert('accounting_entries', [
                    'date' => date('Y-m-d'),
                    'invoice_id' => $caisse_id,
                    'account' => '571 - Caisse',
                    'debit' => $this->input->post('amount'),
                    'credit' => 0,
                    'description' => $desc,
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                // Crédit Capital (101)
                $this->db->insert('accounting_entries', [
                    'date' => date('Y-m-d'),
                    'invoice_id' => $caisse_id,
                    'account' => '101 - Capital',
                    'debit' => 0,
                    'credit' => $this->input->post('amount'),
                    'description' => $desc,
                    'created_at' => date('Y-m-d H:i:s')
                ]);

                $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Caisse créée avec succès</div>');
            } else {
                $this->session->set_flashdata('msg', '<div class="alert alert-danger text-left">Erreur lors de la création de la caisse</div>');
            }

            redirect('admin/income/index');
        }
    }



    public function IncomeByID(){
        if($this->session->set_userdata('user_login_access') != False) {
            $id= $this->input->get('id');
            $data['incomeByid'] = $this->logistic_model->GetIncomeValueId($id);
            echo json_encode($data);
        }
        else{
            redirect(base_url() , 'refresh');
        }
    }

    public function handle_upload()
    {
        $image_validate = $this->config->item('file_validate');
        $result         = $this->filetype_model->get();
        if (isset($_FILES["documents"]) && !empty($_FILES['documents']['name'])) {

            $file_type = $_FILES["documents"]['type'];
            $file_size = $_FILES["documents"]["size"];
            $file_name = $_FILES["documents"]["name"];

            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->file_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->file_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($files = filesize($_FILES['documents']['tmp_name'])) {

                if (!in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', 'File Type Not Allowed');
                    return false;
                }

                if (!in_array($ext, $allowed_extension) || !in_array($file_type, $allowed_mime_type)) {
                    $this->form_validation->set_message('handle_upload', 'Extension Not Allowed');
                    return false;
                }
                if ($file_size > $result->file_size) {
                    $this->form_validation->set_message('handle_upload', $this->lang->line('file_size_shoud_be_less_than') . number_format($image_validate['upload_size'] / 1048576, 2) . " MB");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', "File Type / Extension Error Uploading  Image");
                return false;
            }

            return true;
        }
        return true;
    }

    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('caisse', 'can_edit')) {
            access_denied();
        }

        $data['title']      = 'Edit Caisse';
        $data['id']         = $id;
        $income             = $this->income_model->got($id);
        $data['income']     = $income;
        $data['title_list'] = 'Liste des Caisses';

        $journal_comptable  = $this->journal_model->get();
        $data['journal']    = $journal_comptable;

        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('documents', $this->lang->line('documents'), 'callback_handle_upload');

        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/income/incomeEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $amount    = (float) $this->input->post('amount');
            $amount_re = (float) $this->input->post('amount_re');

            $data = array(
                'id'             => $id,
                'name'           => $this->input->post('name'),
                'user'           => $this->input->post('user'),
                'date'           => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'amount'         => $amount,
                'amount_re'      => $amount_re,
                'invoice_no'     => $this->input->post('invoice_no'),
                'note'           => $this->input->post('description'),
                'est_actif'      => $this->input->post('est_actif') ? 1 : 0,
                'type_operation' => $this->input->post('type_operation'),
            );

            $insert_id = $this->income_model->add($data);

            if (isset($_FILES["documents"]) && !empty($_FILES['documents']['name'])) {
                $fileInfo = pathinfo($_FILES["documents"]["name"]);
                $img_name = $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["documents"]["tmp_name"], "./uploads/school_income/" . $img_name);
                $data_img = array('id' => $id, 'documents' => 'uploads/school_income/' . $img_name);
                $this->income_model->add($data_img);
            }

            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/income/index');
        }
    }

    public function increase_edit($id)
    {
        if (!$this->rbac->hasPrivilege('caisse', 'can_edit')) {
            access_denied();
        }
        $data['title']       = 'Edit Fees Master';
        $data['id']          = $id;
        $income              = $this->income_model->get($id);
        $data['income']      = $income;
        $data['title_list']  = 'Fees Master List';
        $expnseHead          = $this->incomehead_model->get();
        $data['incheadlist'] = $expnseHead;
        $this->form_validation->set_rules('inc_head_id', $this->lang->line('income_head'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('documents', $this->lang->line('documents'), 'callback_handle_upload');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/income/incomeEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'id'          => $id,
                'inc_head_id' => $this->input->post('inc_head_id'),
                'name'        => $this->input->post('name'),
                'user'        => $this->input->post('user'),
                'date'        => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'amount'      => $this->input->post('amount'),
                'invoice_no'  => $this->input->post('invoice_no'),
                'note'        => $this->input->post('description'),
                'status'        => $this->input->post('status'),
            );
            $insert_id = $this->income_model->add($data);
            if (isset($_FILES["documents"]) && !empty($_FILES['documents']['name'])) {
                $fileInfo = pathinfo($_FILES["documents"]["name"]);
                $img_name = $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["documents"]["tmp_name"], "./uploads/school_income/" . $img_name);
                $data_img = array('id' => $id, 'documents' => 'uploads/school_income/' . $img_name);
                $this->income_model->add($data_img);
            }

            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/income/index');
        }
    }

    public function transfer_amount()
    {
        $this->session->set_userdata('sub_menu', 'income/transfer_amount');
        $from_id = $this->input->post('from_caisse_id');
        $to_id   = $this->input->post('to_caisse_id');
        $amount  = $this->input->post('amount');

        // Vérifier que les deux caisses existent
        $from = $this->income_model->get($from_id);
        $to   = $this->income_model->get($to_id);

        if ($from && $to && $from->status == 'fermée' && $to->status == 'ouverte') {
            if ($from->amount_re >= $amount && $amount > 0) {

                // Déduire de la caisse fermée
                $this->income_model->update($from_id, [
                    'amount_re' => $from->amount_re - $amount
                ]);

                // Ajouter à la caisse ouverte
                $this->income_model->update($to_id, [
                    'amount_re' => $to->amount_re + $amount
                ]);

                // Enregistrer le transfert (table historique_transfert)
                $this->db->insert('transfert_caisse', [
                    'from_id' => $from_id,
                    'to_id'   => $to_id,
                    'amount'  => $amount,
                    'date'    => date('Y-m-d H:i:s')
                ]);

                $this->session->set_flashdata('msg_success', 'Montant transféré avec succès.');
            } else {
                $this->session->set_flashdata('msg_error', 'Montant invalide ou solde insuffisant.');
            }
        } else {
            $this->session->set_flashdata('msg_error', 'Caisses invalides ou statut incorrect.');
        }

        redirect('admin/income/transfer_form');
        $this->load->view('layout/header');
        $this->load->view('admin/income/transfer_form');
        $this->load->view('layout/footer');
    }

    public function incomeSearch()
    {
        if (!$this->rbac->hasPrivilege('search_due_fees', 'can_view')) {
            access_denied();
        }
        $data['searchlist'] = $this->customlib->get_searchtype();
        $this->session->set_userdata('top_menu', 'Income');
        $this->session->set_userdata('sub_menu', 'income/incomesearch');
        $data['search_type'] = '';
        $data['title']       = 'Search Income';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/income/incomeSearch', $data);
        $this->load->view('layout/footer', $data);

    }

    public function getincomelist()
    {
        $m = $this->income_model->getincomelist(); // Appelle le modèle
        $m = json_decode($m);
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $dt_data = array();

        if (!empty($m->data)) {
            foreach ($m->data as $key => $value) {
                $editbtn     = '';
                $deletebtn   = '';
                $documents   = '';
                $increase    = '';
                $viewIncrease = '';

                $title = "<a href='#' tabindex='0' data-toggle='popover' class='detail_popover'>" . $value->name . "</a>";

                if ($value->documents) {
                    $documents = "<a href='" . base_url() . "admin/income/download/" . $value->documents . "' class='btn btn-default btn-xs' title='Télécharger'><i class='fa fa-download'></i></a>";
                }

                if ($this->rbac->hasPrivilege('caisse', 'can_edit')) {
                    $editbtn = "<a href='" . base_url() . "admin/income/edit/" . $value->id . "' class='btn btn-default btn-xs' title='Modifier'><i class='fa fa-pencil'></i></a>";
                }

                if ($this->rbac->hasPrivilege('caisse', 'can_delete')) {
                    $deletebtn = "<a onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer ?\")' href='" . base_url() . "admin/income/delete/" . $value->id . "' class='btn btn-default btn-xs' title='Supprimer'><i class='fa fa-trash'></i></a>";
                }

                $viewIncrease = '<a data-toggle="modal" data-target="#viewIncreaseList" data-row-id="' . $value->id . '" class="btn btn-sm btn-icon viewIncrease" title="Voir approvisionnements"><i class="fa fa-eye"></i></a>';
                if ($this->rbac->hasPrivilege('superadmin')) :

                    $increase = '<a data-toggle="modal" data-target="#increaseForm" data-row-id="' . $value->id . '" class="btn btn-sm btn-icon increaseAmount" title="Réapprovisionner"><i class="fa fa-plus"></i></a>';

                    $toggle_status_btn = '<button type="button" class="btn btn-xs toggle-status" data-id="' . $value->id . '" data-status="' . $value->est_actif . '" style="background-color:' . ($value->est_actif == "1" ? '#4CAF50' : '#e91e63') . '; color:white;" title="Activer/Désactiver">
                <i class="fa ' . ($value->est_actif == "1" ? 'fa-unlock' : 'fa-lock') . '"></i>
            </button>';
                endif;

                $message = ($value->amount_re <= 50000)
                    ? "<span style='color:red; font-weight:bold;'> (Solde critique)</span>"
                    : "";

                $row = [];

                $row[] = "<div style='margin-top:7px;'><a href='#' tabindex='0' data-toggle='popover' class='detail_popover'>" . $value->name . "</a></div>";

                $row[] = "<div style='margin-top:7px;'>" . date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value->date)) . "</div>";
                $row[] = "<div style='margin-top:7px;'>" . $value->montant . $currency_symbol . "</div>";
                $row[] = "<div style='margin-top:7px;'>" . number_format($value->amount_re, 0, ',', ' ') . ' ' . $currency_symbol . $message . "</div>";

                if ($value->est_actif == "1") {
                    $row[] = "<h6><span class='label label-warning' style='background-color: #ff9801; border-radius: 2px;'>Ouverte</span></h6>";
                } else {
                    $row[] = "<h6><span class='label label-danger' style='background-color: #e91e63; border-radius: 2px;'>Fermée</span></h6>";
                }

                $row[] = $documents . ' ' . $editbtn . ' ' . $increase . ' ' . $viewIncrease . ' ' . $toggle_status_btn . ' ' . $deletebtn;

                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw" => intval($m->draw),
            "recordsTotal" => intval($m->recordsTotal),
            "recordsFiltered" => intval($m->recordsFiltered),
            "data" => $dt_data,
        );

        echo json_encode($json_data);
    }

    public function checkvalidation()
    {
        $search    = $this->input->post('search');
        $date_from = "";
        $date_to   = "";
        if ($search == "search_filter") {
            $this->form_validation->set_rules('search_type', $this->lang->line('search') . " " . $this->lang->line('type'), 'trim|required|xss_clean');
            if ($this->form_validation->run() == false) {
                $msg        = array('search_type' => form_error('search_type'));
                $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');

            } else {
                $search_type = $this->input->post('search_type');
                $date_from   = $this->input->post('date_from');
                $date_to     = $this->input->post('date_to');

                if (isset($date_from) && $date_from != "" && isset($date_to) && $date_to != "") {
                    $date_from = strtotime($date_from);
                    $date_to   = strtotime($date_to);
                }

                $json_array = array('status' => 'success', 'error' => '', 'search_type' => $search_type, 'message' => $this->lang->line('success_message'), 'date_from' => $date_from, 'date_to' => $date_to);
            }
        } else {

            $this->form_validation->set_rules('search_text', $this->lang->line('search_text'), 'trim|required|xss_clean');
            if ($this->form_validation->run() == false) {
                $msg        = array('search_text' => form_error('search_text'));
                $json_array = array('status' => 'fail', 'error' => $msg, 'message' => '');

            } else {
                $search_type = $this->input->post('search_text');

                $json_array = array('status' => 'success', 'error' => '', 'search_type' => $search_type, 'message' => $this->lang->line('success_message'));
            }
        }
        echo json_encode($json_array);
    }

    public function getincomesearchlist($str)
    {
        $res         = explode("-", $str);
        $search_type = $res[0];
        $search      = $res[1];
        if (count($res) == 4) {
            $date_from = $res[2];
            $date_to   = $res[3];
            $date_from = date('Y-m-d', $date_from);
            $date_to   = date('Y-m-d', $date_to);
        }

        if ($search == "search_filter") {

            if (isset($search_type) && $search_type != '') {

                if ($search_type == 'all') {
                    $dates = $this->customlib->get_betweendate('this_year');
                }
                if ($search_type == 'period') {
                    $dates['from_date'] = $date_from;
                    $dates['to_date']   = $date_to;
                } else {

                    $dates = $this->customlib->get_betweendate($search_type);

                }

                $data['search_type'] = $search_type;
            } else {

                $dates               = $this->customlib->get_betweendate('this_year');
                $data['search_type'] = '';
            }

            $dateformat = $this->customlib->getSchoolDateFormat();
            $this->customlib->dateFormatToYYYYMMDD($dates['from_date']);
            $date_from         = date('Y-m-d', strtotime($dates['from_date']));
            $date_to           = date('Y-m-d', strtotime($dates['to_date']));
            $search            = $this->input->post('search');
            $data['inc_title'] = 'Income Result From ' . date($dateformat, strtotime($date_from)) . " To " . date($dateformat, strtotime($date_to));

            $date_from  = date('Y-m-d', $this->customlib->dateYYYYMMDDtoStrtotime($date_from));
            $date_to    = date('Y-m-d', $this->customlib->dateYYYYMMDDtoStrtotime($date_to));
            $resultList = $this->income_model->search("", $date_from, $date_to);
            $resultList = $resultList;
        } else {

            $search_text = $search_type;
            $resultList  = $this->income_model->search($search_text, "", "");
            $resultList  = $resultList;
        }
        $m               = json_decode($resultList);
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $dt_data         = array();
        $total_amount    = 0;
        if (!empty($m->data)) {
            foreach ($m->data as $key => $value) {
                $total_amount += $value->amount;
                $row       = array();
                $row[]     = $value->name;
                $row[]     = $value->user;
                $row[]     = $value->amount_re;
                $row[]     = $value->invoice_no;
                $row[]     = $value->income_category;
                $row[]     = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value->date));
                $row[]     = $currency_symbol . $value->amount;
                $dt_data[] = $row;
            }
            $footer_row   = array();
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "";
            $footer_row[] = "<b>" . $this->lang->line('grand_total') . " : " . $currency_symbol . $total_amount . "</b>";
            $dt_data[]    = $footer_row;
        }

        $json_data = array(
            "draw"            => intval($m->draw),
            "recordsTotal"    => intval($m->recordsTotal),
            "recordsFiltered" => intval($m->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);

    }

    public function formIncrease()
    {
        $data['rowID'] = (!empty($this->input->post('rowID')) && (int)$this->input->post('rowID') > 0) ? (int)$this->input->post('rowID') : 0;
        $this->load->view('admin/income/increase_form', $data);
    }

    public function delete_increase($id)
    {
        $appro = $this->Income_processing_model->get_appro_by_id($id);

        if ($appro) {
            $montant = $appro['amount'];
            $income_id = $appro['income_id'];
            $this->Income_processing_model->deduire_montant_income($income_id, $montant);
            $this->Income_processing_model->delete_increase($id);
        }

        redirect('admin/income/index');
    }

    public function EditIncrease()
    {
        $data['rowID'] = (!empty($this->input->post('rowID')) && (int)$this->input->post('rowID') > 0) ? (int)$this->input->post('rowID') : 0;
        $this->load->view('admin/income/increase_form', $data);
    }

    public function deletd()
    {
        if (!$this->rbac->hasPrivilege('caisse', 'can_add')) {
            access_denied();
        }

        $rowId = $this->input->post('rowId');
        $amount = $this->input->post('amount');
        $reason = $this->input->post('reason');

        if (empty($rowId) || empty($amount) || empty($reason)) {
            $response = [
                'type'    => 'danger',
                'message' => 'Tous les champs marqués de ce symbole <code>*</code> sont obligatoires.',
            ];
            echo json_encode($response);
            return;
        }

        $oldRow = $this->db->select('*')
            ->from('income')
            ->where(['id' => $rowId])
            ->get()
            ->row();

        if (!$oldRow) {
            $response = [
                'type'    => 'danger',
                'message' => 'La ligne spécifiée est introuvable.',
            ];
            echo json_encode($response);
            return;
        }

        $newAmountRe = (float)$oldRow->amount_re + (float)$amount;
        $rowUpdated = $this->income_model->updateP(['id' => $rowId], [
            'amount_re' => $newAmountRe
        ]);

        if ($rowUpdated) {
            $this->Income_processing_model->createP([
                'income_id' => $rowId,
                'amount'    => $amount,
                'reason'    => $reason,
                'raison'    => $this->input->post('raison'),
                'user'      => $this->input->post('user'),
                'date'      => $this->input->post('date'),
            ]);

            $response = [
                'type'    => 'success',
                'message' => 'Le réapprovisionnement a été effectué avec succès.',
            ];
        } else {
            $response = [
                'type'    => 'warning',
                'message' => 'Impossible de mettre à jour la ligne, une erreur est survenue.',
            ];
        }

        echo json_encode($response);
    }


    public function setIncrease()
    {
        if (!$this->rbac->hasPrivilege('caisse', 'can_add')) {
            access_denied();
        }

        $rowId  = $this->input->post('rowId') ? trim($this->input->post('rowId')) : 0;
        $amount = $this->input->post('amount') ? floatval($this->input->post('amount')) : 0;
        $reason = $this->input->post('reason') ? trim($this->input->post('reason')) : '';
        $raison = $this->input->post('raison') ? trim($this->input->post('raison')) : '';
        $user   = $this->input->post('user') ? trim($this->input->post('user')) : '';
        $date   = $this->input->post('date') ? trim($this->input->post('date')) : '';

        if (empty($rowId) || empty($amount) || empty($reason)) {
            $response = [
                'type'    => 'danger',
                'message' => 'Tous les champs marqués de ce symbole <code>*</code> sont obligatoires.',
            ];
            echo json_encode($response);
            return;
        }

        $oldRow = $this->db->select('*')
            ->from('income')
            ->where(['id' => $rowId])
            ->get()
            ->row();

        if (!$oldRow) {
            $response = [
                'type'    => 'danger',
                'message' => 'La ligne spécifiée est introuvable.',
            ];
            echo json_encode($response);
            return;
        }

        $newAmountRe = (float)$oldRow->amount_re + (float)$amount;
        $rowUpdated = $this->income_model->updateP(['id' => $rowId], [
            'amount_re' => $newAmountRe,
            'montant' => $newAmountRe
        ]);

        if ($rowUpdated) {
            $this->Income_processing_model->createP([
                'income_id' => $rowId,
                'amount'    => $amount,
                'reason'    => $reason,
                'raison'    => $raison,
                'user'      => $user,
                'date'      => $date,
            ]);

            // Enregistrer dans mouvements
            $mouvement_data = [
                'type_mouvement' => 'entree',
                'montant'        => $amount,
                'description'    => "Réapprovisionnement caisse (Income ID: $rowId) - $reason",
                'reference'      => 'INCP-' . uniqid(),
                'id_employe'     => null,
                'date_mouvement' => $date,
                'mode_paiement'  => 'virement',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ];
            $this->db->insert('mouvements', $mouvement_data);

            // Enregistrer les écritures comptables
            $desc    = "Réapprovisionnement caisse (Income ID: $rowId)";
            $date_op = !empty($date) ? $date : date('Y-m-d');

            // Débit Caisse (571)
            $this->db->insert('accounting_entries', [
                'date'        => $date_op,
                'invoice_id'  => $rowId,
                'account'     => '571 - Caisse',
                'debit'       => $amount,
                'credit'      => 0,
                'description' => $desc,
                'created_at'  => date('Y-m-d H:i:s')
            ]);

            // Crédit Banque (512)
            $this->db->insert('accounting_entries', [
                'date'        => $date_op,
                'invoice_id'  => $rowId,
                'account'     => '512 - Banque',
                'debit'       => 0,
                'credit'      => $amount,
                'description' => $desc,
                'created_at'  => date('Y-m-d H:i:s')
            ]);

            $response = [
                'type'    => 'success',
                'message' => 'Le réapprovisionnement a été effectué avec succès et enregistré en comptabilité.',
            ];
        } else {
            $response = [
                'type'    => 'warning',
                'message' => 'Impossible de mettre à jour la ligne, une erreur est survenue.',
            ];
        }

        echo json_encode($response);
    }



    public function listIncrease()
    {
        if (!$this->rbac->hasPrivilege('caisse', 'can_edit')) {
            access_denied();
        }

        $rowID = (!empty($this->input->post('rowID')) && (int)$this->input->post('rowID') > 0) ? (int)$this->input->post('rowID') : 0;

        $join  = [
            'table'     => 'income',
            'condition' => 'income.id = income_processing.income_id',
            'type'      => 'inner'
        ];

        $select = 'income.name, income.user, income_processing.id, income_processing.amount, income_processing.reason, income_processing.date, income_processing.raison';

        $where  = [
            'income_id' => $rowID,
        ];

        $data['rows'] = $this->db->select($select)
            ->from('income_processing')
            ->join($join['table'], $join['condition'], $join['type'])
            ->where($where)
            ->get()
            ->result();
        $this->db->flush_cache();

        $this->load->view('admin/income/increaseList', $data);
    }

    //caisse fermerture

    // Dans votre contrôleur (admin/Income.php par exemple)
    public function toggle_caisse_status()
    {
        $id = $this->input->post('id');
        $new_status = $this->input->post('status');

        // Récupérer les infos de la caisse
        $this->db->select('*');
        $this->db->where('id', $id);
        $caisse = $this->db->get('income')->row();

        if ($caisse) {
            // Si on passe de ACTIF (1) à INACTIF (0) -> FERMETURE
            if ($caisse->est_actif == '1' && $new_status == '0') {
                // 1. Enregistrer la fermeture dans cycles_caisse
                $cycle_data = array(
                    'caisse_id' => $id,
                    'type' => 'fermeture',
                    'montant_initial' => $caisse->amount,
                    'solde_final' => $caisse->amount_re,
                    'date_debut' => $caisse->last_operation_date, // ou date d'ouverture
                    'date_fin' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'type_cycle' => 'fermeture'
                );
                $this->db->insert('cycles_caisse', $cycle_data);

                // 2. Transférer amount_re vers amount pour la prochaine ouverture
                $update_data = array(
                    'est_actif' => '0',
                    'amount' => $caisse->amount_re, // Le nouveau montant initial = ancien solde réel
                    'amount_re' => 0, // Réinitialiser le solde réel
                    'total_entrees' => 0,
                    'total_sorties' => 0,
                    'last_operation_date' => date('Y-m-d H:i:s')
                );

                $this->db->where('id', $id);
                $this->db->update('income', $update_data);

                echo json_encode(array('success' => true, 'message' => 'Caisse fermée. Montant reporté: ' . number_format($caisse->amount_re, 2, ',', ' ') . ' FCFA'));
            }
            // Si on passe de INACTIF (0) à ACTIF (1) -> OUVERTURE
            elseif ($caisse->est_actif == '0' && $new_status == '1') {
                // 1. Enregistrer l'ouverture dans cycles_caisse
                $cycle_data = array(
                    'caisse_id' => $id,
                    'type' => 'ouverture',
                    'montant_initial' => $caisse->amount, // Le amount récupéré lors de la fermeture
                    'solde_final' => 0, // À remplir à la prochaine fermeture
                    'date_debut' => date('Y-m-d H:i:s'),
                    'date_fin' => NULL,
                    'created_at' => date('Y-m-d H:i:s'),
                    'type_cycle' => 'ouverture'
                );
                $this->db->insert('cycles_caisse', $cycle_data);

                // 2. Activer la caisse avec le montant initial préservé
                $update_data = array(
                    'est_actif' => '1',
                    'amount_re' => $caisse->amount, // Le solde réel = montant initial
                    'last_operation_date' => date('Y-m-d H:i:s')
                );

                $this->db->where('id', $id);
                $this->db->update('income', $update_data);

                echo json_encode(array('success' => true, 'message' => 'Caisse ouverte avec montant initial: ' . number_format($caisse->amount, 2, ',', ' ') . ' FCFA'));
            }
            else {
                echo json_encode(array('success' => false, 'message' => 'État invalide'));
            }
        } else {
            echo json_encode(array('success' => false, 'message' => 'Caisse non trouvée'));
        }
    }
    public function refresh_totals()
    {
        // Même logique que ci-dessus
        // 1. Totaux de toutes les caisses
        $this->db->select('
        SUM(amount) as total_amount_all,
        SUM(total_entrees) as total_entrees_all,
        SUM(total_sorties) as total_sorties_all,
        COUNT(*) as nb_caisses_total'
        );
        $this->db->from('income');
        $this->db->where('is_deleted', 'no');
        $totaux_toutes_caisses = $this->db->get()->row();

        // 2. Totaux des caisses actives
        $this->db->select('
        SUM(amount_re) as total_amount_re_actives,
        COUNT(*) as nb_caisses_actives'
        );
        $this->db->from('income');
        $this->db->where('is_deleted', 'no');
        $this->db->where('est_actif', '1');
        $totaux_caisses_actives = $this->db->get()->row();

        $response = array(
            'success' => true,
            'total_amount' => floatval($totaux_toutes_caisses->total_amount_all ?? 0),
            'total_amount_re' => floatval($totaux_caisses_actives->total_amount_re_actives ?? 0),
            'total_entrees' => floatval($totaux_toutes_caisses->total_entrees_all ?? 0),
            'total_sorties' => floatval($totaux_toutes_caisses->total_sorties_all ?? 0),
            'nb_caisses_total' => intval($totaux_toutes_caisses->nb_caisses_total ?? 0),
            'nb_caisses_actives' => intval($totaux_caisses_actives->nb_caisses_actives ?? 0),
            'last_update' => date('d/m/Y H:i')
        );

        echo json_encode($response);
    }
    public function get_global_totals()
    {
        // Totaux de TOUTES les caisses (actives et inactives)
        $this->db->select('
        SUM(amount) as total_amount,
        SUM(total_entrees) as total_entrees,
        SUM(total_sorties) as total_sorties,
        COUNT(*) as nb_caisses'
        );
        $this->db->from('income');
        $this->db->where('is_deleted', 'no');
        $all_totals = $this->db->get()->row();

        // Totaux des caisses ACTIVES seulement (pour amount_re)
        $this->db->select('SUM(amount_re) as total_amount_re, COUNT(*) as nb_caisses_actives');
        $this->db->from('income');
        $this->db->where('is_deleted', 'no');
        $this->db->where('est_actif', '1');
        $active_totals = $this->db->get()->row();

        $response = array(
            'success' => true,
            'total_amount' => floatval($all_totals->total_amount ?? 0),
            'total_amount_re' => floatval($active_totals->total_amount_re ?? 0),
            'total_entrees' => floatval($all_totals->total_entrees ?? 0),
            'total_sorties' => floatval($all_totals->total_sorties ?? 0),
            'nb_caisses' => intval($all_totals->nb_caisses ?? 0),
            'nb_caisses_actives' => intval($active_totals->nb_caisses_actives ?? 0),
            'last_update' => date('d/m/Y H:i')
        );

        header('Content-Type: application/json');
        echo json_encode($response);
    }
    public function get_totals()
    {
        // Même logique que dans votre vue pour les totaux
        $this->db->select('
        SUM(amount) as total_amount,
        SUM(amount_re) as total_amount_re,
        SUM(total_entrees) as total_entrees_all,
        SUM(total_sorties) as total_sorties_all,
        COUNT(*) as nb_caisses'
        );
        $this->db->from('income');
        $this->db->where('is_deleted', 'no');
        $this->db->where('est_actif', '1');
        $totaux_generaux = $this->db->get()->row();

        // Chargez la vue partielle avec les totaux
        $data['totaux_generaux'] = $totaux_generaux;
        $this->load->view('admin/income/totals_partial', $data);
    }


    /**
     * Éditer une opération - Formulaire
     */
    public function edit_operation($operation_id)
    {
        // Vérifier les permissions
        if (!$this->rbac->hasPrivilege('caisse', 'can_edit')) {
            access_denied();
        }

        // Récupérer l'opération
        $this->db->select('oc.*, c.name as caisse_nom, eh.exp_category as category_name');
        $this->db->from('operation_caisse oc');
        $this->db->join('income c', 'c.id = oc.caisse_id', 'left');
        $this->db->join('expense_head eh', 'eh.id = oc.exp_head_id', 'left');
        $this->db->where('oc.id', $operation_id);
        $this->db->where('oc.deleted', 'no');
        $operation = $this->db->get()->row_array();

        if (!$operation) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Opération non trouvée</div>');
            redirect('admin/income');
        }

        // Charger les données pour la vue
        $data = array();
        $data['title'] = 'Éditer Opération';
        $data['operation'] = $operation;

        // Récupérer les caisses actives
        $data['caisses'] = $this->db->select('*')
            ->from('income')
            ->where('is_deleted', 'no')
            ->where('est_actif', '1')
            ->order_by('name', 'ASC')
            ->get()
            ->result_array();

        // Récupérer les catégories de dépenses
        $this->load->model('expensehead_model');
        $data['expheadlist'] = $this->expensehead_model->get();

        // Charger la vue d'édition
        $this->load->view('layout/header', $data);
        $this->load->view('admin/income/edit_operation', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * Éditer une opération - Formulaire pour modal AJAX
     */
    public function edit_operation_form($operation_id)
    {
        // Vérifier les permissions
        if (!$this->rbac->hasPrivilege('caisse', 'can_edit')) {
            echo '<div class="alert alert-danger">Permission non accordée</div>';
            return;
        }

        // Récupérer l'opération
        $this->db->select('oc.*, c.name as caisse_nom, eh.exp_category as category_name');
        $this->db->from('operation_caisse oc');
        $this->db->join('income c', 'c.id = oc.caisse_id', 'left');
        $this->db->join('expense_head eh', 'eh.id = oc.exp_head_id', 'left');
        $this->db->where('oc.id', $operation_id);
        $this->db->where('oc.deleted', 'no');
        $operation = $this->db->get()->row_array();

        if (!$operation) {
            echo '<div class="alert alert-danger">Opération non trouvée</div>';
            return;
        }

        // Charger les données pour la vue
        $data = array();
        $data['operation'] = $operation;

        // Récupérer les caisses actives
        $data['caisses'] = $this->db->select('*')
            ->from('income')
            ->where('is_deleted', 'no')
            ->where('est_actif', '1')
            ->order_by('name', 'ASC')
            ->get()
            ->result_array();

        // Récupérer les catégories de dépenses
        $this->load->model('expensehead_model');
        $data['expheadlist'] = $this->expensehead_model->get();

        // Charger la vue partielle pour modal
        $this->load->view('admin/income/edit_operation_form', $data);
    }

    /**
     * Mettre à jour une opération - Traitement
     */
    /**
     * Mettre à jour une opération - Traitement
     */
    public function update_operation($operation_id)
    {
        // Vérifier les permissions
        if (!$this->rbac->hasPrivilege('caisse', 'can_edit')) {
            access_denied();
        }

        // Valider les données
        $this->form_validation->set_rules('caisse_id', 'Caisse', 'required|numeric');
        $this->form_validation->set_rules('date', 'Date', 'required');
        $this->form_validation->set_rules('type', 'Type d\'opération', 'required|in_list[entree,sortie]');
        $this->form_validation->set_rules('designation', 'Désignation', 'required');
        $this->form_validation->set_rules('montant', 'Montant', 'required|numeric|greater_than[0]');
        $this->form_validation->set_rules('exp_head_id', 'Catégorie', 'required|numeric');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('admin/income/edit_operation/' . $operation_id);
        }

        // Récupérer l'opération existante
        $this->db->select('*');
        $this->db->from('operation_caisse');
        $this->db->where('id', $operation_id);
        $this->db->where('deleted', 'no');
        $old_operation = $this->db->get()->row_array();

        if (!$old_operation) {
            $this->session->set_flashdata('error', 'Opération non trouvée');
            redirect('admin/income');
        }

        // Récupérer les données du formulaire
        $caisse_id = $this->input->post('caisse_id');
        $date = $this->input->post('date');
        $type = $this->input->post('type');
        $designation = $this->input->post('designation');
        $montant = floatval($this->input->post('montant'));
        $exp_head_id = $this->input->post('exp_head_id');
        $exp_category_name = $this->input->post('exp_category_name');
        $reference = $this->input->post('reference');
        $mode_paiement = $this->input->post('mode_paiement');

        // Préparer les nouvelles données
        $operation_data = array(
            'caisse_id' => $caisse_id,
            'date' => $date,
            'designation' => $designation,
            'exp_head_id' => $exp_head_id,
            'exp_category_name' => $exp_category_name,
            'reference' => $reference,
            'mode_paiement' => $mode_paiement,
            'updated_at' => date('Y-m-d H:i:s')
        );

        // Déterminer le type d'opération
        if ($type == 'entree') {
            $operation_data['entree'] = $montant;
            $operation_data['sortie'] = 0;
            $operation_data['type_operation'] = 'entrée';
        } else {
            $operation_data['entree'] = 0;
            $operation_data['sortie'] = $montant;
            $operation_data['type_operation'] = 'sortie';
        }

        try {
            // 1. Annuler l'effet de l'ancienne opération
            $this->annuler_effet_operation($old_operation);

            // 2. Calculer les nouveaux soldes
            $this->calculer_soldes_operation($caisse_id, $operation_data);

            // 3. Mettre à jour l'opération
            $this->db->where('id', $operation_id);
            $this->db->update('operation_caisse', $operation_data);


            // 5. Recalculer tous les soldes pour cette caisse
            $this->recalculer_soldes_caisse($caisse_id);

            $this->session->set_flashdata('msg', '<div class="alert alert-success">Opération modifiée avec succès</div>');
            redirect('admin/income');

        } catch (Exception $e) {
            $this->session->set_flashdata('error', $e->getMessage());
            redirect('admin/income/edit_operation/' . $operation_id);
        }
    }


    /**
     * Supprimer une opération (soft delete)
     */
    /**
     * Supprimer une opération (soft delete)
     */

    public function delete_operation($operation_id)
    {
        // Vérifier les permissions
        if (!$this->rbac->hasPrivilege('caisse', 'can_delete')) {
            access_denied();
        }

        // Récupérer l'opération via le modèle
        $this->db->select('*');
        $this->db->from('operation_caisse');
        $this->db->where('id', $operation_id);
        $this->db->where('deleted', 'no');
        $operation = $this->db->get()->row_array();

        if (!$operation) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Opération non trouvée</div>');
            redirect('admin/income/index');
        }

        // Annuler l'effet sur la caisse
        $this->annuler_effet_operation($operation);

        // Appeler la méthode removed() du modèle qui gère la transaction et le log
        $result = $this->income_model->removed($operation_id);

        if ($result) {
            // Recalculer les soldes pour cette caisse
            $this->recalculer_soldes_caisse($operation['caisse_id']);

            $this->session->set_flashdata('msg', '<div class="alert alert-success">Opération supprimée avec succès</div>');
        } else {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Erreur lors de la suppression</div>');
        }

        // Redirection vers la page income pour actualiser
        redirect('admin/income/index');
    }

    public function delete_operation_290($operation_id)
    {
        // Vérifier les permissions
        if (!$this->rbac->hasPrivilege('caisse', 'can_delete')) {
            access_denied();
        }

        // Récupérer l'opération via le modèle
        $this->db->select('*');
        $this->db->from('operation_caisse');
        $this->db->where('id', $operation_id);
        $this->db->where('deleted', 'no');
        $operation = $this->db->get()->row_array();

        if (!$operation) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Opération non trouvée</div>');
            redirect('admin/income/index');
        }

        // =============================================
        // VÉRIFICATION DE LA DATE - SEULEMENT AUJOURD'HUI
        // =============================================
        $date_operation = !empty($operation['date']) ? date('Y-m-d', strtotime($operation['date'])) : '';
        $date_actuelle = date('Y-m-d');

        // Vérifier si l'opération est d'aujourd'hui
        if ($date_operation != $date_actuelle) {
            $date_formatee = !empty($operation['date']) ? date('d/m/Y', strtotime($operation['date'])) : 'Date inconnue';

            $this->session->set_flashdata('msg',
                '<div class="alert alert-danger">
                <i class="fa fa-calendar-times"></i> 
                <strong>Suppression non autorisée !</strong><br>
                Cette opération date du <strong>' . $date_formatee . '</strong>.<br>
                La suppression n\'est autorisée que pour les opérations du jour (' . date('d/m/Y') . ').
            </div>'
            );
            redirect('admin/income/index');
        }

        // Optionnel : Vérifier aussi l'heure limite
        $heure_operation = !empty($operation['date']) ? date('H:i', strtotime($operation['date'])) : '';
        $heure_actuelle = date('H:i');

        // Vous pouvez ajouter une limite d'heure si nécessaire
        // $heure_limite = '23:59'; // Minuit
        // if ($heure_operation > $heure_limite) {
        //     $this->session->set_flashdata('msg',
        //         '<div class="alert alert-danger">
        //             <i class="fa fa-clock-o"></i>
        //             <strong>Heure limite dépassée !</strong><br>
        //             L\'opération a été effectuée à ' . $heure_operation . '.<br>
        //             La suppression n\'est autorisée que jusqu\'à ' . $heure_limite . '.
        //         </div>'
        //     );
        //     redirect('admin/income/index');
        // }
        // =============================================

        // =============================================
        // VÉRIFICATION SUPPLÉMENTAIRE : OPÉRATION VERROUILLÉE
        // =============================================
        // Vérifier si l'opération est marquée comme verrouillée
        if (isset($operation['est_verrouillee']) && $operation['est_verrouillee'] == 'oui') {
            $this->session->set_flashdata('msg',
                '<div class="alert alert-warning">
                <i class="fa fa-lock"></i> 
                <strong>Opération verrouillée !</strong><br>
                Cette opération a été verrouillée et ne peut pas être supprimée.
            </div>'
            );
            redirect('admin/income/index');
        }

        // Vérifier si la caisse associée est active
        $this->db->select('est_actif');
        $this->db->from('income');
        $this->db->where('id', $operation['caisse_id']);
        $caisse = $this->db->get()->row();

        if ($caisse && $caisse->est_actif == '0') {
            $this->session->set_flashdata('msg',
                '<div class="alert alert-warning">
                <i class="fa fa-ban"></i> 
                <strong>Caisse inactive !</strong><br>
                La caisse associée à cette opération est inactive. Réactivez-la d\'abord.
            </div>'
            );
            redirect('admin/income/index');
        }
        // =============================================

        // Confirmation supplémentaire (optionnelle)
        // Vous pouvez ajouter une confirmation par token si nécessaire
        $token = $this->input->post('token');
        if (!$this->input->is_ajax_request()) {
            // Pour les requêtes normales, vérifier le token CSRF
            if (!$this->security->csrf_verify()) {
                $this->session->set_flashdata('msg',
                    '<div class="alert alert-danger">
                    <i class="fa fa-shield"></i> 
                    <strong>Token de sécurité invalide !</strong><br>
                    Veuillez rafraîchir la page et réessayer.
                </div>'
                );
                redirect('admin/income/index');
            }
        }

        try {
            // Annuler l'effet sur la caisse
            $this->annuler_effet_operation($operation);

            // Appeler la méthode removed() du modèle qui gère la transaction et le log
            $result = $this->income_model->removed($operation_id);

            if ($result) {
                // Recalculer les soldes pour cette caisse
                $this->recalculer_soldes_caisse($operation['caisse_id']);

                // Enregistrer l'audit de suppression
                $this->enregistrer_audit_suppression($operation);

                // Message de succès avec détails
                $montant = max($operation['entree'], $operation['sortie']);
                $type = $operation['entree'] > 0 ? 'entrée' : 'sortie';

                $this->session->set_flashdata('msg',
                    '<div class="alert alert-success">
                    <i class="fa fa-check-circle"></i> 
                    <strong>Opération supprimée avec succès !</strong><br>
                    <small>
                        Référence: ' . $operation['reference'] . '<br>
                        Type: ' . $type . ' de ' . number_format($montant, 0, ',', ' ') . ' FCFA<br>
                        Date: ' . date('d/m/Y H:i', strtotime($operation['date'])) . '<br>
                        Supprimé par: ' . $this->customlib->getAdminSessionUserName() . '
                    </small>
                </div>'
                );

            } else {
                $this->session->set_flashdata('msg',
                    '<div class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle"></i> 
                    <strong>Erreur lors de la suppression</strong><br>
                    Le modèle a retourné une erreur. Veuillez contacter l\'administrateur.
                </div>'
                );
            }

        } catch (Exception $e) {
            // Gestion des erreurs
            log_message('error', 'Erreur suppression opération ' . $operation_id . ': ' . $e->getMessage());

            $this->session->set_flashdata('msg',
                '<div class="alert alert-danger">
                <i class="fa fa-exclamation-circle"></i> 
                <strong>Erreur système !</strong><br>
                Une erreur est survenue lors de la suppression.<br>
                <small>Erreur: ' . $e->getMessage() . '</small>
            </div>'
            );
        }

        // Redirection vers la page income pour actualiser
        redirect('admin/income/index');
    }

// =============================================
// MÉTHODES AUXILIAIRES
// =============================================

    /**
     * Enregistrer un audit de suppression
     */
    private function enregistrer_audit_suppression($operation)
    {
        $audit_data = array(
            'type_action' => 'suppression_operation',
            'id_operation' => $operation['id'],
            'reference' => $operation['reference'],
            'caisse_id' => $operation['caisse_id'],
            'date_operation' => $operation['date'],
            'montant' => max($operation['entree'], $operation['sortie']),
            'type_operation' => $operation['entree'] > 0 ? 'entrée' : 'sortie',
            'utilisateur' => $this->customlib->getAdminSessionUserName(),
            'date_suppression' => date('Y-m-d H:i:s'),
            'ip_address' => $this->input->ip_address(),
            'user_agent' => $this->input->user_agent()
        );

        // Si vous avez une table d'audit, enregistrez-y
        // $this->db->insert('audit_operations', $audit_data);

        // Sinon, log dans un fichier
        $log_message = sprintf(
            "[%s] SUPPRESSION - Opération %s (%s) - Caisse: %s - Montant: %s - Par: %s - IP: %s",
            date('Y-m-d H:i:s'),
            $operation['id'],
            $operation['reference'],
            $operation['caisse_id'],
            number_format(max($operation['entree'], $operation['sortie']), 0, ',', ' '),
            $this->customlib->getAdminSessionUserName(),
            $this->input->ip_address()
        );

        log_message('info', $log_message);
    }

    /**
     * Version AJAX pour la suppression (si nécessaire)
     */
    public function delete_operation_ajax($operation_id)
    {
        header('Content-Type: application/json');

        // Vérifier les permissions
        if (!$this->rbac->hasPrivilege('caisse', 'can_delete')) {
            echo json_encode([
                'success' => false,
                'message' => 'Permission non accordée'
            ]);
            return;
        }

        // Récupérer l'opération
        $this->db->select('*');
        $this->db->from('operation_caisse');
        $this->db->where('id', $operation_id);
        $this->db->where('deleted', 'no');
        $operation = $this->db->get()->row_array();

        if (!$operation) {
            echo json_encode([
                'success' => false,
                'message' => 'Opération non trouvée'
            ]);
            return;
        }

        // VÉRIFICATION DE LA DATE - SEULEMENT AUJOURD'HUI
        $date_operation = !empty($operation['date']) ? date('Y-m-d', strtotime($operation['date'])) : '';
        $date_actuelle = date('Y-m-d');

        if ($date_operation != $date_actuelle) {
            $date_formatee = !empty($operation['date']) ? date('d/m/Y', strtotime($operation['date'])) : 'Date inconnue';

            echo json_encode([
                'success' => false,
                'message' => 'La suppression n\'est autorisée que pour les opérations du jour. Cette opération date du ' . $date_formatee
            ]);
            return;
        }

        try {
            // Annuler l'effet sur la caisse
            $this->annuler_effet_operation($operation);

            // Marquer l'opération comme supprimée
            $this->db->where('id', $operation_id);
            $update_result = $this->db->update('operation_caisse', array(
                'deleted' => 'yes',
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => $this->customlib->getAdminSessionUserName()
            ));

            if ($update_result) {
                // Marquer le mouvement comme supprimé (si existe)
                $this->db->where('operation_id', $operation_id);
                $this->db->update('mouvements', array(
                    'deleted' => 'yes',
                    'deleted_at' => date('Y-m-d H:i:s')
                ));

                // Recalculer les soldes pour cette caisse
                $this->recalculer_soldes_caisse($operation['caisse_id']);

                // Enregistrer l'audit
                $this->enregistrer_audit_suppression($operation);

                echo json_encode([
                    'success' => true,
                    'message' => 'Opération supprimée avec succès',
                    'operation_id' => $operation_id
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Échec de la mise à jour en base de données'
                ]);
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }
    }


    /**
     * Supprimer une opération via AJAX
     */
    public function delete_operation_ajax_070226($operation_id)
    {
        // Vérifier les permissions
        if (!$this->rbac->hasPrivilege('caisse', 'can_delete')) {
            echo json_encode(array('success' => false, 'message' => 'Permission non accordée'));
            return;
        }

        try {
            // Récupérer l'opération
            $this->db->select('*');
            $this->db->from('operation_caisse');
            $this->db->where('id', $operation_id);
            $this->db->where('deleted', 'no');
            $operation = $this->db->get()->row_array();

            if (!$operation) {
                echo json_encode(array('success' => false, 'message' => 'Opération non trouvée'));
                return;
            }

            // Annuler l'effet sur la caisse
            $this->annuler_effet_operation($operation);

            // Marquer l'opération comme supprimée
            $this->db->where('id', $operation_id);
            $this->db->update('operation_caisse', array(
                'deleted' => 'yes',
                'deleted_at' => date('Y-m-d H:i:s')
            ));

            // Marquer le mouvement comme supprimé (si existe)
            $this->db->where('operation_id', $operation_id);
            $this->db->update('mouvements', array(
                'deleted' => 'yes',
                'deleted_at' => date('Y-m-d H:i:s')
            ));

            // Recalculer les soldes pour cette caisse
            $this->recalculer_soldes_caisse($operation['caisse_id']);

            echo json_encode(array(
                'success' => true,
                'message' => 'Opération supprimée avec succès'
            ));

        } catch (Exception $e) {
            echo json_encode(array(
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ));
        }
    }

    /**
     * Annuler l'effet d'une opération
     */
    private function annuler_effet_operation($operation)
    {
        $caisse_id = $operation['caisse_id'];
        $entree = floatval($operation['entree']);
        $sortie = floatval($operation['sortie']);

        // Récupérer les totaux actuels de la caisse
        $this->db->select('total_entrees, total_sorties, amount_re');
        $this->db->from('income');
        $this->db->where('id', $caisse_id);
        $caisse_info = $this->db->get()->row();

        if ($caisse_info) {
            $update_data = array();

            // Convertir amount_re en float (il est stocké en varchar dans votre table)
            $current_amount_re = floatval($caisse_info->amount_re);
            $current_total_entrees = floatval($caisse_info->total_entrees);
            $current_total_sorties = floatval($caisse_info->total_sorties);

            // Annuler l'entrée
            if ($entree > 0) {
                $update_data['total_entrees'] = max(0, $current_total_entrees - $entree);
                $update_data['amount_re'] = max(0, $current_amount_re - $entree);
            }

            // Annuler la sortie
            if ($sortie > 0) {
                $update_data['total_sorties'] = max(0, $current_total_sorties - $sortie);
                $update_data['amount_re'] = $current_amount_re + $sortie;
            }

            // Mettre à jour la caisse
            if (!empty($update_data)) {
                $this->db->where('id', $caisse_id);
                $this->db->update('income', $update_data);
            }
        }
    }
    /**
     * Calculer les soldes pour une opération
     */
    private function calculer_soldes_operation($caisse_id, &$operation_data)
    {
        // Récupérer le solde actuel de la caisse
        $this->db->select('amount_re, total_entrees, total_sorties');
        $this->db->from('income');
        $this->db->where('id', $caisse_id);
        $caisse = $this->db->get()->row();

        if (!$caisse) {
            throw new Exception('Caisse non trouvée');
        }

        // Convertir les valeurs (amount_re est en varchar)
        $solde_avant = floatval($caisse->amount_re);
        $total_entrees = floatval($caisse->total_entrees);
        $total_sorties = floatval($caisse->total_sorties);

        // Calculer le solde après
        if ($operation_data['entree'] > 0) {
            $solde_apres = $solde_avant + $operation_data['entree'];

            // Mettre à jour les totaux de la caisse
            $update_data = array(
                'total_entrees' => $total_entrees + $operation_data['entree'],
                'amount_re' => $solde_apres,
                'last_operation_date' => date('Y-m-d H:i:s')
            );
        } else {
            // Vérifier si le solde est suffisant
            if ($solde_avant < $operation_data['sortie']) {
                throw new Exception('Solde insuffisant dans la caisse. Solde disponible: ' . number_format($solde_avant, 2, ',', ' ') . ' FCFA');
            }
            $solde_apres = $solde_avant - $operation_data['sortie'];

            // Mettre à jour les totaux de la caisse
            $update_data = array(
                'total_sorties' => $total_sorties + $operation_data['sortie'],
                'amount_re' => $solde_apres,
                'last_operation_date' => date('Y-m-d H:i:s')
            );
        }

        // Mettre à jour la caisse
        $this->db->where('id', $caisse_id);
        $this->db->update('income', $update_data);

        // Ajouter les soldes à l'opération
        $operation_data['solde_avant_operation'] = $solde_avant;
        $operation_data['solde_apres_operation'] = $solde_apres;
    }

    /**
     * Recalculer tous les soldes pour une caisse
     */
    private function recalculer_soldes_caisse($caisse_id)
    {
        // Récupérer toutes les opérations non supprimées de cette caisse
        $this->db->select('*');
        $this->db->from('operation_caisse');
        $this->db->where('caisse_id', $caisse_id);
        $this->db->where('deleted', 'no');
        $this->db->order_by('date', 'ASC');
        $this->db->order_by('id', 'ASC');
        $operations = $this->db->get()->result_array();

        // Récupérer le solde initial de la caisse
        $this->db->select('amount');
        $this->db->from('income');
        $this->db->where('id', $caisse_id);
        $caisse = $this->db->get()->row();
        $solde_courant = $caisse ? floatval($caisse->amount) : 0;

        // Variables pour les totaux
        $total_entrees = 0;
        $total_sorties = 0;

        // Récalculer les soldes pour chaque opération
        foreach ($operations as $operation) {
            $solde_avant = $solde_courant;

            if ($operation['entree'] > 0) {
                $solde_courant += floatval($operation['entree']);
                $total_entrees += floatval($operation['entree']);
            } else {
                $solde_courant -= floatval($operation['sortie']);
                $total_sorties += floatval($operation['sortie']);
            }

            // Mettre à jour les soldes de l'opération
            $this->db->where('id', $operation['id']);
            $this->db->update('operation_caisse', array(
                'solde_avant_operation' => $solde_avant,
                'solde_apres_operation' => $solde_courant
            ));
        }

        // Mettre à jour les totaux de la caisse
        $this->db->where('id', $caisse_id);
        $this->db->update('income', array(
            'total_entrees' => $total_entrees,
            'total_sorties' => $total_sorties,
            'amount_re' => $solde_courant,
            'last_operation_date' => date('Y-m-d H:i:s')
        ));
    }

    private function get_modes_paiement_for_filter()
    {
        $this->db->distinct();
        $this->db->select('mode_paiement');
        $this->db->from('operation_caisse');
        $this->db->where('mode_paiement IS NOT NULL');
        $this->db->where('mode_paiement !=', '');
        $this->db->where('deleted', 'no');
        $this->db->order_by('mode_paiement', 'ASC');

        $query = $this->db->get();

        $modes = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                if (!empty($row['mode_paiement'])) {
                    $modes[] = $row['mode_paiement'];
                }
            }
        }

        // Ajouter des modes par défaut s'il n'y en a pas
        if (empty($modes)) {
            $modes = array('espèces', 'chèque', 'virement', 'carte', 'Orange money', 'wave', 'mtn money');
        }

        return array_unique($modes);
    }

    /**
     * Récupérer les totaux par mode de paiement
     */
    private function get_totaux_par_mode_paiement($date_debut = null, $date_fin = null, $caisse_id = null)
    {
        // Dates par défaut
        if (empty($date_debut)) {
            $date_debut = date('Y-m-01');
        }
        if (empty($date_fin)) {
            $date_fin = date('Y-m-d');
        }

        $this->db->select("
        mode_paiement,
        SUM(entree) as total_entrees,
        SUM(sortie) as total_sorties,
        (SUM(entree) - SUM(sortie)) as solde_net,
        COUNT(*) as nombre_operations
    ");

        $this->db->from('operation_caisse');

        // Dates
        $this->db->where('DATE(date) >=', $date_debut);
        $this->db->where('DATE(date) <=', $date_fin);

        // Caisse spécifique si fournie
        if (!empty($caisse_id)) {
            $this->db->where('caisse_id', $caisse_id);
        }

        $this->db->where('deleted', 'no');
        $this->db->where('mode_paiement IS NOT NULL');
        $this->db->where('mode_paiement !=', '');

        $this->db->group_by('mode_paiement');
        $this->db->order_by('mode_paiement', 'ASC');

        $query = $this->db->get();
        $resultats = $query->result_array();

        // Organiser par mode de paiement
        $totaux_par_mode = array();
        foreach ($resultats as $row) {
            $mode = $row['mode_paiement'];
            $totaux_par_mode[$mode] = array(
                'total_entrees' => floatval($row['total_entrees']),
                'total_sorties' => floatval($row['total_sorties']),
                'solde_net' => floatval($row['solde_net']),
                'nombre_operations' => intval($row['nombre_operations'])
            );
        }

        return $totaux_par_mode;
    }

    /**
     * Calculer les totaux pour une période spécifique
     */
    private function calculer_totaux_periode($date_debut, $date_fin, $caisse_id = null)
    {
        // Entrées
        $this->db->select('SUM(entree) as total_entrees, COUNT(*) as nb_entrees');
        $this->db->from('operation_caisse');
        $this->db->where('DATE(date) >=', $date_debut);
        $this->db->where('DATE(date) <=', $date_fin);
        $this->db->where('entree >', 0);
        $this->db->where('deleted', 'no');

        if (!empty($caisse_id)) {
            $this->db->where('caisse_id', $caisse_id);
        }

        $entrees = $this->db->get()->row();

        // Sorties
        $this->db->select('SUM(sortie) as total_sorties, COUNT(*) as nb_sorties');
        $this->db->from('operation_caisse');
        $this->db->where('DATE(date) >=', $date_debut);
        $this->db->where('DATE(date) <=', $date_fin);
        $this->db->where('sortie >', 0);
        $this->db->where('deleted', 'no');

        if (!empty($caisse_id)) {
            $this->db->where('caisse_id', $caisse_id);
        }

        $sorties = $this->db->get()->row();

        return array(
            'total_entrees' => floatval($entrees->total_entrees ?? 0),
            'nb_entrees' => intval($entrees->nb_entrees ?? 0),
            'total_sorties' => floatval($sorties->total_sorties ?? 0),
            'nb_sorties' => intval($sorties->nb_sorties ?? 0),
            'solde_net' => floatval(($entrees->total_entrees ?? 0) - ($sorties->total_sorties ?? 0))
        );
    }

    /**
     * Récupérer les opérations par type (entrée/sortie) pour popup
     */
    public function get_operations_par_type()
    {
        $type = $this->input->get('type'); // 'entree' ou 'sortie'
        $date_debut = $this->input->get('date_debut');
        $date_fin = $this->input->get('date_fin');
        $caisse_id = $this->input->get('caisse_id');
        $mode_paiement = $this->input->get('mode_paiement');

        if (empty($date_debut)) {
            $date_debut = date('Y-m-01');
        }
        if (empty($date_fin)) {
            $date_fin = date('Y-m-d');
        }

        $this->db->select("
        oc.*,
        c.name as caisse_nom,
        eh.exp_category as category_name
    ");

        $this->db->from('operation_caisse oc');
        $this->db->join('income c', 'c.id = oc.caisse_id', 'left');
        $this->db->join('expense_head eh', 'eh.id = oc.exp_head_id', 'left');

        // Dates
        $this->db->where('DATE(oc.date) >=', $date_debut);
        $this->db->where('DATE(oc.date) <=', $date_fin);

        // Type d'opération
        if ($type == 'entree') {
            $this->db->where('oc.entree >', 0);
        } elseif ($type == 'sortie') {
            $this->db->where('oc.sortie >', 0);
        }

        // Caisse
        if (!empty($caisse_id)) {
            $this->db->where('oc.caisse_id', $caisse_id);
        }

        // Mode paiement
        if (!empty($mode_paiement)) {
            $this->db->where('oc.mode_paiement', $mode_paiement);
        }

        $this->db->where('oc.deleted', 'no');
        $this->db->order_by('oc.date', 'ASC');
        $this->db->order_by('oc.created_at', 'ASC');

        $query = $this->db->get();
        $operations = $query->result_array();

        // Calculer les totaux
        $total = 0;
        foreach ($operations as $op) {
            $total += ($type == 'entree') ? $op['entree'] : $op['sortie'];
        }

        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => true,
            'type' => $type,
            'total' => $total,
            'nombre_operations' => count($operations),
            'operations' => $operations,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin
        ));
    }

    // Récupérer les sous-comptes via AJAX
    public function get_sous_comptes_ajax($caisse_id) {
        $sous_comptes = $this->income_model->get_sous_comptes($caisse_id);

        header('Content-Type: application/json');
        echo json_encode(array(
            'success' => true,
            'sous_comptes' => $sous_comptes
        ));
    }

    // Gestion des sous-comptes
    public function gestion_sous_comptes($caisse_id = null) {
        if (!$this->rbac->hasPrivilege('caisse', 'can_edit')) {
            access_denied();
        }

        if ($this->input->post()) {
            $data = array(
                'caisse_id' => $this->input->post('caisse_id'),
                'nom' => $this->input->post('nom'),
                'type' => $this->input->post('type'),
                'montant_initial' => $this->input->post('montant_initial'),
                'solde_actuel' => $this->input->post('montant_initial'),
                'est_actif' => 1
            );

            $this->income_model->create_sous_compte($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">Sous-compte créé avec succès</div>');
            redirect('admin/income');
        }

        $data['caisse'] = $this->get_caisse_by_id($caisse_id);
        $data['sous_comptes'] = $this->income_model->get_sous_comptes($caisse_id);

        $this->load->view('admin/income/gestion_sous_comptes', $data);
    }

}