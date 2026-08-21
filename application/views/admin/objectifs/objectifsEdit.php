<div class="content-wrapper">
    <section class="content-header">
        <h1>Modifier l'objectif annuel</h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title">Objectif annuel</h3>
                    </div>
                    <form action="<?php echo site_url('admin/objectifs/edit/'.$objective['id']); ?>" method="post">
                        <div class="box-body">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <div class="form-group">
                                <label>Montant</label>
                                <input type="number" name="amount" class="form-control" value="<?php echo $objective['amount']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" name="date" class="form-control" value="<?php echo $objective['date']; ?>" required>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Mettre à jour</button>
                            <a href="<?php echo site_url('admin/objectifs/index'); ?>" class="btn btn-default">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>