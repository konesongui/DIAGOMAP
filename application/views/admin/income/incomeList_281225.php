<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
$language = $this->customlib->getLanguage();
$language_name = $language["short_code"];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Ajout des bibliothèques nécessaires pour PDF et Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
</head>
<body>
<!-- CSS additionnel pour améliorer l'affichage -->
<style>
    /* Ajouter dans votre section head ou fichier CSS */
    #bankTable tbody tr:hover {
        background-color: #f5f5f5;
        cursor: pointer;
    }

    .bank-name-display {
        font-weight: bold;
        color: #2c3e50;
    }

    .alert {
        margin: 15px;
        border-radius: 4px;
    }

    .modal-content {
        border-radius: 8px;
    }

    .form-group.has-error {
        border-left: 3px solid #dc3545;
        padding-left: 10px;
    }

    .form-group.has-error input {
        border-color: #dc3545;
    }

    .help-block {
        font-size: 12px;
        margin-top: 5px;
    }

    .text-danger {
        color: #dc3545;
    }
</style>
<style>
    .text-entree {
        color: #28a745;
        font-weight: 500;
    }

    .text-sortie {
        color: #dc3545;
        font-weight: 500;
    }

    .text-solde-avant {
        color: #6c757d;
        font-style: italic;
    }

    .text-solde-apres {
        font-weight: 600;
    }

    .table-livre-caisse tbody tr:hover {
        background-color: #f5f5f5;
    }

    .table-livre-caisse thead th {
        background-color: #4e73df;
        color: white;
        border-color: #4e73df;
    }

    .total-row td {
        border-top: 2px solid #dee2e6;
    }

    .solde-final-row td {
        border-top: 3px double #dee2e6;
    }

    /* Styles pour les statistiques */
    .stat-box {
        transition: all 0.3s ease;
        padding: 8px;
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 5px;
        margin: 5px 0;
    }
    .stat-box:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .stat-value {
        font-weight: bold;
        margin-bottom: 5px;
    }
    .stat-label {
        color: #e0e0e0;
    }

    /* Barre de progression */
    .progress {
        background-color: #e9ecef;
        border-radius: 10px;
        height: 8px;
        margin-bottom: 5px;
    }
    .progress-bar {
        border-radius: 10px;
    }

    /* Animation pour les montants */
    .montant-caisse {
        transition: all 0.3s ease;
        font-size: 18px;
        font-weight: bold;
        color: #2c3e50;
    }
    .montant-caisse:hover {
        transform: scale(1.05);
    }

    /* Style pour les réapprovisionnements */
    .reappro-row {
        background-color: #e8f4f8 !important;
        border-left: 4px solid #17a2b8 !important;
    }

    .reappro-row:hover {
        background-color: #d1ecf1 !important;
    }

    .text-reappro {
        color: #17a2b8 !important;
        font-weight: bold !important;
    }

    .badge-reappro {
        background-color: #17a2b8;
        color: white;
        font-size: 10px;
        padding: 2px 6px;
        border-radius: 3px;
        margin-left: 5px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .total-centralisation .row > div {
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 16px !important;
        }
    }
</style>
<style type="text/css">
    @media print {
        .no-print {
            visibility: hidden !important;
            display: none !important;
        }

        /* Cacher tout sauf le tableau */
        body * {
            visibility: hidden !important;
        }

        #livre-caisse-table, #livre-caisse-table * {
            visibility: visible !important;
        }

        #livre-caisse-table {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
            font-size: 12px !important;
        }

        /* Améliorer l'apparence pour l'impression */
        .table-livre-caisse {
            border-collapse: collapse !important;
            width: 100% !important;
        }

        .table-livre-caisse th,
        .table-livre-caisse td {
            border: 1px solid #000 !important;
            padding: 4px !important;
        }

        .table-livre-caisse th {
            background-color: #f0f0f0 !important;
            font-weight: bold !important;
        }

        /* Masquer les boutons et autres éléments */
        .box-tools, .filter-form, .modal, .caisse-card, .total-centralisation {
            display: none !important;
        }

        /* Style spécial pour les totaux dans l'impression */
        .print-totals-row {
            background-color: #e9ecef !important;
            font-weight: bold !important;
            border-top: 3px double #000 !important;
        }

        .print-solde-final {
            background-color: #d4edda !important;
            font-weight: bold !important;
            border-top: 3px double #000 !important;
            border-bottom: 3px double #000 !important;
        }
    }

    /* Styles pour le livre de caisse */
    .table-livre-caisse {
        font-size: 13px;
    }
    .table-livre-caisse th {
        background-color: #f5f5f5;
        font-weight: bold;
        text-align: center;
    }
    .text-entree {
        color: #28a745;
        font-weight: bold;
        text-align: right;
    }
    .text-sortie {
        color: #dc3545;
        font-weight: bold;
        text-align: right;
    }
    .solde-total {
        background-color: #e9ecef;
        font-weight: bold;
        font-size: 14px;
    }
    .table-totals {
        background-color: #f8f9fa;
        border-top: 2px solid #dee2e6;
    }
    .caisse-card {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
        background-color: #f9f9f9;
    }
    .caisse-active {
        border-left: 5px solid #28a745;
    }
    .caisse-inactive {
        border-left: 5px solid #dc3545;
    }
    .total-centralisation {
        background-color: #2c3e50;
        color: white;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        font-size: 18px;
        text-align: center;
    }
    .badge-solde {
        font-size: 12px;
        padding: 5px 10px;
    }
    .badge-solde-positif {
        background-color: #28a745;
        color: white;
    }
    .badge-solde-negatif {
        background-color: #dc3545;
        color: white;
    }
    .filter-form {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    /* Style pour le PDF preview */
    .pdf-preview {
        background-color: white;
        padding: 20px;
        border: 1px solid #ddd;
        margin-bottom: 20px;
    }

    .pdf-header {
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #000;
        padding-bottom: 10px;
    }

    .pdf-title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 5px;
    }

    .pdf-period {
        font-size: 14px;
        margin-bottom: 10px;
    }

    .pdf-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
    }

    .pdf-table th {
        background-color: #f2f2f2;
        border: 1px solid #000;
        padding: 5px;
        text-align: center;
        font-weight: bold;
    }

    .pdf-table td {
        border: 1px solid #000;
        padding: 5px;
    }

    .pdf-totals {
        font-weight: bold;
        background-color: #e9ecef;
        border-top: 2px solid #000 !important;
    }

    .pdf-solde-final {
        font-weight: bold;
        background-color: #d4edda;
        border-top: 2px solid #000 !important;
        border-bottom: 2px solid #000 !important;
    }

    .pdf-footer {
        text-align: center;
        margin-top: 20px;
        font-size: 10px;
        color: #666;
    }

    /* Styles pour les boutons d'export */
    .export-buttons {
        margin-top: 10px;
    }

    .export-buttons .btn {
        margin-right: 5px;
        margin-bottom: 5px;
    }

    /* Style spécial pour les totaux dans la vue */
    .total-row {
        background-color: #f8f9fa !important;
        font-weight: bold;
        border-top: 2px solid #dee2e6;
    }

    .total-entrees {
        color: #28a745;
        font-weight: bold;
    }

    .total-sorties {
        color: #dc3545;
        font-weight: bold;
    }

    .solde-final-row {
        background-color: #e9ecef !important;
        font-weight: bold;
        border-top: 2px solid #dee2e6;
        border-bottom: 2px solid #dee2e6;
    }

    /* Styles pour les boutons de caisse */
    .btn-group-xs .btn {
        padding: 1px 5px;
        font-size: 12px;
        line-height: 1.5;
        border-radius: 3px;
    }

    /* Style pour le bouton de réapprovisionnement */
    .btn-reappro {
        background-color: #17a2b8;
        border-color: #17a2b8;
        color: white;
    }

    .btn-reappro:hover {
        background-color: #138496;
        border-color: #117a8b;
        color: white;
    }

    /* Style pour le bouton voir réapprovisionnements */
    .btn-view-reappro {
        background-color: #6c757d;
        border-color: #6c757d;
        color: white;
    }

    .btn-view-reappro:hover {
        background-color: #5a6268;
        border-color: #545b62;
        color: white;
    }
