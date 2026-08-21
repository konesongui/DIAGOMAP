<style>
    /* ============================================
       STYLES MODERNES POUR LE FORMULAIRE
    ============================================ */
    .form-section {
        background: #f8faff;
        border-radius: 12px;
        padding: 25px 20px 15px 20px;
        margin-bottom: 25px;
        border: 1px solid #eef2f8;
        transition: all 0.3s ease;
    }

    .form-section:hover {
        border-color: #c5d0e0;
        box-shadow: 0 2px 12px rgba(30, 58, 138, 0.06);
    }

    .form-section-title {
        font-size: 16px;
        font-weight: 600;
        color: #1E3A8A;
        margin-top: -8px;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid #e8edf5;
        display: flex;
        align-items: center;
        gap: 10px;
        letter-spacing: 0.3px;
    }

    .form-section-title i {
        width: 30px;
        height: 30px;
        background: #1E3A8A;
        color: #fff;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    .form-section-title .badge-section {
        margin-left: auto;
        font-size: 11px;
        font-weight: 400;
        background: #eef2f8;
        color: #6b7a93;
        padding: 3px 12px;
        border-radius: 20px;
    }

    /* Champs de formulaire modernes */
    .form-group-modern {
        margin-bottom: 18px;
    }

    .form-group-modern label {
        font-weight: 500;
        font-size: 13px;
        color: #2c3e50;
        margin-bottom: 6px;
        display: block;
    }

    .form-group-modern label .required {
        color: #ef4444;
        margin-left: 3px;
    }

    .form-group-modern .form-control {
        border-radius: 8px;
        border: 1.5px solid #e2e8f0;
        padding: 10px 14px;
        font-size: 14px;
        transition: all 0.25s ease;
        background: #ffffff;
        box-shadow: none;
        height: 42px;
    }

    .form-group-modern .form-control:focus {
        border-color: #1E3A8A;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
        outline: none;
    }

    .form-group-modern .form-control::placeholder {
        color: #aab3c5;
        font-size: 13px;
    }

    .form-group-modern select.form-control {
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7a93' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        padding-right: 36px;
    }

    .form-group-modern .help-text {
        font-size: 12px;
        color: #8896ab;
        margin-top: 5px;
        display: block;
    }

    .form-group-modern .help-text i {
        margin-right: 4px;
    }

    /* Input file stylisé */
    .file-input-wrapper {
        position: relative;
        overflow: hidden;
        display: inline-block;
        width: 100%;
    }

    .file-input-wrapper input[type="file"] {
        position: absolute;
        left: 0;
        top: 0;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }

    .file-input-wrapper .file-label {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px;
        background: #f8faff;
        border: 1.5px dashed #c5d0e0;
        border-radius: 8px;
        color: #6b7a93;
        font-size: 13px;
        transition: all 0.25s ease;
        cursor: pointer;
    }

    .file-input-wrapper .file-label:hover {
        border-color: #1E3A8A;
        background: #f0f4ff;
    }

    .file-input-wrapper .file-label i {
        font-size: 18px;
        color: #1E3A8A;
    }

    .file-input-wrapper .file-label .file-name {
        color: #1E3A8A;
        font-weight: 500;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        flex: 1;
    }

    /* Boutons */
    .btn-modern-primary {
        background: linear-gradient(135deg, #1E3A8A 0%, #2a4fb0 100%);
        border: none;
        color: #fff;
        padding: 11px 32px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.25);
        letter-spacing: 0.3px;
    }

    .btn-modern-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(30, 58, 138, 0.35);
        background: linear-gradient(135deg, #162d6e 0%, #1E3A8A 100%);
        color: #fff;
    }

    .btn-modern-primary:active {
        transform: translateY(0px);
    }

    .btn-modern-secondary {
        background: #f1f4f9;
        border: 1.5px solid #e2e8f0;
        color: #4a5a72;
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.25s ease;
    }

    .btn-modern-secondary:hover {
        background: #e8edf5;
        border-color: #c5d0e0;
    }

    /* Alert moderne */
    .alert-modern {
        border-radius: 10px;
        border: none;
        padding: 14px 20px;
        margin-bottom: 20px;
    }

    .alert-modern.alert-success {
        background: #ecfdf5;
        border-left: 4px solid #22c55e;
        color: #065f46;
    }

    .alert-modern.alert-danger {
        background: #fef2f2;
        border-left: 4px solid #ef4444;
        color: #991b1b;
    }

    /* Box principal */
    .box-modern {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        border: 1px solid #eef2f8;
        overflow: hidden;
    }

    .box-modern .box-header {
        padding: 22px 28px;
        background: #fafcff;
        border-bottom: 1px solid #eef2f8;
    }

    .box-modern .box-header .box-title {
        font-size: 18px;
        font-weight: 600;
        color: #1E3A8A;
        margin: 0;
    }

    .box-modern .box-header .box-title i {
        margin-right: 10px;
    }

    .box-modern .box-body {
        padding: 28px 28px 10px 28px;
    }

    .box-modern .box-footer {
        padding: 18px 28px;
        background: #fafcff;
        border-top: 1px solid #eef2f8;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .box-modern .box-body {
            padding: 18px 15px 5px 15px;
        }
        .box-modern .box-header {
            padding: 16px 18px;
        }
        .box-modern .box-footer {
            padding: 14px 18px;
            flex-direction: column;
            gap: 12px;
        }
        .form-section {
            padding: 18px 14px 10px 14px;
        }
        .btn-modern-primary {
            width: 100%;
            text-align: center;
        }
        .btn-modern-secondary {
            width: 100%;
            text-align: center;
        }
    }

    /* Animation douce */
    .fade-in-section {
        animation: fadeInUp 0.4s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(15px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<?php
$branch_mode = !empty($branch_mode);
$can_company_create = isset($can_company_create) ? (bool) $can_company_create : ($this->rbac->hasPrivilege('souscription', 'can_add') || $this->rbac->hasPrivilege('comptes', 'can_add'));
$page_title = $branch_mode ? 'Création de Succursale' : 'Création d\'Entreprise';
$page_subtitle = $branch_mode ? 'Nouvelle succursale rattachée au siège' : 'Nouvelle entreprise';
$submit_label = $branch_mode ? 'Créer la succursale' : 'Créer l\'entreprise';
$return_url = $branch_mode ? base_url() . 'admin/comptes/succursales' : base_url() . 'admin/comptes';
$form_action = !empty($form_action) ? $form_action : site_url('admin/comptes/create');
?>

<div class="content-wrapper" style="min-height: 946px; background: #f5f7fb;">
    <section class="content-header">
        <h1 style="display: flex; align-items: center; gap: 12px;">
            <span style="background: linear-gradient(135deg, #1E3A8A, #2a4fb0); width: 40px; height: 40px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; color: #fff;">
                <i class="fa fa-plus"></i>
            </span>
            <span style="font-weight: 600; color: #1E3A8A;"><?php echo $page_title; ?></span>
            <small style="font-size: 13px; font-weight: 400; color: #8896ab; margin-left: 8px;">
                <i class="fa fa-chevron-right" style="font-size: 10px;"></i> <?php echo $page_subtitle; ?>
            </small>
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <?php if ($can_company_create) { ?>
                <div class="col-md-12">
                    <div class="box-modern fade-in-section">
                        <div class="box-header">
                            <h3 class="box-title">
                               <i class="fa fa-building-o"></i>
                               <?php echo $page_title; ?>
                               <span style="font-size: 12px; font-weight: 400; color: #8896ab; margin-left: 12px;">
                                   <i class="fa fa-info-circle"></i> Remplissez tous les champs obligatoires (*)
                               </span>
                            </h3>
                        </div>

                        <form action="<?php echo $form_action; ?>"
                              id="employeeform"
                              name="employeeform"
                              method="post"
                              enctype="multipart/form-data"
                              accept-charset="utf-8"
                              onsubmit="return confirmSave();">

                            <div class="box-body">
                                <?php echo $this->customlib->getCSRF(); ?>

                                <?php if ($this->session->flashdata('msg')) { ?>
                                    <div class="alert-modern alert-<?php echo strpos($this->session->flashdata('msg'), 'success') !== false ? 'success' : 'danger'; ?>">
                                        <?php echo $this->session->flashdata('msg') ?>
                                    </div>
                                <?php } ?>

                                <?php if (isset($error_message)) { ?>
                                    <div class="alert-modern alert-danger"><?php echo $error_message; ?></div>
                                <?php } ?>

                                <?php if ($branch_mode && !empty($current_head_office)) { ?>
                                    <div class="alert-modern alert-success">
                                        Cette succursale sera automatiquement rattachée au siège <strong><?php echo html_escape($current_head_office->nom); ?></strong>.
                                    </div>
                                <?php } ?>

                                <!-- ============================================
                                     SECTION 1 - INFORMATIONS ENTREPRISE
                                ============================================ -->
                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="fa fa-building"></i>
                                        Informations Entreprise
                                        <span class="badge-section">Étape 1/4</span>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label>Nom officiel de l'entreprise <span class="required">*</span></label>
                                               <input type="text" name="nom" class="form-control" placeholder="Ex: SARL Dupont & Fils" value="<?php echo set_value('nom'); ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label>Slug / Sous-domaine <span class="required">*</span></label>
                                               <input type="text" name="slug" class="form-control" placeholder="ex: sarl-dupont" value="<?php echo set_value('slug'); ?>" required>
                                                <span class="help-text"><i class="fa fa-info-circle"></i> Utilisé pour la base de données et l'URL</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label>Numéro Contribuable (NCC)</label>
                                                <input type="text" name="ncc" class="form-control" placeholder="Ex: 123456789">
                                                <span class="help-text"><i class="fa fa-info-circle"></i> Pour facturation FNE</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label>RCCM</label>
                                                <input type="text" name="rccm" class="form-control" placeholder="Numéro RCCM">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label>Type de structure <span class="required">*</span></label>
                                                <?php if ($branch_mode) { ?>
                                                    <input type="hidden" name="type_structure" value="succursale">
                                                    <input type="text" class="form-control" value="Succursale" readonly>
                                                <?php } else { ?>
                                                    <select name="type_structure" id="type_structure" class="form-control" required>
                                                        <option value="siege" <?php echo set_select('type_structure', 'siege', true); ?>>Siège</option>
                                                        <option value="succursale" <?php echo set_select('type_structure', 'succursale'); ?>>Succursale</option>
                                                    </select>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4 branch-only" style="<?php echo $branch_mode ? '' : 'display:none;'; ?>">
                                            <div class="form-group-modern">
                                                <label>Siège de rattachement <span class="required">*</span></label>
                                                <?php if ($branch_mode && !empty($current_head_office)) { ?>
                                                    <input type="hidden" name="parent_entreprise_id" value="<?php echo (int) $current_head_office->id; ?>">
                                                    <input type="text" class="form-control" value="<?php echo html_escape($current_head_office->nom); ?>" readonly>
                                                <?php } else { ?>
                                                    <select name="parent_entreprise_id" id="parent_entreprise_id" class="form-control">
                                                        <option value="">Sélectionner un siège</option>
                                                        <?php foreach (($head_offices ?? array()) as $head_office): ?>
                                                            <option value="<?php echo (int) $head_office['id']; ?>" <?php echo set_select('parent_entreprise_id', (string) $head_office['id']); ?>>
                                                                <?php echo html_escape($head_office['nom']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="col-md-4 branch-only" style="<?php echo $branch_mode ? '' : 'display:none;'; ?>">
                                            <div class="form-group-modern">
                                                <label>Code succursale <span class="required">*</span></label>
                                                <input type="text" name="code_succursale" id="code_succursale" class="form-control" placeholder="Ex: ABJ-CENTRE" value="<?php echo set_value('code_succursale'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row head-office-only">
                                        <div class="col-md-12">
                                            <div class="form-group-modern">
                                                <label>
                                                    <input type="checkbox" name="can_manage_succursales" value="1" <?php echo set_checkbox('can_manage_succursales', '1'); ?>>
                                                    Cette entreprise a des succursales et pourra les créer depuis son propre espace.
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row branch-only" style="<?php echo $branch_mode ? '' : 'display:none;'; ?>">
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label><input type="checkbox" name="inherit_settings" value="1" <?php echo set_checkbox('inherit_settings', '1', true); ?>> Hériter des paramètres du siège</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label><input type="checkbox" name="inherit_roles" value="1" <?php echo set_checkbox('inherit_roles', '1', true); ?>> Hériter des permissions du siège</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label><input type="checkbox" name="inherit_ohada" value="1" <?php echo set_checkbox('inherit_ohada', '1', true); ?>> Hériter de la configuration OHADA</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label>Forfait <span class="required">*</span></label>
                                                <select name="forfait" class="form-control" required>
                                                    <option value="basic">⭐ Basic</option>
                                                    <option value="pro">🚀 Pro</option>
                                                    <option value="premium">👑 Premium</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label>Limite utilisateurs</label>
                                                <input type="number" name="limite_utilisateurs" class="form-control" value="3" min="1" max="50">
                                                <span class="help-text"><i class="fa fa-users"></i> Nombre max d'utilisateurs</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ============================================
                                     SECTION 2 - CONTACT PRINCIPAL
                                ============================================ -->
                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="fa fa-user-circle"></i>
                                        Contact Principal
                                        <span class="badge-section">Étape 2/4</span>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label>Nom du contact <span class="required">*</span></label>
                                                <input type="text" name="contact_nom" class="form-control" placeholder="Nom complet" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label>Email de contact</label>
                                                <input type="email" name="email" class="form-control" placeholder="contact@entreprise.com">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label>Téléphone</label>
                                                <input type="text" name="telephone" class="form-control" placeholder="+225 01 23 45 67 89">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label>Adresse</label>
                                                <input type="text" name="adresse" class="form-control" placeholder="Adresse complète">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label>Logo (facultatif)</label>
                                                <div class="file-input-wrapper">
                                                    <div class="file-label">
                                                        <i class="fa fa-upload"></i>
                                                        <span class="file-name" id="logoFileName">Choisir un logo</span>
                                                    </div>
                                                    <input type="file" name="logo" accept="image/*" onchange="updateFileName(this)">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label>Statut <span class="required">*</span></label>
                                                <select name="statut" class="form-control" required>
                                                    <option value="actif" selected>🟢 Actif</option>
                                                    <option value="expiré">🔴 Expiré</option>
                                                    <option value="suspendu">🟡 Suspendu</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label>Date de début</label>
                                                <input type="date" name="date_debut" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label>Date d'expiration</label>
                                                <input type="date" name="date_expiration" class="form-control">
                                                <span class="help-text"><i class="fa fa-calendar"></i> Ajustée selon le forfait</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ============================================
                                     SECTION 3 - CONFIGURATION FNE
                                ============================================ -->
                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="fa fa-cloud-upload"></i>
                                        Configuration FNE
                                        <span class="badge-section">Étape 3/4</span>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label>Clé API FNE</label>
                                                <input type="text" name="fne_api_key" class="form-control" placeholder="KAF01gEM40r1Uz5WLJn5lxAnGMWvViCME">
                                                <span class="help-text"><i class="fa fa-key"></i> Clé fournie par la plateforme FNE</span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label>Point de vente FNE</label>
                                                <input type="text" name="fne_point_vente" class="form-control" value="Siège" placeholder="Siège">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label>Établissement FNE</label>
                                                <input type="text" name="fne_establishment" class="form-control" placeholder="Nom de l'établissement">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- ============================================
                                     SECTION 4 - COMPTE ADMINISTRATEUR
                                ============================================ -->
                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="fa fa-user-shield"></i>
                                        Compte Administrateur
                                        <span class="badge-section">Étape 4/4</span>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label>Nom d'utilisateur admin <span class="required">*</span></label>
                                                <input type="text" name="admin_username" class="form-control" value="admin" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label>Email admin <span class="required">*</span></label>
                                                <input type="email" name="admin_email" class="form-control" placeholder="admin@entreprise.com" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group-modern">
                                                <label>Mot de passe admin <span class="required">*</span></label>
                                                <input type="password" name="admin_password" class="form-control" value="<?php echo bin2hex(random_bytes(4)); ?>" required>
                                                <span class="help-text"><i class="fa fa-lock"></i> Généré automatiquement, modifiable</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div><!-- /.box-body -->

                            <div class="box-footer">
                                <a href="<?php echo $return_url; ?>" class="btn-modern-secondary">
                                    <i class="fa fa-arrow-left"></i> Retour
                                </a>
                                <button type="submit" class="btn-modern-primary">
                                    <i class="fa fa-check-circle"></i> <?php echo $submit_label; ?>
                                </button>
                            </div>
                        </form>

                    </div>
                </div>
            <?php } ?>
        </div>
    </section>
</div>

<script>
    function confirmSave() {
        return confirm("Voulez-vous vraiment enregistrer cette entreprise ? Les identifiants seront envoyés par email.");
    }
</script>

<script type="text/javascript">
    $(document).ready(function () {
        var branchMode = <?php echo $branch_mode ? 'true' : 'false'; ?>;

        // Générer automatiquement le slug à partir du nom
        $('input[name="nom"]').on('blur', function() {
            var nom = $(this).val();
            if (nom && !$('input[name="slug"]').val()) {
                var slug = nom.toLowerCase()
                    .replace(/[^\w ]+/g, '')
                    .replace(/ +/g, '_');
                $('input[name="slug"]').val(slug);
            }
        });

        // Ajuster la date d'expiration selon le forfait
        $('select[name="forfait"]').on('change', function() {
            var dateDebut = $('input[name="date_debut"]').val();
            if (dateDebut) {
                var date = new Date(dateDebut);
                if ($(this).val() === 'premium') {
                    date.setFullYear(date.getFullYear() + 1);
                } else {
                    date.setMonth(date.getMonth() + 1);
                }
                $('input[name="date_expiration"]').val(date.toISOString().split('T')[0]);
            }
        });

        // Réinitialisation
        $("#btnreset").click(function () {
            $("#employeeform")[0].reset();
        });

        function toggleBranchFields() {
            var isBranch = branchMode || $('#type_structure').val() === 'succursale';
            $('.branch-only').toggle(isBranch);
            $('.head-office-only').toggle(!isBranch);
            $('#parent_entreprise_id, #code_succursale').prop('required', isBranch);
        }

        if (!branchMode) {
            $('#type_structure').on('change', toggleBranchFields);
        }
        toggleBranchFields();
    });

    // Mise à jour du nom du fichier sélectionné
    function updateFileName(input) {
        var fileName = input.files[0] ? input.files[0].name : 'Choisir un logo';
        document.getElementById('logoFileName').textContent = fileName;
    }
</script>