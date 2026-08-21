<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<style type="text/css">
    .text-left{text-align: left !important;}

    /* Styles modernes pour le DataTable */
    .modern-card {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border: none;
        margin-bottom: 20px;
    }

    .modern-card .card-header {
        background: linear-gradient(135deg, #0b3e97 0%, #0b3e97 100%);
        color: white;
        border-radius: 12px 12px 0 0;
        border: none;
        padding: 15px 20px;
    }

    .modern-card .card-header h3 {
        margin: 0;
        font-weight: 600;
        font-size: 18px;
    }

    .dataTables_wrapper {
        padding: 20px;
    }

    .dataTables_filter input {
        border-radius: 20px;
        border: 1px solid #ddd;
        padding: 8px 15px;
        margin-left: 10px;
    }

    .dataTables_length select {
        border-radius: 20px;
        border: 1px solid #ddd;
        padding: 5px 10px;
    }

    .table-modern {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .table-modern thead th {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: none;
        padding: 15px 10px;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #667eea;
    }

    .table-modern tbody td {
        padding: 12px 10px;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
    }

    .table-modern tbody tr:hover {
        background-color: #f8f9fa;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .badge-status {
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 600;
    }

    .btn-modern {
        border-radius: 20px;
        padding: 6px 15px;
        font-weight: 500;
        border: none;
        transition: all 0.3s ease;
    }

    .pagination > li > a {
        border-radius: 20px !important;
        margin: 0 2px;
        border: none;
        color: #667eea;
    }

    .pagination > li.active > a {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
    }

    .search-form {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .form-control-modern {
        border-radius: 10px;
        border: 1px solid #e0e0e0;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }

    .form-control-modern:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .stats-card {
        background: white;
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }

    .stats-number {
        font-size: 24px;
        font-weight: 700;
        color: #667eea;
        margin: 10px 0;
    }

    .stats-label {
        color: #6c757d;
        font-size: 14px;
        font-weight: 500;
    }

    /* Styles pour les boutons d'exportation */
    .dt-buttons .btn {
        border-radius: 20px;
        margin-right: 5px;
        margin-bottom: 10px;
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><i class="fa fa-bus"></i> <?php echo $this->lang->line('transport'); ?></h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <?php $this->load->view('reports/_human_resource'); ?>

        <div class="row">
            <div class="col-md-12">
                <div class="modern-card">
                    <div class="card-header">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>

                    <div class="card-body">
                        <form role="form" action="<?php echo site_url('report/staff_report') ?>" method="post" class="search-form">
                            <?php echo $this->customlib->getCSRF(); ?>

                            <div class="row">
                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $this->lang->line('search') . " " . $this->lang->line('type') . " (" . $this->lang->line('by') . " " . $this->lang->line('date_of_joining') . ")"; ?></label>
                                        <select class="form-control form-control-modern" name="search_type" onchange="showdate(this.value)">
                                            <?php foreach ($searchlist as $key => $search) { ?>
                                                <option value="<?php echo $key ?>" <?php echo (isset($search_type) && $search_type == $key) ? "selected" : ""; ?>>
                                                    <?php echo $search ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $this->lang->line('status') ?></label>
                                        <select class="form-control form-control-modern" name="staff_status">
                                            <?php foreach ($status as $status_key => $status_value) { ?>
                                                <option value="<?php echo $status_key ?>" <?php echo (isset($status_val) && $status_val == $status_key) ? "selected" : ""; ?>>
                                                    <?php echo $status_value ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $this->lang->line('role'); ?></label>
                                        <select class="form-control form-control-modern" name="role">
                                            <option value=""><?php echo $this->lang->line('select') ?></option>
                                            <?php foreach ($roles as $value) { ?>
                                                <option value="<?php echo $value['id'] ?>" <?php echo (isset($role_val) && $role_val == $value['id']) ? "selected" : ""; ?>>
                                                    <?php echo $value['name'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3 col-sm-6">
                                    <div class="form-group">
                                        <label class="form-label"><?php echo $this->lang->line('designation'); ?></label>
                                        <select class="form-control form-control-modern" name="designation">
                                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                                            <?php foreach ($designation as $value) { ?>
                                                <option value="<?php echo $value['id'] ?>" <?php echo (isset($designation_val) && $designation_val == $value['id']) ? "selected" : ""; ?>>
                                                    <?php echo $value['designation'] ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12" id='date_result'></div>

                                <div class="col-md-12 text-right mt-3">
                                    <button type="submit" name="search" value="search_filter" class="btn btn-modern btn-primary">
                                        <i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Cartes de statistiques -->
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <div class="stats-card">
                            <i class="fa fa-users fa-2x text-primary"></i>
                            <div class="stats-number"><?php echo count($resultlist); ?></div>
                            <div class="stats-label">Total Staff</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stats-card">
                            <i class="fa fa-check-circle fa-2x text-success"></i>
                            <div class="stats-number">
                                <?php
                                $active_count = 0;
                                if(!empty($resultlist)) {
                                    foreach($resultlist as $staff) {
                                        if($staff['is_active'] == 1) $active_count++;
                                    }
                                }
                                echo $active_count;
                                ?>
                            </div>
                            <div class="stats-label">Staff Actifs</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stats-card">
                            <i class="fa fa-clock fa-2x text-warning"></i>
                            <div class="stats-number">
                                <?php
                                $this_month = 0;
                                if(!empty($resultlist)) {
                                    foreach($resultlist as $staff) {
                                        if(date('m', strtotime($staff['date_of_joining'])) == date('m')) {
                                            $this_month++;
                                        }
                                    }
                                }
                                echo $this_month;
                                ?>
                            </div>
                            <div class="stats-label">Nouveaux ce mois</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <div class="stats-card">
                            <i class="fa fa-chart-pie fa-2x text-info"></i>
                            <div class="stats-number"><?php echo count($roles); ?></div>
                            <div class="stats-label">Rôles différents</div>
                        </div>
                    </div>
                </div>

                <div class="modern-card">
                    <div class="card-header">
                        <h3 class="box-title"><i class="fa fa-money"></i> <?php echo $this->lang->line('staff_report'); ?></h3>
                    </div>

                    <div class="card-body">
                        <div class="download-label mb-3">
                            <strong><?php echo $this->lang->line('staff_report') . " (" . $this->customlib->get_postmessage() . ")"; ?></strong>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover table-modern" id="staffTable">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?php echo $this->lang->line('staff_id'); ?></th>
                                    <th><?php echo $this->lang->line('name'); ?></th>
                                    <th><?php echo $this->lang->line('role'); ?></th>
                                    <th><?php echo $this->lang->line('designation'); ?></th>
                                    <th><?php echo $this->lang->line('department'); ?></th>
                                    <th><?php echo $this->lang->line('email'); ?></th>
                                    <th><?php echo $this->lang->line('phone'); ?></th>
                                    <th><?php echo $this->lang->line('date_of_joining'); ?></th>
                                    <th>Status</th>
                                    <!--<th class="text-center">Actions</th>-->
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (empty($resultlist)) { ?>
                                    <tr>
                                        <td colspan="11" class="text-center text-muted py-4">
                                            <i class="fa fa-info-circle fa-2x mb-2"></i><br>
                                            Aucun staff trouvé
                                        </td>
                                    </tr>
                                <?php } else {
                                    $count = 1;
                                    foreach ($resultlist as $staff) { ?>
                                        <tr>
                                            <td><?php echo $count; ?></td>
                                            <td>
                                                <span class="badge badge-primary badge-status"><?php echo $staff['employee_id']; ?></span>
                                            </td>
                                            <td>
                                                <strong><?php echo $staff['name'] . " " . $staff['surname']; ?></strong>
                                            </td>
                                            <td><?php echo $staff['user_type']; ?></td>
                                            <td><?php echo $staff['designation']; ?></td>
                                            <td><?php echo $staff['department']; ?></td>
                                            <td>
                                                <a href="mailto:<?php echo $staff['email']; ?>" class="text-primary">
                                                    <i class="fa fa-envelope"></i> <?php echo $staff['email']; ?>
                                                </a>
                                            </td>
                                            <td>
                                                <?php if(!empty($staff['contact_no'])) { ?>
                                                    <a href="tel:<?php echo $staff['contact_no']; ?>" class="text-success">
                                                        <i class="fa fa-phone"></i> <?php echo $staff['contact_no']; ?>
                                                    </a>
                                                <?php } else { ?>
                                                    <span class="text-muted">N/A</span>
                                                <?php } ?>
                                            </td>
                                            <td><?php echo date($this->customlib->getSchoolDateFormat(), strtotime($staff['date_of_joining'])); ?></td>
                                            <td>
                                                    <span class="badge badge-status <?php echo ($staff['is_active'] == 1) ? 'badge-success' : 'badge-danger'; ?>">
                                                        <?php echo ($staff['is_active'] == 1) ? 'Actif' : 'Inactif'; ?>
                                                    </span>
                                            </td>
                                            <td class="text-center" hidden>
                                                <button class="btn btn-sm btn-modern btn-outline-primary view-details"
                                                        data-staff-id="<?php echo $staff['id']; ?>"
                                                        data-toggle="tooltip" title="Voir détails">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-modern btn-outline-info"
                                                        data-toggle="tooltip" title="Modifier">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php
                                        $count++;
                                    }
                                } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal pour les détails du staff -->
<div class="modal fade" id="staffDetailsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails du Staff</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="staffDetailsContent">
                <!-- Les détails seront chargés ici -->
            </div>
        </div>
    </div>
</div>

<!-- CSS supplémentaire -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">

<!-- Scripts DataTable avec extensions -->
<script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
    $(document).ready(function() {
        // Vérifier si DataTable est déjà initialisé
        if ($.fn.DataTable.isDataTable('#staffTable')) {
            $('#staffTable').DataTable().destroy();
        }

        // Initialisation du DataTable avec des options modernes et boutons d'exportation
        var table = $('#staffTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json"
            },
            "responsive": true,
            "dom": '<"row"<"col-md-4"l><"col-md-4"B><"col-md-4"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
            "buttons": [
                {
                    extend: 'copy',
                    text: '<i class="fa fa-copy"></i> Copier',
                    className: 'btn btn-modern btn-light',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    }
                },
                {
                    extend: 'excel',
                    text: '<i class="fa fa-file-excel"></i> Excel',
                    className: 'btn btn-modern btn-success',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    }
                },
                {
                    extend: 'pdf',
                    text: '<i class="fa fa-file-pdf"></i> PDF',
                    className: 'btn btn-modern btn-danger',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="fa fa-print"></i> Imprimer',
                    className: 'btn btn-modern btn-info',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    }
                }
            ],
            "pageLength": 25,
            "order": [[0, 'asc']],
            "columnDefs": [
                { "orderable": false, "targets": [10] },
                { "className": "text-center", "targets": [0, 9, 10] },
                { "width": "5%", "targets": [0] },
                { "width": "10%", "targets": [10] }
            ],
            "initComplete": function() {
                // Ajouter des classes CSS aux éléments du DataTable
                $('.dataTables_length select').addClass('form-control-modern');
                $('.dataTables_filter input').addClass('form-control-modern');

                // Déplacer les boutons dans un conteneur plus approprié
                $('.dt-buttons').addClass('text-center mb-3');
            }
        });

        // Tooltips
        $('[data-toggle="tooltip"]').tooltip();

        // Gestion du clic sur le bouton de détails
        $('.view-details').on('click', function() {
            var staffId = $(this).data('staff-id');
            // Ici vous pouvez ajouter une requête AJAX pour charger les détails
            $('#staffDetailsModal').modal('show');
        });

        <?php if ($search_type == 'period') { ?>
        showdate('period');
        <?php } ?>
    });

    function showdate(value) {
        if (value == 'period') {
            var html = '';
            html += '<div class="col-md-3 col-sm-6"><div class="form-group"><label><?php echo $this->lang->line('from_date'); ?></label><input type="text" name="date_from" class="form-control form-control-modern date" value="<?php echo set_value('date_from', date('Y-m-d')); ?>"></div></div>';
            html += '<div class="col-md-3 col-sm-6"><div class="form-group"><label><?php echo $this->lang->line('to_date'); ?></label><input type="text" name="date_to" class="form-control form-control-modern date" value="<?php echo set_value('date_to', date('Y-m-d')); ?>"></div></div>';
            $('#date_result').html(html);
            $('.date').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true
            });
        } else {
            $('#date_result').html('');
        }
    }
</script>