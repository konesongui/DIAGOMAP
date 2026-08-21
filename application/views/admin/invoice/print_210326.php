<?php



$companyName = $company['name']??"N/A";
$companyComptBank = $company['compt_bank']??"N/A";
$companyRccm = $company['rccm']??"N/A";
$companyAddress = $company['address']??"N/A";
$companyPhone = $company['phone']??"N/A";
$companyEmail = $company['email']??"N/A";
$companyCentreimpot = $company['centre_impot']??"N/A";
$companyRegime = $company['regime_imposition']??"N/A";
$companyWebsite = $company['site_web']??"N/A";
$companySupplier = $company['company_supplier']??"N/A";
$companyLogo = $company['admin_logo']??"N/A";
$companyLogo = base_url('assets/images/admin_logo.png');
$companyBank = $company['bank']??"N/A";

$customerFullname = $invoice['customer_name'].' '.$invoice['customer_last_name']??"N/A";
$customerAddress = $invoice['customer_address'].' / '.$invoice['customer_phone']??"N/A";
$invoiceDate = !empty($invoice['invoice_date'])? date('d/m/Y', strtotime($invoice['invoice_date'])) :"N/A";
$invoiceDesignation = $invoice['designation']??"N/A";
$invoiceNumber = $invoice['invoice_number']??"N/A";
$notes = $invoice['notes']??"N/A";

$items = $invoice['items']? $invoice['items']:[];

$tva_amount = (!empty($invoice['tva_amount']) && floatval($invoice['tva_amount']) > 0)? floatval($invoice['tva_amount']) :"Non facturée";
$tva_rate = (!empty($invoice['tva_rate']) && floatval($invoice['tva_rate']) > 0)? floatval($invoice['tva_rate']) :0;
$total_ht = (!empty($invoice['total_ht']) && floatval($invoice['total_ht']) > 0)? floatval($invoice['total_ht']) :0;
$total_ttc = (!empty($invoice['total_ttc']) && floatval($invoice['total_ttc']) > 0)? floatval($invoice['total_ttc']) :0;
$payment_method = !empty($invoice['method'])? $invoice['method'] :"N/A";
$payment_terms = !empty($invoice['payment_terms'])? $invoice['payment_terms'] :"N/A";
$payment_method = !empty($invoice['method'])? $invoice['method'] :"N/A";
$valid_until = !empty($invoice['valid_until'])? $invoice['valid_until'] :"N/A";
$delivery_terms = !empty($invoice['delivery_terms'])? $invoice['delivery_terms'] :"N/A";
$delivery_location = !empty($invoice['delivery_location'])? $invoice['delivery_location'] :"N/A";

$userName = !empty($invoice['user_name'])? $invoice['user_name'] :"N/A";

// Informations FNE
$fne_reference = $invoice['fne_reference'] ?? '';
$fne_certified = !empty($invoice['fne_certified']) && $invoice['fne_certified'] == 1;
$fne_token = $invoice['fne_token'] ?? '';



