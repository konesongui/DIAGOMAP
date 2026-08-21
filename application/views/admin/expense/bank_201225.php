<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>
<style>
    .bank-logo {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: contain;
        background: #f8f9fa;
        padding: 5px;
        border: 1px solid #ddd;
        margin-right: 10px;
    }

    .bank-logo-default {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 16px;
        margin-right: 10px;
    }

    .bank-name-with-logo {
        display: flex;
        align-items: center;
    }

    .bank-actions {
        display: flex;
        gap: 5px;
        flex-wrap: nowrap;
    }

    .upload-logo-btn {
        position: relative;
        overflow: hidden;
        display: inline-block;
    }

    .upload-logo-btn input[type=file] {
        position: absolute;
        left: 0;
        top: 0;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }

    /* Correction de la balise style fermée */
    .btn-group-xs .btn {
        padding: 2px 6px;
        font-size: 11px;
        margin-right: 2px;
    }

    .add-transaction-btn, .view-transactions-btn {
        transition: all 0.3s ease;
    }

    .add-transaction-btn:hover {
        background-color: #28a745;
        transform: scale(1.1);
    }

    .view-transactions-btn:hover {
        background-color: #17a2b8;
        transform: scale(1.1);
    }

    #bankTable tbody tr {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    #bankTable tbody tr:hover {
        background-color: #f5f5f5;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .badge-success {
        background-color: #28a745;
    }

    .badge-danger {
        background-color: #dc3545;
    }

    .modal-xl {
        max-width: 95%;
    }

    .total-box {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
        border-left: 4px solid #3498db;
    }

    .total-amount {
        font-size: 24px;
        font-weight: bold;
        color: #2c3e50;
    }

    .total-label {
        font-size: 14px;
        color: #7f8c8d;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .export-section {
        background-color: #e8f4f8;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
    }
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-university"></i>
            Gestion des Banques
            <small>Transactions et paramétrage</small>
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <!-- Colonne pour la liste des banques -->
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-bank"></i> Liste des Banques
                            <?php if (!empty($banks)): ?>
                                <span class="badge badge-info"><?php echo count($banks); ?></span>
                            <?php endif; ?>
                        </h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#bankModal">
                                <i class="fa fa-plus"></i> Nouvelle Banque
                            </button>
                            <button type="button" class="btn btn-primary btn-sm" id="refreshPageBtn" title="Rafraîchir la page (F5)">
                                <i class="fa fa-refresh"></i>
                                <span class="badge badge-light" id="refreshCounter" style="position: absolute; top: -5px; right: -5px; display: none;"></span>
                            </button>
                        </div>
                    </div>
                    <div class="box-body" style="max-height: 600px; overflow-y: auto;">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover" id="bankTable">
                                <thead>
                                <tr>
                                    <th width="35%">Nom</th>
                                    <th width="25%">Code</th>
                                    <th width="20%" class="text-center">Solde</th>
                                    <th width="20%" class="text-center">Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($banks)): ?>
                                    <?php foreach ($banks as $bank):
                                        $balance = isset($bank->balance) ? $bank->balance : 0;
                                        $logo_url = !empty($bank->logo) ? base_url($bank->logo) : '';
                                        $bank_initial = strtoupper(substr($bank->name, 0, 2));
                                        ?>
                                        <tr data-bank-id="<?php echo $bank->id; ?>"
                                            data-bank-name="<?php echo htmlspecialchars($bank->name); ?>"
                                            data-bank-code="<?php echo htmlspecialchars($bank->code); ?>"
                                            data-bank-balance="<?php echo $balance; ?>"
                                            data-bank-logo="<?php echo $logo_url; ?>">
                                            <td>
                                                <div class="bank-name-with-logo">
                                                    <?php if ($logo_url): ?>
                                                        <img src="<?php echo $logo_url; ?>"
                                                             alt="<?php echo htmlspecialchars($bank->name); ?>"
                                                             class="bank-logo"
                                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                        <div class="bank-logo-default" style="display: none;">
                                                            <?php echo $bank_initial; ?>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="bank-logo-default">
                                                            <?php echo $bank_initial; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <strong style="color: #3498db;"><?php echo htmlspecialchars($bank->name); ?></strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="fa fa-hashtag"></i> <?php echo htmlspecialchars($bank->code); ?>
                                                        </small>
                                                        <?php if (!empty($bank->account_number)): ?>
                                                            <br>
                                                            <small class="text-muted">
                                                                <i class="fa fa-credit-card"></i> <?php echo htmlspecialchars($bank->account_number); ?>
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="label label-primary"><?php echo htmlspecialchars($bank->code); ?></span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge <?php echo $balance >= 0 ? 'badge-success' : 'badge-danger'; ?>"
                                                      style="font-size: 12px; padding: 5px 10px;">
                                                    <?php echo number_format($balance, 2, ',', ' ') . ' ' . $currency_symbol; ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div class="bank-actions">
                                                    <!-- Bouton pour changer le logo -->
                                                    <button class="btn btn-sm btn-outline-secondary change-logo-btn"
                                                            data-bank-id="<?php echo $bank->id; ?>"
                                                            data-bank-name="<?php echo htmlspecialchars($bank->name); ?>"
                                                            title="Changer le logo">
                                                        <i class="fa fa-image"></i>
                                                    </button>

                                                    <!-- Bouton pour ajouter une transaction -->
                                                    <button class="btn btn-sm btn-success add-transaction-btn"
                                                            data-bank-id="<?php echo $bank->id; ?>"
                                                            data-bank-name="<?php echo htmlspecialchars($bank->name); ?>"
                                                            title="Ajouter transaction">
                                                        <i class="fa fa-plus-circle"></i>
                                                    </button>

                                                    <!-- Bouton pour voir les détails -->
                                                    <button class="btn btn-sm btn-info view-transactions-btn"
                                                            data-bank-id="<?php echo $bank->id; ?>"
                                                            data-bank-name="<?php echo htmlspecialchars($bank->name); ?>"
                                                            title="Voir transactions">
                                                        <i class="fa fa-eye"></i>
                                                    </button>

                                                    <!-- Bouton rafraîchir cette banque -->
                                                    <button class="btn btn-sm btn-primary refresh-bank-btn"
                                                            data-bank-id="<?php echo $bank->id; ?>"
                                                            data-bank-name="<?php echo htmlspecialchars($bank->name); ?>"
                                                            title="Actualiser cette banque">
                                                        <i class="fa fa-refresh"></i>
                                                    </button>

                                                    <!-- Bouton éditer -->
                                                    <button class="btn btn-sm btn-warning edit-bank"
                                                            data-id="<?php echo $bank->id; ?>"
                                                            data-name="<?php echo htmlspecialchars($bank->name); ?>"
                                                            data-code="<?php echo htmlspecialchars($bank->code); ?>"
                                                            data-account_number="<?php echo htmlspecialchars($bank->account_number ?? ''); ?>"
                                                            data-logo="<?php echo $logo_url; ?>"
                                                            data-initial_balance="<?php echo $balance; ?>"
                                                            title="Modifier">
                                                        <i class="fa fa-edit"></i>
                                                    </button>

                                                    <!-- Bouton supprimer -->
                                                    <button class="btn btn-sm btn-danger delete-bank"
                                                            data-id="<?php echo $bank->id; ?>"
                                                            title="Supprimer">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            <div class="alert alert-info" style="margin: 10px 0;">
                                                <i class="fa fa-info-circle"></i> Aucune banque enregistrée
                                            </div>
                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#bankModal">
                                                <i class="fa fa-plus"></i> Créer une banque
                                            </button>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if (!empty($banks)): ?>
                            <div class="well" style="margin-top: 15px; padding: 10px; background-color: #f8f9fa;">
                                <div class="row">
                                    <div class="col-xs-6">
                                        <small><strong>Total Banques:</strong> <?php echo count($banks); ?></small>
                                    </div>
                                    <div class="col-xs-6 text-right">
                                        <small><strong>Solde Total:</strong>
                                            <?php
                                            $total_balance = 0;
                                            foreach ($banks as $bank) {
                                                $total_balance += isset($bank->balance) ? $bank->balance : 0;
                                            }
                                            echo '<span class="' . ($total_balance >= 0 ? 'text-success' : 'text-danger') . '">';
                                            echo number_format($total_balance, 2, ',', ' ') . ' ' . $currency_symbol;
                                            echo '</span>';
                                            ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal pour créer/modifier une banque -->
