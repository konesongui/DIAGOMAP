<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Quotenostock_purchase_model extends CI_Model {

    protected $table = 'quotes_nostock';
    protected $items_table = 'quote_nostock_items';
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
     * Récupère la liste des devis au format JSON pour DataTables
     * 
     * @return string JSON
     */
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
            q.*,
            c.item_supplier as customer_name,
            c.lastname as customer_last_name,
            c.email as customer_email,
            c.phone as customer_phone,
            c.address as customer_address
        ');
        $this->db->from($this->table . ' q');
        $this->db->join('clients c', 'c.id = q.customer_id', 'left');
        
        if($search) {
            $this->db->group_start();
            $this->db->like('q.designation', $search);
            $this->db->or_like('q.quote_number', $search);
            $this->db->or_like('c.item_supplier', $search);
            $this->db->or_like('c.email', $search);
            $this->db->group_end();
        }

        // Ajout du filtre sur le statut
        if($status !== '' && (int)$status > 0) {
            $this->db->where('q.status', $status);
        }
        
        $this->db->stop_cache();

        $filtered_records = $this->db->get()->num_rows();
        
        $this->db->order_by('q.created_at', 'DESC');
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
                'payment_terms' => $row->payment_terms ?? 'Non défini',
                'delivery_terms' => $row->delivery_terms ?? 'Non défini',
                'delivery_location' => $row->delivery_location ?? 'Non défini',
                'customer' => [
                    'name' => $row->customer_name.' '.$row->customer_last_name,
                    'email' => $row->customer_email,
                    'phone' => $row->customer_phone,
                    'address' => $row->customer_address
                ],
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
     * Ajoute un nouveau devis
     * 
     * @param array $data Données du devis
     * @return int|bool ID du devis créé ou false en cas d'échec
     */
    public function add($data)
    {
        $this->db->trans_start();

        try {
            // Génération du numéro de devis
            $quote_number = $this->generateQuoteNumber();

            // Préparation des données du devis
            $quote_data = array(
                'quote_number' => $quote_number,
                'supplier_id' => $data['supplier_id'],
                'documents' => $data['documents'],
                'quote_date' => $data['quote_date'],
                'valid_until' => $data['valid_until'],
                'payment_terms' => $data['delivery_terms'],
                'delivery_terms' => $data['delivery_terms'],
                'delivery_location' => $data['delivery_location'],
                'apply_tva' => $data['apply_tva'],
                'tva_rate' => $data['tva_rate'],
                'tva_amount' => $data['tva_amount'],
                'total_ht' => $data['total_ht'],
                'total_ttc' => $data['total_ttc'],
                'status' => self::STATUS_PENDING,
                'created_at' => date('Y-m-d H:i:s')
            );

            // Insertion du devis
            $this->db->insert($this->table, $quote_data);
            $quote_id = $this->db->insert_id();

            if (!$quote_id) {
                throw new Exception('Erreur lors de la création du devis');
            }

            // Insertion des articles
            foreach ($data['items'] as $item) {
                $quote_item_data = array(
                    'quote_id' => $quote_id,
                    'category_name' => $item['category'], // Nom de la catégorie directement
                    'product_name' => $item['item'], // Nom du produit directement
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'unit' => $item['unit'],
                    'line_total' => $item['line_total']
                );

                if (!$this->db->insert($this->items_table, $quote_item_data)) {
                    throw new Exception('Erreur lors de l\'ajout d\'un article');
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Erreur lors de la transaction');
            }

            return $quote_id;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Quote Add Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère un devis avec ses articles
     * 
     * @param int $id ID du devis
     * @return array|bool Données du devis ou false si non trouvé
     */
    public function getQuoteWithItems($id)
    {
        // Récupération du devis
        $this->db->select('
            q.*,
            c.item_supplier as customer_name,
            c.lastname as customer_last_name,
            c.email as customer_email,
            c.phone as customer_phone,
            c.address as customer_address
        ');
        $this->db->from($this->table . ' q');
        $this->db->join('clients c', 'c.id = q.customer_id', 'left');
        $this->db->where('q.id', $id);
        $query = $this->db->get();
        
        if ($query->num_rows() == 0) {
            return false;
        }

        $quote = $query->row_array();

        // Récupération des articles
        $this->db->select('qi.*');
        $this->db->from($this->items_table . ' qi');
        $this->db->where('qi.quote_id', $id);
        $this->db->order_by('qi.id', 'ASC');
        $query = $this->db->get();
        
        $quote['items'] = $query->result_array();

        return $quote;
    }

    /**
     * Vérifie si un devis est validé
     * 
     * @param int $id ID du devis
     * @return bool
     */
    public function isQuoteValidated($id)
    {
        $this->db->where('id', $id);
        $this->db->where('status', self::STATUS_VALIDATED);
        return $this->db->count_all_results($this->table) > 0;
    }

    /**
     * Vérifie si un devis est rejeté
     * 
     * @param int $id ID du devis
     * @return bool
     */
    public function isQuoteRejected($id)
    {
        $this->db->where('id', $id);
        $this->db->where('status', self::STATUS_REJECTED);
        return $this->db->count_all_results($this->table) > 0;
    }

    /**
     * Valide un devis
     * 
     * @param int $id ID du devis
     * @param array $data Données de validation
     * @return bool
     */
    public function validateQuote($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Rejette un devis
     * 
     * @param int $id ID du devis
     * @param array $data Données de rejet
     * @return bool
     */
    public function rejectQuote($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    /**
     * Met à jour un devis
     * 
     * @param array $data Données du devis
     * @return bool
     */
    public function update($data)
    {
        $this->db->trans_start();

        try {
            // Mise à jour du devis
            $quote_data = array(
                'customer_id' => $data['customer_id'],
                'quote_date' => $data['quote_date'],
                'valid_until' => $data['valid_until'],
                'payment_terms' => $data['payment_terms'],
                'delivery_terms' => $data['delivery_terms'],
                'delivery_location' => $data['delivery_location'],
                'apply_tva' => $data['apply_tva']??0,
                'tva_rate' => $data['tva_rate'],
                'tva_amount' => $data['tva_amount'],
                'total_ht' => $data['total_ht'],
                'total_ttc' => $data['total_ttc'],
                'designation' => $data['designation']
            );

            $this->db->where('id', $data['id']);
            if (!$this->db->update($this->table, $quote_data)) {
                throw new Exception('Erreur lors de la mise à jour du devis');
            }

            // Suppression des anciens articles
            $this->db->where('quote_id', $data['id']);
            $this->db->delete($this->items_table);

            // Insertion des nouveaux articles
            foreach ($data['items'] as $item) {
                $quote_item_data = array(
                    'quote_id' => $data['id'],
                    'category_name' => $item['category'], // Nom de la catégorie directement
                    'product_name' => $item['item'], // Nom du produit directement
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'unit' => $item['unit'],
                    'line_total' => $item['line_total']
                );

                if (!$this->db->insert($this->items_table, $quote_item_data)) {
                    throw new Exception('Erreur lors de l\'ajout d\'un article');
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception('Erreur lors de la transaction');
            }

            return true;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Quote Update Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Génère un numéro unique pour un devis
     * Format: DEV-YYYYMM-XXXX où XXXX est un numéro séquentiel
     * 
     * @return string
     */
    private function generateQuoteNumber()
    {
        $prefix = 'DEV';  // DEV pour Devis
        $date = date('Ym');  // Format YYYYMM

        // Recherche le dernier numéro pour ce mois
        $this->db->like('quote_number', $prefix . '-' . $date, 'after');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get($this->table);

        if ($query->num_rows() > 0) {
            // Extrait le numéro séquentiel du dernier devis
            $last_ref = $query->row()->quote_number;
            $sequence = intval(substr($last_ref, -4)) + 1;
        } else {
            // Premier devis du mois
            $sequence = 1;
        }

        // Formate le numéro séquentiel sur 4 chiffres
        $sequence_padded = str_pad($sequence, 4, '0', STR_PAD_LEFT);

        return $prefix . '-' . $date . '-' . $sequence_padded;
    }
} 