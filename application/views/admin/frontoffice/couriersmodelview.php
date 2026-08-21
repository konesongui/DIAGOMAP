<!-- ============================================================
     MODAL DÉTAILS - Courrier
     DESCRIPTION : Affichage des détails complets d'un courrier
     ============================================================ -->

<div class="modal-body" style="padding: 20px;">
    <?php if (!empty($courier)) : ?>
        <div class="row">
            <div class="col-md-12">

                <!-- En-tête avec statut -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #e2e8f0;">
                    <h4 style="margin: 0; color: #1e293b; font-weight: 600;">
                        <i class="fa fa-envelope" style="color: #3b82f6;"></i>
                        Détails du courrier
                    </h4>
                    <span class="badge-status <?php
                    echo $courier['status'] == 'pending' ? 'pending' :
                        ($courier['status'] == 'processed' ? 'processed' : 'archived');
                    ?>" style="padding: 5px 16px; font-size: 13px;">
                    <?php
                    $status_labels = [
                        'pending' => 'En attente',
                        'processed' => 'Traité',
                        'archived' => 'Archivé'
                    ];
                    echo $status_labels[$courier['status']] ?? $courier['status'];
                    ?>
                </span>
                </div>

                <!-- Carte d'identité du courrier -->
                <div style="background: #f8fafc; padding: 18px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e2e8f0;">
                    <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #e2e8f0; padding-bottom: 8px;">
                        <i class="fa fa-info-circle" style="margin-right: 8px; color: #3B82F6;"></i> INFORMATIONS DU COURRIER
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <p style="margin: 6px 0;"><strong>Type de courrier :</strong>
                                <span class="badge-courier-type <?php
                                $type = strtolower($courier['courier_type'] ?? '');
                                if (strpos($type, 'reçu') !== false || strpos($type, 'incoming') !== false) echo 'incoming';
                                elseif (strpos($type, 'envoi') !== false || strpos($type, 'outgoing') !== false) echo 'outgoing';
                                elseif (strpos($type, 'interne') !== false || strpos($type, 'internal') !== false) echo 'internal';
                                else echo 'other';
                                ?>">
                                <?php echo htmlspecialchars($courier['courier_type']); ?>
                            </span>
                            </p>
                            <p style="margin: 6px 0;"><strong>Nom :</strong> <?php echo htmlspecialchars($courier['sender_name']); ?></p>
                            <p style="margin: 6px 0;"><strong>Référence :</strong> <?php echo htmlspecialchars($courier['reference'] ?? '—'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p style="margin: 6px 0;"><strong>Date :</strong>
                                <?php
                                if (!empty($courier['date_received'])) {
                                    echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($courier['date_received']));
                                } else {
                                    echo '—';
                                }
                                ?>
                            </p>
                            <p style="margin: 6px 0;"><strong>Adresse :</strong> <?php echo htmlspecialchars($courier['address'] ?? '—'); ?></p>
                            <p style="margin: 6px 0;"><strong>Statut :</strong>
                                <span class="badge-status <?php
                                echo $courier['status'] == 'pending' ? 'pending' :
                                    ($courier['status'] == 'processed' ? 'processed' : 'archived');
                                ?>">
                                <?php echo $status_labels[$courier['status']] ?? $courier['status']; ?>
                            </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Description et note -->
                <div style="background: #eff6ff; padding: 18px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #3B82F6; border: 1px solid #dbeafe;">
                    <h5 style="margin: 0 0 12px 0; color: #1e293b; font-size: 13px; font-weight: 600; border-bottom: 2px solid #dbeafe; padding-bottom: 8px;">
                        <i class="fa fa-file-text" style="margin-right: 8px; color: #3B82F6;"></i> DESCRIPTION & NOTES
                    </h5>
                    <?php if (!empty($courier['description'])) : ?>
                        <div style="margin-bottom: 12px; padding: 10px; background: #ffffff; border-radius: 6px; border: 1px solid #e2e8f0;">
                            <strong style="color: #334155; font-size: 12px;">Description :</strong>
                            <p style="margin: 4px 0 0 0; color: #475569; font-size: 13px;"><?php echo nl2br(htmlspecialchars($courier['description'])); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($courier['note'])) : ?>
                        <div style="padding: 10px; background: #ffffff; border-radius: 6px; border: 1px solid #e2e8f0;">
                            <strong style="color: #334155; font-size: 12px;">Note :</strong>
                            <p style="margin: 4px 0 0 0; color: #475569; font-size: 13px;"><?php echo nl2br(htmlspecialchars($courier['note'])); ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Document attaché -->
                <?php if (!empty($courier['attachment'])) : ?>
                    <div style="background: #fef3c7; padding: 15px 20px; border-radius: 8px; border-left: 4px solid #f59e0b; border: 1px solid #fde68a;">
                        <h5 style="margin: 0 0 10px 0; color: #1e293b; font-size: 13px; font-weight: 600;">
                            <i class="fa fa-paperclip" style="margin-right: 8px; color: #f59e0b;"></i> DOCUMENT ATTACHÉ
                        </h5>
                        <p style="margin: 5px 0; display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                    <span style="background: #ffffff; padding: 4px 12px; border-radius: 4px; border: 1px solid #e2e8f0;">
                        <i class="fa fa-file" style="color: #3b82f6;"></i>
                        <?php echo htmlspecialchars($courier['attachment']); ?>
                    </span>
                            <a href="<?php echo base_url('admin/couriers/download/' . $courier['attachment']); ?>"
                               class="btn btn-sm btn-primary"
                               style="padding: 4px 16px; border-radius: 6px; background: #3b82f6; color: #fff; border: none; text-decoration: none;">
                                <i class="fa fa-download"></i> Télécharger
                            </a>
                        </p>
                    </div>
                <?php endif; ?>

                <!-- Métadonnées -->
                <div style="margin-top: 20px; padding: 12px 16px; background: #f1f5f9; border-radius: 6px; display: flex; gap: 20px; flex-wrap: wrap; font-size: 12px; color: #64748b;">
                    <span><strong>ID :</strong> #<?php echo $courier['id']; ?></span>
                    <span><strong>Créé le :</strong> <?php echo !empty($courier['created_at']) ? date('d/m/Y H:i', strtotime($courier['created_at'])) : '—'; ?></span>
                    <?php if (!empty($courier['updated_at']) && $courier['updated_at'] != $courier['created_at']) : ?>
                        <span><strong>Modifié le :</strong> <?php echo date('d/m/Y H:i', strtotime($courier['updated_at'])); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="text-center text-muted" style="padding: 40px 0;">
            <i class="fa fa-exclamation-circle" style="font-size: 48px; color: #cbd5e1; display: block; margin-bottom: 16px;"></i>
            <p style="font-size: 16px;">Aucun détail disponible</p>
            <p style="font-size: 13px; color: #94a3b8;">Le courrier demandé n'a pas été trouvé</p>
        </div>
    <?php endif; ?>
