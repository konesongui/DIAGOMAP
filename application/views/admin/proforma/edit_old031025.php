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
    .discount-input {
        width: 100%;
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

                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail1">Client</label><small class="req"> *</small>
                                <select class="form-control" name="customer">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php foreach ($clients as $client) { ?>
                                        <option value="<?php echo $client['id']; ?>" <?php echo ((int)$client['id'] == (int)$quote['customer_id']) ? 'selected' : ''; ?>>
                                            <?php echo $client['item_supplier'] .' ' . $client['lastname']. ' (' . $client['phone'] . ')'; ?>
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
                                <div class="form-group col-md-4">
                                    <label for="payment_method">Méthode de paiement</label>
                                    <select class="form-control select2" id="payment_method" name="payment_method">
                                        <option value="">Sélectionner...</option>
                                        <option value="cash" <?php echo ($quote['payment_method'] == 'cash') ? 'selected' : ''; ?>>Espèces</option>
                                        <option value="check" <?php echo ($quote['payment_method'] == 'check') ? 'selected' : ''; ?>>Chèque</option>
                                        <option value="bank_transfer" <?php echo ($quote['payment_method'] == 'bank_transfer') ? 'selected' : ''; ?>>Virement</option>
                                        <option value="card" <?php echo ($quote['payment_method'] == 'card') ? 'selected' : ''; ?>>Carte bancaire</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Objet <small class="req">*</small></label>
                                    <input id="objet" name="objet" type="text" class="form-control" value="<?php echo $quote['objet']; ?>"/>
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
                                                <div class="form-group col-md-2">
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
                                                <div class="form-group col-md-2">
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
                                                <div class="form-group col-md-2">
                                                    <label>Remise</label>
                                                    <div class="input-group">
                                                        <input type="number" name="discount[]" class="form-control discount" min="0" step="0.01" value="<?php echo isset($item['discount']) ? $item['discount'] : 0; ?>" placeholder="0.00">
                                                        <div class="input-group-addon">
                                                            <select name="discount_type[]" class="discount-type">
                                                                <option value="amount" <?php echo (isset($item['discount_type']) && $item['discount_type'] == 'amount') ? 'selected' : ''; ?>>FCFA</option>
                                                                <option value="percent" <?php echo (isset($item['discount_type']) && $item['discount_type'] == 'percent') ? 'selected' : ''; ?>>%</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>Total</label>
                                                    <div class="total-price"><?php echo number_format($item['line_total'], 2); ?></div>
                                                    <input type="hidden" name="line_total[]" class="line-total" value="<?php echo $item['line_total']; ?>">
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
                                    <tr>
                                        <td>Remise globale:</td>
                                        <td class="text-right">
                                            <div class="input-group" style="max-width: 150px; float: right;">
                                                <input type="number" id="global_discount" name="global_discount" class="form-control" min="0" step="0.01" value="<?php echo isset($quote['global_discount']) ? $quote['global_discount'] : 0; ?>">
                                                <div class="input-group-addon">
                                                    <select id="global_discount_type" name="global_discount_type" class="form-control">
                                                        <option value="amount" <?php echo (isset($quote['global_discount_type']) && $quote['global_discount_type'] == 'amount') ? 'selected' : ''; ?>>FCFA</option>
                                                        <option value="percent" <?php echo (isset($quote['global_discount_type']) && $quote['global_discount_type'] == 'percent') ? 'selected' : ''; ?>>%</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Total après remise:</td>
                                        <td class="text-right"><span id="total_after_discount"><?php echo number_format($quote['total_ht'], 2); ?></span></td>
                                        <input type="hidden" name="total_after_discount" id="totalAfterDiscount" value="<?php echo $quote['total_ht']; ?>">
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
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/quote/actions.js"></script>

<script type="text/javascript">
    // Fonction pour calculer le total d'un article avec remise
    function calculateItemTotal(quantity, price, discount, discountType) {
        quantity = parseFloat(quantity) || 0;
        price = parseFloat(price) || 0;
        discount = parseFloat(discount) || 0;

        let total = quantity * price;

        if (discount > 0) {
            if (discountType === 'percent') {
                total = total - (total * discount / 100);
            } else {
                total = total - discount;
            }
        }

        return Math.max(0, total); // Éviter les totaux négatifs
    }

    // Fonction pour calculer les totaux globaux
    function calculateTotals() {
        let totalHT = 0;

        // Calculer le total HT de tous les articles
        $('.repeater-item').each(function() {
            const quantity = $(this).find('.quantity').val();
            const price = $(this).find('.price').val();
            const discount = $(this).find('.discount').val();
            const discountType = $(this).find('.discount-type').val();

            const itemTotal = calculateItemTotal(quantity, price, discount, discountType);
            totalHT += itemTotal;

            // Mettre à jour l'affichage du total de l'article
            $(this).find('.total-price').text(itemTotal.toFixed(2));
            $(this).find('.line-total').val(itemTotal);
        });

        // Appliquer la remise globale
        const globalDiscount = parseFloat($('#global_discount').val()) || 0;
        const globalDiscountType = $('#global_discount_type').val();
        let totalAfterDiscount = totalHT;

        if (globalDiscount > 0) {
            if (globalDiscountType === 'percent') {
                totalAfterDiscount = totalHT - (totalHT * globalDiscount / 100);
            } else {
                totalAfterDiscount = totalHT - globalDiscount;
            }
        }

        totalAfterDiscount = Math.max(0, totalAfterDiscount);

        // Calculer la TVA
        const applyTVA = $('#apply_tva').is(':checked');
        const tvaRate = <?php echo $quote['tva_rate']; ?>;
        const tvaAmount = applyTVA ? totalAfterDiscount * tvaRate / 100 : 0;
        const totalTTC = totalAfterDiscount + tvaAmount;

        // Mettre à jour l'affichage
        $('#total_ht').text(totalHT.toFixed(2));
        $('#totalHT').val(totalHT);
        $('#total_after_discount').text(totalAfterDiscount.toFixed(2));
        $('#totalAfterDiscount').val(totalAfterDiscount);
        $('#tva_amount').text(tvaAmount.toFixed(2));
        $('#tvaAmount').val(tvaAmount);
        $('#total_ttc').text(totalTTC.toFixed(2));
        $('#totalTTC').val(totalTTC);
    }

    // Événements pour le calcul automatique
    $(document).ready(function() {
        // Calcul initial
        calculateTotals();

        // Événements sur les champs des articles
        $(document).on('input', '.quantity, .price, .discount', function() {
            calculateTotals();
        });

        $(document).on('change', '.discount-type', function() {
            calculateTotals();
        });

        // Événements sur la remise globale
        $('#global_discount, #global_discount_type').on('input change', function() {
            calculateTotals();
        });

        // Événement sur la case à cocher TVA
        $('#apply_tva').on('change', function() {
            if ($(this).is(':checked')) {
                $('.tva-row').show();
            } else {
                $('.tva-row').hide();
            }
            calculateTotals();
        });

        // Gestion de l'ajout/suppression d'articles (si votre script actions.js le gère)
        $(document).on('click', '#add-item', function() {
            // Votre code existant pour ajouter un article...
            // Puis appeler calculateTotals() après l'ajout
            setTimeout(calculateTotals, 100);
        });

        $(document).on('click', '.remove-item', function() {
            $(this).closest('.repeater-item').remove();
            calculateTotals();
        });
    });
</script>

<script>// Calcule le total d'un article avec sa remise
    function calculateItemTotal(item) {
        const quantity = parseFloat(item.querySelector('.quantity').value) || 0;
        const price = parseFloat(item.querySelector('.price').value) || 0;
        const discountAmount = parseFloat(item.querySelector('.discount-amount').value) || 0;
        const discountType = item.querySelector('.discount-type').value;

        let brut = quantity * price;
        let discount = 0;

        if (discountAmount > 0) {
            if (discountType === 'percent') {
                discount = brut * (discountAmount / 100);
            } else {
                discount = discountAmount;
            }
        }

        const net = Math.max(brut - discount, 0);

        // Affiche uniquement le net dans la ligne produit
        item.querySelector('.total-price').textContent = net.toFixed(2);

        return { brut, discount, net };
    }

    function calculateGlobalTotals() {
        let totalBrut = 0;
        let totalRemiseProduits = 0;
        let totalNetProduits = 0;

        const items = document.querySelectorAll('.repeater-item');
        items.forEach(item => {
            const { brut, discount, net } = calculateItemTotal(item);
            totalBrut += brut;
            totalRemiseProduits += discount;
            totalNetProduits += net;
        });

        // Calcul de la remise globale
        const globalDiscountAmount = parseFloat(document.getElementById('global_discount_amount').value) || 0;
        const globalDiscountType = document.getElementById('global_discount_type').value;
        let globalDiscount = 0;

        if (globalDiscountAmount > 0) {
            if (globalDiscountType === 'percent') {
                globalDiscount = totalNetProduits * (globalDiscountAmount / 100);
            } else {
                globalDiscount = globalDiscountAmount;
            }
        }

        const totalRemises = totalRemiseProduits + globalDiscount;
        const totalAfterDiscount = Math.max(totalBrut - totalRemises, 0);

        // TVA
        const applyTVA = document.getElementById('apply_tva').checked;
        const tvaRate = 0.18;
        const tvaAmount = applyTVA ? totalAfterDiscount * tvaRate : 0;
        const totalTTC = totalAfterDiscount + tvaAmount;

        // Mise à jour affichage
        document.getElementById('total_brut').textContent = totalBrut.toFixed(2);
        document.getElementById('totalBrut').value = totalBrut.toFixed(2);

        document.getElementById('total_remise').textContent = totalRemises.toFixed(2);
        document.getElementById('totalRemise').value = totalRemises.toFixed(2);

        document.getElementById('total_after_discount').textContent = totalAfterDiscount.toFixed(2);
        document.getElementById('totalAfterDiscount').value = totalAfterDiscount.toFixed(2);

        document.getElementById('tva_amount').textContent = tvaAmount.toFixed(2);
        document.getElementById('tvaAmount').value = tvaAmount.toFixed(2);

        document.getElementById('total_ttc').textContent = totalTTC.toFixed(2);
        document.getElementById('totalTTC').value = totalTTC.toFixed(2);

        document.querySelector('.tva-row').style.display = applyTVA ? '' : 'none';
    }
</script>