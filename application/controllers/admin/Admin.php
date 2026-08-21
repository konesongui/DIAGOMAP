<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
} 

class Admin extends Admin_Controller
{

    public function __construct_19()
    {
        parent::__construct();
        $this->load->model("classteacher_model");
        $this->load->model("Staff_model");
        $this->load->library('Enc_lib');
        $this->sch_setting_detail = $this->setting_model->getSetting();

    }

    public function __construct()
    {
        parent::__construct();
        $this->load->model("classteacher_model");
        $this->load->model("Staff_model");
        $this->load->library('Enc_lib');

        // ===== RÉCUPÉRATION DES SETTINGS AVEC GESTION DES ERREURS =====
        try {
            // Récupérer les données de l'utilisateur
            $userdata = $this->customlib->getUserData();
            $staff_id = $userdata['id'] ?? 0;
            $entreprise_id = $userdata['entreprise_id'] ?? 0;
            $role_id = $userdata['role_id'] ?? 0;

            // ===== IDENTIFICATION DU SUPER ADMIN =====
            // Le super admin est identifié par :
            // - ID = 1 (compte principal)
            // - OU role_id = 7 (rôle Super Admin)
            // - OU is_superadmin = 1 dans la table roles
            $is_super_admin = false;

            // Vérification 1 : ID = 1
            if ($staff_id == 1) {
                $is_super_admin = true;
            }

            // Vérification 2 : role_id = 7 (Super Admin)
            if (!$is_super_admin && $role_id == 7) {
                $is_super_admin = true;
            }

            // Vérification 3 : via la table roles (is_superadmin = 1)
            if (!$is_super_admin && $role_id > 0) {
                $this->db->select('is_superadmin');
                $this->db->from('roles');
                $this->db->where('id', $role_id);
                $role = $this->db->get()->row();
                if ($role && $role->is_superadmin == 1) {
                    $is_super_admin = true;
                }
            }

            // Essayer de récupérer les settings de l'entreprise
            if ($entreprise_id > 0) {
                $this->db->select('*');
                $this->db->from('sch_settings');
                $this->db->where('entreprise_id', $entreprise_id);
                $query = $this->db->get();

                if ($query->num_rows() > 0) {
                    $this->sch_setting_detail = $query->row();
                } else {
                    // Fallback : récupérer le premier enregistrement
                    $this->db->select('*');
                    $this->db->from('sch_settings');
                    $this->db->order_by('id');
                    $this->db->limit(1);
                    $query = $this->db->get();

                    if ($query->num_rows() > 0) {
                        $this->sch_setting_detail = $query->row();
                    } else {
                        // Créer un objet par défaut
                        $this->sch_setting_detail = $this->createDefaultSettings();
                    }
                }
            } else {
                // Super admin ou utilisateur sans entreprise : récupérer le premier
                $this->db->select('*');
                $this->db->from('sch_settings');
                $this->db->order_by('id');
                $this->db->limit(1);
                $query = $this->db->get();

                if ($query->num_rows() > 0) {
                    $this->sch_setting_detail = $query->row();
                } else {
                    $this->sch_setting_detail = $this->createDefaultSettings();
                }
            }
        } catch (Exception $e) {
            $this->sch_setting_detail = $this->createDefaultSettings();
            log_message('error', 'Erreur dans Admin_Controller constructeur: ' . $e->getMessage());
        }

        // ===== VÉRIFICATION DE L'ENTREPRISE ET DU STAFF =====
        // Vérifier si l'utilisateur est connecté
        if ($this->session->userdata('admin')) {
            $userdata = $this->customlib->getUserData();
            $staff_id = $userdata['id'] ?? 0;
            $role_id = $userdata['role_id'] ?? 0;
            $entreprise_id = $userdata['entreprise_id'] ?? 0;

            // ===== IDENTIFICATION DU SUPER ADMIN =====
            $is_super_admin = false;
            if ($staff_id == 1 || $role_id == 7) {
                $is_super_admin = true;
            }

            // 1. Vérifier si le staff est actif (pour tous les utilisateurs)
            if ($staff_id > 0) {
                $this->db->select('is_active');
                $this->db->from('staff');
                $this->db->where('id', $staff_id);
                $staff = $this->db->get()->row();

                if (!$staff || $staff->is_active != 1) {
                    $this->session->unset_userdata('admin');
                    $this->session->set_flashdata('error', 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.');
                    redirect('site/login');
                    return;
                }
            }

            // 2. Vérifier le statut de l'entreprise (exclure super admin)
            if (!$is_super_admin && $entreprise_id > 0) {
                $this->db->select('statut, nom, date_expiration');
                $this->db->from('compte_entreprise');
                $this->db->where('id', $entreprise_id);
                $entreprise = $this->db->get()->row();

                if ($entreprise) {
                    if ($entreprise->statut == 'suspendu') {
                        $this->session->unset_userdata('admin');
                        $this->session->set_flashdata('error', 'Votre entreprise "' . $entreprise->nom . '" est suspendue. Veuillez contacter l\'administrateur.');
                        redirect('site/login');
                        return;
                    }

                    if ($entreprise->statut == 'expiré') {
                        $this->session->unset_userdata('admin');
                        $this->session->set_flashdata('error', 'Votre abonnement pour "' . $entreprise->nom . '" a expiré. Veuillez renouveler votre abonnement.');
                        redirect('site/login');
                        return;
                    }
                }
            }
        }
    }

    /**
     * Crée un objet de paramètres par défaut
     */
    private function createDefaultSettings()
    {
        $settings = new stdClass();
        $settings->id = 1;
        $settings->attendence_type = 0;
        $settings->name = 'Mon Entreprise';
        $settings->email = '';
        $settings->phone = '';
        $settings->address = '';
        $settings->date_format = 'd-m-Y';
        $settings->currency_symbol = 'FCFA';
        $settings->currency_place = 'after_number';
        $settings->start_month = 'January';
        $settings->start_week = 'Monday';
        $settings->timezone = 'UTC';
        $settings->is_rtl = 'disabled';
        $settings->theme = 'default.jpg';
        $settings->image = '';
        $settings->admin_logo = '';
        $settings->admin_small_logo = '';
        $settings->lang_id = 1;
        $settings->session_id = 1;
        $settings->currency = 'XOF';
        $settings->biometric = 0;
        $settings->biometric_device = '';
        $settings->adm_auto_insert = 1;
        $settings->adm_prefix = 'ssadm';
        $settings->adm_start_from = '1';
        $settings->adm_no_digit = 6;
        $settings->adm_update_status = 1;
        $settings->staffid_auto_insert = 1;
        $settings->staffid_prefix = 'staff';
        $settings->staffid_start_from = '1';
        $settings->staffid_no_digit = 6;
        $settings->staffid_update_status = 1;
        $settings->class_teacher = 'no';
        $settings->is_duplicate_fees_invoice = 0;
        $settings->is_student_house = 1;
        $settings->is_blood_group = 1;
        $settings->online_admission = 0;
        $settings->online_admission_payment = '';
        $settings->online_admission_amount = 0;
        $settings->online_admission_instruction = '';
        $settings->online_admission_conditions = '';
        $settings->cron_secret_key = md5(uniqid(rand(), true));
        $settings->app_primary_color_code = '#273772';
        $settings->app_secondary_color_code = '#ffc107';
        $settings->mobile_api_url = '';
        $settings->student_profile_edit = 0;
        $settings->my_question = 0;
        $settings->roll_no = 1;
        $settings->category = 1;
        $settings->cast = 1;
        $settings->religion = 1;
        $settings->mobile_no = 1;
        $settings->student_email = 1;
        $settings->admission_date = 1;
        $settings->lastname = 1;
        $settings->middlename = 1;
        $settings->student_photo = 1;
        $settings->student_height = 1;
        $settings->student_weight = 1;
        $settings->measurement_date = 1;
        $settings->father_name = 1;
        $settings->father_phone = 1;
        $settings->father_occupation = 1;
        $settings->father_pic = 1;
        $settings->mother_name = 1;
        $settings->mother_phone = 1;
        $settings->mother_occupation = 1;
        $settings->mother_pic = 1;
        $settings->guardian_name = 1;
        $settings->guardian_relation = 1;
        $settings->guardian_phone = 1;
        $settings->guardian_email = 1;
        $settings->guardian_pic = 1;
        $settings->guardian_occupation = 1;
        $settings->guardian_address = 1;
        $settings->current_address = 1;
        $settings->permanent_address = 1;
        $settings->route_list = 1;
        $settings->hostel_id = 1;
        $settings->bank_account_no = 1;
        $settings->bank_name = 1;
        $settings->ifsc_code = 1;
        $settings->national_identification_no = 1;
        $settings->local_identification_no = 1;
        $settings->rte = 1;
        $settings->previous_school_details = 1;
        $settings->student_note = 1;
        $settings->upload_documents = 1;
        $settings->staff_designation = 1;
        $settings->staff_department = 1;
        $settings->staff_last_name = 1;
        $settings->staff_father_name = 1;
        $settings->staff_mother_name = 1;
        $settings->staff_date_of_joining = 1;
        $settings->staff_phone = 1;
        $settings->staff_emergency_contact = 1;
        $settings->staff_marital_status = 1;
        $settings->staff_photo = 1;
        $settings->staff_current_address = 1;
        $settings->staff_permanent_address = 1;
        $settings->staff_qualification = 1;
        $settings->staff_work_experience = 1;
        $settings->staff_note = 1;
        $settings->staff_epf_no = 1;
        $settings->staff_basic_salary = 1;
        $settings->staff_contract_type = 1;
        $settings->staff_work_shift = 1;
        $settings->staff_work_location = 1;
        $settings->staff_leaves = 1;
        $settings->staff_account_details = 1;
        $settings->staff_social_media = 1;
        $settings->staff_upload_documents = 1;

        return $settings;
    }


    public function unauthorized()
    {
        $data = array();
        $this->load->view('layout/header', $data);
        $this->load->view('unauthorized', $data);
        $this->load->view('layout/footer', $data);
    }

    public function tickets()
    {
         // ===== RÉCUPÉRATION DU PAYS =====
    if ($this->session->userdata('user_country')) {
        $data['user_country'] = $this->session->userdata('user_country');
    } else {
        $data['user_country'] = $this->getUserCountry();
        $this->session->set_userdata('user_country', $data['user_country']);
    }
    
        $data['dashboard'] = $this->admin_model->getDashboardData();

        $role            = $this->customlib->getStaffRole();
        $role_id         = json_decode($role)->id;
        $data['role_id'] = $role_id;

        $staffid       = $this->customlib->getStaffID();
        $notifications = $this->notification_model->getUnreadStaffNotification($staffid, $role_id);

        $data['notifications'] = $notifications;
        $input                 = $this->setting_model->getCurrentSessionName();

        list($a, $b)  = explode('-', $input);
        $Current_year = $a;
        if (strlen($b) == 2) {
            $Next_year = substr($a, 0, 2) . $b;
        } else {
            $Next_year = $b;
        }
        $data['mysqlVersion'] = $this->setting_model->getMysqlVersion();
        $data['sqlMode']      = $this->setting_model->getSqlMode();
        //========================== Current Attendence ==========================
        $current_date       = date('Y-m-d');
        $data['title']      = 'Comptabilité OHADA';
        $Current_start_date = date('01');
        $Current_date       = date('d');
        $Current_month      = date('m');
        $month_collection   = 0;
        $month_expense      = 0;
        $total_students     = 0;
        $total_teachers     = 0;
        $ar                 = $this->startmonthandend();
        $year_str_month     = $Current_year . '-' . $ar[0] . '-01';
        $year_end_month     = date("Y-m-t", strtotime($Next_year . '-' . $ar[1] . '-01'));
        $getDepositeAmount  = $this->studentfeemaster_model->getDepositAmountBetweenDate($year_str_month, $year_end_month);
        //======================Current Month Collection ==============================
        $first_day_this_month     = date('Y-m-01');
        $current_month_collection = $this->studentfeemaster_model->getDepositAmountBetweenDate($first_day_this_month, $current_date);
        $month_collection         = $this->whatever($current_month_collection, $first_day_this_month, $current_date);
        $expense                  = $this->expense_model->getTotalExpenseBwdate($first_day_this_month, $current_date);
        if (!empty($expense)) {
            $month_expense = $month_expense + $expense->amount;
        }

        $data['month_collection'] = $month_collection;
        $data['month_expense']    = $month_expense;

        $tot_students = $this->studentsession_model->getTotalStudentBySession();
        if (!empty($tot_students)) {
            $total_students = $tot_students->total_student;
        }

        $data['total_students'] = $total_students;

        $tot_roles = $this->role_model->get();

        foreach ($tot_roles as $key => $value) {

            $count_roles[$value["name"]] = $this->role_model->count_roles($value["id"]);

        }
        $data["roles"] = $count_roles;

        //======================== get collection by month ==========================
        $start_month = strtotime($year_str_month);
        $start       = strtotime($year_str_month);
        $end         = strtotime($year_end_month);
        $coll_month  = array();
        $s           = array();
        $total_month = array();
        while ($start_month <= $end) {
            $total_month[] = date('M', $start_month);
            $month_start   = date('Y-m-d', $start_month);
            $month_end     = date("Y-m-t", $start_month);
            $return        = $this->whatever($getDepositeAmount, $month_start, $month_end);
            if ($return) {
                $s[] = $return;
            } else {
                $s[] = "0.00";
            }

            $start_month = strtotime("+1 month", $start_month);
        }
        //======================== getexpense by month ==============================
        $ex                  = array();
        $start_session_month = strtotime($year_str_month);
        while ($start_session_month <= $end) {

            $month_start = date('Y-m-d', $start_session_month);
            $month_end   = date("Y-m-t", $start_session_month);

            $expense_monthly = $this->expense_model->getTotalExpenseBwdate($month_start, $month_end);

            if (!empty($expense_monthly)) {
                $amt  = 0;
                $ex[] = $amt + $expense_monthly->amount;
            }

            $start_session_month = strtotime("+1 month", $start_session_month);
        }

        $data['yearly_collection'] = $s;
        $data['yearly_expense']    = $ex;
        $data['total_month']       = $total_month;

        //======================= current month collection /expense ===================
        // hardcoded '01' for first day
        $startdate       = date('m/01/Y');
        $enddate         = date('m/t/Y');
        $start           = strtotime($startdate);
        $end             = strtotime($enddate);
        $currentdate     = $start;
        $month_days      = array();
        $days_collection = array();
        while ($currentdate <= $end) {
            $cur_date          = date('Y-m-d', $currentdate);
            $month_days[]      = date('d', $currentdate);
            $coll_amt          = $this->whatever($getDepositeAmount, $cur_date, $cur_date);
            $days_collection[] = $coll_amt;
            $currentdate       = strtotime('+1 day', $currentdate);
        }
        $data['current_month_days'] = $month_days;
        $data['days_collection']    = $days_collection;

        //======================= current month /expense ==============================
        // hardcoded '01' for first day

        $startdate    = date('m/01/Y');
        $enddate      = date('m/t/Y');
        $start        = strtotime($startdate);
        $end          = strtotime($enddate);
        $currentdate  = $start;
        $days_expense = array();
        while ($currentdate <= $end) {
            $cur_date       = date('Y-m-d', $currentdate);
            $month_days[]   = date('d', $currentdate);
            $currentdate    = strtotime('+1 day', $currentdate);
            $ct             = $this->getExpensebyday($cur_date);
            $days_expense[] = $ct;
        }

        $data['days_expense']        = $days_expense;
        $student_fee_history         = $this->studentfee_model->getTodayStudentFees();
        $data['student_fee_history'] = $student_fee_history;

        $event_colors         = array("#03a9f4", "#c53da9", "#757575", "#8e24aa", "#d81b60", "#7cb342", "#fb8c00", "#fb3b3b");
        $data["event_colors"] = $event_colors;
        $userdata             = $this->customlib->getUserData();
        $data["role"]         = $userdata["user_type"];
        $start_date           = date('Y-m-01');
        $end_date             = date('Y-m-t');
        $student_due_fee      = $this->studentfeemaster_model->getFeesAwaiting($start_date, $end_date);

        $data['fees_awaiting'] = $student_due_fee;

        $total_fess    = 0;
        $total_paid    = 0;
        $total_unpaid  = 0;
        $total_partial = 0;

        if (!empty($data['fees_awaiting'])) {

            foreach ($data['fees_awaiting'] as $awaiting_key => $awaiting_value) {

                $amount_to_be_taken = 0;
                if ($awaiting_value->is_system) {
                    if ($awaiting_value->amount > 0) {
                        $amount_to_be_taken = $awaiting_value->amount;
                    }
                } elseif ($awaiting_value->is_system == 0) {
                    if ($awaiting_value->fee_amount > 0) {
                        $amount_to_be_taken = $awaiting_value->fee_amount;
                    }

                }
                if ($amount_to_be_taken > 0) {
                    $total_fess++;

                    if (is_string($awaiting_value->amount_detail) && is_array(json_decode($awaiting_value->amount_detail, true)) && (json_last_error() == JSON_ERROR_NONE)) {
                        $amount_paid_details = (json_decode($awaiting_value->amount_detail));
                        $amt_                = 0;
                        foreach ($amount_paid_details as $amount_paid_detail_key => $amount_paid_detail_value) {
                            $amt_ = $amt_ + $amount_paid_detail_value->amount;
                        }

                        if (($amt_ + $amount_paid_detail_value->amount_discount) >= $amount_to_be_taken) {
                            $total_paid++;
                        } elseif (($amt_ + $amount_paid_detail_value->amount_discount) < $amount_to_be_taken) {
                            $total_partial++;
                        }
                    } else {
                        $total_unpaid++;
                    }

                }
            }
        }

        $data['incomegraph'] = $this->income_model->getIncomeHeadsData($start_date, $end_date);
        $data['expensegraph'] = $this->expense_model->getExpenseHeadData($start_date, $end_date);
        $enquiry       = $this->admin_model->getAllEnquiryCount($start_date, $end_date);
        $total_counter = $total_paid + $total_unpaid + $total_partial;

        $data['fees_overview'] = array(
            'total_unpaid'     => $total_unpaid,
            'unpaid_progress'  => ($total_counter > 0) ? (($total_unpaid * 100) / $total_counter) : 0,
            'total_paid'       => $total_paid,
            'paid_progress'    => ($total_counter > 0) ? (($total_paid * 100) / $total_counter) : 0,
            'total_partial'    => $total_partial,
            'partial_progress' => ($total_counter > 0) ? (($total_partial * 100) / $total_counter) : 0,
        );

        $total_enquiry = $enquiry['total'];

        if ($total_enquiry > 0) {

            $data['enquiry_overview'] = array(
                'won'              => $enquiry['complete'],
                'won_progress'     => ($enquiry['complete'] * 100) / $total_enquiry,
                'active'           => $enquiry['active'],
                'active_progress'  => ($enquiry['active'] * 100) / $total_enquiry,
                'passive'          => $enquiry['passive'],
                'passive_progress' => ($enquiry['passive'] * 100) / $total_enquiry,
                'dead'             => $enquiry['dead'],
                'dead_progress'    => ($enquiry['dead'] * 100) / $total_enquiry,
                'lost'             => $enquiry['lost'],
                'lost_progress'    => ($enquiry['lost'] * 100) / $total_enquiry,
            );

        } else {

            $data['enquiry_overview'] = array(
                'won'              => 0,
                'won_progress'     => 0,
                'active'           => 0,
                'active_progress'  => 0,
                'passive'          => 0,
                'passive_progress' => 0,
                'dead'             => 0,
                'dead_progress'    => 0,
                'lost'             => 0,
                'lost_progress'    => 0,
            );

        }

        $data['total_paid'] = $total_paid;
        $data['total_fees'] = $total_fess;
        if ($total_fess > 0) {
            $data['fessprogressbar'] = ($total_paid * 100) / $total_fess;
        } else {
            $data['fessprogressbar'] = 0;
        }

        $data['total_enquiry']  = $total_enquiry  = $enquiry['total'];
        $data['total_complete'] = $complete_enquiry = $enquiry['complete'];
        if ($total_enquiry > 0) {
            $data['fenquiryprogressbar'] = ($complete_enquiry * 100) / $total_enquiry;
        } else {
            $data['fenquiryprogressbar'] = 0;
        }

        $bookoverview      = $this->book_model->bookoverview($start_date, $end_date);
        $bookduereport     = $this->bookissue_model->dueforreturn($start_date, $end_date);
        $forreturndata     = $this->bookissue_model->forreturn($start_date, $end_date);
        $dueforreturn      = $bookduereport[0]['total'];
        $forreturn         = $forreturndata[0]['total'];
        $total_qty         = $bookoverview[0]['qty'];
        $total_issued      = $bookoverview[0]['total_issue'];
        $availble          = '0';
        $availble_progress = 0;
        $issued_progress   = 0;
        if ($total_qty > 0) {
            $availble          = $total_qty - $total_issued;
            $availble_progress = ($availble * 100) / $total_qty;
            $issued_progress   = ($total_issued * 100) / $total_qty;
        }
        $data['book_overview'] = array(
            'total'             => $total_qty,
            'total_progress'    => 100,
            'availble'          => $availble,
            'availble_progress' => round($availble_progress, 2),
            'total_issued'      => $total_issued,
            'issued_progress'   => round($issued_progress, 2),
            'dueforreturn'      => $dueforreturn,
            'forreturn'         => $forreturn,
        );

        $Attendence                   = $this->stuattendence_model->getTodayDayAttendance($total_students);
        $data['attendence_data']      = $Attendence;
        $Staffattendence              = $this->Staff_model->getTodayDayAttendance();
        $data['Staffattendence_data'] = $Staffattendence;
        $getTotalStaff                = $this->Staff_model->getTotalStaff();
        $data['getTotalStaff_data']   = $getTotalStaff;
        if ($getTotalStaff > 0) {$percentTotalStaff_data = ($Staffattendence * 100) / ($getTotalStaff);} else { $percentTotalStaff_data = '0';}
        $data['percentTotalStaff_data'] = $percentTotalStaff_data;
        $data['sch_setting']            = $this->sch_setting_detail;

        if ($data['sch_setting']->attendence_type == 0) {
            $data['std_graphclass'] = "col-lg-3 col-md-6 col-sm-6";
        } else {
            $data['std_graphclass'] = "col-lg-4 col-md-6 col-sm-6";
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/tickets', $data);
        $this->load->view('layout/footer', $data);
    }

    public function associations()
    {
         // ===== RÉCUPÉRATION DU PAYS =====
    if ($this->session->userdata('user_country')) {
        $data['user_country'] = $this->session->userdata('user_country');
    } else {
        $data['user_country'] = $this->getUserCountry();
        $this->session->set_userdata('user_country', $data['user_country']);
    }
    
        $data['dashboard'] = $this->admin_model->getDashboardData();

        $role            = $this->customlib->getStaffRole();
        $role_id         = json_decode($role)->id;
        $data['role_id'] = $role_id;

        $staffid       = $this->customlib->getStaffID();
        $notifications = $this->notification_model->getUnreadStaffNotification($staffid, $role_id);

        $data['notifications'] = $notifications;
        $input                 = $this->setting_model->getCurrentSessionName();

        list($a, $b)  = explode('-', $input);
        $Current_year = $a;
        if (strlen($b) == 2) {
            $Next_year = substr($a, 0, 2) . $b;
        } else {
            $Next_year = $b;
        }
        $data['mysqlVersion'] = $this->setting_model->getMysqlVersion();
        $data['sqlMode']      = $this->setting_model->getSqlMode();
        //========================== Current Attendence ==========================
        $current_date       = date('Y-m-d');
        $data['title']      = 'Dashboard';
        $Current_start_date = date('01');
        $Current_date       = date('d');
        $Current_month      = date('m');
        $month_collection   = 0;
        $month_expense      = 0;
        $total_students     = 0;
        $total_teachers     = 0;
        $ar                 = $this->startmonthandend();
        $year_str_month     = $Current_year . '-' . $ar[0] . '-01';
        $year_end_month     = date("Y-m-t", strtotime($Next_year . '-' . $ar[1] . '-01'));
        $getDepositeAmount  = $this->studentfeemaster_model->getDepositAmountBetweenDate($year_str_month, $year_end_month);
        //======================Current Month Collection ==============================
        $first_day_this_month     = date('Y-m-01');
        $current_month_collection = $this->studentfeemaster_model->getDepositAmountBetweenDate($first_day_this_month, $current_date);
        $month_collection         = $this->whatever($current_month_collection, $first_day_this_month, $current_date);
        $expense                  = $this->expense_model->getTotalExpenseBwdate($first_day_this_month, $current_date);
        if (!empty($expense)) {
            $month_expense = $month_expense + $expense->amount;
        }

        $data['month_collection'] = $month_collection;
        $data['month_expense']    = $month_expense;

        $tot_students = $this->studentsession_model->getTotalStudentBySession();
        if (!empty($tot_students)) {
            $total_students = $tot_students->total_student;
        }

        $data['total_students'] = $total_students;

        $tot_roles = $this->role_model->get();

        foreach ($tot_roles as $key => $value) {

            $count_roles[$value["name"]] = $this->role_model->count_roles($value["id"]);

        }
        $data["roles"] = $count_roles;

        //======================== get collection by month ==========================
        $start_month = strtotime($year_str_month);
        $start       = strtotime($year_str_month);
        $end         = strtotime($year_end_month);
        $coll_month  = array();
        $s           = array();
        $total_month = array();
        while ($start_month <= $end) {
            $total_month[] = date('M', $start_month);
            $month_start   = date('Y-m-d', $start_month);
            $month_end     = date("Y-m-t", $start_month);
            $return        = $this->whatever($getDepositeAmount, $month_start, $month_end);
            if ($return) {
                $s[] = $return;
            } else {
                $s[] = "0.00";
            }

            $start_month = strtotime("+1 month", $start_month);
        }
        //======================== getexpense by month ==============================
        $ex                  = array();
        $start_session_month = strtotime($year_str_month);
        while ($start_session_month <= $end) {

            $month_start = date('Y-m-d', $start_session_month);
            $month_end   = date("Y-m-t", $start_session_month);

            $expense_monthly = $this->expense_model->getTotalExpenseBwdate($month_start, $month_end);

            if (!empty($expense_monthly)) {
                $amt  = 0;
                $ex[] = $amt + $expense_monthly->amount;
            }

            $start_session_month = strtotime("+1 month", $start_session_month);
        }

        $data['yearly_collection'] = $s;
        $data['yearly_expense']    = $ex;
        $data['total_month']       = $total_month;

        //======================= current month collection /expense ===================
        // hardcoded '01' for first day
        $startdate       = date('m/01/Y');
        $enddate         = date('m/t/Y');
        $start           = strtotime($startdate);
        $end             = strtotime($enddate);
        $currentdate     = $start;
        $month_days      = array();
        $days_collection = array();
        while ($currentdate <= $end) {
            $cur_date          = date('Y-m-d', $currentdate);
            $month_days[]      = date('d', $currentdate);
            $coll_amt          = $this->whatever($getDepositeAmount, $cur_date, $cur_date);
            $days_collection[] = $coll_amt;
            $currentdate       = strtotime('+1 day', $currentdate);
        }
        $data['current_month_days'] = $month_days;
        $data['days_collection']    = $days_collection;

        //======================= current month /expense ==============================
        // hardcoded '01' for first day

        $startdate    = date('m/01/Y');
        $enddate      = date('m/t/Y');
        $start        = strtotime($startdate);
        $end          = strtotime($enddate);
        $currentdate  = $start;
        $days_expense = array();
        while ($currentdate <= $end) {
            $cur_date       = date('Y-m-d', $currentdate);
            $month_days[]   = date('d', $currentdate);
            $currentdate    = strtotime('+1 day', $currentdate);
            $ct             = $this->getExpensebyday($cur_date);
            $days_expense[] = $ct;
        }

        $data['days_expense']        = $days_expense;
        $student_fee_history         = $this->studentfee_model->getTodayStudentFees();
        $data['student_fee_history'] = $student_fee_history;

        $event_colors         = array("#03a9f4", "#c53da9", "#757575", "#8e24aa", "#d81b60", "#7cb342", "#fb8c00", "#fb3b3b");
        $data["event_colors"] = $event_colors;
        $userdata             = $this->customlib->getUserData();
        $data["role"]         = $userdata["user_type"];
        $start_date           = date('Y-m-01');
        $end_date             = date('Y-m-t');
        $student_due_fee      = $this->studentfeemaster_model->getFeesAwaiting($start_date, $end_date);

        $data['fees_awaiting'] = $student_due_fee;

        $total_fess    = 0;
        $total_paid    = 0;
        $total_unpaid  = 0;
        $total_partial = 0;

        if (!empty($data['fees_awaiting'])) {

            foreach ($data['fees_awaiting'] as $awaiting_key => $awaiting_value) {

                $amount_to_be_taken = 0;
                if ($awaiting_value->is_system) {
                    if ($awaiting_value->amount > 0) {
                        $amount_to_be_taken = $awaiting_value->amount;
                    }
                } elseif ($awaiting_value->is_system == 0) {
                    if ($awaiting_value->fee_amount > 0) {
                        $amount_to_be_taken = $awaiting_value->fee_amount;
                    }

                }
                if ($amount_to_be_taken > 0) {
                    $total_fess++;

                    if (is_string($awaiting_value->amount_detail) && is_array(json_decode($awaiting_value->amount_detail, true)) && (json_last_error() == JSON_ERROR_NONE)) {
                        $amount_paid_details = (json_decode($awaiting_value->amount_detail));
                        $amt_                = 0;
                        foreach ($amount_paid_details as $amount_paid_detail_key => $amount_paid_detail_value) {
                            $amt_ = $amt_ + $amount_paid_detail_value->amount;
                        }

                        if (($amt_ + $amount_paid_detail_value->amount_discount) >= $amount_to_be_taken) {
                            $total_paid++;
                        } elseif (($amt_ + $amount_paid_detail_value->amount_discount) < $amount_to_be_taken) {
                            $total_partial++;
                        }
                    } else {
                        $total_unpaid++;
                    }

                }
            }
        }

        $data['incomegraph'] = $this->income_model->getIncomeHeadsData($start_date, $end_date);
        $data['expensegraph'] = $this->expense_model->getExpenseHeadData($start_date, $end_date);
        $enquiry       = $this->admin_model->getAllEnquiryCount($start_date, $end_date);
        $total_counter = $total_paid + $total_unpaid + $total_partial;

        $data['fees_overview'] = array(
            'total_unpaid'     => $total_unpaid,
            'unpaid_progress'  => ($total_counter > 0) ? (($total_unpaid * 100) / $total_counter) : 0,
            'total_paid'       => $total_paid,
            'paid_progress'    => ($total_counter > 0) ? (($total_paid * 100) / $total_counter) : 0,
            'total_partial'    => $total_partial,
            'partial_progress' => ($total_counter > 0) ? (($total_partial * 100) / $total_counter) : 0,
        );

        $total_enquiry = $enquiry['total'];

        if ($total_enquiry > 0) {

            $data['enquiry_overview'] = array(
                'won'              => $enquiry['complete'],
                'won_progress'     => ($enquiry['complete'] * 100) / $total_enquiry,
                'active'           => $enquiry['active'],
                'active_progress'  => ($enquiry['active'] * 100) / $total_enquiry,
                'passive'          => $enquiry['passive'],
                'passive_progress' => ($enquiry['passive'] * 100) / $total_enquiry,
                'dead'             => $enquiry['dead'],
                'dead_progress'    => ($enquiry['dead'] * 100) / $total_enquiry,
                'lost'             => $enquiry['lost'],
                'lost_progress'    => ($enquiry['lost'] * 100) / $total_enquiry,
            );

        } else {

            $data['enquiry_overview'] = array(
                'won'              => 0,
                'won_progress'     => 0,
                'active'           => 0,
                'active_progress'  => 0,
                'passive'          => 0,
                'passive_progress' => 0,
                'dead'             => 0,
                'dead_progress'    => 0,
                'lost'             => 0,
                'lost_progress'    => 0,
            );

        }

        $data['total_paid'] = $total_paid;
        $data['total_fees'] = $total_fess;
        if ($total_fess > 0) {
            $data['fessprogressbar'] = ($total_paid * 100) / $total_fess;
        } else {
            $data['fessprogressbar'] = 0;
        }

        $data['total_enquiry']  = $total_enquiry  = $enquiry['total'];
        $data['total_complete'] = $complete_enquiry = $enquiry['complete'];
        if ($total_enquiry > 0) {
            $data['fenquiryprogressbar'] = ($complete_enquiry * 100) / $total_enquiry;
        } else {
            $data['fenquiryprogressbar'] = 0;
        }

        $bookoverview      = $this->book_model->bookoverview($start_date, $end_date);
        $bookduereport     = $this->bookissue_model->dueforreturn($start_date, $end_date);
        $forreturndata     = $this->bookissue_model->forreturn($start_date, $end_date);
        $dueforreturn      = $bookduereport[0]['total'];
        $forreturn         = $forreturndata[0]['total'];
        $total_qty         = $bookoverview[0]['qty'];
        $total_issued      = $bookoverview[0]['total_issue'];
        $availble          = '0';
        $availble_progress = 0;
        $issued_progress   = 0;
        if ($total_qty > 0) {
            $availble          = $total_qty - $total_issued;
            $availble_progress = ($availble * 100) / $total_qty;
            $issued_progress   = ($total_issued * 100) / $total_qty;
        }
        $data['book_overview'] = array(
            'total'             => $total_qty,
            'total_progress'    => 100,
            'availble'          => $availble,
            'availble_progress' => round($availble_progress, 2),
            'total_issued'      => $total_issued,
            'issued_progress'   => round($issued_progress, 2),
            'dueforreturn'      => $dueforreturn,
            'forreturn'         => $forreturn,
        );

        $Attendence                   = $this->stuattendence_model->getTodayDayAttendance($total_students);
        $data['attendence_data']      = $Attendence;
        $Staffattendence              = $this->Staff_model->getTodayDayAttendance();
        $data['Staffattendence_data'] = $Staffattendence;
        $getTotalStaff                = $this->Staff_model->getTotalStaff();
        $data['getTotalStaff_data']   = $getTotalStaff;
        if ($getTotalStaff > 0) {$percentTotalStaff_data = ($Staffattendence * 100) / ($getTotalStaff);} else { $percentTotalStaff_data = '0';}
        $data['percentTotalStaff_data'] = $percentTotalStaff_data;
        $data['sch_setting']            = $this->sch_setting_detail;

        if ($data['sch_setting']->attendence_type == 0) {
            $data['std_graphclass'] = "col-lg-3 col-md-6 col-sm-6";
        } else {
            $data['std_graphclass'] = "col-lg-4 col-md-6 col-sm-6";
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/associations', $data);
        $this->load->view('layout/footer', $data);
    }

    public function church()
    {
         // ===== RÉCUPÉRATION DU PAYS =====
    if ($this->session->userdata('user_country')) {
        $data['user_country'] = $this->session->userdata('user_country');
    } else {
        $data['user_country'] = $this->getUserCountry();
        $this->session->set_userdata('user_country', $data['user_country']);
    }
    
        $data['dashboard'] = $this->admin_model->getDashboardData();

        $role            = $this->customlib->getStaffRole();
        $role_id         = json_decode($role)->id;
        $data['role_id'] = $role_id;

        $staffid       = $this->customlib->getStaffID();
        $notifications = $this->notification_model->getUnreadStaffNotification($staffid, $role_id);

        $data['notifications'] = $notifications;
        $input                 = $this->setting_model->getCurrentSessionName();

        list($a, $b)  = explode('-', $input);
        $Current_year = $a;
        if (strlen($b) == 2) {
            $Next_year = substr($a, 0, 2) . $b;
        } else {
            $Next_year = $b;
        }
        $data['mysqlVersion'] = $this->setting_model->getMysqlVersion();
        $data['sqlMode']      = $this->setting_model->getSqlMode();
        //========================== Current Attendence ==========================
        $current_date       = date('Y-m-d');
        $data['title']      = 'Dashboard';
        $Current_start_date = date('01');
        $Current_date       = date('d');
        $Current_month      = date('m');
        $month_collection   = 0;
        $month_expense      = 0;
        $total_students     = 0;
        $total_teachers     = 0;
        $ar                 = $this->startmonthandend();
        $year_str_month     = $Current_year . '-' . $ar[0] . '-01';
        $year_end_month     = date("Y-m-t", strtotime($Next_year . '-' . $ar[1] . '-01'));
        $getDepositeAmount  = $this->studentfeemaster_model->getDepositAmountBetweenDate($year_str_month, $year_end_month);
        //======================Current Month Collection ==============================
        $first_day_this_month     = date('Y-m-01');
        $current_month_collection = $this->studentfeemaster_model->getDepositAmountBetweenDate($first_day_this_month, $current_date);
        $month_collection         = $this->whatever($current_month_collection, $first_day_this_month, $current_date);
        $expense                  = $this->expense_model->getTotalExpenseBwdate($first_day_this_month, $current_date);
        if (!empty($expense)) {
            $month_expense = $month_expense + $expense->amount;
        }

        $data['month_collection'] = $month_collection;
        $data['month_expense']    = $month_expense;

        $tot_students = $this->studentsession_model->getTotalStudentBySession();
        if (!empty($tot_students)) {
            $total_students = $tot_students->total_student;
        }

        $data['total_students'] = $total_students;

        $tot_roles = $this->role_model->get();

        foreach ($tot_roles as $key => $value) {

            $count_roles[$value["name"]] = $this->role_model->count_roles($value["id"]);

        }
        $data["roles"] = $count_roles;

        //======================== get collection by month ==========================
        $start_month = strtotime($year_str_month);
        $start       = strtotime($year_str_month);
        $end         = strtotime($year_end_month);
        $coll_month  = array();
        $s           = array();
        $total_month = array();
        while ($start_month <= $end) {
            $total_month[] = date('M', $start_month);
            $month_start   = date('Y-m-d', $start_month);
            $month_end     = date("Y-m-t", $start_month);
            $return        = $this->whatever($getDepositeAmount, $month_start, $month_end);
            if ($return) {
                $s[] = $return;
            } else {
                $s[] = "0.00";
            }

            $start_month = strtotime("+1 month", $start_month);
        }
        //======================== getexpense by month ==============================
        $ex                  = array();
        $start_session_month = strtotime($year_str_month);
        while ($start_session_month <= $end) {

            $month_start = date('Y-m-d', $start_session_month);
            $month_end   = date("Y-m-t", $start_session_month);

            $expense_monthly = $this->expense_model->getTotalExpenseBwdate($month_start, $month_end);

            if (!empty($expense_monthly)) {
                $amt  = 0;
                $ex[] = $amt + $expense_monthly->amount;
            }

            $start_session_month = strtotime("+1 month", $start_session_month);
        }

        $data['yearly_collection'] = $s;
        $data['yearly_expense']    = $ex;
        $data['total_month']       = $total_month;

        //======================= current month collection /expense ===================
        // hardcoded '01' for first day
        $startdate       = date('m/01/Y');
        $enddate         = date('m/t/Y');
        $start           = strtotime($startdate);
        $end             = strtotime($enddate);
        $currentdate     = $start;
        $month_days      = array();
        $days_collection = array();
        while ($currentdate <= $end) {
            $cur_date          = date('Y-m-d', $currentdate);
            $month_days[]      = date('d', $currentdate);
            $coll_amt          = $this->whatever($getDepositeAmount, $cur_date, $cur_date);
            $days_collection[] = $coll_amt;
            $currentdate       = strtotime('+1 day', $currentdate);
        }
        $data['current_month_days'] = $month_days;
        $data['days_collection']    = $days_collection;

        //======================= current month /expense ==============================
        // hardcoded '01' for first day

        $startdate    = date('m/01/Y');
        $enddate      = date('m/t/Y');
        $start        = strtotime($startdate);
        $end          = strtotime($enddate);
        $currentdate  = $start;
        $days_expense = array();
        while ($currentdate <= $end) {
            $cur_date       = date('Y-m-d', $currentdate);
            $month_days[]   = date('d', $currentdate);
            $currentdate    = strtotime('+1 day', $currentdate);
            $ct             = $this->getExpensebyday($cur_date);
            $days_expense[] = $ct;
        }

        $data['days_expense']        = $days_expense;
        $student_fee_history         = $this->studentfee_model->getTodayStudentFees();
        $data['student_fee_history'] = $student_fee_history;

        $event_colors         = array("#03a9f4", "#c53da9", "#757575", "#8e24aa", "#d81b60", "#7cb342", "#fb8c00", "#fb3b3b");
        $data["event_colors"] = $event_colors;
        $userdata             = $this->customlib->getUserData();
        $data["role"]         = $userdata["user_type"];
        $start_date           = date('Y-m-01');
        $end_date             = date('Y-m-t');
        $student_due_fee      = $this->studentfeemaster_model->getFeesAwaiting($start_date, $end_date);

        $data['fees_awaiting'] = $student_due_fee;

        $total_fess    = 0;
        $total_paid    = 0;
        $total_unpaid  = 0;
        $total_partial = 0;

        if (!empty($data['fees_awaiting'])) {

            foreach ($data['fees_awaiting'] as $awaiting_key => $awaiting_value) {

                $amount_to_be_taken = 0;
                if ($awaiting_value->is_system) {
                    if ($awaiting_value->amount > 0) {
                        $amount_to_be_taken = $awaiting_value->amount;
                    }
                } elseif ($awaiting_value->is_system == 0) {
                    if ($awaiting_value->fee_amount > 0) {
                        $amount_to_be_taken = $awaiting_value->fee_amount;
                    }

                }
                if ($amount_to_be_taken > 0) {
                    $total_fess++;

                    if (is_string($awaiting_value->amount_detail) && is_array(json_decode($awaiting_value->amount_detail, true)) && (json_last_error() == JSON_ERROR_NONE)) {
                        $amount_paid_details = (json_decode($awaiting_value->amount_detail));
                        $amt_                = 0;
                        foreach ($amount_paid_details as $amount_paid_detail_key => $amount_paid_detail_value) {
                            $amt_ = $amt_ + $amount_paid_detail_value->amount;
                        }

                        if (($amt_ + $amount_paid_detail_value->amount_discount) >= $amount_to_be_taken) {
                            $total_paid++;
                        } elseif (($amt_ + $amount_paid_detail_value->amount_discount) < $amount_to_be_taken) {
                            $total_partial++;
                        }
                    } else {
                        $total_unpaid++;
                    }

                }
            }
        }

        $data['incomegraph'] = $this->income_model->getIncomeHeadsData($start_date, $end_date);
        $data['expensegraph'] = $this->expense_model->getExpenseHeadData($start_date, $end_date);
        $enquiry       = $this->admin_model->getAllEnquiryCount($start_date, $end_date);
        $total_counter = $total_paid + $total_unpaid + $total_partial;

        $data['fees_overview'] = array(
            'total_unpaid'     => $total_unpaid,
            'unpaid_progress'  => ($total_counter > 0) ? (($total_unpaid * 100) / $total_counter) : 0,
            'total_paid'       => $total_paid,
            'paid_progress'    => ($total_counter > 0) ? (($total_paid * 100) / $total_counter) : 0,
            'total_partial'    => $total_partial,
            'partial_progress' => ($total_counter > 0) ? (($total_partial * 100) / $total_counter) : 0,
        );

        $total_enquiry = $enquiry['total'];

        if ($total_enquiry > 0) {

            $data['enquiry_overview'] = array(
                'won'              => $enquiry['complete'],
                'won_progress'     => ($enquiry['complete'] * 100) / $total_enquiry,
                'active'           => $enquiry['active'],
                'active_progress'  => ($enquiry['active'] * 100) / $total_enquiry,
                'passive'          => $enquiry['passive'],
                'passive_progress' => ($enquiry['passive'] * 100) / $total_enquiry,
                'dead'             => $enquiry['dead'],
                'dead_progress'    => ($enquiry['dead'] * 100) / $total_enquiry,
                'lost'             => $enquiry['lost'],
                'lost_progress'    => ($enquiry['lost'] * 100) / $total_enquiry,
            );

        } else {

            $data['enquiry_overview'] = array(
                'won'              => 0,
                'won_progress'     => 0,
                'active'           => 0,
                'active_progress'  => 0,
                'passive'          => 0,
                'passive_progress' => 0,
                'dead'             => 0,
                'dead_progress'    => 0,
                'lost'             => 0,
                'lost_progress'    => 0,
            );

        }

        $data['total_paid'] = $total_paid;
        $data['total_fees'] = $total_fess;
        if ($total_fess > 0) {
            $data['fessprogressbar'] = ($total_paid * 100) / $total_fess;
        } else {
            $data['fessprogressbar'] = 0;
        }

        $data['total_enquiry']  = $total_enquiry  = $enquiry['total'];
        $data['total_complete'] = $complete_enquiry = $enquiry['complete'];
        if ($total_enquiry > 0) {
            $data['fenquiryprogressbar'] = ($complete_enquiry * 100) / $total_enquiry;
        } else {
            $data['fenquiryprogressbar'] = 0;
        }

        $bookoverview      = $this->book_model->bookoverview($start_date, $end_date);
        $bookduereport     = $this->bookissue_model->dueforreturn($start_date, $end_date);
        $forreturndata     = $this->bookissue_model->forreturn($start_date, $end_date);
        $dueforreturn      = $bookduereport[0]['total'];
        $forreturn         = $forreturndata[0]['total'];
        $total_qty         = $bookoverview[0]['qty'];
        $total_issued      = $bookoverview[0]['total_issue'];
        $availble          = '0';
        $availble_progress = 0;
        $issued_progress   = 0;
        if ($total_qty > 0) {
            $availble          = $total_qty - $total_issued;
            $availble_progress = ($availble * 100) / $total_qty;
            $issued_progress   = ($total_issued * 100) / $total_qty;
        }
        $data['book_overview'] = array(
            'total'             => $total_qty,
            'total_progress'    => 100,
            'availble'          => $availble,
            'availble_progress' => round($availble_progress, 2),
            'total_issued'      => $total_issued,
            'issued_progress'   => round($issued_progress, 2),
            'dueforreturn'      => $dueforreturn,
            'forreturn'         => $forreturn,
        );

        $Attendence                   = $this->stuattendence_model->getTodayDayAttendance($total_students);
        $data['attendence_data']      = $Attendence;
        $Staffattendence              = $this->Staff_model->getTodayDayAttendance();
        $data['Staffattendence_data'] = $Staffattendence;
        $getTotalStaff                = $this->Staff_model->getTotalStaff();
        $data['getTotalStaff_data']   = $getTotalStaff;
        if ($getTotalStaff > 0) {$percentTotalStaff_data = ($Staffattendence * 100) / ($getTotalStaff);} else { $percentTotalStaff_data = '0';}
        $data['percentTotalStaff_data'] = $percentTotalStaff_data;
        $data['sch_setting']            = $this->sch_setting_detail;

        if ($data['sch_setting']->attendence_type == 0) {
            $data['std_graphclass'] = "col-lg-3 col-md-6 col-sm-6";
        } else {
            $data['std_graphclass'] = "col-lg-4 col-md-6 col-sm-6";
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/church', $data);
        $this->load->view('layout/footer', $data);
    }

    public function hub()
    {
         // ===== RÉCUPÉRATION DU PAYS =====
    if ($this->session->userdata('user_country')) {
        $data['user_country'] = $this->session->userdata('user_country');
    } else {
        $data['user_country'] = $this->getUserCountry();
        $this->session->set_userdata('user_country', $data['user_country']);
    }
    
        $data['dashboard'] = $this->admin_model->getDashboardData();

        $role            = $this->customlib->getStaffRole();
        $role_id         = json_decode($role)->id;
        $data['role_id'] = $role_id;

        $staffid       = $this->customlib->getStaffID();
        $notifications = $this->notification_model->getUnreadStaffNotification($staffid, $role_id);

        $data['notifications'] = $notifications;
        $input                 = $this->setting_model->getCurrentSessionName();

        list($a, $b)  = explode('-', $input);
        $Current_year = $a;
        if (strlen($b) == 2) {
            $Next_year = substr($a, 0, 2) . $b;
        } else {
            $Next_year = $b;
        }
        $data['mysqlVersion'] = $this->setting_model->getMysqlVersion();
        $data['sqlMode']      = $this->setting_model->getSqlMode();
        //========================== Current Attendence ==========================
        $current_date       = date('Y-m-d');
        $data['title']      = 'Dashboard';
        $Current_start_date = date('01');
        $Current_date       = date('d');
        $Current_month      = date('m');
        $month_collection   = 0;
        $month_expense      = 0;
        $total_students     = 0;
        $total_teachers     = 0;
        $ar                 = $this->startmonthandend();
        $year_str_month     = $Current_year . '-' . $ar[0] . '-01';
        $year_end_month     = date("Y-m-t", strtotime($Next_year . '-' . $ar[1] . '-01'));
        $getDepositeAmount  = $this->studentfeemaster_model->getDepositAmountBetweenDate($year_str_month, $year_end_month);
        //======================Current Month Collection ==============================
        $first_day_this_month     = date('Y-m-01');
        $current_month_collection = $this->studentfeemaster_model->getDepositAmountBetweenDate($first_day_this_month, $current_date);
        $month_collection         = $this->whatever($current_month_collection, $first_day_this_month, $current_date);
        $expense                  = $this->expense_model->getTotalExpenseBwdate($first_day_this_month, $current_date);
        if (!empty($expense)) {
            $month_expense = $month_expense + $expense->amount;
        }

        $data['month_collection'] = $month_collection;
        $data['month_expense']    = $month_expense;

        $tot_students = $this->studentsession_model->getTotalStudentBySession();
        if (!empty($tot_students)) {
            $total_students = $tot_students->total_student;
        }

        $data['total_students'] = $total_students;

        $tot_roles = $this->role_model->get();

        foreach ($tot_roles as $key => $value) {

            $count_roles[$value["name"]] = $this->role_model->count_roles($value["id"]);

        }
        $data["roles"] = $count_roles;

        //======================== get collection by month ==========================
        $start_month = strtotime($year_str_month);
        $start       = strtotime($year_str_month);
        $end         = strtotime($year_end_month);
        $coll_month  = array();
        $s           = array();
        $total_month = array();
        while ($start_month <= $end) {
            $total_month[] = date('M', $start_month);
            $month_start   = date('Y-m-d', $start_month);
            $month_end     = date("Y-m-t", $start_month);
            $return        = $this->whatever($getDepositeAmount, $month_start, $month_end);
            if ($return) {
                $s[] = $return;
            } else {
                $s[] = "0.00";
            }

            $start_month = strtotime("+1 month", $start_month);
        }
        //======================== getexpense by month ==============================
        $ex                  = array();
        $start_session_month = strtotime($year_str_month);
        while ($start_session_month <= $end) {

            $month_start = date('Y-m-d', $start_session_month);
            $month_end   = date("Y-m-t", $start_session_month);

            $expense_monthly = $this->expense_model->getTotalExpenseBwdate($month_start, $month_end);

            if (!empty($expense_monthly)) {
                $amt  = 0;
                $ex[] = $amt + $expense_monthly->amount;
            }

            $start_session_month = strtotime("+1 month", $start_session_month);
        }

        $data['yearly_collection'] = $s;
        $data['yearly_expense']    = $ex;
        $data['total_month']       = $total_month;

        //======================= current month collection /expense ===================
        // hardcoded '01' for first day
        $startdate       = date('m/01/Y');
        $enddate         = date('m/t/Y');
        $start           = strtotime($startdate);
        $end             = strtotime($enddate);
        $currentdate     = $start;
        $month_days      = array();
        $days_collection = array();
        while ($currentdate <= $end) {
            $cur_date          = date('Y-m-d', $currentdate);
            $month_days[]      = date('d', $currentdate);
            $coll_amt          = $this->whatever($getDepositeAmount, $cur_date, $cur_date);
            $days_collection[] = $coll_amt;
            $currentdate       = strtotime('+1 day', $currentdate);
        }
        $data['current_month_days'] = $month_days;
        $data['days_collection']    = $days_collection;

        //======================= current month /expense ==============================
        // hardcoded '01' for first day

        $startdate    = date('m/01/Y');
        $enddate      = date('m/t/Y');
        $start        = strtotime($startdate);
        $end          = strtotime($enddate);
        $currentdate  = $start;
        $days_expense = array();
        while ($currentdate <= $end) {
            $cur_date       = date('Y-m-d', $currentdate);
            $month_days[]   = date('d', $currentdate);
            $currentdate    = strtotime('+1 day', $currentdate);
            $ct             = $this->getExpensebyday($cur_date);
            $days_expense[] = $ct;
        }

        $data['days_expense']        = $days_expense;
        $student_fee_history         = $this->studentfee_model->getTodayStudentFees();
        $data['student_fee_history'] = $student_fee_history;

        $event_colors         = array("#03a9f4", "#c53da9", "#757575", "#8e24aa", "#d81b60", "#7cb342", "#fb8c00", "#fb3b3b");
        $data["event_colors"] = $event_colors;
        $userdata             = $this->customlib->getUserData();
        $data["role"]         = $userdata["user_type"];
        $start_date           = date('Y-m-01');
        $end_date             = date('Y-m-t');
        $student_due_fee      = $this->studentfeemaster_model->getFeesAwaiting($start_date, $end_date);

        $data['fees_awaiting'] = $student_due_fee;

        $total_fess    = 0;
        $total_paid    = 0;
        $total_unpaid  = 0;
        $total_partial = 0;

        if (!empty($data['fees_awaiting'])) {

            foreach ($data['fees_awaiting'] as $awaiting_key => $awaiting_value) {

                $amount_to_be_taken = 0;
                if ($awaiting_value->is_system) {
                    if ($awaiting_value->amount > 0) {
                        $amount_to_be_taken = $awaiting_value->amount;
                    }
                } elseif ($awaiting_value->is_system == 0) {
                    if ($awaiting_value->fee_amount > 0) {
                        $amount_to_be_taken = $awaiting_value->fee_amount;
                    }

                }
                if ($amount_to_be_taken > 0) {
                    $total_fess++;

                    if (is_string($awaiting_value->amount_detail) && is_array(json_decode($awaiting_value->amount_detail, true)) && (json_last_error() == JSON_ERROR_NONE)) {
                        $amount_paid_details = (json_decode($awaiting_value->amount_detail));
                        $amt_                = 0;
                        foreach ($amount_paid_details as $amount_paid_detail_key => $amount_paid_detail_value) {
                            $amt_ = $amt_ + $amount_paid_detail_value->amount;
                        }

                        if (($amt_ + $amount_paid_detail_value->amount_discount) >= $amount_to_be_taken) {
                            $total_paid++;
                        } elseif (($amt_ + $amount_paid_detail_value->amount_discount) < $amount_to_be_taken) {
                            $total_partial++;
                        }
                    } else {
                        $total_unpaid++;
                    }

                }
            }
        }

        $data['incomegraph'] = $this->income_model->getIncomeHeadsData($start_date, $end_date);
        $data['expensegraph'] = $this->expense_model->getExpenseHeadData($start_date, $end_date);
        $enquiry       = $this->admin_model->getAllEnquiryCount($start_date, $end_date);
        $total_counter = $total_paid + $total_unpaid + $total_partial;

        $data['fees_overview'] = array(
            'total_unpaid'     => $total_unpaid,
            'unpaid_progress'  => ($total_counter > 0) ? (($total_unpaid * 100) / $total_counter) : 0,
            'total_paid'       => $total_paid,
            'paid_progress'    => ($total_counter > 0) ? (($total_paid * 100) / $total_counter) : 0,
            'total_partial'    => $total_partial,
            'partial_progress' => ($total_counter > 0) ? (($total_partial * 100) / $total_counter) : 0,
        );

        $total_enquiry = $enquiry['total'];

        if ($total_enquiry > 0) {

            $data['enquiry_overview'] = array(
                'won'              => $enquiry['complete'],
                'won_progress'     => ($enquiry['complete'] * 100) / $total_enquiry,
                'active'           => $enquiry['active'],
                'active_progress'  => ($enquiry['active'] * 100) / $total_enquiry,
                'passive'          => $enquiry['passive'],
                'passive_progress' => ($enquiry['passive'] * 100) / $total_enquiry,
                'dead'             => $enquiry['dead'],
                'dead_progress'    => ($enquiry['dead'] * 100) / $total_enquiry,
                'lost'             => $enquiry['lost'],
                'lost_progress'    => ($enquiry['lost'] * 100) / $total_enquiry,
            );

        } else {

            $data['enquiry_overview'] = array(
                'won'              => 0,
                'won_progress'     => 0,
                'active'           => 0,
                'active_progress'  => 0,
                'passive'          => 0,
                'passive_progress' => 0,
                'dead'             => 0,
                'dead_progress'    => 0,
                'lost'             => 0,
                'lost_progress'    => 0,
            );

        }

        $data['total_paid'] = $total_paid;
        $data['total_fees'] = $total_fess;
        if ($total_fess > 0) {
            $data['fessprogressbar'] = ($total_paid * 100) / $total_fess;
        } else {
            $data['fessprogressbar'] = 0;
        }

        $data['total_enquiry']  = $total_enquiry  = $enquiry['total'];
        $data['total_complete'] = $complete_enquiry = $enquiry['complete'];
        if ($total_enquiry > 0) {
            $data['fenquiryprogressbar'] = ($complete_enquiry * 100) / $total_enquiry;
        } else {
            $data['fenquiryprogressbar'] = 0;
        }

        $bookoverview      = $this->book_model->bookoverview($start_date, $end_date);
        $bookduereport     = $this->bookissue_model->dueforreturn($start_date, $end_date);
        $forreturndata     = $this->bookissue_model->forreturn($start_date, $end_date);
        $dueforreturn      = $bookduereport[0]['total'];
        $forreturn         = $forreturndata[0]['total'];
        $total_qty         = $bookoverview[0]['qty'];
        $total_issued      = $bookoverview[0]['total_issue'];
        $availble          = '0';
        $availble_progress = 0;
        $issued_progress   = 0;
        if ($total_qty > 0) {
            $availble          = $total_qty - $total_issued;
            $availble_progress = ($availble * 100) / $total_qty;
            $issued_progress   = ($total_issued * 100) / $total_qty;
        }
        $data['book_overview'] = array(
            'total'             => $total_qty,
            'total_progress'    => 100,
            'availble'          => $availble,
            'availble_progress' => round($availble_progress, 2),
            'total_issued'      => $total_issued,
            'issued_progress'   => round($issued_progress, 2),
            'dueforreturn'      => $dueforreturn,
            'forreturn'         => $forreturn,
        );

        $Attendence                   = $this->stuattendence_model->getTodayDayAttendance($total_students);
        $data['attendence_data']      = $Attendence;
        $Staffattendence              = $this->Staff_model->getTodayDayAttendance();
        $data['Staffattendence_data'] = $Staffattendence;
        $getTotalStaff                = $this->Staff_model->getTotalStaff();
        $data['getTotalStaff_data']   = $getTotalStaff;
        if ($getTotalStaff > 0) {$percentTotalStaff_data = ($Staffattendence * 100) / ($getTotalStaff);} else { $percentTotalStaff_data = '0';}
        $data['percentTotalStaff_data'] = $percentTotalStaff_data;
        $data['sch_setting']            = $this->sch_setting_detail;

        if ($data['sch_setting']->attendence_type == 0) {
            $data['std_graphclass'] = "col-lg-3 col-md-6 col-sm-6";
        } else {
            $data['std_graphclass'] = "col-lg-4 col-md-6 col-sm-6";
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/hub', $data);
        $this->load->view('layout/footer', $data);
    }
    public function rh()
    {
         // ===== RÉCUPÉRATION DU PAYS =====
    if ($this->session->userdata('user_country')) {
        $data['user_country'] = $this->session->userdata('user_country');
    } else {
        $data['user_country'] = $this->getUserCountry();
        $this->session->set_userdata('user_country', $data['user_country']);
    }
    
        $data['dashboard'] = $this->admin_model->getDashboardData();

        $role            = $this->customlib->getStaffRole();
        $role_id         = json_decode($role)->id;
        $data['role_id'] = $role_id;

        $staffid       = $this->customlib->getStaffID();
        $notifications = $this->notification_model->getUnreadStaffNotification($staffid, $role_id);

        $data['notifications'] = $notifications;
        $input                 = $this->setting_model->getCurrentSessionName();

        list($a, $b)  = explode('-', $input);
        $Current_year = $a;
        if (strlen($b) == 2) {
            $Next_year = substr($a, 0, 2) . $b;
        } else {
            $Next_year = $b;
        }
        $data['mysqlVersion'] = $this->setting_model->getMysqlVersion();
        $data['sqlMode']      = $this->setting_model->getSqlMode();
        //========================== Current Attendence ==========================
        $current_date       = date('Y-m-d');
        $data['title']      = 'Dashboard';
        $Current_start_date = date('01');
        $Current_date       = date('d');
        $Current_month      = date('m');
        $month_collection   = 0;
        $month_expense      = 0;
        $total_students     = 0;
        $total_teachers     = 0;
        $ar                 = $this->startmonthandend();
        $year_str_month     = $Current_year . '-' . $ar[0] . '-01';
        $year_end_month     = date("Y-m-t", strtotime($Next_year . '-' . $ar[1] . '-01'));
        $getDepositeAmount  = $this->studentfeemaster_model->getDepositAmountBetweenDate($year_str_month, $year_end_month);
        //======================Current Month Collection ==============================
        $first_day_this_month     = date('Y-m-01');
        $current_month_collection = $this->studentfeemaster_model->getDepositAmountBetweenDate($first_day_this_month, $current_date);
        $month_collection         = $this->whatever($current_month_collection, $first_day_this_month, $current_date);
        $expense                  = $this->expense_model->getTotalExpenseBwdate($first_day_this_month, $current_date);
        if (!empty($expense)) {
            $month_expense = $month_expense + $expense->amount;
        }

        $data['month_collection'] = $month_collection;
        $data['month_expense']    = $month_expense;

        $tot_students = $this->studentsession_model->getTotalStudentBySession();
        if (!empty($tot_students)) {
            $total_students = $tot_students->total_student;
        }

        $data['total_students'] = $total_students;

        $tot_roles = $this->role_model->get();

        foreach ($tot_roles as $key => $value) {

            $count_roles[$value["name"]] = $this->role_model->count_roles($value["id"]);

        }
        $data["roles"] = $count_roles;

        //======================== get collection by month ==========================
        $start_month = strtotime($year_str_month);
        $start       = strtotime($year_str_month);
        $end         = strtotime($year_end_month);
        $coll_month  = array();
        $s           = array();
        $total_month = array();
        while ($start_month <= $end) {
            $total_month[] = date('M', $start_month);
            $month_start   = date('Y-m-d', $start_month);
            $month_end     = date("Y-m-t", $start_month);
            $return        = $this->whatever($getDepositeAmount, $month_start, $month_end);
            if ($return) {
                $s[] = $return;
            } else {
                $s[] = "0.00";
            }

            $start_month = strtotime("+1 month", $start_month);
        }
        //======================== getexpense by month ==============================
        $ex                  = array();
        $start_session_month = strtotime($year_str_month);
        while ($start_session_month <= $end) {

            $month_start = date('Y-m-d', $start_session_month);
            $month_end   = date("Y-m-t", $start_session_month);

            $expense_monthly = $this->expense_model->getTotalExpenseBwdate($month_start, $month_end);

            if (!empty($expense_monthly)) {
                $amt  = 0;
                $ex[] = $amt + $expense_monthly->amount;
            }

            $start_session_month = strtotime("+1 month", $start_session_month);
        }

        $data['yearly_collection'] = $s;
        $data['yearly_expense']    = $ex;
        $data['total_month']       = $total_month;

        //======================= current month collection /expense ===================
        // hardcoded '01' for first day
        $startdate       = date('m/01/Y');
        $enddate         = date('m/t/Y');
        $start           = strtotime($startdate);
        $end             = strtotime($enddate);
        $currentdate     = $start;
        $month_days      = array();
        $days_collection = array();
        while ($currentdate <= $end) {
            $cur_date          = date('Y-m-d', $currentdate);
            $month_days[]      = date('d', $currentdate);
            $coll_amt          = $this->whatever($getDepositeAmount, $cur_date, $cur_date);
            $days_collection[] = $coll_amt;
            $currentdate       = strtotime('+1 day', $currentdate);
        }
        $data['current_month_days'] = $month_days;
        $data['days_collection']    = $days_collection;

        //======================= current month /expense ==============================
        // hardcoded '01' for first day

        $startdate    = date('m/01/Y');
        $enddate      = date('m/t/Y');
        $start        = strtotime($startdate);
        $end          = strtotime($enddate);
        $currentdate  = $start;
        $days_expense = array();
        while ($currentdate <= $end) {
            $cur_date       = date('Y-m-d', $currentdate);
            $month_days[]   = date('d', $currentdate);
            $currentdate    = strtotime('+1 day', $currentdate);
            $ct             = $this->getExpensebyday($cur_date);
            $days_expense[] = $ct;
        }

        $data['days_expense']        = $days_expense;
        $student_fee_history         = $this->studentfee_model->getTodayStudentFees();
        $data['student_fee_history'] = $student_fee_history;

        $event_colors         = array("#03a9f4", "#c53da9", "#757575", "#8e24aa", "#d81b60", "#7cb342", "#fb8c00", "#fb3b3b");
        $data["event_colors"] = $event_colors;
        $userdata             = $this->customlib->getUserData();
        $data["role"]         = $userdata["user_type"];
        $start_date           = date('Y-m-01');
        $end_date             = date('Y-m-t');
        $student_due_fee      = $this->studentfeemaster_model->getFeesAwaiting($start_date, $end_date);

        $data['fees_awaiting'] = $student_due_fee;

        $total_fess    = 0;
        $total_paid    = 0;
        $total_unpaid  = 0;
        $total_partial = 0;

        if (!empty($data['fees_awaiting'])) {

            foreach ($data['fees_awaiting'] as $awaiting_key => $awaiting_value) {

                $amount_to_be_taken = 0;
                if ($awaiting_value->is_system) {
                    if ($awaiting_value->amount > 0) {
                        $amount_to_be_taken = $awaiting_value->amount;
                    }
                } elseif ($awaiting_value->is_system == 0) {
                    if ($awaiting_value->fee_amount > 0) {
                        $amount_to_be_taken = $awaiting_value->fee_amount;
                    }

                }
                if ($amount_to_be_taken > 0) {
                    $total_fess++;

                    if (is_string($awaiting_value->amount_detail) && is_array(json_decode($awaiting_value->amount_detail, true)) && (json_last_error() == JSON_ERROR_NONE)) {
                        $amount_paid_details = (json_decode($awaiting_value->amount_detail));
                        $amt_                = 0;
                        foreach ($amount_paid_details as $amount_paid_detail_key => $amount_paid_detail_value) {
                            $amt_ = $amt_ + $amount_paid_detail_value->amount;
                        }

                        if (($amt_ + $amount_paid_detail_value->amount_discount) >= $amount_to_be_taken) {
                            $total_paid++;
                        } elseif (($amt_ + $amount_paid_detail_value->amount_discount) < $amount_to_be_taken) {
                            $total_partial++;
                        }
                    } else {
                        $total_unpaid++;
                    }

                }
            }
        }

        $data['incomegraph'] = $this->income_model->getIncomeHeadsData($start_date, $end_date);
        $data['expensegraph'] = $this->expense_model->getExpenseHeadData($start_date, $end_date);
        $enquiry       = $this->admin_model->getAllEnquiryCount($start_date, $end_date);
        $total_counter = $total_paid + $total_unpaid + $total_partial;

        $data['fees_overview'] = array(
            'total_unpaid'     => $total_unpaid,
            'unpaid_progress'  => ($total_counter > 0) ? (($total_unpaid * 100) / $total_counter) : 0,
            'total_paid'       => $total_paid,
            'paid_progress'    => ($total_counter > 0) ? (($total_paid * 100) / $total_counter) : 0,
            'total_partial'    => $total_partial,
            'partial_progress' => ($total_counter > 0) ? (($total_partial * 100) / $total_counter) : 0,
        );

        $total_enquiry = $enquiry['total'];

        if ($total_enquiry > 0) {

            $data['enquiry_overview'] = array(
                'won'              => $enquiry['complete'],
                'won_progress'     => ($enquiry['complete'] * 100) / $total_enquiry,
                'active'           => $enquiry['active'],
                'active_progress'  => ($enquiry['active'] * 100) / $total_enquiry,
                'passive'          => $enquiry['passive'],
                'passive_progress' => ($enquiry['passive'] * 100) / $total_enquiry,
                'dead'             => $enquiry['dead'],
                'dead_progress'    => ($enquiry['dead'] * 100) / $total_enquiry,
                'lost'             => $enquiry['lost'],
                'lost_progress'    => ($enquiry['lost'] * 100) / $total_enquiry,
            );

        } else {

            $data['enquiry_overview'] = array(
                'won'              => 0,
                'won_progress'     => 0,
                'active'           => 0,
                'active_progress'  => 0,
                'passive'          => 0,
                'passive_progress' => 0,
                'dead'             => 0,
                'dead_progress'    => 0,
                'lost'             => 0,
                'lost_progress'    => 0,
            );

        }

        $data['total_paid'] = $total_paid;
        $data['total_fees'] = $total_fess;
        if ($total_fess > 0) {
            $data['fessprogressbar'] = ($total_paid * 100) / $total_fess;
        } else {
            $data['fessprogressbar'] = 0;
        }

        $data['total_enquiry']  = $total_enquiry  = $enquiry['total'];
        $data['total_complete'] = $complete_enquiry = $enquiry['complete'];
        if ($total_enquiry > 0) {
            $data['fenquiryprogressbar'] = ($complete_enquiry * 100) / $total_enquiry;
        } else {
            $data['fenquiryprogressbar'] = 0;
        }

        $bookoverview      = $this->book_model->bookoverview($start_date, $end_date);
        $bookduereport     = $this->bookissue_model->dueforreturn($start_date, $end_date);
        $forreturndata     = $this->bookissue_model->forreturn($start_date, $end_date);
        $dueforreturn      = $bookduereport[0]['total'];
        $forreturn         = $forreturndata[0]['total'];
        $total_qty         = $bookoverview[0]['qty'];
        $total_issued      = $bookoverview[0]['total_issue'];
        $availble          = '0';
        $availble_progress = 0;
        $issued_progress   = 0;
        if ($total_qty > 0) {
            $availble          = $total_qty - $total_issued;
            $availble_progress = ($availble * 100) / $total_qty;
            $issued_progress   = ($total_issued * 100) / $total_qty;
        }
        $data['book_overview'] = array(
            'total'             => $total_qty,
            'total_progress'    => 100,
            'availble'          => $availble,
            'availble_progress' => round($availble_progress, 2),
            'total_issued'      => $total_issued,
            'issued_progress'   => round($issued_progress, 2),
            'dueforreturn'      => $dueforreturn,
            'forreturn'         => $forreturn,
        );

        $Attendence                   = $this->stuattendence_model->getTodayDayAttendance($total_students);
        $data['attendence_data']      = $Attendence;
        $Staffattendence              = $this->Staff_model->getTodayDayAttendance();
        $data['Staffattendence_data'] = $Staffattendence;
        $getTotalStaff                = $this->Staff_model->getTotalStaff();
        $data['getTotalStaff_data']   = $getTotalStaff;
        if ($getTotalStaff > 0) {$percentTotalStaff_data = ($Staffattendence * 100) / ($getTotalStaff);} else { $percentTotalStaff_data = '0';}
        $data['percentTotalStaff_data'] = $percentTotalStaff_data;
        $data['sch_setting']            = $this->sch_setting_detail;

        if ($data['sch_setting']->attendence_type == 0) {
            $data['std_graphclass'] = "col-lg-3 col-md-6 col-sm-6";
        } else {
            $data['std_graphclass'] = "col-lg-4 col-md-6 col-sm-6";
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/rh', $data);
        $this->load->view('layout/footer', $data);
    }

    public function commercial()
    {
         // ===== RÉCUPÉRATION DU PAYS =====
    if ($this->session->userdata('user_country')) {
        $data['user_country'] = $this->session->userdata('user_country');
    } else {
        $data['user_country'] = $this->getUserCountry();
        $this->session->set_userdata('user_country', $data['user_country']);
    }
    
        $data['dashboard'] = $this->admin_model->getDashboardData();

        $role            = $this->customlib->getStaffRole();
        $role_id         = json_decode($role)->id;
        $data['role_id'] = $role_id;

        $staffid       = $this->customlib->getStaffID();
        $notifications = $this->notification_model->getUnreadStaffNotification($staffid, $role_id);

        $data['notifications'] = $notifications;
        $input                 = $this->setting_model->getCurrentSessionName();

        list($a, $b)  = explode('-', $input);
        $Current_year = $a;
        if (strlen($b) == 2) {
            $Next_year = substr($a, 0, 2) . $b;
        } else {
            $Next_year = $b;
        }
        $data['mysqlVersion'] = $this->setting_model->getMysqlVersion();
        $data['sqlMode']      = $this->setting_model->getSqlMode();
        //========================== Current Attendence ==========================
        $current_date       = date('Y-m-d');
        $data['title']      = 'Dashboard';
        $Current_start_date = date('01');
        $Current_date       = date('d');
        $Current_month      = date('m');
        $month_collection   = 0;
        $month_expense      = 0;
        $total_students     = 0;
        $total_teachers     = 0;
        $ar                 = $this->startmonthandend();
        $year_str_month     = $Current_year . '-' . $ar[0] . '-01';
        $year_end_month     = date("Y-m-t", strtotime($Next_year . '-' . $ar[1] . '-01'));
        $getDepositeAmount  = $this->studentfeemaster_model->getDepositAmountBetweenDate($year_str_month, $year_end_month);
        //======================Current Month Collection ==============================
        $first_day_this_month     = date('Y-m-01');
        $current_month_collection = $this->studentfeemaster_model->getDepositAmountBetweenDate($first_day_this_month, $current_date);
        $month_collection         = $this->whatever($current_month_collection, $first_day_this_month, $current_date);
        $expense                  = $this->expense_model->getTotalExpenseBwdate($first_day_this_month, $current_date);
        if (!empty($expense)) {
            $month_expense = $month_expense + $expense->amount;
        }

        $data['month_collection'] = $month_collection;
        $data['month_expense']    = $month_expense;

        $tot_students = $this->studentsession_model->getTotalStudentBySession();
        if (!empty($tot_students)) {
            $total_students = $tot_students->total_student;
        }

        $data['total_students'] = $total_students;

        $tot_roles = $this->role_model->get();

        foreach ($tot_roles as $key => $value) {

            $count_roles[$value["name"]] = $this->role_model->count_roles($value["id"]);

        }
        $data["roles"] = $count_roles;

        //======================== get collection by month ==========================
        $start_month = strtotime($year_str_month);
        $start       = strtotime($year_str_month);
        $end         = strtotime($year_end_month);
        $coll_month  = array();
        $s           = array();
        $total_month = array();
        while ($start_month <= $end) {
            $total_month[] = date('M', $start_month);
            $month_start   = date('Y-m-d', $start_month);
            $month_end     = date("Y-m-t", $start_month);
            $return        = $this->whatever($getDepositeAmount, $month_start, $month_end);
            if ($return) {
                $s[] = $return;
            } else {
                $s[] = "0.00";
            }

            $start_month = strtotime("+1 month", $start_month);
        }
        //======================== getexpense by month ==============================
        $ex                  = array();
        $start_session_month = strtotime($year_str_month);
        while ($start_session_month <= $end) {

            $month_start = date('Y-m-d', $start_session_month);
            $month_end   = date("Y-m-t", $start_session_month);

            $expense_monthly = $this->expense_model->getTotalExpenseBwdate($month_start, $month_end);

            if (!empty($expense_monthly)) {
                $amt  = 0;
                $ex[] = $amt + $expense_monthly->amount;
            }

            $start_session_month = strtotime("+1 month", $start_session_month);
        }

        $data['yearly_collection'] = $s;
        $data['yearly_expense']    = $ex;
        $data['total_month']       = $total_month;

        //======================= current month collection /expense ===================
        // hardcoded '01' for first day
        $startdate       = date('m/01/Y');
        $enddate         = date('m/t/Y');
        $start           = strtotime($startdate);
        $end             = strtotime($enddate);
        $currentdate     = $start;
        $month_days      = array();
        $days_collection = array();
        while ($currentdate <= $end) {
            $cur_date          = date('Y-m-d', $currentdate);
            $month_days[]      = date('d', $currentdate);
            $coll_amt          = $this->whatever($getDepositeAmount, $cur_date, $cur_date);
            $days_collection[] = $coll_amt;
            $currentdate       = strtotime('+1 day', $currentdate);
        }
        $data['current_month_days'] = $month_days;
        $data['days_collection']    = $days_collection;

        //======================= current month /expense ==============================
        // hardcoded '01' for first day

        $startdate    = date('m/01/Y');
        $enddate      = date('m/t/Y');
        $start        = strtotime($startdate);
        $end          = strtotime($enddate);
        $currentdate  = $start;
        $days_expense = array();
        while ($currentdate <= $end) {
            $cur_date       = date('Y-m-d', $currentdate);
            $month_days[]   = date('d', $currentdate);
            $currentdate    = strtotime('+1 day', $currentdate);
            $ct             = $this->getExpensebyday($cur_date);
            $days_expense[] = $ct;
        }

        $data['days_expense']        = $days_expense;
        $student_fee_history         = $this->studentfee_model->getTodayStudentFees();
        $data['student_fee_history'] = $student_fee_history;

        $event_colors         = array("#03a9f4", "#c53da9", "#757575", "#8e24aa", "#d81b60", "#7cb342", "#fb8c00", "#fb3b3b");
        $data["event_colors"] = $event_colors;
        $userdata             = $this->customlib->getUserData();
        $data["role"]         = $userdata["user_type"];
        $start_date           = date('Y-m-01');
        $end_date             = date('Y-m-t');
        $student_due_fee      = $this->studentfeemaster_model->getFeesAwaiting($start_date, $end_date);

        $data['fees_awaiting'] = $student_due_fee;

        $total_fess    = 0;
        $total_paid    = 0;
        $total_unpaid  = 0;
        $total_partial = 0;

        if (!empty($data['fees_awaiting'])) {

            foreach ($data['fees_awaiting'] as $awaiting_key => $awaiting_value) {

                $amount_to_be_taken = 0;
                if ($awaiting_value->is_system) {
                    if ($awaiting_value->amount > 0) {
                        $amount_to_be_taken = $awaiting_value->amount;
                    }
                } elseif ($awaiting_value->is_system == 0) {
                    if ($awaiting_value->fee_amount > 0) {
                        $amount_to_be_taken = $awaiting_value->fee_amount;
                    }

                }
                if ($amount_to_be_taken > 0) {
                    $total_fess++;

                    if (is_string($awaiting_value->amount_detail) && is_array(json_decode($awaiting_value->amount_detail, true)) && (json_last_error() == JSON_ERROR_NONE)) {
                        $amount_paid_details = (json_decode($awaiting_value->amount_detail));
                        $amt_                = 0;
                        foreach ($amount_paid_details as $amount_paid_detail_key => $amount_paid_detail_value) {
                            $amt_ = $amt_ + $amount_paid_detail_value->amount;
                        }

                        if (($amt_ + $amount_paid_detail_value->amount_discount) >= $amount_to_be_taken) {
                            $total_paid++;
                        } elseif (($amt_ + $amount_paid_detail_value->amount_discount) < $amount_to_be_taken) {
                            $total_partial++;
                        }
                    } else {
                        $total_unpaid++;
                    }

                }
            }
        }

        $data['incomegraph'] = $this->income_model->getIncomeHeadsData($start_date, $end_date);
        $data['expensegraph'] = $this->expense_model->getExpenseHeadData($start_date, $end_date);
        $enquiry       = $this->admin_model->getAllEnquiryCount($start_date, $end_date);
        $total_counter = $total_paid + $total_unpaid + $total_partial;

        $data['fees_overview'] = array(
            'total_unpaid'     => $total_unpaid,
            'unpaid_progress'  => ($total_counter > 0) ? (($total_unpaid * 100) / $total_counter) : 0,
            'total_paid'       => $total_paid,
            'paid_progress'    => ($total_counter > 0) ? (($total_paid * 100) / $total_counter) : 0,
            'total_partial'    => $total_partial,
            'partial_progress' => ($total_counter > 0) ? (($total_partial * 100) / $total_counter) : 0,
        );

        $total_enquiry = $enquiry['total'];

        if ($total_enquiry > 0) {

            $data['enquiry_overview'] = array(
                'won'              => $enquiry['complete'],
                'won_progress'     => ($enquiry['complete'] * 100) / $total_enquiry,
                'active'           => $enquiry['active'],
                'active_progress'  => ($enquiry['active'] * 100) / $total_enquiry,
                'passive'          => $enquiry['passive'],
                'passive_progress' => ($enquiry['passive'] * 100) / $total_enquiry,
                'dead'             => $enquiry['dead'],
                'dead_progress'    => ($enquiry['dead'] * 100) / $total_enquiry,
                'lost'             => $enquiry['lost'],
                'lost_progress'    => ($enquiry['lost'] * 100) / $total_enquiry,
            );

        } else {

            $data['enquiry_overview'] = array(
                'won'              => 0,
                'won_progress'     => 0,
                'active'           => 0,
                'active_progress'  => 0,
                'passive'          => 0,
                'passive_progress' => 0,
                'dead'             => 0,
                'dead_progress'    => 0,
                'lost'             => 0,
                'lost_progress'    => 0,
            );

        }

        $data['total_paid'] = $total_paid;
        $data['total_fees'] = $total_fess;
        if ($total_fess > 0) {
            $data['fessprogressbar'] = ($total_paid * 100) / $total_fess;
        } else {
            $data['fessprogressbar'] = 0;
        }

        $data['total_enquiry']  = $total_enquiry  = $enquiry['total'];
        $data['total_complete'] = $complete_enquiry = $enquiry['complete'];
        if ($total_enquiry > 0) {
            $data['fenquiryprogressbar'] = ($complete_enquiry * 100) / $total_enquiry;
        } else {
            $data['fenquiryprogressbar'] = 0;
        }

        $bookoverview      = $this->book_model->bookoverview($start_date, $end_date);
        $bookduereport     = $this->bookissue_model->dueforreturn($start_date, $end_date);
        $forreturndata     = $this->bookissue_model->forreturn($start_date, $end_date);
        $dueforreturn      = $bookduereport[0]['total'];
        $forreturn         = $forreturndata[0]['total'];
        $total_qty         = $bookoverview[0]['qty'];
        $total_issued      = $bookoverview[0]['total_issue'];
        $availble          = '0';
        $availble_progress = 0;
        $issued_progress   = 0;
        if ($total_qty > 0) {
            $availble          = $total_qty - $total_issued;
            $availble_progress = ($availble * 100) / $total_qty;
            $issued_progress   = ($total_issued * 100) / $total_qty;
        }
        $data['book_overview'] = array(
            'total'             => $total_qty,
            'total_progress'    => 100,
            'availble'          => $availble,
            'availble_progress' => round($availble_progress, 2),
            'total_issued'      => $total_issued,
            'issued_progress'   => round($issued_progress, 2),
            'dueforreturn'      => $dueforreturn,
            'forreturn'         => $forreturn,
        );

        $Attendence                   = $this->stuattendence_model->getTodayDayAttendance($total_students);
        $data['attendence_data']      = $Attendence;
        $Staffattendence              = $this->Staff_model->getTodayDayAttendance();
        $data['Staffattendence_data'] = $Staffattendence;
        $getTotalStaff                = $this->Staff_model->getTotalStaff();
        $data['getTotalStaff_data']   = $getTotalStaff;
        if ($getTotalStaff > 0) {$percentTotalStaff_data = ($Staffattendence * 100) / ($getTotalStaff);} else { $percentTotalStaff_data = '0';}
        $data['percentTotalStaff_data'] = $percentTotalStaff_data;
        $data['sch_setting']            = $this->sch_setting_detail;

        if ($data['sch_setting']->attendence_type == 0) {
            $data['std_graphclass'] = "col-lg-3 col-md-6 col-sm-6";
        } else {
            $data['std_graphclass'] = "col-lg-4 col-md-6 col-sm-6";
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/commercial', $data);
        $this->load->view('layout/footer', $data);
    }

    public function setting()
    {
         // ===== RÉCUPÉRATION DU PAYS =====
    if ($this->session->userdata('user_country')) {
        $data['user_country'] = $this->session->userdata('user_country');
    } else {
        $data['user_country'] = $this->getUserCountry();
        $this->session->set_userdata('user_country', $data['user_country']);
    }
    
        $data['dashboard'] = $this->admin_model->getDashboardData();

        $role            = $this->customlib->getStaffRole();
        $role_id         = json_decode($role)->id;
        $data['role_id'] = $role_id;

        $staffid       = $this->customlib->getStaffID();
        $notifications = $this->notification_model->getUnreadStaffNotification($staffid, $role_id);

        $data['notifications'] = $notifications;
        $input                 = $this->setting_model->getCurrentSessionName();

        list($a, $b)  = explode('-', $input);
        $Current_year = $a;
        if (strlen($b) == 2) {
            $Next_year = substr($a, 0, 2) . $b;
        } else {
            $Next_year = $b;
        }
        $data['mysqlVersion'] = $this->setting_model->getMysqlVersion();
        $data['sqlMode']      = $this->setting_model->getSqlMode();
        //========================== Current Attendence ==========================
        $current_date       = date('Y-m-d');
        $data['title']      = 'Dashboard';
        $Current_start_date = date('01');
        $Current_date       = date('d');
        $Current_month      = date('m');
        $month_collection   = 0;
        $month_expense      = 0;
        $total_students     = 0;
        $total_teachers     = 0;
        $ar                 = $this->startmonthandend();
        $year_str_month     = $Current_year . '-' . $ar[0] . '-01';
        $year_end_month     = date("Y-m-t", strtotime($Next_year . '-' . $ar[1] . '-01'));
        $getDepositeAmount  = $this->studentfeemaster_model->getDepositAmountBetweenDate($year_str_month, $year_end_month);
        //======================Current Month Collection ==============================
        $first_day_this_month     = date('Y-m-01');
        $current_month_collection = $this->studentfeemaster_model->getDepositAmountBetweenDate($first_day_this_month, $current_date);
        $month_collection         = $this->whatever($current_month_collection, $first_day_this_month, $current_date);
        $expense                  = $this->expense_model->getTotalExpenseBwdate($first_day_this_month, $current_date);
        if (!empty($expense)) {
            $month_expense = $month_expense + $expense->amount;
        }

        $data['month_collection'] = $month_collection;
        $data['month_expense']    = $month_expense;

        $tot_students = $this->studentsession_model->getTotalStudentBySession();
        if (!empty($tot_students)) {
            $total_students = $tot_students->total_student;
        }

        $data['total_students'] = $total_students;

        $tot_roles = $this->role_model->get();

        foreach ($tot_roles as $key => $value) {

            $count_roles[$value["name"]] = $this->role_model->count_roles($value["id"]);

        }
        $data["roles"] = $count_roles;

        //======================== get collection by month ==========================
        $start_month = strtotime($year_str_month);
        $start       = strtotime($year_str_month);
        $end         = strtotime($year_end_month);
        $coll_month  = array();
        $s           = array();
        $total_month = array();
        while ($start_month <= $end) {
            $total_month[] = date('M', $start_month);
            $month_start   = date('Y-m-d', $start_month);
            $month_end     = date("Y-m-t", $start_month);
            $return        = $this->whatever($getDepositeAmount, $month_start, $month_end);
            if ($return) {
                $s[] = $return;
            } else {
                $s[] = "0.00";
            }

            $start_month = strtotime("+1 month", $start_month);
        }
        //======================== getexpense by month ==============================
        $ex                  = array();
        $start_session_month = strtotime($year_str_month);
        while ($start_session_month <= $end) {

            $month_start = date('Y-m-d', $start_session_month);
            $month_end   = date("Y-m-t", $start_session_month);

            $expense_monthly = $this->expense_model->getTotalExpenseBwdate($month_start, $month_end);

            if (!empty($expense_monthly)) {
                $amt  = 0;
                $ex[] = $amt + $expense_monthly->amount;
            }

            $start_session_month = strtotime("+1 month", $start_session_month);
        }

        $data['yearly_collection'] = $s;
        $data['yearly_expense']    = $ex;
        $data['total_month']       = $total_month;

        //======================= current month collection /expense ===================
        // hardcoded '01' for first day
        $startdate       = date('m/01/Y');
        $enddate         = date('m/t/Y');
        $start           = strtotime($startdate);
        $end             = strtotime($enddate);
        $currentdate     = $start;
        $month_days      = array();
        $days_collection = array();
        while ($currentdate <= $end) {
            $cur_date          = date('Y-m-d', $currentdate);
            $month_days[]      = date('d', $currentdate);
            $coll_amt          = $this->whatever($getDepositeAmount, $cur_date, $cur_date);
            $days_collection[] = $coll_amt;
            $currentdate       = strtotime('+1 day', $currentdate);
        }
        $data['current_month_days'] = $month_days;
        $data['days_collection']    = $days_collection;

        //======================= current month /expense ==============================
        // hardcoded '01' for first day

        $startdate    = date('m/01/Y');
        $enddate      = date('m/t/Y');
        $start        = strtotime($startdate);
        $end          = strtotime($enddate);
        $currentdate  = $start;
        $days_expense = array();
        while ($currentdate <= $end) {
            $cur_date       = date('Y-m-d', $currentdate);
            $month_days[]   = date('d', $currentdate);
            $currentdate    = strtotime('+1 day', $currentdate);
            $ct             = $this->getExpensebyday($cur_date);
            $days_expense[] = $ct;
        }

        $data['days_expense']        = $days_expense;
        $student_fee_history         = $this->studentfee_model->getTodayStudentFees();
        $data['student_fee_history'] = $student_fee_history;

        $event_colors         = array("#03a9f4", "#c53da9", "#757575", "#8e24aa", "#d81b60", "#7cb342", "#fb8c00", "#fb3b3b");
        $data["event_colors"] = $event_colors;
        $userdata             = $this->customlib->getUserData();
        $data["role"]         = $userdata["user_type"];
        $start_date           = date('Y-m-01');
        $end_date             = date('Y-m-t');
        $student_due_fee      = $this->studentfeemaster_model->getFeesAwaiting($start_date, $end_date);

        $data['fees_awaiting'] = $student_due_fee;

        $total_fess    = 0;
        $total_paid    = 0;
        $total_unpaid  = 0;
        $total_partial = 0;

        if (!empty($data['fees_awaiting'])) {

            foreach ($data['fees_awaiting'] as $awaiting_key => $awaiting_value) {

                $amount_to_be_taken = 0;
                if ($awaiting_value->is_system) {
                    if ($awaiting_value->amount > 0) {
                        $amount_to_be_taken = $awaiting_value->amount;
                    }
                } elseif ($awaiting_value->is_system == 0) {
                    if ($awaiting_value->fee_amount > 0) {
                        $amount_to_be_taken = $awaiting_value->fee_amount;
                    }

                }
                if ($amount_to_be_taken > 0) {
                    $total_fess++;

                    if (is_string($awaiting_value->amount_detail) && is_array(json_decode($awaiting_value->amount_detail, true)) && (json_last_error() == JSON_ERROR_NONE)) {
                        $amount_paid_details = (json_decode($awaiting_value->amount_detail));
                        $amt_                = 0;
                        foreach ($amount_paid_details as $amount_paid_detail_key => $amount_paid_detail_value) {
                            $amt_ = $amt_ + $amount_paid_detail_value->amount;
                        }

                        if (($amt_ + $amount_paid_detail_value->amount_discount) >= $amount_to_be_taken) {
                            $total_paid++;
                        } elseif (($amt_ + $amount_paid_detail_value->amount_discount) < $amount_to_be_taken) {
                            $total_partial++;
                        }
                    } else {
                        $total_unpaid++;
                    }

                }
            }
        }

        $data['incomegraph'] = $this->income_model->getIncomeHeadsData($start_date, $end_date);
        $data['expensegraph'] = $this->expense_model->getExpenseHeadData($start_date, $end_date);
        $enquiry       = $this->admin_model->getAllEnquiryCount($start_date, $end_date);
        $total_counter = $total_paid + $total_unpaid + $total_partial;

        $data['fees_overview'] = array(
            'total_unpaid'     => $total_unpaid,
            'unpaid_progress'  => ($total_counter > 0) ? (($total_unpaid * 100) / $total_counter) : 0,
            'total_paid'       => $total_paid,
            'paid_progress'    => ($total_counter > 0) ? (($total_paid * 100) / $total_counter) : 0,
            'total_partial'    => $total_partial,
            'partial_progress' => ($total_counter > 0) ? (($total_partial * 100) / $total_counter) : 0,
        );

        $total_enquiry = $enquiry['total'];

        if ($total_enquiry > 0) {

            $data['enquiry_overview'] = array(
                'won'              => $enquiry['complete'],
                'won_progress'     => ($enquiry['complete'] * 100) / $total_enquiry,
                'active'           => $enquiry['active'],
                'active_progress'  => ($enquiry['active'] * 100) / $total_enquiry,
                'passive'          => $enquiry['passive'],
                'passive_progress' => ($enquiry['passive'] * 100) / $total_enquiry,
                'dead'             => $enquiry['dead'],
                'dead_progress'    => ($enquiry['dead'] * 100) / $total_enquiry,
                'lost'             => $enquiry['lost'],
                'lost_progress'    => ($enquiry['lost'] * 100) / $total_enquiry,
            );

        } else {

            $data['enquiry_overview'] = array(
                'won'              => 0,
                'won_progress'     => 0,
                'active'           => 0,
                'active_progress'  => 0,
                'passive'          => 0,
                'passive_progress' => 0,
                'dead'             => 0,
                'dead_progress'    => 0,
                'lost'             => 0,
                'lost_progress'    => 0,
            );

        }

        $data['total_paid'] = $total_paid;
        $data['total_fees'] = $total_fess;
        if ($total_fess > 0) {
            $data['fessprogressbar'] = ($total_paid * 100) / $total_fess;
        } else {
            $data['fessprogressbar'] = 0;
        }

        $data['total_enquiry']  = $total_enquiry  = $enquiry['total'];
        $data['total_complete'] = $complete_enquiry = $enquiry['complete'];
        if ($total_enquiry > 0) {
            $data['fenquiryprogressbar'] = ($complete_enquiry * 100) / $total_enquiry;
        } else {
            $data['fenquiryprogressbar'] = 0;
        }

        $bookoverview      = $this->book_model->bookoverview($start_date, $end_date);
        $bookduereport     = $this->bookissue_model->dueforreturn($start_date, $end_date);
        $forreturndata     = $this->bookissue_model->forreturn($start_date, $end_date);
        $dueforreturn      = $bookduereport[0]['total'];
        $forreturn         = $forreturndata[0]['total'];
        $total_qty         = $bookoverview[0]['qty'];
        $total_issued      = $bookoverview[0]['total_issue'];
        $availble          = '0';
        $availble_progress = 0;
        $issued_progress   = 0;
        if ($total_qty > 0) {
            $availble          = $total_qty - $total_issued;
            $availble_progress = ($availble * 100) / $total_qty;
            $issued_progress   = ($total_issued * 100) / $total_qty;
        }
        $data['book_overview'] = array(
            'total'             => $total_qty,
            'total_progress'    => 100,
            'availble'          => $availble,
            'availble_progress' => round($availble_progress, 2),
            'total_issued'      => $total_issued,
            'issued_progress'   => round($issued_progress, 2),
            'dueforreturn'      => $dueforreturn,
            'forreturn'         => $forreturn,
        );

        $Attendence                   = $this->stuattendence_model->getTodayDayAttendance($total_students);
        $data['attendence_data']      = $Attendence;
        $Staffattendence              = $this->Staff_model->getTodayDayAttendance();
        $data['Staffattendence_data'] = $Staffattendence;
        $getTotalStaff                = $this->Staff_model->getTotalStaff();
        $data['getTotalStaff_data']   = $getTotalStaff;
        if ($getTotalStaff > 0) {$percentTotalStaff_data = ($Staffattendence * 100) / ($getTotalStaff);} else { $percentTotalStaff_data = '0';}
        $data['percentTotalStaff_data'] = $percentTotalStaff_data;
        $data['sch_setting']            = $this->sch_setting_detail;

        if ($data['sch_setting']->attendence_type == 0) {
            $data['std_graphclass'] = "col-lg-3 col-md-6 col-sm-6";
        } else {
            $data['std_graphclass'] = "col-lg-4 col-md-6 col-sm-6";
        }

        $this->load->view('layout/header', $data);
        $this->load->view('admin/setting', $data);
        $this->load->view('layout/footer', $data);
    }

    public function comptabilite()
    {
         // ===== RÉCUPÉRATION DU PAYS =====
    if ($this->session->userdata('user_country')) {
        $data['user_country'] = $this->session->userdata('user_country');
    } else {
        $data['user_country'] = $this->getUserCountry();
        $this->session->set_userdata('user_country', $data['user_country']);
    }
    
        $data['dashboard'] = $this->admin_model->getDashboardData();

        $role            = $this->customlib->getStaffRole();
        $role_id         = json_decode($role)->id;
        $data['role_id'] = $role_id;

        $staffid       = $this->customlib->getStaffID();
        $notifications = $this->notification_model->getUnreadStaffNotification($staffid, $role_id);

        $data['notifications'] = $notifications;
        $input                 = $this->setting_model->getCurrentSessionName();

        list($a, $b)  = explode('-', $input);
        $Current_year = $a;
        if (strlen($b) == 2) {
            $Next_year = substr($a, 0, 2) . $b;
        } else {
            $Next_year = $b;
        }
        $data['mysqlVersion'] = $this->setting_model->getMysqlVersion();
        $data['sqlMode']      = $this->setting_model->getSqlMode();
        //========================== Current Attendence ==========================
        $current_date       = date('Y-m-d');
        $data['title']      = 'Dashboard';
        $Current_start_date = date('01');
        $Current_date       = date('d');
        $Current_month      = date('m');
        $month_collection   = 0;
        $month_expense      = 0;
        $total_students     = 0;
        $total_teachers     = 0;
        $ar                 = $this->startmonthandend();
        $year_str_month     = $Current_year . '-' . $ar[0] . '-01';
        $year_end_month     = date("Y-m-t", strtotime($Next_year . '-' . $ar[1] . '-01'));
        $getDepositeAmount  = $this->studentfeemaster_model->getDepositAmountBetweenDate($year_str_month, $year_end_month);
        //======================Current Month Collection ==============================
        $first_day_this_month     = date('Y-m-01');
        $current_month_collection = $this->studentfeemaster_model->getDepositAmountBetweenDate($first_day_this_month, $current_date);
        $month_collection         = $this->whatever($current_month_collection, $first_day_this_month, $current_date);
        $expense                  = $this->expense_model->getTotalExpenseBwdate($first_day_this_month, $current_date);
        if (!empty($expense)) {
            $month_expense = $month_expense + $expense->amount;
        }

        $data['month_collection'] = $month_collection;
        $data['month_expense']    = $month_expense;

        $tot_students = $this->studentsession_model->getTotalStudentBySession();
        if (!empty($tot_students)) {
            $total_students = $tot_students->total_student;
        }

        $data['total_students'] = $total_students;

        $tot_roles = $this->role_model->get();

        foreach ($tot_roles as $key => $value) {

            $count_roles[$value["name"]] = $this->role_model->count_roles($value["id"]);

        }
        $data["roles"] = $count_roles;

        //======================== get collection by month ==========================
        $start_month = strtotime($year_str_month);
        $start       = strtotime($year_str_month);
        $end         = strtotime($year_end_month);
        $coll_month  = array();
        $s           = array();
        $total_month = array();
        while ($start_month <= $end) {
            $total_month[] = date('M', $start_month);
            $month_start   = date('Y-m-d', $start_month);
            $month_end     = date("Y-m-t", $start_month);
            $return        = $this->whatever($getDepositeAmount, $month_start, $month_end);
            if ($return) {
                $s[] = $return;
            } else {
                $s[] = "0.00";
            }

            $start_month = strtotime("+1 month", $start_month);
        }
        //======================== getexpense by month ==============================
        $ex                  = array();
        $start_session_month = strtotime($year_str_month);
        while ($start_session_month <= $end) {

            $month_start = date('Y-m-d', $start_session_month);
            $month_end   = date("Y-m-t", $start_session_month);

            $expense_monthly = $this->expense_model->getTotalExpenseBwdate($month_start, $month_end);

            if (!empty($expense_monthly)) {
                $amt  = 0;
                $ex[] = $amt + $expense_monthly->amount;
            }

            $start_session_month = strtotime("+1 month", $start_session_month);
        }

        $data['yearly_collection'] = $s;
        $data['yearly_expense']    = $ex;
        $data['total_month']       = $total_month;

        //======================= current month collection /expense ===================
        // hardcoded '01' for first day
        $startdate       = date('m/01/Y');
        $enddate         = date('m/t/Y');
        $start           = strtotime($startdate);
        $end             = strtotime($enddate);
        $currentdate     = $start;
        $month_days      = array();
        $days_collection = array();
        while ($currentdate <= $end) {
            $cur_date          = date('Y-m-d', $currentdate);
            $month_days[]      = date('d', $currentdate);
            $coll_amt          = $this->whatever($getDepositeAmount, $cur_date, $cur_date);
            $days_collection[] = $coll_amt;
            $currentdate       = strtotime('+1 day', $currentdate);
        }
        $data['current_month_days'] = $month_days;
        $data['days_collection']    = $days_collection;

        //======================= current month /expense ==============================
        // hardcoded '01' for first day

        $startdate    = date('m/01/Y');
        $enddate      = date('m/t/Y');
        $start        = strtotime($startdate);
        $end          = strtotime($enddate);
        $currentdate  = $start;
        $days_expense = array();
        while ($currentdate <= $end) {
            $cur_date       = date('Y-m-d', $currentdate);
            $month_days[]   = date('d', $currentdate);
            $currentdate    = strtotime('+1 day', $currentdate);
            $ct             = $this->getExpensebyday($cur_date);
            $days_expense[] = $ct;
        }

        $data['days_expense']        = $days_expense;
        $student_fee_history         = $this->studentfee_model->getTodayStudentFees();
        $data['student_fee_history'] = $student_fee_history;

        $event_colors         = array("#03a9f4", "#c53da9", "#757575", "#8e24aa", "#d81b60", "#7cb342", "#fb8c00", "#fb3b3b");
        $data["event_colors"] = $event_colors;
        $userdata             = $this->customlib->getUserData();
        $data["role"]         = $userdata["user_type"];
        $start_date           = date('Y-m-01');
        $end_date             = date('Y-m-t');
        $student_due_fee      = $this->studentfeemaster_model->getFeesAwaiting($start_date, $end_date);

        $data['fees_awaiting'] = $student_due_fee;

        $total_fess    = 0;
        $total_paid    = 0;
        $total_unpaid  = 0;
        $total_partial = 0;

        if (!empty($data['fees_awaiting'])) {

            foreach ($data['fees_awaiting'] as $awaiting_key => $awaiting_value) {

                $amount_to_be_taken = 0;
                if ($awaiting_value->is_system) {
                    if ($awaiting_value->amount > 0) {
                        $amount_to_be_taken = $awaiting_value->amount;
                    }
                } elseif ($awaiting_value->is_system == 0) {
                    if ($awaiting_value->fee_amount > 0) {
                        $amount_to_be_taken = $awaiting_value->fee_amount;
                    }

                }
                if ($amount_to_be_taken > 0) {
                    $total_fess++;

                    if (is_string($awaiting_value->amount_detail) && is_array(json_decode($awaiting_value->amount_detail, true)) && (json_last_error() == JSON_ERROR_NONE)) {
                        $amount_paid_details = (json_decode($awaiting_value->amount_detail));
                        $amt_                = 0;
                        foreach ($amount_paid_details as $amount_paid_detail_key => $amount_paid_detail_value) {
                            $amt_ = $amt_ + $amount_paid_detail_value->amount;
                        }

                        if (($amt_ + $amount_paid_detail_value->amount_discount) >= $amount_to_be_taken) {
                            $total_paid++;
                        } elseif (($amt_ + $amount_paid_detail_value->amount_discount) < $amount_to_be_taken) {
                            $total_partial++;
                        }
                    } else {
                        $total_unpaid++;
                    }

                }
            }
        }

        $data['incomegraph'] = $this->income_model->getIncomeHeadsData($start_date, $end_date);
        $data['expensegraph'] = $this->expense_model->getExpenseHeadData($start_date, $end_date);
        $enquiry       = $this->admin_model->getAllEnquiryCount($start_date, $end_date);
        $total_counter = $total_paid + $total_unpaid + $total_partial;

        $data['fees_overview'] = array(
            'total_unpaid'     => $total_unpaid,
            'unpaid_progress'  => ($total_counter > 0) ? (($total_unpaid * 100) / $total_counter) : 0,
            'total_paid'       => $total_paid,
            'paid_progress'    => ($total_counter > 0) ? (($total_paid * 100) / $total_counter) : 0,
            'total_partial'    => $total_partial,
            'partial_progress' => ($total_counter > 0) ? (($total_partial * 100) / $total_counter) : 0,
        );

        $total_enquiry = $enquiry['total'];

        if ($total_enquiry > 0) {

            $data['enquiry_overview'] = array(
                'won'              => $enquiry['complete'],
                'won_progress'     => ($enquiry['complete'] * 100) / $total_enquiry,
                'active'           => $enquiry['active'],
                'active_progress'  => ($enquiry['active'] * 100) / $total_enquiry,
                'passive'          => $enquiry['passive'],
                'passive_progress' => ($enquiry['passive'] * 100) / $total_enquiry,
                'dead'             => $enquiry['dead'],
                'dead_progress'    => ($enquiry['dead'] * 100) / $total_enquiry,
                'lost'             => $enquiry['lost'],
                'lost_progress'    => ($enquiry['lost'] * 100) / $total_enquiry,
            );

        } else {

            $data['enquiry_overview'] = array(
                'won'              => 0,
                'won_progress'     => 0,
                'active'           => 0,
                'active_progress'  => 0,
                'passive'          => 0,
                'passive_progress' => 0,
                'dead'             => 0,
                'dead_progress'    => 0,
                'lost'             => 0,
                'lost_progress'    => 0,
            );

        }

        $data['total_paid'] = $total_paid;
        $data['total_fees'] = $total_fess;
        if ($total_fess > 0) {
            $data['fessprogressbar'] = ($total_paid * 100) / $total_fess;
        } else {
            $data['fessprogressbar'] = 0;
        }

        $data['total_enquiry']  = $total_enquiry  = $enquiry['total'];
        $data['total_complete'] = $complete_enquiry = $enquiry['complete'];
        if ($total_enquiry > 0) {
            $data['fenquiryprogressbar'] = ($complete_enquiry * 100) / $total_enquiry;
        } else {
            $data['fenquiryprogressbar'] = 0;
        }

        $bookoverview      = $this->book_model->bookoverview($start_date, $end_date);
        $bookduereport     = $this->bookissue_model->dueforreturn($start_date, $end_date);
        $forreturndata     = $this->bookissue_model->forreturn($start_date, $end_date);
        $dueforreturn      = $bookduereport[0]['total'];
        $forreturn         = $forreturndata[0]['total'];
        $total_qty         = $bookoverview[0]['qty'];
        $total_issued      = $bookoverview[0]['total_issue'];
        $availble          = '0';
        $availble_progress = 0;
        $issued_progress   = 0;
        if ($total_qty > 0) {
            $availble          = $total_qty - $total_issued;
            $availble_progress = ($availble * 100) / $total_qty;
            $issued_progress   = ($total_issued * 100) / $total_qty;
        }
        $data['book_overview'] = array(
            'total'             => $total_qty,
            'total_progress'    => 100,
            'availble'          => $availble,
            'availble_progress' => round($availble_progress, 2),
            'total_issued'      => $total_issued,
            'issued_progress'   => round($issued_progress, 2),
            'dueforreturn'      => $dueforreturn,
            'forreturn'         => $forreturn,
        );

        $Attendence                   = $this->stuattendence_model->getTodayDayAttendance($total_students);
        $data['attendence_data']      = $Attendence;
        $Staffattendence              = $this->Staff_model->getTodayDayAttendance();
        $data['Staffattendence_data'] = $Staffattendence;
        $getTotalStaff                = $this->Staff_model->getTotalStaff();
        $data['getTotalStaff_data']   = $getTotalStaff;
        if ($getTotalStaff > 0) {$percentTotalStaff_data = ($Staffattendence * 100) / ($getTotalStaff);} else { $percentTotalStaff_data = '0';}
        $data['percentTotalStaff_data'] = $percentTotalStaff_data;
        $data['sch_setting']            = $this->sch_setting_detail;

        if ($data['sch_setting']->attendence_type == 0) {
            $data['std_graphclass'] = "col-lg-3 col-md-6 col-sm-6";
        } else {
            $data['std_graphclass'] = "col-lg-4 col-md-6 col-sm-6";
        }

        $data['comptabilite_hub'] = $this->buildComptabiliteHub();

        $this->load->view('layout/header', $data);
        $this->load->view('admin/comptabilite', $data);
        $this->load->view('layout/footer', $data);
    }

    protected function buildComptabiliteHub()
    {
        $sections = array(
            array(
                'key' => 'socle',
                'title' => 'Socle operationnel',
                'subtitle' => 'Operations de tresorerie, facturation et immobilisations qui alimentent la comptabilite.',
                'icon' => 'fa-money',
                'accent' => 'blue',
                'items' => array(
                    $this->makeComptabiliteModule('Tresorerie', 'Gestion de tresorerie et des caisses', 'admin/income', 'Finance', 'fa-money', 'blue', 'socle finance tresorerie caisse', true, true),
                    $this->makeComptabiliteModule('Banques', 'Gestion des comptes bancaires', 'admin/expense/bank', 'Banque', 'fa-university', 'green', 'socle banque comptes bancaires', true, true),
                    $this->makeComptabiliteModule('Etat tresorerie', 'Situation globale de tresorerie', 'admin/income/global', 'Analyse', 'fa-pie-chart', 'purple', 'socle analyse tresorerie etat', true, true),
                    $this->makeComptabiliteModule('Rapport financier', 'Analyses et syntheses financieres', 'admin/income/finance', 'Rapport', 'fa-file-text', 'orange', 'socle rapport financier synthese', true, true),
                    $this->makeComptabiliteModule('Transfert de montant', 'Mouvements entre comptes', 'admin/transfer', 'Transfert', 'fa-exchange', 'teal', 'socle transfert mouvements comptes', true, $this->rbac->hasPrivilege('transfer', 'can_view')),
                    $this->makeComptabiliteModule('Categorie depenses', 'Gestion des categories de depenses', 'admin/expensehead', 'Categorie', 'fa-tags', 'red', 'socle depenses categories', true, true),
                    $this->makeComptabiliteModule('Immobilisations', 'Gestion des actifs immobilises', 'admin/immobilisations', 'Actif', 'fa-building', 'purple', 'socle immobilisations actifs', true, true),
                    $this->makeComptabiliteModule('Rapports amortissement', 'Suivi et rapports des amortissements', 'admin/amortissements', 'Actif', 'fa-line-chart', 'orange', 'socle amortissement actifs', true, true),
                    $this->makeComptabiliteModule('Factures ventes', 'Gestion des factures de ventes', 'admin/invoiceitem', 'Ventes', 'fa-file-text-o', 'cyan', 'socle ventes factures clients', true, $this->rbac->hasPrivilege('caisse', 'can_view')),
                    $this->makeComptabiliteModule('Factures achats', 'Gestion des factures d achats', 'admin/invoiceitem_supplier', 'Achats', 'fa-shopping-cart', 'pink', 'socle achats factures fournisseurs', true, $this->rbac->hasPrivilege('caisse', 'can_view')),
                    $this->makeComptabiliteModule('Plan comptable', 'Gestion des comptes comptables', 'admin/chart_of_accounts', 'Comptabilite', 'fa-book', 'gray', 'socle plan comptable comptes', true, true),
                    $this->makeComptabiliteModule('Ecritures comptables', 'Journal des ecritures comptables', 'admin/journal_entries', 'Comptabilite', 'fa-pencil-square-o', 'indigo', 'socle ecritures journal comptable', true, true),
                ),
            ),
            array(
                'key' => 'journaux',
                'title' => 'Cycle OHADA - journaux et balances',
                'subtitle' => 'Noyau SYSCOHADA pour la tenue des journaux et le controle de l egalite debit credit.',
                'icon' => 'fa-columns',
                'accent' => 'indigo',
                'items' => array(
                    $this->makeComptabiliteModule('Journaux auxiliaires', 'ACHATS, VENTES, BANQUE, CAISSE, PAIE, OPD, A-NOUVEAUX', 'admin/journaux_auxiliaires', 'OHADA', 'fa-book', 'indigo', 'ohada journaux auxiliaires achats ventes banque caisse paie opd a nouveaux', $this->moduleControllerExists('Journaux_auxiliaires'), true),
                    $this->makeComptabiliteModule('Balance generale', 'Verification debit = credit', 'admin/balance_generale', 'OHADA', 'fa-balance-scale', 'blue', 'ohada balance generale verification debit credit', $this->moduleControllerExists('Balance_generale'), true),
                    $this->makeComptabiliteModule('Balance auxiliaire', 'Par client, fournisseur et tiers', 'admin/balance_auxiliaire', 'OHADA', 'fa-list-alt', 'teal', 'ohada balance auxiliaire clients fournisseurs tiers', $this->moduleControllerExists('Balance_auxiliaire'), true),
                    $this->makeComptabiliteModule('Grand livre', 'Tous les comptes detailles', 'admin/grand_livre', 'OHADA', 'fa-book', 'purple', 'ohada grand livre comptes detailles', $this->moduleControllerExists('Grand_livre'), true),
                ),
            ),
           // array(
             //   'key' => 'etats',
               // 'title' => 'Etats financiers OHADA',
                //'subtitle' => 'Documents de synthese obligatoires pour une comptabilite conforme et exploitable.',
                //'icon' => 'fa-file-pdf-o',
                //'accent' => 'green',
                //'items' => array(
                  //  $this->makeComptabiliteModule('Bilan comptable', 'Actif / Passif conforme OHADA', 'admin/bilan_comptable', 'OHADA', 'fa-file-text-o', 'green', 'ohada bilan comptable actif passif', $this->moduleControllerExists('Bilan_comptable'), true),
                   // $this->makeComptabiliteModule('Compte de resultat', 'Avec soldes intermediaires de gestion', 'admin/compte_resultat', 'OHADA', 'fa-bar-chart', 'orange', 'ohada compte resultat sig soldes intermediaires', $this->moduleControllerExists('Compte_resultat'), true),
                    //$this->makeComptabiliteModule('TAFIRE', 'Tableau de financement et flux', 'admin/tafire', 'OHADA', 'fa-table', 'cyan', 'ohada tafire tableau financement flux', $this->moduleControllerExists('Tafire'), true),
                    //$this->makeComptabiliteModule('Notes annexes', 'Informations complementaires des etats financiers', 'admin/notes_annexes', 'OHADA', 'fa-sticky-note', 'pink', 'ohada notes annexes etats financiers', $this->moduleControllerExists('Notes_annexes'), true),
                //),
           // ),
            array(
                'key' => 'pilotage',
                'title' => 'Pilotage et controles',
                'subtitle' => 'Fonctions de cloture, lettrage, tiers et analytique pour une comptabilite flexible.',
                'icon' => 'fa-cogs',
                'accent' => 'red',
                'items' => array(
                    $this->makeComptabiliteModule('Cloture d exercice', 'Cloture annuelle avec report a nouveau', 'admin/cloture_exercice', 'OHADA', 'fa-lock', 'red', 'ohada cloture exercice report a nouveau', $this->moduleControllerExists('Cloture_exercice'), true),
                    $this->makeComptabiliteModule('Rapprochement bancaire', 'Lettrage des comptes bancaires', 'admin/rapprochement_bancaire', 'Banque', 'fa-handshake-o', 'emerald', 'ohada rapprochement bancaire lettrage', $this->moduleControllerExists('Rapprochement_bancaire'), true),
                    $this->makeComptabiliteModule('Gestion des tiers', 'Clients, fournisseurs et tiers rassembles', 'admin/tiers', 'Tiers', 'fa-users', 'gray', 'ohada tiers clients fournisseurs', $this->moduleControllerExists('Tiers'), true),
                    $this->makeComptabiliteModule('Comptabilite analytique', 'Par projet, entite, eglise ou association', 'admin/analytique', 'Analytique', 'fa-sitemap', 'purple', 'ohada analytique projet entite eglise association', $this->moduleControllerExists('Analytique'), true),
                ),
            ),
            array(
                'key' => 'configuration',
                'title' => 'Configuration OHADA',
                'subtitle' => 'Parametrage du referentiel OHADA, des exercices et des journaux par defaut.',
                'icon' => 'fa-gears',
                'accent' => 'gray',
                'items' => array(
                    $this->makeComptabiliteModule('Parametres OHADA', 'Configuration SYSCOHADA / SYCEBNL', 'admin/parametres_ohada', 'Configuration', 'fa-cog', 'gray', 'configuration ohada syscohada sycebnl', $this->moduleControllerExists('Parametres_ohada'), true),
                    $this->makeComptabiliteModule('Exercices comptables', 'Gestion des annees fiscales', 'admin/exercices_comptables', 'Configuration', 'fa-calendar', 'blue', 'configuration exercices comptables annees fiscales', $this->moduleControllerExists('Exercices_comptables'), true),
                    $this->makeComptabiliteModule('Plan SYSCOHADA', '9 classes de comptes OHADA', 'admin/syscohada', 'Configuration', 'fa-archive', 'indigo', 'configuration plan syscohada classes comptes', $this->moduleControllerExists('Syscohada'), true),
                    $this->makeComptabiliteModule('Journaux par defaut', 'Parametrage des journaux OHADA', 'admin/journaux_config', 'Configuration', 'fa-sliders', 'teal', 'configuration journaux ohada par defaut', $this->moduleControllerExists('Journaux_config'), true),
                ),
            ),
        );

        $visible_sections = array();
        $summary = array(
            'total' => 0,
            'available' => 0,
            'planned' => 0,
            'ohada' => 0,
        );

        foreach ($sections as $section) {
            $items = array();

            foreach ($section['items'] as $item) {
                if (!$item['visible']) {
                    continue;
                }

                $items[] = $item;
                $summary['total']++;
                if ($item['status'] === 'available') {
                    $summary['available']++;
                } else {
                    $summary['planned']++;
                }
                if ($item['is_ohada']) {
                    $summary['ohada']++;
                }
            }

            if (!empty($items)) {
                $section['items'] = $items;
                $section['count'] = count($items);
                $visible_sections[] = $section;
            }
        }

        return array(
            'title' => 'Espace Comptabilite OHADA',
            'subtitle' => 'Un hub flexible pour piloter les operations financieres, les cycles OHADA et les modules a activer.',
            'sections' => $visible_sections,
            'summary' => $summary,
            'workflow' => array(
                array('label' => '1. Parametrer', 'module' => 'Parametres OHADA / Plan SYSCOHADA / Exercices'),
                array('label' => '2. Produire', 'module' => 'Journaux auxiliaires / Ecritures comptables'),
                array('label' => '3. Controler', 'module' => 'Balance generale / Balance auxiliaire / Grand livre'),
                array('label' => '4. Publier', 'module' => 'Bilan / Compte de resultat / TAFIRE / Annexes'),
            ),
        );
    }

    protected function makeComptabiliteModule($title, $description, $url, $badge, $icon, $color, $keywords, $available, $visible, $is_ohada = false)
    {
        return array(
            'title' => $title,
            'description' => $description,
            'url' => $available ? base_url($url) : '',
            'badge' => $badge,
            'icon' => $icon,
            'color' => $color,
            'keywords' => $keywords,
            'status' => $available ? 'available' : 'planned',
            'status_label' => $available ? 'Disponible' : 'A configurer',
            'visible' => $visible,
            'is_ohada' => $is_ohada || $badge === 'OHADA',
        );
    }

    protected function moduleControllerExists($controller)
    {
        return file_exists(APPPATH . 'controllers/admin/' . $controller . '.php');
    }

public function dashboard()
{
    // ===== RÉCUPÉRATION DU PAYS (UNE SEULE FOIS AU DÉBUT) =====
    if ($this->session->userdata('user_country')) {
        $data['user_country'] = $this->session->userdata('user_country');
    } else {
        $data['user_country'] = $this->getUserCountry();
        $this->session->set_userdata('user_country', $data['user_country']);
    }

    // ===== VÉRIFICATION DE L'ENTREPRISE ET DES SETTINGS =====
    $userdata = $this->customlib->getUserData();
    $entreprise_id = $userdata['entreprise_id'] ?? 0;
    $staff_id = $userdata['id'] ?? 0;

    // Vérifier si le staff existe et est actif
    if ($staff_id > 0) {
        $this->db->select('is_active');
        $this->db->from('staff');
        $this->db->where('id', $staff_id);
        $staff = $this->db->get()->row();

        if (!$staff || $staff->is_active != 1) {
            $this->session->unset_userdata('admin');
            $this->session->set_flashdata('error', 'Votre compte a été désactivé. Veuillez contacter l\'administrateur.');
            redirect('site/login');
            return;
        }
    }

    // Vérifier le statut de l'entreprise (exclure super admin)
    $role_id = $userdata['role_id'] ?? 0;
    $is_super_admin = ($role_id == 1 && $entreprise_id == 1);

    if (!$is_super_admin && $entreprise_id > 0) {
        $this->db->select('statut');
        $this->db->from('compte_entreprise');
        $this->db->where('id', $entreprise_id);
        $entreprise = $this->db->get()->row();

        if ($entreprise && ($entreprise->statut == 'suspendu' || $entreprise->statut == 'expiré')) {
            $this->session->unset_userdata('admin');
            $this->session->set_flashdata('error', 'Votre entreprise est ' . $entreprise->statut . '. Veuillez contacter l\'administrateur.');
            redirect('site/login');
            return;
        }
    }

    // ===== RÉCUPÉRATION DES SETTINGS AVEC FALLBACK =====
    if ($entreprise_id > 0) {
        $this->db->select('*');
        $this->db->from('sch_settings');
        $this->db->where('entreprise_id', $entreprise_id);
        $settings_query = $this->db->get();

        if ($settings_query->num_rows() > 0) {
            $this->sch_setting_detail = $settings_query->row();
        } else {
            $this->db->select('*');
            $this->db->from('sch_settings');
            $this->db->order_by('id');
            $this->db->limit(1);
            $settings_query = $this->db->get();
            $this->sch_setting_detail = $settings_query->row();

            if (!$this->sch_setting_detail) {
                $this->sch_setting_detail = new stdClass();
                $this->sch_setting_detail->attendence_type = 0;
                $this->sch_setting_detail->name = 'Mon Entreprise';
                $this->sch_setting_detail->date_format = 'd-m-Y';
                $this->sch_setting_detail->currency_symbol = 'FCFA';
                $this->sch_setting_detail->currency_place = 'after_number';
                $this->sch_setting_detail->start_month = 'January';
                $this->sch_setting_detail->start_week = 'Monday';
                $this->sch_setting_detail->timezone = 'UTC';
                $this->sch_setting_detail->is_rtl = 'disabled';
                $this->sch_setting_detail->theme = 'default.jpg';
            }
        }
    } else {
        $this->db->select('*');
        $this->db->from('sch_settings');
        $this->db->order_by('id');
        $this->db->limit(1);
        $settings_query = $this->db->get();
        $this->sch_setting_detail = $settings_query->row();

        if (!$this->sch_setting_detail) {
            $this->sch_setting_detail = new stdClass();
            $this->sch_setting_detail->attendence_type = 0;
            $this->sch_setting_detail->name = 'Mon Entreprise';
            $this->sch_setting_detail->date_format = 'd-m-Y';
            $this->sch_setting_detail->currency_symbol = 'FCFA';
            $this->sch_setting_detail->currency_place = 'after_number';
            $this->sch_setting_detail->start_month = 'January';
            $this->sch_setting_detail->start_week = 'Monday';
            $this->sch_setting_detail->timezone = 'UTC';
            $this->sch_setting_detail->is_rtl = 'disabled';
            $this->sch_setting_detail->theme = 'default.jpg';
        }
    }

    // ===== SUITE DU CODE DASHBOARD =====
    $data['dashboard'] = $this->admin_model->getDashboardData();

    $role = $this->customlib->getStaffRole();
    $role_id = json_decode($role)->id;
    $data['role_id'] = $role_id;

    $staffid = $this->customlib->getStaffID();
    $notifications = $this->notification_model->getUnreadStaffNotification($staffid, $role_id);

    $data['notifications'] = $notifications;
    $input = $this->setting_model->getCurrentSessionName();

    list($a, $b) = explode('-', $input);
    $Current_year = $a;
    if (strlen($b) == 2) {
        $Next_year = substr($a, 0, 2) . $b;
    } else {
        $Next_year = $b;
    }
    $data['mysqlVersion'] = $this->setting_model->getMysqlVersion();
    $data['sqlMode'] = $this->setting_model->getSqlMode();
    
    //========================== Current Attendence ==========================
    $current_date = date('Y-m-d');
    $data['title'] = 'Dashboard';
    $Current_start_date = date('01');
    $Current_date = date('d');
    $Current_month = date('m');
    $month_collection = 0;
    $month_expense = 0;
    $total_students = 0;
    $total_teachers = 0;
    $ar = $this->startmonthandend();
    $year_str_month = $Current_year . '-' . $ar[0] . '-01';
    $year_end_month = date("Y-m-t", strtotime($Next_year . '-' . $ar[1] . '-01'));
    $getDepositeAmount = $this->studentfeemaster_model->getDepositAmountBetweenDate($year_str_month, $year_end_month);
    
    //======================Current Month Collection ==============================
    $first_day_this_month = date('Y-m-01');
    $current_month_collection = $this->studentfeemaster_model->getDepositAmountBetweenDate($first_day_this_month, $current_date);
    $month_collection = $this->whatever($current_month_collection, $first_day_this_month, $current_date);
    $expense = $this->expense_model->getTotalExpenseBwdate($first_day_this_month, $current_date);
    if (!empty($expense)) {
        $month_expense = $month_expense + $expense->amount;
    }

    $data['month_collection'] = $month_collection;
    $data['month_expense'] = $month_expense;

    $tot_students = $this->studentsession_model->getTotalStudentBySession();
    if (!empty($tot_students)) {
        $total_students = $tot_students->total_student;
    }

    $data['total_students'] = $total_students;

    $tot_roles = $this->role_model->get();

    foreach ($tot_roles as $key => $value) {
        $count_roles[$value["name"]] = $this->role_model->count_roles($value["id"]);
    }
    $data["roles"] = $count_roles;

    //======================== get collection by month ==========================
    $start_month = strtotime($year_str_month);
    $start = strtotime($year_str_month);
    $end = strtotime($year_end_month);
    $coll_month = array();
    $s = array();
    $total_month = array();
    while ($start_month <= $end) {
        $total_month[] = date('M', $start_month);
        $month_start = date('Y-m-d', $start_month);
        $month_end = date("Y-m-t", $start_month);
        $return = $this->whatever($getDepositeAmount, $month_start, $month_end);
        if ($return) {
            $s[] = $return;
        } else {
            $s[] = "0.00";
        }

        $start_month = strtotime("+1 month", $start_month);
    }
    
    //======================== getexpense by month ==============================
    $ex = array();
    $start_session_month = strtotime($year_str_month);
    while ($start_session_month <= $end) {
        $month_start = date('Y-m-d', $start_session_month);
        $month_end = date("Y-m-t", $start_session_month);

        $expense_monthly = $this->expense_model->getTotalExpenseBwdate($month_start, $month_end);

        if (!empty($expense_monthly)) {
            $amt = 0;
            $ex[] = $amt + $expense_monthly->amount;
        }

        $start_session_month = strtotime("+1 month", $start_session_month);
    }

    $data['yearly_collection'] = $s;
    $data['yearly_expense'] = $ex;
    $data['total_month'] = $total_month;

    //======================= current month collection /expense ===================
    $startdate = date('m/01/Y');
    $enddate = date('m/t/Y');
    $start = strtotime($startdate);
    $end = strtotime($enddate);
    $currentdate = $start;
    $month_days = array();
    $days_collection = array();
    while ($currentdate <= $end) {
        $cur_date = date('Y-m-d', $currentdate);
        $month_days[] = date('d', $currentdate);
        $coll_amt = $this->whatever($getDepositeAmount, $cur_date, $cur_date);
        $days_collection[] = $coll_amt;
        $currentdate = strtotime('+1 day', $currentdate);
    }
    $data['current_month_days'] = $month_days;
    $data['days_collection'] = $days_collection;

    //======================= current month /expense ==============================
    $startdate = date('m/01/Y');
    $enddate = date('m/t/Y');
    $start = strtotime($startdate);
    $end = strtotime($enddate);
    $currentdate = $start;
    $days_expense = array();
    while ($currentdate <= $end) {
        $cur_date = date('Y-m-d', $currentdate);
        $month_days[] = date('d', $currentdate);
        $currentdate = strtotime('+1 day', $currentdate);
        $ct = $this->getExpensebyday($cur_date);
        $days_expense[] = $ct;
    }

    $data['days_expense'] = $days_expense;
    $student_fee_history = $this->studentfee_model->getTodayStudentFees();
    $data['student_fee_history'] = $student_fee_history;

    $event_colors = array("#03a9f4", "#c53da9", "#757575", "#8e24aa", "#d81b60", "#7cb342", "#fb8c00", "#fb3b3b");
    $data["event_colors"] = $event_colors;
    $userdata = $this->customlib->getUserData();
    $data["role"] = $userdata["user_type"];
    $start_date = date('Y-m-01');
    $end_date = date('Y-m-t');
    $student_due_fee = $this->studentfeemaster_model->getFeesAwaiting($start_date, $end_date);

    $data['fees_awaiting'] = $student_due_fee;

    $total_fess = 0;
    $total_paid = 0;
    $total_unpaid = 0;
    $total_partial = 0;

    if (!empty($data['fees_awaiting'])) {
        foreach ($data['fees_awaiting'] as $awaiting_key => $awaiting_value) {
            $amount_to_be_taken = 0;
            if ($awaiting_value->is_system) {
                if ($awaiting_value->amount > 0) {
                    $amount_to_be_taken = $awaiting_value->amount;
                }
            } elseif ($awaiting_value->is_system == 0) {
                if ($awaiting_value->fee_amount > 0) {
                    $amount_to_be_taken = $awaiting_value->fee_amount;
                }
            }
            if ($amount_to_be_taken > 0) {
                $total_fess++;
                if (is_string($awaiting_value->amount_detail) && is_array(json_decode($awaiting_value->amount_detail, true)) && (json_last_error() == JSON_ERROR_NONE)) {
                    $amount_paid_details = (json_decode($awaiting_value->amount_detail));
                    $amt_ = 0;
                    foreach ($amount_paid_details as $amount_paid_detail_key => $amount_paid_detail_value) {
                        $amt_ = $amt_ + $amount_paid_detail_value->amount;
                    }

                    if (($amt_ + $amount_paid_detail_value->amount_discount) >= $amount_to_be_taken) {
                        $total_paid++;
                    } elseif (($amt_ + $amount_paid_detail_value->amount_discount) < $amount_to_be_taken) {
                        $total_partial++;
                    }
                } else {
                    $total_unpaid++;
                }
            }
        }
    }

    $data['incomegraph'] = $this->income_model->getIncomeHeadsData($start_date, $end_date);
    $data['expensegraph'] = $this->expense_model->getExpenseHeadData($start_date, $end_date);
    $enquiry = $this->admin_model->getAllEnquiryCount($start_date, $end_date);
    $total_counter = $total_paid + $total_unpaid + $total_partial;

    $data['fees_overview'] = array(
        'total_unpaid' => $total_unpaid,
        'unpaid_progress' => ($total_counter > 0) ? (($total_unpaid * 100) / $total_counter) : 0,
        'total_paid' => $total_paid,
        'paid_progress' => ($total_counter > 0) ? (($total_paid * 100) / $total_counter) : 0,
        'total_partial' => $total_partial,
        'partial_progress' => ($total_counter > 0) ? (($total_partial * 100) / $total_counter) : 0,
    );

    $total_enquiry = $enquiry['total'];

    if ($total_enquiry > 0) {
        $data['enquiry_overview'] = array(
            'won' => $enquiry['complete'],
            'won_progress' => ($enquiry['complete'] * 100) / $total_enquiry,
            'active' => $enquiry['active'],
            'active_progress' => ($enquiry['active'] * 100) / $total_enquiry,
            'passive' => $enquiry['passive'],
            'passive_progress' => ($enquiry['passive'] * 100) / $total_enquiry,
            'dead' => $enquiry['dead'],
            'dead_progress' => ($enquiry['dead'] * 100) / $total_enquiry,
            'lost' => $enquiry['lost'],
            'lost_progress' => ($enquiry['lost'] * 100) / $total_enquiry,
        );
    } else {
        $data['enquiry_overview'] = array(
            'won' => 0,
            'won_progress' => 0,
            'active' => 0,
            'active_progress' => 0,
            'passive' => 0,
            'passive_progress' => 0,
            'dead' => 0,
            'dead_progress' => 0,
            'lost' => 0,
            'lost_progress' => 0,
        );
    }

    $data['total_paid'] = $total_paid;
    $data['total_fees'] = $total_fess;
    if ($total_fess > 0) {
        $data['fessprogressbar'] = ($total_paid * 100) / $total_fess;
    } else {
        $data['fessprogressbar'] = 0;
    }

    $data['total_enquiry'] = $total_enquiry = $enquiry['total'];
    $data['total_complete'] = $complete_enquiry = $enquiry['complete'];
    if ($total_enquiry > 0) {
        $data['fenquiryprogressbar'] = ($complete_enquiry * 100) / $total_enquiry;
    } else {
        $data['fenquiryprogressbar'] = 0;
    }

    $bookoverview = $this->book_model->bookoverview($start_date, $end_date);
    $bookduereport = $this->bookissue_model->dueforreturn($start_date, $end_date);
    $forreturndata = $this->bookissue_model->forreturn($start_date, $end_date);
    $dueforreturn = $bookduereport[0]['total'];
    $forreturn = $forreturndata[0]['total'];
    $total_qty = $bookoverview[0]['qty'];
    $total_issued = $bookoverview[0]['total_issue'];
    $availble = '0';
    $availble_progress = 0;
    $issued_progress = 0;
    if ($total_qty > 0) {
        $availble = $total_qty - $total_issued;
        $availble_progress = ($availble * 100) / $total_qty;
        $issued_progress = ($total_issued * 100) / $total_qty;
    }
    $data['book_overview'] = array(
        'total' => $total_qty,
        'total_progress' => 100,
        'availble' => $availble,
        'availble_progress' => round($availble_progress, 2),
        'total_issued' => $total_issued,
        'issued_progress' => round($issued_progress, 2),
        'dueforreturn' => $dueforreturn,
        'forreturn' => $forreturn,
    );

    $Attendence = $this->stuattendence_model->getTodayDayAttendance($total_students);
    $data['attendence_data'] = $Attendence;
    $Staffattendence = $this->Staff_model->getTodayDayAttendance();
    $data['Staffattendence_data'] = $Staffattendence;
    $getTotalStaff = $this->Staff_model->getTotalStaff();
    $data['getTotalStaff_data'] = $getTotalStaff;
    if ($getTotalStaff > 0) {
        $percentTotalStaff_data = ($Staffattendence * 100) / ($getTotalStaff);
    } else {
        $percentTotalStaff_data = '0';
    }
    $data['percentTotalStaff_data'] = $percentTotalStaff_data;

    // ===== UTILISER LE SCH_SETTING DETAIL RÉCUPÉRÉ =====
    $data['sch_setting'] = $this->sch_setting_detail;

    if ($data['sch_setting'] && isset($data['sch_setting']->attendence_type) && $data['sch_setting']->attendence_type == 0) {
        $data['std_graphclass'] = "col-lg-3 col-md-6 col-sm-6";
    } else {
        $data['std_graphclass'] = "col-lg-4 col-md-6 col-sm-6";
    }

    $this->load->view('layout/header', $data);
    $this->load->view('admin/dashboard', $data);
    $this->load->view('layout/footer', $data);
}
 
    public function dashboard_19()
    {
        $data['dashboard'] = $this->admin_model->getDashboardData();

        $role            = $this->customlib->getStaffRole();
        $role_id         = json_decode($role)->id;
        $data['role_id'] = $role_id;

        $staffid       = $this->customlib->getStaffID();
        $notifications = $this->notification_model->getUnreadStaffNotification($staffid, $role_id);

        $data['notifications'] = $notifications;
        $input                 = $this->setting_model->getCurrentSessionName();

        list($a, $b)  = explode('-', $input);
        $Current_year = $a;
        if (strlen($b) == 2) {
            $Next_year = substr($a, 0, 2) . $b;
        } else {
            $Next_year = $b;
        }
        $data['mysqlVersion'] = $this->setting_model->getMysqlVersion();
        $data['sqlMode']      = $this->setting_model->getSqlMode();
        //========================== Current Attendence ==========================
        $current_date       = date('Y-m-d');
        $data['title']      = 'Dashboard';
        $Current_start_date = date('01');
        $Current_date       = date('d');
        $Current_month      = date('m');
        $month_collection   = 0;
        $month_expense      = 0;
        $total_students     = 0;
        $total_teachers     = 0;
        $ar                 = $this->startmonthandend();
        $year_str_month     = $Current_year . '-' . $ar[0] . '-01';
        $year_end_month     = date("Y-m-t", strtotime($Next_year . '-' . $ar[1] . '-01'));
        $getDepositeAmount  = $this->studentfeemaster_model->getDepositAmountBetweenDate($year_str_month, $year_end_month);
        //======================Current Month Collection ==============================
        $first_day_this_month     = date('Y-m-01');
        $current_month_collection = $this->studentfeemaster_model->getDepositAmountBetweenDate($first_day_this_month, $current_date);
        $month_collection         = $this->whatever($current_month_collection, $first_day_this_month, $current_date);
        $expense                  = $this->expense_model->getTotalExpenseBwdate($first_day_this_month, $current_date);
        if (!empty($expense)) {
            $month_expense = $month_expense + $expense->amount;
        }

        $data['month_collection'] = $month_collection;
        $data['month_expense']    = $month_expense;

        $tot_students = $this->studentsession_model->getTotalStudentBySession();
        if (!empty($tot_students)) {
            $total_students = $tot_students->total_student;
        }

        $data['total_students'] = $total_students;

        $tot_roles = $this->role_model->get();

        foreach ($tot_roles as $key => $value) {

            $count_roles[$value["name"]] = $this->role_model->count_roles($value["id"]);

        }
        $data["roles"] = $count_roles;

        //======================== get collection by month ==========================
        $start_month = strtotime($year_str_month);
        $start       = strtotime($year_str_month);
        $end         = strtotime($year_end_month);
        $coll_month  = array();
        $s           = array();
        $total_month = array();
        while ($start_month <= $end) {
            $total_month[] = date('M', $start_month);
            $month_start   = date('Y-m-d', $start_month);
            $month_end     = date("Y-m-t", $start_month);
            $return        = $this->whatever($getDepositeAmount, $month_start, $month_end);
            if ($return) {
                $s[] = $return;
            } else {
                $s[] = "0.00";
            }

            $start_month = strtotime("+1 month", $start_month);
        }
        //======================== getexpense by month ==============================
        $ex                  = array();
        $start_session_month = strtotime($year_str_month);
        while ($start_session_month <= $end) {

            $month_start = date('Y-m-d', $start_session_month);
            $month_end   = date("Y-m-t", $start_session_month);

            $expense_monthly = $this->expense_model->getTotalExpenseBwdate($month_start, $month_end);

            if (!empty($expense_monthly)) {
                $amt  = 0;
                $ex[] = $amt + $expense_monthly->amount;
            }

            $start_session_month = strtotime("+1 month", $start_session_month);
        }

        $data['yearly_collection'] = $s;
        $data['yearly_expense']    = $ex;
        $data['total_month']       = $total_month;

        //======================= current month collection /expense ===================
        // hardcoded '01' for first day
        $startdate       = date('m/01/Y');
        $enddate         = date('m/t/Y');
        $start           = strtotime($startdate);
        $end             = strtotime($enddate);
        $currentdate     = $start;
        $month_days      = array();
        $days_collection = array();
        while ($currentdate <= $end) {
            $cur_date          = date('Y-m-d', $currentdate);
            $month_days[]      = date('d', $currentdate);
            $coll_amt          = $this->whatever($getDepositeAmount, $cur_date, $cur_date);
            $days_collection[] = $coll_amt;
            $currentdate       = strtotime('+1 day', $currentdate);
        }
        $data['current_month_days'] = $month_days;
        $data['days_collection']    = $days_collection;

        //======================= current month /expense ==============================
        // hardcoded '01' for first day

        $startdate    = date('m/01/Y');
        $enddate      = date('m/t/Y');
        $start        = strtotime($startdate);
        $end          = strtotime($enddate);
        $currentdate  = $start;
        $days_expense = array();
        while ($currentdate <= $end) {
            $cur_date       = date('Y-m-d', $currentdate);
            $month_days[]   = date('d', $currentdate);
            $currentdate    = strtotime('+1 day', $currentdate);
            $ct             = $this->getExpensebyday($cur_date);
            $days_expense[] = $ct;
        }

        $data['days_expense']        = $days_expense;
        $student_fee_history         = $this->studentfee_model->getTodayStudentFees();
        $data['student_fee_history'] = $student_fee_history;

        $event_colors         = array("#03a9f4", "#c53da9", "#757575", "#8e24aa", "#d81b60", "#7cb342", "#fb8c00", "#fb3b3b");
        $data["event_colors"] = $event_colors;
        $userdata             = $this->customlib->getUserData();
        $data["role"]         = $userdata["user_type"];
        $start_date           = date('Y-m-01');
        $end_date             = date('Y-m-t');
        $student_due_fee      = $this->studentfeemaster_model->getFeesAwaiting($start_date, $end_date);

        $data['fees_awaiting'] = $student_due_fee;

        $total_fess    = 0;
        $total_paid    = 0;
        $total_unpaid  = 0;
        $total_partial = 0;

        if (!empty($data['fees_awaiting'])) {

            foreach ($data['fees_awaiting'] as $awaiting_key => $awaiting_value) {

                $amount_to_be_taken = 0;
                if ($awaiting_value->is_system) {
                    if ($awaiting_value->amount > 0) {
                        $amount_to_be_taken = $awaiting_value->amount;
                    }
                } elseif ($awaiting_value->is_system == 0) {
                    if ($awaiting_value->fee_amount > 0) {
                        $amount_to_be_taken = $awaiting_value->fee_amount;
                    }

                }
                if ($amount_to_be_taken > 0) {
                    $total_fess++;

                    if (is_string($awaiting_value->amount_detail) && is_array(json_decode($awaiting_value->amount_detail, true)) && (json_last_error() == JSON_ERROR_NONE)) {
                        $amount_paid_details = (json_decode($awaiting_value->amount_detail));
                        $amt_                = 0;
                        foreach ($amount_paid_details as $amount_paid_detail_key => $amount_paid_detail_value) {
                            $amt_ = $amt_ + $amount_paid_detail_value->amount;
                        }

                        if (($amt_ + $amount_paid_detail_value->amount_discount) >= $amount_to_be_taken) {
                            $total_paid++;
                        } elseif (($amt_ + $amount_paid_detail_value->amount_discount) < $amount_to_be_taken) {
                            $total_partial++;
                        }
                    } else {
                        $total_unpaid++;
                    }

                }
            }
        }

        $data['incomegraph'] = $this->income_model->getIncomeHeadsData($start_date, $end_date);
        $data['expensegraph'] = $this->expense_model->getExpenseHeadData($start_date, $end_date);
        $enquiry       = $this->admin_model->getAllEnquiryCount($start_date, $end_date);
        $total_counter = $total_paid + $total_unpaid + $total_partial;

        $data['fees_overview'] = array(
            'total_unpaid'     => $total_unpaid,
            'unpaid_progress'  => ($total_counter > 0) ? (($total_unpaid * 100) / $total_counter) : 0,
            'total_paid'       => $total_paid,
            'paid_progress'    => ($total_counter > 0) ? (($total_paid * 100) / $total_counter) : 0,
            'total_partial'    => $total_partial,
            'partial_progress' => ($total_counter > 0) ? (($total_partial * 100) / $total_counter) : 0,
        );

        $total_enquiry = $enquiry['total'];

        if ($total_enquiry > 0) {

            $data['enquiry_overview'] = array(
                'won'              => $enquiry['complete'],
                'won_progress'     => ($enquiry['complete'] * 100) / $total_enquiry,
                'active'           => $enquiry['active'],
                'active_progress'  => ($enquiry['active'] * 100) / $total_enquiry,
                'passive'          => $enquiry['passive'],
                'passive_progress' => ($enquiry['passive'] * 100) / $total_enquiry,
                'dead'             => $enquiry['dead'],
                'dead_progress'    => ($enquiry['dead'] * 100) / $total_enquiry,
                'lost'             => $enquiry['lost'],
                'lost_progress'    => ($enquiry['lost'] * 100) / $total_enquiry,
            );

        } else {

            $data['enquiry_overview'] = array(
                'won'              => 0,
                'won_progress'     => 0,
                'active'           => 0,
                'active_progress'  => 0,
                'passive'          => 0,
                'passive_progress' => 0,
                'dead'             => 0,
                'dead_progress'    => 0,
                'lost'             => 0,
                'lost_progress'    => 0,
            );

        }

        $data['total_paid'] = $total_paid;
        $data['total_fees'] = $total_fess;
        if ($total_fess > 0) {
            $data['fessprogressbar'] = ($total_paid * 100) / $total_fess;
        } else {
            $data['fessprogressbar'] = 0;
        }

        $data['total_enquiry']  = $total_enquiry  = $enquiry['total'];
        $data['total_complete'] = $complete_enquiry = $enquiry['complete'];
        if ($total_enquiry > 0) {
            $data['fenquiryprogressbar'] = ($complete_enquiry * 100) / $total_enquiry;
        } else {
            $data['fenquiryprogressbar'] = 0;
        }

        $bookoverview      = $this->book_model->bookoverview($start_date, $end_date);
        $bookduereport     = $this->bookissue_model->dueforreturn($start_date, $end_date);
        $forreturndata     = $this->bookissue_model->forreturn($start_date, $end_date);
        $dueforreturn      = $bookduereport[0]['total'];
        $forreturn         = $forreturndata[0]['total'];
        $total_qty         = $bookoverview[0]['qty'];
        $total_issued      = $bookoverview[0]['total_issue'];
        $availble          = '0';
        $availble_progress = 0;
        $issued_progress   = 0;
        if ($total_qty > 0) {
            $availble          = $total_qty - $total_issued;
            $availble_progress = ($availble * 100) / $total_qty;
            $issued_progress   = ($total_issued * 100) / $total_qty;
        }
        $data['book_overview'] = array(
            'total'             => $total_qty,
            'total_progress'    => 100,
            'availble'          => $availble,
            'availble_progress' => round($availble_progress, 2),
            'total_issued'      => $total_issued,
            'issued_progress'   => round($issued_progress, 2),
            'dueforreturn'      => $dueforreturn,
            'forreturn'         => $forreturn,
        );

        $Attendence                   = $this->stuattendence_model->getTodayDayAttendance($total_students);
        $data['attendence_data']      = $Attendence;
        $Staffattendence              = $this->Staff_model->getTodayDayAttendance();
        $data['Staffattendence_data'] = $Staffattendence;
        $getTotalStaff                = $this->Staff_model->getTotalStaff();
        $data['getTotalStaff_data']   = $getTotalStaff;
        if ($getTotalStaff > 0) {$percentTotalStaff_data = ($Staffattendence * 100) / ($getTotalStaff);} else { $percentTotalStaff_data = '0';}
        $data['percentTotalStaff_data'] = $percentTotalStaff_data;
        $data['sch_setting']            = $this->sch_setting_detail;

        if ($data['sch_setting']->attendence_type == 0) {
            $data['std_graphclass'] = "col-lg-3 col-md-6 col-sm-6";
        } else {
            $data['std_graphclass'] = "col-lg-4 col-md-6 col-sm-6";
        }
       
        $this->load->view('layout/header', $data);
        $this->load->view('admin/dashboard', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getUserImage()
    {
        $id     = $this->session->userdata["admin"]["id"];
        $result = $this->staff_model->get($id);
    }

    public function getSession()
    {
        if (!$this->rbac->hasPrivilege('quick_session_change', 'can_view')) {
            access_denied();
        }
        $session             = $this->session_model->getAllSession();
        $data                = array();
        $session_array       = $this->session->has_userdata('session_array');
        $data['sessionData'] = array('session_id' => 0);
        if ($session_array) {
            $data['sessionData'] = $this->session->userdata('session_array');
        } else {
            $setting             = $this->setting_model->get();
            $data['sessionData'] = array('session_id' => $setting[0]['session_id']);
        }
        $data['sessionList'] = $session;
        $this->load->view('admin/partial/_session', $data);
    }

    public function updateSession()
    {
        $session       = $this->input->post('popup_session');
        $session_array = $this->session->has_userdata('session_array');
        if ($session_array) {
            $this->session->unset_userdata('session_array');
        } 
        $session       = $this->session_model->get($session);
        $session_array = array('session_id' => $session['id'], 'session' => $session['session']);
        $this->session->set_userdata('session_array', $session_array);
        echo json_encode(array('status' => 1, 'message' => $this->lang->line('session_changed_successfully')));
    }

    public function updatePurchaseCode()
    {
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim|xss_clean');
        $this->form_validation->set_rules('envato_market_purchase_code', 'Purchase Code', 'required|trim|xss_clean');
        if ($this->form_validation->run() == false) {
            $data = array(
                'email'                       => form_error('email'),
                'envato_market_purchase_code' => form_error('envato_market_purchase_code'),
            );
            $array = array('status' => '2', 'error' => $data);

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($array));
        } else {
            //==================
            $response = $this->auth->app_update();
        }
    }

    // Ajouter cette méthode dans Admin.php
    public function setAutoBackupSchedule() {
        if (!$this->rbac->hasPrivilege('backup', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('auto_backup', $this->lang->line('auto_backup'), 'trim|required');
        $this->form_validation->set_rules('backup_day', $this->lang->line('backup_day'), 'trim|required');

        if ($this->form_validation->run() == false) {
            $data = array(
                'auto_backup' => form_error('auto_backup'),
                'backup_day' => form_error('backup_day')
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {
            $auto_backup = $this->input->post('auto_backup');
            $backup_day = $this->input->post('backup_day');

            $data = array(
                'id' => 1,
                'auto_backup' => $auto_backup,
                'backup_day' => $backup_day
            );

            $this->setting_model->add($data);

            // Créer une tâche cron automatiquement si activé
            if ($auto_backup == '1') {
                $this->setupAutoBackupCron($backup_day);
            }

            $array = array('status' => 'success', 'message' => $this->lang->line('update_message'));
            echo json_encode($array);
        }
    }

// Méthode pour configurer le cron automatique
    private function setupAutoBackupCron($day) {
        $cron_command = "59 23 * * * curl -s " . base_url() . "admin/admin/autoBackup/" . $this->setting_model->getSetting()->cron_secret_key . " > /dev/null 2>&1";

        // Sauvegarder la commande cron dans un fichier pour référence
        $cron_info = array(
            'command' => $cron_command,
            'description' => 'Sauvegarde automatique quotidienne à 23:59',
            'day' => $day,
            'created_at' => date('Y-m-d H:i:s')
        );

        file_put_contents(APPPATH . 'logs/backup_cron.log', json_encode($cron_info));
    }

    private function ensureBackupDirectory() {
        $dirs = array(
            './backup',
            './backup/database_backup',
            './backup/temp_uploaded'
        );

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
        }
    }

    private function createDatabaseBackupFile($prefix = 'manual') {
        $this->ensureBackupDirectory();
        $this->load->helper('file');
        $this->load->dbutil();
        $version  = $this->customlib->getAppVersion();
        $filename = 'db_' . $prefix . '_' . $version . '_' . date('Y-m-d_H-i-s') . '.sql';
        $prefs    = array(
            'ignore'     => array(),
            'format'     => 'txt',
            'filename'   => 'mybackup.sql',
            'add_drop'   => true,
            'add_insert' => true,
            'newline'    => "\n",
        );

        $backup = $this->dbutil->backup($prefs);
        $path   = './backup/database_backup/' . $filename;
        if (!write_file($path, $backup)) {
            return false;
        }

        return $filename;
    }

    public function maybe_run_daily_auto_backup() {
        $marker_file = FCPATH . 'backup/.last_daily_backup';
        $today = date('Y-m-d');

        if (date('H:i') < '23:59') {
            return false;
        }

        $last_run = file_exists($marker_file) ? trim((string) file_get_contents($marker_file)) : '';
        if ($last_run === $today) {
            return false;
        }

        $created = $this->createDatabaseBackupFile('daily_' . date('Ymd'));
        if ($created) {
            file_put_contents($marker_file, $today);
            return true;
        }

        return false;
    }

// Méthode pour la sauvegarde automatique via cron
    public function autoBackup($secret_key = null) {
        $setting = $this->setting_model->getSetting();

        if ($secret_key != $setting->cron_secret_key) {
            log_message('error', 'Tentative de sauvegarde automatique avec clé secrète invalide');
            show_404();
        }

        if ($setting->auto_backup != '1') {
            log_message('error', 'Sauvegarde automatique désactivée dans les paramètres');
            return;
        }

        try {
            $this->ensureBackupDirectory();
            $created = $this->createDatabaseBackupFile('auto_' . date('Ymd'));
            if ($created) {
                $this->backupImportantFiles();
                log_message('info', 'Sauvegarde automatique effectuée avec succès: ' . $created);
            }
        } catch (Exception $e) {
            log_message('error', 'Erreur lors de la sauvegarde automatique: ' . $e->getMessage());
        }
    }


// Méthode pour calculer la prochaine date de sauvegarde
    private function getNextBackupDate($day) {
        $days = array(
            '0' => 'sunday',
            '1' => 'monday',
            '2' => 'tuesday',
            '3' => 'wednesday',
            '4' => 'thursday',
            '5' => 'friday',
            '6' => 'saturday'
        );

        $next_date = date('Y-m-d', strtotime('next ' . $days[$day]));
        return $this->customlib->dateformat($next_date);
    }

// Méthode pour sauvegarder les fichiers importants
    private function backupImportantFiles() {
        $backup_dirs = array(
            './uploads/',
            './images/',
            './themes/'
        );

        $this->load->library('zip');

        foreach ($backup_dirs as $dir) {
            if (is_dir($dir)) {
                $this->zip->read_dir($dir, false);
            }
        }

        $files_filename = 'files_backup_' . date('Y-m-d_H-i-s') . '.zip';
        $this->zip->archive('./backup/' . $files_filename);
    }
    public function backup()
    {
        if (!$this->rbac->hasPrivilege('backup', 'can_view')) {
            access_denied();
        }

        $this->ensureBackupDirectory();
        if ($this->maybe_run_daily_auto_backup()) {
            $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Sauvegarde automatique quotidienne effectuée à 23:59.</div>');
        }

        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'admin/backup');
        $data['title'] = 'Backup History';
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            if ($this->input->post('backup') == "upload") {
                $this->form_validation->set_rules('file', 'Image', 'callback_handle_upload');
                if ($this->form_validation->run() == false) {

                } else {
                    if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
                        $fileInfo  = pathinfo($_FILES["file"]["name"]);
                        $file_name = "db-" . date("Y-m-d_H-i-s") . ".sql";
                        move_uploaded_file($_FILES["file"]["tmp_name"], "./backup/temp_uploaded/" . $file_name);
                        $folder_name  = 'temp_uploaded';
                        $path         = './backup/';
                        $file_restore = $this->load->file($path . $folder_name . '/' . $file_name, true);
                        $file_array   = explode(';', $file_restore);
                        foreach ($file_array as $query) {
                            $trimQuery1 = trim($query);
                            if (!empty($trimQuery1)) {
                                $this->db->query("SET FOREIGN_KEY_CHECKS = 0");
                                $this->db->query($query);
                                $this->db->query("SET FOREIGN_KEY_CHECKS = 1");
                            }
                        }
                        $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Backup restored successfully!</div>');
                        redirect('admin/admin/backup');
                    }
                }
            }
            if ($this->input->post('backup') == "backup") {
                $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">Backup created successfully!</div>');
                $this->load->helper('download');
                $this->load->dbutil();
                $version  = $this->customlib->getAppVersion();
                $filename = "db_ver_" . $version . '_' . date("Y-m-d_H-i-s") . ".sql";
                $prefs    = array(
                    'ignore'     => array(),
                    'format'     => 'txt',
                    'filename'   => 'mybackup.sql',
                    'add_drop'   => true,
                    'add_insert' => true,
                    'newline'    => "\n",
                );
                $backup = $this->dbutil->backup($prefs);
                $this->load->helper('file');
                write_file('./backup/database_backup/' . $filename, $backup);
                redirect('admin/admin/backup');
                force_download($filename, $backup);
                $this->session->set_flashdata('feedback', 'Success message for client to see');
                redirect('admin/admin/backup');
            } else if ($this->input->post('backup') == "restore") {
                $folder_name  = 'database_backup';
                $file_name    = $this->input->post('filename');
                $path         = './backup/';
                $filePath     = $path . $folder_name . '/' . $file_name;
                $file_restore = $this->load->file($path . $folder_name . '/' . $file_name, true);
                $db           = (array) get_instance()->db;
                $conn         = mysqli_connect('localhost', $db['username'], $db['password'], $db['database']);

                $sql       = '';
                $error     = false;
                $error_msg = "";
                $result    = mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");

                if (!$result) {
                    $error_msg = "Database failed: " . mysqli_error();
                    $error     = true;
                }
                if (!$error) {
                    if (file_exists($filePath)) {
                        $lines = file($filePath);

                        foreach ($lines as $line) {

                            // Ignoring comments from the SQL script
                            if (substr($line, 0, 2) == '--' || $line == '') {
                                continue;
                            }

                            $sql .= $line;

                            if (substr(trim($line), -1, 1) == ';') {
                                $result = mysqli_query($conn, $sql);
                                if (!$result) {
                                    $error_msg = "Database failed: " . mysqli_error();
                                    $error     = true;
                                    break;
                                }
                                $sql = '';
                            }
                        }
                        if (!$error) {

                            $msg = "Backup restored successfully!";
                        }

                    } // end if file exists

                }

                $result = mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");
                if (!$result) {
                    $error_msg = "Database failed: " . mysqli_error();
                    $error     = true;
                }
                if ($error) {
                    $this->session->set_flashdata('msg', '<div class="alert alert-danger text-left">' . $msg . '</div>');
                } else {
                    $this->session->set_flashdata('msg', '<div class="alert alert-success text-left">' . $msg . '</div>');
                }

                redirect('admin/admin/backup');
            }
        }
        $dir    = "./backup/database_backup/";
        $result = array();
        $cdir   = scandir($dir);
        foreach ($cdir as $key => $value) {
            if (!in_array($value, array(".", ".."))) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                    $result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value);
                } else {
                    $result[] = $value;
                }
            }
        }
        $data['dbfileList']  = $result;
        $setting_result      = $this->setting_model->get();
        $data['settinglist'] = $setting_result;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/backup', $data);
        $this->load->view('layout/footer', $data);
    }

    public function changepass()
    {
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'changepass/index');
        $data['title'] = 'Change Password';
        $this->form_validation->set_rules('current_pass', 'Current password', 'trim|required|xss_clean');
        $this->form_validation->set_rules('new_pass', 'New password', 'trim|required|xss_clean|matches[confirm_pass]');
        $this->form_validation->set_rules('confirm_pass', 'Confirm password', 'trim|required|xss_clean');
        if ($this->form_validation->run() == false) {
            $sessionData            = $this->session->userdata('loggedIn');
            $this->data['id']       = $sessionData['id'];
            $this->data['username'] = $sessionData['username'];
            $this->load->view('layout/header', $data);
            $this->load->view('admin/change_password', $data);
            $this->load->view('layout/footer', $data);
        } else {
            $sessionData = $this->session->userdata('admin');
            $userdata    = $this->customlib->getUserData();
            $data_array  = array(
                'current_pass' => $this->input->post('current_pass'),
                'new_pass'     => md5($this->input->post('new_pass')),
                'user_id'      => $sessionData['id'],
                'user_email'   => $sessionData['email'],
                'user_name'    => $sessionData['username'],
            );
            $newdata = array(
                'id'       => $sessionData['id'],
                'password' => $this->enc_lib->passHashEnc($this->input->post('new_pass')),
            );
            $check = $this->enc_lib->passHashDyc($this->input->post('current_pass'), $userdata["password"]);
            $query1 = $this->admin_model->checkOldPass($data_array);

            if ($query1) {

                if ($check) {
                    $query2 = $this->admin_model->saveNewPass($newdata);
                    if ($query2) {
                        $data['error_message'] = "<div class='alert alert-success'>Password changed successfully</div>";
                        $this->load->view('layout/header', $data);
                        $this->load->view('admin/change_password', $data);
                        $this->load->view('layout/footer', $data);
                    }
                } else {
                    $data['error_message'] = "<div class='alert alert-danger'>Invalid current password</div>";
                    $this->load->view('layout/header', $data);
                    $this->load->view('admin/change_password', $data);
                    $this->load->view('layout/footer', $data);
                }
            } else {

                $data['error_message'] = "<div class='alert alert-danger'>Invalid current password</div>";
                $this->load->view('layout/header', $data);
                $this->load->view('admin/change_password', $data);
                $this->load->view('layout/footer', $data);
            }
        }
    }

    public function pdf_report()
    {
        $data        = array();
        $html        = $this->load->view('reports/students_detail', $data, true);
        $pdfFilePath = "output_pdf_name.pdf";
        $this->load->library('m_pdf');
        $this->m_pdf->pdf->WriteHTML($html);
        $this->m_pdf->pdf->Output($pdfFilePath, "D");
    }

    public function downloadbackup($file)
    {
        $this->load->helper('download');
        $filepath = "./backup/database_backup/" . $file;
        $data     = file_get_contents($filepath);
        $name     = $file;
        force_download($name, $data);
    }

    public function dropbackup($file)
    {
        if (!$this->rbac->hasPrivilege('backup', 'can_delete')) {
            access_denied();
        }
        unlink('./backup/database_backup/' . $file);
        redirect('admin/admin/backup');
    }

    public function search()
    {

        $data['title']           = 'Search';
        $search_text             = $this->input->post('search_text1');
        $data['sch_setting']     = $this->sch_setting_detail;
        $data['search_text']     = trim($this->input->post('search_text1'));
        $userdata                = $this->customlib->getUserData();
        $data['adm_auto_insert'] = $this->sch_setting_detail->adm_auto_insert;
        $carray                  = array();
        $class                   = $this->class_model->get();
        $data['classlist']       = $class;
        $data['fields']          = $this->customfield_model->get_custom_fields('students', 1);
        $userdata                = $this->customlib->getUserData();
        $carray                  = array();

        if (!empty($data["classlist"])) {
            foreach ($data["classlist"] as $ckey => $cvalue) {

                $carray[] = $cvalue["id"];
            }
        }

        $resultlist = $this->student_model->searchusersbyFullText($search_text, $carray);

        $data['resultlist'] = $resultlist;
        $this->load->view('layout/header', $data);
        $this->load->view('admin/search', $data);
        $this->load->view('layout/footer', $data);
    }

    public function getCollectionbymonth()
    {
        $result = $this->admin_model->getMonthlyCollection();
        return $result;
    }

    public function getCollectionbyday($date)
    {
        $result = $this->admin_model->getCollectionbyDay($date);
        if ($result[0]['amount'] == "") {
            $return = 0;
        } else {
            $return = $result[0]['amount'];
        }
        return $return;
    }

    public function getExpensebyday($date)
    {
        $result = $this->admin_model->getExpensebyDay($date);
        if ($result[0]['amount'] == "") {
            $return = 0;
        } else {
            $return = $result[0]['amount'];
        }
        return $return;
    }

    public function getExpensebymonth()
    {
        $result = $this->admin_model->getMonthlyExpense();
        return $result;
    }

    public function whatever($feecollection_array, $start_month_date, $end_month_date)
    {
        $return_amount = 0;
        $st_date       = strtotime($start_month_date);
        $ed_date       = strtotime($end_month_date);
        if (!empty($feecollection_array)) {
            while ($st_date <= $ed_date) {
                $date = date('Y-m-d', $st_date);
                foreach ($feecollection_array as $key => $value) {

                    if ($value['date'] == $date) {

                        $return_amount = $return_amount + $value['amount'] + $value['amount_fine'];
                    }
                }
                $st_date = $st_date + 86400;
            }
        } else {

        }

        return $return_amount;
    }

    public function startmonthandend()
    {
        $startmonth = $this->setting_model->getStartMonth();
        if ($startmonth == 1) {
            $endmonth = 12;
        } else {
            $endmonth = $startmonth - 1;
        }
        return array($startmonth, $endmonth);
    }

    public function handle_upload()
    {
        if (isset($_FILES["file"]) && !empty($_FILES['file']['name'])) {
            $allowedExts = array('sql');
            $temp        = explode(".", $_FILES["file"]["name"]);
            $extension   = end($temp);
            if ($_FILES["file"]["error"] > 0) {
                $error .= "Error opening the file<br />";
            }
            if ($_FILES["file"]["type"] != 'application/octet-stream') {

                $this->form_validation->set_message('handle_upload', 'File type not allowed');
                return false;
            }
            if (!in_array($extension, $allowedExts)) {

                $this->form_validation->set_message('handle_upload', 'Extension not allowed');
                return false;
            }
            if ($_FILES["file"]["size"] > 102400000) {

                $this->form_validation->set_message('handle_upload', 'File size shoud be less than 100 MB');
                return false;
            }
            return true;
        } else {
            $this->form_validation->set_message('handle_upload', 'File field is required');
            return false;
        }
    }

    public function generate_key($length = 12)
    {
        $str        = "";
        $characters = array_merge(range('A', 'Z'), range('a', 'z'), range('0', '9'));
        $max        = count($characters) - 1;
        for ($i = 0; $i < $length; $i++) {
            $rand = mt_rand(0, $max);
            $str .= $characters[$rand];
        }
        return $str;
    }

    public function addCronsecretkey($id)
    {
        $key = $this->generate_key(25);
        $data = array('cron_secret_key' => $key);
        $this->setting_model->add_cronsecretkey($data, $id);
        redirect('admin/admin/backup');
    }

    public function updateandappCode()
    {
        $this->form_validation->set_rules('app-email', 'Email', 'required|valid_email|trim|xss_clean');
        $this->form_validation->set_rules('app-envato_market_purchase_code', 'Purchase Code', 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'app-email'                       => form_error('app-email'),
                'app-envato_market_purchase_code' => form_error('app-envato_market_purchase_code'),
            );
            $array = array('status' => '2', 'error' => $data);

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($array));
        } else {
            //==================
            $response = $this->auth->andapp_update();
        }
    }

    public function filetype()
    {
    
        $data          = array();
        $data['title'] = 'File Type List';
        $this->session->set_userdata('top_menu', 'System Settings');
        $this->session->set_userdata('sub_menu', 'System Settings/filetype');
        $data['filetype'] = $this->filetype_model->get();
        $this->load->view('layout/header', $data);
        $this->load->view('admin/filetype', $data);
        $this->load->view('layout/footer', $data);
    }

    public function addfiletype()
    {
        $this->form_validation->set_rules('file_extension', $this->lang->line('allowed_extension'), 'required|trim|xss_clean|callback_validate_extension');
        $this->form_validation->set_rules('image_extension', $this->lang->line('allowed_extension'), 'required|trim|xss_clean|callback_validate_extension');
        $this->form_validation->set_rules('file_mime', $this->lang->line('allowed_mime_type'), 'required|trim|xss_clean|callback_validate_mime');
        $this->form_validation->set_rules('image_mime', $this->lang->line('allowed_mime_type'), 'required|trim|xss_clean|callback_validate_mime');
        $this->form_validation->set_rules('image_size', $this->lang->line('upload_size_in_bytes'), 'required|trim|xss_clean');
        $this->form_validation->set_rules('file_size', $this->lang->line('upload_size_in_bytes'), 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {
            $data = array(
                'file_extension'  => form_error('file_extension'),
                'file_mime'       => form_error('file_mime'),
                'image_extension' => form_error('image_extension'),
                'image_mime'      => form_error('image_mime'),
                'image_size'      => form_error('image_size'),
                'file_size'       => form_error('file_size'),
            );
            $array = array('status' => 'fail', 'error' => $data);
            echo json_encode($array);
        } else {
            $insert_array = array(
                'file_extension'  => $this->input->post('file_extension'),
                'file_mime'       => $this->input->post('file_mime'),
                'image_extension' => $this->input->post('image_extension'),
                'image_mime'      => $this->input->post('image_mime'),
                'file_size'       => $this->input->post('file_size'),
                'image_size'      => $this->input->post('image_size'),
            );

            $inserted_id = $this->filetype_model->add($insert_array);

            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
            echo json_encode($array);
        }
    }

    public function validate_extension($extension)
    {
        if (preg_match('/^([A-Za-z0-9]+)(,\s[A-Za-z0-9]+)*$/', $extension)) {
            return true;
        } else {
            $this->form_validation->set_message('validate_extension', 'The %s field must be like jpg, jpeg');
            return false;
        }
    }

    public function validate_mime($mime)
    {
        if (preg_match('/^([A-Za-z0-9-.+\/]+)(,\s[A-Za-z0-9-.+\/]+)*$/', $mime)) {
            return true;
        } else {
            $this->form_validation->set_message('validate_mime', 'The %s field must be like audio/mp4, video/mp4');
            return false;
        }
    }

      public function updateaddon()
    {
        $this->form_validation->set_rules('app-email', 'Email', 'required|valid_email|trim|xss_clean');
        $this->form_validation->set_rules('app-envato_market_purchase_code', 'Purchase Code', 'required|trim|xss_clean');

        if ($this->form_validation->run() == false) {

            $data = array(
                'app-email'                       => form_error('app-email'),
                'app-envato_market_purchase_code' => form_error('app-envato_market_purchase_code'),
            );
            
            $array = array('status' => '2', 'error' => $data);

            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($array));
        } else {
            //==================
            $response = $this->auth->addon_update();
        }
    }

    public function searchvalidation()
    {
        $search_text1       = $this->input->post('search_text1');
        $params      = array('search_text1'=> $search_text1);
        $array       = array('status' => 1, 'error' => '', 'params' => $params);
        echo json_encode($array);
    }

    /**
 * Récupérer le pays de l'utilisateur via IP
 * À AJOUTER dans le contrôleur Admin.php
 */
/**
 * Récupérer le pays de l'utilisateur via IP
 */
public function getUserCountry() {
    // Ne pas passer d'IP à l'API, elle détecte automatiquement celle du visiteur
    $url = "http://ip-api.com/json/?fields=status,country,countryCode,region,regionName,city,timezone";
    
    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode == 200 && $response) {
            $data = json_decode($response, true);
            
            if ($data && isset($data['status']) && $data['status'] == 'success') {
                return array(
                    'country' => $data['country'] ?? 'Non détecté',
                    'country_code' => $data['countryCode'] ?? '??',
                    'region' => $data['regionName'] ?? '',
                    'city' => $data['city'] ?? '',
                    'timezone' => $data['timezone'] ?? ''
                );
            }
        }
    } catch (Exception $e) {
        log_message('error', 'Erreur de géolocalisation: ' . $e->getMessage());
    }
    
    return array(
        'country' => 'Non détecté',
        'country_code' => '??',
        'region' => '',
        'city' => '',
        'timezone' => ''
    );
}
/**
 * Rafraîchir le pays de l'utilisateur (AJAX)
 */
public function refreshUserCountry() {
    $country = $this->getUserCountry();
    $this->session->set_userdata('user_country', $country);
    echo json_encode($country);
}
/**
 * Rafraîchir le pays de l'utilisateur (AJAX)
 * À AJOUTER dans le contrôleur Admin.php
 */


