<?php

    $companyName = $company['name'];
    $companyComptBank = $company['compt_bank'];
    $companyRccm = $company['rccm'];
    $companyAddress = $company['address'];
    $companyPhone = $company['phone'];
    $companyEmail = $company['email'];
    $companyWebsite = $company['website'];
    $companyLogo = $company['app_logo'];
    $companyLogo = base_url('assets/images/logo.png');
    $companyBank = $company['bank'];

    // var_dump($company);
    // var_dump($companyLogo);
    // var_dump($invoice);
    // die();
?>

<!-- <!DOCTYPE html>
<html>
<head>
    <title>Facture Proforma</title>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #000;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid rgb(250, 183, 22) ;
            padding: 20px;
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            border-bottom: 1px solid rgb(19, 96, 171);
            padding-bottom: 10px;
        }
        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .invoice-number {
            text-align: right;
        }
        .invoice-info {
            margin-bottom: 20px;
        }
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .invoice-table th {
            text-align: left;
            border-bottom: 1px solid #ddd;
            padding: 8px;
            background-color: rgb(250, 183, 22);
        }
        .invoice-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .invoice-total {
            text-align: right;
            margin-top: 20px;
            font-weight: bold;
        }
        .invoice-footer {
            margin-top: 30px;
            font-size: 12px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        @media print {
            body { margin: 0; }
            @page { margin: 2cm; }
            .invoice-container {
                border: none;
                padding: 0;
            }
        }
    </style>
</head>
<body> -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Proforma SICAB</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        .head { background-color: rgb(250, 183, 22); padding: 10px; text-align: center; font-weight: bold; font-size: 18px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 5px; text-align: center; }
        .no-border { border: none; }
        .logo { text-align: left; }
    </style>
</head>
<body>

<div class="head">FACTURE PROFORMA N°190525_02 / CM</div>

<table>
    <tr>
        <td colspan="2" class="logo">
            <img src="logo.png" alt="Logo" width="180" height="70">
        </td>
        <td colspan="4" class="no-border"></td>
    </tr>
    <tr>
        <td colspan="6">
            <strong>SICAB</strong><br>
            RCCM : [À renseigner]<br>
            Téléphone : [À renseigner]
        </td>
    </tr>
    <tr>
        <td colspan="6">
            <strong>Date :</strong> 19 mai 2025<br>
            <strong>Client :</strong> Client CC<br>
            <strong>Objet :</strong> Projet de câblage réseau Avec WiFi
        </td>
    </tr>
</table>

<table>
    <thead style="background-color:#ccd1cc;">
        <tr>
            <th>Qté</th>
            <th>Description</th>
            <th>PU (CFA)</th>
            <th>Total (CFA)</th>
        </tr>
    </thead>
    <tbody>
        <tr><td>700</td><td>Grandstream GRP2602P - Téléphone IP</td><td>700</td><td>490 000</td></tr>
        <tr><td>20</td><td>Fanvil X6U - Poste opérateur</td><td>5 000</td><td>100 000</td></tr>
        <tr><td>24</td><td>Boitier mosaïque 2 modules</td><td>1 500</td><td>36 000</td></tr>
        <tr><td>20</td><td>Prises RJ45 Cat 6</td><td>2 850</td><td>57 000</td></tr>
        <tr><td>20</td><td>Cordon de descente 3m</td><td>4 800</td><td>96 000</td></tr>
        <tr><td>1</td><td>IPBX Yeastar Grandstream UCM6302A</td><td>205 000</td><td>205 000</td></tr>
        <tr><td>1</td><td>Parasurtenseur</td><td>250 000</td><td>250 000</td></tr>
        <tr><td>1</td><td>Onduleur DS-UPS2000</td><td>22 500</td><td>22 500</td></tr>
        <tr><td>1</td><td>Switch DS-3E1326P-EI</td><td>210 000</td><td>210 000</td></tr>
        <tr><td>50</td><td>Prises</td><td>1 500</td><td>75 000</td></tr>
        <tr><td>14</td><td>Accessoire de raccordement</td><td>3 000</td><td>42 000</td></tr>
        <tr><td>6</td><td>Boitier réseau</td><td>4 500</td><td>27 000</td></tr>
        <tr><td>1</td><td>Moulure 40*16</td><td>250 000</td><td>250 000</td></tr>
        <tr><td>1</td><td>Moulure 75*50</td><td>350 000</td><td>350 000</td></tr>
        <tr><td>15</td><td>Main d'œuvre</td><td>60 000</td><td>900 000</td></tr>
        <tr><td>3</td><td>Coffret informatique 9U</td><td>150 000</td><td>450 000</td></tr>
        <tr><td>1</td><td>Unifi lite wifi point d'accès</td><td>75 000</td><td>75 000</td></tr>
        <tr><td>1</td><td>Grandstream DP722</td><td>70 000</td><td>70 000</td></tr>
        <tr><td>1</td><td>Cordon de brassage 1m</td><td>50 000</td><td>50 000</td></tr>
        <tr><td>1</td><td>Goulotte 105*50</td><td>100 000</td><td>100 000</td></tr>
        <tr><td>4</td><td>Câble informatique Cat6 FTP</td><td>125 000</td><td>500 000</td></tr>
        <tr><td>1</td><td>Panneau de brassage</td><td>100 000</td><td>100 000</td></tr>
        <tr><td>1</td><td>Bandeau électrique 8*2P+T 16A</td><td>415 000</td><td>415 000</td></tr>
    </tbody>
</table>

<table>
    <tr>
        <td colspan="3" class="no-border" align="right"><strong>Total HT :</strong></td>
        <td>4 620 500 CFA</td>
    </tr>
    <tr>
        <td colspan="3" class="no-border" align="right"><strong>TVA (18%) :</strong></td>
        <td>Non facturée</td>
    </tr>
    <tr>
        <td colspan="3" class="no-border" align="right"><strong>Total TTC :</strong></td>
        <td>4 620 500 CFA</td>
    </tr>
</table>

<p><strong>Montant en lettres :</strong> quatre millions six cent vingt mille cinq cents francs CFA</p>
<p><strong>Mode de paiement :</strong> Chèque - Espèce - Virement</p>
<p><strong>Garantie :</strong> [à spécifier]</p>
<p><strong>Règlement :</strong> Payable 30 jours dépôt de facture</p>
<p><strong>Centre des impôts :</strong> II Plateaux 1</p>
<p><strong>Affaire suivie par :</strong> Christ GNIZAKO</p>

</body>
</html>


    <!-- <div class="invoice-container">
        <div class="head" style="background-color: rgb(250, 183, 22); padding: 10px; text-align: center; font-weight: bold; font-size: 18px;"></div>
        <div class="invoice-header-logo">
            <img src="<?= base_url('assets/images/logo.png') ?>" alt="Logo" style="width: 100px; height: 100px;">
        </div>
        <div class="invoice-header">
            <div>
                <div class="invoice-title">Facture Proforma</div>
                <div>Projet de cablâge réseau Avec WiFi</div>
                <div>Affaire suivi par: Christ GNIZAKO</div>
            </div>
            <div class="invoice-number">
                <div>N°190525_02 / CM</div>
                <div>Abidjan le</div>
            </div>
        </div>

        <div class="invoice-info">
            <div><strong>Regime:</strong> I. M</div>
            <div><strong>Date:</strong> 19 mai 2025</div>
            <div><strong>Client:</strong> SICAB</div>
            <div><strong>Disponibilité:</strong> </div>
            <div><strong>Centre des impots:</strong> II Plateaux 1</div>
            <div><strong>Offre valable 30 jours</strong></div>
        </div>

        <table class="invoice-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Qté</th>
                    <th>Prix unitaire</th>
                    <th>TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Bandeau électrique 8*2P+T 16A</td>
                    <td>700</td>
                    <td>700</td>
                    <td>490 000</td>
                </tr>
                <tr>
                    <td>Prises RJ45 Cat 6</td>
                    <td>20</td>
                    <td>5 000</td>
                    <td>100 000</td>
                </tr>
                <tr>
                    <td>Cable informatique Cat6 FTP</td>
                    <td>24</td>
                    <td>1 500</td>
                    <td>36 000</td>
                </tr>
                <tr>
                    <td>Cordon de descente 3m</td>
                    <td>20</td>
                    <td>2 850</td>
                    <td>57 000</td>
                </tr>
                <tr>
                    <td>Cablâge sans WiFi</td>
                    <td>20</td>
                    <td>4 800</td>
                    <td>96 000</td>
                </tr>
                <tr>
                    <td>Boitier mosaique 2modules</td>
                    <td>1</td>
                    <td>205 000</td>
                    <td>205 000</td>
                </tr>
                <tr>
                    <td>Panneau de brassage Cat6 FTP</td>
                    <td>1</td>
                    <td>250 000</td>
                    <td></td>
                </tr>
                <tr>
                    <td>DS-3E1326P-EI(O-STD) Switch PoE Smart</td>
                    <td>1</td>
                    <td>22 500</td>
                    <td>22 500</td>
                </tr>
                <tr>
                    <td>Parasurtenseur 2p+t 16A</td>
                    <td>1</td>
                    <td>210 000</td>
                    <td>210 000</td>
                </tr>
                <tr>
                    <td>DS-UPS2000(O-STD)/EU - Onduleur - Regulateur de tension</td>
                    <td>50</td>
                    <td>1 500</td>
                    <td>75 000</td>
                </tr>
                <tr>
                    <td>Moulure 40*16</td>
                    <td>14</td>
                    <td>3 000</td>
                    <td>42 000</td>
                </tr>
                <tr>
                    <td>Moulure 75*50</td>
                    <td>6</td>
                    <td>4 500</td>
                    <td>27 000</td>
                </tr>
                <tr>
                    <td>Goulotte 105*50</td>
                    <td>1</td>
                    <td>250 000</td>
                    <td>250 000</td>
                </tr>
                <tr>
                    <td>Coffret informatique 9u</td>
                    <td>1</td>
                    <td>350 000</td>
                    <td>350 000</td>
                </tr>
                <tr>
                    <td>Cordon de brassage 1m</td>
                    <td>15</td>
                    <td>60 000</td>
                    <td>900 000</td>
                </tr>
                <tr>
                    <td>Configuration autocom et téléphone</td>
                    <td>3</td>
                    <td>150 000</td>
                    <td>450 000</td>
                </tr>
                <tr>
                    <td>ACCESSOIRE DE RACCORDEMENT</td>
                    <td>1</td>
                    <td>75 000</td>
                    <td>75 000</td>
                </tr>
                <tr>
                    <td>MAIN D'ŒUVRE</td>
                    <td>1</td>
                    <td>70 000</td>
                    <td>70 000</td>
                </tr>
                <tr>
                    <td>IPBX Yeastar Grandstream UCM6302A - VoIP Ipbx</td>
                    <td>1</td>
                    <td>50 000</td>
                    <td>50 000</td>
                </tr>
                <tr>
                    <td>Grandstream GRP2602P - Téléphone IP</td>
                    <td>1</td>
                    <td>100 000</td>
                    <td>100 000</td>
                </tr>
                <tr>
                    <td>téléphone fixe Fanvil X6U poste opérteur</td>
                    <td>4</td>
                    <td>125 000</td>
                    <td>500 000</td>
                </tr>
                <tr>
                    <td>FANVIL V62G</td>
                    <td>1</td>
                    <td>100 000</td>
                    <td>100 000</td>
                </tr>
                <tr>
                    <td>Grandstream DP722</td>
                    <td>1</td>
                    <td>415 000</td>
                    <td>415 000</td>
                </tr>
                <tr>
                    <td>Unifi lite wifi poin d'accès</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <div class="invoice-total">
            <div>TOTAL Hors Taxe: 4 620 500 CFA</div>
            <div>TVA (18%): Non facturée</div>
            <div>TOTAL TTC: 4 620 500 CFA</div>
        </div>

        <div class="text-center" style="margin: 20px 0;">
            quatre millions six cent vingt mille cinq cents
        </div>

        <div class="invoice-footer">
            <div><strong>Garantie:</strong> </div>
            <div><strong>Mode de payement:</strong> Chèque - Espèce - Virement</div>
            <div><strong>Règlement:</strong> Payable 30 jours dépôt de facture</div>
            <div>Les dates de disponibilité ainsi que les prix ci-dessus sont donnés à titre indicatif et peuvent être modifiés sans préavis.</div>
            <div style="margin-top: 10px;"><strong>Arrèté la présente Facture Proforma à la somme de:</strong></div>
            <div><strong>Termes et Conditions:</strong> </div>
        </div>
    </div> -->
<!-- </body>
</html> -->