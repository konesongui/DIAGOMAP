<?php
$dtItemStockID = 'itemStockDatatable';
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-object-group"></i> État de stock
            <small>Aperçu du stock et bénéfice potentiel</small>
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-pie-chart"></i> État de stock détaillé</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-info btn-sm" id="toggleProfitDashboard" style="margin-right: 5px;">
                                <i class="fa fa-line-chart"></i> Afficher
                            </button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
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
                    </div>

                    <div class="box-body">
                        <!-- Tableau de bord bénéfice (caché initialement) -->
                        <div id="profitDashboard" style="display: none;">
                            <!-- Première rangée : cartes existantes -->
                            <div class="row">
                                <div class="col-lg-3 col-xs-6">
                                    <div class="small-box">
                                        <div class="inner" style="color: black">
                                            <h3 id="total-benefice">0 FCFA</h3>
                                            <p>Bénéfice potentiel total</p>
                                        </div>
                                        <div style="font-size: 30px" class="icon"><i class="fa fa-line-chart bg-green"></i></div>
                                        <div style="color: black" class="small-box-footer">Marge: <span id="marge-moyenne">0</span>%</div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-xs-6">
                                    <div class="small-box">
                                        <div class="inner">
                                            <h3 id="total-valeur-achat">0 FCFA</h3>
                                            <p>Valeur stock (prix d'achat)</p>
                                        </div>
                                        <div style="font-size: 30px" class="icon"><i class="fa fa-shopping-cart bg-blue"></i></div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-xs-6">
                                    <div class="small-box">
                                        <div class="inner">
                                            <h3 id="total-valeur-vente">0 FCFA</h3>
                                            <p>Valeur potentielle (vente)</p>
                                        </div>
                                        <div style="font-size: 30px" class="icon"><i class="fa fa-money bg-yellow"></i></div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-xs-6">
                                    <div class="small-box">
                                        <div class="inner">
                                            <h3 id="articles-rupture">0</h3>
                                            <p>Articles en rupture</p>
                                        </div>
                                        <div style="font-size: 30px" class="icon"><i class="fa fa-warning bg-red"></i></div>
                                    </div>
                                </div>
                            </div>

                            <!-- DEUXIÈME RANGÉE : INDICATEURS DE PERFORMANCE (ajoutés) -->
                            <div class="row">
                                <div class="col-lg-6 col-xs-12">
                                    <div class="small-box" style="background-color: #f9f9f9; border-left: 4px solid #00c0ef;">
                                        <div class="inner">
                                            <h3 id="rotation-stock">0</h3>
                                            <p>Taux de rotation du stock (sorties / stock moyen)</p>
                                        </div>
                                        <div style="font-size: 30px" class="icon"><i class="fa fa-refresh"></i></div>
                                        <div class="small-box-footer">
                                            Plus il est élevé, plus le stock est « actif »
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-xs-12">
                                    <div class="small-box" style="background-color: #f9f9f9; border-left: 4px solid #f39c12;">
                                        <div class="inner">
                                            <h3 id="taux-rupture">0%</h3>
                                            <p>Taux de rupture</p>
                                        </div>
                                        <div style="font-size: 30px" class="icon"><i class="fa fa-exclamation-triangle"></i></div>
                                        <div class="small-box-footer">
                                            % d’articles avec quantité disponible = 0
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tableau bénéfice par catégorie -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="box box-info">
                                        <div class="box-header with-border">
                                            <h3 class="box-title"><i class="fa fa-pie-chart"></i> Bénéfice potentiel par catégorie</h3>
                                        </div>
                                        <div class="box-body table-responsive">
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
                                                <tr><td colspan="6" class="text-center">Chargement...</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filtre rupture -->
                        <div class="row" style="margin-bottom: 15px;">
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label><input type="checkbox" id="showZeroStock"> Afficher les articles en rupture (quantité = 0)</label>
                                </div>
                            </div>
                        </div>

                        <!-- Tableau principal -->
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover <?= $dtItemStockID ?>" id="stockTable">
                                <thead>
                                <tr>
                                    <th>Article</th>
                                    <th>Catégorie</th>
                                    <th>Unité</th>
                                    <th class="text-right">Qté initiale</th>
                                    <th class="text-right">Qté sortie</th>
                                    <th class="text-right">Qté disponible</th>
                                    <th class="text-right">Prix d'achat</th>
                                    <th class="text-right">Prix de vente</th>
                                    <th class="text-right">Marge unitaire</th>
                                    <th class="text-right">Bénéfice potentiel</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="5" style="text-align:right">Total quantités disponibles :</th>
                                    <th id="totalQuantity" class="text-right"></th>
                                    <th colspan="4"></th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal pop-up pour les valeurs du stock -->
<div class="modal fade" id="stockValuesModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content" style="width: 300px">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-cubes"></i> Valeurs du stock en cours</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-blue"><i class="fa fa-shopping-cart"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Valeur stock (prix d'achat)</span>
                                <span class="info-box-number" id="modal-valeur-achat">0 FCFA</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-yellow"><i class="fa fa-money"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Valeur potentielle (vente)</span>
                                <span class="info-box-number" id="modal-valeur-vente">0 FCFA</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-line-chart"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Bénéfice potentiel total</span>
                                <span class="info-box-number" id="modal-benefice">0 FCFA</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal PDF -->
<div class="modal fade" id="pdfOptionsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-file-pdf-o"></i> Options PDF</h4>
            </div>
            <div class="modal-body">
                <form id="pdfOptionsForm">
                    <div class="form-group">
                        <label>Titre :</label>
                        <input type="text" class="form-control" id="report_title" value="État de Stock">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Orientation :</label>
                                <select class="form-control" id="orientation">
                                    <option value="portrait">Portrait</option>
                                    <option value="landscape" selected>Paysage</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Format :</label>
                                <select class="form-control" id="format">
                                    <option value="A4">A4</option>
                                    <option value="A3">A3</option>
                                    <option value="letter">Lettre</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="checkbox">
                        <label><input type="checkbox" id="show_zero_stock_pdf" checked> Inclure les ruptures</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="generatePdf">Générer PDF</button>
            </div>
        </div>
    </div>
</div>

<style>
    .small-box { border-radius: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.12); transition: 0.3s; }
    .small-box:hover { box-shadow: 0 14px 28px rgba(0,0,0,0.25); transform: translateY(-5px); }
    .zero-stock { background-color: #fff3f3 !important; }
    .benefice-positive { color: #28a745; font-weight: bold; }
    .benefice-negative { color: #dc3545; font-weight: bold; }
    #profitDashboard { animation: fadeIn 0.5s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .text-right { text-align: right; }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script type="text/javascript">
    var baseurl = '<?php echo base_url(); ?>';
    var dtID = '<?php echo $dtItemStockID; ?>';

    function formatNumber(n) { return new Intl.NumberFormat('fr-FR').format(n || 0); }
    function formatCurrency(n) { return new Intl.NumberFormat('fr-FR', {minimumFractionDigits: 0}).format(n || 0) + ' FCFA'; }

    // Charger les totaux et catégories (pour le dashboard)
    function loadProfitTotals() {
        $.get(baseurl + 'admin/itemstock/get_profit_totals', function(res) {
            if (res.status === 'success') {
                $('#total-benefice').text(formatCurrency(res.data.total_benefice_potentiel));
                $('#total-valeur-achat').text(formatCurrency(res.data.total_valeur_achat));
                $('#total-valeur-vente').text(formatCurrency(res.data.total_valeur_vente));
                $('#articles-rupture').text(res.data.articles_rupture || 0);
                $('#marge-moyenne').text((res.data.marge_moyenne || 0).toFixed(1));

                // Calcul du taux de rupture à partir des données backend
                var totalArticles = res.data.total_articles || 0;
                var articlesRupture = res.data.articles_rupture || 0;
                var tauxRupture = (totalArticles > 0) ? ((articlesRupture / totalArticles) * 100).toFixed(1) : 0;
                $('#taux-rupture').text(tauxRupture + '%');
                if (tauxRupture > 20) $('#taux-rupture').css('color', '#dc3545');
                else $('#taux-rupture').css('color', '#28a745');
            }
        });
    }

    function loadProfitByCategory() {
        $.get(baseurl + 'admin/itemstock/get_profit_by_category', function(res) {
            if (res.status === 'success' && res.data.length) {
                var html = '';
                $.each(res.data, function(i, cat) {
                    var marge = cat.valeur_vente > 0 ? (cat.benefice_potentiel / cat.valeur_vente * 100) : 0;
                    html += `<tr>
                        <td>${cat.category_name}</td>
                        <td class="text-right">${formatNumber(cat.nb_articles)}</td>
                        <td class="text-right">${formatCurrency(cat.valeur_achat)}</td>
                        <td class="text-right">${formatCurrency(cat.valeur_vente)}</td>
                        <td class="text-right"><strong class="${cat.benefice_potentiel >= 0 ? 'benefice-positive' : 'benefice-negative'}">${formatCurrency(cat.benefice_potentiel)}</strong></td>
                        <td class="text-right">${marge.toFixed(1)}%</td>
                    </tr>`;
                });
                $('#category-profit-table tbody').html(html);
            }
        });
    }

    // NOUVELLE FONCTION : indicateurs de performance (rotation et taux de rupture)
    function updatePerformanceIndicators() {
        // Calcul du taux de rotation à partir des cellules du tableau (plus fiable)
        var totalSorties = 0;
        var totalStockActuel = 0;
        var totalStockInitial = 0;
        var nbLignes = 0;

        $('.' + dtID + ' tbody tr').each(function() {
            var $row = $(this);
            // Les colonnes : 3 = Qté initiale, 4 = Qté sortie, 5 = Qté disponible
            var init = parseFloat($row.find('td:eq(3)').text().replace(/\s/g, '').replace(',', '.')) || 0;
            var sortie = parseFloat($row.find('td:eq(4)').text().replace(/\s/g, '').replace(',', '.')) || 0;
            var dispo = parseFloat($row.find('td:eq(5)').text().replace(/\s/g, '').replace(',', '.')) || 0;

            totalSorties += sortie;
            totalStockActuel += dispo;
            totalStockInitial += init;
            nbLignes++;
        });

        var stockMoyen = (totalStockInitial + totalStockActuel) / 2;
        var rotation = (stockMoyen > 0) ? (totalSorties / stockMoyen).toFixed(2) : 0;

        $('#rotation-stock').text(rotation);
        if (rotation < 1) $('#rotation-stock').css('color', '#dc3545');
        else $('#rotation-stock').css('color', '#28a745');
    }

    $(document).ready(function() {
        // Bouton toggle pour le dashboard bénéfice
        $('#toggleProfitDashboard').click(function() {
            $('#profitDashboard').slideToggle(300);
            var btn = $(this);
            if ($('#profitDashboard').is(':visible')) {
                btn.html('<i class="fa fa-line-chart"></i> Masquer tableau de bord');
                if ($('#category-profit-table tbody tr:first td:first').text() === 'Chargement...') {
                    loadProfitTotals();
                    loadProfitByCategory();
                }
                // Mettre à jour les indicateurs de performance si la table est déjà chargée
                updatePerformanceIndicators();
            } else {
                btn.html('<i class="fa fa-line-chart"></i> Afficher tableau de bord');
            }
        });

        // DataTable
        if ($.fn.DataTable.isDataTable('.' + dtID)) $('.' + dtID).DataTable().destroy();

        var table = $('.' + dtID).DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: baseurl + 'admin/itemstock/get_full_stock_data',
                type: 'POST',
                data: function(d) { d.show_zero_stock = $('#showZeroStock').is(':checked') ? 1 : 0; },
                dataSrc: function(json) { setTimeout(calcTotal, 100); return json.data; }
            },
            columns: [
                { data: "article" },
                { data: "category" },
                { data: "unit" },
                { data: "quantite_initiale", className: "text-right" },
                { data: "quantite_sortie", className: "text-right" },
                { data: "quantite_disponible", className: "text-right" },
                { data: "prix_achat", className: "text-right" },
                { data: "prix_vente", className: "text-right" },
                {
                    data: "marge_unitaire", className: "text-right",
                    render: function(data) {
                        var val = parseFloat(data.replace(/\s/g,'').replace(',','.')) || 0;
                        return `<span class="${val >= 0 ? 'benefice-positive' : 'benefice-negative'}">${data}</span>`;
                    }
                },
                {
                    data: "benefice_potentiel", className: "text-right",
                    render: function(data) {
                        var val = parseFloat(data.replace(/\s/g,'').replace(',','.')) || 0;
                        return `<strong class="${val >= 0 ? 'benefice-positive' : 'benefice-negative'}">${data}</strong>`;
                    }
                }
            ],
            order: [[0, "asc"]],
            language: { url: baseurl + "assets/js/french.json" },
            drawCallback: function() {
                $('.' + dtID + ' tbody tr').each(function() {
                    var qty = parseFloat($(this).find('td:eq(5)').text().replace(/\s/g,'').replace(',','.')) || 0;
                    if (qty === 0) $(this).addClass('zero-stock');
                    else $(this).removeClass('zero-stock');
                });
                calcTotal();
                // Mettre à jour les indicateurs si le dashboard est visible
                if ($('#profitDashboard').is(':visible')) {
                    updatePerformanceIndicators();
                }
            },
            initComplete: function() { $('#showZeroStock').change(() => table.ajax.reload()); }
        });

        function calcTotal() {
            var total = 0;
            $('.' + dtID + ' tbody tr').each(function() {
                var q = parseFloat($(this).find('td:eq(5)').text().replace(/\s/g,'').replace(',','.')) || 0;
                total += q;
            });
            $('#totalQuantity').text(formatNumber(total));
        }

        // Export Excel
        $('#export-excel').click(function(e) {
            e.preventDefault();
            var data = table.rows().data().toArray();
            var wsData = [['Article','Catégorie','Unité','Qté initiale','Qté sortie','Qté disponible','Prix achat','Prix vente','Marge unitaire','Bénéfice']];
            $.each(data, function(i, r) {
                wsData.push([r.article, r.category, r.unit, r.quantite_initiale, r.quantite_sortie, r.quantite_disponible, r.prix_achat, r.prix_vente, r.marge_unitaire, r.benefice_potentiel]);
            });
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(wsData), 'Etat_Stock');
            XLSX.writeFile(wb, 'etat_stock_' + new Date().toISOString().slice(0,19) + '.xlsx');
        });

        // Export PDF
        $('#export-pdf').click(function(e) { e.preventDefault(); $('#pdfOptionsModal').modal('show'); });
        $('#generatePdf').click(function() {
            var doc = new jspdf.jsPDF({ orientation: $('#orientation').val(), unit: 'mm', format: $('#format').val() });
            doc.setFontSize(18); doc.text($('#report_title').val(), 14, 22);
            doc.setFontSize(11); doc.text('Généré le: ' + new Date().toLocaleDateString('fr-FR'), 14, 32);

            var data = table.rows().data().toArray();
            var showZero = $('#show_zero_stock_pdf').is(':checked');
            var filtered = showZero ? data : data.filter(r => parseFloat(r.quantite_disponible.replace(/\s/g,'')) > 0);
            var body = filtered.map(r => [r.article, r.category, r.unit, r.quantite_initiale, r.quantite_sortie, r.quantite_disponible, r.prix_achat, r.prix_vente, r.marge_unitaire, r.benefice_potentiel]);

            doc.autoTable({
                head: [['Article','Catégorie','Unité','Qté init','Qté sortie','Qté disp','Achat','Vente','Marge','Bénéfice']],
                body: body,
                startY: 40,
                theme: 'striped',
                styles: { fontSize: 8, cellPadding: 2 },
                headStyles: { fillColor: [41,128,185], textColor: 255 }
            });
            doc.save('etat_stock.pdf');
            $('#pdfOptionsModal').modal('hide');
        });

        $('#export-print').click(function(e) { e.preventDefault(); window.print(); });
    });

    // Mise à jour du pop-up
    function updateStockModal() {
        $.get(baseurl + 'admin/itemstock/get_profit_totals', function(res) {
            if (res.status === 'success') {
                $('#modal-valeur-achat').text(formatCurrency(res.data.total_valeur_achat));
                $('#modal-valeur-vente').text(formatCurrency(res.data.total_valeur_vente));
                $('#modal-benefice').text(formatCurrency(res.data.total_benefice_potentiel));
            }
        });
    }

    $('#openStockModal').click(function() {
        updateStockModal();
        $('#stockValuesModal').modal('show');
    });
</script>