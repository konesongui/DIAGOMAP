"use strict";
// Class definition

// Set all the required variables for the following methods
var dtID = 'stockEntryDatatable',
    formID = 'stockEntryForm',
    dtButtons = {
        set: 'submitBtn',
    },
    remoteAJAXFunctions = {
        loadData: 'admin/stockentry/data',
        add: 'admin/stockentry/add',
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
        if (!$('input[name="designation"]').val().trim()) {
            errors.push('La désignation est obligatoire');
            isValid = false;
        }

        if (!$('input[name="issue_date"]').val().trim()) {
            errors.push('La date est obligatoire');
            isValid = false;
        }

        // Vérification des articles
        let hasItems = false;
        $('.repeater-item').each(function() {
            const $item = $(this);
            const category = $item.find('.item-category').val();
            const itemName = $item.find('.item-name').val();
            const quantity = $item.find('.quantity').val();

            if (category || itemName || quantity) {
                hasItems = true;
                if (!category) {
                    errors.push('La catégorie est obligatoire pour tous les articles');
                    isValid = false;
                }
                if (!itemName) {
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

// Fonction pour charger les articles d'une catégorie
    function loadItemsByCategory(categoryName, $datalist, index) {
        if (categoryName) {
            $.ajax({
                type: "POST",
                url: base_url + "admin/stockentry/get_items_by_category_name", // Correction de l'URL
                data: {'category_name': categoryName},
                dataType: "json",
                success: function (data) {
                    var options = '';
                    if (data.length > 0) {
                        $.each(data, function (i, obj) {
                            options += '<option value="' + obj.name + '" data-unit="' + (obj.unit || '') + '" data-price="' + (obj.unit_price || 0) + '" data-id="' + obj.id + '">';
                        });
                    } else {
                        options = '<option value="">Aucun article trouvé. Vous pouvez en créer un nouveau en tapant directement.</option>';
                    }
                    $datalist.html(options);
                },
                error: function(xhr, status, error) {
                    console.error('Erreur lors du chargement des articles:', error);
                    console.log('URL appelée:', base_url + "admin/stockentry/get_items_by_category_name");
                    $datalist.html('<option value="">Erreur de chargement</option>');
                }
            });
        } else {
            $datalist.html('<option value="">Sélectionnez d\'abord une catégorie</option>');
        }
    }

// Fonction pour récupérer les détails d'un article
    function getItemDetails(itemName, categoryName, $row) {
        $.ajax({
            type: "POST",
            url: base_url + "admin/stockentry/get_item_details", // Correction de l'URL
            data: {
                'item_name': itemName,
                'category_name': categoryName
            },
            dataType: "json",
            success: function (response) {
                if (response.status === 'success') {
                    $row.find('.unit').val(response.unit || '');
                    $row.find('.price').val(response.price || '');
                    $row.find('.available-qty').text(response.quantity || 0);

                    // Stocker l'ID de l'article pour la soumission
                    $row.find('.item-name').data('item-id', response.item_id);

                    calculateItemTotal($row);
                } else {
                    // Article nouveau, laisser l'utilisateur saisir les infos
                    $row.find('.unit').val('');
                    $row.find('.price').val('');
                    $row.find('.available-qty').text('0');
                    $row.find('.item-name').data('item-id', 'new');
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur lors de la récupération des détails de l\'article:', error);
                console.log('URL appelée:', base_url + "admin/stockentry/get_item_details");
            }
        });
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
        var index = $('.repeater-item').length;
        var newItem = $('.repeater-item').first().clone();
        newItem.find('input').val('');
        newItem.find('.total-price').text('0.00');
        newItem.find('.available-qty').text('0');
        newItem.find('.item-name').attr('list', 'item-list-' + index);
        newItem.find('.item-name').data('item-id', '');

        // Créer un nouveau datalist pour cette ligne
        var newDatalist = $('<datalist>', {
            id: 'item-list-' + index,
            class: 'item-datalist'
        }).html('<option value="">Sélectionnez d\'abord une catégorie</option>');

        newItem.find('.item-name').after(newDatalist);
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

    // Changement de catégorie
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
        $row.find('.price').val('');
        $row.find('.available-qty').text('0');
        calculateItemTotal($row);
    });

    // Changement d'article
    $(document).on('change', '.item-name', function() {
        var $row = $(this).closest('.repeater-item');
        var itemName = $(this).val();
        var categoryName = $row.find('.item-category').val();

        if (itemName && categoryName) {
            getItemDetails(itemName, categoryName, $row);
        }
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
            text: "Voulez-vous enregistrer cette entrée de stock ?",
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
                        Swal.fire('Erreur', 'Une erreur est survenue lors de l\'enregistrement.', 'error');
                        $submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Enregistrer');
                    }
                });
            }
        });
    });
});