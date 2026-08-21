<?php
$dID = 'deliveryDatatable';
// Vérifier les privilèges RBAC
$is_superadmin = $this->rbac->hasPrivilege('superadmin');
$is_admin = $this->rbac->hasPrivilege('admin');
$is_admin_user = ($is_superadmin || $is_admin);
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sora:wght@500;600;700&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

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

    .content-wrapper{ background:var(--pf-paper); font-family: var(--font-body); }

    /* Ne jamais toucher la police des icônes (FontAwesome) */
    .content-wrapper i.fa,
    .content-wrapper i[class*="fa-"] {
        font-family: "FontAwesome" !important;
        font-style: normal;
        font-weight: normal;
        speak: none;
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
        width:220px; height:220px;
        border:1.5px dashed rgba(255,255,255,0.14);
        border-radius:50%;
    }
    .content-header::before{
        content:"";
        position:absolute;
        right:20px; top:20px;
        width:130px; height:130px;
        border:1.5px dashed rgba(255,255,255,0.10);
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

    section.content{ padding-top:6px; }

    /* ---- Card ---- */
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
        padding:16px 20px;
    }
    .box.box-primary > .box-header .box-title{
        font-family:var(--font-display);
        font-weight:600;
        font-size:16px;
        color:var(--pf-ink);
        margin-bottom:10px;
        display:block;
    }
    .box.box-primary .box-body{ padding:0; background:var(--pf-card); }

    /* ---- Toolbar (box-tools) ---- */
    .box-tools{
        display:flex;
        flex-wrap:wrap;
        align-items:center;
        gap:10px;
    }
    .box-tools .form-group{ margin:0 !important; }
    .box-tools select.form-control{
        border:1px solid var(--pf-line);
        border-radius:9px;
        font-size:13px;
        height:36px;
        box-shadow:none;
        min-width:170px;
        color:var(--pf-ink);
        background:#fff;
    }
    .box-tools select.form-control:focus{
        border-color:var(--pf-ink);
        box-shadow:0 0 0 3px var(--pf-amber-soft);
    }

    .badge{
        display:inline-flex;
        align-items:center;
        gap:6px;
        border-radius:999px !important;
        font-weight:600;
        font-size:12px;
        padding:6px 12px !important;
        margin:0 !important;
    }
    .badge.bg-info{ background:var(--pf-blue-soft) !important; color:#1F4A80 !important; }
    .badge.bg-success{ background:var(--pf-green-soft) !important; color:#155F41 !important; }
    .badge small{ font-weight:500; opacity:.8; margin-left:2px; }

    .box-tools .btn{
        height:36px;
        display:inline-flex;
        align-items:center;
        gap:7px;
        padding:0 16px !important;
        border-radius:9px;
        font-size:13px;
        font-weight:600;
        border:1px solid transparent;
        white-space:nowrap;
    }
    .box-tools .btn-primary{ background:var(--pf-ink); border-color:var(--pf-ink); color:#fff; }
    .box-tools .btn-primary:hover{ background:var(--pf-ink-soft); border-color:var(--pf-ink-soft); box-shadow:0 4px 12px rgba(39,55,114,0.25); color:#fff; text-decoration:none; }

    @media (max-width: 991px) {
        .box-tools{ width:100%; margin-top:8px; }
    }


    /* ---- Table ---- */
    table.deliveryDatatable{
        margin:0 !important;
        border-collapse:separate;
        border-spacing:0;
        width:100%;
    }
    table.deliveryDatatable thead th{
        background:var(--pf-paper);
        border-bottom:1px solid var(--pf-line) !important;
        border-top:none !important;
        font-family:var(--font-display);
        font-size:11px;
        text-transform:uppercase;
        letter-spacing:.07em;
        color:var(--pf-muted);
        font-weight:600;
        padding:12px 16px;
    }
    table.deliveryDatatable tbody td{
        padding:13px 16px;
        font-size:13.5px;
        color:var(--pf-ink);
        border-top:1px solid var(--pf-line) !important;
        vertical-align:middle;
    }
    table.deliveryDatatable tbody tr:hover{ background:#FAFBFD; }
    table.deliveryDatatable tbody tr td:first-child{
        font-family:var(--font-mono);
        font-weight:500;
        color:var(--pf-ink-soft);
        letter-spacing:.01em;
    }

    /* status badges — appliquer pf-status-<n> depuis index.js sur la colonne Statut si besoin */
    .pf-status{
        display:inline-flex;
        align-items:center;
        gap:6px;
        padding:3px 10px;
        border-radius:999px;
        font-size:11.5px;
        font-weight:600;
        font-family:var(--font-body);
    }
    .pf-status::before{ content:""; width:6px; height:6px; border-radius:50%; }
    .pf-status-preparing{ background:var(--pf-blue-soft); color:#1F4A80; } .pf-status-preparing::before{ background:var(--pf-blue); }
    .pf-status-partial{ background:var(--pf-amber-soft); color:#8A6415; } .pf-status-partial::before{ background:var(--pf-amber); }
    .pf-status-delivered{ background:var(--pf-green-soft); color:#155F41; } .pf-status-delivered::before{ background:var(--pf-green); }
    .pf-status-cancelled{ background:#EFEFEF; color:#7A7A7A; } .pf-status-cancelled::before{ background:#9A9A9A; }

    @media (max-width: 991px){
        .content-header{ margin:12px; padding:20px !important; }
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-object-group"></i> Bons de livraison
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Horizontal Form -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Bons de livraison</h3>
                        <div class="box-tools pull-right">
                            <div class="form-group btn-sm" style="display: inline-block; margin-right: 10px;">
                                <select id="statusFilter" class="form-control">
                                    <option value="">Tous les statuts</option>
                                    <option value="1">En préparation</option>
                                    <!-- <option value="2">En cours de livraison</option> -->
                                    <option value="6">Partiellement livré</option>
                                    <option value="3">Livré</option>
                                    <option value="4">Annulée</option>
                                </select>
                            </div>

                            <!-- 🔹 Indicateur de filtre utilisateur -->
                            <?php if (!$is_admin_user): ?>
                                <span class="badge bg-info" style="margin-right: 10px; padding: 8px;">
                                    <i class="fa fa-user"></i> Mes livraisons uniquement
                                </span>
                            <?php else: ?>
                                <span class="badge bg-success" style="margin-right: 10px; padding: 8px;">
                                    <i class="fa fa-eye"></i> Vue administrateur (toutes les livraisons)
                                    <?php if ($is_superadmin): ?>
                                        <small>(Super Admin)</small>
                                    <?php elseif ($is_admin): ?>
                                        <small>(Admin)</small>
                                    <?php endif; ?>
                                </span>
                            <?php endif; ?>

                            <?php if ($this->rbac->hasPrivilege('deliveryitem', 'can_add')) { ?>
                                <!-- <a href="<?php echo base_url(); ?>admin/deliveryitem/form" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus"></i> Nouveau bon de livraison
                                </a>-->
                            <?php } ?>
                        </div><!-- /.box-header -->
                        <div class="box-body">
                            <div class="mailbox-messages table-responsive">
                                <table class="table table-striped table-bordered table-hover <?= $dID ?>" data-export-title="<?php echo $this->lang->line('issue_item'); ?>">
                                    <thead>
                                    <tr>
                                        <th class="bold">Référence</th>
                                        <th class="bold">Client</th>
                                        <th class="bold">Date d'édition</th>
                                        <th class="bold">Terme de paiement</th>
                                        <th class="bold">Lieu de livraison client</th>
                                        <th class="bold">Ajouté le</th>
                                        <th class="bold">Montant total</th>
                                        <th class="bold">Statut</th>
                                        <th class="text-right bold"><?php echo $this->lang->line('action'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table><!-- /.table -->
                            </div><!-- /.mail-box-messages -->
                        </div><!-- /.box-body -->
                    </div>
                </div><!--/.col (right) -->
            </div>

    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<!-- SweetAlert2 Joseph -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script src="<?= base_url('assets/js/delivery/index.js') ?>"></script>