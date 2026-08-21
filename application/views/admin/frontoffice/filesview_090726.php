<!-- ============================================================
     PAGE : Liste des courriers (design modernisé)
     ============================================================ -->

<style>
    /* ===== STYLES GÉNÉRAUX ===== */
    .card-modern {
        border: none;
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
        background: #ffffff;
        margin-bottom: 30px;
        overflow: hidden;
    }
    .card-modern .card-header {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        padding: 20px 24px;
        border-bottom: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .card-modern .card-header h3 {
        color: #ffffff;
        font-size: 20px;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .card-modern .card-header h3 i {
        color: #60a5fa;
    }
    .card-modern .card-body {
        padding: 24px;
        background: #fafcff;
    }

    /* Barre de filtres */
    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
        padding: 15px 20px;
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
    }
    .filter-bar .filter-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .filter-bar .filter-group label {
        font-weight: 500;
        color: #475569;
        font-size: 14px;
        margin: 0;
    }
    .filter-bar .filter-group select,
    .filter-bar .filter-group input {
        border-radius: 30px;
        border: 1px solid #e2e8f0;
        padding: 6px 16px 6px 12px;
        font-size: 14px;
        background: white;
        color: #1e293b;
        height: 38px;
        min-width: 130px;
        transition: border-color 0.2s;
    }
    .filter-bar .filter-group select:focus,
    .filter-bar .filter-group input:focus {
        border-color: #3b82f6;
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .filter-bar .search-group {
        flex: 1;
        min-width: 200px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .filter-bar .search-group input {
        flex: 1;
        border-radius: 30px;
        border: 1px solid #e2e8f0;
        padding: 6px 18px;
        font-size: 14px;
        height: 38px;
        background: white;
        transition: border-color 0.2s;
    }
    .filter-bar .search-group input:focus {
        border-color: #3b82f6;
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .filter-bar .info-auto {
        color: #64748b;
        font-size: 13px;
        font-style: italic;
        margin-left: auto;
    }

    /* Tableau modernisé */
    .table-modern {
        border-collapse: separate;
        border-spacing: 0 6px;
        width: 100%;
        margin-bottom: 0;
    }
    .table-modern thead th {
        background: #f1f5f9;
        color: #1e293b;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 16px;
        border: none;
        border-bottom: 2px solid #e2e8f0;
    }
    .table-modern tbody td {
        background: #ffffff;
        padding: 12px 16px;
        border: none;
        border-bottom: 1px solid #eef2f6;
        vertical-align: middle;
        font-size: 14px;
        color: #1e293b;
    }
    .table-modern tbody tr:hover td {
        background: #f8fafc;
        transition: background 0.15s ease;
    }
    .table-modern tbody tr:last-child td {
        border-bottom: none;
    }

    /* Boutons d'action (dropdown) */
    .btn-action-dropdown {
        background: transparent;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 6px 14px;
        color: #475569;
        font-size: 13px;
        transition: all 0.2s;
    }
    .btn-action-dropdown:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }
    .btn-action-dropdown .caret {
        margin-left: 6px;
    }
    .dropdown-menu.actions-menu {
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        padding: 6px 0;
        min-width: 160px;
    }
    .dropdown-menu.actions-menu li a {
        padding: 8px 20px;
        font-size: 14px;
        color: #1e293b;
        transition: background 0.15s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .dropdown-menu.actions-menu li a:hover {
        background: #f1f5f9;
    }
    .dropdown-menu.actions-menu li a.text-danger:hover {
        background: #fef2f2;
        color: #dc2626;
    }

    /* Boutons "Retour" et "Ajouter" */
    .btn-back {
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 30px;
        padding: 6px 18px;
        font-size: 14px;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-back:hover {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
        border-color: rgba(255, 255, 255, 0.5);
        text-decoration: none;
    }
    .btn-add-modern {
        background: #3b82f6;
        border: none;
        color: #fff;
        font-weight: 500;
        border-radius: 30px;
        padding: 8px 22px;
        font-size: 13px;
        transition: all 0.2s;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    .btn-add-modern:hover {
        background: #2563eb;
        color: #fff;
        box-shadow: 0 6px 18px rgba(59, 130, 246, 0.4);
        transform: translateY(-2px);
    }

    /* Conteneur des boutons d'export */
    .export-buttons {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        margin-bottom: 15px;
    }
    .export-buttons .btn {
        border-radius: 30px;
        padding: 5px 16px;
        font-size: 13px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }
    .export-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .export-buttons .btn-excel {
        background: #1e7e34;
        color: #fff;
        border: 1px solid #1e7e34;
    }
    .export-buttons .btn-excel:hover {
        background: #146c2a;
        border-color: #146c2a;
    }
    .export-buttons .btn-pdf {
        background: #c9302c;
        color: #fff;
        border: 1px solid #c9302c;
    }
    .export-buttons .btn-pdf:hover {
        background: #b52b27;
        border-color: #b52b27;
    }
    .export-buttons .btn-copy {
        background: #6c757d;
        color: #fff;
        border: 1px solid #6c757d;
    }
    .export-buttons .btn-copy:hover {
        background: #5a6268;
        border-color: #5a6268;
    }
    .export-buttons .btn-print {
        background: #0d6efd;
        color: #fff;
        border: 1px solid #0d6efd;
    }
    .export-buttons .btn-print:hover {
        background: #0b5ed7;
        border-color: #0b5ed7;
    }

    @media (max-width: 768px) {
        .card-modern .card-header {
            flex-direction: column;
            align-items: stretch;
        }
        .filter-bar {
            flex-direction: column;
            align-items: stretch;
        }
        .filter-bar .filter-group,
        .filter-bar .search-group {
            width: 100%;
        }
        .filter-bar .filter-group select,
        .filter-bar .search-group input {
            width: 100%;
        }
        .filter-bar .info-auto {
            margin-left: 0;
            text-align: center;
        }
        .export-buttons {
            justify-content: center;
        }
        .table-modern thead th,
        .table-modern tbody td {
            padding: 10px 12px;
            font-size: 13px;
        }
        .btn-add-modern {
            width: 100%;
            text-align: center;
        }
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-ioxhost"></i> <?php echo $this->lang->line('front_office'); ?></h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card-modern">
                    <!-- En-tête -->
                    <div class="card-header">
                        <h3><i class="fa fa-envelope"></i> Liste des courriers</h3>
                        <div class="d-flex align-items-center gap-3" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <a href="<?php echo base_url(); ?>admin/admin/hub" class="btn-back" title="Retour au hub">
                                <i class="fa fa-arrow-left"></i> Retour
                            </a>
                            <?php if ($this->rbac->hasPrivilege('file', 'can_add')) : ?>
                                <button type="button" class="btn-add-modern" data-toggle="modal" data-target="#addCourierModal">
                                    <i class="fa fa-plus"></i> Ajouter un courrier
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Messages flash -->
                        <?php if ($this->session->flashdata('msg')) : ?>
                            <div class="alert alert-success alert-dismissible fade in">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <i class="fa fa-check-circle"></i> <?php echo $this->session->flashdata('msg'); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($this->session->flashdata('error')) : ?>
                            <div class="alert alert-danger alert-dismissible fade in">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <i class="fa fa-exclamation-circle"></i> <?php echo $this->session->flashdata('error'); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Barre de filtres -->
                        <div class="filter-bar">
                            <div class="filter-group">
                                <label for="purposeFilter">Type</label>
                                <select id="purposeFilter" class="form-control">
                                    <option value="">TOUS</option>
                                    <?php
                                    $purposes = array_unique(array_column($visitor_list, 'purpose'));
                                    foreach ($purposes as $purpose) {
                                        if (!empty($purpose)) {
                                            echo '<option value="' . htmlspecialchars($purpose) . '">' . htmlspecialchars($purpose) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="search-group">
                                <input type="text" id="searchInput" class="form-control" placeholder="Rechercher...">
                            </div>
                            <div class="info-auto">
                                <i class="fa fa-info-circle"></i> La liste se met à jour automatiquement
                            </div>
                        </div>

                        <!-- Conteneur des boutons d'export (rempli par DataTables) -->
                        <div class="export-buttons" id="exportButtonsContainer"></div>

                        <!-- Tableau -->
                        <div class="table-responsive">
                            <table class="table table-modern" id="dataTable">
                                <thead>
                                <tr>
                                    <th>Type de courrier</th>
                                    <th><?php echo $this->lang->line('name'); ?></th>
                                    <th>Référence</th>
                                    <th><?php echo $this->lang->line('date'); ?></th>
                                    <th>Remarque</th>
                                    <th>Adresse</th>
                                    <th class="text-center noExport" style="width: 100px;"><?php echo $this->lang->line('action'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($visitor_list)) : ?>
                                    <?php foreach ($visitor_list as $value) : ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($value['purpose']); ?></td>
                                            <td><?php echo htmlspecialchars($value['name']); ?></td>
                                            <td><?php echo htmlspecialchars($value['contact']); ?></td>
                                            <td><?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value['date'])); ?></td>
                                            <td><?php echo htmlspecialchars($value['note']); ?></td>
                                            <td><?php echo htmlspecialchars($value['id_proof']); ?></td>
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <button class="btn-action-dropdown dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                                        Actions <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu actions-menu dropdown-menu-right">
                                                        <li><a href="#" onclick="getRecord(<?php echo $value['id']; ?>); return false;"><i class="fa fa-reorder" style="color: #3b82f6;"></i> Voir</a></li>
                                                        <?php if (!empty($value['image'])) : ?>
                                                            <li><a href="<?php echo base_url(); ?>admin/files/download/<?php echo $value['image']; ?>"><i class="fa fa-download" style="color: #10b981;"></i> Télécharger</a></li>
                                                        <?php endif; ?>
                                                        <?php if ($this->rbac->hasPrivilege('section', 'can_edit')) : ?>
                                                            <li><a href="<?php echo base_url(); ?>admin/files/edit/<?php echo $value['id']; ?>"><i class="fa fa-pencil" style="color: #f59e0b;"></i> Modifier</a></li>
                                                        <?php endif; ?>
                                                        <?php if ($this->rbac->hasPrivilege('section', 'can_delete')) : ?>
                                                            <?php
                                                            $deleteUrl = base_url('admin/files/delete/' . $value['id']);
                                                            if (!empty($value['image'])) {
                                                                $deleteUrl = base_url('admin/files/imagesdelete/' . $value['id'] . '/' . $value['image']);
                                                            }
                                                            ?>
                                                            <li>
                                                                <a href="#" class="text-danger delete-courier"
                                                                   data-url="<?php echo $deleteUrl; ?>"
                                                                   data-name="<?php echo htmlspecialchars($value['name']); ?>">
                                                                    <i class="fa fa-trash"></i> Supprimer
                                                                </a>
                                                            </li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr><td colspan="7" class="text-center text-muted">Aucun courrier enregistré</td></tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Ajouter un courrier (identique à l’existant) -->
<div id="addCourierModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #1e293b, #0f172a); color: white; border-radius: 12px 12px 0 0;">
                <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 0.8;">&times;</button>
                <h4 class="modal-title"><i class="fa fa-plus-circle"></i> Ajouter un courrier</h4>
            </div>
            <form id="form1" action="<?php echo site_url('admin/files') ?>" method="post" enctype="multipart/form-data">
                <div class="modal-body" style="padding: 24px;">
                    <?php echo $this->session->flashdata('msg'); ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Type de courrier <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="purpose" value="<?php echo set_value('purpose'); ?>" placeholder="Ex: Reçu, Facture, ...">
                                <?php echo form_error('purpose', '<span class="text-danger">', '</span>'); ?>
                            </div>
                            <div class="form-group">
                                <label><?php echo $this->lang->line('name'); ?> <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" value="<?php echo set_value('name'); ?>" placeholder="Nom complet">
                                <?php echo form_error('name', '<span class="text-danger">', '</span>'); ?>
                            </div>
                            <div class="form-group">
                                <label>Référence</label>
                                <input type="text" class="form-control" name="contact" value="<?php echo set_value('contact'); ?>" placeholder="Numéro de référence">
                            </div>
                            <div class="form-group">
                                <label>Adresse</label>
                                <input type="text" class="form-control" name="id_proof" value="<?php echo set_value('id_proof'); ?>" placeholder="Adresse complète">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" hidden>
                                <label><?php echo $this->lang->line('number_of_person'); ?></label>
                                <input type="text" class="form-control" name="pepples" value="<?php echo set_value('pepples'); ?>">
                            </div>
                            <div class="form-group">
                                <label><?php echo $this->lang->line('date'); ?></label>
                                <input type="text" class="form-control date" name="date" value="<?php echo set_value('date', date($this->customlib->getSchoolDateFormat())); ?>" readonly>
                                <?php echo form_error('date', '<span class="text-danger">', '</span>'); ?>
                            </div>
                            <div class="form-group" hidden>
                                <label><?php echo $this->lang->line('in_time'); ?></label>
                                <div class="input-group">
                                    <input type="text" name="time" class="form-control timepicker" value="<?php echo set_value('time'); ?>">
                                    <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                                </div>
                            </div>
                            <div class="form-group" hidden>
                                <label><?php echo $this->lang->line('out_time'); ?></label>
                                <div class="input-group">
                                    <input type="text" name="out_time" class="form-control timepicker" value="<?php echo set_value('out_time'); ?>">
                                    <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label><?php echo $this->lang->line('note'); ?></label>
                                <textarea class="form-control" name="note" rows="3" placeholder="Notes..."><?php echo set_value('note'); ?></textarea>
                            </div>
                            <div class="form-group">
                                <label><?php echo $this->lang->line('attach_document'); ?></label>
                                <input class="form-control" type="file" name="file" />
                                <?php echo form_error('file', '<span class="text-danger">', '</span>'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #e2e8f0; padding: 16px 24px;">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Fermer</button>
                    <button type="reset" class="btn btn-warning"><i class="fa fa-refresh"></i> Réinitialiser</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> <?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Détails -->
<div id="visitordetails" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: #1e293b; color: white;">
                <button type="button" class="close" data-dismiss="modal" style="color: white;">&times;</button>
                <h4 class="modal-title"><i class="fa fa-info-circle"></i> <?php echo $this->lang->line('details'); ?></h4>
            </div>
            <div class="modal-body" id="getdetails"></div>
        </div>
    </div>
</div>

<!-- ============================================================
     DEPENDANCES ET SCRIPTS
     ============================================================ -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>backend/plugins/timepicker/bootstrap-timepicker.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
<script src="<?php echo base_url(); ?>backend/plugins/timepicker/bootstrap-timepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
    $(function () {
        $(".timepicker").timepicker({});
    });

    function getRecord(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/files/details/' + id,
            success: function (result) {
                $('#getdetails').html(result);
                $('#visitordetails').modal('show');
            }
        });
    }

    $(document).ready(function () {
        // --- Initialisation de DataTable ---
        var table = $('#dataTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'copy',
                    className: 'btn btn-sm btn-copy',
                    text: '<i class="fa fa-copy"></i> Copier',
                    exportOptions: { columns: ':not(.noExport)' } // exclut la colonne Action
                },
                {
                    extend: 'csv',
                    className: 'btn btn-sm btn-copy',
                    text: '<i class="fa fa-file-text-o"></i> CSV',
                    exportOptions: { columns: ':not(.noExport)' }
                },
                {
                    extend: 'excel',
                    className: 'btn btn-sm btn-excel',
                    text: '<i class="fa fa-file-excel-o"></i> Excel',
                    exportOptions: { columns: ':not(.noExport)' }
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-sm btn-pdf',
                    text: '<i class="fa fa-file-pdf-o"></i> PDF',
                    exportOptions: { columns: ':not(.noExport)' }
                },
                {
                    extend: 'print',
                    className: 'btn btn-sm btn-print',
                    text: '<i class="fa fa-print"></i> Imprimer',
                    exportOptions: { columns: ':not(.noExport)' }
                }
            ],
            language: {
                processing: "Traitement en cours...",
                search: "Rechercher :",
                lengthMenu: "Afficher _MENU_ éléments",
                info: "Affichage de _START_ à _END_ sur _TOTAL_ éléments",
                infoEmpty: "Affichage de 0 à 0 sur 0 élément",
                infoFiltered: "(filtré de _MAX_ éléments au total)",
                loadingRecords: "Chargement...",
                zeroRecords: "Aucun élément correspondant trouvé",
                emptyTable: "Aucune donnée disponible",
                paginate: {
                    first: "Premier",
                    previous: "Précédent",
                    next: "Suivant",
                    last: "Dernier"
                },
                buttons: {
                    copy: "Copier",
                    csv: "CSV",
                    excel: "Excel",
                    pdf: "PDF",
                    print: "Imprimer"
                }
            },
            // On désactive le tri sur la colonne Action
            columnDefs: [
                { orderable: false, targets: -1 }
            ]
        });

        // Déplacer les boutons dans le conteneur dédié
        $('.dt-buttons').appendTo('#exportButtonsContainer');

        // --- Filtres ---
        $('#purposeFilter').on('change', function() {
            var val = $(this).val();
            table.column(0).search(val).draw();
        });

        $('#searchInput').on('keyup', function() {
            table.search(this.value).draw();
        });

        // --- Réouverture de la modal en cas d'erreur de validation ---
        <?php if (validation_errors() && !$this->input->get('delete')) : ?>
        $('#addCourierModal').modal('show');
        <?php endif; ?>

        // --- Suppression avec SweetAlert2 ---
        $(document).on('click', '.delete-courier', function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var name = $(this).data('name');

            Swal.fire({
                title: 'Confirmation',
                text: 'Supprimer définitivement le courrier de "' + name + '" ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });
</script>