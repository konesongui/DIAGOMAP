<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Présences du jour - <?php echo date('d/m/Y'); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f4f6f9; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 20px; }
        .header { border-bottom: 2px solid #3c8dbc; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { color: #333; font-size: 24px; }
        .header h1 i { color: #3c8dbc; margin-right: 10px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
        .stat-box { background: #f8f9fa; padding: 15px 20px; border-radius: 8px; border-left: 4px solid #3c8dbc; }
        .stat-box .number { font-size: 28px; font-weight: bold; color: #3c8dbc; }
        .stat-box .label { color: #666; font-size: 14px; margin-top: 5px; }
        .stat-box.green { border-left-color: #28a745; }
        .stat-box.green .number { color: #28a745; }
        .stat-box.blue { border-left-color: #17a2b8; }
        .stat-box.blue .number { color: #17a2b8; }
        .stat-box.yellow { border-left-color: #ffc107; }
        .stat-box.yellow .number { color: #ffc107; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #f8f9fa; font-weight: bold; }
        tr:hover { background: #f5f5f5; }
        .badge { padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
        .badge-success { background: #d4edda; color: #155724; }
        .badge-warning { background: #fff3cd; color: #856404; }
        .badge-info { background: #d1ecf1; color: #0c5460; }
        .no-data { text-align: center; padding: 40px; color: #999; }
        .btn { display: inline-block; padding: 8px 16px; background: #28a745; color: #fff; text-decoration: none; border-radius: 4px; font-size: 14px; }
        .btn:hover { background: #218838; }
        .btn i { margin-right: 5px; }
        .footer { margin-top: 20px; padding-top: 15px; border-top: 1px solid #ddd; color: #999; font-size: 12px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fa fa-qrcode"></i> Présences du jour - <?php echo date('d/m/Y'); ?></h1>
            <div style="margin-top: 10px;">
                <a href="<?php echo base_url('admin/qrattendance/attendance_report'); ?>" class="btn">
                    <i class="fa fa-bar-chart"></i> Rapport détaillé
                </a>
            </div>
        </div>

        <div class="stats">
            <div class="stat-box">
                <div class="number"><?php echo isset($stats['total']) ? (int)$stats['total'] : 0; ?></div>
                <div class="label"><i class="fa fa-users"></i> Total des pointages</div>
            </div>
            <div class="stat-box green">
                <div class="number"><?php echo isset($stats['arrivals']) ? (int)$stats['arrivals'] : 0; ?></div>
                <div class="label"><i class="fa fa-sign-in"></i> Arrivées</div>
            </div>
            <div class="stat-box blue">
                <div class="number"><?php echo isset($stats['departures']) ? (int)$stats['departures'] : 0; ?></div>
                <div class="label"><i class="fa fa-sign-out"></i> Départs</div>
            </div>
            <div class="stat-box yellow">
                <div class="number"><?php echo isset($stats['incomplete']) ? (int)$stats['incomplete'] : 0; ?></div>
                <div class="label"><i class="fa fa-hourglass-half"></i> En attente</div>
            </div>
        </div>

        <h3 style="margin: 20px 0 10px;"><i class="fa fa-list"></i> Détail des pointages</h3>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Employé</th>
                        <th>Matricule</th>
                        <th>Arrivée</th>
                        <th>Départ</th>
                        <th>Durée</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($attendances)) { ?>
                        <tr>
                            <td colspan="6" class="no-data">Aucune présence enregistrée aujourd'hui</td>
                        </tr>
                    <?php } else { ?>
                        <?php foreach ($attendances as $att) { ?>
                            <tr>
                                <td><strong><?php echo html_escape(($att['name'] ?? '') . ' ' . ($att['surname'] ?? '')); ?></strong></td>
                                <td><?php echo html_escape($att['employee_id'] ?? '-'); ?></td>
                                <td>
                                    <?php if (!empty($att['arrival_time'])) { ?>
                                        <span class="badge badge-success"><?php echo html_escape(substr($att['arrival_time'], 0, 5)); ?></span>
                                    <?php } else { ?>
                                        -
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if (!empty($att['departure_time'])) { ?>
                                        <span class="badge badge-info"><?php echo html_escape(substr($att['departure_time'], 0, 5)); ?></span>
                                    <?php } else { ?>
                                        <span class="badge badge-warning">En cours</span>
                                    <?php } ?>
                                </td>
                                <td><?php echo !empty($att['duration']) ? html_escape($att['duration']) : '-'; ?></td>
                                <td>
                                    <?php if (!empty($att['departure_time'])) { ?>
                                        <span class="badge badge-success">✅ Complet</span>
                                    <?php } else { ?>
                                        <span class="badge badge-warning">⏳ En attente</span>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="footer">
            Dernière mise à jour : <?php echo date('H:i:s'); ?>
        </div>
    </div>
</body>
</html>