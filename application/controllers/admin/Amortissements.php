<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Amortissements extends Admin_Controller {   

    function __construct() {
        parent::__construct();
        $this->load->model("amortissements_model");
        $this->load->model("immobilisations_model");
    }

    // ========================================== //
    // INDEX - LISTE DES AMORTISSEMENTS           //
    // ========================================== //
    public function index() {
        if (!$this->rbac->hasPrivilege('amortissements', 'can_view')) {
            access_denied();
        }

        $this->session->set_userdata('top_menu', 'finance');
        $this->session->set_userdata('sub_menu', 'admin/amortissements');

        $data['amortissements'] = $this->amortissements_model->get_all();
        $data['stats'] = $this->amortissements_model->get_stats();
        $data['categories'] = $this->immobilisations_model->get_categories();
        $data['statuses'] = ['actif', 'amorti', 'ceder', 'sortie'];

        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/amortissements', $data);
        $this->load->view('layout/footer');
    }

    // ========================================== //
    // DÉTAILS D'UN AMORTISSEMENT                 //
    // ========================================== //
    public function details($id) {
        if (!$this->rbac->hasPrivilege('amortissements', 'can_view')) {
            access_denied();
        }

        $data['amortissement'] = $this->amortissements_model->get_all($id);
        $this->load->view('admin/frontoffice/amortissements_details', $data);
    }

    // ========================================== //
    // EXPORT EXCEL                               //
    // ========================================== //
    public function export_excel() {
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');
        $categorie = $this->input->get('categorie');

        $data = $this->amortissements_model->export_csv($date_from, $date_to, $categorie);

        $filename = 'amortissements_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: public');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, [
            'Immobilisation', 'Code', 'Catégorie', 'Période début', 'Période fin',
            'Montant', 'Type', 'Description'
        ]);

        foreach ($data as $item) {
            fputcsv($output, [
                $item['immobilisation_nom'] ?? '',
                $item['immobilisation_code'] ?? '',
                $item['categorie'] ?? '',
                !empty($item['periode_debut']) ? date('d/m/Y', strtotime($item['periode_debut'])) : '',
                !empty($item['periode_fin']) ? date('d/m/Y', strtotime($item['periode_fin'])) : '',
                $item['montant'] ?? 0,
                $item['type'] ?? '',
                $item['description'] ?? ''
            ]);
        }

        fclose($output);
        exit;
    }

    // ========================================== //
    // EXPORT PDF                                 //
    // ========================================== //
    public function export_pdf() {
        $date_from = $this->input->get('date_from');
        $date_to = $this->input->get('date_to');
        $categorie = $this->input->get('categorie');

        $data['amortissements'] = $this->amortissements_model->export_csv($date_from, $date_to, $categorie);
        $data['stats'] = $this->amortissements_model->get_stats();
        $data['title'] = 'Rapport des amortissements';
        $data['date_generated'] = date('d/m/Y H:i');

        $html = $this->load->view('admin/frontoffice/amortissements_pdf_export', $data, true);

        if (class_exists('Dompdf\Dompdf')) {
            $this->load->library('pdf');
            $this->pdf->loadHtml($html);
            $this->pdf->setPaper('A4', 'landscape');
            $this->pdf->render();
            $this->pdf->stream('amortissements_' . date('Y-m-d') . '.pdf', array("Attachment" => 1));
        } else {
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->WriteHTML($html);
            $this->m_pdf->pdf->Output('amortissements_' . date('Y-m-d') . '.pdf', 'D');
        }
        exit;
    }

    // ========================================== //
    // PLAN D'AMORTISSEMENT PAR IMMOBILISATION    //
    // ========================================== //
    public function plan($id) {
        if (!$this->rbac->hasPrivilege('amortissements', 'can_view')) {
            access_denied();
        }

        $data['plan'] = $this->amortissements_model->get_plan_amortissement($id);
        $data['immobilisation'] = $this->immobilisations_model->get_by_id($id);
        $this->load->view('admin/frontoffice/amortissements_plan', $data);
    }
}