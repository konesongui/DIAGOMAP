<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Balance generale OHADA</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #222; }
        h2 { margin-bottom: 4px; }
        p { margin-top: 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #d8d8d8; padding: 6px; }
        th { background: #f3f4f6; text-align: left; }
        .text-right { text-align: right; }
        .total { font-weight: bold; background: #fafafa; }
    </style>
</head>
<body>
    <h2>Balance generale OHADA</h2>
    <p>Periode du <?php echo date('d/m/Y', strtotime($date_debut)); ?> au <?php echo date('d/m/Y', strtotime($date_fin)); ?></p>

    <table>
        <thead>
        <tr>
            <th>Compte</th>
            <th>Libelle</th>
            <th>Classe</th>
            <th class="text-right">Ouverture debit</th>
            <th class="text-right">Ouverture credit</th>
            <th class="text-right">Mouvement debit</th>
            <th class="text-right">Mouvement credit</th>
            <th class="text-right">Cloture debit</th>
            <th class="text-right">Cloture credit</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($balance as $row) : ?>
            <tr>
                <td><?php echo html_escape($row['compte']); ?></td>
                <td><?php echo html_escape($row['libelle']); ?></td>
                <td><?php echo html_escape($row['classe']); ?></td>
                <td class="text-right"><?php echo number_format($row['solde_ouverture_debit'], 2, ',', ' '); ?></td>
                <td class="text-right"><?php echo number_format($row['solde_ouverture_credit'], 2, ',', ' '); ?></td>
                <td class="text-right"><?php echo number_format($row['mouvement_debit'], 2, ',', ' '); ?></td>
                <td class="text-right"><?php echo number_format($row['mouvement_credit'], 2, ',', ' '); ?></td>
                <td class="text-right"><?php echo number_format($row['solde_cloture_debit'], 2, ',', ' '); ?></td>
                <td class="text-right"><?php echo number_format($row['solde_cloture_credit'], 2, ',', ' '); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr class="total">
            <td colspan="3">TOTAUX</td>
            <td class="text-right"><?php echo number_format($total_ouverture_debit, 2, ',', ' '); ?></td>
            <td class="text-right"><?php echo number_format($total_ouverture_credit, 2, ',', ' '); ?></td>
            <td class="text-right"><?php echo number_format($total_debit, 2, ',', ' '); ?></td>
            <td class="text-right"><?php echo number_format($total_credit, 2, ',', ' '); ?></td>
            <td class="text-right"><?php echo number_format($total_solde_debiteur, 2, ',', ' '); ?></td>
            <td class="text-right"><?php echo number_format($total_solde_crediteur, 2, ',', ' '); ?></td>
        </tr>
        </tbody>
    </table>
</body>
</html>
