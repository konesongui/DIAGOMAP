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

    $customerFullname = $invoice['customer_name'].' '.$invoice['customer_last_name']??"N/A";
    $customerAddress = $invoice['customer_address'].' / '.$invoice['customer_phone']??"N/A";
    $invoiceDate = !empty($invoice['invoice_date'])? date('d/m/Y', strtotime($invoice['invoice_date'])) :"N/A";
    $invoiceDesignation = $invoice['designation']??"N/A";
    $invoiceNumber = $invoice['invoice_number']??"N/A";

    $items = $invoice['items']? $invoice['items']:[]; 

    $tva_amount = (!empty($invoice['tva_amount']) && floatval($invoice['tva_amount']) > 0)? floatval($invoice['tva_amount']) :"Non facturée";
    $tva_rate = (!empty($invoice['tva_rate']) && floatval($invoice['tva_rate']) > 0)? floatval($invoice['tva_rate']) :0;
    $total_ht = (!empty($invoice['total_ht']) && floatval($invoice['total_ht']) > 0)? floatval($invoice['total_ht']) :0;
    $total_ttc = (!empty($invoice['total_ttc']) && floatval($invoice['total_ttc']) > 0)? floatval($invoice['total_ttc']) :0;
    $payment_method = !empty($invoice['payment_method'])? $invoice['payment_method'] :"N/A";





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
</head>
<body style="font-family: Arial, sans-serif; font-size: 12px; color: rgb(19, 96, 171); margin: 0; padding: 0; width: 21cm; height: auto;">
    <div class="main-content" style="padding-bottom: 2cm;">
        <div class="invoice-content" style="margin-top: 0;">
            <table class="products-table" style="width: 100%; border-collapse: collapse; margin-top: 3px;">
                <thead style="background-color:#ccd1cc;">
                    <tr>
                        <th style="background-color: rgb(19, 96, 171); color: white; padding: 5px; text-align: left; font-size: 12px;">Qté</th>
                        <th style="background-color: rgb(19, 96, 171); color: white; padding: 5px; text-align: left; font-size: 12px;">Description</th>
                        <th style="background-color: rgb(19, 96, 171); color: white; padding: 5px; text-align: left; font-size: 12px;">PU (CFA)</th>
                        <th style="background-color: rgb(19, 96, 171); color: white; padding: 5px; text-align: left; font-size: 12px;">Total (CFA)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr style="background-color: <?= $loop->even ? 'rgba(250, 183, 22, 0.1)' : 'transparent'; ?>">
                            <td style="border: 1px solid #ccc; padding: 5px; font-size: 12px;"><?= htmlspecialchars($item['quantity']) ?></td>
                            <td style="border: 1px solid #ccc; padding: 5px; font-size: 12px;"><?= htmlspecialchars($item['item_name']) ?> - <?= htmlspecialchars($item['category_name']) ?></td>
                            <td style="border: 1px solid #ccc; padding: 5px; font-size: 12px;"><?= htmlspecialchars($item['unit_price']) ?></td>
                            <td style="border: 1px solid #ccc; padding: 5px; font-size: 12px;"><?= htmlspecialchars($item['line_total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table><br>

            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td colspan="3" style="border: none; text-align: right;"><strong style="color: rgb(19, 96, 171);">Total HT :</strong></td>
                    <td style="border: 1px solid #ccc; padding: 5px; font-size: 12px;"><?= number_format($total_ht, 2, ',', ' ') ;?></td>
                </tr>
                <tr>
                    <td colspan="3" style="border: none; text-align: right;"><strong style="color: rgb(19, 96, 171);">TVA (<?= $tva_rate ;?>%) :</strong></td>
                    <td style="border: 1px solid #ccc; padding: 5px; font-size: 12px;"><?= $tva_amount ;?></td>
                </tr>
                <tr>
                    <td colspan="3" style="border: none; text-align: right;"><strong style="color: rgb(19, 96, 171);">Total TTC :</strong></td>
                    <td style="border: 1px solid #ccc; padding: 5px; font-size: 12px;"><?= number_format($total_ttc, 2, ',', ' ') ;?></td>
                </tr>
            </table>

            <div style="margin-top: 10px; padding: 10px; background-color: rgba(19, 96, 171, 0.05); border-radius: 5px; border-left: 4px solid rgb(250, 183, 22);">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="width: 50%; padding: 5px; vertical-align: top;">
                            <div style="font-weight: bold; color: rgb(19, 96, 171); font-size: 12px;">Montant en lettres :</div>
                            <div style="color: #333; font-size: 12px;"><?= $totalAsletter ;?></div>
                        </td>
                        <td style="width: 50%; padding: 5px; vertical-align: top;">
                            <div style="font-weight: bold; color: rgb(19, 96, 171); font-size: 12px;">Mode de paiement :</div>
                            <div style="color: #333; font-size: 12px;"><?= $payment_method;?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 50%; padding: 5px; vertical-align: top;">
                            <div style="font-weight: bold; color: rgb(19, 96, 171); font-size: 12px;">Garantie :</div>
                            <div style="color: #333; font-size: 12px;">[à spécifier]</div>
                        </td>
                        <td style="width: 50%; padding: 5px; vertical-align: top;">
                            <div style="font-weight: bold; color: rgb(19, 96, 171); font-size: 12px;">Règlement :</div>
                            <div style="color: #333; font-size: 12px;">Payable 30 jours dépôt de facture</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>