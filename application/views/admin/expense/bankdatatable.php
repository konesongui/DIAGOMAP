<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<style>
    /* Design inspiré du simulateur de prêt bancaire */
    .content-wrapper {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 20px;
    }

    .loan-simulator-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        overflow: hidden;
        margin-bottom: 30px;
    }

    .simulator-header {
        background: linear-gradient(135deg, #4a6bdf 0%, #6a3ca3 100%);
        color: white;
        padding: 30px;
        text-align: center;
        position: relative;
    }

    .simulator-header h1 {
        font-size: 28px;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .simulator-header .subtitle {
        font-size: 16px;
        opacity: 0.9;
        margin-bottom: 20px;
    }

    .steps-container {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 30px 0;
        position: relative;
    }

    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        position: relative;
        z-index: 2;
        flex: 1;
        max-width: 200px;
    }

    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        border: 2px solid rgba(255,255,255,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 18px;
        margin-bottom: 10px;
        transition: all 0.3s ease;
    }

    .step.active .step-number {
        background: white;
        color: #4a6bdf;
        border-color: white;
        transform: scale(1.1);
    }

    .step-label {
        font-size: 14px;
        font-weight: 500;
        text-align: center;
        opacity: 0.8;
    }

    .step.active .step-label {
        opacity: 1;
        font-weight: 600;
    }

    .steps-line {
        position: absolute;
        top: 20px;
        left: 50px;
        right: 50px;
        height: 2px;
        background: rgba(255,255,255,0.3);
        z-index: 1;
    }

    .simulator-body {
        padding: 40px;
    }

    .section-title {
        font-size: 22px;
        font-weight: 600;
        color: #333;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f2f5;
    }

    .banks-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 20px;
        margin: 30px 0;
    }

    .bank-card {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }

    .bank-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border-color: #667eea;
        background: white;
    }

    .bank-card.selected {
        border-color: #667eea;
        background: white;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.2);
    }

    .bank-logo-large {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        object-fit: contain;
        background: white;
        padding: 10px;
        margin: 0 auto 15px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
    }

    .bank-logo-default-large {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 24px;
        margin: 0 auto 15px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .bank-name {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }

    .bank-code {
        font-size: 13px;
        color: #666;
        background: #e9ecef;
        padding: 3px 10px;
        border-radius: 10px;
        display: inline-block;
    }

    .bank-info {
        text-align: left;
        margin-top: 10px;
        font-size: 13px;
        color: #666;
    }

    .bank-info i {
        margin-right: 5px;
        color: #667eea;
    }

    .action-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 40px;
        padding-top: 30px;
        border-top: 1px solid #f0f2f5;
    }

    .btn-simulator {
        padding: 15px 40px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 16px;
        transition: all 0.3s ease;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-primary-simulator {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-primary-simulator:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    }

    .btn-outline-simulator {
        background: transparent;
        border: 2px solid #667eea;
        color: #667eea;
    }

    .btn-outline-simulator:hover {
        background: #667eea;
        color: white;
        transform: translateY(-2px);
    }

    .search-container-simulator {
        position: relative;
        margin-bottom: 30px;
    }

    .search-input-simulator {
        width: 100%;
        padding: 15px 20px 15px 50px;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        font-size: 16px;
        transition: all 0.3s ease;
        background: white;
    }

    .search-input-simulator:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }

    .search-icon-simulator {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: #667eea;
        font-size: 18px;
    }

    .bank-actions-mini {
        position: absolute;
        top: 10px;
        right: 10px;
        display: flex;
        gap: 5px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .bank-card:hover .bank-actions-mini {
        opacity: 1;
    }

    .btn-action-mini {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        border: none;
        background: white;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        transition: all 0.2s ease;
    }

    .btn-action-mini:hover {
        transform: scale(1.1);
    }

    .export-section-simulator {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 20px;
        margin: 30px 0;
    }

    .export-title {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
    }

    .stats-badge {
        display: inline-flex;
        align-items: center;
        background: #f0f2f5;
        padding: 10px 20px;
        border-radius: 25px;
        font-size: 14px;
        color: #333;
        margin-right: 15px;
        margin-bottom: 10px;
    }

    .stats-badge i {
        margin-right: 8px;
        color: #667eea;
    }

    .stats-badge .number {
        font-weight: 600;
        margin-left: 5px;
        color: #667eea;
    }

    .modal-simulator .modal-content {
        border-radius: 20px;
        overflow: hidden;
        border: none;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    }

    .modal-simulator .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 25px;
    }

    .modal-simulator .modal-body {
        padding: 30px;
    }

    .form-group-simulator {
        margin-bottom: 25px;
    }

    .form-group-simulator label {
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
        display: block;
    }

    .form-control-simulator {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 12px 15px;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .form-control-simulator:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .disclaimer {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-top: 30px;
        border-left: 4px solid #667eea;
    }

    .disclaimer h6 {
        color: #333;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .disclaimer p {
        color: #666;
        font-size: 14px;
        margin: 0;
        line-height: 1.6;
    }

    /* Styles DataTable */
    #transactionsTable_wrapper {
        margin-top: 20px;
    }

    #transactionsTable {
        width: 100% !important;
        border-collapse: collapse !important;
    }

    #transactionsTable th {
        background: #667eea;
        color: white;
        padding: 12px 10px;
        text-align: left;
        font-weight: 600;
    }

    #transactionsTable td {
        padding: 10px;
        border-bottom: 1px solid #e9ecef;
        vertical-align: middle;
    }

    #transactionsTable tr:hover {
        background-color: #f8f9fa;
    }

    .dataTables_wrapper .dataTables_filter input {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 8px 12px;
        margin-left: 10px;
    }

    .dataTables_wrapper .dataTables_length select {
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 8px;
    }

    .dt-buttons {
        margin-bottom: 15px;
    }

    .dt-buttons .btn {
        margin-right: 5px;
        margin-bottom: 5px;
        border-radius: 8px;
        background: #667eea;
        color: white;
        border: none;
        padding: 8px 15px;
        font-size: 14px;
    }

    .dt-buttons .btn:hover {
        background: #5569d0;
    }

    .badge-transaction {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-credit {
        background-color: #d4edda;
        color: #155724;
    }

    .badge-debit {
        background-color: #f8d7da;
        color: #721c24;
    }

    .transaction-actions {
        display: flex;
        gap: 5px;
    }

    .btn-transaction-action {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        border: none;
        transition: all 0.2s ease;
    }

    .btn-transaction-action:hover {
        transform: scale(1.1);
    }

    .btn-edit-transaction {
        background: #ffc107;
        color: white;
    }

    .btn-delete-transaction {
        background: #dc3545;
        color: white;
    }

    .total-transactions {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 15px;
        border-radius: 10px;
        margin-top: 20px;
        text-align: center;
        font-weight: 600;
        font-size: 16px;
        color: #333;
    }

    @media (max-width: 768px) {
        .banks-grid {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        }

        .simulator-body {
            padding: 20px;
        }

        .action-buttons {
            flex-direction: column;
            gap: 15px;
        }

        .btn-simulator {
            width: 100%;
        }

        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            float: none !important;
            text-align: center;
            margin-bottom: 10px;
        }
    }
