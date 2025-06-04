<?php

    // Set all the CRUD variables Stock entry Tool
    $dtItemStockID = 'itemStockDatatable';

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-object-group"></i> Etat de stock
        </h1>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Horizontal Form -->
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Etat de stock</h3>
                    </div><!-- /.box-header -->

                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover <?= $dtItemStockID ?>" role="grid" aria-label="Entrée de stock">
                                <thead>
                                    <tr>
                                        <th scope="col">Article</th>
                                        <th scope="col">Catégorie</th>
                                        <th scope="col">Unité</th>
                                        <th scope="col">Coût moyen pondéré</th>
                                        <th scope="col">Quantité disponible</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div><!--/.col (right) -->
        </div>
    </section><!-- /.content -->
</div><!-- /.content-wrapper -->

<!-- Scripts -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/js/itemstock/index.js"></script>