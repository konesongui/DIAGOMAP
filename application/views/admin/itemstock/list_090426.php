il faut rendre la vue chic mets option depliant au cas ou on veut voir le tableau de bord avec Bénéfice potentiel par catégorie on clique sur le bouton pour affiché: <?php
// Set all the CRUD variables Stock entry Tool
$dtItemStockID = 'itemStockDatatable';
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-object-group"></i> État de stock
            <small>Aperçu du stock et bénéfice potentiel</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Horizontal Form -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">État de stock</h3>
                        <!-- Boutons d'exportation -->
                        <div class="box-tools pull-right">
                            <div class="btn-group">
                                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-download"></i> Exporter <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="#" id="export-excel"><i class="fa fa-file-excel-o text-success"></i> Excel</a></li>
                                    <li><a href="#" id="export-pdf"><i class="fa fa-file-pdf-o text-danger"></i> PDF</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="#" id="export-print"><i class="fa fa-print"></i> Imprimer</a></li>
                                </ul>
                            </div>
                        </div>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <!-- Cartes de bénéfice potentiel -->
                        <div class="row" id="profit-cards" style="display: none;">
                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-green">
                                    <div class="inner">
                                        <h3 id="total-benefice">0 FCFA</h3>
                                        <p>Bénéfice potentiel total</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-line-chart"></i>
                                    </div>
                                    <div class="small-box-footer">
                                        Marge: <span id="marge-moyenne">0</span>%
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-blue">
                                    <div class="inner">
                                        <h3 id="total-valeur-achat">0 FCFA</h3>
                                        <p>Valeur stock (prix d'achat)</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-shopping-cart"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-yellow">
                                    <div class="inner">
                                        <h3 id="total-valeur-vente">0 FCFA</h3>
                                        <p>Valeur potentielle (prix de vente)</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-money"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-red">
                                    <div class="inner">
                                        <h3 id="articles-rupture">0</h3>
                                        <p>Articles en rupture</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fa fa-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tableau des bénéfices par catégorie -->
                        <div class="row" id="profit-category-table" style="display: none;">
                            <div class="col-md-12">
                                <div class="box box-info">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><i class="fa fa-pie-chart"></i> Bénéfice potentiel par catégorie</h3>
                                    </div>
                                    <div class="box-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered" id="category-profit-table">
                                                <thead>
                                                <tr>
                                                    <th>Catégorie</th>
                                                    <th class="text-right">Nb articles</th>
                                                    <th class="text-right">Valeur achat (FCFA)</th>
                                                    <th class="text-right">Valeur vente (FCFA)</th>
                                                    <th class="text-right">Bénéfice potentiel (FCFA)</th>
                                                    <th class="text-right">Marge (%)</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td colspan="6" class="text-center">Chargement...</td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filtre pour afficher/masquer les articles en rupture -->
                        <div class="row" style="margin-bottom: 15px;">
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" id="showZeroStock"> Afficher les articles en rupture de stock (quantité = 0)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Tableau principal des stocks -->
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover <?= $dtItemStockID ?>" id="stockTable" role="grid" aria-label="État de stock">
                                <thead>
                                <tr>
                                    <th scope="col">Article</th>
                                    <th scope="col">Catégorie</th>
                                    <th scope="col">Unité</th>
                                    <th scope="col">Stock disponible</th>
                                    <th scope="col">Prix d'achat</th>
                                    <th scope="col">Prix de vente</th>
                                    <th scope="col">Marge unitaire</th>
                                    <th scope="col">Bénéfice potentiel</th>
                                </tr>
                                </thead>
                                <tbody>
                                <!-- Données chargées via DataTables -->
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="3" style="text-align:right">Total:</th>
                                    <th id="totalQuantity"></th>
                                    <th colspan="4"></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div><!--/.col (right) -->
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<!-- Modal pour les options PDF -->
<div class="modal fade" id="pdfOptionsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><i class="fa fa-file-pdf-o"></i> Options d'exportation PDF</h4>
            </div>
            <div class="modal-body">
                <form id="pdfOptionsForm">
                    <div class="form-group">
                        <label for="report_title"><i class="fa fa-font"></i> Titre du rapport :</label>
                        <input type="text" class="form-control" id="report_title" name="report_title" value="État de Stock" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="orientation"><i class="fa fa-arrows-alt"></i> Orientation :</label>
                                <select class="form-control" id="orientation" name="orientation">
                                    <option value="portrait">Portrait</option>
                                    <option value="landscape" selected>Paysage</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="format"><i class="fa fa-file"></i> Format :</label>
                                <select class="form-control" id="format" name="format">
                                    <option value="A4">A4</option>
                                    <option value="A3">A3</option>
                                    <option value="letter">Lettre</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="show_zero_stock_pdf" name="show_zero_stock" checked> Inclure les articles en rupture de stock
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="generatePdf">
                    <i class="fa fa-file-pdf-o"></i> Générer PDF
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Styles CSS supplémentaires -->
<style>
    .small-box {
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
        transition: all 0.3s cubic-bezier(.25,.8,.25,1);
    }

    .small-box:hover {
        box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
    }

    .small-box .inner h3 {
        font-size: 28px;
        font-weight: bold;
    }

    .export-btn-group .dropdown-menu li a {
        padding: 8px 15px;
    }

    .export-btn-group .dropdown-menu li a i {
        margin-right: 8px;
        width: 20px;
        text-align: center;
    }

    #export-status {
        padding: 10px 15px;
        border-radius: 4px;
    }

    .dataTables_wrapper .dt-buttons {
        float: right;
        margin-left: 10px;
    }

    .total-row {
        background-color: #f9f9f9 !important;
        font-weight: bold;
        border-top: 2px solid #333 !important;
    }

    .text-right {
        text-align: right;
    }

    /* Style pour les lignes avec stock = 0 */
    .zero-stock {
        background-color: #fff3f3 !important;
    }

    .zero-stock td {
        color: #999;
    }

    /* Animation pour les cartes */
    #profit-cards, #profit-category-table {
        animation: fadeIn 0.5s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Style pour le bénéfice potentiel dans le tableau */
    .benefice-positive {
        color: #28a745;
        font-weight: bold;
    }

    .benefice-negative {
        color: #dc3545;
        font-weight: bold;
    }
