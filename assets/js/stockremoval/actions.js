"use strict";
// Class definition

// Set all the required variables for the following methods
var dtID = 'stockRemovalDatatable',
  formID = 'stockRemovalForm',
  dtButtons = {
      set: 'submitBtn',
  },
  remoteAJAXFunctions = {
      loadData: 'admin/stockremoval/data',
      add: 'admin/stockremoval/add',
  };



$(document).ready(function() {
    // Initialisation du datepicker
    $('.date').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayHighlight: true
    });

    // Validation du formulaire avant soumission
    function validateForm() {
        let isValid = true;
        let errors = [];

        // Vérification des champs obligatoires
        if (!$('input[name="origin"]').val().trim()) {
            errors.push('L\'origine est obligatoire');
            isValid = false;
        }

        if (!$('input[name="issue_date"]').val().trim()) {
            errors.push('La date est obligatoire');
            isValid = false;
        }
        if (!$('input[name="reason"]').val().trim()) {
            errors.push('Le motif est obligatoire');
            isValid = false;
        }

        // Vérification des articles
        let hasItems = false;
        $('.repeater-item').each(function() {
            const $item = $(this);
            const category = $item.find('.item-category').val();
            const itemId = $item.find('.item-select').val();
            const quantity = $item.find('.quantity').val();

            if (category || itemId || quantity) {
                hasItems = true;
                if (!category) {
                    errors.push('La catégorie est obligatoire pour tous les articles');
                    isValid = false;
                }
                if (!itemId) {
                    errors.push('L\'article est obligatoire pour tous les articles');
                    isValid = false;
                }
                if (!quantity || quantity <= 0) {
                    errors.push('La quantité doit être supérieure à 0 pour tous les articles');
                    isValid = false;
                }
            }
        });

        if (!hasItems) {
            errors.push('Vous devez ajouter au moins un article');
            isValid = false;
        }

        return { isValid, errors };
    }

    // Chargement initial des articles pour la première ligne
    var firstCategory = $('.repeater-item:first').find('.item-category').val();
    if (firstCategory) {
        populateItem($('.repeater-item:first').find('.item-select'), firstCategory);
    }

    // Fonction pour peupler les articles selon la catégorie
    function populateItem(itemSelect, categoryId) {
        if (categoryId) {
            var container = $(itemSelect).closest('.repeater-item');
            container.find('.item-select').html("<option value=''>Chargement...</option>");
            
            $.ajax({
                type: "GET",
                url: base_url + "admin/itemstock/getItemByCategory",
                data: {'item_category_id': categoryId},
                dataType: "json",
                success: function (data) {
                    var options = "<option value=''>Sélectionner</option>";
                    $.each(data, function (i, obj) {
                        options += "<option value='" + obj.id + "' data-unit='" + (obj.unit || '') + "' data-price='" + (obj.unit_price || 0) + "'>" + obj.name + "</option>";
                    });
                    container.find('.item-select').html(options);
                },
                error: function(xhr, status, error) {
                    console.error('Erreur lors du chargement des articles:', error);
                    container.find('.item-select').html("<option value=''>Erreur de chargement</option>");
                }
            });
        }
    }

    // Calcul du total pour un article
    function calculateItemTotal(container) {
        var quantity = parseFloat(container.find('.quantity').val()) || 0;
        var price = parseFloat(container.find('.price').val()) || 0;
        var total = quantity * price;
        container.find('.total-price').text(total.toFixed(2));
        calculateGrandTotal();
    }

    // Calcul du total général
    function calculateGrandTotal() {
        var grandTotal = 0;
        $('.repeater-item').each(function() {
            var totalText = $(this).find('.total-price').text();
            grandTotal += parseFloat(totalText) || 0;
        });
        $('#grand-total').text(grandTotal.toFixed(2));
        $('#grandTotal').val(grandTotal.toFixed(2));
    }

    // Ajout d'un nouvel article
    $('#add-item').click(function() {
        var newItem = $('.repeater-item').first().clone();
        newItem.find('select').val('');
        newItem.find('input').val('');
        newItem.find('.total-price').text('0.00');
        $('#items-container').append(newItem);
    });

    // Suppression d'un article
    $(document).on('click', '.remove-item', function() {
        if ($('.repeater-item').length > 1) {
            $(this).closest('.repeater-item').remove();
            calculateGrandTotal();
        } else {
            Swal.fire('Attention', 'Vous devez avoir au moins un article.', 'warning');
        }
    });

    // Changement de catégorie d'article
    $(document).on('change', '.item-category', function() {
        var itemSelect = $(this).closest('.repeater-item').find('.item-select');
        populateItem(itemSelect, $(this).val());
    });

    // Changement d'article sélectionné
    $(document).on('change', '.item-select', function() {
        var selectedOption = $(this).find('option:selected');
        var container = $(this).closest('.repeater-item');
        
        container.find('.unit').val(selectedOption.data('unit') || '');
        container.find('.price').val(selectedOption.data('price') || '');
        
        calculateItemTotal(container);
    });

    // Changement de quantité ou prix
    $(document).on('input', '.quantity, .price', function() {
        var container = $(this).closest('.repeater-item');
        calculateItemTotal(container);
    });

    // Soumission du formulaire
    $(document).on('click', "#"+dtButtons.set, function(e) {
        e.preventDefault();
        
        var $form = $("#"+formID);
        var $submitBtn = $(this);

        // Validation du formulaire
        const { isValid, errors } = validateForm();
        if (!isValid) {
            Swal.fire('Erreur', errors.join('<br>'), 'error');
            return;
        }

        Swal.fire({
            title: 'Confirmation',
            text: "Voulez-vous enregistrer cette entrée de stock, il n'est possible d'annuler ou de modifier l'enregistrement ?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Oui, enregistrer',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Enregistrement...');

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire('Succès', response.message, 'success').then(() => {
                                window.location.href = base_url + 'admin/stockentry';
                            });
                        } else {
                            Swal.fire('Erreur', response.message, 'error');
                            $submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Enregistrer');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Erreur AJAX:', error);
                        Swal.fire('Erreur', 'Une erreur est survenue lors de l\'enregistrement. Veuillez réessayer.', 'error');
                        $submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Enregistrer');
                    }
                });
            }
        });
    });
});