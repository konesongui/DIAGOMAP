<?php if (!empty($rapport)) : ?>
    <div class="row">
        <div class="col-md-12">
            <!-- ===== EN-TÊTE ===== -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #e2e8f0;">
                <div>
                    <h4 style="margin: 0; color: #1e293b; font-weight: 600;">
                        <i class="fa fa-church" style="color: #8b5cf6; margin-right: 10px;"></i>
                        <?php echo isset($rapport['theme']) ? htmlspecialchars($rapport['theme']) : 'Rapport de culte'; ?>
                    </h4>
                    <small style="color: #94a3b8; font-size: 13px;">
                        <i class="fa fa-calendar"></i>
                        <?php echo !empty($rapport['date_culte']) ? date('d/m/Y', strtotime($rapport['date_culte'])) : ''; ?>
                        <?php if (!empty($rapport['type_culte'])) : ?>
                            | <i class="fa fa-tag"></i>
                            <?php
                            $types = [
                                'matin' => 'Culte du matin',
                                'soir' => 'Culte du soir',
                                'jeunesse' => 'Culte des jeunes',
                                'enfants' => 'Culte des enfants',
                                'autre' => 'Autre'
                            ];
                            echo isset($types[$rapport['type_culte']]) ? $types[$rapport['type_culte']] : $rapport['type_culte'];
                            ?>
                        <?php endif; ?>
                    </small>
                </div>
                <div>
                <span class="badge-status <?php
                $status = isset($rapport['statut']) ? $rapport['statut'] : 'brouillon';
                if ($status == 'brouillon') echo 'brouillon';
                elseif ($status == 'valide') echo 'valide';
                elseif ($status == 'archive') echo 'archive';
                ?>">
                    <?php
                    $statusLabels = [
                        'brouillon' => 'Brouillon',
                        'valide' => 'Validé',
                        'archive' => 'Archivé'
                    ];
                    echo isset($statusLabels[$status]) ? $statusLabels[$status] : $status;
                    ?>
                </span>
                </div>
            </div>

            <div class="row">
                <!-- ===== COLONNE GAUCHE ===== -->
                <div class="col-md-6">
                    <!-- Informations du culte -->
                    <div style="background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 16px; border: 1px solid #eef2f6;">
                        <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px;">
                            <i class="fa fa-info-circle" style="margin-right: 8px; color: #3B82F6;"></i> INFORMATIONS DU CULTE
                        </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <p style="margin: 5px 0; font-size: 13px;">
                                    <strong>Date :</strong>
                                    <?php echo !empty($rapport['date_culte']) ? date('d/m/Y', strtotime($rapport['date_culte'])) : '—'; ?>
                                </p>
                                <p style="margin: 5px 0; font-size: 13px;">
                                    <strong>Type :</strong>
                                    <span style="background: #f1f5f9; padding: 2px 12px; border-radius: 12px; font-size: 12px; color: #475569;">
                                    <?php echo isset($types[$rapport['type_culte']]) ? $types[$rapport['type_culte']] : $rapport['type_culte']; ?>
                                </span>
                                </p>
                                <p style="margin: 5px 0; font-size: 13px;">
                                    <strong>Thème :</strong>
                                    <span style="font-weight: 600; color: #1e293b;">
                                    <?php echo isset($rapport['theme']) ? htmlspecialchars($rapport['theme']) : '—'; ?>
                                </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p style="margin: 5px 0; font-size: 13px;">
                                    <strong>Prédicateur :</strong>
                                    <?php echo isset($rapport['predicateur']) ? htmlspecialchars($rapport['predicateur']) : '—'; ?>
                                </p>
                                <p style="margin: 5px 0; font-size: 13px;">
                                    <strong>Passage biblique :</strong>
                                    <?php echo isset($rapport['passage_biblique']) ? htmlspecialchars($rapport['passage_biblique']) : '—'; ?>
                                </p>
                                <p style="margin: 5px 0; font-size: 13px;">
                                    <strong>Responsable :</strong>
                                    <?php echo isset($rapport['responsable_culte']) ? htmlspecialchars($rapport['responsable_culte']) : '—'; ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Participants -->
                    <div style="background: #eff6ff; padding: 15px; border-radius: 8px; margin-bottom: 16px; border-left: 4px solid #3B82F6; border: 1px solid #dbeafe;">
                        <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #dbeafe; padding-bottom: 8px;">
                            <i class="fa fa-users" style="margin-right: 8px; color: #3B82F6;"></i> PARTICIPANTS
                        </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div style="background: #ffffff; padding: 8px 12px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 6px;">
                                    <span style="font-size: 12px; color: #94a3b8;">Hommes</span>
                                    <div style="font-size: 18px; font-weight: 700; color: #3b82f6;">
                                        <?php echo isset($rapport['nombre_hommes']) ? $rapport['nombre_hommes'] : 0; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div style="background: #ffffff; padding: 8px 12px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 6px;">
                                    <span style="font-size: 12px; color: #94a3b8;">Femmes</span>
                                    <div style="font-size: 18px; font-weight: 700; color: #10b981;">
                                        <?php echo isset($rapport['nombre_femmes']) ? $rapport['nombre_femmes'] : 0; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div style="background: #ffffff; padding: 8px 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
                                    <span style="font-size: 12px; color: #94a3b8;">Enfants</span>
                                    <div style="font-size: 18px; font-weight: 700; color: #f59e0b;">
                                        <?php echo isset($rapport['nombre_enfants']) ? $rapport['nombre_enfants'] : 0; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div style="background: #ffffff; padding: 8px 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
                                    <span style="font-size: 12px; color: #94a3b8;">Visiteurs</span>
                                    <div style="font-size: 18px; font-weight: 700; color: #8b5cf6;">
                                        <?php echo isset($rapport['nombre_visiteurs']) ? $rapport['nombre_visiteurs'] : 0; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 10px; padding: 10px; background: #ffffff; border-radius: 6px; border: 1px solid #dbeafe; text-align: center;">
                            <span style="font-size: 13px; color: #64748b;">Total participants</span>
                            <div style="font-size: 24px; font-weight: 700; color: #1e293b;">
                                <?php
                                $total = (isset($rapport['nombre_hommes']) ? $rapport['nombre_hommes'] : 0) +
                                    (isset($rapport['nombre_femmes']) ? $rapport['nombre_femmes'] : 0) +
                                    (isset($rapport['nombre_enfants']) ? $rapport['nombre_enfants'] : 0) +
                                    (isset($rapport['nombre_visiteurs']) ? $rapport['nombre_visiteurs'] : 0);
                                echo $total;
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Finances -->
                    <div style="background: #fef3c7; padding: 15px; border-radius: 8px; margin-bottom: 16px; border-left: 4px solid #f59e0b; border: 1px solid #fde68a;">
                        <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #fde68a; padding-bottom: 8px;">
                            <i class="fa fa-money" style="margin-right: 8px; color: #f59e0b;"></i> FINANCES
                        </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div style="background: #ffffff; padding: 8px 12px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 6px;">
                                    <span style="font-size: 12px; color: #94a3b8;">Offrande</span>
                                    <div style="font-size: 16px; font-weight: 600; color: #f59e0b;">
                                        <?php echo isset($rapport['offrande']) ? number_format($rapport['offrande'], 0, ',', ' ') . ' FCFA' : '0 FCFA'; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div style="background: #ffffff; padding: 8px 12px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 6px;">
                                    <span style="font-size: 12px; color: #94a3b8;">Dîme</span>
                                    <div style="font-size: 16px; font-weight: 600; color: #f59e0b;">
                                        <?php echo isset($rapport['dime']) ? number_format($rapport['dime'], 0, ',', ' ') . ' FCFA' : '0 FCFA'; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div style="background: #ffffff; padding: 8px 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
                                    <span style="font-size: 12px; color: #94a3b8;">Actions de grâce</span>
                                    <div style="font-size: 16px; font-weight: 600; color: #f59e0b;">
                                        <?php echo isset($rapport['actions_de_grace']) ? number_format($rapport['actions_de_grace'], 0, ',', ' ') . ' FCFA' : '0 FCFA'; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div style="background: #ffffff; padding: 8px 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
                                    <span style="font-size: 12px; color: #94a3b8;">Autres offrandes</span>
                                    <div style="font-size: 16px; font-weight: 600; color: #f59e0b;">
                                        <?php echo isset($rapport['autres_offrandes']) ? number_format($rapport['autres_offrandes'], 0, ',', ' ') . ' FCFA' : '0 FCFA'; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 10px; padding: 10px; background: #ffffff; border-radius: 6px; border: 1px solid #fde68a; text-align: center;">
                            <span style="font-size: 13px; color: #64748b;">Total finances</span>
                            <div style="font-size: 22px; font-weight: 700; color: #f59e0b;">
                                <?php
                                $total_finances = (isset($rapport['offrande']) ? $rapport['offrande'] : 0) +
                                    (isset($rapport['dime']) ? $rapport['dime'] : 0) +
                                    (isset($rapport['actions_de_grace']) ? $rapport['actions_de_grace'] : 0) +
                                    (isset($rapport['autres_offrandes']) ? $rapport['autres_offrandes'] : 0);
                                echo number_format($total_finances, 0, ',', ' ') . ' FCFA';
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== COLONNE DROITE ===== -->
                <div class="col-md-6">
                    <!-- Statistiques spirituelles -->
                    <div style="background: #f3e8ff; padding: 15px; border-radius: 8px; margin-bottom: 16px; border-left: 4px solid #8b5cf6; border: 1px solid #ede9fe;">
                        <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #ede9fe; padding-bottom: 8px;">
                            <i class="fa fa-cross" style="margin-right: 8px; color: #8b5cf6;"></i> STATISTIQUES SPIRITUELLES
                        </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div style="background: #ffffff; padding: 8px 12px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 6px;">
                                    <span style="font-size: 12px; color: #94a3b8;">1ère Communion</span>
                                    <div style="font-size: 16px; font-weight: 600; color: #8b5cf6;">
                                        <?php echo isset($rapport['premiere_communion']) ? $rapport['premiere_communion'] : 0; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div style="background: #ffffff; padding: 8px 12px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 6px;">
                                    <span style="font-size: 12px; color: #94a3b8;">Baptêmes</span>
                                    <div style="font-size: 16px; font-weight: 600; color: #8b5cf6;">
                                        <?php echo isset($rapport['baptemes']) ? $rapport['baptemes'] : 0; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div style="background: #ffffff; padding: 8px 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
                                    <span style="font-size: 12px; color: #94a3b8;">Mariages</span>
                                    <div style="font-size: 16px; font-weight: 600; color: #8b5cf6;">
                                        <?php echo isset($rapport['mariages']) ? $rapport['mariages'] : 0; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div style="background: #ffffff; padding: 8px 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
                                    <span style="font-size: 12px; color: #94a3b8;">Funérailles</span>
                                    <div style="font-size: 16px; font-weight: 600; color: #8b5cf6;">
                                        <?php echo isset($rapport['funerailles']) ? $rapport['funerailles'] : 0; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div style="background: #ffffff; padding: 8px 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
                                    <span style="font-size: 12px; color: #94a3b8;">Prière malades</span>
                                    <div style="font-size: 16px; font-weight: 600; color: #8b5cf6;">
                                        <?php echo isset($rapport['priere_malades']) ? $rapport['priere_malades'] : 0; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div style="background: #ffffff; padding: 8px 12px; border-radius: 6px; border: 1px solid #e2e8f0;">
                                    <span style="font-size: 12px; color: #94a3b8;">Nouvelles conversions</span>
                                    <div style="font-size: 16px; font-weight: 600; color: #8b5cf6;">
                                        <?php echo isset($rapport['nouvelles_conversions']) ? $rapport['nouvelles_conversions'] : 0; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Suivi -->
                    <div style="background: #f0fdf4; padding: 15px; border-radius: 8px; margin-bottom: 16px; border-left: 4px solid #10b981; border: 1px solid #dcfce7;">
                        <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #dcfce7; padding-bottom: 8px;">
                            <i class="fa fa-heart" style="margin-right: 8px; color: #10b981;"></i> SUIVI ET VISITES
                        </h5>

                        <div class="row">
                            <div class="col-md-6">
                                <div style="background: #ffffff; padding: 8px 12px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 6px;">
                                    <span style="font-size: 12px; color: #94a3b8;">Rencontres maison</span>
                                    <div style="font-size: 16px; font-weight: 600; color: #10b981;">
                                        <?php echo isset($rapport['rencontres_maison']) ? $rapport['rencontres_maison'] : 0; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div style="background: #ffffff; padding: 8px 12px; border-radius: 6px; border: 1px solid #e2e8f0; margin-bottom: 6px;">
                                    <span style="font-size: 12px; color: #94a3b8;">Visites aux malades</span>
                                    <div style="font-size: 16px; font-weight: 600; color: #10b981;">
                                        <?php echo isset($rapport['visites_malades']) ? $rapport['visites_malades'] : 0; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Remarques -->
                    <?php if (!empty($rapport['remarques'])) : ?>
                        <div style="background: #f1f5f9; padding: 15px; border-radius: 8px; border: 1px solid #e2e8f0;">
                            <h5 style="margin: 0 0 10px 0; color: #1e293b; font-size: 13px; font-weight: 600;">
                                <i class="fa fa-file-text" style="margin-right: 8px; color: #64748b;"></i> REMARQUES
                            </h5>
                            <p style="margin: 0; color: #475569; font-size: 14px; line-height: 1.6;">
                                <?php echo nl2br(htmlspecialchars($rapport['remarques'])); ?>
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- Métadonnées -->
                    <div style="margin-top: 16px; padding: 12px 16px; background: #f1f5f9; border-radius: 6px; display: flex; gap: 20px; flex-wrap: wrap; font-size: 12px; color: #64748b;">
                        <span><strong>ID :</strong> #<?php echo $rapport['id']; ?></span>
                        <span><strong>Créé le :</strong>
                        <?php echo !empty($rapport['date_creation']) ? date('d/m/Y H:i', strtotime($rapport['date_creation'])) : '—'; ?>
                    </span>
                        <?php if (!empty($rapport['date_modification']) && $rapport['date_modification'] != $rapport['date_creation']) : ?>
                            <span><strong>Modifié le :</strong>
                        <?php echo date('d/m/Y H:i', strtotime($rapport['date_modification'])); ?>
                    </span>
                        <?php endif; ?>
                        <span><strong>Statut :</strong>
                        <?php
                        $statusLabels = [
                            'brouillon' => 'Brouillon',
                            'valide' => 'Validé',
                            'archive' => 'Archivé'
                        ];
                        echo isset($statusLabels[$rapport['statut']]) ? $statusLabels[$rapport['statut']] : $rapport['statut'];
                        ?>
                    </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else : ?>
    <div class="text-center text-muted" style="padding: 40px 0;">
        <i class="fa fa-exclamation-circle" style="font-size: 48px; color: #cbd5e1; display: block; margin-bottom: 16px;"></i>
        <p style="font-size: 16px;">Aucun détail disponible</p>
        <p style="font-size: 13px; color: #94a3b8;">Le rapport demandé n'a pas été trouvé</p>
    </div>
