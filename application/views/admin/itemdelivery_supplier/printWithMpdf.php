<?php
// Informations de l'entreprise
$companyName        = $company['name'] ?? "N/A";
$companyComptBank   = $company['compt_bank'] ?? "N/A";
$companyRccm        = $company['rccm'] ?? "N/A";
$companyAddress     = $company['address'] ?? "N/A";
$companyPhone       = $company['phone'] ?? "N/A";
$companyEmail       = $company['email'] ?? "N/A";
$companyCentreimpot = $company['centre_impot'] ?? "N/A";
$companyRegime      = $company['regime_imposition'] ?? "N/A";
$companyWebsite     = $company['site_web'] ?? "N/A";
$companyLogo        = $company['admin_logo'] ?? "N/A";
$companyLogo        = base_url('assets/images/admin_logo.png');
$companyBank        = $company['bank'] ?? "N/A";

// Infos client & livraison
$customerFullname   = ($delivery['customer_name'] ?? '') . ' ' . ($delivery['customer_last_name'] ?? '');
$customerComptec    = $delivery['comptec'] ?? "N/A";
$customerAddress    = $delivery['customer_address'] ?? "N/A";
$customerPhone      = $delivery['customer_phone'] ?? "N/A";
$customerEmail      = ($delivery['email'] ?? "N/A") . ' / ' . ($delivery['customer_phone'] ?? "N/A");
$deliveryDate       = !empty($delivery['delivery_date']) ? date('d/m/Y', strtotime($delivery['delivery_date'])) : "N/A";
$deliveryDesignation= $delivery['objet'] ?? "N/A";
$deliveryNumber     = $delivery['delivery_number'] ?? "N/A";
$UsersName = $delivery['user_name'] ?? "N/A";

// Items
$items              = $delivery['items'] ?? [];

// Totaux
$tva_amount         = (!empty($delivery['tva_amount']) && floatval($delivery['tva_amount']) > 0) ? floatval($delivery['tva_amount']) : "Non facturée";
$tva_rate           = (!empty($delivery['tva_rate']) && floatval($delivery['tva_rate']) > 0) ? floatval($delivery['tva_rate']) : 0;
$total_ht           = (!empty($delivery['total_ht']) && floatval($delivery['total_ht']) > 0) ? floatval($delivery['total_ht']) : 0;
$total_ttc          = (!empty($delivery['total_ttc']) && floatval($delivery['total_ttc']) > 0) ? floatval($delivery['total_ttc']) : 0;

// Paiement
$payment_method     = $delivery['payment_method'] ?? "N/A";
$payment_terms      = $delivery['payment_terms'] ?? "N/A";
$delivery_terms     = $delivery['delivery_terms'] ?? "N/A";
$delivery_location  = $delivery['delivery_location'] ?? "N/A";

// Utilisateur
$userName           = $delivery['user_name'] ?? "N/A";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bon de commande d'achat N° <?= $deliveryNumber ?></title>
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
<body style="font-family: Arial, sans-serif; font-size: 12px; color: rgb(19,96,171); margin: 0; padding: 0; width: 21cm;">

<!-- En-tête -->
<div style="background-color: rgb(250,183,22); padding: 8px; text-align: center; font-weight: bold; font-size: 16px; color: #000; border-radius: 5px; margin-bottom: 5px;">
    Bon de commande d'achat N° <?= $deliveryNumber ?>
</div>

<!-- Logo & Infos -->
<table style="width: 100%; margin-top: 10px; border-spacing: 10px;">
    <tr>
        <!-- Logo centré -->
        <td colspan="2" style="text-align: left; padding-bottom: 8px;">
            <img src="<?= base_url() . "/uploads/school_content/admin_logo/" . $company['admin_logo'] ?>"
                 style="width: 140px; height: auto;">
        </td>
    </tr>
    <tr>
        <!-- Colonne Entreprise -->
        <td style="width: 50%; vertical-align: top;">
            <div style="background: #F6F9FC; border: 1px solid #ddd;
                        border-radius: 6px; padding: 12px; font-size: 12px; min-height: 160px;">
                <strong style="font-size: 14px; display: block; margin-bottom: 6px;"><?= $companyName ?></strong>
                <div>RCCM : <?= $companyRccm ?></div>
                <div>Centre d'impôt : <?= $companyCentreimpot ?></div>
                <div>Régime d’Imposition : <?= $companyRegime ?></div>
                <div>Tél : <?= $companyPhone ?></div>
                <div>Email : <?= $companyEmail ?></div>
                <div>Adresse : <?= $companyAddress ?></div>
                <div><em>Affaire suivie par :</em> <?= $UsersName ?></div>
            </div>
        </td>

        <!-- Colonne Client -->
        <td style="width: 50%; vertical-align: top;">
            <div style="background: #F6F9FC; border: 1px solid #ddd;
                        border-radius: 6px; padding: 12px; font-size: 12px; min-height: 160px;">
                <strong style="font-size: 14px; display: block; margin-bottom: 6px;">Fournisseur : <?= $customerFullname ?></strong>
                <div>Compte contribuable : <?= $customerComptec ?></div>
                <div>Téléphone : <?= $customerPhone ?></div>
                <div>Email : <?= $customerAddress ?></div>
                <div>Adresse : <?= $customerAddresse ?></div>
                <div>Responsable : <?= $customerResponsable ?></div>
            </div>
        </td>
    </tr>
