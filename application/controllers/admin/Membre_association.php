<?php
// ============================================================
// CONTRÔLEUR : Membre_association
// DESCRIPTION : Gestion des adhérents de l'association (vue unique avec modals)
// ROUTE : admin/frontoffice/membre_association
// ============================================================

defined('BASEPATH') OR exit('No direct script access allowed');

class Membre_association extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Association_membre_model', 'membre_model');
        $this->load->library('form_validation');
        $this->load->helper('form');
        $this->load->helper('file');

        // Vérifier si l'utilisateur est connecté
        if (!$this->session->userdata('staff_id')) {
            redirect('admin/auth/login');
        }
    }

    /**
     * Page principale - Liste des adhérents avec modals
     * URL: admin/frontoffice/membre_association
     */
    public function index() {
        $data['title'] = 'Gestion des adhérents';
        $data['breadcrumb'] = [
            ['label' => 'Association', 'url' => '#'],
            ['label' => 'Adhérents', 'url' => ''],
        ];

        // Filtres
        $filters = [];
        if ($this->input->get('search')) {
            $filters['search'] = $this->input->get('search');
        }
        if ($this->input->get('type_membre')) {
            $filters['type_membre'] = $this->input->get('type_membre');
        }
        if ($this->input->get('statut') !== null && $this->input->get('statut') !== '') {
            $filters['statut'] = $this->input->get('statut');
        }
        if ($this->input->get('categorie_id')) {
            $filters['categorie_id'] = $this->input->get('categorie_id');
        }

        // Pagination
        $config['base_url'] = site_url('admin/frontoffice/membre_association/index');
        $config['total_rows'] = $this->membre_model->count_membres($filters);
        $config['per_page'] = 20;
        $config['uri_segment'] = 5;
        $this->pagination->initialize($config);

        $page = ($this->uri->segment(5)) ? $this->uri->segment(5) : 0;
        $data['membres'] = $this->membre_model->get_all_membres($filters, $config['per_page'], $page);
        $data['pagination'] = $this->pagination->create_links();

        // Catégories pour le filtre
        $data['categories'] = $this->membre_model->get_all_categories();

        // Statistiques
        $data['stats'] = $this->membre_model->get_stats();

        $data['content'] = 'admin/frontoffice/membre_association/index';
        $this->load->view('admin/layouts/main', $data);
    }

    /**
     * AJAX - Récupérer un adhérent pour affichage/modification
     * URL: admin/frontoffice/membre_association/get_membre
     */
    public function get_membre() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $id = $this->input->post('id');
        $membre = $this->membre_model->get_membre_by_id($id);

        if ($membre) {
            echo json_encode(['status' => 'success', 'data' => $membre]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Adhérent non trouvé.']);
        }
    }

    /**
     * AJAX - Ajouter un adhérent
     * URL: admin/frontoffice/membre_association/add
     */
    public function add() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->form_validation->set_rules('nom', 'Nom', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('prenom', 'Prénom', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|max_length[100]|is_unique[association_membres.email]');
        $this->form_validation->set_rules('telephone', 'Téléphone', 'trim|max_length[20]');
        $this->form_validation->set_rules('date_naissance', 'Date de naissance', 'trim');
        $this->form_validation->set_rules('adresse', 'Adresse', 'trim|max_length[255]');
        $this->form_validation->set_rules('ville', 'Ville', 'trim|max_length[100]');
        $this->form_validation->set_rules('categorie_id', 'Catégorie', 'trim|numeric');
        $this->form_validation->set_rules('type_membre', 'Type de membre', 'trim');
        $this->form_validation->set_rules('montant_cotisation', 'Montant cotisation', 'trim|numeric');
        $this->form_validation->set_rules('date_adhesion', 'Date d\'adhésion', 'trim');

        $this->form_validation->set_message('required', 'Le champ %s est obligatoire');
        $this->form_validation->set_message('valid_email', 'L\'email n\'est pas valide');
        $this->form_validation->set_message('is_unique', 'Cet email est déjà utilisé');
        $this->form_validation->set_message('numeric', 'Le champ %s doit être un nombre');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 'error',
                'errors' => $this->form_validation->error_array()
            ]);
            return;
        }

        $membre_data = [
            'civilite' => $this->input->post('civilite'),
            'nom' => $this->input->post('nom'),
            'prenom' => $this->input->post('prenom'),
            'date_naissance' => $this->input->post('date_naissance') ?: null,
            'lieu_naissance' => $this->input->post('lieu_naissance'),
            'nationalite' => $this->input->post('nationalite'),
            'profession' => $this->input->post('profession'),
            'email' => $this->input->post('email'),
            'telephone' => $this->input->post('telephone'),
            'telephone2' => $this->input->post('telephone2'),
            'adresse' => $this->input->post('adresse'),
            'code_postal' => $this->input->post('code_postal'),
            'ville' => $this->input->post('ville'),
            'pays' => $this->input->post('pays') ?: 'Côte d\'Ivoire',
            'type_membre' => $this->input->post('type_membre') ?: 'actif',
            'categorie_id' => $this->input->post('categorie_id') ?: null,
            'statut' => $this->input->post('statut') ?: 1,
            'date_adhesion' => $this->input->post('date_adhesion') ?: date('Y-m-d'),
            'date_expiration' => $this->input->post('date_expiration') ?: null,
            'montant_cotisation' => $this->input->post('montant_cotisation') ?: null,
            'mode_paiement' => $this->input->post('mode_paiement'),
            'commentaire' => $this->input->post('commentaire'),
            'created_by' => $this->session->userdata('staff_id')
        ];

        // Gestion de la photo
        if (!empty($_FILES['photo']['name'])) {
            $config['upload_path'] = './uploads/membres/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size'] = 2048;
            $config['encrypt_name'] = TRUE;

            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, TRUE);
            }

            $this->load->library('upload', $config);
            if ($this->upload->do_upload('photo')) {
                $upload_data = $this->upload->data();
                $membre_data['photo'] = 'uploads/membres/' . $upload_data['file_name'];
            }
        }

        $insert_id = $this->membre_model->create_membre($membre_data);

        if ($insert_id) {
            $new_membre = $this->membre_model->get_membre_by_id($insert_id);
            echo json_encode([
                'status' => 'success',
                'message' => 'Adhérent ajouté avec succès.',
                'data' => $new_membre
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors de l\'ajout.'
            ]);
        }
    }

    /**
     * AJAX - Modifier un adhérent
     * URL: admin/frontoffice/membre_association/edit
     */
    public function edit() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $id = $this->input->post('id');
        $membre = $this->membre_model->get_membre_by_id($id);

        if (!$membre) {
            echo json_encode(['status' => 'error', 'message' => 'Adhérent non trouvé.']);
            return;
        }

        $this->form_validation->set_rules('nom', 'Nom', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('prenom', 'Prénom', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|max_length[100]');
        $this->form_validation->set_rules('telephone', 'Téléphone', 'trim|max_length[20]');

        $this->form_validation->set_message('required', 'Le champ %s est obligatoire');
        $this->form_validation->set_message('valid_email', 'L\'email n\'est pas valide');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode([
                'status' => 'error',
                'errors' => $this->form_validation->error_array()
            ]);
            return;
        }

        $membre_data = [
            'civilite' => $this->input->post('civilite'),
            'nom' => $this->input->post('nom'),
            'prenom' => $this->input->post('prenom'),
            'date_naissance' => $this->input->post('date_naissance') ?: null,
            'lieu_naissance' => $this->input->post('lieu_naissance'),
            'nationalite' => $this->input->post('nationalite'),
            'profession' => $this->input->post('profession'),
            'email' => $this->input->post('email'),
            'telephone' => $this->input->post('telephone'),
            'telephone2' => $this->input->post('telephone2'),
            'adresse' => $this->input->post('adresse'),
            'code_postal' => $this->input->post('code_postal'),
            'ville' => $this->input->post('ville'),
            'pays' => $this->input->post('pays') ?: 'Côte d\'Ivoire',
            'type_membre' => $this->input->post('type_membre'),
            'categorie_id' => $this->input->post('categorie_id') ?: null,
            'statut' => $this->input->post('statut'),
            'date_adhesion' => $this->input->post('date_adhesion'),
            'date_expiration' => $this->input->post('date_expiration') ?: null,
            'montant_cotisation' => $this->input->post('montant_cotisation') ?: null,
            'mode_paiement' => $this->input->post('mode_paiement'),
            'commentaire' => $this->input->post('commentaire')
        ];

        // Gestion de la photo
        if (!empty($_FILES['photo']['name'])) {
            $config['upload_path'] = './uploads/membres/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size'] = 2048;
            $config['encrypt_name'] = TRUE;

            $this->load->library('upload', $config);
            if ($this->upload->do_upload('photo')) {
                // Supprimer l'ancienne photo
                if ($membre->photo && file_exists('./' . $membre->photo)) {
                    unlink('./' . $membre->photo);
                }
                $upload_data = $this->upload->data();
                $membre_data['photo'] = 'uploads/membres/' . $upload_data['file_name'];
            }
        }

        $updated = $this->membre_model->update_membre($id, $membre_data);

        if ($updated) {
            $updated_membre = $this->membre_model->get_membre_by_id($id);
            echo json_encode([
                'status' => 'success',
                'message' => 'Adhérent modifié avec succès.',
                'data' => $updated_membre
            ]);
        } else {
            echo json_encode([
                'status' => 'info',
                'message' => 'Aucune modification n\'a été effectuée.'
            ]);
        }
    }

    /**
     * AJAX - Supprimer un adhérent
     * URL: admin/frontoffice/membre_association/delete
     */
    public function delete() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $id = $this->input->post('id');
        $membre = $this->membre_model->get_membre_by_id($id);

        if (!$membre) {
            echo json_encode(['status' => 'error', 'message' => 'Adhérent non trouvé.']);
            return;
        }

        $deleted = $this->membre_model->delete_membre($id);

        if ($deleted) {
            // Supprimer la photo
            if ($membre->photo && file_exists('./' . $membre->photo)) {
                unlink('./' . $membre->photo);
            }
            echo json_encode(['status' => 'success', 'message' => 'Adhérent supprimé avec succès.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la suppression.']);
        }
    }

    /**
     * AJAX - Changer le statut d'un adhérent
     * URL: admin/frontoffice/membre_association/toggle_status
     */
    public function toggle_status() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $id = $this->input->post('id');
        $membre = $this->membre_model->get_membre_by_id($id);

        if (!$membre) {
            echo json_encode(['status' => 'error', 'message' => 'Adhérent non trouvé.']);
            return;
        }

        $new_status = $membre->statut == 1 ? 0 : 1;
        $updated = $this->membre_model->toggle_statut($id, $new_status);

        if ($updated) {
            $status_label = $new_status == 1 ? 'activé' : 'désactivé';
            echo json_encode([
                'status' => 'success',
                'message' => 'Adhérent ' . $status_label . ' avec succès.',
                'new_statut' => $new_status
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors du changement de statut.']);
        }
    }

    /**
     * AJAX - Vérifier si l'email existe déjà
     * URL: admin/frontoffice/membre_association/check_email
     */
    public function check_email() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $email = $this->input->post('email');
        $id = $this->input->post('id');

        if (empty($email)) {
            echo json_encode(['valid' => true]);
            return;
        }

        $membre = $this->membre_model->get_membre_by_email($email);

        if ($membre && $membre->id != $id) {
            echo json_encode(['valid' => false, 'message' => 'Cet email est déjà utilisé par un autre adhérent.']);
        } else {
            echo json_encode(['valid' => true]);
        }
    }

    /**
     * AJAX - Récupérer les catégories
     * URL: admin/frontoffice/membre_association/get_categories
     */
    public function get_categories() {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $categories = $this->membre_model->get_all_categories();
        echo json_encode(['status' => 'success', 'data' => $categories]);
    }

    /**
     * Exporter les adhérents en CSV
     * URL: admin/frontoffice/membre_association/export_csv
     */
    public function export_csv() {
        $this->load->helper('download');

        $filters = [];
        if ($this->input->get('search')) {
            $filters['search'] = $this->input->get('search');
        }
        if ($this->input->get('type_membre')) {
            $filters['type_membre'] = $this->input->get('type_membre');
        }
        if ($this->input->get('statut') !== null && $this->input->get('statut') !== '') {
            $filters['statut'] = $this->input->get('statut');
        }

        $membres = $this->membre_model->get_all_membres($filters);

        $csv_data = "Matricule,Nom,Prénom,Email,Téléphone,Ville,Type,Statut,Date d'adhésion,Catégorie\n";
        foreach ($membres as $m) {
            $statut = $m->statut == 1 ? 'Actif' : 'Inactif';
            $csv_data .= '"' . $m->matricule . '","'
                . $m->nom . '","'
                . $m->prenom . '","'
                . $m->email . '","'
                . $m->telephone . '","'
                . $m->ville . '","'
                . $m->type_membre . '","'
                . $statut . '","'
                . $m->date_adhesion . '","'
                . ($m->categorie_nom ?? '') . "\"\n";
        }

        $filename = 'adherents_' . date('Y-m-d') . '.csv';
        force_download($filename, $csv_data);
    }
}