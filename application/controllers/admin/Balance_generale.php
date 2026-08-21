<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Balance_generale extends Admin_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model("balance_generale_model");
    }

    // ========================================== //
    // INDEX - AFFICHER LA BALANCE GÉNÉRALE       //
    // ========================================== //
    public function index() {
        $this->session->set_userdata('top_menu', 'finance');
        $this->session->set_userdata('sub_menu', 'admin/frontoffice/balance_generale');

        // Récupérer les paramètres de filtrage
        $date_debut = $this->input->get('date_debut');
        $date_fin = $this->input->get('date_fin');
        $classe = $this->input->get('classe');

        // Dates par défaut : mois en cours
        if (empty($date_debut)) {
            $date_debut = date('Y-m-01');
        }
        if (empty($date_fin)) {
            $date_fin = date('Y-m-t');
        }

        $data['date_debut'] = $date_debut;
        $data['date_fin'] = $date_fin;
        $data['classe_selected'] = $classe;

        $report = $this->balance_generale_model->get_report($date_debut, $date_fin, $classe);
        $data['balance'] = $report['rows'];
        $data['totals'] = $report['totals'];
        $data['total_debit'] = $report['totals']['mouvement_debit'];
        $data['total_credit'] = $report['totals']['mouvement_credit'];
        $data['total_solde_debiteur'] = $report['totals']['cloture_debit'];
        $data['total_solde_crediteur'] = $report['totals']['cloture_credit'];
        $data['total_ouverture_debit'] = $report['totals']['ouverture_debit'];
        $data['total_ouverture_credit'] = $report['totals']['ouverture_credit'];
        $data['classes'] = $this->balance_generale_model->get_classes();

        // Statistiques
        $data['stats'] = array(
            'total_comptes' => $report['stats']['total_comptes'],
            'total_mouvements' => $report['stats']['total_mouvements'],
            'total_ecritures' => $report['stats']['total_ecritures']
        );

        $this->load->view('layout/header');
        $this->load->view('admin/frontoffice/balance_generale', $data);
        $this->load->view('layout/footer');
    }

    // ========================================== //
    // EXPORT EXCEL                               //
    // ========================================== //
    public function export_excel() {
        $date_debut = $this->input->get('date_debut') ?: date('Y-m-01');
        $date_fin = $this->input->get('date_fin') ?: date('Y-m-t');
        $classe = $this->input->get('classe');

        $report = $this->balance_generale_model->get_report($date_debut, $date_fin, $classe);
        $balance = $report['rows'];
        $total_ouverture_debit = $report['totals']['ouverture_debit'];
        $total_ouverture_credit = $report['totals']['ouverture_credit'];
        $total_debit = $report['totals']['mouvement_debit'];
        $total_credit = $report['totals']['mouvement_credit'];
        $total_solde_debiteur = $report['totals']['cloture_debit'];
        $total_solde_crediteur = $report['totals']['cloture_credit'];

        $filename = 'balance_generale_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, [
            'Période du ' . date('d/m/Y', strtotime($date_debut)) . ' au ' . date('d/m/Y', strtotime($date_fin))
        ]);
        fputcsv($output, []);
        fputcsv($output, [
            'Compte', 'Libellé', 'Classe', 'Ouverture Débit', 'Ouverture Crédit', 'Mouvement Débit', 'Mouvement Crédit', 'Clôture Débit', 'Clôture Crédit'
        ]);

        foreach ($balance as $row) {
            fputcsv($output, [
                $row['compte'] ?? '',
                $row['libelle'] ?? '',
                $row['classe'] ?? '',
                number_format($row['solde_ouverture_debit'] ?? 0, 0, ',', ' '),
                number_format($row['solde_ouverture_credit'] ?? 0, 0, ',', ' '),
                number_format($row['mouvement_debit'] ?? 0, 0, ',', ' '),
                number_format($row['mouvement_credit'] ?? 0, 0, ',', ' '),
                number_format($row['solde_cloture_debit'] ?? 0, 0, ',', ' '),
                number_format($row['solde_cloture_credit'] ?? 0, 0, ',', ' ')
            ]);
        }

        fputcsv($output, []);
        fputcsv($output, [
            'TOTAUX', '', '',
            number_format($total_ouverture_debit, 0, ',', ' '),
            number_format($total_ouverture_credit, 0, ',', ' '),
            number_format($total_debit ?? 0, 0, ',', ' '),
            number_format($total_credit ?? 0, 0, ',', ' '),
            number_format($total_solde_debiteur, 0, ',', ' '),
            number_format($total_solde_crediteur, 0, ',', ' ')
        ]);

        fclose($output);
        exit;
    }

    // ========================================== //
    // EXPORT PDF                                 //
    // ========================================== //
    public function export_pdf() {
        $date_debut = $this->input->get('date_debut') ?: date('Y-m-01');
        $date_fin = $this->input->get('date_fin') ?: date('Y-m-t');
        $classe = $this->input->get('classe');

        $data['balance'] = $this->balance_generale_model->get_balance($date_debut, $date_fin, $classe);
        $report = $this->balance_generale_model->get_report($date_debut, $date_fin, $classe);
        $data['balance'] = $report['rows'];
        $data['totals'] = $report['totals'];
        $data['total_debit'] = $report['totals']['mouvement_debit'];
        $data['total_credit'] = $report['totals']['mouvement_credit'];
        $data['total_ouverture_debit'] = $report['totals']['ouverture_debit'];
        $data['total_ouverture_credit'] = $report['totals']['ouverture_credit'];
        $data['date_debut'] = $date_debut;
        $data['date_fin'] = $date_fin;
        $data['title'] = 'Balance générale';
        $data['stats'] = array(
            'total_comptes' => $report['stats']['total_comptes'],
            'total_mouvements' => $report['stats']['total_mouvements'],
            'total_ecritures' => $report['stats']['total_ecritures']
        );
        $data['classe_selected'] = $classe;
        $data['classes'] = $this->balance_generale_model->get_classes();

        $html = $this->load->view('admin/frontoffice/balance_generale_pdf', $data, true);

        if (class_exists('Dompdf\Dompdf')) {
            $this->load->library('pdf');
            $this->pdf->loadHtml($html);
            $this->pdf->setPaper('A4', 'landscape');
            $this->pdf->render();
            $this->pdf->stream('balance_generale_' . date('Y-m-d') . '.pdf', array("Attachment" => 1));
        } else {
            $this->load->library('m_pdf');
            $this->m_pdf->pdf->WriteHTML($html);
            $this->m_pdf->pdf->Output('balance_generale_' . date('Y-m-d') . '.pdf', 'D');
        }
        exit;
    }

    // ========================================== //
    // VÉRIFIER L'ÉQUILIBRE                       //
    // ========================================== //
    public function verifier() {
        $date_debut = $this->input->get('date_debut') ?: date('Y-m-01');
        $date_fin = $this->input->get('date_fin') ?: date('Y-m-t');

        $total_debit = $this->balance_generale_model->get_total_debit($date_debut, $date_fin);
        $total_credit = $this->balance_generale_model->get_total_credit($date_debut, $date_fin);
        $difference = $total_debit - $total_credit;

        $result = array(
            'total_debit' => $total_debit,
            'total_credit' => $total_credit,
            'difference' => $difference,
            'equilibre' => ($difference == 0),
            'message' => ($difference == 0) ? '✅ La balance est équilibrée' : '❌ La balance n\'est pas équilibrée'
        );

        echo json_encode($result);
        exit;
    }
}