</style>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Simulateur Card -->
        <div class="loan-simulator-card">
            <!-- Header du simulateur -->
            <div class="simulator-header">
                <h1>Gestion des Banques</h1>
                <div class="subtitle">Gérez vos banques et transactions en toute transparence</div>

                <!-- Étapes -->
                <div class="steps-container">
                    <div class="steps-line"></div>
                    <div class="step active">
                        <div class="step-number">1</div>
                        <div class="step-label">Banques</div>
                    </div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <div class="step-label">Transactions</div>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <div class="step-label">Rapports</div>
                    </div>
                </div>
            </div>

            <!-- Corps du simulateur -->
            <div class="simulator-body">
                <!-- Section de recherche -->
                <div class="search-container-simulator">
                    <i class="fa fa-search search-icon-simulator"></i>
                    <input type="text" class="search-input-simulator" id="searchBanks"
                           placeholder="Rechercher une banque par nom, code ou numéro de compte...">
                </div>

                <!-- Statistiques -->
                <div class="d-flex flex-wrap">
                    <div class="stats-badge">
                        <i class="fa fa-university"></i>
                        Banques: <span class="number" id="totalBanks"><?php echo count($banks); ?></span>
                    </div>
                    <div class="stats-badge">
                        <i class="fa fa-money"></i>
                        Solde total: <span class="number">
                            <?php
                            $total_balance = 0;
                            foreach ($banks as $bank) {
                                $total_balance += isset($bank->balance) ? $bank->balance : 0;
                            }
                            echo number_format($total_balance, 2, ',', ' ') . ' ' . $currency_symbol;
                            ?>
                        </span>
                    </div>
                    <div class="stats-badge">
                        <i class="fa fa-download"></i>
                        Exporter les données
                    </div>
                </div>

                <!-- Section d'exportation -->
                <div class="export-section-simulator">
                    <div class="export-title">Options d'exportation</div>
                    <div class="d-flex flex-wrap">
                        <button class="btn btn-outline-simulator mr-3 mb-2" onclick="exportAllBanksPDF()">
                            <i class="fa fa-file-pdf-o mr-2"></i> PDF
                        </button>
                        <button class="btn btn-outline-simulator mr-3 mb-2" onclick="exportAllBanksExcel()">
                            <i class="fa fa-file-excel-o mr-2"></i> Excel
                        </button>
                        <button class="btn btn-outline-simulator mr-3 mb-2" onclick="printBankList()">
                            <i class="fa fa-print mr-2"></i> Imprimer
                        </button>
                        <button class="btn btn-primary-simulator mb-2" data-toggle="modal" data-target="#bankModal">
                            <i class="fa fa-plus mr-2"></i> Nouvelle Banque
                        </button>
                    </div>
                </div>

                <!-- Grille des banques -->
                <h2 class="section-title">Banques Disponibles</h2>

                <?php if (!empty($banks)): ?>
                    <div class="banks-grid" id="banksGrid">
                        <?php foreach ($banks as $bank):
                            $balance = isset($bank->balance) ? $bank->balance : 0;
                            $logo_url = !empty($bank->logo) ? base_url($bank->logo) : '';
                            $bank_initial = strtoupper(substr($bank->name, 0, 2));
                            ?>
                            <div class="bank-card"
                                 data-bank-id="<?php echo $bank->id; ?>"
                                 data-bank-name="<?php echo htmlspecialchars($bank->name); ?>"
                                 data-bank-code="<?php echo htmlspecialchars($bank->code); ?>"
                                 data-bank-balance="<?php echo $balance; ?>"
                                 data-bank-logo="<?php echo $logo_url; ?>"
                                 data-bank-account="<?php echo htmlspecialchars($bank->account_number ?? ''); ?>">

                                <!-- Actions miniatures -->
                                <div class="bank-actions-mini">
                                    <button class="btn-action-mini btn-warning edit-bank-mini"
                                            data-id="<?php echo $bank->id; ?>"
                                            data-name="<?php echo htmlspecialchars($bank->name); ?>"
                                            data-code="<?php echo htmlspecialchars($bank->code); ?>"
                                            data-account_number="<?php echo htmlspecialchars($bank->account_number ?? ''); ?>"
                                            data-logo="<?php echo $logo_url; ?>"
                                            data-initial_balance="<?php echo $balance; ?>"
                                            title="Modifier">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn-action-mini btn-danger delete-bank-mini"
                                            data-id="<?php echo $bank->id; ?>"
                                            title="Supprimer">
                                        <i class="fa fa-trash"></i>
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
                                <div class="bank-info mt-3">
                                    <?php if (!empty($bank->account_number)): ?>
                                        <div><i class="fa fa-credit-card"></i> <?php echo htmlspecialchars($bank->account_number); ?></div>
                                    <?php endif; ?>
                                    <div>
                                        <i class="fa fa-money"></i>
                                        <span class="<?php echo $balance >= 0 ? 'text-success' : 'text-danger'; ?>">
                                            <?php echo number_format($balance, 2, ',', ' ') . ' ' . $currency_symbol; ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Boutons d'action -->
                                <div class="d-flex justify-content-center gap-2 mt-3">
                                    <button class="btn btn-sm btn-outline-simulator add-transaction-btn"
                                            data-bank-id="<?php echo $bank->id; ?>"
                                            data-bank-name="<?php echo htmlspecialchars($bank->name); ?>"
                                            title="Ajouter transaction">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                    <button class="btn btn-sm btn-primary-simulator view-transactions-btn"
                                            data-bank-id="<?php echo $bank->id; ?>"
                                            data-bank-name="<?php echo htmlspecialchars($bank->name); ?>"
                                            title="Voir transactions">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <div class="bank-logo-default-large mx-auto mb-4">
                            <i class="fa fa-university"></i>
                        </div>
                        <h3 class="mb-3">Aucune banque enregistrée</h3>
                        <p class="text-muted mb-4">Commencez par ajouter votre première banque</p>
                        <button class="btn btn-primary-simulator" data-toggle="modal" data-target="#bankModal">
                            <i class="fa fa-plus mr-2"></i> Créer une banque
                        </button>
                    </div>
                <?php endif; ?>

                <!-- Actions principales -->
                <div class="action-buttons">
                    <button type="button" class="btn btn-outline-simulator" id="refreshPageBtn">
                        <i class="fa fa-refresh mr-2"></i> Rafraîchir
                    </button>
                    <button type="button" class="btn btn-primary-simulator" data-toggle="modal" data-target="#transactionModal">
                        <i class="fa fa-plus-circle mr-2"></i> Nouvelle Transaction
                    </button>
                </div>

                <!-- Avertissement -->
                <div class="disclaimer">
                    <h6>Information importante</h6>
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
                <h4 class="modal-title" id="bankModalLabel">Nouvelle Banque</h4>
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

                            <label class="btn btn-outline-simulator upload-logo-btn">
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
                                    <span class="input-group-addon"><?php echo $currency_symbol; ?></span>
                                    <input type="number" id="initial_balance" name="initial_balance" class="form-control form-control-simulator"
                                           step="0.01" value="0.00" min="0" placeholder="0.00">
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
                    <h4 class="modal-title" id="transactionModalLabel">Nouvelle Transaction Bancaire</h4>
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
                                <label>Désignation *</label>
                                <select id="designation" name="designation" class="form-control form-control-simulator" required>
                                    <option value="">Sélectionner</option>
                                    <option value="Débit">Débit</option>
                                    <option value="Crédit">Crédit</option>
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
                                <label>Montant *</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><?php echo $currency_symbol; ?></span>
                                    <input id="amount" name="amount" type="number" step="0.01" class="form-control form-control-simulator" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-simulator">
                                <label>Numéro de référence</label>
                                <input id="reference" name="reference" type="text" class="form-control form-control-simulator"
                                       placeholder="N° chèque, virement, etc.">
                            </div>
                        </div>
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
                    <i class="fa fa-list"></i> Transactions de la banque : <span id="bankNameTitle"></span>
                </h4>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="bankTransactionsContent">
                    <!-- Contenu chargé dynamiquement -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-simulator" data-dismiss="modal">
                    <i class="fa fa-times"></i> Fermer
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
                    <i class="fa fa-exclamation-triangle"></i> Confirmation
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

