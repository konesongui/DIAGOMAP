<?php
// Set all the form data
$formID     = 'quoteForm';
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
        color: #333;
    }
    .total-price-after-discount {
        font-weight: bold;
        margin-top: 5px;
        color: #d35400;
    }
    .discount-field {
        background-color: #fffde7;
    }
    .discount-type-group {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 5px;
    }
    .discount-type-btn {
        padding: 4px 8px;
        font-size: 12px;
        border: 1px solid #ccc;
        background: #f8f9fa;
        cursor: pointer;
        border-radius: 3px;
    }
    .discount-type-btn.active {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }
    .discount-input-group {
        display: flex;
        align-items: center;
    }
    .discount-symbol {
        padding: 0 8px;
        background: #e9ecef;
        border: 1px solid #ced4da;
        border-right: none;
        height: 38px;
        display: flex;
        align-items: center;
        border-radius: 4px 0 0 4px;
        font-size: 14px;
    }
    .global-discount-row {
        background-color: #fff3cd;
        border-left: 4px solid #ffc107;
    }
    .global-discount-inputs {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-top: 5px;
    }
    .global-discount-type-group {
        display: flex;
        gap: 5px;
    }
    .global-discount-btn {
        padding: 4px 12px;
        font-size: 12px;
        border: 1px solid #ced4da;
        background: #f8f9fa;
        cursor: pointer;
        border-radius: 4px;
    }
    .global-discount-btn.active {
        background: #28a745;
        color: white;
        border-color: #28a745;
    }
    .tax-options {
        margin-bottom: 10px;
    }
    .other-tax-container {
        margin-top: 10px;
        display: none;
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

                <form action="<?php echo site_url('admin/quoteitem/update') ?>" id="<?= $formID; ?>" method="post" accept-charset="utf-8">
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
                            <div class="form-group" hidden>
                                <label>User<small class="req"> *</small></label>
                                <input id="user_name" name="user_name" readonly type="text" class="form-control" value="<?php echo $quote['user_name']; ?>" />
                            </div>
                            <div class="form-group col-md-4">
                                <label>Client</label><small class="req"> *</small>
                                <select class="form-control" name="customer">
                                    <option value="">Sélectionner</option>
                                    <?php foreach ($clients as $client) { ?>
                                        <option value="<?php echo $client['id']; ?>" <?php echo ((int)$client['id'] == (int)$quote['customer_id']) ? 'selected' : ''; ?>>
                                            <?php echo $client['item_supplier'] .' ' . $client['lastname']. ' (' . $client['phone'] . ')'; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label>Date <small class="req">*</small></label>
                                <input id="quote_date" name="quote_date" type="text" class="form-control date" value="<?php echo date('d/m/Y', strtotime($quote['quote_date'])); ?>" />
                            </div>

                            <div class="form-group col-md-4">
                                <label>Date de validité</label>
                                <input id="valid_until" name="valid_until" type="text" class="form-control date" value="<?php echo date('d/m/Y', strtotime($quote['valid_until'])); ?>" />
                            </div>

                            <div class="clearfix"></div>
                            <hr>

                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Termes de paiement</label>
                                    <textarea name="payment_terms" class="form-control"><?php echo $quote['payment_terms']; ?></textarea>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Termes de livraison</label>
                                    <textarea name="delivery_terms" class="form-control"><?php echo $quote['delivery_terms']; ?></textarea>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Lieu de livraison</label>
                                    <textarea name="delivery_location" class="form-control"><?php echo $quote['delivery_location']; ?></textarea>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Mode de paiement</label>
                                    <select class="form-control" id="payment_method" name="payment_method">
                                        <option value="">Sélectionner...</option>
                                        <option value="Espèces" <?php echo ($quote['payment_method'] == 'Espèces') ? 'selected' : ''; ?>>Espèces</option>
                                        <option value="Chèque" <?php echo ($quote['payment_method'] == 'Chèque') ? 'selected' : ''; ?>>Chèque</option>
                                        <option value="Virement" <?php echo ($quote['payment_method'] == 'Virement') ? 'selected' : ''; ?>>Virement</option>
                                        <option value="Carte bancaire" <?php echo ($quote['payment_method'] == 'Carte bancaire') ? 'selected' : ''; ?>>Carte bancaire</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Objet</label>
                                    <input id="objet" name="objet" type="text" class="form-control" value="<?php echo $quote['objet']; ?>"/>
                                </div>
                            </div>

                            <div class="clearfix"></div>
                            <hr>

                            <div class="col-md-12">
                                <h4>Articles / Services du devis</h4>
                                <div id="items-container">
                                    <?php foreach ($quote['items'] as $item) {
                                        $discountType = isset($item['discount_type']) ? $item['discount_type'] : 'percent';
                                        $discountValue = isset($item['discount']) ? $item['discount'] : 0;
                                        $itemType = isset($item['item_type']) ? $item['item_type'] : 'product';
                                        ?>
                                        <div class="repeater-item">
                                            <div class="row">
                                                <!-- Type (produit/service) -->
                                                <div class="form-group col-md-1">
                                                    <label>Type</label>
                                                    <select name="item_type[]" class="form-control item-type">
                                                        <option value="product" <?= $itemType == 'product' ? 'selected' : '' ?>>Produit</option>
                                                        <option value="service" <?= $itemType == 'service' ? 'selected' : '' ?>>Service</option>
                                                    </select>
                                                </div>
                                                <!-- Catégorie (visible uniquement pour produit) -->
                                                <div class="form-group col-md-2 cat-group" <?= $itemType == 'service' ? 'style="display:none;"' : '' ?>>
                                                    <label>Catégorie <small class="req">*</small></label>
                                                    <input type="text" name="item_category[]" class="form-control item-category" list="category-list" value="<?php echo $item['category_name']; ?>" placeholder="Sélectionner ou enregistrer une catégorie" <?= $itemType == 'product' ? 'required' : '' ?>>
                                                    <datalist id="category-list">
                                                        <?php foreach ($itemcatlist as $category): ?>
                                                        <option value="<?= $category['item_category'] ?>">
                                                            <?php endforeach; ?>
                                                    </datalist>
                                                </div>
                                                <!-- Article / Service -->
                                                <div class="form-group col-md-2">
                                                    <label>Article / Service <small class="req">*</small></label>
                                                    <input type="text" name="item_name[]" class="form-control item-name" value="<?php echo $item['item_name']; ?>" placeholder="Sélectionner" required>
                                                    <datalist class="item-datalist"></datalist>
                                                </div>
                                                <!-- Unité / Durée -->
                                                <div class="form-group col-md-1">
                                                    <label>Unité / Durée</label>
                                                    <input type="text" name="unit[]" class="form-control unit" value="<?php echo $item['unit']; ?>">
                                                </div>
                                                <!-- Quantité -->
                                                <div class="form-group col-md-1">
                                                    <label>Quantité <small class="req">*</small></label>
                                                    <input type="number" name="quantity[]" class="form-control quantity" value="<?php echo $item['quantity']; ?>" min="1" required>
                                                    <div class="availability" <?= $itemType == 'service' ? 'style="display:none;"' : '' ?>>
                                                        Stock: <span class="available-qty">0</span>
                                                    </div>
                                                </div>
                                                <!-- Prix unitaire -->
                                                <div class="form-group col-md-1">
                                                    <label>Prix unitaire</label>
                                                    <input type="number" name="price[]" class="form-control price" step="0.01" value="<?php echo $item['unit_price']; ?>">
                                                </div>
                                                <!-- Remise -->
                                                <div class="form-group col-md-2">
                                                    <label>Remise</label>
                                                    <div class="discount-type-group">
                                                        <button type="button" class="discount-type-btn <?php echo $discountType == 'percent' ? 'active' : ''; ?>" data-type="percent">%</button>
                                                        <button type="button" class="discount-type-btn <?php echo $discountType == 'amount' ? 'active' : ''; ?>" data-type="amount">FCFA</button>
                                                        <input type="hidden" name="discount_type[]" class="discount-type" value="<?php echo $discountType; ?>">
                                                    </div>
                                                    <div class="discount-input-group">
                                                        <span class="discount-symbol">
                                                            <span class="discount-symbol-text"><?php echo $discountType == 'percent' ? '%' : 'FCFA'; ?></span>
                                                        </span>
                                                        <input type="number" name="discount[]" class="form-control discount discount-field" step="0.01" value="<?php echo $discountValue; ?>" placeholder="0.00">
                                                    </div>
                                                </div>
                                                <!-- P.U NET -->
                                                <div class="form-group col-md-1">
                                                    <label>P.U NET</label>
                                                    <div class="total-price">0.00</div>
                                                </div>
                                                <!-- MONTANT.NET -->
                                                <div class="form-group col-md-1">
                                                    <label>MONTANT.NET</label>
                                                    <div class="total-price-after-discount">0.00</div>
                                                    <input type="hidden" name="line_total_after_discount[]" class="line-total-after-discount" value="<?php echo isset($item['line_total_after_discount']) ? $item['line_total_after_discount'] : $item['line_total']; ?>">
                                                </div>
                                                <!-- Suppression -->
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
                                    <i class="fa fa-plus"></i> Ajouter une ligne
                                </button>
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
                                                <input type="hidden" name="global_discount_type" id="global_discount_type" value="<?php echo isset($quote['global_discount_type']) ? $quote['global_discount_type'] : 'percent'; ?>">
                                            </div>
                                            <div class="discount-input-group">
                                                <span class="discount-symbol">
                                                    <span class="discount-symbol-text" id="global_discount_symbol"><?php echo (isset($quote['global_discount_type']) && $quote['global_discount_type'] == 'amount') ? 'FCFA' : '%'; ?></span>
                                                </span>
                                                <input type="number" id="global_discount_amount" name="global_discount_amount" class="form-control" step="0.01" value="<?php echo isset($quote['global_discount_amount']) ? $quote['global_discount_amount'] : 0; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Options de taxe -->
                                <div class="form-group tax-options">
                                    <label>Options de taxe</label>
                                    <?php
                                    $tax_option = isset($quote['tax_option']) ? $quote['tax_option'] : 'none';
                                    ?>
                                    <div class="radio"><label><input type="radio" name="tax_option" value="none" <?= $tax_option == 'none' ? 'checked' : '' ?>> Aucune taxe</label></div>
                                    <div class="radio"><label><input type="radio" name="tax_option" value="tva" <?= $tax_option == 'tva' ? 'checked' : '' ?>> Appliquer la TVA (18%)</label></div>
                                    <div class="radio"><label><input type="radio" name="tax_option" value="other" <?= $tax_option == 'other' ? 'checked' : '' ?>> Autre taxe</label></div>
                                    <div class="other-tax-container" id="other_tax_container" style="display: <?= $tax_option == 'other' ? 'block' : 'none' ?>;">
                                        <div class="row">
                                            <div class="col-md-6"><label>Nom de la taxe</label><input type="text" name="other_tax_name" id="other_tax_name" class="form-control" value="<?= isset($quote['other_tax_name']) ? $quote['other_tax_name'] : '' ?>"></div>
                                            <div class="col-md-6"><label>Taux (%)</label><div class="input-group"><input type="number" name="other_tax_rate" id="other_tax_rate" class="form-control" step="0.01" value="<?= isset($quote['other_tax_rate']) ? $quote['other_tax_rate'] : '' ?>"><span class="input-group-addon">%</span></div></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <table class="table table-bordered">
                                    <table>
                                        <td>Total HT:</td>
                                        <td class="text-right"><span id="total_ht">0.00</span><input type="hidden" name="total_ht" id="totalHT"></td>
                                        </tr>
                                        <tr>
                                            <td>Total Remise:</td>
                                            <td class="text-right"><span id="total_discount">0.00</span><input type="hidden" name="total_discount" id="totalDiscount"></td>
                                        </tr>
                                        <tr>
                                            <td>Montant Net HT:</td>
                                            <td class="text-right"><span id="total_after_discount">0.00</span><input type="hidden" name="total_after_discount" id="totalAfterDiscount"></td>
                                        </tr>
                                        <tr class="tva-row" style="display:none;">
                                            <td>TVA (18%):</td>
                                            <td class="text-right"><span id="tva_amount">0.00</span><input type="hidden" name="tva_amount" id="tvaAmount" value="0"><input type="hidden" name="tva_rate" value="18"></td>
                                        </tr>
                                        <tr class="other-tax-row" style="display:none;">
                                            <td id="other_tax_label">Autre taxe:</td>
                                            <td class="text-right"><span id="other_tax_amount">0.00</span><input type="hidden" name="other_tax_amount" id="otherTaxAmount" value="0"><input type="hidden" name="other_tax_rate" id="otherTaxRate" value="0"></td>
                                        </tr>
                                        <tr class="ttc-row">
                                            <td><strong>Total TTC:</strong></td>
                                            <td class="text-right"><strong><span id="total_ttc">0.00</span></strong><input type="hidden" name="total_ttc" id="totalTTC"></td>
                                        </tr>
                                    </table>
                            </div>
                        </div>
                        <a href="<?php echo base_url('admin/quoteitem'); ?>" class="btn btn-default">
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
<script type="text/javascript">
    var base_url = '<?= base_url() ?>';
    document.addEventListener('DOMContentLoaded', function() {
        // ========== CALCUL DES TOTAUX ==========
        function calculateItemTotal(item) {
            const $item = $(item);
            const qty = parseFloat($item.find('.quantity').val()) || 0;
            const price = parseFloat($item.find('.price').val()) || 0;
            const discount = parseFloat($item.find('.discount').val()) || 0;
            const discType = $item.find('.discount-type').val();

            let discAmount = 0;
            if (discType === 'percent') {
                discAmount = price * (discount / 100);
            } else {
                discAmount = Math.min(discount, price);
            }
            const netPrice = price - discAmount;
            const totalNet = netPrice * qty;

            $item.find('.total-price').text(netPrice.toFixed(2));
            $item.find('.total-price-after-discount').text(totalNet.toFixed(2));
            $item.find('.line-total-after-discount').val(totalNet.toFixed(2));

            return { totalHT: price * qty, discountAmount: discAmount * qty, totalAfterDiscount: totalNet };
        }

        function calculateTotals() {
            let totalHT = 0, totalDiscount = 0, totalAfter = 0;
            $('.repeater-item').each(function() {
                const res = calculateItemTotal(this);
                totalHT += res.totalHT;
                totalDiscount += res.discountAmount;
                totalAfter += res.totalAfterDiscount;
            });

            const globalDiscAmount = parseFloat($('#global_discount_amount').val()) || 0;
            const globalDiscType = $('#global_discount_type').val();
            let globalDisc = 0;
            if (globalDiscAmount > 0) {
                globalDisc = globalDiscType === 'percent' ? totalAfter * globalDiscAmount / 100 : Math.min(globalDiscAmount, totalAfter);
            }
            const finalDiscount = totalDiscount + globalDisc;
            const finalAfter = Math.max(totalAfter - globalDisc, 0);

            // Taxes
            const taxOption = $('input[name="tax_option"]:checked').val();
            let taxAmount = 0;
            if (taxOption === 'tva') {
                taxAmount = finalAfter * 0.18;
                $('#other_tax_label').text('TVA (18%)');
                $('#otherTaxRate').val(18);
            } else if (taxOption === 'other') {
                const otherRate = parseFloat($('#other_tax_rate').val()) || 0;
                taxAmount = finalAfter * (otherRate / 100);
                const taxName = $('#other_tax_name').val() || 'Autre taxe';
                $('#other_tax_label').text(taxName + ' (' + otherRate.toFixed(2) + '%)');
                $('#otherTaxRate').val(otherRate);
            }
            const totalTTC = finalAfter + taxAmount;

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

        // ========== GESTION PRODUIT / SERVICE ==========
        function handleItemTypeChange($row) {
            const type = $row.find('.item-type').val();
            const $catGroup = $row.find('.cat-group');
            const $availability = $row.find('.availability');
            const $unit = $row.find('.unit');
            const $itemName = $row.find('.item-name');
            const $datalist = $row.find('.item-datalist');
            const uniqueId = 'datalist_' + new Date().getTime() + '_' + Math.random();

            if (type === 'product') {
                $catGroup.show();
                $availability.show();
                $unit.attr('readonly', true).attr('placeholder', 'ex: pièce');
                const category = $row.find('.item-category').val();
                if (category) loadProductsByCategory(category, $itemName, $row);
                else $itemName.attr('placeholder', 'Sélectionnez une catégorie');
            } else {
                $catGroup.hide();
                $availability.hide();
                $unit.attr('readonly', false).attr('placeholder', 'ex: heure, forfait');
                $itemName.attr('list', uniqueId);
                $datalist.attr('id', uniqueId);
                $.ajax({
                    url: base_url + 'admin/services/get_services_json',
                    dataType: 'json',
                    success: function(services) {
                        let opts = '<option value="">Sélectionnez un service</option>';
                        $.each(services, function(i, s) {
                            opts += '<option value="' + s.name + '" data-price="' + s.unit_price + '" data-unit="' + (s.duration || 'prestation') + '">';
                        });
                        $datalist.html(opts);
                        $itemName.attr('placeholder', 'Tapez ou choisissez un service');
                    }
                });
            }
            calculateTotals();
        }

        function loadProductsByCategory(category, $input, $row) {
            if (!category) return;
            $input.attr('placeholder', 'Chargement...');
            $.post(base_url + 'admin/quoteitem/get_items_by_category_name', { category_name: category }, function(data) {
                const uniqueId = 'prodlist_' + new Date().getTime();
                $input.attr('list', uniqueId);
                const $datalist = $row.find('.item-datalist');
                $datalist.attr('id', uniqueId);
                let opts = '';
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

        function onItemNameChange($row) {
            const type = $row.find('.item-type').val();
            const val = $row.find('.item-name').val();
            const $datalist = $row.find('.item-datalist');

            if (type === 'product') {
                const option = $datalist.find('option[value="' + val + '"]');
                if (option.length) {
                    $row.find('.unit').val(option.data('unit') || '');
                    $row.find('.price').val(option.data('price') || 0);
                    $row.find('.available-qty').text(option.data('stock') || 0);
                } else {
                    $row.find('.unit').val('');
                    $row.find('.price').val(0);
                    $row.find('.available-qty').text('0');
                }
            } else {
                const opt = $datalist.find('option[value="' + val + '"]');
                if (opt.length) {
                    $row.find('.price').val(opt.data('price') || 0);
                    $row.find('.unit').val(opt.data('unit') || 'prestation');
                    $row.find('.available-qty').text('N/A');
                } else {
                    $row.find('.price').val(0);
                    $row.find('.unit').val('');
                }
            }
            calculateTotals();
        }

        // ========== ÉVÉNEMENTS ==========
        $(document).on('change', '.item-type', function() { handleItemTypeChange($(this).closest('.repeater-item')); });
        $(document).on('change', '.item-category', function() {
            const $row = $(this).closest('.repeater-item');
            if ($row.find('.item-type').val() === 'product') {
                const cat = $(this).val();
                if (cat) loadProductsByCategory(cat, $row.find('.item-name'), $row);
                $row.find('.item-name').val('');
                $row.find('.unit').val('');
                $row.find('.price').val(0);
                $row.find('.available-qty').text('0');
                calculateTotals();
            }
        });
        $(document).on('change', '.item-name', function() { onItemNameChange($(this).closest('.repeater-item')); });
        $(document).on('input', '.quantity, .price, .discount, #global_discount_amount, #other_tax_rate, #other_tax_name', function() { calculateTotals(); });

        // Ajout / suppression de lignes
        $('#add-item').on('click', function() {
            const $first = $('.repeater-item').first();
            const $new = $first.clone();
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

        // Gestion des boutons de remise (article et global)
        function setupDiscountButtons() {
            $('.discount-type-btn').off('click').on('click', function() {
                const $group = $(this).closest('.discount-type-group');
                $group.find('.discount-type-btn').removeClass('active');
                $(this).addClass('active');
                const type = $(this).data('type');
                if ($(this).closest('.repeater-item').length) {
                    const $row = $(this).closest('.repeater-item');
                    $row.find('.discount-type').val(type);
                    $row.find('.discount-symbol-text').text(type === 'percent' ? '%' : 'FCFA');
                } else {
                    $('#global_discount_type').val(type);
                    $('#global_discount_symbol').text(type === 'percent' ? '%' : 'FCFA');
                }
                calculateTotals();
            });
        }
        setupDiscountButtons();

        // Options de taxe
        $('input[name="tax_option"]').change(function() {
            $('#other_tax_container').toggle($(this).val() === 'other');
            calculateTotals();
        });

        // Soumission du formulaire
        $('#<?= $formID; ?>').on('submit', function(e) {
            e.preventDefault();
            const taxOpt = $('input[name="tax_option"]:checked').val();
            if (taxOpt === 'other') {
                const taxName = $('#other_tax_name').val().trim();
                const taxRate = parseFloat($('#other_tax_rate').val());
                if (!taxName) { Swal.fire({ icon: 'error', title: 'Erreur', text: 'Nom de taxe requis' }); return; }
                if (isNaN(taxRate) || taxRate <= 0) { Swal.fire({ icon: 'error', title: 'Erreur', text: 'Taux de taxe valide requis' }); return; }
            }
            Swal.fire({
                title: "Confirmation",
                text: "Voulez-vous vraiment mettre à jour ce devis ?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Oui, mettre à jour",
                cancelButtonText: "Annuler"
            }).then((result) => {
                if (result.isConfirmed) {
                    const submitBtn = $('#<?= $submitBtn; ?>');
                    const originalText = submitBtn.html();
                    submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Mise à jour...').prop('disabled', true);
                    fetch(this.action, {
                        method: "POST",
                        body: new FormData(this)
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire({ title: "Succès", text: data.message, icon: "success" }).then(() => {
                                    window.location.href = data.redirect_url || '<?= base_url('admin/quoteitem') ?>';
                                });
                            } else {
                                let msg = data.message || (data.error ? Object.values(data.error).join('\n') : 'Erreur');
                                Swal.fire({ icon: 'error', title: 'Erreur', html: msg.replace(/\n/g, '<br>') });
                            }
                        })
                        .catch(err => Swal.fire({ icon: 'error', title: 'Erreur', text: 'Erreur réseau' }))
                        .finally(() => { submitBtn.html(originalText).prop('disabled', false); });
                }
            });
        });

        // Initialisation des lignes existantes
        // Initialisation des lignes existantes
        $('.repeater-item').each(function() {
            const $row = $(this);
            const initialType = $row.find('.item-type').val();
            if (initialType === 'product') {
                const category = $row.find('.item-category').val();
                if (category) {
                    loadProductsByCategory(category, $row.find('.item-name'), $row);
                }
                // Calculer le total de la ligne (les prix sont déjà dans les champs)
                calculateItemTotal(this);
            } else {
                // Pour un service, on doit charger les détails via AJAX
                const serviceName = $row.find('.item-name').val();
                if (serviceName) {
                    // Créer un datalist vide et configurer le champ
                    const uniqueId = 'datalist_init_' + new Date().getTime();
                    $row.find('.item-name').attr('list', uniqueId);
                    const $datalist = $row.find('.item-datalist');
                    $datalist.attr('id', uniqueId);
                    // Appel AJAX pour obtenir les détails du service
                    $.ajax({
                        url: base_url + 'admin/services/get_service_details',
                        type: 'POST',
                        data: { name: serviceName },
                        dataType: 'json',
                        success: function(service) {
                            if (service) {
                                $row.find('.unit').val(service.duration || '');
                                $row.find('.price').val(service.unit_price);
                                $row.find('.available-qty').text('N/A');
                                // Ajouter l'option au datalist pour les futures sélections
                                $datalist.html('<option value="' + service.name + '" data-price="' + service.unit_price + '" data-unit="' + (service.duration || 'prestation') + '">');
                            }
                            calculateItemTotal($row[0]);
                            calculateTotals();
                        }
                    });
                } else {
                    // Aucun nom, on initialise normalement
                    handleItemTypeChange($row);
                    calculateItemTotal($row[0]);
                }
            }
        });
        calculateTotals();

    });
</script>