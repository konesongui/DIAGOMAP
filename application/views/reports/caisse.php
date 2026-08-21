<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<style type="text/css">
    /* STYLES AMÉLIORÉS AVEC COULEURS */
    .text-primary{
        color: #2c3e50;
        text-transform: uppercase;
        font-weight: 600;
    }

    .box-header {
        background: linear-gradient(135deg, #2563EB 0%, #2563EB 100%);
        color: white;
        border-radius: 5px 5px 0 0;
    }

    .box-title {
        color: white !important;
        font-weight: 600;
    }

    .table thead {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    }

    .table thead th {
        color: white;
        border: none;
        padding: 15px 10px;
        font-weight: 600;
    }

    .table tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    .table tbody tr:hover {
        background-color: #e3f2fd;
        transition: background-color 0.3s ease;
    }

    .total-bg {
        background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%) !important;
        color: white;
        font-weight: bold;
    }

    .total-bg td {
        color: white !important;
        font-size: 14px;
        font-weight: 600;
    }

    .btn-export {
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        color: white;
        border: none;
        border-radius: 4px;
    }

    .btn-export:hover {
        background: linear-gradient(135deg, #c0392b 0%, #a93226 100%);
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }

    .btn-search {
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
        border: none;
        border-radius: 4px;
    }

    .btn-search:hover {
        background: linear-gradient(135deg, #2980b9 0%, #21618c 100%);
    }

    .form-control {
        border: 2px solid #bdc3c7;
        border-radius: 4px;
        transition: border-color 0.3s ease;
    }

    .form-control:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }

    .download_label {
        background: #ecf0f1;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        border-left: 4px solid #3498db;
    }

    /* Styles pour les montants */
    .montant-entree {
        color: #27ae60;
        font-weight: 600;
    }

    .montant-sortie {
        color: #e74c3c;
        font-weight: 600;
    }

    /* Animation de chargement */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .table-responsive {
        animation: fadeIn 0.5s ease;
    }
</style>

<div class="content-wrapper" style="min-height: 946px;">
    <section class="content">
        <?php $this->load->view('reports/_finance'); ?>

        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius" style="border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">

                    <!-- En-tête avec titre et filtre -->
                    <div class="box-header with-border" style="border-radius: 10px 10px 0 0;">
                        <h3 class="box-title"><i class="fa fa-calculator"></i> Livre de caisse</h3>

                        <!-- Boutons d'exportation -->
                        <div class="pull-right box-tools">
                            <div class="btn-group">
                                <button type="button" class="btn btn-export btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-download"></i> Exporter
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="#" onclick="exportToPDF()" style="color: #e74c3c;">
                                            <i class="fa fa-file-pdf-o"></i> Export PDF
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" onclick="exportToExcel()" style="color: #27ae60;">
                                            <i class="fa fa-file-excel-o"></i> Export Excel
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Formulaire de recherche -->
                    <div class="box-body" style="background: #f8f9fa; border-bottom: 1px solid #dee2e6;">
                        <form role="form" action="<?php echo site_url('report/searchreportvalidation') ?>" method="post" class="" id="reportform">
                            <div class="row">
                                <?php echo $this->customlib->getCSRF(); ?>

                                <div class="col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label style="color: #2c3e50; font-weight: 600;">Type de recherche</label>
                                        <select class="form-control" name="search_type" onchange="showdate(this.value)">
                                            <?php foreach ($searchlist as $key => $search) { ?>
                                                <option value="<?php echo $key ?>" <?php echo (isset($search_type) && $search_type == $key) ? "selected" : ""; ?>>
                                                    <?php echo $search ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger" id="error_search_type"></span>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-3" id='date_result'>
                                    <!-- Les champs de date seront injectés ici -->
                                </div>

                                <div class="col-sm-6 col-md-3" style="padding-top: 25px;">
                                    <button type="submit" name="search" value="search_filter" class="btn btn-search btn-sm">
                                        <i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Corps du tableau -->
                    <div class="box-body table-responsive">
                        <div class="download_label">
                            <div class="row">
                                <div class="col-md-2">
                                    <img style="width: 120px; height: 60px !important;" src="<?= base_url() . "/uploads/school_content/admin_logo/" . $sch_setting->admin_logo ?>" alt="Logo" />
                                </div>
                                <div class="col-md-10">
                                    <h4 style="color: #2c3e50; margin: 0;">LIVRE DE CAISSE</h4>
                                    <p style="color: #7f8c8d; margin: 5px 0 0 0; font-size: 14px;">
                                        <?php
                                        echo "Période : ";
                                        $this->customlib->get_postmessage();
                                        ?>
                                    </p>
                                    <p style="color: #95a5a6; margin: 0; font-size: 12px;">
                                        Généré le : <?php echo date('d/m/Y à H:i'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Tableau du livre de caisse -->
                        <table class="table table-striped table-bordered table-hover expense-list"
                               data-export-title="Livre de caisse <?php $this->customlib->get_postmessage(); ?>"
                               id="livre-caisse-table">
                            <thead>
                            <tr>
                                <th class="text text-left text-primary">Référence</th>
                                <th class="text text-left text-primary">Date</th>
                                <th class="text text-left text-primary">Désignations</th>
                                <th class="text text-left text-primary">Entrée</th>
                                <th class="text text-left text-primary">Sortie</th>
                                <!--<th class="text text-left text-primary">Solde</th>-->
                            </tr>
                            </thead>
                            <tbody>
                            <!-- Les données seront chargées via AJAX -->
                            </tbody>
                            <tfoot>
                            <tr class="total-bg">
                                <td colspan="3" class="text text-left"><strong>TOTAUX</strong></td>
                                <td class="montant-entree"><strong id="total-entree">0,00</strong></td>
                                <td class="montant-sortie"><strong id="total-sortie">0,00</strong></td>

                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Scripts pour l'exportation -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    <?php if ($search_type == 'period') { ?>
    $(document).ready(function () {
        showdate('period');
    });
    <?php } ?>

    // Fonction d'exportation PDF
    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // Titre du document
        doc.setFontSize(16);
        doc.setTextColor(44, 62, 80);
        doc.text('LIVRE DE CAISSE', 105, 15, { align: 'center' });

        // Période
        doc.setFontSize(10);
        doc.setTextColor(127, 140, 141);
        doc.text('Période: <?php echo $this->customlib->get_postmessage(); ?>', 14, 25);

        // Date d'exportation
        const exportDate = new Date().toLocaleDateString('fr-FR');
        doc.text('Exporté le: ' + exportDate, 14, 32);

        // Préparation des données du tableau
        const table = document.getElementById('livre-caisse-table');
        const headers = [];
        const rows = [];

        // Récupération des en-têtes
        table.querySelectorAll('thead th').forEach(th => {
            headers.push(th.textContent.trim());
        });

        // Récupération des données
        table.querySelectorAll('tbody tr').forEach(tr => {
            const row = [];
            tr.querySelectorAll('td').forEach(td => {
                row.push(td.textContent.trim());
            });
            if (row.length > 0) {
                rows.push(row);
            }
        });

        // Ajout des totaux
        rows.push(['TOTAUX', '', '',
            document.getElementById('total-entree').textContent,
            document.getElementById('total-sortie').textContent,
            document.getElementById('solde-final').textContent
        ]);

        // Génération du tableau PDF
        doc.autoTable({
            head: [headers],
            body: rows,
            startY: 40,
            styles: {
                fontSize: 9,
                cellPadding: 3
            },
            headStyles: {
                fillColor: [52, 152, 219],
                textColor: 255,
                fontStyle: 'bold'
            },
            alternateRowStyles: {
                fillColor: [248, 249, 250]
            },
            margin: { top: 40 }
        });

        // Sauvegarde du PDF
        doc.save('livre_caisse_<?php echo date('Y-m-d'); ?>.pdf');
    }

    // Fonction d'exportation Excel
    function exportToExcel() {
        // Préparation des données
        const data = [];
        const headers = [];

        // Récupération des en-têtes
        document.querySelectorAll('#livre-caisse-table thead th').forEach(th => {
            headers.push(th.textContent.trim());
        });
        data.push(headers);

        // Récupération des données
        document.querySelectorAll('#livre-caisse-table tbody tr').forEach(tr => {
            const row = [];
            tr.querySelectorAll('td').forEach(td => {
                row.push(td.textContent.trim());
            });
            if (row.length > 0) {
                data.push(row);
            }
        });

        // Ajout des totaux
        data.push(['TOTAUX', '', '',
            document.getElementById('total-entree').textContent,
            document.getElementById('total-sortie').textContent,
            document.getElementById('solde-final').textContent
        ]);

        // Création du workbook
        const ws = XLSX.utils.aoa_to_sheet(data);
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Livre de Caisse');

        // Style pour les en-têtes
        if (!ws['!cols']) ws['!cols'] = [];
        headers.forEach((_, i) => {
            ws['!cols'][i] = { width: i === 2 ? 25 : 15 }; // Colonne désignation plus large
        });

        // Sauvegarde du fichier Excel
        XLSX.writeFile(wb, 'livre_caisse_<?php echo date('Y-m-d'); ?>.xlsx');
    }

    // Initialisation DataTable
    $(document).ready(function() {
        initDatatable('expense-list','report/getcaisselistbydt');
    });

    // Gestion du formulaire
    $(document).on('submit','#reportform',function(e){
        e.preventDefault();
        var $this = $(this).find("button[type=submit]:focus");
        var form = $(this);
        var url = form.attr('action');
        var form_data = form.serializeArray();

        $.ajax({
            url: url,
            type: "POST",
            dataType:'JSON',
            data: form_data,
            beforeSend: function () {
                $('[id^=error]').html("");
                $this.button('loading');
            },
            success: function(response) {
                if(!response.status){
                    $.each(response.error, function(key, value) {
                        $('#error_' + key).html(value);
                    });
                } else {
                    initDatatable('expense-list','report/getcaisselistbydt',response.params);
                }
            },
            error: function() {
                $this.button('reset');
            },
            complete: function() {
                $this.button('reset');
            }
        });
    });
</script>