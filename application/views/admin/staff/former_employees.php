<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<style type="text/css">
    .label-success {
        background-color: #00a65a;
    }
    .label-danger {
        background-color: #dd4b39;
    }
    .label-warning {
        background-color: #f39c12;
    }
    .label {
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 12px;
    }
    .box-header h3.box-title i {
        margin-right: 5px;
    }
    .former-employee-badge {
        background-color: #605ca8;
        color: white;
        padding: 3px 8px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: normal;
    }
    .table-striped > tbody > tr.former-row {
        background-color: #f9f9fc;
    }
    .date-leaving-badge {
        background-color: #e08e0b;
        color: white;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 11px;
    }
    .btn-reinstate {
        background-color: #27ae60;
        border-color: #27ae60;
        color: white;
    }
    .btn-reinstate:hover {
        background-color: #229954;
        border-color: #229954;
        color: white;
    }
    .reinstate-modal .modal-header {
        background-color: #27ae60;
        color: white;
        border-radius: 5px 5px 0 0;
    }
    .reinstate-modal .modal-header .close {
        color: white;
        opacity: 0.8;
    }
    .reinstate-modal .modal-header .close:hover {
        opacity: 1;
    }
    /* Styles pour les boutons d'export */
    .btn-export {
        margin-right: 5px;
    }
    .export-tools {
        margin-bottom: 15px;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .btn-pdf {
        background-color: #f56954;
        border-color: #f56954;
        color: white;
    }
    .btn-pdf:hover {
        background-color: #d6543a;
        border-color: #d6543a;
        color: white;
    }
    .btn-excel {
        background-color: #00a65a;
        border-color: #00a65a;
        color: white;
    }
    .btn-excel:hover {
        background-color: #008d4c;
        border-color: #008d4c;
        color: white;
    }
    .btn-print {
        background-color: #3c8dbc;
        border-color: #3c8dbc;
        color: white;
    }
    .btn-print:hover {
        background-color: #337ab7;
        border-color: #337ab7;
        color: white;
    }
    /* Styles pour l'impression */
    @media print {
        .no-print, .btn, .box-tools, .modal, .dataTables_length, .dataTables_filter, .dataTables_paginate {
            display: none !important;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
    }
</style>

<div class="content-wrapper" style="min-height: 946px;">
    <section class="content-header">
        <h1>
            <i class="fa fa-sitemap"></i> <?php echo $this->lang->line('human_resource'); ?>
            <small class="pull-right">
                <a href="<?php echo base_url(); ?>admin/staff" class="btn btn-primary btn-sm">
                    <i class="fa fa-arrow-left"></i> Retour à la liste des employés
                </a>
            </small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-history"></i> Anciens employés
                            <span class="former-employee-badge"><?php echo count($resultlist); ?> ancien(s)</span>
                        </h3>
                        <div class="box-tools pull-right">
                            <small class="pull-right" style="margin-right: 121px">
                                <i class="fa fa-info-circle text-info"></i>
                                Employés ayant quitté l'entreprise
                            </small>
                        </div>
                    </div>

                    <div class="box-body">
                        <!-- OUTILS D'EXPORTATION ET D'IMPRESSION -->
                        <div class="export-tools no-print">
                            <button type="button" class="btn btn-pdf btn-sm btn-export" onclick="exportToPDF()">
                                <i class="fa fa-file-pdf-o"></i> Exporter en PDF
                            </button>
                            <button type="button" class="btn btn-excel btn-sm btn-export" onclick="exportToExcel()">
                                <i class="fa fa-file-excel-o"></i> Exporter en Excel
                            </button>
                            <button type="button" class="btn btn-print btn-sm btn-export" onclick="window.print()">
                                <i class="fa fa-print"></i> Imprimer
                            </button>
                        </div>

                        <?php if ($this->session->flashdata('msg')) { ?>
                            <?php echo $this->session->flashdata('msg'); ?>
                        <?php } ?>

                        <?php if (isset($resultlist) && !empty($resultlist)) { ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="formerEmployeesTable" cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('staff_id'); ?></th>
                                        <th>Civilité</th>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('department'); ?></th>
                                        <th><?php echo $this->lang->line('designation'); ?></th>
                                        <th>Date de sortie</th>
                                        <th>Motif de départ</th>
                                        <th>Catégorie salariale</th>
                                        <th><?php echo $this->lang->line('mobile_no'); ?></th>
                                        <th>Statut</th>
                                        <?php if (!empty($fields)) {
                                            foreach ($fields as $fields_key => $fields_value) { ?>
                                                <th><?php echo $fields_value->name; ?></th>
                                            <?php }
                                        } ?>
                                        <th class="text-center no-print" style="width: 120px;"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (!empty($resultlist)) {
                                        foreach ($resultlist as $staff) {
                                            // 🔒 On saute (ignore) le Super Admin
                                            if (strtolower($staff['user_type']) === 'super admin') {
                                                continue;
                                            }
                                            ?>
                                            <tr class="former-row">
                                                <td><?php echo $staff['employee_id']; ?></td>
                                                <td>
                                                    <?php
                                                    $gender = strtolower($staff['gender']);
                                                    echo ($gender === 'male') ? 'Homme' : 'Femme';
                                                    ?>
                                                </td>
                                                <td>
                                                    <a href="<?php echo base_url(); ?>admin/staff/profile/<?php echo $staff['id']; ?>" class="no-print">
                                                        <?php echo $staff['name'] . " " . $staff['surname']; ?>
                                                    </a>
                                                    <span class="print-only" style="display: none;"><?php echo $staff['name'] . " " . $staff['surname']; ?></span>
                                                </td>
                                                <td><?php echo $staff['department']; ?></td>
                                                <td><?php echo $staff['designation']; ?></td>
                                                <td>
                                                        <span class="date-leaving-badge">
                                                            <i class="fa fa-calendar-times-o"></i>
                                                            <?php echo $staff['date_of_leaving']; ?>
                                                        </span>
                                                </td>
                                                <td>
                                                    <?php
                                                    $reason = isset($staff['leaving_reason']) ? $staff['leaving_reason'] : 'Non spécifié';
                                                    echo $reason;
                                                    ?>
                                                </td>
                                                <td><?php echo $staff['categorie_salaire']; ?></td>
                                                <td><?php echo $staff['contact_no']; ?></td>
                                                <td>
                                                    <span class="label label-warning">Ancien</span>
                                                </td>
                                                <?php if (!empty($fields)) {
                                                    foreach ($fields as $fields_key => $fields_value) {
                                                        $display_field = $staff[$fields_value->name];
                                                        if ($fields_value->type == "link") {
                                                            $display_field = "<a href='" . $staff[$fields_value->name] . "' target='_blank' class='no-print'>" . $staff[$fields_value->name] . "</a><span class='print-only' style='display: none;'>" . $staff[$fields_value->name] . "</span>";
                                                        }
                                                        echo "<td>" . $display_field . "</td>";
                                                    }
                                                } ?>
                                                <td class="text-center no-print">
                                                    <div class="btn-group">
                                                        <a data-placement="left"
                                                           href="<?php echo base_url(); ?>admin/staff/profile/<?php echo $staff['id']; ?>"
                                                           class="btn btn-default btn-xs"
                                                           data-toggle="tooltip"
                                                           title="Voir le profil">
                                                            <i class="fa fa-eye"></i>
                                                        </a>

                                                        <!-- BOUTON RECONDUIRE -->
                                                        <button type="button"
                                                                class="btn btn-reinstate btn-xs"
                                                                data-toggle="modal"
                                                                data-target="#reinstateModal_<?php echo $staff['id']; ?>"
                                                                title="Reconduire l'employé">
                                                            <i class="fa fa-refresh"></i> Reconduire
                                                        </button>
                                                    </div>

                                                    <!-- Modal pour reconduire l'employé -->
                                                    <div class="modal fade reinstate-modal" id="reinstateModal_<?php echo $staff['id']; ?>" tabindex="-1" role="dialog">
                                                        <div class="modal-dialog" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                    <h4 class="modal-title">
                                                                        <i class="fa fa-refresh"></i> Reconduire l'employé
                                                                    </h4>
                                                                </div>
                                                                <form action="<?php echo base_url(); ?>admin/staff/reinstate/<?php echo $staff['id']; ?>" method="post">
                                                                    <div class="modal-body">
                                                                        <div class="form-group">
                                                                            <label for="new_joining_date">Nouvelle date d'entrée</label>
                                                                            <input type="date" class="form-control" name="new_joining_date"
                                                                                   value="<?php echo date('Y-m-d'); ?>" required>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="reason">Motif de la reconduction</label>
                                                                            <textarea class="form-control" name="reason" rows="3"
                                                                                      placeholder="Raison de la réintégration..."></textarea>
                                                                        </div>
                                                                        <div class="alert alert-info">
                                                                            <i class="fa fa-info-circle"></i>
                                                                            L'employé sera réactivé et pourra à nouveau accéder au système.
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                                                                        <button type="submit" class="btn btn-reinstate">
                                                                            <i class="fa fa-check"></i> Confirmer la reconduction
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <div class="alert alert-info text-center">
                                <i class="fa fa-info-circle"></i> Aucun ancien employé trouvé.
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Inclusion des bibliothèques nécessaires pour les exports -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

<script>
    $(document).ready(function() {
        // Activer les tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Initialiser DataTables avec options
        var table = $('#formerEmployeesTable').DataTable({
            "paging": true,
            "lengthChange": true,
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tous"]],
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/French.json"
            },
            "columnDefs": [
                { "orderable": false, "targets": [10, 11] } // Désactiver le tri sur les colonnes d'action et statut
            ]
        });
    });

    // Fonction d'exportation en PDF
    function exportToPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'mm', 'a4'); // Orientation paysage pour plus de colonnes

        // Titre du document
        doc.setFontSize(16);
        doc.text('Liste des anciens employés', 14, 15);
        doc.setFontSize(10);
        doc.text('Généré le : ' + new Date().toLocaleDateString('fr-FR'), 14, 22);

        // Récupérer les données du tableau
        var table = document.getElementById('formerEmployeesTable');
        var rows = [];
        var headers = [];

        // Récupérer les en-têtes (sans la colonne d'action)
        var headerCells = table.querySelectorAll('thead tr th');
        for (var i = 0; i < headerCells.length - 1; i++) { // Exclure la dernière colonne (action)
            headers.push(headerCells[i].innerText);
        }

        // Récupérer les données des lignes
        var bodyRows = table.querySelectorAll('tbody tr');
        for (var i = 0; i < bodyRows.length; i++) {
            var row = [];
            var cells = bodyRows[i].querySelectorAll('td');
            for (var j = 0; j < cells.length - 1; j++) { // Exclure la dernière colonne (action)
                // Nettoyer le contenu HTML pour n'avoir que le texte
                var cellText = cells[j].innerText.replace(/\n/g, ' ').trim();
                row.push(cellText);
            }
            rows.push(row);
        }

        // Générer le tableau dans le PDF
        doc.autoTable({
            head: [headers],
            body: rows,
            startY: 30,
            styles: { fontSize: 8 },
            headStyles: { fillColor: [96, 92, 168] },
            margin: { top: 30 }
        });

        // Sauvegarder le PDF
        doc.save('anciens_employes.pdf');
    }

    // Fonction d'exportation en Excel
    function exportToExcel() {
        // Récupérer le tableau
        var table = document.getElementById('formerEmployeesTable');

        // Cloner le tableau pour ne pas affecter l'original
        var cloneTable = table.cloneNode(true);

        // Supprimer la dernière colonne (action) du clone
        cloneTable.querySelectorAll('thead tr th:last-child, tbody tr td:last-child').forEach(function(el) {
            el.remove();
        });

        // Convertir en feuille de calcul
        var wb = XLSX.utils.table_to_book(cloneTable, {sheet: "Anciens employés"});

        // Sauvegarder le fichier Excel
        XLSX.writeFile(wb, 'anciens_employes.xlsx');
    }

    // Amélioration pour l'impression
    window.onbeforeprint = function() {
        // Cacher les éléments non imprimables
        document.querySelectorAll('.no-print').forEach(function(el) {
            el.style.display = 'none';
        });
        // Afficher les éléments pour l'impression
        document.querySelectorAll('.print-only').forEach(function(el) {
            el.style.display = 'inline';
        });
    };

    window.onafterprint = function() {
        // Restaurer l'affichage normal
        document.querySelectorAll('.no-print').forEach(function(el) {
            el.style.display = '';
        });
        document.querySelectorAll('.print-only').forEach(function(el) {
            el.style.display = 'none';
        });
    };
</script>

<!-- Styles supplémentaires pour l'impression -->
<style>
    @media print {
        .print-only {
            display: inline !important;
        }
        .no-print, .btn, .box-tools, .modal, .dataTables_length, .dataTables_filter, .dataTables_paginate, .dataTables_info {
            display: none !important;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 12px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 4px;
            text-align: left;
        }
        table th {
            background-color: #605ca8 !important;
            color: white !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .date-leaving-badge {
            background-color: #e08e0b !important;
            color: white !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
            padding: 2px 4px;
        }
        .label-warning {
            background-color: #f39c12 !important;
            color: white !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .table-striped > tbody > tr.former-row {
            background-color: #f9f9fc !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    }
</style>