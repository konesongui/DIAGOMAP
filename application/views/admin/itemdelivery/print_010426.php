<?php
// =============================
// INFOS ENTREPRISE
// =============================
$companyName = $company['name'] ?? "N/A";
$companyComptBank = $company['compt_bank'] ?? "N/A";
$companyRccm = $company['rccm'] ?? "N/A";
$companyAddress = $company['address'] ?? "N/A";
$companyPhone = $company['phone'] ?? "N/A";
$companyEmail = $company['email'] ?? "N/A";
$companyCentreimpot = $company['centre_impot'] ?? "N/A";
$companyRegime = $company['regime_imposition'] ?? "N/A";
$companyWebsite = $company['site_web'] ?? "N/A";
$companyLogo = base_url('assets/images/admin_logo.png');
$companyBank = $company['bank'] ?? "N/A";

// =============================
// INFOS CLIENT & LIVRAISON
// =============================
$customerFullname = ($delivery['customer_name'] ?? "") . " " . ($delivery['customer_last_name'] ?? "");
$customerAddress = $delivery['customer_address'] ?? "N/A";
$customerPhone = $delivery['customer_phone'] ?? "N/A";
$customerEmail = ($delivery['email'] ?? "N/A") . " / " . ($delivery['customer_phone'] ?? "N/A");
$customerComptec = $delivery['comptec'] ?? "N/A";
$deliveryDate = !empty($delivery['delivery_date']) ? date('d/m/Y', strtotime($delivery['delivery_date'])) : "N/A";
$deliveryDesignation = $delivery['objet'] ?? "N/A";
$deliveryNumber = $delivery['delivery_number'] ?? "N/A";
$UsersName = $delivery['user_name'] ?? "N/A";

$items = $delivery['items'] ?? [];

// =============================
// INFOS MONTANTS
// =============================
$tva_amount = (!empty($delivery['tva_amount']) && floatval($delivery['tva_amount']) > 0) ? floatval($delivery['tva_amount']) : "Non facturée";
$tva_rate = (!empty($delivery['tva_rate']) && floatval($delivery['tva_rate']) > 0) ? floatval($delivery['tva_rate']) : 0;
$total_ht = (!empty($delivery['total_ht']) && floatval($delivery['total_ht']) > 0) ? floatval($delivery['total_ht']) : 0;
$total_ttc = (!empty($delivery['total_ttc']) && floatval($delivery['total_ttc']) > 0) ? floatval($delivery['total_ttc']) : 0;

// =============================
// PAIEMENT & AUTRES
// =============================
$payment_method = $delivery['payment_method'] ?? "N/A";
$payment_terms = $delivery['payment_terms'] ?? "N/A";
$valid_until = $delivery['valid_until'] ?? "N/A";
$delivery_terms = $delivery['delivery_terms'] ?? "N/A";
$delivery_location = $delivery['delivery_location'] ?? "N/A";
$userName = $delivery['user_name'] ?? "N/A";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bon de Livraison <?= $deliveryNumber ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: rgb(19, 96, 171);
            margin: 0;
            padding: 0;
            width: 21cm;
        }

        .head {
            background-color: rgb(250, 183, 22);
            padding: 8px;
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            color: #000;
            border-radius: 5px;
            margin-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        th {
            background-color: rgb(19, 96, 171);
            color: white;
            padding: 5px;
            text-align: left;
            font-size: 12px;
        }

        td {
            border: 1px solid #ccc;
            padding: 5px;
            font-size: 12px;
            vertical-align: top;
        }

        .no-border {
            border: none;
        }

        .info-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
            margin-bottom: 10px;
        }

        .info-box {
            width: 48%;
            border: 1px solid #ccc;
            padding: 8px;
            border-radius: 5px;
        }

        .logo img {
            width: 140px;
            height: auto;
        }

        tbody tr:nth-child(even) {
            background-color: rgba(250, 183, 22, 0.1);
        }

        tbody tr:hover {
            background-color: rgba(19, 96, 171, 0.1);
        }

        .payment-details {
            margin-top: 10px;
            padding: 10px;
            background-color: rgba(19, 96, 171, 0.05);
            border-radius: 5px;
            border-left: 4px solid rgb(250, 183, 22);
        }

        .payment-section {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
        }

        .footer {
            background-color: rgb(250, 183, 22);
            padding: 6px;
            font-size: 10px;
            text-align: left;   /* <-- aligné à gauche */
            margin-top: 20px;
            border-top: 1px solid rgb(19, 96, 171);
            line-height: 1.5;   /* <-- espace entre les lignes pour plus de lisibilité */
        }

    </style>
</head>
<body>

