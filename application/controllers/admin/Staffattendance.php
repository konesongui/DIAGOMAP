<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Staffattendance extends Admin_Controller {

    function __construct() {

        parent::__construct();
        $this->load->helper('file');

        $this->config->load("mailsms");
        $this->config->load("payroll");
        $this->load->library('mailsmsconf');
        $this->config_attendance = $this->config->item('attendence');
        $this->staff_attendance = $this->config->item('staffattendance');
        $this->load->model("staffattendancemodel");
        $this->load->model("staff_model");
        $this->load->model("payroll_model");
    }

    function index() {
        if (!($this->rbac->hasPrivilege('staff_attendance', 'can_view'))) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'HR');
        $this->session->set_userdata('sub_menu', 'admin/staffattendance');
        $data['title'] = 'Staff Attendance List';
        $data['title_list'] = 'Staff Attendance List';
        $user_type = $this->staff_model->getStaffRole();
        $data['classlist'] = $user_type;
        $data['class_id'] = "";
        $data['section_id'] = "";
        $data['date'] = "";
        $user_type_id = $this->input->post('user_id');
        $data["user_type_id"] = $user_type_id;

        if (!(isset($user_type_id))) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/staffattendance/staffattendancelist', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $user_type = $this->input->post('user_id');
            $date = $this->input->post('date');
            $user_list = $this->staffattendancemodel->get();
            $data['userlist'] = $user_list;
            $data['class_id'] = $user_list;
            $data['user_type_id'] = $user_type_id;
            $data['section_id'] = "";
            $data['date'] = $date;
            $search = $this->input->post('search');
            $holiday = $this->input->post('holiday');
            $this->session->set_flashdata('msg', '');

            if ($search == "saveattendence") {
                $user_type_ary = $this->input->post('student_session');
                $absent_staff_list = array(); // Renommé pour plus de clarté

                foreach ($user_type_ary as $key => $value) {
                    $checkForUpdate = $this->input->post('attendendence_id' . $value);
                    $attendance_type_id = null;

                    // Déterminer le type de présence
                    if (isset($holiday)) {
                        $attendance_type_id = 5; // ID pour "Congé/Jour férié"
                    } else {
                        $attendance_type_id = $this->input->post('attendencetype' . $value);
                    }

                    // Vérifier si c'est une absence (à configurer selon votre système)
                    // Supposons que l'ID pour "Absent" est 4 (à vérifier dans votre base de données)
                    $absent_config = 4; // ID pour "Absent" - À ajuster selon votre configuration

                    if ($checkForUpdate != 0) {
                        $arr = array(
                            'id' => $checkForUpdate,
                            'staff_id' => $value,
                            'staff_attendance_type_id' => $attendance_type_id,
                            'remark' => $this->input->post("remark" . $value),
                            'date' => date('Y-m-d', $this->customlib->datetostrtotime($date))
                        );

                        $insert_id = $this->staffattendancemodel->add($arr);
                    } else {
                        $arr = array(
                            'staff_id' => $value,
                            'staff_attendance_type_id' => $attendance_type_id,
                            'date' => date('Y-m-d', $this->customlib->datetostrtotime($date)),
                            'remark' => $this->input->post("remark" . $value),
                        );

                        $insert_id = $this->staffattendancemodel->add($arr);

                        // Si c'est une absence, ajouter à la liste pour notification
                        if ($attendance_type_id == $absent_config) {
                            $absent_staff_list[] = $value;
                        }
                    }

                    // Mise à jour pour les enregistrements existants aussi
                    if ($checkForUpdate != 0 && $attendance_type_id == $absent_config) {
                        $absent_staff_list[] = $value;
                    }
                }

                // Envoyer les notifications pour les absences
                if (!empty($absent_staff_list)) {
                    $this->mailsmsconf->mailsms('absent_attendence', $absent_staff_list, $date);
                }

                $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
                redirect('admin/staffattendance/index');
            }

            $attendencetypes = $this->attendencetype_model->getStaffAttendanceType();
            $data['attendencetypeslist'] = $attendencetypes;
            $resultlist = $this->staffattendancemodel->searchAttendenceUserType($user_type, date('Y-m-d', $this->customlib->datetostrtotime($date)));
            $data['resultlist'] = $resultlist;

            $this->load->view('layout/header', $data);
            $this->load->view('admin/staffattendance/staffattendancelist', $data);
            $this->load->view('layout/footer', $data);
        }
    }

    function index_15() {

        if (!($this->rbac->hasPrivilege('staff_attendance', 'can_view') )) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'HR');
        $this->session->set_userdata('sub_menu', 'admin/staffattendance');
        $data['title'] = 'Staff Attendance List';
        $data['title_list'] = 'Staff Attendance List';
        $user_type = $this->staff_model->getStaffRole();
        $data['classlist'] = $user_type;
        $data['class_id'] = "";
        $data['section_id'] = "";
        $data['date'] = "";
        $user_type_id = $this->input->post('user_id');
        $data["user_type_id"] = $user_type_id;
        if (!(isset($user_type_id))) {
            $this->load->view('layout/header', $data);
            $this->load->view('admin/staffattendance/staffattendancelist', $data);
            $this->load->view('layout/footer', $data);
        } else {

            $user_type = $this->input->post('user_id');
            $date = $this->input->post('date');
            $user_list = $this->staffattendancemodel->get();
            $data['userlist'] = $user_list;
            $data['class_id'] = $user_list;
            $data['user_type_id'] = $user_type_id;
            $data['section_id'] = "";
            $data['date'] = $date;
            $search = $this->input->post('search');
            $holiday = $this->input->post('holiday');
            $this->session->set_flashdata('msg', '');
            if ($search == "saveattendence") {
                $user_type_ary = $this->input->post('student_session');
                $absent_student_list = array();
                foreach ($user_type_ary as $key => $value) {
                    $checkForUpdate = $this->input->post('attendendence_id' . $value);
                    if ($checkForUpdate != 0) {
                        if (isset($holiday)) {
                            $arr = array(
                                'id' => $checkForUpdate,
                                'staff_id' => $value,
                                'staff_attendance_type_id' => 5,
                                'remark' => $this->input->post("remark" . $value),
                                'date' => date('Y-m-d', $this->customlib->datetostrtotime($date))
                            );
                        } else {
                            $arr = array(
                                'id' => $checkForUpdate,
                                'staff_id' => $value,
                                'staff_attendance_type_id' => $this->input->post('attendencetype' . $value),
                                'remark' => $this->input->post("remark" . $value),
                                'date' => date('Y-m-d', $this->customlib->datetostrtotime($date))
                            );
                        }

                        $insert_id = $this->staffattendancemodel->add($arr);
                    } else {
                        if (isset($holiday)) {
                            $arr = array(
                                'staff_id' => $value,
                                'staff_attendance_type_id' => 5,
                                'date' => date('Y-m-d', $this->customlib->datetostrtotime($date)),
                                'remark' => ''
                            );
                        } else {
                            $arr = array(
                                'staff_id' => $value,
                                'staff_attendance_type_id' => $this->input->post('attendencetype' . $value),
                                'date' => date('Y-m-d', $this->customlib->datetostrtotime($date)),
                                'remark' => $this->input->post("remark" . $value),
                            );
                        }
                        $insert_id = $this->staffattendancemodel->add($arr);
                        $absent_config = $this->config_attendance['absent'];
                        if ($arr['staff_attendance_type_id'] == $absent_config) {
                            $absent_student_list[] = $value;
                        }
                    }
                }

                $absent_config = $this->config_attendance['absent'];
                if (!empty($absent_student_list)) {

                    $this->mailsmsconf->mailsms('absent_attendence', $absent_student_list, $date);
                }
                $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $this->lang->line('success_message') . '</div>');
                redirect('admin/staffattendance/index');
            }

            $attendencetypes = $this->attendencetype_model->getStaffAttendanceType();
            $data['attendencetypeslist'] = $attendencetypes;
            $resultlist = $this->staffattendancemodel->searchAttendenceUserType($user_type, date('Y-m-d', $this->customlib->datetostrtotime($date)));
            $data['resultlist'] = $resultlist;
            $this->load->view('layout/header', $data);
            $this->load->view('admin/staffattendance/staffattendancelist', $data);
            $this->load->view('layout/footer', $data);
        }
    }

    function attendancereport() {

        if (!$this->rbac->hasPrivilege('staff_attendance_report', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'Reports');
        $this->session->set_userdata('sub_menu', 'Reports/attendance');
        $this->session->set_userdata('subsub_menu', 'Reports/attendance/staff_attendance_report');
        $attendencetypes = $this->staffattendancemodel->getStaffAttendanceType();
        $data['attendencetypeslist'] = $attendencetypes;
        $staffRole = $this->staff_model->getStaffRole();
        $data["role"] = $staffRole;
        $data['title'] = 'Attendance Report';
        $data['title_list'] = 'Attendance';
        $data['monthlist'] = $this->customlib->getMonthDropdown();
        $data['yearlist'] = $this->staffattendancemodel->attendanceYearCount();
        $data['date'] = "";
        $data['month_selected'] = "";
        $data["role_selected"] = "";
        $role = $this->input->post("role");
        $this->form_validation->set_rules('month', $this->lang->line('month'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == FALSE) {

            $this->load->view('layout/header', $data);
            $this->load->view('admin/staffattendance/attendancereport', $data);
            $this->load->view('layout/footer', $data);
        } else {

            $resultlist = array();
            $month = $this->input->post('month');
            $searchyear = $this->input->post('year');
            $data['month_selected'] = $month;
            $data["role_selected"] = $role;
            $stafflist = $this->staff_model->getEmployee($role);
            $session_current = $this->setting_model->getCurrentSessionName();
            $startMonth = $this->setting_model->getStartMonth();
            $centenary = substr($session_current, 0, 2); //2017-18 to 2017
            $year_first_substring = substr($session_current, 2, 2); //2017-18 to 2017
            $year_second_substring = substr($session_current, 5, 2); //2017-18 to 18
            $month_number = date("m", strtotime($month));

            if ($month_number >= $startMonth && $month_number <= 12) {
                $year = $centenary . $year_first_substring;
            } else {
                $year = $centenary . $year_second_substring;
            }

            $num_of_days = cal_days_in_month(CAL_GREGORIAN, $month_number, $searchyear);
            $attr_result = array();
            $attendence_array = array();
            $student_result = array();
            $data['no_of_days'] = $num_of_days;
            $date_result = array();
            $monthAttendance = array();

            for ($i = 1; $i <= $num_of_days; $i++) {
                $att_date = $searchyear . "-" . $month_number . "-" . sprintf("%02d", $i);
                $attendence_array[] = $att_date;

                $res = $this->staffattendancemodel->searchAttendanceReport($role, $att_date);


                $student_result = $res;
                $s = array();

                foreach ($res as $result_k => $result_v) {

                    $date = $searchyear . "-" . $month;
                    $newdate = date('Y-m-d', strtotime($date));

                    $s[$result_v['id']] = $result_v;
                }

                $date_result[$att_date] = $s;
            }

            foreach ($res as $result_k => $result_v) {

                $date = $searchyear . "-" . $month;
                $newdate = date('Y-m-d', strtotime($date));
                $monthAttendance[] = $this->monthAttendance($newdate, 1, $result_v['id']);
            }

            $data['monthAttendance'] = $monthAttendance;
            $data['resultlist'] = $date_result;
            if (!empty($searchyear)) {
                $data['attendence_array'] = $attendence_array;
                $data['student_array'] = $student_result;
            } else {

                $data['attendence_array'] = array();
                $data['student_array'] = array();
            }
 
            $this->load->view('layout/header', $data);
            $this->load->view('admin/staffattendance/attendancereport', $data);
            $this->load->view('layout/footer', $data);
        }
    }

    function monthAttendance($st_month, $no_of_months, $emp) {

        $this->load->model("payroll_model");
        $record = array();

        $r = array();
        $month = date('m', strtotime($st_month));
        $year = date('Y', strtotime($st_month));

        foreach ($this->staff_attendance as $att_key => $att_value) {

            $s = $this->payroll_model->count_attendance_obj($month, $year, $emp, $att_value);

            $r[$att_key] = $s;
        }

        $record[$emp] = $r;

        return $record;
    }

    function profileattendance() {

        $monthlist = $this->customlib->getMonthDropdown();
        $startMonth = $this->setting_model->getStartMonth();
        $data["monthlist"] = $monthlist;
        $data['yearlist'] = $this->staffattendancemodel->attendanceYearCount();
        $staffRole = $this->staff_model->getStaffRole();
        $data["role"] = $staffRole;
        $data["role_selected"] = "";
        $j = 0;
        for ($i = 1; $i <= 31; $i++) {

            $att_date = sprintf("%02d", $i);

            $attendence_array[] = $att_date;

            foreach ($monthlist as $key => $value) {

                $datemonth = date("m", strtotime($value));
                $att_dates = date("Y") . "-" . $datemonth . "-" . sprintf("%02d", $i);
                $date_array[] = $att_dates;
                $res[$att_dates] = $this->staffattendancemodel->searchStaffattendance($att_dates, $staff_id = 8);
            }

            $j++;
        }

        $data["resultlist"] = $res;
        $data["attendence_array"] = $attendence_array;
        $data["date_array"] = $date_array;

        $this->load->view("layout/header");
        $this->load->view("admin/staff/staffattendance", $data);
        $this->load->view("layout/footer");
    }

    public function scanAttendanceByQr() {
        $this->output->set_content_type('application/json');

        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        if (!($this->rbac->hasPrivilege('staff_attendance', 'can_add'))) {
            echo json_encode(array('status' => 'error', 'message' => 'Acces refuse'));
            return;
        }

        $qr_raw = trim((string)$this->input->post('qr_data'));
        $attendance_date_input = trim((string)$this->input->post('date'));
        $role_name = trim((string)$this->input->post('role_name'));

        if ($qr_raw === '') {
            echo json_encode(array('status' => 'error', 'message' => 'QR code vide.'));
            return;
        }

        $parsed = $this->parseStaffQrPayload($qr_raw);

        if (empty($parsed['staff_id']) && empty($parsed['employee_id'])) {
            echo json_encode(array('status' => 'error', 'message' => 'Format de QR code non reconnu.'));
            return;
        }

        $attendance_date = date('Y-m-d');
        if ($attendance_date_input !== '') {
            $timestamp = $this->customlib->datetostrtotime($attendance_date_input);
            if (empty($timestamp)) {
                $timestamp = strtotime(str_replace('/', '-', $attendance_date_input));
            }
            if (!empty($timestamp)) {
                $attendance_date = date('Y-m-d', $timestamp);
            }
        }

        $staff = $this->staffattendancemodel->getStaffForAttendanceByIdentifier($parsed['staff_id'], $parsed['employee_id'], $role_name);

        // Fallback: si le role filtre ne correspond pas, tenter sans filtre pour remonter un message utile.
        if (empty($staff)) {
            $staff = $this->staffattendancemodel->getStaffForAttendanceByIdentifier($parsed['staff_id'], $parsed['employee_id'], null);
        }

        if (empty($staff)) {
            echo json_encode(array('status' => 'error', 'message' => 'Employe introuvable ou role non correspondant.'));
            return;
        }

        $present_type_id = $this->staffattendancemodel->getAttendanceTypeIdByKey('P', 1);
        $holiday_type_id = $this->staffattendancemodel->getAttendanceTypeIdByKey('H', 5);

        $existing = $this->staffattendancemodel->getAttendanceByStaffAndDate($staff['staff_id'], $attendance_date);

        if (!empty($existing) && (int)$existing['staff_attendance_type_id'] === (int)$holiday_type_id) {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Presence non enregistree: jour ferie deja defini pour cette date.'
            ));
            return;
        }

        if (!empty($existing) && (int)$existing['staff_attendance_type_id'] === (int)$present_type_id) {
            echo json_encode(array(
                'status' => 'success',
                'already_marked' => true,
                'message' => 'Presence deja enregistree pour aujourd\'hui.',
                'staff' => array(
                    'staff_id' => (int)$staff['staff_id'],
                    'employee_id' => $staff['employee_id'],
                    'name' => trim($staff['name'] . ' ' . $staff['surname']),
                    'role_name' => $staff['role_name']
                ),
                'attendance_date' => $attendance_date
            ));
            return;
        }

        $payload = array(
            'staff_id' => (int)$staff['staff_id'],
            'staff_attendance_type_id' => (int)$present_type_id,
            'date' => $attendance_date,
            'remark' => 'Presence enregistree par scan QR'
        );

        if (!empty($existing['id'])) {
            $payload['id'] = (int)$existing['id'];
        }

        $this->staffattendancemodel->add($payload);

        echo json_encode(array(
            'status' => 'success',
            'message' => 'Presence enregistree avec succes.',
            'staff' => array(
                'staff_id' => (int)$staff['staff_id'],
                'employee_id' => $staff['employee_id'],
                'name' => trim($staff['name'] . ' ' . $staff['surname']),
                'role_name' => $staff['role_name']
            ),
            'attendance_date' => $attendance_date,
            'attendance_type_id' => (int)$present_type_id
        ));
    }

    private function parseStaffQrPayload($raw_payload) {
        $result = array(
            'staff_id' => null,
            'employee_id' => null,
        );

        $raw_payload = trim((string)$raw_payload);

        if ($raw_payload === '') {
            return $result;
        }

        $json_data = json_decode($raw_payload, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($json_data)) {
            if (!empty($json_data['staff_id']) && is_numeric($json_data['staff_id'])) {
                $result['staff_id'] = (int)$json_data['staff_id'];
            }
            if (!empty($json_data['employee_id'])) {
                $result['employee_id'] = trim((string)$json_data['employee_id']);
            }
            if (!empty($result['staff_id']) || !empty($result['employee_id'])) {
                return $result;
            }
        }

        if (strpos($raw_payload, 'DIAGOMAQR:') === 0) {
            $encoded = substr($raw_payload, strlen('DIAGOMAQR:'));
            $decoded = base64_decode($encoded, true);
            if ($decoded !== false) {
                $decoded_json = json_decode($decoded, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded_json)) {
                    if (!empty($decoded_json['staff_id']) && is_numeric($decoded_json['staff_id'])) {
                        $result['staff_id'] = (int)$decoded_json['staff_id'];
                    }
                    if (!empty($decoded_json['employee_id'])) {
                        $result['employee_id'] = trim((string)$decoded_json['employee_id']);
                    }
                    if (!empty($result['staff_id']) || !empty($result['employee_id'])) {
                        return $result;
                    }
                }
            }
        }

        if (strpos($raw_payload, 'DIAGOMA-STAFF:') === 0) {
            $staff_part = trim(substr($raw_payload, strlen('DIAGOMA-STAFF:')));
            if (is_numeric($staff_part)) {
                $result['staff_id'] = (int)$staff_part;
                return $result;
            }
        }

        if (strpos($raw_payload, 'STAFF_ID:') === 0) {
            $staff_part = trim(substr($raw_payload, strlen('STAFF_ID:')));
            if (is_numeric($staff_part)) {
                $result['staff_id'] = (int)$staff_part;
                return $result;
            }
        }

        // Supporte un format URL du type ?staff_id=12&employee_id=EMP001.
        if (filter_var($raw_payload, FILTER_VALIDATE_URL)) {
            $query = parse_url($raw_payload, PHP_URL_QUERY);
            if (!empty($query)) {
                parse_str($query, $params);
                if (!empty($params['staff_id']) && is_numeric($params['staff_id'])) {
                    $result['staff_id'] = (int)$params['staff_id'];
                }
                if (!empty($params['employee_id'])) {
                    $result['employee_id'] = trim((string)$params['employee_id']);
                }
                if (!empty($result['staff_id']) || !empty($result['employee_id'])) {
                    return $result;
                }
            }
        }

        if (is_numeric($raw_payload)) {
            $result['staff_id'] = (int)$raw_payload;
            return $result;
        }

        $result['employee_id'] = $raw_payload;
        return $result;
    }

    public function getAbsencesList() {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $employee_id = $this->input->post('employee_id');
        $date_from = $this->input->post('date_from');
        $date_to = $this->input->post('date_to');

        // Convertir les dates du format français au format SQL
        if ($date_from) {
            $date_from = date('Y-m-d', strtotime(str_replace('-', '/', $date_from)));
        }
        if ($date_to) {
            $date_to = date('Y-m-d', strtotime(str_replace('-', '/', $date_to)));
        }

        $absences = $this->staffattendancemodel->getAbsencesByFilters($employee_id, $date_from, $date_to);
        echo json_encode($absences);
    }

    public function getGlobalStats() {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $year = $this->input->post('year');
        $month = $this->input->post('month');

        $stats = $this->staffattendancemodel->getGlobalStatistics($year, $month);
        echo json_encode($stats);
    }

    public function getEmployeeStats() {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }

        $employee_id = $this->input->post('employee_id');
        $year = $this->input->post('year');
        $month = $this->input->post('month');

        $stats = $this->staffattendancemodel->getEmployeeStatistics($employee_id, $year, $month);
        echo json_encode($stats);
    }

}

?>