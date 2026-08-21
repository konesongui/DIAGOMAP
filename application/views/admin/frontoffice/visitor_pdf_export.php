<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo $title; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', 'Helvetica Neue', Arial, sans-serif;
            padding: 20px;
            background: #fff;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 3px solid #273772;
            margin-bottom: 20px;
        }
        .header h2 {
            color: #273772;
            font-size: 24px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .header p {
            color: #64748b;
            font-size: 12px;
            margin-top: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        thead th {
            background: #273772;
            color: #ffffff;
            padding: 10px 12px;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }
        tbody td {
            padding: 8px 12px;
            border-bottom: 1px solid #e2e8f0;
            color: #1e293b;
        }
        tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
        }
        .badge-meeting { background: #dbeafe; color: #1d4ed8; }
        .badge-delivery { background: #d1fae5; color: #059669; }
        .badge-visit { background: #fef3c7; color: #d97706; }
        .badge-other { background: #f1f5f9; color: #64748b; }
        .status-active {
            color: #10b981;
            font-weight: 600;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #94a3b8;
            font-size: 11px;
        }
    </style>
</head>
<body>
<div class="header">
    <h2><?php echo $title; ?></h2>
    <p>Généré le <?php echo $date_generated; ?></p>
</div>

<table>
    <thead>
    <tr>
        <th>Motif</th>
        <th>Nom</th>
        <th>Téléphone</th>
        <th>Date</th>
        <th>Arrivée</th>
        <th>Sortie</th>
        <th>Observation</th>
    </tr>
    </thead>
    <tbody>
    <?php if (empty($visitors)) : ?>
        <tr>
            <td colspan="7" style="text-align:center;padding:30px 0;color:#94a3b8;">
                Aucun visiteur trouvé
            </td>
        </tr>
    <?php else : ?>
        <?php foreach ($visitors as $v) :
            $badgeClass = 'other';
            $purpose = strtolower($v['purpose'] ?? '');
            if (strpos($purpose, 'réunion') !== false || strpos($purpose, 'meeting') !== false) $badgeClass = 'meeting';
            elseif (strpos($purpose, 'livraison') !== false || strpos($purpose, 'delivery') !== false) $badgeClass = 'delivery';
            elseif (strpos($purpose, 'visite') !== false || strpos($purpose, 'visit') !== false) $badgeClass = 'visit';
            ?>
            <tr>
                <td><span class="badge badge-<?php echo $badgeClass; ?>"><?php echo $v['purpose']; ?></span></td>
                <td><?php echo $v['name']; ?></td>
                <td><?php echo $v['contact']; ?></td>
                <td><?php echo date('d/m/Y', strtotime($v['date'])); ?></td>
                <td><?php echo $v['in_time']; ?></td>
                <td><?php echo !empty($v['out_time']) ? $v['out_time'] : '<span class="status-active">● En cours</span>'; ?></td>
                <td><?php echo $v['note']; ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<div class="footer">
    <p>Document généré depuis le système de gestion des visiteurs</p>
    <p>Total: <?php echo count($visitors); ?> visiteur(s)</p>
</div>
</body>
</html>