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
    }

    public function index()
    {

        if (!$this->rbac->hasPrivilege('caisse', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Caisse');
        $this->session->set_userdata('sub_menu', 'income/index');
        $data['title']      = 'Add Income';
        $data['title_list'] = 'Recent Incomes';
        $this->form_validation->set_rules('inc_head_id', $this->lang->line('income_head'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('amount', $this->lang->line('amount'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('documents', $this->lang->line('documents'), 'callback_handle_upload');
        if ($this->form_validation->run() == false) {

        } else {
            $data = array(
                'inc_head_id' => $this->input->post('inc_head_id'),
                'name'        => $this->input->post('name'),
                'user'        => $this->input->post('user'),
                'date'        => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'amount'      => $this->input->post('amount'),
                'amount_re'   => $this->input->post('amount'),
                'invoice_no'  => $this->input->post('invoice_no'),
                'note'        => $this->input->post('description'),
                'documents'   => $this->input->post('documents'),
                'status'   => $this->input->post('status'),

            );
            $insert_id = $this->income_model->add($data);
            if (isset($_FILES["documents"]) && !empty($_FILES['documents']['name'])) {
                $fileInfo = pathinfo($_FILES["documents"]["name"]);
                $img_name = $insert_id . '.' . $fileInfo['extension'];
                move_uploaded_file($_FILES["documents"]["tmp_name"], "./uploads/school_income/" . $img_name);
                $data_img = array('id' => $insert_id, 'documents' => 'uploads/school_income/' . $img_name);
                $this->income_model->add($data_img);
            }
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/income/index');
        }


        // $data['incomeTotal']  = $this->income_model->getTotalIncome();

        // var_dump($data['incomeTotal']);
        // exit;

        $income_result       = $this->income_model->get();
        $data['incomelist']  = $income_result;
        $data['incomeTotal']  = $this->income_model->getTotalIncome();
        $incomeHead          = $this->incomehead_model->get();
        $data['incheadlist'] = $incomeHead;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/income/incomeList', $data);
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
        $data['title']       = 'Edit Fees Master';
        $data['id']          = $id;
        $income              = $this->income_model->got($id);
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
                'amount_re'      => $this->input->post('amount_re'),
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
        $m               = $this->income_model->getincomelist();
        $m               = json_decode($m);
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $dt_data         = array();
        if (!empty($m->data)) {
            foreach ($m->data as $key => $value) {
                $editbtn     = '';
                $deletebtn   = '';
                $documents   = '';
                $increase    = '';
                $viewIncrease = '';
                $inc_head_id = $value->inc_head_id;
                $arr1        = str_split($inc_head_id);

                $title = "<a href='#' tabindex='0' data-toggle='popover' class='detail_popover'>" . $value->name . "</a>  ";
                if ($value->documents) {

                    $documents = "<a href='" . base_url() . "admin/income/download/" . $value->documents . "'   class='btn btn-default btn-xs'  data-toggle='tooltip' data-placement='left' title='" . $this->lang->line('download') . "'><i class='fa fa-download'></i></a>";
                }

                if ($this->rbac->hasPrivilege('caisse', 'can_edit')) {
                    $editbtn = "<a href='" . base_url() . "admin/income/edit/" . $value->id . "'   class='btn btn-default btn-xs'  data-toggle='tooltip' data-placement='left' title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                }
                if ($this->rbac->hasPrivilege('caisse', 'can_delete')) {
                    $deletebtn = '';
                    $deletebtn = "<a onclick='return confirm(" . '"' . $this->lang->line('delete_confirm') . '"' . "  )' href='" . base_url() . "admin/income/delete/" . $value->id . "' class='btn btn-default btn-xs' data-placement='left' title='" . $this->lang->line('delete') . "' data-toggle='tooltip'><i class='fa fa-trash'></i></a>";
                }

                if ($value->documents) {
                    $document = "<a data-placement='left' href='" . base_url() . "admin/income/download/" . $value->documents . "' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('download') . "'>
                         <i class='fa fa-download'></i> </a>";
                }

                $increase = '<a data-toggle="modal" data-target="#increaseForm" data-row-id = "' . $value->id . '" data-toggle="tooltip" data-placement="left" title="Réapprovisionner" type="button" class="btn btn-sm text-dark btn-icon increaseAmount"><i class="fa fa-plus text-dark font-size-sm"></i></a>';

                $viewIncrease = '<a data-toggle="modal" data-target="#viewIncreaseList" data-row-id = "' . $value->id . '" data-toggle="tooltip" data-placement="left" title="Liste réapprovisionnement" type="button" class="btn btn-sm text-dark btn-icon viewIncrease"><i class="fa fa-eye text-dark font-size-sm"></i></a>';

                $row   = array();
                $row[] = $title;

                /* if ($value->note == "") {
                     $row[] = $this->lang->line('no_description');
                 } else {
                     $row[] = $value->note;
                 }*/

                /* $row[]     = $value->invoice_no;*/
                $row[]     = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value->date));
                $row[]     = $value->income_category;
                $row[]     = $value->amount .$currency_symbol;
                $row[]     = $value->amount_re .$currency_symbol;
                /*$row[]     = $value->user;*/

                if ($value->status=="Ouvert") {
                    $row[]     =  "<h6><span class='label label-warning' style='background-color: #ff9801 !important; border-radius: 2px'>Ouverte<span/></h6>";
                }
                else{
                    $row[]     = "<h6><span class='label label-warning' style='background-color: #e91e63 !important; border-radius: 2px'>Fermée<span/></h6>";
                }

                //$row[]     = $value->status;

                $row[]     = $documents . ' ' . $editbtn . ' '. $increase.' '. $viewIncrease .' '. $deletebtn;
                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw"            => intval($m->draw),
            "recordsTotal"    => intval($m->recordsTotal),
            "recordsFiltered" => intval($m->recordsFiltered),
            "data"            => $dt_data,
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

    public function delete_increase($id){



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
        $data['rows'] = $this->db->select($select)
            ->from('income_processing')
            ->join($join['table'], $join['condition'], $join['type'])
            ->where($where)
            ->get()
            ->result();
        $this->db->flush_cache();

        // var_dump($rows);


        // Load the form view with all the data required
        $this->load->view('admin/income/increaseList', $data);

    } // End function
    //--------------------------------------------------

}