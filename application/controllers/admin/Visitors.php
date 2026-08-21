<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Visitors extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("visitors_model");
    }

    // ========================================== //
    // INDEX - LISTE DES VISITEURS                //
    // ========================================== //
    function index() {
        if (!$this->rbac->hasPrivilege('visiteurs', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'receptioniste');
        $this->session->set_userdata('sub_menu', 'admin/visitors');
        $this->form_validation->set_rules('purpose', $this->lang->line('purpose'), 'required');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('firstname', $this->lang->line('firstname'), 'required');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');
        $this->form_validation->set_rules('file', $this->lang->line('file'), 'callback_handle_upload[file]');

        if ($this->form_validation->run() == FALSE) {
            $data['visitor_list'] = $this->visitors_model->visitors_list();
            $data['Purpose'] = $this->visitors_model->getPurpose();
            $this->load->view('layout/header');
            $this->load->view('admin/frontoffice/visitorview', $data);
            $this->load->view('layout/footer');
        } else {
            $visitors = array(
                'purpose' => $this->input->post('purpose'),
                'name' => $this->input->post('name'),
                'firstname' => $this->input->post('firstname'),
                'contact' => $this->input->post('contact'),
                'email' => $this->input->post('email'),
                'organisation' => $this->input->post('organisation'),
                'function' => $this->input->post('function'),
                'id_proof' => $this->input->post('id_proof'),
                'id_type' => $this->input->post('id_type'),
                'access_level' => $this->input->post('access_level'),
                'badge' => $this->input->post('badge'),
                'no_of_pepple' => $this->input->post('pepples'),
                'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'in_time' => $this->input->post('time'),
                'out_time' => $this->input->post('out_time'),
                'note' => $this->input->post('note')
            );

            $visitor_id = $this->visitors_model->add($visitors);

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = 'id' . $visitor_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/front_office/visitors/" . $img_name);
                $this->visitors_model->image_add($visitor_id, $img_name);
            }

            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/visitors');
        }
    }

    // ========================================== //
    // SUPPRESSION D'UN VISITEUR                  //
    // ========================================== //
    public function delete($id) {
        if (!$this->rbac->hasPrivilege('visiteurs', 'can_delete')) {
            access_denied();
        }
        $this->visitors_model->delete($id);
        // La redirection est déjà dans le modèle
    }

    // ========================================== //
    // ÉDITION D'UN VISITEUR (PAGE SÉPARÉE)       //
    // ========================================== //
    public function edit($id) {
        if (!$this->rbac->hasPrivilege('visiteurs', 'can_edit')) {
            access_denied();
        }

        $this->form_validation->set_rules('purpose', $this->lang->line('purpose'), 'required');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('firstname', $this->lang->line('firstname'), 'required');
        $this->form_validation->set_rules('file', $this->lang->line('file'), 'callback_handle_upload[file]');

        if ($this->form_validation->run() == FALSE) {
            $data['Purpose'] = $this->visitors_model->getPurpose();
            $data['visitor_list'] = $this->visitors_model->visitors_list();
            $data['visitor_data'] = $this->visitors_model->visitors_list($id);
            $this->load->view('layout/header');
            $this->load->view('admin/frontoffice/visitoreditview', $data);
            $this->load->view('layout/footer');
        } else {
            $visitors = array(
                'purpose' => $this->input->post('purpose'),
                'name' => $this->input->post('name'),
                'firstname' => $this->input->post('firstname'),
                'contact' => $this->input->post('contact'),
                'email' => $this->input->post('email'),
                'organisation' => $this->input->post('organisation'),
                'function' => $this->input->post('function'),
                'id_proof' => $this->input->post('id_proof'),
                'id_type' => $this->input->post('id_type'),
                'access_level' => $this->input->post('access_level'),
                'badge' => $this->input->post('badge'),
                'no_of_pepple' => $this->input->post('pepples'),
                'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'in_time' => $this->input->post('time'),
                'out_time' => $this->input->post('out_time'),
                'note' => $this->input->post('note')
            );

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                $fileInfo = pathinfo($_FILES["file"]["name"]);
                $img_name = 'id' . $id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/front_office/visitors/" . $img_name);
                $this->visitors_model->image_update($id, $img_name);
            }

            $this->visitors_model->update($id, $visitors);
            // La redirection est déjà dans le modèle
        }
    }

    // ========================================== //
    // GET VISITOR DATA (AJAX pour modal)         //
    // ========================================== //
    public function get_visitor_data($id) {
        // Désactiver le débogage et les erreurs
        error_reporting(0);
        ini_set('display_errors', 0);

        // Vider les buffers pour éviter les caractères indésirables
        while (ob_get_level()) {
            ob_end_clean();
        }

        // Définir l'en-tête JSON
        header('Content-Type: application/json');

        // Vérifier les permissions
        if (!$this->rbac->hasPrivilege('visiteurs', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        // Vérifier si l'ID est valide
        if (!$id || !is_numeric($id)) {
            echo json_encode(['success' => false, 'message' => 'ID invalide']);
            return;
        }

        try {
            // Récupérer les données depuis le modèle
            $data = $this->visitors_model->get_visitor_by_id($id);

            if ($data && !empty($data)) {
                // S'assurer que les données sont correctement formatées
                $visitor = [
                    'id' => (int)$data['id'],
                    'purpose' => (string)($data['purpose'] ?? ''),
                    'name' => (string)($data['name'] ?? ''),
                    'firstname' => (string)($data['firstname'] ?? ''),
                    'contact' => (string)($data['contact'] ?? ''),
                    'email' => (string)($data['email'] ?? ''),
                    'organisation' => (string)($data['organisation'] ?? ''),
                    'function' => (string)($data['function'] ?? ''),
                    'id_proof' => (string)($data['id_proof'] ?? ''),
                    'id_type' => (string)($data['id_type'] ?? ''),
                    'access_level' => (string)($data['access_level'] ?? ''),
                    'badge' => (string)($data['badge'] ?? ''),
                    'no_of_pepple' => (int)($data['no_of_pepple'] ?? 1),
                    'date' => (string)($data['date'] ?? ''),
                    'in_time' => (string)($data['in_time'] ?? ''),
                    'out_time' => (string)($data['out_time'] ?? ''),
                    'note' => (string)($data['note'] ?? ''),
                    'image' => (string)($data['image'] ?? '')
                ];

                echo json_encode([
                    'success' => true,
                    'visitor' => $visitor
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Visiteur non trouvé']);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        }
    }

    // ========================================== //
    // UPDATE VIA AJAX                            //
    // ========================================== //
    public function update_ajax() {
        // Désactiver le débogage
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('visiteurs', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $id = $this->input->post('edit_id');

        $this->form_validation->set_rules('purpose', $this->lang->line('purpose'), 'required');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'required');
        $this->form_validation->set_rules('firstname', $this->lang->line('firstname'), 'required');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $visitors = array(
            'purpose' => $this->input->post('purpose'),
            'name' => $this->input->post('name'),
            'firstname' => $this->input->post('firstname'),
            'contact' => $this->input->post('contact'),
            'email' => $this->input->post('email'),
            'organisation' => $this->input->post('organisation'),
            'function' => $this->input->post('function'),
            'id_proof' => $this->input->post('id_proof'),
            'id_type' => $this->input->post('id_type'),
            'access_level' => $this->input->post('access_level'),
            'badge' => $this->input->post('badge'),
            'no_of_pepple' => $this->input->post('pepples'),
            'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
            'in_time' => $this->input->post('time'),
            'out_time' => $this->input->post('out_time'),
            'note' => $this->input->post('note')
        );

        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
            $fileInfo = pathinfo($_FILES["file"]["name"]);
            $img_name = 'id' . $id . '.' . $fileInfo['extension'];
            move_uploaded_file($_FILES["file"]["tmp_name"], "./uploads/front_office/visitors/" . $img_name);
            $this->visitors_model->image_update($id, $img_name);
        }

        $result = $this->visitors_model->update($id, $visitors);

        if ($result !== false) {
            echo json_encode(['success' => true, 'message' => 'Visiteur mis à jour avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }

    // ========================================== //
    // DETAILS D'UN VISITEUR (MODAL)              //
    // ========================================== //
    public function details($id) {
        if (!$this->rbac->hasPrivilege('visiteurs', 'can_view')) {
            access_denied();
        }
        $data['data'] = $this->visitors_model->visitors_list($id);
        $this->load->view('admin/frontoffice/Visitormodelview', $data);
    }

    // ========================================== //
    // TÉLÉCHARGER UN DOCUMENT                    //
    // ========================================== //
    public function download($documents) {
        $this->load->helper('download');
        $filepath = "./uploads/front_office/visitors/" . $documents;
        if (file_exists($filepath)) {
            $data = file_get_contents($filepath);
            $name = $documents;
            force_download($name, $data);
        } else {
            show_404();
        }
    }

    // ========================================== //
    // SUPPRIMER L'IMAGE D'UN VISITEUR            //
    // ========================================== //
    public function imagedelete($id, $image) {
        if (!$this->rbac->hasPrivilege('visiteurs', 'can_delete')) {
            access_denied();
        }
        $this->visitors_model->image_delete($id, $image);
        // La redirection est déjà dans le modèle
    }

    // ========================================== //
    // EXPORT EXCEL (CSV)                         //
    // ========================================== //
    public function export_excel() {
        // Récupérer les filtres
        $purpose = $this->input->get('purpose');
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');
        $status = $this->input->get('status');

        // Récupérer les données
        $data = $this->visitors_model->get_filtered_visitors($purpose, $date_from, $date_to, $status);

        // Nom du fichier
        $filename = 'visiteurs_' . date('Y-m-d') . '.csv';

        // En-têtes HTTP
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: public');

        // Créer le fichier CSV
        $output = fopen('php://output', 'w');

        // BOM pour UTF-8 (nécessaire pour Excel)
        fputs($output, "\xEF\xBB\xBF");

        // En-têtes du tableau
        fputcsv($output, [
            'Motif',
            'Nom',
            'Prénom',
            'Téléphone',
            'Email',
            'Organisation',
            'Fonction',
            'Pièce ID',
            'Type pièce',
            'Niveau accès',
            'Badge',
            'Date',
            'Arrivée',
            'Sortie',
            'Nb personnes',
            'Observation'
        ]);

        // Données
        foreach ($data as $visitor) {
            fputcsv($output, [
                $visitor['purpose'] ?? '',
                $visitor['name'] ?? '',
                $visitor['firstname'] ?? '',
                $visitor['contact'] ?? '',
                $visitor['email'] ?? '',
                $visitor['organisation'] ?? '',
                $visitor['function'] ?? '',
                $visitor['id_proof'] ?? '',
                $visitor['id_type'] ?? '',
                $visitor['access_level'] ?? '',
                $visitor['badge'] ?? '',
                !empty($visitor['date']) ? date('d/m/Y', strtotime($visitor['date'])) : '',
                $visitor['in_time'] ?? '',
                $visitor['out_time'] ?: 'En cours',
                $visitor['no_of_pepple'] ?? 1,
                $visitor['note'] ?? ''
            ]);
        }

        fclose($output);
        exit;
    }

    // ========================================== //
    // EXPORT PDF                                 //
    // ========================================== //
    public function export_pdf() {
        // Récupérer les filtres
        $purpose = $this->input->get('purpose');
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');
        $status = $this->input->get('status');

        // Récupérer les données
        $data['visitors'] = $this->visitors_model->get_filtered_visitors($purpose, $date_from, $date_to, $status);
        $data['title'] = 'Liste des visiteurs';
        $data['date_generated'] = date('d/m/Y H:i');

        // Charger la vue PDF
        $html = $this->load->view('admin/frontoffice/visitor_pdf_export', $data, true);

        // Vérifier la librairie PDF disponible
        if (class_exists('Dompdf\Dompdf')) {
            $this->load->library('pdf');
            $this->pdf->loadHtml($html);
            $this->pdf->setPaper('A4', 'landscape');
            $this->pdf->render();
            $this->pdf->stream('visiteurs_' . date('Y-m-d') . '.pdf', array("Attachment" => 1));
        } else {
            // Alternative avec mpdf
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->WriteHTML($html);
            $this->m_pdf->pdf->Output('visiteurs_' . date('Y-m-d') . '.pdf', 'D');
        }
        exit;
    }

    // ========================================== //
    // TEST DE RÉCUPÉRATION (pour déboguer)       //
    // ========================================== //
    public function test_get($id) {
        // Désactiver le débogage
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        $data = $this->visitors_model->get_visitor_by_id($id);

        if ($data) {
            echo json_encode([
                'success' => true,
                'data' => $data
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Aucune donnée trouvée pour l\'ID ' . $id
            ]);
        }
    }

    // Dans votre contrôleur Visitors
    public function generate_badge() {
        $access_level = $this->input->post('access_level');
        $id_type = $this->input->post('id_type');

        $badge = $this->visitors_model->generate_badge_number($access_level, $id_type);

        echo json_encode(['success' => true, 'badge' => $badge]);
    }

// Dans votre modèle Visitors_model
    public function generate_badge_number($access_level, $id_type) {
        // Définir les préfixes
        $prefixes = [
            'Niveau 1' => 'A',
            'Niveau 2' => 'B',
            'Niveau 3' => 'C',
            'Niveau 4' => 'S'
        ];

        // Préfixe du type de pièce
        $id_prefixes = [
            'CNI' => 'CN',
            'Passeport' => 'PP',
            'Permis' => 'PR',
            'Carte séjour' => 'CS',
            'Autre' => 'AU'
        ];

        $prefix = $prefixes[$access_level] ?? 'V';
        $idPrefix = $id_prefixes[$id_type] ?? 'V';

        // Générer un nombre unique
        $year = date('y');
        $month = date('m');
        $random = rand(1000, 9999);

        // Vérifier l'unicité dans la base de données
        $badge = $prefix . '-' . $idPrefix . '-' . $year . $month . $random;

        // Vérifier si le badge existe déjà
        $this->db->where('badge', $badge);
        $query = $this->db->get('visitors_book');

        // Si le badge existe déjà, régénérer
        while ($query->num_rows() > 0) {
            $random = rand(1000, 9999);
            $badge = $prefix . '-' . $idPrefix . '-' . $year . $month . $random;
            $this->db->where('badge', $badge);
            $query = $this->db->get('visitors_book');
        }

        return $badge;
    }



    // ========================================== //
    // MIGRATION - AJOUT DES COLONNES             //
    // ========================================== //
    public function migrate_visitors_table() {
        // Vérifier les permissions (admin seulement)
        if (!$this->rbac->hasPrivilege('visiteurs', 'can_edit')) {
            access_denied();
        }

        $result = $this->visitors_model->migrate_add_columns();

        if ($result) {
            echo "<h2 style='color: green;'>✅ Migration réussie !</h2>";
            echo "<p>Toutes les colonnes ont été ajoutées avec succès.</p>";
            echo "<p>Colonnes ajoutées :</p>";
            echo "<ul>";
            echo "<li><strong>firstname</strong> - VARCHAR(100) - Prénom</li>";
            echo "<li><strong>organisation</strong> - VARCHAR(200) - Organisation</li>";
            echo "<li><strong>function</strong> - VARCHAR(150) - Fonction/Poste</li>";
            echo "<li><strong>id_type</strong> - VARCHAR(50) - Type de pièce</li>";
            echo "<li><strong>access_level</strong> - VARCHAR(100) - Niveau d'accès</li>";
            echo "<li><strong>badge</strong> - VARCHAR(50) - Numéro de badge</li>";
            echo "</ul>";
            echo "<p><a href='" . base_url('admin/visitors') . "' class='btn btn-primary'>Retour à la liste</a></p>";
        } else {
            echo "<h2 style='color: red;'>❌ Erreur lors de la migration.</h2>";
            echo "<p>Veuillez vérifier les logs ou exécuter manuellement le script SQL.</p>";
        }
    }

    // ========================================== //
    // VALIDATION PERSONNALISÉE                   //
    // ========================================== //
    public function check_default($post_string) {
        return $post_string == "" ? FALSE : TRUE;
    }

    // ========================================== //
    // GESTION DE L'UPLOAD DE FICHIER             //
    // ========================================== //
    public function handle_upload($str, $var) {
        $image_validate = $this->config->item('file_validate');
        $result = $this->filetype_model->get();
        if (isset($_FILES[$var]) && !empty($_FILES[$var]['name'])) {
            $file_type = $_FILES[$var]['type'];
            $file_size = $_FILES[$var]["size"];
            $file_name = $_FILES[$var]["name"];

            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->file_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->file_mime)));
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if ($files = filesize($_FILES[$var]['tmp_name'])) {
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
                $this->form_validation->set_message('handle_upload', "File Type / Extension Error Uploading Image");
                return false;
            }
            return true;
        }
        return true;
    }


}