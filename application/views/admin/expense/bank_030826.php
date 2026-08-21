<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>

<style>
    /* ========================================== */
    /* DESIGN MODERNE BANCAIRE                    */
    /* ========================================== */
    @import url('https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap');

    :root {
        --pro-primary: #273772;
        --pro-secondary: #0066B4;
        --pro-accent: #F5A623;
        --pro-light-bg: #F8FAFE;
        --pro-white: #FFFFFF;
        --pro-gray-100: #F0F2F5;
        --pro-gray-200: #E4E7EC;
        --pro-gray-300: #D0D5DD;
        --pro-text-dark: #1E293B;
        --pro-text-muted: #475569;
        --pro-border: #E2E8F0;
        --pro-shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
        --pro-shadow-md: 0 4px 12px rgba(0,0,0,0.08);
        --pro-shadow-lg: 0 10px 25px -5px rgba(0,0,0,0.1);
        --pro-radius: 20px;
        --pro-radius-lg: 16px;
        --pro-radius-sm: 12px;
    }

    .content-wrapper {
        background: var(--pro-light-bg);
        min-height: 100vh;
        padding: 24px 20px;
    }

    .container-fluid {
        max-width: 1400px;
        margin: 0 auto;
    }

    /* ========================================== */
    /* STATISTIQUES - TABLEAU DE BORD             */
    /* ========================================== */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: var(--pro-white);
        border-radius: var(--pro-radius-lg);
        padding: 18px 20px;
        box-shadow: var(--pro-shadow-sm);
        border: 1px solid var(--pro-border);
        transition: all 0.25s ease;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--pro-shadow-md);
        border-color: var(--pro-secondary);
    }

    .stat-card .stat-info h4 {
        font-size: 12px;
        color: var(--pro-text-muted);
        font-weight: 500;
        margin: 0 0 4px 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-card .stat-info .number {
        font-size: 22px;
        font-weight: 700;
        color: var(--pro-text-dark);
        margin: 0;
        line-height: 1.2;
    }

    .stat-card .stat-icon {
        font-size: 32px;
        opacity: 0.7;
    }

    .stat-card:nth-child(1) { border-left: 4px solid white; }
    .stat-card:nth-child(1) .stat-icon { color: white; }
    .stat-card:nth-child(2) { border-left: 4px solid white; }
    .stat-card:nth-child(2) .stat-icon { color: white; }
    .stat-card:nth-child(3) { border-left: 4px solid white; }
    .stat-card:nth-child(3) .stat-icon { color: white; }
    .stat-card:nth-child(4) { border-left: 4px solid white; }
    .stat-card:nth-child(4) .stat-icon { color: #white; }

    /* ========================================== */
    /* FILTRES                                    */
    /* ========================================== */
    .filter-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        padding: 16px 20px;
        background: var(--pro-white);
        border-radius: var(--pro-radius-lg);
        border: 1px solid var(--pro-border);
        margin-bottom: 24px;
        box-shadow: var(--pro-shadow-sm);
    }

    .filter-bar .filter-group {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .filter-bar .filter-group label {
        font-size: 13px;
        font-weight: 500;
        color: var(--pro-text-muted);
        margin: 0;
        white-space: nowrap;
    }

    .filter-bar .filter-group select,
    .filter-bar .filter-group input {
        border-radius: var(--pro-radius-sm);
        border: 1px solid var(--pro-border);
        padding: 8px 14px;
        font-size: 13px;
        background: var(--pro-white);
        transition: all 0.2s;
        min-width: 140px;
        height: 40px;
        color: var(--pro-text-dark);
    }

    .filter-bar .filter-group select:focus,
    .filter-bar .filter-group input:focus {
        outline: none;
        border-color: var(--pro-secondary);
        box-shadow: 0 0 0 3px rgba(0, 102, 180, 0.1);
    }

    .filter-bar .btn-filter {
        background: var(--pro-primary);
        color: #ffffff;
        border: none;
        border-radius: var(--pro-radius-sm);
        padding: 8px 20px;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.2s;
        cursor: pointer;
        height: 40px;
    }

    .filter-bar .btn-filter:hover {
        background: #0F2F4F;
        transform: translateY(-1px);
        box-shadow: var(--pro-shadow-sm);
    }

    .filter-bar .btn-reset {
        background: var(--pro-gray-100);
        color: var(--pro-text-dark);
        border: 1px solid var(--pro-border);
        border-radius: var(--pro-radius-sm);
        padding: 8px 18px;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.2s;
        cursor: pointer;
        height: 40px;
    }

    .filter-bar .btn-reset:hover {
        background: var(--pro-gray-200);
    }

    /* ========================================== */
    /* CARTE PRINCIPALE                           */
    /* ========================================== */
    .loan-simulator-card {
        background: var(--pro-white);
        border-radius: var(--pro-radius);
        box-shadow: var(--pro-shadow-md);
        border: 1px solid var(--pro-border);
        overflow: hidden;
    }

    .simulator-body {
        padding: 24px 28px 28px;
    }

    /* ========================================== */
    /* SECTION EXPORT                             */
    /* ========================================== */
    .export-section {
        background: var(--pro-white);
        border-radius: var(--pro-radius-lg);
        padding: 20px 24px;
        margin-bottom: 24px;
        border: 1px solid var(--pro-border);
        box-shadow: var(--pro-shadow-sm);
    }

    .export-title {
        font-size: 20px;
        font-weight: 600;
        color: var(--pro-primary);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .export-title i {
        color: var(--pro-secondary);
        font-size: 22px;
    }

    .stats-badge {
        background: var(--pro-gray-100);
        border-radius: 40px;
        padding: 6px 18px;
        font-size: 14px;
        font-weight: 500;
        color: var(--pro-text-dark);
        border: 1px solid var(--pro-border);
        display: inline-block;
        margin-right: 8px;
    }

    .stats-badge i {
        color: var(--pro-secondary);
        margin-right: 6px;
    }

    .stats-badge .number {
        font-weight: 700;
        color: var(--pro-primary);
    }

    /* ========================================== */
    /* BOUTONS                                    */
    /* ========================================== */
    .btn-primary-simulator {
        background: var(--pro-primary);
        color: white;
        border: none;
        border-radius: 11px;
        padding: 10px 24px;
        font-weight: 500;
        font-size: 13px;
        transition: all 0.2s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary-simulator:hover {
        background: #0F2F4F;
        transform: translateY(-2px);
        box-shadow: var(--pro-shadow-md);
        color: #fff;
    }

    .btn-outline-simulator {
        background: transparent;
        border: 1px solid var(--pro-border);
        color: var(--pro-text-dark);
        border-radius: 40px;
        padding: 10px 20px;
        font-weight: 500;
        font-size: 13px;
        transition: all 0.2s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-outline-simulator:hover {
        background: var(--pro-gray-100);
        border-color: var(--pro-secondary);
        color: var(--pro-secondary);
    }

    /* ========================================== */
    /* RECHERCHE                                  */
    /* ========================================== */
    .search-container {
        position: relative;
        width: 100%;
        max-width: 400px;
    }

    .search-input {
        width: 100%;
        padding: 10px 18px 10px 44px;
        border: 1px solid var(--pro-border);
        border-radius: 48px;
        font-size: 14px;
        background: var(--pro-white);
        transition: all 0.2s;
        height: 44px;
    }

    .search-input:focus {
        border-color: var(--pro-secondary);
        box-shadow: 0 0 0 3px rgba(0, 102, 180, 0.1);
        outline: none;
    }

    .search-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--pro-text-muted);
    }

    /* ========================================== */
    /* GRILLE BANQUES                             */
    /* ========================================== */
    .banks-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    .bank-card {
        background: var(--pro-white);
        border-radius: var(--pro-radius-lg);
        padding: 20px;
        border: 1px solid var(--pro-border);
        transition: all 0.25s ease;
        cursor: pointer;
        position: relative;
        box-shadow: var(--pro-shadow-sm);
    }

    .bank-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--pro-shadow-lg);
        border-color: var(--pro-secondary);
    }

    .bank-card.selected {
        border: 2px solid var(--pro-secondary);
        background: #F9FCFE;
    }

    .bank-logo-large {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        object-fit: cover;
        background: var(--pro-white);
        padding: 8px;
        border: 1px solid var(--pro-border);
        box-shadow: var(--pro-shadow-sm);
        margin-bottom: 12px;
    }

    .bank-logo-default-large {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--pro-primary), var(--pro-secondary));
        color: white;
        font-weight: 600;
        font-size: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
    }

    .bank-name {
        font-size: 17px;
        font-weight: 700;
        color: var(--pro-primary);
        margin-bottom: 2px;
    }

    .bank-code {
        font-size: 11px;
        background: var(--pro-gray-100);
        padding: 4px 12px;
        border-radius: 30px;
        font-weight: 500;
        color: var(--pro-text-muted);
        display: inline-block;
    }

    .bank-info {
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid var(--pro-border);
        font-size: 13px;
    }

    .bank-info i {
        width: 24px;
        color: var(--pro-secondary);
    }

    .amount-credit {
        color: #10B981;
        font-weight: 600;
    }

    .amount-debit {
        color: #EF4444;
        font-weight: 600;
    }

    /* ========================================== */
    /* ACTIONS MINI                              */
    /* ========================================== */
    .bank-actions-mini {
        position: absolute;
        top: 12px;
        right: 12px;
        display: flex;
        gap: 6px;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .bank-card:hover .bank-actions-mini {
        opacity: 1;
    }

    .btn-action-mini {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: white;
        border: 1px solid var(--pro-border);
        box-shadow: var(--pro-shadow-sm);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-action-mini.btn-warning {
        background: var(--pro-accent);
        color: white;
        border: none;
    }

    .btn-action-mini.btn-danger {
        background: #EF4444;
        color: white;
        border: none;
    }

    /* ========================================== */
    /* MODALS                                     */
    /* ========================================== */
    .modal-simulator .modal-content {
        border-radius: var(--pro-radius);
        border: none;
        overflow: hidden;
        box-shadow: var(--pro-shadow-lg);
    }

    .modal-simulator .modal-header {
        background: var(--pro-primary);
        color: white;
        border: none;
        padding: 18px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-simulator .modal-header .close {
        color: white;
        opacity: 0.8;
        font-size: 28px;
        margin: -6px -8px -6px auto;
        padding: 0;
    }

    .modal-simulator .modal-header .close:hover {
        opacity: 1;
    }

    .modal-simulator .modal-body {
        padding: 24px;
    }

    .form-group-simulator label {
        font-weight: 500;
        color: var(--pro-text-dark);
        margin-bottom: 4px;
        font-size: 13px;
        display: block;
    }

    .form-control-simulator {
        height: 44px;
        border-radius: var(--pro-radius-sm);
        border: 1px solid var(--pro-border);
        padding: 0 16px;
        font-size: 14px;
        transition: 0.2s;
        width: 100%;
    }

    .form-control-simulator:focus {
        border-color: var(--pro-secondary);
        box-shadow: 0 0 0 3px rgba(0, 102, 180, 0.1);
    }

    textarea.form-control-simulator {
        height: auto;
        padding: 12px 16px;
        resize: vertical;
    }

    /* ========================================== */
    /* RESPONSIVE                                 */
    /* ========================================== */
    @media (max-width: 768px) {
        .simulator-body { padding: 16px; }
        .banks-grid { gap: 16px; }
        .btn-primary-simulator, .btn-outline-simulator { padding: 8px 16px; font-size: 12px; }
        .export-section .d-flex { flex-direction: column; gap: 12px; }
        .filter-bar { flex-direction: column; align-items: stretch; }
        .filter-bar .filter-group { width: 100%; }
        .filter-bar .filter-group select, .filter-bar .filter-group input { width: 100%; min-width: unset; }
        .search-container { max-width: 100%; }
        .stats-grid { grid-template-columns: 1fr 1fr; }
        .stat-card { padding: 14px 16px; }
        .stat-card .stat-info .number { font-size: 18px; }
        .bank-actions-mini { opacity: 1; }
        .modal-simulator .modal-body { padding: 16px; }
    }

    @media (max-width: 480px) {
        .stats-grid { grid-template-columns: 1fr; }
        .export-title { font-size: 17px; }
        .bank-card { padding: 16px; }
    }
</style>

<div class="content-wrapper">
    <div class="container-fluid">

        <!-- ========================================== -->
        <!-- CARTE PRINCIPALE                            -->
        <!-- ========================================== -->
        <div class="loan-simulator-card">
            <div class="simulator-body">

                <!-- ========================================== -->
                <!-- TABLEAU DE BORD - STATISTIQUES              -->
                <!-- ========================================== -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-info">
                            <h4 style="color: #273772; font-family: Roboto-Bold"><i class="fa fa-university"></i> Total banques</h4>
                            <p class="number" id="totalBanks"><?php echo count($banks); ?></p>
                        </div>
                        <div class="stat-icon"><i class="fa fa-building"></i></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-info">
                            <h4 style="color: #273772; font-family: Roboto-Bold"><i class="fa fa-credit-card"></i> Solde total</h4>
                            <p class="number">
                                <?php
                                $total_balance = 0;
                                foreach ($banks as $bank) {
                                    $total_balance += isset($bank->balance) ? $bank->balance : 0;
                                }
                                echo number_format($total_balance, 0, ',', ' ') . ' ';
                                ?>
                            </p>
                        </div>
                        <div class="stat-icon"><i class="fa fa-money"></i></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-info">
                            <h4 style="color: #273772; font-family: Roboto-Bold"><i class="fa fa-arrow-up" style="color:#10b981;"></i> Crédits</h4>
                            <p class="number">
                                <?php
                                $total_credit = 0;
                                foreach ($banks as $bank) {
                                    $balance = isset($bank->balance) ? $bank->balance : 0;
                                    if ($balance > 0) $total_credit += $balance;
                                }
                                echo number_format($total_credit, 0, ',', ' ') . ' ';
                                ?>
                            </p>
                        </div>
                        <div class="stat-icon"><i class="fa fa-arrow-up"></i></div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-info">
                            <h4 style="color: #273772; font-family: Roboto-Bold"><i class="fa fa-arrow-down" style="color:#ef4444;"></i> Débits</h4>
                            <p class="number">
                                <?php
                                $total_debit = 0;
                                foreach ($banks as $bank) {
                                    $balance = isset($bank->balance) ? $bank->balance : 0;
                                    if ($balance < 0) $total_debit += abs($balance);
                                }
                                echo number_format($total_debit, 0, ',', ' ') . ' ';
                                ?>
                            </p>
                        </div>
                        <div class="stat-icon"><i class="fa fa-arrow-down"></i></div>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- FILTRES                                    -->
                <!-- ========================================== -->
                <div class="filter-bar" style="background-color: gainsboro">
                    <div class="filter-group">
                        <label><i class="fa fa-filter"></i> Type :</label>
                        <select id="filterType" onchange="applyFilters()">
                            <option value="">Toutes</option>
                            <?php if (!empty($banks)): ?>
                                <?php foreach ($banks as $bank): ?>
                                    <option value="<?php echo htmlspecialchars($bank->name); ?>"><?php echo htmlspecialchars($bank->name); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label><i class="fa fa-credit-card"></i> Solde :</label>
                        <select id="filterBalance" onchange="applyFilters()">
                            <option value="">Tous</option>
                            <option value="positive">Créditeur</option>
                            <option value="negative">Débiteur</option>
                            <option value="zero">Zéro</option>
                        </select>
                    </div>
                    <div style="display:flex;gap:8px;margin-left:auto;flex-wrap:wrap;">

                        <button class="btn-reset" onclick="resetFilters()"><i class="fa fa-undo"></i> Réinitialiser</button>
                        <button class="btn btn-outline-simulator" onclick="printBankList()"><i class="fa fa-print"></i> Imprimer</button>
                        <button class="btn btn-primary-simulator" data-toggle="modal" data-target="#bankModal"><i class="fa fa-plus"></i> Nouvelle Banque</button>
                    </div>
                </div>

                <!-- ========================================== -->
                <!-- SECTION EXPORT                             -->
                <!-- ========================================== -->
                <!--<div class="export-section">
                    <div class="export-title">
                        <i class="fa fa-university"></i> Gestion des Banques
                    </div>
                      <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <div>
                            <div class="stats-badge">
                                <i class="fa fa-building"></i> Établissements: <span class="number" id="totalBanksDisplay"><?php echo count($banks); ?></span>
                            </div>
                            <div class="stats-badge">
                                <i class="fa fa-credit-card"></i> Solde total: <span class="number">
                                    <?php echo number_format($total_balance, 0, ',', ' ') . ' ' . $currency_symbol; ?>
                                </span>
                            </div>
                        </div>
                      <div class="d-flex flex-wrap gap-2 mt-2 mt-sm-0">
                            <button class="btn btn-outline-simulator" onclick="exportAllBanksPDF()"><i class="fa fa-file-pdf-o"></i> PDF</button>
                            <button class="btn btn-outline-simulator" onclick="exportAllBanksExcel()"><i class="fa fa-file-excel-o"></i> Excel</button>

                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="search-container">
                            <i class="fa fa-search search-icon"></i>
                            <input type="text" class="search-input" id="searchBanks" placeholder="Rechercher une banque... (nom, code, compte)">
                        </div>
                    </div>
                </div>-->

                <!-- ========================================== -->
                <!-- GRILLE DES BANQUES                         -->
                <!-- ========================================== -->
                <?php if (!empty($banks)): ?>
                    <div class="banks-grid" id="banksGrid">
                        <?php foreach ($banks as $bank):
                            $balance = isset($bank->balance) ? $bank->balance : 0;
                            $initial_balance = isset($bank->initial_balance) ? $bank->initial_balance : 0;
                            $logo_url = !empty($bank->logo) ? base_url($bank->logo) : '';
                            $bank_initial = strtoupper(substr($bank->name, 0, 2));
                            ?>
                            <div class="bank-card"
                                 data-bank-id="<?php echo $bank->id; ?>"
                                 data-bank-name="<?php echo htmlspecialchars($bank->name); ?>"
                                 data-bank-code="<?php echo htmlspecialchars($bank->code); ?>"
                                 data-bank-balance="<?php echo $balance; ?>"
                                 data-initial-balance="<?php echo $initial_balance; ?>"
                                 data-bank-logo="<?php echo $logo_url; ?>"
                                 data-bank-account="<?php echo htmlspecialchars($bank->account_number ?? ''); ?>">
                                <div class="bank-actions-mini">
                                    <button class="btn-action-mini edit-bank-mini" data-id="<?php echo $bank->id; ?>" data-name="<?php echo htmlspecialchars($bank->name); ?>" data-code="<?php echo htmlspecialchars($bank->code); ?>" data-account_number="<?php echo htmlspecialchars($bank->account_number ?? ''); ?>" data-logo="<?php echo $logo_url; ?>" data-initial_balance="<?php echo $initial_balance; ?>" title="Modifier"><i class="fa fa-pencil"></i></button>
                                    <button class="btn-action-mini delete-bank-mini" data-id="<?php echo $bank->id; ?>" title="Supprimer"><i class="fa fa-trash-o"></i></button>
                                </div>
                                <?php if ($logo_url): ?>
                                    <img src="<?php echo $logo_url; ?>" class="bank-logo-large" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="bank-logo-default-large" style="display: none;"><?php echo $bank_initial; ?></div>
                                <?php else: ?>
                                    <div class="bank-logo-default-large"><?php echo $bank_initial; ?></div>
                                <?php endif; ?>
                                <div class="bank-name"><?php echo htmlspecialchars($bank->name); ?></div>
                                <div class="bank-code"><?php echo htmlspecialchars($bank->code); ?></div>
                                <div class="bank-info">
                                    <?php if (!empty($bank->account_number)): ?>
                                        <div><i class="fa fa-credit-card"></i> <?php echo htmlspecialchars($bank->account_number); ?></div>
                                    <?php endif; ?>
                                    <div><i class="fa fa-<?php echo $balance >= 0 ? 'arrow-up' : 'arrow-down'; ?>"></i> <span class="<?php echo $balance >= 0 ? 'amount-credit' : 'amount-debit'; ?>"><?php echo number_format($balance, 0, ',', ' ') . ' ' . $currency_symbol; ?></span></div>
                                </div>
                                <div class="d-flex justify-content-center gap-2 mt-3">
                                    <button class="btn btn-outline-simulator btn-sm add-transaction-btn" data-bank-id="<?php echo $bank->id; ?>" data-bank-name="<?php echo htmlspecialchars($bank->name); ?>"><i class="fa fa-plus"></i> Transaction</button>
                                    <button class="btn btn-primary-simulator btn-sm view-transactions-btn" data-bank-id="<?php echo $bank->id; ?>" data-bank-name="<?php echo htmlspecialchars($bank->name); ?>"><i class="fa fa-eye"></i> Détails</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <div class="bank-logo-default-large mx-auto mb-4" style="width: 100px; height: 100px; font-size: 48px;"><i class="fa fa-university"></i></div>
                        <h3 class="mb-3" style="color: var(--pro-primary);">Aucune banque enregistrée</h3>
                        <p class="text-muted mb-4">Commencez par ajouter votre première banque</p>
                        <button class="btn btn-primary-simulator" data-toggle="modal" data-target="#bankModal"><i class="fa fa-plus mr-2"></i> Créer une banque</button>
                    </div>
                <?php endif; ?>

                <!--<div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-outline-simulator" id="refreshPageBtn"><i class="fa fa-refresh mr-2"></i> Actualiser</button>
                </div>

                <div class="disclaimer mt-4" style="background: #FEFCE8; border-left: 4px solid var(--pro-accent); border-radius: 14px; padding: 16px 20px;">
                    <h6 style="color: var(--pro-primary); font-weight: 600;"><i class="fa fa-info-circle mr-2"></i> Information importante</h6>
                    <p style="color: var(--pro-text-muted); font-size: 13px; margin: 0;">Les données affichées sont basées sur les informations enregistrées. Pour des données à jour, veuillez consulter vos relevés bancaires officiels.</p>
                </div>-->
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL BANQUE                               -->
<!-- ========================================== -->
<div class="modal fade modal-simulator" id="bankModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="bankModalLabel"><i class="fa fa-university mr-2"></i> Nouvelle Banque</h4>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <button type="button" class="close" data-dismiss="modal"
                    aria-label="Close"
                    style="color: #ffffff; opacity: 0.8; font-size: 32px; font-weight: 300; text-shadow: none; border: none; border-radius: 50%; padding: 57px; margin: 16px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease; flex-shrink: 0; line-height: 1;"
                    onmouseover="this.style.opacity='1'; this.style.background='rgba(255,255,255,0.25)'; this.style.transform='rotate(90deg)';"
                    onmouseout="this.style.opacity='0.8'; this.style.background='rgba(255,255,255,0.1)'; this.style.transform='rotate(0)';">
                <span style="line-height: 1; font-size: 28px;">&times;</span>
            </button>
            <form id="bankForm" action="<?php echo base_url() ?>admin/expense/save_bank" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <?php echo $this->customlib->getCSRF(); ?>
                    <input type="hidden" id="bank_id_input" name="bank_id" value="">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <div id="logoPreview" style="margin-bottom: 20px;">
                                <div class="bank-logo-default-large" id="defaultLogo" style="margin:0 auto;"><span id="logoInitials">BN</span></div>
                                <img id="logoImage" src="" alt="" class="bank-logo-large" style="display:none; margin:0 auto;">
                            </div>
                            <label class="btn btn-outline-simulator upload-logo-btn"><i class="fa fa-camera"></i> Choisir logo
                                <input type="file" name="logo" id="logoInput" accept="image/*" style="display:none;">
                            </label>
                            <small class="text-muted d-block mt-2">JPG, PNG, SVG (max 2MB)</small>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group-simulator"><label>Nom de la banque *</label><input type="text" id="bank_name" name="bank_name" class="form-control form-control-simulator" required oninput="updateLogoInitials()" placeholder="Ex: BIN, NSA Banque, Ecobank..."></div>
                            <div class="form-group-simulator"><label>Code banque *</label><input type="text" id="bank_code" name="bank_code" class="form-control form-control-simulator" required placeholder="Ex: BIC, SWIFT"></div>
                            <div class="form-group-simulator"><label>Numéro de compte</label><input type="text" id="account_number" name="account_number" class="form-control form-control-simulator" placeholder="Numéro de compte bancaire"></div>
                            <div class="form-group-simulator"><label>Solde initial</label>
                                <div class="input-group">
                                    <span class="input-group-addon" style="background: var(--pro-gray-100); border:1px solid var(--pro-border); border-right:none; border-radius:12px 0 0 12px; padding:0 15px; display:flex; align-items:center;"><?php echo $currency_symbol; ?></span>
                                    <input type="number" id="initial_balance" name="initial_balance" class="form-control form-control-simulator" step="0.01" value="0.00" min="0" placeholder="0.00" style="border-radius:0 12px 12px 0;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary-simulator"><i class="fa fa-save"></i> Enregistrer</button>
                    <button type="button" class="btn btn-outline-simulator" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL TRANSACTION                          -->
<!-- ========================================== -->
<div class="modal fade modal-simulator" id="transactionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="transactionForm" action="<?php echo base_url() ?>admin/expense/bank" method="post">
                <div class="modal-header">
                    <h4 class="modal-title" id="transactionModalLabel"><i class="fa fa-exchange mr-2"></i> Nouvelle Transaction</h4>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close"
                        style="color: #ffffff; opacity: 0.8; font-size: 32px; font-weight: 300; text-shadow: none; border: none; border-radius: 50%; padding: 57px; margin: 16px; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.3s ease; flex-shrink: 0; line-height: 1;"
                        onmouseover="this.style.opacity='1'; this.style.background='rgba(255,255,255,0.25)'; this.style.transform='rotate(90deg)';"
                        onmouseout="this.style.opacity='0.8'; this.style.background='rgba(255,255,255,0.1)'; this.style.transform='rotate(0)';">
                    <span style="line-height: 1; font-size: 28px;">&times;</span>
                </button>
                <div class="modal-body">
                    <?php echo $this->customlib->getCSRF(); ?>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-simulator"><label>Banque *</label>
                                <select id="transaction_bank_id" name="bank_id" class="form-control form-control-simulator" required>
                                    <option value="">Sélectionner</option>
                                    <?php if (!empty($banks) && is_array($banks)): foreach ($banks as $bank): ?>
                                        <option value="<?php echo $bank->id; ?>"><?php echo htmlspecialchars($bank->name . ' (' . $bank->code . ')'); ?></option>
                                    <?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-simulator"><label>Type de transaction *</label>
                                <select id="transaction_type" name="transaction_type" class="form-control form-control-simulator" required>
                                    <option value="">Sélectionner</option>
                                    <option value="Dépôt">Dépôt</option>
                                    <option value="Retrait">Retrait</option>
                                    <option value="Virement entrant">Virement entrant</option>
                                    <option value="Virement sortant">Virement sortant</option>
                                    <option value="Chèque">Chèque</option>
                                    <option value="Prélèvement">Prélèvement</option>
                                    <option value="Frais bancaires">Frais bancaires</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-simulator"><label>Opération *</label>
                                <select id="designation" name="designation" class="form-control form-control-simulator" required>
                                    <option value="">Sélectionner</option>
                                    <option value="Débit">Sortie</option>
                                    <option value="Crédit">Entrée</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-simulator"><label>Date *</label>
                                <input id="date" name="date" type="text" class="form-control form-control-simulator date" value="<?php echo set_value('date', date($this->customlib->getSchoolDateFormat())); ?>" readonly required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-simulator"><label>Libellé *</label>
                                <input id="name" name="name" type="text" class="form-control form-control-simulator" required placeholder="Libellé de l'opération">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-simulator"><label>Nom</label>
                                <input id="nom" name="nom" type="text" class="form-control form-control-simulator" placeholder="Nom du bénéficiaire ou émetteur">
                                <small class="text-muted">Nom de la personne ou entreprise concernée</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-simulator"><label>Montant *</label>
                                <div class="input-group">
                                    <span class="input-group-addon" style="background: var(--pro-gray-100); border:1px solid var(--pro-border); border-right:none; border-radius:12px 0 0 12px; padding:0 15px;"><?php echo $currency_symbol; ?></span>
                                    <input id="amount" name="amount" type="number" step="0.01" class="form-control form-control-simulator" required style="border-radius:0 12px 12px 0;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-simulator"><label>Numéro de référence</label>
                                <input id="reference" name="reference" type="text" class="form-control form-control-simulator" placeholder="N° chèque, virement, etc.">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-simulator"><label>Mode de paiement</label>
                                <select id="payment_mode" name="payment_mode" class="form-control form-control-simulator">
                                    <option value="">Sélectionner</option>
                                    <option value="Espèces">Espèces</option>
                                    <option value="Chèque">Chèque</option>
                                    <option value="Virement">Virement</option>
                                    <option value="Carte">Carte bancaire</option>
                                    <option value="Prélèvement">Prélèvement</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-simulator"><label>Nom de la banque</label>
                                <input id="category" name="category" type="text" class="form-control form-control-simulator" placeholder="Banque">
                            </div>
                        </div>
                    </div>
                    <div class="form-group-simulator"><label>Description</label>
                        <textarea id="description" name="description" class="form-control form-control-simulator" rows="3" placeholder="Détails de la transaction"></textarea>
                    </div>
                    <div class="form-group-simulator" hidden><label>Pièce jointe</label>
                        <input id="documents" name="documents" type="file" class="form-control form-control-simulator">
                        <small class="text-muted">Extrait bancaire, chèque, etc. (PDF, JPG, PNG)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary-simulator"><i class="fa fa-save"></i> Enregistrer</button>
                    <button type="button" class="btn btn-outline-simulator" data-dismiss="modal"><i class="fa fa-times"></i> Fermer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL VOIR TRANSACTIONS                    -->
<!-- ========================================== -->
<div class="modal fade modal-simulator" id="viewTransactionsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fa fa-list-alt mr-2"></i> <span id="bankNameTitle" style="font-weight:600;"></span></h4>
                <button type="button" class="btn btn-outline-simulator" data-dismiss="modal" style="color: white;margin-left: 76%"><i class="fa fa-times"></i> Fermer</button>
            </div>
            <div class="modal-body">
                <div id="bankTransactionsContent">
                    <div class="text-center py-5">
                        <div class="spinner-border" role="status"><span class="sr-only">Chargement...</span></div>
                        <p class="mt-3 text-muted">Chargement des transactions...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-simulator" data-dismiss="modal"><i class="fa fa-times"></i> Fermer</button>
                <button type="button" class="btn btn-danger" onclick="exportBankTransactionsPDF()"><i class="fa fa-file-pdf-o"></i> PDF</button>
                <button type="button" class="btn btn-success" onclick="exportBankTransactionsExcel()"><i class="fa fa-file-excel-o"></i> Excel</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL SUPPRESSION                          -->
<!-- ========================================== -->
<div class="modal fade modal-simulator" id="deleteBankModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><i class="fa fa-exclamation-triangle mr-2"></i> Confirmation de suppression</h4>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette banque ?</p>
                <p class="text-warning"><small><i class="fa fa-warning"></i> Cette action ne peut pas être annulée.</small></p>
                <p id="deleteWarningMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="confirmDelete"><i class="fa fa-trash"></i> Supprimer</button>
                <button type="button" class="btn btn-outline-simulator" data-dismiss="modal"><i class="fa fa-times"></i> Annuler</button>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- SCRIPTS                                    -->
<!-- ========================================== -->
<script>
    /* ============= SCRIPTS ORIGINAUX CONSERVÉS ============= */
    var currentBankId = null;
    var currentBankName = null;
    var currentTransactionsHTML = null;

    $(document).ready(function() { initializeEventHandlers(); updateLogoInitials(); });

    function updateLogoInitials() { var name = $('#bank_name').val(); var initials = name ? name.substring(0,2).toUpperCase() : 'BN'; $('#logoInitials').text(initials); }
    $('#logoInput').change(function(e) { var file = e.target.files[0]; if(file) { var reader = new FileReader(); reader.onload = function(e) { $('#logoImage').attr('src', e.target.result).show(); $('#defaultLogo').hide(); }; reader.readAsDataURL(file); } });
    $('#newLogoInput').change(function(e) { var file = e.target.files[0]; if(file) { var reader = new FileReader(); reader.onload = function(e) { $('#previewImage').attr('src', e.target.result); $('#newLogoPreview').show(); }; reader.readAsDataURL(file); } });

    function showToast(type, message) {
        var toastClass = 'alert-success';
        var icon = '<i class="fa fa-check-circle"></i> ';
        var title = 'Succès';
        if(type == 'error') { toastClass = 'alert-danger'; icon = '<i class="fa fa-exclamation-circle"></i> '; title = 'Erreur'; }
        else if(type == 'warning') { toastClass = 'alert-warning'; icon = '<i class="fa fa-exclamation-triangle"></i> '; title = 'Attention'; }
        else if(type == 'info') { toastClass = 'alert-info'; icon = '<i class="fa fa-info-circle"></i> '; title = 'Information'; }
        $('.custom-toast').remove();
        var toast = '<div class="alert ' + toastClass + ' alert-dismissible custom-toast fade show" role="alert" style="position: fixed; top: 70px; right: 20px; z-index: 1060; min-width: 300px; max-width: 400px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-left: 4px solid ' + (type=='success'?'#27ae60':type=='error'?'#e74c3c':type=='warning'?'#f39c12':'#3498db') + ';"><button type="button" class="close" data-dismiss="alert" aria-label="Close" style="position: absolute; top:5px; right:10px;"><span aria-hidden="true">&times;</span></button><div class="d-flex align-items-start"><div style="font-size:20px; margin-right:10px;">' + icon + '</div><div style="flex:1; padding-right:20px;"><strong style="display:block; margin-bottom:2px;">' + title + '</strong><span style="font-size:13px;">' + message + '</span></div></div></div>';
        $('body').append(toast);
        $('.custom-toast').hide().fadeIn(300);
        setTimeout(function() { $('.custom-toast').fadeOut(300, function() { $(this).remove(); }); }, 4000);
    }

    function formatNumber(number) { return parseFloat(number).toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }

    // ========================================== //
    // RECHERCHE                                  //
    // ========================================== //
    $('#searchBanks').on('input', function() {
        var searchTerm = $(this).val().toLowerCase();
        var bankCards = $('.bank-card');
        var visibleCount = 0;
        bankCards.each(function() {
            var bankName = $(this).data('bank-name').toLowerCase();
            var bankCode = $(this).data('bank-code').toLowerCase();
            var bankAccount = $(this).data('bank-account').toLowerCase();
            if(bankName.includes(searchTerm) || bankCode.includes(searchTerm) || bankAccount.includes(searchTerm) || searchTerm === '') {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });
        $('#totalBanks').text(visibleCount);
        $('#totalBanksDisplay').text(visibleCount);
    });

    // ========================================== //
    // FILTRES                                    //
    // ========================================== //
    function applyFilters() {
        var filterType = document.getElementById('filterType').value.toLowerCase();
        var filterBalance = document.getElementById('filterBalance').value;
        var bankCards = $('.bank-card');
        var visibleCount = 0;

        bankCards.each(function() {
            var bankName = $(this).data('bank-name').toLowerCase();
            var balance = parseFloat($(this).data('bank-balance'));
            var show = true;

            if (filterType && !bankName.includes(filterType)) show = false;
            if (filterBalance === 'positive' && balance <= 0) show = false;
            if (filterBalance === 'negative' && balance >= 0) show = false;
            if (filterBalance === 'zero' && balance !== 0) show = false;

            if (show) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });

        $('#totalBanks').text(visibleCount);
        $('#totalBanksDisplay').text(visibleCount);
    }

    function resetFilters() {
        document.getElementById('filterType').value = '';
        document.getElementById('filterBalance').value = '';
        $('#searchBanks').val('');
        $('.bank-card').show();
        var total = $('.bank-card').length;
        $('#totalBanks').text(total);
        $('#totalBanksDisplay').text(total);
    }

    // ========================================== //
    // INITIALISATION DES ÉVÉNEMENTS              //
    // ========================================== //
    function initializeEventHandlers() {
        $('.add-transaction-btn').click(function(e) { e.stopPropagation(); var bankId = $(this).data('bank-id'); var bankName = $(this).data('bank-name'); $('#transaction_bank_id').val(bankId); $('#transactionModalLabel').html('<i class="fa fa-exchange mr-2"></i> Nouvelle Transaction - ' + bankName); $('#transactionModal').modal('show'); });
        $('.view-transactions-btn').click(function(e) { e.stopPropagation(); var bankId = $(this).data('bank-id'); var bankName = $(this).data('bank-name'); currentBankId = bankId; currentBankName = bankName; loadBankTransactions(bankId, bankName); });
        $('.edit-bank-mini').click(function(e) { e.stopPropagation(); var id = $(this).data('id'); var name = $(this).data('name'); var code = $(this).data('code'); var account_number = $(this).data('account_number'); var logo = $(this).data('logo'); var balance = $(this).data('bank-balance'); $.ajax({ url: '<?php echo base_url(); ?>admin/expense/get_bank_initial_balance', type: 'POST', data: { '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>', bank_id: id }, dataType: 'json', success: function(response) { if(response.status == 'success') { $('#bank_id_input').val(id); $('#bank_name').val(name); $('#bank_code').val(code); $('#account_number').val(account_number || ''); $('#initial_balance').val(response.initial_balance || '0.00'); if(logo) { $('#logoImage').attr('src', logo).show(); $('#defaultLogo').hide(); } else { $('#logoImage').hide(); $('#defaultLogo').show(); updateLogoInitials(); } $('#bankModalLabel').html('<i class="fa fa-university mr-2"></i> Modifier la Banque'); $('#bankModal').modal('show'); } } }); });
        $('.delete-bank-mini').click(function(e) { e.stopPropagation(); var bankId = $(this).data('id'); var bankCard = $(this).closest('.bank-card'); var bankName = bankCard.data('bank-name'); var balance = bankCard.data('bank-balance') || 0; var warningMessage = ''; if(parseFloat(balance) !== 0) { warningMessage = '<div class="alert alert-warning mt-2"><i class="fa fa-exclamation-triangle"></i> Attention: Cette banque a un solde de ' + formatNumber(balance) + ' <?php echo $currency_symbol; ?>. La suppression effacera toutes les transactions associées.</div>'; } $('#deleteWarningMessage').html(warningMessage); $('#deleteBankModal').data('bank-id', bankId); $('#deleteBankModal').modal('show'); });
        $('#confirmDelete').off('click').on('click', function() { var bankId = $('#deleteBankModal').data('bank-id'); var deleteBtn = $(this); deleteBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Suppression...'); $.ajax({ url: '<?php echo base_url(); ?>admin/expense/delete_bank', type: 'POST', data: { '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>', bank_id: bankId }, dataType: 'json', success: function(response) { if(response.status == 'success') { showToast('success', response.message); $('#deleteBankModal').modal('hide'); setTimeout(function() { location.reload(); }, 300); } else { showToast('error', response.message); } }, error: function(xhr, status, error) { var errorMsg = 'Erreur lors de la suppression'; try { var jsonResponse = JSON.parse(xhr.responseText); if(jsonResponse && jsonResponse.message) errorMsg = jsonResponse.message; } catch(e) { errorMsg = 'Erreur serveur: ' + error; } showToast('error', errorMsg); }, complete: function() { deleteBtn.prop('disabled', false).html('<i class="fa fa-trash"></i> Supprimer'); } }); });
        $('#bankForm').submit(function(e) { e.preventDefault(); var form = $(this); var submitBtn = form.find('button[type="submit"]'); var modal = $('#bankModal'); submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Enregistrement...'); var formData = new FormData(this); $.ajax({ url: form.attr('action'), type: 'POST', data: formData, processData: false, contentType: false, dataType: 'json', success: function(response) { if(response.status == 'success') { showToast('success', response.message); modal.modal('hide'); form[0].reset(); $('#bank_id_input').val(''); $('#logoImage').attr('src', '').hide(); $('#defaultLogo').show(); updateLogoInitials(); setTimeout(function() { location.reload(); }, 500); } else { showToast('error', response.message || 'Erreur inconnue'); } }, error: function(xhr, status, error) { var errorMsg = 'Erreur serveur'; try { var jsonResponse = JSON.parse(xhr.responseText); if(jsonResponse && jsonResponse.message) errorMsg = jsonResponse.message; } catch(e) { errorMsg = error; } showToast('error', errorMsg); }, complete: function() { submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Enregistrer'); } }); });
        $('#transactionForm').submit(function(e) { e.preventDefault(); var form = $(this); var submitBtn = form.find('button[type="submit"]'); var modal = $('#transactionModal'); submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Enregistrement...'); var formData = new FormData(this); $.ajax({ url: form.attr('action'), type: 'POST', data: formData, processData: false, contentType: false, dataType: 'json', success: function(response) { if(response.status == 'success') { showToast('success', response.message); modal.modal('hide'); form[0].reset(); setTimeout(function() { location.reload(); }, 500); } else { showToast('error', response.message || 'Erreur inconnue'); } }, error: function(xhr, status, error) { var errorMsg = 'Erreur serveur'; try { var jsonResponse = JSON.parse(xhr.responseText); if(jsonResponse && jsonResponse.message) errorMsg = jsonResponse.message; } catch(e) { errorMsg = error; } showToast('error', errorMsg); }, complete: function() { submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Enregistrer'); } }); });
        $(document).on('click', '.delete-transaction', function(e) { e.stopPropagation(); var transactionId = $(this).data('id'); var transactionItem = $(this).closest('tr'); if(!confirm('Êtes-vous sûr de vouloir supprimer cette transaction ?\nCette action est irréversible et mettra à jour le solde de la banque.')) return; var deleteBtn = $(this); deleteBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>'); $.ajax({ url: '<?php echo base_url(); ?>admin/expense/delete_transaction', type: 'POST', data: { '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>', transaction_id: transactionId }, dataType: 'json', success: function(response) { if(response.status == 'success') { showToast('success', response.message); transactionItem.fadeOut(300, function() { $(this).remove(); if($('#viewTransactionsModal tbody tr').length <= 1) loadBankTransactions(currentBankId, currentBankName); setTimeout(function() { location.reload(); }, 500); }); } else { showToast('error', response.message); deleteBtn.prop('disabled', false).html('<i class="fa fa-trash"></i>'); } }, error: function(xhr, status, error) { showToast('error', 'Erreur lors de la suppression: ' + error); deleteBtn.prop('disabled', false).html('<i class="fa fa-trash"></i>'); } }); });
        $(document).on('click', '.edit-transaction', function(e) { e.stopPropagation(); var transactionId = $(this).data('id'); var editBtn = $(this); editBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>'); $.ajax({ url: '<?php echo base_url(); ?>admin/expense/get_transaction', type: 'POST', data: { '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>', transaction_id: transactionId }, dataType: 'json', success: function(response) { if(response.status == 'success') { var data = response.data; $('input[name="transaction_id"]').remove(); $('#transactionForm')[0].reset(); $('#transactionForm').append('<input type="hidden" name="transaction_id" value="' + data.id + '">'); $('#transaction_bank_id').val(data.bank_id); $('#transaction_type').val(data.transaction_type); $('#designation').val(data.designation); $('#date').val(data.date); $('#nom').val(data.nom); $('#name').val(data.name); $('#amount').val(data.amount); $('#reference').val(data.reference); $('#payment_mode').val(data.payment_mode); $('#description').val(data.note); $('#transactionModalLabel').html('<i class="fa fa-exchange mr-2"></i> Modifier Transaction'); $('#viewTransactionsModal').modal('hide'); $('#transactionModal').modal('show'); } else { showToast('error', response.message); } }, error: function() { showToast('error', 'Impossible de charger la transaction'); }, complete: function() { editBtn.prop('disabled', false).html('<i class="fa fa-edit"></i>'); } }); });
        $('#refreshPageBtn').click(function() { location.reload(); });
        $('.bank-logo-large').on('error', function() { $(this).hide(); $(this).next('.bank-logo-default-large').show(); });
        $('#bankModal').on('hidden.bs.modal', function() { $('#bankForm')[0].reset(); $('#bank_id_input').val(''); $('#bankModalLabel').html('<i class="fa fa-university mr-2"></i> Nouvelle Banque'); $('#logoImage').hide(); $('#defaultLogo').show(); updateLogoInitials(); });
        $('#transactionModal').on('hidden.bs.modal', function() { $('input[name="transaction_id"]').remove(); $('#transactionForm')[0].reset(); $('#transactionModalLabel').html('<i class="fa fa-exchange mr-2"></i> Nouvelle Transaction'); });
    }

    // ========================================== //
    // CHARGEMENT DES TRANSACTIONS                //
    // ========================================== //
    function loadBankTransactions(bankId, bankName) { currentBankId = bankId; currentBankName = bankName; $('#bankNameTitle').text(bankName); $('#viewTransactionsModal').modal('show'); $('#bankTransactionsContent').html('<div class="text-center p-4"><div class="spinner-border text-primary" role="status"><span class="sr-only">Chargement...</span></div><p class="mt-2">Chargement des transactions...</p></div>'); $.ajax({ url: '<?php echo base_url(); ?>admin/expense/get_bank_transactions', type: 'POST', data: { '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>', bank_id: bankId }, success: function(response) { $('#bankTransactionsContent').html(response); window.currentTransactionsHTML = response; addDateRangeFilter(); setTimeout(initTransactionTableFilters, 100); }, error: function(xhr, status, error) { var errorMsg = 'Erreur lors du chargement'; try { var jsonResponse = JSON.parse(xhr.responseText); if(jsonResponse && jsonResponse.message) errorMsg = jsonResponse.message; } catch(e) { errorMsg = error; } $('#bankTransactionsContent').html('<div class="alert alert-danger m-3"><i class="fa fa-exclamation-circle"></i> ' + errorMsg + '</div>'); } }); }

    // ========================================== //
    // FILTRE PAR DATE                            //
    // ========================================== //
    function addDateRangeFilter() { var $table = $('#bankTransactionsContent table'); if($table.length === 0) return; if($('#dateRangeFilterBar').length > 0) return; var filterBar = $('<div id="dateRangeFilterBar" class="mb-3 p-3" style="background:#f8f9fa; border-radius:12px; border:1px solid #e2e8f0;"><div class="row align-items-end"><div class="col-md-4 mb-2 mb-md-0"><label class="form-label small text-muted mb-1">Date début</label><input type="date" id="filterDateStart" class="form-control form-control-sm" style="border-radius:8px;"></div><div class="col-md-4 mb-2 mb-md-0"><label class="form-label small text-muted mb-1">Date fin</label><input type="date" id="filterDateEnd" class="form-control form-control-sm" style="border-radius:8px;"></div><div class="col-md-4"><button id="applyDateFilter" class="btn btn-primary-simulator btn-sm mr-2"><i class="fa fa-filter"></i> Filtrer</button><button id="resetDateFilter" class="btn btn-outline-simulator btn-sm"><i class="fa fa-refresh"></i> Réinitialiser</button></div></div></div>'); $table.before(filterBar); $('#applyDateFilter').off('click').on('click', function() { var startDate = $('#filterDateStart').val(); var endDate = $('#filterDateEnd').val(); filterTransactionsByDate(startDate, endDate); }); $('#resetDateFilter').off('click').on('click', function() { $('#filterDateStart').val(''); $('#filterDateEnd').val(''); $('#bankTransactionsContent tbody tr').show(); updateTransactionCountDisplay(); }); }
    function filterTransactionsByDate(startDate, endDate) { var rows = $('#bankTransactionsContent tbody tr'); var visibleCount = 0; var totalCount = rows.length; rows.each(function() { var dateCell = $(this).find('td:first'); if(dateCell.length === 0) return; var dateText = dateCell.text().trim(); var rowDate = parseDate(dateText); if(!rowDate) { $(this).show(); visibleCount++; return; } var show = true; if(startDate && rowDate < new Date(startDate)) show = false; if(endDate && rowDate > new Date(endDate)) show = false; if(show) { $(this).show(); visibleCount++; } else { $(this).hide(); } }); var infoMessage = $('#dateFilterInfo'); if(infoMessage.length === 0 && visibleCount === 0 && totalCount > 0) { $('<div id="dateFilterInfo" class="alert alert-info mt-2"><i class="fa fa-info-circle"></i> Aucune transaction dans cette période.</div>').insertAfter('#dateRangeFilterBar'); } else if(visibleCount > 0) { $('#dateFilterInfo').remove(); } updateTransactionCountDisplay(visibleCount, totalCount); }
    function parseDate(dateStr) { var parts = dateStr.split('/'); if(parts.length === 3) { var day = parseInt(parts[0],10); var month = parseInt(parts[1],10)-1; var year = parseInt(parts[2],10); if(!isNaN(day) && !isNaN(month) && !isNaN(year)) return new Date(year, month, day); } var isoParts = dateStr.split('-'); if(isoParts.length === 3 && isoParts[0].length === 4) { var y = parseInt(isoParts[0],10); var m = parseInt(isoParts[1],10)-1; var d = parseInt(isoParts[2],10); if(!isNaN(y) && !isNaN(m) && !isNaN(d)) return new Date(y, m, d); } return null; }
    function updateTransactionCountDisplay(visible, total) { var countSpan = $('#transactionCount'); if(countSpan.length === 0) { var tableWrapper = $('#bankTransactionsContent .table-responsive, #bankTransactionsContent .table-container'); if(tableWrapper.length === 0) tableWrapper = $('#bankTransactionsContent table').parent(); if(tableWrapper.length) { var badge = $('<div class="text-right mb-2"><span id="transactionCount" class="badge badge-primary p-2">Total: ' + (total || $('#bankTransactionsContent tbody tr').length) + ' transaction(s)</span></div>'); tableWrapper.before(badge); countSpan = $('#transactionCount'); } } if(countSpan.length) { if(visible !== undefined && total !== undefined && visible < total) { countSpan.text('Affichage: ' + visible + ' / ' + total + ' transaction(s)').removeClass('badge-primary').addClass('badge-warning'); } else { var allTotal = $('#bankTransactionsContent tbody tr').length; countSpan.text('Total: ' + allTotal + ' transaction(s)').removeClass('badge-warning').addClass('badge-primary'); } } }
    function initTransactionTableFilters() { setTimeout(function() { const table = document.getElementById('transactionsTable'); const tableBody = document.getElementById('transactionsBody'); const resetBtn = document.getElementById('resetFiltersBtn'); const transactionCount = document.getElementById('transactionCount'); if(!table || !tableBody) return; const filterInputs = table.querySelectorAll('.filter-input'); filterInputs.forEach(input => { input.addEventListener('input', filterTransactionTable); input.addEventListener('change', filterTransactionTable); }); if(resetBtn) { resetBtn.addEventListener('click', function() { filterInputs.forEach(input => { input.value = ''; }); filterTransactionTable(); this.style.display = 'none'; }); } function filterTransactionTable() { const rows = tableBody.querySelectorAll('tr'); let hasActiveFilters = false; let visibleCount = 0; filterInputs.forEach(input => { if(input.value.trim() !== '') hasActiveFilters = true; }); if(resetBtn) resetBtn.style.display = hasActiveFilters ? 'inline-block' : 'none'; rows.forEach(row => { let showRow = true; const cells = row.querySelectorAll('td'); filterInputs.forEach(input => { const columnIndex = parseInt(input.getAttribute('data-column')); const filterValue = input.value.trim().toLowerCase(); if(filterValue !== '' && cells[columnIndex]) { let cellText = ''; if(columnIndex === 4) { const badge = cells[columnIndex].querySelector('.badge'); cellText = badge ? badge.textContent.trim().toLowerCase() : ''; } else if(columnIndex === 5) { cellText = cells[columnIndex].textContent.trim().toLowerCase(); cellText = cellText.replace('fcfa', '').trim(); } else { cellText = cells[columnIndex].textContent.trim().toLowerCase(); } if(input.tagName === 'SELECT') { if(filterValue !== '' && cellText !== filterValue) showRow = false; } else { if(filterValue !== '' && !cellText.includes(filterValue)) showRow = false; } } }); if(showRow) { row.style.display = ''; visibleCount++; } else { row.style.display = 'none'; } }); if(transactionCount) { const totalRows = rows.length; if(visibleCount < totalRows) { transactionCount.textContent = 'Total: ' + visibleCount + ' transaction(s) sur ' + totalRows; transactionCount.className = 'badge badge-warning'; } else { transactionCount.textContent = 'Total: ' + visibleCount + ' transaction(s)'; transactionCount.className = 'badge badge-primary'; } } } if(transactionCount && tableBody) { const totalRows = tableBody.querySelectorAll('tr').length; transactionCount.textContent = 'Total: ' + totalRows + ' transaction(s)'; } }, 300); }

    // ========================================== //
    // EXPORTS                                    //
    // ========================================== //
    function exportAllBanksPDF() { var banksData = []; $('.bank-card').each(function() { banksData.push({ name: $(this).data('bank-name'), code: $(this).data('bank-code'), balance: $(this).data('bank-balance'), initial_balance: $(this).data('initial-balance'), account: $(this).data('bank-account') || 'Non défini' }); }); var totalBalance = banksData.reduce((sum, bank) => sum + parseFloat(bank.balance), 0); var totalInitialBalance = banksData.reduce((sum, bank) => sum + parseFloat(bank.initial_balance), 0); var printWindow = window.open('', '_blank'); var content = `<!DOCTYPE html><html><head><title>Liste des Banques - Export PDF</title><style>body{font-family:'Inter',sans-serif;margin:40px;background:#f8fafc;} .pdf-container{max-width:1100px;margin:0 auto;background:white;padding:40px;border-radius:24px;box-shadow:0 20px 40px rgba(0,0,0,0.05);} h1{color:#0A2540;text-align:center;font-size:28px;} table{width:100%;border-collapse:collapse;margin-top:20px;} th{background:#0A2540;color:white;padding:12px;} td{padding:10px;border-bottom:1px solid #e2e8f0;} .total{margin-top:30px;padding:20px;background:#f8f9fa;border-radius:16px;text-align:right;}</style></head><body><div class="pdf-container"><h1>Liste des Banques</h1><p style="text-align:center;color:#475569;">Exporté le ${new Date().toLocaleDateString('fr-FR', { weekday:'long', year:'numeric', month:'long', day:'numeric', hour:'2-digit', minute:'2-digit' })}</p> <table><thead><tr><th>Nom</th><th>Code</th><th>Compte</th><th>Solde initial</th><th>Solde actuel</th></tr></thead><tbody>${banksData.map(bank => `<tr><td>${bank.name}</td><td>${bank.code}</td><td>${bank.account}</td><td>${formatNumber(bank.initial_balance)} <?php echo $currency_symbol; ?></td><td>${formatNumber(bank.balance)} <?php echo $currency_symbol; ?></td></tr>`).join('')}</tbody></table><div class="total"><p><strong>Total banques:</strong> ${banksData.length}</p><p><strong>Total soldes initiaux:</strong> ${formatNumber(totalInitialBalance)} <?php echo $currency_symbol; ?></p><p><strong>Total soldes actuels:</strong> ${formatNumber(totalBalance)} <?php echo $currency_symbol; ?></p></div></div></body></html>`; printWindow.document.write(content); printWindow.document.close(); setTimeout(function() { printWindow.print(); setTimeout(function() { printWindow.close(); }, 1000); }, 1000); }
    function exportAllBanksExcel() { var csvContent = "data:text/csv;charset=utf-8,"; csvContent += "Nom de la Banque,Code,Numéro de Compte,Solde initial (<?php echo $currency_symbol; ?>),Solde actuel (<?php echo $currency_symbol; ?>),Date d'export\r\n"; $('.bank-card').each(function() { var name = $(this).data('bank-name'); var code = $(this).data('bank-code'); var account = $(this).data('bank-account') || 'Non défini'; var initialBalance = $(this).data('initial-balance'); var currentBalance = $(this).data('bank-balance'); csvContent += `"${name}","${code}","${account}","${initialBalance}","${currentBalance}","${new Date().toLocaleDateString('fr-FR')}"\r\n`; }); var totalInitialBalance = 0; var totalCurrentBalance = 0; $('.bank-card').each(function() { totalInitialBalance += parseFloat($(this).data('initial-balance')); totalCurrentBalance += parseFloat($(this).data('bank-balance')); }); csvContent += `\r\nRésumé,,,,,\r\n"Total des banques","${$('.bank-card').length}",,,,\r\n"Total soldes initiaux","${totalInitialBalance.toFixed(2)}",,,,\r\n"Total soldes actuels","${totalCurrentBalance.toFixed(2)}",,,,\r\n"Date d'export","${new Date().toLocaleDateString('fr-FR')}",,,,\r\n`; var encodedUri = encodeURI(csvContent); var link = document.createElement("a"); link.setAttribute("href", encodedUri); link.setAttribute("download", "banques_export_" + new Date().toISOString().split('T')[0] + ".csv"); document.body.appendChild(link); link.click(); document.body.removeChild(link); showToast('success', 'Export Excel terminé avec succès'); }

    // ========================================== //
    // PRINT                                      //
    // ========================================== //
    function printBankList() { var banksData = []; $('.bank-card').each(function() { banksData.push({ name: $(this).data('bank-name'), code: $(this).data('bank-code'), balance: $(this).data('bank-balance'), initial_balance: $(this).data('initial-balance'), account: $(this).data('bank-account') || 'Non défini' }); }); var totalBalance = banksData.reduce((sum, bank) => sum + parseFloat(bank.balance), 0); var totalInitialBalance = banksData.reduce((sum, bank) => sum + parseFloat(bank.initial_balance), 0); var printContent = `<html><head><title>Liste des Banques</title><style>body{font-family:'Inter',sans-serif;margin:40px;} h1{text-align:center;color:#0A2540;} table{width:100%;border-collapse:collapse;} th,td{border:1px solid #ccc;padding:8px;text-align:left;} th{background:#0A2540;color:white;}</style></head><body><h1>Liste des Banques</h1><p style="text-align:center;">Généré le ${new Date().toLocaleDateString('fr-FR')}</p><table><thead><tr><th>Nom</th><th>Code</th><th>Compte</th><th>Solde initial</th><th>Solde actuel</th></tr></thead><tbody>${banksData.map(bank => `<tr><td>${bank.name}</td><td>${bank.code}</td><td>${bank.account}</td><td>${formatNumber(bank.initial_balance)} <?php echo $currency_symbol; ?></td><td>${formatNumber(bank.balance)} <?php echo $currency_symbol; ?></td></tr>`).join('')}</tbody></table><div style="margin-top:20px;"><p>Total banques: ${banksData.length}</p><p>Total soldes initiaux: ${formatNumber(totalInitialBalance)} <?php echo $currency_symbol; ?></p><p>Total soldes actuels: ${formatNumber(totalBalance)} <?php echo $currency_symbol; ?></p></div></body></html>`; var printWindow = window.open('', '_blank'); printWindow.document.write(printContent); printWindow.document.close(); setTimeout(function() { printWindow.print(); setTimeout(function() { printWindow.close(); }, 500); }, 500); }

    function exportBankTransactionsPDF() {
        if(!currentBankId) { showToast('error', 'Aucune banque sélectionnée'); return; }
        var rows = $('#viewTransactionsModal table tbody tr:visible');
        var transactionsData = [];
        var totalAmount = 0;
        var creditTotal = 0;
        var debitTotal = 0;

        rows.each(function() {
            var cells = $(this).find('td');
            if(cells.length >= 9) {
                var date = $(cells[0]).text().trim();
                var nom = $(cells[1]).text().trim();
                var libelle = $(cells[2]).text().trim();
                var type = $(cells[3]).text().trim();
                var designationElem = $(cells[4]);
                var designation = designationElem.find('.badge').text().trim() || designationElem.text().trim();
                var entreeText = $(cells[5]).text().trim();
                var sortieText = $(cells[6]).text().trim();
                var reference = $(cells[7]).text().trim();

                var isCredit = (designation === 'Crédit');
                var amount = 0;
                var entreeValue = '';
                var sortieValue = '';

                if (isCredit && entreeText !== '-' && entreeText !== '') {
                    entreeValue = entreeText;
                    var match = entreeText.match(/([\d\s,]+)/);
                    if (match) {
                        amount = parseFloat(match[1].replace(/\s/g, '').replace(',', '.'));
                        creditTotal += amount;
                    }
                } else if (!isCredit && sortieText !== '-' && sortieText !== '') {
                    sortieValue = sortieText;
                    var match = sortieText.match(/([\d\s,]+)/);
                    if (match) {
                        amount = parseFloat(match[1].replace(/\s/g, '').replace(',', '.'));
                        debitTotal += amount;
                        amount = -amount;
                    }
                }
                totalAmount += amount;

                transactionsData.push({
                    date: date,
                    nom: nom,
                    libelle: libelle,
                    type: type,
                    designation: designation,
                    entree: entreeValue,
                    sortie: sortieValue,
                    reference: reference
                });
            }
        });

        var initialBalance = 0;
        var bankCard = $('.bank-card[data-bank-id="'+currentBankId+'"]');
        if(bankCard.length) { initialBalance = parseFloat(bankCard.data('initial-balance')) || 0; }

        var printWindow = window.open('', '_blank');
        var content = `<!DOCTYPE html><html><head><title>Transactions - ${currentBankName}</title><style>
        body{font-family:'Inter',sans-serif;margin:40px;}
        .pdf-container{max-width:1200px;margin:0 auto;background:white;padding:40px;border-radius:24px;}
        h1{color:#0A2540;} table{width:100%;border-collapse:collapse;margin-top:20px;}
        th{background:#0A2540;color:white;padding:12px;} td{padding:10px;border-bottom:1px solid #e2e8f0;}
        .amount-credit{color:#10B981;font-weight:600;} .amount-debit{color:#EF4444;font-weight:600;}
    </style></head><body><div class="pdf-container">
    <h1>Transactions Bancaires</h1><h2>Banque : ${currentBankName}</h2>
    <p>Exporté le ${new Date().toLocaleDateString('fr-FR')}</p>
    <p><strong>Solde initial:</strong> ${formatNumber(initialBalance)} <?php echo $currency_symbol; ?></p>
    <table><thead><tr><th>Date</th><th>Nom</th><th>Libellé</th><th>Type</th><th>Désignation</th><th>Entrée</th><th>Sortie</th></tr></thead><tbody>
    ${transactionsData.map(trans => `
        <tr>
            <td>${trans.date}</td>
            <td>${trans.nom}</td>
            <td>${trans.libelle}</td>
            <td>${trans.type}</td>
            <td>${trans.designation}</td>
            <td class="amount-credit">${trans.entree}</td>
            <td class="amount-debit">${trans.sortie}</td>
        </tr>
    `).join('')}
    </tbody></table>
    <div style="margin-top:30px;"><p>Total Crédits (Entrées): ${formatNumber(creditTotal)} <?php echo $currency_symbol; ?></p>
    <p>Total Débits (Sorties): ${formatNumber(debitTotal)} <?php echo $currency_symbol; ?></p>
    <p>Solde net: ${formatNumber(totalAmount)} <?php echo $currency_symbol; ?></p></div>
    </div></body></html>`;
        printWindow.document.write(content);
        printWindow.document.close();
        setTimeout(function() { printWindow.print(); setTimeout(function() { printWindow.close(); }, 1000); }, 1000);
    }
    function exportBankTransactionsExcel() {
        if(!currentBankId) { showToast('error', 'Aucune banque sélectionnée'); return; }
        var rows = $('#viewTransactionsModal table tbody tr:visible');
        var csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "Date,Nom,Libellé,Type,Désignation,Entrée,Sortie,Référence,Banque,Date d'export\r\n";
        var transactionsCount = 0, creditTotal = 0, debitTotal = 0, netTotal = 0;
        rows.each(function() {
            var cells = $(this).find('td');
            if(cells.length >= 8) {
                var date = $(cells[0]).text() || '';
                var nom = $(cells[1]).text() || '';
                var libelle = $(cells[2]).text() || '';
                var type = $(cells[3]).text() || '';
                var designation = $(cells[4]).find('.badge').text() || $(cells[4]).text() || '';
                var entree = $(cells[5]).text() || '';
                var sortie = $(cells[6]).text() || '';
                var reference = $(cells[7]).text() || '';
                var isCredit = (designation === 'Crédit') || (entree !== '-' && entree !== '');
                var amount = 0;
                if(isCredit && entree !== '-') {
                    var match = entree.match(/[\d\s,]+/);
                    if(match) amount = parseFloat(match[0].replace(/\s/g, '').replace(',', '.'));
                    creditTotal += amount;
                } else if(!isCredit && sortie !== '-') {
                    var match = sortie.match(/[\d\s,]+/);
                    if(match) amount = parseFloat(match[0].replace(/\s/g, '').replace(',', '.'));
                    debitTotal += amount;
                    amount = -amount;
                }
                netTotal += amount;
                csvContent += `"${date}","${nom}","${libelle}","${type}","${designation}","${entree}","${sortie}","${reference}","${currentBankName}","${new Date().toLocaleDateString('fr-FR')}"\r\n`;
                transactionsCount++;
            }
        });
        var initialBalance = 0;
        var bankCard = $('.bank-card[data-bank-id="'+currentBankId+'"]');
        if(bankCard.length) { initialBalance = parseFloat(bankCard.data('initial-balance')) || 0; }
        csvContent += `\r\nRÉSUMÉ,,,,,,,,\r\n"Solde initial de la banque","${initialBalance.toFixed(2)} <?php echo $currency_symbol; ?>",,,,,,,\r\n"Nombre total de transactions (filtrées)","${transactionsCount}",,,,,,,\r\n"Total Crédits (Entrées)","${creditTotal.toFixed(2)} <?php echo $currency_symbol; ?>",,,,,,,\r\n"Total Débits (Sorties)","${debitTotal.toFixed(2)} <?php echo $currency_symbol; ?>",,,,,,,\r\n"Solde Net des mouvements","${netTotal.toFixed(2)} <?php echo $currency_symbol; ?>",,,,,,,\r\n"Date d'export","${new Date().toLocaleDateString('fr-FR')}",,,,,,,\r\n`;
        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "transactions_" + currentBankName.replace(/[^a-z0-9]/gi, '_') + "_" + new Date().toISOString().split('T')[0] + ".csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        showToast('success', 'Export Excel des transactions filtrées terminé');
    }
</script>