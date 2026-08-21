<?php if (!empty($membre)) : ?>
    <div class="row">
        <div class="col-md-12">
            <!-- ===== EN-TÊTE ===== -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #e2e8f0;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <?php if (!empty($membre['photo'])) : ?>
                        <img src="<?php echo base_url('uploads/membres/' . $membre['photo']); ?>"
                             style="width: 64px; height: 64px; border-radius: 50%; object-fit: cover; border: 3px solid #e2e8f0;">
                    <?php else : ?>
                        <div style="width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, #273772, #3b82f6); display: flex; align-items: center; justify-content: center; color: white; font-size: 28px; font-weight: 600;">
                            <?php echo isset($membre['nom']) ? strtoupper(substr($membre['nom'], 0, 1)) : '?'; ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <h4 style="margin: 0; color: #1e293b; font-weight: 600;">
                            <?php echo isset($membre['nom']) ? htmlspecialchars($membre['nom']) : ''; ?>
                            <?php echo isset($membre['prenom']) ? htmlspecialchars($membre['prenom']) : ''; ?>
                        </h4>
                        <small style="color: #94a3b8; font-size: 13px;">
                            <i class="fa fa-barcode"></i> Code: <?php echo isset($membre['code_membre']) ? htmlspecialchars($membre['code_membre']) : ''; ?>
                            <?php if (!empty($membre['telephone'])) : ?>
                                | <i class="fa fa-phone"></i> <?php echo htmlspecialchars($membre['telephone']); ?>
                            <?php endif; ?>
                            <?php if (!empty($membre['email'])) : ?>
                                | <i class="fa fa-envelope"></i> <?php echo htmlspecialchars($membre['email']); ?>
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                <span class="badge-status <?php echo isset($membre['statut_membre']) ? $this->membres_model->get_status_badge($membre['statut_membre']) : 'actif'; ?>">
                    <?php
                    $statusLabels = [
                        'actif' => 'Actif',
                        'inactif' => 'Inactif',
                        'transfert' => 'En transfert',
                        'decede' => 'Décédé'
                    ];
                    echo isset($statusLabels[$membre['statut_membre']]) ? $statusLabels[$membre['statut_membre']] : $membre['statut_membre'];
                    ?>
                </span>
                    <span class="badge-role <?php echo isset($membre['role']) ? $this->membres_model->get_role_badge($membre['role']) : 'membre'; ?>">
                    <?php
                    $roleLabels = [
                        'membre' => 'Membre',
                        'diacre' => 'Diacre',
                        'ancien' => 'Ancien',
                        'pasteur' => 'Pasteur',
                        'evangeliste' => 'Évangéliste',
                        'autre' => 'Autre'
                    ];
                    echo isset($roleLabels[$membre['role']]) ? $roleLabels[$membre['role']] : $membre['role'];
                    ?>
                </span>
                    <span style="background: <?php echo $membre['sexe'] == 'M' ? '#dbeafe' : '#fef3c7'; ?>; padding: 4px 14px; border-radius: 12px; font-size: 12px; font-weight: 500; color: <?php echo $membre['sexe'] == 'M' ? '#1d4ed8' : '#92400e'; ?>;">
                    <i class="fa <?php echo $membre['sexe'] == 'M' ? 'fa-male' : 'fa-female'; ?>"></i>
                    <?php echo $membre['sexe'] == 'M' ? 'Homme' : 'Femme'; ?>
                </span>
                </div>
            </div>

            <div class="row">
                <!-- ===== COLONNE GAUCHE ===== -->
                <div class="col-md-6">
                    <!-- Informations personnelles -->
                    <div style="background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 16px; border: 1px solid #eef2f6;">
                        <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px;">
                            <i class="fa fa-id-card" style="margin-right: 8px; color: #3B82F6;"></i> INFORMATIONS PERSONNELLES
                        </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <p style="margin: 5px 0; font-size: 13px;">
                                    <strong>Nom :</strong> <?php echo isset($membre['nom']) ? htmlspecialchars($membre['nom']) : '—'; ?>
                                </p>
                                <p style="margin: 5px 0; font-size: 13px;">
                                    <strong>Prénom :</strong> <?php echo isset($membre['prenom']) ? htmlspecialchars($membre['prenom']) : '—'; ?>
                                </p>
                                <p style="margin: 5px 0; font-size: 13px;">
                                    <strong>Sexe :</strong>
                                    <span style="color: <?php echo $membre['sexe'] == 'M' ? '#3b82f6' : '#f59e0b'; ?>; font-weight: 600;">
                                    <?php echo $membre['sexe'] == 'M' ? 'Homme' : 'Femme'; ?>
                                </span>
                                </p>
                                <p style="margin: 5px 0; font-size: 13px;">
                                    <strong>Date naissance :</strong>
                                    <?php echo !empty($membre['date_naissance']) ? date('d/m/Y', strtotime($membre['date_naissance'])) : '—'; ?>
                                    <?php if (!empty($membre['date_naissance'])) : ?>
                                        <span style="color: #94a3b8; font-size: 11px;">
                                        (<?php
                                            $age = date_diff(date_create($membre['date_naissance']), date_create('today'))->y;
                                            echo $age . ' ans';
                                            ?>)
                                    </span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p style="margin: 5px 0; font-size: 13px;">
                                    <strong>Lieu naissance :</strong>
                                    <?php echo isset($membre['lieu_naissance']) ? htmlspecialchars($membre['lieu_naissance']) : '—'; ?>
                                </p>
                                <p style="margin: 5px 0; font-size: 13px;">
                                    <strong>Nationalité :</strong>
                                    <?php echo isset($membre['nationalite']) ? htmlspecialchars($membre['nationalite']) : '—'; ?>
                                </p>
                                <p style="margin: 5px 0; font-size: 13px;">
                                    <strong>Profession :</strong>
                                    <?php echo isset($membre['profession']) ? htmlspecialchars($membre['profession']) : '—'; ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Contact -->
                    <div style="background: #eff6ff; padding: 15px; border-radius: 8px; margin-bottom: 16px; border-left: 4px solid #3B82F6; border: 1px solid #dbeafe;">
                        <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #dbeafe; padding-bottom: 8px;">
                            <i class="fa fa-address-card" style="margin-right: 8px; color: #3B82F6;"></i> CONTACT
                        </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <p style="margin: 5px 0; font-size: 13px;">
                                    <strong><i class="fa fa-phone" style="color: #3b82f6;"></i> Téléphone :</strong>
                                    <?php echo isset($membre['telephone']) ? htmlspecialchars($membre['telephone']) : '—'; ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p style="margin: 5px 0; font-size: 13px;">
                                    <strong><i class="fa fa-envelope" style="color: #3b82f6;"></i> Email :</strong>
                                    <?php echo isset($membre['email']) ? htmlspecialchars($membre['email']) : '—'; ?>
                                </p>
                            </div>
                        </div>
                        <p style="margin: 5px 0; font-size: 13px;">
                            <strong><i class="fa fa-map-marker" style="color: #3b82f6;"></i> Adresse :</strong>
                            <?php echo isset($membre['adresse']) ? htmlspecialchars($membre['adresse']) : '—'; ?>
                        </p>
                    </div>

                    <!-- Famille -->
                    <?php if (!empty($membre['nom_conjoint']) || !empty($membre['nombre_enfants'])) : ?>
                        <div style="background: #f0fdf4; padding: 15px; border-radius: 8px; border-left: 4px solid #10b981; border: 1px solid #dcfce7;">
                            <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #dcfce7; padding-bottom: 8px;">
                                <i class="fa fa-heart" style="margin-right: 8px; color: #10b981;"></i> FAMILLE
                            </h5>

                            <?php if (!empty($membre['nom_conjoint'])) : ?>
                                <p style="margin: 5px 0; font-size: 13px;">
                                    <strong>Conjoint :</strong> <?php echo htmlspecialchars($membre['nom_conjoint']); ?>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($membre['nombre_enfants'])) : ?>
                                <p style="margin: 5px 0; font-size: 13px;">
                                    <strong>Nombre d'enfants :</strong> <?php echo $membre['nombre_enfants']; ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ===== COLONNE DROITE ===== -->
                <div class="col-md-6">
                    <!-- Vie de l'église -->
                    <div style="background: #f5f3ff; padding: 15px; border-radius: 8px; margin-bottom: 16px; border-left: 4px solid #8b5cf6; border: 1px solid #ede9fe;">
                        <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #ede9fe; padding-bottom: 8px;">
                            <i class="fa fa-church" style="margin-right: 8px; color: #8b5cf6;"></i> VIE DE L'ÉGLISE
                        </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <p style="margin: 5px 0; font-size: 13px;">
                                    <strong>Rôle :</strong>
                                    <span class="badge-role <?php echo isset($membre['role']) ? $this->membres_model->get_role_badge($membre['role']) : 'membre'; ?>" style="font-size: 12px;">
                                    <?php
                                    $roleLabels = [
                                        'membre' => 'Membre',
                                        'diacre' => 'Diacre',
                                        'ancien' => 'Ancien',
                                        'pasteur' => 'Pasteur',
                                        'evangeliste' => 'Évangéliste',
                                        'autre' => 'Autre'
                                    ];
                                    echo isset($roleLabels[$membre['role']]) ? $roleLabels[$membre['role']] : $membre['role'];
                                    ?>
                                </span>
                                </p>
                                <p style="margin: 5px 0; font-size: 13px;">
                                    <strong>Statut :</strong>
                                    <span class="badge-status <?php echo isset($membre['statut_membre']) ? $this->membres_model->get_status_badge($membre['statut_membre']) : 'actif'; ?>" style="font-size: 12px;">
                                    <?php
                                    $statusLabels = [
                                        'actif' => 'Actif',
                                        'inactif' => 'Inactif',
                                        'transfert' => 'En transfert',
                                        'decede' => 'Décédé'
                                    ];
                                    echo isset($statusLabels[$membre['statut_membre']]) ? $statusLabels[$membre['statut_membre']] : $membre['statut_membre'];
                                    ?>
                                </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p style="margin: 5px 0; font-size: 13px;">
                                    <strong>Département :</strong>
                                    <?php echo isset($membre['departement']) ? htmlspecialchars($membre['departement']) : '—'; ?>
                                </p>
                                <p style="margin: 5px 0; font-size: 13px;">
                                    <strong>Cellule :</strong>
                                    <?php echo isset($membre['groupe_cellule']) ? htmlspecialchars($membre['groupe_cellule']) : '—'; ?>
                                </p>
                            </div>
                        </div>

                        <?php if (!empty($membre['date_bapteme'])) : ?>
                            <p style="margin: 5px 0; font-size: 13px;">
                                <strong><i class="fa fa-water" style="color: #8b5cf6;"></i> Date de baptême :</strong>
                                <?php echo date('d/m/Y', strtotime($membre['date_bapteme'])); ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($membre['date_affiliation'])) : ?>
                            <p style="margin: 5px 0; font-size: 13px;">
                                <strong><i class="fa fa-handshake-o" style="color: #8b5cf6;"></i> Date d'affiliation :</strong>
                                <?php echo date('d/m/Y', strtotime($membre['date_affiliation'])); ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <!-- Remarques -->
                    <?php if (!empty($membre['remarques'])) : ?>
                        <div style="background: #f1f5f9; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <h5 style="margin: 0 0 10px 0; color: #1e293b; font-size: 13px; font-weight: 600;">
                                <i class="fa fa-file-text" style="margin-right: 8px; color: #64748b;"></i> REMARQUES
                            </h5>
                            <p style="margin: 0; color: #475569; font-size: 14px; line-height: 1.6;">
                                <?php echo nl2br(htmlspecialchars($membre['remarques'])); ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- Métadonnées -->
                    <div style="margin-top: 16px; padding: 12px 16px; background: #f1f5f9; border-radius: 6px; display: flex; gap: 20px; flex-wrap: wrap; font-size: 12px; color: #64748b;">
                        <span><strong>ID :</strong> #<?php echo $membre['id']; ?></span>
                        <span><strong>Code :</strong> <?php echo isset($membre['code_membre']) ? htmlspecialchars($membre['code_membre']) : '—'; ?></span>
                        <span><strong>Créé le :</strong>
                        <?php echo !empty($membre['date_creation']) ? date('d/m/Y H:i', strtotime($membre['date_creation'])) : '—'; ?>
                    </span>
                        <?php if (!empty($membre['date_modification']) && $membre['date_modification'] != $membre['date_creation']) : ?>
                            <span><strong>Modifié le :</strong>
                        <?php echo date('d/m/Y H:i', strtotime($membre['date_modification'])); ?>
                    </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else : ?>
    <div class="text-center text-muted" style="padding: 40px 0;">
        <i class="fa fa-exclamation-circle" style="font-size: 48px; color: #cbd5e1; display: block; margin-bottom: 16px;"></i>
        <p style="font-size: 16px;">Aucun détail disponible</p>
        <p style="font-size: 13px; color: #94a3b8;">Le membre demandé n'a pas été trouvé</p>
    </div>
