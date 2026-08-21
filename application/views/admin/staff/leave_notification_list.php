<?php if (!empty($list)): ?>
    <ul class="notification-list">
        <?php foreach ($list as $item): ?>
            <li class="notification-item <?php echo ($item->is_read == 0) ? 'unread' : ''; ?>" data-id="<?php echo $item->id; ?>">
                <div class="notification-icon-wrapper">
                    <i class="fa fa-calendar-check-o"></i>
                    <?php if ($item->is_read == 0): ?>
                        <span class="notification-dot"></span>
                    <?php endif; ?>
                </div>
                <div class="notification-content">
                    <div class="notification-title">
                        <a href="<?php echo base_url(); ?>admin/leaverequest/leaverequest">
                            <?php echo $item->name . ' ' . $item->surname; ?>
                        </a>
                        <span class="notification-type type-leave">
                            <i class="fa fa-plane"></i> Congé
                        </span>
                    </div>
                    <div class="notification-details">
                        <span><i class="fa fa-id-card"></i> <?php echo $item->employee_id; ?></span>
                        <span><i class="fa fa-tag"></i> <?php echo $item->leave_type_name; ?></span>
                        <span><i class="fa fa-calendar"></i> <?php echo date('d/m/Y', strtotime($item->leave_from)); ?> - <?php echo date('d/m/Y', strtotime($item->leave_to)); ?></span>
                        <span><i class="fa fa-clock-o"></i> <?php echo $item->leave_days; ?> jour(s)</span>
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
        <p>Aucune demande de congé en attente</p>
        <span>Toutes les demandes ont été traitées</span>
    </div>
<?php endif; ?>