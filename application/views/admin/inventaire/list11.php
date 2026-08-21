<?php
$dtInventoryID = 'inventoryDatatable';
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-cubes"></i> Espace Inventaire</h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-list-alt"></i> État de l'inventaire</h3>
                        <div class="box-tools pull-right">
                            <div class="btn-group">
                                <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                                    <i class="fa fa-download"></i> Exporter
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
                        <div class="row" style="margin-bottom: 15px;">
                            <div class="col-md-6">
                                <div class="checkbox">
                                    <label><input type="checkbox" id="showZeroStock"> Afficher les articles en rupture (stock = 0)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div id="lowStockAlert" class="alert alert-warning" style="display: none;">
                                    <i class="fa fa-exclamation-triangle"></i>
                                    <span id="lowStockCount">0</span> article(s) ont atteint leur seuil d'alerte.
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover <?= $dtInventoryID ?>" id="inventoryTable">
                                <thead>
                                <tr>
                                    <th>Désignation</th>
                                    <th class="text-right">Stock</th>
                                    <th class="text-right">Entrée</th>
                                    <th class="text-right">Sortie</th>
                                    <th class="text-right">Stock théorique</th>
                                    <th class="text-right">Stock réel</th>
                                    <th class="text-right">Écart</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                <tr>
                                    <th colspan="6" style="text-align:right">Écart total :</th>
                                    <th id="totalEcart" class="text-right"></th>
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

<!-- Modal PDF (identique) -->
<div class="modal fade" id="pdfOptionsModal" tabindex="-1">...</div>