     public function dtstudentlist($search_text)
    {
       if($search_text==="0"){           
            $search_text="";
        }
       $sch_setting    = $this->sch_setting_detail;
       $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $classlist                   = $this->class_model->get();
                $classlist      = $classlist;
                $carray   = array();
                if (!empty($classlist)) {
                    foreach ($classlist as $ckey => $cvalue) {

                        $carray[] = $cvalue["id"];
                    }
                }
        
      
        $resultlist = $this->student_model->searchFullText($search_text, $carray);
        $fields = $this->customfield_model->get_custom_fields('students', 1);
        $students = json_decode($resultlist);
         $dt_data=array();
        if (!empty($students->data)) {
            foreach ($students->data as $student_key => $student) {

                $editbtn='';
                $deletebtn = '';
                $viewbtn='';

                 $viewbtn = "<a href='".base_url()."student/view/".$student->id."'   class='btn btn-default btn-xs'  data-toggle='tooltip' data-placement='left' title='" . $this->lang->line('show') . "'><i class='fa fa-reorder'></i></a>";

                 if ($this->rbac->hasPrivilege('student', 'can_edit')) {
                    $editbtn = "<a href='".base_url()."student/edit/".$student->id."'   class='btn btn-default btn-xs'  data-toggle='tooltip' data-placement='left' title='" . $this->lang->line('edit') . "'><i class='fa fa-pencil'></i></a>";
                }
                if ($this->rbac->hasPrivilege('collect_fees', 'can_add')) {
                    
                    $collectbtn = "<a href='".base_url()."studentfee/addfee/".$student->student_session_id."'   class='btn btn-default btn-xs'  data-toggle='tooltip' data-placement='left' title='" . $this->lang->line('add_fees') . "'><span >".$currency_symbol."</a>";
                }
             
                $row   = array();
                $row[] = $student->admission_no;
                $row[] =  "<a href='".base_url()."student/view/".$student->id."'>".$this->customlib->getFullName($student->firstname,$student->middlename,$student->lastname,$sch_setting->middlename,$sch_setting->lastname)."</a>";              
                  $row[] = $student->class . "(" . $student->section . ")";
                if ($sch_setting->father_name) {
                    $row[]= $student->father_name ;
                }
                
                   $row[]=  $this->customlib->dateformat($student->dob);
              

                $row[] = $student->gender;
                if ($sch_setting->category) {
                    $row[] = $student->category ;
                }
                if ($sch_setting->mobile_no) {
                    $row[] = $student->mobileno ;
                }

                foreach ($fields as $fields_key => $fields_value) {
                   
                    $custom_name = $fields_value->name ;
                   $display_field=$student->$custom_name;
                 if($fields_value->type == "link"){
                     $display_field= "<a href=".$student->$custom_name." target='_blank'>".$student->$custom_name."</a>";

                 }
                 $row[] = $display_field ;  

                }
                $row[] = $viewbtn.''.$editbtn.''.$collectbtn;

                $dt_data[] = $row;
            }

        }
        $json_data = array(
            "draw"            => intval($students->draw),
            "recordsTotal"    => intval($students->recordsTotal),
            "recordsFiltered" => intval($students->recordsFiltered),
            "data"            => $dt_data,
        );
        echo json_encode($json_data); 

    }

