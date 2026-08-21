<?php if (!empty($rendezvous)) : ?>
    <div class="row">
        <div class="col-md-12">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #e2e8f0;">
                <h4 style="margin: 0; color: #1e293b; font-weight: 600;">
                    <span style="display: inline-block; width: 16px; height: 16px; border-radius: 50%; background: <?php echo $rendezvous['couleur'] ?? '#3b82f6'; ?>; vertical-align: middle; margin-right: 10px;"></span>
                    <?php echo htmlspecialchars($rendezvous['titre']); ?>
                </h4>
                <span class="badge-status <?php echo $this->rendezvous_model->get_status_badge($rendezvous['statut']); ?>">
                <?php echo $this->rendezvous_model->get_status_label($rendezvous['statut']); ?>
            </span>
            </div>

            <div style="background: #f8fafc; padding: 18px; border-radius: 8px; margin-bottom: 20px;">
                <div class="row">
                    <div class="col-md-6">
                        <p style="margin: 6px 0;"><strong>Date :</strong> <?php echo !empty($rendezvous['date_rendez_vous']) ? date('d/m/Y', strtotime($rendezvous['date_rendez_vous'])) : ''; ?></p>
                        <p style="margin: 6px 0;"><strong>Heure :</strong> <?php echo substr($rendezvous['heure_debut'] ?? '', 0, 5); ?> - <?php echo substr($rendezvous['heure_fin'] ?? '', 0, 5); ?></p>
                        <p style="margin: 6px 0;"><strong>Lieu :</strong> <?php echo htmlspecialchars($rendezvous['lieu'] ?? '—'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p style="margin: 6px 0;"><strong>Participants :</strong> <?php echo htmlspecialchars($rendezvous['participants'] ?? '—'); ?></p>
                        <p style="margin: 6px 0;"><strong>Rappel :</strong> <?php echo ($rendezvous['rappel'] ?? 0) ? 'Activé' : 'Désactivé'; ?></p>
                        <p style="margin: 6px 0;"><strong>Date de création :</strong> <?php echo !empty($rendezvous['date_creation']) ? date('d/m/Y H:i', strtotime($rendezvous['date_creation'])) : ''; ?></p>
                    </div>
                </div>
            </div>

            <?php if (!empty($rendezvous['description'])) : ?>
                <div style="background: #eff6ff; padding: 18px; border-radius: 8px; border-left: 4px solid #3B82F6;">
                    <h5 style="margin: 0 0 10px 0; color: #1e293b; font-size: 13px; font-weight: 600;">
                        <i class="fa fa-file-text" style="margin-right: 8px; color: #3B82F6;"></i> Description
                    </h5>
                    <p style="margin: 0; color: #475569; font-size: 14px; line-height: 1.6;">
                        <?php echo nl2br(htmlspecialchars($rendezvous['description'])); ?>
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