<style>
    .text-right { text-align: right; }
    .zero-stock { background-color: #fff3f3 !important; }
    .low-stock { background-color: #fff3cd !important; }
    .ecart-positif { color: #dc3545; font-weight: bold; }
    .ecart-negatif { color: #28a745; font-weight: bold; }
    .editable-cell { cursor: pointer; background-color: #f9f9f9; }
    .editable-cell:hover { background-color: #e6e6e6; }
    .edit-input { width: 100%; padding: 4px; text-align: right; border: 1px solid #ccc; border-radius: 3px; }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script type="text/javascript">
    var baseurl = '<?php echo base_url(); ?>';
    var dtID = '<?php echo $dtInventoryID; ?>';

    function formatNumber(n) {
        if (typeof n === 'string') n = n.replace(/\s/g, '').replace(',', '.');
        n = parseFloat(n) || 0;
        return new Intl.NumberFormat('fr-FR').format(n);
    }

    $(document).ready(function() {
        if ($.fn.DataTable.isDataTable('.' + dtID)) $('.' + dtID).DataTable().destroy();

        var table = $('.' + dtID).DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: baseurl + 'admin/inventaire/get_inventory_datatable',
                type: 'POST',
                data: function(d) {
                    d.show_zero_stock = $('#showZeroStock').is(':checked') ? 1 : 0;
                },
                dataSrc: function(json) {
                    setTimeout(calcTotalEcart, 100);
                    return json.data;
                }
            },
            columns: [
                { data: "article" },
                { data: "quantite_disponible", className: "text-right", render: formatNumber },
                { data: "quantite_initiale", className: "text-right", render: formatNumber },
                { data: "quantite_sortie", className: "text-right", render: formatNumber },
                { data: "quantite_disponible", className: "text-right", render: formatNumber },
                { data: "stock_reel", className: "text-right editable-cell", render: function(data) { return formatNumber(data); } },
                { data: "ecart", className: "text-right", render: function(data) {
                        var val = parseFloat(data) || 0;
                        var sign = val >= 0 ? '+' : '';
                        var css = val < 0 ? 'ecart-positif' : (val > 0 ? 'ecart-negatif' : '');
                        return '<span class="' + css + '">' + sign + formatNumber(val) + '</span>';
                    } },
                { data: "stock_threshold", visible: false },
                { data: "item_id", visible: false }
            ],
            order: [[0, "asc"]],
            language: { url: baseurl + "assets/js/french.json" },
            drawCallback: function() {
                var lowStockCount = 0;
                $('.' + dtID + ' tbody tr').each(function() {
                    var $row = $(this);
                    var stock = parseFloat($row.find('td:eq(1)').text().replace(/\s/g, '')) || 0;
                    var threshold = parseFloat($row.find('td:eq(7)').text().replace(/\s/g, '')) || 0;
                    if (stock === 0) {
                        $row.addClass('zero-stock').removeClass('low-stock');
                    } else if (stock <= threshold) {
                        $row.addClass('low-stock').removeClass('zero-stock');
                        lowStockCount++;
                    } else {
                        $row.removeClass('zero-stock low-stock');
                    }
                });
                if (lowStockCount > 0) {
                    $('#lowStockCount').text(lowStockCount);
                    $('#lowStockAlert').fadeIn();
                } else {
                    $('#lowStockAlert').fadeOut();
                }
                calcTotalEcart();
                attachInlineEdit();
            },
            initComplete: function() {
                $('#showZeroStock').change(() => table.ajax.reload());
                attachInlineEdit();
            }
        });

        function calcTotalEcart() {
            var total = 0;
            $('.' + dtID + ' tbody tr').each(function() {
                var ecartText = $(this).find('td:eq(6)').text().replace(/[^0-9-]/g, '');
                total += parseFloat(ecartText) || 0;
            });
            var totalFormatted = (total >= 0 ? '+' : '') + formatNumber(total);
            $('#totalEcart').html(totalFormatted);
            $('#totalEcart').css('color', total < 0 ? '#dc3545' : (total > 0 ? '#28a745' : '#333'));
        }

        function attachInlineEdit() {
            $('.' + dtID + ' tbody td:nth-child(6)').off('dblclick').on('dblclick', function(e) {
                e.stopPropagation();
                var $cell = $(this);
                var $row = $cell.closest('tr');
                var rowData = table.row($row).data();
                if (!rowData) return;

                var currentVal = rowData.stock_reel;
                var itemId = rowData.item_id;

                var $input = $('<input type="number" step="1" class="edit-input" value="' + currentVal + '">');
                $cell.empty().append($input);
                $input.focus();

                var saveEdit = function() {
                    var newValue = parseInt($input.val(), 10);
                    if (isNaN(newValue)) newValue = 0;
                    if (newValue === currentVal) {
                        $cell.html(formatNumber(currentVal));
                        return;
                    }
                    $cell.html(formatNumber(newValue));
                    $.ajax({
                        url: baseurl + 'admin/inventaire/update_real_stock',
                        type: 'POST',
                        data: { item_id: itemId, real_quantity: newValue },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                table.ajax.reload(null, false);
                            } else {
                                alert('Erreur : ' + response.message);
                                $cell.html(formatNumber(currentVal));
                            }
                        },
                        error: function() {
                            alert('Erreur de connexion');
                            $cell.html(formatNumber(currentVal));
                        }
                    });
                };

                $input.on('blur', saveEdit);
                $input.on('keypress', function(e) {
                    if (e.which === 13) {
                        e.preventDefault();
                        saveEdit();
                    }
                });
            });
        }

        $(document).on('stockMovementDone', function() {
            table.ajax.reload(null, false);
        });

        // Export Excel
        $('#export-excel').click(function(e) {
            e.preventDefault();
            var data = table.rows().data().toArray();
            var wsData = [['Désignation','Stock','Entrée','Sortie','Stock théorique','Stock réel','Écart']];
            $.each(data, function(i, r) {
                wsData.push([r.article, r.quantite_disponible, r.quantite_initiale, r.quantite_sortie, r.quantite_disponible, r.stock_reel, r.ecart]);
            });
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, XLSX.utils.aoa_to_sheet(wsData), 'Inventaire');
            XLSX.writeFile(wb, 'inventaire_' + new Date().toISOString().slice(0,19) + '.xlsx');
        });

        // Export PDF
        $('#export-pdf').click(function(e) { e.preventDefault(); $('#pdfOptionsModal').modal('show'); });
        $('#generatePdf').click(function() {
            var doc = new jspdf.jsPDF({ orientation: $('#orientation').val(), unit: 'mm', format: $('#format').val() });
            doc.setFontSize(18); doc.text($('#report_title').val(), 14, 22);
            doc.setFontSize(11); doc.text('Généré le: ' + new Date().toLocaleDateString('fr-FR'), 14, 32);
            var data = table.rows().data().toArray();
            var showZero = $('#show_zero_stock_pdf').is(':checked');
            var filtered = showZero ? data : data.filter(r => parseFloat(r.quantite_disponible) > 0);
            var body = filtered.map(r => [r.article, r.quantite_disponible, r.quantite_initiale, r.quantite_sortie, r.quantite_disponible, r.stock_reel, r.ecart]);
            doc.autoTable({
                head: [['Désignation','Stock','Entrée','Sortie','Stock théorique','Stock réel','Écart']],
                body: body,
                startY: 40,
                theme: 'striped',
                styles: { fontSize: 8, cellPadding: 2 },
                headStyles: { fillColor: [41,128,185], textColor: 255 }
            });
            doc.save('inventaire.pdf');
            $('#pdfOptionsModal').modal('hide');
        });

        $('#export-print').click(function(e) { e.preventDefault(); window.print(); });
    });
</script>