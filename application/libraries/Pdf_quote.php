<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'third_party/dompdf/autoload.inc.php';

// use Dompdf\Dompdf;
// use Dompdf\Options;

class Pdf_quote {
    private $CI;
    private $dompdf;

    public function __construct() {
        $this->CI =& get_instance();

        var_dump($this->CI);
        exit;
        
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isRemoteEnabled', true);

        // var_dump($options);
        // exit;
        
        $this->dompdf = new Dompdf($options);
    }

    public function generateQuote($quote_data) {
        // Charger les données nécessaires
        $this->CI->load->model('setting_model');
        $company_info = $this->CI->setting_model->get()[0];
        
        // Préparer les données pour la vue
        $data = array(
            'company' => $company_info,
            'quote' => $quote_data
        );

        var_dump($data);
        exit;
        
        // Charger la vue
        $html = $this->CI->load->view('admin/quote/print', $data, true);
        
        // Configurer et générer le PDF
        $this->dompdf->loadHtml($html);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();
        
        return $this->dompdf->output();
    }
} 