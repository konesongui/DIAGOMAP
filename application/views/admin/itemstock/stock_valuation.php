<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-calculator"></i> État de stock - Valorisation actuelle
            <small>Bénéfice potentiel, valeur achat / vente</small>
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-pie-chart"></i> Analyse du stock</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-info btn-sm" id="toggleSummary">
                                <i class="fa fa-bar-chart"></i> Afficher résumé par catégorie
                            </button>
                            <div class="btn-group">
                                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-download"></i> Exporter <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a href="#" id="export-excel"><i class="fa fa-file-excel-o text-success"></i> Excel</a></li>
                                    <li><a href="#" id="export-pdf"><i class="fa fa-file-pdf-o text-danger"></i> PDF</a></li>
                                    <li><a href="#" id="export-print"><i class="fa fa-print"></i> Imprimer</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="box-body">
                        <!-- Cartes récapitulatives -->
                        <div class="row">
                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-green">
                                    <div class="inner">
                                        <h3 id="total-benefice">0 FCFA</h3>
                                        <p>Bénéfice potentiel total</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-line-chart"></i></div>
                                    <div class="small-box-footer">Marge moyenne : <span id="marge-moyenne">0</span>%</div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-blue">
                                    <div class="inner">
                                        <h3 id="total-valeur-achat">0 FCFA</h3>
                                        <p>Valeur stock (prix d'achat)</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-shopping-cart"></i></div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-yellow">
                                    <div class="inner">
                                        <h3 id="total-valeur-vente">0 FCFA</h3>
                                        <p>Valeur potentielle (vente)</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-money"></i></div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-xs-6">
                                <div class="small-box bg-red">
                                    <div class="inner">
                                        <h3 id="articles-rupture">0</h3>
                                        <p>Articles en rupture</p>
                                    </div>
                                    <div class="icon"><i class="fa fa-warning"></i></div>
                                </div>
                            </div>
                        </div>

                        <!-- Bloc résumé par catégorie (caché au départ) -->
                        <div id="categorySummary" style="display: none; margin-bottom: 20px;">
                            <div class="box box-info">
                                <div class="box-header with-border">
                                    <h3 class="box-title"><i class="fa fa-tags"></i> Valorisation par catégorie</h3>
                                </div>
                                <div class="box-body table-responsive">
                                    <table class="table table-striped table-bordered" id="category-table">
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

                        <!-- Filtre afficher les ruptures -->
                        <div class="row" style="margin-bottom: 15px;">
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label><input type="checkbox" id="showZeroStock"> Afficher les articles en rupture (stock = 0)</label>
                                </div>
                            </div>
                        </div>

                        <!-- Tableau principal -->
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" id="valuationTable">
                                <thead>
                                <tr>
                                    <th>Article</th>
                                    <th>Catégorie</th>
                                    <th>Unité</th>
                                    <th class="text-right">Qté disponible</th>
                                    <th class="text-right">Prix d'achat (unité)</th>
                                    <th class="text-right">Prix de vente (unité)</th>
                                    <th class="text-right">Marge unitaire</th>
                                    <th class="text-right">Bénéfice potentiel total</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="3" style="text-align:right">Totaux :</th>
                                    <th id="totalQty" class="text-right"></th>
                                    <th id="totalAchat" class="text-right"></th>
                                    <th id="totalVente" class="text-right"></th>
                                    <th></th>
                                    <th id="totalBeneficeTable" class="text-right"></th>
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

<!-- Modal PDF Options -->
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
                        <input type="text" class="form-control" id="report_title" value="État de stock - Valorisation">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Orientation :</label>
                            <select class="form-control" id="orientation">
                                <option value="portrait">Portrait</option>
                                <option value="landscape" selected>Paysage</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Format :</label>
                            <select class="form-control" id="format">
                                <option value="A4">A4</option>
                                <option value="A3">A3</option>
                                <option value="letter">Lettre</option>
                            </select>
                        </div>
                    </div>
                    <div class="checkbox">
                        <label><input type="checkbox" id="pdf_include_zero" checked> Inclure les ruptures</label>
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
    .zero-stock { background-color: #fff3f3 !important; }
    .text-right { text-align: right; }
    .small-box { border-radius: 10px; }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    var baseurl = '<?php echo base_url(); ?>';

    function formatNumber(n) { return new Intl.NumberFormat('fr-FR').format(n || 0); }
    function formatCurrency(n) { return formatNumber(n) + ' FCFA'; }

    $(document).ready(function() {
        // ---------- Chargement des totaux et catégories ----------
        function loadTotals() {
            $.get(baseurl + 'admin/stock_valuation/get_valuation_totals', function(res) {
                if (res.status === 'success') {
                    $('#total-benefice').text(formatCurrency(res.data.total_benefice_potentiel));
                    $('#total-valeur-achat').text(formatCurrency(res.data.total_valeur_achat));
                    $('#total-valeur-vente').text(formatCurrency(res.data.total_valeur_vente));
                    $('#articles-rupture').text(res.data.articles_rupture || 0);
                    $('#marge-moyenne').text((res.data.marge_moyenne || 0).toFixed(1));
                }
            });
        }

        function loadCategoryData() {
            $.get(baseurl + 'admin/stock_valuation/get_valuation_by_category', function(res) {
                if (res.status === 'success' && res.data.length) {
                    var html = '';
                    $.each(res.data, function(i, cat) {
                        var marge = cat.valeur_vente > 0 ? (cat.benefice_potentiel / cat.valeur_vente * 100) : 0;
                        html += `<tr>
                        <td>${cat.category_name}</td>
                        <td class="text-right">${formatNumber(cat.nb_articles)}</td>
                        <td class="text-right">${formatCurrency(cat.valeur_achat)}</td>
                        <td class="text-right">${formatCurrency(cat.valeur_vente)}</td>
                        <td class="text-right"><strong class="text-green">${formatCurrency(cat.benefice_potentiel)}</strong></td>
                        <td class="text-right">${marge.toFixed(1)}%</td>
                    </tr>`;
                    });
                    $('#category-table tbody').html(html);
                } else {
                    $('#category-table tbody').html('<tr><td colspan="6" class="text-center">Aucune donnée</td></tr>');
                }
            });
        }

        // Toggle affichage résumé par catégorie
        $('#toggleSummary').click(function() {
            $('#categorySummary').slideToggle(300);
            if ($('#categorySummary').is(':visible') && $('#category-table tbody tr:first td:first').text() === 'Chargement...') {
                loadCategoryData();
            }
            $(this).html($('#categorySummary').is(':visible') ? '<i class="fa fa-bar-chart"></i> Masquer résumé' : '<i class="fa fa-bar-chart"></i> Afficher résumé');
        });

        // Initialisation DataTable
        var table = $('#valuationTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: baseurl + 'admin/stock_valuation/get_valuation_data',
                type: 'POST',
                data: function(d) {
                    d.show_zero_stock = $('#showZeroStock').is(':checked') ? '1' : '0';
                },
                dataSrc: function(json) {
                    setTimeout(updateTotals, 100);
                    return json.data;
                }
            },
            columns: [
                { data: "article" },
                { data: "category" },
                { data: "unit" },
                { data: "quantite_disponible", className: "text-right" },
                { data: "prix_achat", className: "text-right" },
                { data: "prix_vente", className: "text-right" },
                {
                    data: "marge_unitaire", className: "text-right",
                    render: function(data) {
                        let val = parseFloat(data.replace(/\s/g, '').replace(',', '.'));
                        return `<span class="${val >= 0 ? 'text-green' : 'text-red'}">${data}</span>`;
                    }
                },
                {
                    data: "benefice_potentiel", className: "text-right",
                    render: function(data) {
                        let val = parseFloat(data.replace(/\s/g, '').replace(',', '.'));
                        return `<strong class="${val >= 0 ? 'text-green' : 'text-red'}">${data}</strong>`;
                    }
                }
            ],
            order: [[0, "asc"]],
            language: { url: baseurl + "assets/js/french.json" },
            drawCallback: function() {
                $('#valuationTable tbody tr').each(function() {
                    let qty = parseFloat($(this).find('td:eq(3)').text().replace(/\s/g, '').replace(',', '.')) || 0;
                    if (qty === 0) $(this).addClass('zero-stock');
                    else $(this).removeClass('zero-stock');
                });
            },
            initComplete: function() {
                $('#showZeroStock').change(() => table.ajax.reload());
                loadTotals();
            }
        });

        // Mise à jour des totaux en pied de tableau
        function updateTotals() {
            let totalQty = 0, totalAchat = 0, totalVente = 0, totalBenef = 0;
            table.rows().data().each(function(row) {
                let qty = parseFloat(row.quantite_disponible.replace(/\s/g, '').replace(',', '.'));
                let achat = parseFloat(row.prix_achat.replace(/\s/g, '').replace(',', '.'));
                let vente = parseFloat(row.prix_vente.replace(/\s/g, '').replace(',', '.'));
                let benef = parseFloat(row.benefice_potentiel.replace(/\s/g, '').replace(',', '.'));
                totalQty += qty;
                totalAchat += achat * qty;
                totalVente += vente * qty;
                totalBenef += benef;
            });
            $('#totalQty').text(formatNumber(totalQty));
            $('#totalAchat').text(formatCurrency(totalAchat));
            $('#totalVente').text(formatCurrency(totalVente));
            $('#totalBeneficeTable').text(formatCurrency(totalBenef));
        }

        // ---------- EXPORT EXCEL ----------
        $('#export-excel').click(function(e) {
            e.preventDefault();
            var data = table.rows().data().toArray();
            var wsData = [['Article', 'Catégorie', 'Unité', 'Qté disponible', "Prix d'achat unitaire", "Prix de vente unitaire", 'Marge unitaire', 'Bénéfice potentiel']];
            $.each(data, function(i, r) {
                wsData.push([r.article, r.category, r.unit, r.quantite_disponible, r.prix_achat, r.prix_vente, r.marge_unitaire, r.benefice_potentiel]);
            });
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(wsData), 'Valorisation_Stock');
            XLSX.writeFile(wb, 'valorisation_stock_' + new Date().toISOString().slice(0,19) + '.xlsx');
        });

        // ---------- EXPORT PDF ----------
        $('#export-pdf').click(function(e) { e.preventDefault(); $('#pdfOptionsModal').modal('show'); });
        $('#generatePdf').click(function() {
            var doc = new jspdf.jsPDF({ orientation: $('#orientation').val(), unit: 'mm', format: $('#format').val() });
            doc.setFontSize(18); doc.text($('#report_title').val(), 14, 22);
            doc.setFontSize(11); doc.text('Généré le : ' + new Date().toLocaleDateString('fr-FR'), 14, 32);

            var data = table.rows().data().toArray();
            var showZero = $('#pdf_include_zero').is(':checked');
            var filtered = showZero ? data : data.filter(r => parseFloat(r.quantite_disponible.replace(/\s/g, '')) > 0);
            var body = filtered.map(r => [r.article, r.category, r.unit, r.quantite_disponible, r.prix_achat, r.prix_vente, r.marge_unitaire, r.benefice_potentiel]);

            doc.autoTable({
                head: [['Article', 'Catégorie', 'Unité', 'Qté disp.', 'Prix achat', 'Prix vente', 'Marge unit.', 'Bénéfice total']],
                body: body,
                startY: 40,
                theme: 'striped',
                styles: { fontSize: 8, cellPadding: 2 },
                headStyles: { fillColor: [41,128,185], textColor: 255 }
            });
            doc.save('valorisation_stock.pdf');
            $('#pdfOptionsModal').modal('hide');
        });

        // ---------- IMPRESSION ----------
        $('#export-print').click(function(e) { e.preventDefault(); window.print(); });
    });
</script>