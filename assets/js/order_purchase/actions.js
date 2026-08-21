"use strict";
// Class definition

// Set all the required variables for the following methods
var dtID = 'orderDatatable',
  formID = 'orderForm',
  formEditID = 'orderEditForm',
  dtButtons = {
      set: 'submitBtn',
      edit: 'submitEditBtn',
  },
  remoteAJAXFunctions = {
      loadData: 'admin/orderformitem_purchase/data',
      add: 'admin/orderformitem_purchase/add',
      edit: 'admin/orderformitem_purchase/update',
  };

// Taux de TVA
const TVA_RATE = 0.18;

$(document).ready(function() {
    // Initialisation du datepicker
    $('.date').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayHighlight: true
    });

    // Chargement initial des articles pour toutes les lignes existantes
    $('.repeater-item').each(function() {
        var $item = $(this);
        var categoryId = $item.find('.item-category').val();
        var itemId = $item.find('.item-select').val();
        
        if (categoryId) {
            // Attendre que le DOM soit complètement chargé
            setTimeout(function() {
                populateItem($item.find('.item-select'), categoryId, itemId);
            }, 100);
        }
    });

    // Validation du formulaire avant soumission
    function validateForm() {
        let isValid = true;
        let errors = [];

        // Vérification des champs obligatoires
        if (!$('input[name="designation"]').val()?.trim()) {
            errors.push('La désignation est obligatoire');
            isValid = false;
        }

        if (!$('input[name="order_date"]').val()?.trim()) {
            errors.push('La date est obligatoire');
            isValid = false;
        }

        if (!$('input[name="valid_until"]').val()?.trim()) {
            errors.push('La date de validité est obligatoire');
            isValid = false;
        }

        if (!$('select[name="customer"]').val()) {
            errors.push('Le client est obligatoire');
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

    // Fonction pour peupler les articles selon la catégorie
    function populateItem(itemSelect, categoryId, selectedItemId = null) {
        if (categoryId) {
            var container = $(itemSelect).closest('.repeater-item');
            container.find('.item-select').html("<option value=''>Chargement...</option>");
            
            $.ajax({
                type: "GET",
                url: base_url + "admin/quoteitem/getItemByCategory",
                data: {'item_category_id': categoryId},
                dataType: "json",
                success: function (data) {
                    var options = "<option value=''>Sélectionner</option>";
                    $.each(data, function (i, obj) {
                        var selected = (selectedItemId && selectedItemId == obj.id) ? 'selected' : '';
                        options += "<option data-available='" + obj.current_quantity + "' value='" + obj.id + "' data-unit='" + (obj.unit || '') + "' data-price='" + (obj.weighted_avg_price || 0) + "' " + selected + ">" + obj.name + "</option>";
                    });
                    container.find('.item-select').html(options);
                    
                    // Si un article est sélectionné, déclencher l'événement change
                    if (selectedItemId) {
                        container.find('.item-select').trigger('change');
                    }
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

        // Total HT
        $('#total_ht').text(grandTotal.toFixed(2));
        $('#totalHT').val(grandTotal.toFixed(2));

        // Calcul TVA si applicable
        if ($('#apply_tva').is(':checked')) {
            var tva = grandTotal * TVA_RATE;
            var totalTTC = grandTotal + tva;
            
            $('#tva_amount').text(tva.toFixed(2));
            $('#tvaAmount').val(tva.toFixed(2));
            $('#total_ttc').text(totalTTC.toFixed(2));
            $('#totalTTC').val(totalTTC.toFixed(2));
            $('.tva-row').show();
            $('.ttc-row').show();
        } else {
            $('#tva_amount').text('0.00');
            $('#tvaAmount').val('0.00');
            $('#total_ttc').text(grandTotal.toFixed(2));
            $('#totalTTC').val(grandTotal.toFixed(2));
            $('.tva-row').hide();
            $('.ttc-row').hide();
        }
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
        var selectedItemId = itemSelect.val();
        populateItem(itemSelect, $(this).val(), selectedItemId);
    });

    // Changement d'article sélectionné
    $(document).on('change', '.item-select', function() {
        var selectedOption = $(this).find('option:selected');
        var container = $(this).closest('.repeater-item');
        var availableQty = selectedOption.data('available') || 0;
        
        container.find('.unit').val(selectedOption.data('unit') || '');
        container.find('.price').val(selectedOption.data('price') || '');
        container.find('.available-qty').text(availableQty);
        
        // Mettre à jour la quantité maximale autorisée
        container.find('.quantity').attr('max', availableQty);
        
        calculateItemTotal(container);
    });

    // Validation de la quantité saisie
    $(document).on('input', '.quantity', function() {
        var container = $(this).closest('.repeater-item');
        var availableQty = parseInt(container.find('.available-qty').text()) || 0;
        var quantity = parseInt($(this).val()) || 0;
        
        // if (quantity > availableQty) {
        //     Swal.fire({
        //         title: 'Attention',
        //         text: 'La quantité saisie (' + quantity + ') dépasse la quantité disponible (' + availableQty + ')',
        //         icon: 'warning'
        //     });
        //     $(this).val(availableQty);
        //     quantity = availableQty;
        // }
        
        calculateItemTotal(container);
    });

    // Changement de quantité ou prix
    $(document).on('input', '.quantity, .price', function() {
        var container = $(this).closest('.repeater-item');
        calculateItemTotal(container);
    });

    // Gestion du changement de l'option TVA
    $(document).on('change', '#apply_tva', function() {
        calculateGrandTotal();
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
            text: "Voulez-vous enregistrer cette commande ?",
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
                                window.location.href = base_url + 'admin/orderformitem_purchase';
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

    // Soumission du formulaire de modification
    $(document).on('click', "#"+dtButtons.edit, function(e) {
        e.preventDefault();
        
        var $form = $("#"+formEditID);
        var $submitBtn = $(this);
        
        // Validation du formulaire
        const { isValid, errors } = validateForm();
        if (!isValid) {
            Swal.fire('Erreur', errors.join('<br>'), 'error');
            return;
        }

        // Désactiver le bouton pendant la soumission
        $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Enregistrement...');

        // Envoyer la requête AJAX
        $.ajax({
            url: baseurl + 'admin/orderformitem_purchase/update',
            type: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        title: 'Succès',
                        text: response.message,
                        icon: 'success'
                    }).then(() => {
                        window.location.href = base_url + 'admin/orderformitem_purchase';
                    });
                } else {
                    let errorMessage = response.message || 'Une erreur est survenue';
                    if (response.error) {
                        errorMessage += '<br>Détails :<br>' + Object.entries(response.error)
                            .map(([key, value]) => `${key}: ${value}`)
                            .join('<br>');
                    }
                    Swal.fire('Erreur', errorMessage, 'error');
                    $submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Enregistrer les modifications');
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur AJAX:', {
                    status: status,
                    error: error,
                    response: xhr.responseText
                });
                
                let errorMessage = 'Une erreur est survenue lors de l\'enregistrement.';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    }
                    if (response.error) {
                        errorMessage += '<br>Détails :<br>' + Object.entries(response.error)
                            .map(([key, value]) => `${key}: ${value}`)
                            .join('<br>');
                    }
                } catch (e) {
                    errorMessage += '<br>Erreur technique : ' + error;
                }
                
                Swal.fire('Erreur', errorMessage, 'error');
                $submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Enregistrer les modifications');
            }
        });
    });
});