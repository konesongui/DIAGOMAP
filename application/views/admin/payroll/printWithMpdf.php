<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bulletin de paie</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 5px; text-align: left; }
    </style>
</head>
<body>
<div class="header">
    <h3><?= isset($company['name']) ? htmlspecialchars($company['name']) : 'Entreprise' ?></h3>
    <p>
        <?= isset($company['address']) ? htmlspecialchars($company['address']) : 'Adresse non définie' ?>
        - Tel: <?= isset($company['phone']) ? htmlspecialchars($company['phone']) : 'N/A' ?>
    </p>
</div>

<div class="info">
    <strong>Salarié:</strong>
    <?= (isset($payslip['name']) ? htmlspecialchars($payslip['name']) : '') . ' ' .
    (isset($payslip['surname']) ? htmlspecialchars($payslip['surname']) : '') ?><br>

    <strong>Mois:</strong> <?= isset($payslip['month']) ? htmlspecialchars($payslip['month']) : '' ?><br>
    <strong>Année:</strong> <?= isset($payslip['year']) ? htmlspecialchars($payslip['year']) : '' ?><br>
    <strong>N° bulletin:</strong> <?= isset($payslip['payslip_number']) ? htmlspecialchars($payslip['payslip_number']) : '' ?><br>
</div>

<table>
    <tr>
        <th>Rubrique</th>
        <th>Montant</th>
    </tr>
    <tr>
        <td>Salaire Net</td>
        <td>
            <?= isset($payslip['net_salary'])
                ? number_format((float)$payslip['net_salary'], 2, ',', ' ') . ' ' .
                (isset($company['currency']) ? htmlspecialchars($company['currency']) : 'FCFA')
                : '0 ' . (isset($company['currency']) ? htmlspecialchars($company['currency']) : 'FCFA') ?>
        </td>
    </tr>
    <!-- Tu pourras ajouter ici primes, indemnités et retenues -->
</table>
</body>
</html>
