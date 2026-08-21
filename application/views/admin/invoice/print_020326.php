<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture <?= $invoiceNumber ?></title>
    <style>
        /* PAGE SETUP – clean A4 */
        @page {
            size: A4;
            margin: 1.2cm 1cm 1.8cm 1cm; /* top, right, bottom, left */
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            font-size: 10.5px;
            color: #1e2f4e;
            margin: 0;
            padding: 0;
            line-height: 1.4;
            background: white;
        }

        /* main container */
        .invoice-container {
            max-width: 100%;
            position: relative;
            min-height: 27cm;
        }

        /* ===== HEADER (exactly like image: facture n°, FNE, QR) ===== */
        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 18px;
            border-bottom: 2px solid #f3b229; /* yellow accent */
            padding-bottom: 8px;
        }
        .facture-numero {
            font-size: 20px;
            font-weight: 700;
            color: #1e2f4e;
            letter-spacing: 0.3px;
            line-height: 1.2;
        }
        .facture-numero small {
            font-size: 11px;
            font-weight: 400;
            color: #4a627a;
            display: block;
        }
        .fne-block {
            text-align: right;
        }
        .fne-badge {
            background-color: #f3b229;
            color: #1e2f4e;
            font-weight: 800;
            font-size: 22px;
            padding: 6px 20px;
            border-radius: 40px;
            display: inline-block;
            letter-spacing: 2px;
            margin-bottom: 6px;
            border: 1px solid #d4951a;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .qr-simulation {
            background: #1e2f4e;
            color: white;
            font-size: 9px;
            font-weight: 600;
            padding: 8px 14px;
            border-radius: 30px;
            display: inline-block;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .qr-simulation span {
            background: white;
            color: #1e2f4e;
            padding: 2px 6px;
            border-radius: 16px;
            margin-left: 8px;
            font-size: 9px;
        }

        /* company / client info cards */
        .info-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            background: #f8fafd;
            border-radius: 14px;
            padding: 18px 16px;
            margin: 18px 0 22px 0;
            border: 1px solid #e6eef5;
        }
        .company-details {
            width: 45%;
        }
        .client-details {
            width: 45%;
            text-align: right;
        }
        .detail-title {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #6b7d96;
            margin-bottom: 6px;
            font-weight: 600;
        }
        .detail-content {
            font-weight: 500;
            color: #1e2f4e;
            font-size: 11px;
            line-height: 1.5;
        }
        .detail-content strong {
            color: #1e2f4e;
            font-weight: 700;
        }

        /* object line (like “Objet : …” in original) */
        .object-line {
            background: white;
            padding: 8px 16px;
            border-left: 6px solid #f3b229;
            border-radius: 0 10px 10px 0;
            margin: 20px 0 20px 0;
            font-size: 11.5px;
            font-weight: 500;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            border-top: 1px solid #eef3f9;
            border-bottom: 1px solid #eef3f9;
            border-right: 1px solid #eef3f9;
        }

        /* PRODUCT TABLE – clean, light */
        table.products {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0 25px 0;
            font-size: 10.5px;
        }
        table.products th {
            background: #1e2f4e;
            color: white;
            font-weight: 600;
            padding: 12px 8px;
            text-align: left;
            font-size: 10px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        table.products th:first-child {
            border-top-left-radius: 12px;
        }
        table.products th:last-child {
            border-top-right-radius: 12px;
        }
        table.products td {
            padding: 12px 8px;
            border-bottom: 1px solid #e2eaf2;
            background: white;
        }
        table.products tr:last-child td {
            border-bottom: none;
        }
        /* subtle zebra */
        table.products tbody tr:nth-child(even) {
            background-color: #f9fcff;
        }

        /* totals section – compact & right-aligned */
        .totals-block {
            display: flex;
            justify-content: flex-end;
            margin: 6px 0 25px 0;
        }
        .totals-table {
            width: 280px;
            border-collapse: collapse;
        }
        .totals-table td {
            padding: 8px 10px;
            font-size: 11px;
            border: none;
        }
        .totals-table td:first-child {
            font-weight: 600;
            color: #1e2f4e;
            text-align: right;
        }
        .totals-table td:last-child {
            text-align: right;
            font-weight: 600;
            background: #f4f7fc;
            border-radius: 10px;
        }
        .grand-total {
            background: #f3b229 !important;
            color: #1e2f4e;
            font-weight: 800;
            border-radius: 12px;
        }

        /* payment details – grid with 2 columns (like original but cleaner) */
        .payment-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            background: #f4f9ff;
            border-radius: 20px;
            padding: 18px 22px;
            margin: 25px 0 10px 0;
            border: 1px solid #dbe7f3;
        }
        .payment-item {
            display: flex;
            flex-direction: column;
        }
        .payment-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            color: #4f6f8f;
            letter-spacing: 0.4px;
            margin-bottom: 4px;
        }
        .payment-value {
            font-size: 12px;
            font-weight: 600;
            color: #0f263b;
            word-break: break-word;
        }

        /* FOOTER fixed – but we use block because @page margin gives room */
        .footer-info {
            margin-top: 30px;
            border-top: 2px solid #f3b229;
            padding-top: 12px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            font-size: 8.5px;
            color: #3f556b;
        }
        .footer-info div {
            width: 30%;
        }
        .footer-info .footer-center {
            text-align: center;
            width: 38%;
        }
        .footer-info .footer-right {
            text-align: right;
        }

        /* extra small */
        .logo-placeholder {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .logo-placeholder img {
            max-height: 55px;
            max-width: 160px;
            object-fit: contain;
        }

        /* spacing */
        hr {
            border: 0.5px solid #e2eaf2;
            margin: 10px 0;
        }
    </style>
</head>
<body>
<div class="invoice-container">

    <!-- ========== HEADER : numéro facture + FNE + QR ========== -->
    <div class="header-row">
        <div class="facture-numero">
            FACTURE N° <?= $invoiceNumber ?>
            <small>Originale</small>
        </div>
        <div class="fne-block">
            <div class="fne-badge">FNE</div>
            <div class="qr-simulation">
                ⬛⬛⬛ QR code <span>certifiée</span>
            </div>
        </div>
    </div>

    <!-- ========== LOGO / COMPANY + CLIENT (sur la même ligne) ========== -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
        <div class="logo-placeholder">
            <img src="<?= base_url() . "/uploads/school_content/admin_logo/" . $company['admin_logo'] ?>" alt="logo entreprise" style="max-width:160px;">
        </div>
        <div style="color:#1e2f4e; font-weight:500; font-size:11px;">
            N° RCCM: <?= $companyRccm ?>
        </div>
    </div>

    <!-- ========== INFOS SOCIÉTÉ + CLIENT (type “carte”) ========== -->
    <div class="info-grid">
        <!-- émetteur -->
        <div class="company-details">
            <div class="detail-title">ÉMETTEUR</div>
            <div class="detail-content">
                <strong><?= $companyName ?></strong><br>
                <?= $companyAddress ?><br>
                Tél: <?= $companyPhone ?>  |  Email: <?= $companyEmail ?><br>
                RC: <?= $companyRccm ?>  ·  Impôt: <?= $companyCentreimpot ?><br>
                Régime: <?= $companyRegime ?>
            </div>
        </div>
        <!-- client -->
        <div class="client-details">
            <div class="detail-title">CLIENT</div>
            <div class="detail-content">
                <strong><?= $customerFullname ?></strong><br>
                <?= $customerAddress ?><br>
                <span style="font-size:10px;">Affaire suivie par: <?= $userName ?></span><br>
                <span style="font-size:10px;">Date facture: <?= $invoiceDate ?></span>
            </div>
        </div>
    </div>

    <!-- ========== OBJET (designation) ========== -->
    <div class="object-line">
        <strong>📄 Objet :</strong> <?= $invoiceDesignation ?>
    </div>

    <!-- ========== TABLEAU PRODUITS ========== -->
    <table class="products">
        <thead>
        <tr>
            <th>Qté</th>
            <th>Description</th>
            <th>Prix unitaire (CFA)</th>
            <th>Total (CFA)</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['quantity']) ?></td>
                <td><?= htmlspecialchars($item['item_name']) ?> - <?= htmlspecialchars($item['category_name']) ?></td>
                <td><?= number_format((float)$item['unit_price'], 0, ',', ' ') ?></td>
                <td><?= number_format((float)$item['line_total'], 0, ',', ' ') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <!-- ========== TOTAUX (HT, TVA, TTC) à droite ========== -->
    <div class="totals-block">
        <table class="totals-table">
            <tr><td>Total HT</td><td><?= number_format($total_ht, 0, ',', ' ') ?> CFA</td></tr>
            <tr><td>TVA (<?= $tva_rate ?>%)</td><td><?= $tva_amount ?></td></tr>
            <tr><td style="border-top: 2px solid #1e2f4e;">Total TTC</td>
                <td style="border-top: 2px solid #1e2f4e;" class="grand-total"><?= number_format($total_ttc, 0, ',', ' ') ?> CFA</td>
            </tr>
        </table>
    </div>

    <!-- ========== PAIEMENT + NOTES (2 colonnes) ========== -->
    <div class="payment-grid">
        <div class="payment-item">
            <span class="payment-label">Montant en lettres</span>
            <span class="payment-value"><?= $totalAsletter ?? '—' ?></span>
        </div>
        <div class="payment-item">
            <span class="payment-label">Mode de paiement</span>
            <span class="payment-value"><?= $payment_method ?></span>
        </div>
        <div class="payment-item">
            <span class="payment-label">Notes / conditions</span>
            <span class="payment-value"><?= $notes ?></span>
        </div>
        <div class="payment-item">
            <span class="payment-label">Délai / validité</span>
            <span class="payment-value"><?= $valid_until ?>  |  <?= $delivery_terms ?></span>
        </div>
    </div>

    <!-- ========== FOOTER (banque, coordonnées) ========== -->
    <div class="footer-info">
        <div>
            <?= $companyName ?><br>
            RC: <?= $companyRccm ?>
        </div>
        <div class="footer-center">
            🏦 <?= $companyBank ?> · IBAN <?= $companyComptBank ?><br>
            ☎ <?= $companyPhone ?>  ✉ <?= $companyEmail ?>
        </div>
        <div class="footer-right">
            <?= $companyAddress ?><br>
            <?= $companyWebsite ?>
        </div>
    </div>

    <!-- petite signature silencieuse (optionnel) -->
    <div style="text-align: right; font-size: 8px; color: #a0b8cc; margin-top: 12px;">
        Document certifié électroniquement · FNE <?= date('Y') ?>
    </div>

</div>
<!-- .invoice-container -->
</body>
</html>