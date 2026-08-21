"use strict";
// Class definition

// Set all the required variables for the following methods
var dtID = 'itemStockDatatable',
  remoteAJAXFunctions = {
      loadData: 'admin/itemstock/data',
  };



$(document).ready(function() {
    // Initialisation de DataTables
    var itemStockTable = $('.'+ dtID).DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": baseurl + remoteAJAXFunctions.loadData,
            "type": "POST"
        },
        "columns": [
            { "data": "article" },
            { "data": "category" },
            { "data": "unit" },
            { "data": "cout_moyen" },
            { "data": "quantity_available" },
           
        ],
        "order": [[2, "desc"]], // Tri par date décroissante
        "language": {
            "url": baseurl + "assets/js/french.json"
        },
        "dom": "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
               "<'row'<'col-sm-12'tr>>" +
               "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
        "pageLength": 25
    });

    $(document).ready(function() {
        // Configuration de base de DataTable
        var itemStockDatatable = $('.itemStockDatatable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": baseUrl + "itemstock/getStockData",
                "type": "POST"
            },
            "columns": [
                { "data": "article" },
                { "data": "categorie" },
                { "data": "unite" },
                {
                    "data": "cout_moyen",
                    "render": function(data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            return parseFloat(data).toLocaleString('fr-FR', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }) + ' FCFA';
                        }
                        return data;
                    }
                },
                {
                    "data": "quantite",
                    "render": function(data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            return parseFloat(data).toLocaleString('fr-FR');
                        }
                        return data;
                    }
                }
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
            },
            "order": [[0, "asc"]],
            "pageLength": 25,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tous"]],
            "drawCallback": function(settings) {
                // Calculer le total de la quantité
                var api = this.api();
                var total = api.column(4, {page: 'current'}).data().reduce(function(a, b) {
                    return parseFloat(a) + parseFloat(b);
                }, 0);

                // Mettre à jour le footer
                $('#totalQuantity').text(total.toLocaleString('fr-FR'));

                // Initialiser l'exporteur après le chargement des données
                if (typeof StockExporter !== 'undefined' && !window.stockExporterInitialized) {
                    window.stockExporter = new StockExporter(itemStockDatatable);
                    window.stockExporterInitialized = true;
                }
            },
            "initComplete": function(settings, json) {
                // Ajouter des boutons d'exportation à DataTables
                if ($.fn.DataTable.Buttons) {
                    new $.fn.dataTable.Buttons(itemStockDatatable, {
                        buttons: [
                            {
                                extend: 'excel',
                                text: '<i class="fa fa-file-excel-o"></i> Excel',
                                className: 'btn btn-success btn-sm',
                                title: 'Etat_de_Stock'
                            },
                            {
                                extend: 'pdf',
                                text: '<i class="fa fa-file-pdf-o"></i> PDF',
                                className: 'btn btn-danger btn-sm',
                                title: 'Etat_de_Stock'
                            },
                            {
                                extend: 'print',
                                text: '<i class="fa fa-print"></i> Imprimer',
                                className: 'btn btn-info btn-sm'
                            }
                        ]
                    });

                    itemStockDatatable.buttons().container().appendTo('.dataTables_wrapper .col-sm-6:eq(0)');
                }
            }
        });

        // Fonction pour charger des données de démo (à supprimer en production)
        function loadDemoData() {
            var demoData = [
                ["Ordinateur Portable", "Informatique", "Unité", "899.99", "15"],
                ["Souris Sans Fil", "Informatique", "Unité", "29.99", "120"],
                ["Clavier Mécanique", "Informatique", "Unité", "89.99", "45"],
                ["Écran 24\"", "Informatique", "Unité", "249.99", "32"],
                ["Câble HDMI", "Accessoires", "Unité", "19.99", "200"],
                ["Imprimante Laser", "Bureau", "Unité", "299.99", "18"],
                ["Papier A4", "Fournitures", "Ramette", "4.99", "500"],
                ["Stylo Bic", "Fournitures", "Boîte de 10", "2.99", "300"],
                ["Café Arabica", "Cafétéria", "Kg", "24.99", "50"],
                ["Chaise de Bureau", "Mobilier", "Unité", "159.99", "25"],
                ["Bureau Executive", "Mobilier", "Unité", "599.99", "8"],
                ["Serveur Rack", "Informatique", "Unité", "2499.99", "3"],
                ["Licence Windows", "Logiciels", "Unité", "149.99", "60"],
                ["Antivirus", "Logiciels", "Unité", "49.99", "85"],
                ["Clé USB 32Go", "Accessoires", "Unité", "12.99", "250"]
            ];

            var tbody = $('#stockTable tbody');
            tbody.empty();

            demoData.forEach(function(row) {
                var tr = $('<tr>');
                row.forEach(function(cell, index) {
                    var td = $('<td>');
                    if (index === 3) {
                        td.text(parseFloat(cell).toLocaleString('fr-FR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }) + ' FCFA');
                    } else if (index === 4) {
                        td.text(parseInt(cell).toLocaleString('fr-FR'));
                        td.addClass('text-right');
                    } else {
                        td.text(cell);
                    }
                    tr.append(td);
                });
                tbody.append(tr);
            });

            // Calculer le total
            var total = demoData.reduce(function(sum, row) {
                return sum + parseInt(row[4]);
            }, 0);
            $('#totalQuantity').text(total.toLocaleString('fr-FR'));
        }

        // Charger les données de démo si aucun serveur n'est configuré
        if (typeof baseUrl === 'undefined') {
            loadDemoData();
        }
    });
});