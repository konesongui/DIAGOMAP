<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des documents</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            background: #ffffff;
            padding: 15px;
            font-size: 11px;
            color: #1e293b;
        }
        .header {
            text-align: center;
            padding: 15px 0 20px;
            border-bottom: 2px solid #273772;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 20px;
            color: #273772;
            margin: 0;
        }
        .header .subtitle {
            font-size: 12px;
            color: #64748b;
            margin-top: 3px;
        }
        .header .date-generated {
            font-size: 10px;
            color: #94a3b8;
            margin-top: 2px;
        }
        .summary {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        .summary-item {
            text-align: center;
            padding: 6px 15px;
            background: #f8fafc;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }
        .summary-item .number {
            font-size: 18px;
            font-weight: 700;
            color: #273772;
        }
        .summary-item .label {
            font-size: 9px;
            color: #64748b;
            margin-top: 1px;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table thead th {
            background: #273772;
            color: #ffffff;
            padding: 6px 10px;
            text-align: left;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            border: 1px solid #273772;
        }
        table tbody td {
            padding: 5px 10px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
            color: #1e293b;
            vertical-align: middle;
        }
        table tbody tr:nth-child(even) {
            background: #f8fafc;
        }
        .badge-status {
            display: inline-block;
            padding: 1px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: 600;
        }
        .badge-status.actif {
            background: #d1fae5;
            color: #065f46;
        }
        .badge-status.archive {
            background: #fef3c7;
            color: #92400e;
        }
        .badge-category {
            display: inline-block;
            padding: 1px 8px;
            border-radius: 10px;
            font-size: 9px;
            background: #f1f5f9;
            color: #475569;
        }
        .badge-file-type {
            display: inline-block;
            padding: 1px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            background: #f1f5f9;
            color: #475569;
        }
        .badge-file-type.pdf { background: #fef2f2; color: #dc2626; }
        .badge-file-type.doc, .badge-file-type.docx { background: #dbeafe; color: #2563eb; }
        .badge-file-type.xls, .badge-file-type.xlsx { background: #d1fae5; color: #059669; }
        .text-center { text-align: center; }
        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
        }
        .footer p {
            margin: 1px 0;
        }
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 60px;
            color: rgba(39, 55, 114, 0.03);
            font-weight: 700;
            letter-spacing: 8px;
            pointer-events: none;
            z-index: 0;
        }
        @media print {
            body { padding: 10px; }
        }
    </style>
</head>
<body>
<div class="watermark">DOCUMENTS</div>

<div class="header">
    <h1>📄 Liste des documents</h1>
    <div class="subtitle"><?php echo $title ?? 'Gestion documentaire'; ?></div>
    <div class="date-generated">Généré le : <?php echo $date_generated ?? date('d/m/Y H:i'); ?></div>
</div>

<?php if (!empty($documents)) :
    $total = count($documents);
    $actif = 0;
    $archive = 0;
    foreach ($documents as $d) {
        if ($d['statut'] == 'actif') $actif++;
        else $archive++;
    }
    ?>
    <div class="summary">
        <div class="summary-item">
            <div class="number"><?php echo $total; ?></div>
            <div class="label">Total</div>
        </div>
        <div class="summary-item">
            <div class="number" style="color: #10b981;"><?php echo $actif; ?></div>
            <div class="label">Actifs</div>
        </div>
        <div class="summary-item">
            <div class="number" style="color: #f59e0b;"><?php echo $archive; ?></div>
            <div class="label">Archivés</div>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($documents)) : ?>
    <table>
        <thead>
        <tr>
            <th style="width: 5%;">#</th>
            <th style="width: 25%;">Titre</th>
            <th style="width: 18%;">Catégorie</th>
            <th style="width: 12%;">Type</th>
            <th style="width: 12%;">Taille</th>
            <th style="width: 13%;">Statut</th>
            <th style="width: 15%;">Date</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = 1;
        foreach ($documents as $doc) :
            $file_type = $doc['type_fichier'] ?? '';
            $badge_class = '';
            if (in_array($file_type, ['pdf'])) $badge_class = 'pdf';
            elseif (in_array($file_type, ['doc', 'docx'])) $badge_class = 'doc';
            elseif (in_array($file_type, ['xls', 'xlsx'])) $badge_class = 'xls';

            $size = $doc['taille'] ?? 0;
            if ($size >= 1048576) {
                $size_formatted = number_format($size / 1048576, 2) . ' MB';
            } elseif ($size >= 1024) {
                $size_formatted = number_format($size / 1024, 2) . ' KB';
            } else {
                $size_formatted = $size . ' B';
            }

            $category_labels = [
                'contrat' => 'Contrats',
                'facture' => 'Factures',
                'rapport' => 'Rapports',
                'projet' => 'Projets',
                'rh' => 'RH',
                'finance' => 'Finances',
                'marketing' => 'Marketing',
                'technique' => 'Technique',
                'autre' => 'Autre'
            ];
            $category_label = $category_labels[$doc['categorie']] ?? $doc['categorie'];
            ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td>
                    <strong><?php echo htmlspecialchars($doc['titre']); ?></strong>
                    <?php if (!empty($doc['description'])) : ?>
                        <br><span style="color: #94a3b8; font-size: 9px;">
                            <?php echo htmlspecialchars(substr($doc['description'], 0, 50)); ?>
                            <?php if (strlen($doc['description']) > 50) echo '...'; ?>
                        </span>
                    <?php endif; ?>
                </td>
                <td><span class="badge-category"><?php echo $category_label; ?></span></td>
                <td><span class="badge-file-type <?php echo $badge_class; ?>"><?php echo htmlspecialchars($file_type ?: '—'); ?></span></td>
                <td><?php echo $size_formatted; ?></td>
                <td><span class="badge-status <?php echo $doc['statut'] == 'actif' ? 'actif' : 'archive'; ?>"><?php echo $doc['statut'] == 'actif' ? 'Actif' : 'Archivé'; ?></span></td>
                <td><?php echo !empty($doc['date_creation']) ? date('d/m/Y', strtotime($doc['date_creation'])) : ''; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php else : ?>
    <div class="text-center" style="padding: 40px 20px; color: #94a3b8;">
        <p style="font-size: 36px; margin-bottom: 10px;">📭</p>
        <p style="font-size: 16px; font-weight: 500;">Aucun document enregistré</p>
    </div>
<?php endif; ?>

<div class="footer">
    <p><?php echo $this->setting_model->getCurrentSchoolName() ?? 'Diagoma'; ?> &bull; <?php echo date('Y'); ?></p>
    <p><?php echo date('d/m/Y H:i'); ?> &bull; <?php echo count($documents ?? []); ?> document(s)</p>
</div>
</body>
</html>