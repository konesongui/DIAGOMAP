<?php
// Set all the form data
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
        background-color: #fff8e1;
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
    .tax-options {
        margin-bottom: 10px;
    }
    .other-tax-container {
        margin-top: 10px;
        display: none;
    }
    /* Styles pour le module de paiement */
    .payment-module {
        background: #f8f9fa;
        border: 2px solid #28a745;
        border-radius: 8px;
        padding: 20px;
        margin-top: 20px;
        margin-bottom: 20px;
    }
    .payment-title {
        color: #28a745;
        margin-bottom: 15px;
        font-weight: bold;
    }
    .badge-payment {
        font-size: 14px;
        padding: 5px 10px;
    }

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
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Point de vente</h3>
                        <div class="box-tools pull-right">
                            <span class="badge bg-green badge-payment">
                                <i class="fa fa-money"></i> Caisse enregistreuse
                            </span>
                        </div>
                    </div>

                    <form action="<?php echo site_url('admin/selling/add') ?>" id="<?= $formID; ?>" method="post" accept-charset="utf-8">
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
                                    <select class="form-control" name="customer" id="customer_select">

                                        <?php foreach ($clients as $key => $client): ?>
                                            <option value="<?php echo $client['id']; ?>" <?php echo ($client['id'] == 80) ? 'selected' : ''; ?>>
                                                <?php echo $client['item_supplier'].' '.$client['lastname'].' ('.$client['phone'].')'; ?>
                                            </option>
                                        <?php endforeach; ?>
                                        <option value="new">➕ Nouveau client</option>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('customer'); ?></span>
                                </div>

                                <!-- Champs cachés pour le nouveau client -->
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
                                    <label>Date de création<small class="req">*</small></label>
                                    <input id="quote_date" name="quote_date" type="text" class="form-control dateSelect" value="<?= set_value('quote_date', date('d/m/Y')) ?>"/>
                                </div>
                                <!-- <div class="form-group col-md-4">
                                    <label>Date limite</label>
                                    <input id="valid_until" name="valid_until" type="text" class="form-control dateSelect" value="<?= set_value('valid_until', date('d/m/Y')) ?>" readonly />
                                </div>-->
                                <div class="form-group col-md-4" hidden>
                                    <label for="payment_method">Mode de paiement</label>
                                    <select class="form-control select2" id="payment_method" name="payment_method">
                                        <option value="">Sélectionner...</option>
                                        <option value="Espèces">Espèces</option>
                                        <option value="Chèque">Chèque</option>
                                        <option value="Virement">Virement</option>
                                        <option value="Carte bancaire">Carte bancaire</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row" hidden>

                                <div class="form-group col-md-4">
                                    <label>Termes de paiements </label>
                                    <textarea name="payment_terms" class="form-control"><?= set_value('payment_terms') ?></textarea>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Termes de livraison </label>
                                    <textarea name="delivery_terms" class="form-control"><?= set_value('delivery_terms') ?></textarea>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Lieu de livraison </label>
                                    <textarea name="delivery_location" class="form-control"><?= set_value('delivery_location') ?></textarea>
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
                                    <h4>Choisissez les articles <small class="text-muted">(Glissez-déposez pour réorganiser)</small></h4>
                                    <div id="items-container">
                                        <div class="repeater-item" data-index="0">
                                            <span class="item-index">1</span>
                                            <span class="drag-handle"><i class="fa fa-arrows-alt"></i></span>
                                            <div class="row">
                                                <div class="form-group col-md-2">
                                                    <label>Catégorie <small class="req">*</small></label>
                                                    <input type="text" name="item_category[]" class="form-control item-category" list="category-list" placeholder="Sélectionnez ou enregistrer une catégorie" required>
                                                    <datalist id="category-list">
                                                        <?php foreach ($itemcatlist as $category): ?>
                                                        <option value="<?= $category['item_category'] ?>">
                                                            <?php endforeach; ?>
                                                    </datalist>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label>Article <small class="req">*</small></label>
                                                    <input type="text" name="item_name[]" class="form-control item-name" list="item-list" placeholder="Sélectionnez ou enregistrer un article" required>
                                                    <datalist id="item-list">
                                                        <?php foreach ($itemlist as $item): ?>
                                                        <option value="<?= $item['item_name'] ?>" data-id="<?= $item['id'] ?>" data-stock="<?= $item['quantity'] ?>" data-unit="<?= $item['unit'] ?>" data-price="<?= $item['selling_price'] ?>">
                                                            <?php endforeach; ?>
                                                    </datalist>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>Unité</label>
                                                    <input type="text" name="unit[]" class="form-control unit">
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>Quantité <small class="req">*</small></label>
                                                    <input type="number" name="quantity[]" class="form-control quantity" value="0" min="1" required>
                                                    <div class="availability" style="margin-top: 5px; color: #3c8dbc; font-weight: bold;">
                                                        Stock: <span class="available-qty">0</span>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>Prix TTC</label>
                                                    <input type="number" name="price[]" class="form-control price" min="0" step="0.01" value="0">
                                                    <small class="text-muted">Prix toutes taxes comprises</small>
                                                </div>
                                                <!-- Champs de remise améliorés -->
                                                <div class="form-group col-md-2">
                                                    <label>Remise</label>
                                                    <div class="discount-type-group">
                                                        <button type="button" class="discount-type-btn active" data-type="percent">%</button>
                                                        <button type="button" class="discount-type-btn" data-type="amount">FCFA</button>
                                                        <input type="hidden" name="discount_type[]" class="discount-type" value="percent">
                                                    </div>
                                                    <div class="discount-input-group">
                                                        <span class="discount-symbol">
                                                            <span class="discount-symbol-text">%</span>
                                                        </span>
                                                        <input type="number" name="discount[]" class="form-control discount discount-field" min="0" step="0.01" value="0" placeholder="0.00" style="border-radius: 0 4px 4px 0;">
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>P.U HT</label>
                                                    <div class="total-price">0.00</div>
                                                    <small class="text-muted">Hors taxes</small>
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
                                                    <button type="button" class="btn btn-danger btn-sm remove-item" style="margin-top: 5px;">
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


                        <!-- MODULE DE PAIEMENT EXISTANT -->
                        <div class="payment-module">
                            <h4 class="payment-title"><i class="fa fa-money"></i> Module de paiement</h4>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>💰 Montant payé</label>
                                        <input type="number" id="amount_paid" name="amount_paid" class="form-control" step="0.01" placeholder="0.00" value="0">
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
                                            <option value="pending">⏳ En attente de paiement</option>
                                            <option value="partial">💸 Paiement partiel</option>
                                            <option value="paid">✅ Payé totalement</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>🏦 Mode de paiement</label>
                                        <select class="form-control" id="payment_method_type" name="payment_method_type">
                                            <option value="">Sélectionner...</option>
                                            <option value="cash">Espèces (Caisse)</option>
                                            <option value="bank">Paiement bancaire</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- NOUVEAU : Sélection de la caisse ou banque -->
                            <div class="row" id="source_selection" style="display: none;">
                                <div class="col-md-12">
                                    <div class="form-group" id="caisse_group">
                                        <label for="caisse_id">🏦 Sélectionner la caisse <span class="text-danger">*</span></label>
                                        <select class="form-control" id="caisse_id" name="caisse_id">
                                            <option value="">Sélectionner une caisse...</option>
                                            <?php
                                            // Récupérer les caisses actives
                                            $caisses = $this->db->where('est_actif', 1)->where('is_deleted', 'no')->get('income')->result();
                                            foreach ($caisses as $caisse): ?>
                                                <option value="<?= $caisse->id ?>" data-balance="<?= $caisse->amount_re ?>">
                                                    <?= htmlspecialchars($caisse->name) ?> (Solde: <?= number_format($caisse->amount_re, 0, ',', ' ') ?> FCFA)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group" id="banque_group" style="display: none;">
                                        <label for="banque_id">🏦 Sélectionner la banque <span class="text-danger">*</span></label>
                                        <select class="form-control" id="banque_id" name="banque_id">
                                            <option value="">Sélectionner une banque...</option>
                                            <?php
                                            // Récupérer les banques actives
                                            $banques = $this->db->where('status', 1)->get('banks')->result();
                                            foreach ($banques as $banque): ?>
                                                <option value="<?= $banque->id ?>" data-balance="<?= $banque->balance ?>">
                                                    <?= htmlspecialchars($banque->name) ?> (Solde: <?= number_format($banque->balance, 0, ',', ' ') ?> FCFA)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Informations de la source sélectionnée -->
                            <div class="row" id="source_info" style="display: none;">
                                <div class="col-md-12">
                                    <div class="alert alert-info" style="margin-top: 10px;">
                                        <i class="fa fa-info-circle"></i>
                                        <span id="selected_source_text"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group" hidden>
                                        <label>Remise globale</label>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="discount-type-group">
                                                    <button type="button" class="discount-type-btn active" data-type="percent" id="global_discount_btn_percent">%</button>
                                                    <button type="button" class="discount-type-btn" data-type="amount" id="global_discount_btn_amount">FCFA</button>
                                                    <input type="hidden" name="global_discount_type" id="global_discount_type" value="percent">
                                                </div>
                                                <div class="discount-input-group">
                                                    <span class="discount-symbol">
                                                        <span class="discount-symbol-text" id="global_discount_symbol">%</span>
                                                    </span>
                                                    <input type="number" id="global_discount_amount" name="global_discount_amount" class="form-control" min="0" step="0.01" placeholder="0.00" value="0" style="border-radius: 0 4px 4px 0;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- === Options de taxes améliorées === -->
                                    <div class="form-group tax-options" hidden>
                                        <label>Options de taxe</label>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="tax_option" value="none">
                                                Aucune taxe
                                            </label>
                                        </div>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="tax_option" value="tva" checked>
                                                Appliquer la TVA (18%)
                                            </label>
                                        </div>
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="tax_option" value="other">
                                                Autre taxe
                                            </label>
                                        </div>

                                        <!-- Conteneur pour la taxe personnalisée -->
                                        <div class="other-tax-container" id="other_tax_container">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Nom de la taxe</label>
                                                    <input type="text" name="other_tax_name" id="other_tax_name" class="form-control" placeholder="Ex: Taxe spéciale, Droit de douane...">
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Taux (%)</label>
                                                    <div class="input-group">
                                                        <input type="number" name="other_tax_rate" id="other_tax_rate" class="form-control" min="0" max="100" step="0.01" placeholder="0.00">
                                                        <span class="input-group-addon">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- === Tableau des totaux mis à jour (CORRIGÉ) === -->
                                <div class="col-md-4">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td><strong>Total TTC (Toutes taxes comprises):</strong></td>
                                            <td class="text-right"><strong><span id="total_ttc_brut">0.00</span></strong></td>
                                            <input type="hidden" name="total_ttc_brut" id="totalTTCBrut" value="0">
                                        </tr>
                                        <tr class="discount-row">
                                            <td>Total Remise:</th>
                                            <td class="text-right"><span id="total_discount">0.00</span></th>
                                            <input type="hidden" name="total_discount" id="totalDiscount" value="0">
                                        </tr>
                                        <tr class="ht-row">
                                            <td><strong>Total HT (après remises):</strong></th>
                                            <td class="text-right"><strong><span id="total_ht">0.00</span></strong></th>
                                            <input type="hidden" name="total_ht" id="totalHT" value="0">
                                        </tr>
                                        <!-- Ligne TVA (18% du HT après remises) -->
                                        <tr class="tva-row">
                                            <td>TVA (18%):</th>
                                            <td class="text-right"><span id="tva_amount">0.00</span></th>
                                            <input type="hidden" name="tva_amount" id="tvaAmount" value="0">
                                            <input type="hidden" name="tva_rate" value="18">
                                        </tr>
                                        <!-- Ligne Autre taxe -->
                                        <tr class="other-tax-row" style="display:none;">
                                            <td id="other_tax_label">Autre taxe:</th>
                                            <td class="text-right"><span id="other_tax_amount">0.00</span></th>
                                            <input type="hidden" name="other_tax_amount" id="otherTaxAmount" value="0">
                                            <input type="hidden" name="other_tax_rate" id="otherTaxRate" value="0">
                                        </tr>
                                        <tr class="ttc-final-row">
                                            <td><strong>Total TTC final:</strong></th>
                                            <td class="text-right"><strong><span id="total_ttc_final">0.00</span></strong></th>
                                            <input type="hidden" name="total_ttc" id="totalTTC" value="0">
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <a href="<?php echo base_url('admin/selling'); ?>" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Retour à la liste
                            </a>
                            <button type="submit" id="<?= $submitBtn; ?>" class="btn btn-primary">
                                <i class="fa fa-save"></i> Valider la vente
                            </button>
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
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/fr.js"></script>
<script type="text/javascript">
    // Configuration de moment.js en français
    moment.locale('fr');

    // Configuration de base pour jQuery
    var base_url = '<?php echo base_url(); ?>';

    // Variable pour stocker le total TTC final
    var currentTotalTTC = 0;

    // Taux de TVA par défaut
    var TVA_RATE = 0.18;

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

        // ========== GESTION TAXES ==========
        document.querySelectorAll('input[name="tax_option"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const selectedOption = this.value;
                const otherTaxContainer = document.getElementById('other_tax_container');

                if (selectedOption === 'other') {
                    otherTaxContainer.style.display = 'block';
                } else {
                    otherTaxContainer.style.display = 'none';
                }

                document.querySelector('.tva-row').style.display = (selectedOption === 'tva') ? '' : 'none';
                document.querySelector('.other-tax-row').style.display = (selectedOption === 'other') ? '' : 'none';

                calculateTotals();
            });
        });

        // ========== CALCUL DES TOTAUX ==========
        function calculateItemTotal(itemElement) {
            const $item = $(itemElement);
            const quantity = parseFloat($item.find('.quantity').val()) || 0;
            const priceTTC = parseFloat($item.find('.price').val()) || 0;
            const discount = parseFloat($item.find('.discount').val()) || 0;
            const discountType = $item.find('.discount-type').val();

            const taxOption = document.querySelector('input[name="tax_option"]:checked').value;
            let taxRate = 0;

            if (taxOption === 'tva') {
                taxRate = TVA_RATE;
            } else if (taxOption === 'other') {
                taxRate = parseFloat(document.getElementById('other_tax_rate').value) / 100 || 0;
            }

            let priceHT = priceTTC;
            if (taxRate > 0) {
                priceHT = priceTTC / (1 + taxRate);
            }

            let discountAmount = 0;
            if (discountType === 'percent') {
                discountAmount = priceHT * (discount / 100);
            } else {
                discountAmount = Math.min(discount, priceHT);
            }

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

        function calculateTotals() {
            let totalTTCBrut = 0;
            let totalHTBrut = 0;
            let totalDiscount = 0;
            let totalHTAfterDiscount = 0;

            document.querySelectorAll('.repeater-item').forEach(item => {
                const itemTotals = calculateItemTotal(item);
                totalTTCBrut += itemTotals.totalTTCBrut;
                totalHTBrut += itemTotals.totalHTBrut;
                totalDiscount += itemTotals.discountAmount;
                totalHTAfterDiscount += itemTotals.totalHTAfterDiscount;
            });

            const globalDiscountAmount = parseFloat(document.getElementById('global_discount_amount').value) || 0;
            const globalDiscountType = document.getElementById('global_discount_type').value;
            let globalDiscount = 0;

            if (globalDiscountAmount > 0) {
                if (globalDiscountType === 'percent') {
                    globalDiscount = totalHTAfterDiscount * (globalDiscountAmount / 100);
                } else {
                    globalDiscount = Math.min(globalDiscountAmount, totalHTAfterDiscount);
                }
            }

            const finalTotalDiscount = totalDiscount + globalDiscount;
            const finalTotalHT = Math.max(totalHTAfterDiscount - globalDiscount, 0);

            const taxOption = document.querySelector('input[name="tax_option"]:checked').value;
            let taxAmount = 0;
            let taxName = '';
            let taxRate = 0;

            if (taxOption === 'tva') {
                taxRate = 0.18;
                taxAmount = finalTotalHT * taxRate;
                taxName = 'TVA (18%)';
                document.getElementById('other_tax_label').textContent = taxName;
                document.getElementById('otherTaxRate').value = 18;
            } else if (taxOption === 'other') {
                taxRate = parseFloat(document.getElementById('other_tax_rate').value) / 100 || 0;
                const taxNameInput = document.getElementById('other_tax_name').value || 'Autre taxe';
                taxAmount = finalTotalHT * taxRate;
                taxName = taxNameInput;
                document.getElementById('other_tax_label').textContent = taxName + ' (' + (taxRate * 100).toFixed(2) + '%)';
                document.getElementById('otherTaxRate').value = taxRate * 100;
            }

            const totalTTCFinal = finalTotalHT + taxAmount;
            currentTotalTTC = Math.round(totalTTCFinal * 100) / 100;

            document.getElementById('total_ttc_brut').textContent = totalTTCBrut.toFixed(2);
            document.getElementById('totalTTCBrut').value = totalTTCBrut.toFixed(2);
            document.getElementById('total_discount').textContent = finalTotalDiscount.toFixed(2);
            document.getElementById('totalDiscount').value = finalTotalDiscount.toFixed(2);
            document.getElementById('total_ht').textContent = finalTotalHT.toFixed(2);
            document.getElementById('totalHT').value = finalTotalHT.toFixed(2);

            if (taxOption === 'tva') {
                document.getElementById('tva_amount').textContent = taxAmount.toFixed(2);
                document.getElementById('tvaAmount').value = taxAmount.toFixed(2);
                document.getElementById('other_tax_amount').textContent = '0.00';
                document.getElementById('otherTaxAmount').value = '0';
            } else if (taxOption === 'other') {
                document.getElementById('tva_amount').textContent = '0.00';
                document.getElementById('tvaAmount').value = '0';
                document.getElementById('other_tax_amount').textContent = taxAmount.toFixed(2);
                document.getElementById('otherTaxAmount').value = taxAmount.toFixed(2);
            } else {
                document.getElementById('tva_amount').textContent = '0.00';
                document.getElementById('tvaAmount').value = '0';
                document.getElementById('other_tax_amount').textContent = '0.00';
                document.getElementById('otherTaxAmount').value = '0';
            }

            document.getElementById('total_ttc_final').textContent = currentTotalTTC.toFixed(2);
            document.getElementById('totalTTC').value = currentTotalTTC.toFixed(2);

            updatePaymentModule();
        }

        function updatePaymentModule() {
            let paid = parseFloat(document.getElementById('amount_paid').value) || 0;
            let totalTTC = currentTotalTTC;

            paid = Math.round(paid * 100) / 100;
            totalTTC = Math.round(totalTTC * 100) / 100;

            let remaining = Math.round((totalTTC - paid) * 100) / 100;

            if (remaining <= 0) {
                let change = Math.abs(remaining);
                document.getElementById('change_amount').value = change.toFixed(2) + ' FCFA';
                document.getElementById('remaining_amount').value = '0.00 FCFA';
                document.getElementById('payment_status').value = 'paid';
                if (paid !== totalTTC) {
                    document.getElementById('amount_paid').value = totalTTC.toFixed(2);
                }
            } else if (remaining === 0) {
                document.getElementById('change_amount').value = '0.00 FCFA';
                document.getElementById('remaining_amount').value = '0.00 FCFA';
                document.getElementById('payment_status').value = 'paid';
            } else {
                document.getElementById('change_amount').value = '0.00 FCFA';
                document.getElementById('remaining_amount').value = remaining.toFixed(2) + ' FCFA';
                if (paid > 0) {
                    document.getElementById('payment_status').value = 'partial';
                } else {
                    document.getElementById('payment_status').value = 'pending';
                }
            }

            // Vérification de la source de paiement
            if (paid > 0) {
                var paymentMethod = $('#payment_method_type').val();
                if (paymentMethod === 'cash' && !$('#caisse_id').val()) {
                    $('#payment_status').val('pending');
                    $('#remaining_amount').val(currentTotalTTC.toFixed(2) + ' FCFA');
                    $('#change_amount').val('0.00 FCFA');
                    toastr.warning('Veuillez sélectionner une caisse');
                } else if (paymentMethod === 'bank' && !$('#banque_id').val()) {
                    $('#payment_status').val('pending');
                    $('#remaining_amount').val(currentTotalTTC.toFixed(2) + ' FCFA');
                    $('#change_amount').val('0.00 FCFA');
                    toastr.warning('Veuillez sélectionner une banque');
                }
            }
        }

        // ========== GESTION DES TYPES DE REMISE ==========
        function setupDiscountTypeButtons() {
            document.querySelectorAll('.discount-type-btn').forEach(button => {
                if (!button.id.includes('global')) {
                    button.addEventListener('click', function() {
                        const item = this.closest('.repeater-item');
                        const type = this.dataset.type;
                        const discountTypeInput = item.querySelector('.discount-type');
                        const symbolText = item.querySelector('.discount-symbol-text');

                        item.querySelectorAll('.discount-type-btn').forEach(btn => btn.classList.remove('active'));
                        this.classList.add('active');
                        discountTypeInput.value = type;
                        symbolText.textContent = (type === 'percent') ? '%' : 'FCFA';
                        calculateTotals();
                    });
                }
            });

            document.getElementById('global_discount_btn_percent').addEventListener('click', function() {
                this.classList.add('active');
                document.getElementById('global_discount_btn_amount').classList.remove('active');
                document.getElementById('global_discount_type').value = 'percent';
                document.getElementById('global_discount_symbol').textContent = '%';
                calculateTotals();
            });

            document.getElementById('global_discount_btn_amount').addEventListener('click', function() {
                this.classList.add('active');
                document.getElementById('global_discount_btn_percent').classList.remove('active');
                document.getElementById('global_discount_type').value = 'amount';
                document.getElementById('global_discount_symbol').textContent = 'FCFA';
                calculateTotals();
            });
        }

        // ========== ÉVÉNEMENTS DE RECALCUL ==========
        document.addEventListener('input', function(e) {
            if (['quantity', 'price', 'discount'].some(cls => e.target.classList.contains(cls)) ||
                e.target.id === 'global_discount_amount' ||
                e.target.id === 'other_tax_rate' ||
                e.target.id === 'other_tax_name') {
                calculateTotals();
            }
        });

        document.getElementById('amount_paid').addEventListener('input', function() {
            updatePaymentModule();
        });

        document.querySelectorAll('input[name="tax_option"]').forEach(radio => {
            radio.addEventListener('change', calculateTotals);
        });

        // ========== AJOUT D'UN ARTICLE ==========
        document.getElementById('add-item').addEventListener('click', function() {
            const container = document.getElementById('items-container');
            const firstItem = document.querySelector('.repeater-item');
            const newItem = firstItem.cloneNode(true);

            newItem.querySelectorAll('input').forEach(input => {
                if (!input.classList.contains('remove-item')) {
                    input.value = '';
                }
            });
            newItem.querySelector('.quantity').value = '1';
            newItem.querySelector('.price').value = '0';
            newItem.querySelector('.discount').value = '0';
            newItem.querySelector('.discount-type').value = 'percent';
            newItem.querySelector('.discount-symbol-text').textContent = '%';
            newItem.querySelector('.total-price').textContent = '0.00';
            newItem.querySelector('.total-price-after-discount').textContent = '0.00';
            newItem.querySelector('.line-total-after-discount').value = '0';

            newItem.querySelectorAll('.discount-type-btn').forEach(btn => {
                btn.classList.remove('active');
                if (btn.dataset.type === 'percent') btn.classList.add('active');
            });

            container.appendChild(newItem);
            setupDiscountTypeButtons();

            // Supprimer les anciens événements de draggable/droppable
            if ($(newItem).data('ui-draggable')) {
                $(newItem).draggable('destroy');
                $(newItem).droppable('destroy');
            }

            newItem.querySelector('.remove-item').addEventListener('click', function() {
                newItem.remove();
                updateItemIndices();
                calculateTotals();
            });

            initializeDragAndDrop();
            updateItemIndices();
            calculateTotals();
        });

        // ========== SUPPRESSION D'UN ARTICLE ==========
        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.repeater-item').remove();
                updateItemIndices();
                calculateTotals();
            });
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

        // ========== GESTION SÉLECTION D'ARTICLE ==========
        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('item-name')) {
                const input = e.target;
                const datalist = document.getElementById('item-list');
                const options = datalist.options;

                for (let option of options) {
                    if (option.value === input.value) {
                        const itemRow = input.closest('.repeater-item');
                        itemRow.querySelector('.unit').value = option.dataset.unit || '';
                        itemRow.querySelector('.price').value = option.dataset.price || '0';
                        itemRow.querySelector('.available-qty').textContent = option.dataset.stock || '0';
                        calculateTotals();
                        break;
                    }
                }
            }
        });

        // ========== GESTION DE LA SOURCE DE PAIEMENT ==========
        $('#payment_method_type').change(function() {
            var method = $(this).val();

            if (method === 'cash') {
                $('#caisse_group').show();
                $('#banque_group').hide();
                $('#source_selection').show();
                $('#caisse_id').prop('required', true);
                $('#banque_id').prop('required', false);
                updateSourceInfo();
            } else if (method === 'bank') {
                $('#caisse_group').hide();
                $('#banque_group').show();
                $('#source_selection').show();
                $('#caisse_id').prop('required', false);
                $('#banque_id').prop('required', true);
                updateSourceInfo();
            } else {
                $('#source_selection').hide();
                $('#caisse_id').prop('required', false);
                $('#banque_id').prop('required', false);
            }
        });

        function updateSourceInfo() {
            var method = $('#payment_method_type').val();
            var sourceText = '';

            if (method === 'cash') {
                var selectedOption = $('#caisse_id option:selected');
                if ($('#caisse_id').val()) {
                    var caisseName = selectedOption.text();
                    var balance = selectedOption.data('balance');
                    sourceText = 'Caisse sélectionnée : ' + caisseName + ' (Solde actuel: ' + formatNumber(balance) + ' FCFA)';
                    $('#source_info').fadeIn();
                } else {
                    $('#source_info').fadeOut();
                }
            } else if (method === 'bank') {
                var selectedOption = $('#banque_id option:selected');
                if ($('#banque_id').val()) {
                    var banqueName = selectedOption.text();
                    var balance = selectedOption.data('balance');
                    sourceText = 'Banque sélectionnée : ' + banqueName + ' (Solde actuel: ' + formatNumber(balance) + ' FCFA)';
                    $('#source_info').fadeIn();
                } else {
                    $('#source_info').fadeOut();
                }
            } else {
                $('#source_info').fadeOut();
            }

            $('#selected_source_text').text(sourceText);
        }

        $('#caisse_id, #banque_id').change(updateSourceInfo);

        function formatNumber(num) {
            return new Intl.NumberFormat('fr-FR').format(num);
        }

        // ========== SOUMISSION DU FORMULAIRE ==========
        document.getElementById('<?= $formID; ?>').addEventListener('submit', function(e) {
            e.preventDefault();

            const taxOption = document.querySelector('input[name="tax_option"]:checked').value;
            if (taxOption === 'other') {
                const taxName = document.getElementById('other_tax_name').value.trim();
                const taxRate = parseFloat(document.getElementById('other_tax_rate').value);

                if (!taxName) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Veuillez saisir un nom pour la taxe personnalisée',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d33'
                    });
                    return;
                }

                if (isNaN(taxRate) || taxRate <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Veuillez saisir un taux de taxe valide (supérieur à 0)',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d33'
                    });
                    return;
                }
            }

            Swal.fire({
                title: "Confirmation",
                text: "Voulez-vous vraiment créer cette vente ?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Oui, créer la vente",
                cancelButtonText: "Annuler",
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    const submitBtn = document.getElementById('<?= $submitBtn; ?>');
                    const originalText = submitBtn.innerHTML;

                    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Création...';
                    submitBtn.disabled = true;

                    fetch(this.action, {
                        method: "POST",
                        body: new FormData(this)
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire({
                                    title: "Succès",
                                    text: data.message,
                                    icon: "success",
                                    confirmButtonText: "OK",
                                    confirmButtonColor: '#3085d6'
                                }).then(() => {
                                    window.location.href = data.redirect_url || '<?= base_url('admin/selling'); ?>';
                                });
                            } else {
                                let errorMessage = data.message || 'Une erreur est survenue';
                                if (data.error && Object.keys(data.error).length > 0) {
                                    errorMessage = Object.values(data.error).join('\n');
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Erreur',
                                    html: errorMessage.replace(/\n/g, '<br>'),
                                    confirmButtonText: 'OK',
                                    confirmButtonColor: '#d33'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: 'Une erreur réseau est survenue lors de la création de la vente',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#d33'
                            });
                        })
                        .finally(() => {
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        });
                }
            });
        });

        // ========== INITIALISATION ==========
        setupDiscountTypeButtons();
        calculateTotals();
    });

    // ========== FONCTIONS EXTERNES (pour compatibilité) ==========
    // Configuration de moment.js en français
    moment.locale('fr');

    var base_url = '<?php echo base_url(); ?>';

    // Fonction pour charger les articles par catégorie
    function loadItemsByCategory(categoryName, $itemInput, $row) {
        if (categoryName) {
            $itemInput.attr('placeholder', 'Chargement...');

            $.ajax({
                type: "POST",
                url: base_url + "admin/quoteitem/get_items_by_category_name",
                data: {'category_name': categoryName},
                dataType: "json",
                success: function (data) {
                    var datalistId = $itemInput.attr('list');
                    var $datalist = $('#' + datalistId);

                    if (!$datalist.length) {
                        var newId = 'item-list-' + new Date().getTime();
                        $itemInput.attr('list', newId);
                        $datalist = $('<datalist>', { id: newId });
                        $itemInput.after($datalist);
                    }

                    var options = '';
                    if (data.length > 0) {
                        $.each(data, function (i, obj) {
                            options += '<option value="' + obj.name + '" data-id="' + obj.id + '" data-unit="' + (obj.unit || '') + '" data-price="' + (obj.unit_price || 0) + '" data-stock="' + (obj.current_quantity || 0) + '">';
                        });
                        $itemInput.attr('placeholder', 'Sélectionnez ou tapez un article');
                    } else {
                        options = '<option value="">Aucun article trouvé. Vous pouvez en créer un nouveau en tapant directement.</option>';
                        $itemInput.attr('placeholder', 'Aucun article - tapez pour créer');
                    }
                    $datalist.html(options);
                },
                error: function(xhr, status, error) {
                    console.error('Erreur lors du chargement des articles:', error);
                    $itemInput.attr('placeholder', 'Erreur de chargement');
                }
            });
        }
    }

    // Fonction pour récupérer les détails d'un article
    function getItemDetails(itemName, categoryName, $row) {
        if (!itemName || !categoryName) return;

        $.ajax({
            type: "POST",
            url: base_url + "admin/quoteitem/get_item_details",
            data: {
                'item_name': itemName,
                'category_name': categoryName
            },
            dataType: "json",
            success: function (response) {
                if (response.status === 'success') {
                    $row.find('.unit').val(response.unit || '');
                    $row.find('.price').val(response.price || 0);
                    $row.find('.available-qty').text(response.quantity || 0);
                    $row.find('.item-name').data('item-id', response.item_id);

                    if (typeof calculateItemTotal === 'function') {
                        calculateItemTotal($row[0]);
                    }
                    $row.find('.price').trigger('input');

                } else {
                    $row.find('.unit').val('');
                    $row.find('.price').val('');
                    $row.find('.available-qty').text('0');
                    $row.find('.item-name').data('item-id', '');

                    if (typeof calculateItemTotal === 'function') {
                        calculateItemTotal($row[0]);
                    }
                }
            },
            error: function() {
                console.error('Erreur lors de la récupération des détails de l\'article');
            }
        });
    }

    // Attacher l'événement de changement de catégorie
    $(document).on('change', '.item-category', function() {
        var $row = $(this).closest('.repeater-item');
        var categoryName = $(this).val();
        var $itemName = $row.find('.item-name');

        if (categoryName) {
            var uniqueId = 'item-list-' + $row.index();
            $itemName.attr('list', uniqueId);

            if (!$('#' + uniqueId).length) {
                $('<datalist>', { id: uniqueId }).insertAfter($itemName);
            }

            loadItemsByCategory(categoryName, $itemName, $row);
            $itemName.val('');
            $row.find('.unit').val('');
            $row.find('.price').val('');
            $row.find('.available-qty').text('0');

        } else {
            $itemName.attr('list', '');
            $itemName.attr('placeholder', 'Sélectionnez d\'abord une catégorie');
        }

        if (typeof calculateItemTotal === 'function') {
            calculateItemTotal($row[0]);
        }
    });

    // Attacher l'événement de sélection d'article
    $(document).on('change', '.item-name', function() {
        var $row = $(this).closest('.repeater-item');
        var itemName = $(this).val();
        var categoryName = $row.find('.item-category').val();

        if (itemName && categoryName) {
            getItemDetails(itemName, categoryName, $row);
        }
    });

    // Attacher l'événement pour les nouveaux articles ajoutés dynamiquement
    $(document).on('click', '#add-item', function() {
        setTimeout(function() {
            $('.repeater-item:last .item-category').trigger('change');
        }, 100);
    });

    // Après avoir rempli le prix, déclencher le calcul
    $(document).on('change', '.item-name', function() {
        var $row = $(this).closest('.repeater-item');
        var itemName = $(this).val();
        var categoryName = $row.find('.item-category').val();

        if (itemName && categoryName) {
            getItemDetails(itemName, categoryName, $row);
        }
    });

    // S'assurer que le total se met à jour quand le prix change
    $(document).on('input', '.price', function() {
        var $row = $(this).closest('.repeater-item');
        if (typeof calculateItemTotal === 'function') {
            calculateItemTotal($row[0]);
        }
        if (typeof calculateTotals === 'function') {
            calculateTotals();
        }
    });
</script>