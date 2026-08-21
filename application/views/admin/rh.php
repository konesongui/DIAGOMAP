<!-- ============================================================
     PAGE : Tableau de bord RH
     DESCRIPTION : Interface moderne en cards avec sections
                   (Personnel, Salaires, Congés, Tickets, Avancé)
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

    /* Indicateur de recherche active */
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
        background: linear-gradient(135deg, #e8edfd, #d6e0fb);
        border-radius: 12px;
        color: #1a2a5e;
        font-size: 14px;
        font-weight: 500;
        align-items: center;
        gap: 12px;
        animation: slideDown 0.3s ease;
    }

    #resultsInfo i {
        font-size: 18px;
        color: #273772;
    }

    #resultsInfo .result-count {
        font-weight: 700;
        color: #273772;
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

    /* Bouton "Retour" */
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

    /* Section title */
    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: #273772;
        margin-bottom: 20px;
        padding-left: 12px;
        border-left: 4px solid #273772;
        display: flex;
        align-items: center;
        gap: 10px;
        opacity: 0;
        animation: fadeInLeft 0.5s ease forwards;
    }

    .section-title i {
        color: #273772;
    }

    .section-title .badge-section {
        background: #e8edfd;
        color: #273772;
        font-size: 11px;
        padding: 2px 12px;
        border-radius: 20px;
        font-weight: 500;
        margin-left: 8px;
    }

    .section-title .btn-add {
        margin-left: auto;
        background: #273772;
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
        background: #1a2a5e;
        color: white;
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(39, 55, 114, 0.3);
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
        background: linear-gradient(90deg, #273772, #3b82f6);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .hub-card:hover::before {
        opacity: 1;
    }

    .hub-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.08);
        border-color: #dbeafe;
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
        background: rgba(59, 130, 246, 0.08);
        color: #a8adb6;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .hub-card:hover .card-icon {
        background: rgba(59, 130, 246, 0.15);
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
        color: #273772;
        border: 1px solid #273772;
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
        background: #273772;
        color: #fff;
        border-color: #273772;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(39, 55, 114, 0.25);
    }

    /* Badge de notification sur les cartes */
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

    /* ===== CARTE TICKETS SPÉCIALE ===== */
    .hub-card.ticket-card {
        border-color: #fef3c7;
        background: linear-gradient(135deg, #fffbeb, #fef3c7);
    }

    .hub-card.ticket-card .card-icon {
        background: rgba(245, 158, 11, 0.15);
        color: #d97706;
    }

    .hub-card.ticket-card:hover {
        border-color: #f59e0b;
        box-shadow: 0 12px 40px rgba(245, 158, 11, 0.15);
    }

    .hub-card.ticket-card .badge-notif {
        background: #d97706;
    }

    /* Couleurs personnalisées */
    .hub-card .card-icon.blue { background: rgba(59,130,246,0.1); color: #273772; }
    .hub-card .card-icon.green { background: rgba(16,185,129,0.1); color: #10b981; }
    .hub-card .card-icon.purple { background: rgba(139,92,246,0.1); color: #7c3aed; }
    .hub-card .card-icon.orange { background: rgba(245,158,11,0.1); color: #f59e0b; }
    .hub-card .card-icon.red { background: rgba(239,68,68,0.1); color: #ef4444; }
    .hub-card .card-icon.teal { background: rgba(20,184,166,0.1); color: #14b8a6; }
    .hub-card .card-icon.cyan { background: rgba(6,182,212,0.1); color: #06b6d4; }
    .hub-card .card-icon.pink { background: rgba(236,72,153,0.1); color: #ec4899; }
    .hub-card .card-icon.indigo { background: rgba(99,102,241,0.1); color: #4f46e5; }
    .hub-card .card-icon.rose { background: rgba(244,63,94,0.1); color: #f43f5e; }
    .hub-card .card-icon.amber { background: rgba(251,191,36,0.1); color: #d97706; }
    .hub-card .card-icon.emerald { background: rgba(52,211,153,0.1); color: #059669; }

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
        background: rgba(255, 215, 0, 0.25);
        border-radius: 2px;
        padding: 0 2px;
        color: #1a2a5e;
        font-weight: 600;
    }

    /* ===== STATS TICKETS ===== */
    .ticket-stats {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        margin: 16px 0 20px;
        padding: 16px 20px;
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    .ticket-stats .stat-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #64748b;
    }

    .ticket-stats .stat-item .stat-value {
        font-weight: 700;
        color: #1e293b;
        font-size: 15px;
    }

    .ticket-stats .stat-item .stat-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    .ticket-stats .stat-item .stat-dot.open { background: #3b82f6; }
    .ticket-stats .stat-item .stat-dot.in-progress { background: #f59e0b; }
    .ticket-stats .stat-item .stat-dot.closed { background: #10b981; }
    .ticket-stats .stat-item .stat-dot.urgent { background: #ef4444; }

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

        .ticket-stats {
            flex-direction: column;
            gap: 8px;
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
                        <i class="fa fa-id-badge"></i>
                    </div>
                    <h2>
                        Ressources Humaines
                        <small>Hub des modules RH</small>
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

                <!-- ========== SECTION 1 : PILOTAGE RH ========== -->
                <div class="section-title">
                    <i class="fa fa-sitemap"></i> Pilotage RH
                    <span class="badge-section">modules principaux</span>
                </div>
                <div class="row gy-4" id="modulesContainer">

                    <?php if ($this->rbac->hasPrivilege('staff', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="tableau de bord rh ressources humaines">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon blue"><i class="fa fa-dashboard"></i></div>
                                    <h4>Tableau de bord</h4>
                                    <p>Vue d'ensemble RH</p>
                                    <a href="<?php echo base_url(); ?>admin/tableau_rh" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('staff', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="annuaire personnel staff employes">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon blue"><i class="fa fa-address-card"></i></div>
                                    <h4><?php echo $this->lang->line('staff_directory'); ?></h4>
                                    <p>Liste et gestion du personnel</p>
                                    <a href="<?php echo base_url(); ?>admin/staff" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('disable_staff', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="personnel inactif desactive">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon red"><i class="fa fa-user-times"></i></div>
                                    <h4><?php echo $this->lang->line('disabled_staff'); ?></h4>
                                    <p>Personnel inactif</p>
                                    <a href="<?php echo base_url(); ?>admin/staff/disablestafflist" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('staff', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="attestations rh travail fin de contrat salaire">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon amber"><i class="fa fa-file-text-o"></i></div>
                                    <h4>Attestations RH</h4>
                                    <p>Travail, fin de contrat, salaire</p>
                                    <a href="<?php echo base_url(); ?>admin/attestationrh" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div> <!-- /row Section 1 -->

                <!-- ========== SECTION 2 : PRESENCE & BADGES ========== -->
                <div class="section-divider"></div>
                <div class="section-title">
                    <i class="fa fa-qrcode"></i> Presence
                    <span class="badge-section">pointage et controle</span>
                </div>
                <div class="row gy-4">

                    <?php if ($this->rbac->hasPrivilege('staff_attendance', 'can_view')) : ?>
                       <!-- <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="presences absences suivi pointage">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon green"><i class="fa fa-calendar-check-o"></i></div>
                                    <h4><?php echo $this->lang->line('staff_attendance'); ?></h4>
                                    <p>Suivi des présences</p>
                                    <a href="<?php echo base_url(); ?>admin/staffattendance" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>-->
                    <?php endif; ?>       

                    <?php if ($this->rbac->hasPrivilege('generate_staff_id_card', 'can_view')) : ?>
                        <!--<div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="badge qr carte employe presence pointage">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon teal"><i class="fa fa-id-card-o"></i></div>
                                    <h4>Badges QR employes</h4>
                                    <p>Generation et impression des badges</p>
                                    <a href="<?php echo base_url(); ?>admin/generatestaffcertificate/badgeqr" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>-->
                    <?php endif; ?>

                    <!-- NOUVEAU : QR Code Attendance - Afficher QR -->
                    <?php if ($this->rbac->hasPrivilege('staff_attendance', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="qr code scanner presence pointage affichage">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon yellow"><i class="fa fa-qrcode"></i></div>
                                    <h4>Afficher QR Code</h4>
                                    <p>QR pour pointage à l'entrée</p>
                                    <a href="<?php echo base_url(); ?>admin/qrattendance/display_qr" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>  

                    <!-- NOUVEAU : QR Code Attendance - Présences du jour -->
                    <?php if ($this->rbac->hasPrivilege('staff_attendance', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="qr presence jour arrivee depart pointage">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon orange"><i class="fa fa-clock-o"></i></div>
                                    <h4>Présences du jour</h4>
                                    <p>Arrivées et départs en temps réel</p>
                                    <a href="<?php echo base_url(); ?>admin/qrattendance/today_attendance" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- NOUVEAU : QR Code Attendance - Rapport -->
                    <?php if ($this->rbac->hasPrivilege('staff_attendance', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="rapport presence qr code statistiques employe">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon purple"><i class="fa fa-bar-chart"></i></div>
                                    <h4>Rapport Présence</h4>
                                    <p>Rapports et statistiques QR</p>
                                    <a href="<?php echo base_url(); ?>admin/qrattendance/attendance_report" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div> <!-- /row Section 2 -->

                <!-- ========== SECTION 3 : PAIE ET SALAIRES ========== -->
                <div class="section-divider"></div>
                <div class="section-title">
                    <i class="fa fa-money"></i> Paie et salaires
                    <span class="badge-section">remuneration</span>
                </div>
                <div class="row gy-4">

                    <?php if ($this->rbac->hasPrivilege('staff_payroll', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="paie salaire payroll">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon purple"><i class="fa fa-calculator"></i></div>
                                    <h4><?php echo $this->lang->line('payroll'); ?></h4>
                                    <p>Gestion des paies</p>
                                    <a href="<?php echo base_url(); ?>admin/payroll" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('staff_payroll', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="livre paie registre">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon indigo"><i class="fa fa-book"></i></div>
                                    <h4>Livre de paie</h4>
                                    <p>Registre des paies</p>
                                    <a href="<?php echo base_url(); ?>admin/payroll/paie" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('categorie_salaire', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="categorie salariale classification">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon orange"><i class="fa fa-tags"></i></div>
                                    <h4>Catégorie salariale</h4>
                                    <p>Gestion des catégories</p>
                                    <a href="<?php echo base_url(); ?>admin/categorie" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div> <!-- /row Section 3 -->

                <!-- ========== SECTION 4 : CONGES ET DISCIPLINE ========== -->
                <div class="section-divider"></div>
                <div class="section-title">
                    <i class="fa fa-briefcase"></i> Conges et discipline
                    <span class="badge-section">absence, validation, sanctions</span>
                </div>
                <div class="row gy-4">

                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="types conges configuration">
                        <div class="hub-card">
                            <div class="card-content">
                                <div class="card-icon cyan"><i class="fa fa-list"></i></div>
                                <h4><?php echo $this->lang->line('leave_type'); ?></h4>
                                <p>Configuration des types</p>
                                <a href="<?php echo base_url(); ?>admin/leavetypes" class="btn-ouvrir">Ouvrir</a>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="approbation conges validation demandes">
                        <div class="hub-card">
                            <div class="card-content">
                                <div class="card-icon teal"><i class="fa fa-check-circle"></i></div>
                                <h4><?php echo $this->lang->line('approve_leave_request'); ?></h4>
                                <p>Validation des demandes</p>
                                <a href="<?php echo base_url(); ?>admin/leaverequest/leaverequest" class="btn-ouvrir">Ouvrir</a>
                            </div>
                        </div>
                    </div>

                    <?php if ($this->rbac->hasPrivilege('apply_leave', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="demander conges formulaire">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon green"><i class="fa fa-pencil-square-o"></i></div>
                                    <h4><?php echo $this->lang->line('apply_leave'); ?></h4>
                                    <p>Faire une demande</p>
                                    <a href="<?php echo base_url(); ?>admin/staff/leaverequest" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('calendar', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="calendrier conges planning">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon purple"><i class="fa fa-calendar"></i></div>
                                    <h4>Calendrier des congés</h4>
                                    <p>Visualisation globale</p>
                                    <a href="<?php echo base_url(); ?>admin/leaverequest/calendar" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('permission_enquiry', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="permissions demissions depart">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon orange"><i class="fa fa-external-link"></i></div>
                                    <h4>Permissions / Démissions</h4>
                                    <p>Gestion des départs</p>
                                    <a href="<?php echo base_url(); ?>admin/enquiry" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($this->rbac->hasPrivilege('sanction', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="sanctions disciplinaires discipline">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon red"><i class="fa fa-gavel"></i></div>
                                    <h4>Sanction disciplinaire</h4>
                                    <p>Gestion des sanctions</p>
                                    <a href="<?php echo base_url(); ?>admin/sanction/sanction" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div> <!-- /row Section 4 -->

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

        // Afficher/masquer le bouton de suppression
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

        // Mettre à jour le compteur
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

        // Afficher le message si aucun résultat
        if (visibleCount === 0 && filter !== '') {
            noResult.style.display = 'block';
        } else {
            noResult.style.display = 'none';
        }
    }

    // Fonction pour mettre en surbrillance
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

    // Fonction pour effacer la recherche
    function clearSearch() {
        const input = document.getElementById('searchModules');
        input.value = '';
        filterModules();
        input.focus();
    }

    // Détecter la touche Échap
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('searchModules');
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                clearSearch();
            }
        });

        // Focus automatique avec Ctrl+F ou Cmd+F
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                input.focus();
                input.select();
            }
        });
    });

    console.log('🔍 Recherche RH activée - ' + document.querySelectorAll('.module-item').length + ' modules disponibles');
    console.log('🎫 Espace Tickets intégré avec succès');
</script>