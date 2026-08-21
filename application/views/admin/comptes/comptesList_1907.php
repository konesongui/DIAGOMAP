<style>
    /* ==========================
       MODE IMPRESSION
    =========================== */
    @media print {
        .no-print, .no-print * {
            display: none !important;
        }
    }

    /* ==========================
       STYLE GLOBAL
    =========================== */
    .content-wrapper {
        background: #f5f7fb;
        padding: 20px;
        font-family: "Segoe UI", sans-serif;
    }

    .content-header h1 {
        font-weight: 600;
        color: #344767;
        font-size: 24px;
    }

    /* ==========================
       CARD / BOX
    =========================== */
    .box-primary {
        border-radius: 10px;
        border: none !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        background: #fff;
    }

    .box-header {
        padding: 15px 20px;
        border-bottom: 1px solid #f0f0f0;
    }

    .box-title {
        font-size: 20px !important;
        font-weight: 600;
        color: #233142;
    }

    .btn-primary {
        background: #1E3A8A !important;
        border-color: #1E3A8A !important;
        border-radius: 6px;
        font-weight: 500;
    }

    /* ==========================
       TABLE
    =========================== */
    .table thead tr {
        background: #1E3A8A;
        color: #fff;
        text-transform: uppercase;
        font-size: 13px;
    }

    .table tbody tr:hover {
        background: #f3f6ff;
    }

    /* ==========================
       BADGES STATUT
    =========================== */
    .badge {
        padding: 6px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-success { background: #22c55e; color: #fff; }
    .badge-danger  { background: #ef4444; color: #fff; }
    .badge-warning { background: #eab308; color: #fff; }

    /* ==========================
       BADGES FORFAITS
    =========================== */
    .badge-forfait {
        padding: 6px 14px;
        border-radius: 25px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .forfait-starter {
        background: #e3f2fd;
        color: #1E3A8A;
        border: 1px solid #bbdefb;
    }

    .forfait-pro {
        background: #ede7f6;
        color: #1E3A8A;
        border: 1px solid #d1c4e9;
    }

    .forfait-premium {
        background: linear-gradient(135deg, #fef7e0, #fff3c4);
        color: #8d6e22;
        border: 1px solid #f0e3a0;
        box-shadow: 0 0 6px rgba(255,215,0,0.4);
    }

    /* ==========================
       ACTION BUTTONS
    =========================== */
    .action-btn {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        margin-right: 5px;
        border: none;
        cursor: pointer;
    }

    .btn-view       { background: #16a085; color: #fff; }
    .btn-edit       { background: #1E3A8A; color: #fff; }
    .btn-activate   { background: #22c55e; color: #fff; }
    .btn-deactivate { background: #ef4444; color: #fff; }
    .btn-suspend    { background: #eab308; color: #fff; }
</style>

<div class="content-wrapper">

    <!-- TITRE -->
    <section class="content-header">
        <h1><i class="fa fa-building"></i> Gestion des entreprises</h1>
    </section>

    <section class="content">
        <div class="row">

            <div class="box box-primary">

                <!-- HEADER -->
                <div class="box-header ptbnull d-flex justify-content-between">
                    <h3 class="box-title titlefix">Liste des entreprises</h3>

                    <?php if ($this->rbac->hasPrivilege('comptes', 'can_add')): ?>
                        <a href="<?= site_url('admin/comptes/create') ?>" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus"></i> Ajouter une entreprise
                        </a>
                    <?php endif; ?>
                </div>

                <!-- TABLE -->
                <div class="box-body">
                    <div class="table-responsive mailbox-messages">

                        <table class="table table-striped table-bordered table-hover compte-list" id="compte-list">
                            <thead>
                            <tr>
                                <th>Entreprise</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Adresse</th>
                               <!-- <th>Logo</th>-->
                                <th>Forfait</th>
                                <th>Début</th>
                                <th>Expiration</th>
                                <th>Statut</th>
                                <th class="text-right noExport">Actions</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>

                    </div>
                </div>

            </div>

        </div>
    </section>

</div>
<!-- Modal pour voir les détails -->
<div class="modal fade" id="viewCompanyModal" tabindex="-1" role="dialog" aria-labelledby="viewCompanyModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="viewCompanyModalLabel">Détails de l'entreprise</h4>
            </div>
            <div class="modal-body" id="viewCompanyContent">
                <!-- Contenu chargé via AJAX -->
                <div class="text-center">
                    <i class="fa fa-spinner fa-spin fa-2x"></i> Chargement...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour modifier -->
<div class="modal fade" id="editCompanyModal" tabindex="-1" role="dialog" aria-labelledby="editCompanyModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="editCompanyModalLabel">Modifier l'entreprise</h4>
            </div>
            <div class="modal-body" id="editCompanyContent">
                <!-- Contenu chargé via AJAX -->
                <div class="text-center">
                    <i class="fa fa-spinner fa-spin fa-2x"></i> Chargement...
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ==========================
     JAVASCRIPT
========================== -->
<script>
    $(document).ready(function() {
        initDatatable('compte-list', 'admin/comptes/ajaxSearch', [], [], 50);
    });

    // Fonctions pour les modals
    function viewCompanyModal(id) {
        $('#viewCompanyContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i> Chargement...</div>');
        $('#viewCompanyModal').modal('show');

        $.ajax({
            url: '<?php echo site_url("admin/comptes/ajax_view/"); ?>' + id,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#viewCompanyContent').html(response.html);
                } else {
                    $('#viewCompanyContent').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                $('#viewCompanyContent').html('<div class="alert alert-danger">Erreur lors du chargement</div>');
            }
        });
    }

    function editCompanyModal(id) {
        $('#editCompanyContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-2x"></i> Chargement...</div>');
        $('#editCompanyModal').modal('show');

        $.ajax({
            url: '<?php echo site_url("admin/comptes/ajax_edit/"); ?>' + id,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#editCompanyContent').html(response.html);
                } else {
                    $('#editCompanyContent').html('<div class="alert alert-danger">' + response.message + '</div>');
                }
            },
            error: function() {
                $('#editCompanyContent').html('<div class="alert alert-danger">Erreur lors du chargement</div>');
            }
        });
    }

    // Fonction pour soumettre le formulaire d'édition via AJAX
    function submitEditForm() {
        var formData = new FormData($('#editCompanyForm')[0]);

        $.ajax({
            url: $('#editCompanyForm').attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#editCompanyModal').modal('hide');
                    var table = $('.compte-list').DataTable();
                    table.ajax.reload(null, false);

                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message, 'Succès');
                    }
                } else {
                    $('#editCompanyContent').html(response.html);
                }
            },
            error: function() {
                if (typeof toastr !== 'undefined') {
                    toastr.error('Erreur lors de la modification', 'Erreur');
                }
            }
        });
    }

    // Autres fonctions existantes...
    function toggleStatus(id, currentStatus) {
        currentStatus = currentStatus.toLowerCase(); // normaliser

        var newStatus = '';
        var actionText = '';

        if (currentStatus === 'actif') {
            newStatus = 'suspendu';
            actionText = 'suspendre';
        } else if (currentStatus === 'suspendu') {
            newStatus = 'actif';
            actionText = 'activer';
        }
     else if (currentStatus === 'expiré') {
        newStatus = 'actif';
        actionText = 'activer';
    }else {
            alert('Impossible de changer ce statut !');
            return;
        }

        if (confirm('Êtes-vous sûr de vouloir ' + actionText + ' cette entreprise ?')) {
            $.ajax({
                url: '<?php echo site_url("admin/comptes/toggle_status/"); ?>' + id,
                type: 'POST',
                data: {
                    new_status: newStatus,
            <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
        },
            success: function(response) {
                var res = JSON.parse(response);
                $('.compte-list').DataTable().ajax.reload(null, false);
                if (res.success) toastr.success(res.message, 'Succès');
                else toastr.error(res.message, 'Erreur');
            },
            error: function() {
                toastr.error('Erreur lors de la modification du statut', 'Erreur');
            }
        });
        }
    }





    function deleteCompany(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer cette entreprise ? Cette action est irréversible.')) {
            window.location.href = '<?php echo site_url("admin/comptes/delete/"); ?>' + id;
        }
    }
</script>
