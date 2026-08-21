<?php
    // Set all the form data
    $formID     = 'invoiceForm';
    $submitBtn  = 'submitBtn';

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
    .tva-row, .ttc-row {
    transition: all 0.3s ease;
}
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-building-o"></i>Inventaire</h1>
    </section>
    
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $title; ?></h3>
                    </div>
                    
                    <form action="<?php echo site_url('admin/invoiceitem/add') ?>" id="<?= $formID; ?>" method="post" accept-charset="utf-8">
                        <div class="box-body">
                            <input type="hidden" id="totalHT" name="totalHT" value="0">
                            <input type="hidden" id="tvaAmount" name="tvaAmount" value="0">
                            <input type="hidden" id="totalTTC" name="totalTTC" value="0">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="customer">Client <span class="text-danger">*</span></label>
                                        <select class="form-control select2" name="customer">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($clients as $key => $client) {
                                                ?>
                                                <option value="<?php echo $client['id']; ?>"><?php echo $client['item_supplier'] .' ' . $client['lastname']. ' (' . $client['phone'] . ')'; ?></option> 
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="invoice_date">Date de facture <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control date" id="invoice_date" name="invoice_date" value="<?php echo date('d/m/Y'); ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="due_date">Date d'échéance <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control date" id="due_date" name="due_date" value="<?php echo date('d/m/Y', strtotime('+30 days')); ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Choisissez les articles</h4>
                                    <div id="items-container">
                                        <div class="repeater-item">
                                            <div class="row">
                                                <div class="form-group col-md-3">
                                                    <label>Catégorie <small class="req">*</small></label>
                                                    <select name="item_category_id[]" class="form-control item-category" required>
                                                        <option value="">Sélectionner</option>
                                                        <?php foreach ($itemcatlist as $category): ?>
                                                            <option value="<?= $category['id'] ?>" <?= (isset($item) && $item['item_category_id'] == $category['id']) ? 'selected' : '' ?>>
                                                                <?= $category['item_category'] ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label>Article <small class="req">*</small></label>
                                                    <select name="item_id[]" class="form-control item-select" required>
                                                        <option value="">Sélectionner</option>
                                                    </select>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>Unité</label>
                                                    <input type="text" name="unit[]" class="form-control unit" readonly>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label>Quantité <small class="req">*</small></label>
                                                    <input type="number" name="quantity[]" class="form-control quantity" min="1" max="1" required>
                                                    <div class="availability" style="margin-top: 5px; color: #3c8dbc; font-weight: bold;">
                                                        Stock disponible: <span class="available-qty">0</span>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label>Prix unitaire</label>
                                                    <input type="number" name="price[]" class="form-control price" min="0" step="0.01">
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>Total</label>
                                                    <div class="total-price">0.00</div>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>&nbsp;</label>
                                                    <button type="button" class="btn btn-danger btn-sm remove-item">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" id="add-item" class="btn btn-primary btn-sm">
                                        <i class="fa fa-plus"></i> Ajouter un article
                                    </button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="payment_method">Méthode de paiement</label>
                                        <select class="form-control select2" id="payment_method" name="payment_method">
                                            <option value="">Sélectionner...</option>
                                            <option value="cash">Espèces</option>
                                            <option value="check">Chèque</option>
                                            <option value="bank_transfer">Virement</option>
                                            <option value="card">Carte bancaire</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="notes">Notes</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" id="apply_tva" name="apply_tva" value="1"> Appliquer la TVA
                                            </label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="tva_rate">Taux de TVA (%)</label>
                                        <input type="number" class="form-control" id="tva_rate" name="tva_rate" value="20" min="0" max="100" step="0.1">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Total HT</label>
                                                <input type="text" class="form-control" id="total_ht" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>TVA</label>
                                                <input type="text" class="form-control" id="tva_amount" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Total TTC</label>
                                        <input type="text" class="form-control" id="total_ttc" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" id="<?= $submitBtn ?>" class="btn btn-primary">Enregistrer</button>
                            <a href="<?php echo base_url(); ?>admin/invoiceItem" class="btn btn-default">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/invoice/actions.js"></script>    