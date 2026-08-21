<?php if (!empty($list)): ?>
    <?php foreach ($list as $item): ?>
        <div class="notification-item" data-id="<?php echo $item['id']; ?>">
            <div class="notification-icon">
                <i class="fa fa-user-plus"></i>
            </div>
            <div class="notification-content">
                <div class="notification-title">
                    <strong><?php echo $item['name']; ?></strong>
                    <span class="notification-status status-pending">En attente</span>
                </div>
                <div class="notification-details">
                    <span><i class="fa fa-phone"></i> <?php echo $item['contact']; ?></span>
                    <span><i class="fa fa-tag"></i> <?php echo $item['source']; ?></span>
                    <span><i class="fa fa-calendar"></i> <?php echo date('d/m/Y', strtotime($item['date'])); ?></span>
                </div>
                <div class="notification-actions">
                    <a href="<?php echo base_url(); ?>admin/enquiry" class="btn-notification-action">
                        <i class="fa fa-eye"></i> Voir
                    </a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="notification-empty">
        <i class="fa fa-check-circle"></i>
        <p>Aucune demande en attente</p>
        <span>Toutes les demandes ont été traitées</span>
    </div>
<?php endif; ?>