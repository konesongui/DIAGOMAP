<div class="content-wrapper" style="min-height: 946px;">
    <?php 
    // Protection contre les variables manquantes
    if (!isset($stats)) $stats = ['total' => 0, 'arrivals' => 0, 'departures' => 0, 'incomplete' => 0];
    if (!isset($attendances)) $attendances = [];
    if (!isset($today_date)) $today_date = date('d/m/Y');
    ?>
    
    <section class="content-header">
        <h1><i class="fa fa-qrcode"></i> Présences QR du jour</h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-calendar-check-o"></i> Pointages du <?php echo html_escape($today_date); ?>
                        </h3>
                        <div class="box-tools pull-right">
                            <a href="<?php echo base_url('admin/qrattendance/attendance_report'); ?>" class="btn btn-success btn-sm">
                                <i class="fa fa-bar-chart"></i> Rapport détaillé
                            </a>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-aqua"><i class="fa fa-users"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total</span>
                                        <span class="info-box-number"><?php echo (int) $stats['total']; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-green"><i class="fa fa-sign-in"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Arrivées</span>
                                        <span class="info-box-number"><?php echo (int) $stats['arrivals']; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-blue"><i class="fa fa-sign-out"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Départs</span>
                                        <span class="info-box-number"><?php echo (int) $stats['departures']; ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-yellow"><i class="fa fa-hourglass-half"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">En attente</span>
                                        <span class="info-box-number"><?php echo (int) $stats['incomplete']; ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Employé</th>
                                        <th>Matricule</th>
                                        <th>Arrivée</th>
                                        <th>Départ</th>
                                        <th>Durée</th>
                                        <th>Téléphone</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($attendances)) { ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Aucune présence QR enregistrée aujourd'hui.</td>
                                        </tr>
                                    <?php } else { ?>
                                        <?php foreach ($attendances as $att) { ?>
                                            <tr>
                                                <td><strong><?php echo html_escape(trim(($att['name'] ?? '') . ' ' . ($att['surname'] ?? ''))); ?></strong></td>
                                                <td><?php echo html_escape($att['employee_id'] ?? '-'); ?></td>
                                                <td>
                                                    <?php if (!empty($att['arrival_time'])) { ?>
                                                        <label class="label label-success"><?php echo html_escape(substr($att['arrival_time'], 0, 5)); ?></label>
                                                    <?php } else { ?>
                                                        <span class="text-muted">-</span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($att['departure_time'])) { ?>
                                                        <label class="label label-primary"><?php echo html_escape(substr($att['departure_time'], 0, 5)); ?></label>
                                                    <?php } else { ?>
                                                        <label class="label label-warning">En cours</label>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php echo !empty($att['duration']) ? html_escape($att['duration']) : '<span class="text-muted">-</span>'; ?>
                                                </td>
                                                <td><?php echo !empty($att['contact_no']) ? html_escape($att['contact_no']) : '-'; ?></td>
                                                <td>
                                                    <?php if (!empty($att['departure_time'])) { ?>
                                                        <label class="label label-success">Complet</label>
                                                    <?php } else { ?>
                                                        <label class="label label-warning">En attente</label>
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
        </div>
    </section>
</div>