<?php endif; ?>

<!-- ===== FOOTER AVEC ACTIONS ===== -->
<div class="modal-footer" style="padding: 12px 20px; border-top: 1px solid #e2e8f0; background: #f8fafc; border-radius: 0 0 8px 8px; display: flex; gap: 8px; flex-wrap: wrap;">
    <button type="button" class="btn btn-default" data-dismiss="modal" style="padding: 6px 20px;">
        <i class="fa fa-times"></i> Fermer
    </button>

    <?php if (!empty($membre) && $this->rbac->hasPrivilege('membres', 'can_edit')) : ?>
        <button type="button" class="btn btn-primary" onclick="openEditModal(<?php echo $membre['id']; ?>); $('#membreDetailsModal').modal('hide');" style="padding: 6px 20px;">
            <i class="fa fa-pencil"></i> Modifier
        </button>
    <?php endif; ?>

    <?php if (!empty($membre) && $this->rbac->hasPrivilege('membres', 'can_delete')) : ?>
        <button type="button" class="btn btn-danger" onclick="confirmDelete(event, <?php echo $membre['id']; ?>, '<?php echo isset($membre['nom']) ? htmlspecialchars($membre['nom']) . ' ' . htmlspecialchars($membre['prenom']) : ''; ?>'); $('#membreDetailsModal').modal('hide');" style="padding: 6px 20px;">
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
    .badge-status.inactif { background: #fef3c7; color: #92400e; }
    .badge-status.transfert { background: #dbeafe; color: #1d4ed8; }
    .badge-status.decede { background: #e2e8f0; color: #475569; }

    .badge-role {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }
    .badge-role.membre { background: #f1f5f9; color: #475569; }
    .badge-role.diacre { background: #dbeafe; color: #1d4ed8; }
    .badge-role.ancien { background: #fef3c7; color: #92400e; }
    .badge-role.pasteur { background: #f3e8ff; color: #7c3aed; }
    .badge-role.evangeliste { background: #d1fae5; color: #059669; }
    .badge-role.autre { background: #e2e8f0; color: #475569; }

    .btn {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .btn:hover {
        transform: translateY(-2px);
    }
    .btn:active {
        transform: translateY(0);
    }
</style>

<script type="text/javascript">
    // Fonctions nécessaires pour les actions
    function openEditModal(id) {
        if (typeof window.openEditModal === 'function') {
            window.openEditModal(id);
        } else {
            // Fallback: recharger la page avec l'ID d'édition
            window.location.href = '<?php echo base_url('admin/membres/edit/'); ?>' + id;
        }
    }

    function confirmDelete(event, id, nom) {
        if (typeof window.confirmDelete === 'function') {
            window.confirmDelete(event, id, nom);
        } else {
            if (confirm('Supprimer définitivement le membre "' + nom + '" ?')) {
                window.location.href = '<?php echo base_url('admin/membres/delete/'); ?>' + id;
            }
        }
    }
</script>