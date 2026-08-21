<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Bibliothèque pour l'API FNE (Facture Normalisée Électronique)
 */
class Fne_api
{
    protected $CI;
    protected $config = [];

    public function __construct($config = [])
    {
        $this->CI =& get_instance();

        // Configuration par défaut
        $this->config = [
            'test_url' => 'http://54.247.95.108/ws/external/invoices/sign',
            'prod_url' => '',
            'api_key' => 'toCgyP5vdqXavkY16dg5qn7eae3N8bjZ',
            'timeout' => 30,
            'environment' => 'test',
            'point_of_sale' => 'PDV001',
            'establishment' => 'ETABLISSEMENT001'
        ];

        // Fusion avec la configuration passée
        if (!empty($config)) {
            $this->config = array_merge($this->config, $config);
        }

        // Charger la configuration depuis la BDD si disponible
        $this->load_db_config();
    }

    /**
     * Charger la configuration depuis la base de données
     */
    protected function load_db_config()
    {
        if ($this->CI->db->table_exists('fne_settings')) {
            $settings = $this->CI->db->get('fne_settings')->row_array();
            if ($settings) {
                foreach ($settings as $key => $value) {
                    if (isset($this->config[$key])) {
                        $this->config[$key] = $value;
                    }
                }
            }
        }
    }

    /**
     * Certifier une facture
     */
    public function certify_invoice($invoice_data)
    {
        // Valider les données
        $validation = $this->validate_data($invoice_data);
        if (!$validation['valid']) {
            return [
                'status' => 'error',
                'message' => 'Données invalides',
                'errors' => $validation['errors']
            ];
        }

        // Préparer les données pour l'API
        $payload = $this->prepare_payload($invoice_data);

        // Appeler l'API
        return $this->call_api($payload);
    }

    /**
     * Préparer le payload pour l'API
     */
    protected function prepare_payload($invoice)
    {
        // Déterminer le template
        $template = 'B2C';
        if (!empty($invoice['customer_ncc'])) {
            $template = 'B2B';
        } elseif (!empty($invoice['customer_country']) && $invoice['customer_country'] != 'CI') {
            $template = 'B2F';
        }

        // Méthode de paiement
        $paymentMethod = $this->map_payment_method($invoice['method'] ?? 'cash');

        // Code TVA
        $taxCode = $this->get_tax_code($invoice);

        // Préparer les articles
        $items = [];
        foreach ($invoice['items'] as $item) {
            $items[] = [
                'taxes' => [$taxCode],
                'reference' => $item['item_reference'] ?? 'REF-' . str_pad($item['item_id'], 5, '0', STR_PAD_LEFT),
                'description' => $item['item_name'],
                'quantity' => (float) $item['quantity'],
                'amount' => (float) $item['unit_price'],
                'discount' => 0,
                'measurementUnit' => $this->get_measurement_unit($item['unit'] ?? 'pcs'),
            ];
        }

        // Nom du client
        $clientName = trim($invoice['customer_name'] . ' ' . ($invoice['customer_last_name'] ?? ''));
        if (empty($clientName)) {
            $clientName = 'Client';
        }

        $payload = [
            'invoiceType' => 'sale',
            'paymentMethod' => $paymentMethod,
            'template' => $template,
            'isRne' => false,
            'clientCompanyName' => $clientName,
            'clientPhone' => $invoice['customer_phone'] ?? '',
            'clientEmail' => $invoice['customer_email'] ?? '',
            'pointOfSale' => $this->config['point_of_sale'],
            'establishment' => $this->config['establishment'],
            'commercialMessage' => 'Merci pour votre confiance',
            'footer' => 'Service client',
            'foreignCurrency' => '',
            'foreignCurrencyRate' => 0,
            'items' => $items,
            'discount' => 0,
        ];

        // Ajouter NCC si B2B
        if ($template === 'B2B' && !empty($invoice['customer_ncc'])) {
            $payload['clientNcc'] = $invoice['customer_ncc'];
        }

        return $payload;
    }

