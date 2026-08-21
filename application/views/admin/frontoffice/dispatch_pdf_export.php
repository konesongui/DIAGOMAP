<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des dispatches</title>
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
        .badge-dispatch {
            background: #dbeafe;
            color: #1d4ed8;
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
        .page-break {
            page-break-after: always;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 10px; }
        }
        /* Style pour les colonnes */
        .col-to { width: 20%; }
        .col-ref { width: 15%; }
        .col-from { width: 20%; }
        .col-date { width: 12%; }
        .col-address { width: 18%; }
        .col-note { width: 15%; }
    </style>
</head>
<body>
<div class="header">
    <h1>📋 Liste des dispatches</h1>
    <div class="subtitle"><?php echo $title ?? 'Journal des dispatches'; ?></div>
    <div class="date-generated">Généré le : <?php echo $date_generated ?? date('d/m/Y H:i'); ?></div>
</div>

<!-- Résumé -->
<?php if (!empty($dispatches)) :
    $total = count($dispatches);
    $today = date('Y-m-d');
    $today_count = 0;
    foreach ($dispatches as $d) {
        if (isset($d['date']) && $d['date'] === $today) $today_count++;
    }
    ?>
    <div class="summary">
        <div class="summary-item">
            <div class="number"><?php echo $total; ?></div>
            <div class="label">Total dispatches</div>
        </div>
        <div class="summary-item">
            <div class="number" style="color: #3b82f6;"><?php echo $today_count; ?></div>
            <div class="label">Aujourd'hui</div>
        </div>
        <div class="summary-item">
            <div class="number" style="color: #10b981;"><?php echo $total - $today_count; ?></div>
            <div class="label">Autres</div>
        </div>
    </div>
<?php endif; ?>

<!-- Tableau -->
<?php if (!empty($dispatches)) : ?>
    <table>
        <thead>
        <tr>
            <th class="col-to">Destinataire</th>
            <th class="col-ref">Référence</th>
            <th class="col-from">Expéditeur</th>
            <th class="col-date">Date</th>
            <th class="col-address">Adresse</th>
            <th class="col-note">Note</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($dispatches as $dispatch) :
            $date_formatted = !empty($dispatch['date']) ? date('d/m/Y', strtotime($dispatch['date'])) : '';
            ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($dispatch['to_title'] ?? ''); ?></strong></td>
                <td><?php echo htmlspecialchars($dispatch['reference_no'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($dispatch['from_title'] ?? ''); ?></td>
                <td><?php echo $date_formatted; ?></td>
                <td><?php echo htmlspecialchars($dispatch['address'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($dispatch['note'] ?? ''); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else : ?>
    <div class="text-center" style="padding: 60px 20px; color: #94a3b8;">
        <p style="font-size: 18px;">📭 Aucun dispatch enregistré</p>
        <p style="font-size: 13px;">Aucun dispatch ne correspond aux critères sélectionnés</p>
    </div>
<?php endif; ?>

<!-- Pied de page -->
<div class="footer">
    <p><?php echo date('Y'); ?> - <?php echo $this->setting_model->getCurrentSchoolName() ?? 'Diagoma'; ?></p>
    <p>Document généré automatiquement - <?php echo $total ?? 0; ?> dispatch(s) au total</p>
    <p style="margin-top: 4px; font-size: 9px; color: #cbd5e1;">
        * Ce document est généré automatiquement, merci de ne pas le modifier
    </p>
</div>
</body>
</html>