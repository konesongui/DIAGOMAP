<!-- ============================================================
     PAGE : Tableau de bord Association
     DESCRIPTION : Interface moderne en cards avec sections
                   (Adhérents, Cotisations, Bureau, Activités, Communication, Documents, Rapports)
     ============================================================ -->

<style>
    /* ===== STYLES GÉNÉRAUX ===== */
    .content-wrapper {
        background: #f0f4f9;
        padding-bottom: 40px;
        min-height: 100vh;
    }

    .mak {
        margin-top: 10px;
    }

    .card-modern {
        border: none;
        border-radius: 20px;
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.08);
        background: #ffffff;
        margin: 20px 0 30px;
        overflow: hidden;
    }

    /* ===== EN-TÊTE AVEC RECHERCHE ===== */
    .card-modern .card-header {
        background: linear-gradient(135deg, #fec32e 0%, #fec32e 100%);
        padding: 20px 32px;
        border-bottom: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }

    .card-modern .card-header .header-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .card-modern .card-header .header-left .brand-icon {
        width: 44px;
        height: 44px;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: #fff;
        backdrop-filter: blur(4px);
    }

    .card-modern .card-header h2 {
        color: #ffffff;
        font-size: 22px;
        font-weight: 700;
        margin: 0;
        letter-spacing: -0.3px;
    }

    .card-modern .card-header h2 small {
        font-weight: 400;
        font-size: 14px;
        opacity: 0.7;
        margin-left: 8px;
        letter-spacing: 0;
    }

    /* ===== CHAMP DE RECHERCHE MODERNE ===== */
    .search-module {
        position: relative;
        min-width: 280px;
        max-width: 400px;
        flex: 1 1 200px;
    }

    .search-module .search-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.5);
        font-size: 15px;
        pointer-events: none;
        transition: color 0.3s ease;
        z-index: 2;
    }

    .search-module input {
        width: 100%;
        padding: 11px 16px 11px 46px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 30px;
        background: rgba(255, 255, 255, 0.08);
        color: #ffffff;
        font-size: 14px;
        font-weight: 400;
        outline: none;
        transition: all 0.3s ease;
        backdrop-filter: blur(4px);
    }

    .search-module input::placeholder {
        color: rgba(255, 255, 255, 0.4);
        font-weight: 300;
        letter-spacing: 0.3px;
    }

    .search-module input:focus {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.4);
        box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.06);
        padding-right: 90px;
    }

    .search-module input:focus ~ .search-icon {
        color: rgba(255, 255, 255, 0.8);
    }

    .search-module .search-clear {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%) scale(0);
        color: rgba(255, 255, 255, 0.4);
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        z-index: 2;
        background: none;
        border: none;
        padding: 4px;
        opacity: 0;
    }

    .search-module .search-clear.visible {
        transform: translateY(-50%) scale(1);
        opacity: 1;
    }

    .search-module .search-clear:hover {
        color: rgba(255, 255, 255, 0.8);
        transform: translateY(-50%) scale(1.1);
    }

    .search-module .search-count {
        position: absolute;
        right: 46px;
        top: 50%;
        transform: translateY(-50%) scale(0);
        color: rgba(255, 255, 255, 0.4);
        font-size: 11px;
        font-weight: 300;
        background: rgba(255, 255, 255, 0.08);
        padding: 2px 10px;
        border-radius: 12px;
        transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        opacity: 0;
        z-index: 2;
        white-space: nowrap;
    }

    .search-module .search-count.visible {
        transform: translateY(-50%) scale(1);
        opacity: 1;
    }

    .search-module.search-active input {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.4);
        padding-right: 90px;
    }

    /* ===== RESULTATS INFO ===== */
    #resultsInfo {
        display: none;
        margin-bottom: 20px;
        padding: 12px 20px;
        background: linear-gradient(135deg, #e8f5e9, #c8e6c9);
        border-radius: 12px;
        color: #1a472a;
        font-size: 14px;
        font-weight: 500;
        align-items: center;
        gap: 12px;
        animation: slideDown 0.3s ease;
    }

    #resultsInfo i {
        font-size: 18px;
        color: #1a472a;
    }

    #resultsInfo .result-count {
        font-weight: 700;
        color: #1a472a;
        font-size: 16px;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ===== CORPS DE LA CARD ===== */
    .card-modern .card-body {
        padding: 32px 28px 28px;
        background: #fafcff;
    }

    .btn-back {
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 30px;
        padding: 6px 18px;
        font-size: 14px;
        transition: all 0.2s;
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

    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #1a472a;
        margin-bottom: 20px;
        padding-left: 12px;
        border-left: 4px solid #1a472a;
        display: flex;
        align-items: center;
        gap: 10px;
        opacity: 0;
        animation: fadeInLeft 0.5s ease forwards;
    }

    .section-title i {
        color: #1a472a;
    }

    .section-title .badge-section {
        background: #e8f5e9;
        color: #1a472a;
        font-size: 11px;
        padding: 2px 12px;
        border-radius: 20px;
        font-weight: 500;
        margin-left: 8px;
    }

    .section-title .btn-add {
        margin-left: auto;
        background: #1a472a;
        color: white;
        border: none;
        border-radius: 30px;
        padding: 4px 16px;
        font-size: 12px;
        font-weight: 500;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .section-title .btn-add:hover {
        background: #0d2818;
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(26, 71, 42, 0.3);
    }

    @keyframes fadeInLeft {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .section-divider {
        border: none;
        height: 1px;
        background: linear-gradient(to right, transparent, #e2e8f0 20%, #e2e8f0 80%, transparent);
        margin: 36px 0 32px;
    }

    /* ===== HUB-CARDS ===== */
    .hub-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px 16px 18px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        border: 1px solid rgba(226, 232, 240, 0.6);
        transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        text-align: center;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        min-height: 200px;
        position: relative;
        overflow: hidden;
    }

    .hub-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #1a472a, #40916c);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .hub-card:hover::before {
        opacity: 1;
    }

    .hub-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.08);
        border-color: #c8e6c9;
    }

    .hub-card .card-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        gap: 6px;
        position: relative;
        z-index: 1;
    }

    .hub-card .card-icon {
        font-size: 28px;
        width: 58px;
        height: 58px;
        line-height: 58px;
        border-radius: 50%;
        background: rgba(26, 71, 42, 0.08);
        color: #a8adb6;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .hub-card:hover .card-icon {
        background: rgba(26, 71, 42, 0.15);
        transform: scale(1.05);
    }

    .hub-card h4 {
        font-weight: 600;
        font-size: 16px;
        color: #0f172a;
        margin: 6px 0 2px;
        line-height: 1.3;
    }

    .hub-card p {
        font-size: 13px;
        color: #64748b;
        margin: 0 0 4px;
        line-height: 1.4;
    }

    .hub-card .btn-ouvrir {
        background: transparent;
        color: #1a472a;
        border: 1px solid #1a472a;
        border-radius: 30px;
        padding: 5px 22px;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        margin-top: 8px;
        position: relative;
        z-index: 1;
    }

    .hub-card .btn-ouvrir:hover {
        background: #1a472a;
        color: #fff;
        border-color: #1a472a;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(26, 71, 42, 0.25);
    }

    .hub-card .badge-notif {
        position: absolute;
        top: 12px;
        right: 12px;
        background: #ef4444;
        color: white;
        font-size: 10px;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 20px;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    /* Couleurs personnalisées */
    .hub-card .card-icon.blue { background: rgba(59,130,246,0.1); color: #2563eb; }
    .hub-card .card-icon.green { background: rgba(16,185,129,0.1); color: #059669; }
    .hub-card .card-icon.purple { background: rgba(139,92,246,0.1); color: #7c3aed; }
    .hub-card .card-icon.orange { background: rgba(245,158,11,0.1); color: #d97706; }
    .hub-card .card-icon.red { background: rgba(239,68,68,0.1); color: #dc2626; }
    .hub-card .card-icon.teal { background: rgba(20,184,166,0.1); color: #0d9488; }
    .hub-card .card-icon.cyan { background: rgba(6,182,212,0.1); color: #0891b2; }
    .hub-card .card-icon.pink { background: rgba(236,72,153,0.1); color: #db2777; }
    .hub-card .card-icon.indigo { background: rgba(99,102,241,0.1); color: #4f46e5; }
    .hub-card .card-icon.rose { background: rgba(244,63,94,0.1); color: #e11d48; }
    .hub-card .card-icon.amber { background: rgba(251,191,36,0.1); color: #d97706; }
    .hub-card .card-icon.emerald { background: rgba(52,211,153,0.1); color: #059669; }
    .hub-card .card-icon.forest { background: rgba(26,71,42,0.1); color: #1a472a; }
    .hub-card .card-icon.gold { background: rgba(212,175,55,0.1); color: #b8860b; }

    /* ===== ANIMATION DES CARTES ===== */
    .module-item {
        opacity: 0;
        animation: fadeInUp 0.6s ease forwards;
    }

    .module-item:nth-child(1) { animation-delay: 0.02s; }
    .module-item:nth-child(2) { animation-delay: 0.05s; }
    .module-item:nth-child(3) { animation-delay: 0.08s; }
    .module-item:nth-child(4) { animation-delay: 0.11s; }
    .module-item:nth-child(5) { animation-delay: 0.14s; }
    .module-item:nth-child(6) { animation-delay: 0.17s; }
    .module-item:nth-child(7) { animation-delay: 0.20s; }
    .module-item:nth-child(8) { animation-delay: 0.23s; }
    .module-item:nth-child(9) { animation-delay: 0.26s; }
    .module-item:nth-child(10) { animation-delay: 0.29s; }
    .module-item:nth-child(11) { animation-delay: 0.32s; }
    .module-item:nth-child(12) { animation-delay: 0.35s; }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* ===== MESSAGE AUCUN RÉSULTAT ===== */
    .no-result {
        display: none;
        text-align: center;
        padding: 60px 20px;
        background: #ffffff;
        border-radius: 16px;
        border: 2px dashed #e2e8f0;
        margin-top: 20px;
        width: 100%;
    }

    .no-result i {
        font-size: 48px;
        color: #cbd5e1;
        display: block;
        margin-bottom: 16px;
    }

    .no-result h4 {
        color: #1e293b;
        font-size: 18px;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .no-result p {
        color: #94a3b8;
        font-size: 14px;
        margin-bottom: 0;
    }

    /* ===== HIGHLIGHT ===== */
    .highlight {
        background: rgba(26, 71, 42, 0.2);
        border-radius: 2px;
        padding: 0 2px;
        color: #1a472a;
        font-weight: 600;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .card-modern .card-header {
            padding: 16px 20px;
            flex-direction: column;
            align-items: stretch;
        }

        .card-modern .card-header .header-left {
            justify-content: center;
        }

        .search-module {
            min-width: 100%;
            max-width: 100%;
        }

        .card-modern .card-body {
            padding: 20px 16px 16px;
        }

        .hub-card {
            min-height: 170px;
            padding: 18px 12px 14px;
        }

        .hub-card .card-icon {
            width: 50px;
            height: 50px;
            line-height: 50px;
            font-size: 24px;
        }

        .hub-card h4 {
            font-size: 15px;
        }

        .hub-card p {
            font-size: 12px;
        }

        .hub-card .btn-ouvrir {
            padding: 4px 18px;
            font-size: 12px;
        }

        #resultsInfo {
            font-size: 13px;
            padding: 10px 16px;
        }

        .section-title .btn-add {
            margin-left: 0;
            width: 100%;
            justify-content: center;
        }
    }

    @media (max-width: 576px) {
        .card-modern .card-header h2 {
            font-size: 18px;
        }

        .card-modern .card-header h2 small {
            font-size: 12px;
            display: block;
            margin-left: 0;
            margin-top: 2px;
        }

        .hub-card {
            min-height: 150px;
            padding: 14px 10px 12px;
        }

        .hub-card .card-icon {
            width: 44px;
            height: 44px;
            line-height: 44px;
            font-size: 20px;
        }

        .search-module input:focus {
            padding-right: 80px;
        }

        .search-module .search-count {
            right: 40px;
            font-size: 10px;
            padding: 1px 8px;
        }

        .search-module .search-clear {
            right: 12px;
            font-size: 14px;
        }
    }
</style>

<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Carte principale -->
        <div class="card-modern">
            <!-- En-tête avec recherche -->
            <div class="card-header">
                <div class="header-left">
                    <div class="brand-icon">
                        <i class="fa fa-users"></i>
                    </div>
                    <h2>
                        Gestion Association
                        <small>Hub des modules associatifs</small>
                    </h2>
                </div>

                <!-- Champ de recherche moderne -->
                <div class="search-module" id="searchModule">
                    <i class="fa fa-search search-icon"></i>
                    <input type="text" id="searchModules" placeholder="Rechercher un module..." onkeyup="filterModules()" autocomplete="off">
                    <span class="search-count" id="searchCount">0</span>
                    <button class="search-clear" id="searchClear" onclick="clearSearch()">
                        <i class="fa fa-times-circle"></i>
                    </button>
                </div>
            </div>

            <div class="card-body">
                <!-- Indicateur de résultats -->
                <div id="resultsInfo">
                    <i class="fa fa-info-circle"></i>
                    <span><span class="result-count" id="resultsCount">0</span> module(s) trouvé(s)</span>
                    <span style="margin-left: auto; font-size: 12px; font-weight: 400; opacity: 0.7;">
                        <span id="resultsTotal"></span>
                    </span>
                </div>

                <!-- ========== SECTION 1 : ADHÉRENTS ========== -->
                <div class="section-title">
                    <i class="fa fa-id-card"></i> Adhérents
                    <span class="badge-section">5 modules</span>
                    <a href="#" class="btn-add"><i class="fa fa-plus"></i> Nouvel adhérent</a>
                </div>
                <div class="row gy-4" id="modulesContainer">

                    <?php if ($this->rbac->hasPrivilege('members', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="liste adherents annuaire membres">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon blue"><i class="fa fa-address-book"></i></div>
                                    <h4>Liste des adhérents</h4>
                                    <p>Annuaire complet des membres</p>
                                    <a href="<?php echo base_url(); ?>admin/Membre_association" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('members', 'can_add')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="ajouter nouveau membre inscription">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon green"><i class="fa fa-user-plus"></i></div>
                                    <h4>Ajouter un adhérent</h4>
                                    <p>Inscription d'un nouveau membre</p>
                                    <a href="<?php echo base_url(); ?>admin/members/add" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('members', 'can_edit')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="fiches individuelles profil membre">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon purple"><i class="fa fa-user-circle"></i></div>
                                    <h4>Fiches individuelles</h4>
                                    <p>Consultation et modification</p>
                                    <a href="<?php echo base_url(); ?>admin/members/profiles" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('members', 'can_export')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="importer exporter csv excel vcard">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon orange"><i class="fa fa-exchange"></i></div>
                                    <h4>Importer / Exporter</h4>
                                    <p>CSV, Excel, vCard</p>
                                    <a href="<?php echo base_url(); ?>admin/members/importexport" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('members', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="etiquettes publipostage courrier">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon teal"><i class="fa fa-envelope"></i></div>
                                    <h4>Étiquettes & Publipostage</h4>
                                    <p>Envoi de courriers groupés</p>
                                    <a href="<?php echo base_url(); ?>admin/members/mailing" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div> <!-- /row Section 1 -->

                <!-- ========== SECTION 2 : COTISATIONS & PAIEMENTS ========== -->
                <div class="section-divider"></div>
                <div class="section-title">
                    <i class="fa fa-credit-card"></i> Cotisations & Paiements
                    <span class="badge-section">6 modules</span>
                </div>
                <div class="row gy-4">

                    <?php if ($this->rbac->hasPrivilege('subscriptions', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="parametres montants tarifs cotisation">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon amber"><i class="fa fa-cog"></i></div>
                                    <h4>Paramétrer les montants</h4>
                                    <p>Tarifs et catégories</p>
                                    <a href="<?php echo base_url(); ?>admin/subscriptions/settings" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('subscriptions', 'can_add')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="saisie paiements encaissement">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon green"><i class="fa fa-money"></i></div>
                                    <h4>Saisie des paiements</h4>
                                    <p>Encaissement par chèque, espèces, virement</p>
                                    <a href="<?php echo base_url(); ?>admin/subscriptions/payment" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('subscriptions', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="suivi cotisations paye impaye echeance">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon blue"><i class="fa fa-bar-chart"></i></div>
                                    <h4>Suivi des cotisations</h4>
                                    <p>Payé / Impayé / Échéance</p>
                                    <a href="<?php echo base_url(); ?>admin/subscriptions/tracking" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('subscriptions', 'can_edit')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="relances automatiques email impayes">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon red"><i class="fa fa-bell"></i></div>
                                    <h4>Relances automatiques</h4>
                                    <p>Email ou courrier pour impayés</p>
                                    <a href="<?php echo base_url(); ?>admin/subscriptions/reminders" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('subscriptions', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="historique transactions membre">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon purple"><i class="fa fa-history"></i></div>
                                    <h4>Historique des transactions</h4>
                                    <p>Par adhérent</p>
                                    <a href="<?php echo base_url(); ?>admin/subscriptions/history" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('subscriptions', 'can_export')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="recus fiscaux dons cotisations">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon gold"><i class="fa fa-file-pdf-o"></i></div>
                                    <h4>Éditer des reçus fiscaux</h4>
                                    <p>Pour dons ou cotisations</p>
                                    <a href="<?php echo base_url(); ?>admin/subscriptions/receipts" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div> <!-- /row Section 2 -->

                <!-- ========== SECTION 3 : BUREAU & ORGANIGRAMME ========== -->
                <div class="section-divider"></div>
                <div class="section-title">
                    <i class="fa fa-sitemap"></i> Bureau & Organigramme
                    <span class="badge-section">3 modules</span>
                </div>
                <div class="row gy-4">

                    <?php if ($this->rbac->hasPrivilege('bureau', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="membres bureau president tresorier secretaire">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon forest"><i class="fa fa-users"></i></div>
                                    <h4>Membres du bureau</h4>
                                    <p>Président, Trésorier, Secrétaire...</p>
                                    <a href="<?php echo base_url(); ?>admin/bureau" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('bureau', 'can_edit')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="roles permissions acces securite">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon purple"><i class="fa fa-lock"></i></div>
                                    <h4>Rôles et permissions</h4>
                                    <p>Accès et sécurité</p>
                                    <a href="<?php echo base_url(); ?>admin/bureau/roles" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('bureau', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="historique mandats dates debut fin">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon teal"><i class="fa fa-clock-o"></i></div>
                                    <h4>Historique des mandats</h4>
                                    <p>Dates de début/fin</p>
                                    <a href="<?php echo base_url(); ?>admin/bureau/mandates" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div> <!-- /row Section 3 -->

                <!-- ========== SECTION 4 : ACTIVITÉS & ÉVÉNEMENTS ========== -->
                <div class="section-divider"></div>
                <div class="section-title">
                    <i class="fa fa-calendar"></i> Activités & Événements
                    <span class="badge-section">5 modules</span>
                    <a href="#" class="btn-add"><i class="fa fa-plus"></i> Créer une activité</a>
                </div>
                <div class="row gy-4">

                    <?php if ($this->rbac->hasPrivilege('activities', 'can_add')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="creer activite nom date lieu responsable">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon cyan"><i class="fa fa-plus-circle"></i></div>
                                    <h4>Créer une activité</h4>
                                    <p>Nom, date, lieu, responsable</p>
                                    <a href="<?php echo base_url(); ?>admin/activities/add" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('activities', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="inscriptions activites adherents">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon blue"><i class="fa fa-check-square-o"></i></div>
                                    <h4>Inscriptions aux activités</h4>
                                    <p>Par adhérent ou en groupe</p>
                                    <a href="<?php echo base_url(); ?>admin/activities/registrations" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('activities', 'can_edit')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="feuilles emargement presence absence">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon orange"><i class="fa fa-pencil"></i></div>
                                    <h4>Feuilles d'émargement</h4>
                                    <p>Présence / Absence</p>
                                    <a href="<?php echo base_url(); ?>admin/activities/attendance" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('activities', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="planning calendrier mensuel hebdomadaire">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon purple"><i class="fa fa-calendar"></i></div>
                                    <h4>Planning / Calendrier</h4>
                                    <p>Vue mensuelle ou hebdomadaire</p>
                                    <a href="<?php echo base_url(); ?>admin/activities/calendar" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('activities', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="gestion salles ressources materiel">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon teal"><i class="fa fa-building"></i></div>
                                    <h4>Gestion des salles / Ressources</h4>
                                    <p>Matériel et lieux</p>
                                    <a href="<?php echo base_url(); ?>admin/activities/resources" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div> <!-- /row Section 4 -->

                <!-- ========== SECTION 5 : COMMUNICATION ========== -->
                <div class="section-divider"></div>
                <div class="section-title">
                    <i class="fa fa-comments"></i> Communication
                    <span class="badge-section">4 modules</span>
                </div>
                <div class="row gy-4">

                    <?php if ($this->rbac->hasPrivilege('communication', 'can_send')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="envoi emails groupes listes adherents">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon pink"><i class="fa fa-envelope"></i></div>
                                    <h4>Envoi d'emails groupés</h4>
                                    <p>À tous ou par filtre</p>
                                    <a href="<?php echo base_url(); ?>admin/communication/email" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('communication', 'can_edit')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="modeles messages relance invitation newsletter">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon indigo"><i class="fa fa-file-text"></i></div>
                                    <h4>Modèles de messages</h4>
                                    <p>Relance, invitation, newsletter</p>
                                    <a href="<?php echo base_url(); ?>admin/communication/templates" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('communication', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="listes diffusion groupes">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon forest"><i class="fa fa-list-ul"></i></div>
                                    <h4>Listes de diffusion</h4>
                                    <p>Groupes de destinataires</p>
                                    <a href="<?php echo base_url(); ?>admin/communication/lists" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('communication', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="archives envois suivi">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon orange"><i class="fa fa-archive"></i></div>
                                    <h4>Archives des envois</h4>
                                    <p>Suivi des envois effectués</p>
                                    <a href="<?php echo base_url(); ?>admin/communication/archive" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div> <!-- /row Section 5 -->

                <!-- ========== SECTION 6 : DOCUMENTS & RESSOURCES ========== -->
                <div class="section-divider"></div>
                <div class="section-title">
                    <i class="fa fa-folder-open"></i> Documents & Ressources
                    <span class="badge-section">2 modules</span>
                </div>
                <div class="row gy-4">

                    <?php if ($this->rbac->hasPrivilege('documents', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="depot fichiers statuts reglement interieur">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon rose"><i class="fa fa-cloud-upload"></i></div>
                                    <h4>Dépôt de fichiers</h4>
                                    <p>Statuts, règlement, PV, comptes rendus</p>
                                    <a href="<?php echo base_url(); ?>admin/documents" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('documents', 'can_edit')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="classement annee type partage securise">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon teal"><i class="fa fa-folder"></i></div>
                                    <h4>Classement & Partage</h4>
                                    <p>Par année, type, accès sécurisé</p>
                                    <a href="<?php echo base_url(); ?>admin/documents/organize" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div> <!-- /row Section 6 -->

                <!-- ========== SECTION 7 : RAPPORTS & STATISTIQUES ========== -->
                <div class="section-divider"></div>
                <div class="section-title">
                    <i class="fa fa-pie-chart"></i> Rapports & Statistiques
                    <span class="badge-section">5 modules</span>
                </div>
                <div class="row gy-4">

                    <?php if ($this->rbac->hasPrivilege('reports', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="evolution adhesions mois annee">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon blue"><i class="fa fa-line-chart"></i></div>
                                    <h4>Évolution des adhésions</h4>
                                    <p>Par mois / année</p>
                                    <a href="<?php echo base_url(); ?>admin/reports/membership" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('reports', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="taux renouvellement">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon green"><i class="fa fa-refresh"></i></div>
                                    <h4>Taux de renouvellement</h4>
                                    <p>Fidélisation des membres</p>
                                    <a href="<?php echo base_url(); ?>admin/reports/renewal" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('reports', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="repartition age genre ville">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon purple"><i class="fa fa-pie-chart"></i></div>
                                    <h4>Répartition démographique</h4>
                                    <p>Âge / Genre / Ville</p>
                                    <a href="<?php echo base_url(); ?>admin/reports/demographics" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('reports', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="synthese financiere total encaisse cotisations">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon gold"><i class="fa fa-credit-card"></i></div>
                                    <h4>Synthèse financière</h4>
                                    <p>Total encaissé, nombre de cotisations</p>
                                    <a href="<?php echo base_url(); ?>admin/reports/financial" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('reports', 'can_export')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="exporter pdf excel rapport activite">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon orange"><i class="fa fa-download"></i></div>
                                    <h4>Exporter les rapports</h4>
                                    <p>PDF / Excel</p>
                                    <a href="<?php echo base_url(); ?>admin/reports/export" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div> <!-- /row Section 7 -->

                <!-- ========== SECTION 8 : PARAMÈTRES ========== -->
                <div class="section-divider"></div>
                <div class="section-title">
                    <i class="fa fa-cogs"></i> Paramètres
                    <span class="badge-section">4 modules</span>
                </div>
                <div class="row gy-4">

                    <?php if ($this->rbac->hasPrivilege('settings', 'can_edit')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="informations generales logo siret adresse">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon indigo"><i class="fa fa-info-circle"></i></div>
                                    <h4>Informations générales</h4>
                                    <p>Nom, logo, SIRET, adresse</p>
                                    <a href="<?php echo base_url(); ?>admin/settings/general" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('settings', 'can_edit')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="annee en cours exercice comptable">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon orange"><i class="fa fa-calendar"></i></div>
                                    <h4>Année en cours</h4>
                                    <p>Exercice comptable</p>
                                    <a href="<?php echo base_url(); ?>admin/settings/fiscalyear" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('settings', 'can_edit')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="personnalisation champs specifiques">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon purple"><i class="fa fa-puzzle-piece"></i></div>
                                    <h4>Personnalisation des champs</h4>
                                    <p>Champs spécifiques à l'association</p>
                                    <a href="<?php echo base_url(); ?>admin/settings/fields" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('settings', 'can_edit')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="types membres categories">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon teal"><i class="fa fa-tags"></i></div>
                                    <h4>Types de membres</h4>
                                    <p>Catégories personnalisables</p>
                                    <a href="<?php echo base_url(); ?>admin/settings/membertypes" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div> <!-- /row Section 8 -->

                <!-- Message "Aucun résultat" -->
                <div class="no-result" id="noResult">
                    <i class="fa fa-search-minus"></i>
                    <h4>Aucun module trouvé</h4>
                    <p>Aucun module ne correspond à votre recherche. Essayez avec d'autres mots-clés.</p>
                </div>

            </div> <!-- /card-body -->
        </div> <!-- /card-modern -->
    </div> <!-- /container-fluid -->
</div>

<!-- ===== SCRIPT DE RECHERCHE ===== -->
<script>
    function filterModules() {
        const input = document.getElementById('searchModules');
        const filter = input.value.toLowerCase().trim();
        const modules = document.querySelectorAll('.module-item');
        const noResult = document.getElementById('noResult');
        const resultsInfo = document.getElementById('resultsInfo');
        const resultsCount = document.getElementById('resultsCount');
        const searchCount = document.getElementById('searchCount');
        const searchClear = document.getElementById('searchClear');
        const searchModule = document.getElementById('searchModule');
        let visibleCount = 0;
        const totalModules = modules.length;

        if (filter.length > 0) {
            searchClear.classList.add('visible');
            searchModule.classList.add('search-active');
        } else {
            searchClear.classList.remove('visible');
            searchModule.classList.remove('search-active');
        }

        modules.forEach(function(module) {
            const name = module.getAttribute('data-name') || '';
            const text = module.textContent.toLowerCase();

            if (name.includes(filter) || text.includes(filter)) {
                module.style.display = '';
                visibleCount++;

                if (filter.length > 0) {
                    highlightText(module, filter);
                } else {
                    removeHighlight(module);
                }
            } else {
                module.style.display = 'none';
            }
        });

        if (filter.length > 0) {
            searchCount.textContent = visibleCount;
            searchCount.classList.add('visible');
            resultsInfo.style.display = 'flex';
            resultsCount.textContent = visibleCount;
            document.getElementById('resultsTotal').textContent = 'sur ' + totalModules + ' modules';
        } else {
            searchCount.classList.remove('visible');
            resultsInfo.style.display = 'none';
        }

        if (visibleCount === 0 && filter !== '') {
            noResult.style.display = 'block';
        } else {
            noResult.style.display = 'none';
        }
    }

    function highlightText(element, searchText) {
        removeHighlight(element);

        const walker = document.createTreeWalker(
            element,
            NodeFilter.SHOW_TEXT,
            {
                acceptNode: function(node) {
                    if (node.parentElement.tagName === 'SCRIPT' ||
                        node.parentElement.tagName === 'STYLE' ||
                        node.parentElement.classList.contains('btn-ouvrir') ||
                        node.parentElement.closest('.btn-ouvrir')) {
                        return NodeFilter.FILTER_REJECT;
                    }
                    return NodeFilter.FILTER_ACCEPT;
                }
            }
        );

        const nodesToReplace = [];
        let node;
        while (node = walker.nextNode()) {
            const text = node.textContent;
            const lowerText = text.toLowerCase();
            const searchIndex = lowerText.indexOf(searchText);

            if (searchIndex !== -1) {
                nodesToReplace.push({
                    node: node,
                    text: text,
                    index: searchIndex,
                    searchLength: searchText.length
                });
            }
        }

        nodesToReplace.forEach(function(item) {
            const span = document.createElement('span');
            const before = document.createTextNode(item.text.substring(0, item.index));
            const highlight = document.createElement('span');
            highlight.className = 'highlight';
            highlight.textContent = item.text.substring(item.index, item.index + item.searchLength);
            const after = document.createTextNode(item.text.substring(item.index + item.searchLength));

            span.appendChild(before);
            span.appendChild(highlight);
            span.appendChild(after);

            item.node.parentNode.replaceChild(span, item.node);
        });
    }

    function removeHighlight(element) {
        const highlights = element.querySelectorAll('.highlight');
        highlights.forEach(function(highlight) {
            const parent = highlight.parentNode;
            const text = highlight.textContent;
            const textNode = document.createTextNode(text);
            parent.replaceChild(textNode, highlight);
            parent.normalize();
        });
    }

    function clearSearch() {
        const input = document.getElementById('searchModules');
        input.value = '';
        filterModules();
        input.focus();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('searchModules');
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                clearSearch();
            }
        });

        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                input.focus();
                input.select();
            }
        });
    });

    console.log('🏛️ Espace Association activé - ' + document.querySelectorAll('.module-item').length + ' modules disponibles');
</script>