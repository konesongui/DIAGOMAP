<?php
// Set all the form data
$formID     = 'stockEntryForm';
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
    .profit-info {
        font-size: 12px;
        margin-top: 5px;
        color: #28a745;
    }
    .profit-info.negative {
        color: #dc3545;
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-building-o"></i> Inventaire</h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Entrée de stock</h3>
                    </div>

                    <form action="<?php echo site_url('admin/stockentry/add') ?>" id="<?= $formID; ?>" method="post" accept-charset="utf-8">
                        <div class="box-body">
                            <div class="row">
                                <?php if ($this->session->flashdata('msg')) { ?>
                                    <div class="alert alert-info"><?php echo $this->session->flashdata('msg') ?></div>
                                <?php } ?>
                                <?php if (isset($error_message)) { ?>
                                    <div class='alert alert-danger'><?php echo $error_message ?></div>
                                <?php } ?>

                                <?php echo $this->customlib->getCSRF(); ?>

                                <div class="form-group col-md-8">
                                    <label>Désignation <small class="req">*</small></label>
                                    <input name="designation" type="text" class="form-control" value="<?= set_value('designation') ?>" />
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Date <small class="req">*</small></label>
                                    <input id="issue_date" name="issue_date" type="text" class="form-control date" value="<?= set_value('issue_date', date('d/m/Y')) ?>" readonly />
                                </div>

                                <div class="clearfix"></div>
                                <hr>

                                <div class="col-md-12">
                                    <h4>Choisissez les articles</h4>
                                    <div id="items-container">
                                        <div class="repeater-item">
                                            <div class="row">
                                                <div class="form-group col-md-3">
                                                    <label>Catégorie <small class="req">*</small></label>
                                                    <input type="text" name="item_category_name[]" class="form-control item-category" list="category-list" autocomplete="off" placeholder="Tapez ou sélectionnez une catégorie" required>
                                                    <datalist id="category-list">
                                                        <?php foreach ($itemcatlist as $category): ?>
                                                        <option value="<?= $category['item_category'] ?>">
                                                            <?php endforeach; ?>
                                                    </datalist>
                                                </div>
                                                <div class="form-group col-md-3">
                                                    <label>Article <small class="req">*</small></label>
                                                    <input type="text" name="item_name[]" class="form-control item-name" list="item-list-0" autocomplete="off" placeholder="Tapez ou sélectionnez un article" required>
                                                    <datalist id="item-list-0" class="item-datalist">
                                                        <option value="">Sélectionnez d'abord une catégorie</option>
                                                    </datalist>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>Unité</label>
                                                    <input type="text" name="unit[]" class="form-control unit" readonly>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label>Quantité <small class="req">*</small></label>
                                                    <input type="number" name="quantity[]" class="form-control quantity" min="1" required>
                                                    <div class="availability">
                                                        <small>Disponible: <span class="available-qty">0</span></small>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label>Prix d'achat</label>
                                                    <input type="number" name="purchase_price[]" class="form-control purchase_price" min="0" step="0.01" placeholder="Prix d'achat">
                                                </div>

                                                <div class="form-group col-md-2">
                                                    <label>Prix de vente</label>
                                                    <input type="number" name="price[]" class="form-control price" min="0" step="0.01" placeholder="Prix de vente">
                                                </div>
                                                <div class="form-group col-md-2">
                                                    <label>Bénéfice/unité</label>
                                                    <div class="profit-per-unit">0.00</div>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>Total (achat)</label>
                                                    <div class="total-price">0.00</div>
                                                </div>
                                                <div class="form-group col-md-1">
                                                    <label>&nbsp;</label>
                                                    <button type="button" class="btn btn-danger btn-sm remove-item">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="profit-info">
                                                        Bénéfice total: <span class="total-profit">0.00</span>
                                                        (Marge: <span class="margin-percent">0</span>%)
                                                    </div>
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
                            <div class="pull-right" style="margin-right: 15px;">
                                <h4>Total général (Prix d'achat): <span id="grand-total">0.00</span></h4>
                                <h4 style="color: #28a745;">Bénéfice total estimé: <span id="grand-profit">0.00</span></h4>
                                <input type="hidden" name="grandtotal" id="grandTotal">
                                <input type="hidden" name="grandprofit" id="grandProfit">
                            </div>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/stockentry/actions.js"></script>
<script>
    $(document).ready(function() {
        // Initialisation du datepicker
        if ($('.date').length) {
            $('.date').datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                todayHighlight: true
            });
        }

        // Gestionnaire pour l'ajout d'un nouvel article
        $('#add-item').click(function(e) {
            e.preventDefault();

            var index = $('.repeater-item').length;
            var newItem = $('.repeater-item').first().clone();

            newItem.find('input').val('');
            newItem.find('.total-price').text('0.00');
            newItem.find('.available-qty').text('0');
            newItem.find('.profit-per-unit').text('0.00');
            newItem.find('.total-profit').text('0.00');
            newItem.find('.margin-percent').text('0');
            newItem.find('.item-name').attr('list', 'item-list-' + index);
            newItem.find('.item-name').data('item-id', '');

            // Supprimer les IDs existants
            newItem.find('[id]').removeAttr('id');

            // Créer un nouveau datalist pour cette ligne
            var newDatalist = $('<datalist>', {
                id: 'item-list-' + index,
                class: 'item-datalist'
            }).html('<option value="">Sélectionnez d\'abord une catégorie</option>');

            newItem.find('.item-name').after(newDatalist);
            $('#items-container').append(newItem);
            recalculateGrandTotal();
        });

        // Gestionnaire pour supprimer un article
        $(document).on('click', '.remove-item', function() {
            if ($('.repeater-item').length > 1) {
                $(this).closest('.repeater-item').remove();
                recalculateGrandTotal();
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Attention!',
                    text: 'Vous devez avoir au moins un article'
                });
            }
        });

        // Gestionnaire pour le changement de catégorie
        $(document).on('change', '.item-category', function() {
            var $row = $(this).closest('.repeater-item');
            var categoryName = $(this).val();
            var $itemName = $row.find('.item-name');
            var index = $('.repeater-item').index($row);
            var $datalist = $row.find('.item-datalist');

            if (!$datalist.length) {
                $datalist = $('<datalist>', {
                    id: 'item-list-' + index,
                    class: 'item-datalist'
                });
                $itemName.after($datalist);
                $itemName.attr('list', 'item-list-' + index);
            }

            if (categoryName) {
                loadItemsByCategory(categoryName, $datalist, index);
            } else {
                $datalist.html('<option value="">Sélectionnez d\'abord une catégorie</option>');
            }

            $itemName.val('');
            $row.find('.unit').val('');
            $row.find('.purchase_price').val('');
            $row.find('.price').val('');
            $row.find('.available-qty').text('0');
            $row.find('.profit-per-unit').text('0.00');
            $row.find('.total-profit').text('0.00');
            $row.find('.margin-percent').text('0');
            calculateItemTotal($row);
        });

        // Fonction pour charger les articles d'une catégorie
        function loadItemsByCategory(categoryName, $datalist, index) {
            if (categoryName) {
                $.ajax({
                    type: "POST",
                    url: base_url + "admin/stockentry/get_items_by_category_name",
                    data: {'category_name': categoryName},
                    dataType: "json",
                    success: function (data) {
                        var options = '';
                        if (data.length > 0) {
                            $.each(data, function (i, obj) {
                                options += '<option value="' + obj.name + '" data-unit="' + (obj.unit || '') + '" data-purchase_price="' + (obj.purchase_price || 0) + '" data-price="' + (obj.price || 0) + '" data-id="' + obj.id + '">';
                            });
                        } else {
                            options = '<option value="">Aucun article trouvé. Vous pouvez en créer un nouveau en tapant directement.</option>';
                        }
                        $datalist.html(options);
                    },
                    error: function(xhr, status, error) {
                        console.error('Erreur lors du chargement des articles:', error);
                        $datalist.html('<option value="">Erreur de chargement</option>');
                    }
                });
            } else {
                $datalist.html('<option value="">Sélectionnez d\'abord une catégorie</option>');
            }
        }

        // Gestionnaire pour la sélection d'article
        $(document).on('change', '.item-name', function() {
            var $row = $(this).closest('.repeater-item');
            var itemName = $(this).val();
            var categoryName = $row.find('.item-category').val();
            var selectedOption = $(this).find('option:selected');

            var purchase_price = selectedOption.data('purchase_price');
            var price = selectedOption.data('price');

            if (purchase_price !== undefined && purchase_price !== null) {
                $row.find('.purchase_price').val(purchase_price);
            }
            if (price !== undefined && price !== null) {
                $row.find('.price').val(price);
            }

            if (itemName && categoryName) {
                getItemDetails(itemName, categoryName, $row);
            } else {
                calculateProfit($row);
                calculateItemTotal($row);
            }
        });

        // Fonction pour récupérer les détails d'un article
        function getItemDetails(itemName, categoryName, $row) {
            $.ajax({
                type: "POST",
                url: base_url + "admin/stockentry/get_item_details",
                data: {
                    'item_name': itemName,
                    'category_name': categoryName
                },
                dataType: "json",
                success: function (response) {
                    if (response.status === 'success') {
                        $row.find('.unit').val(response.unit || '');
                        $row.find('.purchase_price').val(response.purchase_price || '');
                        $row.find('.price').val(response.price || '');
                        $row.find('.available-qty').text(response.quantity || 0);
                        $row.find('.item-name').data('item-id', response.item_id);
                        calculateProfit($row);
                        calculateItemTotal($row);
                    } else {
                        $row.find('.unit').val('');
                        $row.find('.purchase_price').val('');
                        $row.find('.price').val('');
                        $row.find('.available-qty').text('0');
                        $row.find('.item-name').data('item-id', 'new');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erreur lors de la récupération des détails:', error);
                }
            });
        }

        // Calcul du bénéfice
        function calculateProfit($row) {
            var purchase_price = parseFloat($row.find('.purchase_price').val()) || 0;
            var price = parseFloat($row.find('.price').val()) || 0;
            var quantity = parseFloat($row.find('.quantity').val()) || 0;

            var profit_per_unit = price - purchase_price;
            var total_profit = profit_per_unit * quantity;
            var margin_percent = purchase_price > 0 ? (profit_per_unit / purchase_price * 100) : 0;

            $row.find('.profit-per-unit').text(profit_per_unit.toFixed(2));
            $row.find('.total-profit').text(total_profit.toFixed(2));
            $row.find('.margin-percent').text(margin_percent.toFixed(1));

            var $profitSpan = $row.find('.profit-per-unit');
            var $profitInfo = $row.find('.profit-info');

            if (profit_per_unit < 0) {
                $profitSpan.css('color', '#dc3545');
                $profitInfo.addClass('negative').css('color', '#dc3545');
            } else {
                $profitSpan.css('color', '#28a745');
                $profitInfo.removeClass('negative').css('color', '#28a745');
            }

            calculateGrandProfit();
        }

        // Gestionnaires pour les calculs
        $(document).on('input', '.quantity, .purchase_price, .price', function() {
            var $row = $(this).closest('.repeater-item');
            calculateProfit($row);
            calculateItemTotal($row);
        });

        // Calcul du total par ligne (basé sur prix d'achat)
        function calculateItemTotal($row) {
            var quantity = parseFloat($row.find('.quantity').val()) || 0;
            var purchase_price = parseFloat($row.find('.purchase_price').val()) || 0;
            var total = quantity * purchase_price;
            $row.find('.total-price').text(total.toFixed(2));
            recalculateGrandTotal();
        }

        // Recalcul du total général
        function recalculateGrandTotal() {
            var grandTotal = 0;
            $('.total-price').each(function() {
                var total = parseFloat($(this).text()) || 0;
                grandTotal += total;
            });
            $('#grand-total').text(grandTotal.toFixed(2));
            $('#grandTotal').val(grandTotal.toFixed(2));
        }

        // Recalcul du bénéfice total
        function calculateGrandProfit() {
            var grandProfit = 0;
            $('.total-profit').each(function() {
                var profit = parseFloat($(this).text()) || 0;
                grandProfit += profit;
            });
            $('#grand-profit').text(grandProfit.toFixed(2));
            $('#grandProfit').val(grandProfit.toFixed(2));

            if (grandProfit < 0) {
                $('#grand-profit').css('color', '#dc3545');
            } else {
                $('#grand-profit').css('color', '#28a745');
            }
        }

        // Validation avant soumission
        $('#' + formID).on('submit', function(e) {
            e.preventDefault();

            var isValid = true;
            var errorMessages = [];

            // Vérifier que tous les articles ont un nom
            $('.item-name').each(function(index) {
                if (!$(this).val()) {
                    isValid = false;
                    errorMessages.push('Veuillez sélectionner un article pour chaque ligne');
                    return false;
                }
            });

            // Vérifier que toutes les quantités sont valides
            $('.quantity').each(function(index) {
                var qty = parseFloat($(this).val());
                if (isNaN(qty) || qty <= 0) {
                    isValid = false;
                    errorMessages.push('Veuillez saisir une quantité valide pour chaque article');
                    return false;
                }
            });

            if (!isValid) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation',
                    text: errorMessages.join('\n')
                });
                return false;
            }

            // Désactiver le bouton de soumission
            $('#' + submitBtn).prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Enregistrement...');

            // Soumettre le formulaire
            var form = this;
            $.ajax({
                url: $(form).attr('action'),
                type: 'POST',
                data: $(form).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Succès!',
                            text: response.message,
                            timer: 3000,
                            showConfirmButton: false
                        }).then(function() {
                            window.location.href = base_url + 'admin/stockentry';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur!',
                            text: response.message
                        });
                        $('#' + submitBtn).prop('disabled', false).html('<i class="fa fa-save"></i> Enregistrer');
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur!',
                        text: 'Une erreur est survenue lors de l\'enregistrement'
                    });
                    $('#' + submitBtn).prop('disabled', false).html('<i class="fa fa-save"></i> Enregistrer');
                }
            });

            return false;
        });
    });
</script>