<div class="modal fade" id="bankModal" tabindex="-1" role="dialog" aria-labelledby="bankModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="bankForm" action="<?php echo base_url() ?>admin/expense/save_bank" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h4 class="modal-title" id="bankModalLabel">Nouvelle Banque</h4>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <?php echo $this->customlib->getCSRF(); ?>
                    <input type="hidden" id="bank_id_input" name="bank_id" value="">

                    <!-- Logo preview -->
                    <div class="form-group text-center">
                        <div id="logoPreview" style="margin-bottom: 15px;">
                            <div class="bank-logo-default" id="defaultLogo" style="margin: 0 auto;">
                                <span id="logoInitials">BN</span>
                            </div>
                            <img id="logoImage" src="" alt="" class="bank-logo" style="display: none; margin: 0 auto;">
                        </div>

                        <label class="btn btn-sm btn-outline-primary upload-logo-btn">
                            <i class="fa fa-camera"></i> Choisir un logo
                            <input type="file" name="logo" id="logoInput" accept="image/*">
                        </label>
                        <small class="text-muted d-block mt-2">Format: JPG, PNG, SVG (max 2MB)</small>
                    </div>

                    <div class="form-group">
                        <label>Nom de la banque *</label>
                        <input type="text" id="bank_name" name="bank_name" class="form-control" required
                               oninput="updateLogoInitials()">
                    </div>

                    <div class="form-group">
                        <label>Code banque *</label>
                        <input type="text" id="bank_code" name="bank_code" class="form-control" required>
                        <small class="text-muted">Code unique d'identification (ex: BIC, SWIFT)</small>
                    </div>

                    <div class="form-group">
                        <label>Numéro de compte</label>
                        <input type="text" id="account_number" name="account_number" class="form-control">
                        <small class="text-muted">Numéro de compte bancaire (optionnel)</small>
                    </div>

                    <div class="form-group">
                        <label>Solde initial</label>
                        <div class="input-group">
                            <span class="input-group-addon"><?php echo $currency_symbol; ?></span>
                            <input type="number" id="initial_balance" name="initial_balance" class="form-control"
                                   step="0.01" value="0.00" min="0">
                        </div>
                        <small class="text-muted">Solde de départ de la banque (optionnel)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Enregistrer
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fa fa-times"></i> Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour changer le logo -->
<div class="modal fade" id="changeLogoModal" tabindex="-1" role="dialog">
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
                        <label class="btn btn-primary btn-block">
                            <i class="fa fa-folder-open"></i> Choisir une image
                            <input type="file" name="logo" id="newLogoInput" accept="image/*" style="display: none;" required>
                        </label>
                    </div>

                    <div id="newLogoPreview" style="display: none; margin-top: 15px;">
                        <img id="previewImage" src="" alt="" style="max-width: 100%; max-height: 150px; border-radius: 5px;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-save"></i> Enregistrer
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour les transactions bancaires -->
<div class="modal fade" id="transactionModal" tabindex="-1" role="dialog" aria-labelledby="transactionModalLabel">
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
                            <div class="form-group">
                                <label>Banque *</label>
                                <select id="transaction_bank_id" name="bank_id" class="form-control" required>
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
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
                            <div class="form-group">
                                <label>Type de transaction *</label>
                                <select id="transaction_type" name="transaction_type" class="form-control" required>
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
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
                            <div class="form-group">
                                <label>Désignation *</label>
                                <select id="designation" name="designation" class="form-control" required>
                                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                                    <option value="Débit">Débit</option>
                                    <option value="Crédit">Crédit</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('date'); ?> *</label>
                                <input id="date" name="date" type="text" class="form-control date"
                                       value="<?php echo set_value('date', date($this->customlib->getSchoolDateFormat())); ?>" readonly required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('name'); ?> *</label>
                                <input id="name" name="name" type="text" class="form-control" required
                                       placeholder="Libellé de l'opération">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><?php echo $this->lang->line('amount'); ?> *</label>
                                <div class="input-group">
                                    <span class="input-group-addon"><?php echo $currency_symbol; ?></span>
                                    <input id="amount" name="amount" type="number" step="0.01" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Numéro de référence</label>
                                <input id="reference" name="reference" type="text" class="form-control"
                                       placeholder="N° chèque, virement, etc.">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Mode de paiement</label>
                                <select id="payment_mode" name="payment_mode" class="form-control">
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

                    <div class="form-group">
                        <label><?php echo $this->lang->line('description'); ?></label>
                        <textarea id="description" name="description" class="form-control" rows="3"
                                  placeholder="Détails de la transaction"></textarea>
                    </div>

                    <div class="form-group" hidden>
                        <label>Pièce jointe</label>
                        <input id="documents" name="documents" type="file" class="form-control">
                        <small class="text-muted">Extrait bancaire, chèque, etc. (PDF, JPG, PNG)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> <?php echo $this->lang->line('save'); ?>
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">
                        <i class="fa fa-times"></i> Fermer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour voir les transactions d'une banque -->
