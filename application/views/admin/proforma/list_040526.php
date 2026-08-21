<?php
$dID = 'quoteDatatable';
// Vérifier les privilèges RBAC
$is_superadmin = $this->rbac->hasPrivilege('superadmin');
$is_admin = $this->rbac->hasPrivilege('admin');
$is_admin_user = ($is_superadmin || $is_admin);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-object-group"></i> Devis
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Horizontal Form -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Devis</h3>
                        <div class="box-tools pull-right">
                            <div class="form-group btn-sm" style="display: inline-block; margin-right: 10px;">
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

                            <!-- Nouveau dropdown pour les administrateurs -->
                            <?php if ($is_admin_user): ?>
                                <div style="margin-right: 10px; display: inline-block;">
                                    <select id="adminFilterSelect" class="form-control" style="width: auto; display: inline-block;">
                                        <option value="my">📄 Mes devis</option>
                                        <option value="all">👥 Tous les devis</option>
                                    </select>
                                </div>
                            <?php endif; ?>

                            <!-- Indicateur de filtre utilisateur -->
                            <span id="filterBadge" class="badge bg-info" style="margin-right: 10px; padding: 8px;">
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
                            </span>

                            <?php if ($this->rbac->hasPrivilege('devis', 'can_add')) { ?>
                                <a href="<?php echo site_url('admin/quoteitem/form') ?>" type="button" class="btn btn-primary btn-sm" >
                                    <i class="fa fa-plus"></i> Ajouter un devis
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
                                   <!-- <th>Termes de paiement</th>
                                    <th>Lieu de livraison</th>
                                    <th>Créé le</th>-->
                                    <th>Montant total</th>
                                    <th>Statut</th>
                                    <th>Suivi par</th>
                                    <th class="text-right">Actions</th>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

<script>
    // Variable globale pour stocker le mode de filtre actuel
    var currentFilterMode = '<?php echo !$is_admin_user ? "my" : "my"; ?>';

    // Gestion du dropdown pour les administrateurs
    <?php if ($is_admin_user): ?>
    (function() {
        var adminSelect = document.getElementById('adminFilterSelect');
        if (!adminSelect) return;

        // Fonction pour déclencher le filtre (compatible avec l'existant)
        function triggerFilter(filterValue) {
            // Met à jour la variable globale
            currentFilterMode = filterValue;
            // Cherche le bouton caché correspondant et simule un clic
            var hiddenBtn = document.querySelector('.filter-toggle[data-filter="' + filterValue + '"]');
            if (hiddenBtn) {
                hiddenBtn.click();
            } else {
                // Fallback : si les boutons n'existent pas, on recharge la DataTable manuellement
                if (typeof $.fn.DataTable !== 'undefined') {
                    var table = $('.<?= $dID ?>').DataTable();
                    if (table) {
                        table.ajax.reload();
                    }
                }
            }
        }

        // Initialiser la sélection du dropdown en fonction du mode courant
        adminSelect.value = currentFilterMode;

        // Écouter les changements du dropdown
        adminSelect.addEventListener('change', function(e) {
            triggerFilter(e.target.value);
        });
    })();
    <?php endif; ?>
</script>

<!-- Boutons cachés pour conserver la compatibilité avec l'existant (index.js) -->
<?php if ($is_admin_user): ?>
    <div style="display: none;">
        <div class="btn-group btn-sm" role="group">
            <button type="button" class="btn btn-primary filter-toggle" data-filter="my">
                <i class="fa fa-user"></i> Mes devis
            </button>
            <button type="button" class="btn btn-default filter-toggle" data-filter="all">
                <i class="fa fa-users"></i> Tous les devis
            </button>
        </div>
    </div>
<?php endif; ?>

<script src="<?= base_url('assets/js/quote/index.js') ?>"></script>