<?php
// Set all the form data
$formID     = 'quoteForm';
$submitBtn  = 'submitBtn';
$isEdit = isset($quote) && !empty($quote);
?>

<style type="text/css">
    .repeater-item {
        border: 1px solid #eee;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 4px;
        background: #f9f9f9;
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
    .item-type-select { font-weight: 500; }
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
                        <h3 class="box-title"><?= $isEdit ? 'Modifier le proforma' : 'Créer un proforma' ?></h3>
                    </div>

                    <form action="<?= site_url($isEdit ? 'admin/proforma/update' : 'admin/proforma/add') ?>" id="<?= $formID; ?>" method="post" accept-charset="utf-8">
                        <?php if ($isEdit): ?>
                            <input type="hidden" name="id" value="<?= $quote['id'] ?>">
                        <?php endif; ?>

                        <div class="box-body">
                            <div class="row">
                                <?= $this->customlib->getCSRF(); ?>
                                <div class="form-group" hidden>
                                    <label>User <small class="req">*</small></label>
                                    <input id="user_name" name="user_name" readonly type="text" class="form-control" value="<?= $this->customlib->getAdminSessionUserName(); ?>" />
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Client <small class="req">*</small></label>
                                    <select class="form-control" name="customer" id="customer_select">
                                        <option value="">Sélectionner</option>
                                        <?php foreach ($clients as $client): ?>
                                            <option value="<?= $client['id'] ?>" <?= ($isEdit && $quote['customer_id'] == $client['id']) ? 'selected' : '' ?>>
                                                <?= $client['item_supplier'].' '.$client['lastname'].' ('.$client['phone'].')' ?>
                                            </option>
                                        <?php endforeach; ?>
                                        <option value="new">➕ Nouveau client</option>
                                    </select>
                                </div>

                                <!-- Nouveau client fields -->
                                <div class="form-group col-md-4 new-client-fields" style="display:none;">
                                    <label>Nom du client</label><small class="req">*</small>
                                    <input type="text" name="new_client_name" class="form-control">
                                </div>
                                <div class="form-group col-md-4 new-client-fields" style="display:none;">
                                    <label>Téléphone</label><small class="req">*</small>
                                    <input type="text" name="new_client_phone" class="form-control">
                                </div>
                                <div class="form-group col-md-4 new-client-fields" style="display:none;">
                                    <label>Email</label><small class="req">*</small>
                                    <input type="text" name="new_client_email" class="form-control">
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Date de création <small class="req">*</small></label>
                                    <input id="quote_date" name="quote_date" type="text" class="form-control date" value="<?= $isEdit ? date('d/m/Y', strtotime($quote['quote_date'])) : date('d/m/Y') ?>"/>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Date limite</label>
                                    <input id="valid_until" name="valid_until" type="text" class="form-control date" value="<?= $isEdit && $quote['valid_until'] ? date('d/m/Y', strtotime($quote['valid_until'])) : date('d/m/Y', strtotime('+30 days')) ?>"/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Termes de paiements</label>
                                    <textarea name="payment_terms" class="form-control"><?= $isEdit ? $quote['payment_terms'] : '' ?></textarea>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Termes de livraison</label>
                                    <textarea name="delivery_terms" class="form-control"><?= $isEdit ? $quote['delivery_terms'] : '' ?></textarea>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Lieu de livraison</label>
                                    <textarea name="delivery_location" class="form-control"><?= $isEdit ? $quote['delivery_location'] : '' ?></textarea>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Mode de paiement</label>
                                    <select class="form-control" name="payment_method">
                                        <option value="">Sélectionner...</option>
                                        <?php foreach (['Espèces','Chèque','Virement','Carte bancaire'] as $method): ?>
                                            <option value="<?= $method ?>" <?= ($isEdit && $quote['payment_method'] == $method) ? 'selected' : '' ?>><?= $method ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Objet</label>
                                    <input name="objet" type="text" class="form-control" value="<?= $isEdit ? $quote['objet'] : '' ?>"/>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <h4>Choisissez les articles / services</h4>
                                    <div id="items-container">
                                        <?php if ($isEdit && !empty($quote['items'])): ?>
                                            <?php foreach ($quote['items'] as $index => $item): ?>
                                                <div class="repeater-item" data-item-type="<?= $item['item_type'] ?>">
                                                    <div class="row">
                                                        <div class="form-group col-md-1">
                                                            <label>Type</label>
                                                            <select name="item_type[]" class="form-control item-type">
                                                                <option value="product" <?= $item['item_type'] == 'product' ? 'selected' : '' ?>>Produit</option>
                                                                <option value="service" <?= $item['item_type'] == 'service' ? 'selected' : '' ?>>Service</option>
                                                            </select>
                                                        </div>
                                                        <div class="form-group col-md-2 cat-group" <?= $item['item_type'] == 'service' ? 'style="display:none;"' : '' ?>>
                                                            <label>Catégorie</label>
                                                            <input type="text" name="item_category[]" class="form-control item-category" list="category-list" value="<?= htmlspecialchars($item['category_name']) ?>" placeholder="Sélectionnez une catégorie">
                                                        </div>
                                                        <div class="form-group col-md-2">
                                                            <label>Article / Service</label>
                                                            <input type="text" name="item_name[]" class="form-control item-name" value="<?= htmlspecialchars($item['item_name']) ?>" placeholder="Sélectionnez" required>
                                                            <datalist class="item-datalist"></datalist>
                                                        </div>
                                                        <div class="form-group col-md-1">
                                                            <label>Unité / Durée</label>
                                                            <input type="text" name="unit[]" class="form-control unit" value="<?= htmlspecialchars($item['unit']) ?>">
                                                        </div>
                                                        <div class="form-group col-md-1">
                                                            <label>Quantité</label>
                                                            <input type="number" name="quantity[]" class="form-control quantity" value="<?= $item['quantity'] ?>" min="1" required>
                                                            <div class="availability" <?= $item['item_type'] == 'service' ? 'style="display:none;"' : '' ?>>Stock: <span class="available-qty">0</span></div>
                                                        </div>
                                                        <div class="form-group col-md-1">
                                                            <label>Prix unitaire</label>
                                                            <input type="number" name="price[]" class="form-control price" step="0.01" value="<?= $item['unit_price'] ?>">
                                                        </div>
                                                        <div class="form-group col-md-2">
                                                            <label>Remise</label>
                                                            <div class="discount-type-group">
                                                                <button type="button" class="discount-type-btn <?= $item['discount_type'] == 'percent' ? 'active' : '' ?>" data-type="percent">%</button>
                                                                <button type="button" class="discount-type-btn <?= $item['discount_type'] == 'amount' ? 'active' : '' ?>" data-type="amount">FCFA</button>
                                                                <input type="hidden" name="discount_type[]" class="discount-type" value="<?= $item['discount_type'] ?>">
                                                            </div>
                                                            <div class="discount-input-group">
                                                                <span class="discount-symbol"><span class="discount-symbol-text"><?= $item['discount_type'] == 'percent' ? '%' : 'FCFA' ?></span></span>
                                                                <input type="number" name="discount[]" class="form-control discount discount-field" step="0.01" value="<?= $item['discount'] ?>">
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
                                                            <button type="button" class="btn btn-danger btn-sm remove-item"><i class="fa fa-trash"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <!-- Ligne vide par défaut -->
                                            <div class="repeater-item">
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
                                                        <label>Article / Service</label>
                                                        <input type="text" name="item_name[]" class="form-control item-name" placeholder="Sélectionnez" required>
                                                        <datalist class="item-datalist"></datalist>
                                                    </div>
                                                    <div class="form-group col-md-1">
                                                        <label>Unité / Durée</label>
                                                        <input type="text" name="unit[]" class="form-control unit">
                                                    </div>
                                                    <div class="form-group col-md-1">
                                                        <label>Quantité</label>
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
                                                        <button type="button" class="btn btn-danger btn-sm remove-item"><i class="fa fa-trash"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
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
                                                    <input type="number" id="global_discount_amount" name="global_discount_amount" class="form-control" step="0.01" value="<?= $isEdit ? $quote['global_discount_amount'] : 0 ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Taxes -->
                                    <div class="form-group tax-options">
                                        <label>Options de taxe</label>
                                        <?php
                                        $tax_option = $isEdit ? $quote['tax_option'] : 'none';
                                        ?>
                                        <div class="radio"><label><input type="radio" name="tax_option" value="none" <?= $tax_option == 'none' ? 'checked' : '' ?>> Aucune taxe</label></div>
                                        <div class="radio"><label><input type="radio" name="tax_option" value="tva" <?= $tax_option == 'tva' ? 'checked' : '' ?>> Appliquer la TVA (18%)</label></div>
                                        <div class="radio"><label><input type="radio" name="tax_option" value="other" <?= $tax_option == 'other' ? 'checked' : '' ?>> Autre taxe</label></div>
                                        <div class="other-tax-container" id="other_tax_container" style="display: <?= $tax_option == 'other' ? 'block' : 'none' ?>;">
                                            <div class="row">
                                                <div class="col-md-6"><label>Nom de la taxe</label><input type="text" name="other_tax_name" id="other_tax_name" class="form-control" value="<?= $isEdit ? $quote['other_tax_name'] : '' ?>"></div>
                                                <div class="col-md-6"><label>Taux (%)</label><div class="input-group"><input type="number" name="other_tax_rate" id="other_tax_rate" class="form-control" step="0.01" value="<?= $isEdit ? $quote['other_tax_rate'] : '' ?>"><span class="input-group-addon">%</span></div></div>
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
                            <a href="<?= base_url('admin/proforma') ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Retour</a>
                            <button type="submit" id="<?= $submitBtn; ?>" class="btn btn-primary"><i class="fa fa-save"></i> <?= $isEdit ? 'Mettre à jour' : 'Enregistrer' ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/fr.js"></script>

<script type="text/javascript">
    var base_url = '<?= base_url() ?>';
    moment.locale('fr');

    $(function() {
        // Datalist statique pour les catégories (rempli par PHP)
        var categoryDatalist = '<datalist id="category-list">';
        <?php foreach ($itemcatlist as $cat): ?>
        categoryDatalist += '<option value="<?= addslashes($cat['item_category']) ?>">';
        <?php endforeach; ?>
        categoryDatalist += '</datalist>';
        $('body').append(categoryDatalist);

        // ========== CALCUL DES TOTAUX ==========
        function calculateItemTotal(item) {
            var $item = $(item);
            var qty = parseFloat($item.find('.quantity').val()) || 0;
            var price = parseFloat($item.find('.price').val()) || 0;
            var discount = parseFloat($item.find('.discount').val()) || 0;
            var discType = $item.find('.discount-type').val();
            var discAmount = discType === 'percent' ? price * discount / 100 : Math.min(discount, price);
            var netPrice = price - discAmount;
            var totalNet = netPrice * qty;
            $item.find('.total-price').text(netPrice.toFixed(2));
            $item.find('.total-price-after-discount').text(totalNet.toFixed(2));
            $item.find('.line-total-after-discount').val(totalNet.toFixed(2));
            return { totalHT: price * qty, discountAmount: discAmount * qty, totalAfterDiscount: totalNet };
        }

        function calculateTotals() {
            var totalHT = 0, totalDiscount = 0, totalAfter = 0;
            $('.repeater-item').each(function() {
                var res = calculateItemTotal(this);
                totalHT += res.totalHT;
                totalDiscount += res.discountAmount;
                totalAfter += res.totalAfterDiscount;
            });
            var globalDiscAmount = parseFloat($('#global_discount_amount').val()) || 0;
            var globalDiscType = $('#global_discount_type').val();
            var globalDisc = 0;
            if (globalDiscAmount > 0) {
                globalDisc = globalDiscType === 'percent' ? totalAfter * globalDiscAmount / 100 : Math.min(globalDiscAmount, totalAfter);
            }
            var finalDiscount = totalDiscount + globalDisc;
            var finalAfter = Math.max(totalAfter - globalDisc, 0);
            // Taxes
            var taxOption = $('input[name="tax_option"]:checked').val();
            var taxAmount = 0;
            if (taxOption === 'tva') {
                taxAmount = finalAfter * 0.18;
                $('#other_tax_label').text('TVA (18%)');
                $('#otherTaxRate').val(18);
            } else if (taxOption === 'other') {
                var otherRate = parseFloat($('#other_tax_rate').val()) || 0;
                taxAmount = finalAfter * (otherRate / 100);
                var taxName = $('#other_tax_name').val() || 'Autre taxe';
                $('#other_tax_label').text(taxName + ' (' + otherRate.toFixed(2) + '%)');
                $('#otherTaxRate').val(otherRate);
            }
            var totalTTC = finalAfter + taxAmount;
            // Affichage
            $('#total_ht').text(totalHT.toFixed(2)); $('#totalHT').val(totalHT.toFixed(2));
            $('#total_discount').text(finalDiscount.toFixed(2)); $('#totalDiscount').val(finalDiscount.toFixed(2));
            $('#total_after_discount').text(finalAfter.toFixed(2)); $('#totalAfterDiscount').val(finalAfter.toFixed(2));
            if (taxOption === 'tva') {
                $('#tva_amount').text(taxAmount.toFixed(2)); $('#tvaAmount').val(taxAmount.toFixed(2));
                $('#other_tax_amount').text('0.00'); $('#otherTaxAmount').val('0');
                $('.tva-row').show(); $('.other-tax-row').hide();
            } else if (taxOption === 'other') {
                $('#tva_amount').text('0.00'); $('#tvaAmount').val('0');
                $('#other_tax_amount').text(taxAmount.toFixed(2)); $('#otherTaxAmount').val(taxAmount.toFixed(2));
                $('.tva-row').hide(); $('.other-tax-row').show();
            } else {
                $('#tva_amount').text('0.00'); $('#tvaAmount').val('0');
                $('#other_tax_amount').text('0.00'); $('#otherTaxAmount').val('0');
                $('.tva-row').hide(); $('.other-tax-row').hide();
            }
            $('#total_ttc').text(totalTTC.toFixed(2)); $('#totalTTC').val(totalTTC.toFixed(2));
        }

        // ========== CHARGEMENT DES PRODUITS PAR CATÉGORIE ==========
        function loadProductsByCategory(category, $input, $row) {
            if (!category) return;
            $input.attr('placeholder', 'Chargement...');
            $.post(base_url + 'admin/proforma/get_items_by_category_name', { category_name: category }, function(data) {
                var uniqueId = 'prodlist_' + new Date().getTime();
                $input.attr('list', uniqueId);
                var $datalist = $row.find('.item-datalist');
                $datalist.attr('id', uniqueId);
                var opts = '';
                if (data.length) {
                    $.each(data, function(i, obj) {
                        opts += '<option value="' + obj.name + '" data-unit="' + (obj.unit || '') + '" data-price="' + (obj.unit_price || 0) + '" data-stock="' + (obj.current_quantity || 0) + '">';
                    });
                    $input.attr('placeholder', 'Sélectionnez un produit');
                } else {
                    opts = '<option value="">Aucun produit trouvé</option>';
                    $input.attr('placeholder', 'Aucun produit');
                }
                $datalist.html(opts);
            }, 'json');
        }

        // ========== CHARGEMENT DES SERVICES ==========
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

        // ========== CHANGEMENT DE TYPE PRODUIT / SERVICE ==========
        function handleItemTypeChange($row) {
            var type = $row.find('.item-type').val();
            var $catGroup = $row.find('.cat-group');
            var $availability = $row.find('.availability');
            var $unit = $row.find('.unit');
            var $itemName = $row.find('.item-name');
            var $datalist = $row.find('.item-datalist');

            if (type === 'product') {
                $catGroup.show();
                $availability.show();
                $unit.attr('readonly', true).attr('placeholder', 'ex: pièce');
                var category = $row.find('.item-category').val();
                if (category) {
                    loadProductsByCategory(category, $itemName, $row);
                } else {
                    $itemName.attr('list', '');
                    $itemName.attr('placeholder', 'Sélectionnez d\'abord une catégorie');
                    $datalist.html('');
                }
                // Réinitialiser les champs produit si nécessaire
                $itemName.val('');
                $row.find('.price').val(0);
                $row.find('.unit').val('');
                $row.find('.available-qty').text('0');
            } else { // service
                $catGroup.hide();
                $availability.hide();
                $unit.attr('readonly', false).attr('placeholder', 'ex: heure, forfait');
                loadServices($itemName, $row);
                // Réinitialiser les champs
                $itemName.val('');
                $row.find('.price').val(0);
                $row.find('.unit').val('');
            }
            calculateTotals();
        }

        // ========== LORSQU'ON SÉLECTIONNE UN PRODUIT OU SERVICE ==========
        function onItemNameChange($row) {
            var type = $row.find('.item-type').val();
            var val = $row.find('.item-name').val();
            var $datalist = $row.find('.item-datalist');
            if (type === 'product') {
                var option = $datalist.find('option[value="' + val + '"]');
                if (option.length) {
                    $row.find('.unit').val(option.data('unit') || '');
                    $row.find('.price').val(option.data('price') || 0);
                    $row.find('.available-qty').text(option.data('stock') || 0);
                } else {
                    // Nouveau produit (non existant)
                    $row.find('.unit').val('');
                    $row.find('.price').val(0);
                    $row.find('.available-qty').text('0');
                }
            } else { // service
                var opt = $datalist.find('option[value="' + val + '"]');
                if (opt.length) {
                    $row.find('.price').val(opt.data('price') || 0);
                    $row.find('.unit').val(opt.data('unit') || 'prestation');
                    $row.find('.available-qty').text('N/A');
                } else {
                    // Nouveau service (non existant)
                    $row.find('.price').val(0);
                    $row.find('.unit').val('');
                }
            }
            calculateTotals();
        }

        // ========== INITIALISATION D'UNE LIGNE EXISTANTE (ÉDITION) ==========
        function initExistingLine($row) {
            var type = $row.find('.item-type').val();
            if (type === 'product') {
                var category = $row.find('.item-category').val();
                if (category) {
                    loadProductsByCategory(category, $row.find('.item-name'), $row);
                    // Après chargement, il faut forcer la sélection du produit (si un nom est déjà présent)
                    var productName = $row.find('.item-name').val();
                    if (productName) {
                        // Attendre que le datalist soit prêt puis déclencher la sélection
                        setTimeout(function() {
                            onItemNameChange($row);
                        }, 300);
                    }
                }
            } else {
                loadServices($row.find('.item-name'), $row);
                var serviceName = $row.find('.item-name').val();
                if (serviceName) {
                    setTimeout(function() {
                        onItemNameChange($row);
                    }, 300);
                }
            }
            calculateItemTotal($row[0]);
        }

        // ========== ÉVÉNEMENTS ==========
        $(document).on('change', '.item-type', function() { handleItemTypeChange($(this).closest('.repeater-item')); });
        $(document).on('change', '.item-category', function() {
            var $row = $(this).closest('.repeater-item');
            if ($row.find('.item-type').val() === 'product') {
                var cat = $(this).val();
                if (cat) {
                    loadProductsByCategory(cat, $row.find('.item-name'), $row);
                } else {
                    $row.find('.item-name').attr('list', '').val('');
                }
                $row.find('.unit').val('');
                $row.find('.price').val(0);
                $row.find('.available-qty').text('0');
                calculateTotals();
            }
        });
        $(document).on('change', '.item-name', function() { onItemNameChange($(this).closest('.repeater-item')); });
        $(document).on('input', '.quantity, .price, .discount, #global_discount_amount, #other_tax_rate, #other_tax_name', function() { calculateTotals(); });

        // Ajout d'une ligne
        $(document).on('click', '#add-item', function() {
            var $first = $('.repeater-item').first();
            var $new = $first.clone();
            $new.find('input, select').val('');
            $new.find('.quantity').val(1);
            $new.find('.price').val(0);
            $new.find('.discount').val(0);
            $new.find('.discount-type').val('percent');
            $new.find('.discount-symbol-text').text('%');
            $new.find('.total-price, .total-price-after-discount').text('0.00');
            $new.find('.line-total-after-discount').val('0');
            $new.find('.available-qty').text('0');
            $new.find('.item-type').val('product');
            $new.find('.cat-group').show();
            $new.find('.availability').show();
            $new.find('.item-datalist').html('');
            $new.find('.remove-item').off('click').on('click', function() { $(this).closest('.repeater-item').remove(); calculateTotals(); });
            $('#items-container').append($new);
            handleItemTypeChange($new);
            calculateTotals();
        });

        $(document).on('click', '.remove-item', function() { $(this).closest('.repeater-item').remove(); calculateTotals(); });

        // Gestion nouveau client
        $('#customer_select').change(function() { $('.new-client-fields').toggle($(this).val() === 'new'); });

        // Gestion taxes
        $('input[name="tax_option"]').change(function() {
            $('#other_tax_container').toggle($(this).val() === 'other');
            calculateTotals();
        });

        // Gestion des boutons de remise (article et global)
        function setupDiscountBtns() {
            $('.discount-type-btn').off('click').on('click', function() {
                var $group = $(this).closest('.discount-type-group');
                $group.find('.discount-type-btn').removeClass('active');
                $(this).addClass('active');
                var type = $(this).data('type');
                if ($(this).closest('.repeater-item').length) {
                    var $row = $(this).closest('.repeater-item');
                    $row.find('.discount-type').val(type);
                    $row.find('.discount-symbol-text').text(type === 'percent' ? '%' : 'FCFA');
                } else {
                    $('#global_discount_type').val(type);
                    $('#global_discount_symbol').text(type === 'percent' ? '%' : 'FCFA');
                }
                calculateTotals();
            });
        }
        setupDiscountBtns();

        // Soumission du formulaire
        $('#<?= $formID; ?>').on('submit', function(e) {
            e.preventDefault();
            var form = this;
            var taxOpt = $('input[name="tax_option"]:checked').val();
            if (taxOpt === 'other') {
                var taxName = $('#other_tax_name').val().trim();
                var taxRate = parseFloat($('#other_tax_rate').val());
                if (!taxName) { Swal.fire({ icon: 'error', title: 'Erreur', text: 'Nom de taxe requis' }); return; }
                if (isNaN(taxRate) || taxRate <= 0) { Swal.fire({ icon: 'error', title: 'Erreur', text: 'Taux de taxe valide requis' }); return; }
            }
            Swal.fire({
                title: "Confirmation",
                text: "Voulez-vous vraiment " + (<?= json_encode($isEdit) ?> ? "mettre à jour" : "créer") + " ce proforma ?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Oui",
                cancelButtonText: "Annuler"
            }).then((result) => {
                if (result.isConfirmed) {
                    var btn = $('#<?= $submitBtn; ?>');
                    var original = btn.html();
                    btn.html('<i class="fa fa-spinner fa-spin"></i> ...').prop('disabled', true);
                    fetch(form.action, { method: "POST", body: new FormData(form) })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire({ title: "Succès", text: data.message, icon: "success" }).then(() => {
                                    window.location.href = data.redirect_url || '<?= base_url('admin/proforma') ?>';
                                });
                            } else {
                                var msg = data.message || (data.error ? Object.values(data.error).join('\n') : 'Erreur');
                                Swal.fire({ icon: 'error', title: 'Erreur', html: msg.replace(/\n/g, '<br>') });
                            }
                        })
                        .catch(err => Swal.fire({ icon: 'error', title: 'Erreur', text: 'Erreur réseau' }))
                        .finally(() => { btn.html(original).prop('disabled', false); });
                }
            });
        });

        // Initialisation
        <?php if ($isEdit): ?>
        // En mode édition : initialisation asynchrone de chaque ligne
        $('.repeater-item').each(function() { initExistingLine($(this)); });
        <?php else: ?>
        $('.repeater-item').each(function() { handleItemTypeChange($(this)); });
        <?php endif; ?>
        calculateTotals();
    });
</script>