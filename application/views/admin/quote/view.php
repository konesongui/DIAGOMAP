<?php
$currency_symbol = 'FCFA'; // Symbole de la devise
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-file-text-o"></i> <?php echo $title; ?></h1>
        <small><?php echo $quote['quote_number']; ?></small>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $page_title; ?></h3>
                    </div>
                    <div class="box-body">
                        <!-- Informations générales -->
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Informations du devis</h4>
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th>Référence</th>
                                        <td><span class="pf-ref"><?php echo $quote['quote_number']; ?></span></td>
                                    </tr>
                                    <tr>
                                        <th>Date d'émission</th>
                                        <td><?php echo date('d/m/Y', strtotime($quote['quote_date'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Date de validité</th>
                                        <td><?php echo date('d/m/Y', strtotime($quote['valid_until'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Objet</th>
                                        <td><?php echo $quote['objet']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Statut</th>
                                        <td>
                                            <?php
                                            $status_labels = [
                                                1 => ['label' => 'En attente de validation', 'class' => 'label-warning'],
                                                2 => ['label' => 'Validée', 'class' => 'label-success'],
                                                3 => ['label' => 'Rejetée', 'class' => 'label-danger'],
                                                4 => ['label' => 'En cours', 'class' => 'label-info'],
                                                5 => ['label' => 'Livrée', 'class' => 'label-success'],
                                                6 => ['label' => 'Annulée', 'class' => 'label-default']
                                            ];
                                            $status = isset($status_labels[$quote['status']]) ? $status_labels[$quote['status']] : ['label' => 'Inconnu', 'class' => 'label-default'];
                                            ?>
                                            <span class="label <?php echo $status['class']; ?>"><?php echo $status['label']; ?></span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4>Informations client</h4>
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th>Client</th>
                                        <td><?php echo $quote['customer_name'] . ' ' . $quote['customer_last_name']; ?></td>
                                    </tr>
                                    <?php if (!empty($quote['customer_email'])) : ?>
                                        <tr>
                                            <th>Email</th>
                                            <td><?php echo $quote['customer_email']; ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if (!empty($quote['customer_phone'])) : ?>
                                        <tr>
                                            <th>Téléphone</th>
                                            <td><?php echo $quote['customer_phone']; ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if (!empty($quote['customer_address'])) : ?>
                                        <tr>
                                            <th>Adresse</th>
                                            <td><?php echo $quote['customer_address']; ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </table>
                            </div>
                        </div>

                        <!-- Conditions de paiement et livraison -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h4>Conditions de paiement</h4>
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th>Termes de paiement</th>
                                        <td><?php echo $quote['payment_terms']; ?></td>
                                    </tr>
                                    <?php if (!empty($quote['payment_method'])): ?>
                                        <tr>
                                            <th>Mode de paiement</th>
                                            <td><?php echo $quote['payment_method']; ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4>Conditions de livraison</h4>
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th>Termes de livraison</th>
                                        <td><?php echo $quote['delivery_terms']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Lieu de livraison</th>
                                        <td><?php echo $quote['delivery_location']; ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Articles du devis avec remises -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h4>Articles</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th>Catégorie</th>
                                            <th>Article</th>
                                            <th>Quantité</th>
                                            <th>Unité</th>
                                            <th>Prix unitaire</th>
                                            <th>Remise</th>
                                            <th>Total HT</th>
                                            <th>Total après remise</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($quote['items'] as $item) :
                                            $discount_display = '';
                                            if ($item['discount'] > 0) {
                                                if ($item['discount_type'] === 'percent') {
                                                    $discount_display = $item['discount'] . '%';
                                                } else {
                                                    $discount_display = number_format($item['discount'], 2, ',', ' ') . ' ' . $currency_symbol;
                                                }
                                            }
                                            ?>
                                            <tr>
                                                <td><?php echo $item['category_name']; ?></td>
                                                <td><?php echo $item['item_name']; ?><?php echo $item['user_name']; ?></td>
                                                <td><?php echo $item['quantity']; ?></td>
                                                <td><?php echo $item['unit']; ?></td>
                                                <td><?php echo number_format($item['unit_price'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                                <td>
                                                    <?php if ($discount_display): ?>
                                                        <span class="label label-warning"><?php echo $discount_display; ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted">Aucune</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo number_format($item['line_total'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                                <td>
                                                    <?php if (isset($item['line_total_after_discount']) && $item['line_total_after_discount'] != $item['line_total']): ?>
                                                        <strong class="text-success"><?php echo number_format($item['line_total_after_discount'], 2, ',', ' ') . ' ' . $currency_symbol; ?></strong>
                                                    <?php else: ?>
                                                        <?php echo number_format($item['line_total'], 2, ',', ' ') . ' ' . $currency_symbol; ?>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Récapitulatif des totaux -->
                        <div class="row mt-4">
                            <div class="col-md-6 col-md-offset-6">
                                <div class="box box-info">
                                    <div class="box-header with-border">
                                        <h3 class="box-title">Récapitulatif des totaux</h3>
                                    </div>
                                    <div class="box-body">
                                        <table class="table">
                                            <tr>
                                                <th>Total HT</th>
                                                <td class="text-right"><?php echo number_format($quote['total_ht'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                            </tr>

                                            <!-- Affichage des remises individuelles -->
                                            <?php if (isset($quote['total_discount']) && $quote['total_discount'] > 0): ?>
                                                <tr>
                                                    <th>Total remises articles</th>
                                                    <td class="text-right text-danger">- <?php echo number_format($quote['total_discount'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                                </tr>
                                            <?php endif; ?>

                                            <!-- Affichage de la remise globale -->
                                            <?php if (isset($quote['global_discount_amount']) && $quote['global_discount_amount'] > 0):
                                                $global_discount_display = '';
                                                if ($quote['global_discount_type'] === 'percent') {
                                                    $global_discount_display = $quote['global_discount_amount'] . '%';
                                                } else {
                                                    $global_discount_display = number_format($quote['global_discount_amount'], 2, ',', ' ') . ' ' . $currency_symbol;
                                                }
                                                ?>
                                                <tr>
                                                    <th>Remise globale <?php echo $global_discount_display; ?></th>
                                                    <td class="text-right text-danger">- <?php echo number_format($quote['global_discount_amount'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                                </tr>
                                            <?php endif; ?>

                                            <!-- Total après remise -->
                                            <?php if (isset($quote['total_after_discount']) && $quote['total_after_discount'] != $quote['total_ht']): ?>
                                                <tr class="subtotal-row">
                                                    <th>Total après remise</th>
                                                    <td class="text-right text-success">
                                                        <strong><?php echo number_format($quote['total_after_discount'], 2, ',', ' ') . ' ' . $currency_symbol; ?></strong>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>

                                            <!-- 🔹 Affichage des taxes selon l'option -->
                                            <?php
                                            $tax_option = isset($quote['tax_option']) ? $quote['tax_option'] : 'none';
                                            if ($tax_option === 'tva'): ?>
                                                <tr>
                                                    <th>TVA (<?php echo isset($quote['tva_rate']) ? $quote['tva_rate'] : 18; ?>%)</th>
                                                    <td class="text-right"><?php echo number_format($quote['tva_amount'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                                </tr>
                                            <?php elseif ($tax_option === 'other' && isset($quote['other_tax_amount']) && $quote['other_tax_amount'] > 0): ?>
                                                <tr>
                                                    <th>
                                                        <?php echo !empty($quote['other_tax_name']) ? htmlspecialchars($quote['other_tax_name']) : 'Autre taxe'; ?>
                                                        (<?php echo isset($quote['other_tax_rate']) ? number_format($quote['other_tax_rate'], 2, ',', ' ') : 0; ?>%)
                                                    </th>
                                                    <td class="text-right"><?php echo number_format($quote['other_tax_amount'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                                </tr>
                                            <?php endif; ?>

                                            <!-- Affichage du montant total de taxe (pour compatibilité) -->
                                            <?php if (isset($quote['total_tax_amount']) && $quote['total_tax_amount'] > 0): ?>
                                                <tr class="tax-total-row">
                                                    <th>Total taxes</th>
                                                    <td class="text-right"><?php echo number_format($quote['total_tax_amount'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                                </tr>
                                            <?php endif; ?>

                                            <tr class="total-row">
                                                <th>Total
                                                    <?php
                                                    if ($tax_option === 'tva') {
                                                        echo 'TTC';
                                                    } elseif ($tax_option === 'other') {
                                                        echo !empty($quote['other_tax_name']) ? htmlspecialchars($quote['other_tax_name']) . ' inclus' : 'TTC';
                                                    } else {
                                                        echo 'HT';
                                                    }
                                                    ?>
                                                </th>
                                                <td class="text-right">
                                                    <strong class="total-amount"><?php echo number_format($quote['total_ttc'], 2, ',', ' ') . ' ' . $currency_symbol; ?></strong>
                                                </td>
                                            </tr>

                                            <!-- Économie totale -->
                                            <?php
                                            $total_savings = 0;
                                            if (isset($quote['total_discount'])) {
                                                $total_savings += $quote['total_discount'];
                                            }
                                            if (isset($quote['global_discount_amount'])) {
                                                $total_savings += $quote['global_discount_amount'];
                                            }
                                            if ($total_savings > 0):
                                                ?>
                                                <tr class="savings-row">
                                                    <th>Économie totale</th>
                                                    <td class="text-right text-success">
                                                        <strong>+ <?php echo number_format($total_savings, 2, ',', ' ') . ' ' . $currency_symbol; ?></strong>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes et informations supplémentaires -->
                        <?php if (!empty($quote['notes'])) : ?>
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h4>Notes</h4>
                                    <div class="well">
                                        <?php echo nl2br($quote['notes']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="box-footer">
                        <a href="<?php echo base_url('admin/quoteitem'); ?>" class="btn btn-default">
                            <i class="fa fa-arrow-left"></i> Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    :root{
        --pf-ink:#273772;
        --pf-ink-soft:#334391;
        --pf-paper:#F6F7FA;
        --pf-card:#FFFFFF;
        --pf-line:#E3E6ED;
        --pf-green:#1F7A54;
        --pf-green-soft:#E1F3EA;
        --pf-red:#B23A32;
        --pf-red-soft:#FBE7E5;
        --pf-blue:#2D5FA6;
        --pf-blue-soft:#E5EDF9;
        --pf-amber:#B8892B;
        --pf-amber-soft:#FBF3E1;
        --pf-muted:#6B7793;
        --font-display:'Sora', sans-serif;
        --font-body:'Inter', sans-serif;
        --font-mono:'JetBrains Mono', monospace;
    }

    .content-wrapper{ background:var(--pf-paper); font-family:var(--font-body); }
    .content-wrapper i.fa,
    .content-wrapper i[class*="fa-"]{
        font-family:"FontAwesome" !important;
        font-style:normal;
        font-weight:normal;
    }

    /* ---- Header banner ---- */
    .content-header{
        position:relative;
        overflow:hidden;
        background:linear-gradient(135deg, var(--pf-ink) 0%, var(--pf-ink-soft) 100%);
        border-radius:14px;
        padding:26px 30px !important;
        margin:18px 18px 16px 18px;
        color:#fff;
    }
    .content-header::after{
        content:"";
        position:absolute;
        right:-40px; top:-40px;
        width:200px; height:200px;
        border:1.5px dashed rgba(255,255,255,0.14);
        border-radius:50%;
    }
    .content-header h1{
        font-family:var(--font-display);
        font-weight:700;
        font-size:24px;
        margin:0;
        letter-spacing:-0.01em;
        display:flex;
        align-items:center;
        gap:12px;
    }
    .content-header h1 i{
        width:38px; height:38px;
        border-radius:10px;
        background:rgba(255,255,255,0.10);
        border:1px solid rgba(255,255,255,0.16);
        display:inline-flex;
        align-items:center;
        justify-content:center;
        font-size:16px;
        color:#EAD9A8;
    }
    .content-header small{
        display:block;
        margin:6px 0 0 50px;
        font-size:13px;
        font-weight:400;
        font-family:var(--font-mono);
        color:rgba(255,255,255,0.65);
        letter-spacing:.03em;
    }

    section.content{ padding:0 18px 18px 18px; }

    /* ---- Cards ---- */
    .box.box-primary{
        border-top:none;
        border:1px solid var(--pf-line);
        border-radius:14px;
        box-shadow:0 1px 2px rgba(22,35,63,0.04);
        overflow:hidden;
    }
    .box.box-primary > .box-header.with-border{
        background:var(--pf-card);
        border-bottom:1px solid var(--pf-line);
        padding:18px 22px;
    }
    .box.box-primary > .box-header .box-title{
        font-family:var(--font-display);
        font-weight:600;
        font-size:17px;
        color:var(--pf-ink);
    }
    .box.box-primary > .box-body{ padding:22px; background:var(--pf-card); }
    .box.box-primary > .box-footer{
        background:var(--pf-paper);
        border-top:1px solid var(--pf-line);
        padding:18px 22px;
    }
    .box-footer .btn-default{
        border:1px solid var(--pf-line);
        background:#fff;
        color:var(--pf-ink);
        border-radius:9px;
        font-weight:600;
        padding:9px 18px;
    }
    .box-footer .btn-default:hover{ border-color:var(--pf-ink); color:var(--pf-ink); background:var(--pf-paper); }

    h4{
        font-family:var(--font-display);
        font-weight:600;
        font-size:14px;
        color:var(--pf-ink);
        display:flex;
        align-items:center;
        gap:8px;
        margin:4px 0 12px 0;
    }
    h4::before{
        content:"";
        width:4px; height:14px;
        border-radius:2px;
        background:var(--pf-ink);
        display:inline-block;
    }

    .mt-4{ margin-top:24px; }

    /* ---- Tables ---- */
    .table{ font-size:13.5px; }
    .table th{
        background:var(--pf-paper) !important;
        color:var(--pf-muted);
        font-weight:600;
        font-size:11.5px;
        text-transform:uppercase;
        letter-spacing:.05em;
        border-color:var(--pf-line) !important;
        vertical-align:middle;
    }
    .table td{
        border-color:var(--pf-line) !important;
        color:var(--pf-ink);
        vertical-align:middle;
    }
    .table-bordered{
        border-radius:10px;
        overflow:hidden;
        border-color:var(--pf-line);
    }
    .table-striped > tbody > tr:nth-of-type(odd){ background:#FBFCFE; }

    .pf-ref{
        font-family:var(--font-mono);
        font-weight:600;
        color:var(--pf-ink);
        letter-spacing:.02em;
    }

    .label{
        font-family:var(--font-body);
        border-radius:999px;
        padding:.3em .8em;
        font-weight:600;
        font-size:11.5px;
        letter-spacing:.02em;
    }
    .label-warning{ background-color:var(--pf-amber-soft); color:#8A6415; }
    .label-success{ background-color:var(--pf-green-soft); color:#155F41; }
    .label-danger{ background-color:var(--pf-red-soft); color:#8E2C25; }
    .label-info{ background-color:var(--pf-blue-soft); color:#1F4A80; }
    .label-default{ background-color:#EDEEF2; color:#4A5372; }

    .text-muted{ color:var(--pf-muted); }
    .text-danger{ color:var(--pf-red); }
    .text-success{ color:var(--pf-green); }

    .well{
        background-color:var(--pf-paper);
        border:1px solid var(--pf-line);
        border-radius:10px;
        padding:16px 18px;
        color:var(--pf-ink);
        line-height:1.6;
    }

    /* ---- Totals summary card ---- */
    .box-info{
        border-top:none;
        border:1px solid var(--pf-line);
        border-radius:14px;
        overflow:hidden;
        box-shadow:0 1px 2px rgba(22,35,63,0.04);
    }
    .box-info > .box-header.with-border{
        background:var(--pf-ink);
        border-bottom:none;
        padding:14px 20px;
    }
    .box-info > .box-header .box-title{
        font-family:var(--font-display);
        font-weight:600;
        font-size:14px;
        color:#fff;
    }
    .box-info > .box-body{ background:var(--pf-card); padding:14px 20px; }
    .box-info .table{ margin-bottom:0; }
    .box-info .table th,
    .box-info .table td{
        border-color:var(--pf-line) !important;
        text-transform:none;
        letter-spacing:0;
        font-size:13.5px;
    }
    .box-info .table th{ background:transparent !important; color:var(--pf-muted); font-weight:500; }

    .subtotal-row{
        border-top:1px solid var(--pf-line);
        border-bottom:1px solid var(--pf-line);
    }
    .subtotal-row td{ font-weight:700; }

    .tax-total-row{ border-top:1px solid var(--pf-line); }

    .savings-row{ background-color:var(--pf-green-soft) !important; }
    .savings-row td{ color:var(--pf-green); font-weight:700; }
    .savings-row th{ color:var(--pf-green) !important; font-weight:600; }

    .total-row{
        font-weight:700;
        border-top:2px solid var(--pf-ink);
        background:#EEF0FA;
    }
    .total-row th{
        color:var(--pf-ink) !important;
        font-size:13.5px;
        text-transform:none;
        letter-spacing:0;
    }
    .total-row td{ font-size:1.1em; }
    .total-amount{
        font-family:var(--font-mono);
        font-size:1.2em;
        color:var(--pf-ink);
    }
</style>