<?php
$formID = 'quoteForm';
$submitBtn = 'submitBtn';
$isEdit = false;
?>

<style type="text/css">
    .repeater-item {
        border: 1px solid #eee;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 4px;
        background: #f9f9f9;
        position: relative;
        transition: all 0.3s ease;
        cursor: grab;
    }
    .repeater-item.dragging {
        opacity: 0.5;
        transform: scale(0.98);
        cursor: grabbing;
    }
    .repeater-item.drag-over {
        border: 2px dashed #007bff;
        background: #f0f8ff;
    }
    .remove-item { cursor: pointer; color: #fff; }
    .availability { margin-top: 5px; font-size: 12px; color: #666; }
    .total-price { font-weight: bold; margin-top: 5px; color: #333; }
    .total-price-after-discount { font-weight: bold; margin-top: 5px; color: #d35400; }
    .discount-field { background-color: #fff8e1; }
    .discount-type-group { display: flex; align-items: center; gap: 5px; margin-bottom: 5px; }
    .discount-type-btn { padding: 4px 8px; font-size: 12px; border: 1px solid #ccc; background: #f8f9fa; cursor: pointer; border-radius: 3px; }
    .discount-type-btn.active { background: #007bff; color: white; border-color: #007bff; }
    .discount-input-group { display: flex; align-items: center; }
    .discount-symbol { padding: 0 8px; background: #e9ecef; border: 1px solid #ced4da; border-right: none; height: 38px; display: flex; align-items: center; border-radius: 4px 0 0 4px; font-size: 14px; }
    .tax-options { margin-bottom: 10px; }
    .other-tax-container { margin-top: 10px; display: none; }
    .move-btn { padding: 2px 8px; margin: 0 2px; font-size: 14px; cursor: pointer; border: 1px solid #ddd; border-radius: 3px; background: #fff; color: #333; transition: all 0.2s; }
    .move-btn:hover { background: #007bff; color: white; border-color: #007bff; }
    .move-btn:disabled { opacity: 0.5; cursor: not-allowed; background: #f5f5f5; }
    .move-buttons { display: flex; gap: 3px; margin-top: 5px; }
    .drag-handle { cursor: grab; color: #999; font-size: 18px; padding: 0 5px; user-select: none; position: absolute; top: 10px; right: 10px; }
    .drag-handle:hover { color: #333; }
    .drag-handle:active { cursor: grabbing; }
    .item-index { position: absolute; top: 10px; left: 10px; background: #007bff; color: white; border-radius: 50%; width: 24px; height: 24px; text-align: center; line-height: 24px; font-size: 12px; font-weight: bold; }

    /* Styles spécifiques à la modale */
    .modal-body .box { box-shadow: none; border: none; padding: 0; }
    .modal-body .box-header { display: none; }
    .modal-body .box-footer { background: #f8f9fa; border-top: 1px solid #e9ecef; }
</style>

<div class="box box-primary">
    <form action="<?= site_url('admin/proforma/add') ?>" id="<?= $formID; ?>" method="post" accept-charset="utf-8">
        <div class="box-body">
            <div class="row">
                <?= $this->customlib->getCSRF(); ?>
                <div class="form-group" hidden>
                    <label>User</label>
                    <input id="user_name" name="user_name" readonly type="text" class="form-control" value="<?= $this->customlib->getAdminSessionUserName(); ?>" />
                </div>

                <div class="form-group col-md-4">
                    <label>Client <small class="req">*</small></label>
                    <select class="form-control" name="customer" id="customer_select">
                        <option value="">Sélectionner</option>
                        <?php foreach ($clients as $client): ?>
                            <option value="<?= $client['id'] ?>">
                                <?= $client['item_supplier'].' '.$client['lastname'].' ('.$client['phone'].')' ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="new">➕ Nouveau client</option>
                    </select>
                </div>

                <div class="form-group col-md-4 new-client-fields" style="display:none;">
                    <label>Nom du client <small class="req">*</small></label>
                    <input type="text" name="new_client_name" class="form-control">
                </div>
                <div class="form-group col-md-4 new-client-fields" style="display:none;">
                    <label>Téléphone <small class="req">*</small></label>
                    <input type="text" name="new_client_phone" class="form-control">
                </div>
                <div class="form-group col-md-4 new-client-fields" style="display:none;">
                    <label>Email</label>
                    <input type="text" name="new_client_email" class="form-control">
                </div>

                <div class="form-group col-md-4">
                    <label>Date de création <small class="req">*</small></label>
                    <input id="quote_date" name="quote_date" type="text" class="form-control date" value="<?= date('d/m/Y') ?>"/>
                </div>
                <div class="form-group col-md-4">
                    <label>Date limite</label>
                    <input id="valid_until" name="valid_until" type="text" class="form-control date" value="<?= date('d/m/Y', strtotime('+30 days')) ?>"/>
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-4">
                    <label>Termes de paiement</label>
                    <textarea name="payment_terms" class="form-control"></textarea>
                </div>
                <div class="form-group col-md-4">
                    <label>Termes de livraison</label>
                    <textarea name="delivery_terms" class="form-control"></textarea>
                </div>
                <div class="form-group col-md-4">
                    <label>Lieu de livraison</label>
                    <textarea name="delivery_location" class="form-control"></textarea>
                </div>
                <div class="form-group col-md-4">
                    <label>Mode de paiement</label>
                    <select class="form-control" name="payment_method">
                        <option value="">Sélectionner...</option>
                        <?php foreach (['Espèces','Chèque','Virement','Carte bancaire'] as $method): ?>
                            <option value="<?= $method ?>"><?= $method ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Objet</label>
                    <input name="objet" type="text" class="form-control" value=""/>
                </div>
            </div>

            <div class="clearfix"></div>
            <hr>

            <div class="row">
                <div class="col-md-12">
                    <h4>Articles / Services <small class="text-muted">(Glissez-déposez pour réorganiser)</small></h4>
                    <div id="items-container">
                        <div class="repeater-item" data-index="0">
                            <span class="item-index">1</span>
                            <span class="drag-handle"><i class="fa fa-arrows-alt"></i></span>
                            <div class="row">
                                <div class="form-group col-md-1">
                                    <label>Type</label>
                                    <select name="item_type[]" class="form-control item-type">
                                        <option value="product" selected>Produit</option>
                                        <option value="service">Service</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-2 cat-group">
                                    <label>Catégorie</label>
                                    <input type="text" name="item_category[]" class="form-control item-category" list="category-list" placeholder="Sélectionnez une catégorie">
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Article / Service <small class="req">*</small></label>
                                    <input type="text" name="item_name[]" class="form-control item-name" placeholder="Sélectionnez" required>
                                    <datalist class="item-datalist"></datalist>
                                </div>
                                <div class="form-group col-md-1">
                                    <label>Unité / Durée</label>
                                    <input type="text" name="unit[]" class="form-control unit">
                                </div>
                                <div class="form-group col-md-1">
                                    <label>Quantité <small class="req">*</small></label>
                                    <input type="number" name="quantity[]" class="form-control quantity" value="1" min="1" required>
                                    <div class="availability">Stock: <span class="available-qty">0</span></div>
                                </div>
                                <div class="form-group col-md-1">
                                    <label>Prix unitaire</label>
                                    <input type="number" name="price[]" class="form-control price" step="0.01" value="0">
                                </div>
                                <div class="form-group col-md-2">
                                    <label>Remise</label>
                                    <div class="discount-type-group">
                                        <button type="button" class="discount-type-btn active" data-type="percent">%</button>
                                        <button type="button" class="discount-type-btn" data-type="amount">FCFA</button>
                                        <input type="hidden" name="discount_type[]" class="discount-type" value="percent">
                                    </div>
                                    <div class="discount-input-group">
                                        <span class="discount-symbol"><span class="discount-symbol-text">%</span></span>
                                        <input type="number" name="discount[]" class="form-control discount discount-field" step="0.01" value="0">
                                    </div>
                                </div>
                                <div class="form-group col-md-1">
                                    <label>P.U NET</label>
                                    <div class="total-price">0.00</div>
                                </div>
                                <div class="form-group col-md-1">
                                    <label>MONTANT.NET</label>
                                    <div class="total-price-after-discount">0.00</div>
                                    <input type="hidden" name="line_total_after_discount[]" class="line-total-after-discount" value="0">
                                </div>
                                <div class="form-group col-md-1">
                                    <label>&nbsp;</label>
                                    <div class="move-buttons">
                                        <button type="button" class="move-btn move-up" title="Déplacer vers le haut"><i class="fa fa-chevron-up"></i></button>
                                        <button type="button" class="move-btn move-down" title="Déplacer vers le bas"><i class="fa fa-chevron-down"></i></button>
                                    </div>
                                    <button type="button" class="btn btn-danger btn-sm remove-item" style="margin-top: 5px;"><i class="fa fa-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="add-item" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Ajouter une ligne</button>
                </div>
            </div>
        </div>

        <div class="box-footer">
            <div class="row">
                <div class="col-md-8">
                    <!-- Remise globale -->
                    <div class="form-group">
                        <label>Remise globale</label>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="discount-type-group">
                                    <button type="button" class="discount-type-btn active" data-type="percent" id="global_discount_btn_percent">%</button>
                                    <button type="button" class="discount-type-btn" data-type="amount" id="global_discount_btn_amount">FCFA</button>
                                    <input type="hidden" name="global_discount_type" id="global_discount_type" value="percent">
                                </div>
                                <div class="discount-input-group">
                                    <span class="discount-symbol"><span class="discount-symbol-text" id="global_discount_symbol">%</span></span>
                                    <input type="number" id="global_discount_amount" name="global_discount_amount" class="form-control" step="0.01" value="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Taxes -->
                    <div class="form-group tax-options">
                        <label>Options de taxe</label>
                        <div class="radio"><label><input type="radio" name="tax_option" value="none" checked> Aucune taxe</label></div>
                        <div class="radio"><label><input type="radio" name="tax_option" value="tva"> Appliquer la TVA (18%)</label></div>
                        <div class="radio"><label><input type="radio" name="tax_option" value="other"> Autre taxe</label></div>
                        <div class="other-tax-container" id="other_tax_container" style="display:none;">
                            <div class="row">
                                <div class="col-md-6"><label>Nom de la taxe</label><input type="text" name="other_tax_name" id="other_tax_name" class="form-control"></div>
                                <div class="col-md-6"><label>Taux (%)</label><div class="input-group"><input type="number" name="other_tax_rate" id="other_tax_rate" class="form-control" step="0.01"><span class="input-group-addon">%</span></div></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <table class="table table-bordered">
                        <tr><td>Total HT:</td><td class="text-right"><span id="total_ht">0.00</span><input type="hidden" name="total_ht" id="totalHT"></td></tr>
                        <tr><td>Total Remise:</td><td class="text-right"><span id="total_discount">0.00</span><input type="hidden" name="total_discount" id="totalDiscount"></td></tr>
                        <tr><td>Montant Net HT:</td><td class="text-right"><span id="total_after_discount">0.00</span><input type="hidden" name="total_after_discount" id="totalAfterDiscount"></td></tr>
                        <tr class="tva-row" style="display:none;"><td>TVA (18%):</td><td class="text-right"><span id="tva_amount">0.00</span><input type="hidden" name="tva_amount" id="tvaAmount" value="0"><input type="hidden" name="tva_rate" value="18"></td></tr>
                        <tr class="other-tax-row" style="display:none;"><td id="other_tax_label">Autre taxe:</td><td class="text-right"><span id="other_tax_amount">0.00</span><input type="hidden" name="other_tax_amount" id="otherTaxAmount" value="0"><input type="hidden" name="other_tax_rate" id="otherTaxRate" value="0"></td></tr>
                        <tr><td><strong>Total TTC:</strong></td><td class="text-right"><strong><span id="total_ttc">0.00</span></strong><input type="hidden" name="total_ttc" id="totalTTC"></td></tr>
                    </table>
                </div>
            </div>
            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</button>
            <button type="submit" id="<?= $submitBtn; ?>" class="btn btn-primary"><i class="fa fa-save"></i> Créer le proforma</button>
        </div>
    </form>
</div>

<datalist id="category-list">
    <?php foreach ($itemcatlist as $cat): ?>
    <option value="<?= addslashes($cat['item_category']) ?>">
        <?php endforeach; ?>
</datalist>

<script>
    // S'assurer que base_url est disponible
    if (typeof base_url === 'undefined') {
        var base_url = '<?= base_url() ?>';
    }

    // Fonction pour charger les services (utilisée par handleItemTypeChange)
    function loadServices($input, $row) {
        var uniqueId = 'servlist_' + new Date().getTime();
        $input.attr('list', uniqueId);
        var $datalist = $row.find('.item-datalist');
        $datalist.attr('id', uniqueId);
        $.ajax({
            url: base_url + 'admin/services/get_services_json',
            dataType: 'json',
            success: function(services) {
                var opts = '<option value="">Sélectionnez un service</option>';
                $.each(services, function(i, s) {
                    opts += '<option value="' + s.name + '" data-price="' + s.unit_price + '" data-unit="' + (s.duration || 'prestation') + '">';
                });
                $datalist.html(opts);
                $input.attr('placeholder', 'Tapez ou choisissez un service');
            },
            error: function() {
                $datalist.html('<option value="">Erreur chargement services</option>');
            }
        });
    }
</script>