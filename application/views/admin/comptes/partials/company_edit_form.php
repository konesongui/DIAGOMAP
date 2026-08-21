<?php if ($company): ?>
    <form id="editCompanyForm" method="post" enctype="multipart/form-data">
        <!-- ✅ AJOUTER CE CHAMP CACHÉ AVEC L'ID -->
        <input type="hidden" name="id" value="<?= $id ?? $company->id ?>">
        <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>" value="<?= $this->security->get_csrf_hash() ?>">

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Nom de l'entreprise <span class="text-danger">*</span></label>
                    <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($company->nom ?? '') ?>" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($company->email ?? '') ?>" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Téléphone <span class="text-danger">*</span></label>
                    <input type="text" name="telephone" class="form-control" value="<?= htmlspecialchars($company->telephone ?? '') ?>" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Adresse</label>
                    <input type="text" name="adresse" class="form-control" value="<?= htmlspecialchars($company->adresse ?? '') ?>">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Type de structure</label>
                    <select name="type_structure" id="edit_type_structure" class="form-control">
                        <option value="siege" <?= ($company->type_structure ?? 'siege') === 'siege' ? 'selected' : '' ?>>Siège</option>
                        <option value="succursale" <?= ($company->type_structure ?? '') === 'succursale' ? 'selected' : '' ?>>Succursale</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6 edit-branch-only" style="<?= ($company->type_structure ?? '') === 'succursale' ? '' : 'display:none;' ?>">
                <div class="form-group">
                    <label>Siège de rattachement</label>
                    <select name="parent_entreprise_id" id="edit_parent_entreprise_id" class="form-control">
                        <option value="">Sélectionner un siège</option>
                        <?php foreach (($head_offices ?? array()) as $head_office): ?>
                            <option value="<?= (int) $head_office['id'] ?>" <?= (int) ($company->parent_entreprise_id ?? 0) === (int) $head_office['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($head_office['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="row edit-branch-only" style="<?= ($company->type_structure ?? '') === 'succursale' ? '' : 'display:none;' ?>">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Code succursale</label>
                    <input type="text" name="code_succursale" id="edit_code_succursale" class="form-control" value="<?= htmlspecialchars($company->code_succursale ?? '') ?>">
                </div>
            </div>
            <div class="col-md-2">
                <div class="checkbox" style="margin-top: 34px;">
                    <label><input type="checkbox" name="inherit_settings" value="1" <?= !isset($branch_relation['inherit_settings']) || (int) $branch_relation['inherit_settings'] === 1 ? 'checked' : '' ?>> Paramètres</label>
                </div>
            </div>
            <div class="col-md-2">
                <div class="checkbox" style="margin-top: 34px;">
                    <label><input type="checkbox" name="inherit_roles" value="1" <?= !isset($branch_relation['inherit_roles']) || (int) $branch_relation['inherit_roles'] === 1 ? 'checked' : '' ?>> Permissions</label>
                </div>
            </div>
            <div class="col-md-2">
                <div class="checkbox" style="margin-top: 34px;">
                    <label><input type="checkbox" name="inherit_ohada" value="1" <?= !isset($branch_relation['inherit_ohada']) || (int) $branch_relation['inherit_ohada'] === 1 ? 'checked' : '' ?>> OHADA</label>
                </div>
            </div>
        </div>

        <div class="row edit-head-office-only" style="<?= ($company->type_structure ?? 'siege') === 'siege' ? '' : 'display:none;' ?>">
            <div class="col-md-12">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="can_manage_succursales" value="1" <?= !empty($company->can_manage_succursales) ? 'checked' : '' ?>>
                        Cette entreprise a des succursales et peut les gérer depuis son espace.
                    </label>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Forfait</label>
                    <select name="forfait" class="form-control">
                        <option value="basic" <?= ($company->forfait ?? '') == 'basic' ? 'selected' : '' ?>>Basic</option>
                        <option value="standard" <?= ($company->forfait ?? '') == 'standard' ? 'selected' : '' ?>>Standard</option>
                        <option value="pro" <?= ($company->forfait ?? '') == 'pro' ? 'selected' : '' ?>>Pro</option>
                        <option value="premium" <?= ($company->forfait ?? '') == 'premium' ? 'selected' : '' ?>>Premium</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Statut</label>
                    <select name="statut" class="form-control">
                        <option value="actif" <?= ($company->statut ?? '') == 'actif' ? 'selected' : '' ?>>Actif</option>
                        <option value="suspendu" <?= ($company->statut ?? '') == 'suspendu' ? 'selected' : '' ?>>Suspendu</option>
                        <option value="expiré" <?= ($company->statut ?? '') == 'expiré' ? 'selected' : '' ?>>Expiré</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Date début</label>
                    <input type="date" name="date_debut" class="form-control" value="<?= $company->date_debut ?? '' ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Date expiration</label>
                    <input type="date" name="date_expiration" class="form-control" value="<?= $company->date_expiration ?? '' ?>">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>RCCM</label>
                    <input type="text" name="rccm" class="form-control" value="<?= htmlspecialchars($company->rccm ?? '') ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>NCC</label>
                    <input type="text" name="ncc" class="form-control" value="<?= htmlspecialchars($company->ncc ?? '') ?>">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Nom du contact</label>
                    <input type="text" name="contact_nom" class="form-control" value="<?= htmlspecialchars($company->contact_nom ?? '') ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Limite utilisateurs</label>
                    <input type="number" name="limite_utilisateurs" class="form-control" value="<?= $company->limite_utilisateurs ?? 0 ?>">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Nom admin</label>
                    <input type="text" name="admin_username" class="form-control" value="<?= htmlspecialchars($company->admin_username ?? '') ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Email admin</label>
                    <input type="email" name="admin_email" class="form-control" value="<?= htmlspecialchars($company->admin_email ?? '') ?>">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Slug</label>
                    <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($company->slug ?? '') ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Nouveau mot de passe</label>
                    <input type="password" name="admin_password" class="form-control" placeholder="Laisser vide pour ne pas changer">
                    <small class="text-muted">Remplissez uniquement si vous voulez changer le mot de passe</small>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Logo</label>
                    <input type="file" name="logo" class="form-control" accept="image/*">
                    <?php if (!empty($company->logo)): ?>
                        <div class="mt-2">
                            <small>Logo actuel : </small>
                            <img src="<?= base_url('uploads/logos/'.$company->logo) ?>" alt="Logo" style="max-height: 50px; max-width: 100px;" class="img-thumbnail">
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="box-footer" style="padding: 15px 0 0 0; border-top: 1px solid #e5e5e5;">
            <button type="button" class="btn btn-default" data-dismiss="modal">
                <i class="fa fa-times"></i> Fermer
            </button>
            <button type="submit" class="btn btn-primary pull-right" id="submitEditBtn">
                <i class="fa fa-save"></i> Enregistrer
            </button>
        </div>
    </form>

    <script>
        $(document).ready(function() {
        function toggleEditBranchFields() {
            var isBranch = $('#edit_type_structure').val() === 'succursale';
            $('.edit-branch-only').toggle(isBranch);
            $('.edit-head-office-only').toggle(!isBranch);
            $('#edit_parent_entreprise_id, #edit_code_succursale').prop('required', isBranch);
        }

        $('#edit_type_structure').on('change', toggleEditBranchFields);
        toggleEditBranchFields();

        // Soumission du formulaire via AJAX
        $('#editCompanyForm').on('submit', function(e) {
            e.preventDefault();

                var formData = new FormData(this);
                var submitBtn = $('#submitEditBtn');

                // Désactiver le bouton
                submitBtn.prop('disabled', true);
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Enregistrement...');

                $.ajax({
                    url: '<?= site_url("admin/comptes/update_ajax") ?>',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Fermer la modale
                            $('#editCompanyModal').modal('hide');

                            // Recharger le DataTable
                            var table = $('.compte-list').DataTable();
                            if (table) {
                                table.ajax.reload(null, false);
                            }

                            // Message de succès
                            if (typeof toastr !== 'undefined') {
                                toastr.success(response.message, 'Succès');
                            } else {
                                alert(response.message);
                            }
                        } else {
                            // Afficher l'erreur
                            if (typeof toastr !== 'undefined') {
                                toastr.error(response.message, 'Erreur');
                            } else {
                                alert('Erreur: ' + response.message);
                            }

                            // Réactiver le bouton
                            submitBtn.prop('disabled', false);
                            submitBtn.html('<i class="fa fa-save"></i> Enregistrer');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Erreur AJAX:', error);
                        console.error('Réponse:', xhr.responseText);

                        if (typeof toastr !== 'undefined') {
                            toastr.error('Erreur lors de la modification', 'Erreur');
                        } else {
                            alert('Erreur lors de la modification');
                        }

                        // Réactiver le bouton
                        submitBtn.prop('disabled', false);
                        submitBtn.html('<i class="fa fa-save"></i> Enregistrer');
                    }
                });
            });
        });
    </script>

<?php else: ?>
    <div class="alert alert-danger">Aucune entreprise trouvée</div>
<?php endif; ?>