<?php

    

    $companyName = $company['name']??"N/A";
    $companyComptBank = $company['compt_bank']??"N/A";
    $companyRccm = $company['rccm']??"N/A";
    $companyAddress = $company['address']??"N/A";
    $companyPhone = $company['phone']??"N/A";
    $companyEmail = $company['email']??"N/A";
    $companyWebsite = $company['website']??"N/A";
    $companyLogo = $company['admin_logo']??"N/A";
    $companyLogo = base_url('assets/images/admin_logo.png');
    $companyBank = $company['bank']??"N/A";

    $customerFullname = $quote['customer_name'].' '.$quote['customer_last_name']??"N/A";
    $customerPhone = $quote['customer_phone']??"N/A";
    $customerAddress = $quote['customer_address'].' / '.$quote['customer_email']??"N/A";
    $customerComptec = $quote['comptec']??"N/A";
    $quoteDate = !empty($quote['quote_date'])? date('d/m/Y', strtotime($quote['quote_date'])) :"N/A";
    $quoteDesignation = $quote['designation']??"N/A";
    $quoteNumber = $quote['quote_number']??"N/A";

    $items = $quote['items']? $quote['items']:[]; 

    $tva_amount = (!empty($quote['tva_amount']) && floatval($quote['tva_amount']) > 0)? floatval($quote['tva_amount']) :"Non facturée";
    $tva_rate = (!empty($quote['tva_rate']) && floatval($quote['tva_rate']) > 0)? floatval($quote['tva_rate']) :0;
    $total_ht = (!empty($quote['total_ht']) && floatval($quote['total_ht']) > 0)? floatval($quote['total_ht']) :0;
    $total_ttc = (!empty($quote['total_ttc']) && floatval($quote['total_ttc']) > 0)? floatval($quote['total_ttc']) :0;
    $payment_method = !empty($quote['payment_method'])? $quote['payment_method'] :"N/A";
    
    $userName = !empty($user['name'])? $user['name'] :"N/A";






    // var_dump($company);
    // var_dump($user);
    // var_dump($companyLogo);
    // var_dump($quote);
    // var_dump($items);
    // var_dump($quote);
    // var_dump($userName);
    // die();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture <?= $quoteNumber ?></title>
    <style>
        @page {
            size: A4;
            margin: 1.5cm;
        }
        body { 
            font-family: Arial, sans-serif; 
            font-size: 12px;
            color: rgb(19, 96, 171);
            margin: 0;
            padding: 0;
            width: 21cm;
            height: auto;
        }
        .head { 
            background-color: rgb(250, 183, 22); 
            padding: 5px; 
            text-align: center; 
            font-weight: bold; 
            font-size: 16px;
            color: #000;
            border-radius: 5px;
            margin-bottom: 3px;
        }
        .quote-content {
            margin-top: 0;
            page-break-before: avoid;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 3px;
        }
        .info-table {
            margin-bottom: 3px;
        }
        .products-table {
            margin-top: 3px;
        }
        .totals-table {
            margin-top: 10px;
            margin-bottom: 2cm;
        }
        .footer {
            position: fixed;
            bottom: 0.3cm;
            left: 0.3cm;
            right: 0.3cm;
            background-color: rgb(250, 183, 22);
            padding: 3px;
            font-size: 9px;
            text-align: center;
            color: #000;
            border-top: 1px solid rgb(19, 96, 171);
            z-index: 1000;
        }
        .main-content {
            padding-bottom: 2cm;
        }
        .footer-content {
            display: flex;
            justify-content: space-between;
            padding: 0 10px;
        }
        .footer-left {
            text-align: left;
        }
        .footer-center {
            /* text-align: center; */
            text-align: left;
        }
        .footer-right {
            /* text-align: right; */
            text-align: left;
        }
        .info-table td {
            padding: 2px 5px;
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
        }
        .no-border { 
            border: none; 
        }
        .logo { 
            text-align: left;
            padding: 3px;
        }
        .logo img {
            width: 150px;
            height: auto;
        }
        tbody tr:nth-child(even) {
            background-color: rgba(250, 183, 22, 0.1);
        }
        tbody tr:hover {
            background-color: rgba(19, 96, 171, 0.1);
        }
        strong {
            color: rgb(19, 96, 171);
        }
        p {
            margin: 3px 0;
            padding: 2px;
            border-left: 3px solid rgb(250, 183, 22);
            padding-left: 8px;
            font-size: 12px;
        }
        .page-break {
            page-break-before: always;
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
        .payment-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .payment-label {
            font-weight: bold;
            color: rgb(19, 96, 171);
            font-size: 12px;
        }
        .payment-value {
            color: #333;
            font-size: 12px;
        }
        @media print {
            body {
                width: 100%;
                height: auto;
            }
            .footer {
                position: fixed;
                bottom: 0.5cm;
            }
            .quote-content {
                page-break-before: avoid;
                page-break-after: auto;
            }
            .head {
                page-break-after: avoid;
            }
            .info-table {
                page-break-after: avoid;
            }
            .products-table {
                page-break-before: avoid;
                page-break-after: auto;
            }
            .totals-table {
                page-break-before: auto;
                page-break-after: avoid;
                margin-bottom: 3cm;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="head">Devis N° <?= $quoteNumber ?> du <?= $quoteDate ?></div>
        <div class="quote-content">
            <br>
            <table class="info-table">
                <tr>
                    <td colspan="2" class="logo">
                        <img src="<?= base_url() . "/uploads/school_content/admin_logo/" . $company['admin_logo'] ?>" alt="Logo" width="180" height="70" />
                    </td>
                    <td colspan="4" class="no-border"></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <strong><?= $companyName ?></strong><br>
                        RCCM : <?= $companyRccm ?><br>
                        Téléphone : <?= $companyPhone ?><br>
                        Email : <?= $companyEmail ?><br>
                        Adresse : <?= $companyAddress ?>
                    </td>
                    <td colspan="2"></td>
                    <td colspan="2">
                        <strong>Client :</strong> <?= $customerFullname ?><br>
                        <strong>Compte contribuable :</strong> <?= $customerComptec ?><br>
                        <strong>Téléphone :</strong> <?= $customerPhone ?><br>
                        <strong>Adresse du Client :</strong> <?= $customerAddress ?><br>
                        <strong>Affaire suivi par:</strong> </strong><?= $userName ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" style="padding: 10px;">
                        <strong>Objet :</strong> <?= $quoteDesignation ;?>
                    </td>
                </tr>
            </table><br>

            <table class="products-table">
                <thead style="background-color:#ccd1cc;">
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
                            <td><?= htmlspecialchars($item['unit_price']) ?></td>
                            <td><?= htmlspecialchars($item['line_total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table><br>

            <table>
                <tr>
                    <td colspan="3" class="no-border" align="right"><strong>Total HT :</strong></td>
                    <td><?= number_format($total_ht, 2, ',', ' ') ;?></td>
                </tr>
                <tr>
                    <td colspan="3" class="no-border" align="right"><strong>TVA (<?= $tva_rate ;?>%) :</strong></td>
                    <td><?= $tva_amount ;?></td>
                </tr>
                <tr>
                    <td colspan="3" class="no-border" align="right"><strong>Total TTC :</strong></td>
                    <td><?= number_format($total_ttc, 2, ',', ' ') ;?></td>
                </tr>
            </table>

            <div class="payment-details">
                <div class="payment-section">
                    <div class="payment-item">
                        <div class="payment-label">Montant en lettres :</div>
                        <div class="payment-value"><?= $totalAsletter ;?></div>
                    </div>
                    <div class="payment-item">
                        <div class="payment-label">Mode de paiement :</div>
                        <div class="payment-value"><?= $payment_method;?></div>
                    </div>
                    <div class="payment-item">
                        <div class="payment-label">Garantie :</div>
                        <div class="payment-value">[à spécifier]</div>
                    </div>
                    <div class="payment-item">
                        <div class="payment-label">Règlement :</div>
                        <div class="payment-value">Payable 30 jours dépôt de facture</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="footer-content">
            <div class="footer-left">
                <?= $companyName ?> | RCCM: <?= $companyRccm ?>
            </div>
            <div class="footer-center">
                Téléphone: <?= $companyPhone ?> | Email: <?= $companyEmail ?><br>
                Banque: <?= $companyBank ?> | Numéro de compte bancaire: <?= $companyComptBank ?>
            </div>
            <div class="footer-right">
                Adresse: <?= $companyAddress ?><br>
                Site : <?= $companyWebsite; ?>
            </div>
        </div>
    </div>
</body>
</html>