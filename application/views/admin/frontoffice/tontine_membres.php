<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<style>
    .content-wrapper {
        background: #f1f5f9;
        padding: 20px 15px;
        min-height: 100vh;
    }

    .card-modern {
        border: none;
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
        background: #ffffff;
        margin-bottom: 30px;
        overflow: hidden;
    }

    .card-modern .card-header {
        background: linear-gradient(135deg, #273772 0%, #1a2558 100%);
        padding: 18px 24px;
        border-bottom: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .card-modern .card-header h3 {
        color: #ffffff;
        font-size: 18px;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-modern .card-header h3 i {
        color: #60a5fa;
    }

    .card-modern .card-body {
        padding: 20px 24px;
        background: #f8fafc;
    }

    .btn-back {
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 30px;
        padding: 5px 16px;
        font-size: 13px;
        transition: all 0.25s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-back:hover {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
        border-color: rgba(255, 255, 255, 0.5);
        text-decoration: none;
    }

    .btn-primary-custom {
        background: rgba(59, 130, 246, 0.2);
        color: #60a5fa;
        border: 1px solid rgba(59, 130, 246, 0.3);
        border-radius: 30px;
        padding: 5px 16px;
        font-size: 13px;
        transition: all 0.25s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-primary-custom:hover {
        background: rgba(59, 130, 246, 0.3);
        color: #93bbfc;
        border-color: rgba(59, 130, 246, 0.5);
        text-decoration: none;
    }

    .btn-success-custom {
        background: rgba(16, 185, 129, 0.2);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.3);
        border-radius: 30px;
        padding: 5px 16px;
        font-size: 13px;
        transition: all 0.25s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-success-custom:hover {
        background: rgba(16, 185, 129, 0.3);
        color: #34d399;
        border-color: rgba(16, 185, 129, 0.5);
        text-decoration: none;
    }

    .filter-section {
        background: white;
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 20px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
    }

    .filter-section .form-group {
        margin-bottom: 0;
    }

    .filter-section label {
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 4px;
    }

    .filter-section .form-control {
        height: 38px;
        font-size: 13px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .table-modern {
        border-collapse: separate;
        border-spacing: 0 4px;
        width: 100%;
        margin-bottom: 0;
    }

    .table-modern thead th {
        background: #f1f5f9;
        color: #1e293b;
        font-weight: 600;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 10px 14px;
        border: none;
        border-bottom: 2px solid #e2e8f0;
        white-space: nowrap;
    }

    .table-modern tbody td {
        background: #ffffff;
        padding: 10px 14px;
        border: none;
        border-bottom: 1px solid #eef2f6;
        vertical-align: middle;
        font-size: 13px;
        color: #1e293b;
    }

    .table-modern tbody tr:hover td {
        background: #f8fafc;
    }

    .badge-statut {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
    }

    .badge-statut.actif { background: #d1fae5; color: #065f46; }
    .badge-statut.inactif { background: #fef3c7; color: #92400e; }
    .badge-statut.suspendu { background: #fef2f2; color: #991b1b; }

    .avatar-mini {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: linear-gradient(135deg, #273772 0%, #1a2558 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 14px;
    }

    .btn-action {
        padding: 4px 10px;
        font-size: 12px;
        border-radius: 20px;
        margin: 2px;
        transition: all 0.25s ease;
        text-decoration: none;
        display: inline-block;
        border: none;
        cursor: pointer;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        text-decoration: none;
    }

    .btn-action.btn-view { background: #dbeafe; color: #1d4ed8; }
    .btn-action.btn-edit { background: #d1fae5; color: #065f46; }
    .btn-action.btn-delete { background: #fef2f2; color: #991b1b; }

    .btn-action i {
        font-size: 14px;
    }

    .alert-custom {
        border-radius: 12px;
        padding: 12px 20px;
        margin-bottom: 20px;
    }

    .alert-custom.alert-success {
        background: #d1fae5;
        color: #065f46;
        border: none;
    }

    .alert-custom.alert-danger {
        background: #fef2f2;
        color: #991b1b;
        border: none;
    }

    .pagination-custom {
        display: flex;
        justify-content: center;
        margin-top: 20px;
        gap: 4px;
    }

    .pagination-custom .page-link {
        padding: 6px 12px;
        border-radius: 6px;
        border: 1px solid #e2e8f0;
        color: #1e293b;
        transition: all 0.25s ease;
    }

    .pagination-custom .page-link:hover {
        background: #f1f5f9;
    }

    .pagination-custom .page-item.active .page-link {
        background: #3b82f6;
        border-color: #3b82f6;
        color: white;
    }

    @media (max-width: 768px) {
        .card-modern .card-header {
            flex-direction: column;
            align-items: stretch;
        }
        .filter-section .form-group {
            margin-bottom: 10px;
        }
        .table-modern {
            font-size: 12px;
        }
        .table-modern thead th,
        .table-modern tbody td {
            padding: 6px 8px;
        }
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Affichage des messages flash -->
                <?php if ($this->session->flashdata('success')): ?>
                    <div class="alert alert-custom alert-success">
                        <i class="fa fa-check-circle"></i> <?php echo $this->session->flashdata('success'); ?>
                    </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('error')): ?>
                    <div class="alert alert-custom alert-danger">
                        <i class="fa fa-exclamation-circle"></i> <?php echo $this->session->flashdata('error'); ?>
                    </div>
                <?php endif; ?>

                <div class="card-modern">
                    <div class="card-header">
                        <h3>
                            <i class="fa fa-users"></i> Gestion des Membres
                            <span style="font-size: 14px; font-weight: 400; color: #94a3b8; margin-left: 8px;">
                                <?php echo isset($total_membres) ? $total_membres : 0; ?> membres
                            </span>
                        </h3>
                        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <a href="<?php echo base_url(); ?>admin/tontine_dashboard" class="btn-back">
                                <i class="fa fa-arrow-left"></i> Retour
                            </a>
                            <a href="<?php echo base_url(); ?>admin/tontine_membres/ajouter" class="btn-primary-custom">
                                <i class="fa fa-plus-circle"></i> Ajouter
                            </a>
                            <a href="<?php echo base_url(); ?>admin/tontine_membres/exporter" class="btn-success-custom">
                                <i class="fa fa-download"></i> Exporter
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Filtres -->
                        <div class="filter-section">
                            <form method="GET" action="<?php echo base_url(); ?>admin/tontine_membres">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label><i class="fa fa-search"></i> Recherche</label>
                                            <input type="text" name="search" class="form-control" placeholder="Nom, téléphone..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label><i class="fa fa-filter"></i> Statut</label>
                                            <select name="statut" class="form-control">
                                                <option value="">Tous</option>
                                                <option value="actif" <?php echo (isset($_GET['statut']) && $_GET['statut'] == 'actif') ? 'selected' : ''; ?>>Actif</option>
                                                <option value="inactif" <?php echo (isset($_GET['statut']) && $_GET['statut'] == 'inactif') ? 'selected' : ''; ?>>Inactif</option>
                                                <option value="suspendu" <?php echo (isset($_GET['statut']) && $_GET['statut'] == 'suspendu') ? 'selected' : ''; ?>>Suspendu</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label><i class="fa fa-users"></i> Groupe</label>
                                            <select name="groupe" class="form-control">
                                                <option value="">Tous</option>
                                                <?php if (!empty($groupes)): ?>
                                                    <?php foreach ($groupes as $groupe): ?>
                                                        <option value="<?php echo $groupe['id']; ?>" <?php echo (isset($_GET['groupe']) && $_GET['groupe'] == $groupe['id']) ? 'selected' : ''; ?>>
                                                            <?php echo htmlspecialchars($groupe['nom']); ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" style="display: flex; gap: 8px; align-items: flex-end; flex-wrap: wrap;">
                                            <button type="submit" class="btn btn-primary" style="background: #3b82f6; border: none; border-radius: 8px; padding: 8px 20px;">
                                                <i class="fa fa-search"></i> Filtrer
                                            </button>
                                            <a href="<?php echo base_url(); ?>admin/tontine_membres" class="btn btn-secondary" style="background: #94a3b8; border: none; color: white; border-radius: 8px; padding: 8px 20px;">
                                                <i class="fa fa-undo"></i> Reset
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Liste -->
                        <div style="background: #ffffff; border-radius: 12px; padding: 16px; border: 1px solid #e2e8f0;">
                            <div class="table-responsive">
                                <table class="table table-modern">
                                    <thead>
                                    <tr>
                                        <th style="width: 5%;">#</th>
                                        <th style="width: 5%;"></th>
                                        <th>Membre</th>
                                        <th>Téléphone</th>
                                        <th>Email</th>
                                        <th>Groupe</th>
                                        <th>Statut</th>
                                        <th style="text-align: center; width: 15%;">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (!empty($membres)) : ?>
                                        <?php foreach ($membres as $index => $membre) : ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td>
                                                    <div class="avatar-mini">
                                                        <?php
                                                        $initiales = '';
                                                        if (!empty($membre['prenom'])) $initiales .= strtoupper(substr($membre['prenom'], 0, 1));
                                                        if (!empty($membre['nom'])) $initiales .= strtoupper(substr($membre['nom'], 0, 1));
                                                        echo $initiales ?: 'M';
                                                        ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($membre['prenom'] ?? ''); ?> <?php echo htmlspecialchars($membre['nom'] ?? ''); ?></strong>
                                                    <br>
                                                    <small style="color: #94a3b8; font-size: 11px;">
                                                        <?php echo isset($membre['profession']) ? htmlspecialchars($membre['profession']) : 'Non renseigné'; ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <?php echo isset($membre['telephone']) ? htmlspecialchars($membre['telephone']) : '—'; ?>
                                                    <br>
                                                    <small style="color: #94a3b8; font-size: 10px;">
                                                        Adhésion: <?php echo isset($membre['date_adhesion']) ? date('d/m/Y', strtotime($membre['date_adhesion'])) : '—'; ?>
                                                    </small>
                                                </td>
                                                <td><?php echo isset($membre['email']) ? htmlspecialchars($membre['email']) : '—'; ?></td>
                                                <td>
                                                    <?php if (!empty($membre['groupe_nom'])): ?>
                                                        <span class="badge" style="background: #dbeafe; color: #1d4ed8; padding: 4px 12px; border-radius: 12px; font-size: 11px;">
                                                                <?php echo htmlspecialchars($membre['groupe_nom']); ?>
                                                            </span>
                                                    <?php else: ?>
                                                        <span style="color: #94a3b8; font-size: 12px;">Non assigné</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $statut = isset($membre['statut']) ? $membre['statut'] : 'inactif';
                                                    $statut_label = ucfirst($statut);
                                                    ?>
                                                    <span class="badge-statut <?php echo $statut; ?>">
                                                            <i class="fa <?php echo $statut == 'actif' ? 'fa-check-circle' : ($statut == 'suspendu' ? 'fa-pause-circle' : 'fa-times-circle'); ?>"></i>
                                                            <?php echo $statut_label; ?>
                                                        </span>
                                                </td>
                                                <td style="text-align: center;">
                                                    <a href="<?php echo base_url(); ?>admin/tontine_membres/voir/<?php echo $membre['id']; ?>" class="btn-action btn-view" title="Voir">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="<?php echo base_url(); ?>admin/tontine_membres/modifier/<?php echo $membre['id']; ?>" class="btn-action btn-edit" title="Modifier">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="#" onclick="confirmerSuppression(<?php echo $membre['id']; ?>)" class="btn-action btn-delete" title="Supprimer">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="8" style="text-align: center; padding: 40px 20px; color: #94a3b8;">
                                                <i class="fa fa-users" style="font-size: 48px; display: block; margin-bottom: 16px; color: #cbd5e1;"></i>
                                                <h4 style="color: #1e293b; margin-bottom: 8px;">Aucun membre trouvé</h4>
                                                <p>Aucun membre ne correspond à vos critères de recherche</p>
                                                <a href="<?php echo base_url(); ?>admin/tontine_membres/ajouter" class="btn btn-primary" style="background: #3b82f6; border: none; margin-top: 10px; border-radius: 8px;">
                                                    <i class="fa fa-plus-circle"></i> Ajouter un membre
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <?php if (!empty($membres) && isset($pagination)): ?>
                                <div class="pagination-custom">
                                    <?php echo $pagination; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    function confirmerSuppression(id) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce membre ? Cette action est irréversible.')) {
            window.location.href = '<?php echo base_url(); ?>admin/tontine_membres/supprimer/' + id;
        }
    }

    // Auto-close alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            var alerts = document.querySelectorAll('.alert-custom');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            });
        }, 5000);
    });
</script>