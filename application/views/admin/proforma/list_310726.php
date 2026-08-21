<?php
$dID = 'quoteDatatable';
// Vérifier les privilèges RBAC
$is_superadmin = $this->rbac->hasPrivilege('superadmin');
$is_admin = $this->rbac->hasPrivilege('admin');
$is_admin_user = ($is_superadmin || $is_admin);
?>

<style>
    /* Styles pour l'en-tête avec flexbox */
    .box-tools-custom {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        align-items: center;
        gap: 8px;
    }
    .box-tools-custom .form-group,
    .box-tools-custom .btn-group,
    .box-tools-custom .btn,
    .box-tools-custom .badge,
    .box-tools-custom select,
    .box-tools-custom a {
        margin: 0 !important;
    }
    .box-tools-custom select.form-control {
        width: auto;
        min-width: 140px;
    }
    @media (max-width: 991px) {
        .box-tools-custom {
            justify-content: flex-start;
            margin-top: 10px;
        }
        .box-header .box-title {
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }
    }
</style>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-object-group"></i> Proforma</h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Proforma</h3>
                        <div class="box-tools pull-right box-tools-custom">
                            <!-- Filtre par statut -->
                            <div class="form-group btn-sm">
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

                            <!-- DROPDOWN pour le filtre admin -->
                            <?php if ($is_admin_user): ?>
                                <div>
                                    <select id="adminFilterSelect" class="form-control">
                                        <option value="my">📄 Mes proformas</option>
                                        <option value="all">👥 Tous les proformas</option>
                                    </select>
                                </div>
                            <?php endif; ?>

                            <!-- Badge d'indication -->
                            <!--<span id="filterBadge" class="badge bg-info">
                                <?php if (!$is_admin_user): ?>
                                    <i class="fa fa-user"></i> Mes devis uniquement
                                <?php else: ?>
                                    <i class="fa fa-eye"></i> Vue administrateur
                                    <?php if ($is_superadmin): ?>
                                        <small>(Super Admin)</small>
                                    <?php elseif ($is_admin): ?>
                                        <small>(Admin)</small>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </span>-->

                            <!-- Sélecteur client -->
                            <div class="form-group btn-sm">
                                <select id="customerSelect" class="form-control" style="width: 10px">
                                    <option value="">-- Sélectionner un client --</option>
                                    <?php
                                    // Récupération des clients (déjà passé par le contrôleur)
                                    if (isset($clients)) {
                                        foreach ($clients as $client) {
                                            $name = trim($client['item_supplier'] . ' ' . ($client['lastname'] ?? ''));
                                            echo '<option value="' . $client['id'] . '">' . html_escape($name) . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <!-- Bouton imprimer tous les devis du client -->
                            <button id="printAllCustomerQuotes" class="btn btn-info btn-sm">
                                <i class="fa fa-print"></i> Imprimer tous les proformas du client
                            </button>

                            <!-- Bouton Ajouter un devis -->
                            <?php if ($this->rbac->hasPrivilege('devis', 'can_add')) { ?>
                                <a href="<?php echo site_url('admin/proforma/form') ?>" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus"></i> Ajouter un proforma
                                </a>
                            <?php } ?>
                        </div>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <div class="mailbox-messages table-responsive">
                            <table class="table table-striped table-bordered table-hover <?= $dID ?>" data-export-title="Liste des devis">
                                <thead>
                                <tr>
                                    <th>Référence</th>
                                    <th>Client</th>
                                    <th>Dates</th>
                                    <th>Montant total</th>

                                    <th>Suivi par</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div><!-- /.box-body -->
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