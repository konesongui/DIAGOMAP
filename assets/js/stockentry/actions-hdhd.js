"use strict";
// Class definition

// Set all the required variables for the following methods
var dtID = 'stockEntryDatatable',
  formID = 'stockEntryForm',
  repeater = {
      block: 'stockEntryToolRepeater',
      buttons: {
          add: 'repeaterAddBTN',
          delete: 'repeaterDeleteBTN',
      }
  },
  dtButtons = {
      set: 'stockEntryForm',
  },
  remoteAJAXFunctions = {
      loadData: 'admin/stockentry/data',
      setForm: 'stockEntry/set',
      get: 'purchaseRequestTool/get',
  };



$(document).ready(function() {
    // Initialisation du datepicker
    $('.date').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayHighlight: true
    });

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
                }
            });
        }
    }

    // Fonction pour vérifier la quantité disponible
    // function checkAvailableQuantity(itemSelect) {
    //     var itemId = $(itemSelect).val();
    //     var container = $(itemSelect).closest('.repeater-item');
        
    //     if (itemId) {
    //         $.ajax({
    //             type: "GET",
    //             url: base_url + "admin/item/getAvailQuantity",
    //             data: {'item_id': itemId},
    //             dataType: "json",
    //             success: function (data) {
    //                 container.find('.available-qty').text(data.available || 0);
    //                 container.find('.availability').show();
    //             }
    //         });
    //     } else {
    //         container.find('.availability').hide();
    //     }
    // }

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
    }

    // Ajout d'un nouvel article
    $('#add-item').click(function() {
        var newItem = $('.repeater-item').first().clone();
        newItem.find('select').val('');
        newItem.find('input').val('');
        newItem.find('.availability').hide();
        newItem.find('.total-price').text('0.00');
        $('#items-container').append(newItem);
    });

    // Suppression d'un article
    $(document).on('click', '.remove-item', function() {
        if ($('.repeater-item').length > 1) {
            $(this).closest('.repeater-item').remove();
            calculateGrandTotal();
        } else {
            alert("Vous devez avoir au moins un article.");
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
        
        // checkAvailableQuantity(this);
        calculateItemTotal(container);
    });

    // Changement de quantité ou prix
    $(document).on('input', '.quantity, .price', function() {
        var container = $(this).closest('.repeater-item');
        calculateItemTotal(container);
    });

    // Soumission du formulaire
    $("#issueitem").submit(function(e) {
        e.preventDefault();
        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Enregistrement...');
        
        // Validation des quantités disponibles
        var valid = true;
        $('.repeater-item').each(function() {
            var requested = parseInt($(this).find('.quantity').val()) || 0;
            var available = parseInt($(this).find('.available-qty').text()) || 0;
            if (requested > available) {
                alert("La quantité demandée dépasse le stock disponible pour un article");
                valid = false;
                return false;
            }
        });
        
        if (!valid) {
            $submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Enregistrer');
            return;
        }
        
        $.ajax({
            url: $form.attr("action"),
            type: "POST",
            data: $form.serialize(),
            dataType: 'json',
            success: function(data) {
                if (data.status == "fail") {
                    alert("Erreur: " + data.message);
                } else {
                    alert("Succès: " + data.message);
                    $form[0].reset();
                    $('.repeater-item').not(':first').remove();
                    $('.repeater-item').first().find('select').val('');
                    $('.repeater-item').first().find('input').val('');
                    $('.availability').hide();
                    $('#grand-total').text('0.00');
                }
                $submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Enregistrer');
            },
            error: function() {
                alert("Une erreur est survenue");
                $submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Enregistrer');
            }
        });
    });
});