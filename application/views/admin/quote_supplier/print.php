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
$companyLogo = base_url('assets/images/admin_logo.png');
$companyBank = $company['bank'] ?? "N/A";

$customerFullname = ($quote['customer_name'] ?? "") . ' ' . ($quote['customer_last_name'] ?? "");
$customerPhone = $quote['customer_phone'] ?? "N/A";
$customerAddress = $quote['customer_email'] ?? "N/A";
$customerAddresse = $quote['customer_address'] ?? "N/A";
$customerComptec = $quote['comptec'] ?? "N/A";

$quoteDate = !empty($quote['quote_date']) ? date('d/m/Y', strtotime($quote['quote_date'])) : "N/A";
$quoteDesignation = $quote['objet'] ?? "N/A";
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
    <title>Devis <?= $quoteNumber ?></title>
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
<div class="head">Devis N° <?= $quoteNumber ?> du <?= $quoteDate ?></div>

<style>
    .info-section {
        display: flex;
        gap: 20px;
    }
    .info-box {
        flex: 1;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        background: #fff;
    }
    .company-box {
        flex: 1;
    }
    .client-box {
        flex: 1;
        margin-top: 50px; /* décale vers le bas pour aligner avec RCCM */
    }
    .info-box strong {
        font-weight: bold;
    }
</style>

<style>
    .info-section {
        display: flex;
        gap: 20px;
    }
    .info-box {
        flex: 1;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        background: #fff;
    }
    .company-box {
        flex: 1;
    }
    .client-box {
        flex: 1;
        margin-top: 40px; /* ajuste la valeur pour aligner avec "DIAGO GESTION" */
    }
</style>

<div class="info-section">
    <!-- Entreprise -->
    <div class="info-box company-box">
        <div class="logo">
            <img src="<?= base_url() . "/uploads/school_content/admin_logo/" . $company['admin_logo'] ?>" alt="Logo" />
        </div><br/>
        <strong><?= $companyName ?></strong><br>
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
    <div class="info-box client-box" style="margin-top: 10px">
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
        <th>Description</th>
        <th>Qté</th>
        <th>P.U</th>
        <th>Remise</th>
        <th>P.U NET</th>
        <th>Montant Net</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['item_name']) ?> - <?= htmlspecialchars($item['category_name']) ?></td>
            <td><?= htmlspecialchars($item['quantity']) ?></td>
            <td><?= htmlspecialchars($item['unit_price']) ?></td>
            <td><?= htmlspecialchars($item['discount']) ?></td>
            <td><?= htmlspecialchars($item['line_total']) ?></td>
            <td><?= htmlspecialchars($item['line_total_after_discount']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table><br>


<table>
    <thead>
    <tr>
        <th>Total HT</th>
        <th>Remises Total</th>
        <th>Net Hors-Taxe</th>
        <th>TVA(18%)</th>
        <th>Total TTC</th>

    </tr>
    </thead>
    <tbody>

        <tr>
            <td><?= number_format($total_ht, 2, ',', ' ') ;?></td>
            <td><?= number_format($discount_amount, 2, ',', ' ') ;?></td>
            <td><?= number_format($total_after_discount, 2, ',', ' ') ;?></td>
            <td><?= number_format($tva_amount, 2, ',', ' ') ;?></td>
            <td><?= number_format($total_ttc, 2, ',', ' ') ;?></td>
        </tr>

    </tbody>
</table><br>


<div class="payment-details">
    <div class="payment-section">
        <div><strong>Montant en lettres :</strong> <?= $totalAsletter ;?></div>
        <div><strong>Mode de paiement :</strong> <?= $payment_method;?></div>
        <div><strong>Terme de livraison :</strong> <?= $delivery_terms;?></div>
        <div><strong>Lieu de livraison :</strong> <?= $delivery_location;?></div>
        <div><strong>Règlement :</strong> <?= $payment_terms;?></div>
    </div>
</div>

<div style="bottom: 0.3cm; left: 0.3cm; right: 0.3cm;
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
