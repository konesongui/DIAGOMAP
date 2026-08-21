<?php if (!empty($demande)) : ?>
    <div class="row">
        <div class="col-md-12">
            <!-- En-tête -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #e2e8f0;">
                <h4 style="margin: 0; color: #1e293b; font-weight: 600;">
                    <i class="fa fa-tasks" style="color: #3b82f6;"></i>
                    <?php echo htmlspecialchars($demande['titre']); ?>
                </h4>
                <span class="badge-status <?php
                echo $demande['statut'] == 'en_attente' ? 'en-attente' :
                    ($demande['statut'] == 'en_cours' ? 'en-cours' :
                        ($demande['statut'] == 'termine' ? 'termine' : 'rejete'));
                ?>">
                <?php
                $statusLabels = [
                    'en_attente' => 'En attente',
                    'en_cours' => 'En cours',
                    'termine' => 'Terminé',
                    'rejete' => 'Rejeté'
                ];
                echo $statusLabels[$demande['statut']] ?? $demande['statut'];
                ?>
            </span>
            </div>

            <!-- Informations -->
            <div style="background: #f8fafc; padding: 18px; border-radius: 8px; margin-bottom: 20px;">
                <div class="row">
                    <div class="col-md-6">
                        <p style="margin: 6px 0;"><strong>Catégorie :</strong>
                            <?php
                            $categoryLabels = [
                                'comptabilite' => 'Comptabilité',
                                'ressources_humaines' => 'Ressources Humaines',
                                'informatique' => 'Informatique',
                                'logistique' => 'Logistique',
                                'communication' => 'Communication',
                                'autre' => 'Autre'
                            ];
                            echo $categoryLabels[$demande['categorie']] ?? $demande['categorie'];
                            ?>
                        </p>
                        <p style="margin: 6px 0;"><strong>Priorité :</strong>
                            <span class="badge-priority <?php
                            echo $demande['priorite'] == 'basse' ? 'basse' :
                                ($demande['priorite'] == 'haute' ? 'haute' :
                                    ($demande['priorite'] == 'urgente' ? 'urgente' : 'normale'));
                            ?>">
                            <?php
                            $priorityLabels = [
                                'basse' => 'Basse',
                                'normale' => 'Normale',
                                'haute' => 'Haute',
                                'urgente' => 'Urgente'
                            ];
                            echo $priorityLabels[$demande['priorite']] ?? $demande['priorite'];
                            ?>
                        </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p style="margin: 6px 0;"><strong>Date de création :</strong>
                            <?php echo !empty($demande['date_creation']) ? date('d/m/Y H:i', strtotime($demande['date_creation'])) : ''; ?>
                        </p>
                        <?php if (!empty($demande['date_modification']) && $demande['date_modification'] != $demande['date_creation']) : ?>
                            <p style="margin: 6px 0;"><strong>Dernière modification :</strong>
                                <?php echo date('d/m/Y H:i', strtotime($demande['date_modification'])); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div style="background: #eff6ff; padding: 18px; border-radius: 8px; border-left: 4px solid #3B82F6;">
                <h5 style="margin: 0 0 10px 0; color: #1e293b; font-size: 13px; font-weight: 600;">
                    <i class="fa fa-file-text" style="margin-right: 8px; color: #3B82F6;"></i> Description
                </h5>
                <p style="margin: 0; color: #475569; font-size: 14px; line-height: 1.6;">
                    <?php echo nl2br(htmlspecialchars($demande['description'])); ?>
                </p>
            </div>
        </div>
    </div>
<?php else : ?>
    <div class="text-center text-muted" style="padding: 40px 0;">
        <i class="fa fa-exclamation-circle" style="font-size: 48px; color: #cbd5e1; display: block; margin-bottom: 16px;"></i>
        <p style="font-size: 16px;">Aucun détail disponible</p>
    </div>
<?php endif; ?>