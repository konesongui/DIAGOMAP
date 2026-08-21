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
    }
    .discount-type-btn {
        padding: 4px 8px;
        font-size: 12px;
        border: 1px solid #ccc;
        background: #f8f9fa;
        cursor: pointer;
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
                    <h3 class="box-title">Modifier le devis fournisseur <?php echo $quote['quote_number']; ?></h3>
                </div>

                <form action="<?php echo site_url('admin/quoteitem_supplier/update') ?>" id="<?= $formID; ?>" method="post" accept-charset="utf-8">
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
                                <label for="exampleInputEmail1">User<small class="req"> *</small></label>
                                <input id="user_name" name="user_name" readonly placeholder="" type="text" class="form-control"  value="<?php echo $quote['user_name']; ?>" />
                                <span class="text-danger"><?php echo form_error('user_name'); ?></span>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="exampleInputEmail1">Fournisseur</label><small class="req"> *</small>
                                <select class="form-control" name="customer">
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <?php foreach ($supplier as $client) { ?>
                                        <option value="<?php echo $client['id']; ?>" <?php echo ((int)$client['id'] == (int)$quote['customer_id']) ? 'selected' : ''; ?>>
                                            <?php echo $client['item_supplier'] .' ' . $client['lastname']. ' (' . $client['phone'] . ')'; ?>
                                        </option>
                                    <?php } ?>
                                </select>
                                <span class="text-danger"><?php echo form_error('customer'); ?></span>
                            </div>

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
                                <h4>Articles du devis</h4>
                                <div id="items-container">
                                    <?php foreach ($quote['items'] as $item) {
                                        $discountType = isset($item['discount_type']) ? $item['discount_type'] : 'percent';
                                        $discountValue = isset($item['discount']) ? $item['discount'] : 0;
                                        ?>
                                        <div class="repeater-item">
                                            <div class="row">
                                                <div class="form-group col-md-2">
                                                    <label>Catégorie <small class="req">*</small></label>
                                                    <input type="text" name="item_category[]" class="form-control item-category" list="category-list" value="<?php echo $item['category_name']; ?>" placeholder="Sélectionner ou enregistrer une catégorie" required>
                                                    <datalist id="category-list">
                                                        <?php foreach ($itemcatlist as $category): ?>
                                                        <option value="<?= $category['item_category'] ?>">
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
                                                    </datalist>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>Unité</label>
                                                    <input type="text" name="unit[]" class="form-control unit" value="<?php echo $item['unit']; ?>">
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>Quantité <small class="req">*</small></label>
                                                    <input type="number" name="quantity[]" class="form-control quantity" value="<?php echo $item['quantity']; ?>" min="1" required>
                                                    <div class="availability">
                                                        Stock: <span class="available-qty">0</span>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label>Prix unitaire</label>
                                                    <input type="number" name="price[]" class="form-control price" min="0" step="0.01" value="<?php echo $item['unit_price']; ?>">
                                                </div>
                                                <!-- Champs de remise améliorés -->
                                                <div class="form-group col-md-2">
                                                    <label>Remise</label>
                                                    <div class="discount-type-group mb-1">
                                                        <button type="button" class="discount-type-btn <?php echo $discountType == 'percent' ? 'active' : ''; ?>" data-type="percent">%</button>
                                                        <button type="button" class="discount-type-btn <?php echo $discountType == 'amount' ? 'active' : ''; ?>" data-type="amount">FCFA</button>
                                                        <input type="hidden" name="discount_type[]" class="discount-type" value="<?php echo $discountType; ?>">
                                                    </div>
                                                    <div class="discount-input-group">
                                                        <span class="discount-symbol">
                                                            <span class="discount-symbol-text"><?php echo $discountType == 'percent' ? '%' : 'FCFA'; ?></span>
                                                        </span>
                                                        <input type="number" name="discount[]" class="form-control discount discount-field" min="0" step="0.01" value="<?php echo $discountValue; ?>" placeholder="0.00" style="border-radius: 0 4px 4px 0;">
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>PU.NET</label>
                                                    <div class="total-price"><?php echo number_format($item['line_total'], 2); ?></div>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>Montant Net</label>
                                                    <div class="total-price-after-discount"><?php echo number_format(isset($item['line_total_after_discount']) ? $item['line_total_after_discount'] : $item['line_total'], 2); ?></div>
                                                    <input type="hidden" name="line_total_after_discount[]" class="line-total-after-discount" value="<?php echo isset($item['line_total_after_discount']) ? $item['line_total_after_discount'] : $item['line_total']; ?>">
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
                                        <td>Total Remise:</td>
                                        <td class="text-right"><span id="total_discount"><?php echo number_format(isset($quote['total_discount']) ? $quote['total_discount'] : 0, 2); ?></span></td>
                                        <input type="hidden" name="total_discount" id="totalDiscount" value="<?php echo isset($quote['total_discount']) ? $quote['total_discount'] : 0; ?>">
                                    </tr>
                                    <tr>
                                        <td>Total après remise:</td>
                                        <td class="text-right"><span id="total_after_discount"><?php echo number_format(isset($quote['total_after_discount']) ? $quote['total_after_discount'] : $quote['total_ht'], 2); ?></span></td>
                                        <input type="hidden" name="total_after_discount" id="totalAfterDiscount" value="<?php echo isset($quote['total_after_discount']) ? $quote['total_after_discount'] : $quote['total_ht']; ?>">
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
                        <a href="<?php echo base_url('admin/quoteitem_supplier'); ?>" class="btn btn-default">
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
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded - initializing quote form');

        // Fonction pour calculer le total d'un article
        function calculateItemTotal(item) {
            const quantity = parseFloat(item.querySelector('.quantity').value) || 0;
            const price = parseFloat(item.querySelector('.price').value) || 0;
            const discount = parseFloat(item.querySelector('.discount').value) || 0;
            const discountType = item.querySelector('.discount-type').value;

            let discountAmount = 0;
            if (discountType === 'percent') {
                discountAmount = (price * quantity) * (discount / 100);
            } else {
                discountAmount = discount * quantity;
            }

            const totalHT = price * quantity;
            const totalAfterDiscount = Math.max(totalHT - discountAmount, 0);
            const unitPriceNet = quantity > 0 ? totalAfterDiscount / quantity : 0;

            // Mise à jour de l'affichage
            item.querySelector('.total-price').textContent = unitPriceNet.toFixed(2);
            item.querySelector('.total-price-after-discount').textContent = totalAfterDiscount.toFixed(2);
            item.querySelector('.line-total-after-discount').value = totalAfterDiscount.toFixed(2);

            return {
                totalHT: totalHT,
                discountAmount: discountAmount,
                totalAfterDiscount: totalAfterDiscount
            };
        }

        // Fonction pour calculer les totaux globaux
        function calculateTotals() {
            console.log('Calculating totals...');

            let totalHT = 0;
            let totalDiscount = 0;
            let totalAfterDiscount = 0;


            // Calcul des totaux des articles
            document.querySelectorAll('.repeater-item').forEach((item, index) => {
                const itemTotals = calculateItemTotal(item);
                totalHT += itemTotals.totalHT;
                totalDiscount += itemTotals.discountAmount;
                totalAfterDiscount += itemTotals.totalAfterDiscount;
            });

            // Calcul de la TVA
            const applyTVA = document.getElementById('apply_tva').checked;
            const tvaRate = <?php echo $quote['tva_rate']; ?>;
            const tvaAmount = applyTVA ? totalAfterDiscount * (tvaRate / 100) : 0;
            const totalTTC = totalAfterDiscount + tvaAmount;


            // Mise à jour de l'affichage - FORCER l'affichage
            document.getElementById('total_ht').textContent = totalHT.toFixed(2);
            document.getElementById('totalHT').value = totalHT.toFixed(2);

            document.getElementById('total_discount').textContent = totalDiscount.toFixed(2);
            document.getElementById('totalDiscount').value = totalDiscount.toFixed(2);

            document.getElementById('total_after_discount').textContent = totalAfterDiscount.toFixed(2);
            document.getElementById('totalAfterDiscount').value = totalAfterDiscount.toFixed(2);

            document.getElementById('tva_amount').textContent = tvaAmount.toFixed(2);
            document.getElementById('tvaAmount').value = tvaAmount.toFixed(2);

            document.getElementById('total_ttc').textContent = totalTTC.toFixed(2);
            document.getElementById('totalTTC').value = totalTTC.toFixed(2);

            // Affichage/masquage de la ligne TVA
            document.querySelector('.tva-row').style.display = applyTVA ? '' : 'none';

            console.log('Totals calculated:', { totalHT, totalDiscount, totalAfterDiscount, tvaAmount, totalTTC });
        }

        // Fonction pour changer le type de remise
        function setupDiscountTypeButtons() {
            document.querySelectorAll('.discount-type-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const item = this.closest('.repeater-item');
                    const type = this.dataset.type;
                    const discountTypeInput = item.querySelector('.discount-type');
                    const symbolText = item.querySelector('.discount-symbol-text');

                    // Mettre à jour les boutons
                    item.querySelectorAll('.discount-type-btn').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    this.classList.add('active');

                    // Mettre à jour le type et le symbole
                    discountTypeInput.value = type;
                    symbolText.textContent = type === 'percent' ? '%' : 'FCFA';

                    calculateTotals();
                });
            });
        }

        // Événements pour les champs
        function setupEventListeners() {
            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('quantity') ||
                    e.target.classList.contains('price') ||
                    e.target.classList.contains('discount')) {
                    calculateTotals();
                }
            });

            document.getElementById('apply_tva').addEventListener('change', calculateTotals);
        }

        // Gestion de l'ajout d'articles
        document.getElementById('add-item').addEventListener('click', function() {
            const container = document.getElementById('items-container');
            const firstItem = document.querySelector('.repeater-item');
            const newItem = firstItem.cloneNode(true);

            // Réinitialiser les valeurs
            newItem.querySelector('.item-category').value = '';
            newItem.querySelector('.item-name').value = '';
            newItem.querySelector('.unit').value = '';
            newItem.querySelector('.quantity').value = '1';
            newItem.querySelector('.price').value = '0';
            newItem.querySelector('.discount').value = '0';
            newItem.querySelector('.discount-type').value = 'percent';
            newItem.querySelector('.discount-symbol-text').textContent = '%';
            newItem.querySelector('.total-price').textContent = '0.00';
            newItem.querySelector('.total-price-after-discount').textContent = '0.00';
            newItem.querySelector('.line-total-after-discount').value = '0';
            newItem.querySelector('.available-qty').textContent = '0';

            // Réinitialiser les boutons
            newItem.querySelectorAll('.discount-type-btn').forEach(btn => {
                btn.classList.remove('active');
                if (btn.dataset.type === 'percent') {
                    btn.classList.add('active');
                }
            });

            container.appendChild(newItem);
            setupDiscountTypeButtons();

            // Ajouter l'événement de suppression
            newItem.querySelector('.remove-item').addEventListener('click', function() {
                newItem.remove();
                calculateTotals();
            });

            calculateTotals();
        });

        // Gestion de la suppression d'articles
        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.repeater-item').remove();
                calculateTotals();
            });
        });

        // Gestion de la sélection d'articles
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

        // Soumission du formulaire
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
                    const submitBtn = document.getElementById('<?= $submitBtn; ?>');
                    const originalText = submitBtn.innerHTML;

                    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Mise à jour...';
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
                                    icon: "success"
                                }).then(() => {
                                    window.location.href = data.redirect_url || '<?= base_url('admin/quoteitem'); ?>';
                                });
                            } else {
                                Swal.fire("Erreur", data.message, "error");
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire("Erreur", "Une erreur est survenue", "error");
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
        setupEventListeners();

        // Calcul initial avec un délai pour s'assurer que tout est chargé
        setTimeout(() => {
            calculateTotals();
        }, 100);
    });
</script>