<div class="head">Bon de Livraison N° <?= $deliveryNumber ?></div>

<div class="info-section">
    <!-- Entreprise -->
    <div class="info-box company-box">
        <div class="logo">
            <img src="<?= base_url() . "/uploads/school_content/admin_logo/" . $company['admin_logo'] ?>" alt="Logo" />
        </div><br/>
        <strong><?= $companyName ?></strong>
        <strong><?= $company['company_fullname'] ?></strong><br>
        RCCM : <?= $companyRccm ?><br>
        Centre d'impôt : <?= $companyCentreimpot ?><br>
        Régime d'Imposition : <?= $companyRegime ?><br>
        Téléphone : <?= $companyPhone ?><br>
        Email : <?= $companyEmail ?><br>
        Adresse : <?= $companyAddress ?><br>
        <strong>Affaire suivie par :</strong> <?= $UsersName ?>
    </div>

    <!-- Client -->
    <div class="info-box client-box" style="margin-top: 50px">
        <!-- QR Code au-dessus des infos client -->
        <div style="margin-bottom: 10px; text-align: left;">
            <img src="<?= $qrCodePath ?>" alt="QR Code" style="width: 100px;">
        </div>

        <strong>Client :</strong> <?= $customerFullname ?><br>
        Compte contribuable : <?= $customerComptec ?><br>
        Téléphone : <?= $customerPhone ?><br>
        Adresse email : <?= $customerAddress ?><br>
        Adresse : <?= $customerAddresse ?><br>
    </div>
</div>



<p><strong>Objet :</strong> <?= $quoteDesignation ;?></p>


<table class="products-table">
    <thead>
    <tr>
        <th>Qté</th>
        <th>Description</th>
        <th>PU (CFA)</th>
        <th>Total (CFA)</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['quantity']) ?></td>
            <td><?= htmlspecialchars($item['item_name']) ?> - <?= htmlspecialchars($item['category_name']) ?></td>
            <td><?= number_format($item['unit_price'], 2, ',', ' ') ?></td>
            <td><?= number_format($item['line_total'], 2, ',', ' ') ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<table class="totals-table">
    <tr>
        <td colspan="3" class="no-border" align="right"><strong>Total HT :</strong></td>
        <td><?= number_format($total_ht, 2, ',', ' ') ?></td>
    </tr>
    <tr>
        <td colspan="3" class="no-border" align="right"><strong>TVA (<?= $tva_rate ?>%) :</strong></td>
        <td><?= is_numeric($tva_amount) ? number_format($tva_amount, 2, ',', ' ') : $tva_amount ?></td>
    </tr>
    <tr>
        <td colspan="3" class="no-border" align="right"><strong>Total TTC :</strong></td>
        <td><?= number_format($total_ttc, 2, ',', ' ') ?></td>
    </tr>
</table>

<div class="payment-details">
    <div class="payment-item">
        <span class="payment-label">Montant en lettres :</span>
        <span><?= $totalAsletter ?? "" ?></span>
    </div>
    <div class="payment-item">
        <span class="payment-label">Mode de paiement :</span>
        <span><?= $payment_method ?></span>
    </div>
    <div class="payment-item">
        <span class="payment-label">Terme de livraison :</span>
        <span><?= $delivery_terms ?></span>
    </div>
    <div class="payment-item">
        <span class="payment-label">Lieu de livraison :</span>
        <span><?= $delivery_location ?></span>
    </div>
    <div class="payment-item">
        <span class="payment-label">Règlement :</span>
        <span><?= $payment_terms ?></span>
    </div>
</div>


<div style="position: fixed; bottom: 0.3cm; left: 0.3cm; right: 0.3cm;
    background-color: rgb(250, 183, 22); padding: 3px; font-size: 9px;
    color: #000; border-top: 1px solid rgb(19, 96, 171);">

    <table style="width: 100%; border-collapse: collapse; background-color: rgb(250, 183, 22);">
        <tr>
            <!-- Colonne 1 -->
            <td style="width: 33%; text-align: left; padding: 2px 5px; border:none;">
                <?= $companyName ?> | RCCM: <?= $companyRccm ?>
            </td>

            <!-- Colonne 2 -->
            <td style="width: 34%; text-align: left; padding: 2px 5px; border:none;">
                Téléphone: <?= $companyPhone ?> | Email: <?= $companyEmail ?><br>
                Banque: <?= $companyBank ?> | N° Compte: <?= $companyComptBank ?>
            </td>

            <!-- Colonne 3 -->
            <td style="width: 33%; text-align: left; padding: 2px 5px; border:none;">
                Adresse: <?= $companyAddress ?><br>
                Site: <?= $companyWebsite; ?>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
