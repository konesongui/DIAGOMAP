<?php
$dID = 'quoteDatatable';
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
        --pf-ink: #273772;
        --pf-ink-soft: #273772;
        --pf-paper:#F6F7FA;
        --pf-card:#FFFFFF;
        --pf-line:#E3E6ED;
        --pf-gold: #273772;
        --pf-gold-soft:#F3E7CE;
        --pf-green:#1F7A54;
        --pf-green-soft:#E1F3EA;
        --pf-red:#B23A32;
        --pf-red-soft:#FBE7E5;
        --pf-blue:#2D5FA6;
        --pf-blue-soft:#E5EDF9;
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
    .pf-header{
        width: 94%;
        left: 15px;
        position:relative;
        overflow:hidden;
        background:linear-gradient(135deg, var(--pf-ink) 0%, var(--pf-ink-soft) 100%);
        border-radius:14px;
        padding:28px 32px;
        margin:18px 18px 0 18px;
        color:#fff;
    }
    .pf-header::after{
        content:"";
        position:absolute;
        right:-40px; top:-40px;
        width:220px; height:220px;
        border:1.5px dashed rgba(255,255,255,0.14);
        border-radius:50%;
    }
    .pf-header::before{
        content:"";
        position:absolute;
        right:20px; top:20px;
        width:130px; height:130px;
        border:1.5px dashed rgba(255,255,255,0.10);
        border-radius:50%;
    }
    .pf-eyebrow{
        font-family:var(--font-mono);
        font-size:11px;
        letter-spacing:.14em;
        text-transform:uppercase;
        color:var(--pf-gold-soft);
        opacity:.85;
        margin:0 0 6px 0;
    }
    .pf-header h1{
        font-family:var(--font-display);
        font-weight:700;
        font-size:26px;
        margin:0;
        letter-spacing:-0.01em;
        display:flex;
        align-items:center;
        gap:12px;
    }
    .pf-header h1 .pf-icon{
        width:40px; height:40px;
        border-radius:10px;
        background:rgba(255,255,255,0.10);
        border:1px solid rgba(255,255,255,0.16);
        display:inline-flex;
        align-items:center;
        justify-content:center;
        font-size:17px;
        color:var(--pf-gold-soft);
    }
    .pf-header p.pf-sub{
        margin:8px 0 0 52px;
        font-size:13px;
        color:rgba(255,255,255,0.65);
        font-family:var(--font-body);
    }

    section.content{ padding-top:18px; }

    /* ---- Toolbar card ---- */
    .pf-toolbar{
        margin-left: 21px;
        width: 96%;
        background:var(--pf-card);
        border:1px solid var(--pf-line);
        border-radius:14px;
        padding:16px 18px;
        margin-bottom:16px;
        display:flex;
        flex-wrap:wrap;
        align-items:center;
        gap:10px;
        box-shadow:0 1px 2px rgba(22,35,63,0.03);
    }
    .pf-toolbar-title{
        font-family:var(--font-display);
        font-weight:600;
        font-size:13px;
        color:var(--pf-ink);
        margin-right:4px;
        white-space:nowrap;
    }
    .pf-toolbar .pf-field{
        display:flex;
        flex-direction:column;
        gap:4px;
    }
    .pf-toolbar label.pf-label{
        font-size:10.5px;
        text-transform:uppercase;
        letter-spacing:.08em;
        color:var(--pf-muted);
        font-weight:600;
        margin:0;
    }
    .pf-toolbar select.form-control,
    .pf-toolbar .form-control{
        border:1px solid var(--pf-line);
        border-radius:9px;
        font-size:13px;
        height:36px;
        box-shadow:none;
        min-width:170px;
        color:var(--pf-ink);
        background:#fff;
    }
    .pf-toolbar select.form-control:focus{
        border-color:var(--pf-gold);
        box-shadow:0 0 0 3px var(--pf-gold-soft);
    }
    .pf-spacer{ flex:1 1 auto; }

    .pf-btn{
        height:36px;
        display:inline-flex;
        align-items:center;
        gap:7px;
        padding:0 16px;
        border-radius:9px;
        font-size:13px;
        font-weight:600;
        border:1px solid transparent;
        cursor:pointer;
        transition:transform .12s ease, box-shadow .12s ease, background .15s ease;
        white-space:nowrap;
    }
    .pf-btn:active{ transform:translateY(1px); }
    .pf-btn-ghost{
        background:#fff;
        border-color:var(--pf-line);
        color:var(--pf-ink);
    }
    .pf-btn-ghost:hover{ border-color:var(--pf-blue); color:var(--pf-blue); }
    .pf-btn-primary{
        background:var(--pf-ink);
        color:#fff;
    }
    .pf-btn-primary:hover{ background:var(--pf-ink-soft); box-shadow:0 4px 12px rgba(22,35,63,0.25); color:#fff; text-decoration:none; }
    .pf-btn-gold{
        background:var(--pf-gold);
        color:#fff;
    }
    .pf-btn-gold:hover{ background: #273772; box-shadow:0 4px 12px rgb(39, 55, 114); color:#fff; }

    /* ---- Table card ---- */
    .pf-table-card{
        background:var(--pf-card);
        border:1px solid var(--pf-line);
        border-radius:14px;
        overflow:hidden;
        box-shadow:0 1px 2px rgba(22,35,63,0.03);
        margin:0 18px 18px 18px;
    }
    .pf-table-card .box-body{ padding:0; }

    table.quoteDatatable{
        margin:0 !important;
        border-collapse:separate;
        border-spacing:0;
        width:100%;
    }
    table.quoteDatatable thead th{
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
    table.quoteDatatable tbody td{
        padding:13px 16px;
        font-size:13.5px;
        color:var(--pf-ink);
        border-top:1px solid var(--pf-line) !important;
        vertical-align:middle;
    }
    table.quoteDatatable tbody tr:hover{ background:#FAFBFD; }
    table.quoteDatatable tbody tr td:first-child{
        font-family:var(--font-mono);
        font-weight:500;
        color:var(--pf-ink-soft);
        letter-spacing:.01em;
    }

    /* status badges — apply class pf-status-<n> from JS/render callback if desired */
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
    .pf-status-pending{ background:var(--pf-gold-soft); color:#8A6415; } .pf-status-pending::before{ background:#B8892B; }
    .pf-status-validated{ background:var(--pf-green-soft); color:#155F41; } .pf-status-validated::before{ background:var(--pf-green); }
    .pf-status-rejected{ background:var(--pf-red-soft); color:#8E2C25; } .pf-status-rejected::before{ background:var(--pf-red); }
    .pf-status-processing{ background:var(--pf-blue-soft); color:#1F4A80; } .pf-status-processing::before{ background:var(--pf-blue); }
    .pf-status-delivered{ background:#E9E9EF; color:#3B3F51; } .pf-status-delivered::before{ background:#6B7793; }
    .pf-status-cancelled{ background:#EFEFEF; color:#7A7A7A; } .pf-status-cancelled::before{ background:#9A9A9A; }

    @media (max-width: 991px){
        .pf-header{ margin:12px; padding:22px 20px; }
        .pf-toolbar{ margin:12px; }
        .pf-table-card{ margin:0 12px 12px 12px; }
        .pf-toolbar select.form-control{ min-width:140px; }
        .pf-spacer{ display:none; }
    }
</style>

<!-- Content Wrapper -->
<div class="content-wrapper">

    <!-- Header banner -->
    <div class="pf-header">

        <h1><span class="pf-icon"><i class="fa fa-object-group"></i></span> Proforma</h1>
        <!--<p class="pf-sub">Gestion commerciale</p>-->
    </div>

    <section class="content">

        <!-- Toolbar -->
        <div class="pf-toolbar">
            <span class="pf-toolbar-title"><i class="fa fa-sliders"></i>&nbsp; Filtres</span>

            <div class="pf-field">
                <label class="pf-label" for="statusFilter">Statut</label>
                <select id="statusFilter" class="form-control">
                    <option value="">Tous les statuts</option>
                    <option value="1">En attente de validation</option>
                    <option value="2">Validé</option>
                    <option value="3">Rejeté</option>
                    <option value="4">En cours de traitement</option>
                    <option value="5">Livré</option>
                    <option value="6">Annulé</option>
                </select>
            </div>

            <?php if ($is_admin_user): ?>
                <div class="pf-field">
                    <label class="pf-label" for="adminFilterSelect">Portée</label>
                    <select id="adminFilterSelect" class="form-control">
                        <option value="my">📄 Mes proformas</option>
                        <option value="all">👥 Tous les proformas</option>
                    </select>
                </div>
            <?php endif; ?>

            <!--<div class="pf-field">
                <label class="pf-label" for="customerSelect">Client</label>
                <select id="customerSelect" class="form-control">
                    <option value="">-- Sélectionner un client --</option>
                    <?php
            if (isset($clients)) {
                foreach ($clients as $client) {
                    $name = trim($client['item_supplier'] . ' ' . ($client['lastname'] ?? ''));
                    echo '<option value="' . $client['id'] . '">' . html_escape($name) . '</option>';
                }
            }
            ?>
                </select>
            </div>-->

            <div class="pf-spacer"></div>

            <!-- <button id="printAllCustomerQuotes" class="pf-btn pf-btn-ghost">
                 <i class="fa fa-print"></i> Imprimer les proformas du client
             </button>-->

            <?php if ($this->rbac->hasPrivilege('devis', 'can_add')) { ?>
                <a href="<?php echo site_url('admin/proforma/form') ?>" class="pf-btn pf-btn-gold">
                    <i class="fa fa-plus"></i> Ajouter un proforma
                </a>
            <?php } ?>
        </div>

        <!-- Table -->
        <div class="pf-table-card">
            <div class="box-body">
                <div class="mailbox-messages table-responsive">
                    <table class="table table-striped table-bordered table-hover <?= $dID ?>" data-export-title="Liste des devis">
                        <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Client</th>
                            <th>Dates</th>
                            <th style="width: 207px;">Montant total</th>
                            <th>Suivi par</th>
                            <th class="text-right">Actions</th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </section>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

<script>
    // Variable globale pour le mode de filtre
    var currentFilterMode = '<?php echo !$is_admin_user ? "my" : "my"; ?>';
    var base_url = '<?php echo base_url(); ?>';
    var baseurl = '<?php echo base_url(); ?>';

    // Gestion du filtre admin (select)
    <?php if ($is_admin_user): ?>
    (function() {
        var adminSelect = document.getElementById('adminFilterSelect');
        if (!adminSelect) return;
        adminSelect.value = currentFilterMode;

        function triggerFilter(filterValue) {
            currentFilterMode = filterValue;
            var hiddenBtn = document.querySelector('.filter-toggle[data-filter="' + filterValue + '"]');
            if (hiddenBtn) {
                hiddenBtn.click();
            } else {
                var table = $('.<?= $dID ?>').DataTable();
                if (table) table.ajax.reload();
            }
        }

        adminSelect.addEventListener('change', function(e) {
            triggerFilter(e.target.value);
        });
    })();
    <?php endif; ?>

    // Impression groupée des devis du client
    $(document).ready(function() {
        $('#printAllCustomerQuotes').on('click', function() {
            var customerId = $('#customerSelect').val();
            if (!customerId) {
                Swal.fire('Attention', 'Veuillez sélectionner un client.', 'warning');
                return;
            }

            Swal.fire({
                title: 'Génération en cours...',
                text: 'Veuillez patienter.',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            $.ajax({
                url: base_url + 'admin/proforma/printAllByClient',
                type: 'POST',
                data: { customer_id: customerId },
                dataType: 'json',
                success: function(response) {
                    Swal.close();
                    if (response.status === '1') {
                        var win = window.open();
                        win.document.write(response.page);
                        win.document.close();
                    } else {
                        var msg = response.message || 'Impossible de générer l\'impression.';
                        Swal.fire('Information', msg, 'info');
                    }
                },
                error: function() {
                    Swal.close();
                    Swal.fire('Erreur', 'Erreur de communication avec le serveur.', 'error');
                }
            });
        });
    });
</script>

<!-- Boutons cachés pour compatibilité avec index.js (filtre admin) -->
<?php if ($is_admin_user): ?>
    <div style="display: none;">
        <div class="btn-group btn-sm" role="group">
            <button type="button" class="btn btn-primary filter-toggle" data-filter="my">
                <i class="fa fa-user"></i> Mes proformas
            </button>
            <button type="button" class="btn btn-default filter-toggle" data-filter="all">
                <i class="fa fa-users"></i> Tous les proformas
            </button>
        </div>
    </div>
<?php endif; ?>

<script src="<?= base_url('assets/js/proforma/index.js') ?>"></script>