<?php if (!empty($amortissement)) : ?>
    <div class="row">
        <div class="col-md-12">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #e2e8f0;">
                <h4 style="margin: 0; color: #1e293b; font-weight: 600;">
                    <i class="fa fa-line-chart" style="color: #3b82f6; margin-right: 10px;"></i>
                    Détail de l'amortissement
                </h4>
                <span class="badge-type <?php echo $amortissement['type'] ?? 'effectif'; ?>">
                <?php echo isset($amortissement['type']) ? ucfirst($amortissement['type']) : 'Effectif'; ?>
            </span>
            </div>

            <div style="background: #f8fafc; padding: 18px; border-radius: 8px; margin-bottom: 20px;">
                <div class="row">
                    <div class="col-md-6">
                        <p style="margin: 6px 0;">
                            <strong>Immobilisation :</strong>
                            <?php echo isset($amortissement['immobilisation_nom']) ? htmlspecialchars($amortissement['immobilisation_nom']) : '—'; ?>
                        </p>
                        <p style="margin: 6px 0;">
                            <strong>Code :</strong>
                            <?php echo isset($amortissement['immobilisation_code']) ? htmlspecialchars($amortissement['immobilisation_code']) : '—'; ?>
                        </p>
                        <p style="margin: 6px 0;">
                            <strong>Catégorie :</strong>
                            <?php echo isset($amortissement['categorie']) ? htmlspecialchars($amortissement['categorie']) : '—'; ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p style="margin: 6px 0;">
                            <strong>Période :</strong>
                            <?php if (!empty($amortissement['periode_debut']) && !empty($amortissement['periode_fin'])) : ?>
                                <?php echo date('d/m/Y', strtotime($amortissement['periode_debut'])); ?>
                                <span style="color: #94a3b8;">→</span>
                                <?php echo date('d/m/Y', strtotime($amortissement['periode_fin'])); ?>
                            <?php else : ?>
                                —
                            <?php endif; ?>
                        </p>
                        <p style="margin: 6px 0;">
                            <strong>Montant :</strong>
                            <span style="font-weight: 600; color: #f59e0b; font-size: 16px;">
                            <?php echo isset($amortissement['montant']) ? number_format($amortissement['montant'], 0, ',', ' ') . ' FCFA' : '0 FCFA'; ?>
                        </span>
                        </p>
                        <p style="margin: 6px 0;">
                            <strong>Type :</strong>
                            <?php echo isset($amortissement['type']) ? ucfirst($amortissement['type']) : 'Effectif'; ?>
                        </p>
                    </div>
                </div>
            </div>

            <?php if (!empty($amortissement['description'])) : ?>
                <div style="background: #eff6ff; padding: 18px; border-radius: 8px; border-left: 4px solid #3B82F6;">
                    <h5 style="margin: 0 0 10px 0; color: #1e293b; font-size: 13px; font-weight: 600;">
                        <i class="fa fa-file-text" style="margin-right: 8px; color: #3B82F6;"></i> Description
                    </h5>
                    <p style="margin: 0; color: #475569; font-size: 14px; line-height: 1.6;">
                        <?php echo nl2br(htmlspecialchars($amortissement['description'])); ?>
                    </p>
                </div>
            <?php endif; ?>

            <div style="margin-top: 20px; padding: 12px 16px; background: #f1f5f9; border-radius: 6px; font-size: 12px; color: #64748b;">
                <span><strong>ID :</strong> #<?php echo $amortissement['id']; ?></span>
                <span style="margin-left: 20px;"><strong>Créé le :</strong> <?php echo !empty($amortissement['date_creation']) ? date('d/m/Y H:i', strtotime($amortissement['date_creation'])) : '—'; ?></span>
            </div>
        </div>
    </div>
<?php else : ?>
    <div class="text-center text-muted" style="padding: 40px 0;">
        <i class="fa fa-exclamation-circle" style="font-size: 48px; color: #cbd5e1; display: block; margin-bottom: 16px;"></i>
        <p style="font-size: 16px;">Aucun détail disponible</p>
    </div>
<?php endif; ?>