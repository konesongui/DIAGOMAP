<div class="content-wrapper">
    <!-- En-tête de section avec titre clair -->
    <section class="content-header">
        <h1>
            <i class="fa fa-sitemap"></i>
            <?php echo $this->lang->line('human_resource'); ?>
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Filtres de recherche -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-search"></i>
                            <?php echo $this->lang->line('select_criteria'); ?>
                        </h3>
                    </div>

                    <!-- Messages flash -->
                    <?php if ($this->session->flashdata('msg')): ?>
                        <div class="col-md-12">
                            <?php echo $this->session->flashdata('msg'); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Formulaire de recherche -->
                    <form role="form" action="<?php echo site_url('admin/enquiry'); ?>" method="post">
                        <div class="box-body">
                            <?php echo $this->customlib->getCSRF(); ?>

                            <div class="row">
                                <!-- Champ source caché -->
                                <div class="col-sm-3 col-md-3" hidden>
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('source'); ?></label>
                                        <select id="source" name="source" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($sourcelist as $value): ?>
                                                <option value="<?php echo $value['source']; ?>" <?php echo ($value['source'] == $source_select) ? 'selected' : ''; ?>>
                                                    <?php echo $value['source']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('source'); ?></span>
                                    </div>
                                </div>

                                <!-- Date de début -->
                                <div class="col-sm-3 col-md-3">
                                    <div class="form-group">
                                        <label>
                                            <?php echo $this->lang->line('enquiry') . ' ' . $this->lang->line('from') . ' ' . $this->lang->line('date'); ?>
                                            <small class="text-muted">(AAAA-MM-JJ)</small>
                                        </label>
                                        <input type="text"
                                               autocomplete="off"
                                               name="from_date"
                                               class="form-control datepicker"
                                               value="<?php echo set_value('from_date'); ?>"
                                               placeholder="2024-12-31"
                                               id="from_date">
                                        <span class="text-danger"><?php echo form_error('from_date'); ?></span>
                                    </div>
                                </div>

                                <!-- Date de fin -->
                                <div class="col-sm-3 col-md-3">
                                    <div class="form-group">
                                        <label>
                                            <?php echo $this->lang->line('enquiry') . ' ' . $this->lang->line('to') . ' ' . $this->lang->line('date'); ?>
                                            <small class="text-muted">(AAAA-MM-JJ)</small>
                                        </label>
                                        <input type="text"
                                               autocomplete="off"
                                               name="to_date"
                                               class="form-control datepicker"
                                               value="<?php echo set_value('to_date'); ?>"
                                               placeholder="2024-12-31"
                                               id="to_date">
                                        <span class="text-danger"><?php echo form_error('to_date'); ?></span>
                                    </div>
                                </div>

                                <!-- Statut -->
                                <div class="col-sm-3 col-md-3">
                                    <div class="form-group">
                                        <label><?php echo $this->lang->line('status'); ?></label>
                                        <select id="status" name="status" class="form-control">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <option value="all" <?php echo ($status == 'all') ? 'selected' : ''; ?>>
                                                <?php echo $this->lang->line('all'); ?>
                                            </option>
                                            <?php foreach ($enquiry_status as $enkey => $envalue): ?>
                                                <option value="<?php echo $enkey; ?>" <?php echo ($enkey == $status) ? 'selected' : ''; ?>>
                                                    <?php echo $envalue; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('status'); ?></span>
                                    </div>
                                </div>

                                <!-- Bouton de recherche -->
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <button type="submit"
                                                name="search"
                                                value="search_filter"
                                                class="btn btn-primary pull-right">
                                            <i class="fa fa-search"></i>
                                            <?php echo $this->lang->line('search'); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Tableau des demandes -->
                    <div class="bordertop">
                        <div class="box-header with-border">
                            <h3 class="box-title titlefix">
                                <?php echo $this->lang->line('admission_enquiry'); ?>
                            </h3>
                            <div class="box-tools pull-right">
                                <?php if ($this->rbac->hasPrivilege('permission_enquiry', 'can_add')): ?>
                                    <button type="button"
                                            class="btn btn-sm btn-primary"
                                            data-toggle="modal"
                                            data-target="#myModal">
                                        <i class="fa fa-plus"></i>
                                        <?php echo $this->lang->line('add'); ?>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="box-body">
                            <div class="download_label">
                                <?php echo $this->lang->line('admission_enquiry') . ' ' . $this->lang->line('list'); ?>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover table-striped table-bordered" id="enquirytable">
                                    <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('name'); ?></th>
                                        <th><?php echo $this->lang->line('phone'); ?></th>
                                        <th>Référence</th>
                                        <th>Motif</th>
                                        <th><?php echo $this->lang->line('date'); ?></th>
                                        <th><?php echo $this->lang->line('last_follow_up_date'); ?></th>
                                        <th><?php echo $this->lang->line('next_follow_up_date'); ?></th>
                                        <th><?php echo $this->lang->line('status'); ?></th>
                                        <th class="text-center"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (empty($enquiry_list)): ?>
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">
                                                Aucune demande trouvée
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($enquiry_list as $value): ?>
                                            <?php
                                            $current_date = date("Y-m-d");
                                            $next_date = $value["next_date"] ?: $value["follow_up_date"];
                                            $row_class = ($next_date < $current_date) ? 'danger' : '';

                                            // Style du statut - Adaptation complète avec votre config
                                            $status_styles = [
                                                'pending' => ['label' => 'En attente', 'color' => '#ff9801'],
                                                'approve' => ['label' => 'Approuvé', 'color' => '#4caf50'],
                                                'completed' => ['label' => 'Terminé', 'color' => '#2196f3'],
                                                'disapprove' => ['label' => 'Refusé', 'color' => '#f44337'],
                                                'in_progress' => ['label' => 'En cours', 'color' => '#9c27b0'],
                                                'on_hold' => ['label' => 'En pause', 'color' => '#ff9801'],
                                                'cancelled' => ['label' => 'Annulé', 'color' => '#9e9e9e'],
                                                'review' => ['label' => 'En révision', 'color' => '#00bcd4'],
                                                'draft' => ['label' => 'Brouillon', 'color' => '#9e9e9e'],
                                                'archived' => ['label' => 'Archivé', 'color' => '#607d8b'],
                                                'default' => ['label' => ucfirst($value['status']), 'color' => '#9e9e9e']
                                            ];

                                            $status_data = $status_styles[$value['status']] ?? $status_styles['default'];
                                            ?>

                                            <tr class="<?php echo $row_class; ?>">
                                                <td><?php echo $value['name']; ?></td>
                                                <td><?php echo $value['contact']; ?></td>
                                                <td><?php echo $value['reference']; ?></td>
                                                <td><?php echo $value['source']; ?></td>
                                                <td>
                                                    <?php if (!empty($value['date'])): ?>
                                                        <?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value['date'])); ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($value['followupdate'])): ?>
                                                        <?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($value['followupdate'])); ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($next_date)): ?>
                                                        <?php echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($next_date)); ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                        <span class="label"
                                                              style="background-color: <?php echo $status_data['color']; ?>;
                                                                      border-radius: 2px;
                                                                      color: white;
                                                                      padding: 3px 8px;">
                                                            <?php echo $status_data['label']; ?>
                                                        </span>
                                                </td>
                                                <td class="text-center white-space-nowrap">
                                                    <div class="btn-group">
                                                        <?php if ($this->rbac->hasPrivilege('follow_up_permission_enquiry', 'can_view')): ?>
                                                            <button class="btn btn-default btn-xs"
                                                                    onclick="follow_up('<?php echo $value['id']; ?>', '<?php echo $value['status']; ?>');"
                                                                    data-target="#follow_up"
                                                                    data-toggle="modal"
                                                                    title="<?php echo $this->lang->line('follow_up_admission_enquiry'); ?>">
                                                                <i class="fa fa-reorder"></i>
                                                            </button>
                                                        <?php endif; ?>

                                                        <!-- Bouton d'impression -->
                                                        <?php if ($value['status'] == 'approve' && $this->rbac->hasPrivilege('permission_enquiry', 'can_view')): ?>
                                                            <button class="btn btn-default btn-xs"
                                                                    onclick="printPermission('<?php echo $value['id']; ?>')"
                                                                    title="Imprimer le document d'acceptation"
                                                                    data-id="<?php echo $value['id']; ?>">
                                                                <i class="fa fa-print"></i>
                                                            </button>
                                                        <?php endif; ?>

                                                        <!-- Option alternative: Si vous voulez aussi imprimer pour les statuts "completed" -->
                                                        <?php
                                                        // Décommentez ces lignes si vous voulez aussi imprimer pour les demandes terminées
                                                        /*
                                                        $printable_statuses = ['approve', 'completed'];
                                                        if (in_array($value['status'], $printable_statuses) && $this->rbac->hasPrivilege('permission_enquiry', 'can_view')): ?>
                                                            <button class="btn btn-default btn-xs"
                                                                    onclick="printPermission('<?php echo $value['id']; ?>')"
                                                                    title="Imprimer le document">
                                                                <i class="fa fa-print"></i>
                                                            </button>
                                                        <?php endif;
                                                        */
                                                        ?>

                                                        <?php if ($this->rbac->hasPrivilege('permission_enquiry', 'can_edit')): ?>
                                                            <button class="btn btn-default btn-xs"
                                                                    onclick="getRecord('<?php echo $value['id']; ?>', '<?php echo $value['status']; ?>')"
                                                                    data-target="#myModaledit"
                                                                    data-toggle="modal"
                                                                    title="<?php echo $this->lang->line('edit'); ?>">
                                                                <i class="fa fa-pencil"></i>
                                                            </button>
                                                        <?php endif; ?>

                                                        <?php if ($this->rbac->hasPrivilege('permission_enquiry', 'can_delete')): ?>
                                                            <button class="btn btn-default btn-xs"
                                                                    onclick="delete_enquiry('<?php echo $value['id']; ?>')"
                                                                    title="<?php echo $this->lang->line('delete'); ?>">
                                                                <i class="fa fa-remove"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal d'ajout -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        <i class="fa fa-plus-circle"></i>
                        <?php echo $this->lang->line('admission_enquiry'); ?>
                    </h4>
                </div>

                <div class="modal-body">
                    <form id="formadd" method="post" class="ptt10">
                        <div class="row">
                            <!-- Informations personnelles -->
                            <div class="col-md-6">
                                <h5 class="text-primary"><strong>Informations personnelles</strong></h5>
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('name'); ?> <span class="text-danger">*</span></label>
                                    <input type="text"
                                           id="name_add"
                                           autocomplete="off"
                                           class="form-control"
                                           value="<?php echo $this->customlib->getAdminSessionUserName(); ?>"
                                           name="name"
                                           readonly>
                                    <span id="name_add_error" class="text-danger"></span>
                                </div>

                                <div class="form-group">
                                    <label><?php echo $this->lang->line('phone'); ?> <span class="text-danger">*</span></label>
                                    <input id="number"
                                           autocomplete="off"
                                           name="contact"
                                           type="text"
                                           class="form-control"
                                           value="<?php echo set_value('contact'); ?>">
                                </div>

                                <div class="form-group">
                                    <label><?php echo $this->lang->line('email'); ?></label>
                                    <input type="email"
                                           value="<?php echo set_value('email'); ?>"
                                           name="email"
                                           class="form-control">
                                </div>

                                <div class="form-group">
                                    <label><?php echo $this->lang->line('address'); ?></label>
                                    <textarea name="address" class="form-control" rows="2"><?php echo set_value('address'); ?></textarea>
                                </div>
                            </div>

                            <!-- Détails de la demande -->
                            <div class="col-md-6">
                                <h5 class="text-primary"><strong>Détails de la demande</strong></h5>
                                <div class="form-group">
                                    <label>Date de début <small class="text-muted">(AAAA-MM-JJ)</small></label>
                                    <input type="text"
                                           id="date"
                                           name="date"
                                           class="form-control datepicker"
                                           value="<?php echo set_value('date', date('Y-m-d')); ?>"
                                           placeholder="2024-12-31">
                                    <span id="date_add_error" class="text-danger"></span>
                                </div>

                                <div class="form-group">
                                    <label>Date de fin <small class="text-muted">(AAAA-MM-JJ)</small></label>
                                    <input type="text"
                                           id="date_of_call"
                                           name="follow_up_date"
                                           class="form-control datepicker"
                                           value="<?php echo set_value('follow_up_date', date('Y-m-d')); ?>"
                                           placeholder="2024-12-31">
                                </div>

                                <div class="form-group">
                                    <label><?php echo $this->lang->line('assigned'); ?></label>
                                    <select name="assigned" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($stff_list as $staff): ?>
                                            <option value="<?php echo $staff['name'] . ' ' . $staff['surname']; ?>">
                                                <?php echo $staff['name'] . ' ' . $staff['surname']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Titre référence <span class="text-danger">*</span></label>
                                    <select name="source" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($sourcelist as $value): ?>
                                            <option value="<?php echo $value['source']; ?>">
                                                <?php echo $value['source']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label><?php echo $this->lang->line('reference'); ?></label>
                                    <select name="reference" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($Reference as $value): ?>
                                            <option value="<?php echo $value['reference']; ?>" <?php echo set_select('reference', $value['reference']); ?>>
                                                <?php echo $value['reference']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Notes et description -->
                            <div class="col-md-12">
                                <h5 class="text-primary"><strong>Informations complémentaires</strong></h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('description'); ?></label>
                                            <textarea name="description" class="form-control" rows="3"><?php echo set_value('description'); ?></textarea>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('note'); ?></label>
                                            <textarea name="note" class="form-control" rows="3"><?php echo set_value('note'); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Champs cachés -->
                            <div class="col-sm-3" hidden>
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('class'); ?></label>
                                    <select name="class" class="form-control">
                                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                                        <?php foreach ($class_list as $class): ?>
                                            <option value="<?php echo $class['id']; ?>" <?php echo set_select('class', $class['id']); ?>>
                                                <?php echo $class['class']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-3" hidden>
                                <div class="form-group">
                                    <label><?php echo $this->lang->line('number_of_child'); ?></label>
                                    <input type="number"
                                           class="form-control"
                                           min="1"
                                           value="<?php echo set_value('no_of_child'); ?>"
                                           name="no_of_child">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                    <button onclick="saveEnquiry()" class="btn btn-primary">
                        <i class="fa fa-save"></i> <?php echo $this->lang->line('save'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'édition -->
    <div class="modal fade" id="myModaledit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        <i class="fa fa-edit"></i>
                        <?php echo $this->lang->line('edit_admission_enquiry'); ?>
                    </h4>
                </div>
                <div class="modal-body" id="getdetails"></div>
            </div>
        </div>
    </div>

    <!-- Modal de suivi -->
    <div class="modal fade" id="follow_up" tabindex="-1" role="dialog" aria-labelledby="follow_up">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <button type="button" class="close" onclick="update()" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">
                        <i class="fa fa-clock-o"></i>
                        <?php echo $this->lang->line('follow_up_admission_enquiry'); ?>
                    </h4>
                </div>
                <div class="modal-body" id="getdetails_follow_up"></div>
            </div>
        </div>
    </div>
</div>

<!-- Inclusion des ressources nécessaires -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.fr.min.js"></script>

<script>
    $(document).ready(function () {
        // Initialisation des datepickers avec format YYYY-MM-DD
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            language: 'fr',
            clearBtn: true,
            orientation: 'bottom auto',
            daysOfWeekHighlighted: "0,6",
            todayBtn: true
        });

        // Synchronisation des dates
        $('#from_date').on('changeDate', function(e) {
            var startDate = e.date;
            $('#to_date').datepicker('setStartDate', startDate);
        });

        $('#to_date').on('changeDate', function(e) {
            var endDate = e.date;
            $('#from_date').datepicker('setEndDate', endDate);
        });

        // Initialisation de DataTable
        if ($.fn.DataTable) {
            $("#enquirytable").DataTable({
                searching: true,
                paging: true,
                bSort: true,
                info: true,
                language: {
                    search: "Rechercher:",
                    lengthMenu: "Afficher _MENU_ entrées",
                    info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                    infoEmpty: "Affichage 0 à 0 sur 0 entrées",
                    infoFiltered: "(filtré de _MAX_ entrées totales)",
                    paginate: {
                        first: "Premier",
                        last: "Dernier",
                        next: "Suivant",
                        previous: "Précédent"
                    },
                    emptyTable: "Aucune donnée disponible",
                    zeroRecords: "Aucun enregistrement correspondant trouvé"
                },
                dom: "<'row'<'col-sm-6'l><'col-sm-6'f>>" +
                    "<'row'<'col-sm-12'B>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'i><'col-sm-7'p>>",
                buttons: [
                    {
                        extend: 'copyHtml5',
                        text: '<i class="fa fa-files-o"></i> Copier',
                        titleAttr: 'Copier',
                        title: $('.download_label').html(),
                        exportOptions: { columns: ':visible' },
                        className: 'btn btn-sm btn-default'
                    },
                    {
                        extend: 'excelHtml5',
                        text: '<i class="fa fa-file-excel-o"></i> Excel',
                        titleAttr: 'Excel',
                        title: $('.download_label').html(),
                        exportOptions: { columns: ':visible' },
                        className: 'btn btn-sm btn-success'
                    },
                    {
                        extend: 'csvHtml5',
                        text: '<i class="fa fa-file-text-o"></i> CSV',
                        titleAttr: 'CSV',
                        title: $('.download_label').html(),
                        exportOptions: { columns: ':visible' },
                        className: 'btn btn-sm btn-info'
                    },
                    {
                        extend: 'pdfHtml5',
                        text: '<i class="fa fa-file-pdf-o"></i> PDF',
                        titleAttr: 'PDF',
                        title: $('.download_label').html(),
                        exportOptions: { columns: ':visible' },
                        className: 'btn btn-sm btn-danger',
                        orientation: 'landscape',
                        pageSize: 'A4'
                    },
                    {
                        extend: 'print',
                        text: '<i class="fa fa-print"></i> Imprimer',
                        titleAttr: 'Imprimer',
                        title: $('.download_label').html(),
                        customize: function (win) {
                            $(win.document.body)
                                .css('font-size', '10pt')
                                .find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');

                            $(win.document.body).prepend(
                                '<div style="text-align: center; margin-bottom: 20px;">' +
                                '<h2>Liste des demandes</h2>' +
                                '<p>Généré le ' + new Date().toLocaleDateString('fr-FR') + '</p>' +
                                '</div>'
                            );
                        },
                        exportOptions: { columns: ':visible' },
                        className: 'btn btn-sm btn-primary'
                    },
                    {
                        extend: 'colvis',
                        text: '<i class="fa fa-columns"></i> Colonnes',
                        titleAttr: 'Colonnes',
                        postfixButtons: ['colvisRestore'],
                        className: 'btn btn-sm btn-warning'
                    }
                ],
                initComplete: function() {
                    $('.dt-buttons').before(
                        '<div class="row" style="margin-bottom: 10px;">' +
                        '<div class="col-sm-12">' +
                        '<h4 style="display: inline-block; margin-right: 15px;">Actions d\'export :</h4>' +
                        '</div>' +
                        '</div>'
                    );

                    $('.dt-buttons .btn').css({
                        'margin-right': '5px',
                        'margin-bottom': '10px'
                    });
                }
            });
        }
    });

    function getRecord(id, status) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/enquiry/details/' + id + '/' + status,
            success: function (result) {
                $('#getdetails').html(result);
                $('.modal').on('shown.bs.modal', function() {
                    $('.datepicker').datepicker({
                        format: 'yyyy-mm-dd',
                        autoclose: true,
                        language: 'fr',
                        clearBtn: true
                    });
                });
            },
            error: function () {
                alert('Erreur lors du chargement des détails');
            }
        });
    }

    function postRecord(id) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/enquiry/editpost/' + id,
            type: 'POST',
            data: $("#myForm1").serialize(),
            dataType: 'json',
            success: function (data) {
                if (data.status == "fail") {
                    var message = Object.values(data.error).join('<br>');
                    alert(message);
                } else {
                    alert(data.message);
                    window.location.reload(true);
                }
            },
            error: function () {
                alert('Erreur lors de la mise à jour');
            }
        });
    }

    function saveEnquiry() {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/enquiry/add/',
            type: 'POST',
            dataType: 'json',
            data: $("#formadd").serialize(),
            success: function (data) {
                if (data.status == "fail") {
                    var message = Object.values(data.error).join('<br>');
                    alert(message);
                } else {
                    alert(data.message);
                    window.location.reload(true);
                }
            },
            error: function () {
                alert('Erreur lors de l\'enregistrement');
            }
        });
    }

    function delete_enquiry(id) {
        if (confirm('<?php echo $this->lang->line('delete_confirm'); ?>')) {
            $.ajax({
                url: '<?php echo base_url(); ?>admin/enquiry/delete/' + id,
                type: 'POST',
                dataType: 'json',
                success: function (data) {
                    if (data.status == "fail") {
                        var message = Object.values(data.error).join('<br>');
                        alert(message);
                    } else {
                        alert(data.message);
                        window.location.reload(true);
                    }
                },
                error: function () {
                    alert('Erreur lors de la suppression');
                }
            });
        }
    }

    function follow_up(id, status) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/enquiry/follow_up/' + id + '/' + status,
            success: function (data) {
                $('#getdetails_follow_up').html(data);
                $.ajax({
                    url: '<?php echo base_url(); ?>admin/enquiry/follow_up_list/' + id,
                    success: function (data) {
                        $('#timeline').html(data);
                    },
                    error: function () {
                        alert('Erreur lors du chargement de l\'historique');
                    }
                });
            },
            error: function () {
                alert('Erreur lors du chargement du suivi');
            }
        });
    }

    function update() {
        window.location.reload(true);
    }

    function printPermission(id) {
        var printWindow = window.open('<?php echo base_url(); ?>admin/enquiry/print_permission/' + id, '_blank');
        printWindow.focus();
    }
</script>

<style>
    /* Styles supplémentaires pour améliorer le design */
    .white-space-nowrap {
        white-space: nowrap;
    }

    .modal-header.bg-primary {
        background-color: #3c8dbc;
        color: white;
    }

    .modal-header.bg-primary .close {
        color: white;
        opacity: 0.8;
    }

    .modal-header.bg-primary .close:hover {
        opacity: 1;
    }

    .btn-group .btn {
        margin-right: 2px;
    }

    .btn-group .btn:last-child {
        margin-right: 0;
    }

    .table > tbody > tr.danger > td {
        background-color: #f2dede;
    }

    /* Amélioration de l'affichage des statuts */
    .label {
        display: inline-block;
        font-weight: 600;
        line-height: 1.2;
    }

    /* Style pour le datepicker */
    .datepicker {
        cursor: pointer;
        background-color: #fff;
    }

    /* Responsive design */
    @media (max-width: 768px) {
        .btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 2px;
        }

        .btn-group .btn {
            flex: 1 1 auto;
        }
    }

    /* Amélioration de l'affichage des tooltips */
    .tooltip {
        font-size: 12px;
    }
</style>