</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

    <section class="content-header">
        <h1>
            <i class="fa fa-usd"></i> <?php echo $this->lang->line('income'); ?>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- Section des caisses -->
            <div class="col-md-4">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-briefcase"></i> Caisses</h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('caisse', 'can_add')) { ?>
                                <button class="btn btn-primary btn-xs" data-toggle="modal" data-target="#addCaisseModal">
                                    <i class="fa fa-plus"></i> Nouvelle caisse
                                </button>
                            <?php } ?>
                            <?php if ($this->rbac->hasPrivilege('superadmin', 'can_add')) { ?>
                                <!--  <a href="<?php echo base_url('admin/income/init_soldes_caisse'); ?>"
                                   class="btn btn-warning btn-xs"
                                   onclick="return confirm('Initialiser les soldes des caisses ?')">
                                    <i class="fa fa-refresh"></i> Init Soldes
                                </a>-->
                            <?php } ?>
                        </div>
                    </div>
                    <div class="box-body">
                        <!-- Total de centralisation amélioré -->
                        <?php
                        // Récupérer les totaux généraux depuis la base de données
                        $this->db->select('
                            SUM(amount) as total_amount,
                            SUM(amount_re) as total_amount_re,
                            SUM(total_entrees) as total_entrees_all,
                            SUM(total_sorties) as total_sorties_all,
                            COUNT(*) as nb_caisses'
                        );
                        $this->db->from('income');
                        $this->db->where('is_deleted', 'no');
                        $this->db->where('est_actif', '1');
                        $totaux_generaux = $this->db->get()->row();

                        // Récupérer le total des réapprovisionnements
                        $date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : date('Y-m-01');
                        $date_fin = isset($_GET['date_fin']) ? $_GET['date_fin'] : date('Y-m-d');

                        $this->db->select('SUM(amount) as total_reappro');
                        $this->db->from('income_processing');
                        $this->db->where('date >=', $date_debut);
                        $this->db->where('date <=', $date_fin);
                        $total_reappro_result = $this->db->get()->row();
                        $total_reappro = $total_reappro_result->total_reappro ?? 0;

                        $total_amount = $totaux_generaux->total_amount ?? 0;
                        $total_amount_re = $totaux_generaux->total_amount_re ?? 0;
                        $total_entrees_all = $totaux_generaux->total_entrees_all ?? 0;
                        $total_sorties_all = $totaux_generaux->total_sorties_all ?? 0; // CORRECTION ICI
                        $nb_caisses = $totaux_generaux->nb_caisses ?? 0;
                        ?>

                        <?php if ($total_amount_re > 0 || $total_entrees_all > 0): ?>
                            <div class="total-centralisation">
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <h4 style="margin-top: 0; margin-bottom: 10px;">
                                            <i class="fa fa-money"></i> ÉTAT GÉNÉRAL DES CAISSES
                                        </h4>
                                    </div>
                                </div>

                                <div class="row" style="margin-top: 10px;">
                                    <div class="col-md-3 text-center">
                                        <div class="stat-box">
                                            <div class="stat-value text-success" style="font-size: 18px;">
                                                <?php echo number_format($total_amount, 2, ',', ' '); ?> FCFA
                                            </div>
                                            <div class="stat-label" style="font-size: 12px;">
                                                <i class="fa fa-bank"></i> Montant Initial Total
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3 text-center">
                                        <div class="stat-box">
                                            <div class="stat-value text-info" style="font-size: 18px;">
                                                <?php echo number_format($total_entrees_all, 2, ',', ' '); ?> FCFA
                                            </div>
                                            <div class="stat-label" style="font-size: 12px;">
                                                <i class="fa fa-sign-in"></i> Total Entrées
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3 text-center">
                                        <div class="stat-box">
                                            <div class="stat-value text-warning" style="font-size: 18px;">
                                                <?php echo number_format($total_sorties_all, 2, ',', ' '); ?> FCFA
                                            </div>
                                            <div class="stat-label" style="font-size: 12px;">
                                                <i class="fa fa-sign-out"></i> Total Sorties
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3 text-center">
                                        <div class="stat-box">
                                            <div class="stat-value text-primary" style="font-size: 20px; font-weight: bold;">
                                                <?php echo number_format($total_amount_re, 2, ',', ' '); ?> FCFA
                                            </div>
                                            <div class="stat-label" style="font-size: 12px;">
                                                <i class="fa fa-calculator"></i> Solde Réel Total
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($total_reappro > 0): ?>
                                    <div class="row" style="margin-top: 10px;">
                                        <div class="col-md-12 text-center">
                                            <span class="badge" style="background-color: #17a2b8; color: white; font-size: 14px; padding: 8px 15px;">
                                                <i class="fa fa-refresh"></i> Total Réappro: <?php echo number_format($total_reappro, 2, ',', ' '); ?> FCFA
                                            </span>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="row" style="margin-top: 15px; font-size: 11px;">
                                    <div class="col-md-12 text-center">
                                        <span class="badge badge-info"><?php echo $nb_caisses; ?> Caisses actives</span>
                                        <span class="badge badge-success"><?php echo number_format($total_entrees_all - $total_sorties_all, 2, ',', ' '); ?> FCFA Net</span>
                                        <span class="badge badge-light">Dernière mise à jour: <?php echo date('d/m/Y H:i'); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Liste des caisses -->
                        <?php if (!empty($caisses)): ?>
                            <?php foreach ($caisses as $caisse): ?>
                                <?php
                                // Récupérer les informations complètes de la caisse
                                $this->db->select('amount, amount_re, total_entrees, total_sorties, solde_initial, last_operation_date');
                                $this->db->from('income');
                                $this->db->where('id', $caisse['id']);
                                $caisse_details = $this->db->get()->row();

                                // Calculer le pourcentage d'utilisation
                                $amount = floatval($caisse_details->amount ?? $caisse['amount']);
                                $amount_re = floatval($caisse_details->amount_re ?? 0);
                                $total_entrees = floatval($caisse_details->total_entrees ?? 0);
                                $total_sorties = floatval($caisse_details->total_sorties ?? 0);
                                $last_operation_date = $caisse_details->last_operation_date ?? null;

                                $pourcentage_utilisation = $amount > 0 ? (($total_entrees - $total_sorties) / $amount) * 100 : 0;
                                $pourcentage_utilisation = min(100, max(0, $pourcentage_utilisation));

                                // Déterminer la couleur selon le pourcentage
                                $progress_color = 'bg-success';
                                if ($pourcentage_utilisation > 80) {
                                    $progress_color = 'bg-danger';
                                } elseif ($pourcentage_utilisation > 60) {
                                    $progress_color = 'bg-warning';
                                }
                                ?>
                                <div class="caisse-card <?php echo $caisse['est_actif'] == '1' ? 'caisse-active' : 'caisse-inactive'; ?>">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <h4 style="margin-top: 0; margin-bottom: 5px;">
                                                <strong><?php echo htmlspecialchars($caisse['name']); ?></strong>
                                                <?php if ($caisse['est_actif'] == '1'): ?>
                                                    <span class="label label-success" style="font-size: 10px;">ACTIVE</span>
                                                <?php else: ?>
                                                    <span class="label label-danger" style="font-size: 10px;">INACTIVE</span>
                                                <?php endif; ?>
                                            </h4>

                                            <!-- Informations détaillées -->
                                            <div style="font-size: 11px; color: #666;">
                                                <div class="row">
                                                    <div class="col-xs-6">
                                                        <i class="fa fa-calendar"></i> Créée: <?php echo date('d/m/Y', strtotime($caisse['date'])); ?>
                                                    </div>
                                                    <div class="col-xs-6">
                                                        <i class="fa fa-money"></i> Initial: <?php echo number_format($amount, 2, ',', ' '); ?> FCFA
                                                    </div>
                                                </div>

                                                <?php if ($last_operation_date): ?>
                                                    <div class="row" style="margin-top: 3px;">
                                                        <div class="col-xs-12">
                                                            <i class="fa fa-clock-o"></i> Dernière opération: <?php echo date('d/m/Y H:i', strtotime($last_operation_date)); ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- Barre de progression -->
                                                <div class="row" style="margin-top: 8px;">
                                                    <div class="col-xs-12">
                                                        <div class="progress">
                                                            <div class="progress-bar <?php echo $progress_color; ?>"
                                                                 role="progressbar"
                                                                 style="width: <?php echo $pourcentage_utilisation; ?>%"
                                                                 aria-valuenow="<?php echo $pourcentage_utilisation; ?>"
                                                                 aria-valuemin="0"
                                                                 aria-valuemax="100">
                                                            </div>
                                                        </div>
                                                        <small>Utilisation: <?php echo number_format($pourcentage_utilisation, 1); ?>%</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 text-right">
                                            <!-- Solde actuel -->
                                            <div class="montant-caisse <?php echo $amount_re >= 0 ? 'text-success' : 'text-danger'; ?>">
                                                <?php echo number_format($amount_re, 2, ',', ' '); ?> FCFA
                                            </div>

                                            <!-- Badge avec détails -->
                                            <div style="margin-top: 5px;">
                                                <span class="badge badge-success" style="font-size: 10px; margin-bottom: 2px;">
                                                    <i class="fa fa-plus"></i> <?php echo number_format($total_entrees, 0, ',', ' '); ?> FCFA
                                                </span>
                                                <span class="badge badge-danger" style="font-size: 10px; margin-bottom: 2px;">
                                                    <i class="fa fa-minus"></i> <?php echo number_format($total_sorties, 0, ',', ' '); ?> FCFA
                                                </span>
                                                <span class="badge badge-solde <?php echo $amount_re >= 0 ? 'badge-solde-positif' : 'badge-solde-negatif'; ?>">
                                                    <?php echo $amount_re >= 0 ? '+' : ''; ?>
                                                    <?php echo number_format($amount_re, 0, ',', ' '); ?> FCFA
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-top: 10px;">
                                        <div class="col-md-12">
                                            <div class="btn-group btn-group-xs">
                                                <a href="<?php echo base_url('admin/income?caisse_id=' . $caisse['id']); ?>"
                                                   class="btn btn-info">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <?php if ($this->rbac->hasPrivilege('caisse', 'can_edit')): ?>
                                                    <a href="<?php echo base_url('admin/income/edit/' . $caisse['id']); ?>"
                                                       class="btn btn-warning">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                <?php endif; ?>
                                                <?php if ($this->rbac->hasPrivilege('caisse', 'can_delete')): ?>
                                                    <a href="<?php echo base_url('admin/income/delete/' . $caisse['id']); ?>"
                                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette caisse ?')"
                                                       class="btn btn-danger">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                <?php endif; ?>

                                                <!-- BOUTON RÉAPPROVISIONNEMENT -->
                                                <?php if ($this->rbac->hasPrivilege('superadmin')): ?>
                                                    <button class="btn btn-reappro increaseAmount"
                                                            data-row-id="<?php echo $caisse['id']; ?>"
                                                            title="Réappro">
                                                        <i class="fa fa-plus"></i>
                                                    </button>

                                                    <button class="btn btn-view-reappro viewIncrease"
                                                            data-row-id="<?php echo $caisse['id']; ?>"
                                                            title="Voir réappro">
                                                        <i class="fa fa-list"></i>
                                                    </button>
                                                <?php endif; ?>

                                                <button class="btn btn-primary toggle-status"
                                                        data-id="<?php echo $caisse['id']; ?>"
                                                        data-status="<?php echo $caisse['est_actif']; ?>">
                                                    <?php if ($caisse['est_actif'] == '1'): ?>
                                                        <i class="fa fa-pause"></i>
                                                    <?php else: ?>
                                                        <i class="fa fa-play"></i>
                                                    <?php endif; ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> Aucune caisse créée. Créez votre première caisse.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Section du livre de caisse -->
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header ptbnull">
                        <h3 class="box-title">
                            <?php if (isset($caisse_selectionnee)): ?>
                                Livre de Caisse: <strong><?php echo htmlspecialchars($caisse_selectionnee['name']); ?></strong>
                            <?php else: ?>
                                Livre de Caisse - Toutes les caisses
                            <?php endif; ?>
                        </h3>
                        <div class="box-tools pull-right">
                            <?php if ($this->rbac->hasPrivilege('caisse', 'can_add')) { ?>
                                <button class="btn btn-primary" data-toggle="modal" data-target="#addOperationModal">
                                    <i class="fa fa-plus"></i> Nouvelle opération
                                </button>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- Formulaire de filtre -->
                    <div class="box-body">
                        <div class="filter-form">
                            <form method="get" action="<?php echo base_url('admin/income') ?>" class="form-inline">
                                <div class="form-group">
                                    <label for="caisse_id">Caisse: </label>
                                    <select name="caisse_id" id="caisse_id" class="form-control input-sm" style="width: 200px;">
                                        <option value="">Toutes les caisses</option>
                                        <?php if (!empty($caisses)): ?>
                                            <?php foreach ($caisses as $caisse): ?>
                                                <option value="<?php echo $caisse['id']; ?>"
                                                    <?php echo isset($_GET['caisse_id']) && $_GET['caisse_id'] == $caisse['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($caisse['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="form-group" style="margin-left: 15px;">
                                    <label for="date_debut">Du: </label>
                                    <input type="date" name="date_debut" id="date_debut" class="form-control input-sm"
                                           value="<?php echo isset($_GET['date_debut']) ? $_GET['date_debut'] : date('Y-m-01'); ?>">
                                </div>

                                <div class="form-group" style="margin-left: 15px;">
                                    <label for="date_fin">Au: </label>
                                    <input type="date" name="date_fin" id="date_fin" class="form-control input-sm"
                                           value="<?php echo isset($_GET['date_fin']) ? $_GET['date_fin'] : date('Y-m-d'); ?>">
                                </div>


                                <div class="form-group" style="margin-left: 15px;">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fa fa-filter"></i> Filtrer
                                    </button>
                                    <a href="<?php echo base_url('admin/income'); ?>" class="btn btn-default btn-sm">
                                        <i class="fa fa-refresh"></i> Actualisé
                                    </a>
                                    <div class="export-buttons" style="display: inline-block;">
                                        <button type="button" class="btn btn-success btn-sm" onclick="imprimerLivreCaisse()">
                                            <i class="fa fa-print"></i> Imprimer
                                        </button>
                                       <!-- <button type="button" class="btn btn-danger btn-sm" onclick="genererPDF()">
                                            <i class="fa fa-file-pdf-o"></i> PDF
                                        </button>-->
                                        <button type="button" class="btn btn-info btn-sm" onclick="exporterExcel()">
                                            <i class="fa fa-file-excel-o"></i> Excel
                                        </button>
                                        <button type="button" class="btn btn-warning btn-sm" onclick="previewPDF()">
                                            <i class="fa fa-eye"></i> Aperçu
                                        </button>
                                    </div>
                                    <div class="filter-buttons" style="display: inline-block; margin-left: 10px;">
                                        <button type="button" class="btn btn-sm btn-info" onclick="filterReappro()">
                                            <i class="fa fa-filter"></i> Voir Réappro
                                        </button>
                                        <button type="button" class="btn btn-sm btn-secondary" onclick="showAllOperations()">
                                            <i class="fa fa-eye"></i> Tout voir
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="box-body">
                        <?php if ($this->session->flashdata('msg')) { ?>
                            <?php echo $this->session->flashdata('msg') ?>
                        <?php } ?>
                        <?php if (isset($error_message)) { echo "<div class='alert alert-danger'>" . $error_message . "</div>"; } ?>

                        <div class="table-responsive mailbox-messages">
                            <table class="table table-striped table-bordered table-hover table-livre-caisse" id="livre-caisse-table">
                                <thead>
                                <tr>
                                    <th width="10%">RÉFÉRENCE</th>
                                    <th width="10%">DATE</th>
                                    <th width="20%">DÉSIGNATIONS</th>
                                    <th width="8%">CAT</th>
                                    <th width="10%">User</th>
                                    <th width="10%">ENTRÉE</th>
                                    <th width="10%">SORTIE</th>
                                    <th width="12%">SOLDE AVANT</th>
                                    <th width="12%">SOLDE APRÈS</th>
                                    <th width="8%" class="no-print">ACTIONS</th> <!-- NOUVELLE COLONNE -->
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                // Variables pour les totaux
                                $total_entrees = 0;
                                $total_sorties = 0;
                                $solde_final = 0;

                                // Afficher le solde initial si disponible
                                if (isset($solde_initial) && $solde_initial != 0) {
                                    ?>
                                    <tr style="background-color: #f8f9fa; font-weight: bold;">
                                        <td colspan="7" class="text-right"><strong>SOLDE INITIAL:</strong></td>
                                        <td class="text-center" style="background-color: #e8f4fd;">
                                            <?php echo number_format($solde_initial, 2, ',', ' '); ?> FCFA
                                        </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <?php
                                    $solde_courant = $solde_initial;
                                } else {
                                    $solde_courant = 0;
                                }

                                // Afficher les opérations
                                if (!empty($operations)) {
                                    foreach ($operations as $operation) {
                                        $entree = floatval($operation['entree']);
                                        $sortie = floatval($operation['sortie']);
                                        $solde_avant = floatval($operation['solde_avant_operation'] ?? 0);
                                        $solde_apres = floatval($operation['solde_apres_operation'] ?? 0);

                                        // Déterminer le type d'opération
                                        $is_reappro = ($operation['operation_type'] ?? '') === 'reappro' ||
                                            strpos($operation['reference'] ?? '', 'REAPP-') === 0;

                                        // Pour les réapprovisionnements, ajuster l'affichage des soldes
                                        if ($is_reappro) {
                                            // Pour les réapprovisionnements, on peut afficher des soldes spécifiques
                                            $montant_reappro = floatval($operation['montant_reappro'] ?? $entree);
                                        }

                                        // Si les soldes ne sont pas dans la base, les calculer
                                        if ($solde_avant == 0 && $solde_apres == 0) {
                                            $solde_avant = $solde_courant;
                                            $solde_apres = $solde_courant + $entree - $sortie;
                                        }

                                        $solde_courant = $solde_apres; // Mettre à jour pour la prochaine ligne
                                        ?>
                                        <tr class="<?php echo $is_reappro ? 'reappro-row' : ''; ?>"
                                            data-operation-type="<?php echo $is_reappro ? 'reappro' : 'normal'; ?>"
                                            data-reference="<?php echo htmlspecialchars($operation['reference'] ?? ''); ?>"
                                            data-date="<?php echo !empty($operation['date']) ? date('d-m-Y', strtotime($operation['date'])) : ''; ?>"
                                            data-designation="<?php echo htmlspecialchars($operation['designation'] ?? ''); ?>"
                                            data-category="<?php echo htmlspecialchars($operation['category'] ?? $operation['category_name'] ?? ''); ?>"
                                            data-user="<?php echo htmlspecialchars($operation['user'] ?? $operation['user_name'] ?? 'Système'); ?>"
                                            data-entree="<?php echo $entree; ?>"
                                            data-sortie="<?php echo $sortie; ?>"
                                            data-solde-avant="<?php echo $solde_avant; ?>"
                                            data-solde-apres="<?php echo $solde_apres; ?>"
                                            data-caisse="<?php echo htmlspecialchars($operation['caisse_nom'] ?? ''); ?>">
                                            <td>
                                                <?php echo htmlspecialchars($operation['reference'] ?? 'N/A'); ?>
                                                <?php if ($is_reappro): ?>
                                                    <span class="badge badge-reappro">REAPP</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo !empty($operation['date']) ? date('d-m-Y', strtotime($operation['date'])) : 'N/A'; ?></td>
                                            <td>
                                                <div class="operation-designation">
                                                    <?php echo htmlspecialchars($operation['designation'] ?? ''); ?>
                                                    <?php if (!empty($operation['caisse_nom'])): ?>
                                                        <br>
                                                        <small class="text-muted caisse-info">
                                                            <i class="fa fa-briefcase"></i> Caisse: <?php echo htmlspecialchars($operation['caisse_nom']); ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="operation-category">
                                                    <?php echo htmlspecialchars($operation['category'] ?? $operation['category_name'] ?? ''); ?>
                                                    <?php if ($is_reappro): ?>
                                                        <br><small class="text-reappro"><i class="fa fa-refresh"></i> Réappro</small>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="operation-user">
                                                    <?php echo htmlspecialchars($operation['user'] ?? $operation['user_name'] ?? 'Système'); ?>
                                                </div>
                                            </td>
                                            <td class="text-entree <?php echo $is_reappro ? 'text-reappro' : ''; ?>" style="text-align: right;">
                                                <?php if ($entree > 0): ?>
                                                    <div class="montant-entree">
                                                        <?php echo number_format($entree, 0, ',', ' '); ?>
                                                        <?php if ($is_reappro): ?>
                                                            <!--<br><small class="text-reappro"><i class="fa fa-plus-circle"></i> Réappro</small>-->
                                                        <?php endif; ?>
                                                    </div>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-sortie" style="text-align: right;">
                                                <?php if ($sortie > 0): ?>
                                                    <div class="montant-sortie">
                                                        <?php echo number_format($sortie, 0, ',', ' '); ?>
                                                    </div>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-solde-avant" style="text-align: right; background-color: #f8f9fa;">
                                                <div class="solde-avant">
                                                    <?php echo number_format($solde_avant, 0, ',', ' '); ?>
                                                </div>
                                            </td>
                                            <td class="text-solde-apres" style="text-align: right; font-weight: bold;
                                                    background-color: <?php echo $solde_apres >= 0 ? '#e8f5e8' : '#ffe8e8'; ?>;">
                                                <div class="solde-apres">
                    <span class="<?php echo $solde_apres >= 0 ? 'text-success' : 'text-danger'; ?>">
                        <?php echo number_format($solde_apres, 0, ',', ' '); ?>
                    </span>
                                                    <?php if ($is_reappro): ?>
                                                        <!-- <br><small class="text-reappro">
                                                            <i class="fa fa-arrow-up"></i>
                                                            <?php echo number_format($entree, 0, ',', ' '); ?> FCFA ajouté
                                                        </small>-->
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td class="no-print text-center">
                                                <button class="btn btn-xs btn-info print-operation-btn"
                                                        title="Imprimer cette ligne"
                                                        onclick="printOperation(this)">
                                                    <i class="fa fa-print"></i>
                                                </button>
                                                <!--<button class="btn btn-xs btn-warning edit-operation-btn"
                                                        title="Modifier"
                                                        onclick="editOperation(this)">
                                                    <i class="fa fa-edit"></i>
                                                </button>-->
                                            </td>
                                        </tr>
                                        <?php
                                        // Pour les totaux, inclure les réapprovisionnements dans les entrées
                                        $total_entrees += $entree;
                                        $total_sorties += $sortie;
                                        $solde_final = $solde_apres;
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="10" class="text-center">
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle"></i> Aucune opération trouvée pour cette période.
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                                <tfoot>
                                <tr class="total-row" style="background-color: #f0f0f0;">
                                    <td colspan="5" class="text-right"><strong>TOTAUX:</strong></td>
                                    <td class="text-entree" style="text-align: right; font-weight: bold; color: #28a745;">
                                        <?php echo number_format($total_entrees, 2, ',', ' '); ?> FCFA
                                    </td>
                                    <td class="text-sortie" style="text-align: right; font-weight: bold; color: #dc3545;">
                                        <?php echo number_format($total_sorties, 2, ',', ' '); ?> FCFA
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <!--<tr class="solde-final-row" style="background-color: #e9ecef;">
                                    <td colspan="5" class="text-right"><strong>SOLDE FINAL:</strong></td>
                                    <td colspan="2" class="text-center">
                                        <small>
                                            Entrées: <span class="text-success"><?php echo number_format($total_entrees, 2, ',', ' '); ?> FCFA</span> |
                                            Sorties: <span class="text-danger"><?php echo number_format($total_sorties, 2, ',', ' '); ?> FCFA</span>
                                        </small>
                                    </td>
                                    <td style="text-align: right; font-weight: bold; background-color: #d1ecf1;">
                                        <?php
                                        // Solde avant la première opération
                                        $solde_initial_affiche = isset($solde_initial) ? $solde_initial : 0;
                                        echo number_format($solde_initial_affiche, 2, ',', ' '); ?> FCFA
                                    </td>
                                    <td style="text-align: right; font-weight: bold;
                                            background-color: <?php echo $solde_final >= 0 ? '#d4edda' : '#f8d7da'; ?>;">
                    <span class="<?php echo $solde_final >= 0 ? 'text-success' : 'text-danger'; ?>">
                        <?php echo number_format($solde_final, 2, ',', ' '); ?> FCFA
                    </span>
                                    </td>
                                    <td></td>
                                </tr>-->
                               <!-- <tr style="background-color: #f8f9fa;">
                                    <td colspan="10" class="text-center" style="padding: 8px;">
                                        <small>
                                            <strong>RÉCAPITULATIF:</strong>
                                            Total Entrées: <span class="text-success"><?php echo number_format($total_entrees, 2, ',', ' '); ?> FCFA</span> |
                                            Total Sorties: <span class="text-danger"><?php echo number_format($total_sorties, 2, ',', ' '); ?> FCFA</span> |
                                            Solde Initial: <span class="text-info"><?php echo number_format($solde_initial_affiche, 2, ',', ' '); ?> FCFA</span> |
                                            Solde Final: <span class="<?php echo $solde_final >= 0 ? 'text-success' : 'text-danger'; ?>"><?php echo number_format($solde_final, 2, ',', ' '); ?> FCFA</span>
                                            <?php if ($total_reappro > 0): ?>
                                                | <span class="text-reappro">Réapprovisionnements: <?php echo number_format($total_reappro, 2, ',', ' '); ?> FCFA</span>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                </tr>-->
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal pour nouvelle caisse -->
<div class="modal fade" id="addCaisseModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter une caisse</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formCaisse" action="<?php echo base_url() ?>admin/income/create" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <?php if ($this->session->flashdata('msg')) { ?>
                        <?php echo $this->session->flashdata('msg') ?>
                    <?php } ?>
                    <?php if (isset($error_message)) { echo "<div class='alert alert-danger'>" . $error_message . "</div>"; } ?>
                    <?php echo $this->customlib->getCSRF(); ?>

                    <!-- Tous les champs originaux gardés -->
                    <div class="form-group" hidden>
                        <label for="inc_head_id"><?php echo $this->lang->line('income_head'); ?></label><small class="req"> *</small>
                        <select id="inc_head_id" name="inc_head_id" class="form-control">
                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                            <?php foreach ($incheadlist as $inchead) { ?>
                                <option value="<?php echo $inchead['id'] ?>"<?php if (set_value('inc_head_id') == $inchead['id']) { echo "selected"; } ?>>
                                    <?php echo $inchead['income_category'] ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group" hidden>
                        <label>User</label>
                        <input id="user" name="user" type="text" class="form-control"
                               value="<?php echo $this->customlib->getAdminSessionUserName(); ?>" />
                    </div>

                    <div class="form-group">
                        <label for="name"><?php echo $this->lang->line('name'); ?><small class="req"> *</small></label>
                        <input id="name" name="name" type="text" class="form-control"
                               value="<?php echo set_value('name'); ?>" />
                    </div>

                    <div class="form-group" hidden>
                        <label><?php echo $this->lang->line('invoice_no'); ?></label>
                        <input id="invoice_no" name="invoice_no" type="text" class="form-control"
                               value="<?php echo set_value('invoice_no'); ?>" />
                    </div>

                    <div class="form-group">
                        <label for="date"><?php echo $this->lang->line('date'); ?><small class="req"> *</small></label>
                        <input id="date" name="date" type="text" class="form-control date"
                               value="<?php echo set_value('date', date($this->customlib->getSchoolDateFormat())); ?>" readonly="readonly" />
                    </div>

                    <div class="form-group">
                        <label for="amount"><?php echo $this->lang->line('amount'); ?><small class="req"> *</small></label>
                        <input id="amount" name="amount" type="number" class="form-control"
                               value="<?php echo set_value('amount'); ?>" />
                    </div>

                    <div class="form-group">
                        <label for="description">Description (Optionnel)</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo set_value('description'); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="est_actif">Statut</label>
                        <select id="est_actif" name="est_actif" class="form-control">
                            <option value="1" <?php echo set_value('est_actif') == '1' ? 'selected' : ''; ?>>Active</option>
                            <option value="0" <?php echo set_value('est_actif') == '0' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="form-group" hidden>
                        <label><?php echo $this->lang->line('attach_document'); ?></label>
                        <input id="documents" name="documents" type="file" class="filestyle form-control"
                               data-height="40" value="<?php echo set_value('documents'); ?>" />
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary"><?php echo $this->lang->line('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour nouvelle opération -->
<div class="modal fade" id="addOperationModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle Opération</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formOperation" action="<?php echo base_url('admin/income/create_operation') ?>" method="post">
                <div class="modal-body">
                    <?php echo $this->customlib->getCSRF(); ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="caisse_id">Caisse *</label>
                                <select class="form-control" id="caisse_id" name="caisse_id" required>
                                    <option value="">Sélectionner une caisse</option>
                                    <?php if (!empty($caisses)): ?>
                                        <?php foreach ($caisses as $caisse): ?>
                                            <?php if ($caisse['est_actif'] == '1'): ?>
                                                <option value="<?php echo $caisse['id']; ?>">
                                                    <?php echo htmlspecialchars($caisse['name']); ?>
                                                </option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date">Date *</label>
                                <input type="date" class="form-control" id="date" name="date"
                                       value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="type">Type d'opération *</label>
                        <select class="form-control" id="type" name="type" required>
                            <option value="entree">Entrée (Recette)</option>
                            <option value="sortie">Sortie (Dépense)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="exampleInputEmail1"><?php echo $this->lang->line('expense_head'); ?></label> <small class="req">*</small>


                        <select id="exp_head_id" name="exp_head_id" class="form-control" required>
                            <option value=""><?php echo $this->lang->line('select'); ?></option>
                            <?php foreach ($expheadlist as $exphead): ?>
                                <option
                                        value="<?php echo $exphead['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($exphead['exp_category'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php echo htmlspecialchars($exphead['exp_category'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="exp_category_name" id="exp_category_name">
                        <span class="text-danger"><?php echo form_error('exp_head_id'); ?></span>
                    </div>

                    <div class="form-group">
                        <label for="designation">Désignation *</label>
                        <textarea class="form-control" id="designation" name="designation" rows="3" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="montant">Montant *</label>
                                <input type="number" class="form-control" id="montant" name="montant"
                                       step="0.01" min="0" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="reference">Référence</label>
                                <input type="text" class="form-control" id="reference" name="reference"
                                       placeholder="Ex: RECU-001, FACT-001">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="mode_paiement">Mode de paiement</label>
                        <select class="form-control" id="mode_paiement" name="mode_paiement">
                            <option value="espèces">Espèces</option>
                            <option value="chèque">Chèque</option>
                            <option value="virement">Virement</option>
                            <option value="carte">Carte bancaire</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Enregistrer l'opération</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL POUR RÉAPPROVISIONNEMENT -->
<div class="modal fade" id="increaseForm" tabindex="-1" role="dialog" aria-labelledby="increaseFormLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="increaseFormLabel">Réapprovisionner la caisse</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="increaseFormID" method="post">
                <div class="modal-body">
                    <div id="increaseFormContent">
                        <!-- Le contenu du formulaire sera chargé ici par AJAX -->
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Chargement...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    <button type="submit" id="submitBTN" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL POUR VOIR LES RÉAPPROVISIONNEMENTS -->
<div class="modal fade" id="viewIncreaseList" tabindex="-1" role="dialog" aria-labelledby="viewIncreaseListLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewIncreaseListLabel">Historique des réapprovisionnements</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="ViewIncreaseContent">
                    <!-- Le contenu de la liste sera chargé ici par AJAX -->
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Chargement...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour prévisualisation PDF -->
<div class="modal fade" id="pdfPreviewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Aperçu PDF - Livre de Caisse</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                <!-- Le contenu PDF sera inséré ici -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" onclick="genererPDF()">
                    <i class="fa fa-download"></i> Télécharger PDF
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    ( function ( $ ) {
        'use strict';
        $(document).ready(function () {
            initDatatable('income-list','admin/income/getincomelist',[],[],10);

            // Initialiser les événements pour les boutons de réapprovisionnement
            initReapproButtons();
        });
    } ( jQuery ) )

    var base_url = '<?php echo base_url() ?>';

    // Fonction pour initialiser les événements des boutons de réapprovisionnement
    function initReapproButtons() {
        // Fonction pour charger le formulaire de réapprovisionnement
        $(document).on('click', '.increaseAmount', function(e) {
            e.preventDefault();
            var rowID = $(this).attr('data-row-id');
            console.log('Chargement formulaire réappro pour caisse ID:', rowID);

            $.ajax({
                url: base_url + 'admin/income/formIncrease',
                type: "POST",
                data: {
                    'rowID': rowID,
                },
                success: function(data) {
                    console.log('Données reçues:', data);
                    if(data) {
                        $('#increaseFormContent').html(data);
                        $('#increaseForm').modal('show');
                    } else {
                        alert('Erreur lors du chargement du formulaire');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erreur AJAX:', error);
                    alert('Erreur lors du chargement du formulaire: ' + error);
                }
            });
        });

        // Fonction pour voir l'historique des réapprovisionnements
        $(document).on('click', '.viewIncrease', function(e) {
            e.preventDefault();
            var rowID = $(this).attr('data-row-id');
            console.log('Chargement historique réappro pour caisse ID:', rowID);

            $.ajax({
                url: base_url + 'admin/income/listIncrease',
                type: "POST",
                data: {
                    'rowID': rowID,
                },
                success: function(data) {
                    console.log('Données historique reçues:', data);
                    if(data) {
                        $('#ViewIncreaseContent').html(data);
                        $('#viewIncreaseList').modal('show');
                    } else {
                        alert('Erreur lors du chargement de l\'historique');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erreur AJAX:', error);
                    alert('Erreur lors du chargement de l\'historique: ' + error);
                }
            });
        });
    }

    /*
    ALL ACTIONS BUTTONS ABOUT PERMISSIONS DATATABLE
    */
    // Function to set a increase
    function form_increase(id) {
        $.ajax({
            'url'   : base_url + 'Income/form_increase', // controller link
            'type'  : 'GET', // method used to send data
            'data'  : {
                'id'        : id, // row id
            },
            'success': function (data) {
                var increase_form_content = $('#increase_form_content');
                if (data) {
                    increase_form_content.html(data);
                }
            }
        });
    }

    /**
     * PROCESS CLICK FORM
     * On click on the 'submit' button
     */
    $(document).on("click", `#submitBTN`, function (e) {
        e.preventDefault();
        initPostAjaxRequest();
    });

    // Function to post the form data to the server
    let initPostAjaxRequest = () => {
        var formElement = $('#increaseFormID'),
            formData = new FormData(formElement[0]);

        $.ajax({
            type: "POST",
            url: base_url + 'admin/income/setIncrease',
            processData: false,
            contentType: false,
            data: formData,
            success: function(data) {
                let serverResponse = JSON.parse(data);

                if(serverResponse.type === 'success') {
                    $(`#increaseForm`).modal("hide");
                    toastr.success(serverResponse.message);
                    // Recharger la page pour voir les changements
                    location.reload(true);
                } else if(serverResponse.type === 'warning') {
                    toastr.warning(serverResponse.message);
                } else {
                    toastr.error(serverResponse.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Erreur lors de l\'envoi du formulaire:', error);
                toastr.error('Erreur lors de l\'envoi du formulaire: ' + error);
            }
        });
    }

    $(document).on('click', '.toggle-status', function () {
        const caisseId = $(this).data('id');
        const currentStatus = $(this).data('status');

        if (confirm("Changer le statut de la caisse ?")) {
            $.ajax({
                url: "<?php echo site_url('admin/income/toggle_status'); ?>",
                type: "POST",
                dataType: "json",
                data: {
                    id: caisseId,
                    status: currentStatus
                },
                success: function (res) {
                    alert(res.message);
                    location.reload();
                },
                error: function () {
                    alert("Erreur serveur.");
                }
            });
        }
    });

    // ==============================================
    // FONCTIONS D'EXPORT ET D'IMPRESSION AMÉLIORÉES
    // ==============================================

    // Fonction pour filtrer les réapprovisionnements
    function filterReappro() {
        var table = $('#livre-caisse-table').DataTable();
        if (typeof table !== 'undefined') {
            table.search('REAPP-').draw();
        } else {
            // Si DataTable n'est pas initialisé, faire un filtrage manuel
            $('tr:not(.reappro-row)').hide();
            $('.reappro-row').show();
        }
    }

    // Fonction pour afficher tout
    function showAllOperations() {
        var table = $('#livre-caisse-table').DataTable();
        if (typeof table !== 'undefined') {
            table.search('').columns().search('').draw();
        } else {
            // Si DataTable n'est pas initialisé, afficher tout
            $('tr').show();
        }
    }

    // Fonction d'impression améliorée avec totaux embelli
    function imprimerLivreCaisse() {
        // Créer une nouvelle fenêtre pour l'impression
        var printWindow = window.open('', '_blank');
        var title = "LIVRE DE CAISSE";
        var periode = "";

        // Récupérer les dates du filtre
        var dateDebut = document.getElementById('date_debut').value;
        var dateFin = document.getElementById('date_fin').value;

        if (dateDebut && dateFin) {
            periode = "Période du " + formatDate(dateDebut) + " au " + formatDate(dateFin);
        }

        // Récupérer le nom de la caisse si sélectionnée
        var caisseSelect = document.getElementById('caisse_id');
        var caisseNom = "";
        if (caisseSelect && caisseSelect.value) {
            caisseNom = caisseSelect.options[caisseSelect.selectedIndex].text;
        }

        // Récupérer les totaux depuis le tableau
        var totalEntrees = document.querySelector('.total-entrees') ? document.querySelector('.total-entrees').innerText : "0 FCFA";
        var totalSorties = document.querySelector('.total-sorties') ? document.querySelector('.total-sorties').innerText : "0 FCFA";
        var soldeFinalElement = document.querySelector('.solde-final-row strong span');
        var soldeFinal = soldeFinalElement ? soldeFinalElement.innerText : "0 FCFA";
        var soldeClass = soldeFinalElement ? soldeFinalElement.className : "text-success";

        // Récupérer le total de centralisation depuis PHP
        var totalAmount = <?php echo $total_amount; ?>;
        var totalAmountRe = <?php echo $total_amount_re; ?>;
        var totalEntreesAll = <?php echo $total_entrees_all; ?>;
        var totalSortiesAll = <?php echo $total_sorties_all; ?>;
        var nbCaisses = <?php echo $nb_caisses; ?>;
        var totalReappro = <?php echo $total_reappro; ?>;

        // HTML pour l'impression
        var printContent = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>${title}</title>
                <style>
                    @page {
                        size: A4 landscape;
                        margin: 10mm;
                    }
                    body {
                        font-family: Arial, sans-serif;
                        font-size: 11px;
                        margin: 0;
                        padding: 0;
                    }
                    .print-header {
                        text-align: center;
                        margin-bottom: 20px;
                        border-bottom: 2px solid #000;
                        padding-bottom: 10px;
                    }
                    .print-title {
                        font-size: 18px;
                        font-weight: bold;
                        margin-bottom: 5px;
                    }
                    .print-subtitle {
                        font-size: 14px;
                        margin-bottom: 5px;
                    }
                    .print-period {
                        font-size: 12px;
                        margin-bottom: 10px;
                    }
                    .print-table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-top: 10px;
                    }
                    .print-table th {
                        background-color: #f2f2f2;
                        border: 1px solid #000;
                        padding: 6px;
                        text-align: center;
                        font-weight: bold;
                    }
                    .print-table td {
                        border: 1px solid #000;
                        padding: 5px;
                    }
                    .print-table .text-entree {
                        color: #28a745;
                        font-weight: bold;
                        text-align: right;
                    }
                    .print-table .text-sortie {
                        color: #dc3545;
                        font-weight: bold;
                        text-align: right;
                    }
                    .print-totals-row {
                        font-weight: bold;
                        background-color: #e9ecef !important;
                        border-top: 3px double #000 !important;
                    }
                    .print-totals-row td {
                        font-weight: bold;
                    }
                    .print-solde-final {
                        font-weight: bold;
                        background-color: #d4edda !important;
                        border-top: 3px double #000 !important;
                        border-bottom: 3px double #000 !important;
                    }
                    .print-summary {
                        background-color: #f0f8ff;
                        text-align: center;
                        padding: 8px;
                        margin-top: 10px;
                        border: 1px solid #000;
                        font-weight: bold;
                    }
                    .print-summary .entrees {
                        color: #28a745;
                    }
                    .print-summary .sorties {
                        color: #dc3545;
                    }
                    .print-centralisation {
                        background-color: #2c3e50;
                        color: white;
                        text-align: center;
                        padding: 10px;
                        margin: 10px 0;
                        border-radius: 5px;
                        font-weight: bold;
                    }
                    .print-footer {
                        text-align: center;
                        margin-top: 20px;
                        font-size: 10px;
                        color: #666;
                        border-top: 1px solid #000;
                        padding-top: 10px;
                    }
                    .reappro-print {
                        background-color: #e8f4f8 !important;
                        border-left: 3px solid #17a2b8 !important;
                    }
                    .no-print {
                        display: none;
                    }
                </style>
            </head>
            <body>
                <div class="print-header">
                    <div class="print-title">${title}</div>
                    ${caisseNom ? `<div class="print-subtitle">Caisse: ${caisseNom}</div>` : ''}
                    ${periode ? `<div class="print-period">${periode}</div>` : ''}
                    <div class="print-period">Date d'édition: ${new Date().toLocaleDateString('fr-FR')}</div>
                </div>
        `;

        // Ajouter le total de centralisation si disponible
        if (totalAmountRe > 0) {
            printContent += `
                <div class="print-centralisation">
                    <strong>ÉTAT GÉNÉRAL DES CAISSES (${nbCaisses} caisses)</strong><br>
                    <div style="margin-top: 5px; font-size: 14px;">
                        Montant Initial: ${formatNumber(totalAmount)} FCFA |
                        Total Entrées: ${formatNumber(totalEntreesAll)} FCFA |
                        Total Sorties: ${formatNumber(totalSortiesAll)} FCFA |
                        <strong>Solde Réel: ${formatNumber(totalAmountRe)} FCFA</strong>
                    </div>
                    ${totalReappro > 0 ? `<div style="margin-top: 5px; font-size: 12px; color: #17a2b8;">
                        <i class="fa fa-refresh"></i> Réapprovisionnements: ${formatNumber(totalReappro)} FCFA
                    </div>` : ''}
                </div>
            `;
        }

        // Copier le tableau sans le tfoot (on va le recréer)
        var table = document.getElementById('livre-caisse-table').cloneNode(true);

        // Supprimer le tfoot existant
        var tfoot = table.querySelector('tfoot');
        if (tfoot) {
            tfoot.parentNode.removeChild(tfoot);
        }

        // Nettoyer le tableau pour l'impression
        var buttons = table.querySelectorAll('button, a, .alert, .badge-reappro');
        buttons.forEach(function(btn) {
            btn.parentNode.removeChild(btn);
        });

        // Supprimer la colonne actions
        var rows = table.querySelectorAll('tr');
        rows.forEach(function(row) {
            var cells = row.querySelectorAll('td, th');
            if (cells.length > 9) { // Si la ligne a la colonne actions
                cells[cells.length - 1].remove(); // Supprimer la dernière colonne (actions)
            }
        });

        // Ajouter la classe pour l'impression
        table.className = 'print-table';

        // Ajouter des classes pour les réapprovisionnements
        var rows = table.querySelectorAll('tr');
        rows.forEach(function(row, index) {
            var firstCell = row.querySelector('td');
            if (firstCell && firstCell.textContent.includes('REAPP-')) {
                row.classList.add('reappro-print');
            }
        });

        // Ajouter le tableau au contenu
        printContent += table.outerHTML;

        // Ajouter les totaux embelli
        printContent += `
           <!-- <table class="print-table">
                <tr class="print-totals-row">
                    <td colspan="3" style="text-align: right; font-weight: bold;">TOTAUX:</td>
                    <td class="text-entree" style="font-weight: bold;">${totalEntrees}</td>
                    <td class="text-sortie" style="font-weight: bold;">${totalSorties}</td>
                    <td></td>
                </tr>
                <tr class="print-solde-final">
                    <td colspan="3" style="text-align: right; font-weight: bold;">SOLDE FINAL:</td>
                    <td colspan="2" style="text-align: center; font-size: 10px;">
                        Entrées: ${totalEntrees} | Sorties: ${totalSorties}
                    </td>
                    <td style="text-align: right; font-weight: bold;" class="${soldeClass.includes('success') ? 'text-success' : 'text-danger'}">
                        ${soldeFinal}
                    </td>
                </tr>
            </table>-->

           <!-- <div class="print-summary">
                <strong>RÉCAPITULATIF:</strong>
                Total Entrées: <span class="entrees">${totalEntrees}</span> |
                Total Sorties: <span class="sorties">${totalSorties}</span> |
                Solde Final: <span class="${soldeClass.includes('success') ? 'entrees' : 'sorties'}">${soldeFinal}</span>
                ${totalReappro > 0 ? `| Réapprovisionnements: <span style="color: #17a2b8;">${formatNumber(totalReappro)} FCFA</span>` : ''}
            </div>-->
        `;

        // Ajouter le footer
        printContent += `
                <div class="print-footer">
                    Document généré le ${new Date().toLocaleString('fr-FR')} | ${window.location.href}
                </div>
            </body>
            </html>
        `;

        printWindow.document.write(printContent);
        printWindow.document.close();

        // Attendre que le contenu soit chargé avant d'imprimer
        printWindow.onload = function() {
            printWindow.focus();
            printWindow.print();
        };
    }

    // Fonction pour générer PDF avec totaux embelli
    function genererPDF() {
        // Vérifier si jsPDF est disponible
        if (typeof jsPDF === 'undefined') {
            alert("La fonction PDF nécessite la bibliothèque jsPDF. Veuillez patienter pendant le chargement...");
            location.reload();
            return;
        }

        var doc = new jsPDF('landscape');
        var title = "LIVRE DE CAISSE";
        var periode = "";

        // Récupérer les dates du filtre
        var dateDebut = document.getElementById('date_debut').value;
        var dateFin = document.getElementById('date_fin').value;

        if (dateDebut && dateFin) {
            periode = "Période du " + formatDate(dateDebut) + " au " + formatDate(dateFin);
        }

        // Récupérer le nom de la caisse si sélectionnée
        var caisseSelect = document.getElementById('caisse_id');
        var caisseNom = "";
        if (caisseSelect && caisseSelect.value) {
            caisseNom = caisseSelect.options[caisseSelect.selectedIndex].text;
        }

        // Récupérer les totaux
        var totalEntrees = document.querySelector('.total-entrees') ? document.querySelector('.total-entrees').innerText : "0 FCFA";
        var totalSorties = document.querySelector('.total-sorties') ? document.querySelector('.total-sorties').innerText : "0 FCFA";
        var soldeFinalElement = document.querySelector('.solde-final-row strong span');
        var soldeFinal = soldeFinalElement ? soldeFinalElement.innerText : "0 FCFA";

        // Récupérer les totaux depuis PHP
        var totalAmount = <?php echo $total_amount; ?>;
        var totalAmountRe = <?php echo $total_amount_re; ?>;
        var totalEntreesAll = <?php echo $total_entrees_all; ?>;
        var totalSortiesAll = <?php echo $total_sorties_all; ?>;
        var nbCaisses = <?php echo $nb_caisses; ?>;
        var totalReappro = <?php echo $total_reappro; ?>;

        // En-tête du PDF
        doc.setFontSize(16);
        doc.text(title, 140, 15, null, null, 'center');

        doc.setFontSize(12);
        if (caisseNom) {
            doc.text("Caisse: " + caisseNom, 140, 22, null, null, 'center');
        }

        if (periode) {
            doc.text(periode, 140, 29, null, null, 'center');
        }

        doc.text("Date d'édition: " + new Date().toLocaleDateString('fr-FR'), 140, 36, null, null, 'center');

        // Ajouter le total de centralisation
        if (totalAmountRe > 0) {
            doc.setFontSize(14);
            doc.setTextColor(255, 255, 255);
            doc.setFillColor(44, 62, 80);
            doc.rect(10, 42, 270, 12, 'F');
            doc.text("ÉTAT GÉNÉRAL DES CAISSES (" + nbCaisses + " caisses)", 140, 47, null, null, 'center');

            doc.setFontSize(10);
            doc.text("Montant Initial: " + formatNumber(totalAmount) + " FCFA", 50, 53);
            doc.text("Total Entrées: " + formatNumber(totalEntreesAll) + " FCFA", 100, 53);
            doc.text("Total Sorties: " + formatNumber(totalSortiesAll) + " FCFA", 150, 53);
            doc.setFont(undefined, 'bold');
            doc.text("Solde Réel: " + formatNumber(totalAmountRe) + " FCFA", 200, 53);

            if (totalReappro > 0) {
                doc.setTextColor(23, 162, 184);
                doc.text("Réapprovisionnements: " + formatNumber(totalReappro) + " FCFA", 250, 53);
            }

            doc.setTextColor(0, 0, 0);
            doc.setFont(undefined, 'normal');
        }

        // Préparer les données du tableau (sans les totaux)
        var table = document.getElementById('livre-caisse-table');
        var data = [];

        // Récupérer les en-têtes (sans la colonne actions)
        var headers = [];
        table.querySelectorAll('thead th').forEach(function(th, index) {
            if (index < 9) { // Exclure la dernière colonne (actions)
                headers.push(th.innerText);
            }
        });

        // Récupérer les données du tbody seulement
        table.querySelectorAll('tbody tr').forEach(function(row) {
            var rowData = [];
            row.querySelectorAll('td').forEach(function(td, index) {
                // Exclure la colonne actions (dernière colonne)
                if (index < 9) {
                    // Nettoyer le contenu (enlever les badges)
                    var cellText = td.innerText.replace('REAPP', '').trim();
                    rowData.push(cellText);
                }
            });
            if (rowData.length > 0) {
                // Ajouter un indicateur pour les réapprovisionnements
                var isReappro = row.classList.contains('reappro-row');
                if (isReappro) {
                    rowData[0] = rowData[0] + " [REAPP]";
                }
                data.push(rowData);
            }
        });

        // Générer le tableau principal
        var startY = totalAmountRe > 0 ? 58 : 45;
        doc.autoTable({
            head: [headers],
            body: data,
            startY: startY,
            styles: {
                fontSize: 8,
                cellPadding: 2
            },
            headStyles: {
                fillColor: [242, 242, 242],
                textColor: 0,
                fontStyle: 'bold'
            },
            alternateRowStyles: {
                fillColor: [245, 245, 245]
            },
            columnStyles: {
                5: { halign: 'right', textColor: [40, 167, 69] },
                6: { halign: 'right', textColor: [220, 53, 69] },
                7: { halign: 'right' },
                8: { halign: 'right' }
            },
            didDrawCell: function(data) {
                // Colorer les lignes de réapprovisionnement
                if (data.cell && data.cell.text && data.cell.text.includes('[REAPP]')) {
                    data.cell.styles.fillColor = [232, 244, 248];
                }
            },
            margin: { top: startY }
        });

        // Récupérer la position Y après le tableau
        var finalY = doc.lastAutoTable.finalY + 10;

        // Ajouter les totaux embelli
        doc.setFontSize(10);
        doc.setFont(undefined, 'bold');

        // Ligne des totaux
        doc.autoTable({
            body: [
                ['TOTAUX:', '', '', '', totalEntrees, totalSorties, '']
            ],
            startY: finalY,
            styles: {
                fontSize: 9,
                cellPadding: 3,
                fillColor: [233, 236, 239]
            },
            columnStyles: {
                0: { fontStyle: 'bold', halign: 'right' },
                4: { halign: 'right', fontStyle: 'bold', textColor: [40, 167, 69] },
                5: { halign: 'right', fontStyle: 'bold', textColor: [220, 53, 69] }
            },
            margin: { left: 14 }
        });

        finalY = doc.lastAutoTable.finalY;

        // Ligne du solde final
        doc.autoTable({
            body: [
                ['SOLDE FINAL:', '', '', '', '', '', soldeFinal]
            ],
            startY: finalY,
            styles: {
                fontSize: 9,
                cellPadding: 3,
                fillColor: [212, 237, 218]
            },
            columnStyles: {
                0: { fontStyle: 'bold', halign: 'right' },
                6: { halign: 'right', fontStyle: 'bold', textColor: soldeFinal.includes('-') ? [220, 53, 69] : [40, 167, 69] }
            },
            margin: { left: 14 }
        });

        finalY = doc.lastAutoTable.finalY + 10;

        // Ajouter un récapitulatif
        doc.setFontSize(9);
        doc.setFont(undefined, 'bold');
        doc.text("RÉCAPITULATIF:", 14, finalY);

        doc.setFontSize(8);
        doc.setFont(undefined, 'normal');
        doc.text(`Total Entrées: ${totalEntrees}`, 14, finalY + 5);
        doc.setTextColor(40, 167, 69);
        doc.text(totalEntrees, 60, finalY + 5);

        doc.setTextColor(0, 0, 0);
        doc.text(`Total Sorties: ${totalSorties}`, 100, finalY + 5);
        doc.setTextColor(220, 53, 69);
        doc.text(totalSorties, 146, finalY + 5);

        doc.setTextColor(0, 0, 0);
        doc.text(`Solde Final: ${soldeFinal}`, 180, finalY + 5);
        doc.setTextColor(soldeFinal.includes('-') ? 220 : 40, soldeFinal.includes('-') ? 53 : 167, 69);
        doc.text(soldeFinal, 220, finalY + 5);

        if (totalReappro > 0) {
            doc.setTextColor(23, 162, 184);
            doc.text(`Réapprovisionnements: ${formatNumber(totalReappro)} FCFA`, 250, finalY + 5);
        }

        // Footer
        doc.setFontSize(8);
        doc.setTextColor(100, 100, 100);
        doc.text("Document généré le " + new Date().toLocaleString('fr-FR'), 140, doc.internal.pageSize.height - 10, null, null, 'center');

        // Télécharger le PDF
        var filename = 'livre_caisse_' + new Date().toISOString().slice(0,10);
        if (caisseNom) {
            filename += '_' + caisseNom.replace(/[^a-z0-9]/gi, '_');
        }
        doc.save(filename + '.pdf');
    }

    // Fonction pour exporter en Excel avec totaux embelli
    function exporterExcel() {
        // Vérifier si XLSX est disponible
        if (typeof XLSX === 'undefined') {
            alert("La fonction Excel nécessite la bibliothèque SheetJS. Veuillez patienter pendant le chargement...");
            location.reload();
            return;
        }

        var table = document.getElementById('livre-caisse-table');
        var title = "LIVRE DE CAISSE";
        var periode = "";

        // Récupérer les dates du filtre
        var dateDebut = document.getElementById('date_debut').value;
        var dateFin = document.getElementById('date_fin').value;

        if (dateDebut && dateFin) {
            periode = "Période du " + formatDate(dateDebut) + " au " + formatDate(dateFin);
        }

        // Récupérer le nom de la caisse si sélectionnée
        var caisseSelect = document.getElementById('caisse_id');
        var caisseNom = "";
        if (caisseSelect && caisseSelect.value) {
            caisseNom = caisseSelect.options[caisseSelect.selectedIndex].text;
        }

        // Récupérer les totaux
        var totalEntrees = document.querySelector('.total-entrees') ? document.querySelector('.total-entrees').innerText : "0 FCFA";
        var totalSorties = document.querySelector('.total-sorties') ? document.querySelector('.total-sorties').innerText : "0 FCFA";
        var soldeFinalElement = document.querySelector('.solde-final-row strong span');
        var soldeFinal = soldeFinalElement ? soldeFinalElement.innerText : "0 FCFA";

        // Récupérer les totaux depuis PHP
        var totalAmount = <?php echo $total_amount; ?>;
        var totalAmountRe = <?php echo $total_amount_re; ?>;
        var totalEntreesAll = <?php echo $total_entrees_all; ?>;
        var totalSortiesAll = <?php echo $total_sorties_all; ?>;
        var nbCaisses = <?php echo $nb_caisses; ?>;
        var totalReappro = <?php echo $total_reappro; ?>;

        // Créer un nouveau workbook
        var wb = XLSX.utils.book_new();

        // Préparer les données
        var data = [];

        // Ligne 1: Titre
        data.push([title]);

        // Ligne 2: Caisse
        if (caisseNom) {
            data.push(["Caisse:", caisseNom]);
        }

        // Ligne 3: Période
        if (periode) {
            data.push(["Période:", periode]);
        }

        // Ligne 4: Date d'édition
        data.push(["Date d'édition:", new Date().toLocaleDateString('fr-FR')]);

        // Ligne 5: État général des caisses
        if (totalAmountRe > 0) {
            data.push(["ÉTAT GÉNÉRAL DES CAISSES (" + nbCaisses + " caisses):", ""]);
            data.push(["Montant Initial Total:", formatNumber(totalAmount) + " FCFA"]);
            data.push(["Total des Entrées:", formatNumber(totalEntreesAll) + " FCFA"]);
            data.push(["Total des Sorties:", formatNumber(totalSortiesAll) + " FCFA"]);
            if (totalReappro > 0) {
                data.push(["Total des Réapprovisionnements:", formatNumber(totalReappro) + " FCFA"]);
            }
            data.push(["Solde Réel Total:", formatNumber(totalAmountRe) + " FCFA"]);
        }

        // Ligne vide
        data.push([]);

        // Récupérer les en-têtes (sans la colonne actions)
        var headers = [];
        table.querySelectorAll('thead th').forEach(function(th, index) {
            if (index < 9) { // Exclure la dernière colonne (actions)
                headers.push(th.innerText);
            }
        });
        data.push(headers);

        // Récupérer les données du tbody (sans la colonne actions)
        table.querySelectorAll('tbody tr').forEach(function(row) {
            var rowData = [];
            row.querySelectorAll('td').forEach(function(td, index) {
                // Exclure la colonne actions (dernière colonne)
                if (index < 9) {
                    // Nettoyer le contenu
                    var cellText = td.innerText.replace('REAPP', '').trim();
                    rowData.push(cellText);
                }
            });
            if (rowData.length > 0) {
                // Ajouter une colonne pour indiquer les réapprovisionnements
                if (row.classList.contains('reappro-row')) {
                    rowData[0] = rowData[0] + " (REAPP)";
                }
                data.push(rowData);
            }
        });

        // Ligne vide avant les totaux
        data.push([]);

        // Ajouter les totaux avec style
        data.push(["", "", "", "", "TOTAUX:", totalEntrees, totalSorties, ""]);
        data.push(["", "", "", "", "SOLDE FINAL:", "", "", soldeFinal]);

        // Ligne vide
        data.push([]);

        // Ajouter un récapitulatif
        data.push(["RÉCAPITULATIF:", "", "", "", "", "", "", ""]);
        data.push(["Total Entrées:", totalEntrees, "", "", "Total Sorties:", totalSorties, "", ""]);
        data.push(["Solde Final:", soldeFinal, "", "", "", "", "", ""]);
        if (totalReappro > 0) {
            data.push(["Total Réapprovisionnements:", formatNumber(totalReappro) + " FCFA", "", "", "", "", "", ""]);
        }

        // Créer la feuille Excel
        var ws = XLSX.utils.aoa_to_sheet(data);

        // Définir les styles des cellules
        var range = XLSX.utils.decode_range(ws['!ref']);

        // Style pour le titre (ligne 1)
        if (ws['A1']) {
            ws['A1'].s = {
                font: { sz: 16, bold: true },
                alignment: { horizontal: 'center' }
            };
            // Fusionner les cellules pour le titre
            ws['!merges'] = ws['!merges'] || [];
            ws['!merges'].push({ s: { r: 0, c: 0 }, e: { r: 0, c: 7 } });
        }

        // Style pour l'état général des caisses
        if (totalAmountRe > 0) {
            var etatRow = caisseNom ? 5 : 4;
            if (ws['A' + etatRow]) {
                ws['A' + etatRow].s = {
                    font: { sz: 12, bold: true },
                    fill: { fgColor: { rgb: "2C3E50" } },
                    color: { rgb: "FFFFFF" }
                };
                ws['!merges'].push({ s: { r: etatRow-1, c: 0 }, e: { r: etatRow-1, c: 7 } });
            }
        }

        // Style pour les en-têtes de colonnes
        var headerRow = 7 + (caisseNom ? 1 : 0) + (totalAmountRe > 0 ? (totalReappro > 0 ? 6 : 5) : 0);
        for (var C = 0; C <= 7; ++C) {
            var cell = XLSX.utils.encode_cell({r: headerRow, c: C});
            if (ws[cell]) {
                ws[cell].s = {
                    font: { bold: true, color: { rgb: "FFFFFF" } },
                    fill: { fgColor: { rgb: "4F81BD" } },
                    alignment: { horizontal: 'center' }
                };
            }
        }

        // Style pour la ligne des totaux
        var totalRow = range.e.r - (totalReappro > 0 ? 6 : 5);
        for (var C = 0; C <= 7; ++C) {
            var cell = XLSX.utils.encode_cell({r: totalRow, c: C});
            if (ws[cell]) {
                ws[cell].s = {
                    font: { bold: true },
                    fill: { fgColor: { rgb: "E9ECEF" } }
                };
            }
        }

        // Style pour la ligne du solde final
        var soldeRow = totalRow + 1;
        for (var C = 0; C <= 7; ++C) {
            var cell = XLSX.utils.encode_cell({r: soldeRow, c: C});
            if (ws[cell]) {
                ws[cell].s = {
                    font: { bold: true },
                    fill: { fgColor: { rgb: "D4EDDA" } }
                };
            }
        }

        // Couleurs pour les montants
        for (var R = headerRow + 1; R < totalRow; ++R) {
            var entreeCell = XLSX.utils.encode_cell({r: R, c: 5});
            var sortieCell = XLSX.utils.encode_cell({r: R, c: 6});
            var referenceCell = XLSX.utils.encode_cell({r: R, c: 0});

            if (ws[entreeCell] && ws[entreeCell].v && parseFloat(ws[entreeCell].v) > 0) {
                ws[entreeCell].s = { font: { bold: true } };
            }

            if (ws[sortieCell] && ws[sortieCell].v && parseFloat(ws[sortieCell].v) > 0) {
                ws[sortieCell].s = { font: { color: { rgb: "DC3545" }, bold: true } };
            }

            // Colorer les lignes de réapprovisionnement
            if (ws[referenceCell] && ws[referenceCell].v && ws[referenceCell].v.includes('(REAPP)')) {
                for (var C = 0; C <= 7; ++C) {
                    var cell = XLSX.utils.encode_cell({r: R, c: C});
                    if (ws[cell]) {
                        ws[cell].s = {
                            fill: { fgColor: { rgb: "E8F4F8" } },
                            font: { color: { rgb: "17A2B8" } }
                        };
                    }
                }
                // Spécial pour les entrées de réapprovisionnement
                if (ws[entreeCell]) {
                    ws[entreeCell].s = {
                        font: { color: { rgb: "17A2B8" }, bold: true },
                        fill: { fgColor: { rgb: "E8F4F8" } }
                    };
                }
            } else if (ws[entreeCell] && ws[entreeCell].v && parseFloat(ws[entreeCell].v) > 0) {
                ws[entreeCell].s = { font: { color: { rgb: "28A745" }, bold: true } };
            }
        }

        // Style des colonnes
        var wscols = [
            {wch: 15}, // Référence
            {wch: 12}, // Date
            {wch: 30}, // Désignation
            {wch: 15}, // Catégorie
            {wch: 12}, // User
            {wch: 15}, // Entrée
            {wch: 15}, // Sortie
            {wch: 15}  // Solde
        ];
        ws['!cols'] = wscols;

        // Ajouter la feuille au workbook
        XLSX.utils.book_append_sheet(wb, ws, "Livre de Caisse");

        // Générer et télécharger le fichier Excel
        var filename = 'livre_caisse_' + new Date().toISOString().slice(0,10);
        if (caisseNom) {
            filename += '_' + caisseNom.replace(/[^a-z0-9]/gi, '_');
        }
        XLSX.writeFile(wb, filename + '.xlsx');
    }

    // Fonction pour afficher un aperçu PDF avec totaux embelli
    function previewPDF() {
        var title = "LIVRE DE CAISSE";
        var periode = "";

        // Récupérer les dates du filtre
        var dateDebut = document.getElementById('date_debut').value;
        var dateFin = document.getElementById('date_fin').value;

        if (dateDebut && dateFin) {
            periode = "Période du " + formatDate(dateDebut) + " au " + formatDate(dateFin);
        }

        // Récupérer le nom de la caisse si sélectionnée
        var caisseSelect = document.getElementById('caisse_id');
        var caisseNom = "";
        if (caisseSelect && caisseSelect.value) {
            caisseNom = caisseSelect.options[caisseSelect.selectedIndex].text;
        }

        // Récupérer les totaux
        var totalEntrees = document.querySelector('.total-entrees') ? document.querySelector('.total-entrees').innerText : "0 FCFA";
        var totalSorties = document.querySelector('.total-sorties') ? document.querySelector('.total-sorties').innerText : "0 FCFA";
        var soldeFinalElement = document.querySelector('.solde-final-row strong span');
        var soldeFinal = soldeFinalElement ? soldeFinalElement.innerText : "0 FCFA";
        var soldeClass = soldeFinalElement ? soldeFinalElement.className : "text-success";

        // Récupérer les totaux depuis PHP
        var totalAmount = <?php echo $total_amount; ?>;
        var totalAmountRe = <?php echo $total_amount_re; ?>;
        var totalEntreesAll = <?php echo $total_entrees_all; ?>;
        var totalSortiesAll = <?php echo $total_sorties_all; ?>;
        var nbCaisses = <?php echo $nb_caisses; ?>;
        var totalReappro = <?php echo $total_reappro; ?>;

        // Créer le contenu de l'aperçu
        var previewContent = `
            <div class="pdf-preview">
                <div class="pdf-header">
                    <div class="pdf-title">${title}</div>
                    ${caisseNom ? `<div class="pdf-period"><strong>Caisse:</strong> ${caisseNom}</div>` : ''}
                    ${periode ? `<div class="pdf-period"><strong>Période:</strong> ${periode}</div>` : ''}
                    <div class="pdf-period"><strong>Date d'édition:</strong> ${new Date().toLocaleDateString('fr-FR')}</div>
                </div>
        `;

        // Ajouter le total de centralisation
        if (totalAmountRe > 0) {
            previewContent += `
                <div style="background-color: #2c3e50; color: white; text-align: center; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                    <strong><i class="fa fa-money"></i> ÉTAT GÉNÉRAL DES CAISSES (${nbCaisses} caisses)</strong><br>
                    <div style="margin-top: 10px; font-size: 14px;">
                        <div style="display: inline-block; margin: 0 10px;">
                            <small>Montant Initial:</small><br>
                            <strong>${formatNumber(totalAmount)} FCFA</strong>
                        </div>
                        <div style="display: inline-block; margin: 0 10px;">
                            <small>Total Entrées:</small><br>
                            <strong style="color: #28a745">${formatNumber(totalEntreesAll)} FCFA</strong>
                        </div>
                        <div style="display: inline-block; margin: 0 10px;">
                            <small>Total Sorties:</small><br>
                            <strong style="color: #dc3545">${formatNumber(totalSortiesAll)} FCFA</strong>
                        </div>
                        <div style="display: inline-block; margin: 0 10px;">
                            <small>Solde Réel:</small><br>
                            <strong style="font-size: 16px;">${formatNumber(totalAmountRe)} FCFA</strong>
                        </div>
                    </div>
                    ${totalReappro > 0 ? `
                    <div style="margin-top: 10px; font-size: 12px; color: #17a2b8;">
                        <i class="fa fa-refresh"></i> Total Réapprovisionnements: ${formatNumber(totalReappro)} FCFA
                    </div>` : ''}
                </div>
            `;
        }

        // Copier seulement le tbody du tableau
        var table = document.getElementById('livre-caisse-table').cloneNode(true);
        table.className = 'pdf-table';

        // Nettoyer le tableau pour l'aperçu
        var buttons = table.querySelectorAll('button, a, .alert');
        buttons.forEach(function(btn) {
            btn.parentNode.removeChild(btn);
        });

        // Supprimer le tfoot existant
        var tfoot = table.querySelector('tfoot');
        if (tfoot) {
            tfoot.parentNode.removeChild(tfoot);
        }

        // Supprimer la colonne actions
        var rows = table.querySelectorAll('tr');
        rows.forEach(function(row) {
            var cells = row.querySelectorAll('td, th');
            if (cells.length > 9) { // Si la ligne a la colonne actions
                cells[cells.length - 1].remove(); // Supprimer la dernière colonne (actions)
            }
        });

        previewContent += table.outerHTML;



        // Ajouter le footer
        previewContent += `
                <div class="pdf-footer">
                    Document généré le ${new Date().toLocaleString('fr-FR')} | ${window.location.href}
                </div>
            </div>
        `;

        // Afficher dans la modal
        $('#pdfPreviewModal .modal-body').html(previewContent);
        $('#pdfPreviewModal').modal('show');
    }

    // ==============================================
    // FONCTIONS POUR L'IMPRESSION INDIVIDUELLE
    // ==============================================

    // Fonction pour imprimer une ligne individuelle
    function printOperation(button) {
        var row = $(button).closest('tr');

        // Récupérer les données de la ligne
        var reference = row.data('reference');
        var date = row.data('date');
        var designation = row.data('designation');
        var category = row.data('category');
        var user = row.data('user');
        var entree = parseFloat(row.data('entree')) || 0;
        var sortie = parseFloat(row.data('sortie')) || 0;
        var soldeAvant = parseFloat(row.data('solde-avant')) || 0;
        var soldeApres = parseFloat(row.data('solde-apres')) || 0;
        var caisse = row.data('caisse');

        var isReappro = row.hasClass('reappro-row');

        // Récupérer les informations de la page
        var periode = "";
        var dateDebut = document.getElementById('date_debut').value;
        var dateFin = document.getElementById('date_fin').value;

        if (dateDebut && dateFin) {
            periode = "Période du " + formatDate(dateDebut) + " au " + formatDate(dateFin);
        }

        // Créer une fenêtre d'impression
        var printWindow = window.open('', '_blank');

        var printContent = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Fiche Opération - ${reference}</title>
                <style>
                    @page {
                        size: A4 portrait;
                        margin: 15mm;
                    }
                    body {
                        font-family: Arial, sans-serif;
                        font-size: 12px;
                        margin: 0;
                        padding: 0;
                    }
                    .print-header {
                        text-align: center;
                        margin-bottom: 20px;
                        border-bottom: 2px solid #000;
                        padding-bottom: 10px;
                    }
                    .print-title {
                        font-size: 18px;
                        font-weight: bold;
                        margin-bottom: 5px;
                    }
                    .print-subtitle {
                        font-size: 14px;
                        margin-bottom: 5px;
                    }
                    .print-period {
                        font-size: 12px;
                        margin-bottom: 10px;
                    }
                    .print-details {
                        width: 100%;
                        border-collapse: collapse;
                        margin-top: 20px;
                    }
                    .print-details th {
                        background-color: #f2f2f2;
                        border: 1px solid #000;
                        padding: 8px;
                        text-align: left;
                        font-weight: bold;
                        width: 30%;
                    }
                    .print-details td {
                        border: 1px solid #000;
                        padding: 8px;
                        width: 70%;
                    }
                    .print-amount {
                        font-size: 16px;
                        font-weight: bold;
                        text-align: right;
                    }
                    .print-entree {
                        color: #28a745;
                    }
                    .print-sortie {
                        color: #dc3545;
                    }
                    .print-reappro {
                        background-color: #e8f4f8 !important;
                        border-left: 3px solid #17a2b8 !important;
                    }
                    .print-footer {
                        text-align: center;
                        margin-top: 40px;
                        font-size: 10px;
                        color: #666;
                        border-top: 1px solid #000;
                        padding-top: 10px;
                    }
                    .print-signature {
                        margin-top: 40px;
                    }
                    .signature-line {
                        border-top: 1px solid #000;
                        width: 200px;
                        margin-top: 30px;
                    }
                </style>
            </head>
            <body>
                <div class="print-header">
                    <div class="print-title">FICHE D'OPÉRATION DE CAISSE</div>
                    ${periode ? `<div class="print-period">${periode}</div>` : ''}
                    <div class="print-period">Date d'impression: ${new Date().toLocaleDateString('fr-FR')}</div>
                </div>

                <table class="print-details ${isReappro ? 'print-reappro' : ''}">
                    <tr>
                        <th>Référence:</th>
                        <td><strong>${reference}${isReappro ? ' <span style="color: #17a2b8;">(Réapprovisionnement)</span>' : ''}</strong></td>
                    </tr>
                    <tr>
                        <th>Date:</th>
                        <td>${date}</td>
                    </tr>
                    <tr>
                        <th>Caisse:</th>
                        <td>${caisse || 'Non spécifiée'}</td>
                    </tr>
                    <tr>
                        <th>Désignation:</th>
                        <td>${designation}</td>
                    </tr>
                    <tr>
                        <th>Catégorie:</th>
                        <td>${category}${isReappro ? ' <span style="color: #17a2b8;">(Réapprovisionnement)</span>' : ''}</td>
                    </tr>
                    <tr>
                        <th>Utilisateur:</th>
                        <td>${user}</td>
                    </tr>
                    <tr>
                        <th>Type d'opération:</th>
                        <td>${entree > 0 ? 'ENTRÉE (Recette)' : 'SORTIE (Dépense)'}</td>
                    </tr>
                    <tr>
                        <th>Montant:</th>
                        <td class="print-amount ${entree > 0 ? 'print-entree' : 'print-sortie'}">
                            ${entree > 0 ? '' : ''}${(entree || sortie).toLocaleString('fr-FR', {minimumFractionDigits: 2})} FCFA
                        </td>
                    </tr>
                    <!--<tr>
                        <th>Solde avant opération:</th>
                        <td class="print-amount" style="color: #6c757d;">
                            ${soldeAvant.toLocaleString('fr-FR', {minimumFractionDigits: 2})} FCFA
                        </td>
                    </tr>
                    <tr>
                        <th>Solde après opération:</th>
                        <td class="print-amount" style="color: ${soldeApres >= 0 ? '#28a745' : '#dc3545'};">
                            ${soldeApres >= 0 ? '+' : ''}${soldeApres.toLocaleString('fr-FR', {minimumFractionDigits: 2})} FCFA
                        </td>
                    </tr>-->
                </table>

                <div class="print-signature">
                    <div style="float: left; width: 45%;">
                        <div class="signature-line"></div>
                        <div style="text-align: center; margin-top: 5px;">Signature Opérateur</div>
                    </div>
                    <div style="float: right; width: 45%;">
                        <div class="signature-line"></div>
                        <div style="text-align: center; margin-top: 5px;">Signature Responsable</div>
                    </div>
                    <div style="clear: both;"></div>
                </div>

                <div class="print-footer">
                    Document généré le ${new Date().toLocaleString('fr-FR')} | ${window.location.href}
                </div>
            </body>
            </html>
        `;

        printWindow.document.write(printContent);
        printWindow.document.close();

        // Attendre que le contenu soit chargé avant d'imprimer
        setTimeout(function() {
            printWindow.focus();
            printWindow.print();
        }, 500);
    }

    // Fonction pour éditer une opération
    function editOperation(button) {
        var row = $(button).closest('tr');
        var reference = row.data('reference');

        // Ici vous pouvez ajouter la logique pour éditer l'opération
        // Par exemple, rediriger vers une page d'édition ou ouvrir un modal
        alert('Fonction d\'édition pour l\'opération ' + reference + ' à implémenter.');

        // Exemple: window.location.href = base_url + 'admin/income/edit_operation/' + reference;
    }

    // Fonctions utilitaires
    function formatDate(dateString) {
        if (!dateString) return '';
        var date = new Date(dateString);
        return date.toLocaleDateString('fr-FR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }

    function formatNumber(number) {
        return number.toLocaleString('fr-FR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Gérer le changement de type d'opération
    $('#type').change(function() {
        var montantInput = $('#montant');
        var currentVal = montantInput.val();
        if (currentVal && currentVal > 0) {
            montantInput.val(Math.abs(currentVal));
        }
    });

    // Vérifier que les bibliothèques sont chargées
    $(document).ready(function() {
        // Vérifier après un délai si les bibliothèques sont chargées
        setTimeout(function() {
            if (typeof jsPDF !== 'undefined' && typeof XLSX !== 'undefined') {
                console.log("Bibliothèques PDF et Excel chargées avec succès");
            } else {
                console.warn("Certaines bibliothèques ne sont pas chargées");
            }
        }, 2000);
    });
    // Fonction pour filtrer par type d'opération
    function filterByOperationType(type) {
        if (type === 'all') {
            $('tr[data-operation-type]').show();
        } else {
            $('tr[data-operation-type]').hide();
            $('tr[data-operation-type="' + type + '"]').show();
        }

        // Mettre à jour les boutons actifs
        $('.filter-type-btn').removeClass('active');
        $('.filter-type-btn[data-type="' + type + '"]').addClass('active');
    }

    // Fonction pour surligner les réapprovisionnements récents
    function highlightRecentReappro() {
        // Obtenir la date d'il y a 24 heures
        var yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);

        $('.reappro-row').each(function() {
            var dateCell = $(this).find('td:nth-child(2)').text();
            var operationDate = parseDateToISO(dateCell);

            if (operationDate && new Date(operationDate) > yesterday) {
                $(this).addClass('highlighted');
            }
        });
    }

    // Fonction utilitaire pour parser les dates
    function parseDateToISO(dateString) {
        // Format: dd-mm-yyyy
        var parts = dateString.split('-');
        if (parts.length === 3) {
            return parts[2] + '-' + parts[1] + '-' + parts[0];
        }
        return null;
    }

    // Ajouter des boutons de filtre dans l'interface
    $(document).ready(function() {
        // Ajouter les boutons de filtre par type d'opération
        var filterButtons = `
        <div class="operation-type-filters" style="display: inline-block; margin-left: 10px;">
            <div class="btn-group btn-group-sm" role="group">
                <button type="button" class="btn btn-secondary filter-type-btn active" data-type="all" onclick="filterByOperationType('all')">
                    <i class="fa fa-eye"></i> Toutes
                </button>
                <button type="button" class="btn btn-info filter-type-btn" data-type="normal" onclick="filterByOperationType('normal')">
                    <i class="fa fa-exchange"></i> Opérations
                </button>
                <button type="button" class="btn btn-primary filter-type-btn" data-type="reappro" onclick="filterByOperationType('reappro')">
                    <i class="fa fa-refresh"></i> Réappro
                </button>
            </div>
        </div>
    `;

        // Insérer les boutons après les boutons d'export
        $('.filter-buttons').after(filterButtons);

        // Surligner les réapprovisionnements récents
        setTimeout(function() {
            highlightRecentReappro();
        }, 1000);

        // Initialiser DataTable avec des colonnes personnalisées
        if ($.fn.DataTable) {
            $('#livre-caisse-table').DataTable({
                "pageLength": 50,
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tous"]],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
                },
                "order": [[1, 'asc']], // Trier par date par défaut
                "columnDefs": [
                    {
                        "targets": [5, 6, 7, 8], // Colonnes numériques
                        "orderSequence": ["desc", "asc"]
                    },
                    {
                        "targets": 0, // Colonne référence
                        "render": function(data, type, row) {
                            if (type === 'display') {
                                // Vérifier si c'est un réapprovisionnement
                                var isReappro = data.includes('REAPP-') ||
                                    $(row).hasClass('reappro-row') ||
                                    $(row).data('operation-type') === 'reappro';

                                if (isReappro) {
                                    return data + ' <span class="badge badge-reappro">REAPP</span>';
                                }
                            }
                            return data;
                        }
                    },
                    {
                        "targets": 9, // Colonne actions
                        "orderable": false,
                        "searchable": false
                    }
                ],
                "initComplete": function() {
                    // Compter les types d'opérations
                    var table = this.api();
                    var reapproCount = table.rows('.reappro-row').count();
                    var normalCount = table.rows().count() - reapproCount;

                    // Mettre à jour les boutons avec les comptes
                    $('.filter-type-btn[data-type="all"]').html(
                        '<i class="fa fa-eye"></i> Toutes (' + table.rows().count() + ')'
                    );
                    $('.filter-type-btn[data-type="normal"]').html(
                        '<i class="fa fa-exchange"></i> Opérations (' + normalCount + ')'
                    );
                    $('.filter-type-btn[data-type="reappro"]').html(
                        '<i class="fa fa-refresh"></i> Réappro (' + reapproCount + ')'
                    );
                }
            });
        }
    });
</script>
<script>
    document.getElementById('exp_head_id').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var categoryName = selectedOption.getAttribute('data-name');
        document.getElementById('exp_category_name').value = categoryName || '';
    });
</script>
</body>
</html>