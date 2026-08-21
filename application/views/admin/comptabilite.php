<?php
$hub = isset($comptabilite_hub) && is_array($comptabilite_hub) ? $comptabilite_hub : array();
$summary = isset($hub['summary']) ? $hub['summary'] : array('total' => 0, 'available' => 0, 'planned' => 0, 'ohada' => 0);
$sections = isset($hub['sections']) ? $hub['sections'] : array();
$workflow = isset($hub['workflow']) ? $hub['workflow'] : array();
?>

<style>
    .content-wrapper {
        background: #f5f7fb;
        padding-bottom: 40px;
    }

    .ohada-shell {
        padding: 20px 0 30px;
    }

    .ohada-hero {
        background: linear-gradient(135deg, #FFB900 0%, #273772 55%, #0f172a 100%);
        border-radius: 22px;
        padding: 28px;
        color: #fff;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.15);
        margin-bottom: 22px;
    }

    .ohada-hero-top {
        display: flex;
        justify-content: space-between;
        gap: 18px;
        flex-wrap: wrap;
        align-items: flex-start;
    }

    .ohada-hero-title h1 {
        margin: 0 0 8px;
        font-size: 30px;
        font-weight: 700;
    }

    .ohada-hero-title p {
        margin: 0;
        font-size: 15px;
        line-height: 1.6;
        color: rgba(255, 255, 255, 0.82);
        max-width: 760px;
    }

    .ohada-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.14);
        padding: 8px 14px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.02em;
        text-transform: uppercase;
    }

    .ohada-stats {
        margin-top: 24px;
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .ohada-stat-card {
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 18px;
        padding: 16px 18px;
        backdrop-filter: blur(8px);
    }

    .ohada-stat-card .value {
        display: block;
        font-size: 26px;
        font-weight: 700;
        line-height: 1;
        margin-bottom: 8px;
    }

    .ohada-stat-card .label {
        display: block;
        font-size: 12px;
        color: rgba(255, 255, 255, 0.72);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .ohada-workflow {
        margin-top: 24px;
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .ohada-step {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 14px 16px;
        min-height: 92px;
    }

    .ohada-step strong {
        display: block;
        font-size: 13px;
        margin-bottom: 6px;
    }

    .ohada-step span {
        display: block;
        font-size: 12px;
        color: rgba(255, 255, 255, 0.76);
        line-height: 1.5;
    }

    .ohada-toolbar {
        background: #fff;
        border-radius: 18px;
        padding: 18px;
        margin-bottom: 20px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        border: 1px solid #e5eaf3;
    }

    .ohada-toolbar-top {
        display: flex;
        gap: 12px;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }

    .ohada-search {
        position: relative;
        flex: 1 1 320px;
    }

    .ohada-search input {
        width: 100%;
        border: 1px solid #d7deea;
        border-radius: 14px;
        padding: 12px 44px 12px 42px;
        font-size: 14px;
        color: #0f172a;
        background: #f8fafc;
    }

    .ohada-search i {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        color: #64748b;
        font-size: 14px;
    }

    .ohada-search .fa-search {
        left: 14px;
    }

    .ohada-search .search-clear {
        right: 14px;
        cursor: pointer;
        display: none;
    }

    .ohada-search.has-value .search-clear {
        display: block;
    }

    .ohada-filter-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .ohada-filter-btn {
        border: 1px solid #d7deea;
        background: #fff;
        color: #334155;
        border-radius: 999px;
        padding: 9px 14px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .ohada-filter-btn.active,
    .ohada-filter-btn:hover {
        background: #273772;
        color: #fff;
        border-color: #273772;
    }

    .ohada-toolbar-meta {
        margin-top: 12px;
        font-size: 13px;
        color: #64748b;
        display: flex;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .ohada-section {
        margin-bottom: 24px;
    }

    .ohada-section-header {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        align-items: flex-start;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }

    .ohada-section-title {
        margin: 0;
        font-size: 22px;
        font-weight: 700;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .ohada-section-title i {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #e8eef9;
        color: #273772;
    }

    .ohada-section-header p {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
        max-width: 780px;
    }

    .ohada-section-count {
        background: #eef2ff;
        color: #273772;
        border-radius: 999px;
        padding: 8px 14px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .ohada-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .module-item {
        min-width: 0;
    }

    .ohada-module-card {
        position: relative;
        background: #fff;
        border: 1px solid #e6ebf2;
        border-radius: 18px;
        padding: 18px;
        height: 100%;
        display: flex;
        flex-direction: column;
        box-shadow: 0 6px 20px rgba(15, 23, 42, 0.04);
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
    }

    .ohada-module-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 30px rgba(15, 23, 42, 0.08);
        border-color: #bfd0eb;
    }

    .ohada-module-card.is-planned {
        background: #fbfcfe;
        border-style: dashed;
    }

    .module-topline {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        align-items: center;
        margin-bottom: 14px;
    }

    .module-badge,
    .module-status {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.03em;
    }

    .module-badge {
        background: #f1f5f9;
        color: #475569;
    }

    .module-status.available {
        background: #dcfce7;
        color: #166534;
    }

    .module-status.planned {
        background: #fef3c7;
        color: #92400e;
    }

    .module-icon {
        width: 60px;
        height: 60px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        margin-bottom: 14px;
    }

    .module-icon.blue { background: rgba(59, 130, 246, 0.12); color: #2563eb; }
    .module-icon.green { background: rgba(16, 185, 129, 0.12); color: #059669; }
    .module-icon.purple { background: rgba(139, 92, 246, 0.12); color: #7c3aed; }
    .module-icon.orange { background: rgba(245, 158, 11, 0.12); color: #d97706; }
    .module-icon.red { background: rgba(239, 68, 68, 0.12); color: #dc2626; }
    .module-icon.teal { background: rgba(20, 184, 166, 0.12); color: #0f766e; }
    .module-icon.cyan { background: rgba(6, 182, 212, 0.12); color: #0891b2; }
    .module-icon.pink { background: rgba(236, 72, 153, 0.12); color: #db2777; }
    .module-icon.indigo { background: rgba(79, 70, 229, 0.12); color: #4338ca; }
    .module-icon.gray { background: rgba(100, 116, 139, 0.12); color: #475569; }
    .module-icon.emerald { background: rgba(52, 211, 153, 0.12); color: #059669; }

    .module-title {
        margin: 0 0 8px;
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
    }

    .module-description {
        margin: 0 0 16px;
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
        flex: 1;
    }

    .module-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-top: auto;
    }

    .module-tagline {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
    }

    .module-action,
    .module-action-disabled {
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    .module-action {
        background: #273772;
        color: #fff;
    }

    .module-action:hover {
        background: #1d2f66;
        color: #fff;
    }

    .module-action-disabled {
        background: #e2e8f0;
        color: #64748b;
        cursor: not-allowed;
    }

    .module-hint {
        margin-top: 12px;
        font-size: 12px;
        color: #92400e;
        background: #fff7ed;
        border-radius: 12px;
        padding: 10px 12px;
    }

    .empty-state {
        display: none;
        background: #fff;
        border: 1px dashed #cbd5e1;
        border-radius: 18px;
        padding: 36px 20px;
        text-align: center;
        color: #64748b;
    }

    .empty-state i {
        font-size: 40px;
        display: block;
        margin-bottom: 12px;
        color: #94a3b8;
    }

    @media (max-width: 1199px) {
        .ohada-stats,
        .ohada-workflow,
        .ohada-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767px) {
        .ohada-shell {
            padding-top: 12px;
        }

        .ohada-hero,
        .ohada-toolbar {
            padding: 18px;
            border-radius: 16px;
        }

        .ohada-hero-title h1 {
            font-size: 24px;
        }

        .ohada-stats,
        .ohada-workflow,
        .ohada-grid {
            grid-template-columns: 1fr;
        }

        .ohada-toolbar-meta,
        .module-footer {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div class="content-wrapper">
    <section class="content ohada-shell">
        <div class="ohada-hero">
            <div class="ohada-hero-top">
                <div class="ohada-hero-title">
                    <span class="ohada-pill"><i class="fa fa-shield"></i> SYSCOHADA / OHADA</span>
                    <h1><?php echo html_escape(isset($hub['title']) ? $hub['title'] : 'Espace Comptabilite OHADA'); ?></h1>
                  <!--  <p><?php echo html_escape(isset($hub['subtitle']) ? $hub['subtitle'] : 'Hub comptable flexible.'); ?></p>-->
                </div>
            </div>

           <!-- <div class="ohada-stats">
                <div class="ohada-stat-card">
                    <span class="value"><?php echo (int) $summary['total']; ?></span>
                    <span class="label">Modules visibles</span>
                </div>
                <div class="ohada-stat-card">
                    <span class="value"><?php echo (int) $summary['available']; ?></span>
                    <span class="label">Modules ouverts</span>
                </div>
                <div class="ohada-stat-card">
                    <span class="value"><?php echo (int) $summary['planned']; ?></span>
                    <span class="label">Modules a activer</span>
                </div>
                <div class="ohada-stat-card">
                    <span class="value"><?php echo (int) $summary['ohada']; ?></span>
                    <span class="label">Blocs OHADA</span>
                </div>
            </div>-->

            <?php if (!empty($workflow)) : ?>
               <!-- <div class="ohada-workflow">
                    <?php foreach ($workflow as $step) : ?>
                        <div class="ohada-step">
                            <strong><?php echo html_escape($step['label']); ?></strong>
                            <span><?php echo html_escape($step['module']); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>-->
            <?php endif; ?>
        </div>

        <!--<div class="ohada-toolbar">
            <div class="ohada-toolbar-top">
                <div class="ohada-search" id="ohadaSearchBox">
                    <i class="fa fa-search"></i>
                    <input type="text" id="ohadaSearchInput" placeholder="Rechercher un module, un cycle OHADA ou une fonctionnalite...">
                    <i class="fa fa-times search-clear" id="ohadaSearchClear"></i>
                </div>

                <div class="ohada-filter-buttons">
                    <button type="button" class="ohada-filter-btn active" data-filter="all">Tous</button>
                    <button type="button" class="ohada-filter-btn" data-filter="available">Disponibles</button>
                    <button type="button" class="ohada-filter-btn" data-filter="planned">A configurer</button>
                    <button type="button" class="ohada-filter-btn" data-filter="ohada">OHADA</button>
                    <button type="button" class="ohada-filter-btn" data-filter="configuration">Configuration</button>
                </div>
            </div>

            <div class="ohada-toolbar-meta">
                <span id="moduleCounter"><?php echo (int) $summary['total']; ?> module(s) affiche(s)</span>
                <span>Le hub n'ouvre directement que les modules deja disponibles pour eviter les liens casses.</span>
            </div>
        </div>-->

        <?php foreach ($sections as $section) : ?>
            <div class="ohada-section module-section" data-section="<?php echo html_escape($section['key']); ?>">
                <div class="ohada-section-header">
                    <div>
                        <h2 class="ohada-section-title">
                            <i class="fa <?php echo html_escape($section['icon']); ?>"></i>
                            <?php echo html_escape($section['title']); ?>
                        </h2>
                        <p><?php echo html_escape($section['subtitle']); ?></p>
                    </div>
                    <span class="ohada-section-count"><?php echo (int) $section['count']; ?> module(s)</span>
                </div>

                <div class="ohada-grid">
                    <?php foreach ($section['items'] as $module) : ?>
                        <?php
                        $module_classes = 'ohada-module-card';
                        if ($module['status'] !== 'available') {
                            $module_classes .= ' is-planned';
                        }
                        ?>
                        <div
                            class="module-item"
                            data-section="<?php echo html_escape($section['key']); ?>"
                            data-status="<?php echo html_escape($module['status']); ?>"
                            data-ohada="<?php echo $module['is_ohada'] ? '1' : '0'; ?>"
                            data-search="<?php echo html_escape(strtolower($module['title'] . ' ' . $module['description'] . ' ' . $module['keywords'] . ' ' . $section['title'])); ?>"
                        >
                            <div class="<?php echo $module_classes; ?>">
                                <div class="module-topline">
                                    <span class="module-badge"><?php echo html_escape($module['badge']); ?></span>
                                    <span class="module-status <?php echo html_escape($module['status']); ?>"><?php echo html_escape($module['status_label']); ?></span>
                                </div>

                                <div class="module-icon <?php echo html_escape($module['color']); ?>">
                                    <i class="fa <?php echo html_escape($module['icon']); ?>"></i>
                                </div>

                                <h3 class="module-title"><?php echo html_escape($module['title']); ?></h3>
                                <p class="module-description"><?php echo html_escape($module['description']); ?></p>

                                <div class="module-footer">
                                    <span class="module-tagline"><?php echo $module['is_ohada'] ? 'Conforme cycle OHADA' : 'Module de support'; ?></span>

                                    <?php if ($module['status'] === 'available') : ?>
                                        <a href="<?php echo html_escape($module['url']); ?>" class="module-action">
                                            <i class="fa fa-arrow-right"></i> Ouvrir
                                        </a>
                                    <?php else : ?>
                                        <span class="module-action-disabled">
                                            <i class="fa fa-clock-o"></i> A activer
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <?php if ($module['status'] !== 'available') : ?>
                                    <div class="module-hint">
                                        Ce bloc fait partie du perimetre OHADA, mais le controleur n'est pas encore disponible dans cette installation.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="empty-state" id="emptyModuleState">
            <i class="fa fa-search"></i>
            <strong>Aucun module ne correspond au filtre actuel.</strong>
            <div>Essayez une autre recherche ou changez de filtre.</div>
        </div>
    </section>
</div>

<script type="text/javascript">
    (function() {
        var activeFilter = 'all';
        var searchInput = document.getElementById('ohadaSearchInput');
        var searchBox = document.getElementById('ohadaSearchBox');
        var clearButton = document.getElementById('ohadaSearchClear');
        var filterButtons = document.querySelectorAll('.ohada-filter-btn');
        var modules = document.querySelectorAll('.module-item');
        var sections = document.querySelectorAll('.module-section');
        var counter = document.getElementById('moduleCounter');
        var emptyState = document.getElementById('emptyModuleState');

        function matchesFilter(module) {
            if (activeFilter === 'all') {
                return true;
            }
            if (activeFilter === 'available' || activeFilter === 'planned') {
                return module.getAttribute('data-status') === activeFilter;
            }
            if (activeFilter === 'ohada') {
                return module.getAttribute('data-ohada') === '1';
            }
            return module.getAttribute('data-section') === activeFilter;
        }

        function matchesSearch(module, query) {
            if (!query) {
                return true;
            }
            return (module.getAttribute('data-search') || '').indexOf(query) !== -1;
        }

        function refreshModules() {
            var query = (searchInput.value || '').toLowerCase().trim();
            var visibleCount = 0;

            if (query.length > 0) {
                searchBox.classList.add('has-value');
            } else {
                searchBox.classList.remove('has-value');
            }

            modules.forEach(function(module) {
                var visible = matchesFilter(module) && matchesSearch(module, query);
                module.style.display = visible ? '' : 'none';
                if (visible) {
                    visibleCount++;
                }
            });

            sections.forEach(function(section) {
                var sectionModules = section.querySelectorAll('.module-item');
                var visibleInSection = 0;

                sectionModules.forEach(function(module) {
                    if (module.style.display !== 'none') {
                        visibleInSection++;
                    }
                });

                section.style.display = visibleInSection > 0 ? '' : 'none';

                var countBadge = section.querySelector('.ohada-section-count');
                if (countBadge) {
                    countBadge.textContent = visibleInSection + ' module(s)';
                }
            });

            if (counter) {
                counter.textContent = visibleCount + ' module(s) affiche(s)';
            }

            if (emptyState) {
                emptyState.style.display = visibleCount > 0 ? 'none' : 'block';
            }
        }

        filterButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                activeFilter = button.getAttribute('data-filter') || 'all';
                filterButtons.forEach(function(item) {
                    item.classList.remove('active');
                });
                button.classList.add('active');
                refreshModules();
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', refreshModules);
        }

        if (clearButton) {
            clearButton.addEventListener('click', function() {
                searchInput.value = '';
                refreshModules();
                searchInput.focus();
            });
        }

        refreshModules();
    })();
</script>
