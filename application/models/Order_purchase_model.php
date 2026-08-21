<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Order_purchase_model extends CI_Model {

    protected $table = 'orders_purchase';
    protected $items_table = 'order_items_purchase';
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
     * Ajoute une nouvelle commande avec ses articles
     * 
     * @param array $data Les données de la commande
     * @return int|bool L'ID de la commande créée ou false en cas d'erreur
     */
    public function add($data)
    {   
        // var_dump($data);
        // exit;
        $this->db->trans_start();

        try {
            // Préparation des données de la commande
            $order_data = [
                'order_number'=> $this->generateOrderNumber(),
                'supplier_id' => $data['supplier_id'],
                'designation' => $data['designation'],
                'order_date'  => date('Y-m-d', strtotime($data['order_date'])),
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

            // Insertion de la commande
            $this->db->insert($this->table, $order_data);
            $order_id = $this->db->insert_id();

            if (!$order_id) {
                throw new Exception('Erreur lors de la création de la commande');
            }

            // Insertion des articles
            foreach ($data['items'] as $position => $item) {
                $item_data = [
                    'order_id' => $order_id,
                    'category_id' => $item['category_id'],
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'unit' => $item['unit'],
                    'line_total' => $item['line_total'],
                    'position' => $position + 1,
                ];

                if (!$this->db->insert($this->items_table, $item_data)) {
                    throw new Exception('Erreur lors de l\'ajout d\'un article à la commande');
                }
            }

            $this->db->trans_complete();
            return $order_id;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Order Model Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Génère un numéro unique pour une commande
     * Format: CMD-YYYYMM-XXXX où XXXX est un numéro séquentiel
     * 
     * @return string
     */
    private function generateOrderNumber()
    {
        $prefix = 'CMD';  // CMD pour Commande
        $date = date('Ym');  // Format YYYYMM
        
        // Recherche le dernier numéro pour ce mois
        $this->db->like('order_number', $prefix . '-' . $date, 'after');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get($this->table);
        
        if ($query->num_rows() > 0) {
            // Extrait le numéro séquentiel de la dernière commande
            $last_ref = $query->row()->order_number;
            $sequence = intval(substr($last_ref, -4)) + 1;
        } else {
            // Première commande du mois
            $sequence = 1;
        }
        
        // Formate le numéro séquentiel sur 4 chiffres
        $sequence_padded = str_pad($sequence, 4, '0', STR_PAD_LEFT);
        
        return $prefix . '-' . $date . '-' . $sequence_padded;
    }

    /**
     * Récupère une commande avec ses articles
     * 
     * @param int $order_id ID de la commande
     * @return array|null Les données de la commande et ses articles
     */
    public function getOrderWithItems($order_id)
    {
        // Récupération de la commande
        $this->db->select('orders_purchase.*, item_supplier.item_supplier as customer_name,
            item_supplier.lastname as customer_last_name, item_supplier.email as customer_email');
        $this->db->from($this->table);
        $this->db->join('item_supplier', 'item_supplier.id = orders_purchase.supplier_id');
        $this->db->where('orders_purchase.id', $order_id);
        $order = $this->db->get()->row_array();

        if (!$order) {
            return null;
        }

        // Récupération des articles
        $this->db->select('
            order_items_purchase.*,
            item_category.item_category as category_name,
            item.name as item_name
        ');
        $this->db->from($this->items_table);
        $this->db->join('item_category', 'item_category.id = order_items_purchase.category_id');
        $this->db->join('item', 'item.id = order_items_purchase.item_id');
        $this->db->where('order_id', $order_id);
        $this->db->order_by('position', 'ASC');
        $items = $this->db->get()->result_array();

        $order['items'] = $items;
        return $order;
    }

    /**
     * Met à jour le statut d'une commande
     * 
     * @param int $order_id ID de la commande
     * @param int $status Nouveau statut
     * @return bool
     */
    public function updateStatus($order_id, $status)
    {
        $this->db->where('id', $order_id);
        return $this->db->update($this->table, [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Met à jour le statut d'une commande en annulée avec le motif
     * 
     * @param int $order_id ID de la commande
     * @param string $reason Motif de l'annulation
     * @return bool
     */
    public function updateCancelStatus($order_id, $data)
    {   
        $this->db->where('id', $order_id);
        return $this->db->update($this->table, [
            'status'     => $data['status'], // 3 = Annulée
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

    public function getListData()
    {
        $draw = intval($this->input->post('draw'));
        $start = intval($this->input->post('start'));
        $length = intval($this->input->post('length'));
        $search = $this->input->post('search')['value'];

        $total_records = $this->db->count_all($this->table);

        $this->db->start_cache();
        
        $this->db->select('
            orders_purchase.*,
            item_supplier.item_supplier as customer_name,
            item_supplier.lastname as customer_last_name,
            item_supplier.email as customer_email,
            item_supplier.phone as customer_phone,
            item_supplier.address as customer_address
        ');
        $this->db->from($this->table);
        $this->db->join('item_supplier', 'item_supplier.id = orders_purchase.supplier_id', 'left');
        
        if($search) {
            $this->db->group_start();
            $this->db->like('orders_purchase.designation', $search);
            $this->db->or_like('orders_purchase.order_number', $search);
            $this->db->or_like('item_supplier.item_supplier', $search);
            $this->db->or_like('item_supplier.email', $search);
            $this->db->group_end();
        }
        
        $this->db->stop_cache();

        $filtered_records = $this->db->get()->num_rows();
        
        $this->db->order_by('orders_purchase.created_at', 'DESC');
        if($length != -1) {
            $this->db->limit($length, $start);
        }

        $query = $this->db->get();
        
        $this->db->flush_cache();
        
        $status_labels = [
            self::STATUS_PENDING => ['label' => 'En attente de validation', 'class' => 'label-warning'],
            self::STATUS_VALIDATED => ['label' => 'Validée', 'class' => 'label-success'],
            self::STATUS_REJECTED => ['label' => 'Rejetée', 'class' => 'label-danger'],
            self::STATUS_IN_PROGRESS => ['label' => 'Validée', 'class' => 'label-info'],
            self::STATUS_DELIVERED => ['label' => 'Livrée', 'class' => 'label-success'],
            self::STATUS_CANCELLED => ['label' => 'Annulée', 'class' => 'label-default']
        ];

        $data = [];
        foreach($query->result() as $row) {
            $status_info = isset($status_labels[$row->status]) ? $status_labels[$row->status] : ['label' => 'Inconnu', 'class' => 'label-default'];
            
            $data[] = [
                'id' => $row->id,
                'order_number' => $row->order_number,
                'designation' => $row->designation,
                'payment_terms' => $row->payment_terms??'Non défini',
                'delivery_term' => $row->delivery_term??'Non défini',
                'delivery_location' => $row->delivery_location??'Non défini',
                'customer' => [
                    'name' => $row->customer_name.' '.$row->customer_last_name,
                    'email' => $row->customer_email,
                    'phone' => $row->customer_phone,
                    'address' => $row->customer_address
                ],
                'dates' => [
                    'creation' => date('d/m/Y', strtotime($row->created_at)),
                    'commande' => date('d/m/Y', strtotime($row->order_date)),
                    'livraison' => $row->delivery_date ? date('d/m/Y', strtotime($row->delivery_date)) : 'Non définie',
                    'validation' => $row->validated_at ? date('d/m/Y', strtotime($row->validated_at)) : null,
                    'rejet' => $row->rejected_at ? date('d/m/Y', strtotime($row->rejected_at)) : null
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
     * Met à jour une commande existante et ses articles
     * 
     * @param array $data Les données de la commande à mettre à jour
     * @return bool
     */
    public function update($data) {
        $this->db->trans_start();

        try {
            // Vérification que la commande peut être modifiée
            $order = $this->getOrderWithItems($data['id']);
            if (!$order || $order['status'] != self::STATUS_PENDING) {
                throw new Exception('La commande ne peut pas être modifiée dans son état actuel');
            }

            // Mise à jour des informations principales de la commande
            $order_data = [
                'supplier_id' => $data['customer'],
                'designation' => $data['designation'],
                'order_date'  => date('Y-m-d', strtotime($data['order_date'])),
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
            $this->db->update('orders_purchase', $order_data);

            // Suppression des anciens articles
            $this->db->where('order_id', $data['id']);
            $this->db->delete('order_items_purchase');

            // Insertion des nouveaux articles
            foreach ($data['items'] as $position => $item) {
                $item_data = [
                    'order_id'      => $data['id'],
                    'category_id'   => $item['category_id'],
                    'item_id'       => $item['item_id'],
                    'quantity'      => $item['quantity'],
                    'unit_price'    => $item['price'],
                    'unit'          => $item['unit'],
                    'line_total'    => $item['line_total'],
                    'position'      => $position + 1,
                ];
                $this->db->insert('order_items_purchase', $item_data);
            }

            $this->db->trans_complete();
            return $this->db->trans_status();

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Order Update Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère les données de la commande formatées pour l'impression
     * 
     * @param int $order_id ID de la commande
     * @return array|null Les données formatées de la commande
     */
    public function getOrderForPrint($order_id)
    {
        $order = $this->getOrderWithItems($order_id);
        
        if (!$order) {
            return null;
        }

        // Formatage des données pour l'impression
        $print_data = [
            'order_number' => $order['order_number'],
            'order_date' => date('d/m/Y', strtotime($order['order_date'])),
            'delivery_date' => $order['delivery_date'] ? date('d/m/Y', strtotime($order['delivery_date'])) : 'Non définie',
            'customer' => [
                'name' => $order['customer_name'] . ' ' . $order['customer_last_name'],
                'email' => $order['customer_email'],
                'address' => $order['customer_address'] ?? ''
            ],
            'items' => [],
            'totals' => [
                'ht' => number_format($order['total_ht'], 2, ',', ' '),
                'tva' => number_format($order['tva_amount'], 2, ',', ' '),
                'ttc' => number_format($order['total_ttc'], 2, ',', ' ')
            ],
            'tva_info' => [
                'applied' => $order['apply_tva'] ? 'Oui' : 'Non',
                'rate' => $order['tva_rate'] . '%'
            ],
            'validation' => [
                'status' => $order['validation_status'],
                'date' => $order['validated_at'] ? date('d/m/Y', strtotime($order['validated_at'])) : null,
                'notes' => $order['validation_notes'],
                'rejection_reason' => $order['rejection_reason']
            ],
            'notes' => $order['notes']
        ];

        // Formatage des articles
        foreach ($order['items'] as $item) {
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
     * Valide une commande par le client
     * 
     * @param int $order_id ID de la commande
     * @param array $data Données de validation
     * @return bool
     */
    public function validateOrder($order_id, $data)
    {   
        $this->db->trans_start();

        try {
            // Vérification que la commande n'est pas déjà validée ou rejetée
            $order = $this->getOrderWithItems($order_id);
            if (!$order || $order['status'] != self::STATUS_PENDING) {
                throw new Exception('La commande ne peut pas être validée dans son état actuel');
            }

            $update_data = [
                'status'        => self::STATUS_VALIDATED,
                'notes'         => isset($data['notes']) ? $data['notes'] : null,
                'updated_at'    => date('Y-m-d H:i:s')
            ];

            $this->db->where('id', $order_id);
            $result = $this->db->update($this->table, $update_data);

            $this->db->trans_complete();
            return $result;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Order Validation Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Rejette une commande par le client
     * 
     * @param int $order_id ID de la commande
     * @param array $data Données de rejet
     * @return bool
     */
    public function rejectOrder($order_id, $data)
    {   
        // var_dump($data);
        // exit;
        $this->db->trans_start();

        try {
            // Vérification que la commande n'est pas déjà validée ou rejetée
            $order = $this->getOrderWithItems($order_id);
            if (!$order || $order['status'] != self::STATUS_PENDING) {
                throw new Exception('La commande ne peut pas être rejetée dans son état actuel');
            }

            $update_data = [
                'status'     => self::STATUS_REJECTED, // 3 = Rejeté
                'notes'      => $data['reason'],
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->db->where('id', $order_id);
            $result = $this->db->update($this->table, $update_data);

            $this->db->trans_complete();
            return $result;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Order Rejection Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifie si une commande a été validée par le client
     * 
     * @param int $order_id ID de la commande
     * @return bool
     */
    public function isOrderValidated($order_id)
    {
        $this->db->select('status, validated_at');
        $this->db->where('id', $order_id);
        $order = $this->db->get($this->table)->row();
        
        return ($order && $order->status == self::STATUS_VALIDATED && $order->validated_at !== null);
    }

    /**
     * Vérifie si une commande a été rejetée par le client
     * 
     * @param int $order_id ID de la commande
     * @return bool
     */
    public function isOrderRejected($order_id)
    {
        $this->db->select('status, rejected_at');
        $this->db->where('id', $order_id);
        $order = $this->db->get($this->table)->row();
        
        return ($order && $order->status == self::STATUS_REJECTED && $order->rejected_at !== null);
    }

    /**
     * Récupère les informations de validation d'une commande
     * 
     * @param int $order_id ID de la commande
     * @return array|null
     */
    public function getOrderValidationInfo($order_id)
    {
        $this->db->select('validation_status, validated_at, validated_by, validation_notes, rejected_at, rejected_by, rejection_reason');
        $this->db->where('id', $order_id);
        return $this->db->get($this->table)->row_array();
    }
} 