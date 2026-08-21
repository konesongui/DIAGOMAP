<?php
// ================================================================
// PAGE : Accueil commercial (module Administration)
// DESCRIPTION : Hub-cards avec barre de recherche et design moderne
// ================================================================
?>

<style>
    /* ===== STYLES GÉNÉRAUX ===== */
    .mak{
        margin-top: 10px;
    }
    .content-wrapper {
        background: #f8fafc;
        padding-bottom: 40px;
    }
    .section-header {
        margin-bottom: 30px;
        text-align: center;
    }
    .section-header h2 {
        font-size: 28px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        letter-spacing: -0.5px;
    }
    .section-header p {
        color: #64748b;
        font-size: 16px;
        margin-top: 4px;
    }
    .section-divider {
        border-top: 1px solid #e2e8f0;
        margin: 40px 0 30px;
        position: relative;
    }
    .section-divider .divider-label {
        position: absolute;
        top: -12px;
        left: 50%;
        transform: translateX(-50%);
        background: #f8fafc;
        padding: 0 16px;
        font-size: 12px;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    .sub-title {
        font-size: 18px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 22px;
        padding-left: 12px;
        border-left: 4px solid #273772;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .sub-title .badge-count {
        background: #e2e8f0;
        color: #475569;
        font-size: 12px;
        font-weight: 500;
        padding: 2px 12px;
        border-radius: 20px;
        margin-left: 8px;
    }

    /* ===== CARTE MODERN ===== */
    .card-modern {
        border: none;
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
        background: #ffffff;
        margin-bottom: 30px;
        overflow: hidden;
    }

    .card-modern .card-header {
        background: linear-gradient(135deg, #fec32e 0%, #fec32e 100%);
        padding: 18px 24px;
        border-bottom: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .card-modern .card-header .header-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .card-modern .card-header .header-title h2 {
        color: #ffffff;
        font-size: 20px;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .card-modern .card-header .header-title h2 i {
        color: #60a5fa;
    }

    .card-modern .card-header .header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .card-modern .card-body {
        padding: 24px;
        background: #fafcff;
    }

    /* ===== BARRE DE RECHERCHE ===== */
    .search-bar {
        display: flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 30px;
        padding: 4px 4px 4px 16px;
        transition: all 0.3s ease;
        min-width: 220px;
        backdrop-filter: blur(10px);
    }

    .search-bar:focus-within {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.3);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }

    .search-bar .search-icon {
        color: rgba(255, 255, 255, 0.5);
        font-size: 14px;
        margin-right: 8px;
    }

    .search-bar input {
        background: transparent;
        border: none;
        padding: 8px 0;
        font-size: 14px;
        color: #ffffff;
        width: 100%;
        outline: none;
    }

    .search-bar input::placeholder {
        color: rgba(255, 255, 255, 0.4);
    }

    .search-bar input:-webkit-autofill,
    .search-bar input:-webkit-autofill:hover,
    .search-bar input:-webkit-autofill:focus {
        -webkit-text-fill-color: #ffffff;
        -webkit-box-shadow: 0 0 0px 1000px transparent inset;
        transition: background-color 5000s ease-in-out 0s;
    }

    .search-bar .search-btn {
        background: #ffffff;
        border: none;
        border-radius: 30px;
        padding: 6px 20px;
        font-size: 13px;
        font-weight: 500;
        color: #273772;
        cursor: pointer;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .search-bar .search-btn:hover {
        background: #2563eb;
        color: #ffffff;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .search-bar .clear-btn {
        background: transparent;
        border: none;
        color: rgba(255, 255, 255, 0.4);
        cursor: pointer;
        padding: 4px 8px;
        font-size: 16px;
        display: none;
        transition: all 0.3s ease;
    }

    .search-bar .clear-btn:hover {
        color: #ffffff;
    }

    .search-bar .clear-btn.visible {
        display: block;
    }

    /* ===== STATS MINI ===== */
    .stats-mini {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .stats-mini .stat-item {
        display: flex;
        align-items: center;
        gap: 6px;
        color: rgba(255, 255, 255, 0.8);
        font-size: 13px;
        font-weight: 400;
    }

    .stats-mini .stat-item .stat-number {
        font-weight: 600;
        color: #ffffff;
        font-size: 16px;
    }

    .stats-mini .stat-item .stat-label {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.5);
    }

    .stats-divider {
        width: 1px;
        height: 28px;
        background: rgba(255, 255, 255, 0.15);
    }

    /* ===== HUB-CARDS ===== */
    .hub-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px 16px 18px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        border: 1px solid rgba(226, 232, 240, 0.6);
        transition: all 0.25s ease;
        text-align: center;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        min-height: 210px;
        position: relative;
        overflow: hidden;
    }

    .hub-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #273772, #3b82f6);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .hub-card:hover::before {
        opacity: 1;
    }

    .hub-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.08);
        border-color: #b9d0f0;
    }

    .hub-card .card-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        gap: 6px;
        z-index: 1;
    }

    .hub-card .card-icon {
        font-size: 28px;
        width: 58px;
        height: 58px;
        line-height: 58px;
        border-radius: 50%;
        background: rgba(59, 130, 246, 0.08);
        color: #a8adb6;
        transition: all 0.3s ease;
        flex-shrink: 0;
        text-align: center;
    }

    .hub-card:hover .card-icon {
        background: rgba(59, 130, 246, 0.15);
        transform: scale(1.05);
    }

    .hub-card h4 {
        font-weight: 600;
        font-size: 17px;
        color: #0f172a;
        margin: 6px 0 2px;
        line-height: 1.3;
    }

    .hub-card p {
        font-size: 14px;
        color: #64748b;
        margin: 0 0 4px;
        line-height: 1.4;
    }

    .hub-card .btn-ouvrir {
        background: transparent;
        color: #273772;
        border: 1px solid #273772;
        border-radius: 30px;
        padding: 5px 22px;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
        margin-top: 8px;
    }

    .hub-card .btn-ouvrir:hover {
        background: #273772;
        color: #fff;
        border-color: #273772;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(39, 55, 114, 0.2);
    }

    .hub-card .badge-module {
        position: absolute;
        top: 12px;
        right: 12px;
        font-size: 9px;
        padding: 2px 10px;
        border-radius: 12px;
        background: #f1f5f9;
        color: #64748b;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Couleurs personnalisées */
    .hub-card .card-icon.blue { background: rgba(59,130,246,0.1); color: #3b82f6; }
    .hub-card .card-icon.green { background: rgba(16,185,129,0.1); color: #10b981; }
    .hub-card .card-icon.purple { background: rgba(139,92,246,0.1); color: #8b5cf6; }
    .hub-card .card-icon.orange { background: rgba(245,158,11,0.1); color: #f59e0b; }
    .hub-card .card-icon.red { background: rgba(239,68,68,0.1); color: #ef4444; }
    .hub-card .card-icon.teal { background: rgba(20,184,166,0.1); color: #14b8a6; }
    .hub-card .card-icon.cyan { background: rgba(6,182,212,0.1); color: #06b6d4; }
    .hub-card .card-icon.pink { background: rgba(236,72,153,0.1); color: #ec4899; }
    .hub-card .card-icon.indigo { background: rgba(99,102,241,0.1); color: #6366f1; }

    /* ===== ANIMATIONS ===== */
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

    .hub-card {
        animation: fadeInUp 0.4s ease forwards;
    }

    .hub-card:nth-child(1) { animation-delay: 0.02s; }
    .hub-card:nth-child(2) { animation-delay: 0.04s; }
    .hub-card:nth-child(3) { animation-delay: 0.06s; }
    .hub-card:nth-child(4) { animation-delay: 0.08s; }
    .hub-card:nth-child(5) { animation-delay: 0.10s; }
    .hub-card:nth-child(6) { animation-delay: 0.12s; }
    .hub-card:nth-child(7) { animation-delay: 0.14s; }
    .hub-card:nth-child(8) { animation-delay: 0.16s; }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 992px) {
        .card-modern .card-header {
            flex-direction: column;
            align-items: stretch;
        }

        .search-bar {
            width: 100%;
            min-width: unset;
        }

        .stats-mini {
            width: 100%;
            justify-content: center;
        }

        .stats-divider {
            display: none;
        }
    }

    @media (max-width: 768px) {
        .hub-card { min-height: 180px; padding: 18px 12px 14px; }
        .hub-card .card-icon { width: 50px; height: 50px; line-height: 50px; font-size: 24px; }
        .hub-card h4 { font-size: 15px; }
        .hub-card p { font-size: 13px; }
        .hub-card .btn-ouvrir { padding: 4px 18px; font-size: 12px; }

        .card-modern .card-header .header-title h2 {
            font-size: 17px;
        }

        .stats-mini .stat-item .stat-number {
            font-size: 14px;
        }

        .section-divider .divider-label {
            font-size: 10px;
            padding: 0 10px;
        }
    }

    @media (max-width: 480px) {
        .card-modern .card-body {
            padding: 16px;
        }

        .search-bar input {
            font-size: 13px;
        }

        .search-bar .search-btn {
            font-size: 12px;
            padding: 4px 14px;
        }
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="card-modern">

            <!-- ========== EN-TÊTE AVEC RECHERCHE ========== -->
            <div class="card-header">
                <div class="header-title">
                    <h2>
                        <i class="fa fa-shopping-cart"></i> Module commercial
                    </h2>
                </div>

                <div class="header-actions">
                    <!-- Mini statistiques -->
                    <div class="stats-mini">
                        <div class="stat-item">
                            <span class="stat-number"><?php echo isset($total_modules) ? $total_modules : '12'; ?></span>
                            <span class="stat-label">Modules</span>
                        </div>
                        <div class="stats-divider"></div>
                        <!--<div class="stat-item">
                            <span class="stat-number"><?php echo isset($active_modules) ? $active_modules : '10'; ?></span>
                            <span class="stat-label">Actifs</span>
                        </div>-->
                    </div>

                    <!-- Barre de recherche -->
                    <div class="search-bar">
                        <i class="fa fa-search search-icon"></i>
                        <input type="text" id="searchInput" placeholder="Rechercher un module..." onkeyup="searchModules()">
                        <button class="clear-btn" id="clearBtn" onclick="clearSearch()">
                            <i class="fa fa-times"></i>
                        </button>
                        <button class="search-btn" onclick="searchModules()">
                            <i class="fa fa-search"></i> Rechercher
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body">

                <!-- ========== SECTION 1 : VENTES ========== -->
                <div class="sub-title">
                    <i class="fa fa-shopping-bag" style="color:#3b82f6;"></i> Ventes
                    <span class="badge-count"><?php echo isset($ventes_count) ? $ventes_count : '8'; ?></span>
                </div>
                <div class="row gy-4">

                    <!-- Tableau de bord -->
                    <?php if ($this->rbac->hasPrivilege('panneau', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="tableau de bord ventes dashboard">
                            <div class="hub-card">
                                <span class="badge-module">Dashboard</span>
                                <div class="card-content">
                                    <div class="card-icon blue"><i class="fa fa-dashboard"></i></div>
                                    <h4>Tableau de bord</h4>
                                    <p>Vue d'ensemble des ventes</p>
                                    <a href="<?php echo base_url('admin/selling_table'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Proforma -->
                    <?php if ($this->rbac->hasPrivilege('devis', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="proforma facture pro forma">
                            <div class="hub-card">
                                <span class="badge-module">Facturation</span>
                                <div class="card-content">
                                    <div class="card-icon purple"><i class="fa fa-file-text-o"></i></div>
                                    <h4>Proforma</h4>
                                    <p>Factures pro forma</p>
                                    <a href="<?php echo base_url('admin/proforma'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Devis -->
                    <?php if ($this->rbac->hasPrivilege('devis', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="devis estimation prix">
                            <div class="hub-card">
                                <span class="badge-module">Devis</span>
                                <div class="card-content">
                                    <div class="card-icon green"><i class="fa fa-file-pdf-o"></i></div>
                                    <h4>Devis</h4>
                                    <p>Gestion des devis</p>
                                    <a href="<?php echo base_url('admin/quoteitem'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Bons de commandes -->
                    <?php if ($this->rbac->hasPrivilege('order_item', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="bons de commandes commande client">
                            <div class="hub-card">
                                <span class="badge-module">Commande</span>
                                <div class="card-content">
                                    <div class="card-icon orange"><i class="fa fa-file-text"></i></div>
                                    <h4>Bons de commandes</h4>
                                    <p>Commandes clients</p>
                                    <a href="<?php echo base_url('admin/orderformitem'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Bons de livraisons -->
                    <?php if ($this->rbac->hasPrivilege('deliveryitem', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="bons de livraisons livraison transport">
                            <div class="hub-card">
                                <span class="badge-module">Livraison</span>
                                <div class="card-content">
                                    <div class="card-icon teal"><i class="fa fa-truck"></i></div>
                                    <h4>Bons de livraisons</h4>
                                    <p>Suivi des livraisons</p>
                                    <a href="<?php echo base_url('admin/deliveryitem'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Mes services -->
                    <?php if ($this->rbac->hasPrivilege('services_commercial', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="mes services services commercial">
                            <div class="hub-card">
                                <span class="badge-module">Services</span>
                                <div class="card-content">
                                    <div class="card-icon cyan"><i class="fa fa-gears"></i></div>
                                    <h4>Mes services</h4>
                                    <p>Services commerciaux</p>
                                    <a href="<?php echo base_url('admin/services'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Point de vente -->
                    <?php if ($this->rbac->hasPrivilege('pdv', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="point de vente pdv encaissement">
                            <div class="hub-card">
                                <span class="badge-module">PDV</span>
                                <div class="card-content">
                                    <div class="card-icon indigo"><i class="fa fa-cash-register"></i></div>
                                    <h4>Point de vente</h4>
                                    <p>Encaissement et ventes</p>
                                    <a href="<?php echo base_url('admin/selling'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Clients -->
                    <?php if ($this->rbac->hasPrivilege('clients', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="clients gestion clients">
                            <div class="hub-card">
                                <span class="badge-module">Clients</span>
                                <div class="card-content">
                                    <div class="card-icon pink"><i class="fa fa-users"></i></div>
                                    <h4>Clients</h4>
                                    <p>Gestion des clients</p>
                                    <a href="<?php echo base_url('admin/clients'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Objectifs commercial -->
                    <?php if ($this->rbac->hasPrivilege('objectif', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="objectifs commercial objectif">
                            <div class="hub-card">
                                <span class="badge-module">Objectifs</span>
                                <div class="card-content">
                                    <div class="card-icon red"><i class="fa fa-bullseye"></i></div>
                                    <h4>Objectifs commercial</h4>
                                    <p>Suivi des objectifs</p>
                                    <a href="<?php echo base_url('admin/objectifs'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div> <!-- /row Ventes -->

                <!-- ========== SECTION 2 : ACHATS ========== -->
                <div class="section-divider">
                    <span class="divider-label"><i class="fa fa-shopping-cart"></i> Achats</span>
                </div>
                <div class="sub-title" style="margin-top: 0;">
                    <i class="fa fa-cart-plus" style="color:#10b981;"></i> Achats
                    <span class="badge-count"><?php echo isset($achats_count) ? $achats_count : '3'; ?></span>
                </div>
                <div class="row gy-4">

                    <!-- Bons de commande fournisseurs -->
                    <?php if ($this->rbac->hasPrivilege('deliveryitems', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="bons de commande fournisseur commande">
                            <div class="hub-card">
                                <span class="badge-module">Commande</span>
                                <div class="card-content">
                                    <div class="card-icon blue"><i class="fa fa-file-text"></i></div>
                                    <h4>Bons de commande</h4>
                                    <p>Commandes fournisseurs</p>
                                    <a href="<?php echo base_url('admin/deliveryitem_supplier'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Devis fournisseur -->
                    <?php if ($this->rbac->hasPrivilege('quote_suppliers', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="devis fournisseur demande prix">
                            <div class="hub-card">
                                <span class="badge-module">Devis</span>
                                <div class="card-content">
                                    <div class="card-icon green"><i class="fa fa-file-pdf-o"></i></div>
                                    <h4>Devis fournisseur</h4>
                                    <p>Demandes de prix</p>
                                    <a href="<?php echo base_url('admin/devissupplier'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Fournisseurs -->
                    <?php if ($this->rbac->hasPrivilege('supplier', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="fournisseurs gestion fournisseur">
                            <div class="hub-card">
                                <span class="badge-module">Fournisseurs</span>
                                <div class="card-content">
                                    <div class="card-icon orange"><i class="fa fa-truck"></i></div>
                                    <h4>Fournisseurs</h4>
                                    <p>Gestion des fournisseurs</p>
                                    <a href="<?php echo base_url('admin/itemsupplier'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div> <!-- /row Achats -->

                <!-- ========== SECTION 3 : MAGASIN ET STOCK ========== -->
                <div class="section-divider">
                    <span class="divider-label"><i class="fa fa-archive"></i> Stock</span>
                </div>
                <div class="sub-title" style="margin-top: 0;">
                    <i class="fa fa-archive" style="color:#f59e0b;"></i> Magasin et stock
                    <span class="badge-count"><?php echo isset($stock_count) ? $stock_count : '4'; ?></span>
                </div>
                <div class="row gy-4">

                    <!-- Entrées de stock -->
                    <?php if ($this->rbac->hasPrivilege('item_stock', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="entrees de stock reception marchandises">
                            <div class="hub-card">
                                <span class="badge-module">Entrée</span>
                                <div class="card-content">
                                    <div class="card-icon purple"><i class="fa fa-arrow-down"></i></div>
                                    <h4>Entrées de stock</h4>
                                    <p>Réception de marchandises</p>
                                    <a href="<?php echo base_url('admin/stockentry'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Sorties de stock -->
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="sorties de stock sortie marchandises">
                        <div class="hub-card">
                            <span class="badge-module">Sortie</span>
                            <div class="card-content">
                                <div class="card-icon red"><i class="fa fa-arrow-up"></i></div>
                                <h4>Sorties de stock</h4>
                                <p>Sorties de marchandises</p>
                                <a href="<?php echo base_url('admin/stockremoval'); ?>" class="btn-ouvrir">Ouvrir</a>
                            </div>
                        </div>
                    </div>

                    <!-- Etat de stock -->
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="etat de stock inventaire permanent">
                        <div class="hub-card">
                            <span class="badge-module">Inventaire</span>
                            <div class="card-content">
                                <div class="card-icon teal"><i class="fa fa-list-alt"></i></div>
                                <h4>Etat de stock</h4>
                                <p>Inventaire permanent</p>
                                <a href="<?php echo base_url('admin/itemstock'); ?>" class="btn-ouvrir">Ouvrir</a>
                            </div>
                        </div>
                    </div>

                    <!-- Inventaire -->
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="inventaire gestion inventaire">
                        <div class="hub-card">
                            <span class="badge-module">Inventaire</span>
                            <div class="card-content">
                                <div class="card-icon cyan"><i class="fa fa-calculator"></i></div>
                                <h4>Inventaire</h4>
                                <p>Gestion des inventaires</p>
                                <a href="<?php echo base_url('admin/inventaire'); ?>" class="btn-ouvrir">Ouvrir</a>
                            </div>
                        </div>
                    </div>

                </div> <!-- /row Magasin et stock -->

                <!-- Message lorsqu'aucun résultat -->
                <div id="noResult" class="text-center" style="display: none; padding: 40px 0;">
                    <i class="fa fa-search" style="font-size: 48px; color: #cbd5e1; display: block; margin-bottom: 16px;"></i>
                    <p style="font-size: 16px; color: #64748b;">Aucun module trouvé</p>
                    <p style="font-size: 13px; color: #94a3b8;">Essayez de modifier vos critères de recherche</p>
                </div>

            </div> <!-- /card-body -->
        </div> <!-- /card-modern -->
    </section>
</div>

<!-- ========================================== -->
<!-- SCRIPTS                                    -->
<!-- ========================================== -->
<script type="text/javascript">
    // ========================================== //
    // RECHERCHE DE MODULES                        //
    // ========================================== //
    function searchModules() {
        var input = document.getElementById('searchInput');
        var filter = input.value.toLowerCase().trim();
        var modules = document.querySelectorAll('.module-item');
        var hasVisible = false;
        var clearBtn = document.getElementById('clearBtn');

        // Afficher/cacher le bouton clear
        if (filter.length > 0) {
            clearBtn.classList.add('visible');
        } else {
            clearBtn.classList.remove('visible');
        }

        modules.forEach(function(module) {
            var name = module.getAttribute('data-name') || '';
            var match = name.toLowerCase().includes(filter);
            module.style.display = match ? '' : 'none';
            if (match) hasVisible = true;
        });

        // Afficher le message si aucun résultat
        var noResult = document.getElementById('noResult');
        if (hasVisible) {
            noResult.style.display = 'none';
        } else {
            noResult.style.display = 'block';
        }
    }

    // ========================================== //
    // EFFACER LA RECHERCHE                       //
    // ========================================== //
    function clearSearch() {
        document.getElementById('searchInput').value = '';
        document.getElementById('clearBtn').classList.remove('visible');
        searchModules();
        document.getElementById('searchInput').focus();
    }

    // ========================================== //
    // RECHERCHE AVEC TOUCHE ENTER                //
    // ========================================== //
    document.addEventListener('DOMContentLoaded', function() {
        var input = document.getElementById('searchInput');
        if (input) {
            input.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    searchModules();
                }
            });
        }
    });

    // ========================================== //
    // RECHERCHE AUTOMATIQUE APRÈS SAISIE         //
    // ========================================== //
    document.addEventListener('DOMContentLoaded', function() {
        var input = document.getElementById('searchInput');
        if (input) {
            input.addEventListener('input', function() {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(function() {
                    searchModules();
                }, 300);
            });
        }
    });
</script>