<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();

?>
<?php
$moisEn = [
    'January' => 'Janvier',
    'February' => 'Février',
    'March' => 'Mars',
    'April' => 'Avril',
    'May' => 'Mai',
    'June' => 'Juin',
    'July' => 'Juillet',
    'August' => 'Août',
    'September' => 'Septembre',
    'October' => 'Octobre',
    'November' => 'Novembre',
    'December' => 'Décembre',
];

?>
<?php
$compte= $value["compte_id"];

if($compte = 571)
{
    $compte="caisse";
}
else{
    $compte=0;
}
if($compte = 780){
    $compte = "aca";
}
else{
    $compte=0;
}

$comptes= $compte;

?>
<style type="text/css">
    /*REQUIRED*/
    .carousel-row {
        margin-bottom: 10px;
    }
    .text-primary{
        color: black;
        text-transform: uppercase;
    }
    .slide-row {
        padding: 0;
        background-color: #ffffff;
        min-height: 150px;
        border: 1px solid #e7e7e7;
        overflow: hidden;
        height: auto;
        position: relative;
    }
    .slide-carousel {
        width: 20%;
        float: left;
        display: inline-block;
    }
    .slide-carousel .carousel-indicators {
        margin-bottom: 0;
        bottom: 0;
        background: rgba(0, 0, 0, .5);
    }
    .slide-carousel .carousel-indicators li {
        border-radius: 0;
        width: 20px;
        height: 6px;
    }
    .slide-carousel .carousel-indicators .active {
        margin: 1px;
    }
    .slide-content {
        position: absolute;
        top: 0;
        left: 20%;
        display: block;
        float: left;
        width: 80%;
        max-height: 76%;
        padding: 1.5% 2% 2% 2%;
        overflow-y: auto;
    }
    .slide-content h4 {
        margin-bottom: 3px;
        margin-top: 0;
    }
    .slide-footer {
        position: absolute;
        bottom: 0;
        left: 20%;
        width: 78%;
        height: 20%;
        margin: 1%;
    }
    /* Scrollbars */
    .slide-content::-webkit-scrollbar {
        width: 5px;
    }
    .slide-content::-webkit-scrollbar-thumb:vertical {
        margin: 5px;
        background-color: #999;
        -webkit-border-radius: 5px;
    }
    .slide-content::-webkit-scrollbar-button:start:decrement,
    .slide-content::-webkit-scrollbar-button:end:increment {
        height: 5px;
        display: block;
    }

    /* Styles supplémentaires pour DataTable */
    .dt-buttons .btn {
        margin-right: 5px;
        border-radius: 4px;
    }
    .dataTables_wrapper {
        margin-top: 10px;
    }
    .date-filter-container {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 15px;
        border-left: 4px solid #007bff;
    }
    .date-filter-container label {
        font-weight: bold;
        margin-bottom: 5px;
    }
</style>

<!-- Ajout des CDN pour DataTable et les boutons d'export -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/datetime/1.5.1/css/dataTables.dateTime.min.css"/>

