<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<style>

    /* Design blanc et chic inspiré de l'interface moderne */
    .content-wrapper {
        background: #f8fafc;
        min-height: 100vh;
        padding: 20px;
    }

    .loan-simulator-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        overflow: hidden;
        margin-bottom: 30px;
        border: 1px solid #f1f5f9;
    }

    .simulator-header {
        background: white;
        color: #1e293b;
        padding: 30px;
        text-align: center;
        position: relative;
        border-bottom: 1px solid #f1f5f9;
    }

    .simulator-header h1 {
        font-size: 28px;
        font-weight: 600;
        margin-bottom: 10px;
        color: #1e293b;
    }

    .simulator-header .subtitle {
        font-size: 16px;
        color: #64748b;
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
        background: white;
        border: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 16px;
        margin-bottom: 10px;
        transition: all 0.3s ease;
        color: #64748b;
    }

    .step.active .step-number {
        background: #1e293b;
        color: white;
        border-color: #1e293b;
        transform: scale(1.1);
    }

    .step-label {
        font-size: 14px;
        font-weight: 500;
        text-align: center;
        color: #94a3b8;
    }

    .step.active .step-label {
        color: #1e293b;
        font-weight: 600;
    }

    .steps-line {
        position: absolute;
        top: 20px;
        left: 50px;
        right: 50px;
        height: 2px;
        background: #e2e8f0;
        z-index: 1;
    }

    .simulator-body {
        padding: 40px;
    }

    .section-title {
        font-size: 22px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f1f5f9;
    }

    .banks-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 20px;
        margin: 30px 0;
    }

    .bank-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
        position: relative;
        overflow: hidden;
    }

    .bank-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
        background: white;
    }

    .bank-card.selected {
        border-color: #1e293b;
        background: white;
        box-shadow: 0 8px 25px rgba(30, 41, 59, 0.1);
    }

    .bank-logo-large {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        object-fit: contain;
        background: white;
        padding: 10px;
        margin: 0 auto 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        border: 1px solid #f1f5f9;
    }

    .bank-logo-default-large {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        background: #f8fafc;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 24px;
        margin: 0 auto 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        border: 1px solid #f1f5f9;
    }

    .bank-name {
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 5px;
    }

    .bank-code {
        font-size: 13px;
        color: #64748b;
        background: #f8fafc;
        padding: 4px 12px;
        border-radius: 12px;
        display: inline-block;
        border: 1px solid #e2e8f0;
    }

    .bank-info {
        text-align: left;
        margin-top: 10px;
        font-size: 13px;
        color: #64748b;
    }

    .bank-info i {
        margin-right: 5px;
        color: #64748b;
    }

    .action-buttons {
        display: flex;
        justify-content: space-between;
        margin-top: 40px;
        padding-top: 30px;
        border-top: 1px solid #f1f5f9;
    }

    .btn-simulator {
        padding: 12px 32px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-primary-simulator {
        background: #1e293b;
        color: white;
    }

    .btn-primary-simulator:hover {
        background: #334155;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(30, 41, 59, 0.15);
    }

    .btn-outline-simulator {
        background: transparent;
        border: 1px solid #cbd5e1;
        color: #475569;
    }

    .btn-outline-simulator:hover {
        background: #f8fafc;
        color: #1e293b;
        border-color: #94a3b8;
        transform: translateY(-2px);
    }

    .search-container-simulator {
        position: relative;
        margin-bottom: 30px;
    }

    .search-input-simulator {
        width: 100%;
        padding: 14px 20px 14px 48px;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 15px;
        transition: all 0.3s ease;
        background: white;
        color: #1e293b;
    }

    .search-input-simulator:focus {
        border-color: #94a3b8;
        box-shadow: 0 0 0 3px rgba(148, 163, 184, 0.1);
        outline: none;
    }

    .search-icon-simulator {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 16px;
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
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.2s ease;
        color: #64748b;
    }

    .btn-action-mini:hover {
        transform: scale(1.1);
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.12);
    }

    .export-section-simulator {
        background: #3B82F6;
        border-radius: 12px;
        padding: 24px;
        margin: 30px 0;
        border: 1px solid #e2e8f0;
    }

    .export-title {
        font-size: 16px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 20px;
    }


    .stats-badge {
        display: inline-flex;
        align-items: center;
        background: #f8fafc;
        padding: 10px 20px;
        border-radius: 25px;
        font-size: 14px;
        color: #475569;
        margin-right: 15px;
        margin-bottom: 10px;
        border: 1px solid #e2e8f0;
    }

    .stats-badge i {
        margin-right: 8px;
        color: #64748b;
    }

    .stats-badge .number {
        font-weight: 600;
        margin-left: 5px;
        color: #1e293b;
    }

    .modal-simulator .modal-content {
        border-radius: 16px;
        overflow: hidden;
        border: none;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        border: 1px solid #f1f5f9;
    }

    .modal-simulator .modal-header {
        background: white;
        color: #1e293b;
        border: none;
        padding: 24px;
        border-bottom: 1px solid #f1f5f9;
    }

    .modal-simulator .modal-body {
        padding: 30px;
        background: #f8fafc;
    }

    .form-group-simulator {
        margin-bottom: 25px;
    }

    .form-group-simulator label {
        font-weight: 600;
        color: #475569;
        margin-bottom: 8px;
        display: block;
        font-size: 14px;
    }

    .form-control-simulator {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 12px 15px;
        font-size: 15px;
        transition: all 0.3s ease;
        background: white;
        color: #1e293b;
    }

    .form-control-simulator:focus {
        border-color: #94a3b8;
        box-shadow: 0 0 0 3px rgba(148, 163, 184, 0.1);
    }

    .disclaimer {
        background: #f8fafc;
        border-radius: 10px;
        padding: 20px;
        margin-top: 30px;
        border-left: 4px solid #e2e8f0;
    }

    .disclaimer h6 {
        color: #1e293b;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .disclaimer p {
        color: #64748b;
        font-size: 14px;
        margin: 0;
        line-height: 1.6;
    }

    .transaction-item {
        background: white;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 10px;
        border-left: 4px solid #e2e8f0;
        transition: all 0.3s ease;
        border: 1px solid #f1f5f9;
    }

    .transaction-item:hover {
        background: #f8fafc;
        transform: translateX(5px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .transaction-item.credit {
        border-left-color: #22c55e;
    }

    .transaction-item.debit {
        border-left-color: #ef4444;
    }

    .transaction-date {
        font-size: 12px;
        color: #94a3b8;
        margin-bottom: 5px;
    }

    .transaction-amount {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 5px;
        color: #1e293b;
    }

    .transaction-description {
        font-size: 14px;
        color: #475569;
    }

    .transaction-actions {
        position: absolute;
        right: 15px;
        top: 15px;
        display: flex;
        gap: 5px;
    }

    .btn-transaction-action {
        width: 25px;
        height: 25px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        border: none;
        background: white;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
        transition: all 0.2s ease;
        color: #64748b;
    }

    .btn-transaction-action:hover {
        transform: scale(1.1);
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
    }

    .text-success {
        color: #22c55e !important;
    }

    .text-danger {
        color: #ef4444 !important;
    }

    .btn-warning {
        background: #f59e0b;
        color: white;
    }

    .btn-danger {
        background: #ef4444;
        color: white;
    }

    .text-center.py-5 {
        background: #f8fafc;
        border-radius: 12px;
        padding: 40px !important;
        border: 1px dashed #e2e8f0;
    }

    .text-center.py-5 h3 {
        color: #1e293b;
    }

    .text-center.py-5 p.text-muted {
        color: #94a3b8 !important;
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

        .stats-badge {
            margin-bottom: 10px;
            margin-right: 0;
            width: 100%;
            justify-content: center;
        }
    }
</style>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Simulateur Card -->
        <div class="loan-simulator-card">
            <!-- Header du simulateur -->

            <!--  <div class="simulator-header">
                <h1>Gestion des Banques</h1>
                <div class="subtitle">Gérez vos banques et transactions en toute transparence</div>


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
        </div>-->


            <!-- Corps du simulateur -->
            <div class="simulator-body" style="padding-top:0px">


                <!-- Section d'exportation -->
                <div class="export-section-simulator">
                    <div class="export-title" style="color: white">Banques Disponibles</div>
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
                    <div class="d-flex flex-wrap">

                        <button class="btn btn-outline-simulator mr-3 mb-2" onclick="exportAllBanksPDF()" style="background-color: white">
                            <i class="fa fa-file-pdf-o mr-2"></i> PDF
                        </button>
                        <button class="btn btn-outline-simulator mr-3 mb-2" onclick="exportAllBanksExcel()" style="background-color: white">
                            <i class="fa fa-file-excel-o mr-2"></i> Excel
                        </button>
                        <button class="btn btn-outline-simulator mr-3 mb-2" onclick="printBankList()" style="background-color: white">
                            <i class="fa fa-print mr-2"></i> Imprimer
                        </button>
                        <button class="btn btn-primary-simulator mb-2" data-toggle="modal" data-target="#bankModal">
                            <i class="fa fa-plus mr-2"></i> Nouvelle Banque
                        </button>
                        <div class="btn btn-outline-simulator mr-3 mb-2">
                            <i class="fa fa-search search-icon-simulator"></i>
                            <input type="text" class="search-input-simulator" id="searchBanks"
                                   placeholder="Rechercher une banque par nom, code ou numéro de compte...">
                        </div>
                    </div>
                </div>

                <!-- Grille des banques -->


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
                    <!--<button type="button" class="btn btn-primary-simulator" data-toggle="modal" data-target="#transactionModal">
                        <i class="fa fa-plus-circle mr-2"></i> Nouvelle Transaction
                    </button>-->
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

                        <!-- NOUVEAU CHAMP NOM AJOUTÉ ICI -->
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
                                    <span class="input-group-addon"><?php echo $currency_symbol; ?></span>
                                    <input id="amount" name="amount" type="number" step="0.01" class="form-control form-control-simulator" required>
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
                    <i class="fa fa-list"></i> Transaction de la banque : <span id="bankNameTitle"></span>
                </h4>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div id="bankTransactionsContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Chargement...</span>
                        </div>
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

<!-- Modal pour changer le logo -->
<div class="modal fade modal-simulator" id="changeLogoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Changer le logo</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="changeLogoForm" enctype="multipart/form-data">
                <div class="modal-body text-center">
                    <input type="hidden" name="bank_id" id="changeLogoBankId">
                    <?php echo $this->customlib->getCSRF(); ?>

                    <div id="currentLogoPreview" style="margin-bottom: 20px;">
                        <!-- Logo actuel -->
                    </div>

                    <div class="form-group">
                        <label class="btn btn-primary-simulator btn-block">
                            <i class="fa fa-folder-open"></i> Choisir une image
                            <input type="file" name="logo" id="newLogoInput" accept="image/*" style="display: none;" required>
                        </label>
                    </div>

                    <div id="newLogoPreview" style="display: none; margin-top: 15px;">
                        <img id="previewImage" src="" alt="" style="max-width: 100%; max-height: 150px; border-radius: 10px; border: 2px solid #e2e8f0;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary-simulator">
                        <i class="fa fa-save"></i> Enregistrer
                    </button>
                    <button type="button" class="btn btn-outline-simulator" data-dismiss="modal">Annuler</button>
                </div>
            </form>
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
       TOAST NOTIFICATION (version unifiée)
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
            (type == 'success' ? '#22c55e' : type == 'error' ? '#ef4444' : type == 'warning' ? '#f59e0b' : '#3b82f6') + ';">' +
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close" style="position: absolute; top: 5px; right: 10px;">' +
            '<span aria-hidden="true">&times;</span>' +
            '</button>' +
            '<div class="d-flex align-items-start">' +
            '<div style="font-size: 20px; margin-right: 10px; margin-top: 2px;">' + icon + '</div>' +
            '<div style="flex: 1; padding-right: 20px;">' +
            '<strong style="display: block; margin-bottom: 2px; font-size: 14px;">' + title + '</strong>' +
            '<span style="font-size: 13px; color: #1e293b;">' + message + '</span>' +
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
            $('#transactionModalLabel').html('Nouvelle Transaction - ' + bankName);
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

        // Supprimer une banque
        $('.delete-bank-mini').click(function(e) {
            e.stopPropagation();
            var bankId = $(this).data('id');
            var bankCard = $(this).closest('.bank-card');
            var bankName = bankCard.data('bank-name');
            var balance = bankCard.data('bank-balance') || 0;

            var warningMessage = '';
            if (parseFloat(balance) !== 0) {
                warningMessage = '<div class="alert alert-warning">';
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
        // Gestionnaire d'événement pour la suppression de transaction
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

                        // Animer la suppression
                        transactionItem.fadeOut(300, function() {
                            $(this).remove();

                            // Rafraîchir la liste si vide
                            if ($('#viewTransactionsModal tbody tr').length <= 1) {
                                loadBankTransactions(currentBankId, currentBankName);
                            }

                            // Rafraîchir la page principale après 500ms
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
        // Gestionnaire d'événement pour l'édition de transaction
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
                        // Remplir le formulaire avec les données
                        var data = response.data;

                        // Supprimer l'ancien champ caché s'il existe
                        $('input[name="transaction_id"]').remove();

                        // Réinitialiser et remplir le formulaire
                        $('#transactionForm')[0].reset();

                        // Ajouter le champ caché pour l'édition
                        $('#transactionForm').append(
                            '<input type="hidden" name="transaction_id" value="' + data.id + '">'
                        );

                        // Remplir les champs
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

                        // Changer le titre du modal
                        $('#transactionModalLabel').text('Modifier Transaction');

                        // Cacher le modal des transactions et montrer le modal d'édition
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
            $('#bankModalLabel').text('Nouvelle Banque');
            $('#logoImage').hide();
            $('#defaultLogo').show();
            updateLogoInitials();
        });

        $('#transactionModal').on('hidden.bs.modal', function() {
            $('input[name="transaction_id"]').remove();
            $('#transactionForm')[0].reset();
            $('#transactionModalLabel').text('Nouvelle Transaction');
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

    // Fonction pour exporter toutes les banques en PDF (avec données réelles)
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

        var totalBalance = banksData.reduce((sum, bank) => sum + parseFloat(bank.balance), 0);

        var printWindow = window.open('', '_blank');
        var content = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Liste des Banques - Export PDF</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 20px;
                    background: #f8fafc;
                }

                .pdf-container {
                    max-width: 1000px;
                    margin: 0 auto;
                    background: white;
                    padding: 30px;
                    border-radius: 15px;
                    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
                    border: 1px solid #f1f5f9;
                }

                h1 {
                    color: #1e293b;
                    text-align: center;
                    margin-bottom: 10px;
                }

                .pdf-subtitle {
                    text-align: center;
                    color: #64748b;
                    margin-bottom: 20px;
                    font-size: 16px;
                }

                .pdf-date {
                    text-align: center;
                    color: #94a3b8;
                    margin-bottom: 30px;
                    font-size: 14px;
                    background: #f8fafc;
                    padding: 8px;
                    border-radius: 10px;
                    display: inline-block;
                    margin: 0 auto 30px;
                    display: block;
                    width: fit-content;
                    border: 1px solid #e2e8f0;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 0 3px 15px rgba(0,0,0,0.08);
                    border: 1px solid #f1f5f9;
                }

                th {
                    background: #1e293b;
                    color: white;
                    padding: 15px;
                    text-align: left;
                    font-weight: bold;
                    font-size: 14px;
                }

                th:first-child { border-top-left-radius: 10px; }
                th:last-child { border-top-right-radius: 10px; }

                td {
                    padding: 12px 15px;
                    border-bottom: 1px solid #f1f5f5;
                    text-align: left;
                    font-size: 14px;
                }

                tr:nth-child(even) {
                    background-color: #fafafa;
                }

                tr:hover {
                    background-color: #f8fafc;
                }

                .total-section {
                    margin-top: 40px;
                    padding: 20px;
                    background: #f8fafc;
                    border-radius: 10px;
                    text-align: right;
                    border: 1px solid #e2e8f0;
                }

                .total-item {
                    margin-bottom: 10px;
                    font-size: 16px;
                }

                .total-amount {
                    font-size: 20px;
                    font-weight: bold;
                    color: #1e293b;
                }

                .balance-positive {
                    color: #22c55e;
                    font-weight: 600;
                }

                .balance-negative {
                    color: #ef4444;
                    font-weight: 600;
                }

                .footer {
                    margin-top: 40px;
                    text-align: center;
                    color: #64748b;
                    font-size: 12px;
                    padding-top: 20px;
                    border-top: 1px solid #e2e8f0;
                }
            </style>
        </head>
        <body>
            <div class="pdf-container">
                <h1>LISTE DES BANQUES</h1>
                <div class="pdf-subtitle">Gestion des comptes bancaires</div>
                <div class="pdf-date">Exporté le ${new Date().toLocaleDateString('fr-FR', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        })}</div>

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
                                <td><strong>${bank.name}</strong></td>
                                <td><strong>${bank.code}</strong></td>
                                <td>${bank.account}</td>
                                <td class="${bank.balance >= 0 ? 'balance-positive' : 'balance-negative'}">
                                    ${formatNumber(bank.balance)} <?php echo $currency_symbol; ?>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>

                <div class="total-section">
                    <div class="total-item">
                        <strong>Total des banques :</strong> ${banksData.length}
                    </div>
                    <div class="total-item">
                        <strong>Solde total :</strong>
                        <span class="total-amount ${totalBalance >= 0 ? 'balance-positive' : 'balance-negative'}">
                            ${formatNumber(totalBalance)} <?php echo $currency_symbol; ?>
                        </span>
                    </div>
                </div>

                <div class="footer">
                    <p>Document généré automatiquement par le système de gestion bancaire</p>
                    <p>Les données sont basées sur les informations enregistrées au moment de l'export</p>
                </div>
            </div>
        </body>
        </html>
    `;

        printWindow.document.write(content);
        printWindow.document.close();

        setTimeout(function() {
            printWindow.print();
            setTimeout(function() {
                printWindow.close();
            }, 1000);
        }, 1000);
    }

    // Fonction pour exporter toutes les banques en Excel (avec données réelles)
    function exportAllBanksExcel() {
        var csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "Nom de la Banque,Code,Numéro de Compte,Solde (<?php echo $currency_symbol; ?>),Date d'export\r\n";

        $('.bank-card').each(function() {
            var name = $(this).data('bank-name');
            var code = $(this).data('bank-code');
            var account = $(this).data('bank-account') || 'Non défini';
            var balance = $(this).data('bank-balance');

            csvContent += `"${name}","${code}","${account}","${balance}","${new Date().toLocaleDateString('fr-FR')}"\r\n`;
        });

        var totalBalance = 0;
        $('.bank-card').each(function() {
            totalBalance += parseFloat($(this).data('bank-balance'));
        });

        csvContent += `\r\n`;
        csvContent += `Résumé,,,,\r\n`;
        csvContent += `"Total des banques","${$('.bank-card').length}",,,\r\n`;
        csvContent += `"Solde total","${totalBalance.toFixed(2)} <?php echo $currency_symbol; ?>",,,\r\n`;
        csvContent += `"Date d'export","${new Date().toLocaleDateString('fr-FR')}",,,\r\n`;

        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "banques_export_" + new Date().toISOString().split('T')[0] + ".csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        showToast('success', 'Export Excel terminé avec succès');
    }

    // Fonction pour exporter les transactions d'une banque en PDF
    function exportBankTransactionsPDF() {
        if (!currentBankId) {
            showToast('error', 'Aucune banque sélectionnée');
            return;
        }

        // Récupérer les données des transactions
        var transactionsData = [];
        var totalAmount = 0;
        var creditTotal = 0;
        var debitTotal = 0;

        // Méthode 1: Parcourir les lignes du tableau
        $('#viewTransactionsModal table tbody tr').each(function() {
            var cells = $(this).find('td');
            if (cells.length >= 6) {
                var date = $(cells[0]).text() || '';
                var nom = $(cells[1]).text() || '';
                var libelle = $(cells[2]).text() || '';
                var type = $(cells[3]).text() || '';
                var designation = $(cells[4]).find('.badge').text() || $(cells[4]).text() || '';
                var amountText = $(cells[5]).text() || '';
                var reference = cells.length > 6 ? $(cells[6]).text() || '' : '';

                // Extraire le montant numérique
                var amountMatch = amountText.match(/[+-]?[\d\s,]+/);
                var amount = 0;
                if (amountMatch) {
                    amount = parseFloat(amountMatch[0].replace(/\s/g, '').replace(',', '.'));

                    // Déterminer si c'est un crédit ou débit
                    var isCredit = amountText.includes('+') || designation === 'Crédit' ||
                        ['Dépôt', 'Virement entrant'].includes(type);

                    if (isCredit) {
                        creditTotal += amount;
                    } else {
                        debitTotal += amount;
                        amount = -amount; // Inverser le signe pour les totaux
                    }
                    totalAmount += amount;
                }

                transactionsData.push({
                    date: date,
                    nom: nom,
                    libelle: libelle,
                    type: type,
                    designation: designation,
                    amount: amountText,
                    amountValue: amount,
                    reference: reference
                });
            }
        });

        // Si aucune transaction trouvée
        if (transactionsData.length === 0) {
            // Essayer une autre méthode
            $('.transaction-item').each(function() {
                var date = $(this).find('.transaction-date').text() || '';
                var nom = $(this).find('.transaction-nom').text() || '';
                var libelle = $(this).find('.transaction-description').text() || '';
                var type = $(this).find('.transaction-type').text() || '';
                var designation = $(this).find('.badge').text() || '';
                var amount = $(this).find('.transaction-amount').text() || '';
                var reference = $(this).find('td:nth-child(7)').text() || '';

                transactionsData.push({
                    date: date,
                    nom: nom,
                    libelle: libelle,
                    type: type,
                    designation: designation,
                    amount: amount,
                    reference: reference
                });
            });
        }

        var printWindow = window.open('', '_blank');
        var content = `
    <!DOCTYPE html>
    <html>
    <head>
        <title>Transactions Bancaires - ${currentBankName}</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 20px;
                background: #f8fafc;
            }
            thead {
    background-color: #0ea5e9; /* BLEU CIEL */
     background-color: #0ea5e9;
    color: black;
    padding: 14px 15px;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 3px solid #0369a1;
}

thead th {
    background-color: #0ea5e9;
    color: black;
    padding: 14px 15px;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 3px solid #0369a1;
}


            .pdf-container {
                max-width: 1000px;
                margin: 0 auto;
                background: white;
                padding: 30px;
                border-radius: 15px;
                box-shadow: 0 5px 20px rgba(0,0,0,0.1);
                border: 1px solid #f1f5f9;
            }

            h1 {
                color: #1e293b;
                text-align: center;
                margin-bottom: 10px;
            }

            h2 {
                color: #64748b;
                text-align: center;
                font-size: 18px;
                margin-bottom: 5px;
                font-weight: normal;
            }

            .pdf-date {
                text-align: center;
                color: #94a3b8;
                margin-bottom: 30px;
                font-size: 14px;
                background: #f8fafc;
                padding: 8px;
                border-radius: 10px;
                display: inline-block;
                margin: 0 auto 30px;
                display: block;
                width: fit-content;
                border: 1px solid #e2e8f0;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 3px 15px rgba(0,0,0,0.08);
                border: 1px solid #f1f5f9;
            }


            th {
                background: #1e293b;
                color: white;
                padding: 15px;
                text-align: left;
                font-weight: bold;
                font-size: 14px;
            }

            th:first-child { border-top-left-radius: 10px; }
            th:last-child { border-top-right-radius: 10px; }

            td {
                padding: 12px 15px;
                border-bottom: 1px solid #f1f5f5;
                text-align: left;
                font-size: 13px;
            }

            tr:nth-child(even) {
                background-color: #fafafa;
            }

            .amount-credit {
                color: #22c55e;
                font-weight: 600;
            }

            .amount-debit {
                color: #ef4444;
                font-weight: 600;
            }

            .summary-section {
                margin-top: 40px;
                padding: 25px;
                background: #f8fafc;
                border-radius: 10px;
                border: 1px solid #e2e8f0;
            }

            .summary-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
                margin-top: 20px;
            }

            .summary-item {
                background: white;
                padding: 20px;
                border-radius: 8px;
                border: 1px solid #e2e8f0;
                text-align: center;
            }

            .summary-label {
                font-size: 14px;
                color: #64748b;
                margin-bottom: 5px;
            }

            .summary-value {
                font-size: 24px;
                font-weight: bold;
                color: #1e293b;
            }

            .summary-value.credit {
                color: #22c55e;
            }

            .summary-value.debit {
                color: #ef4444;
            }

            .summary-value.total {
                color: #3b82f6;
                font-size: 28px;
            }

            .footer {
                margin-top: 40px;
                text-align: center;
                color: #64748b;
                font-size: 12px;
                padding-top: 20px;
                border-top: 1px solid #e2e8f0;
            }
        </style>
    </head>
    <body>
        <div class="pdf-container">
            <h1>TRANSACTIONS BANCAIRES</h1>
            <h2>Banque : ${currentBankName}</h2>
            <div class="pdf-date">Exporté le ${new Date().toLocaleDateString('fr-FR', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        })}</div>

            <table class="table-operations">
                <thead>
                    <tr>
                     <th>Date</th>
                      <th>Nom</th>
                       <th>Libéllé</th>

                        <th>Type</th>
                        <th>Désignation</th>
                        <th>Montant</th>

                    </tr>
                </thead>
                <tbody>
                    ${transactionsData.map(trans => `
                        <tr>
                            <td>${trans.date || ''}</td>
                            <td>${trans.nom || ''}</td>
                             <td>${trans.libelle || ''}</td>
                            <td>${trans.type || ''}</td>
                             <td>${trans.designation || ''}</td>



                            <td class="${trans.amount.includes('+') || trans.designation === 'Crédit' ? 'amount-credit' : 'amount-debit'}">
                                ${trans.amount || ''}
                            </td>

                        </tr>
                    `).join('')}
                </tbody>
            </table>

            <div class="summary-section">
                <h3 style="color: #1e293b; margin-bottom: 20px; text-align: center;">RÉSUMÉ DES TRANSACTIONS</h3>
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-label">Nombre total</div>
                        <div class="summary-value">${transactionsData.length}</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Total Crédits</div>
                        <div class="summary-value credit">${formatNumber(creditTotal)} <?php echo $currency_symbol; ?></div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Total Débits</div>
                        <div class="summary-value debit">${formatNumber(debitTotal)} <?php echo $currency_symbol; ?></div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Solde Net</div>
                        <div class="summary-value total ${totalAmount >= 0 ? 'credit' : 'debit'}">
                            ${formatNumber(totalAmount)} <?php echo $currency_symbol; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="footer">
                <p>Document généré automatiquement par le système de gestion bancaire</p>
                <p>Les données sont basées sur les informations enregistrées au moment de l'export</p>
            </div>
        </div>
    </body>
    </html>
`;

        printWindow.document.write(content);
        printWindow.document.close();

        setTimeout(function() {
            printWindow.print();
            setTimeout(function() {
                printWindow.close();
            }, 1000);
        }, 1000);
    }

    // Fonction pour exporter les transactions d'une banque en Excel
    function exportBankTransactionsExcel() {
        if (!currentBankId) {
            showToast('error', 'Aucune banque sélectionnée');
            return;
        }

        var csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "Date,Nom,Libellé,Type,Désignation,Montant,Référence,Banque,Date d'export\r\n";

        var transactionsCount = 0;
        var creditTotal = 0;
        var debitTotal = 0;
        var netTotal = 0;

        // Collecter les données du tableau
        $('#viewTransactionsModal table tbody tr').each(function() {
            var cells = $(this).find('td');
            if (cells.length >= 6) {
                var date = $(cells[0]).text() || '';
                var nom = $(cells[1]).text() || '';
                var libelle = $(cells[2]).text() || '';
                var type = $(cells[3]).text() || '';
                var designation = $(cells[4]).find('.badge').text() || $(cells[4]).text() || '';
                var amountText = $(cells[5]).text() || '';
                var reference = cells.length > 6 ? $(cells[6]).text() || '' : '';

                // Calculer les totaux
                var amountMatch = amountText.match(/[+-]?[\d\s,]+/);
                if (amountMatch) {
                    var amount = parseFloat(amountMatch[0].replace(/\s/g, '').replace(',', '.'));
                    var isCredit = amountText.includes('+') || designation === 'Crédit' ||
                        ['Dépôt', 'Virement entrant'].includes(type);

                    if (isCredit) {
                        creditTotal += amount;
                    } else {
                        debitTotal += amount;
                    }
                    netTotal += isCredit ? amount : -amount;
                }

                csvContent += `"${date}","${nom}","${libelle}","${type}","${designation}","${amountText}","${reference}","${currentBankName}","${new Date().toLocaleDateString('fr-FR')}"\r\n`;
                transactionsCount++;
            }
        });

        // Ajouter les totaux
        csvContent += `\r\n`;
        csvContent += `RÉSUMÉ,,,,,,,,\r\n`;
        csvContent += `"Nombre total de transactions","${transactionsCount}",,,,,,,\r\n`;
        csvContent += `"Total Crédits","${creditTotal.toFixed(2)} <?php echo $currency_symbol; ?>",,,,,,,\r\n`;
        csvContent += `"Total Débits","${debitTotal.toFixed(2)} <?php echo $currency_symbol; ?>",,,,,,,\r\n`;
        csvContent += `"Solde Net","${netTotal.toFixed(2)} <?php echo $currency_symbol; ?>",,,,,,,\r\n`;
        csvContent += `"Date d'export","${new Date().toLocaleDateString('fr-FR')}",,,,,,,\r\n`;

        var encodedUri = encodeURI(csvContent);
        var link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        var filename = "transactions_" + currentBankName.replace(/[^a-z0-9]/gi, '_') + "_" + new Date().toISOString().split('T')[0] + ".csv";
        link.setAttribute("download", filename);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        showToast('success', 'Export Excel des transactions terminé avec succès');
    }

    // Fonction pour imprimer la liste des banques
    function printBankList() {
        var banksData = [];
        $('.bank-card').each(function() {
            banksData.push({
                name: $(this).data('bank-name'),
                code: $(this).data('bank-code'),
                balance: $(this).data('bank-balance'),
                account: $(this).data('bank-account') || 'Non défini'
            });
        });

        var totalBalance = banksData.reduce((sum, bank) => sum + parseFloat(bank.balance), 0);

        var printContent = `
        <html>
        <head>
            <title>Liste des Banques</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; background: #f8fafc; }
                h1 { text-align: center; color: #1e293b; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; border: 1px solid #e2e8f0; }
                th, td { border: 1px solid #e2e8f0; padding: 10px; text-align: left; }
                th { background-color: #1e293b; color: white; }
                tr:nth-child(even) { background-color: #f8fafc; }
                .total { margin-top: 30px; font-weight: bold; padding: 15px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; }
            </style>
        </head>
        <body>
            <h1>Liste des Banques</h1>
            <p style="text-align: center; color: #64748b;">Document généré le ${new Date().toLocaleDateString('fr-FR')}</p>

            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Code</th>
                        <th>Compte</th>
                        <th>Solde</th>
                    </tr>
                </thead>
                <tbody>
                    ${banksData.map(bank => `
                        <tr>
                            <td>${bank.name}</td>
                            <td>${bank.code}</td>
                            <td>${bank.account}</td>
                            <td>${formatNumber(bank.balance)} <?php echo $currency_symbol; ?></td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>

            <div class="total">
                <p>Total banques: ${banksData.length}</p>
                <p>Solde total: ${formatNumber(totalBalance)} <?php echo $currency_symbol; ?></p>
            </div>
        </body>
        </html>
    `;

        var printWindow = window.open('', '_blank');
        printWindow.document.write(printContent);
        printWindow.document.close();

        setTimeout(function() {
            printWindow.print();
            setTimeout(function() {
                printWindow.close();
            }, 500);
        }, 500);
    }

    /* =========================
       FONCTIONS D'EXPORT
       (gardez les fonctions existantes mais assurez-vous
       qu'elles utilisent la bonne version de showToast)
    ========================= */
    // Les fonctions exportAllBanksPDF, exportAllBanksExcel,
    // exportBankTransactionsPDF, exportBankTransactionsExcel,
    // printBankList restent inchangées

    // Fonction pour initialiser les filtres du tableau des transactions
    function initTransactionTableFilters() {
        console.log('Initialisation des filtres de transaction...');

        // Attendre que le contenu soit chargé
        setTimeout(function() {
            const table = document.getElementById('transactionsTable');
            const tableBody = document.getElementById('transactionsBody');
            const resetBtn = document.getElementById('resetFiltersBtn');
            const transactionCount = document.getElementById('transactionCount');

            if (!table || !tableBody) {
                console.log('Table des transactions non trouvée');
                return;
            }

            console.log('Table trouvée, lignes:', tableBody.querySelectorAll('tr').length);

            // Ajouter les événements aux filtres
            const filterInputs = table.querySelectorAll('.filter-input');
            filterInputs.forEach(input => {
                input.addEventListener('input', filterTransactionTable);
                input.addEventListener('change', filterTransactionTable);
            });

            // Bouton réinitialiser
            if (resetBtn) {
                resetBtn.addEventListener('click', function() {
                    filterInputs.forEach(input => {
                        input.value = '';
                    });
                    filterTransactionTable();
                    this.style.display = 'none';
                });
            }

            // Fonction de filtrage
            function filterTransactionTable() {
                const rows = tableBody.querySelectorAll('tr');
                let hasActiveFilters = false;
                let visibleCount = 0;

                // Vérifier les filtres actifs
                filterInputs.forEach(input => {
                    if (input.value.trim() !== '') {
                        hasActiveFilters = true;
                    }
                });

                // Afficher/masquer bouton réinitialiser
                if (resetBtn) {
                    resetBtn.style.display = hasActiveFilters ? 'inline-block' : 'none';
                }

                // Filtrer chaque ligne
                rows.forEach(row => {
                    let showRow = true;
                    const cells = row.querySelectorAll('td');

                    filterInputs.forEach(input => {
                        const columnIndex = parseInt(input.getAttribute('data-column'));
                        const filterValue = input.value.trim().toLowerCase();

                        if (filterValue !== '' && cells[columnIndex]) {
                            let cellText = '';

                            // Récupérer le texte selon la colonne
                            if (columnIndex === 4) { // Désignation (avec badge)
                                const badge = cells[columnIndex].querySelector('.badge');
                                cellText = badge ? badge.textContent.trim().toLowerCase() : '';
                            } else if (columnIndex === 5) { // Montant
                                cellText = cells[columnIndex].textContent.trim().toLowerCase();
                                // Nettoyer le texte pour la recherche
                                cellText = cellText.replace('fcfa', '').trim();
                            } else {
                                cellText = cells[columnIndex].textContent.trim().toLowerCase();
                            }

                            // Appliquer le filtre
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

                    // Afficher/masquer la ligne
                    if (showRow) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // Mettre à jour le compteur
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

            // Initialiser le compteur
            if (transactionCount && tableBody) {
                const totalRows = tableBody.querySelectorAll('tr').length;
                transactionCount.textContent = 'Total: ' + totalRows + '     transaction(s)';
            }

        }, 300); // Petit délai pour être sûr que le HTML est chargé
    }

    // Modifier la fonction loadBankTransactions pour appeler initTransactionTableFilters
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

                // Initialiser les filtres après le chargement
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
</script>


<script>
    $('#transactionForm').on('submit', function () {
        // On laisse le formulaire se soumettre normalement
        setTimeout(function () {
            showToast('success', 'Transaction enregistrée avec succès');

            // Rafraîchir la page après 1,5 seconde
            setTimeout(function () {
                location.reload();
            }, 1500);

        }, 500);
    });


</script>








