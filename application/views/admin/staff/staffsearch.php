<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<style type="text/css">
    /* Styles existants améliorés */
    .label-success {
        background-color: #00a65a;
    }
    .label-danger {
        background-color: #dd4b39;
    }
    .label-warning {
        background-color: #f39c12;
    }
    .label-info {
        background-color: #00c0ef;
    }
    .label-primary {
        background-color: #3c8dbc;
    }
    .label {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 500;
        display: inline-block;
        white-space: nowrap;
    }
    .btn-former {
        background-color: #605ca8;
        border-color: #605ca8;
        color: white;
        transition: all 0.3s ease;
    }
    .btn-former:hover {
        background-color: #4a4893;
        border-color: #4a4893;
        color: white;
        transform: translateY(-1px);
    }
    .btn-former i {
        margin-right: 5px;
    }
    .date-leaving-badge {
        background-color: #e08e0b;
        color: white;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 500;
        display: inline-block;
        white-space: nowrap;
    }
    .date-leaving-badge i {
        margin-right: 4px;
    }
    .staff-name-link {
        color: #3c8dbc;
        font-weight: 500;
        text-decoration: none;
    }
    .staff-name-link:hover {
        color: #0056b3;
        text-decoration: underline;
    }
    .table > thead > tr > th {
        vertical-align: middle;
        white-space: nowrap;
        background-color: #f9f9f9;
        font-weight: 600;
        color: #333;
        border-bottom: 2px solid #ddd;
    }
    .table > tbody > tr > td {
        vertical-align: middle;
        padding: 8px 5px;
    }
    .table-hover > tbody > tr:hover {
        background-color: #f5f5f5;
        transition: background-color 0.2s ease;
    }
    .btn-group-xs > .btn, .btn-xs {
        padding: 3px 8px;
        margin: 0 2px;
        border-radius: 3px;
    }
    .gender-badge {
        padding: 3px 8px;
        border-radius: 12px;
        background-color: #f0f0f0;
        color: #555;
        font-size: 11px;
        white-space: nowrap;
    }
    .gender-badge i {
        margin-right: 4px;
    }
    .gender-male {
        color: #36c6d3;
    }
    .gender-female {
        color: #e35f5f;
    }
    .status-indicator {
        display: inline-block;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 5px;
    }
    .status-active {
        background-color: #00a65a;
        box-shadow: 0 0 5px rgba(0,166,90,0.3);
    }
    .status-inactive {
        background-color: #dd4b39;
        box-shadow: 0 0 5px rgba(221,75,57,0.3);
    }

    /* Styles DataTable améliorés */
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 15px;
        float: right;
    }
    .dataTables_wrapper .dataTables_filter input {
        border-radius: 4px;
        border: 1px solid #ddd;
        padding: 6px 12px;
        margin-left: 5px;
        width: 250px;
    }
    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #3c8dbc;
        outline: none;
        box-shadow: 0 0 5px rgba(60,141,188,0.3);
    }
    .dataTables_wrapper .dataTables_length select {
        border-radius: 4px;
        border: 1px solid #ddd;
        padding: 5px;
        margin: 0 5px;
    }
    .dataTables_wrapper .dataTables_info {
        padding-top: 8px;
    }
    .dataTables_wrapper .dataTables_paginate {
        padding-top: 8px;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 5px 10px;
        margin: 0 2px;
        border-radius: 3px;
        border: 1px solid #ddd;
        background: #fff;
        color: #333 !important;
        cursor: pointer;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #3c8dbc;
        color: white !important;
        border-color: #3c8dbc;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #f4f4f4;
        border-color: #ccc;
    }

    /* Nouveaux styles pour les filtres de date */
    .filter-date-section {
        background-color: #f9f9f9;
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 15px;
        border: 1px solid #e5e5e5;
    }
    .filter-date-section .form-group {
        margin-bottom: 0;
    }
    .filter-date-section label {
        font-weight: 600;
        color: #555;
        font-size: 12px;
    }
    .date-badge {
        background-color: #00c0ef;
        color: white;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 11px;
        white-space: nowrap;
    }
    .date-badge i {
        margin-right: 3px;
    }
    .result-counter {
        background-color: #f39c12;
        color: white;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 12px;
        margin-left: 5px;
    }
    .table td:empty::before {
        content: "-";
        color: #ccc;
        font-style: italic;
    }
    .btn-export {
        background-color: #00a65a;
        border-color: #00a65a;
        color: white;
        margin-right: 5px;
    }
    .btn-export:hover {
        background-color: #008d4c;
        border-color: #008d4c;
        color: white;
    }
    .employee-id-badge {
        background-color: #3c8dbc;
        color: white;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 500;
        white-space: nowrap;
    }
    .table-responsive {
        overflow-x: auto;
        min-height: 300px;
    }
    .action-buttons {
        white-space: nowrap;
    }
    .department-badge {
        background-color: #6c757d;
        color: white;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 11px;
    }
    .designation-badge {
        background-color: #17a2b8;
        color: white;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 11px;
    }
    .input-group {
        position: relative;
    }
    .input-group-addon {
        background-color: #f4f4f4;
        border: 1px solid #d2d6de;
        border-right: none;
        padding: 6px 12px;
    }
    .input-group .form-control {
        border-left: none;
    }
    .input-group .form-control:focus {
        border-color: #d2d6de;
        outline: none;
        box-shadow: none;
    }

        .staff-page-header {
            background: linear-gradient(125deg, #0f3d66 0%, #1a6aa3 48%, #2c9a8b 100%);
            border-radius: 14px;
            padding: 18px 20px;
            color: #fff;
            margin-bottom: 16px;
            box-shadow: 0 10px 30px rgba(7, 37, 66, 0.22);
        }
        .staff-page-header h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.2px;
        }
        .staff-page-header p {
            margin: 5px 0 0;
            opacity: 0.9;
            font-size: 13px;
        }
        .staff-filter-card {
            border: 1px solid #e4edf5;
            border-radius: 12px;
            box-shadow: 0 5px 18px rgba(8, 39, 72, 0.06);
            overflow: hidden;
        }
        .staff-filter-card .box-header {
            background: #f8fbff;
            border-bottom: 1px solid #e4edf5;
            padding: 14px 16px;
        }
        .staff-filter-card .box-title {
            color: #113c64;
            font-weight: 700;
        }
        .staff-filter-card .box-body {
            padding: 16px;
        }
        .staff-filter-actions {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-top: 24px;
        }
        .staff-kpi-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 12px;
        }
        .staff-kpi-chip {
            background: #f4f9ff;
            border: 1px solid #d8e8f7;
            color: #17507c;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }
        .staff-kpi-chip .count {
            color: #0b2f4d;
            margin-left: 4px;
        }
        .department,
        .designation,
        .contract_type {
            display: inline-block;
            background: #f3f7fb;
            border: 1px solid #dce8f3;
            color: #294f6c;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
        }
</style>

<div class="content-wrapper" style="min-height: 946px;">
    <section class="content">
            <div class="staff-page-header">
                <div class="row">
                    <div class="col-sm-8">
                        <h2><i class="fa fa-users"></i> Gestion du personnel</h2>
                        <p>Recherche avancée des employés, suivi des statuts et accès rapide aux profils.</p>
                    </div>
                    <div class="col-sm-4 text-right" style="margin-top:8px;">
                        <?php if ($this->rbac->hasPrivilege('staff', 'can_add')) { ?>
                            <a href="<?php echo base_url(); ?>admin/staff/create" class="btn btn-default btn-sm" style="border:0; color:#0f3d66; font-weight:700;">
                                <i class="fa fa-plus"></i> <?php echo $this->lang->line('add_staff'); ?>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>

        <div class="row">
            <div class="col-md-12">
                    <div class="box box-primary staff-filter-card">
                    <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-sliders"></i> Filtres de recherche</h3>
                    </div>

                    <div class="box-body">
                        <?php if ($this->session->flashdata('msg')) { ?>
                            <?php echo $this->session->flashdata('msg'); ?>
                        <?php } ?>

                            <form role="form" action="<?php echo site_url('admin/staff') ?>" method="post" class="">
                                <?php echo $this->customlib->getCSRF(); ?>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line("status"); ?></label>
                                            <select name="status" class="form-control">
                                                <option value=""><?php echo $this->lang->line("all"); ?></option>
                                                <option value="1" <?php echo (isset($status_filter) && $status_filter === '1') ? 'selected' : ''; ?>>
                                                    <?php echo $this->lang->line("active"); ?>
                                                </option>
                                                <option value="0" <?php echo (isset($status_filter) && $status_filter === '0') ? 'selected' : ''; ?>>
                                                    <?php echo $this->lang->line("inactive"); ?>
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><?php echo $this->lang->line('role'); ?></label>
                                            <select name="role" class="form-control">
                                                <option value="">Tous les roles</option>
                                                <?php if (!empty($role)) { foreach ($role as $r) { ?>
                                                    <option value="<?php echo $r['type']; ?>" <?php echo (isset($role_id) && $role_id == $r['type']) ? 'selected' : ''; ?>>
                                                        <?php echo $r['type']; ?>
                                                    </option>
                                                <?php }} ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><i class="fa fa-calendar"></i> Date début</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input type="date" name="date_from" class="form-control" value="<?php echo isset($date_from) ? $date_from : ''; ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><i class="fa fa-calendar"></i> Date fin</label>
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                <input type="date" name="date_to" class="form-control" value="<?php echo isset($date_to) ? $date_to : ''; ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-8">
                                        <div class="form-group">
                                            <label>Recherche texte</label>
                                            <input type="text" name="search_text" class="form-control" placeholder="Nom, matricule, email, telephone, service..." value="<?php echo isset($search_text) ? $search_text : ''; ?>">
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="staff-filter-actions">
                                            <button type="submit" name="search" value="search_filter" class="btn btn-primary btn-sm checkbox-toggle">
                                                <i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?>
                                            </button>
                                            <a href="<?php echo site_url('admin/staff'); ?>" class="btn btn-default btn-sm">
                                                <i class="fa fa-refresh"></i> <?php echo $this->lang->line('reset'); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                    </div>

                    <?php
                    if (isset($resultlist)) {
                    $counter = 0;
                        $active_count = 0;
                        $inactive_count = 0;
                    foreach ($resultlist as $staff) {
                        if (strtolower($staff['user_type']) !== 'super admin') {
                            $counter++;
                                if ((int)$staff['is_active'] === 1) {
                                    $active_count++;
                                } else {
                                    $inactive_count++;
                                }
                        }
                    }
                    ?>
                    <div class="box-header ptbnull"></div>
                    <div class="nav-tabs-custom border0">
                            <div class="staff-kpi-row" style="padding:10px 10px 0;">
                                <span class="staff-kpi-chip">Total employes: <span class="count"><?php echo $counter; ?></span></span>
                                <span class="staff-kpi-chip">Actifs: <span class="count"><?php echo $active_count; ?></span></span>
                                <span class="staff-kpi-chip">Inactifs: <span class="count"><?php echo $inactive_count; ?></span></span>
                            </div>
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#tab_2" data-toggle="tab" aria-expanded="true"><i class="fa fa-list"></i> La liste des employés <span class="result-counter"><?php echo $counter; ?></span></a></li>
                            <li><a href="<?php echo site_url('admin/staff/former_employees'); ?>" class="btn-former btn-sm">
                                    <i class="fa fa-users"></i> Voir les Anciens employés
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="download_label"><?php echo $title; ?></div>
                            <div class="tab-pane active table-responsive no-padding" id="tab_2">
                                <table class="table table-striped table-bordered table-hover example" cellspacing="0" width="100%">
                                    <thead>
                                    <tr>
                                        <th><?php echo $this->lang->line('staff_id'); ?></th>
                                        <th>Civilité</th>
                                        <th>Nom et prénom</th>
                                        <th><?php echo $this->lang->line('department'); ?></th>
                                        <th><?php echo $this->lang->line('designation'); ?></th>
                                        <th>Nationalité</th>
                                        <th>Contrat</th>
                                        <th>Adresse</th>
                                        <th>Date de naissance</th>
                                        <th>Date d'entrée</th>
                                        <th>Date de sortie</th>
                                        <th>Catégorie salariale</th>
                                        <th>Cnps</th>
                                        <th>Téléphone</th>
                                        <th><?php echo $this->lang->line('status'); ?></th>
                                        <?php
                                        if (!empty($fields)) {
                                            foreach ($fields as $fields_key => $fields_value) {
                                                ?>
                                                <th><?php echo $fields_value->name; ?></th>
                                                <?php
                                            }
                                        }
                                        ?>
                                        <th class="text-right"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (!empty($resultlist)) {
                                        $count = 1;
                                        foreach ($resultlist as $staff) {
                                            // 🔒 On saute (ignore) le Super Admin
                                            if (strtolower($staff['user_type']) === 'super admin') {
                                                continue;
                                            }
                                            ?>
                                            <tr>
                                                <td>
                                                    <span class="employee-id-badge">
                                                        <i class="fa fa-id-card"></i> <?php echo $staff['employee_id']; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="gender-badge">
                                                        <i class="fa <?php echo (strtolower($staff['gender']) === 'male') ? 'fa-male gender-male' : 'fa-female gender-female'; ?>"></i>
                                                        <?php
                                                        $gender = strtolower($staff['gender']);
                                                        echo ($gender === 'male') ? 'Homme' : 'Femme';
                                                        ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a class="staff-name-link"
                                                        <?php if ($this->rbac->hasPrivilege('can_see_other_users_profile', 'can_view')) { ?>
                                                            href="<?php echo base_url(); ?>admin/staff/profile/<?php echo $staff['id']; ?>"
                                                        <?php } ?>>

                                                        <?php echo $staff['name'] . " " . $staff['surname']; ?>
                                                    </a>
                                                </td>
                                                <td>
                                                    <?php if (!empty($staff['department'])) { ?>
                                                        <span class="department">
                                                         <?php echo $staff['department']; ?>
                                                        </span>
                                                    <?php } else { ?>
                                                        <span class="text-muted">-</span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($staff['designation'])) { ?>
                                                        <span class="designation">
                                                            <?php echo $staff['designation']; ?>
                                                        </span>
                                                    <?php } else { ?>
                                                        <span class="text-muted">-</span>
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo !empty($staff['nationalite']) ? $staff['nationalite'] : '<span class="text-muted">-</span>'; ?></td>

                                                <td>
                                                    <?php if (!empty($staff['contract_type'])) { ?>
                                                        <span class="contract_type">
                                                            <?php echo $staff['contract_type']; ?>
                                                        </span>
                                                    <?php } else { ?>
                                                        <span class="text-muted">-</span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if (!empty($staff['permanent_address'])) {
                                                        echo '<span title="' . $staff['permanent_address'] . '">' .
                                                            substr($staff['permanent_address'], 0, 30) .
                                                            (strlen($staff['permanent_address']) > 30 ? '...' : '') . '</span>';
                                                    } else {
                                                        echo '<span class="text-muted">-</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if (!empty($staff['dob']) && $staff['dob'] != '0000-00-00') {
                                                        echo '<span class="date"><i class="fa fa-calendar"></i> ' .
                                                            date('d/m/Y', strtotime($staff['dob'])) . '</span>';
                                                    } else {
                                                        echo '<span class="text-muted">-</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if (!empty($staff['date_of_joining']) && $staff['date_of_joining'] != '0000-00-00') {
                                                        echo '<span class="date"><i class="fa fa-calendar-check-o c"></i> ' .
                                                            date('d/m/Y', strtotime($staff['date_of_joining'])) . '</span>';
                                                    } else {
                                                        echo '<span class="text-muted">-</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($staff['date_of_leaving']) && $staff['date_of_leaving'] != '0000-00-00') { ?>
                                                        <span class="date-leaving-badge">
                                                            <i class="fa fa-calendar-times-o"></i>
                                                            <?php echo date('d/m/Y', strtotime($staff['date_of_leaving'])); ?>
                                                        </span>
                                                    <?php } else { ?>
                                                        <span class="label label-success"><i class="fa fa-check-circle"></i> En poste</span>
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo !empty($staff['categorie_salaire']) ? $staff['categorie_salaire'] : '<span class="text-muted">-</span>'; ?></td>
                                                <td><?php echo !empty($staff['cnps_no']) ? $staff['cnps_no'] : '<span class="text-muted">-</span>'; ?></td>
                                                <td>
                                                    <?php if (!empty($staff['contact_no'])) { ?>
                                                        <i class="fa fa-phone-square"></i> <?php echo $staff['contact_no']; ?>
                                                    <?php } else { ?>
                                                        <span class="text-muted">-</span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if ($staff['is_active'] == 1): ?>
                                                        <span class="label label-success">
                                                            <span class="status-indicator status-active"></span>
                                                            <?php echo $this->lang->line('active'); ?>
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="label label-danger">
                                                            <span class="status-indicator status-inactive"></span>
                                                            <?php echo $this->lang->line('inactive'); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <?php
                                                if (!empty($fields)) {
                                                    foreach ($fields as $fields_key => $fields_value) {
                                                        $display_field = isset($staff[$fields_value->name]) ? $staff[$fields_value->name] : '';
                                                        if ($fields_value->type == "link") {
                                                            $display_field = "<a href='" . $staff[$fields_value->name] . "' target='_blank'><i class='fa fa-external-link'></i> " . $staff[$fields_value->name] . "</a>";
                                                        }
                                                        echo "<td>" . (!empty($display_field) ? $display_field : '<span class="text-muted">-</span>') . "</td>";
                                                    }
                                                }
                                                ?>
                                                <td class="pull-right action-buttons">
                                                    <?php
                                                    $userdata = $this->customlib->getUserData();
                                                    if (($this->rbac->hasPrivilege('can_see_other_users_profile', 'can_view')) || ($userdata["id"] == $staff["id"])) { ?>
                                                        <a data-placement="left" href="<?php echo base_url(); ?>admin/staff/profile/<?php echo $staff['id'] ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('show'); ?>">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    <?php }

                                                    $a = 0;
                                                    $sessionData = $this->session->userdata('admin');
                                                    if (($staff["user_type"] == "Super Admin") && $userdata["id"] == $staff["id"]) {
                                                        $a = 1;
                                                    } elseif (($this->rbac->hasPrivilege('staff', 'can_edit')) && ($this->rbac->hasPrivilege('can_see_other_users_profile', 'can_view'))) {
                                                        $a = 1;
                                                    }
                                                    if ($a == 1) {
                                                        ?>
                                                        <a data-placement="left" href="<?php echo base_url(); ?>admin/staff/edit/<?php echo $staff['id'] ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('edit'); ?>">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php
                                            $count++;
                                        }
                                    } else {
                                        ?>
                                        <tr>
                                            <td colspan="20" class="text-center">
                                                <div class="alert alert-info">
                                                    <i class="fa fa-info-circle"></i>
                                                    <?php echo $this->lang->line('no_record_found'); ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            }
            ?>
        </div>
    </section>
</div>

<script type="text/javascript">
    function getSectionByClass(class_id, section_id) {
        if (class_id != "" && section_id != "") {
            $('#section_id').html("");
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
                        var sel = "";
                        if (section_id == obj.section_id) {
                            sel = "selected";
                        }
                        div_data += "<option value=" + obj.section_id + " " + sel + ">" + obj.section + "</option>";
                    });
                    $('#section_id').append(div_data);
                }
            });
        }
    }
    $(document).ready(function () {
        var class_id = $('#class_id').val();
        var section_id = '<?php echo set_value('section_id') ?>';
        getSectionByClass(class_id, section_id);
        $(document).on('change', '#class_id', function (e) {
            $('#section_id').html("");
            var class_id = $(this).val();
            var base_url = '<?php echo base_url() ?>';
            var div_data = '<option value=""><?php echo $this->lang->line('select'); ?></option>';
            $.ajax({
                type: "GET",
                url: base_url + "sections/getByClass",
                data: {'class_id': class_id},
                dataType: "json",
                success: function (data) {
                    $.each(data, function (i, obj)
                    {
                        div_data += "<option value=" + obj.section_id + ">" + obj.section + "</option>";
                    });
                    $('#section_id').append(div_data);
                }
            });
        });
    });
</script>