// var_dump($company);
// var_dump($companyLogo);
// var_dump($invoice);
// die();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture <?= $invoiceNumber ?></title>

    <style>

        /* ================= PAGE ================= */
        @page {
            size: A4;
            margin: 1.5cm;
        }

        body{
            font-family: Arial, Helvetica, sans-serif;
            font-size:11px;
            color:#000;
            line-height:1.4;
        }

        /* ================= CADRE ENTREPRISE ================= */
        .company-frame {
            border: 2px solid #000;
            padding: 8px 12px;
            margin-bottom: 10px;
            display: inline-block;
            font-weight: bold;
            font-size: 14px;
            line-height: 1.5;
        }

        /* ================= HEADER ================= */
        .header-row{
            display:flex;
            justify-content:space-between;
            margin-bottom:5px;
            position: relative;
        }

        .company-info{
            width: 45%; /* Réduit pour donner plus d'espace à droite */
        }

        .right-content{
            width: 50%;
            display: flex;
            flex-direction: column;
            align-items: flex-start; /* Aligné à gauche */
            margin-left: -30px; /* Décalé plus à gauche */
        }

        .invoice-title{
            text-align:left;
            margin-bottom: 15px;
        }

        .invoice-title .facture-numero{
            font-size:14px;
            font-weight:bold;
        }

        .client-info{
            text-align:left;
        }

        hr{
            border:0.5px solid #000;
            margin:10px 0;
        }

        /* ================= TABLE PRODUITS ================= */
        .products-table{
            width:100%;
            border-collapse:collapse;
            margin-top:15px;
            border:1px solid #000;
        }

        .products-table th{
            border:1px solid #000;
            padding:8px 5px;
            background:#f2f2f2;
            text-align:left;
            font-weight:bold;
            font-size:11px;
        }

        .products-table td{
            border:1px solid #000;
            padding:8px 5px;
        }

        .products-table td:last-child{
            text-align:right;
        }

        /* ================= TOTAUX ================= */
        .totals-container{
            width:40%;
            margin-left:auto;
            margin-top:15px;
        }

        .totals-table{
            width:100%;
            border-collapse:collapse;
        }

        .totals-table td{
            border:1px solid #000;
            padding:8px 10px;
        }

        .totals-table td:first-child{
            font-weight:bold;
        }

        .totals-table td:last-child{
            text-align:right;
        }

        .total-payer{
            font-weight:bold;
        }

        /* ================= RESUME FACTURE ================= */
        .resume-title{
            font-weight:bold;
            font-size:12px;
            margin:20px 0 10px 0;
            text-transform:uppercase;
        }

        .resume-table{
            width:100%;
            border-collapse:collapse;
            border:1px solid #000;
        }

        .resume-table th{
            border:1px solid #000;
            padding:8px 5px;
            background:#f2f2f2;
            text-align:left;
            font-weight:bold;
        }

        .resume-table td{
            border:1px solid #000;
            padding:8px 5px;
        }

        .resume-table td:last-child{
            text-align:right;
        }

        /* ================= ADRESSE ================= */
        .address-line{
            text-align:center;
            margin:20px 0;
            font-size:10px;
            font-weight:bold;
        }

        /* ================= FOOTER ================= */
        .footer{
            margin-top:30px;
            font-size:10px;
        }

        .footer-table{
            width:100%;
            border:none;
        }

        .footer-table td{
            border:none;
            padding:5px 0;
        }
        /* ================= FNE ================= */
        .fne-badge {
            background-color: #28a745;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
        }

        .qr-code {
            text-align: center;
            margin: 20px 0;
        }

        .qr-code img {
            max-width: 150px;
            max-height: 150px;

    </style>
</head>

<body>

<!-- ================= CADRE ENTREPRISE ================= -->
<div class="company-frame">
    <?= $companySupplier ?><br>
    NCC : <?= $companyNcc ?><br>
    Régime d'imposition : <?= $companyRegime ?><br>
    Centre des impôts : <?= $companyCentreimpot ?>
</div>

<img src="<?= base_url() . "/uploads/school_content/admin_logo/" . $company['admin_logo'] ?>" alt="Logo" width="180" height="120" />

<!-- ================= HEADER DOUBLE COLONNES ================= -->
<div class="header-row">
    <!-- COLONNE GAUCHE : INFOS SOCIETE -->
    <div class="company-info">
        RCCM : <?= $companyRccm ?><br>

        Références bancaires :
        <?= $companyBank ?><br>
        Établissement : <?= $companyName ?><br>
        Adresse : <?= $companyAddress ?><br>
        N° Tel : <?= $companyPhone ?><br>
        Mail : <?= $companyEmail ?><br>

        Nom du vendeur : <?= $sellerName ?><br>
        Nom de PDV : <?= $pdvName ?><br>
        Date et heure : <?= $invoiceDateTime ?><br>
        Mode de paiement : <?= $payment_method ?><br>
    </div>

    <!-- COLONNE DROITE : FACTURE N° ET CLIENT (décalés à gauche) -->
    <div class="right-content">
        <div class="invoice-title">
            <div class="facture-numero">
                Facture de vente N° <?= $invoiceNumber ?>
            </div>
        </div>
        <?php if ($fne_certified): ?>
            <div class="fne-badge">
                ✓ Certifié FNE - Réf: <?= $fne_reference ?>
            </div>
        <?php endif; ?>

        <div class="client-info">
            <strong>Client</strong><br>
            Nom : <?= $customerFullname ?><br>
            Adresse : <?= $customerAddress ?><br>
            NCC : <?= $customerNcc ?? '-' ?><br>
            Régime d'imposition : <?= $customerRegime ?? '-' ?>
        </div>
    </div>
</div>



<!-- ================= TABLE PRODUITS ================= -->
<table class="products-table">
    <thead>
    <tr>
        <th>Réf</th>
        <th>Désignation</th>
        <th>P.U HT</th>
        <th>Qté</th>
        <th>Unité</th>
        <th>Taxes (%)</th>
        <th>Rem. (%)</th>
        <th>Montant HT</th>
    </tr>
    </thead>

    <tbody>
    <?php foreach ($items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['reference'] ?? '') ?></td>
            <td><?= htmlspecialchars($item['item_name']) ?></td>
            <td><?= number_format($item['unit_price'],0,',',' ') ?></td>
            <td><?= $item['quantity'] ?></td>
            <td><?= $item['unit'] ?? '' ?></td>
            <td><?= $item['tax_rate'] ?? 'TVAD (0)' ?></td>
            <td><?= $item['discount'] ?? '0' ?></td>
            <td><?= number_format($item['line_total'],0,',',' ') ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<!-- ================= TOTAUX ================= -->
<div class="totals-container">
    <table class="totals-table">
        <tr>
            <td>TOTAL HT</td>
            <td><?= number_format($total_ht,0,',',' ') ?></td>
        </tr>
        <tr>
            <td>TVA</td>
            <td><?= number_format($tva_amount,0,',',' ') ?></td>
        </tr>
        <tr>
            <td>TOTAL TTC</td>
            <td><?= number_format($total_ttc,0,',',' ') ?></td>
        </tr>
        <tr>
            <td>AUTRES TAXES</td>
            <td>0</td>
        </tr>
        <tr class="total-payer">
            <td>TOTAL A PAYER</td>
            <td><?= number_format($total_ttc,0,',',' ') ?></td>
        </tr>
    </table>
</div>

<!-- ================= RESUME FACTURE ================= -->
<div class="resume-title">RESUME DE LA FACTURE</div>

<table class="resume-table">
    <thead>
    <tr>
        <th>CATEGORIE</th>
        <th>SOUS-TOTAL</th>
        <th>TAUX (%)</th>
        <th>TOTAL TAXES</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>TVA exo.lég - Pas de TVA sur HT 00,00% - D</td>
        <td><?= number_format($total_ht,0,',',' ') ?></td>
        <td>0%</td>
        <td>0</td>
    </tr>
    </tbody>
</table>

<!-- ================= ADRESSE ================= -->
<div class="address-line">
    <?= $companyAddress ?>
</div>

<!-- ================= QR CODE FNE ================= -->
<?php if ($fne_certified && !empty($fne_token)): ?>
    <div class="qr-code">
        <p><strong>Code de vérification FNE</strong></p>
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= urlencode($fne_token) ?>" alt="QR Code FNE">
        <p><small>Référence: <?= $fne_reference ?></small></p>
    </div>
<?php endif; ?>

<!-- ================= FOOTER ================= -->


<!-- ================= FOOTER ================= -->
<!--<div class="footer">
    <table class="footer-table">
        <tr>
            <td style="width:33%">
                RCCM : <?= $companyRccm ?><br>
                Centre des impôts : <?= $companyCentreimpot ?>
            </td>
            <td style="width:34%; text-align:center">
                IBAN <?= $iban ?? '' ?><br>
                ☎ <?= $companyPhone ?> | ✉ <?= $companyEmail ?>
            </td>
            <td style="width:33%; text-align:right">
                Document certifié électroniquement FNE 2026
            </td>
        </tr>
    </table>
</div>-->

</body>
</html>