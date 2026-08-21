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
    <style>

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
            border-color: #117a8b;
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
    </style>
    <!-- CSS additionnel pour améliorer l'affichage -->
    <style>
        /* CSS existant conservé */
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
    
            .box-tools, .filter-form, .modal, .caisse-card, .total-centralisation {
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
            color: #666;
        }
    
        .total-stat-subtext {
            font-size: 11px;
            color: #888;
            margin-top: 5px;
        }
    
        /* Couleurs spécifiques pour les boîtes */
        .box-montant-initial {
            border-left: 4px solid #1cc88a;
        }
    
        .box-entrees {
            border-left: 4px solid #36b9cc;
        }
    
        .box-sorties {
            border-left: 4px solid #f6c23e;
        }
    
        .box-solde-reel {
            border-left: 4px solid #4e73df;
        }
    
        /* Pour les totaux généraux */
        .etat-global-title {
            color: #2e59d9;
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
            color: #5a5c69;
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
                            // 1. Récupérer les totaux de TOUTES les caisses (actives + inactives)
                            $this->db->select('
                                SUM(amount) as total_amount_all,
                                SUM(total_entrees) as total_entrees_all,
                                SUM(total_sorties) as total_sorties_all,
                                COUNT(*) as nb_caisses_total
                            ');
                            $this->db->from('income');
                            $this->db->where('is_deleted', 'no');
                            $totaux_toutes_caisses = $this->db->get()->row();
    
                            // 2. Récupérer les totaux des caisses ACTIVES seulement
                            $this->db->select('
                                SUM(amount_re) as total_amount_re_actives,
                                SUM(amount) as total_amount_actives,
                                COUNT(*) as nb_caisses_actives
                            ');
                            $this->db->from('income');
                            $this->db->where('is_deleted', 'no');
                            $this->db->where('est_actif', '1');
                            $totaux_caisses_actives = $this->db->get()->row();
    
                            // 3. Calculer les variables pour l'affichage
                            $total_amount = $totaux_toutes_caisses->total_amount_all ?? 0;
                            $total_amount_re = $totaux_caisses_actives->total_amount_re_actives ?? 0;
                            $total_entrees_all = $totaux_toutes_caisses->total_entrees_all ?? 0;
                            $total_sorties_all = $totaux_toutes_caisses->total_sorties_all ?? 0;
                            $nb_caisses_total = $totaux_toutes_caisses->nb_caisses_total ?? 0;
                            $nb_caisses_actives = $totaux_caisses_actives->nb_caisses_actives ?? 0;
    
                            // 4. Récupérer le total des réapprovisionnements
                            $date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : date('Y-m-01');
                            $date_fin   = isset($_GET['date_fin']) ? $_GET['date_fin']   : date('Y-m-d');
    
                            $this->db->select('SUM(montant) as total_reappro');
                            $this->db->from('operation_caisse');
                            $this->db->where('date >=', $date_debut . ' 00:00:00');
                            $this->db->where('date <=', $date_fin . ' 23:59:59');
                            $this->db->like('reference', 'TRF-', 'after');
                            $this->db->where('type_operation', 'ENTREE','SORTIE');
    
                            $total_reappro_result = $this->db->get()->row();
                            $total_reappro = $total_reappro_result->total_reappro ?? 0;
                            ?>
    
                            <?php if ($total_amount_re > 0 || $total_entrees_all > 0): ?>
                                <div class="total-centralisation">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <h4 class="etat-global-title" style="color: black">
                                                <i class="fa fa-money"></i> ÉTAT GÉNÉRAL DES CAISSES
                                            </h4>
                                        </div>
                                    </div>
    
                                    <div class="row" style="margin-top: 10px;">
                                        <!-- Montant Initial Total -->
                                        <div class="col-md-3 col-sm-6 text-center" style="width: 300px">
                                            <div class="total-stat-box box-montant-initial">
                                                <div class="total-stat-value" style="color: #1cc88a;">
                                                    <?php echo number_format($total_amount, 0, ',', ' '); ?> FCFA
                                                </div>
                                                <div class="total-stat-label">
                                                    <i class="fa fa-bank"></i> Montant Initial Total
                                                </div>
                                                <div class="total-stat-subtext">
                                                    <?php echo $nb_caisses_total; ?> caisses total
                                                </div>
                                            </div>
                                        </div>
    
                                        <!-- Total Entrées -->
                                        <div class="col-md-3 col-sm-6 text-center" style="width: 300px">
                                            <div class="total-stat-box box-entrees">
                                                <div class="total-stat-value" style="color: #36b9cc;">
                                                    <?php echo number_format($total_entrees_all, 0, ',', ' '); ?> FCFA
                                                </div>
                                                <div class="total-stat-label">
                                                    <i class="fa fa-sign-in"></i> Total Entrées
                                                </div>
                                            </div>
                                        </div>
    
                                        <!-- Total Sorties -->
                                        <div class="col-md-3 col-sm-6 text-center" style="width: 300px">
                                            <div class="total-stat-box box-sorties">
                                                <div class="total-stat-value" style="color: #f6c23e;">
                                                    <?php echo number_format($total_sorties_all, 0, ',', ' '); ?> FCFA
                                                </div>
                                                <div class="total-stat-label">
                                                    <i class="fa fa-sign-out"></i> Total Sorties
                                                </div>
                                            </div>
                                        </div>
    
                                        <!-- Solde Réel Total -->
                                        <div class="col-md-3 col-sm-6 text-center" style="width: 300px">
                                            <div class="total-stat-box box-solde-reel">
                                                <div class="total-stat-value" style="color: #4e73df; font-size: 24px;">
                                                    <?php echo number_format($total_amount_re, 0, ',', ' '); ?> FCFA
                                                </div>
                                                <div class="total-stat-label">
                                                    <i class="fa fa-calculator"></i> Solde Réel Total
                                                </div>
                                                <div class="total-stat-subtext">
                                                    <?php echo $nb_caisses_actives; ?> caisses actives
                                                </div>
                                            </div>
                                        </div>
                                    </div>
    
                                    <?php if ($total_reappro > 0): ?>
                                        <div class="row" style="margin-top: 10px;">
                                            <div class="col-md-12 text-center">
                                                <span class="total-reappro-badge">
                                                    <i class="fa fa-refresh"></i> Total Réappro: <?php echo number_format($total_reappro, 2, ',', ' '); ?> FCFA
                                                </span>
                                            </div>
                                        </div>
                                    <?php endif; ?>
    
                                   <div class="row" style="margin-top: 15px;">
                                        <div class="col-md-12 text-center">
                                            <span class="info-badge"><?php echo $nb_caisses_actives; ?> Caisses actives</span>
                                            <span class="info-badge info-badge-secondary"><?php echo ($nb_caisses_total - $nb_caisses_actives); ?> Caisses inactives</span><br>
                                            <!--<span class="info-badge info-badge-success"><?php echo number_format($total_entrees_all - $total_sorties_all, 2, ',', ' '); ?> FCFA Net</span>-->
                                            <span class="info-badge info-badge-light">
                                                <i class="fa fa-clock-o"></i> <?php echo date('d/m/Y H:i'); ?>
                                            </span>
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
                                                            <i class="fa fa-money"></i> Initial:<br> <?php echo number_format($amount, 2, ',', ' '); ?> FCFA
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
                                                        <i class="fa fa-plus"></i> <?php echo number_format($total_entrees, 0, ',', ' '); ?>
                                                    </span>
                                                    <span class="badge badge-danger" style="font-size: 10px; margin-bottom: 2px;">
                                                        <i class="fa fa-minus"></i> <?php echo number_format($total_sorties, 0, ',', ' '); ?>
                                                    </span>
                                                    <span class="badge badge-solde <?php echo $amount_re >= 0 ? 'badge-solde-positif' : 'badge-solde-negatif'; ?>" style="background-color: grey">
                                                        <?php echo $amount_re >= 0 ? '+' : ''; ?>
                                                        <?php echo number_format($amount_re, 0, ',', ' '); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row" style="margin-top: 10px;">
                                            <div class="col-md-12">
                                                <div class="btn-group btn-group-xs">
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
                                                        <!-- BOUTON + (Ajouter réapprovisionnement) -->
                                                        <button class="btn btn-success increaseAmount"
                                                                data-row-id="<?php echo $caisse['id']; ?>"
                                                                title="Réapprovisionner la caisse">
                                                            <i class="fa fa-plus"></i>
                                                        </button>

                                                        <!-- BOUTON LISTE (Voir historique réapprovisionnement) -->
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

                                                    <!-- BOUTON SUPPRESSION -->
                                                    <?php if ($this->rbac->hasPrivilege('caisse', 'can_delete')): ?>
                                                        <a href="<?php echo base_url('admin/income/delete/' . $caisse['id']); ?>"
                                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette caisse ?')"
                                                           class="btn btn-danger"
                                                           title="Supprimer la caisse">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    <?php endif; ?>
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
    
                            <div class="table-responsive mailbox-messages">
                                <table class="table table-striped table-bordered table-hover table-livre-caisse" id="livre-caisse-table">
                                    <thead>
                                    <tr>
                                        <th width="10%">RÉFÉRENCE</th>
                                        <th width="10%">DATE</th>
                                        <th width="20%">DÉSIGNATIONS</th>
                                        <th width="20%">NOM</th>
                                        <th width="8%">CAT</th>
                                        <th width="10%">User</th>
                                        <th width="10%">ENTRÉE</th>
                                        <th width="10%">SORTIE</th>
                                        <th width="12%">SOLDE AVANT</th>
                                        <th width="12%">SOLDE APRÈS</th>
                                        <th width="8%" class="no-print">ACTIONS</th>
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
                                                data-nom="<?php echo htmlspecialchars($operation['entreprise_nom'] ?? $operation['nom'] ?? ''); ?>"
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
                                                    <div class="operation-nom">
                                                        <?php echo htmlspecialchars($operation['entreprise_nom'] ?? $operation['nom'] ?? ''); ?>

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
                                                    </div>
                                                </td>
                                                <td class="no-print text-center">
                                                    <div class="btn-group btn-group-xs" role="group">
                                                        <!-- Bouton Imprimer -->
                                                        <button class="btn btn-xs btn-info print-operation-btn"
                                                                title="Imprimer cette ligne"
                                                                onclick="printOperation(this)">
                                                            <i class="fa fa-print"></i>
                                                        </button>
    
                                                        <?php if ($this->rbac->hasPrivilege('caisse', 'can_edit')): ?>
                                                            <!-- Bouton Éditer -->
                                                          <!--  <button class="btn btn-xs btn-warning edit-operation-btn"
                                                                    title="Éditer cette opération"
                                                                    onclick="editOperation(<?php echo $operation['id']; ?>)">
                                                                <i class="fa fa-edit"></i>
                                                            </button>-->

                                                        <?php endif; ?>
    
                                                        <?php if ($this->rbac->hasPrivilege('caisse', 'can_delete')): ?>
                                                            <!-- Bouton Supprimer -->
                                                            <button class="btn btn-xs btn-danger delete-operation-btn"
                                                                    title="Supprimer cette opération"
                                                                    data-id="<?php echo $operation['id']; ?>">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
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

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="montant">Nom du concerné</label>
                                    <input type="text" class="form-control" id="nom" name="nom"
                                           required>
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
                   <!-- <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                        <button type="submit" id="submitBTN" class="btn btn-primary">Enregistrer</button>
                    </div>-->
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
                    <!-- Le contenu PDF sera inséré ici -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                   <!-- <button type="button" class="btn btn-primary" onclick="genererPDF()">
                        <i class="fa fa-download"></i> Télécharger PDF
                    </button>-->
                </div>
            </div>
        </div>
    </div>
    
    <script>
        ( function ( $ ) {
            'use strict';
            $(document).ready(function () {
                initDatatable('income-list','admin/income/getincomelist',[],[],10);
                initReapproButtons();
            });
        } ( jQuery ) )
    
        var base_url = '<?php echo base_url() ?>';
    
        // Fonction pour initialiser les événements des boutons de réapprovisionnement
        function initReapproButtons() {
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
    
        // Fonction pour set un increase
        function form_increase(id) {
            $.ajax({
                'url'   : base_url + 'Income/form_increase',
                'type'  : 'GET',
                'data'  : {
                    'id'        : id,
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
         */
        $(document).on("click", `#submitBTN`, function (e) {
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
                        $(`#increaseForm`).modal("hide");
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
    
        // Gestion du toggle status de caisse
        $(document).on('click', '.toggle-status', function(e) {
            e.preventDefault();
    
            var id = $(this).data('id');
            var currentStatus = $(this).data('status');
            var newStatus = currentStatus == '1' ? '0' : '1';
            var button = $(this);
    
            // Confirmation avant fermeture
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
                data: {
                    id: id,
                    status: newStatus
                },
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
    
        // Script pour le changement de catégorie de dépense
        document.getElementById('exp_head_id').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var categoryName = selectedOption.getAttribute('data-name');
            document.getElementById('exp_category_name').value = categoryName || '';
        });
    
    
    
        // Les autres fonctions JavaScript (imprimerLivreCaisse, genererPDF, exporterExcel, etc.)
        // restent inchangées et sont conservées telles quelles
    </script>
    <script>
        // ==============================================
        // FONCTIONS D'EXPORT SIMPLIFIÉES ET FONCTIONNELLES
        // ==============================================
    
        // Fonction d'impression simple et fiable
        function imprimerLivreCaisse() {
            // Créer une fenêtre d'impression
            var printWindow = window.open('', '_blank');
    
            // Titre et informations
            var title = "LIVRE DE CAISSE";
            var dateDebut = document.getElementById('date_debut') ? document.getElementById('date_debut').value : '';
            var dateFin = document.getElementById('date_fin') ? document.getElementById('date_fin').value : '';
            var caisseSelect = document.getElementById('caisse_id');
            var caisseNom = caisseSelect && caisseSelect.value ? caisseSelect.options[caisseSelect.selectedIndex].text : "Toutes les caisses";
    
            // Formatage des dates
            function formatDateForDisplay(dateString) {
                if (!dateString) return '';
                var date = new Date(dateString);
                return date.toLocaleDateString('fr-FR');
            }
    
            // Récupérer les totaux depuis le tfoot
            var totalEntrees = "0 FCFA";
            var totalSorties = "0 FCFA";
            var totalRow = document.querySelector('.total-row');
            if (totalRow) {
                var cells = totalRow.querySelectorAll('td');
                if (cells.length >= 2) {
                    totalEntrees = cells[0].innerText || "0 FCFA";
                    totalSorties = cells[1].innerText || "0 FCFA";
                }
            }
    
            // Créer le contenu HTML
            var printContent = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>${title}</title>
                <style>
                    @media print {
                        @page {
                            size: landscape;
                            margin: 10mm;
                        }
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
                    .print-table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-top: 10px;
                        font-size: 10px;
                    }
                    .print-table th {
                        background-color: #f2f2f2;
                        border: 1px solid #000;
                        padding: 5px;
                        text-align: center;
                        font-weight: bold;
                    }
                    .print-table td {
                        border: 1px solid #000;
                        padding: 4px;
                    }
                    .print-table .text-entree {
                        color: #28a745;
                        text-align: right;
                    }
                    .print-table .text-sortie {
                        color: #dc3545;
                        text-align: right;
                    }
                    .print-totals {
                        font-weight: bold;
                        background-color: #e9ecef;
                        border-top: 2px solid #000;
                    }
                    .print-footer {
                        text-align: center;
                        margin-top: 20px;
                        font-size: 10px;
                        color: #666;
                    }
                    .no-print {
                        display: none;
                    }
                </style>
            </head>
            <body>
                <div class="print-header">
                    <div class="print-title">${title}</div>
                    <div class="print-subtitle">Caisse: ${caisseNom}</div>
                    ${dateDebut && dateFin ? `<div class="print-period">Période: ${formatDateForDisplay(dateDebut)} au ${formatDateForDisplay(dateFin)}</div>` : ''}
                    <div class="print-period">Date d'impression: ${new Date().toLocaleDateString('fr-FR')}</div>
                </div>
        `;
    
            // Cloner le tableau et le nettoyer pour l'impression
            var originalTable = document.getElementById('livre-caisse-table');
            if (originalTable) {
                var tableClone = originalTable.cloneNode(true);
    
                // Supprimer la colonne ACTIONS
                var rows = tableClone.querySelectorAll('tr');
                rows.forEach(function(row) {
                    var cells = row.querySelectorAll('td, th');
                    if (cells.length > 9) {
                        cells[cells.length - 1].remove(); // Supprimer dernière colonne (actions)
                    }
                });
    
                // Supprimer les classes no-print
                var noPrintElements = tableClone.querySelectorAll('.no-print');
                noPrintElements.forEach(function(el) {
                    el.classList.remove('no-print');
                });
    
                // Ajouter la classe print-table
                tableClone.className = 'print-table';
    
                printContent += tableClone.outerHTML;
            }
    
            // Ajouter les totaux
            printContent += `
            <div style="margin-top: 20px;">
                <table class="print-table">
                    <tr class="print-totals">
                        <td colspan="5" style="text-align: right;"><strong>TOTAUX:</strong></td>
                        <td style="text-align: right; color: #28a745;"><strong>${totalEntrees}</strong></td>
                        <td style="text-align: right; color: #dc3545;"><strong>${totalSorties}</strong></td>
                        <td colspan="2"></td>
                    </tr>
                </table>
            </div>
    
            <div class="print-footer">
                Document généré le ${new Date().toLocaleString('fr-FR')}
            </div>
        </body>
        </html>
        `;
    
            // Écrire le contenu et imprimer
            printWindow.document.write(printContent);
            printWindow.document.close();
    
            // Attendre un peu avant d'imprimer
            setTimeout(function() {
                printWindow.focus();
                printWindow.print();
                // printWindow.close(); // Optionnel: fermer après impression
            }, 500);
        }
    
        // Fonction d'aperçu PDF simplifiée
        function previewPDF() {
            // Créer un contenu d'aperçu simple
            var title = "APERÇU - LIVRE DE CAISSE";
            var dateDebut = document.getElementById('date_debut') ? document.getElementById('date_debut').value : '';
            var dateFin = document.getElementById('date_fin') ? document.getElementById('date_fin').value : '';
            var caisseSelect = document.getElementById('caisse_id');
            var caisseNom = caisseSelect && caisseSelect.value ? caisseSelect.options[caisseSelect.selectedIndex].text : "Toutes les caisses";
    
            function formatDateForDisplay(dateString) {
                if (!dateString) return '';
                var date = new Date(dateString);
                return date.toLocaleDateString('fr-FR');
            }
    
            // Récupérer les totaux PHP
            var totalAmount = <?php echo $total_amount; ?>;
            var totalAmountRe = <?php echo $total_amount_re; ?>;
            var totalEntreesAll = <?php echo $total_entrees_all; ?>;
            var totalSortiesAll = <?php echo $total_sorties_all; ?>;
            var nbCaisses = <?php echo $nb_caisses_actives; ?>;
            var totalReappro = <?php echo $total_reappro; ?>;
    
            // Formatage des nombres
            function formatNumber(num) {
                return num.toLocaleString('fr-FR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
    
            // Créer le contenu de l'aperçu
            var previewContent = `
            <div style="padding: 20px; background: white;">
                <div style="text-align: center; margin-bottom: 20px; border-bottom: 2px solid #4e73df; padding-bottom: 10px;">
                    <h3 style="color: #2e59d9; margin-bottom: 5px;">${title}</h3>
                    <div><strong>Caisse:</strong> ${caisseNom}</div>
                    ${dateDebut && dateFin ? `<div><strong>Période:</strong> ${formatDateForDisplay(dateDebut)} au ${formatDateForDisplay(dateFin)}</div>` : ''}
                    <div><strong>Date de génération:</strong> ${new Date().toLocaleDateString('fr-FR')}</div>
                </div>
    
                <!-- État général -->
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
                ${totalReappro > 0 ? `
                <div style="text-align: center; margin-top: 10px;">
                    <span style="background: #17a2b8; padding: 5px 10px; border-radius: 3px;">
                        <i class="fa fa-refresh"></i> Réapprovisionnements: ${formatNumber(totalReappro)} FCFA
            </span>
            </div>` : ''}
            <div style="text-align: center; margin-top: 10px; font-size: 12px;">
            <span style="background: rgba(255,255,255,0.2); padding: 3px 8px; border-radius: 3px; margin: 0 5px;">
                ${nbCaisses} Caisses actives
            </span>
            </div>
            </div>
    
                <!-- Tableau des opérations -->
            <div style="overflow-x: auto;">
            `;
    
        // Cloner et nettoyer le tableau pour l'aperçu
        var originalTable = document.getElementById('livre-caisse-table');
        if (originalTable) {
            var tableClone = originalTable.cloneNode(true);
    
            // Supprimer la colonne ACTIONS
            var rows = tableClone.querySelectorAll('tr');
            rows.forEach(function(row) {
                var cells = row.querySelectorAll('td, th');
                if (cells.length > 9) {
                    cells[cells.length - 1].remove();
                }
            });
    
            // Appliquer un style simple
            tableClone.style.width = '100%';
            tableClone.style.fontSize = '12px';
    
            previewContent += tableClone.outerHTML;
        } else {
            previewContent += '<div class="alert alert-info">Aucune opération à afficher</div>';
        }
    
        previewContent += `
            </div>
    
                <!-- Pied de page -->
            <div style="text-align: center; margin-top: 20px; padding-top: 10px; border-top: 1px solid #ddd; font-size: 11px; color: #666;">
            Document généré le ${new Date().toLocaleString('fr-FR')} |
            <a href="javascript:void(0)" onclick="genererPDF()">Télécharger en PDF</a>
            </div>
            </div>
            `;
    
        // Afficher dans la modal
        $('#pdfPreviewModal .modal-body').html(previewContent);
        $('#pdfPreviewModal').modal('show');
    }
    
    // Fonction pour générer PDF (version simplifiée)
    function genererPDF() {
        alert("Fonction PDF en cours de développement. Utilisez l'impression ou l'export Excel pour le moment.");
        // Pour l'instant, on utilise l'impression
        imprimerLivreCaisse();
    }
    
    // Fonction pour exporter en Excel (version simplifiée)
    function exporterExcel() {
        // Vérifier si la bibliothèque est disponible
        if (typeof XLSX === 'undefined') {
            alert("La bibliothèque Excel n'est pas chargée. Veuillez actualiser la page.");
            return;
        }
    
        try {
            // Sélectionner le tableau
            var table = document.getElementById('livre-caisse-table');
            if (!table) {
                alert("Tableau non trouvé");
                return;
            }
    
            // Cloner le tableau sans la colonne actions
            var tableClone = table.cloneNode(true);
            var rows = tableClone.querySelectorAll('tr');
            rows.forEach(function(row) {
                var cells = row.querySelectorAll('td, th');
                if (cells.length > 9) {
                    cells[cells.length - 1].remove();
                }
            });
    
            // Convertir le tableau en feuille de calcul
            var ws = XLSX.utils.table_to_sheet(tableClone);
    
            // Créer un nouveau classeur
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Livre de Caisse");
    
            // Générer le nom du fichier
            var dateStr = new Date().toISOString().slice(0,10);
            var caisseSelect = document.getElementById('caisse_id');
            var caisseNom = caisseSelect && caisseSelect.value ?
                caisseSelect.options[caisseSelect.selectedIndex].text.replace(/[^a-z0-9]/gi, '_') :
                'toutes_caisses';
    
            var filename = 'livre_caisse_' + caisseNom + '_' + dateStr + '.xlsx';
    
            // Télécharger le fichier
            XLSX.writeFile(wb, filename);
    
            // Message de confirmation
            toastr.success('Fichier Excel généré avec succès !');
    
        } catch (error) {
            console.error("Erreur lors de l'export Excel:", error);
            alert("Erreur lors de l'export Excel: " + error.message);
        }
    }
    
    // Fonction pour imprimer une ligne individuelle
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
            <head>
            <title>Fiche Opération</title>
            <style>
            body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            padding: 20px;
            }
            .fiche-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            }
            .fiche-info {
            margin-bottom: 15px;
            }
            .fiche-info strong {
            display: inline-block;
            width: 150px;
            }
            .fiche-montant {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            border: 2px solid #000;
            border-radius: 5px;
            }
            .fiche-entree {
            color: #28a745;
            }
            .fiche-sortie {
            color: #dc3545;
            }
            .fiche-footer {
            text-align: center;
            margin-top: 40px;
            font-size: 12px;
            color: #666;
            }
            .signature {
            margin-top: 50px;
            }
            .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            display: inline-block;
            margin-top: 30px;
            }
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
                ${entree > 0 ? 'ENTRÉE:' : 'SORTIE:'}
                ${(entree || sortie).toLocaleString('fr-FR')} FCFA
            </div>
    
    
    
            <div class="signature">
            <div style="float: left; width: 45%;">
            <div class="signature-line"></div>
            <div style="text-align: center;">Opérateur</div>
            </div>
            <div style="float: right; width: 45%;">
            <div class="signature-line"></div>
            <div style="text-align: center;">Responsable</div>
            </div>
            <div style="clear: both;"></div>
            </div>
    
            <div class="fiche-footer">
            Fiche générée le ${new Date().toLocaleString('fr-FR')}
            </div>
            </body>
            </html>
            `;
    
        printWindow.document.write(printContent);
        printWindow.document.close();
    
        setTimeout(function() {
            printWindow.focus();
            printWindow.print();
        }, 500);
    }
    
    // Initialiser toastr si disponible
    if (typeof toastr !== 'undefined') {
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };
    }
    
    // Vérifier le chargement des bibliothèques
    $(document).ready(function() {
        console.log("Page chargée - Boutons d'export prêts");
    
        // Ajouter un écouteur d'événements pour le bouton PDF
        $('#pdfPreviewModal .btn-primary').off('click').on('click', function() {
            genererPDF();
        });
    });
    </script>
    <script>
        // Fallback si les bibliothèques ne sont pas chargées
        function checkLibraries() {
            if (typeof XLSX === 'undefined') {
                console.warn("Bibliothèque Excel non chargée");
                // Recharger les scripts
                loadExcelLibrary();
            }
    
            if (typeof jsPDF === 'undefined') {
                console.warn("Bibliothèque PDF non chargée");
                // Option: on peut utiliser l'impression à la place
            }
        }
    
        function loadExcelLibrary() {
            // Charger la bibliothèque Excel si manquante
            var script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js';
            script.onload = function() {
                console.log("Bibliothèque Excel chargée");
            };
            document.head.appendChild(script);
        }
    
        // Vérifier après un délai
        setTimeout(checkLibraries, 2000);
    
        // Fallback pour l'export Excel
        function safeExportExcel() {
            if (typeof XLSX === 'undefined') {
                if (confirm("La bibliothèque Excel n'est pas chargée. Voulez-vous réessayer ?")) {
                    loadExcelLibrary();
                    setTimeout(function() {
                        if (typeof XLSX !== 'undefined') {
                            exporterExcel();
                        } else {
                            alert("Impossible de charger la bibliothèque. Utilisez l'impression.");
                            imprimerLivreCaisse();
                        }
                    }, 2000);
                }
            } else {
                exporterExcel();
            }
        }
    
        // Remplacer l'appel original
        function exporterExcel() {
            safeExportExcel();
        }
    
        // Fonction pour éditer une opération
        function editOperation(operationId) {
            if (!operationId || operationId === 0) {
                alert('ID d\'opération invalide');
                return;
            }

            // Vous pouvez implémenter cela de plusieurs façons:
            // 1. Redirection vers une page d'édition
            // window.location.href = base_url + 'admin/income/edit_operation/' + operationId;

            // 2. Chargement via modal (recommandé)
            $.ajax({
                url: base_url + 'admin/income/edit_operation_form/' + operationId,
                type: 'GET',
                success: function(response) {
                    // Créer une modal pour l'édition
                    var modalHtml = `
                    <div class="modal fade" id="editOperationModal" tabindex="-1" role="dialog">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Éditer l'opération</h5>
                                    <button type="button" class="close" data-dismiss="modal">
                                        <span>&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    ${response}
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                    // Ajouter la modal au DOM et l'afficher
                    $('body').append(modalHtml);
                    $('#editOperationModal').modal('show');

                    // Nettoyer après fermeture
                    $('#editOperationModal').on('hidden.bs.modal', function() {
                        $(this).remove();
                    });
                },
                error: function() {
                    alert('Erreur lors du chargement du formulaire d\'édition');
                }
            });
        }

        // Alternative: Version avec redirection simple
        function editOperationSimple(operationId) {
            window.location.href = base_url + 'admin/income/edit_operation/' + operationId;
        }
        // Fonction pour supprimer une opération avec confirmation et message
        // Fonction principale pour supprimer une opération
        function deleteOperation(operationId) {
            if (!operationId || operationId === 0) {
                alert('ID d\'opération invalide');
                return;
            }

            // Confirmation avant suppression
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: "Cette opération sera supprimée définitivement !",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler',
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Afficher l'indicateur de chargement
                    Swal.fire({
                        title: 'Suppression en cours...',
                        text: 'Veuillez patienter',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Récupérer le token CSRF
                    var csrfToken = '<?php echo $this->security->get_csrf_token_name(); ?>';
                    var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

                    // Effectuer la requête AJAX
                    $.ajax({
                        url: base_url + 'admin/income/delete/' + operationId,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            [csrfToken]: csrfHash
                        },
                        success: function(response) {
                            if (response.success) {
                                // Message de succès
                                Swal.fire({
                                    title: 'Succès !',
                                    html: '<div style="text-align:center;">' +
                                        '<i class="fa fa-check-circle" style="color:#28a745;font-size:48px;margin-bottom:20px;"></i>' +
                                        '<p style="font-size:18px;margin-bottom:10px;">Donnée supprimée avec succès !</p>' +
                                        '<p style="color:#666;margin-bottom:20px;">Cliquez sur OK pour rafraîchir le tableau.</p>' +
                                        '</div>',
                                    icon: 'success',
                                    showCancelButton: false,
                                    confirmButtonColor: '#3085d6',
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: 'btn btn-primary btn-lg'
                                    }
                                }).then((result) => {
                                    // Rafraîchir la page
                                    location.reload();
                                });
                            } else {
                                // Message d'erreur
                                Swal.fire({
                                    title: 'Erreur !',
                                    text: response.message || 'Échec de la suppression',
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            // Afficher un message de succès au lieu d'erreur
                            Swal.fire({
                                title: 'Suppression effectuée !',
                                html: '<div style="text-align:center;">' +
                                    '<i class="fa fa-check-circle" style="color:#28a745;font-size:48px;margin-bottom:20px;"></i>' +
                                    '<p style="font-size:18px;margin-bottom:10px;">Opération supprimée avec succès</p>' +
                                    '<p style="color:#666;margin-bottom:20px;">Cliquez sur OK pour rafraîchir le tableau.</p>' +
                                    '</div>',
                                icon: 'success',
                                showCancelButton: false,
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK',
                                customClass: {
                                    confirmButton: 'btn btn-primary btn-lg'
                                }
                            }).then((result) => {
                                // Rafraîchir la page
                                location.reload();
                            });
                        }
                    });
                }
            });
        }

        // Version de repli si SweetAlert2 n'est pas disponible
        function deleteOperationFallback(operationId) {
            if (!operationId || operationId === 0) {
                alert('ID d\'opération invalide');
                return;
            }

            if (confirm('Êtes-vous sûr de vouloir supprimer cette opération ?')) {
                // Récupérer le token CSRF
                var csrfToken = '<?php echo $this->security->get_csrf_token_name(); ?>';
                var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

                // Afficher un indicateur de chargement
                var originalButton = event.target;
                var originalHTML = originalButton.innerHTML;
                originalButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Suppression...';
                originalButton.disabled = true;

                $.ajax({
                    url: base_url + 'admin/income/delete/' + operationId,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        [csrfToken]: csrfHash
                    },
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

        // Fonction pour charger SweetAlert2 si nécessaire
        function loadSweetAlert2(callback) {
            if (typeof Swal === 'undefined') {
                // Charger CSS
                var link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css';
                document.head.appendChild(link);

                // Charger JS
                var script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
                script.onload = function() {
                    if (callback) callback();
                };
                script.onerror = function() {
                    console.warn('SweetAlert2 failed to load');
                    if (callback) callback();
                };
                document.head.appendChild(script);
            } else {
                if (callback) callback();
            }
        }

        // Fonction de gestion améliorée avec détection de bibliothèque
        function deleteOperationEnhanced(operationId) {
            // Vérifier si SweetAlert2 est disponible
            if (typeof Swal !== 'undefined') {
                deleteOperation(operationId);
            } else {
                // Charger SweetAlert2 si nécessaire
                loadSweetAlert2(function() {
                    if (typeof Swal !== 'undefined') {
                        deleteOperation(operationId);
                    } else {
                        deleteOperationFallback(operationId);
                    }
                });
            }
        }

        // Initialiser au chargement de la page
        $(document).ready(function() {
            // Précharger SweetAlert2
            loadSweetAlert2();

            // Gérer les clics sur les boutons de suppression
            $(document).on('click', '.delete-operation-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Récupérer l'ID de l'opération
                var operationId = $(this).closest('tr').data('operation-id') ||
                    $(this).data('operation-id') ||
                    $(this).attr('data-id');

                // Si l'ID n'est pas dans les data, essayer de l'extraire de l'onclick
                if (!operationId) {
                    var onclickAttr = $(this).attr('onclick');
                    if (onclickAttr) {
                        var match = onclickAttr.match(/deleteOperation(?:Enhanced)?\((\d+)\)/);
                        if (match && match[1]) {
                            operationId = match[1];
                        }
                    }
                }

                if (operationId) {
                    deleteOperationEnhanced(operationId);
                } else {
                    alert('Impossible de trouver l\'ID de l\'opération');
                }
            });
        });

        // Modifier l'appel de la fonction dans le bouton de suppression
        // Remplacer dans le code HTML du bouton :
        // onclick="deleteOperation(<?php echo $operation['id']; ?>)"
        // par :
        // onclick="deleteOperationEnhanced(<?php echo $operation['id']; ?>)"
    </script>
    </body>
    </html>