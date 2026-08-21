<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Quote_selling_model extends CI_Model {

    protected $table = 'quotes_selling';
    protected $items_table = 'quote_items_selling';
    protected $stock_table = 'stock';
    protected $current_session;

    // Constantes pour les statuts
    const STATUS_PENDING = 1;    // En attente de validation
    const STATUS_VALIDATED = 2;  // Validée par le client
    const STATUS_REJECTED = 3;   // Rejetée par le client
    const STATUS_IN_PROGRESS = 4;// En cours de traitement
    const STATUS_DELIVERED = 5;  // Livrée
    const STATUS_CANCELLED = 6;  // Annulée

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    /**
     * Ajoute un nouveau devis avec ses articles
     *
     * @param array $data Les données du devis
     * @return int|bool L'ID du devis créé ou false en cas d'erreur
     */
    public function add($data)
    {
        $this->db->trans_start();

        try {
            // Préparation des données de le devis
            $quote_data = [
                'quote_number'=> $this->generateQuoteNumber(),
                'customer_id' => $data['customer_id'],
                'user_name' => $data['user_name'],
                'designation' => $data['designation'],
                'quote_date'  => date('Y-m-d', strtotime($data['quote_date'])),
                'valid_until' => ((isset($data['valid_until']) && !empty($data['valid_until'])) ? date('Y-m-d', strtotime($data['valid_until'])) : null),
                'payment_terms'     => $data['payment_term'],
                'delivery_terms'    => $data['delivery_term'],
                'delivery_location' => $data['delivery_location'],
                'apply_tva'   => isset($data['apply_tva']) ? $data['apply_tva'] : 1,
                'tva_rate'    => isset($data['tva_rate']) ? $data['tva_rate'] : 20.00,
                'tva_amount'  => $data['tva_amount'],
                'total_ht'    => $data['total_ht'],
                'total_ttc'   => $data['total_ttc'],
                'status'      => $data['status'],
                'created_at'  => $data['created_at'],
            ];

            // Insertion du devis
            $this->db->insert($this->table, $quote_data);
            $quote_id = $this->db->insert_id();

            if (!$quote_id) {
                throw new Exception('Erreur lors de la création de le devis');
            }

            // Insertion des articles
            foreach ($data['items'] as $position => $item) {
                $item_data = [
                    'quote_id' => $quote_id,
                    'category_id' => $item['category_id'],
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'unit' => $item['unit'],
                    'line_total' => $item['line_total'],
                    'position' => $position + 1,
                ];

                if (!$this->db->insert($this->items_table, $item_data)) {
                    throw new Exception('Erreur lors de l\'ajout d\'un article à le devis');
                }
            }

            $this->db->trans_complete();
            return $quote_id;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Quote Model Error: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Récupère l'historique des duplications d'un devis
     */
    public function getDuplicateHistory($quoteId)
    {
        return $this->db
            ->where('original_quote_id', $quoteId)
            ->order_by('created_at', 'DESC')
            ->get('quotes_selling')
            ->result_array();
    }

    /**
     * Vérifie si un devis est une duplication
     */
    public function isDuplicate($quoteId)
    {
        $quote = $this->db
            ->select('original_quote_id')
            ->where('id', $quoteId)
            ->get('quotes_selling')
            ->row();

        return !empty($quote->original_quote_id);
    }

    /**
     * Génère un numéro unique pour une commande
     * Format: CMD-YYYYMM-XXXX où XXXX est un numéro séquentiel
     *
     * @return string
     */

    private function generateQuoteNumber()
    {
        $prefix = 'DEV';
        $date = date('Ym');

        $this->db->like('quote_number', $prefix . '-' . $date, 'after');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get($this->table);

        if ($query->num_rows() > 0) {
            $last_ref = $query->row()->quote_number;
            $sequence = intval(substr($last_ref, -4)) + 1;
        } else {
            $sequence = 1;
        }

        $sequence_padded = str_pad($sequence, 4, '0', STR_PAD_LEFT);

        return $prefix . '-' . $date . '-' . $sequence_padded;
    }

    /**
     * Récupère une commande avec ses articles
     *
     * @param int $quote_id ID de le devis
     * @return array|null Les données de le devis et ses articles
     */

    public function getQuoteWithItems($quote_id)
    {
        $this->db->select('quotes_selling.*, clients.item_supplier as customer_name, user_name,
        clients.lastname as customer_last_name, clients.email as customer_email, clients.phone as customer_phone, clients.address as customer_address, clients.comptec');
        $this->db->from($this->table);
        $this->db->join('clients', 'clients.id = quotes_selling.customer_id');
        $this->db->where('quotes_selling.id', $quote_id);
        $quote = $this->db->get()->row_array();

        if (!$quote) {
            return null;
        }

                $this->db->select('
            quote_items_selling.*,
            quote_items_selling.unit_price_ttc,
            item_category.item_category as category_name,
            item.name as item_name
        ');
        $this->db->from($this->items_table);
        $this->db->join('item_category', 'item_category.id = quote_items_selling.category_id');
        $this->db->join('item', 'item.id = quote_items_selling.item_id');
        $this->db->where('quote_id', $quote_id);
        $this->db->order_by('position', 'ASC');
        $items = $this->db->get()->result_array();

        $quote['items'] = $items;
        return $quote;
    }

    public function getQuoteWithItems_21($quote_id)
    {
        // Récupération de le devis
        $this->db->select('quotes_selling.*, clients.item_supplier as customer_name, user_name,
            clients.lastname as customer_last_name, clients.email as customer_email, clients.phone as customer_phone, clients.address as customer_address, clients.comptec');
        $this->db->from($this->table);
        $this->db->join('clients', 'clients.id = quotes_selling.customer_id');
        $this->db->where('quotes_selling.id', $quote_id);
        $quote = $this->db->get()->row_array();

        if (!$quote) {
            return null;
        }

        // Récupération des articles
        $this->db->select('
            quote_items_selling.*,
            item_category.item_category as category_name,
            item.name as item_name
        ');
        $this->db->from($this->items_table);
        $this->db->join('item_category', 'item_category.id = quote_items_selling.category_id');
        $this->db->join('item', 'item.id = quote_items_selling.item_id');
        $this->db->where('quote_id', $quote_id);
        $this->db->order_by('position', 'ASC');
        $items = $this->db->get()->result_array();

        $quote['items'] = $items;
        return $quote;
    }

    /**
     * Met à jour le statut d'une commande
     *
     * @param int $quote_id ID de le devis
     * @param int $status Nouveau statut
     * @return bool
     */
    public function updateStatus($quote_id, $status)
    {
        $this->db->where('id', $quote_id);
        return $this->db->update($this->table, [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Met à jour le statut d'un devis en annulé avec le motif
     *
     * @param int $quote_id ID du devis
     * @param string $reason Motif de l'annulation
     * @return bool
     */
    public function updateCancelStatus($quote_id, $data)
    {
        $this->db->where('id', $quote_id);
        return $this->db->update($this->table, [
            'status'     => $data['status'],
            'notes'      => $data['reason'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Récupère l'état du stock pour un article
     *
     * @param int $item_id ID de l'article
     * @return object|null
     */
    public function getStockStatus($item_id)
    {
        $this->db->where('item_id', $item_id);
        $query = $this->db->get($this->stock_table);
        return $query->row();
    }

    /**
     * Méthode pour les admins (voient tout)
     */
    public function getListDataForAdmin()
    {
        return $this->_getListData(null);
    }

    /**
     * Méthode pour les utilisateurs normaux (filtré)
     */
    public function getListDataForUser($username)
    {
        return $this->_getListData($username);
    }

    /**
     * Méthode pour compatibilité (ancien code)
     */
    public function getListData($username = null)
    {
        return $this->_getListData($username);
    }

    /**
     * Méthode privée commune
     */

    private function _getListData($username = null)
    {
        $draw = intval($this->input->post('draw'));
        $start = intval($this->input->post('start'));
        $length = intval($this->input->post('length'));
        $search = $this->input->post('search')['value'];
        $status = $this->input->post('status');

        // Compter le total selon le filtre utilisateur
        if ($username !== null) {
            $this->db->where('user_name', $username);
        }
        $total_records = $this->db->count_all_results($this->table);

        $this->db->start_cache();

        $this->db->select('
        quotes_selling.*, user_name,
        clients.item_supplier as customer_name,
        clients.lastname as customer_last_name,
        clients.email as customer_email,
        clients.phone as customer_phone,
        clients.address as customer_address
    ');
        $this->db->from($this->table);
        $this->db->join('clients', 'clients.id = quotes_selling.customer_id', 'left');

        // FILTRAGE PAR UTILISATEUR SI SPÉCIFIÉ
        if ($username !== null) {
            $this->db->where('quotes_selling.user_name', $username);
        }

        if($search) {
            $this->db->group_start();
            $this->db->like('quotes_selling.designation', $search);
            $this->db->or_like('quotes_selling.quote_number', $search);
            $this->db->or_like('clients.item_supplier', $search);
            $this->db->or_like('clients.email', $search);
            $this->db->group_end();
        }

        // Ajout du filtre sur le statut
        if($status !== '' && (int)$status > 0) {
            $this->db->where('quotes_selling.status', $status);
        }

        $this->db->stop_cache();

        $filtered_records = $this->db->get()->num_rows();

        $this->db->order_by('quotes_selling.created_at', 'DESC');
        if($length != -1) {
            $this->db->limit($length, $start);
        }

        $query = $this->db->get();

        $this->db->flush_cache();

        $status_labels = [
            self::STATUS_PENDING => ['label' => 'En attente de validation', 'class' => 'label-warning'],
            self::STATUS_VALIDATED => ['label' => 'Validé', 'class' => 'label-success'],
            self::STATUS_REJECTED => ['label' => 'Rejeté', 'class' => 'label-danger'],
            self::STATUS_IN_PROGRESS => ['label' => 'En cours de traitement', 'class' => 'label-info'],
            self::STATUS_DELIVERED => ['label' => 'Livré', 'class' => 'label-success'],
            self::STATUS_CANCELLED => ['label' => 'Annulé', 'class' => 'label-default']
        ];

        // 🔹 Labels pour le statut de paiement
        $payment_status_labels = [
            'pending' => ['label' => '⏳ En attente', 'class' => 'label-warning'],
            'partial' => ['label' => '💸 Paiement partiel', 'class' => 'label-info'],
            'paid' => ['label' => '✅ Payé', 'class' => 'label-success']
        ];

        $data = [];
        foreach($query->result() as $row) {
            $status_info = isset($status_labels[$row->status]) ? $status_labels[$row->status] : ['label' => 'Inconnu', 'class' => 'label-default'];

            // 🔹 Récupération des informations de paiement
            $amount_paid = isset($row->amount_paid) ? floatval($row->amount_paid) : 0;
            $change_amount = isset($row->change_amount) ? floatval($row->change_amount) : 0;
            $remaining_amount = isset($row->remaining_amount) ? floatval($row->remaining_amount) : 0;
            $payment_status = isset($row->payment_status) ? $row->payment_status : 'pending';

            $payment_status_info = isset($payment_status_labels[$payment_status]) ? $payment_status_labels[$payment_status] : ['label' => 'Inconnu', 'class' => 'label-default'];

            $data[] = [
                'id' => $row->id,
                'quote_number' => $row->quote_number,
                'designation' => $row->designation,

                'dates' => [
                    'creation'      => date('d/m/Y', strtotime($row->created_at)),
                    'quote_date'    => $row->quote_date ? date('d/m/Y', strtotime($row->quote_date)) : 'Non définie',
                    'valid_until'   => $row->valid_until ? date('d/m/Y', strtotime($row->valid_until)) : 'Non définie',
                    'delivery_date' => $row->delivery_date ? date('d/m/Y', strtotime($row->delivery_date)) : 'Non définie',
                    'validation'    => $row->validated_at ? date('d/m/Y', strtotime($row->validated_at)) : null,
                    'rejet'         => $row->rejected_at ? date('d/m/Y', strtotime($row->rejected_at)) : null
                ],
                'amount' => [
                    'ht' => number_format($row->total_ht, 2, ',', ' '),
                    'tva' => number_format($row->tva_amount, 2, ',', ' '),
                    'ttc' => number_format($row->total_ttc, 2, ',', ' ')
                ],

                // 🔹 NOUVEAU : Informations de paiement
                'payment' => [
                    'amount_paid' => number_format($amount_paid, 2, ',', ' '),
                    'amount_paid_raw' => $amount_paid,
                    'change_amount' => number_format($change_amount, 2, ',', ' '),
                    'change_amount_raw' => $change_amount,
                    'remaining_amount' => number_format($remaining_amount, 2, ',', ' '),
                    'remaining_amount_raw' => $remaining_amount,
                    'payment_status' => $payment_status,
                    'payment_status_label' => $payment_status_info['label'],
                    'payment_status_class' => $payment_status_info['class'],
                    'payment_method_type' => isset($row->payment_method_type) ? $row->payment_method_type : null,
                    'payment_method' => isset($row->payment_method) ? $row->payment_method : null
                ],

                'tva_info' => [
                    'appliquee' => $row->apply_tva ? 'Oui' : 'Non',
                    'taux' => $row->tva_rate . '%'
                ],
                'status' => [
                    'code' => $row->status,
                    'label' => $status_info['label'],
                    'class' => $status_info['class']
                ],
                'user_name' => $row->user_name,

                'validation' => [
                    'status' => $row->validation_status,
                    'notes' => $row->validation_notes,
                    'rejection_reason' => $row->rejection_reason
                ],
                'notes' => $row->notes
            ];
        }

        return json_encode([
            'draw' => $draw,
            'recordsTotal' => $total_records,
            'recordsFiltered' => $filtered_records,
            'data' => $data
        ]);
    }
    /**
     * Met à jour un devis existant et ses articles
     *
     * @param array $data Les données du devis à mettre à jour
     * @return bool
     */
    public function update($data) {
        $this->db->trans_start();

        try {
            // Vérification que le devis peut être modifiée
            $quote = $this->getQuoteWithItems($data['id']);
            if (!$quote || $quote['status'] != self::STATUS_PENDING) {
                throw new Exception('Le devis ne peut pas être modifié dans son état actuel');
            }

            // Mise à jour des informations principales de le devis
            $quote_data = [
                'customer_id' => $data['customer'],
                'user_name' => $data['user_name'],
                'designation' => $data['designation'],
                'quote_date'  => date('Y-m-d', strtotime($data['quote_date'])),
                'valid_until' => ((isset($data['valid_until']) && !empty($data['valid_until'])) ? date('Y-m-d', strtotime($data['valid_until'])) : null),
                'payment_terms'     => $data['payment_term'],
                'delivery_terms'    => $data['delivery_term'],
                'delivery_location' => $data['delivery_location'],
                'apply_tva'   => $data['apply_tva'] ? 1 : 0,
                'tva_rate'    => $data['tva_rate'],
                'tva_amount'  => $data['tva_amount'],
                'total_ht'    => $data['total_ht'],
                'total_ttc'   => $data['total_ttc'],
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->db->where('id', $data['id']);
            $this->db->update('quotes', $quote_data);

            // Suppression des anciens articles
            $this->db->where('quote_id', $data['id']);
            $this->db->delete('quote_items_selling');

            // Insertion des nouveaux articles
            foreach ($data['items'] as $position => $item) {
                $item_data = [
                    'quote_id'      => $data['id'],
                    'category_id'   => $item['category_id'],
                    'item_id'       => $item['item_id'],
                    'quantity'      => $item['quantity'],
                    'unit_price'    => $item['price'],
                    'unit'          => $item['unit'],
                    'line_total'    => $item['line_total'],
                    'position'      => $position + 1,
                ];
                $this->db->insert('quote_items_selling', $item_data);
            }

            $this->db->trans_complete();
            return $this->db->trans_status();

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Quote Update Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère les données du devis formatés pour l'impression
     *
     * @param int $quote_id ID du devis
     * @return array|null Les données formatées du devis
     */
    public function getQuoteForPrint($quote_id)
    {
        $quote = $this->getQuoteWithItems($quote_id);

        if (!$quote) {
            return null;
        }

        // Formatage des données pour l'impression
        $print_data = [
            'quote_number' => $quote['quote_number'],
            'quote_date' => date('d/m/Y', strtotime($quote['quote_date'])),
            'delivery_date' => $quote['delivery_date'] ? date('d/m/Y', strtotime($quote['delivery_date'])) : 'Non définie',
            'customer' => [
                'name' => $quote['customer_name'] . ' ' . $quote['customer_last_name'],
                'email' => $quote['customer_email'],
                'address' => $quote['customer_address'] ?? ''
            ],
            'items' => [],
            'totals' => [
                'ht' => number_format($quote['total_ht'], 2, ',', ' '),
                'tva' => number_format($quote['tva_amount'], 2, ',', ' '),
                'ttc' => number_format($quote['total_ttc'], 2, ',', ' ')
            ],
            'tva_info' => [
                'applied' => $quote['apply_tva'] ? 'Oui' : 'Non',
                'rate' => $quote['tva_rate'] . '%'
            ],
            'validation' => [
                'status' => $quote['validation_status'],
                'date' => $quote['validated_at'] ? date('d/m/Y', strtotime($quote['validated_at'])) : null,
                'notes' => $quote['validation_notes'],
                'rejection_reason' => $quote['rejection_reason']
            ],
            'notes' => $quote['notes']
        ];

        // Formatage des articles
        foreach ($quote['items'] as $item) {
            $print_data['items'][] = [
                'category' => $item['category_name'],
                'name' => $item['item_name'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'price' => number_format($item['price'], 2, ',', ' '),
                'total' => number_format($item['line_total'], 2, ',', ' ')
            ];
        }

        return $print_data;
    }

    /**
     * Vérifie si un devis est validé
     *
     * @param int $quote_id ID du devis
     * @return bool
     */
    public function isQuoteValidated($quote_id)
    {
        $this->db->where('id', $quote_id);
        $this->db->where('status', self::STATUS_VALIDATED);
        return $this->db->get($this->table)->num_rows() > 0;
    }

    /**
     * Vérifie si un devis est rejeté
     *
     * @param int $quote_id ID du devis
     * @return bool
     */
    public function isQuoteRejected($quote_id)
    {
        $this->db->where('id', $quote_id);
        $this->db->where('status', self::STATUS_REJECTED);
        return $this->db->get($this->table)->num_rows() > 0;
    }

    /**
     * Valide un devis
     *
     * @param int $quote_id ID du devis
     * @param array $data Données de validation
     * @return bool
     */
    public function validateQuote($quote_id, $data)
    {
        $this->db->trans_start();

        try {
            // Mise à jour du statut du devis
            $this->db->where('id', $quote_id);
            if (!$this->db->update($this->table, $data)) {
                throw new Exception('Erreur lors de la mise à jour du statut');
            }

            $this->db->trans_complete();
            return $this->db->trans_status();

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Quote Model - Validate Error: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Rejette un devis
     *
     * @param int $quote_id ID du devis
     * @param array $data Données de rejet
     * @return bool
     */
    public function rejectQuote($quote_id, $data)
    {
        $this->db->trans_start();

        try {
            // Mise à jour du statut du devis
            $this->db->where('id', $quote_id);
            if (!$this->db->update($this->table, $data)) {
                throw new Exception('Erreur lors de la mise à jour du statut');
            }

            $this->db->trans_complete();
            return $this->db->trans_status();

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Quote Model - Reject Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère toutes les ventes (quotes_selling) d’un client avec leurs articles et paiements
     * @param int $customer_id
     * @return array
     */
    public function getSalesByCustomer($customer_id) {
        $this->db->select('quotes_selling.*, 
        clients.item_supplier as customer_name,
        clients.lastname as customer_last_name,
        clients.email as customer_email,
        clients.phone as customer_phone,
        clients.address as customer_address')
            ->from('quotes_selling')
            ->join('clients', 'clients.id = quotes_selling.customer_id', 'left')
            ->where('quotes_selling.customer_id', $customer_id)
            ->order_by('quotes_selling.quote_date', 'DESC');

        $sales = $this->db->get()->result_array();
        if (empty($sales)) {
            return [];
        }

        // Charger les articles pour chaque vente
        foreach ($sales as &$sale) {
            $this->db->select('quote_items_selling.*, 
            item_category.item_category as category_name,
            item.name as item_name')
                ->from('quote_items_selling')
                ->join('item_category', 'item_category.id = quote_items_selling.category_id')
                ->join('item', 'item.id = quote_items_selling.item_id')
                ->where('quote_id', $sale['id'])
                ->order_by('position', 'ASC');
            $sale['items'] = $this->db->get()->result_array();

            // Paiements : ici on n'a pas de table dédiée, les infos sont dans la vente elle-même
            // (amount_paid, payment_status, etc.) – on peut les exposer directement.
        }

        return $sales;
    }


    /**
     * Récupère les informations de validation d'un devis
     *
     * @param int $quote_id ID du devis
     * @return array|null
     */
    public function getQuoteValidationInfo($quote_id)
    {
        $this->db->select('validation_status, validated_at, validated_by, validation_notes, rejected_at');
        $this->db->where('id', $quote_id);
        return $this->db->get($this->table)->row_array();
    }
}