    /**
     * Mapper la méthode de paiement
     */
    protected function map_payment_method($method)
    {
        $method = strtolower(trim($method));

        $mapping = [
            'cash' => 'cash',
            'espèce' => 'cash',
            'espèces' => 'cash',
            'card' => 'card',
            'carte' => 'card',
            'check' => 'check',
            'chèque' => 'check',
            'mobile-money' => 'mobile-money',
            'mobile' => 'mobile-money',
            'momo' => 'mobile-money',
            'transfer' => 'transfer',
            'virement' => 'transfer',
            'deferred' => 'deferred',
            'terme' => 'deferred',
            'credit' => 'deferred',
        ];

        return $mapping[$method] ?? 'cash';
    }

    /**
     * Obtenir le code TVA
     */
    protected function get_tax_code($invoice)
    {
        if (!$invoice['apply_tva'] || $invoice['tva_rate'] <= 0) {
            return 'TVAE';
        }

        $rate = (float) $invoice['tva_rate'];

        if ($rate == 18) return 'TVA';
        if ($rate == 9) return 'TVAB';
        if ($rate == 0) return 'TVAC';

        return 'TVA';
    }

    /**
     * Obtenir l'unité de mesure
     */
    protected function get_measurement_unit($unit)
    {
        $unit = strtolower(trim($unit));

        $units = [
            'pcs' => 'pcs',
            'piece' => 'pcs',
            'pièce' => 'pcs',
            'kg' => 'kg',
            'kilogramme' => 'kg',
            'l' => 'l',
            'litre' => 'l',
            'm' => 'm',
            'metre' => 'm',
            'm2' => 'm2',
            'm3' => 'm3',
            'heure' => 'heure',
            'h' => 'heure',
            'jour' => 'jour',
            'j' => 'jour',
            'mois' => 'mois',
            'service' => 'service',
        ];

        return $units[$unit] ?? 'pcs';
    }

    /**
     * Valider les données
     */
    protected function validate_data($data)
    {
        $errors = [];

        // Champs requis
        $required = ['customer_name', 'method', 'items'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                $errors[] = "Le champ $field est requis";
            }
        }

        // Valider les articles
        if (!empty($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $i => $item) {
                if (empty($item['item_name'])) {
                    $errors[] = "Article $i: nom requis";
                }
                if (empty($item['quantity']) || $item['quantity'] <= 0) {
                    $errors[] = "Article $i: quantité doit être > 0";
                }
                if (empty($item['unit_price']) || $item['unit_price'] <= 0) {
                    $errors[] = "Article $i: prix doit être > 0";
                }
            }
        } else {
            $errors[] = "Au moins un article est requis";
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Appeler l'API
     */
    protected function call_api($payload)
    {
        if (!function_exists('curl_version')) {
            return [
                'status' => 'error',
                'message' => 'cURL n\'est pas installé'
            ];
        }

        $url = $this->config['environment'] === 'prod' && !empty($this->config['prod_url'])
            ? $this->config['prod_url']
            : $this->config['test_url'];

        $ch = curl_init();

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Bearer ' . $this->config['api_key'],
        ];

        $json_data = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->config['timeout'],
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $json_data,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);

        curl_close($ch);

        // Journaliser
        $this->log_api_call($payload, $response, $http_code, $curl_error);

        if ($curl_error) {
            return [
                'status' => 'error',
                'message' => 'Erreur de connexion: ' . $curl_error
            ];
        }

        $result = json_decode($response, true);

        if ($http_code >= 200 && $http_code < 300) {
            return [
                'status' => 'success',
                'data' => $result
            ];
        } else {
            $error_message = "Erreur API ({$http_code})";
            if (isset($result['message'])) {
                $error_message .= ": " . $result['message'];
            }

            return [
                'status' => 'error',
                'message' => $error_message,
                'details' => $result
            ];
        }
    }

    /**
     * Journaliser l'appel API
     */
    protected function log_api_call($request, $response, $http_code, $error)
    {
        if (!$this->CI->db->table_exists('fne_certification_log')) {
            return;
        }

        $log_data = [
            'request_data' => json_encode($request),
            'response_data' => $response,
            'http_code' => $http_code,
            'status' => ($http_code >= 200 && $http_code < 300) ? 'success' : 'error',
            'error_message' => $error,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->CI->db->insert('fne_certification_log', $log_data);
    }
}