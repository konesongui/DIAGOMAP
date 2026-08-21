"use strict";

// Set all the required variables for the following methods
var dtID = 'itemStockDatatable',
    remoteAJAXFunctions = {
        loadData: 'admin/itemstock/data',
    };

$(document).ready(function() {

    // Désactiver l'initialisation multiple
    if ($.fn.DataTable.isDataTable('.' + dtID)) {
        $('.' + dtID).DataTable().destroy();
    }

    // Initialisation de DataTables avec les nouvelles colonnes
    var itemStockTable = $('.'+ dtID).DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": baseurl + remoteAJAXFunctions.loadData,
            "type": "POST",
            "data": function(d) {
                // Ajouter le paramètre pour le filtre stock nul
                d.show_zero_stock = $('#showZeroStock').is(':checked') ? 1 : 0;
                return d;
            },
            "dataSrc": function(json) {
                // Calculer le total après chargement
                setTimeout(calculateTotal, 100);
                return json.data;
            }
        },
        "columns": [
            { "data": "article" },
            { "data": "category" },
            { "data": "unit" },
            {
                "data": "quantite_initiale",
                "className": "text-right",
                "render": function(data, type, row) {
                    if (type === 'display' || type === 'filter') {
                        return data || '0';
                    }
                    return data;
                }
            },
            {
                "data": "quantite_sortie",
                "className": "text-right",
                "render": function(data, type, row) {
                    if (type === 'display' || type === 'filter') {
                        return data || '0';
                    }
                    return data;
                }
            },
            {
                "data": "quantite_disponible",
                "className": "text-right",
                "render": function(data, type, row) {
                    if (type === 'display' || type === 'filter') {
                        return data || '0';
                    }
                    return data;
                }
            },
            {
                "data": "cout_moyen",
                "className": "text-right",
                "render": function(data, type, row) {
                    if (type === 'display' || type === 'filter') {
                        return data || '0,00';
                    }
                    return data;
                }
            }
        ],
        "order": [[0, "asc"]], // Tri par nom d'article
        "language": {
            "url": baseurl + "assets/js/french.json"
        },
        "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
            "<'row'<'col-sm-12'tr>>" +
            "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
        "pageLength": 25,
        "drawCallback": function(settings) {
            // Appliquer une classe CSS aux lignes avec stock = 0
            $('.' + dtID + ' tbody tr').each(function() {
                var qtyCell = $(this).find('td:eq(5)'); // Colonne quantite_disponible
                var qtyText = qtyCell.text().trim().replace(/\s/g, '').replace(',', '.');
                var qty = parseFloat(qtyText) || 0;

                if (qty === 0) {
                    $(this).addClass('zero-stock');
                } else {
                    $(this).removeClass('zero-stock');
                }
            });

            // Calculer le total de la quantité disponible
            calculateTotal();
        },
        "initComplete": function(settings, json) {
            // Initialiser le filtre "Afficher stock nul" après le chargement
            $('#showZeroStock').on('change', function() {
                itemStockTable.ajax.reload();
            });

            // Ajouter les boutons d'exportation si disponibles
            if (typeof StockExporter !== 'undefined' && !window.stockExporterInitialized) {
                window.stockExporter = new StockExporter(itemStockTable);
                window.stockExporterInitialized = true;
            }
        }
    });

    // Fonction pour calculer le total des quantités disponibles
    function calculateTotal() {
        var total = 0;
        $('.' + dtID + ' tbody tr').each(function() {
            var qtyCell = $(this).find('td:eq(5)'); // Colonne quantite_disponible
            if (qtyCell.length) {
                var qtyText = qtyCell.text().trim().replace(/\s/g, '').replace(',', '.');
                var qty = parseFloat(qtyText) || 0;
                total += qty;
            }
        });

        $('#totalQuantity').text(total.toLocaleString('fr-FR', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }));
    }

    // Exposer les fonctions globalement
    window.itemStockTable = itemStockTable;
    window.calculateTotal = calculateTotal;

    // Supprimer la deuxième initialisation qui est en double
    // La partie suivante est un doublon et doit être supprimée ou commentée
    /*
    $(document).ready(function() {
        // Configuration de base de DataTable
        var itemStockDatatable = $('.itemStockDatatable').DataTable({
            ...
        });
        ...
    });
    */

    // Note: La partie "loadDemoData" n'est pas nécessaire si vous utilisez le serveur
    // Elle peut être gardée pour le développement mais commentée en production

    // Fonction pour charger les totaux de bénéfice
    function loadProfitTotals() {
        $.ajax({
            url: baseurl + 'admin/itemstock/get_profit_totals',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var data = response.data;

                    $('#total-benefice').text(formatNumber(data.total_benefice_potentiel) + ' FCFA');
                    $('#total-valeur-achat').text(formatNumber(data.total_valeur_achat) + ' FCFA');
                    $('#total-valeur-vente').text(formatNumber(data.total_valeur_vente) + ' FCFA');
                    $('#articles-rupture').text(data.articles_rupture);
                    $('#marge-moyenne').text(data.marge_moyenne.toFixed(1));

                    // Afficher les cartes
                    $('#profit-cards').fadeIn();
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur chargement totaux:', error);
            }
        });
    }

