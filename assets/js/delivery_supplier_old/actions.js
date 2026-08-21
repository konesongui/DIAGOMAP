"use strict";
// Class definition

// Set all the required variables for the following methods
var dtID = 'deliveryDatatable',
    formID = 'deliveryForm',
    formEditID = 'deliveryEditForm',
    formValidateID = 'deliveryValidateForm',
    dtButtons = {
        set: 'submitBtn',
        edit: 'submitEditBtn',
        validate: 'validateBtn'
    },
    remoteAJAXFunctions = {
        loadData: 'admin/deliveryitem_supplier/data',
        add: 'admin/deliveryitem_supplier/add',
        edit: 'admin/deliveryitem_supplier/update',
        validate: 'admin/deliveryitem_supplier/validate'
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
        var categoryName = $item.find('.item-category').val();
        var itemName = $item.find('.item-name').val();

        if (categoryName && itemName) {
            // Les valeurs sont déjà présentes dans les champs de texte
            var container = $item;
            container.find('.unit').val($item.find('.unit').val() || '');
            container.find('.price').val($item.find('.price').val() || '');
            container.find('.available-qty').text($item.find('.available-qty').text() || '0');

            // Déclencher le calcul du total
            calculateItemTotal(container);
        }
    });

    // Validation du formulaire avant soumission
    function validateForm() {
        let isValid = true;
        let errors = [];

        // Vérification des champs obligatoires
        if (!$('input[name="delivery_date"]').val()?.trim()) {
            errors.push('La date est obligatoire');
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
            const itemName = $item.find('.item-name').val();
            const quantity = parseFloat($item.find('.quantity').val()) || 0;

            if (category || itemName || quantity > 0) {
                hasItems = true;
                if (!category) {
                    errors.push('La catégorie est obligatoire pour tous les articles');
                    isValid = false;
                }
                if (!itemName) {
                    errors.push('L\'article est obligatoire pour tous les articles');
                    isValid = false;
                }
                if (quantity <= 0) {
                    $item.find('.quantity').val(1);
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

            console.log('Chargement des articles pour la catégorie:', categoryId);

            $.ajax({
                type: "GET",
                url: base_url + "admin/deliveryitem_supplier/getItemByCategory",
                data: {'item_category_id': categoryId},
                dataType: "json",
                success: function (data) {
                    console.log('Réponse reçue:', data);

                    if (!Array.isArray(data)) {
                        console.error('La réponse n\'est pas un tableau:', data);
                        container.find('.item-select').html("<option value=''>Erreur de format</option>");
                        return;
                    }

                    var options = "<option value=''>Sélectionner</option>";
                    $.each(data, function (i, obj) {
                        var selected = (selectedItemId && selectedItemId == obj.id) ? 'selected' : '';

                        console.log(obj);
                        options += "<option data-available='" + (obj.current_quantity || 0) + "' value='" + obj.id + "' data-unit='" + (obj.unit || '') + "' data-price='" + (obj.weighted_avg_price || 0) + "' " + selected + ">" + obj.name + "</option>";
                    });
                    container.find('.item-select').html(options);

                    // Si un article est sélectionné, déclencher l'événement change
                    if (selectedItemId) {
                        container.find('.item-select').trigger('change');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erreur lors du chargement des articles:', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                    container.find('.item-select').html("<option value=''>Erreur de chargement</option>");
                }
            });
        }
    }

    // Gestion de la saisie de catégorie
    $(document).on('input', '.item-category', function() {
        var input = $(this);
        var value = input.val();
        var container = input.closest('.repeater-item');
        var itemNameInput = container.find('.item-name');

        // Vider la liste des produits
        itemNameInput.val('');
        container.find('.unit').val('');
        container.find('.price').val('');
        container.find('.available-qty').text('');
        container.find('.quantity').removeAttr('max');

        if (value) {
            // Vérifier si la catégorie existe
            $.ajax({
                url: base_url + 'admin/deliveryitem_supplier/checkCategory',
                type: 'POST',
                data: { category_name: value },
                dataType: 'json',
                success: function(response) {
                    if (response.exists) {
                        // Si la catégorie existe, charger les produits de cette catégorie
                        $.ajax({
                            url: base_url + 'admin/deliveryitem_supplier/getItemsByCategory',
                            type: 'POST',
                            data: { category_name: value },
                            dataType: 'json',
                            success: function(items) {
                                // Mettre à jour le datalist des produits
                                var datalist = $('#item-list');
                                datalist.empty();

                                items.forEach(function(item) {
                                    datalist.append(
                                        $('<option>', {
                                            value: item.name,
                                            'data-id': item.id,
                                            'data-stock': item.quantity,
                                            'data-unit': item.unit,
                                            'data-price': item.weighted_avg_price
                                        })
                                    );
                                });
                            }
                        });
                    } else {
                        // Si la catégorie n'existe pas, vider le datalist des produits
                        $('#item-list').empty();
                    }
                }
            });
        }
    });

    // Gestion de la sélection d'un article
    $(document).on('input', '.item-name', function() {
        var input = $(this);
        var value = input.val();
        var datalist = $('#item-list option');
        var container = input.closest('.repeater-item');

        // Recherche de l'article correspondant
        var selectedItem = datalist.filter(function() {
            return $(this).val() === value;
        });

        if (selectedItem.length > 0) {
            // Article existant trouvé
            var availableQty = selectedItem.data('stock') || 0;
            var unit = selectedItem.data('unit') || '';
            var price = selectedItem.data('price') || 0;

            // Mise à jour des champs avec les valeurs de l'article
            container.find('.unit').val(unit);
            container.find('.price').val(price);
            container.find('.available-qty').text(availableQty);
            container.find('.quantity').attr('max', availableQty);

            // Déclencher le calcul du total
            calculateItemTotal(container);
        } else {
            // Nouvel article
            container.find('.unit').val('');
            container.find('.price').val('');
            container.find('.available-qty').text('Nouveau produit');
            container.find('.quantity').removeAttr('max');
            container.find('.total-price').text('0.00');
        }
    });

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

        // Nettoyer les valeurs
        newItem.find('select').val('');
        newItem.find('input').val('');
        newItem.find('.total-price').text('0.00');

        // Supprimer les IDs dupliqués s'ils existent
        newItem.find('[id]').removeAttr('id');

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
                                window.location.href = base_url + 'admin/deliveryitem_supplier';
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
            url: base_url + 'admin/deliveryitem_supplier/update',
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
                        window.location.href = base_url + 'admin/deliveryitem_supplier';
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

    // Soumission du formulaire de validation
    $(document).on('click', "#"+dtButtons.validate, function(e) {
        e.preventDefault();

        var $form = $("#"+formValidateID);
        var $submitBtn = $(this);

        // Validation du formulaire
        const { isValid, errors } = validateForm();
        if (!isValid) {
            Swal.fire('Erreur', errors.join('<br>'), 'error');
            return;
        }

        // Désactiver le bouton pendant la soumission
        $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Validation...');

        // Envoyer la requête AJAX
        $.ajax({
            url: base_url + 'admin/deliveryitem_supplier/validate',
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
                        window.location.href = base_url + 'admin/deliveryitem_supplier';
                    });
                } else {
                    let errorMessage = response.message || 'Une erreur est survenue';
                    if (response.error) {
                        errorMessage += '<br>Détails :<br>' + Object.entries(response.error)
                            .map(([key, value]) => `${key}: ${value}`)
                            .join('<br>');
                    }
                    Swal.fire('Erreur', errorMessage, 'error');
                    $submitBtn.prop('disabled', false).html('<i class="fa fa-check"></i> Valider');
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur AJAX:', {
                    status: status,
                    error: error,
                    response: xhr.responseText
                });

                let errorMessage = 'Une erreur est survenue lors de la validation.';
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
                $submitBtn.prop('disabled', false).html('<i class="fa fa-check"></i> Valider');
            }
        });
    });

    // Validation de la quantité livrée
    $(document).on('input', '.quantity_delivered', function() {
        var quantity = parseInt($(this).closest('.repeater-item').find('.quantity').val());
        var delivered = parseInt($(this).val());

        if (delivered > quantity) {
            toastr.warning('La quantité livrée ne peut pas dépasser la quantité commandée (' + quantity + ')');
            $(this).val(quantity);
        }
    });

});