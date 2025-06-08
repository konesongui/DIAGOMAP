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

    $customerFullname = $quote['customer_name'].' '.$quote['customer_last_name']??"N/A";
    $customerPhone = $quote['customer_phone']??"N/A";
    $customerAddress = $quote['customer_address'].' / '.$quote['customer_email']??"N/A";
    $customerComptec = $quote['comptec']??"N/A";
    $quoteDate = !empty($quote['quote_date'])? date('d/m/Y', strtotime($quote['quote_date'])) :"N/A";
    $quoteDesignation = $quote['designation']??"N/A";
    $quoteNumber = $quote['quote_number']??"N/A";

    $items = $quote['items']? $quote['items']:[]; 

    $tva_amount = (!empty($quote['tva_amount']) && floatval($quote['tva_amount']) > 0)? floatval($quote['tva_amount']) :"Non facturée";
    $tva_rate = (!empty($quote['tva_rate']) && floatval($quote['tva_rate']) > 0)? floatval($quote['tva_rate']) :0;
    $total_ht = (!empty($quote['total_ht']) && floatval($quote['total_ht']) > 0)? floatval($quote['total_ht']) :0;
    $total_ttc = (!empty($quote['total_ttc']) && floatval($quote['total_ttc']) > 0)? floatval($quote['total_ttc']) :0;
    $payment_method = !empty($quote['payment_method'])? $quote['payment_method'] :"N/A";
    
    $userName = !empty($user['name'])? $user['name'] :"N/A";






    // var_dump($company);
    // var_dump($user);
    // var_dump($companyLogo);
    // var_dump($quote);
    // var_dump($items);
    // var_dump($quote);
    // var_dump($userName);
    // die();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture <?= $quoteNumber ?></title>
</head>
<body style="font-family: Arial, sans-serif; font-size: 12px; color: rgb(19, 96, 171); margin: 0; padding: 0; width: 21cm; height: auto;">
    <div style="padding-bottom: 2cm;">
        <div style="background-color: rgb(250, 183, 22); padding: 5px; text-align: center; font-weight: bold; font-size: 16px; color: #000; border-radius: 5px; margin-bottom: 3px;">Devis N° <?= $quoteNumber ?> du <?= $quoteDate ?></div>
        <div style="margin-top: 0;">
            <br>
            <table style="width: 100%; border-collapse: collapse; margin-top: 3px; margin-bottom: 3px;">
                <tr>
                    <td colspan="2" style="text-align: left; padding: 3px;">
                        <img src="<?= base_url() . "/uploads/school_content/admin_logo/" . $company['admin_logo'] ?>" alt="Logo" style="width: 180px; height: 70px;" />
                    </td>
                    <td colspan="4" style="border: none;"></td>
                </tr>
                <tr>
                    <td style="width: 35%; padding: 10px 20px 10px 5px; vertical-align: top;">
                        <span style="color: rgb(19, 96, 171); font-weight: bold;"><?= $companyName ?></span><br>
                        RCCM : <?= $companyRccm ?><br>
                        Téléphone : <?= $companyPhone ?><br>
                        Email : <?= $companyEmail ?><br>
                        Adresse : <?= $companyAddress ?>
                    </td>
                    <td style="width: 10%; border: none;"></td>
                    <td style="width:55 %; padding: 10px 5px 10px 20px; vertical-align: top;">
                        <span style="color: rgb(19, 96, 171); font-weight: bold;">Compte contribuable :</span> <?= $customerComptec ?><br>
                        <span style="color: rgb(19, 96, 171); font-weight: bold;">Client :</span> <?= $customerFullname ?><br>
                        <span style="color: rgb(19, 96, 171); font-weight: bold;">Téléphone :</span> <?= $customerPhone ?><br>
                        <span style="color: rgb(19, 96, 171); font-weight: bold;">Adresse du Client :</span> <?= $customerAddress ?><br>
                        <span style="color: rgb(19, 96, 171); font-weight: bold;">Affaire suivi par:</span> <?= $userName ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="6" style="padding: 10px;">
                        <span style="color: rgb(19, 96, 171); font-weight: bold;">Objet :</span> <?= $quoteDesignation ;?>
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
                        <tr style="background-color: <?= $loop->even ? 'rgba(250, 183, 22, 0.1)' : 'transparent' ?>;">
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
                    <td colspan="3" style="border: none; text-align: right;"><span style="color: rgb(19, 96, 171); font-weight: bold;">Total HT :</span></td>
                    <td style="border: 1px solid #ccc; padding: 5px; font-size: 12px;"><?= number_format($total_ht, 2, ',', ' ') ;?></td>
                </tr>
                <tr>
                    <td colspan="3" style="border: none; text-align: right;"><span style="color: rgb(19, 96, 171); font-weight: bold;">TVA (<?= $tva_rate ;?>%) :</span></td>
                    <td style="border: 1px solid #ccc; padding: 5px; font-size: 12px;"><?= $tva_amount ;?></td>
                </tr>
                <tr>
                    <td colspan="3" style="border: none; text-align: right;"><span style="color: rgb(19, 96, 171); font-weight: bold;">Total TTC :</span></td>
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

    <div style="position: fixed; bottom: 0.3cm; left: 0.3cm; right: 0.3cm; background-color: rgb(250, 183, 22); padding: 3px; font-size: 9px; text-align: center; color: #000; border-top: 1px solid rgb(19, 96, 171);">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 33%; text-align: left; padding: 0 5px;">
                    <?= $companyName ?> | Registre de Commerce: <?= $companyRccm ?>
                </td>
                <td style="width: 33%; text-align: center; padding: 0 5px;">
                    Téléphone: <?= $companyPhone ?> | Email: <?= $companyEmail ?><br>
                    Banque: <?= $companyBank ?> | Numéro de compte bancaire: <?= $companyComptBank ?>
                </td>
                <td style="width: 33%; text-align: right; padding: 0 5px;">
                    Adresse: <?= $companyAddress ?><br>
                    Site : <?= $companyWebsite; ?>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>