</style>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

<!-- Script principal mis à jour -->
<script type="text/javascript">
    // Configuration de base
    var baseurl = '<?php echo base_url(); ?>';
    var dtID = '<?php echo $dtItemStockID; ?>';

    // Fonction pour formater les nombres
    function formatNumber(number) {
        if (number === undefined || number === null) return '0';
        return new Intl.NumberFormat('fr-FR', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(number);
    }

    function formatCurrency(number) {
        if (number === undefined || number === null) return '0 FCFA';
        return new Intl.NumberFormat('fr-FR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(number) + ' FCFA';
    }

    // Fonction pour charger les totaux de bénéfice
    function loadProfitTotals() {
        $.ajax({
            url: baseurl + 'admin/itemstock/get_profit_totals',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    var data = response.data;

                    $('#total-benefice').text(formatCurrency(data.total_benefice_potentiel));
                    $('#total-valeur-achat').text(formatCurrency(data.total_valeur_achat));
                    $('#total-valeur-vente').text(formatCurrency(data.total_valeur_vente));
                    $('#articles-rupture').text(data.articles_rupture || 0);
                    $('#marge-moyenne').text((data.marge_moyenne || 0).toFixed(1));

                    // Afficher les cartes avec animation
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
                        var beneficeClass = cat.benefice_potentiel >= 0 ? 'text-success' : 'text-danger';

                        html += '<tr>';
                        html += '<td>' + cat.category_name + '</td>';
                        html += '<td class="text-right">' + formatNumber(cat.nb_articles) + '</td>';
                        html += '<td class="text-right">' + formatCurrency(cat.valeur_achat) + '</td>';
                        html += '<td class="text-right">' + formatCurrency(cat.valeur_vente) + '</td>';
                        html += '<td class="text-right"><strong class="' + beneficeClass + '">' + formatCurrency(cat.benefice_potentiel) + '</strong></td>';
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
                "url": baseurl + 'admin/itemstock/data_with_profit',
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
                    "className": "text-right",
                    "render": function(data, type, row) {
                        if (type === 'display') {
                            var value = parseFloat(data.replace(/,/g, '.')) || 0;
                            var colorClass = value >= 0 ? 'benefice-positive' : 'benefice-negative';
                            return '<span class="' + colorClass + '">' + data + '</span>';
                        }
                        return data;
                    }
                },
                {
                    "data": "benefice_potentiel",
                    "className": "text-right",
                    "render": function(data, type, row) {
                        if (type === 'display') {
                            var value = parseFloat(data.replace(/,/g, '.')) || 0;
                            var colorClass = value >= 0 ? 'benefice-positive' : 'benefice-negative';
                            return '<strong class="' + colorClass + '">' + data + '</strong>';
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
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tous"]],
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

            $('#totalQuantity').text(formatNumber(total));
        }

        // Exporter les fonctions globalement
        window.itemStockTable = itemStockTable;
        window.calculateTotal = calculateTotal;
    });
</script>

<!-- Scripts supplémentaires pour les exports -->
<script type="text/javascript">
    // Export Excel
    $('#export-excel').on('click', function(e) {
        e.preventDefault();
        var data = itemStockTable.rows().data().toArray();
        var wsData = [];

        // En-têtes
        wsData.push(['Article', 'Catégorie', 'Unité', 'Stock disponible', 'Prix d\'achat', 'Prix de vente', 'Marge unitaire', 'Bénéfice potentiel']);

        // Données
        $.each(data, function(i, row) {
            wsData.push([
                row.article,
                row.category,
                row.unit,
                row.quantite_disponible,
                row.prix_achat,
                row.prix_vente,
                row.marge_unitaire,
                row.benefice_potentiel
            ]);
        });

        var ws = XLSX.utils.aoa_to_sheet(wsData);
        var wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Etat_Stock');
        XLSX.writeFile(wb, 'etat_stock_' + new Date().toISOString().slice(0,19) + '.xlsx');
    });

    // Export PDF
    $('#export-pdf').on('click', function(e) {
        e.preventDefault();
        $('#pdfOptionsModal').modal('show');
    });

    $('#generatePdf').on('click', function() {
        var doc = new jspdf.jsPDF({
            orientation: $('#orientation').val(),
            unit: 'mm',
            format: $('#format').val()
        });

        var title = $('#report_title').val();
        var showZeroStock = $('#show_zero_stock_pdf').is(':checked');

        // Titre
        doc.setFontSize(18);
        doc.text(title, 14, 22);
        doc.setFontSize(11);
        doc.text('Généré le: ' + new Date().toLocaleDateString('fr-FR'), 14, 32);

        // Récupérer les données filtrées
        var data = itemStockTable.rows().data().toArray();
        var filteredData = showZeroStock ? data : data.filter(row => parseFloat(row.quantite_disponible.replace(/\s/g, '')) > 0);

        var tableData = filteredData.map(row => [
            row.article,
            row.category,
            row.unit,
            row.quantite_disponible,
            row.prix_achat,
            row.prix_vente,
            row.marge_unitaire,
            row.benefice_potentiel
        ]);

        doc.autoTable({
            head: [['Article', 'Catégorie', 'Unité', 'Stock', 'Prix Achat', 'Prix Vente', 'Marge', 'Bénéfice']],
            body: tableData,
            startY: 40,
            theme: 'striped',
            styles: { fontSize: 8, cellPadding: 2 },
            headStyles: { fillColor: [41, 128, 185], textColor: 255, fontSize: 9 }
        });

        doc.save('etat_stock_' + new Date().toISOString().slice(0,19) + '.pdf');
        $('#pdfOptionsModal').modal('hide');
    });

    // Impression
    $('#export-print').on('click', function(e) {
        e.preventDefault();
        window.print();
    });
</script>