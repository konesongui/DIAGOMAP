<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();

// Définir des valeurs par défaut pour éviter les erreurs
$companyName = $companyName ?? 'Entreprise';
$companyLogo = $companyLogo ?? 'Logo Entreprise';
$companyAddress = $companyAddress ?? 'Adresse non définie';
$companyPhone = $companyPhone ?? 'Téléphone non défini';
$employeeName = $employeeName ?? '';
$employeeSurname = $employeeSurname ?? '';
$employeeId = $employeeId ?? '';
$employeeDesignation = $employeeDesignation ?? 'Non défini';
$employeeDepartment = $employeeDepartment ?? 'Non défini';
$employeeMonth = $employeeMonth ?? '';
$employeeYear = $employeeYear ?? '';
$paymentDate = $paymentDate ?? date('d/m/Y');
$cnpsNo = $cnpsNo ?? '';
$PaymentMode = $PaymentMode ?? '';

// Données financières avec valeurs par défaut
$basicSalary = $basicSalary ?? 0;
$surSalary = $surSalary ?? 0;
$primeAnciennete = $primeAnciennete ?? 0;
$primeTransport = $primeTransport ?? 0;
$primeTechnique = $primeTechnique ?? 0;
$forfaitHS = $forfaitHS ?? 0;
$primeResponsabilite = $primeResponsabilite ?? 0;
$bonus = $bonus ?? 0;
$cnpsRegime = $cnpsRegime ?? 0;
$cnpsAccident = $cnpsAccident ?? 0;
$impotRevenu = $impotRevenu ?? 0;
$cmu = $cmu ?? 0;
$avancesAcomptes = $avancesAcomptes ?? 0;
$grossSalary = $grossSalary ?? 0;
$totalDeduction = $totalDeduction ?? 0;
$netSalary = $netSalary ?? 0;

// Allocations avec tableaux vides par défaut
$positive_allowance = $positive_allowance ?? [];
$negative_allowance = $negative_allowance ?? [];