<?php endif; ?>

<!-- ===== FOOTER AVEC ACTIONS ===== -->
<div class="modal-footer" style="padding: 12px 20px; border-top: 1px solid #e2e8f0; background: #f8fafc; border-radius: 0 0 8px 8px; display: flex; gap: 8px; flex-wrap: wrap;">
    <button type="button" class="btn btn-default" data-dismiss="modal" style="padding: 6px 20px;">
        <i class="fa fa-times"></i> Fermer
    </button>

    <?php if (!empty($rapport) && $this->rbac->hasPrivilege('rapports_cultes', 'can_edit')) : ?>
        <button type="button" class="btn btn-primary" onclick="openEditModal(<?php echo $rapport['id']; ?>); $('#rapportDetailsModal').modal('hide');" style="padding: 6px 20px;">
            <i class="fa fa-pencil"></i> Modifier
        </button>
    <?php endif; ?>

    <?php if (!empty($rapport) && $this->rbac->hasPrivilege('rapports_cultes', 'can_delete')) : ?>
        <button type="button" class="btn btn-danger" onclick="confirmDelete(event, <?php echo $rapport['id']; ?>, '<?php echo htmlspecialchars($rapport['theme']); ?>'); $('#rapportDetailsModal').modal('hide');" style="padding: 6px 20px;">
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
    .badge-status.brouillon { background: #fef3c7; color: #92400e; }
    .badge-status.valide { background: #d1fae5; color: #065f46; }
    .badge-status.archive { background: #e2e8f0; color: #475569; }

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
            window.location.href = '<?php echo base_url('admin/rapports_cultes/edit/'); ?>' + id;
        }
    }

    function confirmDelete(event, id, theme) {
        if (typeof window.confirmDelete === 'function') {
            window.confirmDelete(event, id, theme);
        } else {
            if (confirm('Supprimer définitivement le rapport "' + theme + '" ?')) {
                window.location.href = '<?php echo base_url('admin/rapports_cultes/delete/'); ?>' + id;
            }
        }
    }
</script>