<?php
// Vérification des données
if (!isset($immobilisation)) {
    $immobilisation = array();
}
if (!isset($amortissements)) {
    $amortissements = array();
}
if (!isset($cessions)) {
    $cessions = array();
}

// Labels pour les statuts
$statusLabels = [
    'actif' => 'Actif',
    'amorti' => 'Amorti',
    'ceder' => 'Cédé',
    'sortie' => 'Sortie'
];

$statusBadge = [
    'actif' => 'actif',
    'amorti' => 'amorti',
    'ceder' => 'ceder',
    'sortie' => 'sortie'
];

// Labels pour les types
$typeLabels = [
    'corporelle' => 'Corporelle',
    'incorporelle' => 'Incorporelle',
    'financiere' => 'Financière'
];

// Labels pour les modes d'amortissement
$modeLabels = [
    'lineaire' => 'Linéaire',
    'degresif' => 'Dégressif',
    'variable' => 'Variable'
];
?>

<div class="modal-body" style="padding: 20px;">
    <?php if (empty($immobilisation)) : ?>
        <div class="text-center text-muted" style="padding: 40px 0;">
            <i class="fa fa-exclamation-circle" style="font-size: 48px; color: #cbd5e1; display: block; margin-bottom: 16px;"></i>
            <p style="font-size: 16px;">Aucun détail disponible</p>
            <p style="font-size: 13px; color: #94a3b8;">L'immobilisation demandée n'a pas été trouvée</p>
        </div>
    <?php else : ?>

        <!-- ===== EN-TÊTE ===== -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #e2e8f0;">
            <div>
                <h4 style="margin: 0; color: #1e293b; font-weight: 600;">
                    <i class="fa fa-building" style="color: #3b82f6; margin-right: 10px;"></i>
                    <?php echo isset($immobilisation['nom']) ? htmlspecialchars($immobilisation['nom']) : ''; ?>
                </h4>
                <small style="color: #94a3b8; font-size: 13px;">
                    <i class="fa fa-barcode"></i> Code: <?php echo isset($immobilisation['code']) ? htmlspecialchars($immobilisation['code']) : ''; ?>
                    <?php if (!empty($immobilisation['num_serie'])) : ?>
                        | <i class="fa fa-hashtag"></i> Série: <?php echo htmlspecialchars($immobilisation['num_serie']); ?>
                    <?php endif; ?>
                </small>
            </div>
            <div>
            <span class="badge-status <?php echo isset($immobilisation['statut']) ? $statusBadge[$immobilisation['statut']] ?? 'actif' : 'actif'; ?>">
                <?php echo isset($immobilisation['statut']) ? ($statusLabels[$immobilisation['statut']] ?? $immobilisation['statut']) : 'Actif'; ?>
            </span>
            </div>
        </div>

        <div class="row">
            <!-- ===== COLONNE GAUCHE ===== -->
            <div class="col-md-6">
                <!-- Informations générales -->
                <div style="background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 16px; border: 1px solid #eef2f6;">
                    <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px;">
                        <i class="fa fa-info-circle" style="margin-right: 8px; color: #3B82F6;"></i> INFORMATIONS GÉNÉRALES
                    </h5>

                    <div class="row">
                        <div class="col-md-6">
                            <p style="margin: 5px 0; font-size: 13px;">
                                <strong>Catégorie :</strong>
                                <span style="background: #f1f5f9; padding: 2px 12px; border-radius: 12px; font-size: 12px; color: #475569;">
                                <?php echo isset($immobilisation['categorie']) ? htmlspecialchars($immobilisation['categorie']) : '—'; ?>
                            </span>
                            </p>
                            <p style="margin: 5px 0; font-size: 13px;">
                                <strong>Type :</strong>
                                <?php echo isset($immobilisation['type_immobilisation']) ? ($typeLabels[$immobilisation['type_immobilisation']] ?? $immobilisation['type_immobilisation']) : '—'; ?>
                            </p>
                            <p style="margin: 5px 0; font-size: 13px;">
                                <strong>Mode amortissement :</strong>
                                <?php echo isset($immobilisation['mode_amortissement']) ? ($modeLabels[$immobilisation['mode_amortissement']] ?? $immobilisation['mode_amortissement']) : '—'; ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p style="margin: 5px 0; font-size: 13px;">
                                <strong>Durée amortissement :</strong>
                                <?php echo isset($immobilisation['duree_amortissement']) ? $immobilisation['duree_amortissement'] . ' ans' : '—'; ?>
                            </p>
                            <p style="margin: 5px 0; font-size: 13px;">
                                <strong>Taux amortissement :</strong>
                                <?php echo isset($immobilisation['taux_amortissement']) ? $immobilisation['taux_amortissement'] . '%' : '—'; ?>
                            </p>
                            <p style="margin: 5px 0; font-size: 13px;">
                                <strong>Responsable :</strong>
                                <?php echo isset($immobilisation['responsable']) ? htmlspecialchars($immobilisation['responsable']) : '—'; ?>
                            </p>
                        </div>
                    </div>

                    <?php if (!empty($immobilisation['description'])) : ?>
                        <div style="margin-top: 10px; padding: 10px; background: #ffffff; border-radius: 6px; border: 1px solid #e2e8f0;">
                            <strong style="color: #334155; font-size: 12px;">Description :</strong>
                            <p style="margin: 4px 0 0 0; color: #475569; font-size: 13px;">
                                <?php echo nl2br(htmlspecialchars($immobilisation['description'])); ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Acquisition -->
                <div style="background: #eff6ff; padding: 15px; border-radius: 8px; margin-bottom: 16px; border-left: 4px solid #3B82F6;">
                    <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #dbeafe; padding-bottom: 8px;">
                        <i class="fa fa-shopping-cart" style="margin-right: 8px; color: #3B82F6;"></i> ACQUISITION
                    </h5>

                    <div class="row">
                        <div class="col-md-6">
                            <p style="margin: 5px 0; font-size: 13px;">
                                <strong>Date d'acquisition :</strong>
                                <?php echo !empty($immobilisation['date_acquisition']) ? date('d/m/Y', strtotime($immobilisation['date_acquisition'])) : '—'; ?>
                            </p>
                            <p style="margin: 5px 0; font-size: 13px;">
                                <strong>Date mise en service :</strong>
                                <?php echo !empty($immobilisation['date_mise_en_service']) ? date('d/m/Y', strtotime($immobilisation['date_mise_en_service'])) : '—'; ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p style="margin: 5px 0; font-size: 13px;">
                                <strong>Valeur originale :</strong>
                                <span style="font-weight: 600; color: #1e293b;">
                                <?php echo isset($immobilisation['valeur_originale']) ? number_format($immobilisation['valeur_originale'], 0, ',', ' ') . ' FCFA' : '0 FCFA'; ?>
                            </span>
                            </p>
                            <p style="margin: 5px 0; font-size: 13px;">
                                <strong>Valeur résiduelle :</strong>
                                <?php echo isset($immobilisation['valeur_residuelle']) ? number_format($immobilisation['valeur_residuelle'], 0, ',', ' ') . ' FCFA' : '0 FCFA'; ?>
                            </p>
                        </div>
                    </div>

                    <?php if (!empty($immobilisation['fournisseur_id']) || !empty($immobilisation['num_facture'])) : ?>
                        <div style="margin-top: 10px; padding: 10px; background: #ffffff; border-radius: 6px; border: 1px solid #dbeafe;">
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="margin: 5px 0; font-size: 12px; color: #475569;">
                                        <strong>Fournisseur :</strong>
                                        <?php echo isset($immobilisation['fournisseur_nom']) ? htmlspecialchars($immobilisation['fournisseur_nom']) : '—'; ?>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p style="margin: 5px 0; font-size: 12px; color: #475569;">
                                        <strong>N° Facture :</strong>
                                        <?php echo isset($immobilisation['num_facture']) ? htmlspecialchars($immobilisation['num_facture']) : '—'; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Localisation -->
                <?php if (!empty($immobilisation['localisation'])) : ?>
                    <div style="background: #f0fdf4; padding: 15px; border-radius: 8px; border-left: 4px solid #10b981;">
                        <h5 style="margin: 0 0 10px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #dcfce7; padding-bottom: 8px;">
                            <i class="fa fa-map-marker" style="margin-right: 8px; color: #10b981;"></i> LOCALISATION
                        </h5>
                        <p style="margin: 0; font-size: 13px; color: #475569;">
                            <i class="fa fa-location-arrow" style="color: #10b981; margin-right: 6px;"></i>
                            <?php echo htmlspecialchars($immobilisation['localisation']); ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ===== COLONNE DROITE ===== -->
            <div class="col-md-6">
                <!-- Valeurs financières -->
                <div style="background: #f5f3ff; padding: 15px; border-radius: 8px; margin-bottom: 16px; border-left: 4px solid #8b5cf6;">
                    <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #ede9fe; padding-bottom: 8px;">
                        <i class="fa fa-money" style="margin-right: 8px; color: #8b5cf6;"></i> VALEURS FINANCIÈRES
                    </h5>

                    <div class="row">
                        <div class="col-md-6">
                            <div style="background: #ffffff; padding: 10px; border-radius: 6px; text-align: center; border: 1px solid #e2e8f0; margin-bottom: 8px;">
                                <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase;">Valeur originale</div>
                                <div style="font-size: 18px; font-weight: 700; color: #1e293b;">
                                    <?php echo isset($immobilisation['valeur_originale']) ? number_format($immobilisation['valeur_originale'], 0, ',', ' ') . ' FCFA' : '0 FCFA'; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div style="background: #ffffff; padding: 10px; border-radius: 6px; text-align: center; border: 1px solid #e2e8f0; margin-bottom: 8px;">
                                <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase;">Valeur nette</div>
                                <div style="font-size: 18px; font-weight: 700; color: <?php echo (isset($immobilisation['valeur_nette']) && $immobilisation['valeur_nette'] > 0) ? '#10b981' : '#ef4444'; ?>;">
                                    <?php echo isset($immobilisation['valeur_nette']) ? number_format($immobilisation['valeur_nette'], 0, ',', ' ') . ' FCFA' : '0 FCFA'; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div style="background: #ffffff; padding: 10px; border-radius: 6px; text-align: center; border: 1px solid #e2e8f0;">
                                <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase;">Amortissement cumulé</div>
                                <div style="font-size: 16px; font-weight: 700; color: #f59e0b;">
                                    <?php echo isset($immobilisation['amortissement_cumule']) ? number_format($immobilisation['amortissement_cumule'], 0, ',', ' ') . ' FCFA' : '0 FCFA'; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div style="background: #ffffff; padding: 10px; border-radius: 6px; text-align: center; border: 1px solid #e2e8f0;">
                                <div style="font-size: 11px; color: #94a3b8; text-transform: uppercase;">Taux d'amortissement</div>
                                <div style="font-size: 16px; font-weight: 700; color: #8b5cf6;">
                                    <?php echo isset($immobilisation['taux_amortissement']) ? $immobilisation['taux_amortissement'] . '%' : '—'; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Historique des amortissements -->
                <div style="background: #fef3c7; padding: 15px; border-radius: 8px; margin-bottom: 16px; border-left: 4px solid #f59e0b;">
                    <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #fde68a; padding-bottom: 8px;">
                        <i class="fa fa-line-chart" style="margin-right: 8px; color: #f59e0b;"></i> HISTORIQUE DES AMORTISSEMENTS
                        <span class="badge" style="background: #f59e0b; color: #fff; padding: 2px 8px; border-radius: 10px; font-size: 10px; margin-left: 6px;">
                        <?php echo count($amortissements); ?>
                    </span>
                    </h5>

                    <?php if (empty($amortissements)) : ?>
                        <p style="text-align: center; color: #94a3b8; font-size: 13px; padding: 10px 0;">
                            <i class="fa fa-info-circle"></i> Aucun amortissement enregistré
                        </p>
                    <?php else : ?>
                        <div style="max-height: 150px; overflow-y: auto;">
                            <table class="table table-condensed" style="margin: 0; font-size: 12px;">
                                <thead>
                                <tr style="background: #ffffff;">
                                    <th>Période</th>
                                    <th>Montant</th>
                                    <th>Type</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($amortissements as $amort) : ?>
                                    <tr>
                                        <td>
                                            <?php echo !empty($amort['periode_debut']) ? date('d/m/Y', strtotime($amort['periode_debut'])) : ''; ?>
                                            →
                                            <?php echo !empty($amort['periode_fin']) ? date('d/m/Y', strtotime($amort['periode_fin'])) : ''; ?>
                                        </td>
                                        <td style="font-weight: 500; color: #f59e0b;">
                                            <?php echo isset($amort['montant']) ? number_format($amort['montant'], 0, ',', ' ') . ' FCFA' : '0 FCFA'; ?>
                                        </td>
                                        <td>
                                        <span style="background: <?php echo $amort['type'] == 'effectif' ? '#d1fae5' : '#fef3c7'; ?>; padding: 2px 8px; border-radius: 10px; font-size: 10px;">
                                            <?php echo $amort['type'] == 'effectif' ? 'Effectif' : 'Prévisionnel'; ?>
                                        </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Historique des cessions -->
                <?php if (!empty($cessions)) : ?>
                    <div style="background: #fef2f2; padding: 15px; border-radius: 8px; border-left: 4px solid #ef4444;">
                        <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #fecaca; padding-bottom: 8px;">
                            <i class="fa fa-handshake-o" style="margin-right: 8px; color: #ef4444;"></i> HISTORIQUE DES CESSIONS
                            <span class="badge" style="background: #ef4444; color: #fff; padding: 2px 8px; border-radius: 10px; font-size: 10px; margin-left: 6px;">
                        <?php echo count($cessions); ?>
                    </span>
                        </h5>

                        <?php foreach ($cessions as $cession) : ?>
                            <div style="background: #ffffff; padding: 10px; border-radius: 6px; border: 1px solid #fecaca; margin-bottom: 8px;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p style="margin: 2px 0; font-size: 12px;">
                                            <strong>Date :</strong>
                                            <?php echo !empty($cession['date_cession']) ? date('d/m/Y', strtotime($cession['date_cession'])) : '—'; ?>
                                        </p>
                                        <p style="margin: 2px 0; font-size: 12px;">
                                            <strong>Acheteur :</strong>
                                            <?php echo isset($cession['acheteur']) ? htmlspecialchars($cession['acheteur']) : '—'; ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p style="margin: 2px 0; font-size: 12px;">
                                            <strong>Montant :</strong>
                                            <span style="font-weight: 600; color: #ef4444;">
                                    <?php echo isset($cession['montant_cession']) ? number_format($cession['montant_cession'], 0, ',', ' ') . ' FCFA' : '0 FCFA'; ?>
                                </span>
                                        </p>
                                        <?php if (!empty($cession['motif'])) : ?>
                                            <p style="margin: 2px 0; font-size: 12px;">
                                                <strong>Motif :</strong>
                                                <?php echo htmlspecialchars($cession['motif']); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Métadonnées -->
                <div style="margin-top: 16px; padding: 12px 16px; background: #f1f5f9; border-radius: 6px; display: flex; gap: 20px; flex-wrap: wrap; font-size: 12px; color: #64748b;">
                <span><strong>Créé le :</strong>
                    <?php echo !empty($immobilisation['date_creation']) ? date('d/m/Y H:i', strtotime($immobilisation['date_creation'])) : '—'; ?>
                </span>
                    <?php if (!empty($immobilisation['date_modification']) && $immobilisation['date_modification'] != $immobilisation['date_creation']) : ?>
                        <span><strong>Modifié le :</strong>
                    <?php echo date('d/m/Y H:i', strtotime($immobilisation['date_modification'])); ?>
                </span>
                    <?php endif; ?>
                    <span><strong>ID :</strong> #<?php echo $immobilisation['id']; ?></span>
                </div>
            </div>
        </div>

    <?php endif; ?>