// Fonction pour charger les bénéfices par catégorie
    function loadProfitByCategory() {
        $.ajax({
            url: baseurl + 'admin/itemstock/get_profit_by_category',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success' && response.data.length > 0) {
                    var html = '';
                    $.each(response.data, function(index, cat) {
                        var marge = cat.valeur_vente > 0 ? (cat.benefice_potentiel / cat.valeur_vente * 100) : 0;
                        html += '<tr>';
                        html += '<td>' + cat.category_name + '</td>';
                        html += '<td class="text-right">' + cat.nb_articles + '</td>';
                        html += '<td class="text-right">' + formatNumber(cat.valeur_achat) + '</td>';
                        html += '<td class="text-right">' + formatNumber(cat.valeur_vente) + '</td>';
                        html += '<td class="text-right"><strong class="text-success">' + formatNumber(cat.benefice_potentiel) + '</strong></td>';
                        html += '<td class="text-right">' + marge.toFixed(1) + '%</td>';
                        html += '</tr>';
                    });
                    $('#category-profit-table tbody').html(html);
                    $('#profit-category-table').fadeIn();
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur chargement catégories:', error);
            }
        });
    }

// Fonction pour formater les nombres
    function formatNumber(number) {
        return new Intl.NumberFormat('fr-FR', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(number);
    }

// Modifier l'initialisation de DataTables pour ajouter les nouvelles colonnes
    $(document).ready(function() {
        // Charger les données de bénéfice
        loadProfitTotals();
        loadProfitByCategory();

        // Désactiver l'initialisation multiple
        if ($.fn.DataTable.isDataTable('.' + dtID)) {
            $('.' + dtID).DataTable().destroy();
        }

        // Initialisation de DataTables avec les nouvelles colonnes
        var itemStockTable = $('.'+ dtID).DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": baseurl + 'admin/itemstock/data_with_profit', // Utiliser la nouvelle méthode
                "type": "POST",
                "data": function(d) {
                    d.show_zero_stock = $('#showZeroStock').is(':checked') ? 1 : 0;
                    return d;
                },
                "dataSrc": function(json) {
                    setTimeout(calculateTotal, 100);
                    return json.data;
                }
            },
            "columns": [
                { "data": "article" },
                { "data": "category" },
                { "data": "unit" },
                {
                    "data": "quantite_disponible",
                    "className": "text-right"
                },
                {
                    "data": "prix_achat",
                    "className": "text-right"
                },
                {
                    "data": "prix_vente",
                    "className": "text-right"
                },
                {
                    "data": "marge_unitaire",
                    "className": "text-right"
                },
                {
                    "data": "benefice_potentiel",
                    "className": "text-right",
                    "render": function(data, type, row) {
                        if (type === 'display') {
                            return '<strong class="text-success">' + data + '</strong>';
                        }
                        return data;
                    }
                }
            ],
            "order": [[0, "asc"]],
            "language": {
                "url": baseurl + "assets/js/french.json"
            },
            "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i><'col-sm-7'p>>",
            "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
            "pageLength": 25,
            "drawCallback": function(settings) {
                // Appliquer une classe CSS aux lignes avec stock = 0
                $('.' + dtID + ' tbody tr').each(function() {
                    var qtyCell = $(this).find('td:eq(3)');
                    var qtyText = qtyCell.text().trim().replace(/\s/g, '').replace(',', '.');
                    var qty = parseFloat(qtyText) || 0;

                    if (qty === 0) {
                        $(this).addClass('zero-stock');
                    } else {
                        $(this).removeClass('zero-stock');
                    }
                });

                calculateTotal();
            },
            "initComplete": function(settings, json) {
                $('#showZeroStock').on('change', function() {
                    itemStockTable.ajax.reload();
                });
            }
        });

        // Fonction pour calculer le total des quantités disponibles
        function calculateTotal() {
            var total = 0;
            $('.' + dtID + ' tbody tr').each(function() {
                var qtyCell = $(this).find('td:eq(3)');
                if (qtyCell.length) {
                    var qtyText = qtyCell.text().trim().replace(/\s/g, '').replace(',', '.');
                    var qty = parseFloat(qtyText) || 0;
                    total += qty;
                }
            });

            $('#totalQuantity').text(total.toLocaleString('fr-FR', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }));
        }

        // Exposer les fonctions globalement
        window.itemStockTable = itemStockTable;
        window.calculateTotal = calculateTotal;
    });
});