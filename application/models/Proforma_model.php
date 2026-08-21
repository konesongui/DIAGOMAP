<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Proforma_model extends CI_Model {

    protected $table = 'proforma';
    protected $items_table = 'proforma_items';
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
     * (Cette méthode est conservée pour compatibilité, mais le contrôleur utilise sa propre logique)
     */
    public function add($data)
    {
        $this->db->trans_start();

        try {
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

            $this->db->insert($this->table, $quote_data);
            $quote_id = $this->db->insert_id();

            if (!$quote_id) {
                throw new Exception('Erreur lors de la création du devis');
            }

            foreach ($data['items'] as $position => $item) {
                $item_data = [
                    'quote_id' => $quote_id,
                    'category_id' => $item['category_id'],
                    'item_id' => $item['item_id'],
                    'service_id' => isset($item['service_id']) ? $item['service_id'] : null,
                    'item_type' => isset($item['item_type']) ? $item['item_type'] : 'product',
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'unit' => $item['unit'],
                    'line_total' => $item['line_total'],
                    'position' => $position + 1,
                ];

                if (!$this->db->insert($this->items_table, $item_data)) {
                    throw new Exception('Erreur lors de l\'ajout d\'un article au devis');
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
     * Génère un numéro unique pour un devis
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
     * Récupère un devis avec ses articles (produits et services)
     *
     * @param int $quote_id ID du devis
     * @return array|null
     */
    public function getQuoteWithItems($quote_id)
    {
        // Récupération des informations générales du devis
        $this->db->select('proforma.*, clients.item_supplier as customer_name, clients.lastname as customer_last_name, clients.email as customer_email, clients.phone as customer_phone, clients.address as customer_address, clients.comptec');
        $this->db->from($this->table);
        $this->db->join('clients', 'clients.id = proforma.customer_id');
        $this->db->where('proforma.id', $quote_id);
        $quote = $this->db->get()->row_array();

        if (!$quote) {
            return null;
        }

        // Récupération des articles (produits et services)
        $this->db->select('
            proforma_items.*,
            item_category.item_category as category_name,
            item.name as item_name,
            services.name as service_name,
            services.duration as service_duration
        ');
        $this->db->from($this->items_table);
        $this->db->join('item_category', 'item_category.id = proforma_items.category_id', 'left');
        $this->db->join('item', 'item.id = proforma_items.item_id', 'left');
        $this->db->join('services', 'services.id = proforma_items.service_id', 'left');
        $this->db->where('proforma_items.quote_id', $quote_id);
        $this->db->order_by('proforma_items.position', 'ASC');
        $items = $this->db->get()->result_array();

        // Pour chaque ligne, définir le nom affiché et l'unité en fonction du type
        foreach ($items as &$item) {
            if ($item['item_type'] == 'service') {
                $item['item_name'] = $item['service_name'];       // Nom du service
                $item['category_name'] = null;                    // Pas de catégorie pour un service
                if (empty($item['unit']) && !empty($item['service_duration'])) {
                    $item['unit'] = $item['service_duration'];    // Utiliser la durée comme unité par défaut
                }
            } else {
                // Produit : le nom est déjà dans item_name
                $item['service_name'] = null;
            }
        }

        $quote['items'] = $items;
        return $quote;
    }

    /**
     * Met à jour le statut d'un devis
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
     * Met à jour le statut d'un devis en annulé avec motif
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
     */
    public function getStockStatus($item_id)
    {
        $this->db->where('item_id', $item_id);
        $query = $this->db->get($this->stock_table);
        return $query->row();
    }

    /**
     * Liste des devis pour DataTable (admin)
     */
    public function getListDataForAdmin()
    {
        return $this->_getListData(null);
    }

    /**
     * Liste des devis pour un utilisateur normal
     */
    public function getListDataForUser($username)
    {
        return $this->_getListData($username);
    }

    /**
     * Méthode privée pour la liste DataTable
     */
    private function _getListData($username = null)
    {
        $draw = intval($this->input->post('draw'));
        $start = intval($this->input->post('start'));
        $length = intval($this->input->post('length'));
        $search = $this->input->post('search')['value'];
        $status = $this->input->post('status');

        if ($username !== null) {
            $this->db->where('user_name', $username);
        }
        $total_records = $this->db->count_all_results($this->table);

        $this->db->start_cache();

        $this->db->select('
            proforma.*, user_name,
            clients.item_supplier as customer_name,
            clients.lastname as customer_last_name,
            clients.email as customer_email,
            clients.phone as customer_phone,
            clients.address as customer_address
        ');
        $this->db->from($this->table);
        $this->db->join('clients', 'clients.id = proforma.customer_id', 'left');

        if ($username !== null) {
            $this->db->where('proforma.user_name', $username);
        }

        if($search) {
            $this->db->group_start();
            $this->db->like('proforma.designation', $search);
            $this->db->or_like('proforma.quote_number', $search);
            $this->db->or_like('clients.item_supplier', $search);
            $this->db->or_like('clients.email', $search);
            $this->db->group_end();
        }

        if($status !== '' && (int)$status > 0) {
            $this->db->where('proforma.status', $status);
        }

        $this->db->stop_cache();

        $filtered_records = $this->db->get()->num_rows();

        $this->db->order_by('proforma.created_at', 'DESC');
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

        $data = [];
        foreach($query->result() as $row) {
            $status_info = isset($status_labels[$row->status]) ? $status_labels[$row->status] : ['label' => 'Inconnu', 'class' => 'label-default'];

            $data[] = [
                'id' => $row->id,
                'quote_number' => $row->quote_number,
                'designation' => $row->designation,
                'customer' => [
                    'name' => $row->customer_name.' '.$row->customer_last_name,
                    'email' => $row->customer_email,
                    'phone' => $row->customer_phone,
                    'address' => $row->customer_address
                ],
                'dates' => [
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
     * Met à jour un devis existant et ses articles (utilisé si le contrôleur appelle cette méthode)
     */
    public function update($data) {
        $this->db->trans_start();

        try {
            $quote = $this->getQuoteWithItems($data['id']);
            if (!$quote || $quote['status'] != self::STATUS_PENDING) {
                throw new Exception('Le devis ne peut pas être modifié dans son état actuel');
            }

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
            $this->db->update('proforma', $quote_data);

            // Suppression des anciens articles
            $this->db->where('quote_id', $data['id']);
            $this->db->delete('proforma_items');

            // Insertion des nouveaux articles
            foreach ($data['items'] as $position => $item) {
                $item_data = [
                    'quote_id'      => $data['id'],
                    'category_id'   => isset($item['category_id']) ? $item['category_id'] : null,
                    'item_id'       => isset($item['item_id']) ? $item['item_id'] : null,
                    'service_id'    => isset($item['service_id']) ? $item['service_id'] : null,
                    'item_type'     => isset($item['item_type']) ? $item['item_type'] : 'product',
                    'quantity'      => $item['quantity'],
                    'unit_price'    => $item['price'],
                    'unit'          => $item['unit'],
                    'line_total'    => $item['line_total'],
                    'position'      => $position + 1,
                ];
                $this->db->insert('proforma_items', $item_data);
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
     * Récupère les données du devis formatées pour l'impression
     */
    public function getQuoteForPrint($quote_id)
    {
        $quote = $this->getQuoteWithItems($quote_id);
        if (!$quote) return null;

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

        foreach ($quote['items'] as $item) {
            $print_data['items'][] = [
                'category' => $item['category_name'],
                'name' => $item['item_name'],
                'quantity' => $item['quantity'],
                'unit' => $item['unit'],
                'price' => number_format($item['unit_price'], 2, ',', ' '),
                'total' => number_format($item['line_total'], 2, ',', ' ')
            ];
        }

        return $print_data;
    }

    /**
     * Vérifie si un devis est validé
     */
    public function isQuoteValidated($quote_id)
    {
        $this->db->where('id', $quote_id);
        $this->db->where('status', self::STATUS_VALIDATED);
        return $this->db->get($this->table)->num_rows() > 0;
    }

    /**
     * Vérifie si un devis est rejeté
     */
    public function isQuoteRejected($quote_id)
    {
        $this->db->where('id', $quote_id);
        $this->db->where('status', self::STATUS_REJECTED);
        return $this->db->get($this->table)->num_rows() > 0;
    }

    /**
     * Valide un devis
     */
    public function validateQuote($quote_id, $data)
    {
        $this->db->trans_start();
        try {
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
     */
    public function rejectQuote($quote_id, $data)
    {
        $this->db->trans_start();
        try {
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
     * Récupère les informations de validation d'un devis
     */
    public function getQuoteValidationInfo($quote_id)
    {
        $this->db->select('validation_status, validated_at, validated_by, validation_notes, rejected_at');
        $this->db->where('id', $quote_id);
        return $this->db->get($this->table)->row_array();
    }

    /**
     * Récupère tous les devis d'un client avec leurs articles (produits et services)
     */
    public function getQuotesByCustomer($customer_id) {
        $this->db->select('proforma.*, 
            clients.item_supplier as customer_name,
            clients.lastname as customer_last_name,
            clients.email as customer_email,
            clients.phone as customer_phone,
            clients.address as customer_address')
            ->from('proforma')
            ->join('clients', 'clients.id = proforma.customer_id', 'left')
            ->where('proforma.customer_id', $customer_id)
            ->order_by('proforma.quote_date', 'DESC');

        $quotes = $this->db->get()->result_array();
        if (empty($quotes)) {
            return [];
        }

        // Charger les articles pour chaque devis
        foreach ($quotes as &$quote) {
            $this->db->select('
                proforma_items.*,
                item_category.item_category as category_name,
                item.name as item_name,
                services.name as service_name,
                services.duration as service_duration
            ')
                ->from('proforma_items')
                ->join('item_category', 'item_category.id = proforma_items.category_id', 'left')
                ->join('item', 'item.id = proforma_items.item_id', 'left')
                ->join('services', 'services.id = proforma_items.service_id', 'left')
                ->where('quote_id', $quote['id'])
                ->order_by('position', 'ASC');
            $items = $this->db->get()->result_array();

            // Normalisation des noms pour les services
            foreach ($items as &$item) {
                if ($item['item_type'] == 'service') {
                    $item['item_name'] = $item['service_name'];
                    $item['category_name'] = null;
                }
            }
            $quote['items'] = $items;
        }

        return $quotes;
    }
}