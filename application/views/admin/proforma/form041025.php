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
    }
    .discount-field {
        background-color: #fff8e1;
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
                        <h3 class="box-title">Devis</h3>
                    </div>

                    <form action="<?php echo site_url('admin/quoteitem/add') ?>" id="<?= $formID; ?>" method="post" accept-charset="utf-8">
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
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($clients as $key => $client): ?>
                                            <option value="<?php echo $client['id']; ?>">
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
                                    <input id="quote_date" name="quote_date" type="text" class="form-control dateSelect" value="<?= set_value('quote_date', date('d/m/Y')) ?>" readonly />
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Date limite</label>
                                    <input id="valid_until" name="valid_until" type="text" class="form-control dateSelect" value="<?= set_value('valid_until', date('d/m/Y')) ?>" readonly />
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
                                        <option value="Espèces">Espèces</option>
                                        <option value="Chèque">Chèque</option>
                                        <option value="Virement">Virement</option>
                                        <option value="Carte bancaire">Carte bancaire</option>
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
                                                <div class="form-group col-md-2">
                                                    <label>Quantité <small class="req">*</small></label>
                                                    <input type="text" name="quantity[]" class="form-control quantity" value="" required>
                                                    <div class="availability" style="margin-top: 5px; color: #3c8dbc; font-weight: bold;">
                                                        Stock disponible: <span class="available-qty">0</span>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label>Prix unitaire</label>
                                                    <input type="number" name="price[]" class="form-control price" min="0" step="0.01">
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label>Remise</label>
                                                    <div class="input-group">
                                                        <input type="number" name="discount_amount[]" class="form-control discount-amount" min="0" step="0.01" placeholder="Montant">
                                                        <select name="discount_type[]" class="form-control discount-type">
                                                            <option value="fixed">FCFA</option>
                                                            <option value="percent">%</option>
                                                        </select>
                                                    </div>
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
                                        <label>Remise globale</label>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="input-group">
                                                    <input type="number" id="global_discount_amount" name="global_discount_amount" class="form-control" min="0" step="0.01" placeholder="Montant" value="0">
                                                    <select id="global_discount_type" name="global_discount_type" class="form-control">
                                                        <option value="fixed">FCFA</option>
                                                        <option value="percent">%</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" id="apply_tva" name="apply_tva" value="1"> Appliquer la TVA (18%)
                                        </label>
                                    </div>
                                </div>
                                <!-- === Tableau des totaux mis à jour === -->
                                <div class="col-md-4">
                                    <table class="table table-bordered">
                                        <tr>
                                            <td>Total brut (avant remise):</td>
                                            <td class="text-right"><span id="total_brut">0.00</span></td>
                                            <input type="hidden" name="total_ht" id="totalBrut">
                                        </tr>
                                        <tr>
                                            <td>Total remises:</td>
                                            <td class="text-right"><span id="total_remise">0.00</span></td>
                                            <input type="hidden" name="total_remise" id="totalRemise">
                                        </tr>
                                        <tr>
                                            <td>Total après remise:</td>
                                            <td class="text-right"><span id="total_after_discount">0.00</span></td>
                                            <input type="hidden" name="total_after_discount" id="totalAfterDiscount">
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
</script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/quote/actions.js"></script>

<script>
    document.getElementById("customer_select").addEventListener("change", function() {
        let val = this.value;
        if (val === "new") {
            document.querySelectorAll(".new-client-fields").forEach(el => el.style.display = "block");
        } else {
            document.querySelectorAll(".new-client-fields").forEach(el => el.style.display = "none");
        }
    });

    // Fonctions pour gérer les remises
    function calculateItemTotal(item) {
        const quantity = parseFloat(item.querySelector('.quantity').value) || 0;
        const price = parseFloat(item.querySelector('.price').value) || 0;
        const discountAmount = parseFloat(item.querySelector('.discount-amount').value) || 0;
        const discountType = item.querySelector('.discount-type').value;

        let total = quantity * price;
        let discount = 0;

        if (discountAmount > 0) {
            if (discountType === 'percent') {
                discount = total * (discountAmount / 100);
            } else {
                discount = discountAmount;
            }
            total -= discount;
        }

        item.querySelector('.total-price').textContent = total.toFixed(2);
        return Math.max(total, 0);
    }

    function calculateGlobalTotals() {
        let totalHT = 0;
        const items = document.querySelectorAll('.repeater-item');

        items.forEach(item => {
            totalHT += calculateItemTotal(item);
        });

        // Calcul de la remise globale
        const globalDiscountAmount = parseFloat(document.getElementById('global_discount_amount').value) || 0;
        const globalDiscountType = document.getElementById('global_discount_type').value;
        let globalDiscount = 0;

        if (globalDiscountAmount > 0) {
            if (globalDiscountType === 'percent') {
                globalDiscount = totalHT * (globalDiscountAmount / 100);
            } else {
                globalDiscount = globalDiscountAmount;
            }
        }

        const totalAfterDiscount = Math.max(totalHT - globalDiscount, 0);

        // Calcul de la TVA
        const applyTVA = document.getElementById('apply_tva').checked;
        const tvaRate = 0.18;
        const tvaAmount = applyTVA ? totalAfterDiscount * tvaRate : 0;
        const totalTTC = totalAfterDiscount + tvaAmount;

        // Mise à jour de l'affichage
        document.getElementById('total_ht').textContent = totalHT.toFixed(2);
        document.getElementById('totalHT').value = totalHT.toFixed(2);
        document.getElementById('global_discount_display').textContent = globalDiscount.toFixed(2);
        document.getElementById('globalDiscountTotal').value = globalDiscount.toFixed(2);
        document.getElementById('total_after_discount').textContent = totalAfterDiscount.toFixed(2);
        document.getElementById('totalAfterDiscount').value = totalAfterDiscount.toFixed(2);
        document.getElementById('tva_amount').textContent = tvaAmount.toFixed(2);
        document.getElementById('tvaAmount').value = tvaAmount.toFixed(2);
        document.getElementById('total_ttc').textContent = totalTTC.toFixed(2);
        document.getElementById('totalTTC').value = totalTTC.toFixed(2);

        // Affichage/masquage de la ligne TVA
        document.querySelector('.tva-row').style.display = applyTVA ? '' : 'none';
    }

    // Événements pour le calcul automatique
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity') ||
            e.target.classList.contains('price') ||
            e.target.classList.contains('discount-amount') ||
            e.target.classList.contains('discount-type')) {
            calculateGlobalTotals();
        }
    });

    document.getElementById('apply_tva').addEventListener('change', calculateGlobalTotals);
    document.getElementById('global_discount_amount').addEventListener('input', calculateGlobalTotals);
    document.getElementById('global_discount_type').addEventListener('change', calculateGlobalTotals);

    // Initialisation du calcul
    calculateGlobalTotals();
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