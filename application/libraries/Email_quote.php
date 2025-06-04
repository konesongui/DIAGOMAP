<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Email_quote {
    private $CI;
    private $company_info;

    public function __construct() {
        $this->CI =& get_instance();
        
        // Chargement des dépendances
        $this->CI->load->library('email');
        $this->CI->load->model('setting_model');
        
        // Récupération des informations de l'entreprise
        $this->company_info = $this->CI->setting_model->get()[0];
        
        // Configuration de l'email
        $config = array(
            'protocol' => 'smtp',
            'smtp_host' => $this->company_info['smtp_host'],
            'smtp_port' => $this->company_info['smtp_port'],
            'smtp_user' => $this->company_info['smtp_user'],
            'smtp_pass' => $this->company_info['smtp_pass'],
            'mailtype' => 'html',
            'charset' => 'utf-8',
            'wordwrap' => TRUE
        );
        
        $this->CI->email->initialize($config);
    }

    /**
     * Envoie le devis par email au client
     * 
     * @param array $quote_data Les données du devis
     * @param string $pdf_path Le chemin du fichier PDF à joindre
     * @return bool True si l'email a été envoyé avec succès, False sinon
     */
    public function sendQuote($quote_data, $pdf_path) {
        try {
            // Configuration de l'email
            $this->CI->email->from($this->company_info['email'], $this->company_info['name']);
            $this->CI->email->to($quote_data['customer_email']);
            $this->CI->email->subject('Devis ' . $quote_data['quote_number']);
            
            // Construction du corps de l'email
            $message = $this->buildEmailBody($quote_data);
            $this->CI->email->message($message);
            
            // Ajout de la pièce jointe
            $this->CI->email->attach($pdf_path);
            
            // Envoi de l'email
            return $this->CI->email->send();
            
        } catch (Exception $e) {
            log_message('error', 'Email Quote Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Construit le corps de l'email
     * 
     * @param array $quote_data Les données du devis
     * @return string Le corps de l'email en HTML
     */
    private function buildEmailBody($quote_data) {
        $message = '
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { margin-bottom: 30px; }
                .footer { margin-top: 30px; font-size: 12px; color: #666; }
                .quote-details { margin: 20px 0; }
                .amount { font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2>Bonjour,</h2>
                    <p>Veuillez trouver ci-joint notre devis ' . $quote_data['quote_number'] . '.</p>
                </div>
                
                <div class="quote-details">
                    <p>Montant total HT : <span class="amount">' . number_format($quote_data['total_ht'], 2, ',', ' ') . ' €</span></p>';
        
        if ($quote_data['apply_tva']) {
            $message .= '<p>TVA (' . number_format($quote_data['tva_rate'], 2, ',', ' ') . '%) : <span class="amount">' . 
                number_format($quote_data['tva_amount'], 2, ',', ' ') . ' €</span></p>';
        }
        
        $message .= '<p>Montant total TTC : <span class="amount">' . number_format($quote_data['total_ttc'], 2, ',', ' ') . ' €</span></p>';
        
        if ($quote_data['valid_until']) {
            $message .= '<p>Ce devis est valable jusqu\'au ' . date('d/m/Y', strtotime($quote_data['valid_until'])) . '.</p>';
        }
        
        $message .= '
                </div>
                
                <p>Pour toute question concernant ce devis, n\'hésitez pas à nous contacter.</p>
                
                <div class="footer">
                    <p>Cordialement,<br>' . $this->company_info['name'] . '</p>';
        
        if (!empty($this->company_info['phone'])) {
            $message .= '<p>Tél: ' . $this->company_info['phone'] . '</p>';
        }
        
        $message .= '
                </div>
            </div>
        </body>
        </html>';
        
        return $message;
    }
} 