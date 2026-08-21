<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des rapports</title>
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
            position: relative;
        }
        .header .logo {
            max-height: 60px;
            margin-bottom: 10px;
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
        .header .stats-info {
            font-size: 12px;
            color: #475569;
            margin-top: 8px;
        }
        .header .stats-info span {
            margin: 0 10px;
            padding: 2px 12px;
            border-radius: 12px;
        }
        .filters-info {
            background: #f8fafc;
            border-left: 4px solid #3b82f6;
            padding: 10px 16px;
            margin-bottom: 20px;
            border-radius: 4px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .filters-info .filter-item {
            font-size: 11px;
            color: #475569;
        }
        .filters-info .filter-item strong {
            color: #1e293b;
            font-weight: 600;
        }
        .filters-info .filter-item .value {
            color: #3b82f6;
            font-weight: 500;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table thead th {
            background: linear-gradient(135deg, #273772, #1a2558);
            color: #ffffff;
            padding: 10px 12px;
            text-align: left;
            font-size: 10px;
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
        .badge-status {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
        }
        .badge-status.en-attente { background: #fef3c7; color: #92400e; }
        .badge-status.en-cours { background: #dbeafe; color: #1d4ed8; }
        .badge-status.termine { background: #d1fae5; color: #065f46; }
        .badge-status.archive { background: #e2e8f0; color: #475569; }

        .badge-priority {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: 600;
        }
        .badge-priority.basse { background: #e2e8f0; color: #475569; }
        .badge-priority.normale { background: #dbeafe; color: #1d4ed8; }
        .badge-priority.haute { background: #fef3c7; color: #92400e; }
        .badge-priority.urgente { background: #fef2f2; color: #991b1b; }

        .text-center { text-align: center; }
        .text-muted { color: #94a3b8; }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
        }
        .footer p { margin: 2px 0; }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            color: rgba(39, 55, 114, 0.03);
            font-weight: 700;
            letter-spacing: 10px;
            pointer-events: none;
            z-index: 0;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
            margin: 15px 0 20px;
        }
        .summary-card {
            text-align: center;
            padding: 10px 12px;
            background: #ffffff;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        .summary-card .number {
            font-size: 20px;
            font-weight: 700;
            color: #273772;
        }
        .summary-card .label {
            font-size: 9px;
            color: #64748b;
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        @media print {
            .no-print { display: none; }
            body { padding: 10px; }
        }
    </style>
</head>
<body>
<!-- Filigrane -->
<div class="watermark">RAPPORTS</div>

<div class="header">
    <?php if (!empty($logo_path)) : ?>
        <img src="<?php echo $logo_path; ?>" alt="Logo" class="logo">
    <?php endif; ?>
    <h1>📊 Liste des rapports</h1>
    <div class="subtitle"><?php echo $title ?? 'Gestion des rapports'; ?></div>
    <div class="date-generated">Généré le : <?php echo $date_generated ?? date('d/m/Y H:i'); ?></div>
    <?php if (!empty($stats)) : ?>
        <div class="stats-info">
            <span style="background: #3b82f6; color: #fff;">Total: <?php echo $stats['total'] ?? 0; ?></span>
            <span style="background: #f59e0b; color: #fff;">En attente: <?php echo $stats['en_attente'] ?? 0; ?></span>
            <span style="background: #8b5cf6; color: #fff;">En cours: <?php echo $stats['en_cours'] ?? 0; ?></span>
            <span style="background: #10b981; color: #fff;">Terminés: <?php echo $stats['termine'] ?? 0; ?></span>
            <span style="background: #6b7280; color: #fff;">Archivés: <?php echo $stats['archive'] ?? 0; ?></span>
        </div>
    <?php endif; ?>
</div>

<!-- Filtres appliqués -->
<?php if (!empty($filters)) : ?>
    <div class="filters-info">
        <?php if (!empty($filters['type_rapport'])) : ?>
            <div class="filter-item"><strong>Type :</strong> <span class="value"><?php echo htmlspecialchars($filters['type_rapport']); ?></span></div>
        <?php endif; ?>
        <?php if (!empty($filters['statut'])) : ?>
            <div class="filter-item"><strong>Statut :</strong> <span class="value"><?php echo isset($statuses[$filters['statut']]) ? $statuses[$filters['statut']] : $filters['statut']; ?></span></div>
        <?php endif; ?>
        <?php if (!empty($filters['date_from'])) : ?>
            <div class="filter-item"><strong>Date du :</strong> <span class="value"><?php echo date('d/m/Y', strtotime($filters['date_from'])); ?></span></div>
        <?php endif; ?>
        <?php if (!empty($filters['date_to'])) : ?>
            <div class="filter-item"><strong>Date au :</strong> <span class="value"><?php echo date('d/m/Y', strtotime($filters['date_to'])); ?></span></div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Tableau -->
<?php if (!empty($rapports)) : ?>
    <table>
        <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th style="width: 20%;">Titre</th>
            <th style="width: 15%;">Type</th>
            <th style="width: 12%;">Statut</th>
            <th style="width: 12%;">Priorité</th>
            <th style="width: 18%;">Période</th>
            <th style="width: 18%;">Date création</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = 1;
        $typeLabels = [
            'finance' => 'Rapport financier',
            'statistique' => 'Rapport statistique',
            'projet' => 'Rapport de projet',
            'activite' => 'Rapport d\'activité',
            'rh' => 'Rapport RH',
            'vente' => 'Rapport de vente',
            'inventaire' => 'Rapport d\'inventaire',
            'autre' => 'Autre'
        ];
        $statusLabels = [
            'en_attente' => 'En attente',
            'en_cours' => 'En cours',
            'termine' => 'Terminé',
            'archive' => 'Archivé'
        ];
        $priorityLabels = [
            'basse' => 'Basse',
            'normale' => 'Normale',
            'haute' => 'Haute',
            'urgente' => 'Urgente'
        ];
        foreach ($rapports as $rapport) :
            $status = $rapport['statut'] ?? 'en_attente';
            $priority = $rapport['priorite'] ?? 'normale';
            $type = $rapport['type_rapport'] ?? 'autre';
            ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td>
                    <strong><?php echo htmlspecialchars($rapport['titre'] ?? ''); ?></strong>
                    <?php if (!empty($rapport['description'])) : ?>
                        <br><span style="color: #94a3b8; font-size: 9px;">
                            <?php echo htmlspecialchars(substr($rapport['description'], 0, 60)); ?>
                            <?php if (strlen($rapport['description'] ?? '') > 60) echo '...'; ?>
                        </span>
                    <?php endif; ?>
                </td>
                <td>
                    <span style="background: #f1f5f9; padding: 2px 10px; border-radius: 10px; font-size: 10px; color: #475569;">
                        <?php echo isset($typeLabels[$type]) ? $typeLabels[$type] : $type; ?>
                    </span>
                </td>
                <td>
                    <span class="badge-status <?php echo $status; ?>">
                        <?php echo isset($statusLabels[$status]) ? $statusLabels[$status] : $status; ?>
                    </span>
                </td>
                <td>
                    <span class="badge-priority <?php echo $priority; ?>">
                        <?php echo isset($priorityLabels[$priority]) ? $priorityLabels[$priority] : $priority; ?>
                    </span>
                </td>
                <td>
                    <?php if (!empty($rapport['periode_debut']) && !empty($rapport['periode_fin'])) : ?>
                        <?php echo date('d/m/Y', strtotime($rapport['periode_debut'])); ?>
                        <span style="color: #94a3b8;">→</span>
                        <?php echo date('d/m/Y', strtotime($rapport['periode_fin'])); ?>
                    <?php else : ?>
                        <span style="color: #94a3b8;">—</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo !empty($rapport['date_creation']) ? date('d/m/Y', strtotime($rapport['date_creation'])) : ''; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else : ?>
    <div class="text-center" style="padding: 60px 20px; color: #94a3b8;">
        <p style="font-size: 48px; margin-bottom: 16px;">📭</p>
        <p style="font-size: 18px; font-weight: 500;">Aucun rapport enregistré</p>
        <p style="font-size: 13px; margin-top: 8px;">Aucun rapport ne correspond aux critères sélectionnés</p>
    </div>
<?php endif; ?>

<!-- Pied de page -->
<div class="footer">
    <p>
        <span style="font-weight: 600; color: #273772;"><?php echo $this->setting_model->getCurrentSchoolName() ?? 'Diagoma'; ?></span>
        &nbsp;•&nbsp; <?php echo date('Y'); ?>
    </p>
    <p>Document généré automatiquement le <?php echo date('d/m/Y H:i'); ?> - <?php echo count($rapports ?? []); ?> rapport(s)</p>
    <p style="margin-top: 4px; font-size: 9px; color: #cbd5e1;">
        * Ce document est confidentiel et généré automatiquement
    </p>
</div>
</body>
</html>