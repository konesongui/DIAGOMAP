<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title ?? 'Solde facture', ENT_QUOTES, 'UTF-8'); ?></title>
    <style>
        @page { size: A4; margin: 1cm; }

        * { box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #2b2b2b;
            margin: 0;
            padding: 0;
            line-height: 1.4;
        }

        .container { padding: 10mm 12mm; }

        /* ===== HEADER ===== */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px solid #1E4DB7;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }

        .brand-block { display: flex; align-items: center; gap: 12px; }

        .brand-block img {
            max-width: 90px;
            max-height: 70px;
            object-fit: contain;
        }

        .brand-name {
            font-size: 16px;
            font-weight: 700;
            color: #1E4DB7;
            letter-spacing: 0.3px;
        }

        .doc-title-block { text-align: right; }

        .doc-title-block .label {
            display: inline-block;
            background: #1E4DB7;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            padding: 5px 14px;
            border-radius: 3px;
            letter-spacing: 0.4px;
        }

        .doc-title-block .facture-numero {
            margin-top: 6px;
            font-size: 12px;
            font-weight: 700;
            color: #2b2b2b;
        }

        /* ===== INFO ROW ===== */
        .header-row {
            display: flex;
            justify-content: space-between;
            gap: 24px;
            margin-bottom: 18px;
        }

        .info-card {
            width: 48%;
            background: #f7f8fa;
            border: 1px solid #e2e4e8;
            border-radius: 4px;
            padding: 10px 12px;
        }

        .info-card .info-title {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1E4DB7;
            margin-bottom: 6px;
        }

        .info-card .small { font-size: 9px; color: #000; margin-bottom: 2px; }
        .info-card .small strong { color: #000; }

        /* ===== PRODUCTS TABLE ===== */
        table { width: 100%; border-collapse: collapse; }

        .products-table {
            margin-top: 4px;
            border: 1px solid #dcdfe4;
            border-radius: 4px;
            overflow: hidden;
        }

        .products-table th {
            background: #1E4DB7;
            color: #fff;
            padding: 8px 8px;
            text-align: left;
            font-weight: 600;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .products-table td {
            border-bottom: 1px solid #eceef1;
            padding: 7px 8px;
            font-size: 9.5px;
        }

        .products-table tbody tr:nth-child(even) { background: #f9fafb; }

        .products-table td.numeric { text-align: right; white-space: nowrap; }
        .products-table td.qty { text-align: center; }

        /* ===== TOTALS ===== */
        .totals-container {
            width: 50%;
            margin-left: auto;
            margin-top: 14px;
        }

        .totals-table { width: 100%; border-collapse: collapse; }

        .totals-table td {
            padding: 7px 10px;
            font-size: 10px;
            border-bottom: 1px solid #eceef1;
        }

        .totals-table td:first-child { font-weight: 600; color: #000; }
        .totals-table td:last-child { text-align: right; font-weight: 600; color: #000; }

        .total-payer td {
            border-top: 2px solid #1E4DB7;
            border-bottom: none;
            font-size: 12px;
            padding-top: 10px;
        }

        .total-payer td:first-child { color: #000; font-weight: 700; }
        .total-payer td:last-child { color: #000; font-weight: 700; }

        /* ===== PAYMENT HISTORY ===== */
        .payments-section { margin-top: 20px; }

        .payments-section .section-title {
            font-size: 10.5px;
            font-weight: 700;
            color: #1E4DB7;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #dcdfe4;
        }

        /* ===== FOOTER ===== */
        .footer-block {
            margin-top: 28px;
            border-top: 1px solid #e2e4e8;
            padding-top: 10px;
        }

        .address-line {
            text-align: center;
            font-size: 9px;
            color: #000;
            margin: 6px 0;
        }

        .fne-badge {
            background-color: #2A9D8F;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 9.5px;
            font-weight: 700;
            display: inline-block;
            margin-top: 10px;
        }

        .qr-code { text-align: center; margin: 14px 0; }
        .qr-code img { max-width: 90px; max-height: 90px; }
        .qr-code p { margin: 4px 0 0; font-size: 8.5px; color: #000; }

        .small { font-size: 9px; color: #000; }
    </style>
</head>
<body>
    <div class="container">

        <!-- ===== HEADER ===== -->
        <div class="top-bar">
            <div class="brand-block">
                <?php if (!empty($company['admin_logo'])): ?>
                    <img src="<?= base_url() . '/uploads/school_content/admin_logo/' . $company['admin_logo'] ?>" alt="Logo">
                <?php endif; ?>
                <div class="brand-name">
                    <?= htmlspecialchars($company['company_supplier'] ?? $company['name'] ?? 'Société', ENT_QUOTES, 'UTF-8'); ?>
                </div>
            </div>
            <div class="doc-title-block">
                <span class="label">SOLDE FACTURE</span>
                <div class="facture-numero">N° <?= htmlspecialchars($invoice['invoice_number'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
        </div>

        <!-- ===== INFO ROW ===== -->
        <div class="header-row">
            <div class="info-card">
                <div class="info-title">Émetteur</div>
                <div class="small"><?= nl2br(htmlspecialchars($company['address'] ?? '', ENT_QUOTES, 'UTF-8')); ?></div>
                <div class="small"><strong>Tél :</strong> <?= htmlspecialchars($company['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                <div class="small"><strong>Email :</strong> <?= htmlspecialchars($company['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
            <div class="info-card">
                <div class="info-title">Détails</div>
                <div class="small"><strong>Date :</strong> <?= !empty($invoice['invoice_date']) ? date('d/m/Y', strtotime($invoice['invoice_date'])) : '-' ?></div>
                <div class="small"><strong>Échéance :</strong> <?= !empty($invoice['due_date']) ? date('d/m/Y', strtotime($invoice['due_date'])) : '-' ?></div>
                <div class="small"><strong>Client :</strong> <?= htmlspecialchars(trim(($invoice['customer_name'] ?? '') . ' ' . ($invoice['customer_last_name'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></div>
            </div>
        </div>

        <!-- ===== PRODUCTS ===== -->
        <table class="products-table">
            <thead>
                <tr>
                    <th style="width:56%">Article</th>
                    <th style="width:12%; text-align:center">Qté</th>
                    <th style="width:16%; text-align:right">PU (FCFA)</th>
                    <th style="width:16%; text-align:right">Total (FCFA)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($invoice['items'])): ?>
                    <?php foreach ($invoice['items'] as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['item_name'] ?? $item['service_name'] ?? 'Article', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="qty"><?= htmlspecialchars($item['quantity'] ?? 0, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="numeric"><?= number_format((float)($item['unit_price'] ?? 0), 0, ',', ' '); ?></td>
                            <td class="numeric"><?= number_format((float)($item['line_total'] ?? 0), 0, ',', ' '); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align:center; color:#000;">Aucun article</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- ===== TOTALS ===== -->
        <div class="totals-container">
            <table class="totals-table">
                <tr>
                    <td>Total TTC</td>
                    <td><?= number_format($total_ttc ?? 0, 0, ',', ' '); ?> FCFA</td>
                </tr>
                <tr>
                    <td>Montant payé</td>
                    <td><?= number_format($amount_paid ?? 0, 0, ',', ' '); ?> FCFA</td>
                </tr>
                <tr class="total-payer">
                    <td>Reste à payer</td>
                    <td><?= number_format($remaining_amount ?? 0, 0, ',', ' '); ?> FCFA</td>
                </tr>
            </table>
        </div>

        <!-- ===== PAYMENT HISTORY ===== -->
        <?php if (!empty($payments)): ?>
            <div class="payments-section">
                <div class="section-title">Historique des paiements</div>
                <table class="products-table small">
                    <thead>
                        <tr>
                            <th style="width:25%">Date</th>
                            <th style="width:25%; text-align:right">Montant (FCFA)</th>
                            <th style="width:25%">Mode</th>
                            <th style="width:25%">Référence</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td><?= !empty($payment['payment_date']) ? date('d/m/Y', strtotime($payment['payment_date'])) : '-' ?></td>
                                <td class="numeric"><?= number_format((float)($payment['amount'] ?? 0), 0, ',', ' '); ?></td>
                                <td><?= htmlspecialchars($payment['method'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?= htmlspecialchars($payment['reference'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- ===== FOOTER ===== -->
        <div class="footer-block">
            <div class="address-line"><?= nl2br(htmlspecialchars($company['address'] ?? '', ENT_QUOTES, 'UTF-8')); ?></div>

            <?php if (!empty($invoice['fne_certified']) && !empty($invoice['fne_token'])): ?>
                <div style="text-align:center;">
                    <div class="fne-badge">✓ Certifié FNE - Réf : <?= htmlspecialchars($invoice['fne_reference'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                    <div class="qr-code">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=<?= urlencode($invoice['fne_token']) ?>" alt="QR Code FNE">
                        <p>Référence : <?= htmlspecialchars($invoice['fne_reference'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>