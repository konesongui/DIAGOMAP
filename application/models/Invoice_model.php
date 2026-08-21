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
     * Récupère les métriques pour le tableau de bord des factures : totaux et compte FNE
     * @param int|null $entreprise_id
     * @return array
     */
    public function get_dashboard_metrics($entreprise_id = null) {
        $columns = $this->db->list_fields('invoices');
        $hasEntrepriseId = in_array('entreprise_id', $columns);

        if (empty($entreprise_id)) {
            $entreprise_id = $this->session->userdata('entreprise_id');
        }

        $this->db->select('
            COALESCE(SUM(total_ttc),0) as total_ttc,
            COALESCE(SUM(amount_paid),0) as total_paid,
            COALESCE(SUM(remaining_amount),0) as total_remaining,
            COUNT(*) as total_invoices
        ');
        $this->db->from('invoices');

        if ($hasEntrepriseId && !empty($entreprise_id) && (int)$entreprise_id > 0) {
            $this->db->where('entreprise_id', (int)$entreprise_id);
        }

        $result = $this->db->get()->row_array();

        // Compter les certifiées FNE
        $this->db->from('invoices');
        if ($hasEntrepriseId && !empty($entreprise_id) && (int)$entreprise_id > 0) {
            $this->db->where('entreprise_id', (int)$entreprise_id);
        }
        // Champ fne_reference / fne_token / fne_certified_at peuvent ne pas exister
        $fne_count = 0;
        $columns = $this->db->list_fields('invoices');
        $hasFneReference = in_array('fne_reference', $columns);
        $hasFneToken = in_array('fne_token', $columns);
        $hasFneCertifiedAt = in_array('fne_certified_at', $columns);
        if ($hasFneReference || $hasFneToken || $hasFneCertifiedAt) {
            $this->db->select('COUNT(*) as cnt', false);
            $this->db->group_start();
            if ($hasFneReference) $this->db->or_where('fne_reference !=', '');
            if ($hasFneToken) $this->db->or_where('fne_token !=', '');
            if ($hasFneCertifiedAt) $this->db->or_where('fne_certified_at IS NOT NULL', null, false);
            $this->db->group_end();
            $row = $this->db->get()->row_array();
            $fne_count = isset($row['cnt']) ? (int)$row['cnt'] : 0;
        }

        $non_fne = (int)$result['total_invoices'] - $fne_count;

        return [
            'total_ttc' => (float)$result['total_ttc'],
            'total_paid' => (float)$result['total_paid'],
            'total_remaining' => (float)$result['total_remaining'],
            'total_invoices' => (int)$result['total_invoices'],
            'fne_count' => (int)$fne_count,
            'non_fne_count' => max(0, (int)$non_fne)
        ];
    }

    /**
     * Récupère les métriques pour le tableau de bord avec filtres optionnels (AJAX)
     * @param int|null $entreprise_id
     * @param string|null $start_date YYYY-MM-DD
     * @param string|null $end_date YYYY-MM-DD
     * @param int|null $status
     * @param int|null $customer_id
     * @return array
     */
    public function get_dashboard_metrics_filtered($entreprise_id = null, $start_date = null, $end_date = null, $status = null, $customer_id = null) {
        // Réutilise la logique de get_dashboard_metrics mais avec paramètres
        $columns = $this->db->list_fields('invoices');
        $hasEntrepriseId = in_array('entreprise_id', $columns);

        if (empty($entreprise_id)) {
            $entreprise_id = $this->session->userdata('entreprise_id');
        }

        // Par défaut, période année courante si non fournie
        if (empty($start_date) && empty($end_date)) {
            $start_date = date('Y-01-01');
            $end_date = date('Y-12-31');
        }

        // Totaux
        $this->db->select("\n            COALESCE(SUM(total_ttc),0) as total_ttc,\n            COALESCE(SUM(amount_paid),0) as total_paid,\n            COALESCE(SUM(remaining_amount),0) as total_remaining,\n            COUNT(*) as total_invoices\n        ");
        $this->db->from('invoices');

        if ($hasEntrepriseId && !empty($entreprise_id) && (int)$entreprise_id > 0) {
            $this->db->where('entreprise_id', (int)$entreprise_id);
        }

        if (!empty($start_date) && !empty($end_date)) {
            if (in_array('invoice_date', $columns)) {
                $this->db->where('invoice_date >=', $start_date);
                $this->db->where('invoice_date <=', $end_date);
            } else {
                $this->db->where('created_at >=', $start_date);
                $this->db->where('created_at <=', $end_date);
            }
        }

        if (!empty($status)) {
            $this->db->where('status', $status);
        }

        if (!empty($customer_id)) {
            $this->db->where('customer_id', $customer_id);
        }

        $result = $this->db->get()->row_array();

        // FNE count with same filters
        $fne_count = 0;
        $hasFneReference = in_array('fne_reference', $columns);
        $hasFneToken = in_array('fne_token', $columns);
        $hasFneCertifiedAt = in_array('fne_certified_at', $columns);
        if ($hasFneReference || $hasFneToken || $hasFneCertifiedAt) {
            $this->db->select('COUNT(*) as cnt', false);
            $this->db->from('invoices');
            if ($hasEntrepriseId && !empty($entreprise_id) && (int)$entreprise_id > 0) {
                $this->db->where('entreprise_id', (int)$entreprise_id);
            }
            if (!empty($start_date) && !empty($end_date)) {
                if (in_array('invoice_date', $columns)) {
                    $this->db->where('invoice_date >=', $start_date);
                    $this->db->where('invoice_date <=', $end_date);
                } else {
                    $this->db->where('created_at >=', $start_date);
                    $this->db->where('created_at <=', $end_date);
                }
            }
            if (!empty($status)) {
                $this->db->where('status', $status);
            }
            if (!empty($customer_id)) {
                $this->db->where('customer_id', $customer_id);
            }

            $this->db->group_start();
            if ($hasFneReference) $this->db->or_where('fne_reference !=', '');
            if ($hasFneToken) $this->db->or_where('fne_token !=', '');
            if ($hasFneCertifiedAt) $this->db->or_where('fne_certified_at IS NOT NULL', null, false);
            $this->db->group_end();

            $row = $this->db->get()->row_array();
            $fne_count = isset($row['cnt']) ? (int)$row['cnt'] : 0;
        }

        $non_fne = (int)$result['total_invoices'] - $fne_count;

        return [
            'total_ttc' => (float)$result['total_ttc'],
            'total_paid' => (float)$result['total_paid'],
            'total_remaining' => (float)$result['total_remaining'],
            'total_invoices' => (int)$result['total_invoices'],
            'fne_count' => (int)$fne_count,
            'non_fne_count' => max(0, (int)$non_fne)
        ];
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
        $entreprise_id = $this->input->post('entreprise_id');

        // Récupérer l'entreprise_id de la session si non fourni
        if (empty($entreprise_id)) {
            $entreprise_id = $this->session->userdata('entreprise_id');
        }

        // Vérifier que la colonne entreprise_id existe
        $columns = $this->db->list_fields('invoices');
        $hasEntrepriseId = in_array('entreprise_id', $columns);

        // Total des enregistrements (avec filtre entreprise si disponible)
        if ($hasEntrepriseId && !empty($entreprise_id)) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $total_records = $this->db->count_all('invoices');

        $this->db->start_cache();
        $hasFneReference = in_array('fne_reference', $columns);
        $hasFneToken = in_array('fne_token', $columns);
        $hasFneBalance = in_array('fne_balance_sticker', $columns);
        $hasFneCertifiedAt = in_array('fne_certified_at', $columns);

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

        // Filtre par entreprise_id - APPLIQUÉ EN PREMIER
        if ($hasEntrepriseId && !empty($entreprise_id) && (int)$entreprise_id > 0) {
            $this->db->where('invoices.entreprise_id', (int)$entreprise_id);
        }

        // Filtre de recherche
        if($search) {
            $this->db->group_start();
            $this->db->like('invoices.invoice_number', $search);
            $this->db->or_like('clients.item_supplier', $search);
            $this->db->or_like('clients.email', $search);
            $this->db->group_end();
        }

        // Filtre par statut
        if($status !== '' &&  (int)$status > 0 ) {
            $this->db->where('invoices.status', $status);
        }

        $this->db->stop_cache();

        // Récupérer le nombre d'enregistrements filtrés
        $filtered_records = $this->db->get()->num_rows();

        // Appliquer le tri et la limite
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

            $fneReference = $hasFneReference ? (string)$row->fne_reference : '';
            $fneToken = $hasFneToken ? (string)$row->fne_token : '';
            $fneCertifiedAt = $hasFneCertifiedAt ? (string)$row->fne_certified_at : '';
            $fne_status = [
                'certified' => ($fneReference !== '' || $fneToken !== '' || !empty($fneCertifiedAt)),
                'reference' => $fneReference,
                'token' => $fneToken,
                'balance_sticker' => $hasFneBalance ? (int)$row->fne_balance_sticker : 0,
                'certified_at' => $fneCertifiedAt
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
    public function getInvoiceWithItems($invoice_id) {
        // Ajout du filtre entreprise_id pour la sécurité
        $entreprise_id = $this->session->userdata('entreprise_id');
        $columns = $this->db->list_fields('invoices');
        $hasEntrepriseId = in_array('entreprise_id', $columns);

        $this->db->select('invoices.*, clients.item_supplier as customer_name, clients.lastname as customer_last_name, clients.email as customer_email');
        $this->db->from('invoices');
        $this->db->join('clients', 'clients.id = invoices.customer_id');
        $this->db->where('invoices.id', $invoice_id);

        // Filtrer par entreprise_id pour la sécurité
        if ($hasEntrepriseId && !empty($entreprise_id)) {
            $this->db->where('invoices.entreprise_id', $entreprise_id);
        }

        $invoice = $this->db->get()->row_array();

        if ($invoice) {
            $this->db->select('
            invoice_items.*,
            item_category.item_category as category_name,
            item.name as item_name,
            services.name as service_name,
            services.duration as service_duration
        ');
            $this->db->from('invoice_items');
            $this->db->join('item_category', 'item_category.id = invoice_items.category_id', 'left');
            $this->db->join('item', 'item.id = invoice_items.item_id', 'left');
            $this->db->join('services', 'services.id = invoice_items.service_id', 'left');
            $this->db->where('invoice_items.invoice_id', $invoice_id);
            $this->db->order_by('invoice_items.position', 'ASC');
            $items = $this->db->get()->result_array();

            // Normalisation : pour les services, on utilise service_name
            foreach ($items as &$item) {
                if ($item['item_type'] == 'service') {
                    $item['item_name'] = $item['service_name'];
                    $item['category_name'] = null;
                }
            }
            $invoice['items'] = $items;
        }
        return $invoice;
    }


    public function getInvoiceWithItems_25($id) {
        // Ajout du filtre entreprise_id pour la sécurité
        $entreprise_id = $this->session->userdata('entreprise_id');
        $columns = $this->db->list_fields('invoices');
        $hasEntrepriseId = in_array('entreprise_id', $columns);

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

        // Filtrer par entreprise_id pour la sécurité
        if ($hasEntrepriseId && !empty($entreprise_id)) {
            $this->db->where('invoices.entreprise_id', $entreprise_id);
        }

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
            // Récupérer l'entreprise_id depuis la session si non fourni
            $entreprise_id = isset($data['entreprise_id']) ? $data['entreprise_id'] : $this->session->userdata('entreprise_id');

            $invoice_data = [
                'invoice_number'=> $this->generateInvoiceNumber($entreprise_id),
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

            // Ajout de l'entreprise_id
            if (!empty($entreprise_id)) {
                $invoice_data['entreprise_id'] = $entreprise_id;
            }

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
            // Vérifier que la facture appartient bien à l'entreprise
            $entreprise_id = $this->session->userdata('entreprise_id');
            $columns = $this->db->list_fields('invoices');
            $hasEntrepriseId = in_array('entreprise_id', $columns);

            if ($hasEntrepriseId && !empty($entreprise_id)) {
                $this->db->where('entreprise_id', $entreprise_id);
            }
            $this->db->where('id', $data['id']);
            $invoice_check = $this->db->get($this->table)->row();

            if (!$invoice_check) {
                throw new Exception('Facture introuvable ou accès non autorisé');
            }

            if ($invoice_check->status != self::STATUS_PENDING) {
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
        // Vérifier que la facture appartient bien à l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        $columns = $this->db->list_fields('invoices');
        $hasEntrepriseId = in_array('entreprise_id', $columns);

        if ($hasEntrepriseId && !empty($entreprise_id)) {
            $this->db->where('entreprise_id', $entreprise_id);
        }
        $this->db->where('id', (int)$invoice_id);
        $invoice = $this->db->get('invoices')->row();

        if (!$invoice) {
            return false;
        }

        $columns = $this->db->list_fields('invoices');
        $update = [];

        if (in_array('fne_certified', $columns)) {
            $update['fne_certified'] = 1;
        }
        if (in_array('fne_reference', $columns) && isset($fne_data['fne_reference'])) {
            $update['fne_reference'] = (string)$fne_data['fne_reference'];
        }
        if (in_array('fne_token', $columns) && isset($fne_data['fne_token'])) {
            $update['fne_token'] = (string)$fne_data['fne_token'];
        }
        if (in_array('fne_balance_sticker', $columns) && isset($fne_data['fne_balance_sticker'])) {
            $update['fne_balance_sticker'] = (int)$fne_data['fne_balance_sticker'];
        }
        if (in_array('fne_certified_at', $columns)) {
            $update['fne_certified_at'] = date('Y-m-d H:i:s');
        }
        if (in_array('fne_response_data', $columns) && isset($fne_data['fne_response_data'])) {
            $update['fne_response_data'] = (string)$fne_data['fne_response_data'];
        }

        if (empty($update)) {
            return true;
        }

        return (bool)$this->db->where('id', (int)$invoice_id)->update('invoices', $update);
    }

    /**
     * Récupère le statut FNE d'une facture (version temporaire)
     */
    public function getFNEStatus($invoice_id) {
        $columns = $this->db->list_fields('invoices');
        $selects = [];
        foreach (['fne_certified', 'fne_reference', 'fne_token', 'fne_balance_sticker', 'fne_certified_at'] as $field) {
            if (in_array($field, $columns)) {
                $selects[] = $field;
            }
        }
        if (empty($selects)) {
            return [
                'certified' => false,
                'reference' => '',
                'token' => '',
                'balance_sticker' => 0,
                'certified_at' => ''
            ];
        }

        $row = $this->db->select(implode(',', $selects))
            ->from('invoices')
            ->where('id', (int)$invoice_id)
            ->get()
            ->row_array();
        if (!$row) {
            return [
                'certified' => false,
                'reference' => '',
                'token' => '',
                'balance_sticker' => 0,
                'certified_at' => ''
            ];
        }

        return [
            'certified' => !empty($row['fne_reference']) || !empty($row['fne_token']) || !empty($row['fne_certified_at']),
            'reference' => isset($row['fne_reference']) ? $row['fne_reference'] : '',
            'token' => isset($row['fne_token']) ? $row['fne_token'] : '',
            'balance_sticker' => isset($row['fne_balance_sticker']) ? (int)$row['fne_balance_sticker'] : 0,
            'certified_at' => isset($row['fne_certified_at']) ? $row['fne_certified_at'] : ''
        ];
    }

    /**
     * Annule une facture
     */
    public function cancel($invoice_id, $data)
    {
        $this->db->trans_start();

        try {
            // Vérifier que la facture appartient bien à l'entreprise
            $entreprise_id = $this->session->userdata('entreprise_id');
            $columns = $this->db->list_fields('invoices');
            $hasEntrepriseId = in_array('entreprise_id', $columns);

            if ($hasEntrepriseId && !empty($entreprise_id)) {
                $this->db->where('entreprise_id', $entreprise_id);
            }
            $this->db->where('id', $invoice_id);
            $invoice_check = $this->db->get($this->table)->row();

            if (!$invoice_check) {
                throw new Exception('Facture introuvable ou accès non autorisé');
            }

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
        // Vérifier que la facture appartient bien à l'entreprise
        $entreprise_id = $this->session->userdata('entreprise_id');
        $columns = $this->db->list_fields('invoices');
        $hasEntrepriseId = in_array('entreprise_id', $columns);

        $this->db->select('remaining_amount')
            ->from('invoices')
            ->where('id', $id);

        if ($hasEntrepriseId && !empty($entreprise_id)) {
            $this->db->where('entreprise_id', $entreprise_id);
        }

        $invoice = $this->db->get()->row_array();

        return $invoice && $invoice['remaining_amount'] <= 0;
    }

    /**
     * Ajoute un paiement à une facture
     */
    public function addPayment($data) {
        $this->db->trans_start();

        try {
            // Vérifier que la facture appartient bien à l'entreprise
            $entreprise_id = $this->session->userdata('entreprise_id');
            $columns = $this->db->list_fields('invoices');
            $hasEntrepriseId = in_array('entreprise_id', $columns);

            if ($hasEntrepriseId && !empty($entreprise_id)) {
                $this->db->where('entreprise_id', $entreprise_id);
            }
            $this->db->where('id', $data['invoice_id']);
            $invoice_check = $this->db->get($this->table)->row();

            if (!$invoice_check) {
                throw new Exception('Facture introuvable ou accès non autorisé');
            }

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
    public function generateInvoiceNumber($entreprise_id = 0) {
        $prefix = 'FAC';
        $date = date('Ym');

        $this->db->like('invoice_number', $prefix . '-' . $date, 'after')
            ->order_by('id', 'DESC')
            ->limit(1);

        if ($entreprise_id > 0 && in_array('entreprise_id', $this->db->list_fields('invoices'))) {
            $this->db->where('entreprise_id', $entreprise_id);
        }

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

        // Filtre par entreprise_id (priorité au filtre passé en paramètre)
        $entreprise_id = isset($filters['entreprise_id']) ? $filters['entreprise_id'] : $this->session->userdata('entreprise_id');
        $columns = $this->db->list_fields('invoices');
        $hasEntrepriseId = in_array('entreprise_id', $columns);

        if ($hasEntrepriseId && !empty($entreprise_id)) {
            $this->db->where('invoices.entreprise_id', $entreprise_id);
        }

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

    /**
     * Récupère toutes les factures d’un client avec leurs articles et paiements
     * @param int $customer_id
     * @return array
     */
    public function getInvoicesByCustomer($customer_id) {
        // Ajout du filtre entreprise_id
        $entreprise_id = $this->session->userdata('entreprise_id');
        $columns = $this->db->list_fields('invoices');
        $hasEntrepriseId = in_array('entreprise_id', $columns);

        $this->db->select('invoices.*, 
        clients.item_supplier as customer_name,
        clients.lastname as customer_last_name,
        clients.email as customer_email,
        clients.phone as customer_phone,
        clients.address as customer_address')
            ->from('invoices')
            ->join('clients', 'clients.id = invoices.customer_id', 'left')
            ->where('invoices.customer_id', $customer_id);

        // Filtrer par entreprise_id
        if ($hasEntrepriseId && !empty($entreprise_id)) {
            $this->db->where('invoices.entreprise_id', $entreprise_id);
        }

        $this->db->order_by('invoices.invoice_date', 'DESC');

        $invoices = $this->db->get()->result_array();
        if (empty($invoices)) {
            return [];
        }

        // Charger les articles et paiements pour chaque facture
        foreach ($invoices as &$invoice) {
            // Articles
            $this->db->select('invoice_items.*, item.name as item_name, item_category.item_category as category_name')
                ->from('invoice_items')
                ->join('item', 'item.id = invoice_items.item_id')
                ->join('item_category', 'item_category.id = invoice_items.category_id')
                ->where('invoice_id', $invoice['id'])
                ->order_by('position', 'ASC');
            $invoice['items'] = $this->db->get()->result_array();

            // Paiements
            $this->db->select('*')
                ->from($this->payments_table)
                ->where('invoice_id', $invoice['id'])
                ->order_by('payment_date', 'DESC');
            $invoice['payments'] = $this->db->get()->result_array();
        }

        return $invoices;
    }

}