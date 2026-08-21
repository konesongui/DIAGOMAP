<?php
    // Set all the form data
    $formID     = 'deliveryEditForm';
    $submitBtn  = 'submitEditBtn';

    // var_dump($itemList);
    // var_dump($delivery);
    // exit;
?>

<style type="text/css">
    .repeater-item {
        border: 1px solid #eee;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 4px;
        background: #f9f9f9;
    }
    .remove-item {
        cursor: pointer;
        color: #fff;
    }
    .availability {
        margin-top: 5px;
        font-size: 12px;
        color: #666;
    }
    .total-price {
        font-weight: bold;
        margin-top: 5px;
    }
    .item-select {
        font-weight: 500;
        color: #333;
    }
    .item-select option {
        padding: 5px;
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-building-o"></i>Inventaire</h1>
    </section>
    
    <section class="content">
        <div class="row">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Modifier le devis <?php echo $delivery['delivery_number']; ?></h3>
                </div>
                
                <form action="<?php echo site_url('admin/deliveryitem/update') ?>" id="<?= $formID; ?>" method="post" accept-charset="utf-8">
                    <div class="box-body">
                        <input type="hidden" name="id" value="<?php echo $delivery['id']; ?>">
                        <div class="row">
                            <?php if ($this->session->flashdata('msg')) { ?>
                                <div class="alert alert-info"><?php echo $this->session->flashdata('msg') ?></div>
                            <?php } ?>
                            <?php if (isset($error_message)) { ?>
                                <div class='alert alert-danger'><?php echo $error_message ?></div>
                            <?php } ?>
                            
                            <?php echo $this->customlib->getCSRF(); ?>
                            
                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail1">Client</label><small class="req"> *</small>
                                <select class="form-control" name="customer">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php foreach ($clients as $client) { ?>
                                        <option value="<?php echo $client['id']; ?>" <?php echo ((int)$client['id'] == (int)$delivery['customer_id']) ? 'selected' : ''; ?>>
                                            <?php echo $client['item_supplier'] .' ' . $client['lastname']. ' (' . $client['phone'] . ')'; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <span class="text-danger"><?php echo form_error('customer'); ?></span>
                            </div>

                            <!-- <div class="form-group col-md-3">
                                <label>Désignation <small class="req">*</small></label>
                                <input name="designation" type="text" class="form-control" value="<?php echo $delivery['designation']; ?>" />
                            </div> -->

                            <div class="form-group col-md-4">
                                <label>Date <small class="req">*</small></label>
                                <input id="delivery_date" name="delivery_date" type="text" class="form-control date" value="<?php echo date('d/m/Y', strtotime($delivery['delivery_date'])); ?>" readonly />
                            </div>

                            <div class="form-group col-md-4">
                                <label>Date de validité <small class="req">*</small></label>
                                <input id="valid_until" name="valid_until" type="text" class="form-control date" value="<?php echo date('d/m/Y', strtotime($delivery['valid_until'])); ?>" readonly />
                            </div>
                            
                            <div class="clearfix"></div>
                            <hr>

                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Termes de paiement <small class="req">*</small></label>
                                    <textarea name="payment_terms" class="form-control"><?php echo $delivery['payment_terms']; ?></textarea>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Termes de livraison <small class="req">*</small></label>
                                    <textarea name="delivery_terms" class="form-control"><?php echo $delivery['delivery_terms']; ?></textarea>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Lieu de livraison <small class="req">*</small></label>
                                    <textarea name="delivery_location" class="form-control"><?php echo $delivery['delivery_location']; ?></textarea>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="payment_method">Méthode de paiement</label>
                                    <select class="form-control select2" id="payment_method" name="payment_method">
                                        <option value="">Sélectionner...</option>
                                        <option value="cash" <?php echo ($delivery['payment_method'] == 'cash') ? 'selected' : ''; ?>>Espèces</option>
                                        <option value="check" <?php echo ($delivery['payment_method'] == 'check') ? 'selected' : ''; ?>>Chèque</option>
                                        <option value="bank_transfer" <?php echo ($delivery['payment_method'] == 'bank_transfer') ? 'selected' : ''; ?>>Virement</option>
                                        <option value="card" <?php echo ($delivery['payment_method'] == 'card') ? 'selected' : ''; ?>>Carte bancaire</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Objet <small class="req">*</small></label>
                                    <input id="objet" name="objet" type="text" class="form-control date" value="<?php echo $delivery['objet']; ?>" />
                                </div>
                            </div>

                            <div class="clearfix"></div>
                            <hr>

                            <div class="col-md-12">
                                <h4>Articles du devis</h4>
                                <div id="items-container">
                                    <?php foreach ($delivery['items'] as $item) { ?>
                                        <div class="repeater-item">
                                            <div class="row">
                                                <div class="form-group col-md-3">
                                                    <label>Catégorie <small class="req">*</small></label>
                                                    <select name="item_category_id[]" class="form-control item-category" required>
                                                        <option value="">Sélectionner</option>
                                                        <?php foreach ($itemcatlist as $category) { ?>
                                                            <option value="<?php echo $category['id']; ?>" <?php echo ((int)$category['id'] == (int)$item['category_id']) ? 'selected' : ''; ?>>
                                                                <?php echo $category['item_category']; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label>Article <small class="req">*</small></label>
                                                    <select name="item_id[]" class="form-control item-select" required>
                                                        <?php foreach ($itemList as $list) { ?>
                                                            <option value="<?php echo $list['id']; ?>" <?php echo ((int)$list['id'] == (int)$item['item_id']) ? 'selected' : ''; ?>>
                                                                <?php echo $list['name']; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>Unité</label>
                                                    <input type="text" name="unit[]" class="form-control unit" value="<?php echo $item['unit']; ?>" readonly>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label>Quantité <small class="req">*</small></label>
                                                    <input type="number" name="quantity[]" class="form-control quantity" min="1" value="<?php echo $item['quantity']; ?>" required>
                                                    <div class="availability">
                                                        Stock disponible: <span class="available-qty">0</span>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label>Prix unitaire</label>
                                                    <input type="number" name="price[]" class="form-control price" min="0" step="0.01" value="<?php echo $item['unit_price']; ?>">
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>Total</label>
                                                    <div class="total-price"><?php echo number_format($item['line_total'], 2); ?></div>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>&nbsp;</label>
                                                    <button type="button" class="btn btn-danger btn-sm remove-item">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <button type="button" id="add-item" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus"></i> Ajouter un article
                                </button>
                            </div>
                        </div>       
                    </div>

                    <div class="box-footer">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" id="apply_tva" name="apply_tva" value="1" <?php echo ($delivery['apply_tva']) ? 'checked' : ''; ?>> 
                                        Appliquer la TVA (<?php echo $delivery['tva_rate']; ?>%)
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <table class="table table-bordered">
                                    <tr>
                                        <td>Total HT:</td>
                                        <td class="text-right"><span id="total_ht"><?php echo number_format($delivery['total_ht'], 2); ?></span></td>
                                        <input type="hidden" name="total_ht" id="totalHT" value="<?php echo $delivery['total_ht']; ?>">
                                    </tr>
                                    <tr class="tva-row" style="<?php echo ($delivery['apply_tva']) ? '' : 'display:none;'; ?>">
                                        <td>TVA (<?php echo $delivery['tva_rate']; ?>%):</td>
                                        <td class="text-right"><span id="tva_amount"><?php echo number_format($delivery['tva_amount'], 2); ?></span></td>
                                        <input type="hidden" name="tva_amount" id="tvaAmount" value="<?php echo $delivery['tva_amount']; ?>">
                                        <input type="hidden" name="tva_rate" value="<?php echo $delivery['tva_rate']; ?>">
                                    </tr>
                                    <tr class="ttc-row">
                                        <td><strong>Total TTC:</strong></td>
                                        <td class="text-right"><strong><span id="total_ttc"><?php echo number_format($delivery['total_ttc'], 2); ?></span></strong></td>
                                        <input type="hidden" name="total_ttc" id="totalTTC" value="<?php echo $delivery['total_ttc']; ?>">
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <a href="<?php echo base_url('admin/deliveryitem'); ?>" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> Retour à la liste
                        </a>
                        <button type="submit" id="<?= $submitBtn; ?>" class="btn btn-primary">
                            <i class="fa fa-save"></i> Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<!-- SweetAlert2 Joseph -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/delivery/actions.js"></script> 