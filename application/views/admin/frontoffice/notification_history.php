<?php if (!empty($list)): ?>
    <ul class="notification-list history-list">
        <?php foreach ($list as $item): ?>
            <li class="notification-item history-item">
                <div class="notification-icon-wrapper history-icon">
                    <i class="fa fa-check-circle"></i>
                </div>
                <div class="notification-content">
                    <div class="notification-title">
                        <a href="<?php echo base_url(); ?>admin/enquiry">
                            <?php echo $item->name; ?>
                        </a>
                        <span class="notification-status-badge status-<?php echo $item->status; ?>">
                            <?php echo ucfirst($item->status); ?>
                        </span>
                    </div>
                    <div class="notification-details">
                        <span><i class="fa fa-phone"></i> <?php echo $item->contact; ?></span>
                        <span><i class="fa fa-calendar"></i> <?php echo date('d/m/Y H:i', strtotime($item->date)); ?></span>
                    </div>
                    <?php if (!empty($item->read_at)): ?>
                        <div class="notification-read-at">
                            <i class="fa fa-clock-o"></i> Lu le <?php echo date('d/m/Y H:i', strtotime($item->read_at)); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
<?php else: ?>
    <div class="empty-notifications">
        <i class="fa fa-history"></i>
        <p>Aucun historique</p>
        <span>Aucune demande traitée récemment</span>
    </div>
<?php endif; ?>