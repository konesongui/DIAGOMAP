<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Stockentry extends Admin_Controller {

    /**
     * Constructor - Loads necessary helpers and performs initialization
     */
    function __construct() {
        parent::__construct();
        $this->load->helper('form');
        $this->load->model('stockentry_model');
        $this->load->model('itemcategory_model');
        $this->load->model('staff_model');
        // Charger l'helper Excel
        $this->load->helper('excel');
    }

    /**
     * Main index method - Handles item listing and creation
     */
    function index() {
        // Check view permission
        if (!$this->rbac->hasPrivilege('item_stock', 'can_view')) {
            access_denied();
        }

        // Set menu active states
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'Stockentry/index');

        // Initialize page data
        $data = [
            'title' => 'Stock Entry',
            'title_list' => 'Recent Stock Entry',
        ];

        // Load views
        $this->load->view('layout/header', $data);
        $this->load->view('admin/stockentry/list', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * GET STOCK ENTRY DATA
     * IN JSON FORMAT
     *
     * @return  JSON   $response
     */
    public function data()
    {
        // Récupère les données du modèle
        $result = $this->stockentry_model->getListData();

        // Les données sont déjà au format JSON, on les renvoie directement
        echo $result;
    }

    /**
     * STOCK ENTRY TOOL FORM
     */
    public function form() {
        // Définition des menus actifs
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'Stockentry/index');

        // Préparation des données pour la vue
        $data = [
            'title' => 'Ajouter un article au stock',
            'title_list' => 'Derniers articles ajoutés au stock',
            'roles' => $this->role_model->get(),
            'itemcatlist' => $this->itemcategory_model->get(),
            'staff' => $this->staff_model->inventry_staff()
        ];

        // Chargement des vues
        $this->load->view('layout/header', $data);
        $this->load->view('admin/stockentry/form', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * Fonction d'importation Excel avec PhpSpreadsheet
     */


    /**
     * Fonction privée pour mettre à jour le stock
     */
    private function _update_item_stock($item_id, $quantity) {
        $current_stock = $this->db->select('quantity')
            ->where('item_id', $item_id)
            ->get('item_stock')
            ->row();

        if ($current_stock) {
            // Mettre à jour le stock existant
            $new_quantity = $current_stock->quantity + $quantity;
            $this->db->where('item_id', $item_id)
                ->update('item_stock', ['quantity' => $new_quantity]);
        } else {
            // Créer une nouvelle entrée de stock
            $this->db->insert('item_stock', [
                'item_id' => $item_id,
                'quantity' => $quantity,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * STOCK ENTRY TOOL FORM - Version manuelle
     */
    public function add() {
        // Initialisation de la réponse
        $response = ['status' => 'fail', 'message' => ''];

        try {
            // Vérification des données POST
            if (!$this->input->post()) {
                throw new Exception('Aucune donnée reçue');
            }

            // Validation des champs obligatoires
            $this->form_validation->set_rules('designation', 'Désignation', 'required|trim');
            $this->form_validation->set_rules('issue_date', 'Date', 'required');
            $this->form_validation->set_rules('item_category_id[]', 'Catégorie', 'required');
            $this->form_validation->set_rules('item_id[]', 'Article', 'required');
            $this->form_validation->set_rules('quantity[]', 'Quantité', 'required|numeric|greater_than[0]');
            $this->form_validation->set_rules('price[]', 'Prix unitaire', 'required|numeric|greater_than[0]');

            if ($this->form_validation->run() == false) {
                throw new Exception(validation_errors());
            }

            // Récupération et validation des données
            $data = [
                'designation' => $this->input->post('designation'),
                'issue_date' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('issue_date')))),
                'grand_total' => $this->input->post('grandtotal'),
                'items' => []
            ];

            // Validation des articles
            $categories = $this->input->post('item_category_id');
            $items = $this->input->post('item_id');
            $quantities = $this->input->post('quantity');
            $prices = $this->input->post('price');
            $units = $this->input->post('unit');

            if (!is_array($categories) || !is_array($items) || !is_array($quantities) || !is_array($prices) || !is_array($units)) {
                throw new Exception('Format de données invalide');
            }

            // Construction du tableau d'articles
            foreach ($categories as $index => $category_id) {
                if (empty($items[$index]) || empty($quantities[$index])) {
                    throw new Exception('Données d\'article manquantes');
                }

                $data['items'][] = [
                    'category_id' => $category_id,
                    'item_id' => $items[$index],
                    'quantity' => $quantities[$index],
                    'price' => $prices[$index] ?? 0,
                    'unit' => $units[$index] ?? '',
                    'line_total' => $quantities[$index] * ($prices[$index] ?? 0)
                ];
            }

            // Enregistrement des données
            $insert_id = $this->stockentry_model->add($data);

            if (!$insert_id) {
                throw new Exception('Erreur lors de l\'enregistrement');
            }

            $response['status'] = 'success';
            $response['message'] = 'L\'entrée de stock a été enregistrée avec succès';

        } catch (Exception $e) {
            $response['message'] = 'Erreur: ' . $e->getMessage();
            log_message('error', 'Stockentry Add Error: ' . $e->getMessage());
        }

        // Retourner la réponse en JSON
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($response));
    }

    /**
     * Affiche les détails d'une entrée de stock
     *
     * @param int $id ID de l'entrée de stock
     * @return void
     */
    public function view($id)
    {
        if (!$this->rbac->hasPrivilege('item_stock', 'can_view')) {
            access_denied();
        }

        $data['entry'] = $this->stockentry_model->getEntryWithItems($id);

        if (!$data['entry']) {
            show_error('Entrée de stock non trouvée', 404);
        }

        $data['title'] = 'Détails de l\'entrée de stock';
        $data['page_title'] = 'Détails de l\'entrée ' . $data['entry']['reference'];

        $this->load->view('layout/header');
        $this->load->view('admin/stockentry/view', $data);
        $this->load->view('layout/footer');
    }

    /**
     * Fonction d'importation Excel/CSV pour les entrées de stock
     */

    /**
     * Affiche le formulaire d'importation
     */
    public function import()
    {
        if (!$this->rbac->hasPrivilege('item_stock', 'can_add')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'Stockentry/import');

        $data = [
            'title' => 'Importation des entrées de stock',
            'title_list' => 'Importation par fichier CSV',
            'itemcatlist' => $this->itemcategory_model->get(),
            'staff' => $this->staff_model->inventry_staff()
        ];

        // Définir les champs d'importation
        $data['field'] = array(
            "reference" => "Référence",
            "designation" => "Désignation",
            "issue_date" => "Date d'entrée",
            "item_name" => "Nom article",
            "category_name" => "Nom catégorie",
            "quantity" => "Quantité",
            "unit" => "Unité",
            "unit_price" => "Prix unitaire",
            "total_price" => "Prix total"
        );

        $this->load->view('layout/header', $data);
        $this->load->view('admin/stockentry/import', $data);
        $this->load->view('layout/footer', $data);
    }

    /**
     * Traitement de l'importation
     */
    public function do_import_old()
    {
        if (!$this->rbac->hasPrivilege('item_stock', 'can_add')) {
            access_denied();
        }

        // Vérifier si un fichier a été uploadé
        if (empty($_FILES['file']['name'])) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Veuillez sélectionner un fichier CSV</div>');
            redirect('admin/stockentry/import');
        }

        $file_path = $_FILES['file']['tmp_name'];
        $file_name = $_FILES['file']['name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

        // Vérifier l'extension
        if (strtolower($file_ext) !== 'csv') {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Seuls les fichiers CSV sont autorisés</div>');
            redirect('admin/stockentry/import');
        }

        try {
            // Lire le fichier CSV
            $csv_data = $this->read_csv_file($file_path);

            if (empty($csv_data)) {
                throw new Exception('Le fichier CSV est vide ou mal formaté');
            }

            // Traiter les données
            $results = $this->process_import_data($csv_data);

            // Afficher les résultats
            $this->show_import_results($results);

        } catch (Exception $e) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Erreur: ' . $e->getMessage() . '</div>');
            redirect('admin/stockentry/import');
        }
    }
    public function do_import()
    {
        if (!$this->rbac->hasPrivilege('item_stock', 'can_add')) {
            access_denied();
        }

        // Vérifier si un fichier a été uploadé
        if (empty($_FILES['file']['name'])) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Veuillez sélectionner un fichier CSV</div>');
            redirect('admin/stockentry/import');
        }

        $file_path = $_FILES['file']['tmp_name'];
        $file_name = $_FILES['file']['name'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);

        // Vérifier l'extension
        if (strtolower($file_ext) !== 'csv') {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Seuls les fichiers CSV sont autorisés</div>');
            redirect('admin/stockentry/import');
        }

        try {
            // Lire le fichier CSV
            $csv_data = $this->read_csv_file($file_path);

            if (empty($csv_data)) {
                throw new Exception('Le fichier CSV est vide ou mal formaté');
            }

            // Traiter les données avec création automatique
            $results = $this->process_import_data_with_creation($csv_data);

            // Afficher les résultats
            $this->show_import_results($results);

        } catch (Exception $e) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">Erreur: ' . $e->getMessage() . '</div>');
            redirect('admin/stockentry/import');
        }
    }
    /**
     * Lire le fichier CSV
     */
    private function read_csv_file_od($file_path)
    {
        $data = array();

        if (($handle = fopen($file_path, "r")) !== FALSE) {
            // Lire l'en-tête (première ligne)
            $headers = fgetcsv($handle, 1000, ",");

            // Vérifier les en-têtes requis
            $required_headers = ['reference', 'designation', 'issue_date', 'item_name', 'category_name', 'quantity'];
            foreach ($required_headers as $required) {
                if (!in_array($required, $headers)) {
                    throw new Exception('En-tête "' . $required . '" manquant dans le fichier CSV');
                }
            }

            // Lire les données
            $line_number = 2; // Commence à 2 (1 = en-tête)
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($row) == count($headers)) {
                    $row_data = array_combine($headers, $row);
                    $row_data['line_number'] = $line_number;
                    $data[] = $row_data;
                }
                $line_number++;
            }
            fclose($handle);
        }

        return $data;
    }
    /**
     * Lire le fichier CSV
     */
    private function read_csv_file($file_path)
    {
        $data = array();

        if (($handle = fopen($file_path, "r")) !== FALSE) {
            // Lire l'en-tête (première ligne)
            $headers = fgetcsv($handle, 1000, ",");

            // Vérifier les en-têtes requis minimaux
            $required_headers = ['reference', 'designation', 'issue_date', 'item_name', 'category_name', 'quantity'];
            foreach ($required_headers as $required) {
                if (!in_array($required, $headers)) {
                    throw new Exception('En-tête "' . $required . '" manquant dans le fichier CSV');
                }
            }

            // Lire les données
            $line_number = 2; // Commence à 2 (1 = en-tête)
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if (count($row) == count($headers)) {
                    $row_data = array_combine($headers, $row);
                    $row_data['line_number'] = $line_number;
                    $data[] = $row_data;
                } else {
                    // Gérer les lignes avec un nombre de colonnes différent
                    throw new Exception('Ligne ' . $line_number . ': Nombre de colonnes incorrect');
                }
                $line_number++;
            }
            fclose($handle);
        }

        return $data;
    }
    /**
     * Traiter les données d'importation
     */
    private function process_import_data($csv_data)
    {
        $results = array(
            'success' => 0,
            'errors' => array(),
            'entries_created' => 0
        );

        // Grouper par référence (une référence = une entrée de stock)
        $grouped_data = array();
        foreach ($csv_data as $row) {
            $reference = trim($row['reference']);
            if (!isset($grouped_data[$reference])) {
                $grouped_data[$reference] = array(
                    'entry_data' => array(
                        'reference' => $reference,
                        'designation' => trim($row['designation']),
                        'issue_date' => $this->parse_date(trim($row['issue_date']))
                    ),
                    'items' => array()
                );
            }

            // Ajouter l'article
            $grouped_data[$reference]['items'][] = array(
                'item_name' => trim($row['item_name']),
                'category_name' => trim($row['category_name']),
                'quantity' => trim($row['quantity']),
                'unit' => isset($row['unit']) ? trim($row['unit']) : '',
                'unit_price' => isset($row['unit_price']) ? trim($row['unit_price']) : 0,
                'total_price' => isset($row['total_price']) ? trim($row['total_price']) : 0,
                'line_number' => $row['line_number']
            );
        }

        // Traiter chaque groupe
        foreach ($grouped_data as $reference => $entry_group) {
            try {
                // Valider l'entrée
                $this->validate_entry($entry_group['entry_data']);

                // Préparer les articles
                $items = array();
                $grand_total = 0;

                foreach ($entry_group['items'] as $item_data) {
                    $item = $this->prepare_item($item_data);
                    $items[] = $item;
                    $grand_total += $item['line_total'];
                }

                if (empty($items)) {
                    throw new Exception('Aucun article valide pour cette entrée');
                }

                // Créer l'entrée
                $entry_data = array(
                    'designation' => $entry_group['entry_data']['designation'],
                    'issue_date' => $entry_group['entry_data']['issue_date'],
                    'grand_total' => $grand_total,
                    'items' => $items
                );

                $entry_id = $this->stockentry_model->add($entry_data);

                if ($entry_id) {
                    $results['success']++;
                    $results['entries_created']++;
                } else {
                    $results['errors'][] = "Erreur lors de la création de l'entrée " . $reference;
                }

            } catch (Exception $e) {
                $results['errors'][] = "Entrée " . $reference . " (ligne " . $entry_group['items'][0]['line_number'] . "): " . $e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Valider les données d'une entrée
     */
    private function validate_entry($entry_data)
    {
        if (empty($entry_data['reference'])) {
            throw new Exception('Référence manquante');
        }

        if (empty($entry_data['designation'])) {
            throw new Exception('Désignation manquante');
        }

        if (empty($entry_data['issue_date'])) {
            throw new Exception('Date manquante');
        }

        // Vérifier si la référence existe déjà
        if ($this->stockentry_model->checkReferenceExists($entry_data['reference'])) {
            throw new Exception('La référence existe déjà: ' . $entry_data['reference']);
        }

        return true;
    }

    /**
     * Préparer un article pour l'importation
     */
    private function prepare_item($item_data)
    {
        // Valider les données de base
        if (empty($item_data['item_name'])) {
            throw new Exception('Nom d\'article manquant');
        }

        if (empty($item_data['category_name'])) {
            throw new Exception('Catégorie manquante');
        }

        $quantity = floatval($item_data['quantity']);
        if ($quantity <= 0) {
            throw new Exception('Quantité invalide: ' . $item_data['quantity']);
        }

        // Trouver l'article
        $item = $this->db->select('item.*, item_category.item_category as category_name')
            ->from('item')
            ->join('item_category', 'item_category.id = item.item_category_id', 'left')
            ->where('item.name', $item_data['item_name'])
            ->where('item_category.item_category', $item_data['category_name'])
            ->get()
            ->row();

        if (!$item) {
            throw new Exception('Article non trouvé: ' . $item_data['item_name'] . ' dans ' . $item_data['category_name']);
        }

        // Calculer les prix
        $unit_price = floatval($item_data['unit_price']);
        $total_price = floatval($item_data['total_price']);

        if ($unit_price > 0 && $total_price > 0) {
            // Utiliser le prix unitaire fourni
            $line_total = $unit_price * $quantity;
        } elseif ($total_price > 0) {
            // Calculer le prix unitaire à partir du total
            $unit_price = $total_price / $quantity;
            $line_total = $total_price;
        } elseif ($unit_price > 0) {
            // Calculer le total à partir du prix unitaire
            $line_total = $unit_price * $quantity;
        } else {
            // Aucun prix fourni
            $unit_price = 0;
            $line_total = 0;
        }

        return array(
            'item_id' => $item->id,
            'category_id' => $item->item_category_id,
            'quantity' => $quantity,
            'unit' => !empty($item_data['unit']) ? $item_data['unit'] : $item->unit,
            'price' => $unit_price,
            'line_total' => $line_total
        );
    }

    /**
     * Parser la date
     */
    private function parse_date($date_string)
    {
        // Essayer différents formats
        $formats = array('d/m/Y', 'Y-m-d', 'd-m-Y', 'm/d/Y', 'Y/m/d');

        foreach ($formats as $format) {
            $date = DateTime::createFromFormat($format, $date_string);
            if ($date && $date->format($format) === $date_string) {
                return $date->format('Y-m-d');
            }
        }

        // Essayer strtotime
        $timestamp = strtotime($date_string);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        throw new Exception('Date invalide: ' . $date_string);
    }

    /**
     * Afficher les résultats de l'importation
     */

    /**
     * Afficher les résultats de l'importation avec détails
     */
    private function show_import_results($results)
    {
        $message = '<div class="alert ';

        if (empty($results['errors'])) {
            $message .= 'alert-success';
            $message .= '"><h4><i class="fa fa-check"></i> Importation réussie!</h4>';
        } else {
            $message .= 'alert-warning';
            $message .= '"><h4><i class="fa fa-exclamation-triangle"></i> Importation partielle</h4>';
        }

        // Résumé
        $message .= '<div class="row">';
        $message .= '<div class="col-md-3"><strong>Entrées créées:</strong> ' . $results['entries_created'] . '</div>';
        $message .= '<div class="col-md-3"><strong>Articles créés:</strong> ' . $results['items_created'] . '</div>';
        $message .= '<div class="col-md-3"><strong>Catégories créées:</strong> ' . $results['categories_created'] . '</div>';
        //$message .= '<div class="col-md-3"><strong>Erreurs:</strong> ' . count($results['errors']) . '</div>';
        $message .= '</div>';

        // Avertissements (créations automatiques)
        if (!empty($results['warnings'])) {
            $message .= '<h5><i class="fa fa-info-circle"></i> Actions effectuées:</h5><ul>';
            foreach ($results['warnings'] as $warning) {
                $message .= '<li>' . $warning . '</li>';
            }
            $message .= '</ul>';
        }

        // Erreurs
        if (!empty($results['errors'])) {
            $message .= '<h5><i class="fa fa-times-circle"></i> Erreurs rencontrées:</h5><ul>';
            foreach ($results['errors'] as $error) {
                $message .= '<li>' . $error . '</li>';
            }
            $message .= '</ul>';
        }

        $message .= '</div>';

        $this->session->set_flashdata('msg', $message);
        redirect('admin/stockentry/import');
    }
    private function show_import_results_old($results)
    {
        $message = '<div class="alert ';

        if (empty($results['errors'])) {
            $message .= 'alert-success';
            $message .= '"><i class="fa fa-check"></i> Importation réussie! ';
            $message .= $results['success'] . ' entrées créées.';
        } else {
            $message .= 'alert-warning';
            $message .= '"><h4><i class="fa fa-exclamation-triangle"></i> Importation partielle</h4>';
            $message .= '<p>' . $results['entries_created'] . ' entrées créées avec succès.</p>';
            $message .= '<h5>Erreurs rencontrées:</h5><ul>';

            foreach ($results['errors'] as $error) {
                $message .= '<li>' . $error . '</li>';
            }

            $message .= '</ul>';
        }

        $message .= '</div>';

        $this->session->set_flashdata('msg', $message);
        redirect('admin/stockentry/import');
    }

    /**
     * Télécharger le modèle CSV
     */
    /**
     * Télécharger le modèle CSV
     */
    public function exportformat_old()
    {
        $this->load->helper('download');

        // Récupérer quelques exemples d'articles et catégories
        $categories = $this->db->select('item_category')
            ->from('item_category')
            ->limit(3)
            ->get()
            ->result_array();

        // Vérifier les champs disponibles dans la table item
        $this->db->select('item.*, item_category.item_category');
        $this->db->from('item');
        $this->db->join('item_category', 'item_category.id = item.item_category_id');
        $this->db->limit(3);
        $items = $this->db->get()->result_array();

        // Afficher les champs disponibles (pour débogage)
        // echo "<pre>"; print_r($items); echo "</pre>"; exit;

        // Créer le contenu CSV avec les champs disponibles
        $csv_content = "reference,designation,issue_date,item_name,category_name,quantity,unit,unit_price,total_price\n";

        // Ajouter des exemples
        $date = date('d/m/Y');
        $reference = 'ES-' . date('Ym') . '-0001';

        foreach ($items as $index => $item) {
            $csv_content .= $reference . ',';
            $csv_content .= 'Importation de test,';
            $csv_content .= $date . ',';
            $csv_content .= $item['name'] . ',';
            $csv_content .= $item['item_category'] . ',';
            $csv_content .= ($index + 1) * 10 . ',';

            // Utiliser le champ unit s'il existe, sinon valeur par défaut
            $unit = isset($item['unit']) ? $item['unit'] : 'Pièce';
            $csv_content .= $unit . ',';

            $csv_content .= ($index + 1) * 5 . '.00,';
            $csv_content .= (($index + 1) * 10 * ($index + 1) * 5) . '.00';
            $csv_content .= "\n";
        }

        $filename = 'modele_import_stock.csv';

        force_download($filename, $csv_content);
    }
    /**
     * Télécharger le modèle CSV avec champs optionnels
     */
    public function exportformat()
    {
        $this->load->helper('download');

        // Créer le contenu CSV avec tous les champs possibles
        $csv_content = "reference,designation,issue_date,item_code,item_name,category_name,quantity,unit,unit_price,total_price\n";

        // Ajouter des exemples
        $date = date('d/m/Y');
        $reference = 'ES-' . date('Ym') . '-0001';

        // Exemple 1 : Article existant
        $csv_content .= $reference . ',';
        $csv_content .= 'Importation de test - Articles existants,';
        $csv_content .= $date . ',';
        $csv_content .= 'ART001,';
        $csv_content .= 'Ordinateur Portable,';
        $csv_content .= 'Informatique,';
        $csv_content .= '5,';
        $csv_content .= 'Unité,';
        $csv_content .= '1200.00,';
        $csv_content .= '6000.00';
        $csv_content .= "\n";

        // Exemple 2 : Nouvel article avec code
        $csv_content .= $reference . ',';
        $csv_content .= 'Importation de test - Articles existants,';
        $csv_content .= $date . ',';
        $csv_content .= 'ART002,';
        $csv_content .= 'Souris Sans Fil,';
        $csv_content .= 'Informatique,';
        $csv_content .= '10,';
        $csv_content .= 'Pièce,';
        $csv_content .= '25.50,';
        $csv_content .= '255.00';
        $csv_content .= "\n";

        // Exemple 3 : Nouvel article sans code
        $csv_content .= $reference . ',';
        $csv_content .= 'Importation de test - Articles existants,';
        $csv_content .= $date . ',';
        $csv_content .= ',';
        $csv_content .= 'Clavier Mécanique,';
        $csv_content .= 'Informatique,';
        $csv_content .= '8,';
        $csv_content .= 'Pièce,';
        $csv_content .= '45.00,';
        $csv_content .= '360.00';
        $csv_content .= "\n";

        // Exemple 4 : Nouvelle catégorie et article
        $reference2 = 'ES-' . date('Ym') . '-0002';
        $csv_content .= $reference2 . ',';
        $csv_content .= 'Importation de test - Nouvelle catégorie,';
        $csv_content .= $date . ',';
        $csv_content .= 'ART003,';
        $csv_content .= 'Table de Réunion,';
        $csv_content .= 'Mobilier de Bureau,';
        $csv_content .= '2,';
        $csv_content .= 'Unité,';
        $csv_content .= '350.00,';
        $csv_content .= '700.00';
        $csv_content .= "\n";

        $filename = 'modele_import_stock.csv';

        force_download($filename, $csv_content);
    }
    public function check_item_structure()
    {
        $this->db->query("DESCRIBE item");
        $fields = $this->db->result_array();

        echo "<h3>Structure de la table 'item'</h3>";
        echo "<table border='1'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";

        foreach ($fields as $field) {
            echo "<tr>";
            echo "<td>" . $field['Field'] . "</td>";
            echo "<td>" . $field['Type'] . "</td>";
            echo "<td>" . $field['Null'] . "</td>";
            echo "<td>" . $field['Key'] . "</td>";
            echo "<td>" . $field['Default'] . "</td>";
            echo "<td>" . $field['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    private function process_import_data_with_creation($csv_data)
    {
        $results = array(
            'success' => 0,
            'errors' => array(),
            'entries_created' => 0,
            'categories_created' => 0,
            'items_created' => 0,
            'warnings' => array()
        );

        // Grouper par référence (une référence = une entrée de stock)
        $grouped_data = array();
        foreach ($csv_data as $row) {
            $reference = trim($row['reference']);
            if (!isset($grouped_data[$reference])) {
                $grouped_data[$reference] = array(
                    'entry_data' => array(
                        'reference' => $reference,
                        'designation' => trim($row['designation']),
                        'issue_date' => $this->parse_date(trim($row['issue_date']))
                    ),
                    'items' => array()
                );
            }

            // Ajouter l'article
            $grouped_data[$reference]['items'][] = array(
                'item_name' => trim($row['item_name']),
                'category_name' => trim($row['category_name']),
                'quantity' => trim($row['quantity']),
                'unit' => isset($row['unit']) ? trim($row['unit']) : '',
                'unit_price' => isset($row['unit_price']) ? trim($row['unit_price']) : 0,
                'total_price' => isset($row['total_price']) ? trim($row['total_price']) : 0,
                'item_code' => isset($row['item_code']) ? trim($row['item_code']) : '',
                'line_number' => $row['line_number']
            );
        }

        // Traiter chaque groupe
        foreach ($grouped_data as $reference => $entry_group) {
            try {
                // Valider l'entrée
                $this->validate_entry($entry_group['entry_data']);

                // Préparer les articles avec création automatique
                $items = array();
                $grand_total = 0;

                foreach ($entry_group['items'] as $item_data) {
                    $item = $this->prepare_item_with_creation($item_data, $results);
                    $items[] = $item;
                    $grand_total += $item['line_total'];
                }

                if (empty($items)) {
                    throw new Exception('Aucun article valide pour cette entrée');
                }

                // Créer l'entrée
                $entry_data = array(
                    'designation' => $entry_group['entry_data']['designation'],
                    'issue_date' => $entry_group['entry_data']['issue_date'],
                    'grand_total' => $grand_total,
                    'items' => $items
                );

                $entry_id = $this->stockentry_model->add($entry_data);

                if ($entry_id) {
                    $results['success']++;
                    $results['entries_created']++;
                } else {
                    $results['errors'][] = "Erreur lors de la création de l'entrée " . $reference;
                }

            } catch (Exception $e) {
                $results['errors'][] = "Entrée " . $reference . " (ligne " . $entry_group['items'][0]['line_number'] . "): " . $e->getMessage();
            }
        }

        return $results;
    }

    private function prepare_item_with_creation($item_data, &$results)
    {
        // Valider les données de base
        if (empty($item_data['item_name'])) {
            throw new Exception('Nom d\'article manquant');
        }

        if (empty($item_data['category_name'])) {
            throw new Exception('Catégorie manquante');
        }

        $quantity = floatval($item_data['quantity']);
        if ($quantity <= 0) {
            throw new Exception('Quantité invalide: ' . $item_data['quantity']);
        }

        // 1. Vérifier/Créer la catégorie
        $category_id = $this->get_or_create_category($item_data['category_name'], $results);

        // 2. Vérifier/Créer l'article
        $item = $this->get_or_create_item($item_data, $category_id, $results);

        // 3. Calculer les prix
        $unit_price = floatval($item_data['unit_price']);
        $total_price = floatval($item_data['total_price']);

        if ($unit_price > 0 && $total_price > 0) {
            // Utiliser le prix unitaire fourni
            $line_total = $unit_price * $quantity;
        } elseif ($total_price > 0) {
            // Calculer le prix unitaire à partir du total
            $unit_price = $total_price / $quantity;
            $line_total = $total_price;
        } elseif ($unit_price > 0) {
            // Calculer le total à partir du prix unitaire
            $line_total = $unit_price * $quantity;
        } else {
            // Aucun prix fourni
            $unit_price = 0;
            $line_total = 0;
            $results['warnings'][] = "Article '{$item_data['item_name']}' sans prix - valeur 0 assignée";
        }

        return array(
            'item_id' => $item->id,
            'category_id' => $category_id,
            'quantity' => $quantity,
            'unit' => !empty($item_data['unit']) ? $item_data['unit'] : $item->unit,
            'price' => $unit_price,
            'line_total' => $line_total
        );
    }

    private function get_or_create_category($category_name, &$results)
    {
        // Vérifier si la catégorie existe
        $this->db->where('item_category', $category_name);
        $category = $this->db->get('item_category')->row();

        if ($category) {
            return $category->id;
        }

        // Créer la nouvelle catégorie
        $category_data = array(
            'item_category' => $category_name,
            'description' => 'Créée automatiquement lors de l\'importation',
            'is_active' => 'yes',
            'created_at' => date('Y-m-d H:i:s')
        );

        $this->db->insert('item_category', $category_data);
        $category_id = $this->db->insert_id();

        $results['categories_created']++;
        $results['warnings'][] = "Nouvelle catégorie créée: '{$category_name}'";

        return $category_id;
    }

    /**
     * Récupérer ou créer un article
     */
    private function get_or_create_item($item_data, $category_id, &$results)
    {
        $item_name = $item_data['item_name'];
        $item_code = $item_data['item_code'];

        // Chercher l'article par nom
        $this->db->where('name', $item_name);
        $this->db->where('item_category_id', $category_id);
        $item = $this->db->get('item')->row();

        if ($item) {
            return $item;
        }

        // Si non trouvé, chercher par code si fourni
        if (!empty($item_code)) {
            $this->db->where('item_code', $item_code);
            $item = $this->db->get('item')->row();

            if ($item) {
                // Mettre à jour la catégorie si différente
                if ($item->item_category_id != $category_id) {
                    $this->db->where('id', $item->id);
                    $this->db->update('item', array('item_category_id' => $category_id));
                }
                return $item;
            }
        }

        // Créer le nouvel article
        $item_data_insert = array(
            'name' => $item_name,
            'item_category_id' => $category_id,
            'description' => 'Créé automatiquement lors de l\'importation',
            'unit' => !empty($item_data['unit']) ? $item_data['unit'] : 'Pièce',
            'created_at' => date('Y-m-d H:i:s')
        );

        // Ajouter le code d'article s'il est fourni
        if (!empty($item_code)) {
            $item_data_insert['item_code'] = $item_code;
        }

        $this->db->insert('item', $item_data_insert);
        $item_id = $this->db->insert_id();

        // Récupérer l'article créé
        $item = $this->db->where('id', $item_id)->get('item')->row();

        $results['items_created']++;
        $results['warnings'][] = "Nouvel article créé: '{$item_name}' dans la catégorie '{$item_data['category_name']}'";

        return $item;
    }

}