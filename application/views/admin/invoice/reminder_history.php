<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-history"></i> Historique des relances
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Liste des relances envoyées</h3>
                        <div class="box-tools pull-right">
                            <a href="<?php echo base_url('admin/invoiceitem/configure_reminders'); ?>" class="btn btn-info btn-sm">
                                <i class="fa fa-cog"></i> Configuration
                            </a>
                        </div>
                    </div>

                    <div class="box-body">
                        <table class="table table-striped table-bordered" id="reminderTable">
                            <thead>
                            <tr>
                                <th>Date d'envoi</th>
                                <th>N° Facture</th>
                                <th>Client</th>
                                <th>Niveau relance</th>
                                <th>Jours (delta)</th>
                                <th>Statut</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($reminders as $reminder): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y H:i', strtotime($reminder['sent_at'])); ?></td>
                                    <td>
                                        <a href="<?php echo base_url('admin/invoiceitem/view/' . $reminder['invoice_id']); ?>">
                                            <?php echo $reminder['invoice_number']; ?>
                                        </a>
                                    </td>
                                    <td><?php echo html_escape($reminder['customer_name'] . ' ' . $reminder['customer_last_name']); ?></td>
                                    <td>
                                        <?php
                                        $level_labels = [
                                            'before_3days' => ['label' => 'Avant échéance', 'class' => 'info'],
                                            'due_date' => ['label' => 'Jour J', 'class' => 'warning'],
                                            'late_3days' => ['label' => 'Retard J+3', 'class' => 'danger'],
                                            'late_7days' => ['label' => 'Retard J+7', 'class' => 'danger'],
                                            'late_15days' => ['label' => 'Retard J+15', 'class' => 'danger'],
                                            'other' => ['label' => 'Autre', 'class' => 'default']
                                        ];
                                        $level = $level_labels[$reminder['reminder_level']] ?? $level_labels['other'];
                                        ?>
                                        <span class="label label-<?php echo $level['class']; ?>">
                                            <?php echo $level['label']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $reminder['days_delta'] > 0 ? '+' . $reminder['days_delta'] : $reminder['days_delta']; ?></td>
                                    <td>
                                        <span class="label label-success">Envoyé</span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        $('#reminderTable').DataTable({
            "order": [[0, "desc"]],
            "language": {
                "url": "<?php echo base_url('assets/js/french.json'); ?>"
            },
            "pageLength": 25
        });
    });
</script>