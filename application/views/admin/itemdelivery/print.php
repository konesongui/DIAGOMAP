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
$customerAddresse = $delivery['customer_address'] ?? "N/A";
$quoteDesignation = $delivery['objet'] ?? "N/A";

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

// =============================
// LOGIQUE DE PAGINATION (comme le devis)
// =============================
$lines_first_page = 20;      // Nombre max de lignes sur la première page
$lines_other_pages = 20;     // Nombre max de lignes sur les pages suivantes
$space_for_totals_lines = 12; // Espace réservé pour les totaux sur la première page

// Pagination simple des articles
$total_items = count($items);
$items_per_page = [];
$current_page_items = [];
$current_lines = 0;
$is_first_page = true;

foreach ($items as $item) {
    // 1 ligne par article
    $item_lines = 1;

    // Déterminer le nombre maximum de lignes pour la page actuelle
    $max_lines_current_page = $is_first_page ? $lines_first_page : $lines_other_pages;

    // Si c'est la première page et qu'on a déjà atteint le nombre minimum de lignes,
    // on vérifie s'il reste assez d'espace pour les totaux
    if ($is_first_page && $current_lines >= $lines_first_page - $space_for_totals_lines) {
        $max_lines_current_page = $lines_first_page;
    }

    if ($current_lines + $item_lines > $max_lines_current_page && $current_lines > 0) {
        $items_per_page[] = $current_page_items;
        $current_page_items = [];
        $current_lines = 0;
        $is_first_page = false;
    }

    $current_page_items[] = $item;
    $current_lines += $item_lines;
}

// Ajouter la dernière page
if (!empty($current_page_items)) {
    $items_per_page[] = $current_page_items;
}

$total_pages = count($items_per_page);