<!-- Modal de confirmation suppression transaction -->
<div class="modal fade modal-simulator" id="deleteTransactionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">
                    <i class="fa fa-exclamation-triangle"></i> Confirmation
                </h4>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette transaction ?</p>
                <p class="text-warning">
                    <small><i class="fa fa-warning"></i> Cette action ne peut pas être annulée.</small>
                </p>
                <input type="hidden" id="transactionToDelete" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="confirmDeleteTransaction">
                    <i class="fa fa-trash"></i> Supprimer
                </button>
                <button type="button" class="btn btn-outline-simulator" data-dismiss="modal">
                    <i class="fa fa-times"></i> Annuler
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'édition de transaction -->
<div class="modal fade modal-simulator" id="editTransactionModal" tabindex="-1" role="dialog" aria-labelledby="editTransactionModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="editTransactionForm" action="<?php echo base_url() ?>admin/expense/update_transaction" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h4 class="modal-title" id="editTransactionModalLabel">Modifier la Transaction</h4>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <?php echo $this->customlib->getCSRF(); ?>
                    <input type="hidden" id="edit_transaction_id" name="transaction_id" value="">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-simulator">
                                <label>Banque</label>
                                <select id="edit_transaction_bank_id" name="bank_id" class="form-control form-control-simulator" required disabled>
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
                                <select id="edit_transaction_type" name="transaction_type" class="form-control form-control-simulator" required>
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
                                <label>Désignation *</label>
                                <select id="edit_designation" name="designation" class="form-control form-control-simulator" required>
                                    <option value="">Sélectionner</option>
                                    <option value="Débit">Débit</option>
                                    <option value="Crédit">Crédit</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-simulator">
                                <label>Date *</label>
                                <input id="edit_date" name="date" type="text" class="form-control form-control-simulator date" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-simulator">
                                <label>Libellé *</label>
                                <input id="edit_name" name="name" type="text" class="form-control form-control-simulator" required
                                       placeholder="Libellé de l'opération">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-simulator">
                                <label>Montant *</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><?php echo $currency_symbol; ?></span>
                                    <input id="edit_amount" name="amount" type="number" step="0.01" class="form-control form-control-simulator" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group-simulator">
                                <label>Numéro de référence</label>
                                <input id="edit_reference" name="reference" type="text" class="form-control form-control-simulator"
                                       placeholder="N° chèque, virement, etc.">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group-simulator">
                                <label>Mode de paiement</label>
                                <select id="edit_payment_mode" name="payment_mode" class="form-control form-control-simulator">
                                    <option value="">Sélectionner</option>
                                    <option value="Espèces">Espèces</option>
                                    <option value="Chèque">Chèque</option>
                                    <option value="Virement">Virement</option>
                                    <option value="Carte">Carte bancaire</option>
                                    <option value="Prélèvement">Prélèvement</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group-simulator">
                        <label>Description</label>
                        <textarea id="edit_description" name="description" class="form-control form-control-simulator" rows="3"
                                  placeholder="Détails de la transaction"></textarea>
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

