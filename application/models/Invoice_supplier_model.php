<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Invoice_supplier_model extends MY_Model {

    protected $table = 'invoices_supplier';
    protected $items_table = 'invoice_supplier_items';
    protected $payments_table = 'payments_supplier';
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
        $status = $this->input->post('status');
        $entreprise_id = $this->input->post('entreprise_id');

        // Récupérer l'entreprise_id de la session si non fourni
        if (empty($entreprise_id)) {
            $entreprise_id = $this->session->userdata('entreprise_id');
        }

        // Vérifier que la colonne entreprise_id existe
        $columns = $this->db->list_fields('invoices_supplier');
        $hasEntrepriseId = in_array('entreprise_id', $columns);

        // Total des enregistrements (avec filtre entreprise si disponible)
        if ($hasEntrepriseId && !empty($entreprise_id)) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $total_records = $this->db->count_all('invoices_supplier');

        $this->db->start_cache();

        $this->db->select('
            invoices_supplier.*,user_name,
            item_supplier.item_supplier as customer_name,
            item_supplier.lastname as customer_last_name,
            item_supplier.email as customer_email,
            item_supplier.phone as customer_phone,
            item_supplier.address as customer_address
        ');
        $this->db->from('invoices_supplier');
        $this->db->join('item_supplier', 'item_supplier.id = invoices_supplier.customer_id', 'left');

        // Filtre par entreprise_id - APPLIQUÉ EN PREMIER
        if ($hasEntrepriseId && !empty($entreprise_id) && (int)$entreprise_id > 0) {
            $this->db->where('invoices_supplier.entreprise_id', (int)$entreprise_id);
        }

        if($search) {
            $this->db->group_start();
            $this->db->like('invoices_supplier.invoice_number', $search);
            $this->db->or_like('item_supplier.item_supplier', $search);
            $this->db->or_like('item_supplier.email', $search);
            $this->db->group_end();
        }

        // Ajout du filtre sur le statut
        if($status !== '' &&  (int)$status > 0 ) {
            $this->db->where('invoices_supplier.status', $status);
        }

        $this->db->stop_cache();

        $filtered_records = $this->db->get()->num_rows();

        $this->db->order_by('invoices_supplier.created_at', 'DESC');
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
        // Ajout du filtre entreprise_id pour la sécurité
        $entreprise_id = $this->session->userdata('entreprise_id');
        $columns = $this->db->list_fields('invoices_supplier');
        $hasEntrepriseId = in_array('entreprise_id', $columns);

        // Récupération de la facture
        $this->db->select('invoices_supplier.*,item_supplier.item_supplier as customer_name,
            item_supplier.lastname as customer_last_name,
            item_supplier.email as customer_email,
            item_supplier.phone as customer_phone, item_supplier.comptec, 
            item_supplier.address as customer_address, deliveries_supplier.designation')
            ->from('invoices_supplier')
            ->join('item_supplier', 'item_supplier.id = invoices_supplier.customer_id')
            ->join('deliveries_supplier', 'deliveries_supplier.id = invoices_supplier.delivery_id', 'left')
            ->where('invoices_supplier.id', $id);

        // Filtrer par entreprise_id pour la sécurité
        if ($hasEntrepriseId && !empty($entreprise_id)) {
            $this->db->where('invoices_supplier.entreprise_id', $entreprise_id);
        }

        $invoice = $this->db->get()->row_array();

        if (!$invoice) {
            return false;
        }

        // Récupération des articles
        $this->db->select('invoice_supplier_items.*, item.name as item_name, item_category.item_category as category_name')
            ->from('invoice_supplier_items')
            ->join('item', 'item.id = invoice_supplier_items.item_id')
            ->join('item_category', 'item_category.id = invoice_supplier_items.category_id')
            ->where('invoice_id', $id)
            ->order_by('position', 'ASC');

        $invoice['items'] = $this->db->get()->result_array();

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
        $this->db->trans_start();

        try {
            // Récupérer l'entreprise_id depuis la session si non fourni
            $entreprise_id = isset($data['entreprise_id']) ? $data['entreprise_id'] : $this->session->userdata('entreprise_id');

            // Préparation des données de la facture
            $invoice_data = [
                'invoice_number'=> $this->generateInvoiceNumber(),
                'customer_id'   => $data['customer_id'],
                'method'        => $data['method'],
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

            // Ajout de l'entreprise_id
            if (!empty($entreprise_id)) {
                $invoice_data['entreprise_id'] = $entreprise_id;
            }

            // Insertion de la facture
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
        $this->db->trans_start();

        try {
            // Vérifier que la facture appartient bien à l'entreprise
            $entreprise_id = $this->session->userdata('entreprise_id');
            $columns = $this->db->list_fields('invoices_supplier');
            $hasEntrepriseId = in_array('entreprise_id', $columns);

            if ($hasEntrepriseId && !empty($entreprise_id)) {
                $this->db->where('entreprise_id', $entreprise_id);
            }
            $this->db->where('id', $data['id']);
            $invoice_check = $this->db->get($this->table)->row();

            if (!$invoice_check) {
                throw new Exception('Facture introuvable ou accès non autorisé');
            }

            // Vérification que la facture peut être modifiée
            if ($invoice_check->status != self::STATUS_PENDING) {
                throw new Exception('La facture ne peut pas être modifiée dans son état actuel');
            }

            // Mise à jour des informations principales de la facture
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
            // Vérifier que la facture appartient bien à l'entreprise
            $entreprise_id = $this->session->userdata('entreprise_id');
            $columns = $this->db->list_fields('invoices_supplier');
            $hasEntrepriseId = in_array('entreprise_id', $columns);

            if ($hasEntrepriseId && !empty($entreprise_id)) {
                $this->db->where('entreprise_id', $entreprise_id);
            }
            $this->db->where('id', $invoice_id);
            $invoice_check = $this->db->get($this->table)->row();

            if (!$invoice_check) {
                throw new Exception('Facture introuvable ou accès non autorisé');
            }

            // Mise à jour du statut de la facture
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
        // Vérifier que la facture appartient bien à l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        $columns = $this->db->list_fields('invoices_supplier');
        $hasEntrepriseId = in_array('entreprise_id', $columns);

        $this->db->select('remaining_amount')
            ->from('invoices_supplier')
            ->where('id', $id);

        if ($hasEntrepriseId && !empty($entreprise_id)) {
            $this->db->where('entreprise_id', $entreprise_id);
        }

        $invoice = $this->db->get()->row_array();

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

        try {
            // Vérifier que la facture appartient bien à l'entreprise
            $entreprise_id = $this->session->userdata('entreprise_id');
            $columns = $this->db->list_fields('invoices_supplier');
            $hasEntrepriseId = in_array('entreprise_id', $columns);

            if ($hasEntrepriseId && !empty($entreprise_id)) {
                $this->db->where('entreprise_id', $entreprise_id);
            }
            $this->db->where('id', $data['invoice_id']);
            $invoice_check = $this->db->get($this->table)->row();

            if (!$invoice_check) {
                throw new Exception('Facture introuvable ou accès non autorisé');
            }

            // Récupérer la liste des colonnes de la table payments_supplier
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

            // Gérer le champ de date de création
            $created_at_fields = ['created_at', 'created_date', 'date_created', 'created'];
            $found = false;
            foreach ($created_at_fields as $field) {
                if (in_array($field, $columns)) {
                    $payment_data[$field] = $data['created_at'] ?? date('Y-m-d H:i:s');
                    $found = true;
                    break;
                }
            }

            if (!$found && in_array('created_at', $columns)) {
                $payment_data['created_at'] = $data['created_at'] ?? date('Y-m-d H:i:s');
            }

            if (empty($payment_data)) {
                throw new Exception('Aucune colonne valide pour l\'insertion');
            }

            // Insertion du paiement
            if (!$this->db->insert($this->payments_table, $payment_data)) {
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
        return $this->db->select('payments_supplier.*')
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

        $query = $this->db->get('invoices_supplier');

        if ($query->num_rows() > 0) {
            $last_ref = $query->row()->invoice_number;
            $sequence = intval(substr($last_ref, -4)) + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . '-' . $date . '-' . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}