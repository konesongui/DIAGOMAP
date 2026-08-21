<?php

$dID = 'invoiceDatatable';
$modalID = 'addPaymentModal';
$modalContentID = 'addPaymentContent';

?>

<!-- Content Wrapper. Contains page content -->
<style>
    :root {
        --inv-blue: #1E4DB7;
        --inv-blue-dark: #16368a;
        --inv-green: #2A9D8F;
        --inv-orange: #F4A261;
        --inv-red: #E76F51;
        --inv-bg: #f4f6f9;
        --inv-border: #e6e9ee;
    }

    /* ===== Page header ===== */
    .content-header h1 {
        font-weight: 700;
        color: #26324a;
        font-size: 22px;
    }
    .content-header h1 i { color: var(--inv-blue); margin-right: 8px; }

    /* ===== Box container ===== */
    .box.box-primary {
        border-top: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(20, 30, 60, 0.06);
        border: 1px solid var(--inv-border);
    }
    .box-header.with-border {
        background: linear-gradient(90deg, var(--inv-blue) 0%, var(--inv-blue-dark) 100%);
        border-bottom: none;
        padding: 16px 20px;
    }
    .box-header.with-border .box-title {
        color: #fff;
        font-weight: 700;
        font-size: 17px;
        letter-spacing: .2px;
    }
    .box-body { background: var(--inv-bg); padding: 20px; }

    /* ===== Toolbar / controls ===== */
    .invoice-controls .form-group { display: inline-block; margin-right: 8px; margin-bottom: 6px; }
    .invoice-controls .btn { vertical-align: middle; }
    .invoice-controls .form-control {
        border-radius: 6px;
        border: 1px solid rgba(255,255,255,0.5);
        background: rgba(255,255,255,0.95);
        font-size: 13px;
        height: 34px;
    }
    #printAllCustomerInvoices {
        border-radius: 6px;
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(0,0,0,0.12);
    }

    /* Horizontal scroll wrapper to allow wide tables */
    .table-responsive-horizontal { overflow-x: auto; -webkit-overflow-scrolling: touch; }

    table.<?= $dID ?> {
        min-width: 1200px;
        background: #fff;
        border-radius: 8px;
        overflow: hidden;
    }

    table.<?= $dID ?> thead th {
        background: #eef1f7;
        color: #33415c;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .3px;
        border-bottom: 2px solid var(--inv-border);
        white-space: nowrap;
    }

    table.<?= $dID ?> tbody td {
        font-size: 13px;
        color: #26324a;
        vertical-align: middle;
    }

    table.<?= $dID ?> tbody tr:hover { background: #f0f5ff; }

    /* Align numeric columns (Montants) to right and avoid wrapping */
    table.<?= $dID ?> thead th.numeric,
    table.<?= $dID ?> tbody td:nth-child(5),
    table.<?= $dID ?> tbody td:nth-child(6),
    table.<?= $dID ?> tbody td:nth-child(7),
    table.<?= $dID ?> tbody td:nth-child(8),
    table.<?= $dID ?> tbody td:nth-child(9) {
        text-align: right;
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }

    /* Center quantity columns if present */
    table.<?= $dID ?> thead th.qty, table.<?= $dID ?> tbody td.qty, table.<?= $dID ?> tbody td:nth-child(5) { text-align: center; }

    /* Make the action column compact */
    table.<?= $dID ?> thead th.actions, table.<?= $dID ?> tbody td.actions { text-align: right; width:110px; white-space: nowrap; }

    /* Slightly reduce padding for dense tables */
    table.<?= $dID ?> td, table.<?= $dID ?> th { padding: 10px 8px; }

    /* Responsive box for controls */
    .box-tools .form-group .form-control { min-width: 160px; }

    /* Align dashboard filter controls on one line */
    .form-inline {
        display: flex;
        flex-wrap: nowrap;
        align-items: center;
        gap: 8px;
        background: #fff;
        border: 1px solid var(--inv-border);
        border-radius: 8px;
        padding: 10px 14px;
        box-shadow: 0 1px 3px rgba(20,30,60,0.04);
    }
    .form-inline .form-control { display: inline-block; width: auto; border-radius: 6px; }
    .form-inline label.small { margin-bottom: 0; font-weight: 600; color: #4a5568; }

    /* Make the apply/reset buttons uniform and slightly wider to be easier to click */
    #applyDashFilters, #resetDashFilters {
        white-space: nowrap;
        min-width: 110px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        font-weight: 600;
    }
    #applyDashFilters { background: var(--inv-blue); border-color: var(--inv-blue); }
    #applyDashFilters:hover { background: var(--inv-blue-dark); border-color: var(--inv-blue-dark); }

    /* Columns toggle menu - improve contrast and spacing */
    #columnsToggleMenu {
        background: #fff;
        color: #212529;
        min-width: 220px;
        border-radius: 8px;
        border: 1px solid var(--inv-border);
        box-shadow: 0 6px 18px rgba(20,30,60,0.12);
    }
    #columnsToggleMenu .form-check { padding: 6px 8px; }
    #columnsToggleMenu .form-check-label { color: #212529; font-weight: 500; }
    #columnsToggleMenu .form-check-input { margin-right: 8px; }

    /* ===== Dashboard summary cards ===== */
    .dashboard-card {
        background: #fff;
        border: 1px solid var(--inv-border);
        padding: 16px 18px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        box-shadow: 0 2px 8px rgba(20, 30, 60, 0.05);
        transition: transform .15s ease, box-shadow .15s ease;
        height: 100%;
    }
    .dashboard-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(20, 30, 60, 0.10);
    }
    .dashboard-card .card-label {
        font-size: 12px;
        font-weight: 600;
        color: #7a8699;
        text-transform: uppercase;
        letter-spacing: .4px;
        margin-bottom: 4px;
    }
    .dashboard-card .card-value {
        font-size: 16px;
        font-weight: 700;
        color: #1f2a44;
        line-height: 1.2;
    }
    .dashboard-icon-wrap {
        width: 46px;
        height: 46px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .dashboard-icon { font-size: 20px; }
    .icon-aqua { color: #0aa2c0; }
    .icon-aqua-wrap { background: rgba(10, 162, 192, 0.12); }
    .icon-green { color: var(--inv-green); }
    .icon-green-wrap { background: rgba(42, 157, 143, 0.14); }
    .icon-warning { color: var(--inv-orange); }
    .icon-warning-wrap { background: rgba(244, 162, 97, 0.16); }
    .icon-red { color: var(--inv-red); }
    .icon-red-wrap { background: rgba(231, 111, 81, 0.14); }

    .mailbox-messages {
        background: #fff;
        border-radius: 8px;
        padding: 4px;
        border: 1px solid var(--inv-border);
    }

    @media (max-width: 768px) {
        .form-inline { flex-wrap: wrap; gap:6px; }
        .form-inline .form-control { width: 100%; }
        #applyDashFilters, #resetDashFilters { width: 100%; }
    }
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-object-group"></i> Factures
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Horizontal Form -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $title_list; ?></h3>
                        <div class="box-tools pull-right">
                            <div class="invoice-controls">
                                <div class="form-group btn-sm">
                                    <select id="statusFilter" class="form-control">
                                        <option value="">Tous les statuts</option>
                                        <option value="1">Non payée</option>
                                        <option value="2">Payée</option>
                                        <option value="3">Partiellement payée</option>
                                        <option value="5">Annulée</option>
                                    </select>
                                </div>

                                <!-- Sélecteur client -->
                                <div class="form-group btn-sm">
                                    <select id="customerSelect" class="form-control">
                                        <option value="">-- Sélectionner un client --</option>
                                        <?php
                                        // Récupérer la liste des clients depuis le modèle
                                        $clients = $this->clients_model->get();
                                        foreach ($clients as $client) {
                                            $name = trim($client['item_supplier'] . ' ' . ($client['lastname'] ?? ''));
                                            echo '<option value="' . $client['id'] . '">' . html_escape($name) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- Bouton imprimer toutes les factures du client -->
                                <button id="printAllCustomerInvoices" class="btn btn-info btn-sm">
                                    <i class="fa fa-print"></i> Imprimer les factures du client
                                </button>

                            </div>
                            <?php if ($this->rbac->hasPrivilege('facture', 'can_add')) { ?>
                                <!--<a href="<?php echo base_url(); ?>admin/invoiceitem/form" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus"></i> Nouvelle facture
                                </a>-->
                            <?php } ?>
                        </div>
                    </div>
                    <div class="box-body">
                        <!-- Dashboard summary + filters -->
                        <?php $year_start = date('Y-01-01'); $year_end = date('Y-12-31'); ?>
                        <div class="row" style="margin-bottom:16px; align-items:center;">
                            <div class="col-md-9"  style='width: 100%';>
                                <div class="form-inline">
                                    <label class="small" style="margin-right:8px;">Période</label>
                                    <input type="date" id="dashStartDate" class="form-control input-sm" value="<?= set_value('dashStartDate', $year_start); ?>" style="margin-right:6px;" />
                                    <input type="date" id="dashEndDate" class="form-control input-sm" value="<?= set_value('dashEndDate', $year_end); ?>" style="margin-right:8px;" />

                                    <label class="small" style="margin-right:8px; margin-left:6px;">Année </label>
                                    <select id="dashYearSelect" class="form-control input-sm" style="width:120px; margin-right:8px;">
                                        <?php for ($y = date('Y'); $y >= date('Y')-5; $y--): ?>
                                            <option value="<?= $y ?>" <?= $y == date('Y') ? 'selected' : '' ?>><?= $y ?></option>
                                        <?php endfor; ?>
                                    </select>

                                    <button id="applyDashFilters" class="btn btn-primary btn-sm">Appliquer</button>
                                    <button id="resetDashFilters" class="btn btn-default btn-sm" style="margin-left:6px;">Réinitialiser</button>
                                    <!-- Toggle colonnes -->
                                    <div class="btn-group" style="margin-left:8px;">
                                        <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa fa-columns"></i> Colonnes
                                        </button>
                                        <div class="dropdown-menu p-3" id="columnsToggleMenu" style="min-width:220px;">
                                            <!-- JS will populate column toggles here -->
                                            <div class="text-muted small">Chargement...</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 text-right small">
                                <span style="margin-right:12px;"></span>
                                <!-- reuse existing statusFilter control visually by cloning -->
                                <!-- (statusFilter exists above; dashboard JS will read its value directly) -->
                            </div>
                        </div>

                        <div class="row" style="margin-bottom:16px;">
                            <div class="col-sm-3">
                                <div class="dashboard-card">
                                    <div>
                                        <div class="card-label">Total TTC</div>
                                        <div id="totalTtcValue" class="card-value"><?= number_format($dashboard['total_ttc'] ?? 0, 0, ',', ' '); ?> FCFA</div>
                                    </div>
                                    <div class="dashboard-icon-wrap icon-aqua-wrap"><i class="fa fa-money dashboard-icon icon-aqua" aria-hidden="true"></i></div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="dashboard-card">
                                    <div>
                                        <div class="card-label">Total Payé</div>
                                        <div id="totalPaidValue" class="card-value"><?= number_format($dashboard['total_paid'] ?? 0, 0, ',', ' '); ?> FCFA</div>
                                    </div>
                                    <div class="dashboard-icon-wrap icon-green-wrap"><i class="fa fa-credit-card dashboard-icon icon-green" aria-hidden="true"></i></div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="dashboard-card">
                                    <div>
                                        <div class="card-label">Reste à payer</div>
                                        <div id="totalRemainingValue" class="card-value"><?= number_format($dashboard['total_remaining'] ?? 0, 0, ',', ' '); ?> FCFA</div>
                                    </div>
                                    <div class="dashboard-icon-wrap icon-warning-wrap"><i class="fa fa-balance-scale dashboard-icon icon-warning" aria-hidden="true"></i></div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="dashboard-card">
                                    <div>
                                        <div class="card-label">FNE / Non-FNE</div>
                                        <div style="display:flex; align-items:baseline; gap:8px;">
                                            <div id="fneCountValue" style="font-size:16px; font-weight:700; color:#1f2a44;"><?= ($dashboard['fne_count'] ?? 0); ?> cert.</div>
                                            <div id="nonFneCountValue" style="font-size:13px; color:#7a8699; font-weight:600;"><?= ($dashboard['non_fne_count'] ?? 0); ?> non cert.</div>
                                        </div>
                                    </div>
                                    <div class="dashboard-icon-wrap icon-red-wrap"><i class="fa fa-certificate dashboard-icon icon-red" aria-hidden="true"></i></div>
                                </div>
                            </div>
                        </div>

                        <div class="mailbox-messages table-responsive table-responsive-horizontal">
                            <table class="table table-striped table-bordered table-hover <?= $dID ?>" data-export-title="<?php echo $this->lang->line('issue_item'); ?>">
                                <thead>
                                    <tr>
                                      <th>N° Facture</th>
                                      <th>Client</th>
                                      <th>Date</th>
                                      <th>Échéance</th>
                                      <th class="numeric">Montant HT</th>
                                      <th class="numeric">TVA</th>
                                      <th class="numeric">Total TTC</th>
                                      <th class="numeric">Payé</th>
                                      <th class="numeric">Reste</th>
                                        <th>Suivie par</th>
                                        <th>Statut FNE</th>

                                      <th>Statut</th>
                                      <th class="actions"><?php echo $this->lang->line('action'); ?></th>
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

    <div id="<?= $modalID ?>" class="modal fade" data-backdrop="false">
        <div class="modal-dialog">
            <div class="modal-content" id="<?= $modalContentID ?>">

            </div>
        </div>
    </div>

</div><!-- /.content-wrapper -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script src="<?= base_url('assets/js/invoice/index.js') ?>"></script>