</table>

<!-- Tableau articles -->
<table style="width: 100%; border-collapse: collapse; margin-top: 12px;">
    <thead>
    <tr>
        <th style="background-color: rgb(19,96,171); color: #fff; padding: 5px; text-align: left;">Qté</th>
        <th style="background-color: rgb(19,96,171); color: #fff; padding: 5px; text-align: left;">Qté livré</th>
        <th style="background-color: rgb(19,96,171); color: #fff; padding: 5px; text-align: left;">Description</th>
        <th style="background-color: rgb(19,96,171); color: #fff; padding: 5px; text-align: left;">PU (CFA)</th>
        <th style="background-color: rgb(19,96,171); color: #fff; padding: 5px; text-align: left;">Total (CFA)</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $index => $item): ?>
        <tr style="background-color: <?= $index % 2 == 0 ? 'rgba(250,183,22,0.1)' : 'transparent'; ?>">
            <td style="border: 1px solid #ccc; padding: 5px;"><?= htmlspecialchars($item['quantity']) ?></td>
            <td style="border: 1px solid #ccc; padding: 5px;"><?= htmlspecialchars($item['delivered_quantity']) ?></td>
            <td style="border: 1px solid #ccc; padding: 5px;"><?= htmlspecialchars($item['item_name']) ?> - <?= htmlspecialchars($item['category_name']) ?></td>
            <td style="border: 1px solid #ccc; padding: 5px;"><?= number_format($item['unit_price'],0,',',' ') ?></td>
            <td style="border: 1px solid #ccc; padding: 5px;"><?= number_format($item['line_total'],0,',',' ') ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<!-- Totaux -->
<table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
    <tr>
        <td style="text-align: right; padding: 5px; border: none;" colspan="3"><strong>Total HT :</strong></td>
        <td style="border: 1px solid #ccc; padding: 5px;"><?= number_format($total_ht, 2, ',', ' ') ?></td>
    </tr>
    <tr>
        <td style="text-align: right; padding: 5px; border: none;" colspan="3"><strong>TVA (<?= $tva_rate ?>%) :</strong></td>
        <td style="border: 1px solid #ccc; padding: 5px;"><?= $tva_amount ?></td>
    </tr>
    <tr>
        <td style="text-align: right; padding: 5px; border: none;" colspan="3"><strong>Total TTC :</strong></td>
        <td style="border: 1px solid #ccc; padding: 5px;"><?= number_format($total_ttc, 2, ',', ' ') ?></td>
    </tr>
</table>

<!-- Infos supplémentaires -->
<div style="margin-top: 15px; padding: 10px; background-color: rgba(19,96,171,0.05); border-left: 4px solid rgb(250,183,22); border-radius: 5px;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 50%; padding: 5px;">
                <strong>Montant en lettres :</strong><br><?= $totalAsletter ?>
            </td>
            <td style="width: 50%; padding: 5px;">
                <strong>Mode de paiement :</strong><br><?= $payment_method ?>
            </td>
        </tr>
        <tr>
            <td style="padding: 5px;">
                <strong>Terme de livraison :</strong><br><?= $delivery_terms ?>
            </td>
            <td style="padding: 5px;">
                <strong>Lieu de livraison :</strong><br><?= $delivery_location ?>
            </td>
        </tr>
        <tr>
            <td style="padding: 5px;">
                <strong>Règlement :</strong><br><?= $payment_terms ?>
            </td>
        </tr>
    </table>
</div>


<!-- Pied de page -->
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
