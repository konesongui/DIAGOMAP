<?php
$formID     = 'quotesForm';
$submitBtn  = 'submitBtn';
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
    .discount-field { background-color: #fffde7; }
    .discount-type-group { display: flex; align-items: center; gap: 5px; margin-bottom: 5px; }
    .discount-type-btn { padding: 4px 8px; font-size: 12px; border: 1px solid #ccc; background: #f8f9fa; cursor: pointer; border-radius: 3px; }
    .discount-type-btn.active { background: #007bff; color: white; border-color: #007bff; }
    .discount-input-group { display: flex; align-items: center; }
    .discount-symbol { padding: 0 8px; background: #e9ecef; border: 1px solid #ced4da; border-right: none; height: 38px; display: flex; align-items: center; border-radius: 4px 0 0 4px; }
    .tax-options { margin-bottom: 10px; }
    .other-tax-container { margin-top: 10px; display: none; }
    .payment-module { background: #f8f9fa; border: 2px solid #28a745; border-radius: 8px; padding: 20px; margin-top: 20px; margin-bottom: 20px; }
    .payment-title { color: #28a745; margin-bottom: 15px; font-weight: bold; }
    .badge-payment { font-size: 14px; padding: 5px 10px; }
    .global-discount-row { background-color: #fff3cd; border-left: 4px solid #ffc107; }
    .global-discount-inputs { display: flex; gap: 10px; align-items: center; margin-top: 5px; }
    .global-discount-type-group { display: flex; gap: 5px; }
    .global-discount-btn { padding: 4px 12px; font-size: 12px; border: 1px solid #ced4da; background: #f8f9fa; cursor: pointer; border-radius: 4px; }
    .global-discount-btn.active { background: #28a745; color: white; border-color: #28a745; }

    /* Styles pour les boutons de déplacement */
    .move-btn {
        padding: 2px 8px;
        margin: 0 2px;
        font-size: 14px;
        cursor: pointer;
        border: 1px solid #ddd;
        border-radius: 3px;
        background: #fff;
        color: #333;
        transition: all 0.2s;
    }
    .move-btn:hover {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }
    .move-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background: #f5f5f5;
    }
    .move-buttons {
        display: flex;
        gap: 3px;
        margin-top: 5px;
    }
    .drag-handle {
        cursor: grab;
        color: #999;
        font-size: 18px;
        padding: 0 5px;
        user-select: none;
        position: absolute;
        top: 10px;
        right: 10px;
    }
    .drag-handle:hover {
        color: #333;
    }
    .drag-handle:active {
        cursor: grabbing;
    }
    .item-index {
        position: absolute;
        top: 10px;
        left: 10px;
        background: #007bff;
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        text-align: center;
        line-height: 24px;
        font-size: 12px;
        font-weight: bold;
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
                    <div class="box-tools pull-right">
                        <span class="badge bg-green badge-payment"><i class="fa fa-money"></i> Caisse enregistreuse</span>
                    </div>
                </div>

                <form action="<?php echo site_url('admin/selling/update') ?>" id="<?= $formID; ?>" method="post" accept-charset="utf-8">
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
                                <label>User</label>
                                <input id="user_name" name="user_name" readonly class="form-control" value="<?php echo $quote['user_name']; ?>" />
                            </div>

                            <!-- Client avec option "Nouveau client" -->
                            <div class="form-group col-md-4">
                                <label>Client</label><small class="req"> *</small>
                                <select class="form-control" name="customer" id="customer_select">
                                    <?php foreach ($clients as $client) { ?>
                                        <option value="<?php echo $client['id']; ?>" <?php echo ((int)$client['id'] == (int)$quote['customer_id']) ? 'selected' : ''; ?>>
                                            <?php echo $client['item_supplier'] . ' ' . $client['lastname'] . ' (' . $client['phone'] . ')'; ?>
                                        </option>
                                    <?php } ?>
                                    <option value="new">➕ Nouveau client</option>
                                </select>
                                <span class="text-danger"><?php echo form_error('customer'); ?></span>
                            </div>

                            <!-- Champs nouveau client -->
                            <div class="form-group col-md-4 new-client-fields" style="display:none;">
                                <label>Nom du client</label><small class="req">*</small>
                                <input type="text" name="new_client_name" class="form-control" placeholder="Nom du client">
                            </div>
                            <div class="form-group col-md-4 new-client-fields" style="display:none;">
                                <label>Téléphone</label><small class="req">*</small>
                                <input type="text" name="new_client_phone" class="form-control" placeholder="Téléphone">
                            </div>
                            <div class="form-group col-md-4 new-client-fields" style="display:none;">
                                <label>Email</label><small class="req">*</small>
                                <input type="text" name="new_client_email" class="form-control" placeholder="Email">
                            </div>

                            <div class="form-group col-md-4">
                                <label>Date</label><small class="req">*</small>
                                <input id="quote_date" name="quote_date" type="text" class="form-control date" value="<?php echo date('d/m/Y', strtotime($quote['quote_date'])); ?>"/>
                            </div>
                            <div class="form-group col-md-4" hidden>
                                <label>Date de validité</label>
                                <input id="valid_until" name="valid_until" type="text" class="form-control date" value="<?php echo date('d/m/Y', strtotime($quote['valid_until'])); ?>" readonly />
                            </div>
                        </div>

                        <div class="row" hidden>
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
                                <label>Objet</label>
                                <input id="objet" name="objet" type="text" class="form-control" value="<?php echo $quote['objet']; ?>"/>
                            </div>
                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <h4>Choisissez les articles <small class="text-muted">(Glissez-déposez pour réorganiser)</small></h4>
                                <div id="items-container">
                                    <?php
                                    $itemIndex = 0;
                                    foreach ($quote['items'] as $item) {
                                        $discountType = isset($item['discount_type']) ? $item['discount_type'] : 'percent';
                                        $discountValue = isset($item['discount']) ? $item['discount'] : 0;
                                        $price_ttc = isset($item['unit_price_ttc']) ? $item['unit_price_ttc'] : $item['unit_price'];
                                        ?>
                                        <div class="repeater-item" data-index="<?= $itemIndex ?>" data-item-type="product">
                                            <span class="item-index"><?= $itemIndex + 1 ?></span>
                                            <span class="drag-handle"><i class="fa fa-arrows-alt"></i></span>
                                            <div class="row">
                                                <div class="form-group col-md-2">
                                                    <label>Catégorie</label>
                                                    <input type="text" name="item_category[]" class="form-control item-category" list="category-list" value="<?php echo $item['category_name']; ?>" placeholder="Sélectionnez ou enregistrer une catégorie" required>
                                                    <datalist id="category-list">
                                                        <?php foreach ($itemcatlist as $category): ?>
                                                        <option value="<?= $category['item_category'] ?>">
                                                            <?php endforeach; ?>
                                                    </datalist>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label>Article</label>
                                                    <input type="text" name="item_name[]" class="form-control item-name" list="item-list-<?= rand() ?>" value="<?php echo $item['item_name']; ?>" placeholder="Sélectionnez ou enregistrer un article" required>
                                                    <datalist class="item-datalist"></datalist>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>Unité</label>
                                                    <input type="text" name="unit[]" class="form-control unit" value="<?php echo $item['unit']; ?>">
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>Quantité</label>
                                                    <input type="number" name="quantity[]" class="form-control quantity" value="<?php echo $item['quantity']; ?>" min="1" required>
                                                    <div class="availability">Stock: <span class="available-qty">0</span></div>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>Prix TTC</label>
                                                    <input type="number" name="price[]" class="form-control price" min="0" step="0.01" value="<?php echo $price_ttc; ?>">
                                                    <small class="text-muted">Toutes taxes comprises</small>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label>Remise</label>
                                                    <div class="discount-type-group">
                                                        <button type="button" class="discount-type-btn <?= $discountType == 'percent' ? 'active' : '' ?>" data-type="percent">%</button>
                                                        <button type="button" class="discount-type-btn <?= $discountType == 'amount' ? 'active' : '' ?>" data-type="amount">FCFA</button>
                                                        <input type="hidden" name="discount_type[]" class="discount-type" value="<?= $discountType ?>">
                                                    </div>
                                                    <div class="discount-input-group">
                                                        <span class="discount-symbol"><span class="discount-symbol-text"><?= $discountType == 'percent' ? '%' : 'FCFA' ?></span></span>
                                                        <input type="number" name="discount[]" class="form-control discount discount-field" min="0" step="0.01" value="<?= $discountValue ?>" placeholder="0.00">
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>P.U HT</label>
                                                    <div class="total-price">0.00</div>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>MONTANT HT</label>
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
                                        <?php
                                        $itemIndex++;
                                    }
                                    ?>
                                </div>
                                <button type="button" id="add-item" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Ajouter un article</button>
                            </div>
                        </div>

                        <!-- MODULE DE PAIEMENT COMPLET -->
                        <div class="payment-module">
                            <h4 class="payment-title"><i class="fa fa-money"></i> Module de paiement</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>💰 Montant payé</label>
                                        <input type="number" id="amount_paid" name="amount_paid" class="form-control" step="0.01" placeholder="0.00" value="<?php echo isset($quote['amount_paid']) ? $quote['amount_paid'] : 0; ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>💳 Reste à payer</label>
                                        <input type="text" id="remaining_amount" class="form-control" readonly placeholder="0.00" style="background-color: #fff3cd;">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>🔄 Montant rendu</label>
                                        <input type="text" id="change_amount" class="form-control" readonly placeholder="0.00" style="background-color: #d4edda;">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>📝 Statut du paiement</label>
                                        <select class="form-control" id="payment_status" name="payment_status">
                                            <option value="pending" <?php echo (isset($quote['payment_status']) && $quote['payment_status'] == 'pending') ? 'selected' : ''; ?>>⏳ En attente de paiement</option>
                                            <option value="partial" <?php echo (isset($quote['payment_status']) && $quote['payment_status'] == 'partial') ? 'selected' : ''; ?>>💸 Paiement partiel</option>
                                            <option value="paid" <?php echo (isset($quote['payment_status']) && $quote['payment_status'] == 'paid') ? 'selected' : ''; ?>>✅ Payé totalement</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>🏦 Mode de paiement</label>
                                        <select class="form-control" id="payment_method_type" name="payment_method_type">
                                            <option value="">Sélectionner...</option>
                                            <option value="cash" <?php echo (isset($quote['payment_method_type']) && $quote['payment_method_type'] == 'cash') ? 'selected' : ''; ?>>Espèces (Caisse)</option>
                                            <option value="bank" <?php echo (isset($quote['payment_method_type']) && $quote['payment_method_type'] == 'bank') ? 'selected' : ''; ?>>Paiement bancaire</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="source_selection" style="display: none;">
                                <div class="col-md-12">
                                    <div class="form-group" id="caisse_group">
                                        <label>🏦 Sélectionner la caisse</label>
                                        <select class="form-control" id="caisse_id" name="caisse_id">
                                            <option value="">Sélectionner une caisse...</option>
                                            <?php foreach ($caisses as $caisse): ?>
                                                <option value="<?= $caisse->id ?>" data-balance="<?= $caisse->amount_re ?>" <?php echo (isset($quote['caisse_id']) && $quote['caisse_id'] == $caisse->id) ? 'selected' : ''; ?>>
                                                    <?= htmlspecialchars($caisse->name) ?> (Solde: <?= number_format($caisse->amount_re, 0, ',', ' ') ?> FCFA)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group" id="banque_group" style="display: none;">
                                        <label>🏦 Sélectionner la banque</label>
                                        <select class="form-control" id="banque_id" name="banque_id">
                                            <option value="">Sélectionner une banque...</option>
                                            <?php foreach ($banques as $banque): ?>
                                                <option value="<?= $banque->id ?>" data-balance="<?= $banque->balance ?>" <?php echo (isset($quote['banque_id']) && $quote['banque_id'] == $banque->id) ? 'selected' : ''; ?>>
                                                    <?= htmlspecialchars($banque->name) ?> (Solde: <?= number_format($banque->balance, 0, ',', ' ') ?> FCFA)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="source_info" style="display: none;">
                                <div class="col-md-12">
                                    <div class="alert alert-info" style="margin-top: 10px;">
                                        <i class="fa fa-info-circle"></i> <span id="selected_source_text"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box-footer">
                            <div class="row">
                                <div class="col-md-8">
                                    <!-- Options de taxes -->
                                    <div class="form-group tax-options" hidden>
                                        <label>Options de taxe</label>
                                        <div class="radio">
                                            <label><input type="radio" name="tax_option" value="none" <?php echo (isset($quote['tax_option']) && $quote['tax_option'] == 'none') ? 'checked' : ''; ?>> Aucune taxe</label>
                                        </div>
                                        <div class="radio">
                                            <label><input type="radio" name="tax_option" value="tva" <?php echo (!isset($quote['tax_option']) || $quote['tax_option'] == 'tva') ? 'checked' : ''; ?>> Appliquer la TVA (18%)</label>
                                        </div>
                                        <div class="radio">
                                            <label><input type="radio" name="tax_option" value="other" <?php echo (isset($quote['tax_option']) && $quote['tax_option'] == 'other') ? 'checked' : ''; ?>> Autre taxe</label>
                                        </div>
                                        <div class="other-tax-container" id="other_tax_container" style="<?php echo (isset($quote['tax_option']) && $quote['tax_option'] == 'other') ? 'display:block;' : 'display:none;'; ?>">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Nom de la taxe</label>
                                                    <input type="text" name="other_tax_name" id="other_tax_name" class="form-control" placeholder="Ex: Taxe spéciale" value="<?php echo isset($quote['other_tax_name']) ? $quote['other_tax_name'] : ''; ?>">
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Taux (%)</label>
                                                    <div class="input-group">
                                                        <input type="number" name="other_tax_rate" id="other_tax_rate" class="form-control" min="0" max="100" step="0.01" placeholder="0.00" value="<?php echo isset($quote['other_tax_rate']) ? $quote['other_tax_rate'] : 0; ?>">
                                                        <span class="input-group-addon">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Remise globale -->
                                    <div class="form-group" hidden>
                                        <label>Remise globale</label>
                                        <div class="global-discount-inputs">
                                            <div class="global-discount-type-group">
                                                <button type="button" class="global-discount-btn <?php echo (isset($quote['global_discount_type']) && $quote['global_discount_type'] == 'percent') ? 'active' : ''; ?>" data-type="percent">%</button>
                                                <button type="button" class="global-discount-btn <?php echo (isset($quote['global_discount_type']) && $quote['global_discount_type'] == 'amount') ? 'active' : ''; ?>" data-type="amount">FCFA</button>
                                                <input type="hidden" name="global_discount_type" id="global_discount_type" value="<?php echo isset($quote['global_discount_type']) ? $quote['global_discount_type'] : 'percent'; ?>">
                                            </div>
                                            <input type="number" name="global_discount_value" id="global_discount_value" class="form-control" min="0" step="0.01" value="<?php echo isset($quote['global_discount_value']) ? $quote['global_discount_value'] : 0; ?>" placeholder="0.00" style="width: 150px;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Tableau des totaux -->
                                <div class="col-md-4">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td><strong>Total TTC brut</strong></td>
                                            <td class="text-right"><strong><span id="total_ttc_brut">0.00</span></strong></td>
                                            <input type="hidden" name="total_ttc_brut" id="totalTTCBrut" value="0">
                                        </tr>
                                        <tr>
                                            <td>Total Remise</th>
                                            <td class="text-right"><span id="total_discount">0.00</span></th>
                                            <input type="hidden" name="total_discount" id="totalDiscount" value="0">
                                        </tr>
                                        <tr>
                                            <td><strong>Total HT (après remises)</strong></th>
                                            <td class="text-right"><strong><span id="total_ht">0.00</span></strong></th>
                                            <input type="hidden" name="total_ht" id="totalHT" value="0">
                                        </tr>
                                        <tr class="tva-row">
                                            <td>TVA (18%) :</th>
                                            <td class="text-right"><span id="tva_amount">0.00</span></th>
                                            <input type="hidden" name="tva_amount" id="tvaAmount" value="0">
                                        </tr>
                                        <tr class="other-tax-row" style="display:none;">
                                            <td id="other_tax_label">Autre taxe :</th>
                                            <td class="text-right"><span id="other_tax_amount">0.00</span></th>
                                            <input type="hidden" name="other_tax_amount" id="otherTaxAmount" value="0">
                                        </tr>
                                        <tr class="ttc-final-row">
                                            <td><strong>Total TTC final</strong></th>
                                            <td class="text-right"><strong><span id="total_ttc_final">0.00</span></strong></th>
                                            <input type="hidden" name="total_ttc" id="totalTTC" value="0">
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <a href="<?php echo base_url('admin/selling'); ?>" class="btn btn-default"><i class="fa fa-arrow-left"></i> Retour</a>
                            <button type="submit" id="<?= $submitBtn; ?>" class="btn btn-primary"><i class="fa fa-save"></i> Enregistrer</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    var base_url = '<?php echo base_url(); ?>';
    var TVA_RATE = 0.18;
    var currentTotalTTC = 0;

    $(function() {
        // ========== FONCTIONS DE DÉPLACEMENT ==========
        function updateItemIndices() {
            $('#items-container .repeater-item').each(function(index) {
                $(this).find('.item-index').text(index + 1);
                $(this).data('index', index);
                // Mettre à jour les boutons de déplacement
                var $upBtn = $(this).find('.move-up');
                var $downBtn = $(this).find('.move-down');
                var total = $('#items-container .repeater-item').length;
                $upBtn.prop('disabled', index === 0);
                $downBtn.prop('disabled', index === total - 1);
            });
        }

        function moveItem($item, direction) {
            var $container = $('#items-container');
            var items = $container.find('.repeater-item');
            var currentIndex = items.index($item);
            var newIndex = currentIndex + direction;

            if (newIndex < 0 || newIndex >= items.length) return;

            if (direction < 0) {
                $item.insertBefore(items.eq(newIndex));
            } else {
                $item.insertAfter(items.eq(newIndex));
            }

            updateItemIndices();
            calculateTotals();
        }

        // ========== DRAG & DROP ==========
        var dragOptions = {
            cursor: 'grabbing',
            opacity: 0.6,
            revert: true,
            revertDuration: 200,
            helper: function(e) {
                var $item = $(this);
                var $clone = $item.clone();
                $clone.css({
                    'width': $item.outerWidth(),
                    'background': '#fff',
                    'box-shadow': '0 5px 15px rgba(0,0,0,0.2)',
                    'border': '2px solid #007bff'
                });
                $item.addClass('dragging');
                return $clone;
            },
            start: function(e, ui) {
                $(this).addClass('dragging');
            },
            stop: function(e, ui) {
                $(this).removeClass('dragging');
                $('#items-container .repeater-item').removeClass('drag-over');
                updateItemIndices();
                calculateTotals();
            }
        };

        var dropOptions = {
            drop: function(e, ui) {
                var $target = $(this);
                var $dragged = ui.draggable;

                if ($dragged.is($target)) return;

                $('#items-container .repeater-item').removeClass('drag-over');

                var targetPos = $target.position();
                var mouseY = e.pageY;
                var targetMid = targetPos.top + $target.outerHeight() / 2;

                if (mouseY < targetMid) {
                    $dragged.insertBefore($target);
                } else {
                    $dragged.insertAfter($target);
                }

                updateItemIndices();
                calculateTotals();
            },
            over: function(e, ui) {
                $(this).addClass('drag-over');
            },
            out: function(e, ui) {
                $(this).removeClass('drag-over');
            }
        };

        function initializeDragAndDrop() {
            $('#items-container .repeater-item').each(function() {
                if (!$(this).data('ui-draggable')) {
                    $(this).draggable(dragOptions);
                    $(this).droppable(dropOptions);
                }
            });
        }

        // Initialiser le drag and drop
        initializeDragAndDrop();
        updateItemIndices();

        // ========== GESTION NOUVEAU CLIENT ==========
        document.getElementById("customer_select").addEventListener("change", function() {
            let val = this.value;
            document.querySelectorAll(".new-client-fields").forEach(el => {
                el.style.display = (val === "new") ? "block" : "none";
            });
        });
        if (document.getElementById("customer_select").value === "new") {
            document.querySelectorAll(".new-client-fields").forEach(el => el.style.display = "block");
        }

        // ========== GESTION DES OPTIONS DE TAXE ==========
        function handleTaxOptions() {
            const selected = document.querySelector('input[name="tax_option"]:checked').value;
            const otherContainer = document.getElementById('other_tax_container');
            otherContainer.style.display = (selected === 'other') ? 'block' : 'none';
            document.querySelector('.tva-row').style.display = (selected === 'tva') ? '' : 'none';
            document.querySelector('.other-tax-row').style.display = (selected === 'other') ? '' : 'none';
            calculateTotals();
        }
        document.querySelectorAll('input[name="tax_option"]').forEach(radio => radio.addEventListener('change', handleTaxOptions));
        handleTaxOptions();

        // ========== CALCUL D'UN ARTICLE ==========
        function calculateItemTotal(itemElement) {
            const $item = $(itemElement);
            const quantity = parseFloat($item.find('.quantity').val()) || 0;
            const priceTTC = parseFloat($item.find('.price').val()) || 0;
            const discount = parseFloat($item.find('.discount').val()) || 0;
            const discountType = $item.find('.discount-type').val();

            const taxOption = document.querySelector('input[name="tax_option"]:checked').value;
            let taxRate = 0;
            if (taxOption === 'tva') taxRate = TVA_RATE;
            else if (taxOption === 'other') taxRate = parseFloat(document.getElementById('other_tax_rate').value) / 100 || 0;

            let priceHT = priceTTC;
            if (taxRate > 0) priceHT = priceTTC / (1 + taxRate);

            let discountAmount = 0;
            if (discountType === 'percent') discountAmount = priceHT * (discount / 100);
            else discountAmount = Math.min(discount, priceHT);

            const unitPriceHTNet = priceHT - discountAmount;
            const totalHTAfterDiscount = unitPriceHTNet * quantity;

            $item.find('.total-price').text(unitPriceHTNet.toFixed(2));
            $item.find('.total-price-after-discount').text(totalHTAfterDiscount.toFixed(2));
            $item.find('.line-total-after-discount').val(totalHTAfterDiscount.toFixed(2));

            return {
                totalTTCBrut: priceTTC * quantity,
                totalHTBrut: priceHT * quantity,
                discountAmount: discountAmount * quantity,
                totalHTAfterDiscount: totalHTAfterDiscount
            };
        }

        // ========== CALCUL GLOBAL ==========
        function calculateTotals() {
            let totalTTCBrut = 0, totalHTBrut = 0, totalDiscount = 0, totalHTAfterDiscount = 0;
            document.querySelectorAll('.repeater-item').forEach(item => {
                const t = calculateItemTotal(item);
                totalTTCBrut += t.totalTTCBrut;
                totalHTBrut += t.totalHTBrut;
                totalDiscount += t.discountAmount;
                totalHTAfterDiscount += t.totalHTAfterDiscount;
            });

            const globalDiscountVal = parseFloat(document.getElementById('global_discount_value').value) || 0;
            const globalDiscountType = document.getElementById('global_discount_type').value;
            let globalDiscount = 0;
            if (globalDiscountVal > 0) {
                if (globalDiscountType === 'percent') globalDiscount = totalHTAfterDiscount * (globalDiscountVal / 100);
                else globalDiscount = Math.min(globalDiscountVal, totalHTAfterDiscount);
            }
            const finalTotalHT = Math.max(totalHTAfterDiscount - globalDiscount, 0);
            const finalTotalDiscount = totalDiscount + globalDiscount;

            const taxOption = document.querySelector('input[name="tax_option"]:checked').value;
            let taxAmount = 0;
            if (taxOption === 'tva') taxAmount = finalTotalHT * TVA_RATE;
            else if (taxOption === 'other') taxAmount = finalTotalHT * (parseFloat(document.getElementById('other_tax_rate').value) / 100 || 0);

            const finalTotalTTC = finalTotalHT + taxAmount;
            currentTotalTTC = Math.round(finalTotalTTC * 100) / 100;

            // Mise à jour affichage
            document.getElementById('total_ttc_brut').innerText = totalTTCBrut.toFixed(2);
            document.getElementById('totalTTCBrut').value = totalTTCBrut.toFixed(2);
            document.getElementById('total_discount').innerText = finalTotalDiscount.toFixed(2);
            document.getElementById('totalDiscount').value = finalTotalDiscount.toFixed(2);
            document.getElementById('total_ht').innerText = finalTotalHT.toFixed(2);
            document.getElementById('totalHT').value = finalTotalHT.toFixed(2);
            document.getElementById('tva_amount').innerText = (taxOption === 'tva' ? taxAmount : 0).toFixed(2);
            document.getElementById('tvaAmount').value = (taxOption === 'tva' ? taxAmount : 0).toFixed(2);
            document.getElementById('other_tax_amount').innerText = (taxOption === 'other' ? taxAmount : 0).toFixed(2);
            document.getElementById('otherTaxAmount').value = (taxOption === 'other' ? taxAmount : 0).toFixed(2);
            document.getElementById('total_ttc_final').innerText = currentTotalTTC.toFixed(2);
            document.getElementById('totalTTC').value = currentTotalTTC.toFixed(2);

            updatePaymentModule();
        }

        // ========== MODULE PAIEMENT ==========
        function updatePaymentModule() {
            let paid = parseFloat(document.getElementById('amount_paid').value) || 0;
            let total = currentTotalTTC;
            let remaining = Math.max(total - paid, 0);
            let change = (paid > total) ? (paid - total) : 0;

            document.getElementById('remaining_amount').value = remaining.toFixed(2) + ' FCFA';
            document.getElementById('change_amount').value = change.toFixed(2) + ' FCFA';

            let status = 'pending';
            if (paid >= total && total > 0) status = 'paid';
            else if (paid > 0) status = 'partial';
            document.getElementById('payment_status').value = status;
        }

        // ========== GESTION DES TYPES DE REMISE PAR ARTICLE ==========
        function setupDiscountTypeButtons() {
            document.querySelectorAll('.discount-type-btn').forEach(btn => {
                btn.removeEventListener('click', discountClickHandler);
                btn.addEventListener('click', discountClickHandler);
            });
        }
        function discountClickHandler() {
            const item = this.closest('.repeater-item');
            const type = this.dataset.type;
            item.querySelector('.discount-type').value = type;
            item.querySelectorAll('.discount-type-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            item.querySelector('.discount-symbol-text').innerText = type === 'percent' ? '%' : 'FCFA';
            calculateTotals();
        }

        // ========== REMISE GLOBALE ==========
        function setupGlobalDiscount() {
            document.querySelectorAll('.global-discount-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const type = this.dataset.type;
                    document.getElementById('global_discount_type').value = type;
                    document.querySelectorAll('.global-discount-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    calculateTotals();
                });
            });
            document.getElementById('global_discount_value').addEventListener('input', calculateTotals);
        }

        // ========== ÉVÉNEMENTS INPUT ==========
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('quantity') || e.target.classList.contains('price') || e.target.classList.contains('discount')) {
                calculateTotals();
            }
        });
        document.getElementById('amount_paid').addEventListener('input', updatePaymentModule);

        // ========== AJOUT D'UN ARTICLE ==========
        document.getElementById('add-item').addEventListener('click', function() {
            const container = document.getElementById('items-container');
            const first = document.querySelector('.repeater-item');
            const clone = first.cloneNode(true);

            // Réinitialiser les champs
            clone.querySelectorAll('input').forEach(inp => {
                if (inp.type !== 'hidden') inp.value = '';
            });
            clone.querySelector('.quantity').value = '1';
            clone.querySelector('.price').value = '0';
            clone.querySelector('.discount').value = '0';
            clone.querySelector('.discount-type').value = 'percent';
            clone.querySelector('.discount-symbol-text').innerText = '%';
            clone.querySelector('.total-price').innerText = '0.00';
            clone.querySelector('.total-price-after-discount').innerText = '0.00';
            clone.querySelector('.line-total-after-discount').value = '0';

            // Réinitialiser les boutons de remise
            clone.querySelectorAll('.discount-type-btn').forEach(btn => btn.classList.remove('active'));
            clone.querySelector('.discount-type-btn[data-type="percent"]').classList.add('active');

            // Supprimer les anciens événements de draggable/droppable
            if ($(clone).data('ui-draggable')) {
                $(clone).draggable('destroy');
                $(clone).droppable('destroy');
            }

            container.appendChild(clone);
            setupDiscountTypeButtons();

            clone.querySelector('.remove-item').addEventListener('click', function() {
                clone.remove();
                updateItemIndices();
                calculateTotals();
            });

            initializeDragAndDrop();
            updateItemIndices();
            calculateTotals();
        });

        // ========== SUPPRESSION D'UN ARTICLE ==========
        $(document).on('click', '.remove-item', function() {
            $(this).closest('.repeater-item').remove();
            updateItemIndices();
            calculateTotals();
        });

        // ========== ÉVÉNEMENTS DE DÉPLACEMENT ==========
        $(document).on('click', '.move-up', function() {
            var $item = $(this).closest('.repeater-item');
            moveItem($item, -1);
        });

        $(document).on('click', '.move-down', function() {
            var $item = $(this).closest('.repeater-item');
            moveItem($item, 1);
        });

        // ========== CHARGEMENT DES ARTICLES PAR CATÉGORIE ==========
        function loadItemsByCategory(categoryName, $itemInput) {
            if (!categoryName) return;
            $itemInput.attr('placeholder', 'Chargement...');
            $.ajax({
                url: base_url + "admin/selling/get_items_by_category_name",
                type: "POST",
                data: { 'category_name': categoryName },
                dataType: "json",
                success: function(data) {
                    let datalistId = 'item-list-' + new Date().getTime();
                    $itemInput.attr('list', datalistId);
                    let $datalist = $('#' + datalistId);
                    if (!$datalist.length) $datalist = $('<datalist>', { id: datalistId }).insertAfter($itemInput);
                    let options = '';
                    data.forEach(item => {
                        options += `<option value="${item.name}" data-id="${item.id}" data-unit="${item.unit || ''}" data-price="${item.selling_price || 0}" data-stock="${item.current_quantity || 0}">`;
                    });
                    $datalist.html(options);
                    $itemInput.attr('placeholder', 'Sélectionnez ou tapez un article');
                }
            });
        }

        function getItemDetails(itemName, categoryName, $row) {
            $.ajax({
                url: base_url + "admin/selling/get_item_details",
                type: "POST",
                data: { 'item_name': itemName, 'category_name': categoryName },
                dataType: "json",
                success: function(res) {
                    if (res.status === 'success') {
                        $row.find('.unit').val(res.unit || '');
                        $row.find('.price').val(res.price || 0);
                        $row.find('.available-qty').text(res.quantity || 0);
                        calculateTotals();
                    }
                }
            });
        }

        $(document).on('change', '.item-category', function() {
            let $row = $(this).closest('.repeater-item');
            let cat = $(this).val();
            let $itemName = $row.find('.item-name');
            if (cat) loadItemsByCategory(cat, $itemName);
            $itemName.val('');
            $row.find('.unit, .price').val('');
            $row.find('.available-qty').text('0');
            calculateTotals();
        });

        $(document).on('change', '.item-name', function() {
            let $row = $(this).closest('.repeater-item');
            let itemName = $(this).val();
            let cat = $row.find('.item-category').val();
            if (itemName && cat) getItemDetails(itemName, cat, $row);
        });

        // ========== GESTION CAISSE/BANQUE ==========
        $('#payment_method_type').change(function() {
            let method = $(this).val();
            if (method === 'cash') {
                $('#caisse_group').show(); $('#banque_group').hide();
                $('#source_selection').show();
                $('#caisse_id').prop('required', true); $('#banque_id').prop('required', false);
            } else if (method === 'bank') {
                $('#caisse_group').hide(); $('#banque_group').show();
                $('#source_selection').show();
                $('#caisse_id').prop('required', false); $('#banque_id').prop('required', true);
            } else {
                $('#source_selection').hide();
                $('#caisse_id, #banque_id').prop('required', false);
            }
            updateSourceInfo();
        });

        function updateSourceInfo() {
            let method = $('#payment_method_type').val();
            let text = '';
            if (method === 'cash' && $('#caisse_id').val()) {
                let opt = $('#caisse_id option:selected');
                text = `Caisse : ${opt.text()}`;
            } else if (method === 'bank' && $('#banque_id').val()) {
                let opt = $('#banque_id option:selected');
                text = `Banque : ${opt.text()}`;
            }
            $('#selected_source_text').text(text);
            $('#source_info').toggle(!!text);
        }
        $('#caisse_id, #banque_id').change(updateSourceInfo);
        $('#payment_method_type').trigger('change');

        // ========== SOUMISSION DU FORMULAIRE ==========
        document.getElementById('<?= $formID; ?>').addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: "Confirmation",
                text: "Voulez-vous vraiment mettre à jour ce devis ?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Oui, mettre à jour",
                cancelButtonText: "Annuler"
            }).then((result) => {
                if (result.isConfirmed) {
                    const btn = document.getElementById('<?= $submitBtn; ?>');
                    const orig = btn.innerHTML;
                    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Mise à jour...';
                    btn.disabled = true;
                    fetch(this.action, { method: "POST", body: new FormData(this) })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire("Succès", data.message, "success").then(() => window.location.href = data.redirect_url || '<?= base_url('admin/selling'); ?>');
                            } else {
                                Swal.fire("Erreur", data.message, "error");
                            }
                        })
                        .catch(() => Swal.fire("Erreur", "Une erreur réseau est survenue", "error"))
                        .finally(() => { btn.innerHTML = orig; btn.disabled = false; });
                }
            });
        });

        // ========== INITIALISATION ==========
        setupDiscountTypeButtons();
        setupGlobalDiscount();
        calculateTotals();
    });
</script>