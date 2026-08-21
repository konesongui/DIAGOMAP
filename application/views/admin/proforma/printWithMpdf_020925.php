<?php
$companyName = $company['name'] ?? "N/A";
$companyComptBank = $company['compt_bank'] ?? "N/A";
$companyRccm = $company['rccm'] ?? "N/A";
$companyAddress = $company['address'] ?? "N/A";
$companyPhone = $company['phone'] ?? "N/A";
$companyCentreimpot = $company['centre_impot'] ?? "N/A";
$companyRegime = $company['regime_imposition'] ?? "N/A";
$companyEmail = $company['email'] ?? "N/A";
$companyWebsite = $company['website'] ?? "N/A";
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

$items = $quote['items'] ?? [];

$tva_amount = (!empty($quote['tva_amount']) && floatval($quote['tva_amount']) > 0) ? floatval($quote['tva_amount']) : "Non facturée";
$tva_rate = (!empty($quote['tva_rate']) && floatval($quote['tva_rate']) > 0) ? floatval($quote['tva_rate']) : 0;
$total_ht = (!empty($quote['total_ht']) && floatval($quote['total_ht']) > 0) ? floatval($quote['total_ht']) : 0;
$total_ttc = (!empty($quote['total_ttc']) && floatval($quote['total_ttc']) > 0) ? floatval($quote['total_ttc']) : 0;
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
        }
        .bordered td, .bordered th {
            border: 1px solid #ccc;
        }
        .highlight {
            background: #FAD234;
            font-weight: bold;
            padding: 5px;
        }
        .info-box {
            background: #F6F9FC;
            border-left: 4px solid #FAD234;
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
            background: #FAD234;
            padding: 5px;
            font-size: 9px;
            text-align: center;
            border-top: 1px solid #13305B;
        }
    </style>
</head>
<body>
<div class="header">Devis N° <?= $quoteNumber ?> du <?= $quoteDate ?></div>

<table>
    <tr>
        <td style="width: 40%;">
            <img src="<?= base_url() . "/uploads/school_content/admin_logo/" . $company['admin_logo'] ?>" style="width: 150px; height: 60px;">
        </td>
        <td style="width: 60%; text-align: right; font-size: 12px;">
            <strong><?= $companyName ?></strong><br>
            RCCM : <?= $companyRccm ?><br>
            Centre d'impôt : <?= $companyCentreimpot ?><br>
            Régime d’Imposition : <?= $companyRegime ?><br>
            Tél : <?= $companyPhone ?><br>
            Email : <?= $companyEmail ?><br>
            Adresse : <?= $companyAddress ?><br>
            <em>Affaire suivie par :</em> <?= $userName ?>
        </td>
    </tr>
</table>

<div class="highlight">Client : <?= $customerFullname ?></div>
<table>
    <tr>
        <td><b>Compte contribuable :</b> <?= $customerComptec ?></td>
        <td><b>Téléphone :</b> <?= $customerPhone ?></td>
    </tr>
    <tr>
        <td><b>Email :</b> <?= $customerAddress ?></td>
        <td><b>Adresse :</b> <?= $customerAddresse ?></td>
    </tr>
</table>

<div style="margin: 10px 0;"><b>Objet :</b> <?= $quoteDesignation ?></div>

<table class="bordered">
    <thead>
    <tr>
        <th>Qté</th>
        <th>Description</th>
        <th>PU (CFA)</th>
        <th>Total (CFA)</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $index => $item): ?>
        <tr style="background: <?= $index % 2 == 0 ? '#F9FBFD' : '#fff' ?>">
            <td><?= htmlspecialchars($item['quantity']) ?></td>
            <td><?= htmlspecialchars($item['item_name']) ?> - <?= htmlspecialchars($item['category_name']) ?></td>
            <td><?= number_format($item['unit_price'], 0, ',', ' ') ?></td>
            <td><?= number_format($item['line_total'], 0, ',', ' ') ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<table>
    <tr>
        <td style="text-align: right; font-weight: bold;">Total HT :</td>
        <td style="width: 120px; text-align: right;"><?= number_format($total_ht, 2, ',', ' ') ?></td>
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