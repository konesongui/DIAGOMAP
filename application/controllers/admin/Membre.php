<?php
// ============================================================
// CONTRÔLEUR : Membre
// DESCRIPTION : Gestion des adhérents de l'association
// ROUTE : admin/frontoffice/membre
// ============================================================

defined('BASEPATH') OR exit('No direct script access allowed');

class Membre extends CI_Controller {

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
     * Liste des adhérents
     * URL: admin/frontoffice/membre
     */
    public function index() {
        $data['title'] = 'Liste des adhérents';
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
        $config['base_url'] = site_url('admin/frontoffice/membre/index');
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

        $data['content'] = 'admin/frontoffice/membre/liste';
        $this->load->view('admin/layouts/main', $data);
    }

    /**
     * Ajouter un adhérent
     * URL: admin/frontoffice/membre/add
     */
    public function add() {
        $data['title'] = 'Ajouter un adhérent';
        $data['breadcrumb'] = [
            ['label' => 'Association', 'url' => '#'],
            ['label' => 'Adhérents', 'url' => base_url('admin/frontoffice/membre')],
            ['label' => 'Ajouter', 'url' => ''],
        ];

        $data['categories'] = $this->membre_model->get_all_categories();

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
            $data['content'] = 'admin/frontoffice/membre/add';
            $this->load->view('admin/layouts/main', $data);
        } else {
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
                $this->session->set_flashdata('success', 'L\'adhérent a été ajouté avec succès.');
                redirect('admin/frontoffice/membre/view/' . $insert_id);
            } else {
                $this->session->set_flashdata('error', 'Une erreur est survenue lors de l\'ajout.');
                redirect('admin/frontoffice/membre/add');
            }
        }
    }

    /**
     * Voir le détail d'un adhérent
     * URL: admin/frontoffice/membre/view/{id}
     */
    public function view($id) {
        $data['membre'] = $this->membre_model->get_membre_by_id($id);

        if (!$data['membre']) {
            $this->session->set_flashdata('error', 'Adhérent non trouvé.');
            redirect('admin/frontoffice/membre');
        }

        $data['title'] = 'Détails de l\'adhérent - ' . $data['membre']->prenom . ' ' . $data['membre']->nom;
        $data['breadcrumb'] = [
            ['label' => 'Association', 'url' => '#'],
            ['label' => 'Adhérents', 'url' => base_url('admin/frontoffice/membre')],
            ['label' => 'Détails', 'url' => ''],
        ];

        // Récupérer l'historique
        $data['historique'] = $this->membre_model->get_historique($id);

        $data['content'] = 'admin/frontoffice/membre/view';
        $this->load->view('admin/layouts/main', $data);
    }

    /**
     * Modifier un adhérent
     * URL: admin/frontoffice/membre/edit/{id}
     */
    public function edit($id) {
        $data['membre'] = $this->membre_model->get_membre_by_id($id);

        if (!$data['membre']) {
            $this->session->set_flashdata('error', 'Adhérent non trouvé.');
            redirect('admin/frontoffice/membre');
        }

        $data['title'] = 'Modifier l\'adhérent - ' . $data['membre']->prenom . ' ' . $data['membre']->nom;
        $data['breadcrumb'] = [
            ['label' => 'Association', 'url' => '#'],
            ['label' => 'Adhérents', 'url' => base_url('admin/frontoffice/membre')],
            ['label' => 'Modifier', 'url' => ''],
        ];

        $data['categories'] = $this->membre_model->get_all_categories();

        $this->form_validation->set_rules('nom', 'Nom', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('prenom', 'Prénom', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|max_length[100]');
        $this->form_validation->set_rules('telephone', 'Téléphone', 'trim|max_length[20]');

        if ($this->form_validation->run() == FALSE) {
            $data['content'] = 'admin/frontoffice/membre/edit';
            $this->load->view('admin/layouts/main', $data);
        } else {
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
                    if ($data['membre']->photo && file_exists('./' . $data['membre']->photo)) {
                        unlink('./' . $data['membre']->photo);
                    }
                    $upload_data = $this->upload->data();
                    $membre_data['photo'] = 'uploads/membres/' . $upload_data['file_name'];
                }
            }

            $updated = $this->membre_model->update_membre($id, $membre_data);

            if ($updated) {
                $this->session->set_flashdata('success', 'L\'adhérent a été modifié avec succès.');
                redirect('admin/frontoffice/membre/view/' . $id);
            } else {
                $this->session->set_flashdata('info', 'Aucune modification n\'a été effectuée.');
                redirect('admin/frontoffice/membre/view/' . $id);
            }
        }
    }

    /**
     * Supprimer un adhérent (AJAX)
     * URL: admin/frontoffice/membre/delete/{id}
     */
    public function delete($id) {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

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
     * Changer le statut d'un adhérent (AJAX)
     * URL: admin/frontoffice/membre/toggle_status/{id}
     */
    public function toggle_status($id) {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

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
     * Vérifier si l'email existe déjà (AJAX)
     * URL: admin/frontoffice/membre/check_email
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
     * Exporter les adhérents en CSV
     * URL: admin/frontoffice/membre/export_csv
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
            $csv_data .= $m->matricule . ','
                . $m->nom . ','
                . $m->prenom . ','
                . $m->email . ','
                . $m->telephone . ','
                . $m->ville . ','
                . $m->type_membre . ','
                . $statut . ','
                . $m->date_adhesion . ','
                . ($m->categorie_nom ?? '') . "\n";
        }

        $filename = 'adherents_' . date('Y-m-d') . '.csv';
        force_download($filename, $csv_data);
    }
}