    public function tableau_bord() {
        $master_db = $this->load->database('master', TRUE);

        // Statistiques globales
        $data['stats'] = [
            'total_entreprises' => $master_db->count_all('entreprises'),
            'entreprises_actives' => $master_db->where('statut', 'actif')->count_all_results('entreprises'),
            'chiffre_affaires' => $master_db->select('SUM(montant_mensuel) as ca')
                ->from('abonnements')
                ->where('statut', 'actif')
                ->get()->row()->ca
        ];

        // Liste des entreprises
        $data['entreprises'] = $master_db->get('entreprises')->result();

        $this->load->view('admin/tableau_bord', $data);
    }

    public function creer_entreprise() {
        if ($this->input->post()) {
            $entreprise_data = [
                'uuid' => uniqid(),
                'nom_entreprise' => $this->input->post('nom_entreprise'),
                'rccm' => $this->input->post('rccm'),
                'contact_admin' => $this->input->post('contact_admin'),
                'email' => $this->input->post('email'),
                'plan_abonnement' => $this->input->post('plan_abonnement'),
                'date_creation' => date('Y-m-d H:i:s')
            ];

            $db_name = $this->multi_tenant_model->create_entreprise_database($entreprise_data);

            redirect('admin/tableau_bord');
        }

        $this->load->view('admin/creer_entreprise');
    }

