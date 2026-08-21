<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Delivery_supplier_model extends CI_Model {

    protected $table = 'deliveries_supplier';
    protected $items_table = 'delivery_supplier_items';
    protected $stock_table = 'stock';
    protected $current_session;

    // Constantes pour les statuts
    const STATUS_PENDING = 1;    // En préparation
    const STATUS_IN_PROGRESS = 2;  // En cours de livraison
    const STATUS_DELIVERED = 3;   // Livrée
    const STATUS_CANCELLED = 4;  // Annulée
    const STATUS_CLOSED = 5;  // Terminée
    const STATUS_PARTIALLY_DELIVERED = 6;  // Partiellement livrée

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    /**
     * Ajoute une nouvelle livraison avec ses articles
     *
     * @param array $data Les données de la livraison
     * @return int|bool L'ID de la livraison créée ou false en cas d'erreur
     */
    public function add($data)
    {
        // var_dump($data);
        // die();
        $this->db->trans_start();

        try {
            // Préparation des données de la livraison
            $delivery_data = [
                'delivery_number' => $this->generateDeliveryNumber(),
                'customer_id' => $data['customer_id'],
                'user_name' => $data['user_name'],
                'payment_method' => $data['payment_method'],
                'objet' => $data['objet'],
                'designation' => $data['designation'],
                'delivery_date' => date('Y-m-d', strtotime($data['delivery_date'])),
                'deadline' => date('Y-m-d', strtotime($data['deadline'])),
                'shipping_method' => $data['payment_term'],
                'tracking_number' => $data['delivery_term'],
                'delivery_address' => $data['delivery_location'],
                'apply_tva' => isset($data['apply_tva']) ? $data['apply_tva'] : 1,
                'tva_rate' => isset($data['tva_rate']) ? $data['tva_rate'] : 20.00,
                'tva_amount' => $data['tva_amount'],
                'total_ht' => $data['total_ht'],
                'total_ttc' => $data['total_ttc'],
                'status' => $data['status'],
                'notes' => isset($data['notes']) ? $data['notes'] : null,
                'created_at' => date('Y-m-d H:i:s'),
            ];

            // Insertion de la livraison
            $this->db->insert($this->table, $delivery_data);
            $delivery_id = $this->db->insert_id();

            if (!$delivery_id) {
                throw new Exception('Erreur lors de la création de la livraison');
            }

            // Insertion des articles
            foreach ($data['items'] as $position => $item) {
                $item_data = [
                    'delivery_id' => $delivery_id,
                    'category_id' => $item['category_id'],
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'unit' => $item['unit'],
                    'line_total' => $item['line_total'],
                    'position' => $position + 1,
                ];

                if (!$this->db->insert($this->items_table, $item_data)) {
                    throw new Exception('Erreur lors de l\'ajout d\'un article à la livraison');
                }
            }

            $this->db->trans_complete();
            return $delivery_id;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Delivery Model Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Génère un numéro unique pour une livraison
     * Format: LIV-YYYYMM-XXXX où XXXX est un numéro séquentiel
     *
     * @return string
     */
    private function generateDeliveryNumber()
    {
        $prefix = 'LIV';  // LIV pour Livraison
        $date = date('Ym');  // Format YYYYMM

        // Recherche le dernier numéro pour ce mois
        $this->db->like('delivery_number', $prefix . '-' . $date, 'after');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get($this->table);

        if ($query->num_rows() > 0) {
            $last_ref = $query->row()->delivery_number;
            $sequence = intval(substr($last_ref, -4)) + 1;
        } else {
            $sequence = 1;
        }

        $sequence_padded = str_pad($sequence, 4, '0', STR_PAD_LEFT);

        return $prefix . '-' . $date . '-' . $sequence_padded;
    }

    /**
     * Récupère une livraison avec ses articles
     *
     * @param int $delivery_id ID de la livraison
     * @return array|null Les données de la livraison et ses articles
     */
    public function getDeliveryWithItems($delivery_id)
    {
        // Récupération de la livraison avec les totaux de la commande
        $this->db->select('deliveries_supplier.*,user_name, item_supplier.item_supplier as customer_name,
            item_supplier.lastname as customer_last_name, item_supplier.email as customer_email, item_supplier.phone as customer_phone, item_supplier.address as customer_address, item_supplier.comptec');
        $this->db->from($this->table);
        $this->db->join('item_supplier', 'item_supplier.id = deliveries_supplier.customer_id');
        $this->db->where('deliveries_supplier.id', $delivery_id);
        $delivery = $this->db->get()->row_array();

        if (!$delivery) {
            return null;
        }

        // Récupération des articles
        $this->db->select('
            delivery_supplier_items.*,
            item_category.item_category as category_name,
            item.name as item_name
        ');
        $this->db->from($this->items_table);
        $this->db->join('item_category', 'item_category.id = delivery_supplier_items.category_id');
        $this->db->join('item', 'item.id = delivery_supplier_items.item_id');
        $this->db->where('delivery_id', $delivery_id);
        $this->db->order_by('position', 'ASC');
        $items = $this->db->get()->result_array();

        $delivery['items'] = $items;
        return $delivery;
    }

    /**
     * Met à jour le statut d'une livraison
     *
     * @param int $delivery_id ID de la livraison
     * @param int $status Nouveau statut
     * @return bool
     */
    public function updateStatus($delivery_id, $status)
    {
        $this->db->where('id', $delivery_id);
        return $this->db->update($this->table, [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Met à jour le statut d'une livraison en annulé avec le motif
     *
     * @param int $delivery_id ID de la livraison
     * @param string $reason Motif de l'annulation
     * @return bool
     */
    public function updateCancelStatus($delivery_id, $data)
    {
        $this->db->where('id', $delivery_id);
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
        $status = $this->input->post('status'); // Récupération du statut

        $total_records = $this->db->count_all($this->table);

        $this->db->start_cache();

        $this->db->select('
            deliveries_supplier.*,
            item_supplier.item_supplier as customer_name,
            item_supplier.lastname as customer_last_name,
            item_supplier.email as customer_email,
            item_supplier.phone as customer_phone,
            item_supplier.address as customer_address,
            orders.order_date as order_date,
            deliveries_supplier.delivery_terms as order_delivery_terms,
            deliveries_supplier.delivery_location as order_delivery_location,
            orders.total_ht as order_total_ht,
            orders.tva_amount as order_tva_amount,
            deliveries_supplier.total_ttc as order_total_ttc,
            orders.apply_tva as order_apply_tva,
            orders.tva_rate as order_tva_rate,
            orders.designation as order_designation
        ');
        $this->db->from($this->table);
        $this->db->join('item_supplier', 'item_supplier.id = deliveries_supplier.customer_id', 'left');
        $this->db->join('orders', 'orders.id = deliveries_supplier.order_id', 'left');

        if($search) {
            $this->db->group_start();
            $this->db->like('deliveries_supplier.designation', $search);
            $this->db->or_like('deliveries_supplier.delivery_number', $search);
            $this->db->or_like('item_supplier.item_supplier', $search);
            $this->db->or_like('item_supplier.email', $search);
            $this->db->group_end();
        }

        // Ajout du filtre sur le statut
        if($status !== '' &&  (int)$status > 0 ) {
            $this->db->where('deliveries_supplier.status', $status);
        }

        $this->db->stop_cache();

        $filtered_records = $this->db->get()->num_rows();

        $this->db->order_by('deliveries_supplier.created_at', 'DESC');
        if($length != -1) {
            $this->db->limit($length, $start);
        }

        $query = $this->db->get();

        $this->db->flush_cache();

        $status_labels = [
            self::STATUS_PENDING => ['label' => 'Non livré', 'class' => 'label-warning'],
            self::STATUS_IN_PROGRESS => ['label' => 'En cours de livraison', 'class' => 'label-info'],
            self::STATUS_PARTIALLY_DELIVERED => ['label' => 'Partiellement livré', 'class' => 'label-warning'],
            self::STATUS_DELIVERED => ['label' => 'Livré', 'class' => 'label-success'],
            self::STATUS_CANCELLED => ['label' => 'Annulé', 'class' => 'label-danger'],
            self::STATUS_CLOSED => ['label' => 'Terminé', 'class' => 'label-default']
        ];

        $data = [];
        foreach($query->result() as $row) {
            $status_info = isset($status_labels[$row->status]) ? $status_labels[$row->status] : ['label' => 'Inconnu', 'class' => 'label-default'];

            $data[] = [
                'id' => $row->id,
                'delivery_number' => $row->delivery_number,
                'designation' => $row->order_designation ?? 'Non définie',

                'delivery_terms' => $row->order_delivery_terms ?? 'Non défini',
                'delivery_location' => $row->order_delivery_location ?? 'Non défini',
                'customer' => [
                    'name' => $row->customer_name.' '.$row->customer_last_name,
                    'email' => $row->customer_email,
                    'phone' => $row->customer_phone,
                    'address' => $row->customer_address
                ],
                'dates' => [
                    'creation'      => date('d/m/Y', strtotime($row->created_at)),
                    'delivery_date' => $row->delivery_date ? date('d/m/Y', strtotime($row->delivery_date)) : 'Non définie',
                    'deadline'      => $row->deadline ? date('d/m/Y', strtotime($row->deadline)) : 'Non définie',
                    'order_date'    => $row->order_date ? date('d/m/Y', strtotime($row->order_date)) : 'Non définie',
                    'validation'    => $row->validated_at ? date('d/m/Y', strtotime($row->validated_at)) : null,
                    'rejet'         => $row->rejected_at ? date('d/m/Y', strtotime($row->rejected_at)) : null
                ],
                'amount' => [
                    'ht' => number_format($row->order_total_ht ?? 0, 2, ',', ' '),
                    'tva' => number_format($row->order_tva_amount ?? 0, 2, ',', ' '),
                    'ttc' => number_format($row->order_total_ttc ?? 0, 2, ',', ' ')
                ],
                'tva_info' => [
                    'appliquee' => $row->order_apply_tva ? 'Oui' : 'Non',
                    'taux' => ($row->order_tva_rate ?? 0) . '%'
                ],
                'status' => [
                    'code' => $row->status,
                    'label' => $status_info['label'],
                    'class' => $status_info['class']
                ],
                'validation' => [
                    'status' => $row->validation_status,
                    'notes' => $row->validation_notes,
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
     * Met à jour une livraison existante et ses articles
     *
     * @param array $data Les données de la livraison à mettre à jour
     * @return bool
     */
    public function update($data) {
        $this->db->trans_start();

        try {
            // Vérification que la livraison peut être modifiée
            $delivery = $this->getDeliveryWithItems($data['id']);
            if (!$delivery || $delivery['status'] != self::STATUS_PENDING) {
                throw new Exception('La livraison ne peut pas être modifiée dans son état actuel');
            }

            // Mise à jour des informations principales de la livraison
            $delivery_data = [
                'customer_id' => $data['customer'],
                'designation' => $data['designation'],
                'delivery_date' => date('Y-m-d', strtotime($data['delivery_date'])),

                'delivery_terms' => $data['delivery_term'],
                'delivery_location' => $data['delivery_location'],
                'apply_tva' => $data['apply_tva'] ? 1 : 0,
                'tva_rate' => $data['tva_rate'],
                'tva_amount' => $data['tva_amount'],
                'total_ht' => $data['total_ht'],
                'total_ttc' => $data['total_ttc'],
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->db->where('id', $data['id']);
            $this->db->update($this->table, $delivery_data);

            // Suppression des anciens articles
            $this->db->where('delivery_id', $data['id']);
            $this->db->delete($this->items_table);

            // Insertion des nouveaux articles
            foreach ($data['items'] as $position => $item) {
                $item_data = [
                    'delivery_id' => $data['id'],
                    'category_id' => $item['category_id'],
                    'item_id' => $item['item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'unit' => $item['unit'],
                    'line_total' => $item['line_total'],
                    'position' => $position + 1,
                ];
                $this->db->insert($this->items_table, $item_data);
            }

            $this->db->trans_complete();
            return $this->db->trans_status();

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Delivery Update Error: ' . $e->getMessage());
            return false;
        }
    }



    /**
     * Récupère les données de la livraison formatés pour l'impression
     *
     * @param int $delivery_id ID de la livraison
     * @return array|null Les données formatées de la livraison
     */
    public function getDeliveryForPrint($delivery_id)
    {
        $delivery = $this->getDeliveryWithItems($delivery_id);

        if (!$delivery) {
            return null;
        }

        // Formatage des données pour l'impression
        $print_data = [
            'delivery_number' => $delivery['delivery_number'],
            'delivery_date' => date('d/m/Y', strtotime($delivery['delivery_date'])),
            'customer' => [
                'name' => $delivery['customer_name'] . ' ' . $delivery['customer_last_name'],
                'email' => $delivery['customer_email'],
                'address' => $delivery['customer_address'] ?? ''
            ],
            'items' => [],
            'totals' => [
                'ht' => number_format($delivery['total_ht'], 2, ',', ' '),
                'tva' => number_format($delivery['tva_amount'], 2, ',', ' '),
                'ttc' => number_format($delivery['total_ttc'], 2, ',', ' ')
            ],
            'tva_info' => [
                'applied' => $delivery['apply_tva'] ? 'Oui' : 'Non',
                'rate' => $delivery['tva_rate'] . '%'
            ],
            'validation' => [
                'status' => $delivery['validation_status'],
                'date' => $delivery['validated_at'] ? date('d/m/Y', strtotime($delivery['validated_at'])) : null,
                'notes' => $delivery['validation_notes'],
                'rejection_reason' => $delivery['rejection_reason']
            ],
            'notes' => $delivery['notes']
        ];

        // Formatage des articles
        foreach ($delivery['items'] as $item) {
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
     * Valide une livraison
     *
     * @param int $delivery_id ID de la livraison
     * @param array $data Données de validation
     * @return bool
     */
    public function validateDelivery($delivery_id, $data)
    {
        $this->db->trans_start();

        try {
            // Mise à jour du statut de la livraison
            $this->db->where('id', $delivery_id);
            if (!$this->db->update($this->table, $data)) {
                throw new Exception('Erreur lors de la mise à jour du statut');
            }

            $this->db->trans_complete();
            return $this->db->trans_status();

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Delivery Model - Validate Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Rejette une livraison
     *
     * @param int $delivery_id ID de la livraison
     * @param array $data Données de rejet
     * @return bool
     */
    public function cancelDelivery($delivery_id, $data)
    {
        $this->db->trans_start();

        try {
            // Mise à jour du statut de la livraison
            $this->db->where('id', $delivery_id);
            if (!$this->db->update($this->table, $data)) {
                throw new Exception('Erreur lors de la mise à jour du statut');
            }

            $this->db->trans_complete();
            return $this->db->trans_status();

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Delivery Model - Cancel Error: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Rejette une livraison
     *
     * @param int $delivery_id ID de la livraison
     * @param array $data Données de rejet
     * @return bool
     */
    public function completeDelivery($delivery_id, $data)
    {
        $this->db->trans_start();

        try {
            // Mise à jour du statut de la livraison
            $this->db->where('id', $delivery_id);
            if (!$this->db->update($this->table, $data)) {
                throw new Exception('Erreur lors de la mise à jour du statut');
            }

            $this->db->trans_complete();
            return $this->db->trans_status();

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Delivery Model - Complete Error: ' . $e->getMessage());
            return false;
        }
    }



    /**
     * Récupère les informations de validation d'une livraison
     *
     * @param int $delivery_id ID de la livraison
     * @return array|null
     */
    public function getDeliveryValidationInfo($delivery_id)
    {
        $this->db->select('validation_status, validated_at, validated_by, validation_notes, rejected_at, rejected_by, rejection_reason');
        $this->db->where('id', $delivery_id);
        return $this->db->get($this->table)->row_array();
    }



    /**
     * Vérifie si une livraison est complétée
     *
     * @param int $delivery_id ID de la livraison
     * @return bool
     */
    public function isDeliveryCompleted($delivery_id)
    {
        $this->db->where('id', $delivery_id);
        $this->db->where('status', self::STATUS_DELIVERED);
        return $this->db->get($this->table)->num_rows() > 0;
    }

    /**
     * Vérifie si une livraison est annulée
     *
     * @param int $delivery_id ID de la livraison
     * @return bool
     */
    public function isDeliveryCancelled($delivery_id)
    {
        $this->db->where('id', $delivery_id);
        $this->db->where('status', self::STATUS_CANCELLED);
        return $this->db->get($this->table)->num_rows() > 0;
    }


    /**
     * Met à jour les quantités livrées (partielles ou totales)
     * @param int $delivery_id ID de la livraison
     * @param array $items Tableau des articles avec quantités livrées
     * @return bool True si succès, False si échec
     */
    public function partialDelivery($data) {
        $delivery_id = $data['id'];
        $items = $data['items'];

        // var_dump($delivery_id);
        // var_dump($items);
        // die();
        $this->db->trans_start();

        try {
            // 1. Mise à jour des quantités livrées
            $all_delivered = true;

            foreach ($items as $item) {
                // Vérification quantité valide
                if ($item['delivered_quantity'] < 0 || $item['delivered_quantity'] > $item['quantity']) {
                    throw new Exception("Quantité invalide pour l'article ".$item['item_id']);
                }

                // Mise à jour
                $this->db->where('delivery_id', $delivery_id)
                    ->where('item_id', $item['item_id'])
                    ->update($this->items_table, [
                        'delivered_quantity' => $item['delivered_quantity']
                    ]);

                // Vérifie si article complètement livré
                if ($item['delivered_quantity'] < $item['quantity']) {
                    $all_delivered = false;
                }
            }

            // 2. Détermination du statut
            $status = $all_delivered ? self::STATUS_DELIVERED : self::STATUS_PARTIALLY_DELIVERED;

            // 3. Mise à jour statut livraison
            $this->db->where('id', $delivery_id)
                ->update($this->table, [
                    'status' => $status,
                ]);

            $this->db->trans_complete();
            return $this->db->trans_status();

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Erreur mise à jour livraison: '.$e->getMessage());
            return false;
        }
    }
} 