<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Clients extends Admin_Controller {

    function __construct() {
        parent::__construct();

        $this->load->helper('url');
    }



    function index() {
        if (!$this->rbac->hasPrivilege('clients', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Inventory');
        $this->session->set_userdata('sub_menu', 'clients/index');
        $data['title'] = 'Item Supplier List';
        $itemsupplier_result = $this->clients_model->get();
        $data['itemsupplierlist'] = $itemsupplier_result;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/hub/hub', $data);
        $this->load->view('layout/footer', $data);
    }

    public function get_clients() {
        if (!$this->rbac->hasPrivilege('clients', 'can_view')) {
            access_denied();
        }

        $clients = $this->clients_model->get();

        $data = [];
        foreach ($clients as $c) {
            $row = [];
            $row['item_supplier'] = $c['item_supplier'];
            $row['contact_person_name'] = $c['contact_person_name'];
            $row['phone'] = $c['phone'];
            $row['email'] = $c['email'];
            $row['ville'] = $c['ville'];
            $row['address'] = $c['address'];
            $row['comptec'] = $c['comptec'];

            // Actions (boutons)
            $actions = '';
            if ($this->rbac->hasPrivilege('clients', 'can_edit')) {
                $actions .= '<a href="'.base_url("admin/clients/edit/".$c['id']).'" class="btn btn-default btn-xs"><i class="fa fa-pencil"></i></a> ';
            }
            if ($this->rbac->hasPrivilege('clients', 'can_delete')) {
                $actions .= '<a href="'.base_url("admin/clients/delete/".$c['id']).'" class="btn btn-default btn-xs" onclick="return confirm(\'Confirmer la suppression ?\')"><i class="fa fa-remove"></i></a>';
            }
            $row['action'] = $actions;

            $data[] = $row;
        }

        echo json_encode(['data' => $data]);
    }


    public function import()
    {
        $data['field'] = array(
            "item_supplier"                 => "item_supplier",
            "contact_person_name"               => "contact_person_name",
            "phone"                => "phone",
            "email"                => "email",
            "address"              => "address",
            "comptec"              => "comptec",

        );
        $roles               = $this->role_model->get();
        $data["roles"]       = $roles;
        $designation         = $this->staff_model->getStaffDesignation();
        $data["designation"] = $designation;
        $department          = $this->staff_model->getDepartment();
        $data["department"]  = $department;

        $this->form_validation->set_rules('file', $this->lang->line('image'), 'callback_handle_csv_upload');
        $this->form_validation->set_rules('role', $this->lang->line('role'), 'required');

        if ($this->form_validation->run() == false) {
            $this->load->view("layout/header", $data);
            $this->load->view("admin/clients/import/import", $data);
            $this->load->view("layout/footer", $data);
        } else {

            if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {

                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                if ($ext == 'csv') {

                    $file = $_FILES['file']['tmp_name'];
                    $this->load->library('CSVReader');
                    $result = $this->csvreader->parse_file($file);

                    $rowcount = 0;

                    if (!empty($result)) {

                        foreach ($result as $r_key => $r_value) {

                            $check_exists      = $this->staff_model->import_check_data_exists($result[$r_key]['name'], $result[$r_key]['employee_id']);
                            $check_emailexists = $this->staff_model->import_check_email_exists($result[$r_key]['name'], $result[$r_key]['employee_id']);

                            if ($check_exists == 0 && $check_emailexists == 0) {

                                $result[$r_key]['item_supplier']                 = $this->encoding_lib->toUTF8($result[$r_key]['item_supplier']);
                                $result[$r_key]['phone']              = $this->encoding_lib->toUTF8($result[$r_key]['phone']);
                                $result[$r_key]['email']          = $this->encoding_lib->toUTF8($result[$r_key]['email']);
                                $result[$r_key]['address']          = $this->encoding_lib->toUTF8($result[$r_key]['address']);
                                $result[$r_key]['contact_person_name']           = $this->encoding_lib->toUTF8($result[$r_key]['contact_person_name']);
                                $result[$r_key]['comptec'] = $this->encoding_lib->toUTF8($result[$r_key]['comptec']);

                                $password = $this->role->get_random_password($chars_min = 6, $chars_max = 6, $use_upper_case = false, $include_numbers = true, $include_special_chars = false);

                                $result[$r_key]['password'] = $this->enc_lib->passHashEnc($password);

                                $role_array = array('role_id' => $this->input->post('role'), 'staff_id' => 0);

                                $insert_id = $this->staff_model->batchInsert($result[$r_key], $role_array);
                                $staff_id  = $insert_id;
                                if ($staff_id) {

                                    $teacher_login_detail = array('id' => $staff_id, 'credential_for' => 'staff', 'username' => $result[$r_key]['email'], 'password' => $password, 'contact_no' => $result[$r_key]['contact_no'], 'email' => $result[$r_key]['email']);

                                    $this->mailsmsconf->mailsms('login_credential', $teacher_login_detail);
                                }
                                $rowcount++;
                            }
                        } ///Result loop
                    } //Not emprty l

                    $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('records_found_in_CSV_file_total') . $rowcount . $this->lang->line('records_imported_successfully'));
                }
            } else {
                $msg = array(
                    'e' => $this->lang->line('the_file_field_is_required'),
                );
                $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
            }

            $this->session->set_flashdata('msg', '<div class="alert alert-success text-center">' . $this->lang->line('total') . ' ' . count($result) . " " . $this->lang->line('records_found_in_CSV_file_total') . ' ' . $rowcount . ' ' . $this->lang->line('records_imported_successfully') . '</div>');
            redirect('admin/clients/import');
        }
    }




    function delete($id) {
        if (!$this->rbac->hasPrivilege('clients', 'can_delete')) {
            access_denied();
        }
        $data['title'] = 'Item Supplier List';
        $this->clients_model->remove($id);
        redirect('admin/clients/index');
    }


    public function create() {
        if (!$this->rbac->hasPrivilege('clients', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('phone', $this->lang->line('phone'), 'trim|xss_clean');
        $this->form_validation->set_rules('email', $this->lang->line('email'), 'trim|xss_clean|valid_email');

        if ($this->form_validation->run() == FALSE) {
            if ($this->input->is_ajax_request()) {
                echo json_encode(["status" => "error", "message" => validation_errors()]);
                return;
            }
            // Recharge vue en cas d'erreur (non AJAX)
            $data['title'] = 'Add Client';
            $data['itemsupplierlist'] = $this->clients_model->get();
            $this->load->view('layout/header', $data);
            $this->load->view('admin/clients/clientsList', $data);
            $this->load->view('layout/footer', $data);
        } else {
            // MAPPING CORRECT des champs (selon votre formulaire)
            $data = array(
                'item_supplier'         => $this->input->post('name'),
                'contact_person_name'   => $this->input->post('contact_person_name'),
                'phone'                 => $this->input->post('phone'),
                'email'                 => $this->input->post('email'),
                'ville'                 => $this->input->post('ville'),
                'address'               => $this->input->post('address'),
                'comptec'               => $this->input->post('comptec'),          // compte contribuable
                'ncc'                   => $this->input->post('ncc'),              // ncc
                'regime_imposition'     => $this->input->post('regime_imposition'),
                'created_at'            => date('Y-m-d H:i:s'),
            );

            // Insertion + génération automatique du code client (dans le modèle)
            $insert_id = $this->clients_model->add($data);

            // Récupérer le code généré pour l'afficher (optionnel)
            $client = $this->clients_model->get($insert_id);
            $code_client = $client['code_client'];

            if ($this->input->is_ajax_request()) {
                echo json_encode([
                    "status" => "success",
                    "message" => "✅ Client ajouté avec le code : " . $code_client
                ]);
                return;
            }

            $this->session->set_flashdata('msg', '<div class="alert alert-success">✅ Client ajouté – Code : ' . $code_client . '</div>');
            redirect('admin/clients/index');
        }
    }

    function edit($id) {
        if (!$this->rbac->hasPrivilege('clients', 'can_edit')) {
            access_denied();
        }

        $data['title'] = 'Edit Client';
        $data['itemsupplierlist'] = $this->clients_model->get();
        $data['id'] = $id;
        $store = $this->clients_model->get($id);
        $data['itemsupplier'] = $store;

        // Validation : champ 'name' obligatoire (correspond à 'item_supplier')
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            // En cas d'erreur de validation, on recharge la vue d'édition
            $this->load->view('layout/header', $data);
            $this->load->view('admin/clients/clientsEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            // Préparer les données à mettre à jour (seulement les champs présents dans le formulaire)
            $update_data = array(
                'id'                    => $id,
                'item_supplier'         => $this->input->post('name'),
                'contact_person_name'   => $this->input->post('contact_person_name'),
                'phone'  => $this->input->post('phone'),
                'ncc'                   => $this->input->post('ncc'),
                'regime_imposition'     => $this->input->post('regime_imposition'),
                'email'                 => $this->input->post('email'),
                'ville'                 => $this->input->post('ville'),
                'comptec'               => $this->input->post('comptec'),
                'address'               => $this->input->post('address'),
            );

            // Appel à la méthode update() du modèle (à créer si elle n'existe pas)
            $this->clients_model->update($update_data, $id);

            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/clients/index');
        }
    }

    function edit_070526($id) {
        if (!$this->rbac->hasPrivilege('clients', 'can_edit')) {
            access_denied();
        }
        $data['title'] = 'Edit Item Supplier';
        $itemsupplier_result = $this->clients_model->get();
        $data['itemsupplierlist'] = $itemsupplier_result;
        $data['id'] = $id;
        $store = $this->clients_model->get($id);
        $data['itemsupplier'] = $store;

        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/clients/clientsEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {


            $data = array(
                'id' => $id,

                'comptec' => $this->input->post('comptec'),
                'item_supplier' => $this->input->post('name'),
                'ncc' => $this->input->post('ncc'),
                'regime_imposition' => $this->input->post('regime_imposition'),
                'lastname' => $this->input->post('lastname'),
                'phone' => $this->input->post('phone'),
                'contact_person_phone' => $this->input->post('contact_person_phone'),
                'email' => $this->input->post('email'),
                'ville' => $this->input->post('ville'),
                'address' => $this->input->post('address'),
                'contact_person_name' => $this->input->post('contact_person_name'),
                'contact_person_email' => $this->input->post('contact_person_email'),
                'description' => $this->input->post('description'),
            );
            $this->clients_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/clients/index');
        }
    }

}

?>