<?php

class Payroll extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('file');
        $this->config->load("app-config");
        $this->config->load("mailsms");
        $this->config->load("payroll");
        $this->load->library('mailsmsconf');
        $this->config_attendance = $this->config->item('attendence');
        $this->staff_attendance  = $this->config->item('staffattendance');
        $this->payment_mode      = $this->config->item('payment_mode');
        $this->load->model("payroll_model");
        $this->load->model("staff_model");
        $this->load->model('staffattendancemodel');
        $this->payroll_status     = $this->config->item('payroll_status');
        $this->sch_setting_detail = $this->setting_model->getSetting();
        $this->load->library('mailsmsconf');
        $this->load->library('encoding_lib');
        $this->load->library('customlib');
        $this->load->library('Enc_lib');
    }




    public function index()
    {

        if (!$this->rbac->hasPrivilege('staff_payroll', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'HR');
        $this->session->set_userdata('sub_menu', 'admin/payroll');
        $data["staff_id"]            = "";
        $data["name"]                = "";
        $data["month"]               = date("F", strtotime("-sendPayslipByEmail1 month"));
        $data["year"]                = date("Y");
        $data["present"]             = 0;
        $data["absent"]              = 0;
        $data["late"]                = 0;
        $data["half_day"]            = 0;
        $data["holiday"]             = 0;
        $data["leave_count"]         = 0;
        $data["alloted_leave"]       = 0;
        $data["basic"]               = 0;
        $data["payment_mode"]        = $this->payment_mode;
        $user_type                   = $this->staff_model->getStaffRole();
        $data['classlist']           = $user_type;
        $data['monthlist']           = $this->customlib->getMonthDropdown();
        $data['sch_setting']         = $this->sch_setting_detail;
        $data['staffid_auto_insert'] = $this->sch_setting_detail->staffid_auto_insert;
        $submit                      = $this->input->post("search");
        if (isset($submit) && $submit == "search") {

            $month    = $this->input->post("month");
            $year     = $this->input->post("year");
            $emp_name = $this->input->post("name");
            $role     = $this->input->post("role");

            $searchEmployee = $this->payroll_model->searchEmployee($month, $year, $emp_name, $role);

            $data["resultlist"] = $searchEmployee;
            $data["name"]       = $emp_name;
            $data["month"]      = $month;
            $data["year"]       = $year;
        }

        $data["payroll_status"] = $this->payroll_status;
        $this->load->view("layout/header", $data);
        $this->load->view("admin/payroll/stafflist", $data);
        $this->load->view("layout/footer", $data);
    }

    public function paie()
    {

        if (!$this->rbac->hasPrivilege('staff_payroll', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'HR');
        $this->session->set_userdata('sub_menu', 'admin/payroll');
        $data['searchlist']  = $this->customlib->get_searchtype();
        $data['date_type']   = $this->customlib->date_type();
        $data['date_typeid'] = '';

        if (isset($_POST['search_type']) && $_POST['search_type'] != '') {

            $dates               = $this->customlib->get_betweendate($_POST['search_type']);
            $data['search_type'] = $_POST['search_type'];

        } else {

            $dates               = $this->customlib->get_betweendate('this_year');
            $data['search_type'] = '';

        }

        $start_date = date('Y-m-d', strtotime($dates['from_date']));
        $end_date   = date('Y-m-d', strtotime($dates['to_date']));

        $data['label']        = date($this->customlib->getSchoolDateFormat(), strtotime($start_date)) . " " . $this->lang->line('to') . " " . date($this->customlib->getSchoolDateFormat(), strtotime($end_date));
        $data['payment_mode'] = $this->payment_mode;
        $data['sch_setting'] = $this->sch_setting_detail;

        $result              = $this->payroll_model->getbetweenpayrollReport($start_date, $end_date);
        $data['payrollList'] = $result;
        $this->load->view("layout/header", $data);
        $this->load->view("admin/payroll/paie", $data);
        $this->load->view("layout/footer", $data);
    }

    public function create($month, $year, $id)
    {

        $data["staff_id"]            = "";
        $data["basic"]               = "";
        $data["name"]                = "";
        $data["month"]               = "";
        $data["year"]                = "";
        $data["present"]             = 0;
        $data["absent"]              = 0;
        $data["late"]                = 0;
        $data["half_day"]            = 0;
        $data["holiday"]             = 0;
        $data["leave_count"]         = 0;
        $data["alloted_leave"]       = 0;
        $data['sch_setting']         = $this->sch_setting_detail;
        $data['staffid_auto_insert'] = $this->sch_setting_detail->staffid_auto_insert;
        $user_type                   = $this->staff_model->getStaffRole();
        $data['classlist']           = $user_type;

        $date = $year . "-" . $month;

        $searchEmployee = $this->payroll_model->searchEmployeeById($id);

        $data['result'] = $searchEmployee;
        $data["month"]  = $month;
        $data["year"]   = $year;

        $alloted_leave = $this->staff_model->alloted_leave($id);

        $newdate = date('Y-m-d', strtotime($date . " +1 month"));

        $data['monthAttendance'] = $this->monthAttendance($newdate, 3, $id);
        $data['monthLeaves']     = $this->monthLeaves($newdate, 3, $id);

        $data["attendanceType"] = $this->staffattendancemodel->getStaffAttendanceType();

        $data["alloted_leave"] = $alloted_leave[0]["alloted_leave"];

        $this->load->view("layout/header", $data);
        $this->load->view("admin/payroll/create", $data);
        $this->load->view("layout/footer", $data);
    }

    public function monthAttendance($st_month, $no_of_months, $emp)
    {
        $record = array();
        for ($i = 1; $i <= $no_of_months; $i++) {

            $r     = array();
            $month = date('m', strtotime($st_month . " -$i month"));
            $year  = date('Y', strtotime($st_month . " -$i month"));

            foreach ($this->staff_attendance as $att_key => $att_value) {

                $s = $this->payroll_model->count_attendance_obj($month, $year, $emp, $att_value);

                $r[$att_key] = $s;
            }

            $record['01-' . $month . '-' . $year] = $r;
        }
        return $record;
    }

    public function monthLeaves($st_month, $no_of_months, $emp)
    {
        $record = array();
        for ($i = 1; $i <= $no_of_months; $i++) {

            $r           = array();
            $month       = date('m', strtotime($st_month . " -$i month"));
            $year        = date('Y', strtotime($st_month . " -$i month"));
            $leave_count = $this->staff_model->count_leave($month, $year, $emp);
            if (!empty($leave_count["tl"])) {
                $l = $leave_count["tl"];
            } else {
                $l = "0";
            }

            $record[$month] = $l;
        }

        return $record;
    }

    public function payslip()
    {
        if (!$this->rbac->hasPrivilege('staff_payroll', 'can_add')) {
            access_denied();
        }

        $basic           = $this->input->post("basic");
        $sursalaire           = $this->input->post("sursalaire");
        $part_igr           = $this->input->post("part_igr");
        $its           = $this->input->post("its");
        $categorie_salaire           = $this->input->post("categorie_salaire");
        $categorie_lettre           = $this->input->post("categorie_lettre");
        $salaire_base           = $this->input->post("salaire_base");
        $prime_anc           = $this->input->post("prime_anc");
        $prime_trans           = $this->input->post("prime_trans");
        $primet           = $this->input->post("primet");
        $forfait_hs           = $this->input->post("forfait_hs");
        $prime_resp           = $this->input->post("prime_resp");
        $prime_rend          = $this->input->post("prime_rend");
        $prime_risque           = $this->input->post("prime_risque");
        $total_revenu           = $this->input->post("total_revenu");
        $prime_assi           = $this->input->post("prime_assi");
        $prime_grati           = $this->input->post("prime_grati");
        $imp_sal           = $this->input->post("imp_sal");
        $contra_nat           = $this->input->post("contra_nat");
        $imp_revenu           = $this->input->post("imp_revenu");
        $crns           = $this->input->post("crns");
        $cmu           = $this->input->post("cmu");
        $cmu_enfant           = $this->input->post("cmu_enfant");
        $cnps_regim          = $this->input->post("cnps_regim");
        $cnps_regims          = $this->input->post("cnps_regims");
        $cnps_tra           = $this->input->post("cnps_tra");
        $cnps_pres           = $this->input->post("cnps_pres");
        $fdfp_taxe          = $this->input->post("fdfp_taxe");
        $fdfp_form           = $this->input->post("fdfp_form");
        $avan_acom           = $this->input->post("avan_acom");
        $autre_reve           = $this->input->post("autre_reve");
        $bonus           = $this->input->post("bonus");
        $total_allowance = $this->input->post("total_allowance");
        $total_deduction = $this->input->post("total_deduction");
        $net_salary      = $this->input->post("net_salary");
        $gross_salary      = $this->input->post("gross_salary");
        $total_fiscal     = $this->input->post("total_fiscal");
        $gross_social      = $this->input->post("gross_social");
        $status          = $this->input->post("status");
        $staff_id        = $this->input->post("staff_id");
        $month           = $this->input->post("month");
        $date_from           = $this->input->post("date_from");
        $date_to           = $this->input->post("date_to");
        $name            = $this->input->post("name");
        $year            = $this->input->post("year");
        $tax             = $this->input->post("tax");
        $taxes             = $this->input->post("taxes");
        $leave_deduction = $this->input->post("leave_deduction");
        $this->form_validation->set_rules('net_salary', 'Net Salary', 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {

            $this->create($month, $year, $staff_id);
        } else {

            $data = array('staff_id' => $staff_id,
                'basic'                  => $basic,
                'prime_anc'                  => $prime_anc,
                'prime_trans'                  => $prime_trans,
                'primet'                  => $primet,
                'its'                  => $its,
                'total_revenu'                  => $total_revenu,
                'sursalaire'                  => $sursalaire,
                'part_igr'                  => $part_igr,
                'categorie_salaire'           => $categorie_salaire,
                'categorie_lettre'           => $categorie_lettre,
                'salaire_base'           => $salaire_base,
                'forfait_hs'                  => $forfait_hs,
                'prime_resp'                  => $prime_resp,
                'prime_rend'                  => $prime_rend,
                'prime_risque'                  => $prime_risque,
                'prime_assi'                  => $prime_assi,
                'prime_grati'                  => $prime_grati,
                'imp_sal'                  => $imp_sal,
                'contra_nat'                  => $contra_nat,
                'imp_revenu'                  => $imp_revenu,
                'crns'                  => $crns,
                'cmu'                  => $cmu,
                'cmu_enfant'                  => $cmu_enfant,
                'cnps_regim'                  => $cnps_regim,
                'cnps_regims'                  => $cnps_regims,
                'cnps_tra'                  => $cnps_tra,
                'cnps_pres'                  => $cnps_pres,
                'fdfp_taxe'                  => $fdfp_taxe,
                'fdfp_form'                  => $fdfp_form,
                'avan_acom'                  => $avan_acom,
                'autre_reve'                  => $autre_reve,
                'bonus'                  => $bonus,
                'total_allowance'        => $total_allowance,
                'total_deduction'        => $total_deduction,
                'net_salary'             => $net_salary,
                'gross_salary'             => $gross_salary,
                'total_fiscal'             => $total_fiscal,
                'gross_social'             => $gross_social,
                'payment_date'           => date("Y-m-d"),
                'date_from'             => $date_from,
                'date_to'               => $date_to,
                'status'                 => $status,
                'month'                  => $month,
                'year'                   => $year,
                'tax'                    => $tax,
                'taxes'                    => $taxes,
                'leave_deduction'        => '0',
            );

            $checkForUpdate = $this->payroll_model->checkPayslip($month, $year, $staff_id);

            if ($checkForUpdate == true) {

                $insert_id        = $this->payroll_model->createPayslip($data);
                $payslipid        = $insert_id;
                $allowance_type   = $this->input->post("allowance_type");
                $deduction_type   = $this->input->post("deduction_type");
                $allowance_amount = $this->input->post("allowance_amount");
                $deduction_amount = $this->input->post("deduction_amount");
                if (!empty($allowance_type)) {

                    $i = 0;
                    foreach ($allowance_type as $key => $all) {

                        $all_data = array(
                            'payslip_id'     => $payslipid,
                            'allowance_type' => $allowance_type[$i],
                            'amount'         => $allowance_amount[$i],
                            'staff_id'       => $staff_id,
                            'cal_type'       => "positive",
                        );

                        $insert_payslip_allowance = $this->payroll_model->add_allowance($all_data);

                        $i++;
                    }
                }

                if (!empty($deduction_type)) {
                    $j = 0;
                    foreach ($deduction_type as $key => $type) {

                        $type_data = array('payslip_id' => $payslipid,
                            'allowance_type'                => $deduction_type[$j],
                            'amount'                        => $deduction_amount[$j],
                            'staff_id'                      => $staff_id,
                            'cal_type'                      => "negative",
                        );

                        $insert_payslip_allowance = $this->payroll_model->add_allowance($type_data);

                        $j++;
                    }
                }

                redirect('admin/payroll');
            } else {

                $this->session->set_flashdata("msg", $this->lang->line('payslip_already_generated'));
                redirect('admin/payroll');
            }
        }
    }

    public function search($month, $year, $role = '')
    {

        $user_type         = $this->staff_model->getStaffRole();
        $data['classlist'] = $user_type;
        $data['monthlist'] = $this->customlib->getMonthDropdown();

        $searchEmployee = $this->payroll_model->searchEmployee($month, $year, $emp_name = '', $role);

        $data["resultlist"]     = $searchEmployee;
        $data["name"]           = $emp_name;
        $data["month"]          = $month;
        $data["year"]           = $year;
        $data['sch_setting']    = $this->sch_setting_detail;
        $data["payroll_status"] = $this->payroll_status;
        $data["resultlist"]     = $searchEmployee;
        $data["payment_mode"]   = $this->payment_mode;
        $this->load->view("layout/header", $data);
        $this->load->view("admin/payroll/stafflist", $data);
        $this->load->view("layout/footer", $data);
    }

    public function paymentRecord()
    {

        $month          = $this->input->get_post("month");
        $year           = $this->input->get_post("year");
        $id             = $this->input->get_post("staffid");
        $searchEmployee = $this->payroll_model->searchPayment($id, $month, $year);
        $data['result'] = $searchEmployee;
        $data["month"]  = $month;
        $data["year"]   = $year;
        echo json_encode($data);
    }

    public function paymentStatus($status)
    {

        $id          = $this->input->get('id');
        $updateStaus = $this->payroll_model->updatePaymentStatus($status, $id);
        redirect("admin/payroll");
    }

    public function paymentSuccess()
    {

        $payment_mode = $this->input->post("payment_mode");
        $date         = $this->input->post("payment_date");
        $payment_date = date('Y-m-d', strtotime($date));
        $remark       = $this->input->post("remarks");
        $date_from       = $this->input->post("date_from");
        $date_to       = $this->input->post("date_to");
        $status       = 'paid';
        $payslipid    = $this->input->post("paymentid");
        $this->form_validation->set_rules('payment_mode', $this->lang->line('payment') . " " . $this->lang->line('mode'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {

            $msg = array(
                'payment_mode' => form_error('payment_mode'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $data = array('payment_mode' => $payment_mode,'date_from' => $date_from,'date_to' => $date_to, 'payment_date' => $payment_date, 'remark' => $remark, 'status' => $status);
            $this->payroll_model->paymentSuccess($data, $payslipid);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function payslipView()
    {
        if (!$this->rbac->hasPrivilege('staff', 'can_view')) {
            access_denied();
        }
        $data["payment_mode"] = $this->payment_mode;
        $this->load->model("setting_model");
        $setting_result      = $this->setting_model->get();
        $data['settinglist'] = $setting_result[0];
        $id                  = $this->input->post("payslipid");
        $result              = $this->payroll_model->getPayslip($id);
        $data['sch_setting'] = $this->sch_setting_detail;

        $data['staffid_auto_insert'] = $this->sch_setting_detail->staffid_auto_insert;
        if (!empty($result)) {
            $allowance                  = $this->payroll_model->getAllowance($result["id"]);
            $data["allowance"]          = $allowance;
            $positive_allowance         = $this->payroll_model->getAllowance($result["id"], "positive");
            $data["positive_allowance"] = $positive_allowance;
            $negative_allowance         = $this->payroll_model->getAllowance($result["id"], "negative");
            $data["negative_allowance"] = $negative_allowance;
            $data["result"]             = $result;
            $this->load->view("admin/payroll/payslipview", $data);
        } else {
            echo "<div class='alert alert-info'>No Record Found.</div>";
        }
    }

    public function payslippdf()
    {

        $this->load->model("setting_model");
        $setting_result             = $this->setting_model->get();
        $data['settinglist']        = $setting_result[0];
        $id                         = 15;
        $result                     = $this->payroll_model->getPayslip($id);
        $allowance                  = $this->payroll_model->getAllowance($result["id"]);
        $data["allowance"]          = $allowance;
        $positive_allowance         = $this->payroll_model->getAllowance($result["id"], "positive");
        $data["positive_allowance"] = $positive_allowance;
        $negative_allowance         = $this->payroll_model->getAllowance($result["id"], "negative");
        $data["negative_allowance"] = $negative_allowance;
        $data["result"]             = $result;
        $this->load->view("admin/payroll/payslipview", $data);
    }

    public function payrollreport()
    {
        if (!$this->rbac->hasPrivilege('payroll_report', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/human_resource');
        $this->session->set_userdata('subsub_menu', 'Reports/attendance/attendance_report');
        $month                = $this->input->post("month");
        $year                 = $this->input->post("year");
        $role                 = $this->input->post("role");
        $data["month"]        = $month;
        $data["year"]         = $year;
        $data["role_select"]  = $role;
        $data['monthlist']    = $this->customlib->getMonthDropdown();
        $data['yearlist']     = $this->payroll_model->payrollYearCount();
        $staffRole            = $this->staff_model->getStaffRole();
        $data["role"]         = $staffRole;
        $data["payment_mode"] = $this->payment_mode;

        $this->form_validation->set_rules('year', $this->lang->line('year'), 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {

            $this->load->view("layout/header", $data);
            $this->load->view("admin/payroll/payrollreport", $data);
            $this->load->view("layout/footer", $data);
        } else {

            $result         = $this->payroll_model->getpayrollReport($month, $year, $role);
            $data["result"] = $result;
            $this->load->view("layout/header", $data);
            $this->load->view("admin/payroll/payrollreport", $data);
            $this->load->view("layout/footer", $data);
        }
    }

    public function deletepayroll($payslipid, $month, $year, $role = '')
    {
        if (!$this->rbac->hasPrivilege('staff_payroll', 'can_delete')) {
            access_denied();
        }
        if (!empty($payslipid)) {

            $this->payroll_model->deletePayslip($payslipid);
        }

        redirect('admin/payroll/search/' . $month . "/" . $year . "/" . $role);
    }

    public function revertpayroll($payslipid, $month, $year, $role = '')
    {

        if (!$this->rbac->hasPrivilege('staff_payroll', 'can_delete')) {
            access_denied();
        }
        if (!empty($payslipid)) {

            $this->payroll_model->revertPayslipStatus($payslipid);
        }
        redirect('admin/payroll/search/' . $month . "/" . $year . "/" . $role);

    }

    /**
     * Envoi du bulletin de paie par email
     */

// Test rapide dans un contrôleur

    /**
     * Récupère les informations de l'employé pour l'email
     */
// Exemple de contrôleur

    /**
     * Envoi du bulletin de paie par email
     */
    public function sendPayslipEmail()
    {
        // Vérification des permissions
        if (!$this->rbac->hasPrivilege('staff_payroll', 'can_view')) {
            access_denied();
        }

        // ⭐⭐ TRY-CATCH GLOBAL pour capturer toutes les erreurs ⭐⭐
        try {
            $payslip_id = $this->input->post('id', 0);
            log_message('debug', '🎯 === DÉBUT sendPayslipEmail === ID: ' . $payslip_id);

            $response = ['status' => 'fail', 'message' => ''];

            // ==================== RÉCUPÉRATION DES DONNÉES ====================

            // 1. Récupération du bulletin de paie
            $payslip = $this->payroll_model->getPayslip($payslip_id);
            log_message('debug', '📊 Payslip trouvé: ' . ($payslip ? 'OUI' : 'NON'));

            if (!$payslip) {
                throw new Exception('Bulletin de paie introuvable');
            }

            // 2. Récupération des informations du staff
            $staff = $this->staff_model->get($payslip['staff_id']);
            log_message('debug', '👤 Staff trouvé: ' . ($staff ? 'OUI' : 'NON'));
            log_message('debug', '📧 Email du staff: ' . ($staff['email'] ?? 'NON RENSEIGNÉ'));

            if (!$staff) {
                throw new Exception('Employé introuvable');
            }

            // 3. Vérification de l'email du staff
            if (empty($staff['email'])) {
                throw new Exception("L'employé n'a pas d'adresse email");
            }

            // 4. Récupération des données de la société
            $company = $this->setting_model->get();
            if (empty($company)) {
                throw new Exception('Données entreprise introuvables');
            }
            log_message('debug', '🏢 Company trouvée: ' . ($company ? 'OUI' : 'NON'));

            // 5. Récupération des informations de l'utilisateur connecté
            $user_data = $this->customlib->getUserData();
            $user_name = $this->customlib->getAdminSessionUserName();
            $user_email = $user_data['email'] ?? '';
            log_message('debug', '🔧 User email: ' . $user_email);

            // 6. Récupération des allocations et déductions
            $positive_allowance = $this->payroll_model->getAllowance($payslip['id'], "positive");
            $negative_allowance = $this->payroll_model->getAllowance($payslip['id'], "negative");

            log_message('debug', '💰 Allocations positives: ' . count($positive_allowance));
            log_message('debug', '💸 Déductions négatives: ' . count($negative_allowance));

            // ==================== PRÉPARATION DES DONNÉES POUR L'EMAIL ====================

            $email_data = [
                'id' => $payslip['id'],
                'data' => [
                    // Données du bulletin de paie pour le PDF
                    'payslip' => $payslip,
                    'staff' => $staff,
                    'company' => $company[0],
                    'positive_allowance' => $positive_allowance,
                    'negative_allowance' => $negative_allowance,

                    // Données pour l'email (simulation devis)
                    'quote' => [
                        'customer_name' => $staff['name'],
                        'customer_last_name' => $staff['surname'],
                        'quote_number' => 'PAYSLIP-' . $payslip['id'],
                        'quote_date' => date('Y-m-d'),
                        'net_salary' => $payslip['net_salary'],
                        'payslip_month' => $payslip['month'],
                        'valid_until' => date('Y-m-d', strtotime('+30 days'))
                    ],
                    'user' => $user_data,
                    'user_name' => $user_name,
                    'user_email' => $user_email
                ],
                'credential_for' => 'sendQuoteNoStock',
                'staff_name' => $staff['name'] . ' ' . $staff['surname'],
                'payslip_number' => 'PAYSLIP-' . $payslip['id'],
                'email' => $staff['email'],
                'payslip_month' => $payslip['month'],
                'payslip_year' => $payslip['year'],
                'employee_id' => $staff['employee_id']
            ];

            log_message('debug', '📨 Données email préparées pour: ' . $staff['email']);

            // ==================== ENVOI DE L'EMAIL ====================

            log_message('debug', '🔄 Appel de mailsms avec template send_quote_no_stock...');

            // Appeler send_quote qui fonctionne
            $this->mailsmsconf->mailsms('send_quote_no_stock', $email_data);

            // ==================== RÉPONSE SUCCÈS ====================

            $response['status'] = 'success';
            $response['message'] = 'Bulletin de paie envoyé avec succès à ' . $staff['email'];

            log_message('debug', '✅ === SUCCÈS sendPayslipEmail ===');

            // Retourner la réponse en JSON
            echo json_encode($response);

        } catch (Exception $e) {
            // ⭐⭐ CAPTURE DE TOUTES LES ERREURS ⭐⭐
            log_message('error', '❌ ERREUR GLOBALE sendPayslipEmail: ' . $e->getMessage());
            log_message('error', '❌ Stack trace: ' . $e->getTraceAsString());

            $response = [
                'status' => 'fail',
                'message' => 'Erreur lors de l\'envoi: ' . $e->getMessage()
            ];
            echo json_encode($response);
        }
    }

    public function printMultiplePayslips() {
        $ids = $this->input->post('selected_payslips');

        if (empty($ids)) {
            $this->session->set_flashdata('error', 'Aucun bulletin sélectionné');
            redirect($_SERVER['HTTP_REFERER']);
        }

        // Convertir la chaîne d'IDs en tableau
        if (is_string($ids)) {
            $ids = explode(',', $ids);
        }

        // Nettoyer les IDs pour s'assurer qu'ils ne contiennent que des chiffres
        $ids = array_map('intval', $ids);
        $ids = array_filter($ids);

        if (empty($ids)) {
            $this->session->set_flashdata('error', 'IDs de bulletins invalides');
            redirect($_SERVER['HTTP_REFERER']);
        }

        // Charger les modèles nécessaires
        $this->load->model('payroll_model');
        $this->load->model('staff_model');

        $payslips = array();
        foreach ($ids as $payslip_id) {
            $payslip = $this->payroll_model->getPayslip($payslip_id);
            if ($payslip) {
                $staff = $this->staff_model->get($payslip['staff_id']);
                $payslip['staff'] = $staff;
                $payslips[] = $payslip;
            }
        }

        if (empty($payslips)) {
            $this->session->set_flashdata('error', 'Bulletins non trouvés');
            redirect($_SERVER['HTTP_REFERER']);
        }

        $data['payslips'] = $payslips;
        $data['title'] = 'Impression multiple de bulletins de paie';

        // Charger la vue d'impression
        $this->load->view('admin/payroll/print_multiple_payslips', $data);
    }





}
