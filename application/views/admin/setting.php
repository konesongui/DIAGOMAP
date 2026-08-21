<?php
// ================================================================
// PAGE : Accueil paramètres (module Administration)
// DESCRIPTION : Hub-cards avec barre de recherche et design moderne
// ================================================================
?>

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
    .mak{
        margin-top: 10px;
    }

    .content-wrapper {
        background: #f8fafc;
        padding: 20px 15px;
        min-height: 100vh;
    }

    /* ========================================== */
    /* CARTE MODERN                               */
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
        background-color:#fec32e ;
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

    /* ========================================== */
    /* BARRE DE RECHERCHE                         */
    /* ========================================== */
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

    /* ========================================== */
    /* STATS MINI                                 */
    /* ========================================== */
    .stats-mini {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        background: rgba(255, 255, 255, 0.08);
        padding: 4px 16px;
        border-radius: 30px;
    }

    .stats-mini .stat-item {
        display: flex;
        align-items: center;
        gap: 6px;
        color: rgba(255, 255, 255, 0.8);
        font-size: 12px;
        font-weight: 400;
    }

    .stats-mini .stat-item .stat-number {
        font-weight: 600;
        color: #ffffff;
        font-size: 15px;
    }

    .stats-mini .stat-item .stat-label {
        font-size: 11px;
        color: rgba(255, 255, 255, 0.5);
    }

    .stats-divider {
        width: 1px;
        height: 24px;
        background: rgba(255, 255, 255, 0.15);
    }

    /* ========================================== */
    /* HUB-CARDS                                  */
    /* ========================================== */
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

    /* ========================================== */
    /* SECTIONS                                   */
    /* ========================================== */
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
        background: #fafcff;
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

    .hub-card {
        animation: fadeInUp 0.4s ease forwards;
    }

    .hub-card:nth-child(1) { animation-delay: 0.02s; }
    .hub-card:nth-child(2) { animation-delay: 0.04s; }
    .hub-card:nth-child(3) { animation-delay: 0.06s; }
    .hub-card:nth-child(4) { animation-delay: 0.08s; }
    .hub-card:nth-child(5) { animation-delay: 0.10s; }
    .hub-card:nth-child(6) { animation-delay: 0.12s; }

    /* ========================================== */
    /* RESPONSIVE                                 */
    /* ========================================== */
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
            font-size: 13px;
        }
        .stats-mini .stat-item .stat-label {
            font-size: 10px;
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
                        <i class="fa fa-cogs"></i> Paramètres
                    </h2>
                </div>

                <div class="header-actions">
                    <!-- Mini statistiques -->
                    <div class="stats-mini">
                        <div class="stat-item">
                            <span class="stat-number"><?php echo isset($total_modules) ? $total_modules : '7'; ?></span>
                            <span class="stat-label">Modules</span>
                        </div>
                        <div class="stats-divider"></div>
                        <div class="stat-item">
                            <span class="stat-number"><?php echo isset($active_modules) ? $active_modules : '5'; ?></span>
                            <span class="stat-label">Actifs</span>
                        </div>
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

                <!-- ========== SECTION 1 : CONFIGURATION GÉNÉRALE ========== -->
                <div class="sub-title">
                    <i class="fa fa-sliders" style="color:#3b82f6;"></i> Configuration générale
                    <span class="badge-count">4</span>
                </div>
                <div class="row gy-4">

                    <!-- Paramètres généraux -->
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="parametres generaux configuration application">
                        <div class="hub-card">
                            <span class="badge-module">Config</span>
                            <div class="card-content">
                                <div class="card-icon blue"><i class="fa fa-sliders"></i></div>
                                <h4><?php echo $this->lang->line('general_settings'); ?></h4>
                                <p>Configuration de l'application</p>
                                <a href="<?php echo base_url('schsettings'); ?>" class="btn-ouvrir">Ouvrir</a>
                            </div>
                        </div>
                    </div>

                    <!-- Paramètres email -->
                    <?php if ($this->rbac->hasPrivilege('email_setting', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="parametres email configuration emails">
                            <div class="hub-card">
                                <span class="badge-module">Email</span>
                                <div class="card-content">
                                    <div class="card-icon green"><i class="fa fa-envelope"></i></div>
                                    <h4><?php echo $this->lang->line('email_setting'); ?></h4>
                                    <p>Configuration des emails</p>
                                    <a href="<?php echo base_url('emailconfig'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Départements -->
                    <?php if ($this->rbac->hasPrivilege('department', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item" data-name="departements gestion">
                            <div class="hub-card">
                                <span class="badge-module">Organisation</span>
                                <div class="card-content">
                                    <div class="card-icon purple"><i class="fa fa-building"></i></div>
                                    <h4><?php echo $this->lang->line('department'); ?></h4>
                                    <p>Gestion des départements</p>
                                    <a href="<?php echo base_url('admin/department/department'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Désignations -->
                    <?php if ($this->rbac->hasPrivilege('designation', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="designations postes fonctions">
                            <div class="hub-card">
                                <span class="badge-module">RH</span>
                                <div class="card-content">
                                    <div class="card-icon orange"><i class="fa fa-tag"></i></div>
                                    <h4><?php echo $this->lang->line('designation'); ?></h4>
                                    <p>Gestion des postes</p>
                                    <a href="<?php echo base_url('admin/designation/designation'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>


                    <!-- Rôles et permissions -->
                    <?php if ($this->rbac->hasPrivilege('superadmin')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="roles permissions droits">
                            <div class="hub-card">
                                <span class="badge-module">Sécurité</span>
                                <div class="card-content">
                                    <div class="card-icon red"><i class="fa fa-shield"></i></div>
                                    <h4><?php echo $this->lang->line('roles_permissions'); ?></h4>
                                    <p>Gestion des rôles et droits</p>
                                    <a href="<?php echo base_url('admin/roles'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Utilisateurs -->
                    <?php if ($this->rbac->hasPrivilege('user_status')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="utilisateurs comptes">
                            <div class="hub-card">
                                <span class="badge-module">Comptes</span>
                                <div class="card-content">
                                    <div class="card-icon cyan"><i class="fa fa-user-circle"></i></div>
                                    <h4><?php echo $this->lang->line('users'); ?></h4>
                                    <p>Gestion des comptes</p>
                                    <a href="<?php echo base_url('admin/users'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div> <!-- /row Configuration générale -->

                <!-- ========== SECTION 2 : UTILISATEURS ET RÔLES ========== -->


                <div class="row gy-4">

                    <!-- Sauvegarde / Restauration -->
                    <?php if ($this->rbac->hasPrivilege('backup', 'can_view')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="sauvegarde restauration backup">
                            <div class="hub-card">
                                <span class="badge-module">Sauvegarde</span>
                                <div class="card-content">
                                    <div class="card-icon teal"><i class="fa fa-database"></i></div>
                                    <h4><?php echo $this->lang->line('backup / restore'); ?></h4>
                                    <p>Sauvegardes et restaurations</p>
                                    <a href="<?php echo base_url('admin/admin/backup'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Modules -->
                    <?php if ($this->rbac->hasPrivilege('superadmin')) : ?>
                        <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 module-item mak" data-name="modules activation">
                            <div class="hub-card">
                                <span class="badge-module">Modules</span>
                                <div class="card-content">
                                    <div class="card-icon pink"><i class="fa fa-puzzle-piece"></i></div>
                                    <h4><?php echo $this->lang->line('modules'); ?></h4>
                                    <p>Activation / désactivation des modules</p>
                                    <a href="<?php echo base_url('admin/module'); ?>" class="btn-ouvrir">Ouvrir</a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div> <!-- /row Système et sauvegarde -->

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