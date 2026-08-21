<?php
// Set all the form data
$formID     = 'quoteForm';
$submitBtn  = 'submitBtn';
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

<style type="text/css">
    :root{
        --pf-ink:#273772;
        --pf-ink-soft:#334391;
        --pf-paper:#F6F7FA;
        --pf-card:#FFFFFF;
        --pf-line:#E3E6ED;
        --pf-green:#1F7A54;
        --pf-green-soft:#E1F3EA;
        --pf-red:#B23A32;
        --pf-red-soft:#FBE7E5;
        --pf-amber:#B8892B;
        --pf-amber-soft:#FBF3E1;
        --pf-muted:#6B7793;
        --font-display:'Sora', sans-serif;
        --font-body:'Inter', sans-serif;
        --font-mono:'JetBrains Mono', monospace;
    }

    .content-wrapper{ background:var(--pf-paper); font-family:var(--font-body); }
    .content-wrapper i.fa,
    .content-wrapper i[class*="fa-"]{
        font-family:"FontAwesome" !important;
        font-style:normal;
        font-weight:normal;
    }

    /* ---- Header banner ---- */
    .content-header{
        position:relative;
        overflow:hidden;
        background:linear-gradient(135deg, var(--pf-ink) 0%, var(--pf-ink-soft) 100%);
        border-radius:14px;
        padding:26px 30px !important;
        margin:18px 18px 16px 18px;
        color:#fff;
    }
    .content-header::after{
        content:"";
        position:absolute;
        right:-40px; top:-40px;
        width:200px; height:200px;
        border:1.5px dashed rgba(255,255,255,0.14);
        border-radius:50%;
    }
    .content-header h1{
        font-family:var(--font-display);
        font-weight:700;
        font-size:24px;
        margin:0;
        letter-spacing:-0.01em;
        display:flex;
        align-items:center;
        gap:12px;
    }
    .content-header h1 i{
        width:38px; height:38px;
        border-radius:10px;
        background:rgba(255,255,255,0.10);
        border:1px solid rgba(255,255,255,0.16);
        display:inline-flex;
        align-items:center;
        justify-content:center;
        font-size:16px;
        color:#EAD9A8;
    }
    .content-header small{
        display:block;
        margin:6px 0 0 50px;
        font-size:13px;
        font-weight:400;
        color:rgba(255,255,255,0.65);
        font-family:var(--font-body);
    }

    section.content{ padding:0 18px 18px 18px; }

    /* ---- Card ---- */
    .box.box-primary{
        border-top:none;
        border:1px solid var(--pf-line);
        border-radius:14px;
        box-shadow:0 1px 2px rgba(22,35,63,0.04);
        overflow:hidden;
        width:100%;
    }
    .box.box-primary > .box-header.with-border{
        background:var(--pf-card);
        border-bottom:1px solid var(--pf-line);
        padding:18px 22px;
    }
    .box.box-primary > .box-header .box-title{
        font-family:var(--font-display);
        font-weight:600;
        font-size:17px;
        color:var(--pf-ink);
    }
    .box.box-primary > .box-body{ padding:22px; background:var(--pf-card); }
    .box.box-primary > .box-footer{
        background:var(--pf-paper);
        border-top:1px solid var(--pf-line);
        padding:20px 22px;
    }

    .alert-info{
        border-radius:9px;
        border:1px solid #C7D6EF;
        background:var(--pf-gold-soft, #EEF0FA);
        color:var(--pf-ink);
    }
    .alert-danger{
        border-radius:9px;
        border-color:#EAC3BF;
        background:var(--pf-red-soft);
        color:#8E2C25;
    }

    /* ---- Forms ---- */
    .form-group label{
        font-size:11.5px;
        font-weight:600;
        text-transform:uppercase;
        letter-spacing:.05em;
        color:var(--pf-muted);
        margin-bottom:6px;
    }
    .form-group label .req{ color:var(--pf-red); font-weight:700; }
    .form-control{
        border:1px solid var(--pf-line);
        border-radius:8px;
        box-shadow:none;
        font-size:13.5px;
        color:var(--pf-ink);
        min-height:38px;
    }
    textarea.form-control{ min-height:70px; }
    .form-control:focus{
        border-color:var(--pf-ink);
        box-shadow:0 0 0 3px var(--pf-gold-soft, #EEF0FA);
    }

    hr{ border-top:1px solid var(--pf-line); margin:22px 0; }

    h4{
        font-family:var(--font-display);
        font-weight:600;
        font-size:15px;
        color:var(--pf-ink);
        display:flex;
        align-items:center;
        gap:8px;
        margin-bottom:16px;
    }
    h4::before{
        content:"";
        width:4px; height:16px;
        border-radius:2px;
        background:var(--pf-ink);
        display:inline-block;
    }
    h4 small{ font-weight:400; font-size:12px; color:var(--pf-muted); }

    /* ---- Repeater items ---- */
    .repeater-item{
        border:1px solid var(--pf-line);
        padding:20px 18px 16px 18px;
        margin-bottom:14px;
        border-radius:12px;
        background:var(--pf-card);
        position:relative;
        transition:box-shadow .2s ease, border-color .2s ease, transform .2s ease;
        cursor:grab;
        box-shadow:0 1px 2px rgba(22,35,63,0.03);
    }
    .repeater-item:hover{
        border-color:#C9D0E3;
        box-shadow:0 4px 14px rgba(22,35,63,0.06);
    }
    .repeater-item.dragging{
        opacity:.5;
        transform:scale(0.98);
        cursor:grabbing;
    }
    .repeater-item.drag-over{
        border:1.5px dashed var(--pf-ink);
        background:var(--pf-gold-soft, #EEF0FA);
    }

    .item-index{
        position:absolute;
        top:-10px; left:16px;
        background:var(--pf-ink);
        color:#fff;
        border-radius:999px;
        min-width:24px; height:24px;
        padding:0 7px;
        text-align:center;
        line-height:24px;
        font-size:11.5px;
        font-weight:700;
        font-family:var(--font-mono);
        box-shadow:0 2px 6px rgba(39,55,114,0.35);
    }
    .drag-handle{
        cursor:grab;
        color:#B7BECF;
        font-size:16px;
        padding:4px 6px;
        user-select:none;
        position:absolute;
        top:8px; right:10px;
        border-radius:6px;
        transition:color .15s ease, background .15s ease;
    }
    .drag-handle:hover{ color:var(--pf-ink); background:var(--pf-paper); }
    .drag-handle:active{ cursor:grabbing; }

    .availability{ margin-top:6px; font-size:11.5px; color:var(--pf-muted); }
    .availability .available-qty{ font-weight:600; color:var(--pf-ink); }

    .total-price,
    .total-price-after-discount{
        border:1px solid var(--pf-line);
        border-radius:8px;
        background:var(--pf-paper);
        padding:8px 10px;
        font-family:var(--font-mono);
        font-size:13px;
        min-height:38px;
        display:flex;
        align-items:center;
    }
    .total-price{ font-weight:600; color:var(--pf-ink); }
    .total-price-after-discount{
        font-weight:700;
        color:var(--pf-amber);
        background:var(--pf-amber-soft);
        border-color:#EEDBAA;
    }

    /* ---- Discount controls ---- */
    .discount-field{ background-color:var(--pf-amber-soft) !important; }
    .discount-type-group{ display:flex; align-items:center; gap:6px; margin-bottom:6px; }
    .discount-type-btn{
        padding:5px 12px;
        font-size:11.5px;
        font-weight:600;
        border:1px solid var(--pf-line);
        background:#fff;
        color:var(--pf-muted);
        cursor:pointer;
        border-radius:999px;
        transition:all .15s ease;
    }
    .discount-type-btn:hover{ border-color:var(--pf-ink); color:var(--pf-ink); }
    .discount-type-btn.active{
        background:var(--pf-ink);
        color:#fff;
        border-color:var(--pf-ink);
    }
    .discount-input-group{ display:flex; align-items:center; }
    .discount-symbol{
        padding:0 10px;
        background:var(--pf-paper);
        border:1px solid var(--pf-line);
        border-right:none;
        height:38px;
        display:flex;
        align-items:center;
        border-radius:8px 0 0 8px;
        font-size:12.5px;
        font-weight:600;
        color:var(--pf-muted);
    }
    .discount-input-group .form-control{ border-radius:0 8px 8px 0; }

    .global-discount-row{
        background-color:var(--pf-amber-soft);
        border-left:4px solid var(--pf-amber);
    }
    .global-discount-inputs{ display:flex; gap:10px; align-items:center; margin-top:5px; }
    .global-discount-type-group{ display:flex; gap:5px; }
    .global-discount-btn{
        padding:4px 12px;
        font-size:12px;
        border:1px solid var(--pf-line);
        background:#f8f9fa;
        cursor:pointer;
        border-radius:999px;
    }
    .global-discount-btn.active{ background:var(--pf-green); color:#fff; border-color:var(--pf-green); }

    /* ---- Move / remove buttons ---- */
    .move-buttons{ display:flex; gap:4px; margin-top:6px; }
    .move-btn{
        padding:5px 10px;
        margin:0;
        font-size:12px;
        cursor:pointer;
        border:1px solid var(--pf-line);
        border-radius:7px;
        background:#fff;
        color:var(--pf-ink);
        transition:all .15s ease;
    }
    .move-btn:hover{ background:var(--pf-ink); color:#fff; border-color:var(--pf-ink); }
    .move-btn:disabled{ opacity:.4; cursor:not-allowed; background:var(--pf-paper); color:var(--pf-muted); }

    .remove-item{ cursor:pointer; border-radius:7px; font-weight:600; }

    #add-item{
        border:1.5px dashed var(--pf-ink);
        background:#fff;
        color:var(--pf-ink);
        border-radius:9px;
        font-weight:600;
        padding:8px 16px;
    }
    #add-item:hover{ background:var(--pf-gold-soft, #EEF0FA); }

    /* ---- Tax options ---- */
    .tax-options{ margin-bottom:10px; }
    .tax-options .radio{
        border:1px solid var(--pf-line);
        border-radius:9px;
        padding:9px 12px;
        margin:0 0 8px 0;
        transition:border-color .15s ease, background .15s ease;
    }
    .tax-options .radio:hover{ border-color:var(--pf-ink); background:var(--pf-paper); }
    .tax-options .radio label{
        font-size:13px;
        text-transform:none;
        letter-spacing:0;
        font-weight:500;
        color:var(--pf-ink);
        display:flex;
        align-items:center;
        gap:8px;
    }
    .other-tax-container{
        margin-top:10px;
        padding:14px;
        background:var(--pf-paper);
        border-radius:9px;
        display:none;
    }

    /* ---- Totals table ---- */
    .box-footer .table.table-bordered{
        background:var(--pf-card);
        border:1px solid var(--pf-line);
        border-radius:10px;
        overflow:hidden;
        font-size:13.5px;
    }
    .box-footer .table.table-bordered td{
        border-color:var(--pf-line) !important;
        padding:10px 14px;
        vertical-align:middle;
    }
    .box-footer .table.table-bordered .ttc-row td{
        background:var(--pf-ink);
        color:#fff;
    }
    .box-footer .table.table-bordered .ttc-row td strong{ color:#fff; font-size:15px; }

    /* ---- Footer action buttons ---- */
    .box-footer .btn-default{
        border:1px solid var(--pf-line);
        background:#fff;
        color:var(--pf-ink);
        border-radius:9px;
        font-weight:600;
        padding:9px 18px;
        margin-right:8px;
    }
    .box-footer .btn-default:hover{ border-color:var(--pf-ink); color:var(--pf-ink); background:var(--pf-paper); }
    .box-footer .btn-primary{
        background:var(--pf-ink);
        border-color:var(--pf-ink);
        border-radius:9px;
        font-weight:600;
        padding:9px 20px;
    }
    .box-footer .btn-primary:hover{ background:var(--pf-ink-soft); border-color:var(--pf-ink-soft); }

    @media (max-width: 991px){
        .content-header{ margin:12px; padding:20px !important; }
        section.content{ padding:0 12px 12px 12px; }
        .box.box-primary > .box-body{ padding:16px; }
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-file-text-o"></i> Proforma</h1>
        <small>Modifier le proforma <?php echo $quote['quote_number']; ?></small>
    </section>

    <section class="content">
        <div class="row">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Modifier le proforma <?php echo $quote['quote_number']; ?></h3>
                </div>

                <form action="<?php echo site_url('admin/proforma/update') ?>" id="<?= $formID; ?>" method="post" accept-charset="utf-8">
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
                                <h4>Articles / Services du proforma <small class="text-muted">(Glissez-déposez pour réorganiser)</small></h4>
                                <div id="items-container">
                                    <?php
                                    $itemIndex = 0;
                                    foreach ($quote['items'] as $item) {
                                        $discountType = isset($item['discount_type']) ? $item['discount_type'] : 'percent';
                                        $discountValue = isset($item['discount']) ? $item['discount'] : 0;
                                        $itemType = isset($item['item_type']) ? $item['item_type'] : 'product';
                                        ?>
                                        <div class="repeater-item" data-index="<?= $itemIndex ?>" data-item-type="<?= $itemType ?>">
                                            <span class="item-index"><?= $itemIndex + 1 ?></span>
                                            <span class="drag-handle"><i class="fa fa-arrows-alt"></i></span>
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
                                                    <label>Unité</label>
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
                                                    <label>PU</label>
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
                                                <!-- Suppression et déplacement -->
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
                                        <?php
                                        $itemIndex++;
                                    }
                                    ?>
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
                                    <tr>
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
                        <a href="<?php echo base_url('admin/proforma'); ?>" class="btn btn-default">
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script type="text/javascript">
    var base_url = '<?= base_url() ?>';

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
            $.post(base_url + 'admin/proforma/get_items_by_category_name', { category_name: category }, function(data) {
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

        // Événements de déplacement
        $(document).on('click', '.move-up', function() {
            var $item = $(this).closest('.repeater-item');
            moveItem($item, -1);
        });

        $(document).on('click', '.move-down', function() {
            var $item = $(this).closest('.repeater-item');
            moveItem($item, 1);
        });

        // Ajout de lignes
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

            // Réinitialiser les états des boutons de remise
            $new.find('.discount-type-btn').removeClass('active');
            $new.find('.discount-type-btn[data-type="percent"]').addClass('active');

            // Supprimer les anciens événements de draggable/droppable
            if ($new.data('ui-draggable')) {
                $new.draggable('destroy');
                $new.droppable('destroy');
            }

            $new.find('.remove-item').off('click').on('click', function() {
                $(this).closest('.repeater-item').remove();
                updateItemIndices();
                calculateTotals();
            });

            $('#items-container').append($new);
            initializeDragAndDrop();
            updateItemIndices();
            handleItemTypeChange($new);
            calculateTotals();
        });

        $(document).on('click', '.remove-item', function() {
            $(this).closest('.repeater-item').remove();
            updateItemIndices();
            calculateTotals();
        });

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
                text: "Voulez-vous vraiment mettre à jour ce proforma ?",
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
                                    window.location.href = data.redirect_url || '<?= base_url('admin/proforma') ?>';
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

        // ========== INITIALISATION ==========
        // Initialisation des lignes existantes
        $('.repeater-item').each(function() {
            const $row = $(this);
            const initialType = $row.find('.item-type').val();
            if (initialType === 'product') {
                const category = $row.find('.item-category').val();
                if (category) {
                    loadProductsByCategory(category, $row.find('.item-name'), $row);
                }
                calculateItemTotal(this);
            } else {
                const serviceName = $row.find('.item-name').val();
                if (serviceName) {
                    const uniqueId = 'datalist_init_' + new Date().getTime();
                    $row.find('.item-name').attr('list', uniqueId);
                    const $datalist = $row.find('.item-datalist');
                    $datalist.attr('id', uniqueId);
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
                                $datalist.html('<option value="' + service.name + '" data-price="' + service.unit_price + '" data-unit="' + (service.duration || 'prestation') + '">');
                            }
                            calculateItemTotal($row[0]);
                            calculateTotals();
                        }
                    });
                } else {
                    handleItemTypeChange($row);
                    calculateItemTotal($row[0]);
                }
            }
        });

        // Initialiser le drag and drop et les indices
        initializeDragAndDrop();
        updateItemIndices();
        calculateTotals();
    });
</script>