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
$primeAnciennete = $grossSalary ?? 0;
$primeTransport = $primeTransport ?? 0;
$primeTechnique = $primeTechnique ?? 0;
$forfaitHS = $forfaitHS ?? 0;
$primeResponsabilite = $primeResponsabilite ?? 0;
$primeRendement= $primeRendement ?? 0;
$primeGratification = $primeGratification ?? 0;
$primeAssiduite = $primeAssiduite ?? 0;
$primeRisque = $primeRisque ?? 0;
$primeFonction = $primeResponsabilite ?? 0;

$bonus = $bonus ?? 0;
$cnpsRegime = $cnpsRegime ?? 0;
$cnpsAccident = $cnpsAccident ?? 0;
$impotRevenu = $impotRevenu ?? 0;
$cmu = $cmu ?? 0;
$avancesAcomptes = $avancesAcomptes ?? 0;
$grossSalary = $grossSalary ?? 0;
$grossIts = $grossIts ?? 0;
$grossFiscal = $grossFiscal ?? 0;
$grossSocial = $grossSocial ?? 0;
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
            color: #130808;
            margin: 0;
            padding: 0;
            width: 21cm;
            background: #fff;
        }
        .header {
            background: linear-gradient(90deg, #130808, #130808);
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
            background: #130808;
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
            background: #130808;
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
<div style="border:1px solid #000; padding:15px; margin-bottom:20px; font-size:13px;">

    <table style="width:100%;">
        <tr>
            <!-- COLONNE ENTREPRISE -->
            <td style="width:35%; vertical-align:top; border-right:1px solid #000; padding-right:10px;">
                <div style="font-weight:bold; color:#d4ae00; font-size:16px; margin-bottom:10px;">
                    <img src="<?= base_url() . "/uploads/school_content/admin_logo/" . $companyLogo ?>"
                         alt="Logo entreprise"
                         class="company-logo" />
                </div>

                <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($companyPhone); ?></p>
                <p><strong>Adresse :</strong> <?php echo htmlspecialchars($companyAddress); ?></p>
                <p><strong>Email :</strong> cm@com</p>
            </td>

            <!-- COLONNE EMPLOYÉ -->
            <td style="width:65%; vertical-align:top; padding-left:10px;">
                <div style="padding:8px; color: black font-weight:bold; text-align:center; border-radius:5px;">
                    BULLETIN DE PAIE - <?= htmlspecialchars($employeeMonth . ' ' . $employeeYear); ?>
                </div>

                <p><strong>Matricule :</strong> <?= htmlspecialchars($employeeId); ?>
                    &nbsp;&nbsp;&nbsp;
                    <strong><?php echo htmlspecialchars($employeeSurname . ' ' . $employeeName); ?></strong>
                </p>

                <p><strong>Statut :</strong> <?= htmlspecialchars($employeeDesignation); ?>
                    &nbsp;&nbsp;&nbsp;
                    <strong>Catégorie salariale :</strong> <?= htmlspecialchars($employeeCategory ?? ''); ?>
                </p>

                <p><strong>CNPS N° :</strong> <?= htmlspecialchars($cnpsNo); ?></p>
                <p><strong>Mode de paie :</strong> <?= htmlspecialchars($PaymentMode); ?>
                    &nbsp;&nbsp;&nbsp;
                    <strong>Part IGR :</strong> <?= htmlspecialchars($partIGR ?? ''); ?>
                </p>

                <p><strong>Nombre d'enfant :</strong> <?= htmlspecialchars($childNumber ?? ''); ?></p>
                <p><strong>Date d'embauche :</strong> <?= htmlspecialchars($hiringDate ?? ''); ?></p>
                <p><strong>Ancienneté :</strong> <?= htmlspecialchars($anciennete ?? ''); ?></p>

                <p><strong>Fonction :</strong> <?= htmlspecialchars($employeeDesignation); ?>
                    &nbsp;&nbsp;&nbsp;
                    <strong>Service :</strong> <?= htmlspecialchars($employeeDepartment); ?></p>

                <p><strong>Date :</strong> <?= htmlspecialchars($paymentDate); ?>
                    &nbsp;&nbsp;&nbsp;
                    <strong>Période du :</strong> 01/<?= htmlspecialchars($employeeMonth . '/' . $employeeYear); ?> au 30/<?= htmlspecialchars($employeeMonth . '/' . $employeeYear); ?></p>
            </td>
        </tr>
    </table>
</div>

    <!-- REVENUS -->
    <div class="section">
         <table>
            <tr>
                <th>Designation</th>
                <th class="text-right">Base (<?php echo $currency_symbol; ?>)</th>
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

            <?php if ($primeRendement > 0): ?>
                <tr>
                    <td>Prime de rendement</td>
                    <td class="text-right"><?php echo number_format($primeRendement, 2, ',', ' '); ?></td>
                </tr>
            <?php endif; ?>
            <?php if ($primeFonction > 0): ?>
                <tr>
                    <td>Prime de fonction</td>
                    <td class="text-right"><?php echo number_format($primeFonctione, 2, ',', ' '); ?></td>
                </tr>
            <?php endif; ?>

            <?php if ($primeRisque > 0): ?>
                <tr>
                    <td>Prime de risque</td>
                    <td class="text-right"><?php echo number_format($primeRisque, 2, ',', ' '); ?></td>
                </tr>
            <?php endif; ?>

            <?php if ($primeAssiduite > 0): ?>
                <tr>
                    <td>Prime d'assiduité</td>
                    <td class="text-right"><?php echo number_format($primeAssiduite, 2, ',', ' '); ?></td>
                </tr>
            <?php endif; ?>

            <?php if ($primeGratification > 0): ?>
                <tr>
                    <td>Prime de responsabilité</td>
                    <td class="text-right"><?php echo number_format($primeGratification, 2, ',', ' '); ?></td>
                </tr>
            <?php endif; ?>

            <?php if ($conge > 0): ?>
                <tr>
                    <td>Congé</td>
                    <td class="text-right"><?php echo number_format($conge, 2, ',', ' '); ?></td>
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
                <td><strong>TOTAL BRUTS</strong></td>

                <td class="text-right"><strong><?php echo number_format($grossSalary, 2, ',', ' '); ?></strong></td>

            </tr>
             <tr class="total-row">
                 <td><strong>TOTAL FISCAL</strong></td>

                 <td class="text-right"><strong><?php echo number_format($grossFiscal, 2, ',', ' '); ?></strong></td>

             </tr>

             <tr class="total-row">
                 <td><strong>TOTAL SOCIAL</strong></td>

                 <td class="text-right"><strong><?php echo number_format($grossSocial, 2, ',', ' '); ?></strong></td>

             </tr>

             <tr class="total-row">
                 <td><strong>ITS</strong></td>

                 <td class="text-right"><strong><?php echo number_format($grossIts, 2, ',', ' '); ?></strong></td>

             </tr>
             <?php if ($cnpsRegime > 0): ?>
                 <tr class="total-row">
                     <td>CNPS - Régime de retraite</td>
                     <td class="text-right"><?php echo number_format($cnpsRegime, 2, ',', ' '); ?></td>
                 </tr>
             <?php endif; ?>
             <?php if ($cnpsAccident > 0): ?>
                 <tr class="total-row">
                     <td>CNPS - Accident de travail</td>
                     <td class="text-right"><?php echo number_format($cnpsAccident, 2, ',', ' '); ?></td>
                 </tr>
             <?php endif; ?>

             <?php if ($impotRevenu > 0): ?>
                 <tr class="total-row">
                     <td>Impôt sur le revenu</td>
                     <td class="text-right"><?php echo number_format($impotRevenu, 2, ',', ' '); ?></td>
                 </tr>
             <?php endif; ?>

             <?php if ($cmu > 0): ?>
                 <tr class="total-row">
                     <td>CMU</td>
                     <td class="text-right"><?php echo number_format($cmu, 2, ',', ' '); ?></td>
                 </tr>
             <?php endif; ?>
        </table>
    </div>

    <!-- DÉDUCTIONS -->


    <!-- RÉCAPITULATIF -->
    <div class="section">

        <table>

            <tr class="total-row">
                <td><strong>TOTAL RÉTENUES</strong></td>
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