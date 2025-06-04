<div class="content-wrapper">
    <section class="content-header">
        <h1><?php echo $page_title; ?></h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Informations de l'entrée</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-bordered">
                                    <tr>
                                        <th>Référence</th>
                                        <td><?php echo $removal['reference']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Désignation</th>
                                        <td><?php echo $removal['reason']; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Total Montant</th>
                                        <td><?php echo number_format($removal['grand_total'], 0, ',', ' '); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Date</th>
                                        <td><?php echo date('d/m/Y', strtotime($removal['issue_date'])); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Articles</h3>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Catégorie</th>
                                        <th>Article</th>
                                        <th>Unité</th>
                                        <th>Quantité</th>
                                        <th>Prix unitaire</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($removal['items'] as $item): ?>
                                    <tr>
                                        <td><?php echo $item['category_name']; ?></td>
                                        <td><?php echo $item['item_name']; ?></td>
                                        <td><?php echo $item['unit']; ?></td>
                                        <td><?php echo number_format($item['quantity'], 0, ',', ' '); ?></td>
                                        <td><?php echo number_format($item['price'], 2, ',', ' ') ; ?></td>
                                        <td><?php echo number_format($item['line_total'], 2, ',', ' ') ; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="6" class="text-right">Total</th>
                                        <th><?php echo number_format($removal['grand_total'], 2, ',', ' ') . ' €'; ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="box-footer">
                        <a href="<?php echo base_url('admin/stockremoval'); ?>" class="btn btn-default">Retour à la liste</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div> 