</div>

<!-- ===== FOOTER AVEC ACTIONS ===== -->
<div class="modal-footer" style="padding: 12px 20px; border-top: 1px solid #e2e8f0; background: #f8fafc; border-radius: 0 0 8px 8px; display: flex; gap: 8px; flex-wrap: wrap;">
    <button type="button" class="btn btn-default" data-dismiss="modal" style="padding: 6px 20px;">
        <i class="fa fa-times"></i> Fermer
    </button>

    <?php if (!empty($immobilisation) && $this->rbac->hasPrivilege('immobilisations', 'can_edit')) : ?>
        <button type="button" class="btn btn-primary" onclick="openEditModal(<?php echo $immobilisation['id']; ?>); $('#immobilisationDetailsModal').modal('hide');" style="padding: 6px 20px;">
            <i class="fa fa-pencil"></i> Modifier
        </button>
    <?php endif; ?>

    <?php if (!empty($immobilisation) && $immobilisation['statut'] == 'actif' && $this->rbac->hasPrivilege('immobilisations', 'can_edit')) : ?>
        <button type="button" class="btn btn-warning" onclick="calculerAmortissement(<?php echo $immobilisation['id']; ?>); $('#immobilisationDetailsModal').modal('hide');" style="padding: 6px 20px;">
            <i class="fa fa-calculator"></i> Calculer amortissement
        </button>
        <button type="button" class="btn btn-success" onclick="openCederModal(<?php echo $immobilisation['id']; ?>); $('#immobilisationDetailsModal').modal('hide');" style="padding: 6px 20px;">
            <i class="fa fa-handshake-o"></i> Céder
        </button>
    <?php endif; ?>

    <?php if (!empty($immobilisation) && $this->rbac->hasPrivilege('immobilisations', 'can_delete')) : ?>
        <button type="button" class="btn btn-danger" onclick="confirmDelete(event, <?php echo $immobilisation['id']; ?>, '<?php echo htmlspecialchars($immobilisation['nom']); ?>'); $('#immobilisationDetailsModal').modal('hide');" style="padding: 6px 20px;">
            <i class="fa fa-trash"></i> Supprimer
        </button>
    <?php endif; ?>
