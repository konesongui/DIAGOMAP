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

    $customerFullname = $delivery['customer_name'].' '.$delivery['customer_last_name']??"N/A";
    $customerAddress = $delivery['customer_address'].' / '.$delivery['customer_phone']??"N/A";
    $deliveryDate = !empty($delivery['delivery_date'])? date('d/m/Y', strtotime($delivery['delivery_date'])) :"N/A";
    $deliveryDesignation = $delivery['designation']??"N/A";
    $deliveryNumber = $delivery['delivery_number']??"N/A";

    $items = $delivery['items']? $delivery['items']:[]; 

    $tva_amount = (!empty($delivery['tva_amount']) && floatval($delivery['tva_amount']) > 0)? floatval($delivery['tva_amount']) :"Non facturée";
    $tva_rate = (!empty($delivery['tva_rate']) && floatval($delivery['tva_rate']) > 0)? floatval($delivery['tva_rate']) :0;
    $total_ht = (!empty($delivery['total_ht']) && floatval($delivery['total_ht']) > 0)? floatval($delivery['total_ht']) :0;
    $total_ttc = (!empty($delivery['total_ttc']) && floatval($delivery['total_ttc']) > 0)? floatval($delivery['total_ttc']) :0;
    $payment_method = !empty($delivery['payment_method'])? $delivery['payment_method'] :"N/A";





    // var_dump($company);
    // var_dump($companyLogo);
    // var_dump($delivery);
    // var_dump($items);
    // var_dump($delivery);
    // die();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture <?= $deliveryNumber ?></title>
</head>
<body style="font-family: Arial, sans-serif; font-size: 12px; color: rgb(19, 96, 171); margin: 0; padding: 0; width: 21cm; height: auto;">
    <div style="padding-bottom: 2cm;">
        <div style="background-color: rgb(250, 183, 22); padding: 5px; text-align: center; font-weight: bold; font-size: 16px; color: #000; border-radius: 5px; margin-bottom: 3px;">Bon de livraison N° <?= $deliveryNumber ?> </div>
        <div style="margin-top: 0;">
            <br>
            <table style="width: 100%; border-collapse: collapse; margin-top: 3px; margin-bottom: 3px;">
                <tr>
                    <td colspan="2" style="text-align: left; padding: 3px;">
                        <img src="<?= base_url() . "/uploads/school_content/admin_logo/" . $company['admin_logo'] ?>" alt="Logo" width="180" height="70" />
                    </td>
                    <td colspan="4" style="border: none;"></td>
                </tr>
                <tr>
                    <td colspan="2" style="padding: 2px 5px;">
                        <strong style="color: rgb(19, 96, 171);"><?= $companyName ?></strong><br>
                        RCCM : <?= $companyRccm ?><br>
                        Téléphone : <?= $companyPhone ?><br>
                        Email : <?= $companyEmail ?><br>
                        Adresse : <?= $companyAddress ?>
                    </td>
                    <td colspan="2"></td>
                    <td colspan="2" style="padding: 2px 5px;">
                        <strong style="color: rgb(19, 96, 171);">Date :</strong> <?= $deliveryDate ?><br>
                        <strong style="color: rgb(19, 96, 171);">Client :</strong> <?= $customerFullname ?><br>
                        <strong style="color: rgb(19, 96, 171);">Adresse du Client :</strong> <?= $customerFullname ?><br>
                        <strong style="color: rgb(19, 96, 171);">Affaire suivi par:</strong>  N/A
                    </td>
                </tr>
                <tr>
                    <td colspan="6" style="padding: 10px;">
                        <strong style="color: rgb(19, 96, 171);">Objet :</strong> <?= $deliveryDesignation ;?>
                    </td>
                </tr>
            </table><br>

            <table style="width: 100%; border-collapse: collapse; margin-top: 3px;">
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

            <table style="width: 100%; border-collapse: collapse; margin-top: 10px; margin-bottom: 2cm;">
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

    <div style="position: fixed; bottom: 0.3cm; left: 0.3cm; right: 0.3cm; background-color: rgb(250, 183, 22); padding: 3px; font-size: 9px; text-align: center; color: #000; border-top: 1px solid rgb(19, 96, 171); z-index: 1000;">
        <div style="display: flex; justify-content: space-between; padding: 0 10px;">
            <div style="text-align: left;">
                <?= $companyName ?> | Registre de Commerce: <?= $companyRccm ?>
            </div>
            <div style="text-align: center;">
                Téléphone: <?= $companyPhone ?> | Email: <?= $companyEmail ?><br>
                Banque: <?= $companyBank ?> | Numéro de compte bancaire: <?= $companyComptBank ?>
            </div>
            <div style="text-align: right;">
                Adresse: <?= $companyAddress ?><br>
                Site : <?= $companyWebsite; ?>
            </div>
        </div>
    </div>
</body>
</html>