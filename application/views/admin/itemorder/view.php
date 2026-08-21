<?php
$currency_symbol = 'FCA'; // Symbole de la devise
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-file-text-o"></i> <?php echo $title; ?></h1>
        <small><?php echo $order['order_number']; ?></small>
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
                                <h4>Informations de la commande</h4>
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th>Référence</th>
                                        <td><span class="pf-ref"><?php echo $order['order_number']; ?></span></td>
                                    </tr>
                                    <tr>
                                        <th>Date d'émission</th>
                                        <td><?php echo date('d/m/Y', strtotime($order['order_date'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Date de validité</th>
                                        <td><?php echo date('d/m/Y', strtotime($order['valid_until'])); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Objet</th>
                                        <td><?php echo $order['objet']; ?></td>
                                    </tr>

                                    <tr>
                                        <th>Statut</th>
                                        <td>
                                            <?php
                                            $status_labels = [
                                                1 => ['label' => 'En attente de validation', 'class' => 'label-warning'],
                                                2 => ['label' => 'Validée', 'class' => 'label-success'],
                                                3 => ['label' => 'Rejetée', 'class' => 'label-danger'],
                                                4 => ['label' => 'Validée', 'class' => 'label-info'],
                                                5 => ['label' => 'Livrée', 'class' => 'label-success'],
                                                6 => ['label' => 'Annulée', 'class' => 'label-default']
                                            ];
                                            $status = isset($status_labels[$order['status']]) ? $status_labels[$order['status']] : ['label' => 'Inconnu', 'class' => 'label-default'];
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
                                        <td><?php echo $order['customer_name'] . ' ' . $order['customer_last_name']; ?></td>
                                    </tr>
                                    <?php if (!empty($order['customer_email'])) : ?>
                                        <tr>
                                            <th>Email</th>
                                            <td><?php echo $order['customer_email']; ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if (!empty($order['customer_phone'])) : ?>
                                        <tr>
                                            <th>Téléphone</th>
                                            <td><?php echo $order['customer_phone']; ?></td>
                                        </tr>
                                    <?php endif; ?>
                                    <?php if (!empty($order['customer_address'])) : ?>
                                        <tr>
                                            <th>Adresse</th>
                                            <td><?php echo $order['customer_address']; ?></td>
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
                                        <td><?php echo $order['payment_terms']; ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4>Conditions de livraison</h4>
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <th>Termes de livraison</th>
                                        <td><?php echo $order['delivery_terms']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Lieu de livraison</th>
                                        <td><?php echo $order['delivery_location']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Mode de paiement</th>
                                        <td><?php echo $order['payment_method']; ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Articles de la commande -->
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
                                            <th>Total</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($order['items'] as $item): ?>
                                            <tr>
                                                <td><?= $item['category_name'] ?></td>
                                                <td><?= $item['item_name'] ?></td>
                                                <td><?= $item['quantity'] ?></td>
                                                <td><?= $item['unit'] ?></td>
                                                <td><?= number_format($item['unit_price'], 2, ',', ' ') ?></td>
                                                <td><?= number_format($item['line_total'], 2, ',', ' ') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <th colspan="5" class="text-right">Total HT</th>
                                            <td><?php echo number_format($order['total_ht'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                        </tr>
                                        <?php if ($order['apply_tva']) : ?>
                                            <tr>
                                                <th colspan="5" class="text-right">TVA (<?php echo $order['tva_rate']; ?>%)</th>
                                                <td><?php echo number_format($order['tva_amount'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                            </tr>
                                            <tr class="total-row">
                                                <th colspan="5" class="text-right">Total TTC</th>
                                                <td><?php echo number_format($order['total_ttc'], 2, ',', ' ') . ' ' . $currency_symbol; ?></td>
                                            </tr>
                                        <?php endif; ?>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Notes et informations supplémentaires -->
                        <?php if (!empty($order['notes'])) : ?>
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h4>Notes</h4>
                                    <div class="well">
                                        <?php echo nl2br($order['notes']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="box-footer">
                        <a href="<?php echo base_url('admin/orderformitem'); ?>" class="btn btn-default">
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
    .box-tools .btn{ margin-left:5px; }

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

    .mt-4{ margin-top:20px; }

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

    /* ---- Articles table footer (totaux) ---- */
    tfoot th{
        text-transform:none !important;
        letter-spacing:0 !important;
        font-size:13.5px !important;
        color:var(--pf-ink) !important;
        font-weight:600;
        background:var(--pf-card) !important;
    }
    tfoot td{
        font-family:var(--font-mono);
        font-weight:600;
        color:var(--pf-ink);
    }
    tfoot tr.total-row th,
    tfoot tr.total-row td{
        background:var(--pf-ink) !important;
        color:#fff !important;
        font-size:1.05em;
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

    .well{
        background-color:var(--pf-paper);
        border:1px solid var(--pf-line);
        border-radius:10px;
        padding:16px 18px;
        color:var(--pf-ink);
        line-height:1.6;
    }
</style>