<?php

class Leaverequest extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('file');

        $this->config->load("payroll");

        $this->load->model("staff_model");
        $this->load->model("leaverequest_model");
        $this->contract_type = $this->config->item('contracttype');
        $this->marital_status = $this->config->item('marital_status');
        $this->staff_attendance = $this->config->item('staffattendance');
        $this->payroll_status = $this->config->item('payroll_status');
        $this->payment_mode = $this->config->item('payment_mode');
        $this->status = $this->config->item('status');
        $this->config->load("app-config");
        $this->load->library('Enc_lib');
        $this->load->library('encoding_lib');
        $this->load->library('customlib');
        $this->sch_setting_detail = $this->setting_model->getSetting();
    }

    function leaverequest() {
        if (!$this->rbac->hasPrivilege('approve_leave_request', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'HR');
        $this->session->set_userdata('sub_menu', 'admin/leaverequest/leaverequest');
        $leave_request = $this->leaverequest_model->staff_leave_request();
        $data["leave_request"] = $leave_request;
        $LeaveTypes = $this->staff_model->getLeaveType();
        $userdata = $this->customlib->getUserData();
        $data["leavetype"] = $LeaveTypes;
        $staffRole = $this->staff_model->getStaffRole();
        $data["staffrole"] = $staffRole;
        $data["status"] = $this->status;
        $this->load->view("layout/header", $data);
        $this->load->view("admin/staff/staffleaverequest", $data);
        $this->load->view("layout/footer", $data);
    }
 
    function countLeave($id) {
        $lid = $this->input->post("lid");
        $alloted_leavetype = $this->leaverequest_model->allotedLeaveType($id);

        $i = 0;
        $html = "<select  name='leave_type' id='leave_type' class='form-control'><option value=''>" . $this->lang->line('select') . "</option>";
        $data = array();

        foreach ($alloted_leavetype as $key => $value) {
            $count_leaves[] = $this->leaverequest_model->countLeavesData($id, $value["leave_type_id"]);
            $data[$i]['type'] = $value["type"];
            $data[$i]['id'] = $value["leave_type_id"];
            $data[$i]['alloted_leave'] = $value["alloted_leave"];
            $data[$i]['approve_leave'] = $count_leaves[$i]['approve_leave'];


            $i++;
        }

        foreach ($data as $dkey => $dvalue) {
            if (!empty($dvalue["alloted_leave"])) {
                if ($lid == $dvalue["id"]) {
                    $a = "selected";
                } else {
                    $a = "";
                }

                if ($dvalue["alloted_leave"] == "") {

                    $available = $dvalue["approve_leave"];
                } else {
                    $available = $dvalue["alloted_leave"] - $dvalue["approve_leave"];
                }
                if ($available > 0) {

                    $html .= "<option value=" . $dvalue["id"] . " $a>" . $dvalue["type"] . " (" . $available . ")" . "</option>";
                }
            }
        }

        $html .= "</select>";
        echo $html;
    }

    function leaveStatus() {
        if ((!$this->rbac->hasPrivilege('approve_leave_request', 'can_edit'))) {
            access_denied();
        }
        $leave_request_id = $this->input->post("leave_request_id");
        $status = $this->input->post("status");
        $adminRemark = $this->input->post("detailremark");
        $data = array('status' => $status, 'admin_remark' => $adminRemark);
        $this->leaverequest_model->changeLeaveStatus($data, $leave_request_id);
        $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        echo json_encode($array);
    }

    function remove($id) {

        $this->leaverequest_model->leave_remove($id);
    }

    function leaveRecord() {

        $id = $this->input->post("id");
        $result = $this->staff_model->getLeaveRecord($id);
        $leave_from = date("m/d/Y", strtotime($result->leave_from));
        $result->leavefrom = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($result->leave_from));
        $result->date = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($result->date));
        $leave_to = date("m/d/Y", strtotime($result->leave_to));
        $result->leaveto = date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($result->leave_to));
        $result->days = $this->dateDifference($leave_from, $leave_to);
        echo json_encode($result);
    }

    function dateDifference($date_1, $date_2, $differenceFormat = '%a') {
        $datetime1 = date_create($date_1);
        $datetime2 = date_create($date_2);

        $interval = date_diff($datetime1, $datetime2);

        return $interval->format($differenceFormat) + 1;
    }

    function addLeave() {
        $role          = $this->input->post("role");
        $empid         = $this->input->post("empname");
        $applied_date  = $this->input->post("applieddate");
        $leavetype     = $this->input->post("leave_type");
        $reason        = $this->input->post("reason");
        $remark        = $this->input->post("remark");
        $status        = $this->input->post("addstatus");
        $request_id    = $this->input->post("leaverequestid");
        $return_date   = $this->input->post("return_date");

        // Validation des champs obligatoires
        $this->form_validation->set_rules('role', $this->lang->line('role'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('empname', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('applieddate', $this->lang->line('applied_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('leave_from_date', $this->lang->line('leave')." ".$this->lang->line('from')." ".$this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('leave_to_date', $this->lang->line('leave')." ".$this->lang->line('to')." ".$this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('leave_type', $this->lang->line('available') . " " . $this->lang->line('leave'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('userfile', $this->lang->line('file'), 'callback_handle_upload[userfile]');

        if ($this->form_validation->run() == FALSE) {
            $msg = array(
                'role'            => form_error('role'),
                'empname'         => form_error('empname'),
                'applieddate'     => form_error('applieddate'),
                'leavedates'      => form_error('leavedates'),
                'leave_type'      => form_error('leave_type'),
                'leave_from_date' => form_error('leave_from_date'),
                'leave_to_date'   => form_error('leave_to_date'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            // Conversion des dates au format Y-m-d
            $leavefrom  = date("Y-m-d", $this->customlib->datetostrtotime($this->input->post('leave_from_date')));
            $leaveto    = date("Y-m-d", $this->customlib->datetostrtotime($this->input->post('leave_to_date')));
            $applied_by = $this->customlib->getAdminSessionUserName();
            $leave_days = $this->dateDifference($leavefrom, $leaveto);
            $staff_id   = $empid;

            // Traitement de la date de retour (peut être vide)
            if (!empty($return_date)) {
                $return_date = date("Y-m-d", $this->customlib->datetostrtotime($return_date));
            } else {
                $return_date = null;
            }

            // Vérification du solde de congé
            $my_leave = $this->leaverequest_model->myallotedLeaveType($staff_id, $leavetype);
            if ($my_leave['alloted_leave'] >= $leave_days) {
                // Gestion du document attaché
                if (isset($_FILES["userfile"]) && !empty($_FILES['userfile']['name'])) {
                    $uploaddir = './uploads/staff_documents/' . $staff_id . '/';
                    if (!is_dir($uploaddir) && !mkdir($uploaddir, 0777, true)) {
                        die("Error creating folder $uploaddir");
                    }
                    $fileInfo = pathinfo($_FILES["userfile"]["name"]);
                    $document = time() . '.' . $fileInfo['extension'];
                    move_uploaded_file($_FILES["userfile"]["tmp_name"], $uploaddir . $document);
                } else {
                    $document = $this->input->post("filename");
                }

                // Préparation des données à insérer ou mettre à jour
                if (!empty($request_id)) {
                    $data = array(
                        'id'              => $request_id,
                        'staff_id'        => $staff_id,
                        'date'            => date('Y-m-d', $this->customlib->datetostrtotime($applied_date)),
                        'leave_type_id'   => $leavetype,
                        'leave_days'      => $leave_days,
                        'leave_from'      => $leavefrom,
                        'leave_to'        => $leaveto,
                        'return_date'     => $return_date,
                        'employee_remark' => $reason,
                        'status'          => $status,
                        'admin_remark'    => $remark,
                        'applied_by'      => $applied_by,
                        'document_file'   => $document
                    );
                } else {
                    $data = array(
                        'staff_id'        => $staff_id,
                        'date'            => date("Y-m-d", $this->customlib->datetostrtotime($applied_date)),
                        'leave_days'      => $leave_days,
                        'leave_type_id'   => $leavetype,
                        'leave_from'      => $leavefrom,
                        'leave_to'        => $leaveto,
                        'return_date'     => $return_date,
                        'employee_remark' => $reason,
                        'status'          => $status,
                        'admin_remark'    => $remark,
                        'applied_by'      => $applied_by,
                        'document_file'   => $document
                    );
                }

                $this->leaverequest_model->addLeaveRequest($data);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            } else {
                $msg = array(
                    'applieddate' => $this->lang->line('selected') . " " . $this->lang->line('leave') . " " . $this->lang->line('days') . " > " . $this->lang->line('available') . " " . $this->lang->line('leaves')
                );
                $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
            }
        }
        echo json_encode($array);
    }

    function addLeave150626() {


        $role = $this->input->post("role");
        $empid = $this->input->post("empname");
        $applied_date = $this->input->post("applieddate");
        $leavetype = $this->input->post("leave_type");

        $reason = $this->input->post("reason");
        $remark = $this->input->post("remark");
        $status = $this->input->post("addstatus");
        $request_id = $this->input->post("leaverequestid");

        $this->form_validation->set_rules('role', $this->lang->line('role'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('empname', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('applieddate', $this->lang->line('applied_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('leave_from_date', $this->lang->line('leave')." ".$this->lang->line('from')." ".$this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('leave_to_date', $this->lang->line('leave')." ".$this->lang->line('to')." ".$this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('leave_type', $this->lang->line('available') . " " . $this->lang->line('leave'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('leave_type', $this->lang->line('leave') . " " . $this->lang->line('type'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('userfile', $this->lang->line('file'), 'callback_handle_upload[userfile]');

        if ($this->form_validation->run() == FALSE) {

            $msg = array(
                'role' => form_error('role'),
                'empname' => form_error('empname'),
                'applieddate' => form_error('applieddate'),
                'leavedates' => form_error('leavedates'),
                'leave_type' => form_error('leave_type'),
                'leave_from_date' => form_error('leave_from_date'),
                'leave_to_date' => form_error('leave_to_date'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
          

            $leavefrom = date("Y-m-d", $this->customlib->datetostrtotime($this->input->post('leave_from_date')));
            $leaveto = date("Y-m-d", $this->customlib->datetostrtotime($this->input->post('leave_to_date')));
            $applied_by = $this->customlib->getAdminSessionUserName();
            $leave_days = $this->dateDifference($leavefrom, $leaveto);
            $staff_id = $empid;
            $my_laeve = $this->leaverequest_model->myallotedLeaveType($staff_id, $leavetype);
            if ($my_laeve['alloted_leave'] >= $leave_days) {
                if (isset($_FILES["userfile"]) && !empty($_FILES['userfile']['name'])) {
                    $uploaddir = './uploads/staff_documents/' . $staff_id . '/';
                    if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                        die("Error creating folder $uploaddir");
                    }
                    $fileInfo = pathinfo($_FILES["userfile"]["name"]);
                    $document = time() . '.' . $fileInfo['extension'];

                    move_uploaded_file($_FILES["userfile"]["tmp_name"], './uploads/staff_documents/' . $staff_id . '/' . $document);
                } else {

                    $document = $this->input->post("filename");
                }



                if (!empty($request_id)) {


                    $data = array('id' => $request_id,
                        'staff_id' => $staff_id,
                        'date' => date('Y-m-d', $this->customlib->datetostrtotime($applied_date)),
                        'leave_type_id' => $leavetype,
                        'leave_days' => $leave_days,
                        'leave_from' => $leavefrom,
                        'leave_to' => $leaveto, 'employee_remark' => $reason, 'status' => $status, 'admin_remark' => $remark, 'applied_by' => $applied_by, 'document_file' => $document);
                } else {

                    $data = array('staff_id' => $staff_id, 'date' => date("Y-m-d", $this->customlib->datetostrtotime($applied_date)), 'leave_days' => $leave_days, 'leave_type_id' => $leavetype, 'leave_from' => $leavefrom, 'leave_to' => $leaveto, 'employee_remark' => $reason, 'status' => $status, 'admin_remark' => $remark, 'applied_by' => $applied_by, 'document_file' => $document);
                }



                $this->leaverequest_model->addLeaveRequest($data);
                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            } else {
                $msg = array(
                    'applieddate' => $this->lang->line('selected') . " " . $this->lang->line('leave') . " " . $this->lang->line('days') . " > " . $this->lang->line('available') . " " . $this->lang->line('leaves'),
                );

                $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
            }
          
        }
          echo json_encode($array);
    }



    public function add_staff_leave() {

        $userdata      = $this->customlib->getUserData();
        $applied_date  = $this->input->post("applieddate");
        $leavetype     = $this->input->post("leave_type");
        $reason        = $this->input->post("reason");
        $remark        = '';
        $status        = 'pending';
        $request_id    = $this->input->post("leaverequestid");

        $this->form_validation->set_rules('applieddate', $this->lang->line('applied_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('leave_from_date', $this->lang->line('leave')." ".$this->lang->line('from')." ".$this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('leave_to_date', $this->lang->line('leave')." ".$this->lang->line('to')." ".$this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('leave_type', $this->lang->line('available') . " " . $this->lang->line('leave'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('userfile', $this->lang->line('file'), 'callback_handle_upload[userfile]');

        if ($this->form_validation->run() == FALSE) {
            $msg = array(
                'applieddate'     => form_error('applieddate'),
                'leave_from_date' => form_error('leave_from_date'),
                'leave_to_date'   => form_error('leave_to_date'),
                'leave_type'      => form_error('leave_type'),
                'userfile'        => form_error('userfile'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {

            $leavefrom  = date("Y-m-d", $this->customlib->datetostrtotime($this->input->post('leave_from_date')));
            $leaveto    = date("Y-m-d", $this->customlib->datetostrtotime($this->input->post('leave_to_date')));
            $staff_id   = $userdata["id"];
            $applied_by = $this->customlib->getAdminSessionUserName();
            $leave_days = $this->dateDifference($leavefrom, $leaveto);

            // 🔁 Récupération du solde de congé, avec fallback vers leave_types.durée si vide
            $my_leave = $this->leaverequest_model->myallotedLeaveType($staff_id, $leavetype);

            $available_leave = isset($my_leave['alloted_leave']) && $my_leave['alloted_leave'] != '' ? $my_leave['alloted_leave'] : 0;

            if ($available_leave >= $leave_days) {

                // 📎 Gestion du fichier joint
                if (isset($_FILES["userfile"]) && !empty($_FILES['userfile']['name'])) {
                    $uploaddir = './uploads/staff_documents/' . $staff_id . '/';
                    if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                        die("Error creating folder $uploaddir");
                    }
                    $fileInfo = pathinfo($_FILES["userfile"]["name"]);
                    $document = basename($_FILES['userfile']['name']);
                    $img_name = $uploaddir . basename($_FILES['userfile']['name']);
                    move_uploaded_file($_FILES["userfile"]["tmp_name"], $img_name);
                } else {
                    $document = $this->input->post("filename");
                }

                $data = array(
                    'staff_id'        => $staff_id,
                    'date'            => date("Y-m-d", $this->customlib->datetostrtotime($applied_date)),
                    'leave_days'      => $leave_days,
                    'leave_type_id'   => $leavetype,
                    'leave_from'      => $leavefrom,
                    'leave_to'        => $leaveto,
                    'employee_remark' => $reason,
                    'status'          => $status,
                    'admin_remark'    => $remark,
                    'applied_by'      => $applied_by,
                    'document_file'   => $document
                );

                if (!empty($request_id)) {
                    $data['id'] = $request_id; // mise à jour
                }

                $this->leaverequest_model->addLeaveRequest($data);

                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));

            } else {
                $msg = array(
                    'applieddate' => $this->lang->line('selected') . " " . $this->lang->line('leave') . " " . $this->lang->line('days') . " > " . $this->lang->line('available') . " " . $this->lang->line('leaves'),
                );
                $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
            }
        }

        echo json_encode($array);
    }


    public function add_staff_leave_old() {


        $userdata = $this->customlib->getUserData();
        $applied_date = $this->input->post("applieddate");
        $leavetype = $this->input->post("leave_type");

        $reason = $this->input->post("reason");
        $remark = '';
        $status = 'pending';
        $request_id = $this->input->post("leaverequestid");


        $this->form_validation->set_rules('applieddate', $this->lang->line('applied_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('leave_from_date', $this->lang->line('leave')." ".$this->lang->line('from')." ".$this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('leave_to_date', $this->lang->line('leave')." ".$this->lang->line('to')." ".$this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('leave_type', $this->lang->line('available') . " " . $this->lang->line('leave'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('userfile', $this->lang->line('file'), 'callback_handle_upload[userfile]');

        if ($this->form_validation->run() == FALSE) {


            $msg = array(
                'applieddate' => form_error('applieddate'),
                'leave_from_date' => form_error('leave_from_date'),
                'leave_to_date' => form_error('leave_to_date'),
                'leave_type' => form_error('leave_type'),
				'userfile' => form_error('userfile'),
            );

            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            

            $leavefrom = date("Y-m-d", $this->customlib->datetostrtotime($this->input->post('leave_from_date')));
            $leaveto = date("Y-m-d", $this->customlib->datetostrtotime($this->input->post('leave_to_date')));

            $staff_id = $userdata["id"];
            $applied_by = $this->customlib->getAdminSessionUserName();
            $leave_days = $this->dateDifference($leavefrom, $leaveto);
            $my_laeve = $this->leaverequest_model->myallotedLeaveType($staff_id, $leavetype);

            if ($my_laeve['alloted_leave'] >= $leave_days) {

                if (isset($_FILES["userfile"]) && !empty($_FILES['userfile']['name'])) {
                    $uploaddir = './uploads/staff_documents/' . $staff_id . '/';
                    if (!is_dir($uploaddir) && !mkdir($uploaddir)) {
                        die("Error creating folder $uploaddir");
                    }
                    $fileInfo = pathinfo($_FILES["userfile"]["name"]);
                    $document = basename($_FILES['userfile']['name']);
                    $img_name = $uploaddir . basename($_FILES['userfile']['name']);
                    move_uploaded_file($_FILES["userfile"]["tmp_name"], $img_name);
                } else {

                    $document = $this->input->post("filename");
                }



                if (!empty($request_id)) {


                    $data = array('id' => $request_id,
                        'staff_id' => $staff_id,
                        'date' => date('Y-m-d', $this->customlib->datetostrtotime($applied_date)),
                        'leave_type_id' => $leavetype,
                        'leave_days' => $leave_days,
                        'leave_from' => $leavefrom,
                        'leave_to' => $leaveto, 'employee_remark' => $reason, 'status' => $status, 'admin_remark' => $remark, 'applied_by' => $applied_by, 'document_file' => $document);
                } else {

                    $data = array('staff_id' => $staff_id, 'date' => date("Y-m-d", $this->customlib->datetostrtotime($applied_date)), 'leave_days' => $leave_days, 'leave_type_id' => $leavetype, 'leave_from' => $leavefrom, 'leave_to' => $leaveto, 'employee_remark' => $reason, 'status' => $status, 'admin_remark' => $remark, 'applied_by' => $applied_by, 'document_file' => $document);
                }


                $this->leaverequest_model->addLeaveRequest($data);

                $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            } else {
                $msg = array(
                    'applieddate' => $this->lang->line('selected') . " " . $this->lang->line('leave') . " " . $this->lang->line('days') . " > " . $this->lang->line('available') . " " . $this->lang->line('leaves'),
                );

                $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
            }
        }
        echo json_encode($array);
    }

    public function generate_leave_certificate($leave_id) {
        if (!$this->rbac->hasPrivilege('approve_leave_request', 'can_view')) {
            access_denied();
        }

        // Récupérer le congé
        $leave = $this->staff_model->getLeaveRecord($leave_id);
        if (empty($leave)) {
            show_404();
        }
        if ($leave->status !== 'approve') {
            show_error('Ce congé n\'est pas approuvé.', 403);
        }

        // Récupérer l'employé
        $staff = $this->staff_model->getStaffDetails($leave->staff_id);
        if (empty($staff)) {
            show_404();
        }


        // --- Récupérer le type de congé ---
        $this->load->model('staff_model'); // déjà chargé ? mais on le charge pour être sûr
        $leave_type = $this->staff_model->getLeaveTypeById($leave->leave_type_id);

        // Récupérer les paramètres de l'établissement (logo, coordonnées)
        // On utilise la variable déjà chargée dans le constructeur : $this->sch_setting_detail
        $sch_setting = $this->sch_setting_detail; // ou $this->setting_model->getSetting();

        // Données pour la vue
        $data['staff'] = $staff;
        $data['leave'] = $leave;
        $data['leave_type'] = $leave_type; // on passe le type
        $data['leave_from_formatted'] = date($this->customlib->getSchoolDateFormat(), strtotime($leave->leave_from));
        $data['leave_to_formatted'] = date($this->customlib->getSchoolDateFormat(), strtotime($leave->leave_to));
        $data['leave_days'] = $leave->leave_days;
        $data['current_date'] = date($this->customlib->getSchoolDateFormat(), time());
        $data['sch_setting'] = $sch_setting; // 👈 AJOUT OBLIGATOIRE

        // Charger la vue HTML
        $html = $this->load->view('admin/staff/leave_certificate_pdf', $data, true);

        // Génération du PDF avec Dompdf
        if (!class_exists('Dompdf\Dompdf')) {
            require_once APPPATH . 'third_party/dompdf/autoload.inc.php';
        }
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("attestation_conge_{$staff->employee_id}_{$leave_id}.pdf", array('Attachment' => 1));
    }

    public function generate_leave_certificate_21($leave_id) {
        if (!$this->rbac->hasPrivilege('approve_leave_request', 'can_view')) {
            access_denied();
        }

        // Récupérer le congé
        $this->load->model("setting_model");
        $setting_result             = $this->setting_model->get();
        $data['settinglist']        = $setting_result[0];
        $leave = $this->staff_model->getLeaveRecord($leave_id);
        if (empty($leave)) {
            show_404();
        }
        if ($leave->status !== 'approve') {
            show_error('Ce congé n\'est pas approuvé.', 403);
        }

        // Récupérer l'employé
        $staff = $this->staff_model->getStaffDetails($leave->staff_id);
        if (empty($staff)) {
            show_404();
        }

        // Données pour la vue
        $data['staff'] = $staff;
        $data['leave'] = $leave;
        $data['leave_from_formatted'] = date($this->customlib->getSchoolDateFormat(), strtotime($leave->leave_from));
        $data['leave_to_formatted'] = date($this->customlib->getSchoolDateFormat(), strtotime($leave->leave_to));
        $data['leave_days'] = $leave->leave_days;
        $data['current_date'] = date($this->customlib->getSchoolDateFormat(), time());

        // Charger la vue HTML
        $html = $this->load->view('admin/staff/leave_certificate_pdf', $data, true);

        // Utiliser Dompdf (installation nécessaire)
        // Assurez-vous que Dompdf est installé via Composer ou manuellement
        if (!class_exists('Dompdf\Dompdf')) {
            // Inclure l'autoload de Dompdf si besoin
            require_once APPPATH . 'third_party/dompdf/autoload.inc.php';
        }
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("attestation_conge_{$staff->employee_id}_{$leave_id}.pdf", array('Attachment' => 1));
    }

    public function handle_upload($str,$var)
    {

        $image_validate = $this->config->item('file_validate');
        $result = $this->filetype_model->get();
        if (isset($_FILES[$var]) && !empty($_FILES[$var]['name'])) {

            $file_type         = $_FILES[$var]['type'];
            $file_size         = $_FILES[$var]["size"];
            $file_name         = $_FILES[$var]["name"];

            $allowed_extension = array_map('trim', array_map('strtolower', explode(',', $result->file_extension)));
            $allowed_mime_type = array_map('trim', array_map('strtolower', explode(',', $result->file_mime)));
            $ext               = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

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
                $this->form_validation->set_message('handle_upload', "File Type / Extension Error Uploading ");
                return false;
            }

            return true;
        }
        return true;

    }

    /**
     * Affiche le calendrier des congés
     */
    public function calendar_() {
        if (!$this->rbac->hasPrivilege('approve_leave_request', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'HR');
        $this->session->set_userdata('sub_menu', 'admin/leaverequest/calendar');

        // Récupérer la liste des employés pour le filtre
        $staff_list = $this->staff_model->getStaff();
        $data['staff_list'] = $staff_list;

        // Récupérer les types de congés pour le filtre
        $leave_types = $this->staff_model->getLeaveType();
        $data['leave_types'] = $leave_types;

        $this->load->view('layout/header', $data);
        $this->load->view('admin/staff/leave_calendar', $data);
        $this->load->view('layout/footer', $data);
    }
    public function calendar() {
        if (!$this->rbac->hasPrivilege('approve_leave_request', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'HR');
        $this->session->set_userdata('sub_menu', 'admin/leaverequest/calendar');

        // Chargement des données avec vérification
        $staff_list = [];
        if (method_exists($this->staff_model, 'getStaff')) {
            $staff_list = $this->staff_model->getStaff();
        }
        $leave_types = [];
        if (method_exists($this->staff_model, 'getLeaveType')) {
            $leave_types = $this->staff_model->getLeaveType();
        }

        $data['staff_list'] = $staff_list;
        $data['leave_types'] = $leave_types;


        $this->load->view('layout/header', $data);
        $this->load->view('admin/staff/leave_calendar', $data);
        $this->load->view('layout/footer', $data);
    }
    /**
     * Retourne les événements du calendrier au format JSON (pour FullCalendar)
     */
    public function get_calendar_events() {
        if (!$this->rbac->hasPrivilege('approve_leave_request', 'can_view')) {
            show_error('Accès refusé', 403);
        }

        // Paramètres de filtrage (optionnels)
        $staff_id = $this->input->get('staff_id') ?: null;
        $leave_type_id = $this->input->get('leave_type_id') ?: null;
        $status = $this->input->get('status') ?: null; // 'approve', 'pending', 'disapprove'

        // Supporter à la fois :
        // - les paramètres envoyés automatiquement par FullCalendar : 'start' et 'end'
        // - les champs de filtre personnalisés : 'start_date' et 'end_date'
        $start_input = $this->input->get('start_date') ?: $this->input->get('start');
        $end_input = $this->input->get('end_date') ?: $this->input->get('end');

        // Normaliser en YYYY-MM-DD
        if ($start_input) {
            $start_date = date('Y-m-d', strtotime($start_input));
        } else {
            $start_date = date('Y-m-01');
        }
        if ($end_input) {
            $end_date = date('Y-m-d', strtotime($end_input));
        } else {
            $end_date = date('Y-m-t');
        }

        // Récupérer les congés
        $leaves = $this->leaverequest_model->get_calendar_events($staff_id, $leave_type_id, $status, $start_date, $end_date);

        // Formater les données pour FullCalendar
        $events = [];
        foreach ($leaves as $leave) {
            // Couleur en fonction du statut
            $color = '#28a745'; // vert = approuvé
            $textColor = '#ffffff';
            if ($leave['status'] == 'pending') {
                $color = '#ffc107'; // jaune = en attente
                $textColor = '#000000';
            } elseif ($leave['status'] == 'disapprove') {
                $color = '#dc3545'; // rouge = refusé
            }

            // Construction du titre
            $title = $leave['employee_name'] . ' (' . $leave['leave_type'] . ')';

            // Événement
            $events[] = [
                'id' => $leave['id'],
                'title' => $title,
                'start' => $leave['leave_from'],
                'end' => date('Y-m-d', strtotime($leave['leave_to'] . ' +1 day')), // FullCalendar exclut la date de fin, on ajoute un jour
                'backgroundColor' => $color,
                'borderColor' => $color,
                'textColor' => $textColor,
                'extendedProps' => [
                    'staff_id' => $leave['staff_id'],
                    'leave_type_id' => $leave['leave_type_id'],
                    'status' => $leave['status'],
                    'leave_days' => $leave['leave_days'],
                    'employee_remark' => $leave['employee_remark'],
                    'admin_remark' => $leave['admin_remark']
                ]
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($events);
    }


    /**
     * Récupérer les notifications des demandes de congé en attente
     */
    public function get_leave_notifications() {
        if (!$this->rbac->hasPrivilege('approve_leave_request', 'can_view')) {
            echo json_encode(array('status' => 'error', 'message' => 'Accès non autorisé'));
            return;
        }

        $staff_id = $this->session->userdata('admin_id');

        // Compter les demandes de congé en attente
        $this->db->where('status', 'pending');
        $this->db->where('is_read', 0);
        $this->db->where('assigned_to', $staff_id);
        $total_unread = $this->db->count_all_results('staff_leave_request');

        // Compter toutes les demandes en attente (y compris lues)
        $this->db->where('status', 'pending');
        $this->db->where('assigned_to', $staff_id);
        $total_pending = $this->db->count_all_results('staff_leave_request');

        // Récupérer les dernières demandes non lues
        $this->db->select('slr.*, s.name, s.surname, s.employee_id, lt.type as leave_type_name');
        $this->db->from('staff_leave_request slr');
        $this->db->join('staff s', 's.id = slr.staff_id', 'left');
        $this->db->join('leave_types lt', 'lt.id = slr.leave_type_id', 'left');
        $this->db->where('slr.status', 'pending');
        $this->db->where('slr.assigned_to', $staff_id);
        $this->db->order_by('slr.date', 'DESC');
        $this->db->limit(10);
        $query = $this->db->get();
        $pending_list = $query->result();

        // Compter les notifications pour l'historique (7 derniers jours)
        $this->db->where('assigned_to', $staff_id);
        $this->db->where('date >=', date('Y-m-d', strtotime('-7 days')));
        $history_count = $this->db->count_all_results('staff_leave_request');

        $response = array(
            'status' => 'success',
            'total_unread' => $total_unread,
            'total_pending' => $total_pending,
            'history_count' => $history_count,
            'list' => $pending_list,
            'html' => $this->load->view('admin/staff/leave_notification_list', array('list' => $pending_list, 'unread_count' => $total_unread), true)
        );

        echo json_encode($response);
    }

    /**
     * Marquer une demande de congé comme lue
     */
    public function mark_leave_read($id) {
        if (!$this->rbac->hasPrivilege('approve_leave_request', 'can_view')) {
            echo json_encode(array('status' => 'error', 'message' => 'Accès non autorisé'));
            return;
        }

        $staff_id = $this->session->userdata('admin_id');

        $this->db->where('id', $id);
        $this->db->where('assigned_to', $staff_id);
        $this->db->update('staff_leave_request', array('is_read' => 1, 'read_at' => date('Y-m-d H:i:s')));

        // Récupérer le nombre restant de notifications non lues
        $this->db->where('status', 'pending');
        $this->db->where('is_read', 0);
        $this->db->where('assigned_to', $staff_id);
        $remaining = $this->db->count_all_results('staff_leave_request');

        echo json_encode(array(
            'status' => 'success',
            'message' => 'Demande de congé marquée comme lue',
            'remaining' => $remaining
        ));
    }

    /**
     * Marquer toutes les demandes de congé comme lues
     */
    public function mark_all_leave_read() {
        if (!$this->rbac->hasPrivilege('approve_leave_request', 'can_view')) {
            echo json_encode(array('status' => 'error', 'message' => 'Accès non autorisé'));
            return;
        }

        $staff_id = $this->session->userdata('admin_id');

        $this->db->where('status', 'pending');
        $this->db->where('is_read', 0);
        $this->db->where('assigned_to', $staff_id);
        $this->db->update('staff_leave_request', array('is_read' => 1, 'read_at' => date('Y-m-d H:i:s')));

        $affected = $this->db->affected_rows();

        echo json_encode(array(
            'status' => 'success',
            'message' => $affected . ' demandes de congé marquées comme lues',
            'remaining' => 0
        ));
    }

    /**
     * Récupérer l'historique des demandes de congé
     */
    public function get_leave_history() {
        if (!$this->rbac->hasPrivilege('approve_leave_request', 'can_view')) {
            echo json_encode(array('status' => 'error', 'message' => 'Accès non autorisé'));
            return;
        }

        $staff_id = $this->session->userdata('admin_id');

        // Récupérer les 10 dernières demandes traitées (7 derniers jours)
        $this->db->select('slr.*, s.name, s.surname, s.employee_id, lt.type as leave_type_name');
        $this->db->from('staff_leave_request slr');
        $this->db->join('staff s', 's.id = slr.staff_id', 'left');
        $this->db->join('leave_types lt', 'lt.id = slr.leave_type_id', 'left');
        $this->db->where('slr.assigned_to', $staff_id);
        $this->db->where('slr.date >=', date('Y-m-d', strtotime('-7 days')));
        $this->db->where('slr.status !=', 'pending');
        $this->db->order_by('slr.date', 'DESC');
        $this->db->limit(10);
        $query = $this->db->get();
        $history_list = $query->result();

        $html = $this->load->view('admin/staff/leave_history_list', array('list' => $history_list), true);

        echo json_encode(array('status' => 'success', 'html' => $html));
    }

}

?>