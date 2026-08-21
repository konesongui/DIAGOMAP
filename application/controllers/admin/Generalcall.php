<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Generalcall extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model("general_call_model");
    }

    // ========================================== //
    // INDEX - LISTE DES APPELS                   //
    // ========================================== //
    public function index() {
        if (!$this->rbac->hasPrivilege('journal_appels', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'receptioniste');
        $this->session->set_userdata('sub_menu', 'admin/generalcall');

        $this->form_validation->set_rules('call_type', $this->lang->line('call_type'), 'required');
        $this->form_validation->set_rules('contact', $this->lang->line('contact'), 'required');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');

        if ($this->form_validation->run() == FALSE) {
            $data['call_list'] = $this->general_call_model->call_list();
            $this->load->view('layout/header');
            $this->load->view('admin/frontoffice/generalcallview', $data);
            $this->load->view('layout/footer');
        } else {
            $calls = array(
                'name' => $this->input->post('name'),
                'contact' => $this->input->post('contact'),
                'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'description' => $this->input->post('description'),
                'call_dureation' => $this->input->post('call_dureation'),
                'note' => $this->input->post('note'),
                'call_type' => $this->input->post('call_type')
            );

            if ($_POST['follow_up_date'] != '') {
                $calls['follow_up_date'] = date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('follow_up_date')));
            }

            $this->general_call_model->add($calls);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/generalcall');
        }
    }

    // ========================================== //
    // ÉDITION D'UN APPEL (PAGE SÉPARÉE)          //
    // ========================================== //

    function edit($id) {
        if (!$this->rbac->hasPrivilege('journal_appels', 'can_edit')) {
            access_denied();
        }

        $this->form_validation->set_rules('call_type', $this->lang->line('call_type'), 'required');
        $this->form_validation->set_rules('contact', $this->lang->line('contact'), 'required');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');

        if ($this->form_validation->run() == FALSE) {
            $data['call_list'] = $this->general_call_model->call_list();
            $data['call_data'] = $this->general_call_model->call_list($id);
            $this->load->view('layout/header');
            $this->load->view('admin/frontoffice/generalcalleditview', $data);
            $this->load->view('layout/footer');
        } else {
            $calls_update = array(
                'name' => $this->input->post('name'),
                'contact' => $this->input->post('contact'),
                'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
                'description' => $this->input->post('description'),
                'follow_up_date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('follow_up_date'))),
                'call_dureation' => $this->input->post('call_dureation'),
                'note' => $this->input->post('note'),
                'call_type' => $this->input->post('call_type')
            );

            $this->general_call_model->call_update($id, $calls_update);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('success_message') . '</div>');
            redirect('admin/generalcall');
        }
    }



    // ========================================== //
    // GET CALL DATA (AJAX pour modal)            //
    // ========================================== //
    public function get_call_data($id) {
        // Désactiver le débogage
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('journal_appels', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        if (!$id || !is_numeric($id)) {
            echo json_encode(['success' => false, 'message' => 'ID invalide']);
            return;
        }

        try {
            $data = $this->general_call_model->call_list($id);

            if ($data && !empty($data)) {
                $call = [
                    'id' => (int)$data['id'],
                    'call_type' => (string)($data['call_type'] ?? ''),
                    'name' => (string)($data['name'] ?? ''),
                    'contact' => (string)($data['contact'] ?? ''),
                    'date' => (string)($data['date'] ?? ''),
                    'description' => (string)($data['description'] ?? ''),
                    'call_dureation' => (string)($data['call_dureation'] ?? ''),
                    'note' => (string)($data['note'] ?? ''),

                    'follow_up_date' => (string)($data['follow_up_date'] ?? '')
                ];

                echo json_encode([
                    'success' => true,
                    'call' => $call
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Appel non trouvé']);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ]);
        }
    }

    // ========================================== //
    // UPDATE VIA AJAX                            //
    // ========================================== //
    public function update_ajax() {
        error_reporting(0);
        ini_set('display_errors', 0);

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/json');

        if (!$this->rbac->hasPrivilege('journal_appels', 'can_edit')) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $id = $this->input->post('edit_id');

        $this->form_validation->set_rules('call_type', $this->lang->line('call_type'), 'required');
        $this->form_validation->set_rules('contact', $this->lang->line('contact'), 'required');
        $this->form_validation->set_rules('date', $this->lang->line('date'), 'required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            return;
        }

        $calls_update = array(
            'name' => $this->input->post('name'),
            'contact' => $this->input->post('contact'),
            'date' => date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('date'))),
            'description' => $this->input->post('description'),
            'call_dureation' => $this->input->post('call_dureation'),
            'note' => $this->input->post('note'),
            'call_type' => $this->input->post('call_type')
        );

        if ($this->input->post('follow_up_date') != '') {
            $calls_update['follow_up_date'] = date('Y-m-d', $this->customlib->datetostrtotime($this->input->post('follow_up_date')));
        }

        $result = $this->general_call_model->call_update($id, $calls_update);

        if ($result !== false) {
            echo json_encode(['success' => true, 'message' => 'Appel mis à jour avec succès']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la mise à jour']);
        }
    }

    // ========================================== //
    // DETAILS D'UN APPEL (MODAL)                 //
    // ========================================== //
    function details($id) {
        if (!$this->rbac->hasPrivilege('journal_appels', 'can_view')) {
            access_denied();
        }

        $data['call_data'] = $this->general_call_model->call_list($id);
        $this->load->view('admin/frontoffice/generalmodelview', $data);
    }

    // ========================================== //
    // SUPPRESSION D'UN APPEL                     //
    // ========================================== //
    function delete($id) {
        if (!$this->rbac->hasPrivilege('journal_appels', 'can_delete')) {
            access_denied();
        }
        $this->general_call_model->delete($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">' . $this->lang->line('delete_message') . '</div>');
        redirect('admin/generalcall');
    }

    // ========================================== //
    // EXPORT EXCEL (CSV)                         //
    // ========================================== //
    public function export_excel() {
        // Récupérer les filtres
        $call_type = $this->input->get('call_type');
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        // Récupérer les données
        $data = $this->general_call_model->get_filtered_calls($call_type, $date_from, $date_to);

        // Nom du fichier
        $filename = 'appels_' . date('Y-m-d') . '.csv';

        // En-têtes HTTP
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: public');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF"); // BOM pour UTF-8

        // En-têtes du tableau
        fputcsv($output, ['Type', 'Nom', 'Téléphone', 'Date', 'Durée', 'Description', 'Note', 'Date suivi']);

        // Données
        foreach ($data as $call) {
            $call_type_label = '';
            if ($call['call_type'] == 1) $call_type_label = 'Entrant';
            elseif ($call['call_type'] == 2) $call_type_label = 'Sortant';
            elseif ($call['call_type'] == 3) $call_type_label = 'Manqué';

            fputcsv($output, [
                $call_type_label,
                $call['name'] ?? '',
                $call['contact'] ?? '',
                !empty($call['date']) ? date('d/m/Y', strtotime($call['date'])) : '',
                $call['call_dureation'] ?? '',
                $call['description'] ?? '',
                $call['note'] ?? '',
                !empty($call['follow_up_date']) ? date('d/m/Y', strtotime($call['follow_up_date'])) : ''
            ]);
        }

        fclose($output);
        exit;
    }

    // ========================================== //
    // EXPORT PDF                                 //
    // ========================================== //
    public function export_pdf() {
        $call_type = $this->input->get('call_type');
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');

        $data['calls'] = $this->general_call_model->get_filtered_calls($call_type, $date_from, $date_to);
        $data['title'] = 'Liste des appels';
        $data['date_generated'] = date('d/m/Y H:i');

        $html = $this->load->view('admin/frontoffice/call_pdf_export', $data, true);

        if (class_exists('Dompdf\Dompdf')) {
            $this->load->library('pdf');
            $this->pdf->loadHtml($html);
            $this->pdf->setPaper('A4', 'landscape');
            $this->pdf->render();
            $this->pdf->stream('appels_' . date('Y-m-d') . '.pdf', array("Attachment" => 1));
        } else {
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->WriteHTML($html);
            $this->m_pdf->pdf->Output('appels_' . date('Y-m-d') . '.pdf', 'D');
        }
        exit;
    }

    // ========================================== //
    // DATATABLE AJAX (pour DataTable)            //
    // ========================================== //
    public function getcalllist() {
        $callList = $this->general_call_model->getcalllist();
        $m = json_decode($callList);
        $currency_symbol = $this->customlib->getSchoolCurrencyFormat();
        $dt_data = array();

        if (!empty($m->data)) {
            foreach ($m->data as $key => $value) {
                $editbtn = '';
                $deletebtn = '';
                $viewbtn = '';

                $viewbtn = "<a data-placement='center' 
                           onclick='getRecord(" . $value->id . ")' 
                           class='btn btn-default btn-xs' 
                           data-target='#calldetails' 
                           data-toggle='modal' 
                           title='" . $this->lang->line('view') . "'>
                              <i class='fa fa-reorder'></i>
                           </a>";

                if ($this->rbac->hasPrivilege('phone_call_log', 'can_edit')) {
                    $editbtn = "<a href='" . base_url() . "admin/generalcall/edit/" . $value->id . "' 
                              class='btn btn-default btn-xs' 
                              data-toggle='tooltip' 
                              data-placement='center' 
                              title='" . $this->lang->line('edit') . "'>
                                 <i class='fa fa-pencil'></i>
                           </a>";
                }

                if ($this->rbac->hasPrivilege('phone_call_log', 'can_delete')) {
                    $deletebtn = "<a onclick='return confirm(\"" . $this->lang->line('delete_confirm') . "\')' 
                                 href='" . base_url() . "admin/generalcall/delete/" . $value->id . "' 
                                 class='btn btn-default btn-xs' 
                                 data-placement='center' 
                                 title='" . $this->lang->line('delete') . "' 
                                 data-toggle='tooltip'>
                                    <i class='fa fa-trash'></i>
                              </a>";
                }

                $follow_up_date = '';
                if (!empty($value->follow_up_date) && $value->follow_up_date != '0000-00-00') {
                    $follow_up_date = date(
                        $this->customlib->getSchoolDateFormat(),
                        $this->customlib->dateyyyymmddTodateformat($value->follow_up_date)
                    );
                }

                $call_type_html = '';
                if ($value->call_type == 1) {
                    $call_type_html = "<span class='label label-warning' style='background-color: #ff9801 !important; border-radius: 4px; padding: 4px 12px;'>
                                        <i class='fa fa-arrow-circle-down'></i> Entrant
                                      </span>";
                } elseif ($value->call_type == 2) {
                    $call_type_html = "<span class='label label-success' style='background-color: #66aa18 !important; border-radius: 4px; padding: 4px 12px;'>
                                        <i class='fa fa-arrow-circle-up'></i> Sortant
                                      </span>";
                } else {
                    $call_type_html = "<span class='label label-danger' style='background-color: #dc3545 !important; border-radius: 4px; padding: 4px 12px;'>
                                        <i class='fa fa-phone'></i> Manqué
                                      </span>";
                }

                $row = array();
                $row[] = $call_type_html;
                $row[] = $value->name;
                $row[] = $value->contact;
                $row[] = date(
                    $this->customlib->getSchoolDateFormat(),
                    $this->customlib->dateyyyymmddTodateformat($value->date)
                );
                $row[] = $follow_up_date;
                $row[] = $value->note;
                $row[] = $value->description;
                $row[] = $editbtn . ' ' . $deletebtn;

                $dt_data[] = $row;
            }
        }

        $json_data = array(
            "draw" => intval($m->draw),
            "recordsTotal" => intval($m->recordsTotal),
            "recordsFiltered" => intval($m->recordsFiltered),
            "data" => $dt_data,
        );

        echo json_encode($json_data);
    }

    // ========================================== //
    // VALIDATION PERSONNALISÉE                   //
    // ========================================== //
    public function check_default($post_string) {
        return $post_string == '' ? FALSE : TRUE;
    }
}