</div>

<style>
    .badge-status {
        display: inline-block;
        padding: 5px 16px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }
    .badge-status.actif { background: #d1fae5; color: #065f46; }
    .badge-status.amorti { background: #fef3c7; color: #92400e; }
    .badge-status.ceder { background: #fef2f2; color: #991b1b; }
    .badge-status.sortie { background: #e2e8f0; color: #475569; }

    .table-condensed th,
    .table-condensed td {
        padding: 4px 6px;
    }

    .modal-footer .btn {
        transition: all 0.3s ease;
    }
    .modal-footer .btn:hover {
        transform: translateY(-2px);
    }
    .modal-footer .btn:active {
        transform: translateY(0);
    }
</style>

<script type="text/javascript">
    // Fonctions nécessaires pour les actions
    function openEditModal(id) {
        // Cette fonction est définie dans la vue principale
        if (typeof window.openEditModal === 'function') {
            window.openEditModal(id);
        } else {
            // Fallback: recharger la page avec l'ID d'édition
            window.location.href = '<?php echo base_url('admin/immobilisations/edit/'); ?>' + id;
        }
    }

    // ========================================== //
    // OUVERTURE MODAL - MODIFICATION (AJAX)      //
    // ========================================== //
    function openEditModal(id) {
        console.log('Ouverture du modal d\'édition pour l\'ID:', id);

        if (!id || id === 'undefined' || id === 0) {
            showError('ID invalide');
            return;
        }

        showSpinner();

        Swal.fire({
            title: 'Chargement...',
            text: 'Récupération des données de l\'immobilisation',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        var url = '<?php echo base_url(); ?>admin/immobilisations/get_data/' + id;
        console.log('URL de chargement:', url);

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            timeout: 15000,
            success: function(response) {
                hideSpinner();
                Swal.close();
                console.log('Réponse reçue:', response);

                if (response.success) {
                    var data = response.immobilisation;
                    console.log('Données de l\'immobilisation:', data);
                    fillEditForm(data);
                    $('#immobilisationFormModal').modal('show');
                } else {
                    showError(response.message || 'Impossible de charger les données de l\'immobilisation');
                }
            },
            error: function(xhr, status, error) {
                hideSpinner();
                Swal.close();
                console.error('Erreur AJAX:', status, error);
                console.error('Réponse:', xhr.responseText);
                handleAjaxError(xhr, status, error);
            }
        });
    }

    // ========================================== //
    // REMPLIR LE FORMULAIRE D'ÉDITION            //
    // ========================================== //
    function fillEditForm(data) {
        console.log('Remplissage du formulaire avec:', data);

        // Mettre à jour le titre et le bouton
        $('#formModalIcon').removeClass('fa-plus-circle').addClass('fa-pencil-square-o');
        $('#formTitleText').text('Modifier l\'immobilisation');
        $('#formModalSubtitle').text('Mettez à jour les informations de l\'immobilisation');
        $('#formSubmitText').text('Mettre à jour');
        $('#formSubmitBtn').removeClass('btn-success').addClass('btn-warning');
        $('#formSubmitBtn').data('mode', 'edit'); // Ajouter un indicateur de mode
        $('#immobilisationForm').attr('action', '<?php echo site_url('admin/immobilisations/update_ajax'); ?>');

        // Remplir les champs
        $('#edit_id').val(data.id || '');
        $('#edit_nom').val(data.nom || '');
        $('#edit_description').val(data.description || '');
        $('#edit_categorie').val(data.categorie || '');
        $('#edit_type').val(data.type_immobilisation || 'corporelle');
        $('#edit_valeur_originale').val(data.valeur_originale || '');
        $('#edit_valeur_residuelle').val(data.valeur_residuelle || 0);
        $('#edit_duree').val(data.duree_amortissement || '');
        $('#edit_mode').val(data.mode_amortissement || 'lineaire');
        $('#edit_fournisseur').val(data.fournisseur_id || '');
        $('#edit_num_facture').val(data.num_facture || '');
        $('#edit_num_serie').val(data.num_serie || '');
        $('#edit_localisation').val(data.localisation || '');
        $('#edit_responsable').val(data.responsable || '');
        $('#edit_statut').val(data.statut || 'actif');

        // Formater les dates
        if (data.date_acquisition) {
            $('#edit_date_acquisition').val(formatDate(data.date_acquisition));
        }
        if (data.date_mise_en_service) {
            $('#edit_date_mise_service').val(formatDate(data.date_mise_en_service));
        }

        // Réinitialiser les erreurs
        $('.text-danger').html('');
        $('.form-group').removeClass('has-error');
        $('.form-control').css('border-color', '');
    }

    // ========================================== //
    // SOUMISSION DU FORMULAIRE - ÉDITION (AJAX)  //
    // ========================================== //
    function submitEditForm(form) {
        if (!validateForm()) return false;
        showSpinner();

        var formData = new FormData(form[0]);

        // Ajouter l'ID pour l'édition
        var editId = $('#edit_id').val();
        formData.append('edit_id', editId);

        console.log('Données envoyées pour édition:', formData);
        console.log('ID à modifier:', editId);

        $.ajax({
            url: '<?php echo site_url('admin/immobilisations/update_ajax'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            timeout: 15000,
            success: function(response) {
                hideSpinner();
                console.log('Réponse de mise à jour:', response);

                if (response.success) {
                    $('#immobilisationFormModal').modal('hide');
                    showSuccess(response.message || 'Immobilisation mise à jour avec succès');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showError(response.message || 'Erreur lors de la mise à jour');
                }
            },
            error: function(xhr, status, error) {
                hideSpinner();
                console.error('Erreur AJAX:', status, error);
                console.error('Réponse brute:', xhr.responseText);
                handleAjaxError(xhr, status, error);
            }
        });
    }

    function calculerAmortissement(id) {
        if (typeof window.calculerAmortissement === 'function') {
            window.calculerAmortissement(id);
        } else {
            window.location.href = '<?php echo base_url('admin/immobilisations/calculer_amortissement/'); ?>' + id;
        }
    }

    function openCederModal(id) {
        if (typeof window.openCederModal === 'function') {
            window.openCederModal(id);
        } else {
            alert('La fonction de cession n\'est pas disponible');
        }
    }

    function confirmDelete(event, id, nom) {
        if (typeof window.confirmDelete === 'function') {
            window.confirmDelete(event, id, nom);
        } else {
            if (confirm('Supprimer définitivement l\'immobilisation "' + nom + '" ?')) {
                window.location.href = '<?php echo base_url('admin/immobilisations/delete/'); ?>' + id;
            }
        }
    }
</script>