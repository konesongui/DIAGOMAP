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

// 🔹 Gestion des taxes selon l'option
$tax_option = $quote['tax_option'] ?? 'none';
$tva_amount = 0;
$other_tax_amount = 0;
$total_tax_amount = 0;

if ($tax_option === 'tva') {
    $tva_rate = !empty($quote['tva_rate']) ? floatval($quote['tva_rate']) : 18;
    $tva_amount = !empty($quote['tva_amount']) ? floatval($quote['tva_amount']) : 0;
    $total_tax_amount = $tva_amount;
    $tax_label = "TVA ($tva_rate%)";
    $tax_value = number_format($tva_amount, 2, ',', ' ');
} elseif ($tax_option === 'other') {
    $other_tax_rate = !empty($quote['other_tax_rate']) ? floatval($quote['other_tax_rate']) : 0;
    $other_tax_name = !empty($quote['other_tax_name']) ? $quote['other_tax_name'] : "Autre taxe";
    $other_tax_amount = !empty($quote['other_tax_amount']) ? floatval($quote['other_tax_amount']) : 0;
    $total_tax_amount = $other_tax_amount;
    $tax_label = "$other_tax_name ($other_tax_rate%)";
    $tax_value = number_format($other_tax_amount, 2, ',', ' ');
} else {
    $tax_label = "Taxes";
    $tax_value = "0,00";
}

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

// Suppression de tout le code de regroupement par catégorie
// Les articles seront affichés simplement dans l'ordre du tableau
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Devis <?= $quoteNumber ?></title>
    <style>
        /* --- CONFIGURATION DE LA PAGE --- */
        @page {
            margin: 0.5cm 0.8cm 0.1cm 0.8cm;
            size: A4;
        }

        /* --- RÉINITIALISATION --- */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            color: black;
            margin: 0;
            padding: 0;
            width: 100%;
            line-height: 1.2;
        }

        /* --- STRUCTURE PRINCIPALE --- */
        .document-wrapper {
            position: relative;
            width: 100%;
        }

        /* --- EN-TÊTE DU DEVIS --- */
        .main-header {
            background-color: white;
            padding: 4px;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            color: #000;
            margin-bottom: 8px;
        }

        /* --- BLOCS D'INFORMATIONS --- */
        .info-container {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 8px;
        }

        .info-box {
            flex: 1;
            border: 1px solid #ccc;
            padding: 6px;
            border-radius: 3px;
            font-size: 9px;
            min-height: auto;
            height: auto;
        }

        .company-logo {
            width: 80px;
            height: auto;
            margin-bottom: 3px;
        }

        .qr-code-box {
            width: 70px;
            height: auto;
            margin-bottom: 4px;
        }

        /* --- OBJET --- */
        .object {
            font-size: 10px;
            margin: 6px 0;
            padding-bottom: 3px;
            border-bottom: 1px solid #eee;
            font-weight: bold;
        }

        /* --- TABLEAU DES ARTICLES --- */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0 6px 0;
            font-size: 9px;
            page-break-inside: auto;
        }

        .items-table th {
            background-color: white;
            color: black;
            padding: 5px 3px;
            text-align: left;
            font-weight: bold;
            border: 1px solid rgb(23, 12, 12);
            font-size: 9px;
        }

        .items-table td {
            padding: 4px 3px;
            border: 1px solid #ccc;
            vertical-align: top;
            font-size: 9px;
        }

        .items-table tr:nth-child(even) {
            background-color: rgba(250, 183, 22, 0.05);
        }

        /* Colonnes optimisées */
        .items-table th:nth-child(1),
        .items-table td:nth-child(1) {
            width: 45%;
        }

        .items-table th:nth-child(2),
        .items-table td:nth-child(2),
        .items-table th:nth-child(3),
        .items-table td:nth-child(3),
        .items-table th:nth-child(4),
        .items-table td:nth-child(4),
        .items-table th:nth-child(5),
        .items-table td:nth-child(5),
        .items-table th:nth-child(6),
        .items-table td:nth-child(6) {
            width: 11%;
            text-align: right;
        }

        /* --- TABLEAU DES TOTAUX --- */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 10px;
        }

        .totals-table th {
            background-color: white;
            color: black;
            padding: 6px 3px;
            text-align: center;
            border: 1px solid rgb(0, 0, 0);
        }

        .totals-table td {
            padding: 6px 3px;
            border: 1px solid #ccc;
            text-align: right;
            font-weight: bold;
        }

        /* --- DÉTAILS DE PAIEMENT --- */
        .payment-info {
            margin: 20px 0;
            padding: 15px;
            background-color: rgba(16, 14, 14, 0.05);
            border-radius: 5px;
            border-left: 3px solid rgb(16, 15, 15);
        }

        .payment-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }

        .payment-grid > div {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 8px 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            font-size: 12px;
            line-height: 1.4;
        }

        .payment-grid > div strong {
            color: #333;
            display: inline-block;
            margin-right: 5px;
        }
        /* --- NOUVELLE PAGE --- */
        .page-break {
            page-break-before: always;
            margin-top: 0;
            padding-top: 0;
        }

        .continuation-header {
            background-color: rgb(19, 96, 171);
            color: white;
            padding: 4px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            margin-bottom: 6px;
            border-radius: 2px;
        }

        /* --- FOOTER FIXE --- */
        .footer {
            position: fixed;
            bottom: 0.1cm;
            left: 0.8cm;
            right: 0.8cm;
            height: 1.0cm;
            background-color: white;
            padding: 2px 0;
            font-size: 8px;
            color: #000;
            border-top: 1px solid rgb(12, 11, 11);
            z-index: 1000;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            height: 100%;
        }

        .footer-column {
            flex: 1;
            padding: 0 5px;
            line-height: 1.2;
        }

        /* --- GESTION DE LA PAGINATION --- */
       

        /* Règle pour éviter les coupures dans les lignes du tableau */
        .items-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        /* --- IMPRESSION --- */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                margin: 0;
                font-size: 9px !important;
            }

            .footer {
                position: fixed !important;
                bottom: 0.1cm !important;
                height: 1.0cm !important;
            }

            /* Garder les totaux ensemble sur la dernière page */
            .last-page-section {
                page-break-inside: avoid;
            }

            @page {
                margin: 0.4cm 0.6cm 0.1cm 0.6cm !important;
            }

            .page-break {
                page-break-before: always;
                margin-top: 0 !important;
                padding-top: 0 !important;
            }

            .page-content {
                padding-bottom: 1.1cm !important;
            }
        }

        .tight-spacing {
            margin: 0;
            padding: 0;
        }

        .compact-table tr {
            line-height: 1.1;
        }
    </style>
