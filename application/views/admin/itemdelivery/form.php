<?php
    // Set all the form data
    $formID     = 'deliveryForm';
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
                        <h3 class="box-title">Bon de livraison</h3>
                    </div>
                    
                        <form action="<?php echo site_url('admin/deliveryitem/add') ?>" id="<?= $formID; ?>" method="post" accept-charset="utf-8">
                        <div class="box-body">
                            <div class="row">
                                <?php if ($this->session->flashdata('msg')) { ?>
                                    <div class="alert alert-info"><?php echo $this->session->flashdata('msg') ?></div>
                                <?php } ?>
                                <?php if (isset($error_message)) { ?>
                                    <div class='alert alert-danger'><?php echo $error_message ?></div>
                                <?php } ?>
                                
                                <?php echo $this->customlib->getCSRF(); ?>
                                <div class="form-group" hidden>
                                    <label for="exampleInputEmail1">User<small class="req"> *</small></label>
                                    <input id="user_name" name="user_name" readonly placeholder="" type="text" class="form-control"  value="<?php echo $this->customlib->getAdminSessionUserName(); ?>" />
                                    <span class="text-danger"><?php echo form_error('user_name'); ?></span>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="exampleInputEmail1">Client</label><small class="req"> *</small>

                                    <select class="form-control " name="customer">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php
                                        foreach ($clients as $key => $client) {
                                            ?>
                                            <option value="<?php echo $client['id']; ?>"><?php echo $client['item_supplier'] .' ' . $client['lastname']. ' (' . $client['phone'] . ')'; ?></option> 
                                            <?php
                                        }
                                        ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('customer'); ?></span>

                                </div>

                                <!-- <div class="form-group col-md-3">
                                    <label>Désignation <small class="req">*</small></label>
                                    <input name="designation" type="text" class="form-control" value="<?= set_value('designation') ?>" />
                                </div> -->

                                <div class="form-group col-md-4">
                                    <label>Date de livraison<small class="req">*</small></label>
                                    <input id="delivery_date" name="delivery_date" type="text" class="form-control date" value="<?= set_value('delivery_date', date('d/m/Y')) ?>" readonly />
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Date limite</label>
                                    <input id="valid_until" name="valid_until" type="text" class="form-control date" value="<?= set_value('valid_until', date('d/m/Y')) ?>" readonly />
                                </div>
                            </div> 
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Termes de paiements </label>
                                    <textarea name="payment_term" class="form-control"><?= set_value('payment_term') ?></textarea>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Termes de livraison </label>
                                    <textarea name="delivery_term" class="form-control"><?= set_value('delivery_term') ?></textarea>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Lieu de livraison </label>
                                    <textarea name="delivery_location" class="form-control"><?= set_value('delivery_location') ?></textarea>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="payment_method">Méthode de paiement</label>
                                    <select class="form-control select2" id="payment_method" name="payment_method">
                                        <option value="">Sélectionner...</option>
                                        <option value="cash">Espèces</option>
                                        <option value="check">Chèque</option>
                                        <option value="bank_transfer">Virement</option>
                                        <option value="card">Carte bancaire</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Objet</label>
                                    <input id="objet" name="objet" type="text" class="form-control" value="<?= set_value('objet') ?>"/>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <hr>
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
                                                            <option value="<?= $category['id'] ?>"><?= $category['item_category'] ?></option>
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
                        </div>

                        <div class="box-footer">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" id="apply_tva" name="apply_tva" value="1"> Appliquer la TVA (18%)
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td>Total HT:</td>
                                            <td class="text-right"><span id="total_ht">0.00</span></td>
                                            <input type="hidden" name="total_ht" id="totalHT">
                                        </tr>
                                        <tr class="tva-row" style="display:none;">
                                            <td>TVA (18%):</td>
                                            <td class="text-right"><span id="tva_amount">0.00</span></td>
                                            <input type="hidden" name="tva_amount" id="tvaAmount">
                                            <input type="hidden" name="tva_rate" value="18">
                                        </tr>
                                        <tr class="ttc-row">
                                            <td><strong>Total TTC:</strong></td>
                                            <td class="text-right"><strong><span id="total_ttc">0.00</span></strong></td>
                                            <input type="hidden" name="total_ttc" id="totalTTC">
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <a href="<?php echo base_url('admin/quoteitem'); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Retour à la liste
                            </a>
                            <button type="submit" id="<?= $submitBtn; ?>" class="btn btn-primary">
                                <i class="fa fa-save"></i> Enregistrer
                            </button>
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
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/delivery/actions.js"></script>