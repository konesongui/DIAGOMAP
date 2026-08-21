<?php
$companyName = $company['name'] ?? "N/A";
$companyComptBank = $company['compt_bank'] ?? "N/A";
$companyRccm = $company['rccm'] ?? "N/A";
$companyAddress = $company['address'] ?? "N/A";
$companyPhone = $company['phone'] ?? "N/A";
$companyCentreimpot = $company['centre_impot'] ?? "N/A";
$companyRegime = $company['regime_imposition'] ?? "N/A";
$companyEmail = $company['email'] ?? "N/A";
$companyWebsite = $company['site_web'] ?? "N/A";
$companyLogo = base_url("assets/images/admin_logo.png");
$companyBank = $company['bank'] ?? "N/A";

$customerFullname = $quote['customer_name'] ?? "N/A";
$customerPhone = $quote['customer_phone'] ?? "N/A";
$customerAddresse = $quote['customer_address'] ?? "N/A";
$customerAddress = $quote['customer_email'] ?? "N/A";
$customerResponsable = $quote['customer_contact_person_name'] ?? "N/A";
$customerComptec = $quote['comptec'] ?? "N/A";
$quoteDate = !empty($quote['quote_date']) ? date('d/m/Y', strtotime($quote['quote_date'])) : "N/A";
$quoteDesignation = $quote['designation'] ?? "N/A";
$quoteNumber = $quote['quote_number'] ?? "N/A";
$UsersName = $quote['user_name'] ?? "N/A";

$items = $quote['items'] ?? [];

$tva_amount = (!empty($quote['tva_amount']) && floatval($quote['tva_amount']) > 0) ? floatval($quote['tva_amount']) : "Non facturée";
$tva_rate = (!empty($quote['tva_rate']) && floatval($quote['tva_rate']) > 0) ? floatval($quote['tva_rate']) : 0;
$total_ht = (!empty($quote['total_ht']) && floatval($quote['total_ht']) > 0) ? floatval($quote['total_ht']) : 0;
$total_ttc = (!empty($quote['total_ttc']) && floatval($quote['total_ttc']) > 0) ? floatval($quote['total_ttc']) : 0;
$discount_amount = (!empty($quote['total_discount']) && floatval($quote['total_discount']) > 0) ? floatval($quote['total_discount']) : 0;
$total_after_discount = (!empty($quote['total_after_discount']) && floatval($quote['total_after_discount']) > 0) ? floatval($quote['total_after_discount']) : 0;


$payment_method = !empty($quote['payment_method']) ? $quote['payment_method'] : "N/A";
$payment_terms = !empty($quote['payment_terms']) ? $quote['payment_terms'] : "N/A";
$valid_until = !empty($quote['valid_until']) ? $quote['valid_until'] : "N/A";
$delivery_terms = !empty($quote['delivery_terms']) ? $quote['delivery_terms'] : "N/A";
$delivery_location = !empty($quote['delivery_location']) ? $quote['delivery_location'] : "N/A";

$userName = !empty($user['name']) ? $user['name'] : "N/A";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture <?= $quoteNumber ?></title>
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            font-size: 12px;
            color: black;
            margin: 0;
            padding: 0;
            width: 21cm;
            background: #fff;
        }
        .header {
            background: linear-gradient(90deg, white, white);
            color: black;
            border: solid 1px;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            border-radius: 0 0 10px 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }
        th {
            background: white;
            color: black;
            padding: 6px;
            font-size: 12px;
            text-align: left;
        }
        td {
            padding: 6px;
            font-size: 12px;
        }
        .bordered td, .bordered th {
            border: 1px solid #ccc;
        }
        .highlight {
            background: white;
            font-weight: bold;
            padding: 5px;
        }
        .info-box {
            background: #F6F9FC;
            border-left: 4px solid black;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
            font-size: 12px;
        }
        .footer {
            position: fixed;
            bottom: 0.3cm;
            left: 0.3cm;
            right: 0.3cm;
            background: white;
            padding: 5px;
            font-size: 9px;
            text-align: center;
            border-top: 1px solid black;
        }
    </style>
</head>
<body>
<div class="header">Devis N° <?= $quoteNumber ?> du <?= $quoteDate ?></div>

