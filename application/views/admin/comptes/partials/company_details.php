<style>
    /* Styles pour les modals */
    .modal-header {
        background: linear-gradient(135deg, #1E3A8A, #1E3A8A);
        color: white;
        border-bottom: none;
        border-radius: 8px 8px 0 0;
    }

    .modal-header .close {
        color: white;
        text-shadow: none;
        opacity: 0.8;
    }

    .modal-header .close:hover {
        opacity: 1;
    }

    .modal-title {
        font-weight: 600;
    }

    .modal-content {
        border: none;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }

    .modal-footer {
        border-top: 1px solid #f0f0f0;
        padding: 15px 20px;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <!-- En-tête avec logo et nom -->
        <div class="text-center mb-4">
            <?php
            $logo = is_object($company) ? ($company->logo ?? '') : ($company['logo'] ?? '');
            $nom = is_object($company) ? $company->nom : $company['nom'];

            if (!empty($logo) && file_exists(FCPATH . 'uploads/logos/' . $logo)): ?>
               <!-- <img src="<?php echo base_url('uploads/logos/' . $logo); ?>"
                     alt="Logo <?php echo html_escape($nom); ?>"
                     class="img-circle"
                     style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #1E3A8A;">-->
            <?php else: ?>
                <!--<div class="img-circle" style="width: 100px; height: 100px; background: #1E3A8A; color: white; display: flex; align-items: center; justify-content: center; font-size: 24px; font-weight: bold; margin: 0 auto; border-radius: 50%;">
                    <?php echo strtoupper(substr($nom, 0, 2)); ?>
                </div>-->
            <?php endif; ?>
            <h3 class="mt-3" style="color: #344767; font-weight: 600;"><?php echo html_escape($nom); ?></h3>
        </div>

        <!-- Informations principales -->
        <div class="row">
            <div class="col-md-6">
                <div class="info-box" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                    <h5 style="color: #3d5af1; font-weight: 600; margin-bottom: 10px;">
                        <i class="fa fa-sitemap"></i> Structure
                    </h5>
                    <p><strong>Type :</strong> <?php echo (!empty($company->type_structure) && $company->type_structure === 'succursale') ? 'Succursale' : 'Siège'; ?></p>
                    <?php if (!empty($company->parent_nom)): ?>
                        <p><strong>Siège :</strong> <?php echo html_escape($company->parent_nom); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($company->code_succursale)): ?>
                        <p><strong>Code succursale :</strong> <?php echo html_escape($company->code_succursale); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-box" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                    <h5 style="color: #1E3A8A; font-weight: 600; margin-bottom: 10px;">
                        <i class="fa fa-envelope"></i> Contact
                    </h5>
                    <?php
                    $email = is_object($company) ? $company->email : $company['email'];
                    $telephone = is_object($company) ? $company->telephone : $company['telephone'];
                    ?>
                    <p><strong>Email :</strong> <?php echo !empty($email) ? html_escape($email) : 'Non renseigné'; ?></p>
                    <p><strong>Téléphone :</strong> <?php echo !empty($telephone) ? html_escape($telephone) : 'Non renseigné'; ?></p>
                </div>

                <?php if (!empty($branches)): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="info-box" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                                <h5 style="color: #3d5af1; font-weight: 600; margin-bottom: 10px;">
                                    <i class="fa fa-code-fork"></i> Succursales rattachées
                                </h5>
                                <ul style="margin:0; padding-left:18px;">
                                    <?php foreach ($branches as $branch): ?>
                                        <li>
                                            <?php echo html_escape($branch['nom']); ?>
                                            <?php if (!empty($branch['code_succursale'])): ?>
                                                - <strong><?php echo html_escape($branch['code_succursale']); ?></strong>
                                            <?php endif; ?>
                                            (<?php echo html_escape($branch['statut']); ?>)
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <div class="info-box" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                    <h5 style="color: #1E3A8A; font-weight: 600; margin-bottom: 10px;">
                        <i class="fa fa-map-marker"></i> Adresse
                    </h5>
                    <?php
                    $adresse = is_object($company) ? $company->adresse : $company['adresse'];
                    ?>
                    <p><?php echo !empty($adresse) ? nl2br(html_escape($adresse)) : 'Non renseignée'; ?></p>
                </div>
            </div>
        </div>

        <!-- Informations abonnement -->
        <div class="row">
            <div class="col-md-4">
                <div class="info-box" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                    <h5 style="color: #3d5af1; font-weight: 600; margin-bottom: 10px;">
                        <i class="fa fa-star"></i> Forfait
                    </h5>
                    <?php
                    $forfait = is_object($company) ? $company->forfait : $company['forfait'];
                    $forfait_class = 'default';
                    if (!empty($forfait)) {
                        $forfait_lower = strtolower($forfait);
                        switch ($forfait_lower) {
                            case 'basic':
                                $forfait_class = 'primary';
                                break;
                            case 'standard':
                                $forfait_class = 'success';
                                break;
                            case 'premium':
                                $forfait_class = 'info';
                                break;
                            case 'pro':
                                $forfait_class = 'danger';
                                break;
                            default:
                                $forfait_class = 'default';
                        }
                    }
                    ?>
                    <span class="badge badge-<?php echo $forfait_class; ?>" style="padding: 8px 15px; font-size: 14px; text-transform: uppercase;">
                        <?php echo !empty($forfait) ? html_escape($forfait) : 'Non défini'; ?>
                    </span>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-box" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                    <h5 style="color: #3d5af1; font-weight: 600; margin-bottom: 10px;">
                        <i class="fa fa-calendar-check-o"></i> Début
                    </h5>
                    <?php
                    $date_debut = is_object($company) ? $company->date_debut : $company['date_debut'];
                    ?>
                    <span class="label label-primary" style="padding: 6px 12px; border-radius: 4px; font-size: 14px; display: inline-block;">
                        <?php echo !empty($date_debut) ? date('d/m/Y', strtotime($date_debut)) : 'Non définie'; ?>
                    </span>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-box" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                    <h5 style="color: #3d5af1; font-weight: 600; margin-bottom: 10px;">
                        <i class="fa fa-calendar-times-o"></i> Expiration
                    </h5>
                    <?php
                    $date_expiration = is_object($company) ? $company->date_expiration : $company['date_expiration'];
                    ?>
                    <span class="label label-warning" style="padding: 6px 12px; border-radius: 4px; font-size: 14px; display: inline-block;">
                        <?php echo !empty($date_expiration) ? date('d/m/Y', strtotime($date_expiration)) : 'Non définie'; ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Statut et informations supplémentaires -->
        <div class="row">
            <div class="col-md-6">
                <div class="info-box" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                    <h5 style="color: #3d5af1; font-weight: 600; margin-bottom: 10px;">
                        <i class="fa fa-info-circle"></i> Statut
                    </h5>
                    <?php
                    $statut = is_object($company) ? $company->statut : $company['statut'];
                    $statut_class = (!empty($statut) && strtolower($statut) == "actif") ? "success" : "danger";
                    ?>
                    <span class="badge badge-<?php echo $statut_class; ?>" style="padding: 8px 15px; font-size: 14px;">
                        <?php echo !empty($statut) ? html_escape($statut) : 'Inactif'; ?>
                    </span>
                </div>
            </div>

            <div class="col-md-6">
                <div class="info-box" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                    <h5 style="color: #3d5af1; font-weight: 600; margin-bottom: 10px;">
                        <i class="fa fa-id-card"></i> ID
                    </h5>
                    <?php
                    $id_company = is_object($company) ? $company->id : $company['id'];
                    ?>
                    <p style="font-family: monospace; background: #e9ecef; padding: 5px 10px; border-radius: 4px; display: inline-block;">
                        #<?php echo $id_company; ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Jours restants -->
        <?php
        $date_expiration = is_object($company) ? $company->date_expiration : $company['date_expiration'];
        if (!empty($date_expiration)):
            ?>
            <div class="row">
                <div class="col-md-12">
                    <?php
                    $bg_color = '#e7f7ef';
                    $border_color = '#28a745';
                    $message = '';

                    if ($jours_restants > 0) {
                        $bg_color = $jours_restants > 30 ? '#e7f7ef' : ($jours_restants > 7 ? '#fff3cd' : '#f8d7da');
                        $border_color = $jours_restants > 30 ? '#28a745' : ($jours_restants > 7 ? '#ffc107' : '#dc3545');
                        $message = "<p style='font-size: 18px; font-weight: bold; color: #28a745;'>" . $jours_restants . " jours</p>
                               <p style='margin: 0; color: #6c757d;'>L'abonnement expire le " . date('d/m/Y', strtotime($date_expiration)) . "</p>";
                    } else if ($jours_restants == 0) {
                        $bg_color = '#fff3cd';
                        $border_color = '#ffc107';
                        $message = "<p style='font-size: 18px; font-weight: bold; color: #ffc107;'>Expire aujourd'hui !</p>";
                    } else {
                        $bg_color = '#f8d7da';
                        $border_color = '#dc3545';
                        $message = "<p style='font-size: 18px; font-weight: bold; color: #dc3545;'>Expiré depuis " . abs($jours_restants) . " jours</p>";
                    }
                    ?>
                    <div class="info-box" style="background: <?php echo $bg_color; ?>;
                            padding: 15px; border-radius: 8px; border-left: 4px solid <?php echo $border_color; ?>;">
                        <h5 style="color: #344767; font-weight: 600; margin-bottom: 10px;">
                            <i class="fa fa-clock-o"></i> Jours restants
                        </h5>
                        <?php echo $message; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Date de création et mise à jour -->
        <div class="row">
            <div class="col-md-12">
                <div class="info-box" style="background: #f0f8ff; padding: 15px; border-radius: 8px; margin-bottom: 15px; border-left: 4px solid #3d5af1;">
                    <h5 style="color: #3d5af1; font-weight: 600; margin-bottom: 10px;">
                        <i class="fa fa-history"></i> Historique
                    </h5>
                    <?php
                    $created_at = is_object($company) ? $company->created_at : $company['created_at'];
                    $updated_at = is_object($company) ? $company->updated_at : $company['updated_at'];
                    ?>
                    <p style="margin-bottom: 5px;">
                        <strong>Créé le :</strong>
                        <?php echo !empty($created_at) ? date('d/m/Y à H:i', strtotime($created_at)) : 'Date inconnue'; ?>
                    </p>
                    <?php if (!empty($updated_at) && $updated_at != $created_at): ?>
                       <!-- <p style="margin: 0;">
                            <strong>Modifié le :</strong>
                            <?php echo date('d/m/Y à H:i', strtotime($updated_at)); ?>
                        </p>-->
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .info-box {
        transition: transform 0.2s ease-in-out;
    }
    .info-box:hover {
        transform: translateY(-2px);
    }
    .info-box h5 {
        border-bottom: 2px solid #3d5af1;
        padding-bottom: 5px;
        margin-bottom: 15px;
    }
    .info-box p {
        margin-bottom: 8px;
        color: #555;
        line-height: 1.5;
    }
    .label {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 14px;
    }
    .badge {
        font-size: 12px;
        font-weight: 600;
    }
</style>