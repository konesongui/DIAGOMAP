<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Transfer extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('form');
        $this->config->load('app-config');
        $this->load->library("datatables");
    }




    public function get_accounts()
    {
        $type = $this->input->post('type');
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();

        if ($type == 'bank') {
            // Récupérer toutes les banques
            $accounts = $this->db->get('banks')->result();
            $html = '<option value="">Sélectionner une banque...</option>';
            foreach ($accounts as $account) {
                $balance = isset($account->balance) ? $account->balance : 0;
                $html .= '<option value="' . $account->id . '" data-balance="' . $balance . '">';
                $html .= htmlspecialchars($account->name);
                if (!empty($account->account_number)) {
                    $html .= ' (' . $account->account_number . ')';
                }
                $html .= ' <span class="option-balance">' . number_format($balance, 0, ',', ' ') . ' ' . $currency_symbol . '</span>';
                $html .= '</option>';
            }
        } else {
            // Récupérer toutes les caisses
            $accounts = $this->income_model->getcaisse();
            $html = '<option value="">Sélectionner une caisse...</option>';
            foreach ($accounts as $account) {
                $balance = isset($account->amount_re) ? $account->amount_re : 0;
                $html .= '<option value="' . $account->id . '" data-balance="' . $balance . '">';
                $html .= htmlspecialchars($account->name);
                $html .= ' <span class="option-balance">' . number_format($balance, 0, ',', ' ') . ' ' . $currency_symbol . '</span>';
                $html .= '</option>';
            }
        }

        echo $html;
    }
    public function index()
    {
        if (!$this->rbac->hasPrivilege('transfer', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Caisse');
        $this->session->set_userdata('sub_menu', 'transfer/transfer_amount');

        // Charger les caisses
        $data['caisses'] = $this->income_model->getcaisse();

        // Charger les banques directement depuis la table
        $data['banques'] = $this->db->get('banks')->result();

        $this->load->view('layout/header', $data);
        $this->load->view('admin/income/transfer_form', $data);
        $this->load->view('layout/footer', $data);
    }



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
        if (!$this->rbac->hasPrivilege('transfer', 'can_view')) {
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
        if (!$this->rbac->hasPrivilege('caisse', 'can_delete')) {
            access_denied();
        }
        $data['title'] = 'Reappro List';
        $this->income_model->remove($id);
        redirect('admin/income/index');
    }

    public function create()
    {
        // dd($this->input->post('income'));
        $data['title'] = 'Add Fees Master';
        $this->form_validation->set_rules('income', $this->lang->line('fees_master'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('income/incomeCreate', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'income' => $this->input->post('income'),
            );
            $this->income_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('income/index');
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

    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('caisse', 'can_edit')) {
            access_denied();
        }

        $data['title']      = 'Edit Fees Master';
        $data['id']         = $id;
        $income             = $this->income_model->got($id);
        $data['income']     = $income;
        $data['title_list'] = 'Fees Master List';

        // Supprimé : récupération des income head
        // $expnseHead          = $this->incomehead_model->get();
        // $data['incheadlist'] = $expnseHead;

        $journal_comptable  = $this->journal_model->get();
        $data['journal']    = $journal_comptable;

        // Supprimé : validation du champ inc_head_id
        // $this->form_validation->set_rules('inc_head_id', $this->lang->line('income_head'), 'trim|required|xss_clean');

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
                'id'             => $id,
                // Supprimé : 'inc_head_id' => $this->input->post('inc_head_id'),
                'name'           => $this->input->post('name'),
                'user'           => $this->input->post('user'),
                'date'           => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'amount'         => $this->input->post('amount'),
                'amount_re'      => $this->input->post('amount_re'),
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

    public function edit_old($id)
    {
        if (!$this->rbac->hasPrivilege('caisse', 'can_edit')) {
            access_denied();
        }
        $data['title']       = 'Edit Fees Master';
        $data['id']          = $id;
        $income              = $this->income_model->got($id);
        $data['income']      = $income;
        $data['title_list']  = 'Fees Master List';
        $expnseHead          = $this->incomehead_model->get();
        $data['incheadlist'] = $expnseHead;

        $journal_comptable          = $this->journal_model->get();
        $data['journal'] = $journal_comptable;
      //  $this->form_validation->set_rules('inc_head_id', $this->lang->line('income_head'), 'trim|required|xss_clean');
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
                'amount_re'      => $this->input->post('amount_re'),
                'invoice_no'  => $this->input->post('invoice_no'),
                'note'        => $this->input->post('description'),
                'est_actif' => $this->input->post('est_actif')  ? 1 : 0,
                'type_operation'   => $this->input->post('type_operation'),
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
    public function transfer_form()
    {
        $this->session->set_userdata('sub_menu', 'income/transfer_amount');

        $data['incomelist'] = $this->income_model->getcaisse(); // Toutes les caisses
        $this->load->view('layout/header');
        $this->load->view('admin/income/transfer_form', $data);
        $this->load->view('layout/footer');
    }

    public function transfer_amount()
    {
        // Activer l'affichage des erreurs
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        // Initialiser la réponse JSON
        header('Content-Type: application/json');

        try {
            // Debug: Log des données reçues
            log_message('debug', '=== TRANSFERT - Données POST ===');
            log_message('debug', print_r($_POST, true));

            // Récupérer les données POST
            $from_type = $this->input->post('from_type', true) ?: 'caisse';
            $from_id = (int) $this->input->post('from_id', true);
            $to_type = $this->input->post('to_type', true) ?: 'caisse';
            $to_id = (int) $this->input->post('to_id', true);
            $amount = floatval($this->input->post('amount', true));

            log_message('debug', "Données extraites: from_type=$from_type, from_id=$from_id, to_type=$to_type, to_id=$to_id, amount=$amount");

            // Validation basique
            if ($from_id <= 0 || $to_id <= 0 || $amount <= 0) {
                throw new Exception('Données invalides. Vérifiez les champs.');
            }

            if ($from_type == $to_type && $from_id == $to_id) {
                throw new Exception('Impossible de transférer vers le même compte.');
            }

            // Récupérer les noms des comptes
            $from_name = '';
            $to_name = '';
            $from_balance = 0;
            $to_balance = 0;

            // === COMPTE SOURCE ===
            if ($from_type == 'caisse') {
                $from_account = $this->income_model->getcaisse($from_id);
                if (!$from_account) {
                    throw new Exception('Caisse source introuvable.');
                }
                $from_balance = floatval($from_account->amount_re ?? 0);
                $from_name = $from_account->name ?? 'Caisse inconnue';
                log_message('debug', "Caisse source: $from_name, solde: $from_balance");
            } else {
                $from_account = $this->db->where('id', $from_id)->get('banks')->row();
                if (!$from_account) {
                    throw new Exception('Compte bancaire source introuvable.');
                }
                $from_balance = floatval($from_account->balance ?? 0);
                $from_name = $from_account->name ?? 'Banque inconnue';
                log_message('debug', "Banque source: $from_name, solde: $from_balance");
            }

            // === COMPTE DESTINATION ===
            if ($to_type == 'caisse') {
                $to_account = $this->income_model->getcaisse($to_id);
                if (!$to_account) {
                    throw new Exception('Caisse destination introuvable.');
                }
                $to_balance = floatval($to_account->amount_re ?? 0);
                $to_name = $to_account->name ?? 'Caisse inconnue';
                log_message('debug', "Caisse destination: $to_name, solde: $to_balance");
            } else {
                $to_account = $this->db->where('id', $to_id)->get('banks')->row();
                if (!$to_account) {
                    throw new Exception('Compte bancaire destination introuvable.');
                }
                $to_balance = floatval($to_account->balance ?? 0);
                $to_name = $to_account->name ?? 'Banque inconnue';
                log_message('debug', "Banque destination: $to_name, solde: $to_balance");
            }

            // Vérifier le solde
            if ($from_balance < $amount) {
                throw new Exception('Solde insuffisant. Disponible: ' . number_format($from_balance, 2, ',', ' ') . ' FCFA');
            }

            // Calculer nouveaux soldes
            $new_from_balance = $from_balance - $amount;
            $new_to_balance = $to_balance + $amount;
            $reference = 'TRF-' . date('YmdHis') . '-' . rand(100, 999);

            // Démarrer transaction
            $this->db->trans_begin();
            log_message('debug', 'Transaction DB démarrée');

            // === 1. COMPTE SOURCE ===
            if ($from_type == 'caisse') {
                // Mettre à jour la caisse
                $this->db->where('id', $from_id)->update('income', [
                    'amount_re' => $new_from_balance,
                   // 'amount' => $new_from_balance,
                    //'total_sorties' => ($from_account->total_sorties ?? 0) + $amount,
                    'last_operation_date' => date('Y-m-d H:i:s')
                ]);

                // Opération caisse (sortie)
                $op_from = [
                    'date' => date('Y-m-d H:i:s'),
                    'caisse_id' => $from_id,
                    'designation' => "Transfert vers $to_name",
                    'montant' => $amount,
                    'entree' => 0,
                    'sortie' => $amount,
                    'type_operation' => 'sortie',
                    'user' => $this->customlib->getAdminSessionUserName(),
                    'solde_avant_operation' => $from_balance,
                    'solde_apres_operation' => $new_from_balance,
                    'reference' => $reference,
                    'deleted' => 'no',
                    'est_active' => 'yes',
                    'user' => $this->customlib->getAdminSessionUserName(),
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $this->db->insert('operation_caisse', $op_from);
                $op_from_id = $this->db->insert_id();

                // Mouvement
                $mov_from = [
                    'type_mouvement' => 'sortie',
                    'montant' => $amount,
                    'description' => "Transfert vers $to_name",
                    'user' => $this->customlib->getAdminSessionUserName(),
                    'reference' => $reference,
                    'date_mouvement' => date('Y-m-d H:i:s'),
                    'mode_paiement' => 'Virement',
                    'solde_avant_operation' => $from_balance,
                    'solde_apres_operation' => $new_from_balance,
                    'operation_id' => $op_from_id,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $this->db->insert('mouvements', $mov_from);


            } else {
                // Banque source
                $this->db->where('id', $from_id)->update('banks', [
                    'balance' => $new_from_balance
                ]);

                $bank_from = [
                    'bank_id' => $from_id,
                    'date' => date('Y-m-d H:i:s'),
                    'transaction_type' => 'Virement sortant',
                    'designation' => 'Débit',
                    'name' => "Transfert vers $to_name",
                    'nom' => $this->customlib->getAdminSessionUserName(),
                    'amount' => $amount,
                    'reference' => $reference,
                    'payment_mode' => 'Virement',
                    'note' => "Transfert vers $to_name",
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $this->db->insert('bank', $bank_from);
            }

            // === 2. COMPTE DESTINATION ===
            if ($to_type == 'caisse') {
                // Mettre à jour la caisse
                $this->db->where('id', $to_id)->update('income', [
                    'amount_re' => $new_to_balance,
                    //'amount' => $new_to_balance,
                  //  'total_entrees' => ($to_account->total_entrees ?? 0) + $amount,
                    'last_operation_date' => date('Y-m-d H:i:s')
                ]);

                // Opération caisse (entrée)
                $op_to = [
                    'date' => date('Y-m-d H:i:s'),
                    'caisse_id' => $to_id,
                    'designation' => "Transfert de $from_name",
                    'user' => $this->customlib->getAdminSessionUserName(),
                    'montant' => $amount,
                    'entree' => $amount,
                    'sortie' => 0,
                    'type_operation' => 'entrée',
                    'solde_avant_operation' => $to_balance,
                    'solde_apres_operation' => $new_to_balance,
                    'reference' => $reference,
                    'deleted' => 'no',
                    'est_active' => 'yes',
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $this->db->insert('operation_caisse', $op_to);
                $op_to_id = $this->db->insert_id();

                // Mouvement
                $mov_to = [
                    'type_mouvement' => 'entree',
                    'montant' => $amount,
                    'description' => "Transfert de $from_name",
                    'user' => $this->customlib->getAdminSessionUserName(),
                    'reference' => $reference,
                    'date_mouvement' => date('Y-m-d H:i:s'),
                    'mode_paiement' => 'Virement',
                    'solde_avant_operation' => $to_balance,
                    'solde_apres_operation' => $new_to_balance,
                    'operation_id' => $op_to_id,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $this->db->insert('mouvements', $mov_to);

                 } else {
                // Banque destination
                $this->db->where('id', $to_id)->update('banks', [
                    'balance' => $new_to_balance
                ]);

                $bank_to = [
                    'bank_id' => $to_id,
                    'date' => date('Y-m-d H:i:s'),
                    'transaction_type' => 'Virement entrant',
                    'nom' => $this->customlib->getAdminSessionUserName(),
                    'designation' => 'Crédit',
                    'name' => "Transfert de $from_name",
                    'amount' => $amount,
                    'reference' => $reference,
                    'payment_mode' => 'Virement',
                    'note' => "Transfert de $from_name",
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $this->db->insert('bank', $bank_to);
            }

            // === 3. ENREGISTRER LE TRANSFERT ===
            $transfer_record = [
                'from_type' => $from_type,
                'from_id' => $from_id,
                'from_name' => $from_name,
                'to_type' => $to_type,
                'to_id' => $to_id,
                'to_name' => $to_name,
                'amount' => $amount,
                'reference' => $reference,
                'date' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s')
            ];

            // Vérifier si la table existe et a les colonnes
            if ($this->db->table_exists('transfert_caisse')) {
                // Ajouter seulement les colonnes qui existent
                $fields = $this->db->list_fields('transfert_caisse');
                $data_to_insert = [];

                foreach ($transfer_record as $key => $value) {
                    if (in_array($key, $fields)) {
                        $data_to_insert[$key] = $value;
                    }
                }

                $this->db->insert('transfert_caisse', $data_to_insert);
                $transfer_id = $this->db->insert_id();
                log_message('debug', "Transfert enregistré ID: $transfer_id");
            }

            // === 4. VALIDER ===
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $error = $this->db->error();
                throw new Exception('Erreur base de données: ' . ($error['message'] ?? 'Inconnue'));
            }

            $this->db->trans_commit();
            log_message('debug', 'Transaction validée avec succès');

            // Réponse de succès
            echo json_encode([
                'status' => 'success',
                'message' => '✅ Transfert réussi ! ' .
                    number_format($amount, 0, ',', ' ') . ' FCFA transférés de ' .
                    $from_name . ' vers ' . $to_name .
                    ' (Réf: ' . $reference . ')',
                'reference' => $reference,
                'new_from_balance' => $new_from_balance,
                'new_to_balance' => $new_to_balance
            ]);

        } catch (Exception $e) {
            // Annuler la transaction si active
            if ($this->db->trans_status() !== FALSE) {
                $this->db->trans_rollback();
            }

            log_message('error', 'ERREUR TRANSFERT: ' . $e->getMessage());
            log_message('error', 'Trace: ' . $e->getTraceAsString());

            echo json_encode([
                'status' => 'error',
                'message' => '❌ ' . $e->getMessage()
            ]);
        }
    }

    public function gettransferlist()
    {
        // Vérifier si les colonnes existent
        $columns_exist = $this->db->field_exists('from_type', 'transfert_caisse');

        if ($columns_exist) {
            // Nouvelle méthode avec support des banques
            $m = $this->db
                ->select('t.id, t.amount, t.date, t.from_type, t.to_type, 
                     COALESCE(fc.name, fb.name) as from_account,
                     COALESCE(tc.name, tb.name) as to_account')
                ->from('transfert_caisse as t')
                ->join('income as fc', 'fc.id = t.from_id AND t.from_type = "caisse"', 'left')
                ->join('income as tc', 'tc.id = t.to_id AND t.to_type = "caisse"', 'left')
                ->join('banks as fb', 'fb.id = t.from_id AND t.from_type = "bank"', 'left')
                ->join('banks as tb', 'tb.id = t.to_id AND t.to_type = "bank"', 'left')
                ->order_by('t.date', 'DESC')
                ->get()
                ->result();
        } else {
            // Ancienne méthode (uniquement caisses)
            $m = $this->db
                ->select('t.id, t.amount, t.date, fc.name as from_account, tc.name as to_account')
                ->from('transfert_caisse as t')
                ->join('income as fc', 'fc.id = t.from_id', 'left')
                ->join('income as tc', 'tc.id = t.to_id', 'left')
                ->order_by('t.date', 'DESC')
                ->get()
                ->result();

            // Ajouter les types par défaut
            foreach ($m as &$row) {
                $row->from_type = 'caisse';
                $row->to_type = 'caisse';
            }
        }

        $dt_data = [];
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();

        foreach ($m as $key => $value) {
            // Déterminer le type de transfert
            $transfer_type = 'Caisse → Caisse';
            if ($value->from_type == 'bank' && $value->to_type == 'bank') {
                $transfer_type = 'Banque → Banque';
            } elseif ($value->from_type == 'bank' && $value->to_type == 'caisse') {
                $transfer_type = 'Banque → Caisse';
            } elseif ($value->from_type == 'caisse' && $value->to_type == 'bank') {
                $transfer_type = 'Caisse → Banque';
            }

            $row = [
                'from_account' => $value->from_account ?: 'N/A',
                'to_account' => $value->to_account ?: 'N/A',
                'amount' => $value->amount,
                'date' => date('d/m/Y H:i', strtotime($value->date)),
                'transfer_type' => $transfer_type,
                'from_type' => $value->from_type,
                'to_type' => $value->to_type
            ];

            $dt_data[] = $row;
        }

        // Format DataTables
        $json_data = [
            "draw" => intval($this->input->get('draw') ?: 1),
            "recordsTotal" => count($dt_data),
            "recordsFiltered" => count($dt_data),
            "data" => $dt_data,
        ];

        echo json_encode($json_data);
    }






    public function incomeSearch()
    {
        if (!$this->rbac->hasPrivilege('search_due_fees', 'can_view')) {
            access_denied();
        }
        $data['searchlist'] = $this->customlib->get_searchtype();
        $this->session->set_userdata('top_menu', 'Caisse');
        $this->session->set_userdata('sub_menu', 'income/index');
        $data['search_type'] = '';
        $data['title']       = 'Search Income';
        $this->load->view('layout/header', $data);
        $this->load->view('admin/income/incomeSearch', $data);
        $this->load->view('layout/footer', $data);

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


    //-----------------------------------------------
    // AFFICHER UN FORMULAIRE DE REAPPROVISIONNEMENT
    //-----------------------------------------------
    public function formIncrease()
    {
        // Try to get any row's id sent
        $data['rowID'] = ( ! empty($this->input->post('rowID')) && (int)$this->input->post('rowID') > 0) ? (int)$this->input->post('rowID') : 0;

        // Load the form view with all the data required
        $this->load->view('admin/income/increase_form', $data);



    }

    public function delete_increase($id)
    {
        // Étape 1 : Récupérer les infos de l'appro
        $appro = $this->Income_processing_model->get_appro_by_id($id);

        if ($appro) {
            $montant = $appro['amount']; // ou le vrai nom du champ
            $income_id = $appro['income_id']; // doit exister dans ta table appro

            // Étape 2 : Déduire de amount_re dans income
            $this->Income_processing_model->deduire_montant_income($income_id, $montant);

            // Étape 3 : Supprimer l’appro
            $this->Income_processing_model->delete_increase($id);
        }

        redirect('admin/income/index');
    }


    public function delete_increase_old($id){



        $this->Income_processing_model->delete_increase($id);



        redirect('admin/income/index');

    }

    public function EditIncrease()
    {
        // Try to get any row's id sent
        $data['rowID'] = ( ! empty($this->input->post('rowID')) && (int)$this->input->post('rowID') > 0) ? (int)$this->input->post('rowID') : 0;

        // Load the form view with all the data required
        $this->load->view('admin/income/increase_form', $data);



    }
    // End function
    //--------------------------------------------------
    public function deletd()
    {
        if (!$this->rbac->hasPrivilege('caisse', 'can_add')) {
            access_denied();
        }

        // Récupération des données depuis les entrées POST

        // Vérification des champs obligatoires
        if (empty($rowId) || empty($amount) || empty($reason)) {
            $response = [
                'type'    => 'danger',
                'message' => 'Tous les champs marqués de ce symbole <code>*</code> sont obligatoires.',
            ];
            echo json_encode($response);
            return;
        }

        // Execute the SQL query and store all the results
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


        // var_dump($oldRow);
        // exit;

        // Exemple de calculs avant la modification
        $newAmount = (float)$oldRow->amount + (float)$amount; // Exemple : Ajouter le montant existant à celui fourni
        $newAmountRe = (float)$oldRow->amount_re + (float)$amount; // Exemple : Ajouter le montant existant à celui fourni


        // var_dump($newAmount);
        // var_dump($newAmountRe);
        // exit;


        // Mise à jour de la ligne dans la base de données
        $rowUpdated = $this->income_model->updateP(['id' => $rowId], [
            'amount_re' => $newAmountRe
        ]);

        // var_dump($rowUpdated);
        // exit;


        if ($rowUpdated) {
            // Mise à jour de la ligne dans la base de données
            $this->Income_processing_model->createP([
                'income_id' => $rowId,
                'amount'    => $amount,
                'reason'    => $reason,
                'raison'    => $raison,
                'user'    => $user,
                'date'    => $date,
                /* 'created_at'=> Date('Y-m-d')*/
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

        // Réponse JSON
        echo json_encode($response);
    }

    //----------------------------------------
    // UPDATE A BANK ENTRY IN THE DATABASE
    //----------------------------------------
    public function setIncrease()
    {
        if (!$this->rbac->hasPrivilege('caisse', 'can_add')) {
            access_denied();
        }

        // Récupération des données depuis les entrées POST
        $rowId = $this->input->post('rowId') ? trim($this->input->post('rowId')) : 0;
        $amount = $this->input->post('amount') ? floatval($this->input->post('amount')) : 0;
        $reason = $this->input->post('reason') ? trim($this->input->post('reason')) : '';
        $raison = $this->input->post('raison') ? trim($this->input->post('raison')) : '';
        $user = $this->input->post('user') ? trim($this->input->post('user')) : '';
        $date = $this->input->post('date') ? trim($this->input->post('date')) : '';

        // Vérification des champs obligatoires
        if (empty($rowId) || empty($amount) || empty($reason)) {
            $response = [
                'type'    => 'danger',
                'message' => 'Tous les champs marqués de ce symbole <code>*</code> sont obligatoires.',
            ];
            echo json_encode($response);
            return;
        }

        // Execute the SQL query and store all the results
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


        // var_dump($oldRow);
        // exit;

        // Exemple de calculs avant la modification
        $newAmount = (float)$oldRow->amount + (float)$amount; // Exemple : Ajouter le montant existant à celui fourni
        $newAmountRe = (float)$oldRow->amount_re + (float)$amount; // Exemple : Ajouter le montant existant à celui fourni


        // var_dump($newAmount);
        // var_dump($newAmountRe);
        // exit;


        // Mise à jour de la ligne dans la base de données
        $rowUpdated = $this->income_model->updateP(['id' => $rowId], [
            'amount_re' => $newAmountRe
        ]);

        // var_dump($rowUpdated);
        // exit;


        if ($rowUpdated) {
            // Mise à jour de la ligne dans la base de données
            $this->Income_processing_model->createP([
                'income_id' => $rowId,
                'amount'    => $amount,
                'reason'    => $reason,
                'raison'    => $raison,
                'user'    => $user,
                'date'    => $date,
                /* 'created_at'=> Date('Y-m-d')*/
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

        // Réponse JSON
        echo json_encode($response);
    }



    //-----------------------------------------------
    // AFFICHER UN FORMULAIRE DE REAPPROVISIONNEMENT
    //-----------------------------------------------
    public function listIncrease()
    {
        if (!$this->rbac->hasPrivilege('caisse', 'can_edit')) {
            access_denied();
        }
        // Try to get any row's id sent
        $rowID = ( ! empty($this->input->post('rowID')) && (int)$this->input->post('rowID') > 0) ? (int)$this->input->post('rowID') : 0;

        $join  = [
            'table'     => 'income',
            'condition' => 'income.id = income_processing.income_id',
            'type'      => 'inner'
        ];

        // Définissez tous les champs à sélectionner dans la requête suivante
        $select = 'income.name,income.user,  income_processing.id, income_processing.amount, income_processing.reason, income_processing.date, income_processing.raison';

        // Définissez toutes les conditions de where en utilisant "AND" pour la requête suivante
        $where  = [
            'income_id' => $rowID,
        ];

        // Exécutez la requête SQL et stockez tous les résultats
        $data['rows'] =

            $this->db->select($select)

            ->from('income_processing')

             ->join($join['table'], $join['condition'], $join['type'])
            ->where($where)
          // ->where('income_processing.deleted', '1')
            ->get()
            ->result();
        $this->db->flush_cache();

        // var_dump($rows);


        // Load the form view with all the data required
        $this->load->view('admin/income/increaseList', $data);

    } // End function
    //--------------------------------------------------

}