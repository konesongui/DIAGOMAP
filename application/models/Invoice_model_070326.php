<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Invoice_model extends MY_Model {

    protected $table = 'invoices';
    protected $items_table = 'invoice_items';
    protected $payments_table = 'payments';

    // Constantes pour les statuts
    const STATUS_PENDING = 1;
    const STATUS_PAID = 2;
    const STATUS_PARTIAL = 3;
    const STATUS_OVERDUE = 4;
    const STATUS_CANCELLED = 5;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Récupère la liste des factures
     */
    public function getListData() {
        $draw = intval($this->input->post('draw'));
        $start = intval($this->input->post('start'));
        $length = intval($this->input->post('length'));
        $search = $this->input->post('search')['value'];
        $status = $this->input->post('status');

        $total_records = $this->db->count_all('invoices');

        $this->db->start_cache();

        $this->db->select('
            invoices.*, user_name,
            clients.item_supplier as customer_name,
            clients.lastname as customer_last_name,
            clients.email as customer_email,
            clients.phone as customer_phone,
            clients.address as customer_address
        ');
        $this->db->from('invoices');
        $this->db->join('clients', 'clients.id = invoices.customer_id', 'left');

        if($search) {
            $this->db->group_start();
            $this->db->like('invoices.invoice_number', $search);
            $this->db->or_like('clients.item_supplier', $search);
            $this->db->or_like('clients.email', $search);
            $this->db->group_end();
        }

        if($status !== '' &&  (int)$status > 0 ) {
            $this->db->where('invoices.status', $status);
        }

        $this->db->stop_cache();

        $filtered_records = $this->db->get()->num_rows();

        $this->db->order_by('invoices.created_at', 'DESC');
        if($length != -1) {
            $this->db->limit($length, $start);
        }

        $query = $this->db->get();
        $this->db->flush_cache();

        $status_labels = [
            self::STATUS_PENDING => ['label' => 'Non payée', 'class' => 'label-warning'],
            self::STATUS_PAID => ['label' => 'Payée', 'class' => 'label-success'],
            self::STATUS_PARTIAL => ['label' => 'Partiellement payée', 'class' => 'label-info'],
            self::STATUS_OVERDUE => ['label' => 'En retard', 'class' => 'label-danger'],
            self::STATUS_CANCELLED => ['label' => 'Annulée', 'class' => 'label-danger'],
        ];

        $data = [];
        foreach($query->result() as $row) {
            $status_info = isset($status_labels[$row->status]) ? $status_labels[$row->status] : ['label' => 'Inconnu', 'class' => 'label-default'];

            // Statut FNE temporaire
            $fne_status = [
                'certified' => false,
                'reference' => '',
                'token' => '',
                'balance_sticker' => 0,
                'certified_at' => ''
            ];

            $data[] = [
                'id' => $row->id,
                'invoice_number' => $row->invoice_number,
                'customer' => [
                    'name' => $row->customer_name.' '.$row->customer_last_name,
                    'email' => $row->customer_email,
                    'phone' => $row->customer_phone,
                    'address' => $row->customer_address
                ],
                'dates' => [
                    'invoice' => date('d/m/Y', strtotime($row->invoice_date)),
                    'due' => date('d/m/Y', strtotime($row->due_date)),
                    'creation' => date('d/m/Y', strtotime($row->created_at))
                ],
                'amount' => [
                    'ht' => number_format($row->total_ht, 2, ',', ' '),
                    'ttc' => number_format($row->total_ttc, 2, ',', ' '),
                    'remaining' => number_format($row->remaining_amount, 2, ',', ' '),
                    'paid' => number_format($row->amount_paid, 2, ',', ' '),
                    'tva_amount' => number_format($row->tva_amount, 2, ',', ' ')
                ],
                'user_name' => $row->user_name,
                'fne_status' => $fne_status,
                'status' => [
                    'code' => $row->status,
                    'label' => $status_info['label'],
                    'class' => $status_info['class']
                ]
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
     * Récupère une facture avec ses articles
     */
    public function getInvoiceWithItems($id) {
        $this->db->select('invoices.*, 
            clients.item_supplier as customer_name,
            clients.lastname as customer_last_name,
            clients.email as customer_email,
            clients.phone as customer_phone, 
            clients.comptec, 
            clients.address as customer_address,
            deliveries.designation')
            ->from('invoices')
            ->join('clients', 'clients.id = invoices.customer_id')
            ->join('deliveries', 'deliveries.id = invoices.delivery_id', 'left')
            ->where('invoices.id', $id);

        $invoice = $this->db->get()->row_array();

        if (!$invoice) {
            return false;
        }

        $this->db->select('invoice_items.*, item.name as item_name, item_category.item_category as category_name')
            ->from('invoice_items')
            ->join('item', 'item.id = invoice_items.item_id')
            ->join('item_category', 'item_category.id = invoice_items.category_id')
            ->where('invoice_id', $id)
            ->order_by('position', 'ASC');

        $invoice['items'] = $this->db->get()->result_array();

        return $invoice;
    }

    /**
     * Ajoute une nouvelle facture
     */
    public function add($data)
    {
        $this->db->trans_start();

        try {
            $invoice_data = [
                'invoice_number'=> $this->generateInvoiceNumber(),
                'customer_id'   => $data['customer_id'],
                'method'=> $data['method'],
                'invoice_date'  => date('Y-m-d', strtotime($data['invoice_date'])),
                'due_date'      => ((isset($data['due_date']) && !empty($data['due_date'])) ? date('Y-m-d', strtotime($data['due_date'])) : null),
                'notes'         => $data['notes'],
                'apply_tva'     => isset($data['apply_tva']) ? $data['apply_tva'] : 1,
                'tva_rate'      => isset($data['tva_rate']) ? $data['tva_rate'] : 20.00,
                'tva_amount'    => $data['tva_amount'],
                'total_ht'      => $data['total_ht'],
                'total_ttc'     => $data['total_ttc'],
                'remaining_amount' => $data['remaining_amount'],
                'amount_paid'   => $data['amount_paid'],
                'status'        => $data['status'],
                'created_at'    => $data['created_at'],
            ];

            $this->db->insert($this->table, $invoice_data);
            $invoice_id = $this->db->insert_id();

            if (!$invoice_id) {
                throw new Exception('Erreur lors de la création de la facture');
            }

            foreach ($data['items'] as $position => $item) {
                $item_data = [
                    'invoice_id'    => $invoice_id,
                    'category_id'   => $item['category_id'],
                    'item_id'       => $item['item_id'],
                    'quantity'      => $item['quantity'],
                    'unit_price'    => $item['unit_price'],
                    'unit'          => $item['unit'],
                    'line_total'    => $item['line_total'],
                    'position'      => $position + 1,
                ];

                if (!$this->db->insert($this->items_table, $item_data)) {
                    throw new Exception('Erreur lors de l\'ajout d\'un article à la facture');
                }
            }

            $this->db->trans_complete();
            return $invoice_id;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Invoice Model Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Met à jour une facture existante
     */
    public function update($data) {
        $this->db->trans_start();

        try {
            $invoice = $this->getInvoiceWithItems($data['id']);
            if (!$invoice || $invoice['status'] != self::STATUS_PENDING) {
                throw new Exception('La facture ne peut pas être modifiée dans son état actuel');
            }

            $invoice_data = [
                'customer_id'   => $data['customer_id'],
                'invoice_date'  => date('Y-m-d', strtotime($data['invoice_date'])),
                'due_date'      => ((isset($data['due_date']) && !empty($data['due_date'])) ? date('Y-m-d', strtotime($data['due_date'])) : null),
                'method'  => $data['method'],
                'apply_tva'       => $data['apply_tva'] ? 1 : 0,
                'tva_amount'      => $data['tva_amount'],
                'total_ht'          => $data['total_ht'],
                'total_ttc'         => $data['total_ttc'],
                'remaining_amount'  => $data['remaining_amount'],
                'amount_paid'       => $data['amount_paid'],
                'updated_at'        => date('Y-m-d H:i:s')
            ];

            $this->db->where('id', $data['id']);
            $this->db->update($this->table, $invoice_data);

            $this->db->where('invoice_id', $data['id']);
            $this->db->delete($this->items_table);

            foreach ($data['items'] as $position => $item) {
                $item_data = [
                    'invoice_id'    => $data['id'],
                    'category_id'   => $item['category_id'],
                    'item_id'       => $item['item_id'],
                    'quantity'      => $item['quantity'],
                    'unit_price'    => $item['unit_price'],
                    'unit'          => $item['unit'],
                    'line_total'    => $item['line_total'],
                    'position'      => $position + 1,
                ];
                $this->db->insert($this->items_table, $item_data);
            }

            $this->db->trans_complete();
            return $this->db->trans_status();

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Invoice Update Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Met à jour le statut FNE d'une facture (version temporaire)
     */
    public function updateFNEStatus($invoice_id, $fne_data) {
        // Version temporaire - ne fait rien pour l'instant
        log_message('info', 'FNE Update requested for invoice: ' . $invoice_id);
        return true;
    }

    /**
     * Récupère le statut FNE d'une facture (version temporaire)
     */
    public function getFNEStatus($invoice_id) {
        // Retourne un statut FNE par défaut
        return [
            'certified' => false,
            'reference' => '',
            'token' => '',
            'balance_sticker' => 0,
            'certified_at' => ''
        ];
    }

    /**
     * Annule une facture
     */
    public function cancel($invoice_id, $data)
    {
        $this->db->trans_start();

        try {
            $this->db->where('id', $invoice_id);
            if (!$this->db->update($this->table, $data)) {
                throw new Exception('Erreur lors de la mise à jour du statut');
            }

            $this->db->trans_complete();
            return $this->db->trans_status();

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Invoice Model - Cancel Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifie si une facture est payée
     */
    public function isPaid($id) {
        $invoice = $this->db->select('remaining_amount')
            ->from('invoices')
            ->where('id', $id)
            ->get()
            ->row_array();

        return $invoice && $invoice['remaining_amount'] <= 0;
    }

    /**
     * Ajoute un paiement à une facture
     */
    /**
     * Ajoute un paiement à une facture
     */
    public function addPayment($data) {
        $this->db->trans_start();

        try {
            // Récupérer la liste des colonnes de la table payments
            $columns = $this->db->list_fields($this->payments_table);

            // Construire les données seulement pour les colonnes qui existent
            $payment_data = [];

            if (in_array('invoice_id', $columns)) {
                $payment_data['invoice_id'] = $data['invoice_id'];
            }
            if (in_array('amount', $columns)) {
                $payment_data['amount'] = $data['amount'];
            }
            if (in_array('payment_date', $columns)) {
                $payment_data['payment_date'] = $data['payment_date'];
            }
            if (in_array('method', $columns)) {
                $payment_data['method'] = $data['method'];
            }
            if (in_array('source_type', $columns) && isset($data['source_type'])) {
                $payment_data['source_type'] = $data['source_type'];
            }
            if (in_array('source_id', $columns) && isset($data['source_id'])) {
                $payment_data['source_id'] = $data['source_id'];
            }
            if (in_array('reference', $columns)) {
                $payment_data['reference'] = $data['reference'] ?? null;
            }
            if (in_array('notes', $columns)) {
                $payment_data['notes'] = $data['notes'] ?? null;
            }

            // Gérer le champ de date de création (différents noms possibles)
            $created_at_fields = ['created_at', 'created_date', 'date_created', 'created'];
            $found = false;
            foreach ($created_at_fields as $field) {
                if (in_array($field, $columns)) {
                    $payment_data[$field] = $data['created_at'] ?? date('Y-m-d H:i:s');
                    $found = true;
                    break;
                }
            }

            // Si aucun champ de date n'existe, ajouter le champ par défaut
            if (!$found && in_array('created_at', $columns)) {
                $payment_data['created_at'] = $data['created_at'] ?? date('Y-m-d H:i:s');
            }

            if (empty($payment_data)) {
                throw new Exception('Aucune colonne valide pour l\'insertion');
            }

            if (!$this->db->insert($this->payments_table, $payment_data)) {
                throw new Exception('Erreur lors de l\'enregistrement du paiement');
            }

            $invoice = $this->db->select('total_ttc, remaining_amount')
                ->where('id', $data['invoice_id'])
                ->get($this->table)
                ->row();

            if (!$invoice) {
                throw new Exception('Facture introuvable');
            }

            $new_remaining_amount = $invoice->remaining_amount - $data['amount'];
            $new_status = 3; // Partiellement payé
            if ($new_remaining_amount <= 0) {
                $new_status = 2; // Payée
            }

            $this->db->set('remaining_amount', $new_remaining_amount)
                ->set('amount_paid', 'amount_paid + ' . $data['amount'], false)
                ->set('status', $new_status)
                ->where('id', $data['invoice_id']);

            if (!$this->db->update($this->table)) {
                throw new Exception('Erreur lors de la mise à jour de la facture');
            }

            $this->db->trans_complete();
            return $this->db->trans_status();

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Payment Add Error: ' . $e->getMessage());
            return false;
        }
    }

    public function addPayment_20($data) {
        $this->db->trans_start();

        try {
            if (!$this->db->insert($this->payments_table, $data)) {
                throw new Exception('Erreur lors de l\'enregistrement du paiement');
            }

            $invoice = $this->db->select('total_ttc, remaining_amount')
                ->where('id', $data['invoice_id'])
                ->get($this->table)
                ->row();

            if (!$invoice) {
                throw new Exception('Facture introuvable');
            }

            $new_remaining_amount = $invoice->remaining_amount - $data['amount'];
            $new_status = 3;
            if ($new_remaining_amount <= 0) {
                $new_status = 2;
            }

            $this->db->set('remaining_amount', $new_remaining_amount)
                ->set('amount_paid', 'amount_paid + ' . $data['amount'], false)
                ->set('status', $new_status)
                ->where('id', $data['invoice_id']);

            if (!$this->db->update($this->table)) {
                throw new Exception('Erreur lors de la mise à jour de la facture');
            }

            $this->db->trans_complete();
            return $this->db->trans_status();

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Payment Add Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère les paiements d'une facture
     */
    public function getPayments($invoice_id) {
        return $this->db->select('payments.*')
            ->from($this->payments_table)
            ->where('invoice_id', $invoice_id)
            ->order_by('payment_date', 'DESC')
            ->get()
            ->result_array();
    }

    /**
     * Génère un numéro de facture unique
     */
    public function generateInvoiceNumber() {
        $prefix = 'FAC';
        $date = date('Ym');

        $this->db->like('invoice_number', $prefix . '-' . $date, 'after')
            ->order_by('id', 'DESC')
            ->limit(1);

        $query = $this->db->get('invoices');

        if ($query->num_rows() > 0) {
            $last_ref = $query->row()->invoice_number;
            $sequence = intval(substr($last_ref, -4)) + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . '-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Récupère les factures avec leur statut de relance
     */
    public function getInvoicesWithReminderStatus($filters = [])
    {
        $this->db->select('
        invoices.*,
        clients.item_supplier as customer_name,
        clients.lastname as customer_last_name,
        clients.email as customer_email,
        clients.phone as customer_phone,
        (SELECT COUNT(*) FROM invoice_reminders WHERE invoice_id = invoices.id) as reminder_count,
        (SELECT MAX(sent_at) FROM invoice_reminders WHERE invoice_id = invoices.id) as last_reminder
    ')
            ->from('invoices')
            ->join('clients', 'clients.id = invoices.customer_id', 'left');

        if (!empty($filters['status'])) {
            $this->db->where('invoices.status', $filters['status']);
        }

        if (!empty($filters['has_email'])) {
            $this->db->where('clients.email IS NOT NULL');
            $this->db->where('clients.email !=', '');
        }

        if (!empty($filters['due_before'])) {
            $this->db->where('invoices.due_date <', $filters['due_before']);
        }

        $this->db->order_by('invoices.due_date', 'ASC');

        return $this->db->get()->result_array();
    }

    // Dans votre model Setting_model, ajoutez :
    public function get_fne_settings()
    {
        $settings = $this->db->get('settings')->row();
        return [
            'nif' => $settings->nif ?? '',
            'rc' => $settings->rc ?? '',
            'company_name' => $settings->name ?? '',
            'address' => $settings->address ?? '',
            'phone' => $settings->phone ?? '',
            'email' => $settings->email ?? ''
        ];
    }
}