</div>

<div class="modal-footer" style="padding: 12px 20px; border-top: 1px solid #e2e8f0; background: #f8fafc; border-radius: 0 0 8px 8px;">
    <button type="button" class="btn btn-default" data-dismiss="modal" style="padding: 6px 20px;">
        <i class="fa fa-times"></i> Fermer
    </button>
    <?php if (!empty($courier) && $this->rbac->hasPrivilege('courriers', 'can_edit')) : ?>
        <button type="button" class="btn btn-primary" onclick="openEditModal(<?php echo $courier['id']; ?>); $('#courierdetails').modal('hide');" style="padding: 6px 20px;">
            <i class="fa fa-pencil"></i> Modifier
        </button>
    <?php endif; ?>
    <?php if (!empty($courier) && !empty($courier['attachment'])) : ?>
        <a href="<?php echo base_url('admin/couriers/download/' . $courier['attachment']); ?>" class="btn btn-success" style="padding: 6px 20px;">
            <i class="fa fa-download"></i> Télécharger
        </a>
    <?php endif; ?>
</div>

<style>
    .badge-courier-type {
        display: inline-block;
        padding: 2px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
    .badge-courier-type.incoming { background: #dbeafe; color: #1d4ed8; }
    .badge-courier-type.outgoing { background: #d1fae5; color: #059669; }
    .badge-courier-type.internal { background: #fef3c7; color: #d97706; }
    .badge-courier-type.other { background: #f1f5f9; color: #64748b; }

    .badge-status {
        display: inline-block;
        padding: 2px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }
    .badge-status.pending { background: #fef3c7; color: #92400e; }
    .badge-status.processed { background: #d1fae5; color: #065f46; }
    .badge-status.archived { background: #e2e8f0; color: #475569; }
</style>