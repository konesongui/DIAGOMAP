<?php
    // Set all the form data
    $formID     = 'quoteEditForm';
    $submitBtn  = 'submitEditBtn';

    // var_dump($itemList);
    // var_dump($quote);
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
                    <h3 class="box-title">Modifier le devis <?php echo $quote['quote_number']; ?></h3>
                </div>
                
                <form action="<?php echo site_url('admin/quoteitem_purchase/update') ?>" id="<?= $formID; ?>" method="post" accept-charset="utf-8">
                    <div class="box-body">
                        <input type="hidden" name="id" value="<?php echo $quote['id']; ?>">
                        <div class="row">
                            <?php if ($this->session->flashdata('msg')) { ?>
                                <div class="alert alert-info"><?php echo $this->session->flashdata('msg') ?></div>
                            <?php } ?>
                            <?php if (isset($error_message)) { ?>
                                <div class='alert alert-danger'><?php echo $error_message ?></div>
                            <?php } ?>
                            
                            <?php echo $this->customlib->getCSRF(); ?>
                            
                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail1">Fournisseur</label><small class="req"> *</small>
                                <select class="form-control" name="customer">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php foreach ($supplier as $suppliers) { ?>
                                        <option value="<?php echo $suppliers['id']; ?>" <?php echo ((int)$suppliers['id'] == (int)$quote['supplier_id']) ? 'selected' : ''; ?>>
                                            <?php echo $suppliers['item_supplier'] .' ' . ' (' . $suppliers['phone'] . ')'; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <span class="text-danger"><?php echo form_error('customer'); ?></span>
                            </div>

                            <!-- <div class="form-group col-md-3">
                                <label>Désignation <small class="req">*</small></label>
                                <input name="designation" type="text" class="form-control" value="<?php echo $quote['designation']; ?>" />
                            </div> -->

                            <div class="form-group col-md-4">
                                <label>Date <small class="req">*</small></label>
                                <input id="quote_date" name="quote_date" type="text" class="form-control date" value="<?php echo date('d/m/Y', strtotime($quote['quote_date'])); ?>" readonly />
                            </div>

                            <div class="form-group col-md-3">
                                <label>Date de validité <small class="req">*</small></label>
                                <input id="valid_until" name="valid_until" type="text" class="form-control date" value="<?php echo date('d/m/Y', strtotime($quote['valid_until'])); ?>" readonly />
                            </div>
                            
                            <div class="clearfix"></div>
                            <hr>

                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Termes de paiement <small class="req">*</small></label>
                                    <textarea name="payment_terms" class="form-control"><?php echo $quote['payment_terms']; ?></textarea>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Termes de livraison <small class="req">*</small></label>
                                    <textarea name="delivery_terms" class="form-control"><?php echo $quote['delivery_terms']; ?></textarea>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Lieu de livraison <small class="req">*</small></label>
                                    <textarea name="delivery_location" class="form-control"><?php echo $quote['delivery_location']; ?></textarea>
                                </div>
                            </div>

                            <div class="clearfix"></div>
                            <hr>

                            <div class="col-md-12">
                                <h4>Articles du devis</h4>
                                <div id="items-container">
                                    <?php foreach ($quote['items'] as $item) { ?>
                                        <div class="repeater-item">
                                            <div class="row">
                                                <div class="form-group col-md-3">
                                                    <label>Catégorie <small class="req">*</small></label>
                                                    <input type="text" name="item_category[]" class="form-control item-category" list="category-list" value="<?php echo $item['category_name']; ?>" placeholder="Sélectionner ou enregistrer une catégorie" required>
                                                    <datalist id="category-list">
                                                        <?php foreach ($itemcatlist as $category): ?>
                                                            <option value="<?= $category['item_category'] ?>">
                                                        <?php endforeach; ?>
                                                        <?php foreach ($quote['items'] as $quoteItem): ?>
                                                            <option value="<?= $quoteItem['category_name'] ?>">
                                                        <?php endforeach; ?>
                                                    </datalist>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label>Article <small class="req">*</small></label>
                                                    <input type="text" name="item_name[]" class="form-control item-name" list="item-list" value="<?php echo $item['item_name']; ?>" placeholder="Sélectionner ou enregistrer un article" required>
                                                    <datalist id="item-list">
                                                        <?php foreach ($itemList as $list): ?>
                                                            <option value="<?= $list['name'] ?>" data-id="<?= $list['id'] ?>" data-stock="<?= $list['quantity'] ?>" data-unit="<?= $list['unit'] ?>" data-price="<?= $list['selling_price'] ?>">
                                                        <?php endforeach; ?>
                                                        <?php foreach ($quote['items'] as $quoteItem): ?>
                                                            <option value="<?= $quoteItem['item_name'] ?>" data-id="<?= $quoteItem['item_id'] ?>" data-stock="<?= $quoteItem['quantity'] ?>" data-unit="<?= $quoteItem['unit'] ?>" data-price="<?= $quoteItem['unit_price'] ?>">
                                                        <?php endforeach; ?>
                                                    </datalist>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>Unité</label>
                                                    <input type="text" name="unit[]" class="form-control unit" value="<?php echo $item['unit']; ?>">
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label>Quantité <small class="req">*</small></label>
                                                    <input type="text" name="quantity[]" class="form-control quantity" value="<?php echo $item['quantity']; ?>" required>
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
                                        <input type="checkbox" id="apply_tva" name="apply_tva" value="1" <?php echo ($quote['apply_tva']) ? 'checked' : ''; ?>> 
                                        Appliquer la TVA (<?php echo $quote['tva_rate']; ?>%)
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <table class="table table-bordered">
                                    <tr>
                                        <td>Total HT:</td>
                                        <td class="text-right"><span id="total_ht"><?php echo number_format($quote['total_ht'], 2); ?></span></td>
                                        <input type="hidden" name="total_ht" id="totalHT" value="<?php echo $quote['total_ht']; ?>">
                                    </tr>
                                    <tr class="tva-row" style="<?php echo ($quote['apply_tva']) ? '' : 'display:none;'; ?>">
                                        <td>TVA (<?php echo $quote['tva_rate']; ?>%):</td>
                                        <td class="text-right"><span id="tva_amount"><?php echo number_format($quote['tva_amount'], 2); ?></span></td>
                                        <input type="hidden" name="tva_amount" id="tvaAmount" value="<?php echo $quote['tva_amount']; ?>">
                                        <input type="hidden" name="tva_rate" value="<?php echo $quote['tva_rate']; ?>">
                                    </tr>
                                    <tr class="ttc-row">
                                        <td><strong>Total TTC:</strong></td>
                                        <td class="text-right"><strong><span id="total_ttc"><?php echo number_format($quote['total_ttc'], 2); ?></span></strong></td>
                                        <input type="hidden" name="total_ttc" id="totalTTC" value="<?php echo $quote['total_ttc']; ?>">
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <a href="<?php echo base_url('admin/quoteitem_purchase'); ?>" class="btn btn-default">
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/quote_purchase/actions.js"></script>