<?php if (!empty($reunion)) : ?>
    <div class="row">
        <div class="col-md-12">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #e2e8f0;">
                <h4 style="margin: 0; color: #1e293b; font-weight: 600;">
                    <span style="display: inline-block; width: 16px; height: 16px; border-radius: 50%; background: <?php echo $reunion['couleur'] ?? '#8b5cf6'; ?>; vertical-align: middle; margin-right: 10px;"></span>
                    <?php echo htmlspecialchars($reunion['titre']); ?>
                </h4>
                <span class="badge-status <?php echo $this->reunions_model->get_status_badge($reunion['statut']); ?>">
                <?php echo $this->reunions_model->get_status_label($reunion['statut']); ?>
            </span>
            </div>

            <div style="background: #f8fafc; padding: 18px; border-radius: 8px; margin-bottom: 20px;">
                <div class="row">
                    <div class="col-md-6">
                        <p style="margin: 6px 0;"><strong>Date :</strong> <?php echo !empty($reunion['date_reunion']) ? date('d/m/Y', strtotime($reunion['date_reunion'])) : ''; ?></p>
                        <p style="margin: 6px 0;"><strong>Heure :</strong> <?php echo substr($reunion['heure_debut'] ?? '', 0, 5); ?> - <?php echo substr($reunion['heure_fin'] ?? '', 0, 5); ?></p>
                        <p style="margin: 6px 0;"><strong>Lieu :</strong> <?php echo htmlspecialchars($reunion['lieu'] ?? '—'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p style="margin: 6px 0;"><strong>Participants :</strong> <?php echo htmlspecialchars($reunion['participants'] ?? '—'); ?></p>
                        <p style="margin: 6px 0;"><strong>Date de création :</strong> <?php echo !empty($reunion['date_creation']) ? date('d/m/Y H:i', strtotime($reunion['date_creation'])) : ''; ?></p>
                    </div>
                </div>
            </div>

            <?php if (!empty($reunion['ordre_du_jour'])) : ?>
                <div style="background: #eff6ff; padding: 18px; border-radius: 8px; border-left: 4px solid #3B82F6; margin-bottom: 20px;">
                    <h5 style="margin: 0 0 10px 0; color: #1e293b; font-size: 13px; font-weight: 600;">
                        <i class="fa fa-list" style="margin-right: 8px; color: #3B82F6;"></i> Ordre du jour
                    </h5>
                    <p style="margin: 0; color: #475569; font-size: 14px; line-height: 1.6;">
                        <?php echo nl2br(htmlspecialchars($reunion['ordre_du_jour'])); ?>
                    </p>
                </div>
            <?php endif; ?>

            <?php if (!empty($reunion['compte_rendu'])) : ?>
                <div style="background: #fef3c7; padding: 18px; border-radius: 8px; border-left: 4px solid #f59e0b;">
                    <h5 style="margin: 0 0 10px 0; color: #1e293b; font-size: 13px; font-weight: 600;">
                        <i class="fa fa-file-text" style="margin-right: 8px; color: #f59e0b;"></i> Compte rendu
                    </h5>
                    <p style="margin: 0; color: #475569; font-size: 14px; line-height: 1.6;">
                        <?php echo nl2br(htmlspecialchars($reunion['compte_rendu'])); ?>
                    </p>
                </div>
            <?php endif; ?>

            <?php if (!empty($reunion['description'])) : ?>
                <div style="background: #f1f5f9; padding: 18px; border-radius: 8px; margin-top: 20px;">
                    <h5 style="margin: 0 0 10px 0; color: #1e293b; font-size: 13px; font-weight: 600;">
                        <i class="fa fa-info-circle" style="margin-right: 8px; color: #64748b;"></i> Description
                    </h5>
                    <p style="margin: 0; color: #475569; font-size: 14px; line-height: 1.6;">
                        <?php echo nl2br(htmlspecialchars($reunion['description'])); ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php else : ?>
    <div class="text-center text-muted" style="padding: 40px 0;">
        <i class="fa fa-exclamation-circle" style="font-size: 48px; color: #cbd5e1; display: block; margin-bottom: 16px;"></i>
        <p style="font-size: 16px;">Aucun détail disponible</p>
    </div>
<?php endif; ?>