// Déterminer si les totaux doivent être sur la première page
$first_page_items_count = isset($items_per_page[0]) ? count($items_per_page[0]) : 0;
$force_totals_first_page = ($total_pages == 1 || $first_page_items_count < 20);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bon de Livraison <?= $deliveryNumber ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
            background: #fff;
        }

        /* Container principal */
        .print-container {
            width: 21cm;
            margin: 0 auto;
            position: relative;
        }

        /* Entête avec BORDURE NOIRE */
        .head {
            background-color: white;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            color: #000;
            border-radius: 5px;
            margin-bottom: 5px;
            border: 2px solid black;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background-color: #e9ecef;
            color: #333;
            padding: 5px;
            text-align: left;
            font-size: 11px;
            border: 1px solid #ddd;
        }

        td {
            border: 1px solid #ddd;
            padding: 4px;
            font-size: 10px;
            vertical-align: top;
        }

        .no-border {
            border: none;
        }

        /* Section infos compacte */
        .info-section {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 8px;
        }

        .info-box {
            width: 48%;
            border: 1px solid #ddd;
            padding: 6px;
            border-radius: 4px;
            background-color: #fafafa;
            font-size: 9px;
            line-height: 1.3;
        }

        .logo img {
            width: 60px;
            height: auto;
            margin-bottom: 4px;
        }

        .info-box strong {
            font-size: 10px;
        }

        /* Objet compact */
        .objet {
            margin: 6px 0;
            font-size: 10px;
            padding: 4px;
            background-color: #f9f9f9;
            border-left: 3px solid #aaa;
        }

        /* Tableau produits */
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
            page-break-inside: auto;
        }

        .products-table th,
        .products-table td {
            padding: 4px;
            font-size: 10px;
        }

        .products-table th {
            background-color: #e9ecef;
            position: sticky;
            top: 0;
        }

        /* Éviter les coupures dans les lignes du tableau */
        .products-table tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        /* Lignes paires */
        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Section totaux */
        .totals-section {
            margin-top: 8px;
            page-break-inside: avoid;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-table td {
            border: none;
            padding: 3px;
            font-size: 10px;
        }

        /* Détails paiement */
        .payment-details {
            margin-top: 8px;
            padding: 6px;
            background-color: #fafafa;
            border-radius: 4px;
            border-left: 3px solid #aaa;
            font-size: 9px;
            page-break-inside: avoid;
        }

        .payment-item {
            margin: 2px 0;
        }

        .payment-label {
            font-weight: bold;
            color: #555;
        }

        /* Pied de page fixe */
        .footer {
            position: fixed;
            bottom: 0.1cm;
            left: 0.8cm;
            right: 0.8cm;
            background-color: #f5f5f5;
            padding: 4px;
            font-size: 7px;
            color: #333;
            border-top: 1px solid #ddd;
            z-index: 1000;
        }

        .footer table {
            width: 100%;
            border-collapse: collapse;
        }

        .footer td {
            border: none;
            padding: 1px 3px;
            vertical-align: top;
            font-size: 7px;
        }

        /* Nouvelle page */
        .page-break {
            page-break-before: always;
            margin-top: 0;
            padding-top: 0;
        }

        .continuation-header {
            background-color: white;
            color: black;
            padding: 4px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 6px;
            border-radius: 2px;
        }

        /* Espace pour le footer */
        .page-content {
            padding-bottom: 1.1cm;
        }

        /* ========== STYLES POUR L'IMPRESSION ========== */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .print-container {
                width: 100%;
                margin: 0;
            }

            .footer {
                position: fixed !important;
                bottom: 0.1cm !important;
                left: 0.8cm !important;
                right: 0.8cm !important;
            }

            .page-content {
                padding-bottom: 1.1cm !important;
            }

            .info-section, .objet, .totals-section, .payment-details {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            .products-table {
                page-break-inside: auto;
            }

            .products-table thead {
                display: table-header-group;
            }

            @page {
                margin: 0.4cm 0.6cm 0.1cm 0.6cm !important;
            }
        }

        .print-info {
            font-size: 8px;
            color: #999;
            text-align: right;
            margin-bottom: 5px;
            border-bottom: 1px dashed #ddd;
            padding-bottom: 3px;
        }
    </style>
</head>
<body>
<div class="print-container">
    <!-- Pied de page fixe -->
    <div class="footer">
        <table>
            <tr>
                <td style="width: 33%; text-align: left;">
                    <?= $companyName ?> | RCCM: <?= $companyRccm ?>
                </td>
                <td style="width: 34%; text-align: left;">
                    Tél: <?= $companyPhone ?> | Email: <?= $companyEmail ?><br>
                    Banque: <?= $companyBank ?> | N° Cpt: <?= $companyComptBank ?>
                </td>
                <td style="width: 33%; text-align: left;">
                    Adresse: <?= $companyAddress ?><br>
                    Site: <?= $companyWebsite ?>
                </td>
            </tr>
        </table>
    </div>

    <div class="page-content">
        <?php foreach ($items_per_page as $page_index => $page_items):
        $is_last_page = ($page_index == $total_pages - 1);
        $is_first_page = ($page_index == 0);

        // Déterminer si on affiche les totaux sur cette page
        $show_totals_on_first_page = ($is_first_page && $force_totals_first_page);
        $show_totals_on_this_page = ($is_last_page || $show_totals_on_first_page);
        ?>

        <?php if ($page_index > 0 && !$show_totals_on_first_page): ?>
        <div class="page-break">
            <div class="continuation-header">
                Suite du Bon de Livraison N° <?= $deliveryNumber ?> - Page <?= $page_index + 1 ?>
            </div>
            <?php endif; ?>

            <!-- En-tête seulement sur la première page -->
            <?php if ($is_first_page): ?>
                <div class="head">BON DE LIVRAISON N° <?= $deliveryNumber ?></div>

                <div class="print-info">
                    Impression: <?= date('d/m/Y H:i') ?>
                </div>

                <div class="info-section">
                    <!-- Entreprise -->
                    <div class="info-box">
                        <div class="logo">
                            <img src="<?= base_url() . "/uploads/school_content/admin_logo/" . $company['admin_logo'] ?>" alt="Logo" />
                        </div>
                        <strong><?= $companyName ?></strong><br>
                        RCCM: <?= $companyRccm ?> | Centre: <?= $companyCentreimpot ?><br>
                        Tél: <?= $companyPhone ?> | Email: <?= $companyEmail ?><br>
                        Adresse: <?= $companyAddress ?><br>
                        <strong>Suivi par:</strong> <?= $UsersName ?>
                    </div>

                    <!-- Client -->
                    <div class="info-box">
                        <?php if(!empty($qrCodePath)): ?>
                            <div style="margin-bottom: 3px;">
                                <img src="<?= $qrCodePath ?>" alt="QR Code" style="width: 50px;">
                            </div>
                        <?php endif; ?>
                        <strong>Client:</strong> <?= $customerFullname ?><br>
                        Compte: <?= $customerComptec ?><br>
                        Tél: <?= $customerPhone ?><br>
                        Adresse: <?= $customerAddresse ?>
                    </div>
                </div>

                <div class="objet">
                    <strong>Objet:</strong> <?= $quoteDesignation ?>
                </div>
            <?php endif; ?>

            <!-- Tableau des produits -->
            <table class="products-table">
                <thead>
                <tr>
                    <th style="width: 8%">Qté</th>
                    <th style="width: 62%">Description</th>
                    <!--<th style="width: 15%">PU (CFA)</th>
                    <th style="width: 15%">Total (CFA)</th>-->
                </tr>
                </thead>
                <tbody>
                <?php foreach ($page_items as $item): ?>
                    <tr>
                        <td style="text-align: right;"><?= number_format($item['quantity'], 0, ',', ' ') ?></td>
                        <td><?= htmlspecialchars($item['item_name']) ?> <?= !empty($item['category_name']) ? '- ' . htmlspecialchars($item['category_name']) : '' ?></td>
                       <!-- <td style="text-align: right;"><?= number_format($item['unit_price'], 0, ',', ' ') ?></td>
                        <td style="text-align: right;"><?= number_format($item['line_total'], 0, ',', ' ') ?></td>-->
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Totaux (sur la première page si possible, sinon sur la dernière) -->
            <?php if ($show_totals_on_this_page): ?>
               <!-- <div class="totals-section">
                    <table class="totals-table">
                        <tr>
                            <td align="right" style="width: 85%"><strong>Total HT :</strong></td>
                            <td style="width: 15%; text-align: right;"><strong><?= number_format($total_ht, 0, ',', ' ') ?> FCFA</strong></td>
                        </tr>
                        <?php if(is_numeric($tva_amount)): ?>
                            <tr>
                                <td align="right"><strong>TVA (<?= $tva_rate ?>%) :</strong></td>
                                <td style="text-align: right;"><strong><?= number_format($tva_amount, 0, ',', ' ') ?> FCFA</strong></td>
                            </tr>
                        <?php endif; ?>
                        <tr style="border-top: 2px solid #ddd;">
                            <td align="right"><strong>Total TTC :</strong></td>
                            <td style="text-align: right;"><strong><?= number_format($total_ttc, 0, ',', ' ') ?> FCFA</strong></td>
                        </tr>
                    </table>
                </div>-->

                <!-- Détails de paiement -->
                <div class="payment-details">
                    <!--<div class="payment-item">
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
                        <span class="payment-label">Règlement :</span>
                        <span><?= $payment_terms ?></span>
                    </div>-->
                    <div class="payment-item">
                        <span class="payment-label">Lieu de livraison :</span>
                        <span><?= $delivery_location ?></span>
                    </div>

                </div>
            <?php endif; ?>

            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>