<script>
    // Variables globales
    var currentBankId = null;
    var currentBankName = null;
    var transactionsTable = null;
    var allTransactions = [];

    // Fonction pour afficher les notifications
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
            (type == 'success' ? '#28a745' : type == 'error' ? '#dc3545' : type == 'warning' ? '#ffc107' : '#17a2b8') + ';">' +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close" style="position: absolute; top: 5px; right: 10px;">' +
            '<span aria-hidden="true">&times;</span>' +
            '</button>' +
            '<div class="d-flex align-items-start">' +
            '<div style="font-size: 20px; margin-right: 10px; margin-top: 2px;">' + icon + '</div>' +
            '<div style="flex: 1; padding-right: 20px;">' +
            '<strong style="display: block; margin-bottom: 2px; font-size: 14px;">' + title + '</strong>' +
            '<span style="font-size: 13px; color: #333;">' + message + '</span>' +
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

    // Fonction pour charger les transactions avec DataTable
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
                // Vérifier si c'est du HTML de tableau
                if (response.includes('<table') && response.includes('<tr>')) {
                    // Parser le HTML pour extraire les données
                    var tempDiv = document.createElement('div');
                    tempDiv.innerHTML = response;

                    var rows = tempDiv.querySelectorAll('tbody tr');
                    var tableData = [];
                    var totalTransactions = 0;

                    rows.forEach(function(row) {
                        var cells = row.querySelectorAll('td');
                        if (cells.length >= 5) {
                            var id = row.getAttribute('data-transaction-id') || '';
                            var date = cells[0]?.textContent || '';
                            var nom = cells[1]?.textContent || '';
                            var type = cells[2]?.textContent || '';
                            var montant = cells[3]?.textContent || '';
                            var reference = cells[4]?.textContent || '';
                            var description = cells[5]?.textContent || '';

                            tableData.push([
                                date,
                                nom,
                                type,
                                montant,
                                reference,
                                description,
                                id
                            ]);
                        }
                    });

                    // Récupérer le nombre total de transactions
                    var totalMatch = response.match(/Total: (\d+) transaction/);
                    if (totalMatch) {
                        totalTransactions = totalMatch[1];
                    }

                    // Afficher la DataTable
                    $('#bankTransactionsContent').html(`
                        <div class="table-responsive">
                            <table id="transactionsTable" class="table table-striped" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Nom</th>
                                        <th>Type</th>
                                        <th>Montant</th>
                                        <th>Référence</th>
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="total-transactions">
                            Total: ${totalTransactions} transaction(s)
                        </div>
                    `);

                    // Initialiser DataTable
                    if (transactionsTable) {
                        transactionsTable.destroy();
                    }

                    transactionsTable = $('#transactionsTable').DataTable({
                        "data": tableData,
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/French.json"
                        },
                        "pageLength": 10,
                        "order": [[0, 'desc']],
                        "dom": 'Bfrtip',
                        "buttons": [
                            {
                                extend: 'copy',
                                text: '<i class="fa fa-copy"></i> Copier',
                                className: 'btn btn-secondary'
                            },
                            {
                                extend: 'csv',
                                text: '<i class="fa fa-file-excel-o"></i> CSV',
                                className: 'btn btn-success',
                                filename: 'transactions_' + bankName + '_' + new Date().toISOString().split('T')[0]
                            },
                            {
                                extend: 'excel',
                                text: '<i class="fa fa-file-excel-o"></i> Excel',
                                className: 'btn btn-success',
                                filename: 'transactions_' + bankName + '_' + new Date().toISOString().split('T')[0]
                            },
                            {
                                extend: 'pdf',
                                text: '<i class="fa fa-file-pdf-o"></i> PDF',
                                className: 'btn btn-danger',
                                filename: 'transactions_' + bankName + '_' + new Date().toISOString().split('T')[0],
                                title: 'Transactions - ' + bankName,
                                orientation: 'landscape'
                            },
                            {
                                extend: 'print',
                                text: '<i class="fa fa-print"></i> Imprimer',
                                className: 'btn btn-info',
                                title: 'Transactions - ' + bankName
                            }
                        ],
                        "columnDefs": [
                            {
                                "targets": 3,
                                "render": function(data, type, row) {
                                    if (type === 'display' || type === 'filter') {
                                        var isCredit = data.includes('+') || parseFloat(data.replace(/[^\d.-]/g, '')) > 0;
                                        return '<span class="' + (isCredit ? 'badge-transaction badge-credit' : 'badge-transaction badge-debit') + '">' + data + '</span>';
                                    }
                                    return data;
                                }
                            },
                            {
                                "targets": 6,
                                "orderable": false,
                                "searchable": false,
                                "render": function(data, type, row) {
                                    if (type === 'display') {
                                        return `
                                            <div class="transaction-actions">
                                                <button class="btn-transaction-action btn-edit-transaction" data-id="${data}" title="Modifier">
                                                    <i class="fa fa-edit"></i>
                                                </button>
                                                <button class="btn-transaction-action btn-delete-transaction" data-id="${data}" title="Supprimer">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        `;
                                    }
                                    return data;
                                }
                            }
                        ],
                        "initComplete": function() {
                            // Attacher les événements après l'initialisation
                            attachTransactionEvents();
                        }
                    });

                    // Stocker toutes les transactions pour les exports
                    allTransactions = tableData;
                } else {
                    // Si ce n'est pas un tableau, afficher tel quel
                    $('#bankTransactionsContent').html(response);
                }
            },
            error: function(xhr, status, error) {
                $('#bankTransactionsContent').html(
                    '<div class="alert alert-danger m-3">' +
                    '<i class="fa fa-exclamation-circle"></i> ' +
                    'Erreur lors du chargement des transactions: ' + error +
                    '</div>'
                );
            }
        });
    }

    // Fonction pour attacher les événements aux transactions
    function attachTransactionEvents() {
        // Édition d'une transaction
        $(document).off('click', '.btn-edit-transaction').on('click', '.btn-edit-transaction', function() {
            var transactionId = $(this).data('id');
            loadTransactionData(transactionId);
        });

        // Suppression d'une transaction
        $(document).off('click', '.btn-delete-transaction').on('click', '.btn-delete-transaction', function() {
            var transactionId = $(this).data('id');
            $('#transactionToDelete').val(transactionId);
            $('#deleteTransactionModal').modal('show');
        });
    }

    // Fonction pour charger les données d'une transaction
    function loadTransactionData(transactionId) {
        $.ajax({
            url: '<?php echo base_url(); ?>admin/expense/get_transaction_data',
            type: 'POST',
            data: {
                '<?php echo $this->security->get_csrf_token_name(); ?>': '<?php echo $this->security->get_csrf_hash(); ?>',
                transaction_id: transactionId
            },
            dataType: 'json',
            success: function(response) {
                if (response.status == 'success') {
                    $('#edit_transaction_id').val(response.data.id);
                    $('#edit_transaction_bank_id').val(response.data.bank_id);
                    $('#edit_transaction_type').val(response.data.transaction_type);
                    $('#edit_designation').val(response.data.designation);
                    $('#edit_date').val(response.data.date);
                    $('#edit_name').val(response.data.name);
                    $('#edit_amount').val(response.data.amount);
                    $('#edit_reference').val(response.data.reference || '');
                    $('#edit_payment_mode').val(response.data.payment_mode || '');
                    $('#edit_description').val(response.data.description || '');

                    $('#editTransactionModal').modal('show');
                } else {
                    showToast('error', response.message);
                }
            },
            error: function(xhr, status, error) {
                showToast('error', 'Erreur lors du chargement des données: ' + error);
            }
        });
    }

    $(document).ready(function() {
        // Événement pour ajouter une transaction à une banque spécifique
        $(document).on('click', '.add-transaction-btn', function(e) {
            e.stopPropagation();
            var bankId = $(this).data('bank-id');
            var bankName = $(this).data('bank-name');

            $('#transaction_bank_id').val(bankId);
            $('#transactionModalLabel').html('Nouvelle Transaction - ' + bankName);
            $('#transactionModal').modal('show');
        });

        // Événement pour voir les transactions
        $(document).on('click', '.view-transactions-btn', function(e) {
            e.stopPropagation();
            var bankId = $(this).data('bank-id');
            var bankName = $(this).data('bank-name');

            loadBankTransactions(bankId, bankName);
        });

        // Événement pour éditer une banque
        $(document).on('click', '.edit-bank-mini', function(e) {
            e.stopPropagation();
            var id = $(this).data('id');
            var name = $(this).data('name');
            var code = $(this).data('code');
            var account_number = $(this).data('account_number');
            var logo = $(this).data('logo');
            var initial_balance = $(this).data('initial_balance');

            $('#bank_id_input').val(id);
            $('#bank_name').val(name);
            $('#bank_code').val(code);
            $('#account_number').val(account_number || '');
            $('#initial_balance').val(initial_balance || '0.00');

            if (logo) {
                $('#logoImage').attr('src', logo).show();
                $('#defaultLogo').hide();
            } else {
                $('#logoImage').hide();
                $('#defaultLogo').show();
                updateLogoInitials();
            }

            $('#bankModalLabel').text('Modifier la Banque');
            $('#bankModal').modal('show');
        });

        // Événement pour supprimer une banque
        $(document).on('click', '.delete-bank-mini', function(e) {
            e.stopPropagation();
            var bankId = $(this).data('id');
            var bankCard = $(this).closest('.bank-card');
            var bankName = bankCard.data('bank-name');
            var balance = bankCard.data('bank-balance') || 0;

            var warningMessage = '';
            if (parseFloat(balance) !== 0) {
                warningMessage = '<div class="alert alert-warning">';
                warningMessage += '<i class="fa fa-exclamation-triangle"></i> ';
                warningMessage += 'Attention: Cette banque a un solde de ' + balance.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + ' <?php echo $currency_symbol; ?>. ';
                warningMessage += 'La suppression effacera toutes les transactions associées.';
                warningMessage += '</div>';
            }

            $('#deleteWarningMessage').html(warningMessage);
            $('#deleteBankModal').data('bank-id', bankId);
            $('#deleteBankModal').modal('show');
        });

        // Confirmation de suppression de banque
        $('#confirmDelete').click(function() {
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
                    showToast('error', 'Erreur lors de la suppression: ' + error);
                },
                complete: function() {
                    deleteBtn.prop('disabled', false).html('<i class="fa fa-trash"></i> Supprimer');
                }
            });
        });

        // Confirmation de suppression de transaction
        $('#confirmDeleteTransaction').click(function() {
            var transactionId = $('#transactionToDelete').val();
            var deleteBtn = $(this);

            deleteBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Suppression...');

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
                        $('#deleteTransactionModal').modal('hide');
                        // Recharger les transactions
                        if (currentBankId) {
                            loadBankTransactions(currentBankId, currentBankName);
                        }
                    } else {
                        showToast('error', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    showToast('error', 'Erreur lors de la suppression: ' + error);
                },
                complete: function() {
                    deleteBtn.prop('disabled', false).html('<i class="fa fa-trash"></i> Supprimer');
                }
            });
        });

        // Gestion du formulaire d'édition de transaction
        $('#editTransactionForm').submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var submitBtn = form.find('button[type="submit"]');
            var modal = $('#editTransactionModal');

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
                        // Recharger les transactions
                        if (currentBankId) {
                            loadBankTransactions(currentBankId, currentBankName);
                        }
                    } else {
                        showToast('error', response.message || 'Erreur inconnue');
                    }
                },
                error: function(xhr, status, error) {
                    showToast('error', 'Erreur serveur: ' + (xhr.responseJSON?.message || error));
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Enregistrer');
                }
            });
        });

        // Gestion du formulaire des banques
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
                    showToast('error', 'Erreur serveur: ' + (xhr.responseJSON?.message || error));
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Enregistrer');
                }
            });
        });

        // Gestion du formulaire des transactions
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
                    showToast('error', 'Erreur serveur: ' + (xhr.responseJSON?.message || error));
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Enregistrer');
                }
            });
        });

        // Rafraîchir la page
        $('#refreshPageBtn').click(function() {
            location.reload();
        });

        // Gérer les erreurs de logo
        $('.bank-logo-large').on('error', function() {
            $(this).hide();
            $(this).next('.bank-logo-default-large').show();
        });

        // Réinitialiser les modals
        $('#bankModal').on('hidden.bs.modal', function () {
            $('#bankForm')[0].reset();
            $('#bank_id_input').val('');
            $('#bankModalLabel').text('Nouvelle Banque');
            $('#logoImage').hide();
            $('#defaultLogo').show();
            updateLogoInitials();
        });

        $('#transactionModal').on('hidden.bs.modal', function () {
            $('#transactionForm')[0].reset();
        });

        $('#editTransactionModal').on('hidden.bs.modal', function () {
            $('#editTransactionForm')[0].reset();
        });
    });

    // Fonction pour mettre à jour les initiales du logo
    function updateLogoInitials() {
        var name = $('#bank_name').val();
        var initials = name ? name.substring(0, 2).toUpperCase() : 'BN';
        $('#logoInitials').text(initials);
    }

    // Fonction de recherche des banques
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

    // Fonctions d'exportation
    function exportAllBanksPDF() {
        var banksData = [];
        $('.bank-card').each(function() {
            banksData.push({
                name: $(this).data('bank-name'),
                code: $(this).data('bank-code'),
                balance: $(this).data('bank-balance'),
                account: $(this).data('bank-account') || 'Non défini'
            });
        });

        var printWindow = window.open('', '_blank');
        var content = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Liste des Banques - Export PDF</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    h1 { color: #2c3e50; text-align: center; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
                    th { background-color: #f8f9fa; font-weight: bold; }
                    .total { margin-top: 30px; font-weight: bold; text-align: right; }
                    .header { text-align: center; margin-bottom: 30px; }
                    .date { color: #666; font-size: 12px; }
                    .positive { color: green; }
                    .negative { color: red; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>Liste des Banques</h1>
                    <div class="date">Exporté le ${new Date().toLocaleDateString('fr-FR')}</div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Nom de la Banque</th>
                            <th>Code</th>
                            <th>Numéro de Compte</th>
                            <th>Solde</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${banksData.map(bank => `
                            <tr>
                                <td>${bank.name}</td>
                                <td>${bank.code}</td>
                                <td>${bank.account}</td>
                                <td class="${bank.balance >= 0 ? 'positive' : 'negative'}">${bank.balance.toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})} <?php echo $currency_symbol; ?></td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>

                <div class="total">
                    <p>Total des banques : ${banksData.length}</p>
                    <p>Solde total : ${banksData.reduce((sum, bank) => sum + parseFloat(bank.balance), 0).toLocaleString('fr-FR', {minimumFractionDigits: 2, maximumFractionDigits: 2})} <?php echo $currency_symbol; ?></p>
                </div>
            </body>
            </html>
        `;

        printWindow.document.write(content);
        printWindow.document.close();

        setTimeout(function() {
            printWindow.print();
            printWindow.close();
        }, 500);
    }

    function exportAllBanksExcel() {
        var csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "Nom de la Banque,Code,Numéro de Compte,Solde\r\n";

        $('.bank-card').each(function() {
            var name = $(this).data('bank-name');
            var code = $(this).data('bank-code');
            var account = $(this).data('bank-account') || 'Non défini';
            var balance = $(this).data('bank-balance');

            csvContent += `"${name}","${code}","${account}","${balance}"\r\n`;
        });

        var totalBalance = 0;
        $('.bank-card').each(function() {
            totalBalance += parseFloat($(this).data('bank-balance'));
        });

        csvContent += `\r\nTotal Banques,${$('.bank-card').length}\r\n`;
        csvContent += `Solde Total,${totalBalance.toFixed(2)} <?php echo $currency_symbol; ?>\r\n`;

        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "banques_" + new Date().toISOString().split('T')[0] + ".csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function printBankList() {
        var originalContents = $('body').html();
        var printContents = $('.loan-simulator-card').html();

        $('body').html('<h1>Liste des Banques</h1>' + printContents +
            '<div style="text-align: center; margin-top: 20px; font-size: 12px; color: #666;">' +
            'Document généré le ' + new Date().toLocaleDateString('fr-FR') + ' ' + new Date().toLocaleTimeString('fr-FR') +
            '</div>');

        window.print();

        $('body').html(originalContents);
    }
</script>

<!-- Inclure DataTables CSS et JS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.min.css">
<script type="text/javascript" src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.print.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>