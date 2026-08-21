<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des appels</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            background: #ffffff;
            padding: 20px;
            font-size: 12px;
            color: #1e293b;
        }
        .header {
            text-align: center;
            padding: 20px 0 30px;
            border-bottom: 3px solid #273772;
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 24px;
            color: #273772;
            margin: 0;
            font-weight: 700;
        }
        .header .subtitle {
            font-size: 14px;
            color: #64748b;
            margin-top: 5px;
        }
        .header .date-generated {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 3px;
        }
        .info-box {
            background: #f8fafc;
            border-left: 4px solid #3b82f6;
            padding: 12px 16px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .info-box p {
            margin: 4px 0;
            font-size: 12px;
            color: #475569;
        }
        .info-box strong {
            color: #1e293b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table thead th {
            background: #273772;
            color: #ffffff;
            padding: 10px 12px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid #273772;
        }
        table tbody td {
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            font-size: 11px;
            color: #1e293b;
            vertical-align: middle;
        }
        table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        table tbody tr:hover {
            background: #f1f5f9;
        }
        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge-incoming {
            background: #dbeafe;
            color: #1d4ed8;
        }
        .badge-outgoing {
            background: #d1fae5;
            color: #059669;
        }
        .badge-missed {
            background: #fef3c7;
            color: #d97706;
        }
        .badge-other {
            background: #f1f5f9;
            color: #64748b;
        }
        .status-active {
            color: #059669;
            font-weight: 600;
        }
        .status-inactive {
            color: #94a3b8;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
        }
        .footer p {
            margin: 2px 0;
        }
        .text-center {
            text-align: center;
        }
        .text-muted {
            color: #94a3b8;
        }
        .summary {
            display: flex;
            gap: 30px;
            justify-content: center;
            margin: 15px 0 20px;
            flex-wrap: wrap;
        }
        .summary-item {
            text-align: center;
            padding: 8px 20px;
            background: #f8fafc;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .summary-item .number {
            font-size: 20px;
            font-weight: 700;
            color: #273772;
        }
        .summary-item .label {
            font-size: 11px;
            color: #64748b;
            margin-top: 2px;
        }
        .page-break {
            page-break-after: always;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 10px; }
        }
    </style>
</head>
<body>
<div class="header">
    <h1>📋 Liste des appels téléphoniques</h1>
    <div class="subtitle"><?php echo $title ?? 'Journal des appels'; ?></div>
    <div class="date-generated">Généré le : <?php echo $date_generated ?? date('d/m/Y H:i'); ?></div>
</div>

<!-- Résumé -->
<?php if (!empty($calls)) :
    $total = count($calls);
    $incoming = 0;
    $outgoing = 0;
    $missed = 0;
    foreach ($calls as $call) {
        if ($call['call_type'] == 'Incoming') $incoming++;
        elseif ($call['call_type'] == 'Outgoing') $outgoing++;
        elseif ($call['call_type'] == 'Missed') $missed++;
    }
    ?>
    <div class="summary">
        <div class="summary-item">
            <div class="number"><?php echo $total; ?></div>
            <div class="label">Total appels</div>
        </div>
        <div class="summary-item">
            <div class="number" style="color: #1d4ed8;"><?php echo $incoming; ?></div>
            <div class="label">Entrants</div>
        </div>
        <div class="summary-item">
            <div class="number" style="color: #059669;"><?php echo $outgoing; ?></div>
            <div class="label">Sortants</div>
        </div>
        <div class="summary-item">
            <div class="number" style="color: #d97706;"><?php echo $missed; ?></div>
            <div class="label">Manqués</div>
        </div>
    </div>
<?php endif; ?>

<!-- Tableau -->
<?php if (!empty($calls)) : ?>
    <table>
        <thead>
        <tr>
            <th style="width: 12%;">Type</th>
            <th style="width: 15%;">Nom</th>
            <th style="width: 12%;">Téléphone</th>
            <th style="width: 12%;">Date</th>
            <th style="width: 10%;">Durée</th>
            <th style="width: 20%;">Description</th>
            <th style="width: 19%;">Note</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($calls as $call) :
            $call_type_label = '';
            $badge_class = 'badge-other';
            if ($call['call_type'] == 'Incoming') {
                $call_type_label = 'Entrant';
                $badge_class = 'badge-incoming';
            } elseif ($call['call_type'] == 'Outgoing') {
                $call_type_label = 'Sortant';
                $badge_class = 'badge-outgoing';
            } elseif ($call['call_type'] == 'Missed') {
                $call_type_label = 'Manqué';
                $badge_class = 'badge-missed';
            } else {
                $call_type_label = $call['call_type'] ?? '';
            }

            $date_formatted = !empty($call['date']) ? date('d/m/Y', strtotime($call['date'])) : '';
            $follow_up_date = !empty($call['follow_up_date']) && $call['follow_up_date'] != '0000-00-00' ? date('d/m/Y', strtotime($call['follow_up_date'])) : '';
            ?>
            <tr>
                <td><span class="badge <?php echo $badge_class; ?>"><?php echo $call_type_label; ?></span></td>
                <td><?php echo htmlspecialchars($call['name'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($call['contact'] ?? ''); ?></td>
                <td><?php echo $date_formatted; ?></td>
                <td><?php echo htmlspecialchars($call['call_dureation'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($call['description'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($call['note'] ?? ''); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else : ?>
    <div class="text-center" style="padding: 60px 20px; color: #94a3b8;">
        <p style="font-size: 18px;">📭 Aucun appel enregistré</p>
        <p style="font-size: 13px;">Aucun appel ne correspond aux critères sélectionnés</p>
    </div>
<?php endif; ?>

<!-- Pied de page -->
<div class="footer">
    <p><?php echo date('Y'); ?> - <?php echo $this->setting_model->getCurrentSchoolName() ?? 'Diagoma'; ?></p>
    <p>Document généré automatiquement - <?php echo $total ?? 0; ?> appel(s) au total</p>
</div>
</body>
</html>