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
        /* --- CONFIGURATION DE LA PAGE --- */
        @page {
            margin: 0.5cm 0.8cm 1.8cm 0.8cm; /* Réduit la marge inférieure */
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
            color: rgb(19, 96, 171);
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
            background-color: rgb(250, 183, 22);
            padding: 3px;
            text-align: center;
            font-weight: bold;
            font-size: 11px;
            color: #000;
            margin-bottom: 6px;
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
            padding: 5px;
            border-radius: 2px;
            font-size: 8px;
            min-height: 150px;
        }

        .company-logo {
            width: 80px;
            height: auto;
            margin-bottom: 2px;
        }

        .qr-code-box {
            width: 70px;
            height: auto;
            margin-bottom: 4px;
        }

        /* --- OBJET --- */
        .object {
            font-size: 9px;
            margin: 5px 0;
            padding-bottom: 3px;
            border-bottom: 1px solid #eee;
        }

        /* --- TABLEAU DES ARTICLES --- */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 3px 0 5px 0;
            font-size: 8px;
            page-break-inside: auto;
        }

        .items-table th {
            background-color: rgb(19, 96, 171);
            color: white;
            padding: 4px 2px;
            text-align: left;
            font-weight: bold;
            border: 1px solid rgb(19, 96, 171);
            font-size: 8px;
        }

        .items-table td {
            padding: 4px 2px;
            border: 1px solid #ccc;
            vertical-align: top;
            font-size: 8px;
        }

        .items-table tr:nth-child(even) {
            background-color: rgba(250, 183, 22, 0.05);
        }

        /* Colonnes optimisées */
        .items-table th:nth-child(1),
        .items-table td:nth-child(1) {
            width: 45%; /* Description */
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
            width: 11%; /* Autres colonnes */
            text-align: right;
        }

        /* --- TABLEAU DES TOTAUX --- */
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 9px;
        }

        .totals-table th {
            background-color: rgb(19, 96, 171);
            color: white;
            padding: 5px 2px;
            text-align: center;
            border: 1px solid rgb(19, 96, 171);
        }

        .totals-table td {
            padding: 5px 2px;
            border: 1px solid #ccc;
            text-align: right;
            font-weight: bold;
        }

        /* --- DÉTAILS DE PAIEMENT --- */
        .payment-info {
            margin: 8px 0;
            padding: 6px;
            background-color: rgba(19, 96, 171, 0.03);
            border-radius: 2px;
            border-left: 2px solid rgb(250, 183, 22);
            font-size: 9px;
        }

        .payment-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 6px;
        }

        /* --- NOUVELLE PAGE --- */
        .page-break {
            page-break-before: always;
            margin-top: 15px;
            padding-top: 10px;
        }

        .continuation-header {
            background-color: rgb(19, 96, 171);
            color: white;
            padding: 4px;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 8px;
            border-radius: 2px;
        }

        /* --- FOOTER FIXE --- */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0.8cm;
            right: 0.8cm;
            height: 1.2cm; /* Réduit la hauteur */
            background-color: rgb(250, 183, 22);
            padding: 2px 0;
            font-size: 7px;
            color: #000;
            border-top: 1px solid rgb(19, 96, 171);
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
            padding: 0 4px;
            line-height: 1.1;
        }

        /* --- GESTION DE LA PAGINATION --- */
        .page-content {
            padding-bottom: 1.5cm; /* Espace réservé pour le footer */
        }

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
                font-size: 8px !important;
            }

            .footer {
                position: fixed !important;
                bottom: 0 !important;
            }

            /* Garder les totaux ensemble sur la dernière page */
            .last-page-section {
                page-break-inside: avoid;
            }

            /* Réduire encore les marges en impression */
            @page {
                margin: 0.4cm 0.6cm 1.5cm 0.6cm !important;
            }
        }

        /* Style pour les totaux (dernière page uniquement) */
        .last-page {
            page-break-before: auto;
        }
    </style>
</head>
<body>
<!-- Pied de page fixe -->
<div class="footer">
    <div class="footer-content">
        <div class="footer-column">
            <strong><?= $companyName ?></strong><br>
            RCCM: <?= $companyRccm ?>
        </div>
        <div class="footer-column">
            <strong>Contact</strong><br>
            Tel: <?= $companyPhone ?><br>
            Email: <?= $companyEmail ?><br>
            Banque: <?= $companyBank ?><br>
            Compte: <?= $companyComptBank ?>
        </div>
        <div class="footer-column">
            <strong>Adresse</strong><br>
            <?= $companyAddress ?><br>
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
        // Pagination des articles - AUGMENTÉ pour tenir compte de la réduction de taille
        $items_per_page = 30; // Augmenté car tout est plus petit
        $total_items = count($items);
        $pages = ceil($total_items / $items_per_page);

        for ($page = 0; $page < $pages; $page++):
            $start = $page * $items_per_page;
            $end = min(($page + 1) * $items_per_page, $total_items);
            $page_items = array_slice($items, $start, $items_per_page);

            // Pour la dernière page, ajouter une classe spéciale
            $page_class = ($page == $pages - 1) ? 'last-page' : '';
            ?>

            <?php if ($page > 0): ?>
            <div class="page-break">
            <div class="continuation-header">
                Suite du Devis N° <?= $quoteNumber ?> - Page <?= $page + 1 ?>
            </div>
        <?php endif; ?>

            <!-- Tableau des articles -->
            <table class="items-table">
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
                    <tr>
                        <td><?= htmlspecialchars($item['item_name']) ?> - <?= htmlspecialchars($item['category_name']) ?></td>
                        <td><?= number_format(floatval($item['quantity']), 2, ',', ' ') ?></td>
                        <td><?= number_format(floatval($item['unit_price']), 2, ',', ' ') ?></td>
                        <td><?= number_format(floatval($item['discount']), 2, ',', ' ') ?></td>
                        <td><?= number_format(floatval($item['line_total']), 2, ',', ' ') ?></td>
                        <td><?= number_format(floatval($item['line_total_after_discount']), 2, ',', ' ') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($page == $pages - 1): ?>
            <!-- Totaux (seulement sur la dernière page) -->
            <div class="last-page-section">
                <table class="totals-table">
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
                        <td><?= number_format($total_ht, 2, ',', ' ') ?></td>
                        <td><?= number_format($discount_amount, 2, ',', ' ') ?></td>
                        <td><?= number_format($total_after_discount, 2, ',', ' ') ?></td>
                        <td><?= number_format($tva_amount, 2, ',', ' ') ?></td>
                        <td><?= number_format($total_ttc, 2, ',', ' ') ?></td>
                    </tr>
                    </tbody>
                </table>

                <!-- Informations de paiement -->
                <div class="payment-info">
                    <div class="payment-grid">
                        <div><strong>Montant en lettres :</strong> <?= $totalAsletter ?></div>
                        <div><strong>Mode de paiement :</strong> <?= $payment_method ?></div>
                        <div><strong>Terme de livraison :</strong> <?= $delivery_terms ?></div>
                        <div><strong>Lieu de livraison :</strong> <?= $delivery_location ?></div>
                        <div><strong>Règlement :</strong> <?= $payment_terms ?></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

            <?php if ($page > 0): ?>
            </div>
        <?php endif; ?>

        <?php endfor; ?>
    </div>
</div>
</body>
</html>