<div class="modal fade" id="viewTransactionsModal" tabindex="-1" role="dialog" aria-labelledby="viewTransactionsModalLabel">
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
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Chargement...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times"></i> Fermer
                </button>
                <button type="button" class="btn btn-primary" onclick="exportBankTransactionsPDF()">
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
<div class="modal fade" id="deleteBankModal" tabindex="-1" role="dialog">
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
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times"></i> Annuler
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Gestion du logo dans le modal
    function updateLogoInitials() {
        var name = $('#bank_name').val();
        var initials = name ? name.substring(0, 2).toUpperCase() : 'BN';
        $('#logoInitials').text(initials);
    }

    // Preview du logo lors de la sélection
    $('#logoInput').change(function(e) {
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#logoImage').attr('src', e.target.result).show();
                $('#defaultLogo').hide();
            }
            reader.readAsDataURL(file);
        }
    });

    // Preview du nouveau logo
    $('#newLogoInput').change(function(e) {
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#previewImage').attr('src', e.target.result);
                $('#newLogoPreview').show();
            }
            reader.readAsDataURL(file);
        }
    });

    // Fonction pour afficher les notifications (toast)
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

        // Supprimer les anciennes notifications
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

        // Ajouter la notification
        $('body').append(toast);

        // Animation d'entrée
        $('.custom-toast').hide().fadeIn(300);

        // Supprimer automatiquement après 4 secondes
        setTimeout(function() {
            $('.custom-toast').fadeOut(300, function() {
                $(this).remove();
            });
        }, 4000);
    }

    // Fonction pour formater les nombres
    function formatNumber(number) {
        return parseFloat(number).toLocaleString('fr-FR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    $(document).ready(function () {
        // Variables globales
        var currentBankId = null;
        var currentBankName = null;

        // Fonction pour rafraîchir la liste des banques
        function refreshBankList() {
            var tableBody = $('#bankTable tbody');
            var originalContent = tableBody.html();
            tableBody.html(
                '<tr><td colspan="4" class="text-center">' +
                '<div class="spinner-border spinner-border-sm text-primary" role="status">' +
                '<span class="sr-only">Chargement...</span>' +
                '</div> Mise à jour...' +
                '</td></tr>'
            );

            $.ajax({
                url: '<?php echo base_url(); ?>admin/expense/get_bank_list',
                type: 'GET',
                success: function(response) {
                    tableBody.html(response);
                    attachBankEvents();
                },
                error: function(xhr, status, error) {
                    console.error('Erreur lors du chargement des banques:', error);
                    tableBody.html(originalContent);
                    attachBankEvents();
                    showToast('error', 'Erreur lors du rafraîchissement des banques');
                }
            });
        }

        // Édition d'une banque - charger les données
        $(document).on('click', '.edit-bank', function(e) {
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

            // Gérer le logo
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

        // Bouton pour changer le logo
        $(document).on('click', '.change-logo-btn', function(e) {
            e.stopPropagation();
            var bankId = $(this).data('bank-id');
            var bankName = $(this).data('bank-name');
            var currentLogo = $(this).closest('tr').data('bank-logo');

            $('#changeLogoBankId').val(bankId);
            $('#changeLogoModal .modal-title').text('Logo - ' + bankName);

            // Afficher le logo actuel
            var currentLogoHtml = currentLogo ?
                '<img src="' + currentLogo + '" alt="Logo actuel" style="max-width: 100px; border-radius: 5px;">' :
                '<div class="bank-logo-default" style="margin: 0 auto;">' +
                bankName.substring(0, 2).toUpperCase() + '</div>';

            $('#currentLogoPreview').html(currentLogoHtml);
            $('#changeLogoModal').modal('show');
        });

        // Soumission du formulaire de changement de logo
        $('#changeLogoForm').submit(function(e) {
            e.preventDefault();

            var formData = new FormData(this);

            $.ajax({
                url: '<?php echo base_url(); ?>admin/expense/change_bank_logo',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend: function() {
                    $('#changeLogoForm button[type="submit"]').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
                },
                success: function(response) {
                    if (response.status == 'success') {
                        showToast('success', response.message);
                        $('#changeLogoModal').modal('hide');
                        setTimeout(function() {
                            refreshBankList();
                        }, 300);
                    } else {
                        showToast('error', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    showToast('error', 'Erreur lors du changement de logo: ' + error);
                },
                complete: function() {
                    $('#changeLogoForm button[type="submit"]').prop('disabled', false).html('<i class="fa fa-save"></i> Enregistrer');
                }
            });
        });

        // Attacher les événements aux boutons des banques
        function attachBankEvents() {
            // Bouton pour ajouter une transaction à une banque spécifique
            $('.add-transaction-btn').off('click').on('click', function(e) {
                e.stopPropagation();
                var bankId = $(this).data('bank-id');
                var bankName = $(this).data('bank-name');

                // Sélectionner la banque dans le modal
                $('#transactionModal select[name="bank_id"]').val(bankId);

                // Afficher le modal
                $('#transactionModal').modal('show');

                // Changer le titre du modal
                $('#transactionModalLabel').html('Nouvelle Transaction - ' + bankName);
            });

            // Bouton pour voir les transactions d'une banque
            $('.view-transactions-btn').off('click').on('click', function(e) {
                e.stopPropagation();
                var bankId = $(this).data('bank-id');
                var bankName = $(this).data('bank-name');

                // Stocker les informations de la banque
                currentBankId = bankId;
                currentBankName = bankName;

                // Charger les transactions
                loadBankTransactions(bankId, bankName);
            });

            // Suppression d'une banque
            $('.delete-bank').off('click').on('click', function(e) {
                e.stopPropagation();
                var bankId = $(this).data('id');
                var bankRow = $(this).closest('tr');
                var bankName = bankRow.data('bank-name');
                var balance = bankRow.data('bank-balance') || 0;

                // Préparer le message d'avertissement
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

            // Clic sur une ligne de banque
            $('#bankTable tbody tr').off('click').on('click', function(e) {
                if (!$(e.target).closest('button').length) {
                    var bankId = $(this).data('bank-id');
                    var bankName = $(this).data('bank-name');

                    // Charger les transactions
                    currentBankId = bankId;
                    currentBankName = bankName;
                    loadBankTransactions(bankId, bankName);
                }
            });
        }

        // Fonction pour charger les transactions d'une banque
        function loadBankTransactions(bankId, bankName) {
            $('#bankNameTitle').text(bankName);
            $('#viewTransactionsModal').modal('show');

            // Afficher le spinner de chargement
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

        // Confirmation de suppression d'une banque
        $('#confirmDelete').click(function() {
            var bankId = $('#deleteBankModal').data('bank-id');
            var deleteBtn = $(this);

            // Désactiver le bouton pendant la suppression
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
                            refreshBankList();
                        }, 300);
                    } else {
                        showToast('error', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    showToast('error', 'Erreur lors de la suppression: ' + error);
                },
                complete: function() {
                    // Réactiver le bouton
                    deleteBtn.prop('disabled', false).html('<i class="fa fa-trash"></i> Supprimer');
                }
            });
        });

        // Gérer le formulaire des banques
        // Gérer le formulaire des banques AVEC fichier
        $('#bankForm').submit(function(e) {
            e.preventDefault();
            var form = $(this);
            var submitBtn = form.find('button[type="submit"]');
            var modal = $('#bankModal');

            // Désactiver le bouton pendant l'envoi
            submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Enregistrement...');

            // Utiliser FormData pour les fichiers
            var formData = new FormData(this);

            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,  // IMPORTANT: ne pas traiter les données
                contentType: false,   // IMPORTANT: laisser le navigateur définir le content-type
                dataType: 'json',
                success: function(response) {
                    if (response.status == 'success') {
                        showToast('success', response.message);
                        modal.modal('hide');

                        // Réinitialiser proprement
                        form[0].reset();
                        $('#bank_id_input').val('');
                        $('#logoImage').attr('src', '').hide();
                        $('#defaultLogo').show();
                        updateLogoInitials();

                        setTimeout(function() {
                            refreshBankList();
                        }, 500);
                    } else {
                        showToast('error', response.message || 'Erreur inconnue');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Erreur AJAX:', error);
                    console.error('Response:', xhr.responseText);
                    showToast('error', 'Erreur serveur: ' + (xhr.responseJSON?.message || error));
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Enregistrer');
                }
            });
        });

        // Attacher l'événement au bouton refresh-bank-btn
        $(document).on('click', '.refresh-bank-btn', function(e) {
            e.stopPropagation();
            var bankId = $(this).data('bank-id');
            var bankName = $(this).data('bank-name');

            refreshSingleBank(bankId, bankName);
        });

        // Fonction pour rafraîchir une banque spécifique
        function refreshSingleBank(bankId, bankName) {
            var btn = $('.refresh-bank-btn[data-bank-id="' + bankId + '"]');
            var originalHtml = btn.html();

            btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

            $.ajax({
                url: '<?php echo base_url(); ?>admin/expense/refresh_bank_balance/' + bankId,
                type: 'GET',
                success: function(response) {
                    if (response.status == 'success') {
                        showToast('success', 'Solde de ' + bankName + ' actualisé');
                        // Mettre à jour la ligne spécifique
                        var row = $('tr[data-bank-id="' + bankId + '"]');
                        row.data('bank-balance', response.new_balance);
                        row.find('.badge').text(response.formatted_balance);

                        // Mettre à jour la couleur du badge
                        row.find('.badge').removeClass('badge-success badge-danger')
                            .addClass(response.new_balance >= 0 ? 'badge-success' : 'badge-danger');
                    }
                },
                error: function() {
                    showToast('error', 'Erreur lors de l\'actualisation');
                },
                complete: function() {
                    btn.prop('disabled', false).html(originalHtml);
                }
            });
        }

        // Code minimal pour rafraîchir la page
        $('#refreshPageBtn').click(function() {
            $(this).find('i').addClass('fa-spin');
            setTimeout(function() {
                location.reload();
            }, 200);
        });

        // Raccourci F5
        $(document).keydown(function(e) {
            if (e.keyCode == 116) {
                e.preventDefault();
                $('#refreshPageBtn').click();
            }
        });

        // Initialiser les événements au chargement
        attachBankEvents();

        // Initialiser les logos
        $('.bank-logo').on('error', function() {
            $(this).hide();
            $(this).next('.bank-logo-default').show();
        });

        // Réinitialiser le modal banque quand il se ferme
        $('#bankModal').on('hidden.bs.modal', function () {
            $('#bankForm')[0].reset();
            $('#bank_id_input').val('');
            $('#bankModalLabel').text('Nouvelle Banque');
            $('#logoImage').hide();
            $('#defaultLogo').show();
            updateLogoInitials();
        });

        // Réinitialiser le modal de changement de logo
        $('#changeLogoModal').on('hidden.bs.modal', function () {
            $('#changeLogoForm')[0].reset();
            $('#newLogoPreview').hide();
            $('#currentLogoPreview').html('');
        });
    });
</script>