</head>
<body>
<!-- Pied de page fixe -->
<div class="footer">
    <div class="footer-content">
        <div class="footer-column">
            <strong><?= $companyName ?></strong><br>
            RCCM: <?= $companyRccm ?><br>
            Banque: <?= $companyBank ?><br>
        </div>
        <div class="footer-column">
            <strong>Contact:<?= $companyPhone ?></strong><br>
            Email: <?= $companyEmail ?><br>
            Compte: <?= $companyComptBank ?>
        </div>
        <div class="footer-column">
            <strong>Adresse:  <?= $companyAddress ?></strong><br>
            Site: <?= $companyWebsite ?>
        </div>
    </div>
</div>

<div class="document-wrapper">
    <!-- Espace principal avec marge pour le footer -->
    <div class="page-content">
        <!-- En-tête du devis -->
        <div class="main-header">Devis N° <?= $quoteNumber ?> du <?= $quoteDate ?></div>

        <!-- Informations entreprise et client -->
        <div class="info-container">
            <!-- Entreprise -->
            <div class="info-box">
                <img src="<?= base_url() . "/uploads/school_content/admin_logo/" . $company['admin_logo'] ?>"
                     alt="Logo" class="company-logo" />
                <div><strong><?= $companyName ?></strong></div>
                <div><strong><?= $company['company_fullname'] ?></strong></div>
                <div>RCCM : <?= $companyRccm ?></div>
                <div>Centre d'impôt : <?= $companyCentreimpot ?></div>
                <div>Régime d'Imposition : <?= $companyRegime ?></div>
                <div>Téléphone : <?= $companyPhone ?></div>
                <div>Email : <?= $companyEmail ?></div>
                <div>Adresse : <?= $companyAddress ?></div>
                <div><strong>Affaire suivie par :</strong> <?= $UsersName ?></div>
            </div>

            <!-- Client -->
            <div class="info-box">
                <img src="<?= $qrCodePath ?>" alt="QR Code" class="qr-code-box" />
                <div><strong>Client :</strong> <?= $customerFullname ?></div>
                <div>Compte contribuable : <?= $customerComptec ?></div>
                <div>Téléphone : <?= $customerPhone ?></div>
                <div>Adresse email : <?= $customerAddress ?></div>
                <div>Adresse : <?= $customerAddresse ?></div>
            </div>
        </div>

        <!-- Objet -->
        <div class="object">
            <strong>Objet :</strong> <?= $quoteDesignation ?>
        </div>

        <?php
        // Calcul simplifié pour la pagination sans catégories
        $lines_first_page = 25; // Un peu plus car plus d'en-têtes de catégories
        $lines_other_pages = 42; // Un peu plus aussi

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

        // Afficher les pages
        foreach ($items_per_page as $page_index => $page_items):
            $is_last_page = ($page_index == $total_pages - 1);
            ?>

            <?php if ($page_index > 0): ?>
            <div class="page-break tight-spacing">
            <div class="continuation-header">
                Suite du Devis N° <?= $quoteNumber ?> - Page <?= $page_index + 1 ?>
            </div>
        <?php endif; ?>

            <!-- Tableau des articles -->
            <table class="items-table compact-table">
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
                <?php foreach ($page_items as $item): ?>
                    <tr class="keep-together">
                        <td><?= htmlspecialchars($item['item_name'] ?? '') ?></td>
                        <td><?= number_format(floatval($item['quantity'] ?? 0), 2, ',', ' ') ?></td>
                        <td><?= number_format(floatval($item['unit_price'] ?? 0), 2, ',', ' ') ?></td>
                        <td><?= number_format(floatval($item['discount'] ?? 0), 2, ',', ' ') ?></td>
                        <td><?= number_format(floatval($item['line_total'] ?? 0), 2, ',', ' ') ?></td>
                        <td><?= number_format(floatval($item['line_total_after_discount'] ?? 0), 2, ',', ' ') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($is_last_page): ?>
            <!-- Totaux (seulement sur la dernière page) -->
            <div class="last-page-section">
                <table class="totals-table">
                    <thead>
                    <tr>
                        <th>Total HT</th>
                        <th>Remises Total</th>
                        <th>Net Hors-Taxe</th>
                        <th><?= $tax_label ?></th>
                        <th>Total <?= $tax_option === 'tva' ? 'TTC' : ($tax_option === 'other' ? 'Taxe incluse' : 'HT') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?= number_format($total_ht, 2, ',', ' ') ?></td>
                        <td><?= number_format($discount_amount, 2, ',', ' ') ?></td>
                        <td><?= number_format($total_after_discount, 2, ',', ' ') ?></td>
                        <td><?= $tax_value ?></td>
                        <td><?= number_format($total_ttc, 2, ',', ' ') ?></td>
                    </tr>
                    </tbody>
                </table>

                <!-- Informations de paiement -->
                <div class="payment-info">
                    <div class="payment-grid">
                        <div><strong>Montant en lettres :</strong> <?= isset($totalAsletter) ? $totalAsletter : number_format($total_ttc, 2, ',', ' ') . ' FCFA' ?></div>
                        <div><strong>Mode de paiement :</strong> <?= $payment_method ?></div>
                        <div><strong>Terme de livraison :</strong> <?= $delivery_terms ?></div>
                        <div><strong>Lieu de livraison :</strong> <?= $delivery_location ?></div>
                        <div><strong>Règlement :</strong> <?= $payment_terms ?></div>
                        <?php if ($tax_option === 'other' && !empty($quote['other_tax_name'])): ?>
                            <div><strong>Taxe appliquée :</strong> <?= $quote['other_tax_name'] ?> (<?= number_format($quote['other_tax_rate'], 2, ',', ' ') ?>%)</div>
                        <?php endif; ?>
                    </div>
                </div>
                </div>
            </div>
        <?php endif; ?>

            <?php if ($page_index > 0): ?>
            </div>
        <?php endif; ?>

        <?php endforeach; ?>
    </div>
</div>
</body>
</html>