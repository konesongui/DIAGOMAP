<style type="text/css">
    .absence-checkbox {
        display: inline-block;
        margin-right: 15px;
    }
    .absence-checkbox label {
        display: inline-block;
        vertical-align: middle;
        position: relative;
        padding-left: 25px;
        cursor: pointer;
        font-weight: normal;
    }
    .absence-checkbox label::before {
        content: "";
        display: inline-block;
        position: absolute;
        width: 18px;
        height: 18px;
        left: 0;
        top: 2px;
        border: 1px solid #cccccc;
        border-radius: 3px;
        background-color: #fff;
        -webkit-transition: border 0.15s ease-in-out;
        -o-transition: border 0.15s ease-in-out;
        transition: border 0.15s ease-in-out;
    }
    .absence-checkbox label::after {
        display: inline-block;
        position: absolute;
        content: " ";
        width: 12px;
        height: 12px;
        left: 3px;
        top: 5px;
        background-color: #d9534f;
        border-radius: 2px;
        -webkit-transform: scale(0, 0);
        -ms-transform: scale(0, 0);
        -o-transform: scale(0, 0);
        transform: scale(0, 0);
        -webkit-transition: -webkit-transform 0.1s cubic-bezier(0.8, -0.33, 0.2, 1.33);
        -moz-transition: -moz-transform 0.1s cubic-bezier(0.8, -0.33, 0.2, 1.33);
        -o-transition: -o-transform 0.1s cubic-bezier(0.8, -0.33, 0.2, 1.33);
        transition: transform 0.1s cubic-bezier(0.8, -0.33, 0.2, 1.33);
    }
    .absence-checkbox input[type="checkbox"] {
        opacity: 0;
        z-index: 1;
        position: absolute;
    }
    .absence-checkbox input[type="checkbox"]:checked + label::after {
        -webkit-transform: scale(1, 1);
        -ms-transform: scale(1, 1);
        -o-transform: scale(1, 1);
        transform: scale(1, 1);
    }
    .absence-checkbox input[type="checkbox"]:checked + label::before {
        border-color: #d9534f;
    }
    .absence-checkbox input[type="checkbox"]:disabled + label {
        opacity: 0.65;
        cursor: not-allowed;
    }
    .absence-checkbox input[type="checkbox"]:disabled + label::before {
        cursor: not-allowed;
    }
    .absent-row {
        background-color: #f2dede !important;
    }
    .present-row {
        background-color: #dff0d8 !important;
    }
    .holiday-row {
        background-color: #d9edf7 !important;
    }
    .absence-badge {
        background-color: #d9534f;
        color: white;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 11px;
        margin-left: 5px;
    }
    .remark-input {
        width: 100%;
        padding: 5px;
        border: 1px solid #ddd;
        border-radius: 3px;
    }
    .remark-input:focus {
        border-color: #337ab7;
        outline: none;
    }
    .absence-stats {
        margin-bottom: 15px;
        padding: 10px;
        background: #f9f9f9;
        border-radius: 4px;
        border-left: 3px solid #d9534f;
    }
    .status-text {
        font-weight: bold;
        color: #5cb85c;
    }
    .status-text.absent {
        color: #d9534f;
    }

    /* Styles pour les statistiques */
    .stats-container {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        padding: 20px;
    }
    .stats-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f4f4f4;
    }
    .stats-header h3 {
        margin: 0;
        color: #333;
    }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }
    .stat-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        border-left: 4px solid #d9534f;
    }
    .stat-card.present {
        border-left-color: #5cb85c;
    }
    .stat-card.holiday {
        border-left-color: #5bc0de;
    }
    .stat-value {
        font-size: 32px;
        font-weight: bold;
        color: #333;
    }
    .stat-label {
        color: #666;
        margin-top: 5px;
    }
    .filter-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
    .filter-row {
        display: flex;
        gap: 15px;
        align-items: flex-end;
        flex-wrap: wrap;
    }
    .filter-group {
        flex: 1;
        min-width: 200px;
    }
    .filter-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
        color: #555;
    }
    .employee-stats-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }
    .employee-stats-table th {
        background: #f4f4f4;
        padding: 10px;
        text-align: left;
        font-weight: 600;
    }
    .employee-stats-table td {
        padding: 10px;
        border-bottom: 1px solid #eee;
    }
    .employee-stats-table tr:hover {
        background: #f9f9f9;
    }
    .view-stats-btn {
        background: #337ab7;
        color: white;
        border: none;
        padding: 5px 15px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
    }
    .view-stats-btn:hover {
        background: #286090;
    }
    .absence-justification {
        max-width: 300px;
        font-size: 12px;
        color: #666;
    }
    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        overflow-y: auto;
    }
    .modal-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 20px;
        border-radius: 8px;
        width: 80%;
        max-width: 900px;
        position: relative;
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 15px;
        border-bottom: 1px solid #ddd;
        margin-bottom: 15px;
    }
    .modal-header h2 {
        margin: 0;
        color: #333;
    }
    .close-modal {
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
        color: #666;
        line-height: 1;
        margin-left: 15px;
    }
    .close-modal:hover {
        color: #333;
    }
    .summary-cards {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-bottom: 20px;
    }
    .summary-card {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
    }
    .summary-card h4 {
        margin: 0 0 10px 0;
        color: #666;
    }
    .summary-number {
        font-size: 36px;
        font-weight: bold;
        color: #d9534f;
    }
    .summary-number.annual {
        color: #337ab7;
    }
    .summary-number.monthly {
        color: #5cb85c;
    }
    .btn-view-absences {
        background: #d9534f;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        cursor: pointer;
        margin-right: 10px;
    }
    .btn-view-absences:hover {
        background: #c9302c;
    }
    .btn-stats {
        background: #5cb85c;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        cursor: pointer;
    }
    .btn-stats:hover {
        background: #4cae4c;
    }
    .date-range {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }
    .date-input {
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    .datepicker-dropdown {
        z-index: 10000 !important;
    }
    .export-buttons {
        display: flex;
        gap: 10px;
    }
    .btn-excel {
        background-color: #217346;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }
    .btn-excel:hover {
        background-color: #1a5e38;
    }
    .btn-pdf {
        background-color: #f40f02;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
    }
    .btn-pdf:hover {
        background-color: #c00c00;
    }
    .export-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    @media (max-width:767px){
        .absence-checkbox {display: block; margin-bottom: 10px;}
        .filter-row {flex-direction: column;}
        .filter-group {width: 100%;}
        .summary-cards {grid-template-columns: 1fr;}
        .modal-header {flex-direction: column; gap: 10px;}
        .export-actions {margin-top: 10px;}
    }
</style>

<div class="content-wrapper" style="min-height: 946px;">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1><i class="fa fa-sitemap"></i> <?php echo $this->lang->line('human_resource'); ?></h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Boutons d'action pour les statistiques -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="box-title"><i class="fa fa-calendar-check-o"></i> Gestion des présences</h3>
                            </div>
                            <div class="col-md-6 text-right">
                                <button type="button" class="btn btn-danger btn-sm" id="viewAbsencesBtn">
                                    <i class="fa fa-exclamation-triangle"></i> Voir les absences
                                </button>
                                <button type="button" class="btn btn-success btn-sm" id="viewStatsBtn">
                                    <i class="fa fa-bar-chart"></i> Statistiques globales
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formulaire de recherche principal -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('select_criteria'); ?></h3>
                    </div>

                    <form id='form1' action="<?php echo site_url('admin/staffattendance/index') ?>" method="post" accept-charset="utf-8">
                        <div class="box-body">
                            <?php
                            if ($this->session->flashdata('msg')) {
                                echo $this->session->flashdata('msg');
                            }
                            ?>
                            <?php echo $this->customlib->getCSRF(); ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1"><?php echo $this->lang->line('role'); ?></label>
                                        <select autofocus="" id="class_id" name="user_id" class="form-control">
                                            <option value="select"><?php echo $this->lang->line('select'); ?></option>
                                            <?php
                                            foreach ($classlist as $key => $class) {
                                                ?>
                                                <option value="<?php echo $class["type"] ?>" <?php if ($class["type"] == $user_type_id) echo "selected =selected"; ?>>
                                                    <?php print_r($class["type"]) ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <span class="text-danger"><?php echo form_error('class_id'); ?></span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">
                                            <?php echo $this->lang->line('attendance'); ?>
                                            <?php echo $this->lang->line('date'); ?>
                                        </label>
                                        <input name="date" placeholder="" type="text" class="form-control date" value="<?php echo set_value('date', date($this->customlib->getSchoolDateFormat())); ?>" readonly="readonly"/>
                                        <span class="text-danger"><?php echo form_error('date'); ?></span>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="submit" name="search" value="search" class="btn btn-primary btn-sm pull-right checkbox-toggle">
                                            <i class="fa fa-search"></i> <?php echo $this->lang->line('search'); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <?php
                    if (isset($resultlist)) {
                    ?>
                    <div class="box-header ptbnull"></div>
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-users"></i> <?php echo $this->lang->line('staff'); ?> <?php echo $this->lang->line('list'); ?></h3>
                        <div class="box-tools pull-right"></div>
                    </div>

                    <div class="box-body">
                        <?php
                        if (!empty($resultlist)) {
                            $checked = "";
                            $absent_type_id = 4; // ID pour Absent - À ajuster selon votre configuration
                            $present_type_id = 1; // ID pour Présent - À ajuster
                            $holiday_type_id = 5; // ID pour Congé/Jour férié

                            // Compter les absences du jour
                            $today_absences = 0;
                            foreach ($resultlist as $staff) {
                                if ($staff['staff_attendance_type_id'] == $absent_type_id) {
                                    $today_absences++;
                                }
                            }

                            if (!isset($msg)) {
                                if ($resultlist[0]['staff_attendance_type_id'] != "") {
                                    if ($resultlist[0]['staff_attendance_type_id'] != $holiday_type_id) {
                                        ?>
                                        <div class="alert alert-success">
                                            <?php echo $this->lang->line('attendance_already_submitted_you_can_edit_record'); ?>
                                            <?php if ($today_absences > 0): ?>
                                                <span class="absence-badge"><?php echo $today_absences; ?> absence(s)</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php
                                    } else {
                                        $checked = "checked='checked'";
                                        ?>
                                        <div class="alert alert-warning">
                                            <?php echo $this->lang->line('attendance_already_submitted_as_holiday'); ?>.
                                            <?php echo $this->lang->line('you_can_edit_record'); ?>
                                        </div>
                                        <?php
                                    }
                                }
                            } else {
                                ?>
                                <div class="alert alert-success">
                                    <?php echo $this->lang->line('attendance_saved_successfully'); ?>
                                </div>
                                <?php
                            }

                            // Afficher les statistiques d'absence si nécessaire
                            if ($today_absences > 0 && !isset($holiday)) {
                                ?>
                                <div class="absence-stats">
                                    <strong><i class="fa fa-exclamation-triangle text-danger"></i> <?php echo $today_absences; ?> employé(s) absent(s) aujourd'hui</strong>
                                    - N'oubliez pas de saisir les motifs d'absence dans la colonne "Note"
                                </div>
                                <?php
                            }
                            ?>

                            <form action="<?php echo site_url('admin/staffattendance/index') ?>" id="save_attendance" method="post">
                                <?php echo $this->customlib->getCSRF(); ?>

                                <div class="mailbox-controls">
                                        <span class="button-checkbox">
                                            <button type="button" class="btn btn-sm btn-primary" data-color="primary">
                                                <?php echo $this->lang->line('mark_as_holiday'); ?>
                                            </button>
                                            <input type="checkbox" id="checkbox1" class="hidden" name="holiday" value="checked" <?php echo $checked; ?>/>
                                        </span>

                                    <div class="pull-right">
                                        <?php if ($this->rbac->hasPrivilege('staff_attendance', 'can_add')) { ?>
                                            <button type="submit" name="search" value="saveattendence"
                                                    class="btn btn-primary btn-sm pull-right checkbox-toggle"
                                                    id="load"
                                                    data-loading-text="<i class='fa fa-spinner fa-spin '></i> <?php echo $this->lang->line('save_attendance'); ?>">
                                                <i class="fa fa-save"></i> <?php echo $this->lang->line('save_attendance'); ?>
                                            </button>
                                        <?php } ?>
                                    </div>
                                </div>

                                <input type="hidden" name="user_id" value="<?php echo $user_type_id; ?>">
                                <input type="hidden" name="section_id" value="">
                                <input type="hidden" name="date" value="<?php echo $date; ?>">

                                <div class="table-responsive">
                                    <table class="table table-hover table-striped example">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?php echo $this->lang->line('staff_id'); ?></th>
                                            <th><?php echo $this->lang->line('name'); ?></th>
                                            <th><?php echo $this->lang->line('role'); ?></th>
                                            <th><?php echo $this->lang->line('attendance'); ?></th>
                                            <th class="text-right"><?php echo $this->lang->line('note'); ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $row_count = 1;
                                        $absent_count = 0;

                                        foreach ($resultlist as $key => $value) {
                                            $attendendence_id = $value["id"];
                                            $row_class = "";
                                            $is_absent = false;

                                            // Déterminer si l'employé est absent
                                            if ($value['staff_attendance_type_id'] == $absent_type_id) {
                                                $row_class = "absent-row";
                                                $absent_count++;
                                                $is_absent = true;
                                            } elseif ($value['staff_attendance_type_id'] == $holiday_type_id) {
                                                $row_class = "holiday-row";
                                            } else {
                                                $row_class = "present-row";
                                            }
                                            ?>
                                            <tr class="<?php echo $row_class; ?>">
                                                <td>
                                                    <input type="hidden" name="student_session[]" value="<?php echo $value['staff_id']; ?>">
                                                    <input type="hidden" value="<?php echo $attendendence_id ?>" name="attendendence_id<?php echo $value["staff_id"]; ?>">
                                                    <input type="hidden" name="attendencetype<?php echo $value['staff_id']; ?>" value="<?php echo ($is_absent) ? $absent_type_id : $present_type_id; ?>" id="hidden_attendance_<?php echo $value['staff_id']; ?>">
                                                    <?php echo $row_count; ?>
                                                </td>
                                                <td>
                                                    <?php echo $value['employee_id']; ?>
                                                </td>
                                                <td>
                                                    <?php echo $value['name'] . " " . $value['surname']; ?>
                                                    <?php if ($is_absent): ?>
                                                        <span class="absence-badge">Absent</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo $value['user_type']; ?></td>
                                                <td>
                                                    <div class="absence-checkbox">
                                                        <input type="checkbox"
                                                               id="absent_<?php echo $value['staff_id']; ?>"
                                                               name="absent_<?php echo $value['staff_id']; ?>"
                                                               value="1"
                                                            <?php if ($is_absent) echo "checked"; ?>
                                                            <?php if (isset($holiday) && $holiday == "checked") echo "disabled"; ?>
                                                               data-staff-id="<?php echo $value['staff_id']; ?>">
                                                        <label for="absent_<?php echo $value['staff_id']; ?>">
                                                            <strong>Absent</strong>
                                                        </label>
                                                    </div>
                                                    <span class="status-text <?php if (!$is_absent) echo 'absent'; ?>">
                                                                <?php echo ($is_absent) ? '' : 'Présent'; ?>
                                                            </span>
                                                </td>

                                                <?php if ($value["date"] == 'xxx') { ?>
                                                    <td>
                                                        <input type="text"
                                                               class="remark-input"
                                                               name="remark<?php echo $value["staff_id"] ?>"
                                                               placeholder="<?php echo ($is_absent) ? 'Motif d\'absence...' : 'Note (optionnel)'; ?>"
                                                               data-staff-id="<?php echo $value["staff_id"]; ?>"
                                                            <?php if (!$is_absent) echo 'disabled'; ?>>
                                                    </td>
                                                <?php } else { ?>
                                                    <td>
                                                        <input type="text"
                                                               class="remark-input"
                                                               name="remark<?php echo $value["staff_id"] ?>"
                                                               value="<?php echo $value["remark"]; ?>"
                                                               placeholder="<?php echo ($is_absent) ? 'Motif d\'absence...' : 'Note (optionnel)'; ?>"
                                                               data-staff-id="<?php echo $value["staff_id"]; ?>"
                                                            <?php if (!$is_absent && !$value["remark"]) echo 'disabled'; ?>>
                                                    </td>
                                                <?php } ?>
                                            </tr>
                                            <?php
                                            $row_count++;
                                        }
                                        ?>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td colspan="6">
                                                <div class="pull-right">
                                                    <strong>Total Absences: <span class="text-danger"><?php echo $absent_count; ?></span></strong>
                                                </div>
                                            </td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </form>
                            <?php
                        } else {
                            ?>
                            <div class="alert alert-info"><?php echo $this->lang->line('no_record_found'); ?></div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <?php
                }
                ?>
            </div>
        </div>
    </section>
</div>

<!-- Modal pour voir les absences et justificatifs -->
<div id="absencesModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fa fa-exclamation-triangle text-danger"></i> Liste des absences et justificatifs</h2>
            <div class="export-actions">
                <button type="button" class="btn-excel" id="exportAbsencesExcel">
                    <i class="fa fa-file-excel-o"></i> Excel
                </button>
                <button type="button" class="btn-pdf" id="exportAbsencesPDF">
                    <i class="fa fa-file-pdf-o"></i> PDF
                </button>
                <span class="close-modal">&times;</span>
            </div>
        </div>
        <div class="modal-body">
            <div class="filter-section">
                <h4>Filtrer les absences</h4>
                <div class="filter-row">
                    <div class="filter-group">
                        <label>Employé</label>
                        <select id="filterEmployee" class="form-control">
                            <option value="all">Tous les employés</option>
                            <?php
                            if (!empty($resultlist)) {
                                foreach ($resultlist as $staff) {
                                    echo '<option value="' . $staff['staff_id'] . '">' . $staff['name'] . ' ' . $staff['surname'] . ' (' . $staff['employee_id'] . ')</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Période du</label>
                        <input type="text" id="filterDateFrom" class="form-control datepicker" placeholder="jj-mm-aaaa">
                    </div>
                    <div class="filter-group">
                        <label>Au</label>
                        <input type="text" id="filterDateTo" class="form-control datepicker" placeholder="jj-mm-aaaa">
                    </div>
                    <div class="filter-group">
                        <label>&nbsp;</label>
                        <button id="applyFilter" class="btn btn-primary btn-sm">
                            <i class="fa fa-filter"></i> Filtrer
                        </button>
                        <button id="resetFilter" class="btn btn-default btn-sm">
                            <i class="fa fa-refresh"></i> Réinitialiser
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped" id="absencesTable">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Employé</th>
                        <th>Matricule</th>
                        <th>Motif / Justificatif</th>
                    </tr>
                    </thead>
                    <tbody id="absencesTableBody">
                    <!-- Les données seront chargées via AJAX -->
                    <tr>
                        <td colspan="4" class="text-center">Chargement des données...</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour les statistiques globales -->
<div id="statsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fa fa-bar-chart"></i> Statistiques globales des absences</h2>
            <div class="export-actions">
                <button type="button" class="btn-excel" id="exportStatsExcel">
                    <i class="fa fa-file-excel-o"></i> Excel
                </button>
                <button type="button" class="btn-pdf" id="exportStatsPDF">
                    <i class="fa fa-file-pdf-o"></i> PDF
                </button>
                <span class="close-modal">&times;</span>
            </div>
        </div>
        <div class="modal-body">
            <div class="filter-section">
                <h4>Sélectionner une période</h4>
                <div class="filter-row">
                    <div class="filter-group">
                        <label>Année</label>
                        <select id="statsYear" class="form-control">
                            <?php
                            $current_year = date('Y');
                            for ($year = $current_year; $year >= $current_year - 3; $year--) {
                                $selected = ($year == $current_year) ? 'selected' : '';
                                echo '<option value="' . $year . '" ' . $selected . '>' . $year . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Mois</label>
                        <select id="statsMonth" class="form-control">
                            <option value="all">Tous les mois</option>
                            <option value="01">Janvier</option>
                            <option value="02">Février</option>
                            <option value="03">Mars</option>
                            <option value="04">Avril</option>
                            <option value="05">Mai</option>
                            <option value="06">Juin</option>
                            <option value="07">Juillet</option>
                            <option value="08">Août</option>
                            <option value="09">Septembre</option>
                            <option value="10">Octobre</option>
                            <option value="11">Novembre</option>
                            <option value="12">Décembre</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>&nbsp;</label>
                        <button id="loadStats" class="btn btn-primary btn-sm">
                            <i class="fa fa-line-chart"></i> Afficher les stats
                        </button>
                    </div>
                </div>
            </div>

            <div id="statsSummary" class="stats-grid">
                <!-- Les résumés seront chargés ici -->
            </div>

            <h4>Détail par employé</h4>
            <div class="table-responsive">
                <table class="employee-stats-table" id="employeeStatsTable">
                    <thead>
                    <tr>
                        <th>Employé</th>
                        <th>Matricule</th>
                        <th>Total absences</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody id="employeeStatsBody">
                    <!-- Les données seront chargées via AJAX -->
                    <tr>
                        <td colspan="4" class="text-center">Chargement des données...</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour les statistiques individuelles -->
<div id="employeeStatsModal" class="modal">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h2><i class="fa fa-user"></i> Statistiques de <span id="employeeName"></span></h2>
            <span class="close-modal">&times;</span>
        </div>
        <div class="modal-body">
            <div class="filter-section">
                <h4>Période</h4>
                <div class="filter-row">
                    <div class="filter-group">
                        <label>Année</label>
                        <select id="employeeStatsYear" class="form-control">
                            <?php
                            for ($year = $current_year; $year >= $current_year - 3; $year--) {
                                $selected = ($year == $current_year) ? 'selected' : '';
                                echo '<option value="' . $year . '" ' . $selected . '>' . $year . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Mois</label>
                        <select id="employeeStatsMonth" class="form-control">
                            <option value="all">Tous</option>
                            <option value="01">Janvier</option>
                            <option value="02">Février</option>
                            <option value="03">Mars</option>
                            <option value="04">Avril</option>
                            <option value="05">Mai</option>
                            <option value="06">Juin</option>
                            <option value="07">Juillet</option>
                            <option value="08">Août</option>
                            <option value="09">Septembre</option>
                            <option value="10">Octobre</option>
                            <option value="11">Novembre</option>
                            <option value="12">Décembre</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>&nbsp;</label>
                        <button id="loadEmployeeStats" class="btn btn-primary btn-sm">
                            <i class="fa fa-refresh"></i> Mettre à jour
                        </button>
                    </div>
                </div>
            </div>

            <div class="summary-cards">
                <div class="summary-card">
                    <h4>Total absences</h4>
                    <div class="summary-number" id="totalAbsences">0</div>
                </div>
                <div class="summary-card">
                    <h4>Moyenne mensuelle</h4>
                    <div class="summary-number monthly" id="monthlyAvg">0</div>
                </div>
                <div class="summary-card">
                    <h4>Total annuel</h4>
                    <div class="summary-number annual" id="annualTotal">0</div>
                </div>
            </div>

            <h4>Détail des absences</h4>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Motif / Justificatif</th>
                    </tr>
                    </thead>
                    <tbody id="employeeAbsencesList">
                    <tr>
                        <td colspan="2" class="text-center">Aucune absence sur cette période</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Inclusion des bibliothèques pour les exports -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<script type="text/javascript">
    $(document).on('submit','#save_attendance',function(e) {
        $('#load').button('loading');
    });

    $(document).ready(function () {
        $.extend($.fn.dataTable.defaults, {
            searching: false,
            ordering: true,
            paging: false,
            retrieve: true,
            destroy: true,
            info: false
        });
        var table = $('.example').DataTable();
        table.buttons('.export').remove();

        // Initialiser les datepickers avec le bon format
        $('.datepicker').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            todayHighlight: true,
            orientation: 'top'
        });

        // Gestionnaire pour le bouton "Voir les absences"
        $('#viewAbsencesBtn').click(function() {
            loadAbsencesData();
            $('#absencesModal').show();
        });

        // Gestionnaire pour le bouton "Statistiques globales"
        $('#viewStatsBtn').click(function() {
            loadGlobalStats();
            $('#statsModal').show();
        });

        // Fermer les modals
        $('.close-modal').click(function() {
            $(this).closest('.modal').hide();
        });

        $(window).click(function(event) {
            if ($(event.target).hasClass('modal')) {
                $(event.target).hide();
            }
        });

        // Filtrer les absences
        $('#applyFilter').click(function() {
            loadAbsencesData();
        });

        $('#resetFilter').click(function() {
            $('#filterEmployee').val('all');
            $('#filterDateFrom').val('');
            $('#filterDateTo').val('');
            loadAbsencesData();
        });

        // Charger les statistiques
        $('#loadStats').click(function() {
            loadGlobalStats();
        });

        // Charger les statistiques individuelles
        $('#loadEmployeeStats').click(function() {
            var employeeId = $('#employeeStatsModal').data('employee-id');
            loadEmployeeStats(employeeId);
        });

        // Exports pour les absences
        $('#exportAbsencesExcel').click(function() {
            exportAbsencesToExcel();
        });

        $('#exportAbsencesPDF').click(function() {
            exportAbsencesToPDF();
        });

        // Exports pour les statistiques
        $('#exportStatsExcel').click(function() {
            exportStatsToExcel();
        });

        $('#exportStatsPDF').click(function() {
            exportStatsToPDF();
        });
    });

    // Fonction pour charger les absences
    function loadAbsencesData() {
        var employeeId = $('#filterEmployee').val();
        var dateFrom = $('#filterDateFrom').val();
        var dateTo = $('#filterDateTo').val();

        // Simuler des données pour l'exemple
        setTimeout(function() {
            var html = '';

            // Données simulées pour la démonstration
            var sampleData = [
                {date: '15/03/2026', employee_name: 'Super', employee_surname: 'Admin', employee_id: '9000', remark: 'Maladie'},
                {date: '10/03/2026', employee_name: 'Super', employee_surname: 'Admin', employee_id: '9000', remark: 'Rendez-vous médical'},
                {date: '05/03/2026', employee_name: 'Super', employee_surname: 'Admin', employee_id: '9000', remark: 'Congé sans solde'}
            ];

            $.each(sampleData, function(index, item) {
                html += '<tr>';
                html += '<td>' + item.date + '</td>';
                html += '<td>' + item.employee_name + ' ' + item.employee_surname + '</td>';
                html += '<td>' + item.employee_id + '</td>';
                html += '<td>' + (item.remark ? item.remark : '<em class="text-muted">Non justifié</em>') + '</td>';
                html += '</tr>';
            });

            $('#absencesTableBody').html(html);
        }, 500);
    }

    // Fonction pour charger les statistiques globales
    function loadGlobalStats() {
        var year = $('#statsYear').val();
        var month = $('#statsMonth').val();

        // Simuler des données pour l'exemple
        setTimeout(function() {
            // Afficher le résumé
            var summaryHtml = '';

            // Données simulées basées sur la capture d'écran
            summaryHtml += '<div class="stat-card">';
            summaryHtml += '<div class="stat-value">3</div>';
            summaryHtml += '<div class="stat-label">Total absences</div>';
            summaryHtml += '</div>';

            summaryHtml += '<div class="stat-card present">';
            summaryHtml += '<div class="stat-value">1</div>';
            summaryHtml += '<div class="stat-label">Employés concernés</div>';
            summaryHtml += '</div>';

            summaryHtml += '<div class="stat-card holiday">';
            summaryHtml += '<div class="stat-value">3.0</div>';
            summaryHtml += '<div class="stat-label">Moyenne/employé</div>';
            summaryHtml += '</div>';

            $('#statsSummary').html(summaryHtml);

            // Afficher le détail par employé
            var employeeHtml = '';

            // Données simulées basées sur la capture d'écran
            var employees = [
                {id: 1, name: 'Super Admin', surname: '', employee_id: '9000', total_absences: 3}
            ];

            $.each(employees, function(index, emp) {
                employeeHtml += '<tr>';
                employeeHtml += '<td>' + emp.name + ' ' + emp.surname + '</td>';
                employeeHtml += '<td>' + emp.employee_id + '</td>';
                employeeHtml += '<td>' + emp.total_absences + '</td>';
                employeeHtml += '<td><button class="btn btn-xs btn-info view-employee-stats" data-id="' + emp.id + '" data-name="' + emp.name + ' ' + emp.surname + '"><i class="fa fa-eye"></i> Détail</button></td>';
                employeeHtml += '</tr>';
            });

            $('#employeeStatsBody').html(employeeHtml);

            // Ajouter les événements pour les boutons individuels
            $('.view-employee-stats').click(function() {
                var employeeId = $(this).data('id');
                var employeeName = $(this).data('name');
                viewEmployeeStats(employeeId, employeeName);
            });
        }, 500);
    }

    // Fonction pour voir les statistiques individuelles
    function viewEmployeeStats(employeeId, employeeName) {
        $('#employeeName').text(employeeName);
        $('#employeeStatsModal').data('employee-id', employeeId);
        $('#employeeStatsModal').show();

        // Réinitialiser les filtres
        $('#employeeStatsYear').val(new Date().getFullYear());
        $('#employeeStatsMonth').val('all');

        loadEmployeeStats(employeeId);
    }

    // Fonction pour charger les statistiques individuelles
    function loadEmployeeStats(employeeId) {
        var year = $('#employeeStatsYear').val();
        var month = $('#employeeStatsMonth').val();

        // Simuler des données pour l'exemple
        setTimeout(function() {
            $('#totalAbsences').text('3');
            $('#monthlyAvg').text('1.0');
            $('#annualTotal').text('3');

            var html = '';

            // Données simulées
            var absences = [
                {date: '15/03/2026', remark: 'Maladie'},
                {date: '10/03/2026', remark: 'Rendez-vous médical'},
                {date: '05/03/2026', remark: 'Congé sans solde'}
            ];

            $.each(absences, function(index, absence) {
                html += '<tr>';
                html += '<td>' + absence.date + '</td>';
                html += '<td>' + (absence.remark ? absence.remark : '<em class="text-muted">Non justifié</em>') + '</td>';
                html += '</tr>';
            });

            $('#employeeAbsencesList').html(html);
        }, 500);
    }

    // Fonction pour exporter les absences en Excel
    function exportAbsencesToExcel() {
        var rows = [];

        // En-têtes
        rows.push(['Date', 'Employé', 'Matricule', 'Motif / Justificatif']);

        // Données
        $('#absencesTableBody tr').each(function() {
            var row = [];
            $(this).find('td').each(function() {
                var text = $(this).text().trim();
                // Supprimer les balises HTML
                text = text.replace(/<[^>]*>/g, '');
                row.push(text);
            });
            if (row.length > 0 && row[0] !== 'Chargement des données...' && row[0] !== 'Aucune absence trouvée') {
                rows.push(row);
            }
        });

        if (rows.length <= 1) {
            alert('Aucune donnée à exporter');
            return;
        }

        // Créer le workbook
        var wb = XLSX.utils.book_new();
        var ws = XLSX.utils.aoa_to_sheet(rows);

        // Ajuster la largeur des colonnes
        var colWidths = [
            { wch: 12 }, // Date
            { wch: 30 }, // Employé
            { wch: 15 }, // Matricule
            { wch: 40 }  // Motif
        ];
        ws['!cols'] = colWidths;

        // Ajouter la feuille au workbook
        XLSX.utils.book_append_sheet(wb, ws, 'Absences');

        // Télécharger le fichier
        var date = new Date();
        var filename = 'absences_' + date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate() + '.xlsx';
        XLSX.writeFile(wb, filename);
    }

    // Fonction pour exporter les absences en PDF
    function exportAbsencesToPDF() {
        var rows = [];

        // Données
        $('#absencesTableBody tr').each(function() {
            var row = [];
            $(this).find('td').each(function() {
                var text = $(this).text().trim();
                // Supprimer les balises HTML
                text = text.replace(/<[^>]*>/g, '');
                row.push(text);
            });
            if (row.length > 0 && row[0] !== 'Chargement des données...' && row[0] !== 'Aucune absence trouvée') {
                rows.push(row);
            }
        });

        if (rows.length === 0) {
            alert('Aucune donnée à exporter');
            return;
        }

        // Créer le PDF
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // Titre
        doc.setFontSize(16);
        doc.text('Liste des absences et justificatifs', 14, 15);
        doc.setFontSize(10);
        doc.text('Généré le : ' + new Date().toLocaleDateString('fr-FR'), 14, 22);

        // Tableau
        doc.autoTable({
            head: [['Date', 'Employé', 'Matricule', 'Motif / Justificatif']],
            body: rows,
            startY: 25,
            styles: { fontSize: 8 },
            headStyles: { fillColor: [41, 128, 185], textColor: 255 },
            columnStyles: {
                0: { cellWidth: 25 },
                1: { cellWidth: 60 },
                2: { cellWidth: 30 },
                3: { cellWidth: 75 }
            }
        });

        // Télécharger le PDF
        var date = new Date();
        var filename = 'absences_' + date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate() + '.pdf';
        doc.save(filename);
    }

    // Fonction pour exporter les statistiques en Excel
    function exportStatsToExcel() {
        var rows = [];

        // En-têtes pour le résumé
        rows.push(['STATISTIQUES GLOBALES DES ABSENCES']);
        rows.push(['Période', $('#statsYear').val() + ' - ' + ($('#statsMonth').val() === 'all' ? 'Tous les mois' : $('#statsMonth option:selected').text())]);
        rows.push(['']);

        // Résumé
        var summaryRows = [];
        $('.stat-card').each(function() {
            var value = $(this).find('.stat-value').text().trim();
            var label = $(this).find('.stat-label').text().trim();
            summaryRows.push([label + ':', value]);
        });

        if (summaryRows.length > 0) {
            rows.push(['RÉSUMÉ']);
            rows = rows.concat(summaryRows);
            rows.push(['']);
        }

        // Détail par employé
        rows.push(['DÉTAIL PAR EMPLOYÉ']);
        rows.push(['Employé', 'Matricule', 'Total absences']);

        $('#employeeStatsBody tr').each(function() {
            var row = [];
            $(this).find('td').each(function() {
                var text = $(this).text().trim();
                // Supprimer les balises HTML et le bouton
                text = text.replace(/<[^>]*>/g, '');
                if (!$(this).find('button').length) {
                    row.push(text);
                }
            });
            if (row.length > 0 && row[0] !== 'Chargement des données...' && row[0] !== 'Aucun employé trouvé') {
                rows.push(row);
            }
        });

        if (rows.length <= 5) {
            alert('Aucune donnée à exporter');
            return;
        }

        // Créer le workbook
        var wb = XLSX.utils.book_new();
        var ws = XLSX.utils.aoa_to_sheet(rows);

        // Ajuster la largeur des colonnes
        var colWidths = [
            { wch: 30 }, // Employé/Label
            { wch: 15 }, // Matricule/Valeur
            { wch: 15 }  // Total
        ];
        ws['!cols'] = colWidths;

        // Ajouter la feuille au workbook
        XLSX.utils.book_append_sheet(wb, ws, 'Statistiques');

        // Télécharger le fichier
        var date = new Date();
        var filename = 'statistiques_absences_' + date.getFullYear() + '-' + (date.getMonth() + 1) + '-' + date.getDate() + '.xlsx';
        XLSX.writeFile(wb, filename);
    }

    // Fonction pour exporter les statistiques en PDF
    function exportStatsToPDF() {
        var summaryData = [];
        var employeeData = [];

        // Récupérer le résumé
        $('.stat-card').each(function() {
            var value = $(this).find('.stat-value').text().trim();
            var label = $(this).find('.stat-label').text().trim();
            if (value && label) {
                summaryData.push([label, value]);
            }
        });

        // Récupérer les données des employés
        $('#employeeStatsBody tr').each(function() {
            var row = [];
            $(this).find('td').each(function() {
                var text = $(this).text().trim();
                // Supprimer les balises HTML et le bouton
                text = text.replace(/<[^>]*>/g, '');
                if (!$(this).find('button').length) {
                    row.push(text);
                }
            });
            if (row.length > 0 && row[0] !== 'Chargement des données...' && row[0] !== 'Aucun employé trouvé') {
                employeeData.push(row);
            }
        });

        if (summaryData.length === 0 && employeeData.length === 0) {
            alert('Aucune donnée à exporter');
            return;
        }

        // Créer le PDF
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();

        // Titre
        doc.setFontSize(16);
        doc.text('Statistiques globales des absences', 14, 15);
        doc.setFontSize(10);
        doc.text('Période : ' + $('#statsYear').val() + ' - ' + ($('#statsMonth').val() === 'all' ? 'Tous les mois' : $('#statsMonth option:selected').text()), 14, 22);
        doc.text('Généré le : ' + new Date().toLocaleDateString('fr-FR'), 14, 29);

        var currentY = 35;

        // Résumé
        if (summaryData.length > 0) {
            doc.setFontSize(12);
            doc.text('Résumé', 14, currentY);
            currentY += 5;

            doc.autoTable({
                body: summaryData,
                startY: currentY,
                styles: { fontSize: 10 },
                columnStyles: {
                    0: { cellWidth: 50 },
                    1: { cellWidth: 30 }
                },
                theme: 'plain'
            });

            currentY = doc.lastAutoTable.finalY + 10;
        }

        // Détail par employé
        if (employeeData.length > 0) {
            doc.setFontSize(12);
            doc.text('Détail par employé', 14, currentY);
            currentY += 5;

            doc.autoTable({
                head: [['Employé', 'Matricule', 'Total absences']],
                body: employeeData,
                startY: currentY,
                styles: { fontSize: 8 },
                headStyles: { fillColor: [41, 128, 185], textColor: 255 },
                columnStyles: {
                    0: { cellWidth: 60 },
                    1: { cellWidth: 30 },
                    2: { cellWidth: 25 }
                }
            });
        }

        // Télécharger le PDF
        var date = new Date();
        var filename = 'statistiques_absences_' + date.getFullYear() + '-' + (date.getDate() + 1) + '-' + date.getDate() + '.pdf';
        doc.save(filename);
    }
</script>

<script type="text/javascript">
    window.onload = function xy() {
        var ch = '<?php
            if (!empty($resultlist)) {
                echo $resultlist[0]['staff_attendance_type_id'];
            }
            ?>';

        if (ch == 5) {
            $("input[type=checkbox]").attr('disabled', true);
        } else {
            $("input[type=checkbox]").attr('disabled', false);
        }
    };

    $(document).ready(function () {
        var absentTypeId = 4; // ID pour Absent - À ajuster
        var presentTypeId = 1; // ID pour Présent - À ajuster
        var holidayTypeId = 5; // ID pour Congé/Jour férié

        // Gestionnaire pour le changement des checkboxes d'absence
        $('input[type=checkbox]').on('change', function() {
            var staffId = $(this).data('staff-id');
            var isChecked = $(this).is(':checked');
            var remarkField = $('input[name="remark' + staffId + '"]');
            var parentRow = $(this).closest('tr');
            var hiddenField = $('#hidden_attendance_' + staffId);
            var statusText = $(this).closest('td').find('.status-text');

            // Mettre à jour le champ caché
            if (isChecked) {
                hiddenField.val(absentTypeId);
                parentRow.removeClass('present-row holiday-row').addClass('absent-row');
                remarkField.prop('disabled', false);
                remarkField.attr('placeholder', 'Motif d\'absence obligatoire...');
                statusText.text('Absent').addClass('absent');

                // Mettre en évidence le champ remarque si vide
                if (remarkField.val().trim() == '') {
                    remarkField.css('border-color', '#d9534f');
                }
            } else {
                hiddenField.val(presentTypeId);
                parentRow.removeClass('absent-row holiday-row').addClass('present-row');
                remarkField.prop('disabled', true);
                remarkField.attr('placeholder', 'Note (optionnel)');
                remarkField.css('border-color', '#ddd');
                statusText.text('Présent').removeClass('absent');
            }

            // Mettre à jour le compteur d'absences
            updateAbsenceCounter();
        });

        // Validation du champ remarque pour les absences
        $('.remark-input').on('blur', function() {
            var staffId = $(this).data('staff-id');
            var isAbsent = $('#absent_' + staffId).is(':checked');
            var remarkValue = $(this).val().trim();

            if (isAbsent && remarkValue == '') {
                $(this).css('border-color', '#d9534f');
                $(this).attr('title', 'Le motif d\'absence est requis');
            } else {
                $(this).css('border-color', '#ddd');
                $(this).removeAttr('title');
            }
        });

        // Validation avant soumission
        $('#save_attendance').on('submit', function(e) {
            var hasAbsences = false;
            var missingRemarks = [];
            var absentCount = 0;

            // Vérifier s'il y a des absences marquées
            $('input[type=checkbox]:checked').each(function() {
                hasAbsences = true;
                absentCount++;

                var staffId = $(this).data('staff-id');
                var remarkField = $('input[name="remark' + staffId + '"]');
                var staffName = $(this).closest('tr').find('td:eq(2)').text().trim();

                if (remarkField.val().trim() == '') {
                    missingRemarks.push(staffName);
                    remarkField.css('border-color', '#d9534f');
                }
            });

            // Vérifier les remarques manquantes pour les absences
            if (missingRemarks.length > 0) {
                var message = "Veuillez saisir un motif d'absence pour :\n- " + missingRemarks.join('\n- ');
                alert(message);
                e.preventDefault();
                return false;
            }

            // Confirmation pour les absences
            if (hasAbsences) {
                var confirmationMessage = absentCount + ' absence(s) ont été marquées. Voulez-vous continuer ?';
                if (!confirm(confirmationMessage)) {
                    e.preventDefault();
                    return false;
                }
            }

            $('#load').button('loading');
        });

        // Gestionnaire pour le jour férié
        $('#checkbox1').change(function() {
            if (this.checked) {
                var returnVal = confirm("<?php echo $this->lang->line('are_you_sure'); ?>");
                if (returnVal) {
                    $(this).prop("checked", true);
                    $("input[type=checkbox]").attr('disabled', true);

                    // Décocher toutes les absences
                    $("input[type=checkbox]").prop('checked', false);

                    // Mettre à jour les champs cachés et les lignes
                    $('input[type=checkbox]').each(function() {
                        var staffId = $(this).data('staff-id');
                        $('#hidden_attendance_' + staffId).val(holidayTypeId);
                        var parentRow = $(this).closest('tr');
                        parentRow.removeClass('absent-row present-row').addClass('holiday-row');

                        var remarkField = $('input[name="remark' + staffId + '"]');
                        remarkField.prop('disabled', true);
                        remarkField.attr('placeholder', 'Jour férié');
                        remarkField.css('border-color', '#ddd');

                        var statusText = $(this).closest('td').find('.status-text');
                        statusText.text('Congé').addClass('absent');
                    });

                    updateAbsenceCounter();
                } else {
                    $(this).prop("checked", false);
                    $("input[type=checkbox]").attr('disabled', false);
                }
            } else {
                $("input[type=checkbox]").attr('disabled', false);

                // Remettre les valeurs normales
                $('input[type=checkbox]').each(function() {
                    var staffId = $(this).data('staff-id');
                    var isChecked = $(this).is(':checked');

                    if (isChecked) {
                        $('#hidden_attendance_' + staffId).val(absentTypeId);
                        $(this).closest('tr').removeClass('present-row holiday-row').addClass('absent-row');
                    } else {
                        $('#hidden_attendance_' + staffId).val(presentTypeId);
                        $(this).closest('tr').removeClass('absent-row holiday-row').addClass('present-row');
                    }
                });
            }
        });

        // Fonction pour mettre à jour le compteur d'absences
        function updateAbsenceCounter() {
            var count = $('input[type=checkbox]:checked').length;
            $('tfoot .text-danger').text(count);
        }

        // Initialiser les états au chargement
        $('input[type=checkbox]').each(function() {
            var staffId = $(this).data('staff-id');
            var isChecked = $(this).is(':checked');
            var remarkField = $('input[name="remark' + staffId + '"]');
            var statusText = $(this).closest('td').find('.status-text');

            if (isChecked) {
                statusText.text('Absent').addClass('absent');
                remarkField.prop('disabled', false);
            } else {
                statusText.text('Présent').removeClass('absent');
                remarkField.prop('disabled', true);
            }
        });
    });
</script>

<script type="text/javascript">
    $(function () {
        $('.button-checkbox').each(function () {
            var $widget = $(this),
                $button = $widget.find('button'),
                $checkbox = $widget.find('input:checkbox'),
                color = $button.data('color'),
                settings = {
                    on: {
                        icon: 'glyphicon glyphicon-check'
                    },
                    off: {
                        icon: 'glyphicon glyphicon-unchecked'
                    }
                };
            $button.on('click', function () {
                $checkbox.prop('checked', !$checkbox.is(':checked'));
                $checkbox.triggerHandler('change');
                updateDisplay();
            });
            $checkbox.on('change', function () {
                updateDisplay();
            });

            function updateDisplay() {
                var isChecked = $checkbox.is(':checked');
                $button.data('state', (isChecked) ? "on" : "off");
                $button.find('.state-icon')
                    .removeClass()
                    .addClass('state-icon ' + settings[$button.data('state')].icon);
                if (isChecked) {
                    $button
                        .removeClass('btn-success')
                        .addClass('btn-' + color + ' active');
                } else {
                    $button
                        .removeClass('btn-' + color + ' active')
                        .addClass('btn-primary');
                }
            }

            function init() {
                updateDisplay();
                if ($button.find('.state-icon').length == 0) {
                    $button.prepend('<i class="state-icon ' + settings[$button.data('state')].icon + '"></i> ');
                }
            }
            init();
        });
    });
</script>