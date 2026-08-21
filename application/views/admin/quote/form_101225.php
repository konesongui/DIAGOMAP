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
                                                <div class="form-group col-md-1">
                                                    <label>Quantité <small class="req">*</small></label>
                                                    <input type="number" name="quantity[]" class="form-control quantity" value="1" min="1" required>
                                                    <div class="availability" style="margin-top: 5px; color: #3c8dbc; font-weight: bold;">
                                                        Stock: <span class="available-qty">0</span>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>Prix unitaire</label>
                                                    <input type="number" name="price[]" class="form-control price" min="0" step="0.01" value="0">
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
                                            <td>Total HT:</td>
                                            <td class="text-right"><span id="total_ht">0.00</span></td>
                                            <input type="hidden" name="total_ht" id="totalHT" value="0">
                                        </tr>
                                        <tr>
                                            <td>Total Remise:</td>
                                            <td class="text-right"><span id="total_discount">0.00</span></td>
                                            <input type="hidden" name="total_discount" id="totalDiscount" value="0">
                                        </tr>
                                        <tr>
                                            <td>Montant Net Hors-Taxe:</td>
                                            <td class="text-right"><span id="total_after_discount">0.00</span></td>
                                            <input type="hidden" name="total_after_discount" id="totalAfterDiscount" value="0">
                                        </tr>
                                        <tr class="tva-row" style="display:none;">
                                            <td>TVA (18%):</td>
                                            <td class="text-right"><span id="tva_amount">0.00</span></td>
                                            <input type="hidden" name="tva_amount" id="tvaAmount" value="0">
                                            <input type="hidden" name="tva_rate" value="18">
                                        </tr>
                                        <tr class="ttc-row">
                                            <td><strong>Total TTC:</strong></td>
                                            <td class="text-right"><strong><span id="total_ttc">0.00</span></strong></td>
                                            <input type="hidden" name="total_ttc" id="totalTTC" value="0">
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

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {

        // Gestion du nouveau client
        document.getElementById("customer_select").addEventListener("change", function() {
            let val = this.value;
            document.querySelectorAll(".new-client-fields").forEach(el => {
                el.style.display = (val === "new") ? "block" : "none";
            });
        });

        // 🔹 Fonction pour calculer le total d'un article
        function calculateItemTotal(item) {
            const quantity = parseFloat(item.querySelector('.quantity').value) || 0;
            const price = parseFloat(item.querySelector('.price').value) || 0;
            const discount = parseFloat(item.querySelector('.discount').value) || 0;
            const discountType = item.querySelector('.discount-type').value;

            // Calcul du montant de remise
            let discountAmount = 0;
            if (discountType === 'percent') {
                discountAmount = price * (discount / 100);
            } else {
                discountAmount = Math.min(discount, price);
            }

            // Prix unitaire après remise
            const totalHT = price - discountAmount;
            // Total après remise * quantité
            const totalAfterDiscount = totalHT * quantity;

            // Mise à jour affichage ligne
            item.querySelector('.total-price').textContent = totalHT.toFixed(2);
            item.querySelector('.total-price-after-discount').textContent = totalAfterDiscount.toFixed(2);
            item.querySelector('.line-total-after-discount').value = totalAfterDiscount.toFixed(2);

            return {
                totalHT: totalHT * quantity,
                discountAmount: discountAmount * quantity,
                totalAfterDiscount: totalAfterDiscount
            };
        }

        // 🔹 Fonction principale pour calculer tous les totaux
        function calculateTotals() {
            let totalHT = 0;
            let totalDiscount = 0;
            let totalAfterDiscount = 0;

            // Calcul des totaux des articles
            document.querySelectorAll('.repeater-item').forEach(item => {
                const itemTotals = calculateItemTotal(item);
                totalHT += itemTotals.totalHT;
                totalDiscount += itemTotals.discountAmount;
                totalAfterDiscount += itemTotals.totalAfterDiscount;
            });

            // 🔸 Calcul de la remise globale
            const globalDiscountAmount = parseFloat(document.getElementById('global_discount_amount').value) || 0;
            const globalDiscountType = document.getElementById('global_discount_type').value;
            let globalDiscount = 0;

            if (globalDiscountAmount > 0) {
                if (globalDiscountType === 'percent') {
                    globalDiscount = totalAfterDiscount * (globalDiscountAmount / 100);
                } else {
                    globalDiscount = Math.min(globalDiscountAmount, totalAfterDiscount);
                }
            }

            // 🔸 Totaux finaux
            const finalTotalDiscount = totalDiscount + globalDiscount;
            const finalTotalAfterDiscount = Math.max(totalAfterDiscount - globalDiscount, 0);

            // 🔸 Calcul TVA
            const applyTVA = document.getElementById('apply_tva').checked;
            const tvaRate = 0.18;
            const tvaAmount = applyTVA ? finalTotalAfterDiscount * tvaRate : 0;
            const totalTTC = finalTotalAfterDiscount + tvaAmount;

            // 🔸 Mise à jour affichage
            document.getElementById('total_ht').textContent = totalHT.toFixed(2);
            document.getElementById('totalHT').value = totalHT.toFixed(2);

            document.getElementById('total_discount').textContent = finalTotalDiscount.toFixed(2);
            document.getElementById('totalDiscount').value = finalTotalDiscount.toFixed(2);

            document.getElementById('total_after_discount').textContent = finalTotalAfterDiscount.toFixed(2);
            document.getElementById('totalAfterDiscount').value = finalTotalAfterDiscount.toFixed(2);

            document.getElementById('tva_amount').textContent = tvaAmount.toFixed(2);
            document.getElementById('tvaAmount').value = tvaAmount.toFixed(2);

            document.getElementById('total_ttc').textContent = totalTTC.toFixed(2);
            document.getElementById('totalTTC').value = totalTTC.toFixed(2);

            document.querySelector('.tva-row').style.display = applyTVA ? '' : 'none';
        }

        // 🔹 Gestion des types de remise
        function setupDiscountTypeButtons() {
            // Par article
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

            // Remise globale
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

        // 🔹 Recalcul automatique
        document.addEventListener('input', function(e) {
            if (['quantity', 'price', 'discount'].some(cls => e.target.classList.contains(cls)) ||
                e.target.id === 'global_discount_amount') {
                calculateTotals();
            }
        });

        document.getElementById('apply_tva').addEventListener('change', calculateTotals);

        // 🔹 Ajouter/Supprimer un article
        document.getElementById('add-item').addEventListener('click', function() {
            const container = document.getElementById('items-container');
            const firstItem = document.querySelector('.repeater-item');
            const newItem = firstItem.cloneNode(true);

            newItem.querySelectorAll('input').forEach(input => input.value = '');
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

            newItem.querySelector('.remove-item').addEventListener('click', function() {
                newItem.remove();
                calculateTotals();
            });
        });

        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.repeater-item').remove();
                calculateTotals();
            });
        });

        // 🔹 Gestion sélection d'article
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

        // 🔹 Soumission du formulaire avec confirmation
        document.getElementById('<?= $formID; ?>').addEventListener('submit', function(e) {
            e.preventDefault();

            Swal.fire({
                title: "Confirmation",
                text: "Voulez-vous vraiment créer ce devis ?",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Oui, créer le devis",
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
                                    window.location.href = data.redirect_url || '<?= base_url('admin/quoteitem'); ?>';
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
                                text: 'Une erreur réseau est survenue lors de la création du devis',
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

        // Initialisation
        setupDiscountTypeButtons();
        calculateTotals();
    });
</script>
