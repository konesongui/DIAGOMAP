<div class="content-wrapper">
    <section class="content-header">
        <h1>Super Administration <small>Gestion des entreprises</small></h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <?php if($this->session->flashdata('success')): ?>
                    <div class="alert alert-success"><?= $this->session->flashdata('success') ?></div>
                <?php endif; ?>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Liste des entreprises</h3>
                        <button class="btn btn-primary pull-right" data-toggle="modal" data-target="#modalCreer">+ Nouvelle entreprise</button>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr><th>ID</th><th>Nom</th><th>Sous-domaine</th><th>Statut</th><th>Date création</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                            <?php foreach($entreprises as $e): ?>
                                <tr>
                                    <td><?= $e->id ?></td>
                                    <td><?= htmlspecialchars($e->nom) ?></td>
                                    <td><?= htmlspecialchars($e->sous_domaine) ?></td>
                                    <td><?= ucfirst($e->status) ?></td>
                                    <td><?= date('d/m/Y', strtotime($e->date_creation)) ?></td>
                                    <td>
                                        <a href="<?= base_url('superadmin/suspendre/'.$e->id) ?>" class="btn btn-warning btn-xs">
                                            <?= ($e->status == 'actif') ? 'Suspendre' : 'Activer' ?>
                                        </a>
                                        <a href="<?= base_url('superadmin/supprimer/'.$e->id) ?>" class="btn btn-danger btn-xs" onclick="return confirm('Supprimer définitivement ?')">Supprimer</a>
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

<!-- Modal création entreprise -->
<div class="modal fade" id="modalCreer" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="<?= base_url('superadmin/creer_entreprise') ?>">
                <div class="modal-header"><h4>Créer une entreprise</h4></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nom de l'entreprise *</label>
                        <input type="text" name="nom" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Sous-domaine (optionnel)</label>
                        <input type="text" name="sous_domaine" class="form-control" placeholder="ex: maentreprise">
                    </div>
                    <div class="form-group">
                        <label>Email administrateur *</label>
                        <input type="email" name="email_admin" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer</button>
                </div>
            </form>
        </div>
    </div>
</div>