<!-- ============================================================
     PAGE : Tableau de bord Tontine
     DESCRIPTION : Interface moderne pour le tableau de bord de la tontine
     ============================================================ -->

<style>
    :root {
        --primary-dark: #273772;
        --primary-light: #3b82f6;
        --primary-gradient: linear-gradient(135deg, #273772 0%, #1a2558 100%);
        --bg-light: #f8fafc;
        --text-dark: #1e293b;
        --text-muted: #64748b;
        --border-light: #e2e8f0;
        --shadow-soft: 0 8px 30px rgba(0, 0, 0, 0.06);
        --shadow-hover: 0 12px 40px rgba(0, 0, 0, 0.1);
        --radius-lg: 16px;
        --radius-md: 12px;
        --radius-sm: 8px;
        --transition: all 0.25s ease;
    }

    .content-wrapper {
        background: #f1f5f9;
        padding: 20px 15px;
        min-height: 100vh;
    }

    /* ========================================== */
    /* STATISTIQUES                               */
    /* ========================================== */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 16px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: #ffffff;
        border-radius: var(--radius-lg);
        padding: 16px 20px;
        box-shadow: var(--shadow-soft);
        border-left: 5px solid var(--primary-light);
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .stat-card::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: radial-gradient(circle, rgba(59, 130, 246, 0.05) 0%, transparent 70%);
        border-radius: 50%;
        transform: translate(30px, -30px);
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-hover);
    }

    .stat-card .stat-info {
        position: relative;
        z-index: 1;
    }

    .stat-card .stat-info h4 {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: 500;
        margin: 0 0 4px 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-card .stat-info .number {
        font-size: 22px;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0;
        line-height: 1.2;
    }

    .stat-card .stat-icon {
        font-size: 32px;
        opacity: 0.8;
        position: relative;
        z-index: 1;
    }

    .stat-card:nth-child(1) { border-left-color: #3b82f6; }
    .stat-card:nth-child(1) .stat-icon { color: #3b82f6; }
    .stat-card:nth-child(2) { border-left-color: #10b981; }
    .stat-card:nth-child(2) .stat-icon { color: #10b981; }
    .stat-card:nth-child(3) { border-left-color: #f59e0b; }
    .stat-card:nth-child(3) .stat-icon { color: #f59e0b; }
    .stat-card:nth-child(4) { border-left-color: #8b5cf6; }
    .stat-card:nth-child(4) .stat-icon { color: #8b5cf6; }
    .stat-card:nth-child(5) { border-left-color: #ef4444; }
    .stat-card:nth-child(5) .stat-icon { color: #ef4444; }

    .stat-trend {
        font-size: 11px;
        font-weight: 600;
        margin-top: 4px;
    }
    .stat-trend.up { color: #10b981; }
    .stat-trend.down { color: #ef4444; }

    /* ========================================== */
    /* CARTE PRINCIPALE                           */
    /* ========================================== */
    .card-modern {
        border: none;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-soft);
        background: #ffffff;
        margin-bottom: 30px;
        overflow: hidden;
    }

    .card-modern .card-header {
        background: var(--primary-gradient);
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
        background: var(--bg-light);
    }

    .btn-back {
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 30px;
        padding: 5px 16px;
        font-size: 13px;
        transition: var(--transition);
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
        transform: translateX(-2px);
    }

    .btn-primary-custom {
        background: rgba(59, 130, 246, 0.2);
        color: #60a5fa;
        border: 1px solid rgba(59, 130, 246, 0.3);
        border-radius: 30px;
        padding: 5px 16px;
        font-size: 13px;
        transition: var(--transition);
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

    /* ========================================== */
    /* TABLE                                      */
    /* ========================================== */
    .table-modern {
        border-collapse: separate;
        border-spacing: 0 4px;
        width: 100%;
        margin-bottom: 0;
    }

    .table-modern thead th {
        background: #f1f5f9;
        color: var(--text-dark);
        font-weight: 600;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 10px 14px;
        border: none;
        border-bottom: 2px solid var(--border-light);
        white-space: nowrap;
    }

    .table-modern tbody td {
        background: #ffffff;
        padding: 10px 14px;
        border: none;
        border-bottom: 1px solid #eef2f6;
        vertical-align: middle;
        font-size: 13px;
        color: var(--text-dark);
        transition: background 0.2s ease;
    }

    .table-modern tbody tr {
        transition: transform 0.2s ease;
    }

    .table-modern tbody tr:hover td {
        background: #f8fafc;
    }

    .table-modern tbody tr:last-child td {
        border-bottom: none;
    }

    /* ========================================== */
    /* BADGES                                     */
    /* ========================================== */
    .badge-status {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
        transition: var(--transition);
    }
    .badge-status.paye { background: #d1fae5; color: #065f46; }
    .badge-status.en_attente { background: #fef3c7; color: #92400e; }
    .badge-status.annule { background: #fef2f2; color: #991b1b; }

    .badge-type {
        display: inline-block;
        padding: 3px 12px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 500;
        transition: var(--transition);
    }
    .badge-type.entrant { background: #dbeafe; color: #1d4ed8; }
    .badge-type.sortant { background: #fef3c7; color: #92400e; }

    /* ========================================== */
    /* CHARTES                                    */
    /* ========================================== */
    .chart-container {
        background: #ffffff;
        border-radius: var(--radius-md);
        padding: 16px;
        border: 1px solid var(--border-light);
        margin-bottom: 20px;
        transition: var(--transition);
    }

    .chart-container:hover {
        box-shadow: var(--shadow-soft);
    }

    .chart-container .chart-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .chart-container .chart-title i {
        color: var(--primary-light);
    }

    .chart-wrapper {
        position: relative;
        height: 200px;
    }

    /* ========================================== */
    /* SECTIONS                                   */
    /* ========================================== */
    .section-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 16px;
        padding-left: 12px;
        border-left: 4px solid var(--primary-light);
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .section-title .badge-count {
        background: #e2e8f0;
        color: #475569;
        font-size: 11px;
        padding: 2px 12px;
        border-radius: 20px;
        margin-left: 8px;
        font-weight: 500;
    }

    .section-title .btn-action {
        margin-left: auto;
        font-size: 12px;
        padding: 4px 12px;
        border-radius: 20px;
        background: var(--primary-light);
        color: white;
        border: none;
        transition: var(--transition);
        text-decoration: none;
    }

    .section-title .btn-action:hover {
        background: var(--primary-dark);
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
    }

    /* ========================================== */
    /* EMPTY STATE                                */
    /* ========================================== */
    .empty-state {
        padding: 40px 20px;
        text-align: center;
        color: #94a3b8;
    }

    .empty-state i {
        font-size: 48px;
        color: #cbd5e1;
        display: block;
        margin-bottom: 16px;
    }

    .empty-state h4 {
        font-size: 16px;
        color: var(--text-dark);
        margin-bottom: 8px;
    }

    .empty-state p {
        font-size: 14px;
        margin-bottom: 0;
    }

    .empty-state .btn-empty-action {
        margin-top: 16px;
        display: inline-block;
        padding: 8px 20px;
        border-radius: 20px;
        background: var(--primary-light);
        color: white;
        text-decoration: none;
        font-size: 13px;
        transition: var(--transition);
    }

    .empty-state .btn-empty-action:hover {
        background: var(--primary-dark);
        color: white;
        text-decoration: none;
    }

    /* ========================================== */
    /* TRANSACTION ITEM                          */
    /* ========================================== */
    .transaction-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #f1f5f9;
        transition: var(--transition);
    }

    .transaction-item:last-child {
        border-bottom: none;
    }

    .transaction-item:hover {
        padding-left: 8px;
        background: #f8fafc;
        margin: 0 -8px;
        padding-right: 8px;
        border-radius: var(--radius-sm);
    }

    .transaction-item .transaction-info {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
    }

    .transaction-item .transaction-amount {
        font-weight: 600;
        font-size: 14px;
        white-space: nowrap;
    }

    .transaction-item .transaction-amount.entrant { color: #10b981; }
    .transaction-item .transaction-amount.sortant { color: #ef4444; }

    /* ========================================== */
    /* CYCLE ITEM                                 */
    /* ========================================== */
    .cycle-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #f1f5f9;
        transition: var(--transition);
    }

    .cycle-item:last-child {
        border-bottom: none;
    }

    .cycle-item:hover {
        padding-left: 8px;
        background: #f8fafc;
        margin: 0 -8px;
        padding-right: 8px;
        border-radius: var(--radius-sm);
    }

    .cycle-progress {
        width: 60px;
        height: 4px;
        background: #e2e8f0;
        border-radius: 2px;
        overflow: hidden;
        margin-top: 4px;
    }

    .cycle-progress-bar {
        height: 100%;
        background: var(--primary-light);
        border-radius: 2px;
        transition: width 0.8s ease;
    }

    /* ========================================== */
    /* RESPONSIVE                                 */
    /* ========================================== */
    @media (max-width: 992px) {
        .col-md-8, .col-md-4 {
            width: 100%;
        }
    }

    @media (max-width: 768px) {
        .stats-grid { grid-template-columns: 1fr 1fr; }
        .card-modern .card-header { flex-direction: column; align-items: stretch; }
        .card-modern .card-header h3 { font-size: 16px; }
        .card-modern .card-body { padding: 16px; }
        .table-modern thead th, .table-modern tbody td { padding: 8px 10px; font-size: 12px; }
        .btn-back, .btn-primary-custom { width: 100%; justify-content: center; }
        .stat-card .stat-info .number { font-size: 18px; }
        .stat-card .stat-icon { font-size: 24px; }
        .transaction-item { flex-wrap: wrap; gap: 8px; }
        .section-title .btn-action { margin-left: 0; width: 100%; text-align: center; }
    }

    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: 1fr; }
        .stat-card { padding: 12px 16px; }
        .stat-card .stat-info .number { font-size: 16px; }
    }

    /* ========================================== */
    /* ANIMATIONS                                 */
    /* ========================================== */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .stat-card {
        animation: fadeInUp 0.4s ease forwards;
    }
    .stat-card:nth-child(1) { animation-delay: 0.02s; }
    .stat-card:nth-child(2) { animation-delay: 0.04s; }
    .stat-card:nth-child(3) { animation-delay: 0.06s; }
    .stat-card:nth-child(4) { animation-delay: 0.08s; }
    .stat-card:nth-child(5) { animation-delay: 0.10s; }

    .chart-container {
        animation: fadeInUp 0.5s ease forwards;
        animation-delay: 0.12s;
        opacity: 0;
    }

    .section-title {
        animation: fadeInUp 0.4s ease forwards;
        animation-delay: 0.14s;
        opacity: 0;
    }

    /* Scrollbar personnalisée */
    .table-responsive::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="card-modern">
                    <div class="card-header">
                        <h3>
                            <i class="fa fa-dashboard"></i> Tableau de bord Tontine
                            <span style="font-size: 14px; font-weight: 400; color: #94a3b8; margin-left: 8px;">
                                Vue d'ensemble
                            </span>
                        </h3>
                        <div class="d-flex align-items-center gap-3" style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <a href="<?php echo base_url(); ?>admin/admin/hub" class="btn-back" title="Retour au tableau de bord principal">
                                <i class="fa fa-arrow-left"></i> Retour
                            </a>
                            <a href="<?php echo base_url(); ?>admin/tontine_cycles/ajouter" class="btn-primary-custom" title="Nouveau cycle">
                                <i class="fa fa-plus-circle"></i> Nouveau cycle
                            </a>
                            <a href="<?php echo base_url(); ?>admin/tontine_membres/ajouter" class="btn-primary-custom" title="Ajouter un membre">
                                <i class="fa fa-user-plus"></i> Ajouter membre
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- ========================================== -->
                        <!-- STATISTIQUES                               -->
                        <!-- ========================================== -->
                        <div class="stats-grid">
                            <div class="stat-card" onclick="window.location.href='<?php echo base_url(); ?>admin/tontine_membres'">
                                <div class="stat-info">
                                    <h4><i class="fa fa-users"></i> Total membres</h4>
                                    <p class="number"><?php echo isset($stats['total_membres']) ? $stats['total_membres'] : 0; ?></p>
                                    <?php if (isset($stats['total_membres']) && $stats['total_membres'] > 0): ?>
                                        <div class="stat-trend up">
                                            <i class="fa fa-users"></i>
                                            <?php echo isset($stats['membres_actifs']) ? round(($stats['membres_actifs'] / $stats['total_membres']) * 100) : 0; ?>% actifs
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="stat-icon"><i class="fa fa-users"></i></div>
                            </div>
                            <div class="stat-card" style="border-left-color: #10b981;" onclick="window.location.href='<?php echo base_url(); ?>admin/tontine_membres?filter=actifs'">
                                <div class="stat-info">
                                    <h4><i class="fa fa-check-circle" style="color:#10b981;"></i> Membres actifs</h4>
                                    <p class="number"><?php echo isset($stats['membres_actifs']) ? $stats['membres_actifs'] : 0; ?></p>
                                    <?php if (isset($stats['total_membres']) && $stats['total_membres'] > 0): ?>
                                        <div class="stat-trend up">
                                            <i class="fa fa-circle"></i>
                                            <?php echo round(($stats['membres_actifs'] / $stats['total_membres']) * 100); ?>% du total
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="stat-icon"><i class="fa fa-check-circle"></i></div>
                            </div>
                            <div class="stat-card" style="border-left-color: #f59e0b;" onclick="window.location.href='<?php echo base_url(); ?>admin/tontine_cotisations'">
                                <div class="stat-info">
                                    <h4><i class="fa fa-money" style="color:#f59e0b;"></i> Total cotisations</h4>
                                    <p class="number" style="font-size: 18px;">
                                        <?php echo isset($stats['total_cotisations']) ? number_format($stats['total_cotisations'], 0, ',', ' ') . ' FCFA' : '0 FCFA'; ?>
                                    </p>
                                    <?php if (isset($stats['total_cotisations']) && $stats['total_cotisations'] > 0): ?>
                                        <div class="stat-trend up">
                                            <i class="fa fa-check-circle"></i>
                                            <?php echo isset($stats['arrieres']) ? round((1 - ($stats['arrieres'] / max(1, $stats['total_membres']))) * 100) : 0; ?>% de recouvrement
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="stat-icon"><i class="fa fa-money"></i></div>
                            </div>
                            <div class="stat-card" style="border-left-color: #8b5cf6;" onclick="window.location.href='<?php echo base_url(); ?>admin/tontine_collectes'">
                                <div class="stat-info">
                                    <h4><i class="fa fa-handshake-o" style="color:#8b5cf6;"></i> Collectes</h4>
                                    <p class="number" style="font-size: 18px;">
                                        <?php echo isset($stats['total_collectes']) ? number_format($stats['total_collectes'], 0, ',', ' ') . ' FCFA' : '0 FCFA'; ?>
                                    </p>
                                    <?php if (isset($stats['cycles_en_cours']) && $stats['cycles_en_cours'] > 0): ?>
                                        <div class="stat-trend up">
                                            <i class="fa fa-calendar"></i>
                                            <?php echo $stats['cycles_en_cours']; ?> cycle(s) en cours
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="stat-icon"><i class="fa fa-handshake-o"></i></div>
                            </div>
                            <div class="stat-card" style="border-left-color: #ef4444;" onclick="window.location.href='<?php echo base_url(); ?>admin/tontine_cotisations?filter=arrieres'">
                                <div class="stat-info">
                                    <h4><i class="fa fa-exclamation-triangle" style="color:#ef4444;"></i> Arriérés</h4>
                                    <p class="number" style="font-size: 14px;">
                                        <?php echo isset($stats['arrieres']) ? $stats['arrieres'] : 0; ?> membres
                                        <br>
                                        <span style="font-size: 16px; color: #ef4444;">
                                            <?php echo isset($stats['montant_arrieres']) ? number_format($stats['montant_arrieres'], 0, ',', ' ') . ' FCFA' : '0 FCFA'; ?>
                                        </span>
                                    </p>
                                    <?php if (isset($stats['arrieres']) && $stats['arrieres'] > 0): ?>
                                        <div class="stat-trend down">
                                            <i class="fa fa-exclamation-circle"></i>
                                            <?php echo round(($stats['arrieres'] / max(1, $stats['total_membres'])) * 100); ?>% des membres
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="stat-icon"><i class="fa fa-exclamation-triangle"></i></div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- ========================================== -->
                            <!-- COLONNE GAUCHE                            -->
                            <!-- ========================================== -->
                            <div class="col-md-8">

                                <!-- Évolution des cotisations -->
                                <div class="chart-container">
                                    <div class="chart-title">
                                        <i class="fa fa-line-chart"></i> Évolution des cotisations
                                        <span style="font-size: 12px; color: #94a3b8; font-weight: 400; margin-left: auto;">
                                            Derniers 12 mois
                                        </span>
                                        <a href="<?php echo base_url(); ?>admin/tontine_cotisations" class="btn-action" style="font-size: 11px; padding: 2px 12px; border-radius: 20px; background: var(--primary-light); color: white; border: none; transition: all 0.25s ease; text-decoration: none;">
                                            <i class="fa fa-file-text-o"></i> Voir tout
                                        </a>
                                    </div>
                                    <div class="chart-wrapper">
                                        <canvas id="cotisationsChart" height="200"></canvas>
                                    </div>
                                </div>

                                <!-- Top membres contributeurs -->
                                <div style="background: #ffffff; border-radius: var(--radius-md); padding: 16px; border: 1px solid var(--border-light); margin-bottom: 20px;">
                                    <div class="section-title" style="margin-bottom: 12px;">
                                        <i class="fa fa-trophy" style="color: #f59e0b;"></i> Top contributeurs
                                        <span class="badge-count"><?php echo isset($top_membres) ? count($top_membres) : 0; ?></span>
                                        <a href="<?php echo base_url(); ?>admin/tontine_membres/classement" class="btn-action" style="font-size: 11px; padding: 2px 12px; border-radius: 20px; background: var(--primary-light); color: white; border: none; transition: all 0.25s ease; text-decoration: none;">
                                            Voir tout <i class="fa fa-arrow-right"></i>
                                        </a>
                                    </div>
                                    <?php if (!empty($top_membres)) : ?>
                                        <div class="table-responsive">
                                            <table class="table table-modern">
                                                <thead>
                                                <tr>
                                                    <th style="width: 5%;">#</th>
                                                    <th>Membre</th>
                                                    <th>Téléphone</th>
                                                    <th style="text-align: right;">Total cotisations</th>
                                                    <th style="text-align: center;">Performance</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                $max_cotisation = isset($top_membres[0]['total_cotisations']) && $top_membres[0]['total_cotisations'] > 0
                                                    ? $top_membres[0]['total_cotisations']
                                                    : 1;
                                                foreach ($top_membres as $index => $membre) :
                                                    $taux = isset($membre['total_cotisations']) ? round(($membre['total_cotisations'] / $max_cotisation) * 100) : 0;
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?php if ($index == 0) : ?>
                                                                <span style="color: #f59e0b; font-size: 18px;">🥇</span>
                                                            <?php elseif ($index == 1) : ?>
                                                                <span style="color: #94a3b8; font-size: 18px;">🥈</span>
                                                            <?php elseif ($index == 2) : ?>
                                                                <span style="color: #d97706; font-size: 18px;">🥉</span>
                                                            <?php else : ?>
                                                                <span style="font-weight: 600; color: #64748b;"><?php echo $index + 1; ?></span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <strong><?php echo isset($membre['nom']) ? htmlspecialchars($membre['nom']) : ''; ?>
                                                                <?php echo isset($membre['prenom']) ? htmlspecialchars($membre['prenom']) : ''; ?></strong>
                                                        </td>
                                                        <td><?php echo isset($membre['telephone']) ? htmlspecialchars($membre['telephone']) : '—'; ?></td>
                                                        <td style="text-align: right; font-weight: 600; color: #f59e0b;">
                                                            <?php echo isset($membre['total_cotisations']) ? number_format($membre['total_cotisations'], 0, ',', ' ') . ' FCFA' : '0 FCFA'; ?>
                                                        </td>
                                                        <td style="text-align: center;">
                                                            <div style="display: flex; align-items: center; gap: 6px; justify-content: center;">
                                                                <div style="width: 60px; height: 4px; background: #e2e8f0; border-radius: 2px; overflow: hidden;">
                                                                    <div style="height: 100%; background: #f59e0b; border-radius: 2px; width: <?php echo min($taux, 100); ?>%; transition: width 1s ease;"></div>
                                                                </div>
                                                                <span style="font-size: 11px; color: #94a3b8; font-weight: 600;"><?php echo $taux; ?>%</span>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else : ?>
                                        <div class="empty-state">
                                            <i class="fa fa-users"></i>
                                            <h4>Aucun membre contributeur</h4>
                                            <p>Aucune cotisation n'a encore été enregistrée dans le système</p>
                                            <a href="<?php echo base_url(); ?>admin/tontine_cotisations/ajouter" class="btn-empty-action">
                                                <i class="fa fa-plus-circle"></i> Enregistrer une cotisation
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- ========================================== -->
                            <!-- COLONNE DROITE                           -->
                            <!-- ========================================== -->
                            <div class="col-md-4">

                                <!-- Cycles en cours -->
                                <div style="background: #ffffff; border-radius: var(--radius-md); padding: 16px; border: 1px solid var(--border-light); margin-bottom: 20px;">
                                    <div class="section-title" style="margin-bottom: 12px;">
                                        <i class="fa fa-calendar" style="color: #3b82f6;"></i> Cycles en cours
                                        <span class="badge-count"><?php echo isset($cycles_actifs) ? count($cycles_actifs) : 0; ?></span>
                                        <a href="<?php echo base_url(); ?>admin/tontine_cycles" class="btn-action" style="font-size: 11px; padding: 2px 12px; border-radius: 20px; background: var(--primary-light); color: white; border: none; transition: all 0.25s ease; text-decoration: none;">
                                            Gérer
                                        </a>
                                    </div>
                                    <?php if (!empty($cycles_actifs)) : ?>
                                        <?php foreach ($cycles_actifs as $cycle) :
                                            $progress = isset($cycle['nombre_membres']) && isset($cycle['max_membres']) && $cycle['max_membres'] > 0
                                                ? round(($cycle['nombre_membres'] / $cycle['max_membres']) * 100)
                                                : 0;
                                            $type_label = isset($cycle['type']) ? ucfirst($cycle['type']) : 'Standard';
                                            ?>
                                            <div class="cycle-item">
                                                <div>
                                                    <strong><?php echo isset($cycle['nom']) ? htmlspecialchars($cycle['nom']) : 'Cycle sans nom'; ?></strong>
                                                    <br>
                                                    <small style="color: #94a3b8; font-size: 11px;">
                                                        <i class="fa fa-tag"></i> <?php echo $type_label; ?>
                                                        <?php if (isset($cycle['date_fin']) && $cycle['date_fin'] != '0000-00-00'): ?>
                                                            <i class="fa fa-clock-o" style="margin-left: 8px;"></i> Fin: <?php echo date('d/m/Y', strtotime($cycle['date_fin'])); ?>
                                                        <?php endif; ?>
                                                    </small>
                                                    <div class="cycle-progress">
                                                        <div class="cycle-progress-bar" style="width: <?php echo $progress; ?>%;"></div>
                                                    </div>
                                                </div>
                                                <div style="text-align: right;">
                                                    <span style="font-size: 13px; font-weight: 600; color: #10b981;">
                                                        <?php echo isset($cycle['montant']) ? number_format($cycle['montant'], 0, ',', ' ') . ' FCFA' : '0 FCFA'; ?>
                                                    </span>
                                                    <br>
                                                    <small style="color: #94a3b8; font-size: 11px;">
                                                        <?php echo isset($cycle['nombre_membres']) ? $cycle['nombre_membres'] : 0; ?>/<?php echo isset($cycle['max_membres']) ? $cycle['max_membres'] : 0; ?> membres
                                                    </small>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <div class="empty-state" style="padding: 20px 0;">
                                            <i class="fa fa-calendar" style="font-size: 32px;"></i>
                                            <p style="font-size: 14px; margin-top: 8px;">Aucun cycle en cours</p>
                                            <a href="<?php echo base_url(); ?>admin/tontine_cycles/ajouter" class="btn-empty-action" style="padding: 6px 16px; font-size: 12px;">
                                                <i class="fa fa-plus-circle"></i> Démarrer un cycle
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Répartition des types -->
                                <div style="background: #ffffff; border-radius: var(--radius-md); padding: 16px; border: 1px solid var(--border-light); margin-bottom: 20px;">
                                    <div class="section-title" style="margin-bottom: 12px;">
                                        <i class="fa fa-pie-chart" style="color: #8b5cf6;"></i> Répartition des types
                                        <span class="badge-count"><?php echo isset($repartition_types) ? count($repartition_types) : 0; ?></span>
                                    </div>
                                    <?php if (!empty($repartition_types)) : ?>
                                        <div style="position: relative; height: 150px;">
                                            <canvas id="repartitionChart" height="150"></canvas>
                                        </div>
                                        <div style="display: flex; justify-content: center; gap: 16px; margin-top: 12px; flex-wrap: wrap;">
                                            <?php
                                            $colors = ['#3b82f6', '#f59e0b', '#10b981', '#8b5cf6', '#ef4444'];
                                            foreach ($repartition_types as $index => $type) :
                                                ?>
                                                <span style="font-size: 11px; color: #64748b;">
                                                    <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: <?php echo $colors[$index % count($colors)]; ?>; margin-right: 4px;"></span>
                                                    <?php echo isset($type['type']) ? ucfirst($type['type']) : 'Non défini'; ?>: <?php echo isset($type['total']) ? $type['total'] : 0; ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else : ?>
                                        <div class="empty-state" style="padding: 20px 0;">
                                            <i class="fa fa-pie-chart" style="font-size: 32px;"></i>
                                            <p style="font-size: 14px; margin-top: 8px;">Aucune donnée de répartition</p>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Dernières transactions -->
                                <div style="background: #ffffff; border-radius: var(--radius-md); padding: 16px; border: 1px solid var(--border-light);">
                                    <div class="section-title" style="margin-bottom: 12px;">
                                        <i class="fa fa-history" style="color: #8b5cf6;"></i> Dernières transactions
                                        <span class="badge-count"><?php echo isset($dernieres_transactions) ? count($dernieres_transactions) : 0; ?></span>
                                        <a href="<?php echo base_url(); ?>admin/tontine_transactions" class="btn-action" style="font-size: 11px; padding: 2px 12px; border-radius: 20px; background: var(--primary-light); color: white; border: none; transition: all 0.25s ease; text-decoration: none;">
                                            Voir tout
                                        </a>
                                    </div>
                                    <?php if (!empty($dernieres_transactions)) : ?>
                                        <?php foreach ($dernieres_transactions as $transaction) :
                                            $type = isset($transaction['type']) ? $transaction['type'] : 'sortant';
                                            ?>
                                            <div class="transaction-item">
                                                <div class="transaction-info">
                                                    <span class="badge-type <?php echo $type == 'entrant' ? 'entrant' : 'sortant'; ?>">
                                                        <i class="fa <?php echo $type == 'entrant' ? 'fa-arrow-down' : 'fa-arrow-up'; ?>"></i>
                                                        <?php echo ucfirst($type); ?>
                                                    </span>
                                                    <div>
                                                        <strong style="font-size: 13px;">
                                                            <?php echo isset($transaction['nom']) ? htmlspecialchars($transaction['nom']) : 'Membre'; ?>
                                                            <?php echo isset($transaction['prenom']) ? htmlspecialchars($transaction['prenom']) : ''; ?>
                                                        </strong>
                                                        <br>
                                                        <small style="color: #94a3b8; font-size: 11px;">
                                                            <?php
                                                            if (isset($transaction['date_creation']) && $transaction['date_creation'] != '0000-00-00 00:00:00') {
                                                                echo date('d/m/Y H:i', strtotime($transaction['date_creation']));
                                                            } else {
                                                                echo 'Date non définie';
                                                            }
                                                            ?>
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="transaction-amount <?php echo $type == 'entrant' ? 'entrant' : 'sortant'; ?>">
                                                    <?php echo isset($transaction['montant']) ? number_format($transaction['montant'], 0, ',', ' ') . ' FCFA' : '0 FCFA'; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <div class="empty-state" style="padding: 20px 0;">
                                            <i class="fa fa-history" style="font-size: 32px;"></i>
                                            <p style="font-size: 14px; margin-top: 8px;">Aucune transaction récente</p>
                                            <a href="<?php echo base_url(); ?>admin/tontine_transactions/ajouter" class="btn-empty-action" style="padding: 6px 16px; font-size: 12px;">
                                                <i class="fa fa-plus-circle"></i> Nouvelle transaction
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- ========================================== -->
<!-- SCRIPTS                                    -->
<!-- ========================================== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(document).ready(function() {
        // ========================================== //
        // GRAPHIQUE ÉVOLUTION DES COTISATIONS        //
        // ========================================== //
        <?php if (!empty($cotisations_evolution)) : ?>
        var ctx1 = document.getElementById('cotisationsChart').getContext('2d');
        var cotisationsData = <?php echo json_encode($cotisations_evolution); ?>;

        // Vérifier si des données existent
        var hasData = cotisationsData.some(item => item.montant > 0);

        var gradient = ctx1.createLinearGradient(0, 0, 0, 200);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.02)');

        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: cotisationsData.map(item => item.mois),
                datasets: [{
                    label: 'Cotisations (FCFA)',
                    data: cotisationsData.map(item => item.montant),
                    backgroundColor: gradient,
                    borderColor: '#3b82f6',
                    borderWidth: 3,
                    pointBackgroundColor: '#3b82f6',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: hasData ? 4 : 0,
                    pointHoverRadius: 6,
                    fill: hasData,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        callbacks: {
                            label: function(context) {
                                if (context.parsed.y === 0) return 'Aucune cotisation';
                                return context.parsed.y.toLocaleString('fr-FR') + ' FCFA';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value === 0) return '0';
                                return value.toLocaleString('fr-FR') + ' FCFA';
                            },
                            maxTicksLimit: 6
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
        <?php else : ?>
        // Afficher un message si aucune donnée
        var ctx1 = document.getElementById('cotisationsChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
                datasets: [{
                    label: 'Aucune donnée',
                    data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
                    borderColor: '#cbd5e1',
                    borderWidth: 1,
                    pointRadius: 0,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        enabled: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
        <?php endif; ?>

        // ========================================== //
        // GRAPHIQUE RÉPARTITION DES TYPES            //
        // ========================================== //
        <?php if (!empty($repartition_types)) : ?>
        var ctx2 = document.getElementById('repartitionChart').getContext('2d');
        var typesData = <?php echo json_encode($repartition_types); ?>;

        var labels = typesData.map(item => item.type ? item.type.charAt(0).toUpperCase() + item.type.slice(1) : 'Non défini');
        var data = typesData.map(item => parseInt(item.total) || 0);
        var colors = ['#3b82f6', '#f59e0b', '#10b981', '#8b5cf6', '#ef4444'];
        var backgroundColors = colors.slice(0, data.length);

        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors,
                    borderColor: '#ffffff',
                    borderWidth: 2,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                if (total === 0) return context.label + ': 0 (0%)';
                                var percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });
        <?php endif; ?>

        // ========================================== //
        // ANIMATION DES CYCLES PROGRESS              //
        // ========================================== //
        document.querySelectorAll('.cycle-progress-bar').forEach(function(bar) {
            var width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(function() {
                bar.style.width = width;
            }, 400);
        });

        // ========================================== //
        // ANIMATION DES BARRES DE PERFORMANCE        //
        // ========================================== //
        document.querySelectorAll('.table-modern tbody td div div div').forEach(function(bar) {
            var width = bar.style.width;
            if (width && width !== '0%') {
                bar.style.width = '0%';
                setTimeout(function() {
                    bar.style.width = width;
                }, 500);
            }
        });

        // ========================================== //
        // REFRESH AUTO DES DONNÉES (OPTIONNEL)      //
        // ========================================== //
        // Désactiver le refresh automatique pour éviter les surcharges
        // setTimeout(function() {
        //     location.reload();
        // }, 300000); // 5 minutes

        console.log('📊 Tableau de bord Tontine chargé avec succès');
        console.log('📈 Données disponibles:', {
            'stats': <?php echo json_encode(isset($stats) ? $stats : []); ?>,
            'top_membres': <?php echo isset($top_membres) ? count($top_membres) : 0; ?>,
            'cycles_actifs': <?php echo isset($cycles_actifs) ? count($cycles_actifs) : 0; ?>,
            'transactions': <?php echo isset($dernieres_transactions) ? count($dernieres_transactions) : 0; ?>
        });
    });
</script>