<?php
$currency_symbol = $this->customlib->getSchoolCurrencyFormat();
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-edit"></i> Éditer Opération
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Modifier l'opération #<?php echo htmlspecialchars($operation['reference']); ?></h3>
                    </div>

                    <form action="<?php echo base_url('admin/income/update_operation/' . $operation['id']); ?>" method="post">
                        <?php echo $this->customlib->getCSRF(); ?>

                        <div class="box-body">
                            <?php if (isset($error_message)): ?>
                                <div class="alert alert-danger"><?php echo $error_message; ?></div>
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="caisse_id">Caisse *</label>
                                        <select class="form-control" id="caisse_id" name="caisse_id" required>
                                            <option value="">Sélectionner une caisse</option>
                                            <?php foreach ($caisses as $caisse): ?>
                                                <option value="<?php echo $caisse['id']; ?>"
                                                    <?php echo $operation['caisse_id'] == $caisse['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($caisse['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date">Date *</label>
                                        <input type="date" class="form-control" id="date" name="date"
                                               value="<?php echo date('Y-m-d', strtotime($operation['date'])); ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="type">Type d'opération *</label>
                                <select class="form-control" id="type" name="type" required>
                                    <option value="entree" <?php echo $operation['entree'] > 0 ? 'selected' : ''; ?>>Entrée (Recette)</option>
                                    <option value="sortie" <?php echo $operation['sortie'] > 0 ? 'selected' : ''; ?>>Sortie (Dépense)</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="exp_head_id">Catégorie *</label>
                                <select id="exp_head_id" name="exp_head_id" class="form-control" required>
                                    <option value="">Sélectionner une catégorie</option>
                                    <?php foreach ($expheadlist as $exphead): ?>
                                        <option value="<?php echo $exphead['id']; ?>"
                                            <?php echo $operation['exp_head_id'] == $exphead['id'] ? 'selected' : ''; ?>
                                                data-name="<?php echo htmlspecialchars($exphead['exp_category']); ?>">
                                            <?php echo htmlspecialchars($exphead['exp_category']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" name="exp_category_name" id="exp_category_name"
                                       value="<?php echo htmlspecialchars($operation['category_name'] ?? $operation['exp_category_name'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="designation">Désignation *</label>
                                <textarea class="form-control" id="designation" name="designation" rows="3" required><?php echo htmlspecialchars($operation['designation']); ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="montant">Montant *</label>
                                        <input type="number" class="form-control" id="montant" name="montant"
                                               step="0.01" min="0" required
                                               value="<?php echo $operation['entree'] > 0 ? $operation['entree'] : $operation['sortie']; ?>">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="reference">Référence</label>
                                        <input type="text" class="form-control" id="reference" name="reference"
                                               value="<?php echo htmlspecialchars($operation['reference']); ?>"
                                               placeholder="Ex: RECU-001, FACT-001">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="mode_paiement">Mode de paiement</label>
                                <select class="form-control" id="mode_paiement" name="mode_paiement">
                                    <option value="espèces" <?php echo ($operation['mode_paiement'] ?? '') == 'espèces' ? 'selected' : ''; ?>>Espèces</option>
                                    <option value="chèque" <?php echo ($operation['mode_paiement'] ?? '') == 'chèque' ? 'selected' : ''; ?>>Chèque</option>
                                    <option value="virement" <?php echo ($operation['mode_paiement'] ?? '') == 'virement' ? 'selected' : ''; ?>>Virement</option>
                                    <option value="carte" <?php echo ($operation['mode_paiement'] ?? '') == 'carte' ? 'selected' : ''; ?>>Carte bancaire</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <div class="alert alert-info">
                                    <strong>Informations actuelles:</strong><br>
                                    Type: <?php echo $operation['entree'] > 0 ? 'Entrée' : 'Sortie'; ?><br>
                                    Montant: <?php echo number_format($operation['entree'] > 0 ? $operation['entree'] : $operation['sortie'], 2, ',', ' '); ?> FCFA<br>
                                    Date: <?php echo date('d/m/Y', strtotime($operation['date'])); ?>
                                </div>
                            </div>
                        </div>

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Enregistrer les modifications
                            </button>
                            <a href="<?php echo base_url('admin/income'); ?>" class="btn btn-default">
                                <i class="fa fa-times"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    // Gestion du changement de catégorie
    document.getElementById('exp_head_id').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var categoryName = selectedOption.getAttribute('data-name');
        document.getElementById('exp_category_name').value = categoryName || '';
    });
</script>