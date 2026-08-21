<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Stockremoval_model extends MY_Model
{
    protected $table = 'stock_removals';
    protected $items_table = 'stock_removal_items';
    protected $stock_table = 'stock';

    public function __construct()
    {
        parent::__construct();
        $this->current_session = $this->setting_model->getCurrentSession();
    }

    /**
     * Ajoute une nouvelle entrée de stock avec ses articles
     * 
     * @param array $data Les données de l'entrée de stock
     * @return int|bool L'ID de l'entrée créée ou false en cas d'erreur
     */
    public function add_11($data)
    {
        $this->db->trans_start();

        try {
            // Préparation des données de l'entrée
            $removal_data = [
                'designation' => $data['designation'],
                'reference' => $this->generateReference(),
                'issue_date' => $data['issue_date'],
                'grand_total' => $data['grand_total'],
                'created_at' => date('Y-m-d H:i:s'),
            ];

            // Insertion de l'entrée
            $this->db->insert($this->table, $removal_data);
            $removal_id = $this->db->insert_id();

            if (!$removal_id) {
                throw new Exception('Erreur lors de la création de l\'entrée de stock');
            }

            // Insertion des articles
            foreach ($data['items'] as $item) {
                $item_data = [
                    'stock_removal_id'  => $removal_id,
                    'category_id'     => $item['category_id'],
                    'item_id'         => $item['item_id'],
                    'unit'            => $item['unit'],
                    'quantity'        => $item['quantity'],
                    'price'           => $item['price'],
                    'line_total'      => $item['line_total']
                ];

                if (!$this->db->insert($this->items_table, $item_data)) {
                    throw new Exception('Erreur lors de l\'ajout d\'un article');
                }

                // Mise à jour du stock
                if (!$this->updateStock($item['item_id'], $item['category_id'], $item['quantity'], $item['price'])) {
                    throw new Exception('Erreur lors de la mise à jour du stock');
                }
            }

            $this->db->trans_complete();
            return $removal_id;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Stockremoval Model Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Met à jour le stock d'un article
     * 
     * @param int $item_id ID de l'article
     * @param int $category_id ID de la catégorie
     * @param int $quantity Quantité à ajouter
     * @param float $price Prix unitaire
     * @return bool
     */
    /**
     * Ajoute une nouvelle sortie de stock avec ses articles
     */
    public function add($data)
    {
        $this->db->trans_start();

        try {
            // Préparation des données de la sortie
            $removal_data = [
                'origin' => $data['origin'],  // Changé de 'designation' à 'origin'
                'reason' => $data['reason'],  // Ajouté
                'reference' => $this->generateReference(),
                'issue_date' => $data['issue_date'],
                'grand_total' => $data['grand_total'],
                'created_at' => date('Y-m-d H:i:s'),
            ];

            // Insertion de la sortie
            $this->db->insert($this->table, $removal_data);
            $removal_id = $this->db->insert_id();

            if (!$removal_id) {
                throw new Exception('Erreur lors de la création de la sortie de stock');
            }

            // Insertion des articles
            foreach ($data['items'] as $item) {
                $item_data = [
                    'stock_removal_id' => $removal_id,
                    'category_id' => $item['category_id'],
                    'item_id' => $item['item_id'],
                    'unit' => $item['unit'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'line_total' => $item['line_total']
                ];

                if (!$this->db->insert($this->items_table, $item_data)) {
                    throw new Exception('Erreur lors de l\'ajout d\'un article');
                }

                // Mise à jour du stock (SOUSTRACTION pour une sortie)
                if (!$this->updateStock($item['item_id'], $item['category_id'], -$item['quantity'], $item['price'])) {
                    throw new Exception('Erreur lors de la mise à jour du stock');
                }
            }

            $this->db->trans_complete();
            return $removal_id;

        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Stockremoval Model Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Met à jour le stock d'un article pour une sortie
     */
    private function updateStock($item_id, $category_id, $quantity_change, $price)
    {
        // Vérifie si l'article existe déjà dans le stock
        $this->db->where('item_id', $item_id);
        $query = $this->db->get($this->stock_table);

        if ($query->num_rows() > 0) {
            $stock = $query->row();

            // Vérifier si le stock est suffisant
            if ($stock->current_quantity + $quantity_change < 0) {
                throw new Exception('Stock insuffisant pour l\'article ID: ' . $item_id);
            }

            $new_quantity = $stock->current_quantity + $quantity_change;

            // Pour les sorties, on ne modifie pas le prix moyen pondéré
            // On garde le même prix
            $this->db->where('item_id', $item_id);
            return $this->db->update($this->stock_table, [
                'current_quantity' => $new_quantity,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            // Ne devrait pas arriver pour une sortie (article non en stock)
            throw new Exception('Article non trouvé en stock: ' . $item_id);
        }
    }

    private function updateStock_11($item_id, $category_id, $quantity, $price)
    {
        // Vérifie si l'article existe déjà dans le stock
        $this->db->where('item_id', $item_id);
        $query = $this->db->get($this->stock_table);

        if ($query->num_rows() > 0) {
            // Met à jour le stock existant
            $stock = $query->row();
            $new_quantity = $stock->current_quantity + $quantity;
            
            // Calcul du nouveau prix moyen pondéré
            $total_value = ($stock->current_quantity * $stock->weighted_avg_price) + ($quantity * $price);
            $new_weighted_avg_price = $total_value / $new_quantity;

            $this->db->where('item_id', $item_id);
            return $this->db->update($this->stock_table, [
                'current_quantity' => $new_quantity,
                'weighted_avg_price' => $new_weighted_avg_price,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            // Crée une nouvelle entrée dans le stock
            return $this->db->insert($this->stock_table, [
                'item_id'          => $item_id,
                'initial_quantity' => $quantity,
                'current_quantity' => $quantity,
                'weighted_avg_price'=> $price,
                'status'            => 1,
                'created_at'        => date('Y-m-d H:i:s'),
            ]);
        }
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
     * Génère une référence unique pour une entrée de stock
     * Format: ES-YYYYMM-XXXX où XXXX est un numéro séquentiel
     * 
     * @return string
     */
    private function generateReference()
    {
        $prefix = 'ES';  // ES pour Entrée Stock
        $date = date('Ym');  // Format YYYYMM
        
        // Recherche la dernière référence pour ce mois
        $this->db->like('reference', $prefix . '-' . $date, 'after');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get($this->table);
        
        if ($query->num_rows() > 0) {
            // Extrait le numéro séquentiel de la dernière référence
            $last_ref = $query->row()->reference;
            $sequence = intval(substr($last_ref, -4)) + 1;
        } else {
            // Première entrée du mois
            $sequence = 1;
        }
        
        // Formate le numéro séquentiel sur 4 chiffres
        $sequence_padded = str_pad($sequence, 4, '0', STR_PAD_LEFT);
        
        return $prefix . '-' . $date . '-' . $sequence_padded;
    }


    public function getListData()
    {
        // Récupération des paramètres de DataTables
        $draw = intval($this->input->post('draw'));
        $start = intval($this->input->post('start'));
        $length = intval($this->input->post('length'));
        $search = $this->input->post('search')['value'];

        // Comptage total des enregistrements (sans filtre)
        $total_records = $this->db->count_all($this->table);

        // Construction de la requête principale
        $this->db->start_cache();
        
        $this->db->select('
            stock_removals.id,
            stock_removals.reference,
            stock_removals.origin,
            stock_removals.reason,
            stock_removals.issue_date,
            stock_removals.grand_total,
            stock_removals.created_at
        ');
        $this->db->from($this->table);
        
        // Ajout de la recherche si présente
        if($search) {
            $this->db->group_start();
            $this->db->like('stock_removals.origin', $search);
            $this->db->or_like('stock_removals.reference', $search);
            $this->db->group_end();
        }
        
        $this->db->stop_cache();

        // Comptage des enregistrements filtrés
        $filtered_records = $this->db->get()->num_rows();
        
        // Ajout du tri et de la pagination
        $this->db->order_by('stock_removals.created_at', 'DESC');
        if($length != -1) {
            $this->db->limit($length, $start);
        }

        // Exécution de la requête finale
        $query = $this->db->get();
        
        $this->db->flush_cache();
        
        // Préparation des données
        $data = [];
        foreach($query->result() as $row) {
            $data[] = [
                'id' => $row->id,
                'reference' => $row->reference,
                'origin' => $row->origin,
                'reason' => $row->reason,
                'date' => date('d/m/Y', strtotime($row->issue_date)),
                'montant' => number_format($row->grand_total, 2, ',', ' '),
                'actions' => "
                    <div class='btn-group'>
                        <a href='".base_url("admin/stockremoval/view/".$row->id)."' 
                           class='btn btn-default btn-xs' 
                           title='Voir'>
                           <i class='fa fa-eye'></i>
                        </a>
                    </div>"
            ];
        }

        // Préparation de la réponse
        $response = [
            'draw' => $draw,
            'recordsTotal' => $total_records,
            'recordsFiltered' => $filtered_records,
            'data' => $data
        ];

        return json_encode($response);
    }



    /**
     * Récupère une entrée de stock avec ses articles
     * 
     * @param int $removal_id ID de l'entrée de stock
     * @return array|null Les données de l'entrée et ses articles
     */
    public function getRemovalWithItems($removal_id)
    {
        // Récupération de l'entrée
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('id', $removal_id);
        $removal = $this->db->get()->row_array();

        if (!$removal) {
            return null;
        }

        // Récupération des articles
        $this->db->select('
            stock_removal_items.*,
            item_category.item_category as category_name,
            item.name as item_name,
            stock_removals.reference as removal_reference
        ');
        $this->db->from($this->items_table);
        $this->db->join('item_category', 'item_category.id = stock_removal_items.category_id');
        $this->db->join('item', 'item.id = stock_removal_items.item_id');
        $this->db->join('stock_removals', 'stock_removals.id = stock_removal_items.stock_removal_id');
        $this->db->where('stock_removal_id', $removal_id);
        $items = $this->db->get()->result_array();

        $removal['items'] = $items;
        return $removal;
    }
}
