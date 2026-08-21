<?php
// ================================================================
// PAGE : Accueil physique (module Administration)
// DESCRIPTION : Interface chic avec champ de recherche
// ================================================================
?>

<style>
    /* ===== STYLES GÉNÉRAUX ===== */

    .mak{
        margin-top: 10px;
    }

    .content-wrapper {
        background: #f0f4f9;
        padding-bottom: 40px;
        min-height: 100vh;
    }

    /* ===== CARD PRINCIPALE ===== */
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

    /* ===== CHAMP DE RECHERCHE ===== */
    .search-module {
        position: relative;
        min-width: 240px;
        max-width: 320px;
        flex: 1 1 200px;
    }

    .search-module .search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.5);
        font-size: 14px;
        pointer-events: none;
        transition: color 0.3s;
    }

    .search-module input {
        width: 100%;
        padding: 10px 16px 10px 42px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 30px;
        background: rgba(255, 255, 255, 0.1);
        color: #ffffff;
        font-size: 14px;
        font-weight: 400;
        outline: none;
        transition: all 0.3s ease;
        backdrop-filter: blur(4px);
    }

    .search-module input::placeholder {
        color: rgba(255, 255, 255, 0.5);
        font-weight: 300;
    }

    .search-module input:focus {
        background: rgba(255, 255, 255, 0.18);
        border-color: rgba(255, 255, 255, 0.5);
        box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.08);
    }

    .search-module input:focus + .search-icon {
        color: rgba(255, 255, 255, 0.8);
    }

    /* ===== CORPS DE LA CARD ===== */
    .card-modern .card-body {
        padding: 32px 28px 28px;
        background: #fafcff;
    }

    /* ===== SOUS-TITRE ===== */
    .sub-title {
        font-size: 15px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 22px;
        padding-left: 14px;
        border-left: 4px solid #273772;
        display: flex;
        align-items: center;
        gap: 10px;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        font-size: 13px;
        color: #64748b;
    }

    .sub-title i {
        color: #273772;
        font-size: 16px;
    }

    /* ===== HUB-CARDS ===== */
    .hub-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 28px 16px 22px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(226, 232, 240, 0.5);
        transition: all 0.35s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        text-align: center;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        min-height: 220px;
        position: relative;
        overflow: hidden;
    }

    /* Effet de lumière au survol */
    .hub-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle at 30% 20%, rgba(39, 55, 114, 0.03), transparent 60%);
        opacity: 0;
        transition: opacity 0.6s ease;
        pointer-events: none;
    }

    .hub-card:hover::before {
        opacity: 1;
    }

    .hub-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px rgba(39, 55, 114, 0.10);
        border-color: rgba(39, 55, 114, 0.15);
    }

    .hub-card .card-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        gap: 4px;
        position: relative;
        z-index: 1;
    }

    .hub-card .card-icon {
        font-size: 26px;
        width: 62px;
        height: 62px;
        line-height: 62px;
        border-radius: 16px;
        transition: all 0.4s ease;
        flex-shrink: 0;
        margin-bottom: 4px;
    }

    .hub-card:hover .card-icon {
        transform: scale(1.05) translateY(-2px);
    }

    .hub-card h4 {
        font-weight: 600;
        font-size: 16px;
        color: #0f172a;
        margin: 8px 0 2px;
        line-height: 1.3;
        letter-spacing: -0.2px;
    }

    .hub-card p {
        font-size: 13px;
        color: #94a3b8;
        margin: 0 0 4px;
        line-height: 1.4;
        font-weight: 400;
    }

    .hub-card .btn-ouvrir {
        background: transparent;
        color: #273772;
        border: 1.5px solid #273772;
        border-radius: 30px;
        padding: 6px 26px;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
        margin-top: 10px;
        letter-spacing: 0.3px;
        position: relative;
        z-index: 1;
    }

    .hub-card .btn-ouvrir:hover {
        background: #273772;
        color: #fff;
        border-color: #273772;
        box-shadow: 0 8px 20px rgba(39, 55, 114, 0.25);
        transform: translateY(-1px);
    }

    /* ===== ICONES COLORÉES (plus douces) ===== */
    .hub-card .card-icon.blue {
        background: linear-gradient(135deg, #e8edfd, #d6e0fb);
        color: #273772;
    }
    .hub-card .card-icon.green {
        background: linear-gradient(135deg, #e4f7ef, #c8f0e0);
        color: #0d9e6c;
    }
    .hub-card .card-icon.purple {
        background: linear-gradient(135deg, #f0eafe, #e0d4f8);
        color: #7c3aed;
    }

    .hub-card .card-icon.calendar {
        background: linear-gradient(135deg, #f0eafe, #e0d4f8);
        color: #edba3a;
    }

    .hub-card .card-icon.orange {
        background: linear-gradient(135deg, #fef3e2, #fce4c8);
        color: #d97706;
    }
    .hub-card .card-icon.teal {
        background: linear-gradient(135deg, #ddf5f3, #bdebe7);
        color: #0d9488;
    }
    .hub-card .card-icon.red {
        background: linear-gradient(135deg, #fde8e8, #fad0d0);
        color: #dc2626;
    }

    /* ===== ESPACEMENT ===== */
    .row.gy-5 {
        --bs-gutter-y: 28px;
    }

    /* ===== DIVISEUR ===== */
    .section-divider {
        border: none;
        height: 1px;
        background: linear-gradient(to right, transparent, #e2e8f0 20%, #e2e8f0 80%, transparent);
        margin: 36px 0 32px;
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
            min-height: 180px;
            padding: 20px 12px 16px;
        }

        .hub-card .card-icon {
            width: 52px;
            height: 52px;
            line-height: 52px;
            font-size: 22px;
        }

        .hub-card h4 {
            font-size: 15px;
        }

        .hub-card p {
            font-size: 12px;
        }

        .hub-card .btn-ouvrir {
            padding: 5px 20px;
            font-size: 12px;
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
            min-height: 160px;
            padding: 16px 10px 14px;
        }
    }

    /* ===== ANIMATION D'APPARITION ===== */
    .hub-card {
        opacity: 0;
        animation: fadeUp 0.5s ease forwards;
    }

    .hub-card:nth-child(1) { animation-delay: 0.05s; }
    .hub-card:nth-child(2) { animation-delay: 0.10s; }
    .hub-card:nth-child(3) { animation-delay: 0.15s; }
    .hub-card:nth-child(4) { animation-delay: 0.20s; }
    .hub-card:nth-child(5) { animation-delay: 0.25s; }
    .hub-card:nth-child(6) { animation-delay: 0.30s; }

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(20px);
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
        padding: 40px 20px;
        color: #94a3b8;
        font-size: 15px;
        width: 100%;
    }

    .no-result i {
        font-size: 32px;
        display: block;
        margin-bottom: 12px;
        color: #cbd5e1;
    }

    /* ========================================== */
    /* CARDS MODERNES DU HUB                      */
    /* ========================================== */

    .module-item {
        margin-bottom: 20px;
    }

    .hub-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        border: 1px solid #eef2f6;
        transition: all 0.3s ease;
        height: 100%;
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
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.08);
        border-color: #dbeafe;
    }

    .hub-card .card-content {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }

    .hub-card .card-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-bottom: 6px;
    }

    /* Couleurs des icônes */
    .hub-card .card-icon.purple {
        background: #ede9fe;
        color: #7c3aed;
    }

    .hub-card .card-icon.blue {
        background: #dbeafe;
        color: #2563eb;
    }

    .hub-card .card-icon.orange {
        background: #fef3c7;
        color: #d97706;
    }

    .hub-card .card-icon.green {
        background: #d1fae5;
        color: #059669;
    }

    .hub-card .card-icon.red {
        background: #fef2f2;
        color: #dc2626;
    }

    .hub-card .card-icon.indigo {
        background: #e0e7ff;
        color: #4f46e5;
    }

    .hub-card .card-icon.pink {
        background: #fce7f3;
        color: #db2777;
    }

    .hub-card .card-icon.teal {
        background: #ccfbf1;
        color: #0d9488;
    }

    .hub-card .card-icon.yellow {
        background: #fef9c3;
        color: #ca8a04;
    }

    .hub-card .card-icon.grey {
        background: #f1f5f9;
        color: #475569;
    }

    .hub-card h4 {
        font-size: 15px;
        font-weight: 600;
        color: #1e293b;
        margin: 0;
        line-height: 1.3;
    }

    .hub-card p {
        font-size: 12px;
        color: #94a3b8;
        margin: 0;
        line-height: 1.4;
        flex: 1;
    }

    .hub-card .btn-ouvrir {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f1f5f9;
        border: none;
        border-radius: 30px;
        padding: 6px 18px;
        font-size: 12px;
        font-weight: 500;
        color: #475569;
        text-decoration: none;
        transition: all 0.3s ease;
        margin-top: 4px;
    }

    .hub-card .btn-ouvrir:hover {
        background: #273772;
        color: #ffffff;
        text-decoration: none;
        transform: translateX(2px);
        box-shadow: 0 4px 12px rgba(39, 55, 114, 0.25);
    }

    .hub-card .btn-ouvrir i {
        font-size: 11px;
        transition: transform 0.3s ease;
    }

    .hub-card .btn-ouvrir:hover i {
        transform: translateX(4px);
    }

    /* Animation d'apparition */
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

    .module-item {
        animation: fadeInUp 0.5s ease forwards;
    }

    .module-item:nth-child(1) { animation-delay: 0.02s; }
    .module-item:nth-child(2) { animation-delay: 0.04s; }
    .module-item:nth-child(3) { animation-delay: 0.06s; }
    .module-item:nth-child(4) { animation-delay: 0.08s; }
    .module-item:nth-child(5) { animation-delay: 0.10s; }
    .module-item:nth-child(6) { animation-delay: 0.12s; }
    .module-item:nth-child(7) { animation-delay: 0.14s; }
    .module-item:nth-child(8) { animation-delay: 0.16s; }
    .module-item:nth-child(9) { animation-delay: 0.18s; }
    .module-item:nth-child(10) { animation-delay: 0.20s; }

    /* Responsive */
    @media (max-width: 768px) {
        .hub-card {
            padding: 16px;
        }

        .hub-card .card-icon {
            width: 40px;
            height: 40px;
            font-size: 18px;
        }

        .hub-card h4 {
            font-size: 14px;
        }
    }

    @media (max-width: 480px) {
        .hub-card .card-content {
            align-items: center;
            text-align: center;
        }

        .hub-card .btn-ouvrir {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="card-modern">

            <!-- ========== EN-TÊTE AVEC RECHERCHE ========== -->
            <div class="card-header">
                <div class="header-left">
                    <div class="brand-icon">
                        <i class="fa fa-building"></i>
                    </div>
                    <h2>
                        Gestion d'églises
                        <small>Hub des modules</small>
                    </h2>
                </div>

                <!-- Champ de recherche pour filtrer les modules -->
                <div class="search-module">
                    <i class="fa fa-search search-icon"></i>
                    <input type="text" id="searchModules" placeholder="Rechercher un module..." onkeyup="filterModules()">
                </div>
            </div>

            <!-- ========== CORPS ========== -->
            <div class="card-body">

                <!-- ===== OPÉRATIONS ===== -->
                <div class="sub-title">
                    <i class="fa fa-tasks"></i> Gestion d'églises
                </div>

                <div class="row gy-5" id="modulesContainer">
            <!-- ========================================== -->
                    <!-- SECTION ÉGLISE                             -->
                    <!-- ========================================== -->
                    <div class="row gy-4">

                        <!-- Rapports des cultes -->
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="rapports cultes eglise dimanche">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon purple"><i class="fa fa-church"></i></div>
                                    <h4>Rapports des cultes</h4>
                                    <p>Gestion des rapports du dimanche</p>
                                    <a href="<?php echo base_url(); ?>admin/rapports_cultes" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>

                        <!-- Membres -->
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="membres eglise fideles">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon blue"><i class="fa fa-users"></i></div>
                                    <h4>Membres</h4>
                                    <p>Gestion des membres de l'église</p>
                                    <a href="<?php echo base_url('admin/membres'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>

                        <!-- Groupes / Cellules -->
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="groupes cellules equipes">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon green"><i class="fa fa-group"></i></div>
                                    <h4>Groupes / Cellules</h4>
                                    <p>Gestion des groupes et cellules</p>
                                    <a href="<?php echo base_url('admin/groupes'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>

                        <!-- Événements -->
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="evenements eglise activites">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon orange"><i class="fa fa-calendar"></i></div>
                                    <h4>Événements</h4>
                                    <p>Gestion des événements</p>
                                    <a href="<?php echo base_url('admin/evenements'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>

                        <!-- Offrandes / Dîmes -->
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="offrandes dimes finances">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon teal"><i class="fa fa-money"></i></div>
                                    <h4>Offrandes & Dîmes</h4>
                                    <p>Gestion des offrandes et dîmes</p>
                                    <a href="<?php echo base_url('admin/offrandes'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>

                        <!-- Prédicateurs -->
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="predicateurs pasteurs">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon cyan"><i class="fa fa-microphone"></i></div>
                                    <h4>Prédicateurs</h4>
                                    <p>Gestion des prédicateurs</p>
                                    <a href="<?php echo base_url('admin/predicateurs'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>

                        <!-- Baptêmes -->
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="baptemes eglise">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon indigo"><i class="fa fa-water"></i></div>
                                    <h4>Baptêmes</h4>
                                    <p>Gestion des baptêmes</p>
                                    <a href="<?php echo base_url('admin/baptemes'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>

                        <!-- Mariages -->
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="mariages unions">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon pink"><i class="fa fa-heart"></i></div>
                                    <h4>Mariages</h4>
                                    <p>Gestion des mariages</p>
                                    <a href="<?php echo base_url('admin/mariages'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>

                        <!-- Funérailles -->
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="funerailles obsèques">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon red"><i class="fa fa-cross"></i></div>
                                    <h4>Funérailles</h4>
                                    <p>Gestion des funérailles</p>
                                    <a href="<?php echo base_url('admin/funerailles'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Plaintes -->
                    <?php if ($this->rbac->hasPrivilege('reclamation', 'can_view')) : ?>
                        <!--<div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="plaintes reclamations suivi">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon red"><i class="fa fa-exclamation-triangle"></i></div>
                                    <h4>Plaintes</h4>
                                    <p>Gestion et suivi</p>
                                    <a href="<?php echo base_url('admin/reclamations'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>-->
                    <?php endif; ?>

                </div> <!-- /row Opérations -->

                <!-- ===== CONFIGURATION ===== -->
                <!--<div class="section-divider"></div>

                <div class="sub-title">
                    <i class="fa fa-cogs"></i> Configuration
                </div>

                <div class="row gy-5" id="configContainer">
                    <?php if ($this->rbac->hasPrivilege('parametre', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="parametres accueil configuration">
                            <div class="hub-card">
                                <div class="card-content">
                                    <div class="card-icon blue"><i class="fa fa-sliders-h"></i></div>
                                    <h4>Paramètres d'accueil</h4>
                                    <p>Configuration front office</p>
                                    <a href="<?php echo base_url('admin/visitorspurpose'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>-->

                <!-- Message "Aucun résultat" -->
                <div class="no-result" id="noResult">
                    <i class="fa fa-search-minus"></i>
                    Aucun module ne correspond à votre recherche.
                </div>

            </div> <!-- /card-body -->
        </div> <!-- /card-modern -->
    </section>
</div>

<!-- ===== SCRIPT DE RECHERCHE ===== -->
<script>
    function filterModules() {
        const input = document.getElementById('searchModules');
        const filter = input.value.toLowerCase().trim();
        const modules = document.querySelectorAll('.module-item');
        let visibleCount = 0;

        modules.forEach(function(module) {
            const name = module.getAttribute('data-name') || '';
            const text = module.textContent.toLowerCase();

            if (name.includes(filter) || text.includes(filter)) {
                module.style.display = '';
                visibleCount++;
            } else {
                module.style.display = 'none';
            }
        });

        // Afficher le message si aucun résultat
        const noResult = document.getElementById('noResult');
        if (visibleCount === 0 && filter !== '') {
            noResult.style.display = 'block';
        } else {
            noResult.style.display = 'none';
        }
    }
</script>