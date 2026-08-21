<style>
    /* Mode impression */
    @media print {
        .no-print, .no-print * {
            display: none !important;
        }
    }

    /* STYLE GLOBAL */
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

    /* CARD / BOX */
    .box-primary {
        border-radius: 10px;
        border: none !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        background: white;
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
        background: #3d5af1 !important;
        border-color: #3d5af1 !important;
        border-radius: 6px;
        font-weight: 500;
    }

    /* TABLE */
    .table thead tr {
        background: #3d5af1;
        color: white;
        text-transform: uppercase;
        font-size: 13px;
    }

    .table tbody tr:hover {
        background: #f3f6ff;
    }

    /* BADGES STATUT */
    .badge { padding: 6px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-success { background: #22c55e; color: white; }
    .badge-danger  { background: #ef4444; color: white; }
    .badge-warning { background: #eab308; color: white; }

    /* BADGES FORFAITS */
    .badge-forfait {
        padding: 6px 14px;
        border-radius: 25px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .forfait-starter {
        background: #e3f2fd;
        color: #0d47a1;
        border: 1px solid #bbdefb;
    }

    .forfait-pro {
        background: #ede7f6;
        color: #4527a0;
        border: 1px solid #d1c4e9;
    }

    .forfait-premium {
        background: linear-gradient(135deg, #fef7e0, #fff3c4);
        color: #8d6e22;
        border: 1px solid #f0e3a0;
        box-shadow: 0 0 6px rgba(255, 215, 0, 0.4);
    }

    /* ACTION BUTTONS */
    .action-btn {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        margin-right: 5px;
        border: none;
        cursor: pointer;
    }

    .btn-view { background: #16a085; color: white; }
    .btn-edit { background: #3d5af1; color: white; }
    .btn-activate { background: #22c55e; color: white; }
    .btn-deactivate { background: #ef4444; color: white; }
    .btn-suspend { background: #eab308; color: white; }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-building"></i> Gestion des entreprises</h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="box box-primary">
                <div class="box-header ptbnull d-flex justify-content-between">
                    <h3 class="box-title titlefix">Liste des entreprises</h3>

                    <?php if ($this->rbac->hasPrivilege('comptes', 'can_edit')): ?>
                        <a href="<?= site_url('admin/comptes/view') ?>" class="btn btn-primary btn-sm">
                            <i class="fa fa-plus"></i> Ajouter une entreprise
                        </a>
                    <?php endif; ?>
                </div>

                <div class="box-body">
                    <div class="table-responsive mailbox-messages">
                        <table class="table table-striped table-bordered table-hover compte-list"
                               id="compte-list">
                            <thead>
                            <tr>
                                <th>Entreprise</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Adresse</th>
                                <th>Logo</th>
                                <th>Forfait</th>
                                <th>Début</th>
                                <th>Expiration</th>
                                <th>Statut</th>
                                <th class="text-right noExport">Actions</th>
                            </tr>
                            </thead>

                            <tbody></tbody> <!-- IMPORTANT : pas de foreach -->
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        initDatatable('compte-list', 'admin/comptes/ajaxSearch', [], [], 50);
    });
</script>
