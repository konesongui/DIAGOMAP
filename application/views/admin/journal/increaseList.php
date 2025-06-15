<!-- general form elements -->
<div class="box box-primary">
    <div class="box-header ptbnull" style='background-color: #ff9801; color: white;border-radius: 2px'>

        <h3 class="box-title titlefix">Liste des réapprovisionnements <b><?php echo $row->total_amount ?></b></h3>

        <div class="box-tools pull-right">
        </div><!-- /.box-tools -->
    </div><!-- /.box-header -->
    <div class="box-body">

        <div class="table-responsive mailbox-messages">
            <?php
            // Filtrer les réapprovisionnements avec des montants strictement positifs
            $filteredRows = array_filter($rows, function($row) {
                return isset($row->amount) && (float)$row->amount > 0;
            });
            ?>
            <?php if (!empty($filteredRows)): ?>
                <table class="table table-striped table-bordered table-hover" data-export-title="<?php echo $this->lang->line('income_list'); ?>">
                    <thead>
                    <tr>
                        <!--<th>Utilisateurs</th>-->
                        <th>Référence</th>
                        <th>Nom</th>
                        <th>Montant</th>
                        <th>Motif</th>
                        <th>Date</th>
                        <th><?php echo $this->lang->line('action'); ?></th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($filteredRows as $row): ?>
                        <tr>
                            <!--<td><?= htmlspecialchars($row->user ?? '', ENT_QUOTES, 'UTF-8'); ?></td>-->
                            <td><?= htmlspecialchars($row->name ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars($row->reason ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars((float)$row->amount, ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?= htmlspecialchars($row->raison ?? '', ENT_QUOTES, 'UTF-8'); ?></td>

                            <td><?= htmlspecialchars($row->date ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                            <td class="jsgrid-align-center ">

                                <a data-placement="left" href="<?php echo base_url(); ?>admin/income/delete_increase/<?php echo $row->id; ?>" class="btn btn-default btn-xs" data-toggle="tooltip" title="<?php echo $this->lang->line('delete'); ?>" onclick="return confirm('<?php echo $this->lang->line('delete_confirm') ?>');" data-original-title="<?php echo $this->lang->line('delete'); ?>">
                                    <i class="fa fa-remove"></i>
                                </a>
                            </td>

                            <!--<td><?= htmlspecialchars($row->created_at ?? '', ENT_QUOTES, 'UTF-8'); ?></td>-->
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center">Aucun réapprovisionnement disponible pour l'instant.</p>
            <?php endif; ?>
        </div><!-- /.mail-box-messages -->
    </div><!-- /.box-body -->
    <div class="box-footer text-center">
        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
    </div><!-- /.box-footer -->
</div>

<script>
    ( function ( $ ) {
        'use strict';
        $(document).ready(function () {
            initDatatable('form_increase-list','Income/form_increase',[],[],50);
        });
    } ( jQuery ) )
</script>


