<?php
$operation_type = ($operation['entree'] > 0) ? 'entree' : 'sortie';
$montant = ($operation['entree'] > 0) ? $operation['entree'] : $operation['sortie'];
?>

<form id="editOperationForm" action="<?php echo base_url('admin/income/update_operation/' . $operation['id']); ?>" method="post">
    <?php echo $this->customlib->getCSRF(); ?>

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
            <option value="entree" <?php echo $operation_type == 'entree' ? 'selected' : ''; ?>>Entrée (Recette)</option>
            <option value="sortie" <?php echo $operation_type == 'sortie' ? 'selected' : ''; ?>>Sortie (Dépense)</option>
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
        <textarea class="form-control" id="designation" name="designation" rows="2" required><?php echo htmlspecialchars($operation['designation']); ?></textarea>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="montant">Montant *</label>
                <input type="number" class="form-control" id="montant" name="montant"
                       step="0.01" min="0" required
                       value="<?php echo $montant; ?>">
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

    <div class="text-right">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </div>
</form>

<script>
    // Gestion du changement de catégorie pour le formulaire modal
    $(document).ready(function() {
        $('#exp_head_id').change(function() {
            var selectedOption = $(this).find('option:selected');
            var categoryName = selectedOption.data('name');
            $('#exp_category_name').val(categoryName || '');
        });

        // Soumission AJAX du formulaire
        $('#editOperationForm').submit(function(e) {
            e.preventDefault();

            var formData = $(this).serialize();

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                success: function(response) {
                    // Rediriger après succès
                    window.location.reload();
                },
                error: function(xhr, status, error) {
                    alert('Erreur lors de la mise à jour: ' + error);
                }
            });
        });
    });
</script>