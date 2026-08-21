<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-ticket"></i> Gestion des Tickets
                            <small>(<?php echo isset($stats['total']) ? $stats['total'] : 0; ?>)</small>
                        </h3>
                        <div class="box-tools pull-right">
                            <a href="<?php echo base_url('admin/tickets/add'); ?>" class="btn btn-primary btn-sm">
                                <i class="fa fa-plus-circle"></i> Nouveau ticket
                            </a>
                        </div>
                    </div>
                    <div class="box-body">
                        <?php if (!empty($setup_error)): ?>
                            <div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> <?php echo html_escape($setup_error); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($query_error)): ?>
                            <div class="alert alert-danger"><i class="fa fa-database"></i> <?php echo html_escape($query_error); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($tickets)): ?>
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Numéro</th>
                                    <th>Titre</th>
                                    <th>Catégorie</th>
                                    <th>Priorité</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($tickets as $index => $ticket): ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><strong><?php echo isset($ticket['ticket_number']) ? $ticket['ticket_number'] : ''; ?></strong></td>
                                        <td><?php echo isset($ticket['titre']) ? $ticket['titre'] : ''; ?></td>
                                        <td><?php echo isset($ticket['categorie_nom']) ? $ticket['categorie_nom'] : '-'; ?></td>
                                        <td><?php echo isset($ticket['priorite_nom']) ? $ticket['priorite_nom'] : 'Moyenne'; ?></td>
                                        <td><?php echo isset($ticket['statut_nom']) ? $ticket['statut_nom'] : 'Ouvert'; ?></td>
                                        <td><?php echo isset($ticket['date_creation']) ? date('d/m/Y', strtotime($ticket['date_creation'])) : ''; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="alert alert-info">Aucun ticket trouvé</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>