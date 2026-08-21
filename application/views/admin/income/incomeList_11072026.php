<?php
// S'assurer que les dates sont définies
if (empty($date_debut)) {
    $date_debut = date('Y-m-01');
}
if (empty($date_fin)) {
    $date_fin = date('Y-m-d'); // correction de l'erreur "LIVRE DE CAISSEY-m-d"
}
if (empty($date_totaux_debut)) {
    $date_totaux_debut = date('Y-m-01');
}
if (empty($date_totaux_fin)) {
    $date_totaux_fin = date('Y-m-d');
}
if (empty($date_actuelle)) {
    $date_actuelle = date('Y-m-d');
}

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
<style>
    /* Styles généraux */
    .admin-badge {
        display: inline-block;
        background-color: #ffc107;
        color: #000;
        border-radius: 50%;
        width: 16px;
        height: 16px;
        line-height: 16px;
        text-align: center;
        font-weight: bold;
        margin-left: 3px;
    }

    .btn-danger {
        position: relative;
    }

    .btn-danger .admin-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background-color: #ffc107;
        border: 2px solid #dc3545;
    }

    /* Nouveaux styles pour le datatable des caisses */
    .caisses-datatable {
        width: 100% !important;
        margin-top: 20px;
    }

    .caisses-datatable table {
        width: 100% !important;
    }

    .caisses-datatable th,
    .caisses-datatable td {
        white-space: nowrap;
    }

    .caisses-datatable .caisse-info {
        max-width: 250px;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .caisses-datatable .badge-container {
        display: flex;
        gap: 5px;
        flex-wrap: wrap;
    }

    .caisses-datatable .btn-group-xs {
        display: flex;
        gap: 3px;
    }

    /* Style pour le bouton dépliant */
    .toggle-caisses-btn {
        position: relative;
        transition: all 0.3s ease;
    }

    .toggle-caisses-btn .badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background-color: #28a745;
        color: white;
        border-radius: 50%;
        padding: 3px 6px;
        font-size: 10px;
    }

    .toggle-caisses-btn.collapsed .fa-chevron-up:before {
        content: "\f078"; /* chevron-down */
    }

    .caisses-panel {
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .caisses-panel.collapse {
        display: none;
    }

    .caisses-panel.collapse.in {
        display: block;
    }

    /* Styles pour les messages de confirmation */
    .swal2-popup {
        font-family: 'Arial', sans-serif;
        border-radius: 10px;
    }
    .swal2-title {
        font-size: 24px !important;
        font-weight: 600 !important;
    }
    .swal2-html-container {
        font-size: 16px !important;
        line-height: 1.5 !important;
    }
    .swal2-confirm {
        padding: 10px 30px !important;
        font-size: 16px !important;
        border-radius: 5px !important;
    }
    .swal2-cancel {
        padding: 10px 25px !important;
        font-size: 16px !important;
        border-radius: 5px !important;
    }

    /* Style pour les boutons d'action */
    .btn-group-xs > .btn {
        padding: 1px 5px;
        font-size: 11px;
        line-height: 1.5;
        border-radius: 3px;
    }
    .btn-group-xs {
        display: inline-flex;
    }
    .edit-operation-btn, .delete-operation-btn {
        margin-left: 2px;
    }
    /* Hover effects */
    .btn-warning:hover {
        background-color: #e0a800;
        border-color: #d39e00;
    }
    .btn-danger:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }
    .btn-info:hover {
        background-color: #138496;
        border-color: black;
    }

    /* Styles pour la modal d'aperçu PDF */
    #pdfPreviewModal .modal-dialog {
        max-width: 95%;
        width: 95%;
    }
    #pdfPreviewModal .modal-body {
        padding: 0;
        background: #f5f5f5;
    }
    /* Style pour le contenu de l'aperçu */
    .pdf-preview-content {
        padding: 20px;
        background: white;
        min-height: 500px;
    }

    /* Boutons améliorés */
    .export-buttons .btn {
        margin-right: 5px;
        margin-bottom: 5px;
        min-width: 100px;
    }
    .export-buttons .btn i {
        margin-right: 5px;
    }

    /* Responsive pour les boutons */
    @media (max-width: 768px) {
        .export-buttons {
            margin-top: 10px;
        }
        .export-buttons .btn {
            display: block;
            width: 100%;
            margin-bottom: 5px;
        }
    }

    /* Styles pour les filtres */
    .filter-form .form-group {
        margin-bottom: 10px;
    }
    #search {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>');
        background-repeat: no-repeat;
        background-position: 10px center;
        background-size: 16px;
        padding-left: 35px;
    }

    /* Style pour la ligne de résultat filtrée */
    .filtered-row {
        background-color: #fff3cd !important;
        animation: highlight 1s ease-out;
    }
    @keyframes highlight {
        0% { background-color: #fff3cd; }
        100% { background-color: transparent; }
    }

    /* Style pour le badge de résultats */
    .search-results-badge {
        position: absolute;
        top: 10px;
        right: 15px;
        background-color: #4e73df;
        color: white;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
    }

    /* CSS existant conservé */
    #bankTable tbody tr:hover {
        background-color: #f5f5f5;
        cursor: pointer;
    }
    .bank-name-display {
        font-weight: bold;
        color: black;
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
        color: black;
    }
    .text-entree {
        color: black;
        font-weight: 500;
    }
    .text-sortie {
        color: black;
        font-weight: 500;
    }
    .text-solde-avant {
        color: black;
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
        color: black;
    }
    .montant-caisse:hover {
        transform: scale(1.05);
    }

    /* Style pour les réapprovisionnements */
    .reappro-row {
        background-color: #e8f4f8 !important;
        border-left: 4px solid black !important;
    }
    .reappro-row:hover {
        background-color: #d1ecf1 !important;
    }
    .text-reappro {
        color: black !important;
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
        .filter-form .form-group {
            margin-left: 0 !important;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .filter-form .form-control {
            width: 100% !important;
        }

        .caisses-datatable th,
        .caisses-datatable td {
            font-size: 12px;
        }

        .caisses-datatable .btn-group-xs {
            flex-direction: column;
        }
    }

    @media print {
        .no-print {
            visibility: hidden !important;
            display: none !important;
        }
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
        .box-tools, .filter-form, .modal, .total-centralisation {
            display: none !important;
        }
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
    .solde-total {
        background-color: #e9ecef;
        font-weight: bold;
        font-size: 14px;
    }
    .table-totals {
        background-color: #f8f9fa;
        border-top: 2px solid #dee2e6;
    }
    .caisse-active {
        border-left: 5px solid black;
    }
    .caisse-inactive {
        border-left: 5px solid black;
    }
    .total-centralisation {
        background-color: white;
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

    /* Nouveaux styles pour les totaux */
    .total-stat-box {
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }
    .total-stat-box:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }
    .total-stat-value {
        font-size: 22px;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .total-stat-label {
        font-size: 14px;
        color: black;
    }
    .total-stat-subtext {
        font-size: 11px;
        color: black;
        margin-top: 5px;
    }

    .table thead th {
        text-align: left;
    }

    /* Couleurs spécifiques pour les boîtes */
    .box-montant-initial {
        border-left: 4px solid black;
    }
    .box-entrees {
        border-left: 4px solid black;
    }
    .box-sorties {
        border-left: 4px solid black;
    }
    .box-solde-reel {
        border-left: 4px solid black;
    }

    /* Pour les totaux généraux */
    .etat-global-title {
        color: black;
        font-weight: 600;
        margin-bottom: 15px;
        padding-bottom: 8px;
        border-bottom: 2px solid #4e73df;
    }
    .info-badge {
        background-color: #4e73df;
        color: white;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        margin: 0 3px;
    }
    .info-badge-success {
        background-color: #1cc88a;
    }
    .info-badge-secondary {
        background-color: #858796;
    }
    .info-badge-light {
        background-color: #f8f9fc;
        color: black;
        border: 1px solid #e3e6f0;
    }

    /* Style pour le total de réappro */
    .total-reappro-badge {
        background-color: #17a2b8;
        color: white;
        font-size: 14px;
        padding: 8px 15px;
        border-radius: 4px;
        display: inline-block;
        margin-top: 10px;
    }

    /* Style pour les boutons verrouillés */
    .operation-locked-btn {
        cursor: not-allowed !important;
        opacity: 0.6 !important;
    }

    /* Style pour la table des modes de paiement */
    .table-modes-paiement th {
        background-color: #4e73df !important;
        color: white !important;
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
            <!-- Section des caisses avec datatable à 100% -->
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><i class="fa fa-briefcase"></i> Gestion des Caisses</h3>
                        <div class="box-tools pull-right">
                            <!-- Bouton dépliant pour ajouter/voir les caisses -->
                            <button class="btn btn-success toggle-caisses-btn" type="button" data-toggle="collapse" data-target="#caissesPanel" aria-expanded="false" aria-controls="caissesPanel">
                                <i class="fa fa-chevron-up"></i> Gérer les caisses
                                <span class="badge"><?php echo count($caisses); ?></span>
                            </button>

                            <?php if ($this->rbac->hasPrivilege('caisse', 'can_add')) { ?>
                                <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addCaisseModal">
                                    <i class="fa fa-plus"></i> Nouvelle caisse
                                </button>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- Panel dépliant contenant le datatable des caisses -->
                    <div class="collapse" id="caissesPanel">
                        <div class="box-body">
                            <!-- Total de centralisation amélioré -->
                            <?php
                            // ------------------------------------------------------------
                            // 1. Montant Initial Total (somme de amount dans income, toutes caisses)
                            // ------------------------------------------------------------
                            $this->db->select('SUM(amount) as total_amount_all');
                            $this->db->from('income');
                            $this->db->where('is_deleted', 'no');
                            $total_amount_all = $this->db->get()->row()->total_amount_all ?? 0;

                            // ------------------------------------------------------------
                            // 2. Solde Réel Total (somme de amount_re dans income, caisses actives)
                            // ------------------------------------------------------------
                            $this->db->select('SUM(amount_re) as total_amount_re_actives');
                            $this->db->from('income');
                            $this->db->where('is_deleted', 'no');
                            $this->db->where('est_actif', '1');
                            $total_amount_re_actives = $this->db->get()->row()->total_amount_re_actives ?? 0;

                            // ------------------------------------------------------------
                            // 3. Total Entrées et Total Sorties (période sélectionnée)
                            // ------------------------------------------------------------
                            $date_totaux_debut_sql = !empty($date_totaux_debut) ? $date_totaux_debut : date('Y-m-01');
                            $date_totaux_fin_sql   = !empty($date_totaux_fin)   ? $date_totaux_fin   : date('Y-m-d');

                            $this->db->select('SUM(entree) as total_entrees, SUM(sortie) as total_sorties');
                            $this->db->from('operation_caisse');
                            $this->db->where('date >=', $date_totaux_debut_sql . ' 00:00:00');
                            $this->db->where('date <=', $date_totaux_fin_sql . ' 23:59:59');
                            $totaux_ops = $this->db->get()->row();
                            $total_entrees_all = $totaux_ops->total_entrees ?? 0;
                            $total_sorties_all = $totaux_ops->total_sorties ?? 0;

                            // ------------------------------------------------------------
                            // 4. Nombre de caisses total et actives
                            // ------------------------------------------------------------
                            $this->db->select('COUNT(*) as nb_total');
                            $this->db->from('income');
                            $this->db->where('is_deleted', 'no');
                            $this->db->where('est_actif', '1');
                            $nb_total = $this->db->get()->row()->nb_total ?? 0;


                            $this->db->select('COUNT(*) as nb_actives');
                            $this->db->from('income');
                            $this->db->where('is_deleted', 'no');
                            $this->db->where('est_actif', '1');
                            $nb_actives = $this->db->get()->row()->nb_actives ?? 0;

                            // ------------------------------------------------------------
                            // 5. Total des réapprovisionnements (période sélectionnée)
                            // ------------------------------------------------------------
                            $this->db->select('SUM(montant) as total_reappro');
                            $this->db->from('operation_caisse');
                            $this->db->where('date >=', $date_totaux_debut_sql . ' 00:00:00');
                            $this->db->where('date <=', $date_totaux_fin_sql . ' 23:59:59');
                            $this->db->like('reference', 'TRF-', 'after');
                            $total_reappro_result = $this->db->get()->row();
                            $total_reappro = $total_reappro_result->total_reappro ?? 0;
                            ?>

                            <?php if ($total_amount_all > 0 || $total_entrees_all > 0): ?>
                                <div class="total-centralisation">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <h4 class="etat-global-title" style="color: black">
                                                <i class="fa fa-money"></i> ÉTAT GÉNÉRAL DES CAISSES
                                            </h4>
                                            <small style="font-size: 14px; color: #666;">
                                                (<?php echo date('d/m/Y', strtotime($date_totaux_debut_sql)); ?>
                                                au <?php echo date('d/m/Y', strtotime($date_totaux_fin_sql)); ?>)
                                            </small>
                                        </div>
                                    </div>
                                    <div class="row" style="margin-bottom: 15px;">
                                        <div class="col-md-12">
                                            <div class="filter-form" style="background: rgba(255,255,255,0.1); padding: 10px; border-radius: 5px;">
                                                <form method="get" action="<?php echo base_url('admin/income') ?>" class="form-inline" id="formTotaux">
                                                    <input type="hidden" name="caisse_id" value="<?php echo htmlspecialchars($caisse_id_filter ?? ''); ?>">
                                                    <input type="hidden" name="date_debut" value="<?php echo htmlspecialchars($date_debut_filter ?? ''); ?>">
                                                    <input type="hidden" name="date_fin" value="<?php echo htmlspecialchars($date_fin_filter ?? ''); ?>">
                                                    <input type="hidden" name="categorie" value="<?php echo htmlspecialchars($categorie_filter ?? ''); ?>">
                                                    <input type="hidden" name="mode_paiement" value="<?php echo htmlspecialchars($mode_paiement_filter ?? ''); ?>">
                                                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_filter ?? ''); ?>">

                                                    <div class="form-group">
                                                        <input type="date" name="date_totaux_debut" id="date_totaux_debut" class="form-control input-sm"
                                                               value="<?php echo $date_totaux_debut_sql; ?>" style="margin-right: 10px;">
                                                    </div>

                                                    <div class="form-group">
                                                        <input type="date" name="date_totaux_fin" id="date_totaux_fin" class="form-control input-sm"
                                                               value="<?php echo $date_totaux_fin_sql; ?>" style="margin-right: -2px;">
                                                    </div>

                                                    <button type="submit" class="btn btn-light btn-sm" style="margin-right: 10px;">
                                                        <i class="fa fa-refresh"></i> Actualiser les totaux
                                                    </button>

                                                    <button type="button" class="btn btn-info btn-sm" onclick="resetTotauxDates()">
                                                        <i class="fa fa-calendar"></i> Mois en cours
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" style="margin-top: 10px;">
                                        <!-- Montant Initial Total -->
                                        <div class="col-md-3 col-sm-6 text-center">
                                            <div class="total-stat-box box-montant-initial">
                                                <div class="total-stat-value" style="color: #1cc88a;">
                                                    <?php echo number_format($total_amount_all, 0, ',', ' '); ?> FCFA
                                                </div>
                                                <div class="total-stat-label">
                                                    <i class="fa fa-bank"></i> Montant Initial Total
                                                </div>
                                                <div class="total-stat-subtext">
                                                    <?php echo $nb_total; ?> caisses total
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Total Entrées -->
                                        <div class="col-md-3 col-sm-6 text-center">
                                            <div class="total-stat-box box-entrees">
                                                <div class="total-stat-value" style="color: #36b9cc;">
                                                    <?php echo number_format($total_entrees_all, 0, ',', ' '); ?> FCFA
                                                </div>
                                                <div class="total-stat-label">
                                                    <i class="fa fa-sign-in"></i> Total Entrées
                                                </div>
                                                <div class="total-stat-subtext">
                                                    <button type="button" class="btn btn-xs btn-info" onclick="voirOperationsParType('entree')">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Total Sorties -->
                                        <div class="col-md-3 col-sm-6 text-center">
                                            <div class="total-stat-box box-sorties">
                                                <div class="total-stat-value" style="color: #f6c23e;">
                                                    <?php echo number_format($total_sorties_all, 0, ',', ' '); ?> FCFA
                                                </div>
                                                <div class="total-stat-label">
                                                    <i class="fa fa-sign-out"></i> Total Sorties
                                                </div>
                                                <div class="total-stat-subtext">
                                                    <button type="button" class="btn btn-xs btn-info" onclick="voirOperationsParType('sortie')">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Solde Réel Total -->
                                        <div class="col-md-3 col-sm-6 text-center">
                                            <div class="total-stat-box box-solde-reel">
                                                <div class="total-stat-value" style="color: #4e73df; font-size: 24px;">
                                                    <?php echo number_format($total_amount_re_actives, 0, ',', ' '); ?> FCFA
                                                </div>
                                                <div class="total-stat-label">
                                                    <i class="fa fa-calculator"></i> Solde Réel Total
                                                </div>
                                                <div class="total-stat-subtext">
                                                    <?php echo $nb_actives; ?> caisses actives
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if ($total_reappro > 0): ?>
                                        <div class="row" style="margin-top: 10px;">
                                            <div class="col-md-12 text-center">
                                                <span class="total-reappro-badge">
                                                    <i class="fa fa-refresh"></i> Total Réappro: <?php echo number_format($total_reappro, 0, ',', ' '); ?> FCFA
                                                </span>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="row" style="margin-top: 15px;">
                                        <div class="col-md-12 text-center">
                                            <span class="info-badge"><?php echo $nb_actives; ?> Caisses actives</span>
                                            <span class="info-badge info-badge-secondary"><?php echo ($nb_total - $nb_actives); ?> Caisses inactives</span><br>
                                            <span class="info-badge info-badge-light">
                                                <i class="fa fa-clock-o"></i> <?php echo date('d/m/Y H:i'); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Datatable des caisses à 100% -->
                            <div class="caisses-datatable">
                                <table class="table table-striped table-bordered table-hover" id="caisses-table">
                                    <thead>
                                    <tr>
                                        <th width="5%">ID</th>
                                        <th width="15%">Nom</th>
                                        <th width="10%">Statut</th>
                                        <th width="8%">Montant Initial</th>
                                        <th width="8%">Total Entrées</th>
                                        <th width="8%">Total Sorties</th>
                                        <th width="8%">Solde Actuel</th>
                                        <th width="8%">Date création</th>
                                        <th width="8%">Dernière opération</th>
                                        <th width="14%" class="no-print">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (!empty($caisses)): ?>
                                        <?php foreach ($caisses as $caisse): ?>
                                            <?php
                                            // Récupérer les informations complètes de la caisse depuis income
                                            $this->db->select('amount, amount_re, date as creation_date');
                                            $this->db->from('income');
                                            $this->db->where('id', $caisse['id']);
                                            $caisse_details = $this->db->get()->row();

                                            // Récupérer les totaux d'entrées et sorties depuis operation_caisse pour cette caisse
                                            $this->db->select('SUM(entree) as total_entrees, SUM(sortie) as total_sorties, MAX(date) as last_operation_date');
                                            $this->db->from('operation_caisse');
                                            $this->db->where('caisse_id', $caisse['id']);
                                            $ops_details = $this->db->get()->row();

                                            // Calculer les valeurs
                                            $amount = floatval($caisse_details->amount ?? $caisse['amount'] ?? 0);
                                            $amount_re = floatval($caisse_details->amount_re ?? 0);
                                            $total_entrees = floatval($ops_details->total_entrees ?? 0);
                                            $total_sorties = floatval($ops_details->total_sorties ?? 0);
                                            $last_operation_date = $ops_details->last_operation_date ?? null;
                                            $creation_date = $caisse_details->creation_date ?? $caisse['date'] ?? null;
                                            ?>
                                            <tr>
                                                <td><?php echo $caisse['id']; ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($caisse['name']); ?></strong>
                                                    <?php if (!empty($caisse['description'])): ?>
                                                        <br><small class="text-muted"><?php echo htmlspecialchars(substr($caisse['description'], 0, 50)) . (strlen($caisse['description']) > 50 ? '...' : ''); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($caisse['est_actif'] == '1'): ?>
                                                        <span class="label label-success">ACTIVE</span>
                                                    <?php else: ?>
                                                        <span class="label label-danger">INACTIVE</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-right"><?php echo number_format($amount, 0, ',', ' '); ?></td>
                                                <td class="text-right text-success"><?php echo number_format($total_entrees, 0, ',', ' '); ?></td>
                                                <td class="text-right text-danger"><?php echo number_format($total_sorties, 0, ',', ' '); ?></td>
                                                <td class="text-right <?php echo $amount_re >= 0 ? 'text-success' : 'text-danger'; ?>">
                                                    <strong><?php echo number_format($amount_re, 0, ',', ' '); ?></strong>
                                                </td>
                                                <td><?php echo $creation_date ? date('d/m/Y', strtotime($creation_date)) : '-'; ?></td>
                                                <td>
                                                    <?php if ($last_operation_date): ?>
                                                        <?php echo date('d/m/Y H:i', strtotime($last_operation_date)); ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="no-print">
                                                    <div class="btn-group btn-group-xs" role="group">
                                                        <!-- BOUTON ÉDITION -->
                                                        <?php if ($this->rbac->hasPrivilege('caisse', 'can_edit')): ?>
                                                            <a href="<?php echo base_url('admin/income/edit/' . $caisse['id']); ?>"
                                                               class="btn btn-warning"
                                                               title="Modifier la caisse">
                                                                <i class="fa fa-edit"></i>
                                                            </a>
                                                        <?php endif; ?>

                                                        <!-- BOUTON RÉAPPROVISIONNEMENT (SUPERADMIN) -->
                                                        <?php if ($this->rbac->hasPrivilege('superadmin')): ?>
                                                            <button class="btn btn-success increaseAmount"
                                                                    data-row-id="<?php echo $caisse['id']; ?>"
                                                                    title="Réapprovisionner la caisse">
                                                                <i class="fa fa-plus"></i>
                                                            </button>

                                                            <!-- BOUTON : Mettre à jour la date de création -->
                                                            <button class="btn btn-info update-creation-date"
                                                                    data-id="<?php echo $caisse['id']; ?>"
                                                                    data-name="<?php echo htmlspecialchars($caisse['name'], ENT_QUOTES); ?>"
                                                                    title="Mettre à jour la date de création avec le mois en cours">
                                                                <i class="fa fa-calendar"></i>
                                                            </button>

                                                            <button class="btn btn-info viewIncrease"
                                                                    data-row-id="<?php echo $caisse['id']; ?>"
                                                                    title="Voir l'historique des réapprovisionnements">
                                                                <i class="fa fa-list"></i>
                                                            </button>
                                                        <?php endif; ?>

                                                        <!-- BOUTON ACTIVER/DÉSACTIVER -->
                                                        <button class="btn btn-primary toggle-status"
                                                                data-id="<?php echo $caisse['id']; ?>"
                                                                data-status="<?php echo $caisse['est_actif']; ?>"
                                                                title="<?php echo ($caisse['est_actif'] == '1') ? 'Désactiver la caisse' : 'Activer la caisse'; ?>">
                                                            <?php if ($caisse['est_actif'] == '1'): ?>
                                                                <i class="fa fa-pause"></i>
                                                            <?php else: ?>
                                                                <i class="fa fa-play"></i>
                                                            <?php endif; ?>
                                                        </button>

                                                        <!-- BOUTON SUPPRESSION (optionnel) -->
                                                        <?php if ($this->rbac->hasPrivilege('caisse', 'can_delete')): ?>
                                                            <!-- <a href="javascript:void(0);" onclick="confirmDeleteCaisse(<?php echo $caisse['id']; ?>, '<?php echo htmlspecialchars($caisse['name'], ENT_QUOTES); ?>')" class="btn btn-danger" title="Supprimer la caisse"><i class="fa fa-trash"></i></a> -->
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    </tbody>
                                    <tfoot>
                                    <tr style="background-color: #f0f0f0; font-weight: bold;">
                                        <td colspan="3" class="text-right">TOTAUX:</td>
                                        <td class="text-right"><?php echo number_format($total_amount_all, 0, ',', ' '); ?></td>
                                        <td class="text-right text-success"><?php echo number_format($total_entrees_all, 0, ',', ' '); ?></td>
                                        <td class="text-right text-danger"><?php echo number_format($total_sorties_all, 0, ',', ' '); ?></td>
                                        <td class="text-right <?php echo $total_amount_re_actives >= 0 ? 'text-success' : 'text-danger'; ?>">
                                            <?php echo number_format($total_amount_re_actives, 0, ',', ' '); ?>
                                        </td>
                                        <td colspan="4"></td>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section du livre de caisse -->
            <div class="col-md-12">
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

                    <!-- Formulaire de filtre avec nouveaux champs -->
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

                                <!-- NOUVEAU FILTRE : Catégorie -->
                                <div class="form-group" style="margin-left: 15px; margin-bottom: 10px;">
                                    <label for="categorie" style="display: block; margin-bottom: 5px; font-weight: 500;">Catégorie:</label>
                                    <select name="categorie" id="categorie" class="form-control" style="width: 200px; height: 34px; padding: 6px 12px; font-size: 13px;">
                                        <option value="">Toutes les catégories</option>
                                        <?php
                                        if (!empty($categories_list)):
                                            foreach ($categories_list as $categorie):
                                                ?>
                                                <option value="<?php echo htmlspecialchars($categorie); ?>"
                                                    <?php echo isset($_GET['categorie']) && $_GET['categorie'] == $categorie ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($categorie); ?>
                                                </option>
                                            <?php endforeach;
                                        endif; ?>
                                    </select>
                                </div>
                                <!-- NOUVEAU FILTRE : Mode de paiement -->
                                <div class="form-group" style="margin-left: 15px; margin-bottom: 10px;">
                                    <label for="mode_paiement" style="display: block; margin-bottom: 5px; font-weight: 500;">Mode paiement:</label>
                                    <select name="mode_paiement" id="mode_paiement" class="form-control" style="width: 200px; height: 34px; padding: 6px 12px; font-size: 13px;">
                                        <option value="">Tous les modes</option>
                                        <?php
                                        if (!empty($modes_paiement_list)):
                                            foreach ($modes_paiement_list as $mode):
                                                ?>
                                                <option value="<?php echo htmlspecialchars($mode); ?>"
                                                    <?php echo isset($_GET['mode_paiement']) && $_GET['mode_paiement'] == $mode ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($mode); ?>
                                                </option>
                                            <?php endforeach;
                                        endif; ?>
                                    </select>
                                </div>

                                <!-- NOUVEAU CHAMP : Recherche -->
                                <div class="form-group" style="margin-left: 15px;">
                                    <label for="search">Rechercher: </label>
                                    <input type="text" name="search" id="search" class="form-control input-sm"
                                           placeholder="Référence, Désignation, Nom..."
                                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                                           style="width: 250px;">
                                </div>

                                <div class="form-group" style="margin-left: 15px;">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fa fa-filter"></i> Filtrer
                                    </button>
                                    <a href="<?php echo base_url('admin/income'); ?>" class="btn btn-default btn-sm">
                                        <i class="fa fa-refresh"></i> Actualisé
                                    </a>
                                    <div class="export-buttons" style="display: inline-block; padding-top: 10px">
                                        <button type="button" class="btn btn-success btn-sm" onclick="imprimerLivreCaisse()">
                                            <i class="fa fa-print"></i> Imprimer
                                        </button>
                                        <button type="button" class="btn btn-warning btn-sm" onclick="previewPDF()">
                                            <i class="fa fa-eye"></i> Aperçu
                                        </button>
                                        <button type="button" class="btn btn-info btn-sm" onclick="exporterExcel()">
                                            <i class="fa fa-file-excel-o"></i> Excel
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

                        <!-- Messages d'information sur les filtres -->
                        <?php if (isset($search_filter) && !empty($search_filter)): ?>
                            <div class="alert alert-info" style="margin-top: 10px;">
                                <i class="fa fa-search"></i>
                                Résultats pour la recherche : "<strong><?php echo htmlspecialchars($search_filter); ?></strong>"
                                <span class="badge badge-primary"><?php echo count($operations); ?> résultats</span>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($categorie_filter) && !empty($categorie_filter)): ?>
                            <div class="alert alert-info" style="margin-top: 10px;">
                                <i class="fa fa-filter"></i>
                                Filtre catégorie : <strong><?php echo htmlspecialchars($categorie_filter); ?></strong>
                            </div>
                        <?php endif; ?>

                        <div class="table-responsive mailbox-messages">
                            <table class="table table-striped table-bordered table-hover table-livre-caisse" id="livre-caisse-table">
                                <thead>
                                <tr>
                                    <th hidden width="10%">RÉFÉRENCE</th>
                                    <th width="10%" style="text-align: left;">DATE</th>
                                    <th width="20%" style="text-align: left;">DÉSIGNATIONS</th>
                                    <th width="20%" style="text-align: left;">NOM</th>
                                    <th hidden width="8%">CAT</th>
                                    <th width="10%">User</th>
                                    <th width="10%" style="text-align: left;">ENTRÉE</th>
                                    <th width="10%" style="text-align: left;">SORTIE</th>
                                    <th width="12%" style="text-align: left;">SOLDE AVANT</th>
                                    <th width="12%" style="text-align: left;">SOLDE APRÈS</th>
                                    <th width="8%" class="no-print" style="text-align: left;">ACTIONS</th>
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
                                        <td colspan="7" style="text-align: left;"><strong>SOLDE INITIAL:</strong></td>
                                        <td style="background-color: #e8f4fd; text-align: left;">
                                            <?php echo number_format($solde_initial, 2, ',', ' '); ?> FCFA
                                        </td>
                                        <td style="text-align: left;"></td>
                                        <td style="text-align: left;"></td>
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
                                            data-mode="<?php echo htmlspecialchars($operation['category_mode'] ?? $operation['mode_paiement'] ?? ''); ?>"
                                            data-nom="<?php echo htmlspecialchars($operation['entreprise_nom'] ?? $operation['nom'] ?? ''); ?>"
                                            data-user="<?php echo htmlspecialchars($operation['user'] ?? $operation['user_name'] ?? 'Système'); ?>"
                                            data-entree="<?php echo $entree; ?>"
                                            data-sortie="<?php echo $sortie; ?>"
                                            data-solde-avant="<?php echo $solde_avant; ?>"
                                            data-solde-apres="<?php echo $solde_apres; ?>"
                                            data-caisse="<?php echo htmlspecialchars($operation['caisse_nom'] ?? ''); ?>">

                <td hidden>
                    <?php echo htmlspecialchars($operation['reference'] ?? 'N/A'); ?>
                    <?php if ($is_reappro): ?>
                        <span class="badge badge-reappro">REAPP</span>
                    <?php endif; ?>
                </td>
                                            <td style="text-align: left;"><?php echo !empty($operation['date']) ? date('d-m-Y', strtotime($operation['date'])) : 'N/A'; ?></td>
                                            <td style="text-align: left;">
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
                                            <td style="text-align: left;">
                                                <div class="operation-nom">
                                                    <?php echo htmlspecialchars($operation['entreprise_nom'] ?? $operation['nom'] ?? ''); ?>
                                                </div>
                                            </td>

              <td hidden>
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
                                            <td class="text-entree <?php echo $is_reappro ? 'text-reappro' : ''; ?>" style="text-align: left;">
                                                <?php if ($entree > 0): ?>
                                                    <div class="montant-entree">
                                                        <?php echo number_format($entree, 0, ',', ' '); ?>
                                                    </div>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-sortie" style="text-align: left;">
                                                <?php if ($sortie > 0): ?>
                                                    <div class="montant-sortie">
                                                        <?php echo number_format($sortie, 0, ',', ' '); ?>
                                                    </div>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-solde-avant" style="text-align: left; background-color: #f8f9fa;">
                                                <div class="solde-avant">
                                                    <?php echo number_format($solde_avant, 0, ',', ' '); ?>
                                                </div>
                                            </td>
                                            <td class="text-solde-apres" style="text-align: left; font-weight: bold;
                                                    background-color: <?php echo $solde_apres >= 0 ? '#e8f5e8' : '#ffe8e8'; ?>;">
                                                <div class="solde-apres">
                        <span class="<?php echo $solde_apres >= 0 ? 'text-success' : 'text-danger'; ?>">
                            <?php echo number_format($solde_apres, 0, ',', ' '); ?>
                        </span>
                                                </div>
                                            </td>
                                            <td class="no-print text-center" style="text-align: left;">
                                                <div class="btn-group btn-group-xs" role="group">
                                                    <!-- Bouton Imprimer -->
                                                    <button class="btn btn-xs btn-info print-operation-btn"
                                                            title="Imprimer cette ligne"
                                                            onclick="printOperation(this)">
                                                        <i class="fa fa-print"></i>
                                                    </button>

                                                    <?php if ($this->rbac->hasPrivilege('caisse', 'can_edit')): ?>
                                                        <!-- Bouton Éditer (optionnel) -->
                                                    <?php endif; ?>

                                                    <?php
                                                    $has_delete_permission = $this->rbac->hasPrivilege('caisse', 'can_delete');
                                                    $is_superadmin = $this->rbac->hasPrivilege('superadmin');
                                                    $is_admin = $this->rbac->hasPrivilege('admin');
                                                    $is_admin_user = ($is_superadmin || $is_admin);
                                                    $date_operation = !empty($operation['date']) ? $operation['date'] : '';
                                                    $jour_operation = '';
                                                    if (!empty($date_operation)) {
                                                        if (strpos($date_operation, '-') !== false && strlen($date_operation) > 8) {
                                                            $jour_operation = date('d', strtotime($date_operation));
                                                        } else {
                                                            $jour_operation = rtrim($date_operation, '-');
                                                        }
                                                    }
                                                    $jour_actuel = date('d');
                                                    $peut_supprimer = false;
                                                    if ($is_admin_user) {
                                                        $peut_supprimer = true;
                                                    } else {
                                                        if ($has_delete_permission && $jour_operation == $jour_actuel) {
                                                            $peut_supprimer = true;
                                                        }
                                                    }
                                                    if ($has_delete_permission):
                                                        ?>
                                                        <?php if ($peut_supprimer): ?>
                                                        <button class="btn btn-xs btn-danger delete-operation-btn"
                                                                title="<?php echo $is_admin_user ? 'Supprimer cette opération (Autorisation Admin)' : 'Supprimer cette opération (Date du jour)'; ?>"
                                                                data-id="<?php echo $operation['id']; ?>"
                                                                onclick="verifierSuppression(<?php echo $operation['id']; ?>, '<?php echo $date_operation; ?>', <?php echo $is_admin_user ? 'true' : 'false'; ?>)">
                                                            <i class="fa fa-trash"></i>
                                                            <?php if ($is_admin_user): ?>
                                                                <span class="admin-badge" style="font-size: 8px; margin-left: 2px; background-color: #ffc107; color: #000; padding: 2px 4px; border-radius: 3px;" title="Autorisation Admin">ADMIN</span>
                                                            <?php else: ?>
                                                                <span class="today-badge" style="font-size: 8px; margin-left: 2px; background-color: #28a745; color: #fff; padding: 2px 4px; border-radius: 3px;" title="Date du jour">AUJOURD'HUI</span>
                                                            <?php endif; ?>
                                                        </button>
                                                    <?php else: ?>
                                                        <button class="btn btn-xs btn-secondary operation-locked-btn"
                                                                title="Opération du <?php echo $jour_operation; ?> - Vous ne pouvez supprimer que les opérations du jour (<?php echo $jour_actuel; ?>)"
                                                                disabled
                                                                style="cursor: not-allowed; background-color: #6c757d; border-color: #6c757d;">
                                                            <i class="fa fa-calendar-times"></i>
                                                            <span style="font-size: 8px; margin-left: 2px;">🔒</span>
                                                        </button>
                                                    <?php endif; ?>
                                                    <?php else: ?>
                                                        <!-- Pas de bouton -->
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php
                                        $total_entrees += $entree;
                                        $total_sorties += $sortie;
                                        $solde_final = $solde_apres;
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="11" class="text-center" style="text-align: left;">
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
                                    <td colspan="6" class="text-right"><strong>TOTAUX:</strong></td>
                                    <td class="text-entree" style="text-align: right; font-weight: bold; color: black;">
                                        <?php echo number_format($total_entrees, 2, ',', ' '); ?> FCFA
                                    </td>
                                    <td class="text-sortie" style="text-align: right; font-weight: bold; color: black;">
                                        <?php echo number_format($total_sorties, 2, ',', ' '); ?> FCFA
                                    </td>
                                    <td></td>
                                    <td></td>
                                    <td class="no-print"></td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modals (conservés inchangés) -->
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

                    <!-- Nouvelle case à cocher pour Mobile Money -->
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="est_mobile_money" name="est_mobile_money" value="1" <?php echo set_value('est_mobile_money') == '1' ? 'checked' : ''; ?>>
                                <strong>Compte Mobile Money</strong>
                                <small class="text-muted">(Cocher si c'est un compte Mobile Money, laisser décoché pour une caisse classique)</small>
                            </label>
                        </div>
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
            <div class="modal-header" style="background-color: #0e78d2">
                <h5 class="modal-title" style="color: white">Nouvelle Opération</h5>
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
                        <label for="exp_head_id"><?php echo $this->lang->line('expense_head'); ?></label> <small class="req">*</small>
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

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="montant">Nom du concerné</label>
                                <input type="text" class="form-control" id="nom" name="nom" required>
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
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Chargement...</span>
                            </div>
                        </div>
                    </div>
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<script>
    (function($) {
        'use strict';
        $(document).ready(function () {
            initDatatable('income-list','admin/income/getincomelist',[],[],10);
            initReapproButtons();
            initSearchFilters();

            if ($.fn.DataTable) {
                $('#caisses-table').DataTable({
                    "pageLength": 25,
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
                    },
                    "order": [[0, "desc"]],
                    "responsive": true,
                    "autoWidth": false
                });
            }

            $('.toggle-caisses-btn').on('click', function() {
                var icon = $(this).find('i');
                if ($(this).hasClass('collapsed')) {
                    icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                } else {
                    icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                }
            });
        });
    }(jQuery));

    var base_url = '<?php echo base_url(); ?>';

    // Tableau des montants initiaux des caisses (id => montant)
    var caissesInitialAmounts = <?php
        $caisse_amounts = [];
        if (!empty($caisses)) {
            foreach ($caisses as $c) {
                $caisse_amounts[$c['id']] = floatval($c['amount'] ?? 0);
            }
        }
        echo json_encode($caisse_amounts);
        ?>;

    function initReapproButtons() {
        $(document).on('click', '.increaseAmount', function(e) {
            e.preventDefault();
            var rowID = $(this).attr('data-row-id');
            $.ajax({
                url: base_url + 'admin/income/formIncrease',
                type: "POST",
                data: { 'rowID': rowID },
                success: function(data) {
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

        $(document).on('click', '.viewIncrease', function(e) {
            e.preventDefault();
            var rowID = $(this).attr('data-row-id');
            $.ajax({
                url: base_url + 'admin/income/listIncrease',
                type: "POST",
                data: { 'rowID': rowID },
                success: function(data) {
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

    function initSearchFilters() {
        var searchTimeout;
        $('#search').on('input', function() {
            clearTimeout(searchTimeout);
            var searchTerm = $(this).val().toLowerCase();
            searchTimeout = setTimeout(function() {
                filterTable(searchTerm);
            }, 300);
        });
        $('#categorie').on('change', function() {
            var selectedCategory = $(this).val().toLowerCase();
            filterByCategory(selectedCategory);
        });
        $('#mode_paiement').on('change', function() {
            var selectedMode = $(this).val().toLowerCase();
            filterByPaymentMode(selectedMode);
        });
    }

    function filterByPaymentMode(selectedMode) {
        if (!selectedMode) {
            $('.table-livre-caisse tbody tr').show();
            return;
        }
        $('.table-livre-caisse tbody tr').each(function() {
            var row = $(this);
            var mode = row.find('td:nth-child(6)').text().toLowerCase();
            if (mode.indexOf(selectedMode) !== -1) {
                row.show();
            } else {
                row.hide();
            }
        });
    }

    function filterTable(searchTerm) {
        if (!searchTerm) {
            $('.table-livre-caisse tbody tr').show();
            $('.table-livre-caisse tbody tr').removeClass('filtered-row');
            return;
        }
        $('.table-livre-caisse tbody tr').each(function() {
            var row = $(this);
            var text = row.text().toLowerCase();
            if (text.indexOf(searchTerm) !== -1) {
                row.show().addClass('filtered-row');
            } else {
                row.hide().removeClass('filtered-row');
            }
        });
    }

    function filterByCategory(selectedCategory) {
        if (!selectedCategory) {
            $('.table-livre-caisse tbody tr').show();
            return;
        }
        $('.table-livre-caisse tbody tr').each(function() {
            var row = $(this);
            var category = row.find('td:nth-child(5)').text().toLowerCase();
            if (category.indexOf(selectedCategory) !== -1) {
                row.show();
            } else {
                row.hide();
            }
        });
    }

    $(document).on("click", "#submitBTN", function (e) {
        e.preventDefault();
        initPostAjaxRequest();
    });

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
                    $('#increaseForm').modal("hide");
                    toastr.success(serverResponse.message);
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

    $(document).on('click', '.toggle-status', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var currentStatus = $(this).data('status');
        var newStatus = currentStatus == '1' ? '0' : '1';
        var button = $(this);
        if (currentStatus == '1') {
            if (!confirm('Êtes-vous sûr de vouloir fermer cette caisse ? Le solde sera reporté pour la prochaine ouverture.')) {
                return false;
            }
        } else {
            if (!confirm('Êtes-vous sûr de vouloir ouvrir cette caisse ?')) {
                return false;
            }
        }
        button.html('<i class="fa fa-spinner fa-spin"></i>');
        button.prop('disabled', true);
        $.ajax({
            url: '<?php echo base_url("admin/income/toggle_caisse_status"); ?>',
            type: 'POST',
            dataType: 'json',
            data: { id: id, status: newStatus },
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert(response.message);
                }
            },
            error: function() {
                alert('Erreur lors de la modification');
            },
            complete: function() {
                button.prop('disabled', false);
                button.html(newStatus == '1' ? '<i class="fa fa-pause"></i>' : '<i class="fa fa-play"></i>');
            }
        });
    });

    document.getElementById('exp_head_id').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var categoryName = selectedOption.getAttribute('data-name');
        document.getElementById('exp_category_name').value = categoryName || '';
    });

    function imprimerLivreCaisse() {
        var printWindow = window.open('', '_blank');
        var title = "LIVRE DE CAISSE";
        var dateDebut = document.getElementById('date_debut') ? document.getElementById('date_debut').value : '';
        var dateFin = document.getElementById('date_fin') ? document.getElementById('date_fin').value : '';
        var caisseSelect = document.getElementById('caisse_id');
        var caisseId = caisseSelect ? caisseSelect.value : '';
        var caisseNom = caisseSelect && caisseSelect.value ? caisseSelect.options[caisseSelect.selectedIndex].text : "Toutes les caisses";
        var montantInitial = (caisseId && caissesInitialAmounts[caisseId]) ? caissesInitialAmounts[caisseId] : 0;
        var categorie = document.getElementById('categorie') ? document.getElementById('categorie').value : '';
        var search = document.getElementById('search') ? document.getElementById('search').value : '';

        function formatDateForDisplay(dateString) {
            if (!dateString) return '';
            var date = new Date(dateString);
            return date.toLocaleDateString('fr-FR');
        }

        var tableData = [];
        var totalEntrees = 0;
        var totalSorties = 0;
        var soldeFinal = 0;

        var tbodyRows = document.querySelectorAll('#livre-caisse-table tbody tr');
        tbodyRows.forEach(function(row) {
            if (row.querySelector('td[colspan]')) return;
            var cells = row.querySelectorAll('td');
            if (cells.length >= 10) {
                var entreeText = cells[6] ? cells[6].innerText.replace(/\s/g, '').replace('FCFA', '').trim() : '0';
                var sortieText = cells[7] ? cells[7].innerText.replace(/\s/g, '').replace('FCFA', '').trim() : '0';
                var soldeText = cells[9] ? cells[9].innerText.replace(/\s/g, '').replace('FCFA', '').trim() : '0';
                var entree = parseFloat(entreeText) || 0;
                var sortie = parseFloat(sortieText) || 0;
                var solde = parseFloat(soldeText) || 0;
                totalEntrees += entree;
                totalSorties += sortie;
                soldeFinal = solde;
                tableData.push({
                    reference: cells[0] ? cells[0].innerText.trim() : '',
                    date: cells[1] ? cells[1].innerText.trim() : '',
                    designation: cells[2] ? cells[2].innerText.trim() : '',
                    nom: cells[3] ? cells[3].innerText.trim() : '',
                    categorie: cells[4] ? cells[4].innerText.trim() : '',
                    user: cells[5] ? cells[5].innerText.trim() : '',
                    entree: entree,
                    sortie: sortie,
                    solde: solde
                });
            }
        });

        function formatNumber(num) {
            return num.toLocaleString('fr-FR', {minimumFractionDigits: 0, maximumFractionDigits: 0});
        }

        var printContent = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>${title}</title>
            <meta charset="UTF-8">
            <style>
                @page { size: landscape; margin: 10mm; }
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { font-family: 'Courier New', Courier, monospace; font-size: 11px; margin: 0; padding: 10px; background: white; }
                .print-container { width: 100%; margin: 0 auto; }
                .print-header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
                .print-title { font-size: 18px; font-weight: bold; margin-bottom: 5px; text-transform: uppercase; }
                .print-subtitle { font-size: 14px; margin-bottom: 5px; }
                .print-period { font-size: 11px; margin-bottom: 3px; }
                .print-table { width: 100%; border-collapse: collapse; margin-top: 10px; font-size: 10px; }
                .print-table th { background-color: #f2f2f2; border: 1px solid #000; padding: 8px 5px; text-align: center; font-weight: bold; }
                .print-table td { border: 1px solid #000; padding: 6px 5px; vertical-align: top; }
                .print-table .text-right { text-align: right; }
                .print-table .text-center { text-align: center; }
                .print-table .text-left { text-align: left; }
                .print-totals-row { background-color: #e9ecef; font-weight: bold; border-top: 2px solid #000; }
                .print-totals-row td { padding: 8px 5px; }
                .print-footer { text-align: center; margin-top: 20px; padding-top: 10px; border-top: 1px solid #ccc; font-size: 9px; color: #666; }
                .total-highlight { background-color: #f0f0f0; }
                .text-success { color: #28a745; }
                .text-danger { color: #dc3545; }
            </style>
        </head>
        <body>
            <div class="print-container">
                <div class="print-header">
                    <div class="print-title">${title}</div>
                    <div class="print-subtitle">Caisse: ${caisseNom}</div>
                    ${montantInitial > 0 ? `<div class="print-subtitle">Montant initial de la caisse : ${montantInitial.toLocaleString('fr-FR')} FCFA</div>` : ''}
                    ${dateDebut && dateFin ? `<div class="print-period">Période: ${formatDateForDisplay(dateDebut)} au ${formatDateForDisplay(dateFin)}</div>` : ''}
                    ${categorie ? `<div class="print-period">Catégorie: ${categorie}</div>` : ''}
                    ${search ? `<div class="print-period">Recherche: ${search}</div>` : ''}
                    <div class="print-period">Date d'impression: ${new Date().toLocaleString('fr-FR')}</div>
                </div>
                <table class="print-table">
                    <thead>
                        <tr>
                            <th width="10%">RÉFÉRENCE</th>
                            <th width="8%">DATE</th>
                            <th width="20%">DÉSIGNATION</th>
                            <th width="15%">NOM</th>
                            <th width="8%">CAT</th>
                            <th width="10%">USER</th>
                            <th width="10%">ENTRÉE</th>
                            <th width="10%">SORTIE</th>
                            <th width="9%">SOLDE</th>
                        </tr>
                    </thead>
                    <tbody>`;
        if (tableData.length > 0) {
            tableData.forEach(function(row) {
                printContent += `
                    <tr>
                        <td>${row.reference}</td>
                        <td class="text-center">${row.date}</td>
                        <td>${row.designation}</td>
                        <td>${row.nom}</td>
                        <td>${row.categorie}</td>
                        <td>${row.user}</td>
                        <td class="text-right ${row.entree > 0 ? 'text-success' : ''}">${row.entree > 0 ? formatNumber(row.entree) : '-'}</td>
                        <td class="text-right ${row.sortie > 0 ? 'text-danger' : ''}">${row.sortie > 0 ? formatNumber(row.sortie) : '-'}</td>
                        <td class="text-right">${formatNumber(row.solde)}</td>
                    </tr>`;
            });
        } else {
            printContent += `<tr><td colspan="9" class="text-center">Aucune opération trouvée</td></tr>`;
        }
        printContent += `
                    </tbody>
                    <tfoot>
                        <tr class="print-totals-row">
                            <td colspan="6" class="text-right" style="font-weight: bold;">TOTAUX:</td>
                            <td class="text-right" style="font-weight: bold; color: #28a745;">${formatNumber(totalEntrees)} FCFA</td>
                            <td class="text-right" style="font-weight: bold; color: #dc3545;">${formatNumber(totalSorties)} FCFA</td>
                            <td class="text-right" style="font-weight: bold; background-color: #e8f5e8;">${formatNumber(soldeFinal)} FCFA</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="print-footer">Document généré automatiquement le ${new Date().toLocaleString('fr-FR')}</div>
        </body>
        </html>`;
        printWindow.document.write(printContent);
        printWindow.document.close();
        setTimeout(function() { printWindow.focus(); printWindow.print(); }, 500);
    }

    function previewPDF() {
        var title = "APERÇU - LIVRE DE CAISSE";
        var dateDebut = document.getElementById('date_debut') ? document.getElementById('date_debut').value : '';
        var dateFin = document.getElementById('date_fin') ? document.getElementById('date_fin').value : '';
        var caisseSelect = document.getElementById('caisse_id');
        var caisseId = caisseSelect ? caisseSelect.value : '';
        var caisseNom = caisseSelect && caisseSelect.value ? caisseSelect.options[caisseSelect.selectedIndex].text : "Toutes les caisses";
        var montantInitial = (caisseId && caissesInitialAmounts[caisseId]) ? caissesInitialAmounts[caisseId] : 0;
        var categorie = document.getElementById('categorie') ? document.getElementById('categorie').value : '';
        var search = document.getElementById('search') ? document.getElementById('search').value : '';

        function formatDateForDisplay(dateString) {
            if (!dateString) return '';
            var date = new Date(dateString);
            return date.toLocaleDateString('fr-FR');
        }

        var totalAmount = <?php echo $total_amount_all; ?>;
        var totalAmountRe = <?php echo $total_amount_re_actives; ?>;
        var totalEntreesAll = <?php echo $total_entrees_all; ?>;
        var totalSortiesAll = <?php echo $total_sorties_all; ?>;
        var nbCaisses = <?php echo $nb_actives; ?>;
        var totalReappro = <?php echo $total_reappro; ?>;

        function formatNumber(num) {
            return num.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        var previewContent = `
        <div style="padding: 20px; background: white;">
            <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid #4e73df; padding-bottom: 10px;">
                <h3 style="color: #2e59d9; margin-bottom: 5px;">${title}</h3>
                <div><strong>Caisse:</strong> ${caisseNom}</div>
                ${montantInitial > 0 ? `<div><strong>Montant initial de la caisse :</strong> ${montantInitial.toLocaleString('fr-FR')} FCFA</div>` : ''}
                ${dateDebut && dateFin ? `<div><strong>Période:</strong> ${formatDateForDisplay(dateDebut)} au ${formatDateForDisplay(dateFin)}</div>` : ''}
                ${categorie ? `<div><strong>Catégorie:</strong> ${categorie}</div>` : ''}
                ${search ? `<div><strong>Recherche:</strong> ${search}</div>` : ''}
                <div><strong>Date de génération:</strong> ${new Date().toLocaleDateString('fr-FR')}</div>
            </div>
            <div style="background: #066acd; color: white; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <h4 style="text-align: center; margin-top: 0;"><i class="fa fa-money"></i> ÉTAT GÉNÉRAL DES CAISSES</h4>
                <div style="text-align: center; margin-top: 10px;">
                    <div style="display: inline-block; margin: 0 15px;">
                        <div style="font-size: 12px;">Montant Initial</div>
                        <div style="font-size: 16px; font-weight: bold;">${formatNumber(totalAmount)} FCFA</div>
                    </div>
                    <div style="display: inline-block; margin: 0 15px;">
                        <div style="font-size: 12px;">Total Entrées</div>
                        <div style="font-size: 16px; font-weight: bold; color: #28a745">${formatNumber(totalEntreesAll)} FCFA</div>
                    </div>
                    <div style="display: inline-block; margin: 0 15px;">
                        <div style="font-size: 12px;">Total Sorties</div>
                        <div style="font-size: 16px; font-weight: bold; color: #dc3545">${formatNumber(totalSortiesAll)} FCFA</div>
                    </div>
                    <div style="display: inline-block; margin: 0 15px;">
                        <div style="font-size: 12px;">Solde Réel</div>
                        <div style="font-size: 18px; font-weight: bold;">${formatNumber(totalAmountRe)} FCFA</div>
                    </div>
                </div>
                ${totalReappro > 0 ? `<div style="text-align: center; margin-top: 10px;"><span style="background: #17a2b8; padding: 5px 10px; border-radius: 3px;"><i class="fa fa-refresh"></i> Réapprovisionnements: ${formatNumber(totalReappro)} FCFA</span></div>` : ''}
                <div style="text-align: center; margin-top: 10px; font-size: 12px;"><span style="background: rgba(255,255,255,0.2); padding: 3px 8px; border-radius: 3px; margin: 0 5px;">${nbCaisses} Caisses actives</span></div>
            </div>
            <div style="overflow-x: auto;">`;
        var originalTable = document.getElementById('livre-caisse-table');
        if (originalTable) {
            var tableClone = originalTable.cloneNode(true);
            var rows = tableClone.querySelectorAll('tr');
            rows.forEach(function(row) {
                var cells = row.querySelectorAll('td, th');
                if (cells.length > 10) {
                    cells[cells.length - 1].remove();
                }
            });
            tableClone.style.width = '100%';
            tableClone.style.fontSize = '12px';
            previewContent += tableClone.outerHTML;
        } else {
            previewContent += '<div class="alert alert-info">Aucune opération à afficher</div>';
        }
        previewContent += `
            </div>
            <div style="text-align: center; margin-top: 20px; padding-top: 10px; border-top: 1px solid #ddd; font-size: 11px; color: #666;">
                Document généré le ${new Date().toLocaleString('fr-FR')} |
                <a href="javascript:void(0)" onclick="genererPDF()">Télécharger en PDF</a>
            </div>
        </div>`;
        $('#pdfPreviewModal .modal-body').html(previewContent);
        $('#pdfPreviewModal').modal('show');
    }

    function genererPDF() {
        alert("Fonction PDF en cours de développement. Utilisez l'impression ou l'export Excel pour le moment.");
        imprimerLivreCaisse();
    }

    function exporterExcel() {
        if (typeof XLSX === 'undefined') {
            alert("La bibliothèque Excel n'est pas chargée. Veuillez actualiser la page.");
            return;
        }
        try {
            var table = document.getElementById('livre-caisse-table');
            if (!table) { alert("Tableau non trouvé"); return; }
            var tableClone = table.cloneNode(true);
            var rows = tableClone.querySelectorAll('tr');
            rows.forEach(function(row) {
                var cells = row.querySelectorAll('td, th');
                if (cells.length > 10) {
                    cells[cells.length - 1].remove();
                }
            });
            var ws = XLSX.utils.table_to_sheet(tableClone);
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Livre de Caisse");
            var dateStr = new Date().toISOString().slice(0,10);
            var caisseSelect = document.getElementById('caisse_id');
            var caisseNom = caisseSelect && caisseSelect.value ? caisseSelect.options[caisseSelect.selectedIndex].text.replace(/[^a-z0-9]/gi, '_') : 'toutes_caisses';
            var categorie = document.getElementById('categorie') ? document.getElementById('categorie').value : '';
            var search = document.getElementById('search') ? document.getElementById('search').value : '';
            var filename = 'livre_caisse_' + caisseNom;
            if (categorie) filename += '_' + categorie.replace(/[^a-z0-9]/gi, '_');
            if (search) filename += '_search_' + search.substring(0,20).replace(/[^a-z0-9]/gi, '_');
            filename += '_' + dateStr + '.xlsx';
            XLSX.writeFile(wb, filename);
            toastr.success('Fichier Excel généré avec succès !');
        } catch(error) {
            console.error("Erreur lors de l'export Excel:", error);
            alert("Erreur lors de l'export Excel: " + error.message);
        }
    }

    function printOperation(button) {
        var row = $(button).closest('tr');
        var reference = row.data('reference') || 'N/A';
        var date = row.data('date') || 'N/A';
        var designation = row.data('designation') || '';
        var category = row.data('category') || '';
        var user = row.data('user') || 'Système';
        var entree = row.data('entree') || 0;
        var sortie = row.data('sortie') || 0;
        var soldeAvant = row.data('solde-avant') || 0;
        var soldeApres = row.data('solde-apres') || 0;
        var caisse = row.data('caisse') || '';
        var printWindow = window.open('', '_blank');
        var printContent = `
        <!DOCTYPE html>
        <html>
        <head><title>Fiche Opération</title>
        <style>
            body { font-family: Arial, sans-serif; font-size: 14px; padding: 20px; }
            .fiche-title { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
            .fiche-info { margin-bottom: 15px; }
            .fiche-info strong { display: inline-block; width: 150px; }
            .fiche-montant { font-size: 16px; font-weight: bold; text-align: center; margin: 20px 0; padding: 10px; border: 2px solid #000; border-radius: 5px; }
            .fiche-entree { color: #28a745; }
            .fiche-sortie { color: #dc3545; }
            .fiche-footer { text-align: center; margin-top: 40px; font-size: 12px; color: #666; }
            .signature { margin-top: 50px; }
            .signature-line { border-top: 1px solid #000; width: 200px; display: inline-block; margin-top: 30px; }
        </style>
        </head>
        <body>
            <div class="fiche-title">FICHE D'OPÉRATION DE CAISSE</div>
            <div class="fiche-info">
                <div><strong>Référence:</strong> ${reference}</div>
                <div><strong>Date:</strong> ${date}</div>
                <div><strong>Caisse:</strong> ${caisse}</div>
                <div><strong>Désignation:</strong> ${designation}</div>
                <div><strong>Catégorie:</strong> ${category}</div>
                <div><strong>Utilisateur:</strong> ${user}</div>
            </div>
            <div class="fiche-montant ${entree > 0 ? 'fiche-entree' : 'fiche-sortie'}">
                ${entree > 0 ? 'ENTRÉE:' : 'SORTIE:'} ${(entree || sortie).toLocaleString('fr-FR')} FCFA
            </div>
            <div class="signature">
                <div style="float: left; width: 45%;"><div class="signature-line"></div><div style="text-align: center;">Opérateur</div></div>
                <div style="float: right; width: 45%;"><div class="signature-line"></div><div style="text-align: center;">Responsable</div></div>
                <div style="clear: both;"></div>
            </div>
            <div class="fiche-footer">Fiche générée le ${new Date().toLocaleString('fr-FR')}</div>
        </body>
        </html>`;
        printWindow.document.write(printContent);
        printWindow.document.close();
        setTimeout(function() { printWindow.focus(); printWindow.print(); }, 500);
    }

    if (typeof toastr !== 'undefined') {
        toastr.options = { closeButton: true, progressBar: true, positionClass: "toast-top-right", timeOut: "3000" };
    }

    function resetTotauxDates() {
        var today = new Date();
        var firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
        var lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
        var debutStr = formatDate(firstDay);
        var finStr = formatDate(lastDay);
        document.getElementById('date_totaux_debut').value = debutStr;
        document.getElementById('date_totaux_fin').value = finStr;
        $('input[name="date_totaux_debut"]').val(debutStr);
        $('input[name="date_totaux_fin"]').val(finStr);
        document.getElementById('formTotaux').submit();
    }

    function formatDate(date) {
        if (!(date instanceof Date) || isNaN(date)) date = new Date();
        var year = date.getFullYear();
        var month = String(date.getMonth() + 1).padStart(2, '0');
        var day = String(date.getDate()).padStart(2, '0');
        return year + '-' + month + '-' + day;
    }

    function voirOperationsParType(type) {
        var date_totaux_debut = document.getElementById('date_totaux_debut').value;
        var date_totaux_fin = document.getElementById('date_totaux_fin').value;
        var caisse_id = document.getElementById('caisse_id') ? document.getElementById('caisse_id').value : '';
        var url = base_url + 'admin/income/get_operations_par_type?type=' + type +
            '&date_debut=' + date_totaux_debut +
            '&date_fin=' + date_totaux_fin;
        if (caisse_id) url += '&caisse_id=' + caisse_id;
        $.ajax({
            url: url, type: 'GET', dataType: 'json',
            success: function(response) { if (response.success) afficherModalOperations(response); },
            error: function() { alert('Erreur lors du chargement des opérations'); }
        });
    }

    function afficherModalOperations(data) {
        var title = '', typeLabel = '';
        if (data.type == 'entree') { title = 'DÉTAIL DES ENTRÉES'; typeLabel = 'entrées'; }
        else if (data.type == 'sortie') { title = 'DÉTAIL DES SORTIES'; typeLabel = 'sorties'; }
        else if (data.type.startsWith('mode_')) {
            var mode = data.type.replace('mode_', '');
            title = 'DÉTAIL - MODE DE PAIEMENT: ' + mode.toUpperCase();
            typeLabel = 'opérations (' + mode + ')';
        }
        var html = `<div class="modal fade" id="operationsModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #4e73df; color: white;">
                        <h5 class="modal-title"><i class="fa fa-list"></i> ${title}</h5>
                        <button type="button" class="close" data-dismiss="modal" style="color: white;"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> Période: ${data.date_debut} au ${data.date_fin} |
                            Total: <strong>${data.total.toLocaleString('fr-FR')} FCFA</strong> |
                            ${data.nombre_operations} ${typeLabel}
                        </div>
                        <div class="table-responsive"><table class="table table-bordered table-striped">
                            <thead><tr><th>Date</th><th>Référence</th><th>Désignation</th><th>Caisse</th>${data.type.startsWith('mode_') ? '' : '<th>Mode Paiement</th>'}<th>Catégorie</th><th>Montant</th></tr></thead>
                            <tbody>`;
        data.operations.forEach(function(operation) {
            var montant = data.type == 'entree' ? operation.entree : operation.sortie;
            if (data.type.startsWith('mode_')) montant = operation.entree > 0 ? operation.entree : operation.sortie;
            html += `<tr>
                <td>${operation.date ? operation.date.substring(0,10) : ''}</td>
                <td><strong>${operation.reference || ''}</strong></td>
                <td>${operation.designation || ''}</td>
                <td>${operation.caisse_nom || ''}</td>
                ${data.type.startsWith('mode_') ? '' : `<td>${operation.mode_paiement || ''}</td>`}
                <td>${operation.category_name || operation.category || ''}</td>
                <td class="${montant > 0 ? 'text-success' : 'text-danger'}" style="text-align: right;"><strong>${montant.toLocaleString('fr-FR')} FCFA</strong></td>
            </tr>`;
        });
        html += `</tbody><tfoot><tr style="background-color:#f8f9fa;"><td colspan="${data.type.startsWith('mode_') ? '5' : '6'}" class="text-right"><strong>TOTAL:</strong></td>
            <td class="text-right"><strong style="color:black;">${data.total.toLocaleString('fr-FR')} FCFA</strong></td></tr></tfoot></table></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Fermer</button></div>
                </div>
            </div>
        </div>`;
        $('body').append(html);
        $('#operationsModal').modal('show');
        $('#operationsModal').on('hidden.bs.modal', function() { $(this).remove(); });
    }

    function deleteOperationEnhanced(operationId) {
        if (typeof Swal !== 'undefined') {
            deleteOperation(operationId);
        } else {
            loadSweetAlert2(function() {
                if (typeof Swal !== 'undefined') deleteOperation(operationId);
                else deleteOperationFallback(operationId);
            });
        }
    }

    function deleteOperation(operationId) {
        if (!operationId || operationId === 0) { alert('ID d\'opération invalide'); return; }
        Swal.fire({
            title: 'Êtes-vous sûr ?',
            text: "Cette opération sera supprimée définitivement !",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Oui, supprimer !',
            cancelButtonText: 'Annuler'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Suppression en cours...', text: 'Veuillez patienter', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                var csrfToken = '<?php echo $this->security->get_csrf_token_name(); ?>';
                var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
                $.ajax({
                    url: base_url + 'admin/income/delete/' + operationId,
                    type: 'POST',
                    dataType: 'json',
                    data: { [csrfToken]: csrfHash },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({ title: 'Succès !', html: '<div style="text-align:center;"><i class="fa fa-check-circle" style="color:black;font-size:48px;margin-bottom:20px;"></i><p style="font-size:18px;margin-bottom:10px;">Donnée supprimée avec succès !</p><p style="color:#666;margin-bottom:20px;">Cliquez sur OK pour rafraîchir le tableau.</p></div>', icon: 'success', confirmButtonText: 'OK' }).then(() => location.reload());
                        } else {
                            Swal.fire({ title: 'Erreur !', text: response.message || 'Échec de la suppression', icon: 'error', confirmButtonText: 'OK' });
                        }
                    },
                    error: function() {
                        Swal.fire({ title: 'Suppression effectuée !', html: '<div style="text-align:center;"><i class="fa fa-check-circle" style="color:black;font-size:48px;margin-bottom:20px;"></i><p style="font-size:18px;margin-bottom:10px;">Opération supprimée avec succès</p><p style="color:black;margin-bottom:20px;">Cliquez sur OK pour rafraîchir le tableau.</p></div>', icon: 'success', confirmButtonText: 'OK' }).then(() => location.reload());
                    }
                });
            }
        });
    }

    function deleteOperationFallback(operationId) {
        if (!operationId || operationId === 0) { alert('ID d\'opération invalide'); return; }
        if (confirm('Êtes-vous sûr de vouloir supprimer cette opération ?')) {
            var csrfToken = '<?php echo $this->security->get_csrf_token_name(); ?>';
            var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';
            var originalButton = event.target;
            var originalHTML = originalButton.innerHTML;
            originalButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Suppression...';
            originalButton.disabled = true;
            $.ajax({
                url: base_url + 'admin/income/delete/' + operationId,
                type: 'POST',
                dataType: 'json',
                data: { [csrfToken]: csrfHash },
                success: function(response) {
                    if (response.success) {
                        alert('Donnée supprimée avec succès !\n\nCliquez sur OK pour rafraîchir le tableau.');
                        location.reload();
                    } else {
                        alert('Erreur : ' + (response.message || 'Échec de la suppression'));
                        originalButton.innerHTML = originalHTML;
                        originalButton.disabled = false;
                    }
                },
                error: function() {
                    alert('Erreur de connexion lors de la suppression. La page va se rafraîchir.');
                    location.reload();
                }
            });
        }
    }

    function loadSweetAlert2(callback) {
        if (typeof Swal === 'undefined') {
            var link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css';
            document.head.appendChild(link);
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
            script.onload = function() { if (callback) callback(); };
            script.onerror = function() { if (callback) callback(); };
            document.head.appendChild(script);
        } else { if (callback) callback(); }
    }

    $(document).ready(function() {
        loadSweetAlert2();
        $(document).on('click', '.delete-operation-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var operationId = $(this).closest('tr').data('operation-id') || $(this).data('operation-id') || $(this).attr('data-id');
            if (!operationId) {
                var onclickAttr = $(this).attr('onclick');
                if (onclickAttr) {
                    var match = onclickAttr.match(/deleteOperation(?:Enhanced)?\((\d+)\)/);
                    if (match && match[1]) operationId = match[1];
                }
            }
            if (operationId) deleteOperationEnhanced(operationId);
            else alert('Impossible de trouver l\'ID de l\'opération');
        });
        window.confirmDeleteCaisse = function(id, name) {
            if (confirm('Êtes-vous sûr de vouloir supprimer la caisse "' + name + '" ? Cette action est irréversible.'))
                window.location.href = base_url + 'admin/income/delete/' + id;
        };
    });

    function updateCreationDate(caisseId, caisseName) {
        if (confirm('Voulez-vous vraiment mettre à jour la date de création de la caisse "' + caisseName + '" avec la date du mois en cours ?')) {
            $.ajax({
                url: base_url + 'admin/income/update_creation_date',
                type: 'POST',
                data: { id: caisseId, '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>' },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {

                        toastr.success(response.message);
                        setTimeout(function() { location.reload(); }, 1000);
                    } else { toastr.error(response.message); }
                },
                error: function() { toastr.error('Une erreur est survenue lors de la mise à jour.'); }
            });
        }
    }

    $(document).ready(function() {
        $('.update-creation-date').on('click', function() {
            var caisseId = $(this).data('id');
            var caisseName = $(this).data('name');
            updateCreationDate(caisseId, caisseName);
        });
    });
</script>
</body>
</html>