<table style="width: 100%; margin-top: 10px; border-spacing: 10px;">
    <tr>
        <!-- Logo centré -->
        <td colspan="2" style="text-align: left; padding-bottom: 8px;">
            <img src="<?= base_url() . "/uploads/school_content/admin_logo/" . $company['admin_logo'] ?>"
                 style="width: 140px; height: auto;">
        </td>
    </tr>
    <tr>
        <!-- Colonne Entreprise -->
        <td style="width: 50%; vertical-align: top;">
            <div style="background: #F6F9FC; border: 1px solid #ddd;
                        border-radius: 6px; padding: 12px; font-size: 12px; min-height: 160px;">
                <strong style="font-size: 14px; display: block; margin-bottom: 6px;"><?= $companyName ?></strong>
                <div>RCCM : <?= $companyRccm ?></div>
                <div>Centre d'impôt : <?= $companyCentreimpot ?></div>
                <div>Régime d’Imposition : <?= $companyRegime ?></div>
                <div>Tél : <?= $companyPhone ?></div>
                <div>Email : <?= $companyEmail ?></div>
                <div>Adresse : <?= $companyAddress ?></div>
                <div><em>Affaire suivie par :</em> <?= $UsersName ?></div>
            </div>
        </td>

        <!-- Colonne Client -->
        <td style="width: 50%; vertical-align: top;">

            <div style="background: #F6F9FC; border: 1px solid #ddd;
                        border-radius: 6px; padding: 12px; font-size: 12px; min-height: 160px;">

                <strong style="font-size: 14px; display: block; margin-bottom: 6px;">Client : <?= $customerFullname ?></strong>
                <div>Compte contribuable : <?= $customerComptec ?></div>
                <div>Téléphone : <?= $customerPhone ?></div>
                <div>Email : <?= $customerAddress ?></div>
                <div>Adresse : <?= $customerAddresse ?></div>
                <div>Responsable : <?= $customerResponsable ?></div>
            </div>
        </td>
    </tr>
</table>



<div style="margin: 10px 0;"><b>Objet :</b> <?= $quoteDesignation ?></div>

<table class="bordered">
    <thead>
    <tr>
        <th>Description</th>
        <th>Qté</th>
        <th>P.U H.T</th>
        <th>Remise</th>
        <th>P.U.Net</th>
        <th>Montant HT</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $index => $item): ?>
        <tr style="background: <?= $index % 2 == 0 ? '#F9FBFD' : '#fff' ?>">
            <td><?= $item['user_name'] ?> - <?= htmlspecialchars($item['category_name']) ?></td>
            <td><?= htmlspecialchars($item['quantity']) ?></td>
            <td><?= htmlspecialchars($item['unit_price']) ?></td>
            <td><?= htmlspecialchars($item['discount']) ?></td>
            <td><?= htmlspecialchars($item['line_total']) ?></td>
            <td><?= htmlspecialchars($item['line_total_after_discount']) ?></td> </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<table>
    <tr>
        <td style="text-align: right; font-weight: bold;">Total HT :</td>
        <td style="width: 120px; text-align: right;"><?= number_format($total_ht, 2, ',', ' ') ?></td>
    </tr>
    <tr>
        <td style="text-align: right; font-weight: bold;">Remises Total :</td>
        <td style="width: 120px; text-align: right;"><?= number_format($discount_amount, 2, ',', ' ') ?></td>
    </tr>
    <tr>
        <td style="text-align: right; font-weight: bold;">Net Hors-Taxe :</td>
        <td style="width: 120px; text-align: right;"><?= number_format($total_after_discount, 2, ',', ' ') ?></td>
    </tr>
    <tr>
        <td style="text-align: right; font-weight: bold;">TVA (<?= $tva_rate ?>%) :</td>
        <td style="text-align: right;"><?= $tva_amount ?></td>
    </tr>
    <tr>
        <td style="text-align: right; font-weight: bold;">Total TTC :</td>
        <td style="text-align: right;"><?= number_format($total_ttc, 2, ',', ' ') ?></td>
    </tr>
</table>

<div class="info-box">
    <b>Montant en lettres :</b> <?= $totalAsletter ?><br>
    <b>Mode de paiement :</b> <?= $payment_method ?><br>
    <b>Règlement :</b> <?= $payment_terms ?><br>
    <b>Terme de livraison :</b> <?= $delivery_terms ?><br>
    <b>Lieu de livraison :</b> <?= $delivery_location ?>
</div>

<div style="bottom: 0.3cm; left: 0.3cm; right: 0.3cm;
    background-color: white;border: solid 1px; padding: 3px; font-size: 9px;
    color: black; border-top: 1px solid black;">

    <table style="width: 100%; border-collapse: collapse; background-color: white;">
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