<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Invoice_model extends MY_Model {

    protected $table = 'invoices';
    protected $items_table = 'invoice_items';
    protected $payments_table = 'payments';
    protected $current_session;

    // Constantes pour les statuts
    const STATUS_PENDING = 1;      // En attente
    const STATUS_PAID = 2;         // Payée
    const STATUS_PARTIAL = 3;      // Partiellement payée
    const STATUS_OVERDUE = 4;      // En retard
    const STATUS_CANCELLED = 5;    // Annulée

    public function __construct() {
        parent::__construct();
    }

    /**
     * Récupère la liste des factures
     *
     * @return string JSON
     */
    /**
     * Récupère les écritures comptables pour une facture
     *
     * @param int $invoice_id ID de la facture
     * @return array
     */


    public function getAccountingEntries($invoice_id = null)
    {
        $this->db->select('date, account, debit, credit, description');
        $this->db->from('accounting_entries');

        if ($invoice_id) {
            $this->db->where('invoice_id', $invoice_id);
        }

        $this->db->order_by('date', 'ASC');
        return $this->db->get()->result_array();
    }


    public function getListData() {
        $draw = intval($this->input->post('draw'));
        $start = intval($this->input->post('start'));
        $length = intval($this->input->post('length'));
        $search = $this->input->post('search')['value'];
        $status = $this->input->post('status'); // Récupération du statut

        // var_dump($this->input->post());
        // var_dump($status);
        // exit;

        $total_records = $this->db->count_all('invoices');

        $this->db->start_cache();

        $this->db->select('
            invoices.*,user_name,
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

        // Ajout du filtre sur le statut
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

            // var_dump($row);
            // die();
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
                'status' => [
                    'code' => $row->status,
                    'label' => $status_info['label'],
                    'class' => $status_info['class']
                ]
            ];
        }

        // var_dump($data);
        // die();
        return json_encode([
            'draw' => $draw,
            'recordsTotal' => $total_records,
            'recordsFiltered' => $filtered_records,
            'data' => $data
        ]);
    }

    /**
     * Récupère une facture avec ses articles
     *
     * @param int $id ID de la facture
     * @return array|bool
     */
    public function getInvoiceWithItems($id) {
        // var_dump($id);
        // die();
        // Récupération de la facture
        $this->db->select('invoices.*,clients.item_supplier as customer_name,
            clients.lastname as customer_last_name,
            clients.email as customer_email,
            clients.phone as customer_phone, clients.comptec, 
            clients.address as customer_address, deliveries.designation')
            ->from('invoices')
            ->join('clients', 'clients.id = invoices.customer_id')
            ->join('deliveries', 'deliveries.id = invoices.delivery_id', 'left')
            ->where('invoices.id', $id);

        $invoice = $this->db->get()->row_array();

        if (!$invoice) {
            return false;
        }

        // var_dump($invoice);
        // die();

        // Récupération des articles
        $this->db->select('invoice_items.*, item.name as item_name, item_category.item_category as category_name')
            ->from('invoice_items')
            ->join('item', 'item.id = invoice_items.item_id')
            ->join('item_category', 'item_category.id = invoice_items.category_id')
            ->where('invoice_id', $id)
            ->order_by('position', 'ASC');

        $invoice['items'] = $this->db->get()->result_array();

        // var_dump($invoice);
        // die();

        return $invoice;
    }


    /**
     * Ajoute une nouvelle facture
     *
     * @param array $data Données de la facture
     * @return int|bool ID de la facture ou false en cas d'échec
     */
    public function add($data)
    {
        // var_dump($data);
        // die();
        $this->db->trans_start();

        try {
            // Préparation des données de la facture
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

            // Insertion de la facture - la méthode insert() de CodeIgniter gère correctement le nom de la table
            $this->db->insert($this->table, $invoice_data);
            $invoice_id = $this->db->insert_id();

            if (!$invoice_id) {
                throw new Exception('Erreur lors de la création de la facture');
            }

            // Insertion des articles
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
     * Met à jour un devis existant et ses articles
     *
     * @param array $data Les données du devis à mettre à jour
     * @return bool
     */
    public function update($data) {
        // var_dump($data);
        // die();
        $this->db->trans_start();

        try {
            // Vérification que la facture peut être modifiée
            $invoice = $this->getInvoiceWithItems($data['id']);
            if (!$invoice || $invoice['status'] != self::STATUS_PENDING) {
                throw new Exception('La facture ne peut pas être modifiée dans son état actuel');
            }

            // Mise à jour des informations principales de le devis
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

            // Suppression des anciens articles
            $this->db->where('invoice_id', $data['id']);
            $this->db->delete($this->items_table);

            // Insertion des nouveaux articles
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
     * Supprime une facture
     *
     * @param int $id ID de la facture
     * @return bool
     */
    public function cancel($invoice_id, $data)
    {
        $this->db->trans_start();

        try {
            // Mise à jour du statut du devis
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
     *
     * @param int $id ID de la facture
     * @return bool
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
     *
     * @param array $data Données du paiement
     * @return bool
     */
    public function addPayment($data) {
        $this->db->trans_start();

        // var_dump($data);
        // die();

        try {
            // Insertion du paiement
            if (!$this->db->insert($this->payments_table, $data)) {
                throw new Exception('Erreur lors de l\'enregistrement du paiement');
            }

            // Récupération des informations de la facture
            $invoice = $this->db->select('total_ttc, remaining_amount')
                ->where('id', $data['invoice_id'])
                ->get($this->table)
                ->row();

            if (!$invoice) {
                throw new Exception('Facture introuvable');
            }

            // Calcul du nouveau montant restant
            $new_remaining_amount = $invoice->remaining_amount - $data['amount'];

            // Détermination du statut
            $new_status = 3; // Par défaut, paiement partiel
            if ($new_remaining_amount <= 0) {
                $new_status = 2; // Paiement complet
            }

            // Mise à jour du montant restant et du statut de la facture
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
     *
     * @param int $invoice_id ID de la facture
     * @return array
     */
    public function getPayments($invoice_id) {
        // var_dump($invoice_id);
        // die();
        return $this->db->select('payments.*')
            ->from($this->payments_table)
            ->where('invoice_id', $invoice_id)
            ->order_by('payment_date', 'DESC')
            ->get()
            ->result_array();
    }

    /**
     * Génère un numéro de facture unique
     * Format: FAC-YYYYMM-XXXX où XXXX est un numéro séquentiel
     *
     * @return string
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
}