<div class="content-wrapper" style="min-height: 946px;">

    <section class="content-header">
        <h1>
            <i class="fa fa-bus"></i> <?php echo $this->lang->line('transport'); ?></h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <?php $this->load->view('reports/_finance'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="box removeboxmius">
                    <div class="box-header ptbnull"></div>
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>

                    <form role="form" action="<?php echo site_url('report/recapt_facture_client') ?>" method="post" class="">
                        <div class="box-body row">

                            <?php echo $this->customlib->getCSRF(); ?>

                            <div class="col-sm-6 col-md-3" >
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('search') . " " . $this->lang->line('type'); ?></label>
                                    <select class="form-control" name="search_type" onchange="showdate(this.value)">

                                        <?php foreach ($searchlist as $key => $search) {
                                            ?>
                                            <option value="<?php echo $key ?>" <?php
                                            if ((isset($search_type)) && ($search_type == $key)) {

                                                echo "selected";
                                            }
                                            ?>><?php echo $search ?></option>
                                        <?php } ?>
                                    </select>
                                    <span class="text-danger"><?php echo form_error('search_type'); ?></span>
                                </div>
                            </div>

                            <div id='date_result'>

                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm checkbox-toggle pull-right"><i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?></button>
                                </div>
                            </div>
                        </div>
                    </form>


                    <div class="">
                        <div class="box-header ptbnull"></div>
                        <div class="box-header ptbnull">
                            <h3 class="box-title titlefix"><i class="fa fa-money"></i><b style="color: black">RECAPT FACTURE CLIENT</b></h3>
                        </div>
                        <div class="box-body table-responsive">
                            <div class="download_label">
                                <div class="col-md-4 col-xs-2 col-sm-6">
                                    <img style="width: 150px; height: 70px !important;" src="<?= base_url() . "/uploads/school_content/admin_logo/" . $sch_setting->admin_logo ?>" alt="Image banniere" />
                                </div>
                                <br/><br/><br/><br/>
                                <?php
                                echo "RECAPT FACTURE CLIENT <br/><br/>";
                                echo "period:"; $this->customlib->get_postmessage();
                                ;
                                ?></div>

                            <!-- Filtre de date pour DataTable -->
                            <div class="date-filter-container">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="minDate">Date de début :</label>
                                        <input type="date" id="minDate" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="maxDate">Date de fin :</label>
                                        <input type="date" id="maxDate" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="clientFilter">Filtrer par client :</label>
                                        <input type="text" id="clientFilter" class="form-control form-control-sm" placeholder="Rechercher client...">
                                    </div>
                                    <div class="col-md-3">
                                        <label>&nbsp;</label><br>
                                        <button type="button" id="resetFilters" class="btn btn-warning btn-sm">
                                            <i class="fa fa-refresh"></i> Réinitialiser
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Table avec ID pour DataTable -->
                            <table id="facturesTable" class="table table-striped table-bordered table-hover" style="width:100%">
                                <thead>
                                <tr>
                                    <th class="text text-left text-primary">Date</th>
                                    <th class="text text-left text-primary">Client</th>
                                    <th class="text text-left text-primary">Montant total</th>
                                    <th class="text text-left text-primary">Montant payé</th>
                                    <th class="text text-left text-primary">Reste à payé</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $date = 0;
                                $reference = 0;
                                $montant_paye = 0;
                                $montant_resta = 0;
                                $montant_tt = 0;

                                if (empty($bill)) {
                                    ?>
                                    <tr>
                                        <td colspan="5" class="text-center">Aucune donnée disponible</td>
                                    </tr>
                                    <?php
                                } else {
                                    $count = 1;
                                    foreach ($bill as $key => $value) {
                                        $date += $value["date"];
                                        $reference += $value["customer_name"];
                                        $montant_tt += $value["total_ttc"];
                                        $montant_paye += $value["amount_paid"];
                                        $montant_resta += $value["remaining_amount"];
                                        ?>
                                        <tr>
                                            <td data-order="<?php echo strtotime($value["date"]); ?>">
                                                <?php echo date('d/m/Y', strtotime($value["date"])); ?>
                                            </td>
                                            <td><?php echo $value["customer_name"] ?></td>
                                            <td><?php echo number_format($value["total_ttc"], 2, ',', ' ') ?></td>
                                            <td><?php echo number_format($value["amount_paid"], 2, ',', ' ') ?></td>
                                            <td><?php echo number_format($value["remaining_amount"], 2, ',', ' ') ?></td>
                                        </tr>
                                        <?php
                                        $count++;
                                    }
                                    ?>
                                <?php } ?>
                                </tbody>
                                <tfoot>
                                <tr class="box box-solid total-bg">
                                    <td></td>
                                    <td class="text text-left text-primary"><?php echo $this->lang->line('grand_total'); ?> </td>
                                    <td><?php echo (number_format($montant_tt, 2, ',', ' ')); ?> </td>
                                    <td><?php echo (number_format($montant_paye, 2, ',', ' ')); ?></td>
                                    <td><?php echo (number_format($montant_resta, 2, ',', ' ')); ?></td>
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

<!-- Scripts pour DataTable et export -->
<script type="text/javascript" src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.2/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/datetime/1.5.1/js/dataTables.dateTime.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialisation du DataTable avec les boutons d'export
        var table = $('#facturesTable').DataTable({
            dom: '<"row"<"col-md-6"B><"col-md-6"f>>rtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    className: 'btn btn-success btn-sm',
                    title: 'Recapitulatif_Factures_Clients',
                    messageTop: function() {
                        return $('.download_label').html();
                    },
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    },
                    customize: function(xlsx) {
                        var sheet = xlsx.xl.worksheets['sheet1.xml'];
                        // Appliquer un format monétaire aux colonnes de montants
                        $('row c[r^="C"]', sheet).attr('s', '5'); // Format numérique
                        $('row c[r^="D"]', sheet).attr('s', '5');
                        $('row c[r^="E"]', sheet).attr('s', '5');
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fa fa-file-pdf-o"></i> PDF',
                    className: 'btn btn-danger btn-sm',
                    title: 'Recapitulatif Factures Clients',
                    messageTop: function() {
                        return $('.download_label').html();
                    },
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    },
                    customize: function(doc) {
                        // Personnalisation du PDF
                        doc.defaultStyle.fontSize = 10;
                        doc.styles.tableHeader.fontSize = 11;
                        doc.styles.tableHeader.fillColor = '#2c3e50';
                        doc.content[1].table.widths = ['20%', '30%', '16%', '17%', '17%'];

                        // Ajouter le total en bas du PDF
                        if (doc.content[1].table.body.length > 0) {
                            var totalRow = [
                                {text: '', style: 'tableHeader'},
                                {text: 'TOTAL GENERAL', style: 'tableHeader'},
                                {text: '<?php echo number_format($montant_tt, 2, ',', ' '); ?>', style: 'tableHeader'},
                                {text: '<?php echo number_format($montant_paye, 2, ',', ' '); ?>', style: 'tableHeader'},
                                {text: '<?php echo number_format($montant_resta, 2, ',', ' '); ?>', style: 'tableHeader'}
                            ];
                            doc.content[1].table.body.push(totalRow);
                        }
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Imprimer',
                    className: 'btn btn-info btn-sm',
                    title: 'Recapitulatif Factures Clients',
                    messageTop: function() {
                        return $('.download_label').html();
                    },
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4]
                    }
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
            },
            ordering: true,
            searching: true,
            paging: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tous"]],
            order: [[0, 'desc']], // Tri par date décroissante par défaut
            footerCallback: function (row, data, start, end, display) {
                var api = this.api();

                // Total sur toutes les pages
                var total = api.column(2, {page: 'all'}).data().reduce(function (a, b) {
                    return a + parseFloat(b.replace(/[^\d,]/g, '').replace(',', '.'));
                }, 0);

                var paye = api.column(3, {page: 'all'}).data().reduce(function (a, b) {
                    return a + parseFloat(b.replace(/[^\d,]/g, '').replace(',', '.'));
                }, 0);

                var reste = api.column(4, {page: 'all'}).data().reduce(function (a, b) {
                    return a + parseFloat(b.replace(/[^\d,]/g, '').replace(',', '.'));
                }, 0);

                // Mettre à jour le footer avec les totaux filtrés
                $(api.column(2).footer()).html(numberFormat(total));
                $(api.column(3).footer()).html(numberFormat(paye));
                $(api.column(4).footer()).html(numberFormat(reste));
            }
        });

        // Fonction pour formater les nombres
        function numberFormat(number) {
            return new Intl.NumberFormat('fr-FR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(number);
        }

        // Filtre par date
        $('#minDate, #maxDate').on('change', function() {
            var minDate = $('#minDate').val();
            var maxDate = $('#maxDate').val();

            if (minDate || maxDate) {
                table.draw();
            }
        });

        // Filtre par client
        $('#clientFilter').on('keyup', function() {
            table.column(1).search(this.value).draw();
        });

        // Réinitialiser les filtres
        $('#resetFilters').on('click', function() {
            $('#minDate').val('');
            $('#maxDate').val('');
            $('#clientFilter').val('');
            table.search('').columns().search('').draw();
        });

        // Fonction de filtrage personnalisée pour les dates
        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                var min = $('#minDate').val();
                var max = $('#maxDate').val();
                var date = data[0]; // La date est dans la première colonne

                if (min === '' && max === '') {
                    return true;
                }

                if (min === '' && max !== '') {
                    return (date <= max);
                }

                if (min !== '' && max === '') {
                    return (date >= min);
                }

                return (date >= min && date <= max);
            }
        );
    });

    <?php
    if ($search_type == 'period') {
    ?>
    $(document).ready(function () {
        showdate('period');
    });
    <?php
    }
    ?>

    function showdate(type) {
        // Ta fonction existante pour les dates
    }
</script>