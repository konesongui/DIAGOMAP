<?php if (!empty($list)): ?>
    <ul class="notification-list">
        <?php foreach ($list as $item): ?>
            <li class="notification-item <?php echo ($item->is_read == 0) ? 'unread' : ''; ?>" data-id="<?php echo $item->id; ?>">
                <div class="notification-icon-wrapper">
                    <i class="fa fa-user-plus"></i>
                    <?php if ($item->is_read == 0): ?>
                        <span class="notification-dot"></span>
                    <?php endif; ?>
                </div>
                <div class="notification-content">
                    <div class="notification-title">
                        <a href="<?php echo base_url(); ?>admin/enquiry">
                            <?php echo $item->name; ?>
                        </a>
                        <span class="notification-type <?php echo $item->source == 'permission' ? 'type-permission' : 'type-demission'; ?>">
                            <?php echo ucfirst($item->source); ?>
                        </span>
                    </div>
                    <div class="notification-details">
                        <span><i class="fa fa-phone"></i> <?php echo $item->contact; ?></span>
                        <span><i class="fa fa-calendar"></i> <?php echo date('d/m/Y H:i', strtotime($item->date)); ?></span>
                    </div>
                    <div class="notification-status-badge status-pending">
                        <i class="fa fa-hourglass-1"></i> En attente
                    </div>
                </div>
                <?php if ($item->is_read == 0): ?>
                    <button class="btn-mark-read-single" data-id="<?php echo $item->id; ?>" title="Marquer comme lu">
                        <i class="fa fa-check"></i>
                    </button>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <div class="empty-notifications">
        <i class="fa fa-check-circle"></i>
        <p>Aucune nouvelle notification</p>
        <span>Toutes les demandes ont été traitées</span>
    </div>
<?php endif; ?>