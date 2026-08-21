<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<style>
    /* ========== DESIGN LIGHT CHIC - MODERNE ET ÉLÉGANT ========== */

    /* Variables de style */
    :root {
        --chic-primary: #2c3e50;
        --chic-secondary: #5d6d7e;
        --chic-accent: #3498db;
        --chic-light: #f8f9fa;
        --chic-white: #ffffff;
        --chic-border: #e9ecef;
        --chic-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        --chic-shadow-hover: 0 15px 40px rgba(0, 0, 0, 0.08);
        --chic-radius: 20px;
        --chic-radius-sm: 12px;
    }

    /* Container principal */
    .content-wrapper {
        background: linear-gradient(135deg, #f5f7fa 0%, #f0f2f5 100%);
        min-height: 100vh;
        padding: 30px;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    /* Carte principale */
    .loan-simulator-card {
        background: var(--chic-white);
        border-radius: var(--chic-radius);
        box-shadow: var(--chic-shadow);
        overflow: hidden;
        margin-bottom: 30px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
    }

    .loan-simulator-card:hover {
        box-shadow: var(--chic-shadow-hover);
    }

    /* Header raffiné */
    .simulator-header {
        background: linear-gradient(135deg, #ffffff 0%, #fefefe 100%);
        padding: 40px 30px 30px;
        text-align: center;
        border-bottom: 1px solid var(--chic-border);
        position: relative;
    }

    .simulator-header::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--chic-primary), var(--chic-accent), var(--chic-primary));
    }

    .simulator-header h1 {
        font-size: 32px;
        font-weight: 600;
        margin-bottom: 12px;
        background: linear-gradient(135deg, var(--chic-primary), var(--chic-secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: -0.5px;
    }

    .simulator-header .subtitle {
        font-size: 16px;
        color: #6c757d;
        margin-bottom: 25px;
        font-weight: 400;
    }

    /* Corps du simulateur */
    .simulator-body {
        padding: 40px;
    }

    /* Section d'export stylisée */
    .export-section-simulator {
        background: linear-gradient(135deg, white, white);
        border-radius: var(--chic-radius-sm);
        padding: 30px;
        margin-bottom: 35px;
        position: relative;
        overflow: hidden;
    }

    .export-section-simulator::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        pointer-events: none;
    }

    .export-title {
        font-size: 20px;
        font-weight: bold;
        color: black;
        margin-bottom: 20px;
        letter-spacing: 0.5px;
    }

    .stats-badge {
        display: inline-flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        padding: 8px 20px;
        border-radius: 40px;
        font-size: 14px;
        color: black;
        margin-right: 12px;
        margin-bottom: 10px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }

    .stats-badge:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: translateY(-2px);
    }

    .stats-badge i {
        margin-right: 8px;
        font-size: 14px;
    }

    .stats-badge .number {
        font-weight: 700;
        margin-left: 6px;
        font-size: 16px;
    }

    /* Boutons élégants */
    .btn-simulator {
        padding: 12px 28px;
        border-radius: 40px;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        letter-spacing: 0.3px;
    }

    .btn-primary-simulator {
        background: linear-gradient(135deg, var(--chic-primary), #34495e);
        color: white;
        box-shadow: 0 2px 10px rgba(44, 62, 80, 0.2);
    }

    .btn-primary-simulator:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(44, 62, 80, 0.3);
        background: linear-gradient(135deg, #34495e, var(--chic-primary));
    }

    .btn-outline-simulator {
        background: transparent;
        border: 1.5px solid var(--chic-border);
        color: var(--chic-secondary);
    }

    .btn-outline-simulator:hover {
        background: var(--chic-light);
        border-color: var(--chic-primary);
        color: var(--chic-primary);
        transform: translateY(-2px);
    }

    /* Barre de recherche */
    .search-container-simulator {
        padding-top: 18px;
        position: relative;
        display: inline-block;
    }

    .search-input-simulator {
        width: 280px;
        padding: 12px 20px 12px 45px;
        border: 1.5px solid var(--chic-border);
        border-radius: 40px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: white;
        color: var(--chic-primary);
        font-family: inherit;
    }

    .search-input-simulator:focus {
        border-color: var(--chic-accent);
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        outline: none;
        width: 320px;
    }

    .search-icon-simulator {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #9aa8b9;
        font-size: 16px;
    }

    /* Grille des banques */
    .banks-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 25px;
        margin: 30px 0;
    }

    /* Carte banque chic */
    .bank-card {
        background: white;
        border-radius: 20px;
        padding: 25px;
        text-align: center;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid var(--chic-border);
        position: relative;
        overflow: hidden;
    }

    .bank-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--chic-primary), var(--chic-accent));
        transform: scaleX(0);
        transition: transform 0.4s ease;
    }

    .bank-card:hover::before {
        transform: scaleX(1);
    }

    .bank-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--chic-shadow-hover);
        border-color: transparent;
    }

    .bank-card.selected {
        border: 2px solid var(--chic-accent);
        background: linear-gradient(135deg, #ffffff, #f8f9ff);
        box-shadow: 0 15px 35px rgba(52, 152, 219, 0.15);
    }

    /* Logo */
    .bank-logo-large {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        object-fit: cover;
        background: white;
        padding: 12px;
        margin: 0 auto 18px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        border: 2px solid var(--chic-border);
        transition: all 0.3s ease;
    }

    .bank-card:hover .bank-logo-large {
        transform: scale(1.05);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.12);
    }

    .bank-logo-default-large {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--chic-light), #e9ecef);
        color: var(--chic-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 32px;
        margin: 0 auto 18px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
        border: 2px solid var(--chic-border);
        transition: all 0.3s ease;
    }

    /* Informations banque */
    .bank-name {
        font-size: 18px;
        font-weight: 700;
        color: var(--chic-primary);
        margin-bottom: 8px;
        letter-spacing: -0.2px;
    }

    .bank-code {
        font-size: 12px;
        color: #7f8c8d;
        background: var(--chic-light);
        padding: 4px 12px;
        border-radius: 20px;
        display: inline-block;
        font-weight: 500;
        letter-spacing: 0.5px;
    }

    .bank-info {
        text-align: left;
        margin-top: 15px;
        font-size: 13px;
        color: #5d6d7e;
        padding-top: 12px;
        border-top: 1px solid var(--chic-border);
    }

    .bank-info i {
        margin-right: 8px;
        width: 20px;
        color: var(--chic-accent);
    }

    /* Actions miniatures */
    .bank-actions-mini {
        position: absolute;
        top: 15px;
        right: 15px;
        display: flex;
        gap: 8px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .bank-card:hover .bank-actions-mini {
        opacity: 1;
    }

    .btn-action-mini {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        border: none;
        background: white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .btn-action-mini:hover {
        transform: scale(1.1);
    }

    .btn-action-mini.btn-warning {
        background: #f39c12;
        color: white;
    }

    .btn-action-mini.btn-danger {
        background: #e74c3c;
        color: white;
    }

    /* Boutons d'action sur carte */
    .bank-card .d-flex.gap-2 {
        gap: 10px;
        margin-top: 18px;
    }

    .bank-card .btn-sm {
        padding: 8px 16px;
        font-size: 12px;
        border-radius: 25px;
    }

    /* Modal stylisé */
    .modal-simulator .modal-content {
        border-radius: 24px;
        overflow: hidden;
        border: none;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    }

    .modal-simulator .modal-header {
        background: linear-gradient(135deg, var(--chic-primary), #34495e);
        color: white;
        border: none;
        padding: 25px 30px;
    }

    .modal-simulator .modal-header .close {
        color: white;
        opacity: 0.8;
        text-shadow: none;
    }

    .modal-simulator .modal-header .close:hover {
        opacity: 1;
    }

    .modal-simulator .modal-body {
        padding: 30px;
        background: white;
    }

    /* Formulaires élégants */
    .form-group-simulator {
        margin-bottom: 25px;
    }

    .form-group-simulator label {
        font-weight: 600;
        color: var(--chic-secondary);
        margin-bottom: 8px;
        display: block;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-control-simulator {
        height: 48px;
        border: 1.5px solid var(--chic-border);
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: white;
        color: var(--chic-primary);
        font-family: inherit;
    }

    .form-control-simulator:focus {
        border-color: var(--chic-accent);
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        outline: none;
    }

    /* Tableau des transactions */
    .table-container {
        overflow-x: auto;
        border-radius: var(--chic-radius-sm);
        background: white;
    }

    .table-operations {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 13px;
    }

    .table-operations thead th {
        background: linear-gradient(135deg, var(--chic-primary), #34495e);
        color: white;
        padding: 14px 16px;
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: none;
    }

    .table-operations thead th:first-child {
        border-top-left-radius: 12px;
    }

    .table-operations thead th:last-child {
        border-top-right-radius: 12px;
    }

    .table-operations tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid var(--chic-border);
    }

    .table-operations tbody tr:hover {
        background: var(--chic-light);
        transform: translateX(2px);
    }

    .table-operations td {
        padding: 14px 16px;
        vertical-align: middle;
    }

    /* Badges de transaction */
    .badge-credit {
        background: linear-gradient(135deg, #27ae60, #2ecc71);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
    }

    .badge-debit {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        display: inline-block;
    }

    .amount-credit {
        color: black;
        font-weight: 700;
        font-size: 14px;
    }

    .amount-debit {
        color: #e74c3c;
        font-weight: 700;
        font-size: 14px;
    }

    /* Disclaimer stylisé */
    .disclaimer {
        background: linear-gradient(135deg, #fef9e7, #fff8e7);
        border-radius: 16px;
        padding: 20px 25px;
        margin-top: 40px;
        border-left: 4px solid #f39c12;
    }

    .disclaimer h6 {
        color: #e67e22;
        font-weight: 700;
        margin-bottom: 8px;
        font-size: 14px;
    }

    .disclaimer p {
        color: #7f8c8d;
        font-size: 13px;
        margin: 0;
        line-height: 1.6;
    }

    /* Animation de chargement */
    .spinner-border {
        border-width: 3px;
        border-color: var(--chic-primary);
        border-right-color: transparent;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .content-wrapper {
            padding: 15px;
        }

        .simulator-body {
            padding: 20px;
        }

        .banks-grid {
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 15px;
        }

        .search-input-simulator {
            width: 100%;
        }

        .search-input-simulator:focus {
            width: 100%;
        }

        .export-section-simulator .d-flex {
            flex-direction: column;
            align-items: stretch;
        }

        .stats-badge {
            justify-content: center;
        }

        .btn-simulator {
            width: 100%;
            justify-content: center;
        }

        .table-operations {
            font-size: 11px;
        }

        .table-operations td,
        .table-operations th {
            padding: 10px 8px;
        }
    }
</style>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <div class="container-fluid" style="padding-left: 5px;padding-right: 5px; margin-left: auto;margin-right: auto">
        <!-- Simulateur Card -->
        <div class="loan-simulator-card">
            <!-- Corps du simulateur -->
            <div class="simulator-body">

                <!-- Section d'exportation -->
                <div class="export-section-simulator">
                    <div class="export-title">
                        <i class="fa fa-university mr-2"></i> Gestion des Banques
                    </div>
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <div>
                            <div class="stats-badge">
                                <i class="fa fa-building"></i>
                                Établissements: <span class="number" id="totalBanks"><?php echo count($banks); ?></span>
                            </div>
                            <div class="stats-badge">
                                <i class="fa fa-credit-card"></i>
                                Encours total: <span class="number">
                                    <?php
                                    $total_balance = 0;
                                    foreach ($banks as $bank) {
                                        $total_balance += isset($bank->balance) ? $bank->balance : 0;
                                    }
                                    echo number_format($total_balance, 2, ',', ' ') . ' ' . $currency_symbol;
                                    ?>
                                </span>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 mt-2 mt-sm-0">
                            <button class="btn btn-outline-simulator" onclick="exportAllBanksPDF()">
                                <i class="fa fa-file-pdf-o"></i> PDF
                            </button>
                            <button class="btn btn-outline-simulator" onclick="exportAllBanksExcel()">
                                <i class="fa fa-file-excel-o"></i> Excel
                            </button>
                            <button class="btn btn-outline-simulator" onclick="printBankList()">
                                <i class="fa fa-print"></i> Imprimer
                            </button>
                            <button class="btn btn-primary-simulator" data-toggle="modal" data-target="#bankModal">
                                <i class="fa fa-plus"></i> Nouvelle Banque
                            </button>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="search-container-simulator" style="width: 100%;">
                            <i class="fa fa-search search-icon-simulator"></i>
                            <input type="text" class="search-input-simulator" id="searchBanks"
                                   placeholder="Rechercher une banque... (nom, code, compte)"
                                   style="width: 100%;">
                        </div>
                    </div>
                </div>

                <!-- Grille des banques -->
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

                                <!-- Actions miniatures -->
                                <div class="bank-actions-mini">
                                    <button class="btn-action-mini edit-bank-mini"
                                            data-id="<?php echo $bank->id; ?>"
                                            data-name="<?php echo htmlspecialchars($bank->name); ?>"
                                            data-code="<?php echo htmlspecialchars($bank->code); ?>"
                                            data-account_number="<?php echo htmlspecialchars($bank->account_number ?? ''); ?>"
                                            data-logo="<?php echo $logo_url; ?>"
                                            data-initial_balance="<?php echo $initial_balance; ?>"
                                            title="Modifier">
                                        <i class="fa fa-pencil"></i>
                                    </button>
                                    <button class="btn-action-mini delete-bank-mini"
                                            data-id="<?php echo $bank->id; ?>"
                                            title="Supprimer">
                                        <i class="fa fa-trash-o"></i>
                                    </button>
                                </div>

                                <!-- Logo -->
                                <?php if ($logo_url): ?>
                                    <img src="<?php echo $logo_url; ?>"
                                         alt="<?php echo htmlspecialchars($bank->name); ?>"
                                         class="bank-logo-large"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="bank-logo-default-large" style="display: none;">
                                        <?php echo $bank_initial; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="bank-logo-default-large">
                                        <?php echo $bank_initial; ?>
                                    </div>
                                <?php endif; ?>

                                <!-- Nom et code -->
                                <div class="bank-name"><?php echo htmlspecialchars($bank->name); ?></div>
                                <div class="bank-code"><?php echo htmlspecialchars($bank->code); ?></div>

                                <!-- Informations -->
                                <div class="bank-info">
                                    <?php if (!empty($bank->account_number)): ?>
                                        <div><i class="fa fa-credit-card"></i> <?php echo htmlspecialchars($bank->account_number); ?></div>
                                    <?php endif; ?>
                                    <div>
                                        <i class="fa fa-<?php echo $balance >= 0 ? 'arrow-up' : 'arrow-down'; ?>"></i>
                                        <span class="<?php echo $balance >= 0 ? 'amount-credit' : 'amount-debit'; ?>">
                                            <?php echo number_format($balance, 2, ',', ' ') . ' ' . $currency_symbol; ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Boutons d'action -->
                                <div class="d-flex justify-content-center gap-2 mt-3">
                                    <button class="btn btn-outline-simulator btn-sm add-transaction-btn"
                                            data-bank-id="<?php echo $bank->id; ?>"
                                            data-bank-name="<?php echo htmlspecialchars($bank->name); ?>"
                                            title="Ajouter une transaction">
                                        <i class="fa fa-plus"></i> Transaction
                                    </button>
                                    <button class="btn btn-primary-simulator btn-sm view-transactions-btn"
                                            data-bank-id="<?php echo $bank->id; ?>"
                                            data-bank-name="<?php echo htmlspecialchars($bank->name); ?>"
                                            title="Voir les transactions">
                                        <i class="fa fa-eye"></i> Détails
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <div class="bank-logo-default-large mx-auto mb-4" style="width: 100px; height: 100px; font-size: 48px;">
                            <i class="fa fa-university"></i>
                        </div>
                        <h3 class="mb-3" style="color: var(--chic-primary);">Aucune banque enregistrée</h3>
                        <p class="text-muted mb-4">Commencez par ajouter votre première banque</p>
                        <button class="btn btn-primary-simulator" data-toggle="modal" data-target="#bankModal">
                            <i class="fa fa-plus mr-2"></i> Créer une banque
                        </button>
                    </div>
                <?php endif; ?>

                <!-- Actions principales -->
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                    <button type="button" class="btn btn-outline-simulator" id="refreshPageBtn">
                        <i class="fa fa-refresh mr-2"></i> Actualiser
                    </button>
                </div>

                <!-- Avertissement -->
                <div class="disclaimer">
                    <h6><i class="fa fa-info-circle mr-2"></i> Information importante</h6>
                    <p>Les données affichées sont basées sur les informations enregistrées. Pour des données à jour, veuillez consulter vos relevés bancaires officiels.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour créer/modifier une banque -->
<div class="modal fade modal-simulator" id="bankModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="bankModalLabel"><i class="fa fa-university mr-2"></i> Nouvelle Banque</h4>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form id="bankForm" action="<?php echo base_url() ?>admin/expense/save_bank" method="post" enctype="multipart/form-data">
                <div class="modal-body">
                    <?php echo $this->customlib->getCSRF(); ?>
                    <input type="hidden" id="bank_id_input" name="bank_id" value="">

                    <div class="row">
                        <div class="col-md-4 text-center">
                            <!-- Logo preview -->
                            <div id="logoPreview" style="margin-bottom: 20px;">
                                <div class="bank-logo-default-large" id="defaultLogo" style="margin: 0 auto;">
                                    <span id="logoInitials">BN</span>
                                </div>
                                <img id="logoImage" src="" alt="" class="bank-logo-large" style="display: none; margin: 0 auto;">
                            </div>

                            <label class="btn btn-outline-simulator upload-logo-btn" style="cursor: pointer;">
                                <i class="fa fa-camera"></i> Choisir logo
                                <input type="file" name="logo" id="logoInput" accept="image/*" style="display: none;">
                            </label>
                            <small class="text-muted d-block mt-2">JPG, PNG, SVG (max 2MB)</small>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group-simulator">
                                <label>Nom de la banque *</label>
                                <input type="text" id="bank_name" name="bank_name" class="form-control form-control-simulator" required
                                       oninput="updateLogoInitials()" placeholder="Ex: BIN, NSA Banque, Ecobank...">
                            </div>

                            <div class="form-group-simulator">
                                <label>Code banque *</label>
                                <input type="text" id="bank_code" name="bank_code" class="form-control form-control-simulator" required
                                       placeholder="Ex: BIC, SWIFT">
                            </div>

                            <div class="form-group-simulator">
                                <label>Numéro de compte</label>
                                <input type="text" id="account_number" name="account_number" class="form-control form-control-simulator"
                                       placeholder="Numéro de compte bancaire">
                            </div>

                            <div class="form-group-simulator">
                                <label>Solde initial</label>
                                <div class="input-group">
                                    <span class="input-group-addon" style="background: var(--chic-light); border: 1.5px solid var(--chic-border); border-right: none; border-radius: 12px 0 0 12px; padding: 0 15px; display: flex; align-items: center;"><?php echo $currency_symbol; ?></span>
                                    <input type="number" id="initial_balance" name="initial_balance" class="form-control form-control-simulator"
                                           step="0.01" value="0.00" min="0" placeholder="0.00" style="border-radius: 0 12px 12px 0;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary-simulator">
                        <i class="fa fa-save"></i> Enregistrer
                    </button>
                    <button type="button" class="btn btn-outline-simulator" data-dismiss="modal">
                        <i class="fa fa-times"></i> Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour les transactions bancaires -->
<div class="modal fade modal-simulator" id="transactionModal" tabindex="-1" role="dialog" aria-labelledby="transactionModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="transactionForm" action="<?php echo base_url() ?>admin/expense/bank" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h4 class="modal-title" id="transactionModalLabel"><i class="fa fa-exchange mr-2"></i> Nouvelle Transaction</h4>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <?php echo $this->customlib->getCSRF(); ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-simulator">
                                <label>Banque *</label>
                                <select id="transaction_bank_id" name="bank_id" class="form-control form-control-simulator" required>
                                    <option value="">Sélectionner</option>
                                    <?php if (!empty($banks) && is_array($banks)): ?>
                                        <?php foreach ($banks as $bank): ?>
                                            <option value="<?php echo $bank->id; ?>">
                                                <?php echo htmlspecialchars($bank->name . ' (' . $bank->code . ')'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="">Aucune banque disponible</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-simulator">
                                <label>Type de transaction *</label>
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
                            <div class="form-group-simulator">
                                <label>Opération *</label>
                                <select id="designation" name="designation" class="form-control form-control-simulator" required>
                                    <option value="">Sélectionner</option>
                                    <option value="Débit">Sortie</option>
                                    <option value="Crédit">Entrée</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-simulator">
                                <label>Date *</label>
                                <input id="date" name="date" type="text" class="form-control form-control-simulator date"
                                       value="<?php echo set_value('date', date($this->customlib->getSchoolDateFormat())); ?>" readonly required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-simulator">
                                <label>Libellé *</label>
                                <input id="name" name="name" type="text" class="form-control form-control-simulator" required
                                       placeholder="Libellé de l'opération">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-simulator">
                                <label>Nom</label>
                                <input id="nom" name="nom" type="text" class="form-control form-control-simulator"
                                       placeholder="Nom du bénéficiaire ou émetteur">
                                <small class="text-muted">Nom de la personne ou entreprise concernée</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-simulator">
                                <label>Montant *</label>
                                <div class="input-group">
                                    <span class="input-group-addon" style="background: var(--chic-light); border: 1.5px solid var(--chic-border); border-right: none; border-radius: 12px 0 0 12px; padding: 0 15px; display: flex; align-items: center;"><?php echo $currency_symbol; ?></span>
                                    <input id="amount" name="amount" type="number" step="0.01" class="form-control form-control-simulator" required style="border-radius: 0 12px 12px 0;">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-simulator">
                                <label>Numéro de référence</label>
                                <input id="reference" name="reference" type="text" class="form-control form-control-simulator"
                                       placeholder="N° chèque, virement, etc.">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-simulator">
                                <label>Mode de paiement</label>
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
                            <div class="form-group-simulator">
                                <label>Nom de la banque</label>
                                <input id="category" name="category" type="text" class="form-control form-control-simulator"
                                       placeholder="Banque">
                            </div>
                        </div>
                    </div>

                    <div class="form-group-simulator">
                        <label>Description</label>
                        <textarea id="description" name="description" class="form-control form-control-simulator" rows="3"
                                  placeholder="Détails de la transaction"></textarea>
                    </div>

                    <div class="form-group-simulator" hidden>
                        <label>Pièce jointe</label>
                        <input id="documents" name="documents" type="file" class="form-control form-control-simulator">
                        <small class="text-muted">Extrait bancaire, chèque, etc. (PDF, JPG, PNG)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary-simulator">
                        <i class="fa fa-save"></i> Enregistrer
                    </button>
                    <button type="button" class="btn btn-outline-simulator" data-dismiss="modal">
                        <i class="fa fa-times"></i> Fermer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour voir les transactions d'une banque -->
<div class="modal fade modal-simulator" id="viewTransactionsModal" tabindex="-1" role="dialog" aria-labelledby="viewTransactionsModalLabel">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="viewTransactionsModalLabel">
                    <i class="fa fa-list-alt mr-2"></i> Transactions de la banque : <span id="bankNameTitle" style="font-weight: 600;"></span>
                </h4>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                <button type="button" class="btn btn-outline-simulator" data-dismiss="modal">
                    <i class="fa fa-times"></i> Fermer
                </button>
            </div>
            <div class="modal-body">
                <div id="bankTransactionsContent">
                    <div class="text-center py-5">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Chargement...</span>
                        </div>
                        <p class="mt-3 text-muted">Chargement des transactions...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-simulator" data-dismiss="modal">
                    <i class="fa fa-times"></i> Fermer
                </button>
                <button type="button" class="btn btn-danger" onclick="exportBankTransactionsPDF()">
                    <i class="fa fa-file-pdf-o"></i> PDF
                </button>
                <button type="button" class="btn btn-success" onclick="exportBankTransactionsExcel()">
                    <i class="fa fa-file-excel-o"></i> Excel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmation suppression -->
<div class="modal fade modal-simulator" id="deleteBankModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fa fa-exclamation-triangle mr-2"></i> Confirmation de suppression
                </h4>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette banque ?</p>
                <p class="text-warning">
                    <small><i class="fa fa-warning"></i> Cette action ne peut pas être annulée.</small>
                </p>
                <p id="deleteWarningMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="fa fa-trash"></i> Supprimer
                </button>
                <button type="button" class="btn btn-outline-simulator" data-dismiss="modal">
                    <i class="fa fa-times"></i> Annuler
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    /* =========================
       VARIABLES GLOBALES
    ========================= */
    var currentBankId = null;
    var currentBankName = null;
    var currentTransactionsHTML = null;

    /* =========================
       INITIALISATION
    ========================= */
    $(document).ready(function() {
        initializeEventHandlers();
        updateLogoInitials();
    });

    /* =========================
       GESTION DES LOGOS
    ========================= */
    function updateLogoInitials() {
        var name = $('#bank_name').val();
        var initials = name ? name.substring(0, 2).toUpperCase() : 'BN';
        $('#logoInitials').text(initials);
    }

    $('#logoInput').change(function(e) {
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#logoImage').attr('src', e.target.result).show();
                $('#defaultLogo').hide();
            };
            reader.readAsDataURL(file);
        }
    });

    $('#newLogoInput').change(function(e) {
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImage').attr('src', e.target.result);
                $('#newLogoPreview').show();
            };
            reader.readAsDataURL(file);
        }
    });

    /* =========================
       TOAST NOTIFICATION
    ========================= */
    function showToast(type, message) {
        var toastClass = 'alert-success';
        var icon = '<i class="fa fa-check-circle"></i> ';
        var title = 'Succès';

        if (type == 'error') {
            toastClass = 'alert-danger';
            icon = '<i class="fa fa-exclamation-circle"></i> ';
            title = 'Erreur';
        } else if (type == 'warning') {
            toastClass = 'alert-warning';
            icon = '<i class="fa fa-exclamation-triangle"></i> ';
            title = 'Attention';
        } else if (type == 'info') {
            toastClass = 'alert-info';
            icon = '<i class="fa fa-info-circle"></i> ';
            title = 'Information';
        }

        $('.custom-toast').remove();

        var toast = '<div class="alert ' + toastClass + ' alert-dismissible custom-toast fade show" role="alert" ' +
            'style="position: fixed; top: 70px; right: 20px; z-index: 1060; min-width: 300px; max-width: 400px; ' +
            'box-shadow: 0 4px 12px rgba(0,0,0,0.15); border-left: 4px solid ' +
            (type == 'success' ? '#27ae60' : type == 'error' ? '#e74c3c' : type == 'warning' ? '#f39c12' : '#3498db') + ';">' +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close" style="position: absolute; top: 5px; right: 10px;">' +
            '<span aria-hidden="true">&times;</span>' +
            '</button>' +
            '<div class="d-flex align-items-start">' +
            '<div style="font-size: 20px; margin-right: 10px; margin-top: 2px;">' + icon + '</div>' +
            '<div style="flex: 1; padding-right: 20px;">' +
            '<strong style="display: block; margin-bottom: 2px; font-size: 14px;">' + title + '</strong>' +
            '<span style="font-size: 13px; color: #2c3e50;">' + message + '</span>' +
            '</div>' +
            '</div>' +
            '</div>';

        $('body').append(toast);
        $('.custom-toast').hide().fadeIn(300);

        setTimeout(function() {
            $('.custom-toast').fadeOut(300, function() {
                $(this).remove();
            });
        }, 4000);
    }

    /* =========================
       FORMATAGE DES NOMBRES
    ========================= */
    function formatNumber(number) {
        return parseFloat(number).toLocaleString('fr-FR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    /* =========================
       RECHERCHE
    ========================= */
    $('#searchBanks').on('input', function() {
        var searchTerm = $(this).val().toLowerCase();
        var bankCards = $('.bank-card');
        var visibleCount = 0;

        bankCards.each(function() {
            var bankName = $(this).data('bank-name').toLowerCase();
            var bankCode = $(this).data('bank-code').toLowerCase();
            var bankAccount = $(this).data('bank-account').toLowerCase();

            if (bankName.includes(searchTerm) ||
                bankCode.includes(searchTerm) ||
                bankAccount.includes(searchTerm) ||
                searchTerm === '') {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });

        $('#totalBanks').text(visibleCount);
    });

    /* =========================
       HANDLERS D'ÉVÉNEMENTS
    ========================= */
    function initializeEventHandlers() {
        // Ajouter une transaction
        $('.add-transaction-btn').click(function(e) {
            e.stopPropagation();
            var bankId = $(this).data('bank-id');
            var bankName = $(this).data('bank-name');

            $('#transaction_bank_id').val(bankId);
            $('#transactionModalLabel').html('<i class="fa fa-exchange mr-2"></i> Nouvelle Transaction - ' + bankName);
            $('#transactionModal').modal('show');
        });

        // Voir les transactions
        $('.view-transactions-btn').click(function(e) {
            e.stopPropagation();
            var bankId = $(this).data('bank-id');
            var bankName = $(this).data('bank-name');

            currentBankId = bankId;
            currentBankName = bankName;
            loadBankTransactions(bankId, bankName);
        });

        // Éditer une banque
        $('.edit-bank-mini').click(function(e) {
            e.stopPropagation();
            var id = $(this).data('id');
            var name = $(this).data('name');
            var code = $(this).data('code');
            var account_number = $(this).data('account_number');
            var logo = $(this).data('logo');
            var balance = $(this).data('bank-balance');

            $.ajax({
                url: '<?php echo base_url(); ?>admin/expense/get_bank_initial_balance',
                type: 'POST',
                data: {
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                    bank_id: id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'success') {
                        $('#bank_id_input').val(id);
                        $('#bank_name').val(name);
                        $('#bank_code').val(code);
                        $('#account_number').val(account_number || '');
                        $('#initial_balance').val(response.initial_balance || '0.00');

                        if (logo) {
                            $('#logoImage').attr('src', logo).show();
                            $('#defaultLogo').hide();
                        } else {
                            $('#logoImage').hide();
                            $('#defaultLogo').show();
                            updateLogoInitials();
                        }

                        $('#bankModalLabel').html('<i class="fa fa-university mr-2"></i> Modifier la Banque');
                        $('#bankModal').modal('show');
                    }
                }
            });
        });

        // Supprimer une banque
        $('.delete-bank-mini').click(function(e) {
            e.stopPropagation();
            var bankId = $(this).data('id');
            var bankCard = $(this).closest('.bank-card');
            var bankName = bankCard.data('bank-name');
            var balance = bankCard.data('bank-balance') || 0;

            var warningMessage = '';
            if (parseFloat(balance) !== 0) {
                warningMessage = '<div class="alert alert-warning mt-2">';
                warningMessage += '<i class="fa fa-exclamation-triangle"></i> ';
                warningMessage += 'Attention: Cette banque a un solde de ' + formatNumber(balance) + ' <?php echo $currency_symbol; ?>. ';
                warningMessage += 'La suppression effacera toutes les transactions associées.';
                warningMessage += '</div>';
            }

            $('#deleteWarningMessage').html(warningMessage);
            $('#deleteBankModal').data('bank-id', bankId);
            $('#deleteBankModal').modal('show');
        });

        // Confirmation de suppression
        $('#confirmDelete').off('click').on('click', function() {
            var bankId = $('#deleteBankModal').data('bank-id');
            var deleteBtn = $(this);

            deleteBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Suppression...');

            $.ajax({
                url: '<?php echo base_url(); ?>admin/expense/delete_bank',
                type: 'POST',
                data: {
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                    bank_id: bankId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'success') {
                        showToast('success', response.message);
                        $('#deleteBankModal').modal('hide');
                        setTimeout(function() {
                            location.reload();
                        }, 300);
                    } else {
                        showToast('error', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    var errorMsg = 'Erreur lors de la suppression';
                    try {
                        var jsonResponse = JSON.parse(xhr.responseText);
                        if (jsonResponse && jsonResponse.message) {
                            errorMsg = jsonResponse.message;
                        }
                    } catch (e) {
                        errorMsg = 'Erreur serveur: ' + error;
                    }
                    showToast('error', errorMsg);
                },
                complete: function() {
                    deleteBtn.prop('disabled', false).html('<i class="fa fa-trash"></i> Supprimer');
                }
            });
        });

        // Formulaire banque
        $('#bankForm').submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var submitBtn = form.find('button[type="submit"]');
            var modal = $('#bankModal');

            submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Enregistrement...');

            var formData = new FormData(this);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'success') {
                        showToast('success', response.message);
                        modal.modal('hide');
                        form[0].reset();
                        $('#bank_id_input').val('');
                        $('#logoImage').attr('src', '').hide();
                        $('#defaultLogo').show();
                        updateLogoInitials();

                        setTimeout(function() {
                            location.reload();
                        }, 500);
                    } else {
                        showToast('error', response.message || 'Erreur inconnue');
                    }
                },
                error: function(xhr, status, error) {
                    var errorMsg = 'Erreur serveur';
                    try {
                        var jsonResponse = JSON.parse(xhr.responseText);
                        if (jsonResponse && jsonResponse.message) {
                            errorMsg = jsonResponse.message;
                        }
                    } catch (e) {
                        errorMsg = error;
                    }
                    showToast('error', errorMsg);
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Enregistrer');
                }
            });
        });

        // Formulaire transaction
        $('#transactionForm').submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var submitBtn = form.find('button[type="submit"]');
            var modal = $('#transactionModal');

            submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Enregistrement...');

            var formData = new FormData(this);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'success') {
                        showToast('success', response.message);
                        modal.modal('hide');
                        form[0].reset();
                        setTimeout(function() {
                            location.reload();
                        }, 500);
                    } else {
                        showToast('error', response.message || 'Erreur inconnue');
                    }
                },
                error: function(xhr, status, error) {
                    var errorMsg = 'Erreur serveur';
                    try {
                        var jsonResponse = JSON.parse(xhr.responseText);
                        if (jsonResponse && jsonResponse.message) {
                            errorMsg = jsonResponse.message;
                        }
                    } catch (e) {
                        errorMsg = error;
                    }
                    showToast('error', errorMsg);
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Enregistrer');
                }
            });
        });

        // Supprimer transaction
        $(document).on('click', '.delete-transaction', function(e) {
            e.stopPropagation();
            var transactionId = $(this).data('id');
            var transactionItem = $(this).closest('tr');

            if (!confirm('Êtes-vous sûr de vouloir supprimer cette transaction ?\nCette action est irréversible et mettra à jour le solde de la banque.')) {
                return;
            }

            var deleteBtn = $(this);
            deleteBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

            $.ajax({
                url: '<?php echo base_url(); ?>admin/expense/delete_transaction',
                type: 'POST',
                data: {
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                    transaction_id: transactionId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'success') {
                        showToast('success', response.message);
                        transactionItem.fadeOut(300, function() {
                            $(this).remove();
                            if ($('#viewTransactionsModal tbody tr').length <= 1) {
                                loadBankTransactions(currentBankId, currentBankName);
                            }
                            setTimeout(function() {
                                location.reload();
                            }, 500);
                        });
                    } else {
                        showToast('error', response.message);
                        deleteBtn.prop('disabled', false).html('<i class="fa fa-trash"></i>');
                    }
                },
                error: function(xhr, status, error) {
                    showToast('error', 'Erreur lors de la suppression: ' + error);
                    deleteBtn.prop('disabled', false).html('<i class="fa fa-trash"></i>');
                }
            });
        });

        // Éditer transaction
        $(document).on('click', '.edit-transaction', function(e) {
            e.stopPropagation();
            var transactionId = $(this).data('id');
            var editBtn = $(this);

            editBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

            $.ajax({
                url: '<?php echo base_url(); ?>admin/expense/get_transaction',
                type: 'POST',
                data: {
                    '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                    transaction_id: transactionId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'success') {
                        var data = response.data;
                        $('input[name="transaction_id"]').remove();
                        $('#transactionForm')[0].reset();
                        $('#transactionForm').append(
                            '<input type="hidden" name="transaction_id" value="' + data.id + '">'
                        );

                        $('#transaction_bank_id').val(data.bank_id);
                        $('#transaction_type').val(data.transaction_type);
                        $('#designation').val(data.designation);
                        $('#date').val(data.date);
                        $('#nom').val(data.nom);
                        $('#name').val(data.name);
                        $('#nom').val(data.nom);
                        $('#amount').val(data.amount);
                        $('#reference').val(data.reference);
                        $('#payment_mode').val(data.payment_mode);
                        $('#description').val(data.note);

                        $('#transactionModalLabel').html('<i class="fa fa-exchange mr-2"></i> Modifier Transaction');
                        $('#viewTransactionsModal').modal('hide');
                        $('#transactionModal').modal('show');
                    } else {
                        showToast('error', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    showToast('error', 'Impossible de charger la transaction');
                },
                complete: function() {
                    editBtn.prop('disabled', false).html('<i class="fa fa-edit"></i>');
                }
            });
        });

        // Autres événements
        $('#refreshPageBtn').click(function() {
            location.reload();
        });

        $('.bank-logo-large').on('error', function() {
            $(this).hide();
            $(this).next('.bank-logo-default-large').show();
        });

        $('#bankModal').on('hidden.bs.modal', function() {
            $('#bankForm')[0].reset();
            $('#bank_id_input').val('');
            $('#bankModalLabel').html('<i class="fa fa-university mr-2"></i> Nouvelle Banque');
            $('#logoImage').hide();
            $('#defaultLogo').show();
            updateLogoInitials();
        });

        $('#transactionModal').on('hidden.bs.modal', function() {
            $('input[name="transaction_id"]').remove();
            $('#transactionForm')[0].reset();
            $('#transactionModalLabel').html('<i class="fa fa-exchange mr-2"></i> Nouvelle Transaction');
        });
    }

    /* =========================
       CHARGEMENT TRANSACTIONS
    ========================= */
    function loadBankTransactions(bankId, bankName) {
        currentBankId = bankId;
        currentBankName = bankName;

        $('#bankNameTitle').text(bankName);
        $('#viewTransactionsModal').modal('show');

        $('#bankTransactionsContent').html(
            '<div class="text-center p-4">' +
            '<div class="spinner-border text-primary" role="status">' +
            '<span class="sr-only">Chargement...</span>' +
            '</div>' +
            '<p class="mt-2">Chargement des transactions...</p>' +
            '</div>'
        );

        $.ajax({
            url: '<?php echo base_url(); ?>admin/expense/get_bank_transactions',
            type: 'POST',
            data: {
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                bank_id: bankId
            },
            success: function(response) {
                $('#bankTransactionsContent').html(response);
                window.currentTransactionsHTML = response;

                // Ajouter la barre de filtre par période APRÈS l'insertion du contenu
                addDateRangeFilter();

                setTimeout(initTransactionTableFilters, 100);
            },
            error: function(xhr, status, error) {
                var errorMsg = 'Erreur lors du chargement';
                try {
                    var jsonResponse = JSON.parse(xhr.responseText);
                    if (jsonResponse && jsonResponse.message) {
                        errorMsg = jsonResponse.message;
                    }
                } catch (e) {
                    errorMsg = error;
                }
                $('#bankTransactionsContent').html(
                    '<div class="alert alert-danger m-3">' +
                    '<i class="fa fa-exclamation-circle"></i> ' + errorMsg +
                    '</div>'
                );
            }
        });
    }

    // Fonction corrigée pour ajouter le filtre par plage de dates
    function addDateRangeFilter() {
        // Chercher la table des transactions dans le contenu chargé
        var $table = $('#bankTransactionsContent table');
        if ($table.length === 0) return;

        // Vérifier si la barre est déjà présente
        if ($('#dateRangeFilterBar').length > 0) return;

        // Créer la barre de filtre
        var filterBar = $(
            '<div id="dateRangeFilterBar" class="mb-3 p-3" style="background: #f8f9fa; border-radius: 12px; border: 1px solid #e9ecef;">' +
            '<div class="row align-items-end">' +
            '<div class="col-md-4 mb-2 mb-md-0">' +
            '<label class="form-label small text-muted mb-1">Date début</label>' +
            '<input type="date" id="filterDateStart" class="form-control form-control-sm" style="border-radius: 8px;">' +
            '</div>' +
            '<div class="col-md-4 mb-2 mb-md-0">' +
            '<label class="form-label small text-muted mb-1">Date fin</label>' +
            '<input type="date" id="filterDateEnd" class="form-control form-control-sm" style="border-radius: 8px;">' +
            '</div>' +
            '<div class="col-md-4">' +
            '<button id="applyDateFilter" class="btn btn-primary-simulator btn-sm mr-2"><i class="fa fa-filter"></i> Filtrer</button>' +
            '<button id="resetDateFilter" class="btn btn-outline-simulator btn-sm"><i class="fa fa-refresh"></i> Réinitialiser</button>' +
            '</div>' +
            '</div>' +
            '</div>'
        );

        // Insérer la barre AVANT la table
        $table.before(filterBar);

        // Événements
        $('#applyDateFilter').off('click').on('click', function() {
            var startDate = $('#filterDateStart').val();
            var endDate = $('#filterDateEnd').val();
            filterTransactionsByDate(startDate, endDate);
        });

        $('#resetDateFilter').off('click').on('click', function() {
            $('#filterDateStart').val('');
            $('#filterDateEnd').val('');
            // Réafficher toutes les lignes
            $('#bankTransactionsContent tbody tr').show();
            updateTransactionCountDisplay();
        });
    }

    function filterTransactionsByDate(startDate, endDate) {
        var rows = $('#bankTransactionsContent tbody tr');
        var visibleCount = 0;
        var totalCount = rows.length;

        rows.each(function() {
            var dateCell = $(this).find('td:first');
            if (dateCell.length === 0) return;

            var dateText = dateCell.text().trim();
            var rowDate = parseDate(dateText);
            if (!rowDate) {
                $(this).show();
                visibleCount++;
                return;
            }

            var show = true;
            if (startDate && rowDate < new Date(startDate)) show = false;
            if (endDate && rowDate > new Date(endDate)) show = false;

            if (show) {
                $(this).show();
                visibleCount++;
            } else {
                $(this).hide();
            }
        });

        // Afficher un message si aucun résultat
        var infoMessage = $('#dateFilterInfo');
        if (infoMessage.length === 0 && visibleCount === 0 && totalCount > 0) {
            $('<div id="dateFilterInfo" class="alert alert-info mt-2"><i class="fa fa-info-circle"></i> Aucune transaction dans cette période.</div>')
                .insertAfter('#dateRangeFilterBar');
        } else if (visibleCount > 0) {
            $('#dateFilterInfo').remove();
        }

        updateTransactionCountDisplay(visibleCount, totalCount);
    }

    function parseDate(dateStr) {
        // Format français: DD/MM/YYYY
        var parts = dateStr.split('/');
        if (parts.length === 3) {
            var day = parseInt(parts[0], 10);
            var month = parseInt(parts[1], 10) - 1;
            var year = parseInt(parts[2], 10);
            if (!isNaN(day) && !isNaN(month) && !isNaN(year)) {
                return new Date(year, month, day);
            }
        }
        // Format ISO: YYYY-MM-DD
        var isoParts = dateStr.split('-');
        if (isoParts.length === 3 && isoParts[0].length === 4) {
            var y = parseInt(isoParts[0], 10);
            var m = parseInt(isoParts[1], 10) - 1;
            var d = parseInt(isoParts[2], 10);
            if (!isNaN(y) && !isNaN(m) && !isNaN(d)) {
                return new Date(y, m, d);
            }
        }
        return null;
    }

    function updateTransactionCountDisplay(visible, total) {
        var countSpan = $('#transactionCount');
        if (countSpan.length === 0) {
            // Créer un affichage du nombre si non existant
            var tableWrapper = $('#bankTransactionsContent .table-responsive, #bankTransactionsContent .table-container');
            if (tableWrapper.length === 0) tableWrapper = $('#bankTransactionsContent table').parent();
            if (tableWrapper.length) {
                var badge = $('<div class="text-right mb-2"><span id="transactionCount" class="badge badge-primary p-2">Total: ' + (total || $('#bankTransactionsContent tbody tr').length) + ' transaction(s)</span></div>');
                tableWrapper.before(badge);
                countSpan = $('#transactionCount');
            }
        }
        if (countSpan.length) {
            if (visible !== undefined && total !== undefined && visible < total) {
                countSpan.text('Affichage: ' + visible + ' / ' + total + ' transaction(s)').removeClass('badge-primary').addClass('badge-warning');
            } else {
                var allTotal = $('#bankTransactionsContent tbody tr').length;
                countSpan.text('Total: ' + allTotal + ' transaction(s)').removeClass('badge-warning').addClass('badge-primary');
            }
        }
    }

    function initTransactionTableFilters() {
        setTimeout(function() {
            const table = document.getElementById('transactionsTable');
            const tableBody = document.getElementById('transactionsBody');
            const resetBtn = document.getElementById('resetFiltersBtn');
            const transactionCount = document.getElementById('transactionCount');

            if (!table || !tableBody) return;

            const filterInputs = table.querySelectorAll('.filter-input');
            filterInputs.forEach(input => {
                input.addEventListener('input', filterTransactionTable);
                input.addEventListener('change', filterTransactionTable);
            });

            if (resetBtn) {
                resetBtn.addEventListener('click', function() {
                    filterInputs.forEach(input => {
                        input.value = '';
                    });
                    filterTransactionTable();
                    this.style.display = 'none';
                });
            }

            function filterTransactionTable() {
                const rows = tableBody.querySelectorAll('tr');
                let hasActiveFilters = false;
                let visibleCount = 0;

                filterInputs.forEach(input => {
                    if (input.value.trim() !== '') {
                        hasActiveFilters = true;
                    }
                });

                if (resetBtn) {
                    resetBtn.style.display = hasActiveFilters ? 'inline-block' : 'none';
                }

                rows.forEach(row => {
                    let showRow = true;
                    const cells = row.querySelectorAll('td');

                    filterInputs.forEach(input => {
                        const columnIndex = parseInt(input.getAttribute('data-column'));
                        const filterValue = input.value.trim().toLowerCase();

                        if (filterValue !== '' && cells[columnIndex]) {
                            let cellText = '';

                            if (columnIndex === 4) {
                                const badge = cells[columnIndex].querySelector('.badge');
                                cellText = badge ? badge.textContent.trim().toLowerCase() : '';
                            } else if (columnIndex === 5) {
                                cellText = cells[columnIndex].textContent.trim().toLowerCase();
                                cellText = cellText.replace('fcfa', '').trim();
                            } else {
                                cellText = cells[columnIndex].textContent.trim().toLowerCase();
                            }

                            if (input.tagName === 'SELECT') {
                                if (filterValue !== '' && cellText !== filterValue) {
                                    showRow = false;
                                }
                            } else {
                                if (filterValue !== '' && !cellText.includes(filterValue)) {
                                    showRow = false;
                                }
                            }
                        }
                    });

                    if (showRow) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (transactionCount) {
                    const totalRows = rows.length;
                    if (visibleCount < totalRows) {
                        transactionCount.textContent = 'Total: ' + visibleCount + ' transaction(s) sur ' + totalRows;
                        transactionCount.className = 'badge badge-warning';
                    } else {
                        transactionCount.textContent = 'Total: ' + visibleCount + ' transaction(s)';
                        transactionCount.className = 'badge badge-primary';
                    }
                }
            }

            if (transactionCount && tableBody) {
                const totalRows = tableBody.querySelectorAll('tr').length;
                transactionCount.textContent = 'Total: ' + totalRows + ' transaction(s)';
            }
        }, 300);
    }

    /* =========================
       FONCTIONS D'EXPORT (MODIFIÉES)
    ========================= */
    function exportAllBanksPDF() {
        var banksData = [];
        $('.bank-card').each(function() {
            banksData.push({
                name: $(this).data('bank-name'),
                code: $(this).data('bank-code'),
                balance: $(this).data('bank-balance'),
                initial_balance: $(this).data('initial-balance'),
                account: $(this).data('bank-account') || 'Non défini'
            });
        });

        var totalBalance = banksData.reduce((sum, bank) => sum + parseFloat(bank.balance), 0);
        var totalInitialBalance = banksData.reduce((sum, bank) => sum + parseFloat(bank.initial_balance), 0);

        var printWindow = window.open('', '_blank');
        var content = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Liste des Banques - Export PDF</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                    margin: 40px;
                    background: #f8fafc;
                }
                .pdf-container {
                    max-width: 1100px;
                    margin: 0 auto;
                    background: white;
                    padding: 40px;
                    border-radius: 24px;
                    box-shadow: 0 20px 40px rgba(0,0,0,0.05);
                    border: 1px solid #e9ecef;
                }
                h1 {
                    color: #2c3e50;
                    text-align: center;
                    font-size: 28px;
                    margin-bottom: 12px;
                    font-weight: 600;
                }
                .pdf-subtitle {
                    text-align: center;
                    color: #5d6d7e;
                    margin-bottom: 30px;
                    font-size: 14px;
                }
                .pdf-date {
                    text-align: center;
                    color: #7f8c8d;
                    margin-bottom: 35px;
                    font-size: 13px;
                    background: #f8f9fa;
                    padding: 8px 20px;
                    border-radius: 30px;
                    display: inline-block;
                    margin: 0 auto 35px;
                    display: table;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 25px;
                    border-radius: 16px;
                    overflow: hidden;
                    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
                }
                th {
                    background: #2c3e50;
                    color: white;
                    padding: 14px 16px;
                    text-align: left;
                    font-weight: 600;
                    font-size: 13px;
                    letter-spacing: 0.5px;
                }
                td {
                    padding: 12px 16px;
                    border-bottom: 1px solid #e9ecef;
                    text-align: left;
                    font-size: 13px;
                }
                tr:nth-child(even) {
                    background-color: #fafbfc;
                }
                .total-section {
                    margin-top: 35px;
                    padding: 25px;
                    background: #f8f9fa;
                    border-radius: 16px;
                    text-align: right;
                    border: 1px solid #e9ecef;
                }
                .balance-positive {
                    color: #27ae60;
                    font-weight: 600;
                }
                .balance-negative {
                    color: #e74c3c;
                    font-weight: 600;
                }
                .footer {
                    margin-top: 40px;
                    text-align: center;
                    color: #7f8c8d;
                    font-size: 11px;
                    padding-top: 20px;
                    border-top: 1px solid #e9ecef;
                }
            </style>
        </head>
        <body>
            <div class="pdf-container">
                <h1>Liste des Banques</h1>
                <div class="pdf-subtitle">Gestion des comptes bancaires</div>
                <div style="text-align: center;">
                    <div class="pdf-date">Exporté le ${new Date().toLocaleDateString('fr-FR', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        })}</div>
                </div>
                <table>
                    <thead>
                        <tr><th>Nom</th><th>Code</th><th>Compte</th><th>Solde initial</th><th>Solde actuel</th></tr>
                    </thead>
                    <tbody>
                        ${banksData.map(bank => `
                            <tr>
                                <td><strong>${bank.name}</strong></td>
                                <td>${bank.code}</td>
                                <td>${bank.account}</td>
                                <td>${formatNumber(bank.initial_balance)} <?php echo $currency_symbol; ?></td>
                                <td class="${bank.balance >= 0 ? 'balance-positive' : 'balance-negative'}">
                                    ${formatNumber(bank.balance)} <?php echo $currency_symbol; ?>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
                <div class="total-section">
                    <div><strong>Total des banques :</strong> ${banksData.length}</div>
                    <div><strong>Total soldes initiaux :</strong> ${formatNumber(totalInitialBalance)} <?php echo $currency_symbol; ?></div>
                    <div style="margin-top: 8px;"><strong>Total soldes actuels :</strong> <span class="${totalBalance >= 0 ? 'balance-positive' : 'balance-negative'}">${formatNumber(totalBalance)} <?php echo $currency_symbol; ?></span></div>
                </div>
                <div class="footer">
                    <p>Document généré automatiquement par le système de gestion bancaire</p>
                </div>
            </div>
        </body>
        </html>
    `;

        printWindow.document.write(content);
        printWindow.document.close();
        setTimeout(function() { printWindow.print(); setTimeout(function() { printWindow.close(); }, 1000); }, 1000);
    }

    function exportAllBanksExcel() {
        var csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "Nom de la Banque,Code,Numéro de Compte,Solde initial (<?php echo $currency_symbol; ?>),Solde actuel (<?php echo $currency_symbol; ?>),Date d'export\r\n";

        $('.bank-card').each(function() {
            var name = $(this).data('bank-name');
            var code = $(this).data('bank-code');
            var account = $(this).data('bank-account') || 'Non défini';
            var initialBalance = $(this).data('initial-balance');
            var currentBalance = $(this).data('bank-balance');
            csvContent += `"${name}","${code}","${account}","${initialBalance}","${currentBalance}","${new Date().toLocaleDateString('fr-FR')}"\r\n`;
        });

        var totalInitialBalance = 0;
        var totalCurrentBalance = 0;
        $('.bank-card').each(function() {
            totalInitialBalance += parseFloat($(this).data('initial-balance'));
            totalCurrentBalance += parseFloat($(this).data('bank-balance'));
        });

        csvContent += `\r\nRésumé,,,,,\r\n"Total des banques","${$('.bank-card').length}",,,,\r\n"Total soldes initiaux","${totalInitialBalance.toFixed(2)}",,,,\r\n"Total soldes actuels","${totalCurrentBalance.toFixed(2)}",,,,\r\n"Date d'export","${new Date().toLocaleDateString('fr-FR')}",,,,\r\n`;

        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "banques_export_" + new Date().toISOString().split('T')[0] + ".csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        showToast('success', 'Export Excel terminé avec succès');
    }

    function exportBankTransactionsPDF() {
        if (!currentBankId) {
            showToast('error', 'Aucune banque sélectionnée');
            return;
        }

        // Récupérer uniquement les lignes VISIBLES après filtrage
        var rows = $('#viewTransactionsModal table tbody tr:visible');
        var transactionsData = [];
        var totalAmount = 0;
        var creditTotal = 0;
        var debitTotal = 0;

        rows.each(function() {
            var cells = $(this).find('td');
            if (cells.length >= 6) {
                var date = $(cells[0]).text() || '';
                var nom = $(cells[1]).text() || '';
                var libelle = $(cells[2]).text() || '';
                var type = $(cells[3]).text() || '';
                var designation = $(cells[4]).find('.badge').text() || $(cells[4]).text() || '';
                var amountText = $(cells[5]).text() || '';
                var reference = cells.length > 6 ? $(cells[6]).text() || '' : '';

                var amountMatch = amountText.match(/[+-]?[\d\s,]+/);
                var amount = 0;
                if (amountMatch) {
                    amount = parseFloat(amountMatch[0].replace(/\s/g, '').replace(',', '.'));
                    var isCredit = amountText.includes('+') || designation === 'Crédit' || ['Dépôt', 'Virement entrant'].includes(type);
                    if (isCredit) {
                        creditTotal += amount;
                    } else {
                        debitTotal += amount;
                        amount = -amount;
                    }
                    totalAmount += amount;
                }

                transactionsData.push({
                    date: date, nom: nom, libelle: libelle, type: type,
                    designation: designation, amount: amountText, amountValue: amount, reference: reference
                });
            }
        });

        // Récupérer le solde initial de la banque
        var initialBalance = 0;
        var bankCard = $('.bank-card[data-bank-id="'+currentBankId+'"]');
        if (bankCard.length) {
            initialBalance = parseFloat(bankCard.data('initial-balance')) || 0;
        }

        var printWindow = window.open('', '_blank');
        var content = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Transactions - ${currentBankName}</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                    margin: 40px;
                    background: #f8fafc;
                }
                .pdf-container {
                    max-width: 1200px;
                    margin: 0 auto;
                    background: white;
                    padding: 40px;
                    border-radius: 24px;
                    box-shadow: 0 20px 40px rgba(0,0,0,0.05);
                }
                h1 { color: #2c3e50; text-align: center; font-size: 28px; margin-bottom: 8px; }
                h2 { color: #5d6d7e; text-align: center; font-size: 18px; margin-bottom: 20px; font-weight: normal; }
                .pdf-date {
                    text-align: center; color: #7f8c8d; margin-bottom: 30px; font-size: 13px;
                    background: #f8f9fa; padding: 6px 16px; border-radius: 30px; display: table; margin: 0 auto 30px;
                }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; border-radius: 16px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
                th { background: #2c3e50; color: white; padding: 14px 12px; text-align: left; font-weight: 600; font-size: 12px; letter-spacing: 0.5px; }
                td { padding: 12px 12px; border-bottom: 1px solid #e9ecef; font-size: 12px; }
                tr:nth-child(even) { background-color: #fafbfc; }
                .amount-credit { color: #27ae60; font-weight: 600; }
                .amount-debit { color: #e74c3c; font-weight: 600; }
                .summary-section {
                    margin-top: 35px; padding: 25px; background: #f8f9fa; border-radius: 16px;
                    display: flex; justify-content: space-around; flex-wrap: wrap; gap: 15px;
                }
                .summary-item { text-align: center; flex: 1; min-width: 150px; }
                .summary-label { font-size: 12px; color: #7f8c8d; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
                .summary-value { font-size: 20px; font-weight: 700; color: #2c3e50; }
                .summary-value.credit { color: #27ae60; }
                .summary-value.debit { color: #e74c3c; }
                .footer { margin-top: 35px; text-align: center; color: #7f8c8d; font-size: 11px; padding-top: 20px; border-top: 1px solid #e9ecef; }
            </style>
        </head>
        <body>
            <div class="pdf-container">
                <h1>Transactions Bancaires</h1>
                <h2>Banque : ${currentBankName}</h2>
                <div style="text-align: center;"><div class="pdf-date">Exporté le ${new Date().toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}</div></div>
                <div style="margin-bottom: 20px; background: #f1f5f9; padding: 12px; border-radius: 12px; text-align: center;">
                    <strong>Solde initial de la banque :</strong> ${formatNumber(initialBalance)} <?php echo $currency_symbol; ?>
                </div>
                <table class="table-operations">
                    <thead><tr><th>Date</th><th>Nom</th><th>Libellé</th><th>Type</th><th>Désignation</th><th>Montant</th></tr></thead>
                    <tbody>${transactionsData.map(trans => `<tr><td>${trans.date || ''}</td><td>${trans.nom || ''}</td><td>${trans.libelle || ''}</td><td>${trans.type || ''}</td><td>${trans.designation || ''}</td><td class="${trans.amount.includes('+') || trans.designation === 'Crédit' ? 'amount-credit' : 'amount-debit'}">${trans.amount || ''}</td></tr>`).join('')}</tbody>
                </table>
                <div class="summary-section">
                    <div class="summary-item"><div class="summary-label">Nombre total</div><div class="summary-value">${transactionsData.length}</div></div>
                    <div class="summary-item"><div class="summary-label">Total Crédits</div><div class="summary-value credit">${formatNumber(creditTotal)} <?php echo $currency_symbol; ?></div></div>
                    <div class="summary-item"><div class="summary-label">Total Débits</div><div class="summary-value debit">${formatNumber(debitTotal)} <?php echo $currency_symbol; ?></div></div>
                    <div class="summary-item"><div class="summary-label">Solde Net (mouvements)</div><div class="summary-value ${totalAmount >= 0 ? 'credit' : 'debit'}">${formatNumber(totalAmount)} <?php echo $currency_symbol; ?></div></div>
                </div>
                <div class="footer"><p>Document généré automatiquement par le système de gestion bancaire</p></div>
            </div>
        </body>
        </html>
    `;
        printWindow.document.write(content);
        printWindow.document.close();
        setTimeout(function() { printWindow.print(); setTimeout(function() { printWindow.close(); }, 1000); }, 1000);
    }

    function exportBankTransactionsExcel() {
        if (!currentBankId) { showToast('error', 'Aucune banque sélectionnée'); return; }

        // Récupérer uniquement les lignes VISIBLES
        var rows = $('#viewTransactionsModal table tbody tr:visible');
        var csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "Date,Nom,Libellé,Type,Désignation,Montant,Référence,Banque,Date d'export\r\n";
        var transactionsCount = 0, creditTotal = 0, debitTotal = 0, netTotal = 0;

        rows.each(function() {
            var cells = $(this).find('td');
            if (cells.length >= 6) {
                var date = $(cells[0]).text() || '';
                var nom = $(cells[1]).text() || '';
                var libelle = $(cells[2]).text() || '';
                var type = $(cells[3]).text() || '';
                var designation = $(cells[4]).find('.badge').text() || $(cells[4]).text() || '';
                var amountText = $(cells[5]).text() || '';
                var reference = cells.length > 6 ? $(cells[6]).text() || '' : '';

                var amountMatch = amountText.match(/[+-]?[\d\s,]+/);
                if (amountMatch) {
                    var amount = parseFloat(amountMatch[0].replace(/\s/g, '').replace(',', '.'));
                    var isCredit = amountText.includes('+') || designation === 'Crédit' || ['Dépôt', 'Virement entrant'].includes(type);
                    if (isCredit) creditTotal += amount; else debitTotal += amount;
                    netTotal += isCredit ? amount : -amount;
                }

                csvContent += `"${date}","${nom}","${libelle}","${type}","${designation}","${amountText}","${reference}","${currentBankName}","${new Date().toLocaleDateString('fr-FR')}"\r\n`;
                transactionsCount++;
            }
        });

        // Récupérer solde initial
        var initialBalance = 0;
        var bankCard = $('.bank-card[data-bank-id="'+currentBankId+'"]');
        if (bankCard.length) {
            initialBalance = parseFloat(bankCard.data('initial-balance')) || 0;
        }

        csvContent += `\r\nRÉSUMÉ,,,,,,,,\r\n"Solde initial de la banque","${initialBalance.toFixed(2)} <?php echo $currency_symbol; ?>",,,,,,,\r\n"Nombre total de transactions (filtrées)","${transactionsCount}",,,,,,,\r\n"Total Crédits","${creditTotal.toFixed(2)} <?php echo $currency_symbol; ?>",,,,,,,\r\n"Total Débits","${debitTotal.toFixed(2)} <?php echo $currency_symbol; ?>",,,,,,,\r\n"Solde Net des mouvements","${netTotal.toFixed(2)} <?php echo $currency_symbol; ?>",,,,,,,\r\n"Date d'export","${new Date().toLocaleDateString('fr-FR')}",,,,,,,\r\n`;

        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "transactions_" + currentBankName.replace(/[^a-z0-9]/gi, '_') + "_" + new Date().toISOString().split('T')[0] + ".csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        showToast('success', 'Export Excel des transactions filtrées terminé avec succès');
    }

    function printBankList() {
        var banksData = [];
        $('.bank-card').each(function() {
            banksData.push({
                name: $(this).data('bank-name'),
                code: $(this).data('bank-code'),
                balance: $(this).data('bank-balance'),
                initial_balance: $(this).data('initial-balance'),
                account: $(this).data('bank-account') || 'Non défini'
            });
        });

        var totalBalance = banksData.reduce((sum, bank) => sum + parseFloat(bank.balance), 0);
        var totalInitialBalance = banksData.reduce((sum, bank) => sum + parseFloat(bank.initial_balance), 0);

        var printContent = `<html><head><title>Liste des Banques</title><style>
            body { font-family: 'Inter', sans-serif; margin: 40px; background: white; }
            h1 { text-align: center; color: #2c3e50; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; border: 1px solid #e9ecef; border-radius: 12px; overflow: hidden; }
            th, td { border: 1px solid #e9ecef; padding: 12px; text-align: left; }
            th { background-color: #2c3e50; color: white; }
            .total { margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 12px; text-align: right; }
        </style></head><body>
            <h1>Liste des Banques</h1>
            <p style="text-align: center; color: #5d6d7e;">Document généré le ${new Date().toLocaleDateString('fr-FR')}</p>
            <table><thead><tr><th>Nom</th><th>Code</th><th>Compte</th><th>Solde initial</th><th>Solde actuel</th></tr></thead>
            <tbody>${banksData.map(bank => `<tr><td>${bank.name}</td><td>${bank.code}</td><td>${bank.account}</td><td>${formatNumber(bank.initial_balance)} <?php echo $currency_symbol; ?></td><td>${formatNumber(bank.balance)} <?php echo $currency_symbol; ?></td></tr>`).join('')}</tbody>
            </table>
            <div class="total"><p>Total banques: ${banksData.length}</p><p>Total soldes initiaux: ${formatNumber(totalInitialBalance)} <?php echo $currency_symbol; ?></p><p>Total soldes actuels: ${formatNumber(totalBalance)} <?php echo $currency_symbol; ?></p></div>
        </body></html>`;

        var printWindow = window.open('', '_blank');
        printWindow.document.write(printContent);
        printWindow.document.close();
        setTimeout(function() { printWindow.print(); setTimeout(function() { printWindow.close(); }, 500); }, 500);
    }
</script>