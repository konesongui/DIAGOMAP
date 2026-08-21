<div class="content-wrapper" style="min-height: 946px;">
    <section class="content-header">
        <h1><i class="fa fa-bar-chart"></i> Rapport de présence QR</h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-search"></i> Critères de recherche</h3>
                    </div>
                    <form method="post" action="<?php echo site_url('admin/qrattendance/attendance_report'); ?>">
                        <div class="box-body">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="start_date">Date de début</label>
                                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo html_escape($start_date); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="end_date">Date de fin</label>
                                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo html_escape($end_date); ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="staff_id">Employé</label>
                                        <select class="form-control" id="staff_id" name="staff_id">
                                            <option value="">-- Tous les employés --</option>
                                            <?php foreach ($staff_list as $staff) { ?>
                                                <option value="<?php echo (int) $staff['id']; ?>" <?php echo ((int) $selected_staff_id === (int) $staff['id']) ? 'selected="selected"' : ''; ?>>
                                                    <?php echo html_escape($staff['employee_id'] . ' - ' . trim($staff['name'] . ' ' . $staff['surname'])); ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-block">
                                            <i class="fa fa-search"></i> Rechercher
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-aqua"><i class="fa fa-list"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Pointages</span>
                        <span class="info-box-number"><?php echo isset($summary['records']) ? (int) $summary['records'] : 0; ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-sign-in"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Arrivées</span>
                        <span class="info-box-number"><?php echo isset($summary['arrivals']) ? (int) $summary['arrivals'] : 0; ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-sign-out"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Départs</span>
                        <span class="info-box-number"><?php echo isset($summary['departures']) ? (int) $summary['departures'] : 0; ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow"><i class="fa fa-hourglass-half"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Incomplets</span>
                        <span class="info-box-number"><?php echo isset($summary['incomplete']) ? (int) $summary['incomplete'] : 0; ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-teal"><i class="fa fa-check-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Vérifiés</span>
                        <span class="info-box-number"><?php echo isset($summary['verified']) ? (int) $summary['verified'] : 0; ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-sm-6">
                <div class="info-box">
                    <span class="info-box-icon bg-purple"><i class="fa fa-clock-o"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Durée moy.</span>
                        <span class="info-box-number" style="font-size: 16px;"><?php echo !empty($average_duration) ? html_escape(substr($average_duration, 0, 5)) : '-'; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-table"></i> Résultats détaillés</h3>
                        <?php if (!empty($report)) { ?>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-default btn-sm" onclick="window.print();">
                                    <i class="fa fa-print"></i> Imprimer
                                </button>
                                <button type="button" class="btn btn-success btn-sm" onclick="exportQrAttendanceTable();">
                                    <i class="fa fa-download"></i> Exporter CSV
                                </button>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="box-body table-responsive">
                        <table class="table table-striped table-bordered table-hover" id="qrAttendanceReportTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Employé</th>
                                    <th>Matricule</th>
                                    <th>Arrivée</th>
                                    <th>Départ</th>
                                    <th>Durée</th>
                                    <th>Téléphone</th>
                                    <th>Vérification</th>
                                    <th>Photo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($report)) { ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">Aucun pointage QR trouvé pour cette période.</td>
                                    </tr>
                                <?php } else { ?>
                                    <?php foreach ($report as $row) { ?>
                                        <tr>
                                            <td><?php echo html_escape(date('d/m/Y', strtotime($row['attendance_date']))); ?></td>
                                            <td><strong><?php echo html_escape(trim($row['name'] . ' ' . $row['surname'])); ?></strong></td>
                                            <td><?php echo html_escape($row['employee_id']); ?></td>
                                            <td>
                                                <?php if (!empty($row['arrival_time'])) { ?>
                                                    <label class="label label-success"><?php echo html_escape(substr($row['arrival_time'], 0, 5)); ?></label>
                                                <?php } else { ?>
                                                    <span class="text-muted">-</span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($row['departure_time'])) { ?>
                                                    <label class="label label-primary"><?php echo html_escape(substr($row['departure_time'], 0, 5)); ?></label>
                                                <?php } else { ?>
                                                    <label class="label label-warning">En attente</label>
                                                <?php } ?>
                                            </td>
                                            <td><?php echo !empty($row['duration']) ? html_escape($row['duration']) : '-'; ?></td>
                                            <td><?php echo !empty($row['contact_no']) ? html_escape($row['contact_no']) : '-'; ?></td>
                                            <td>
                                                <?php
                                                $verification_status = !empty($row['verification_status']) ? $row['verification_status'] : 'N/A';
                                                $verification_class = 'label-default';
                                                if ($verification_status === 'verified') {
                                                    $verification_class = 'label-success';
                                                } elseif ($verification_status === 'reference_created') {
                                                    $verification_class = 'label-info';
                                                } elseif ($verification_status === 'rejected') {
                                                    $verification_class = 'label-danger';
                                                }
                                                ?>
                                                <label class="label <?php echo $verification_class; ?>"><?php echo html_escape($verification_status); ?></label>
                                                <?php if (!empty($row['verification_details'])) { ?>
                                                    <br><small class="text-muted"><?php echo html_escape($row['verification_details']); ?></small>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($row['photo_path'])) { ?>
                                                    <a href="<?php echo base_url($row['photo_path']); ?>" target="_blank" class="btn btn-xs btn-info">
                                                        <i class="fa fa-image"></i> Voir
                                                    </a>
                                                <?php } else { ?>
                                                    <span class="text-muted">-</span>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($employee_totals)) { ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-default">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-users"></i> Total par employé</h3>
                        </div>
                        <div class="box-body table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Employé</th>
                                        <th>Matricule</th>
                                        <th>Pointages</th>
                                        <th>Arrivées</th>
                                        <th>Départs</th>
                                        <th>Incomplets</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($employee_totals as $total) { ?>
                                        <tr>
                                            <td><?php echo html_escape(trim($total['name'] . ' ' . $total['surname'])); ?></td>
                                            <td><?php echo html_escape($total['employee_id']); ?></td>
                                            <td><?php echo (int) $total['attendance_count']; ?></td>
                                            <td><?php echo (int) $total['arrival_count']; ?></td>
                                            <td><?php echo (int) $total['departure_count']; ?></td>
                                            <td><?php echo (int) $total['incomplete_count']; ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </section>
</div>

<script type="text/javascript">
    function exportQrAttendanceTable() {
        var table = document.getElementById('qrAttendanceReportTable');
        if (!table) {
            return;
        }

        var csv = [];
        for (var i = 0; i < table.rows.length; i++) {
            var row = [];
            for (var j = 0; j < table.rows[i].cells.length; j++) {
                row.push('"' + table.rows[i].cells[j].innerText.replace(/"/g, '""') + '"');
            }
            csv.push(row.join(','));
        }

        var csvFile = new Blob([csv.join('\n')], {type: 'text/csv;charset=utf-8;'});
        var downloadLink = document.createElement('a');
        downloadLink.href = URL.createObjectURL(csvFile);
        downloadLink.download = 'rapport-qr-presence-<?php echo html_escape($start_date); ?>-au-<?php echo html_escape($end_date); ?>.csv';
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }
</script>

<style type="text/css">
    @media print {
        .btn,
        form,
        .main-header,
        .main-sidebar,
        .content-header,
        .main-footer {
            display: none !important;
        }

        .content-wrapper,
        .content {
            margin: 0 !important;
            padding: 0 !important;
        }
    }
</style>
