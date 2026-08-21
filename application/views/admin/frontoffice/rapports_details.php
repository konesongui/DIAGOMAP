<?php if (!empty($rapport)) : ?>
    <div class="row">
        <div class="col-md-12">
            <!-- ===== EN-TÊTE ===== -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #e2e8f0;">
                <h4 style="margin: 0; color: #1e293b; font-weight: 600;">
                    <i class="fa fa-bar-chart" style="color: #3b82f6; margin-right: 10px;"></i>
                    <?php echo htmlspecialchars($rapport['titre']); ?>
                </h4>
                <div style="display: flex; gap: 10px; align-items: center;">
                <span class="badge-status <?php
                $status = $rapport['statut'] ?? 'en_attente';
                if ($status == 'en_attente') echo 'en-attente';
                elseif ($status == 'en_cours') echo 'en-cours';
                elseif ($status == 'termine') echo 'termine';
                elseif ($status == 'archive') echo 'archive';
                ?>">
                    <?php
                    $statusLabels = [
                        'en_attente' => 'En attente',
                        'en_cours' => 'En cours',
                        'termine' => 'Terminé',
                        'archive' => 'Archivé'
                    ];
                    echo isset($statusLabels[$status]) ? $statusLabels[$status] : $status;
                    ?>
                </span>
                    <span class="badge-priority <?php
                    $priority = $rapport['priorite'] ?? 'normale';
                    if ($priority == 'basse') echo 'basse';
                    elseif ($priority == 'normale') echo 'normale';
                    elseif ($priority == 'haute') echo 'haute';
                    elseif ($priority == 'urgente') echo 'urgente';
                    ?>">
                    <?php
                    $priorityLabels = [
                        'basse' => 'Basse',
                        'normale' => 'Normale',
                        'haute' => 'Haute',
                        'urgente' => 'Urgente'
                    ];
                    echo isset($priorityLabels[$priority]) ? $priorityLabels[$priority] : $priority;
                    ?>
                </span>
                </div>
            </div>

            <!-- ===== INFORMATIONS ===== -->
            <div style="background: #f8fafc; padding: 18px; border-radius: 8px; margin-bottom: 20px;">
                <div class="row">
                    <div class="col-md-6">
                        <p style="margin: 6px 0;">
                            <strong>Type de rapport :</strong>
                            <span style="background: #f1f5f9; padding: 2px 12px; border-radius: 12px; font-size: 12px; color: #475569;">
                            <?php
                            $typeLabels = [
                                'finance' => 'Rapport financier',
                                'statistique' => 'Rapport statistique',
                                'projet' => 'Rapport de projet',
                                'activite' => 'Rapport d\'activité',
                                'rh' => 'Rapport RH',
                                'vente' => 'Rapport de vente',
                                'inventaire' => 'Rapport d\'inventaire',
                                'autre' => 'Autre'
                            ];
                            echo isset($typeLabels[$rapport['type_rapport']]) ? $typeLabels[$rapport['type_rapport']] : $rapport['type_rapport'];
                            ?>
                        </span>
                        </p>
                        <p style="margin: 6px 0;">
                            <strong>Période :</strong>
                            <?php if (!empty($rapport['periode_debut']) && !empty($rapport['periode_fin'])) : ?>
                                <?php echo date('d/m/Y', strtotime($rapport['periode_debut'])); ?>
                                <i class="fa fa-arrow-right" style="color: #94a3b8; font-size: 12px; margin: 0 4px;"></i>
                                <?php echo date('d/m/Y', strtotime($rapport['periode_fin'])); ?>
                            <?php else : ?>
                                <span style="color: #94a3b8;">Non définie</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p style="margin: 6px 0;">
                            <strong>Date de création :</strong>
                            <?php echo !empty($rapport['date_creation']) ? date('d/m/Y H:i', strtotime($rapport['date_creation'])) : ''; ?>
                        </p>
                        <?php if (!empty($rapport['date_modification']) && $rapport['date_modification'] != $rapport['date_creation']) : ?>
                            <p style="margin: 6px 0;">
                                <strong>Dernière modification :</strong>
                                <?php echo date('d/m/Y H:i', strtotime($rapport['date_modification'])); ?>
                            </p>
                        <?php endif; ?>
                        <p style="margin: 6px 0;">
                            <strong>ID :</strong> #<?php echo $rapport['id']; ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- ===== DESCRIPTION ===== -->
            <?php if (!empty($rapport['description'])) : ?>
                <div style="background: #eff6ff; padding: 18px; border-radius: 8px; border-left: 4px solid #3B82F6; margin-bottom: 20px;">
                    <h5 style="margin: 0 0 10px 0; color: #1e293b; font-size: 13px; font-weight: 600;">
                        <i class="fa fa-file-text" style="margin-right: 8px; color: #3B82F6;"></i> Description
                    </h5>
                    <p style="margin: 0; color: #475569; font-size: 14px; line-height: 1.6;">
                        <?php echo nl2br(htmlspecialchars($rapport['description'])); ?>
                    </p>
                </div>
            <?php endif; ?>

            <!-- ===== FICHIER JOINT ===== -->
            <?php if (!empty($rapport['fichier'])) : ?>
                <div style="background: #fef3c7; padding: 15px 20px; border-radius: 8px; border-left: 4px solid #f59e0b; border: 1px solid #fde68a;">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <i class="fa <?php
                            $ext = strtolower(pathinfo($rapport['fichier'], PATHINFO_EXTENSION));
                            $iconMap = [
                                'pdf' => 'fa-file-pdf-o',
                                'doc' => 'fa-file-word-o',
                                'docx' => 'fa-file-word-o',
                                'xls' => 'fa-file-excel-o',
                                'xlsx' => 'fa-file-excel-o',
                                'jpg' => 'fa-file-image-o',
                                'jpeg' => 'fa-file-image-o',
                                'png' => 'fa-file-image-o',
                                'zip' => 'fa-file-archive-o'
                            ];
                            echo isset($iconMap[$ext]) ? $iconMap[$ext] : 'fa-file-o';
                            ?>" style="font-size: 32px; color: <?php
                            $colorMap = [
                                'pdf' => '#dc2626',
                                'doc' => '#2563eb',
                                'docx' => '#2563eb',
                                'xls' => '#16a34a',
                                'xlsx' => '#16a34a',
                                'jpg' => '#8b5cf6',
                                'jpeg' => '#8b5cf6',
                                'png' => '#8b5cf6',
                                'zip' => '#6b7280'
                            ];
                            echo isset($colorMap[$ext]) ? $colorMap[$ext] : '#6b7280';
                            ?>;"></i>
                            <div>
                                <p style="margin: 0; font-weight: 500; color: #1e293b;"><?php echo htmlspecialchars($rapport['fichier']); ?></p>
                                <p style="margin: 0; font-size: 12px; color: #64748b;">
                                    <?php echo isset($rapport['taille']) ? $this->rapports_model->format_size($rapport['taille']) : ''; ?>
                                    <?php if (!empty($rapport['type_fichier'])) : ?>
                                        - <?php echo strtoupper($rapport['type_fichier']); ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <a href="<?php echo base_url('admin/rapports/download/' . $rapport['fichier']); ?>"
                           class="btn btn-primary" style="padding: 8px 24px; border-radius: 8px; background: #3b82f6; color: #fff; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fa fa-download"></i> Télécharger
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ===== STATUT AVANCEMENT ===== -->
            <div style="margin-top: 20px; padding: 15px 20px; background: #f1f5f9; border-radius: 8px;">
                <h5 style="margin: 0 0 10px 0; color: #1e293b; font-size: 13px; font-weight: 600;">
                    <i class="fa fa-info-circle" style="margin-right: 8px; color: #64748b;"></i> Informations supplémentaires
                </h5>
                <div style="display: flex; flex-wrap: wrap; gap: 20px; font-size: 12px; color: #64748b;">
                <span><strong>Statut actuel :</strong>
                    <span class="badge-status <?php
                    $status = $rapport['statut'] ?? 'en_attente';
                    if ($status == 'en_attente') echo 'en-attente';
                    elseif ($status == 'en_cours') echo 'en-cours';
                    elseif ($status == 'termine') echo 'termine';
                    elseif ($status == 'archive') echo 'archive';
                    ?>" style="font-size: 11px;">
                        <?php echo isset($statusLabels[$status]) ? $statusLabels[$status] : $status; ?>
                    </span>
                </span>
                    <span><strong>Priorité :</strong>
                    <span class="badge-priority <?php
                    $priority = $rapport['priorite'] ?? 'normale';
                    if ($priority == 'basse') echo 'basse';
                    elseif ($priority == 'normale') echo 'normale';
                    elseif ($priority == 'haute') echo 'haute';
                    elseif ($priority == 'urgente') echo 'urgente';
                    ?>" style="font-size: 11px;">
                        <?php echo isset($priorityLabels[$priority]) ? $priorityLabels[$priority] : $priority; ?>
                    </span>
                </span>
                    <?php if (!empty($rapport['periode_debut']) && !empty($rapport['periode_fin'])) : ?>
                        <span><strong>Période :</strong> <?php echo date('d/m/Y', strtotime($rapport['periode_debut'])); ?> → <?php echo date('d/m/Y', strtotime($rapport['periode_fin'])); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ===== BOUTONS D'ACTION ===== -->
            <div style="margin-top: 20px; display: flex; gap: 10px; flex-wrap: wrap; padding-top: 15px; border-top: 1px solid #e2e8f0;">
                <?php if ($this->rbac->hasPrivilege('rapports', 'can_edit')) : ?>
                    <button type="button" class="btn btn-primary" onclick="openEditModal(<?php echo $rapport['id']; ?>); $('#rapportDetailsModal').modal('hide');"
                            style="padding: 8px 24px; border-radius: 8px; background: #3b82f6; color: #fff; border: none; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fa fa-pencil"></i> Modifier
                    </button>
                <?php endif; ?>

                <?php if (!empty($rapport['fichier'])) : ?>
                    <a href="<?php echo base_url('admin/rapports/download/' . $rapport['fichier']); ?>"
                       class="btn btn-success" style="padding: 8px 24px; border-radius: 8px; background: #10b981; color: #fff; border: none; display: inline-flex; align-items: center; gap: 8px; text-decoration: none;">
                        <i class="fa fa-download"></i> Télécharger
                    </a>
                <?php endif; ?>

                <?php if ($this->rbac->hasPrivilege('rapports', 'can_delete')) : ?>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete(event, <?php echo $rapport['id']; ?>, '<?php echo htmlspecialchars($rapport['titre']); ?>'); $('#rapportDetailsModal').modal('hide');"
                            style="padding: 8px 24px; border-radius: 8px; background: #dc2626; color: #fff; border: none; display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fa fa-trash"></i> Supprimer
                    </button>
                <?php endif; ?>

                <button type="button" class="btn btn-default" data-dismiss="modal"
                        style="padding: 8px 24px; border-radius: 8px; background: #f1f5f9; color: #475569; border: none; display: inline-flex; align-items: center; gap: 8px;">
                    <i class="fa fa-times"></i> Fermer
                </button>
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

<style>
    .badge-status {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
    }
    .badge-status.en-attente { background: #fef3c7; color: #92400e; }
    .badge-status.en-cours { background: #dbeafe; color: #1d4ed8; }
    .badge-status.termine { background: #d1fae5; color: #065f46; }
    .badge-status.archive { background: #e2e8f0; color: #475569; }

    .badge-priority {
        display: inline-block;
        padding: 3px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 500;
    }
    .badge-priority.basse { background: #e2e8f0; color: #475569; }
    .badge-priority.normale { background: #dbeafe; color: #1d4ed8; }
    .badge-priority.haute { background: #fef3c7; color: #92400e; }
    .badge-priority.urgente { background: #fef2f2; color: #991b1b; }

    .btn {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }
    .btn:active {
        transform: translateY(0);
    }
</style>