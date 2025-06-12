<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Nouveau devis sans stock
        <small>Création d'un nouveau devis</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Accueil</a></li>
        <li><a href="<?php echo base_url('quotenostock'); ?>">Devis sans stock</a></li>
        <li class="active">Nouveau devis</li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Informations du devis</h3>
                </div>
                <form action="<?php echo base_url('quotenostock/store'); ?>" method="post" id="quoteForm">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_id">Client *</label>
                                    <select name="customer_id" id="customer_id" class="form-control select2" required>
                                        <option value="">Sélectionner un client</option>
                                        <?php foreach ($customers as $customer): ?>
                                            <option value="<?php echo $customer['id']; ?>"><?php echo $customer['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="quote_date">Date du devis *</label>
                                    <input type="date" class="form-control" id="quote_date" name="quote_date" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="designation">Désignation *</label>
                                    <input type="text" class="form-control" id="designation" name="designation" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="valid_until">Validité</label>
                                    <input type="date" class="form-control" id="valid_until" name="valid_until">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="delivery_location">Lieu de livraison</label>
                                    <input type="text" class="form-control" id="delivery_location" name="delivery_location">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="delivery_date">Date de livraison</label>
                                    <input type="date" class="form-control" id="delivery_date" name="delivery_date">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_terms">Conditions de paiement</label>
                                    <textarea class="form-control" id="payment_terms" name="payment_terms" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="delivery_terms">Conditions de livraison</label>
                                    <textarea class="form-control" id="delivery_terms" name="delivery_terms" rows="3"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="box box-info">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Articles</h3>
                                        <div class="box-tools pull-right">
                                            <button type="button" class="btn btn-box-tool" id="addItem">
                                                <i class="fa fa-plus"></i> Ajouter un article
                                            </button>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <table class="table table-bordered" id="itemsTable">
                                            <thead>
                                                <tr>
                                                    <th>Catégorie</th>
                                                    <th>Produit</th>
                                                    <th>Quantité</th>
                                                    <th>Prix unitaire</th>
                                                    <th>Total</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="4" class="text-right"><strong>Total HT</strong></td>
                                                    <td colspan="2">
                                                        <input type="number" class="form-control" id="total_ht" name="total_ht" readonly>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="text-right">
                                                        <div class="checkbox">
                                                            <label>
                                                                <input type="checkbox" id="apply_tva" name="apply_tva"> Appliquer la TVA
                                                            </label>
                                                        </div>
                                                    </td>
                                                    <td colspan="2">
                                                        <div class="input-group">
                                                            <input type="number" class="form-control" id="tva_rate" name="tva_rate" value="20" step="0.1" disabled>
                                                            <span class="input-group-addon">%</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="text-right"><strong>TVA</strong></td>
                                                    <td colspan="2">
                                                        <input type="number" class="form-control" id="tva_amount" name="tva_amount" readonly>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4" class="text-right"><strong>Total TTC</strong></td>
                                                    <td colspan="2">
                                                        <input type="number" class="form-control" id="total_ttc" name="total_ttc" readonly>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="notes">Notes</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                        <a href="<?php echo base_url('quotenostock'); ?>" class="btn btn-default">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Template pour les lignes d'articles -->
<template id="itemRowTemplate">
    <tr>
        <td>
            <input type="text" class="form-control category-name" name="items[INDEX][category_name]" required>
        </td>
        <td>
            <input type="text" class="form-control product-name" name="items[INDEX][product_name]" required>
        </td>
        <td>
            <input type="number" class="form-control quantity" name="items[INDEX][quantity]" min="1" value="1" required>
        </td>
        <td>
            <input type="number" class="form-control unit-price" name="items[INDEX][unit_price]" min="0" step="0.01" required>
        </td>
        <td>
            <input type="number" class="form-control line-total" name="items[INDEX][line_total]" readonly>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm remove-item">
                <i class="fa fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

<script>
$(document).ready(function() {
    // Initialisation de Select2
    $('.select2').select2();

    // Gestion de la TVA
    $('#apply_tva').change(function() {
        $('#tva_rate').prop('disabled', !$(this).is(':checked'));
        calculateTotals();
    });

    // Ajout d'une ligne d'article
    $('#addItem').click(function() {
        var template = $('#itemRowTemplate').html();
        var index = $('#itemsTable tbody tr').length;
        template = template.replace(/INDEX/g, index);
        $('#itemsTable tbody').append(template);
    });

    // Suppression d'une ligne d'article
    $(document).on('click', '.remove-item', function() {
        $(this).closest('tr').remove();
        calculateTotals();
    });

    // Calcul du total de la ligne
    $(document).on('input', '.quantity, .unit-price', function() {
        var row = $(this).closest('tr');
        var quantity = parseFloat(row.find('.quantity').val()) || 0;
        var unitPrice = parseFloat(row.find('.unit-price').val()) || 0;
        var lineTotal = quantity * unitPrice;
        row.find('.line-total').val(lineTotal.toFixed(2));
        calculateTotals();
    });

    // Calcul des totaux
    function calculateTotals() {
        var totalHT = 0;
        $('.line-total').each(function() {
            totalHT += parseFloat($(this).val()) || 0;
        });

        $('#total_ht').val(totalHT.toFixed(2));

        var tvaRate = $('#apply_tva').is(':checked') ? parseFloat($('#tva_rate').val()) : 0;
        var tvaAmount = totalHT * (tvaRate / 100);
        var totalTTC = totalHT + tvaAmount;

        $('#tva_amount').val(tvaAmount.toFixed(2));
        $('#total_ttc').val(totalTTC.toFixed(2));
    }

    // Validation du formulaire
    $('#quoteForm').submit(function(e) {
        if ($('#itemsTable tbody tr').length === 0) {
            e.preventDefault();
            alert('Veuillez ajouter au moins un article');
            return false;
        }
    });
});
</script> 