    public function auto_backup() {
        // Vérification de la clé secrète
        $cron_key = $this->input->get('key');
        $setting = $this->setting_model->get(1); // Récupère les paramètres
        if (empty($cron_key) || $cron_key !== $setting[0]['cron_secret_key']) {
            http_response_code(403);
            die('Accès interdit – clé cron invalide.');
        }

        // Exécute la même logique que la création manuelle
        $this->load->model('backup_model');
        $backup_name = 'auto_backup_' . date('Y-m-d_H-i-s') . '.sql';
        $backup_path = './backup/' . $backup_name;

        if ($this->backup_model->create_backup($backup_path)) {
            // Optionnel : supprimer les anciennes sauvegardes automatiques (conserver les 30 derniers jours)
            $this->clean_old_auto_backups(30);
            echo "Sauvegarde automatique réussie : " . $backup_name;
        } else {
            echo "Erreur lors de la création de la sauvegarde automatique.";
        }
    }

    private function clean_old_auto_backups($days_to_keep = 30) {
        $backup_dir = './backup/';
        $files = glob($backup_dir . 'auto_backup_*.sql');
        $now = time();
        foreach ($files as $file) {
            if (is_file($file) && ($now - filemtime($file)) > ($days_to_keep * 86400)) {
                unlink($file);
            }
        }
    }

}
