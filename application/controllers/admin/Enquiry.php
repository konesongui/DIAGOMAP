<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Enquiry extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("enquiry_model");
        $this->load->model("setting_model"); // Ajouté pour les informations de l'école
        $this->config->load("payroll");
        $this->enquiry_status = $this->config->item('enquiry_status');
    }

    public function index() {
        if (!$this->rbac->hasPrivilege('permission_enquiry', 'can_view')) {
            access_denied();
        }
        $this->session->set_userdata('top_menu', 'HR');
        $this->session->set_userdata('sub_menu', 'admin/enquiry');
        $data['class_list'] = $this->class_model->get();
        $data["source_select"] = "";
        $data["status"] = "active";
        $data['stff_list'] = $this->staff_model->get();

        $this->form_validation->set_rules('from_date', $this->lang->line('enquiry')." ".$this->lang->line('from')." ".$this->lang->line('date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('to_date', $this->lang->line('enquiry')." ".$this->lang->line('to')." ".$this->lang->line('date'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == TRUE) {
            $source = $this->input->post("source");
            $status = $this->input->post("status");
            $date_from = date("Y-m-d", $this->customlib->datetostrtotime($this->input->post("from_date")));
            $date_to = date("Y-m-d", $this->customlib->datetostrtotime($this->input->post("to_date")));
            $data["source_select"] = $source;
            $data["status"] = $status;
            $enquiry_list = $this->enquiry_model->searchEnquiry($source, $date_from, $date_to, $status);
        } else {
            $enquiry_list = $this->enquiry_model->getenquiry_list();
        }

        foreach ($enquiry_list as $key => $value) {
            $follow_up = $this->enquiry_model->getFollowByEnquiry($value["id"]);
            $enquiry_list[$key]["followupdate"] = isset($follow_up["date"])?$follow_up["date"]:'';
            $enquiry_list[$key]["next_date"] = isset($follow_up["next_date"])?$follow_up["next_date"]:'';
            $enquiry_list[$key]["response"] = isset($follow_up["response"])?$follow_up["response"]:'';
            $enquiry_list[$key]["note"] = isset($follow_up["note"])?$follow_up["note"]:'';
            $enquiry_list[$key]["followup_by"] = isset($follow_up["followup_by"])?$follow_up["followup_by"]:'';
        }

        $data['enquiry_list'] = $enquiry_list;
        $data['enquiry_status'] = $this->enquiry_status;
        $data['Reference'] = $this->enquiry_model->get_reference();
        $data['sourcelist'] = $this->enquiry_model->getComplaintSource();
        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/enquiryview', $data);
        $this->load->view('layout/footer');
    }



    public function add() {
        if (!$this->rbac->hasPrivilege('permission_enquiry', 'can_add')) {
            access_denied();
        }

        // Validation des champs requis
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('contact', $this->lang->line('phone'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('source', $this->lang->line('source'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            $msg = array(
                'name' => form_error('name'),
                'contact' => form_error('contact'),
                'source' => form_error('source'),
                'date' => form_error('date'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            // Récupérer les données du formulaire
            $date = $this->input->post('date');
            $date_start = $this->input->post('date_start');
            $date_end = $this->input->post('date_end');
            $follow_up_date = $this->input->post('follow_up_date');

            // Convertir les dates si elles existent
            $enquiry = array(
                'name' => $this->input->post('name'),
                'contact' => $this->input->post('contact'),
                'address' => $this->input->post('address'),
                'email' => $this->input->post('email'),
                'reference' => $this->input->post('reference'),
                'assigned' => $this->input->post('assigned'),
                'source' => $this->input->post('source'),
                'description' => $this->input->post('description'),
                'note' => $this->input->post('note'),
                'class' => $this->input->post('class'),
                'no_of_child' => $this->input->post('no_of_child'),
                'status' => 'pending'
            );

            // Gérer la date principale (obligatoire)
            if (!empty($date)) {
                // Vérifier si la date est déjà au format YYYY-MM-DD
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                    $enquiry['date'] = $date;
                } else {
                    // Sinon, la convertir
                    $enquiry['date'] = date('Y-m-d', strtotime($date));
                }
            }

            // Gérer la date de début
            if (!empty($date_start)) {
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_start)) {
                    $enquiry['date_start'] = $date_start;
                } else {
                    $enquiry['date_start'] = date('Y-m-d', strtotime($date_start));
                }
            }

            // Gérer la date de fin
            if (!empty($date_end)) {
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_end)) {
                    $enquiry['date_end'] = $date_end;
                } else {
                    $enquiry['date_end'] = date('Y-m-d', strtotime($date_end));
                }
            }

            // Gérer la date de suivi
            if (!empty($follow_up_date)) {
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $follow_up_date)) {
                    $enquiry['follow_up_date'] = $follow_up_date;
                } else {
                    $enquiry['follow_up_date'] = date('Y-m-d', strtotime($follow_up_date));
                }
            }

            // Insertion dans la base de données
            $this->enquiry_model->add($enquiry);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    public function delete($id) {
        if (!$this->rbac->hasPrivilege('permission_enquiry', 'can_delete')) {
            access_denied();
        }
        if (!empty($id)) {
            $this->enquiry_model->enquiry_delete($id);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('delete_message'));
        }
        echo json_encode($array);
    }

    public function follow_up($enquiry_id, $status) {
        if (!$this->rbac->hasPrivilege('follow_up_admission_enquiry', 'can_view')) {
            access_denied();
        }
        $data['id'] = $enquiry_id;
        $data['enquiry_data'] = $this->enquiry_model->getenquiry_list($enquiry_id, $status);
        $data['next_date'] = $this->enquiry_model->next_follow_up_date($enquiry_id);
        $data['enquiry_status'] = $this->enquiry_status;
        $this->load->view('admin/frontoffice/follow_up_modal', $data);
    }

    function follow_up_insert() {
        if (!$this->rbac->hasPrivilege('follow_up_permission_enquiry', 'can_add')) {
            access_denied();
        }

        $this->form_validation->set_rules('response', $this->lang->line('response'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('follow_up_date'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('follow_up_date', $this->lang->line('next_follow_up_date'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            $msg = array(
                'response' => form_error('response'),
                'follow_up_date' => form_error('follow_up_date'),
                'date' => form_error('date'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $admin = $this->customlib->getLoggedInUserData();
            $follow_up = array(
                'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'next_date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('follow_up_date'))),
                'response' => $this->input->post('response'),
                'note' => $this->input->post('note'),
                'followup_by' => $admin['username'],
                'enquiry_id' => $this->input->post('enquiry_id')
            );
            $this->enquiry_model->add_follow_up($follow_up);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        }
        echo json_encode($array);
    }

    function follow_up_list($id) {
        $data['id'] = $id;
        $data['follow_up_list'] = $this->enquiry_model->getfollow_up_list($id);
        $this->load->view('admin/frontoffice/followuplist', $data);
    }

    function details($id, $status) {
        if (!$this->rbac->hasPrivilege('permission_enquiry', 'can_view')) {
            access_denied();
        }
        $data['source'] = $this->enquiry_model->getComplaintSource();
        $data['enquiry_type'] = $this->enquiry_model->get_enquiry_type();
        $data['Reference'] = $this->enquiry_model->get_reference();
        $data['class_list'] = $this->enquiry_model->getclasses();
        $data['enquiry_data'] = $this->enquiry_model->getenquiry_list($id, $status);
        $data['stff_list'] = $this->staff_model->get();
        $this->load->view('admin/frontoffice/enquiryeditmodalview', $data);
    }

    function editpost($id) {
        if (!$this->rbac->hasPrivilege('permission_enquiry', 'can_edit')) {
            access_denied();
        }
        $this->form_validation->set_rules('name', $this->lang->line('name'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('contact', $this->lang->line('contact'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('source', $this->lang->line('source'), 'trim|required|xss_clean');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'trim|required|xss_clean');

        if ($this->form_validation->run() == FALSE) {
            $msg = array(
                'name' => form_error('name'),
                'contact' => form_error('contact'),
                'source' => form_error('source'),
                'date' => form_error('date'),
            );
            $array = array('status' => 'fail', 'error' => $msg, 'message' => '');
        } else {
            $enquiry_update = array(
                'name' => $this->input->post('name'),
                'contact' => $this->input->post('contact'),
                'address' => $this->input->post('address'),
                'reference' => $this->input->post('reference'),
                'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'description' => $this->input->post('description'),
                'follow_up_date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('follow_up_date'))),
                'note' => $this->input->post('note'),
                'source' => $this->input->post('source'),
                'email' => $this->input->post('email'),
                'assigned' => $this->input->post('assigned'),
                'class' => $this->input->post('class'),
                'no_of_child' => $this->input->post('no_of_child')
            );
            $this->enquiry_model->enquiry_update($id, $enquiry_update);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('update_message'));
        }
        echo json_encode($array);
    }

    public function follow_up_delete($follow_up_id, $enquiry_id) {
        if (!$this->rbac->hasPrivilege('follow_up_permission_enquiry', 'can_delete')) {
            access_denied();
        }
        $this->enquiry_model->delete_follow_up($follow_up_id);
        $data['id'] = $enquiry_id;
        $data['follow_up_list'] = $this->enquiry_model->getfollow_up_list($enquiry_id);
        $this->load->view('admin/frontoffice/followuplist', $data);
    }

    public function check_default($post_string) {
        return $post_string == '' ? FALSE : TRUE;
    }

    public function change_status() {
        $id = $this->input->post("id");
        $status = $this->input->post("status");
        if (!empty($id)) {
            $data = array('id' => $id, 'status' => $status);
            $this->enquiry_model->changeStatus($data);
            $array = array('status' => 'success', 'error' => '', 'message' => $this->lang->line('success_message'));
        } else {
            $array = array('status' => 'fail', 'error' => '', 'message' => $this->lang->line('update_message'));
        }
        echo json_encode($array);
    }

    /**
     * Nouvelle méthode pour imprimer le document de permission
     * @param int $id ID de la demande
     */

    /**
     * IMPRIMER LE DOCUMENT D'ACCEPTATION
     */
    /**
     * IMPRIMER LE DOCUMENT D'ACCEPTATION
     */
    public function print_permission($id) {
        // Vérifier les permissions
        if (!$this->rbac->hasPrivilege('permission_enquiry', 'can_view')) {
            access_denied();
        }

        // Récupérer les détails de la demande
        $enquiry = $this->enquiry_model->get($id);

        if (empty($enquiry)) {
            show_404();
        }

        // Vérifier que le statut est "approve" ou "completed"
        if (!in_array($enquiry['status'], ['approve', 'completed'])) {
            $this->session->set_flashdata('msg', '<div class="alert alert-warning">Le document d\'acceptation n\'est disponible que pour les demandes approuvées ou terminées.</div>');
            redirect('admin/enquiry');
        }

        // Préparer les données pour la vue
        $data = array(
            'enquiry' => $enquiry,
            'school_name' => $this->setting_model->getSchoolName(),
            'school_address' => $this->setting_model->getSchoolAddress(),
            'school_phone' => $this->setting_model->getSchoolPhone(),
            'school_email' => $this->setting_model->getSchoolEmail(),
            'print_date' => date('d/m/Y H:i:s')
        );

        // Charger la vue d'impression
        $this->load->view('admin/frontoffice/print_permission', $data);
    }


    /**
     * Récupérer les notifications des demandes en attente
     */
    public function get_enquiry_notifications() {
        if (!$this->rbac->hasPrivilege('permission_enquiry', 'can_view')) {
            echo json_encode(array('status' => 'error', 'message' => 'Accès non autorisé'));
            return;
        }

        $staff_id = $this->session->userdata('admin_id');

        // Compter les demandes en attente
        $this->db->where('status', 'pending');
        $this->db->where('is_read', 0);
        $this->db->where('assigned_to', $staff_id);
        $total_unread = $this->db->count_all_results('enquiry');

        // Compter toutes les demandes en attente (y compris lues)
        $this->db->where('status', 'pending');
        $this->db->where('assigned_to', $staff_id);
        $total_pending = $this->db->count_all_results('enquiry');

        // Récupérer les dernières demandes non lues
        $this->db->select('id, name, contact, source, date, status, is_read');
        $this->db->where('status', 'pending');
        $this->db->where('assigned_to', $staff_id);
        $this->db->order_by('date', 'DESC');
        $this->db->limit(10);
        $query = $this->db->get('enquiry');
        $pending_list = $query->result();

        // Compter par type
        $this->db->where('status', 'pending');
        $this->db->where('assigned_to', $staff_id);
        $this->db->where('source', 'permission');
        $permission_count = $this->db->count_all_results('enquiry');

        $this->db->where('status', 'pending');
        $this->db->where('assigned_to', $staff_id);
        $this->db->where('source', 'demission');
        $demission_count = $this->db->count_all_results('enquiry');

        // Compter les notifications pour l'historique (7 derniers jours)
        $this->db->where('assigned_to', $staff_id);
        $this->db->where('date >=', date('Y-m-d', strtotime('-7 days')));
        $history_count = $this->db->count_all_results('enquiry');

        $response = array(
            'status' => 'success',
            'total_unread' => $total_unread,
            'total_pending' => $total_pending,
            'permission_count' => $permission_count,
            'demission_count' => $demission_count,
            'history_count' => $history_count,
            'list' => $pending_list,
            'html' => $this->load->view('admin/frontoffice/enquiry_notification_list', array('list' => $pending_list, 'unread_count' => $total_unread), true)
        );

        echo json_encode($response);
    }

    /**
     * Marquer une demande comme lue
     */
    public function mark_enquiry_read($id) {
        if (!$this->rbac->hasPrivilege('permission_enquiry', 'can_view')) {
            echo json_encode(array('status' => 'error', 'message' => 'Accès non autorisé'));
            return;
        }

        $staff_id = $this->session->userdata('admin_id');

        $this->db->where('id', $id);
        $this->db->where('assigned_to', $staff_id);
        $this->db->update('enquiry', array('is_read' => 1, 'read_at' => date('Y-m-d H:i:s')));

        // Récupérer le nombre restant de notifications non lues
        $this->db->where('status', 'pending');
        $this->db->where('is_read', 0);
        $this->db->where('assigned_to', $staff_id);
        $remaining = $this->db->count_all_results('enquiry');

        echo json_encode(array(
            'status' => 'success',
            'message' => 'Demande marquée comme lue',
            'remaining' => $remaining
        ));
    }

    /**
     * Marquer toutes les demandes comme lues
     */
    public function mark_all_enquiry_read() {
        if (!$this->rbac->hasPrivilege('permission_enquiry', 'can_view')) {
            echo json_encode(array('status' => 'error', 'message' => 'Accès non autorisé'));
            return;
        }

        $staff_id = $this->session->userdata('admin_id');

        $this->db->where('status', 'pending');
        $this->db->where('is_read', 0);
        $this->db->where('assigned_to', $staff_id);
        $this->db->update('enquiry', array('is_read' => 1, 'read_at' => date('Y-m-d H:i:s')));

        $affected = $this->db->affected_rows();

        echo json_encode(array(
            'status' => 'success',
            'message' => $affected . ' demandes marquées comme lues',
            'remaining' => 0
        ));
    }

    /**
     * Récupérer l'historique des notifications
     */
    public function get_notification_history() {
        if (!$this->rbac->hasPrivilege('permission_enquiry', 'can_view')) {
            echo json_encode(array('status' => 'error', 'message' => 'Accès non autorisé'));
            return;
        }

        $staff_id = $this->session->userdata('admin_id');

        // Récupérer les 10 dernières demandes traitées (7 derniers jours)
        $this->db->select('id, name, contact, source, date, status, is_read, read_at');
        $this->db->where('assigned_to', $staff_id);
        $this->db->where('date >=', date('Y-m-d', strtotime('-7 days')));
        $this->db->where('status !=', 'pending');
        $this->db->order_by('date', 'DESC');
        $this->db->limit(10);
        $query = $this->db->get('enquiry');
        $history_list = $query->result();

        $html = $this->load->view('admin/frontoffice/notification_history', array('list' => $history_list), true);

        echo json_encode(array('status' => 'success', 'html' => $html));
    }
}