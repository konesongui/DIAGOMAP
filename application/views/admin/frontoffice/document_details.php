<?php if (!empty($document)) : ?>
    <div class="row">
        <div class="col-md-12">
            <!-- En-tête -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #e2e8f0;">
                <h4 style="margin: 0; color: #1e293b; font-weight: 600;">
                    <i class="fa fa-file" style="color: #3b82f6;"></i>
                    <?php echo htmlspecialchars($document['titre']); ?>
                </h4>
                <span class="badge-status <?php echo $document['statut'] == 'actif' ? 'actif' : 'archive'; ?>">
                <?php echo $document['statut'] == 'actif' ? 'Actif' : 'Archivé'; ?>
            </span>
            </div>

            <!-- Informations -->
            <div style="background: #f8fafc; padding: 18px; border-radius: 8px; margin-bottom: 20px;">
                <div class="row">
                    <div class="col-md-6">
                        <p style="margin: 6px 0;"><strong>Catégorie :</strong>
                            <span class="badge-category">
                            <?php echo $this->documents_model->get_category_label($document['categorie']); ?>
                        </span>
                        </p>
                        <p style="margin: 6px 0;">
                            <strong>Type de fichier :</strong>
                            <span style="text-transform: uppercase; font-weight: 600;">
                            <?php echo htmlspecialchars($document['type_fichier'] ?? '—'); ?>
                        </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p style="margin: 6px 0;"><strong>Taille :</strong>
                            <?php echo $this->documents_model->format_size($document['taille'] ?? 0); ?>
                        </p>
                        <p style="margin: 6px 0;"><strong>Date de création :</strong>
                            <?php echo !empty($document['date_creation']) ? date('d/m/Y H:i', strtotime($document['date_creation'])) : ''; ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Description -->
            <?php if (!empty($document['description'])) : ?>
                <div style="background: #eff6ff; padding: 18px; border-radius: 8px; border-left: 4px solid #3B82F6; margin-bottom: 20px;">
                    <h5 style="margin: 0 0 10px 0; color: #1e293b; font-size: 13px; font-weight: 600;">
                        <i class="fa fa-file-text" style="margin-right: 8px; color: #3B82F6;"></i> Description
                    </h5>
                    <p style="margin: 0; color: #475569; font-size: 14px; line-height: 1.6;">
                        <?php echo nl2br(htmlspecialchars($document['description'])); ?>
                    </p>
                </div>
            <?php endif; ?>

            <!-- Téléchargement -->
            <?php if (!empty($document['fichier'])) : ?>
                <div style="background: #fef3c7; padding: 15px 20px; border-radius: 8px; border-left: 4px solid #f59e0b; border: 1px solid #fde68a;">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <i class="fa <?php echo $this->documents_model->get_file_icon($document['type_fichier'] ?? ''); ?>"
                               style="font-size: 32px; color: <?php echo $this->documents_model->get_file_color($document['type_fichier'] ?? ''); ?>;"></i>
                            <div>
                                <p style="margin: 0; font-weight: 500; color: #1e293b;"><?php echo htmlspecialchars($document['fichier']); ?></p>
                                <p style="margin: 0; font-size: 12px; color: #64748b;">
                                    <?php echo $this->documents_model->format_size($document['taille'] ?? 0); ?>
                                    - <?php echo strtoupper($document['type_fichier'] ?? ''); ?>
                                </p>
                            </div>
                        </div>
                        <a href="<?php echo base_url('admin/documents/download/' . $document['fichier']); ?>"
                           class="btn btn-primary" style="padding: 8px 24px; border-radius: 8px; background: #3b82f6; color: #fff; border: none; text-decoration: none;">
                            <i class="fa fa-download"></i> Télécharger
                        </a>
                    </div>
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