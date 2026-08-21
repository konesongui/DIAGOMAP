<div class="modal-header">
    <h5 class="modal-title">
        <i class="fa fa-credit-card"></i> Sous-comptes de la caisse: <?php echo htmlspecialchars($caisse['name']); ?>
    </h5>
    <button type="button" class="close" data-dismiss="modal">
        <span>&times;</span>
    </button>
</div>

<div class="modal-body">
    <!-- Formulaire pour ajouter un sous-compte -->
    <div class="card mb-4">
        <div class="card-header">
            <h6><i class="fa fa-plus"></i> Ajouter un nouveau sous-compte</h6>
        </div>
        <div class="card-body">
            <form method="post" action="<?php echo base_url('admin/income/gestion_sous_comptes/' . $caisse['id']); ?>">
                <?php echo $this->customlib->getCSRF(); ?>
                <input type="hidden" name="caisse_id" value="<?php echo $caisse['id']; ?>">

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nom du sous-compte *</label>
                            <input type="text" name="nom" class="form-control" required
                                   placeholder="Ex: Orange Money Principal">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Type *</label>
                            <select name="type" class="form-control" required>
                                <option value="orange_money">Orange Money</option>
                                <option value="wave">Wave</option>
                                <option value="mtn_money">MTN Money</option>
                                <option value="moov_money">Moov Money</option>
                                <option value="carte_bancaire">Carte Bancaire</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Montant initial *</label>
                            <input type="number" name="montant_initial" class="form-control"
                                   step="0.01" min="0" value="0" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Créer le sous-compte
                </button>
            </form>
        </div>
    </div>

    <!-- Liste des sous-comptes existants -->
    <div class="card">
        <div class="card-header">
            <h6><i class="fa fa-list"></i> Sous-comptes existants</h6>
        </div>
        <div class="card-body">
            <?php if (!empty($sous_comptes)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Type</th>
                            <th>Solde initial</th>
                            <th>Solde actuel</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($sous_comptes as $sous_compte): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($sous_compte['nom']); ?></td>
                                <td>
                                        <span class="badge badge-info">
                                            <?php echo htmlspecialchars($sous_compte['type']); ?>
                                        </span>
                                </td>
                                <td><?php echo number_format($sous_compte['montant_initial'], 2, ',', ' '); ?> FCFA</td>
                                <td class="font-weight-bold
                                        <?php echo $sous_compte['solde_actuel'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo number_format($sous_compte['solde_actuel'], 2, ',', ' '); ?> FCFA
                                </td>
                                <td>
                                    <button class="btn btn-xs btn-warning"
                                            onclick="editerSousCompte(<?php echo $sous_compte['id']; ?>)">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <button class="btn btn-xs btn-danger"
                                            onclick="supprimerSousCompte(<?php echo $sous_compte['id']; ?>)">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i> Aucun sous-compte créé pour cette caisse.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>