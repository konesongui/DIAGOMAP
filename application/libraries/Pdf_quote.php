<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'third_party/tcpdf/tcpdf.php';

class Pdf_quote extends TCPDF {
    private $CI;
    private $company_info;

    public function __construct() {
        parent::__construct(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        $this->CI =& get_instance();
        $this->CI->load->model('setting_model');
        
        // Récupération des informations de l'entreprise
        $this->company_info = $this->CI->setting_model->get()[0];
        
        // Configuration du document
        $this->SetCreator(PDF_CREATOR);
        $this->SetAuthor($this->company_info['name']);
        $this->SetTitle('Devis');
        
        // Configuration des marges
        $this->SetMargins(15, 15, 15);
        $this->SetHeaderMargin(5);
        $this->SetFooterMargin(10);
        
        // Configuration de l'en-tête et du pied de page
        $this->setPrintHeader(true);
        $this->setPrintFooter(true);
        
        // Police par défaut
        $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
        // Configuration de l'espacement automatique
        $this->SetAutoPageBreak(TRUE, 25);
        
        // Échelle de l'image
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        // Police
        $this->SetFont('dejavusans', '', 10);
    }

    public function Header() {
        if ($this->company_info['admin_logo'] != '') {
            $logo_path = base_url() . 'uploads/company/' . $this->company_info['admin_logo'];
            $this->Image($logo_path, 15, 10, 50, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }
        
        $this->SetFont('dejavusans', 'B', 15);
        $this->Cell(0, 10, 'DEVIS', 0, 1, 'R');
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('dejavusans', 'I', 8);
        
        // Informations de l'entreprise
        $footer_text = $this->company_info['name'];
        if (!empty($this->company_info['address'])) {
            $footer_text .= ' - ' . $this->company_info['address'];
        }
        if (!empty($this->company_info['phone'])) {
            $footer_text .= ' - Tél: ' . $this->company_info['phone'];
        }
        if (!empty($this->company_info['email'])) {
            $footer_text .= ' - Email: ' . $this->company_info['email'];
        }
        
        $this->Cell(0, 10, $footer_text, 0, false, 'C', 0, '', 0, false, 'T', 'M');
        $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }

    public function generateQuote($quote_data) {
        // Ajout d'une nouvelle page
        $this->AddPage();
        
        // Information du client
        $this->SetFont('dejavusans', 'B', 12);
        $this->Cell(0, 10, 'DESTINATAIRE', 0, 1, 'L');
        $this->SetFont('dejavusans', '', 10);
        $this->Cell(0, 6, $quote_data['customer_name'], 0, 1, 'L');
        
        // Informations du devis
        $this->Ln(10);
        $this->SetFont('dejavusans', 'B', 10);
        $this->Cell(60, 6, 'N° de devis:', 0, 0, 'L');
        $this->SetFont('dejavusans', '', 10);
        $this->Cell(0, 6, $quote_data['quote_number'], 0, 1, 'L');
        
        $this->SetFont('dejavusans', 'B', 10);
        $this->Cell(60, 6, 'Date d\'émission:', 0, 0, 'L');
        $this->SetFont('dejavusans', '', 10);
        $this->Cell(0, 6, date('d/m/Y', strtotime($quote_data['issue_date'])), 0, 1, 'L');
        
        if (!empty($quote_data['valid_until'])) {
            $this->SetFont('dejavusans', 'B', 10);
            $this->Cell(60, 6, 'Date de validité:', 0, 0, 'L');
            $this->SetFont('dejavusans', '', 10);
            $this->Cell(0, 6, date('d/m/Y', strtotime($quote_data['valid_until'])), 0, 1, 'L');
        }
        
        // Tableau des articles
        $this->Ln(10);
        $this->SetFont('dejavusans', 'B', 10);
        
        // En-têtes du tableau
        $this->SetFillColor(240, 240, 240);
        $this->Cell(80, 7, 'Description', 1, 0, 'C', 1);
        $this->Cell(25, 7, 'Quantité', 1, 0, 'C', 1);
        $this->Cell(25, 7, 'Unité', 1, 0, 'C', 1);
        $this->Cell(30, 7, 'Prix unit.', 1, 0, 'C', 1);
        $this->Cell(30, 7, 'Total HT', 1, 1, 'C', 1);
        
        // Contenu du tableau
        $this->SetFont('dejavusans', '', 10);
        foreach ($quote_data['items'] as $item) {
            $description = $item['description'] ?: $item['item_name'];
            
            $this->MultiCell(80, 6, $description, 1, 'L', 0, 0);
            $this->Cell(25, 6, number_format($item['quantity'], 2, ',', ' '), 1, 0, 'R');
            $this->Cell(25, 6, $item['unit'], 1, 0, 'C');
            $this->Cell(30, 6, number_format($item['price'], 2, ',', ' '), 1, 0, 'R');
            $this->Cell(30, 6, number_format($item['line_total'], 2, ',', ' '), 1, 1, 'R');
        }
        
        // Totaux
        $this->Ln(5);
        $this->SetFont('dejavusans', 'B', 10);
        
        $this->Cell(160, 6, 'Total HT:', 0, 0, 'R');
        $this->Cell(30, 6, number_format($quote_data['total_ht'], 2, ',', ' ') . ' €', 0, 1, 'R');
        
        if ($quote_data['apply_tva']) {
            $this->Cell(160, 6, 'TVA ' . number_format($quote_data['tva_rate'], 2, ',', ' ') . '%:', 0, 0, 'R');
            $this->Cell(30, 6, number_format($quote_data['tva_amount'], 2, ',', ' ') . ' €', 0, 1, 'R');
        }
        
        $this->SetFont('dejavusans', 'B', 12);
        $this->Cell(160, 8, 'Total TTC:', 0, 0, 'R');
        $this->Cell(30, 8, number_format($quote_data['total_ttc'], 2, ',', ' ') . ' €', 0, 1, 'R');
        
        // Notes
        if (!empty($quote_data['notes'])) {
            $this->Ln(10);
            $this->SetFont('dejavusans', 'B', 10);
            $this->Cell(0, 6, 'Notes:', 0, 1, 'L');
            $this->SetFont('dejavusans', '', 10);
            $this->MultiCell(0, 6, $quote_data['notes'], 0, 'L');
        }
        
        // Conditions générales
        $this->Ln(10);
        $this->SetFont('dejavusans', '', 8);
        $this->MultiCell(0, 4, 
            "CONDITIONS GÉNÉRALES:\n" .
            "1. Ce devis est valable " . ($quote_data['valid_until'] ? "jusqu'au " . date('d/m/Y', strtotime($quote_data['valid_until'])) : "30 jours à compter de sa date d'émission") . ".\n" .
            "2. Le paiement est dû à réception de la facture.\n" .
            "3. Les prix indiqués sont en euros et " . ($quote_data['apply_tva'] ? "soumis à la TVA au taux en vigueur." : "non soumis à la TVA."),
            0, 'L');
    }
} 