// Logo avec fallback
$logo_path = base_url() . "/uploads/school_content/admin_logo/" . ($sch_setting->admin_logo ?? 'default_logo.png');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bulletin de Paie - <?php echo htmlspecialchars($companyName); ?></title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 12px;
            color: #13305B;
            margin: 0;
            padding: 0;
            width: 21cm;
            background: #fff;
        }
        .header {
            background: linear-gradient(90deg, #13305B, #4A77A8);
            color: white;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            border-radius: 0 0 10px 10px;
            margin-bottom: 15px;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 0 15px;
        }
        .section {
            margin-bottom: 15px;
        }
        .section-title {
            background: #13305B;
            color: white;
            padding: 6px;
            font-weight: bold;
            border-radius: 4px 4px 0 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }
        th {
            background: #13305B;
            color: white;
            padding: 6px;
            font-size: 12px;
            text-align: left;
        }
        td {
            padding: 6px;
            font-size: 12px;
            border: 1px solid #ddd;
        }
        .info-table td {
            border: 1px solid #ddd;
        }
        .info-table .label {
            background-color: #f9f9f9;
            font-weight: bold;
            width: 30%;
        }
        .total-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .net-salary {
            background-color: #e8f5e8;
            font-weight: bold;
            font-size: 13px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .signature-section {
            margin-top: 40px;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 200px;
            margin-top: 40px;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            text-align: center;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .company-info {
            background: #F6F9FC;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .company-logo {
            width: 100px;
            height: 100px;
            object-fit: contain;
        }
        .company-details {
            flex: 1;
        }
        .no-data {
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
<div class="header">BULLETIN DE PAIE</div>

<div class="container">
    <!-- Informations de l'entreprise -->
    <div class="company-info">
        <img src="<?= base_url() . "/uploads/school_content/admin_logo/" . $companyLogo ?>"
             alt="Logo entreprise"
             class="company-logo" />
        <div class="company-details">
            <strong style="font-size: 14px; display: block; margin-bottom: 6px;"><?php echo htmlspecialchars($companyName); ?></strong>
            <div>Adresse : <?php echo htmlspecialchars($companyAddress); ?></div>
            <div>Tél : <?php echo htmlspecialchars($companyPhone); ?></div>
        </div>
    </div>

    <!-- Informations employé -->
    <div class="section">
        <div class="section-title">INFORMATIONS EMPLOYÉ</div>
        <table class="info-table">
            <tr>
                <td class="label">Nom & Prénom</td>
                <td><?php echo htmlspecialchars($employeeName . ' ' . $employeeSurname); ?></td>
                <td class="label">Matricule</td>
                <td><?php echo !empty($employeeId) ? htmlspecialchars($employeeId) : '<span class="no-data">Non défini</span>'; ?></td>
            </tr>
            <tr>
                <td class="label">Poste</td>
                <td><?php echo htmlspecialchars($employeeDesignation); ?></td>
                <td class="label">Département</td>
                <td><?php echo htmlspecialchars($employeeDepartment); ?></td>
            </tr>
            <tr>
                <td class="label">Période</td>
                <td><?php echo htmlspecialchars($employeeMonth . ' ' . $employeeYear); ?></td>
                <td class="label">Date de paiement</td>
                <td><?php echo htmlspecialchars($paymentDate); ?></td>
            </tr>
            <tr>
                <td class="label">N° CNPS</td>
                <td><?php echo !empty($cnpsNo) ? htmlspecialchars($cnpsNo) : '<span class="no-data">Non défini</span>'; ?></td>
                <td class="label">Mode de paeiment</td>
                <td><?php echo !empty($PaymentMode) ? htmlspecialchars($PaymentMode) : '<span class="no-data">Non défini</span>'; ?></td>
            </tr>
        </table>
    </div>

    <!-- REVENUS -->
    <div class="section">
        <div class="section-title">REVENUS</div>
        <table>
            <tr>
                <th>Description</th>
                <th class="text-right">Montant (<?php echo $currency_symbol; ?>)</th>
            </tr>

            <?php if ($basicSalary > 0): ?>
                <tr>
                    <td>Salaire catégoriel</td>
                    <td class="text-right"><?php echo number_format($basicSalary, 2, ',', ' '); ?></td>
                </tr>
            <?php endif; ?>
            <?php if ($surSalary > 0): ?>
                <tr>
                    <td>Sursalaire</td>
                    <td class="text-right"><?php echo number_format($surSalary, 2, ',', ' '); ?></td>
                </tr>
            <?php endif; ?>

            <?php if ($primeAnciennete > 0): ?>
                <tr>
                    <td>Prime d'ancienneté</td>
                    <td class="text-right"><?php echo number_format($primeAnciennete, 2, ',', ' '); ?></td>
                </tr>
            <?php endif; ?>

            <?php if ($primeTransport > 0): ?>
                <tr>
                    <td>Prime de transport</td>
                    <td class="text-right"><?php echo number_format($primeTransport, 2, ',', ' '); ?></td>
                </tr>
            <?php endif; ?>

            <?php if ($primeTechnique > 0): ?>
                <tr>
                    <td>Prime technique</td>
                    <td class="text-right"><?php echo number_format($primeTechnique, 2, ',', ' '); ?></td>
                </tr>
            <?php endif; ?>

            <?php if ($forfaitHS > 0): ?>
                <tr>
                    <td>Forfait heures supplémentaires</td>
                    <td class="text-right"><?php echo number_format($forfaitHS, 2, ',', ' '); ?></td>
                </tr>
            <?php endif; ?>

            <?php if ($primeResponsabilite > 0): ?>
                <tr>
                    <td>Prime de responsabilité</td>
                    <td class="text-right"><?php echo number_format($primeResponsabilite, 2, ',', ' '); ?></td>
                </tr>
            <?php endif; ?>

            <?php if ($bonus > 0): ?>
                <tr>
                    <td>Bonus</td>
                    <td class="text-right"><?php echo number_format($bonus, 2, ',', ' '); ?></td>
                </tr>
            <?php endif; ?>

            <!-- Allocations positives -->
            <?php if (!empty($positive_allowance)): ?>
                <?php foreach ($positive_allowance as $allowance): ?>
                    <?php if (($allowance['amount'] ?? 0) > 0): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($allowance['allowance_type'] ?? 'Allocation'); ?></td>
                            <td class="text-right"><?php echo number_format($allowance['amount'] ?? 0, 2, ',', ' '); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>

            <tr class="total-row">
                <td><strong>TOTAL REVENUS BRUTS</strong></td>
                <td class="text-right"><strong><?php echo number_format($grossSalary, 2, ',', ' '); ?></strong></td>
            </tr>
        </table>
    </div>

    <!-- DÉDUCTIONS -->
    <div class="section">
        <div class="section-title">DÉDUCTIONS</div>
        <table>
            <tr>
                <th>Description</th>
                <th class="text-right">Montant (<?php echo $currency_symbol; ?>)</th>
            </tr>

            <?php if ($cnpsRegime > 0): ?>
                <tr>
                    <td>CNPS - Régime général</td>
                    <td class="text-right"><?php echo number_format($cnpsRegime, 2, ',', ' '); ?></td>
                </tr>
            <?php endif; ?>

            <?php if ($cnpsAccident > 0): ?>
                <tr>
                    <td>CNPS - Accident de travail</td>
                    <td class="text-right"><?php echo number_format($cnpsAccident, 2, ',', ' '); ?></td>
                </tr>
            <?php endif; ?>

            <?php if ($impotRevenu > 0): ?>
                <tr>
                    <td>Impôt sur le revenu</td>
                    <td class="text-right"><?php echo number_format($impotRevenu, 2, ',', ' '); ?></td>
                </tr>
            <?php endif; ?>

            <?php if ($cmu > 0): ?>
                <tr>
                    <td>CMU</td>
                    <td class="text-right"><?php echo number_format($cmu, 2, ',', ' '); ?></td>
                </tr>
            <?php endif; ?>

            <?php if ($avancesAcomptes > 0): ?>
                <tr>
                    <td>Avances et acomptes</td>
                    <td class="text-right"><?php echo number_format($avancesAcomptes, 2, ',', ' '); ?></td>
                </tr>
            <?php endif; ?>

            <!-- Allocations négatives -->
            <?php if (!empty($negative_allowance)): ?>
                <?php foreach ($negative_allowance as $deduction): ?>
                    <?php if (($deduction['amount'] ?? 0) > 0): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($deduction['allowance_type'] ?? 'Déduction'); ?></td>
                            <td class="text-right"><?php echo number_format($deduction['amount'] ?? 0, 2, ',', ' '); ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>

            <tr class="total-row">
                <td><strong>TOTAL DÉDUCTIONS</strong></td>
                <td class="text-right"><strong><?php echo number_format($totalDeduction, 2, ',', ' '); ?></strong></td>
            </tr>
        </table>
    </div>

    <!-- RÉCAPITULATIF -->
    <div class="section">
        <div class="section-title">RÉCAPITULATIF</div>
        <table>
            <tr class="total-row">
                <td><strong>SALAIRE BRUT</strong></td>
                <td class="text-right"><strong><?php echo number_format($grossSalary, 2, ',', ' '); ?></strong></td>
            </tr>
            <tr class="total-row">
                <td><strong>TOTAL DÉDUCTIONS</strong></td>
                <td class="text-right"><strong>- <?php echo number_format($totalDeduction, 2, ',', ' '); ?></strong></td>
            </tr>
            <tr class="net-salary">
                <td><strong>SALAIRE NET À PAYER</strong></td>
                <td class="text-right"><strong><?php echo number_format($netSalary, 2, ',', ' '); ?></strong></td>
            </tr>
        </table>
    </div>

    <!-- Signatures -->
    <div class="signature-section">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 50%; text-align: center;">
                    <div>L'Employé</div>
                    <div class="signature-line"></div>
                </td>
                <td style="width: 50%; text-align: center;">
                    <div>Le Responsable</div>
                    <div class="signature-line"></div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Pied de page -->
    <div class="footer">
        Document généré le <?php echo date('d/m/Y à H:i'); ?> - <?php echo htmlspecialchars($companyName); ?>
    </div>
</div>
</body>
</html>