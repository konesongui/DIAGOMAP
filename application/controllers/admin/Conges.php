<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Conges extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if (!$this->rbac->hasPrivilege('expense_head', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Conges');
        $this->session->set_userdata('sub_menu', 'conges/index');
        $data['title'] = 'Expense Head List';

        $this->load->view('layout/header', $data);
        $this->load->view('admin/staff/conges', $data);
        $this->load->view('layout/footer', $data);
    }

    public function ajaxSearch()
    {

        $expense_head = $this->comptes_model->getDatatableExpenseHead();

        $expense_head = json_decode($expense_head);
        $dt_data      = array();

        if (!empty($expense_head->data)) {

            if ($this->rbac->hasPrivilege('expense_head', 'can_edit')) {
                $permission_edit = true;
            }

            if ($this->rbac->hasPrivilege('expense_head', 'can_delete')) {
                $permission_delete = true;
            }

            foreach ($expense_head->data as $exhead_key => $exhead_value) {
                $action = "";
                if ($permission_edit) {
                    $action .= "<a data-placement='left' href='" . site_url('admin/expensehead/edit/' . $exhead_value->id) . "' class='btn btn-default btn-xs'  data-toggle='tooltip' title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                }
                if ($permission_delete) {
                    $action .= "<a data-placement='left' href='".site_url('admin/expensehead/delete/'.$exhead_value->id)."' class='btn btn-default btn-xs'  data-toggle='tooltip' title='".$this->lang->line('delete')."' onclick='return confirm('".$this->lang->line('delete_confirm')."');'><i class='fa fa-remove'></i></a>";
                }
                $row           = array();
                $popover_title = "<div class='fee_detail_popover' style='display: none'>";
                if ($exhead_value->description == "") {
                    $popover_title .= "<p class='text text-danger'>" . $this->lang->line('no_description') . "</p>";
                } else {
                    $popover_title .= "<p class='text text-info'>" . $exhead_value->description . "</p>";

                }
                $popover_title .= "</div>";
                $title = "<a href='#' data-toggle='popover' class='detail_popover'>" . $exhead_value->exp_category . "</a>";
                $nom = "<a href='#' data-toggle='popover' class='detail_popover'>" . $exhead_value->nom . "</a>";
                $email = "<a href='#' data-toggle='popover' class='detail_popover'>" . $exhead_value->email . "</a>";
                $telephone = "<a href='#' data-toggle='popover' class='detail_popover'>" . $exhead_value->telephone . "</a>";
                $adresse = "<a href='#' data-toggle='popover' class='detail_popover'>" . $exhead_value->adresse . "</a>";
                $logo = "<a href='#' data-toggle='popover' class='detail_popover'>" . $exhead_value->logo . "</a>";
                $forfait = "<a href='#' data-toggle='popover' class='detail_popover'>" . $exhead_value->forfait . "</a>";
                $date_debut = "<a href='#' data-toggle='popover' class='detail_popover'>" . $exhead_value->date_debut . "</a>";
                $date_expiration = "<a href='#' data-toggle='popover' class='detail_popover'>" . $exhead_value->date_expiration . "</a>";
                $statut = "<a href='#' data-toggle='popover' class='detail_popover'>" . $exhead_value->statut . "</a>";

                $row[]     = $nom;
                $row[]     = $email;
                $row[]     = $telephone;
                $row[]     = $adresse;
                $row[]     = $logo;
                $row[]     = $forfait;
                $row[]     = $date_debut;
                $row[]     = $date_expiration;
                $row[]     = $statut;
                $row[]     = $action;

                $dt_data[] = $row;
            }

        }
        $json_data = array(
            "draw"            => intval($expense_head->draw),
            "recordsTotal"    => intval($expense_head->recordsTotal),
            "recordsFiltered" => intval($expense_head->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data);

    }

    public function view($id)
    {
        if (!$this->rbac->hasPrivilege('expense_head', 'can_view')) {
            access_denied();
        }
        $data['title']    = 'Expense Head List';
        $category         = $this->comptes_model->get($id);
        $data['category'] = $category;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/comptes/comptesShow', $data);
        $this->load->view('layout/footer', $data);
    }

    public function delete($id)
    {
        if (!$this->rbac->hasPrivilege('expense_head', 'can_delete')) {
            access_denied();
        }
        $data['title'] = 'Expense Head List';
        $this->comptes_model->remove($id);
        redirect('admin/comptes/index');
    }

    public function create()
    {
        if (!$this->rbac->hasPrivilege('expense_head', 'can_add')) {
            access_denied();
        }
        $data['title']        = 'Add Expense Head';
        $category_result      = $this->conges_model->get();
        $data['categorylist'] = $category_result;
        $this->form_validation->set_rules('expensehead', $this->lang->line('expense_head'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/staff/conges', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'user_id' => session()->get('id'),
                'date_debut' => $this->input->post('date_debut'),
                'date_fin' => $this->input->post('date_fin'),
                'type_conge' => $this->input->post('type_conge'),
                'motif' => $this->input->post('motif'),
                'statut' => 'attente',
            );
            $this->conges_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/conges/index');
        }
    }

    public function edit($id)
    {
        if (!$this->rbac->hasPrivilege('expense', 'can_edit')) {
            access_denied();
        }
        $data['title']        = 'Edit Expense Head';
        $category_result      = $this->expensehead_model->get();
        $data['categorylist'] = $category_result;
        $data['id']           = $id;
        $category             = $this->expensehead_model->get($id);
        $data['expensehead']  = $category;
        $this->form_validation->set_rules('expensehead', $this->lang->line('expense_head'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/expensehead/expenseheadEdit', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $data = array(
                'id'           => $id,
                'exp_category' => $this->input->post('expensehead'),
                'description'  => $this->input->post('description'),
            );
            $this->expensehead_model->add($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('update_message') . '</div>');
            redirect('admin/expensehead/index');
        }
    }

}
