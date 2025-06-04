<?php

    $companyName = $company['name'];
    $companyComptBank = $company['compt_bank'];
    $companyRccm = $company['rccm'];
    $companyAddress = $company['address'];
    $companyPhone = $company['phone'];
    $companyEmail = $company['email'];
    $companyWebsite = $company['website'];
    $companyLogo = $company['app_logo'];
    $companyLogo = base_url('assets/images/logo.png');
    $companyBank = $company['bank'];

    // var_dump($company);
    // var_dump($companyLogo);
    // var_dump($invoice);
    // die();
?>
<?php

$companyName = $company['name'];
$companyComptBank = $company['compt_bank'];
$companyRccm = $company['rccm'];
$companyAddress = $company['address'];
$companyPhone = $company['phone'];
$companyEmail = $company['email'];
$companyWebsite = $company['website'];
$companyLogo = $company['app_logo'];
$companyLogo = base_url('assets/images/logo.png');
$companyBank = $company['bank'];

// var_dump($company);
// var_dump($companyLogo);
// var_dump($invoice);
// die();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Proforma SICAB</title>
<style>
    @page {
        size: A4;
        margin: 1cm;
    }
    body { 
        font-family: Arial, sans-serif; 
        font-size: 13px;
        color: rgb(19, 96, 171);
        margin: 0;
        padding: 0;
        width: 21cm;
        min-height: 29.7cm;
        margin: 0 auto;
    }
    .footer {
        position: fixed;
        bottom: 1cm;
        left: 1cm;
        right: 1cm;
        background-color: rgb(250, 183, 22);
        padding: 5px;
        font-size: 10px;
        text-align: center;
        color: rgb(19, 96, 171);
        border-top: 1px solid rgb(19, 96, 171);
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
        text-align: center;
    }
    .footer-right {
        text-align: right;
    }
    .main-content {
        padding-bottom: 2cm;
    }
    .page-number {
        position: fixed;
        bottom: 0.5cm;
        right: 1cm;
        font-size: 10px;
        color: rgb(19, 96, 171);
    }
    .head { 
        background-color: rgb(250, 183, 22); 
        padding: 8px; 
        text-align: center; 
        font-weight: bold; 
        font-size: 18px;
        color: rgb(19, 96, 171);
        border-radius: 5px;
        margin-bottom: 3px;
    }
    .invoice-content {
        margin-top: 3px;
    }
    table { 
        width: 100%; 
        border-collapse: collapse; 
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        page-break-inside: avoid;
    }
    .info-table {
        margin-bottom: 3px;
    }
    .info-table td {
        padding: 2px 5px;
    }
    .products-table {
        margin-top: 3px;
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
        padding: 4px;
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
        margin-top: 20px;
        padding: 15px;
        background-color: rgba(19, 96, 171, 0.05);
        border-radius: 5px;
        border-left: 4px solid rgb(250, 183, 22);
    }
    .payment-section {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
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
        .payment-details {
            break-inside: avoid;
        }
    }
</style>
</head>
<body>
<div class="main-content">
    <div class="head">FACTURE PROFORMA N°190525_02 / CM</div>
    <div class="invoice-content">
        <table class="info-table">
            <tr>
                <td colspan="2" class="logo">
                    <img src="logo.png" alt="Logo" width="180" height="70">
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
                    <strong>Date :</strong> <?= $invoice['date'] ?><br>
                    <strong>Client :</strong> <?= $invoice['client'] ?><br>
                    <strong>Affaire suivi par:</strong>  Regime: I. M
                </td>
            </tr>
            <tr>
                <td colspan="6">
                    <strong>Objet :</strong> Projet de câblage réseau Avec WiFi
                </td>
            </tr>
        </table>

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
                <tr><td>700</td><td>Grandstream GRP2602P - Téléphone IP</td><td>700</td><td>490 000</td></tr>
                <tr><td>20</td><td>Fanvil X6U - Poste opérateur</td><td>5 000</td><td>100 000</td></tr>
                <tr><td>24</td><td>Boitier mosaïque 2 modules</td><td>1 500</td><td>36 000</td></tr>
                <tr><td>20</td><td>Prises RJ45 Cat 6</td><td>2 850</td><td>57 000</td></tr>
                <tr><td>20</td><td>Cordon de descente 3m</td><td>4 800</td><td>96 000</td></tr>
                <tr><td>1</td><td>IPBX Yeastar Grandstream UCM6302A</td><td>205 000</td><td>205 000</td></tr>
                <tr><td>1</td><td>Parasurtenseur</td><td>250 000</td><td>250 000</td></tr>
                <tr><td>1</td><td>Onduleur DS-UPS2000</td><td>22 500</td><td>22 500</td></tr>
                <tr><td>1</td><td>Switch DS-3E1326P-EI</td><td>210 000</td><td>210 000</td></tr>
                <tr><td>50</td><td>Prises</td><td>1 500</td><td>75 000</td></tr>
                <tr><td>14</td><td>Accessoire de raccordement</td><td>3 000</td><td>42 000</td></tr>
                <tr><td>6</td><td>Boitier réseau</td><td>4 500</td><td>27 000</td></tr>
                <tr><td>1</td><td>Moulure 40*16</td><td>250 000</td><td>250 000</td></tr>
                <tr><td>1</td><td>Moulure 75*50</td><td>350 000</td><td>350 000</td></tr>
                <tr><td>15</td><td>Main d'œuvre</td><td>60 000</td><td>900 000</td></tr>
                <tr><td>3</td><td>Coffret informatique 9U</td><td>150 000</td><td>450 000</td></tr>
                <tr><td>1</td><td>Unifi lite wifi point d'accès</td><td>75 000</td><td>75 000</td></tr>
                <tr><td>1</td><td>Grandstream DP722</td><td>70 000</td><td>70 000</td></tr>
                <tr><td>1</td><td>Cordon de brassage 1m</td><td>50 000</td><td>50 000</td></tr>
                <tr><td>1</td><td>Goulotte 105*50</td><td>100 000</td><td>100 000</td></tr>
                <tr><td>4</td><td>Câble informatique Cat6 FTP</td><td>125 000</td><td>500 000</td></tr>
                <tr><td>1</td><td>Panneau de brassage</td><td>100 000</td><td>100 000</td></tr>
                <tr><td>1</td><td>Bandeau électrique 8*2P+T 16A</td><td>415 000</td><td>415 000</td></tr>
            </tbody>
        </table>

        <table>
            <tr>
                <td colspan="3" class="no-border" align="right"><strong>Total HT :</strong></td>
                <td>4 620 500 CFA</td>
            </tr>
            <tr>
                <td colspan="3" class="no-border" align="right"><strong>TVA (18%) :</strong></td>
                <td>Non facturée</td>
            </tr>
            <tr>
                <td colspan="3" class="no-border" align="right"><strong>Total TTC :</strong></td>
                <td>4 620 500 CFA</td>
            </tr>
        </table>

        <div class="payment-details">
            <div class="payment-section">
                <div class="payment-item">
                    <div class="payment-label">Montant en lettres :</div>
                    <div class="payment-value">quatre millions six cent vingt mille cinq cents francs CFA</div>
                </div>
                <div class="payment-item">
                    <div class="payment-label">Mode de paiement :</div>
                    <div class="payment-value">Chèque - Espèce - Virement</div>
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
            Tél: <?= $companyPhone ?> | Email: <?= $companyEmail ?>
        </div>
        <div class="footer-right">
            Adresse: <?= $companyAddress ?>
        </div>
    </div>
</div>
<div class="page-number">Page 1</div>
</body>
</html>