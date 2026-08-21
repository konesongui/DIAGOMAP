<div class="modal-body" id="getdetails" style="padding: 20px;">
    <div class="table-responsive">
        <table class="table table-bordered table-striped" style="margin-bottom: 0;">
            <tbody>
            <!-- Identité -->
            <tr style="background: #f8fafc;">
                <th colspan="4" style="text-align: center; font-weight: 700; color: #1e293b; background: #e2e8f0;">
                    <i class="fa fa-id-card" style="margin-right: 8px;"></i> IDENTITÉ DU VISITEUR
                </th>
            </tr>
            <tr>
                <th style="width: 15%; background: white;">Nom</th>
                <td style="width: 35%;"><?php echo htmlspecialchars($data['name'] ?? ''); ?></td>
                <th style="width: 15%; background: white;">Prénom</th>
                <td style="width: 35%;"><?php echo htmlspecialchars($data['firstname'] ?? ''); ?></td>
            </tr>
            <tr>
                <th style="background: white;">Téléphone</th>
                <td><?php echo htmlspecialchars($data['contact'] ?? ''); ?></td>
                <th style="background: white;">Email</th>
                <td><?php echo htmlspecialchars($data['email'] ?? ''); ?></td>
            </tr>
            <tr>
                <th style="background: white;">Organisation</th>
                <td><?php echo htmlspecialchars($data['organisation'] ?? ''); ?></td>
                <th style="background: white;">Fonction</th>
                <td><?php echo htmlspecialchars($data['function'] ?? ''); ?></td>
            </tr>
            <tr>
                <th style="background: white;">Motif</th>
                <td colspan="3"><?php echo htmlspecialchars($data['purpose'] ?? ''); ?></td>
            </tr>

            <!-- Pièce d'identité -->
            <tr style="background: #f0fdf4;">
                <th colspan="4" style="text-align: center; font-weight: 700; color: #1e293b; background: #d1fae5;">
                    <i class="fa fa-id-badge" style="margin-right: 8px;"></i> PIÈCE D'IDENTITÉ & SÉCURITÉ
                </th>
            </tr>
            <tr>
                <th style="background: white;">Type de pièce</th>
                <td><?php echo htmlspecialchars($data['id_type'] ?? ''); ?></td>
                <th style="background: white;">Numéro de pièce</th>
                <td><?php echo htmlspecialchars($data['id_proof'] ?? ''); ?></td>
            </tr>
            <tr>
                <th style="background: white;">Niveau d'accès</th>
                <td><?php echo htmlspecialchars($data['access_level'] ?? ''); ?></td>
                <th style="background: white;">N° de badge</th>
                <td><?php echo htmlspecialchars($data['badge'] ?? ''); ?></td>
            </tr>

            <!-- Informations visite -->
            <tr style="background: #eff6ff;">
                <th colspan="4" style="text-align: center; font-weight: 700; color: #1e293b; background: #dbeafe;">
                    <i class="fa fa-calendar" style="margin-right: 8px;"></i> INFORMATIONS DE LA VISITE
                </th>
            </tr>
            <tr>
                <th style="background: white;">Date</th>
                <td>
                    <?php
                    if (!empty($data['date'])) {
                        echo date($this->customlib->getSchoolDateFormat(), $this->customlib->dateyyyymmddTodateformat($data['date']));
                    }
                    ?>
                </td>
                <th style="background: white;">Nombre de personnes</th>
                <td><?php echo htmlspecialchars($data['no_of_pepple'] ?? 1); ?></td>
            </tr>
            <tr>
                <th style="background: white;">Heure d'arrivée</th>
                <td><?php echo htmlspecialchars($data['in_time'] ?? ''); ?></td>
                <th style="background: white;">Heure de départ</th>
                <td>
                    <?php if (empty($data['out_time'])) : ?>
                        <span style="color: #059669; font-weight: 600;">
                                <i class="fa fa-circle" style="font-size: 8px;"></i> En cours
                            </span>
                    <?php else : ?>
                        <?php echo htmlspecialchars($data['out_time']); ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php if (!empty($data['note'])) : ?>
                <tr>
                    <th style="background: white;">Note</th>
                    <td colspan="3"><?php echo htmlspecialchars($data['note']); ?></td>
                </tr>
            <?php endif; ?>

            <!-- Document -->
            <?php if (!empty($data['image'])) : ?>
               <!-- <tr style="background: #fef3c7;">
                    <th colspan="4" style="text-align: center; font-weight: 700; color: #1e293b; background: #fde68a;">
                        <i class="fa fa-file" style="margin-right: 8px;"></i> DOCUMENT ATTACHÉ
                    </th>
                </tr>
                <tr>
                    <th style="background: white;">Document</th>
                    <td colspan="3">
                        <i class="fa fa-file-pdf-o" style="color: #dc2626;"></i>
                        <?php echo htmlspecialchars($data['image']); ?>
                        <a href="<?php echo base_url('admin/visitors/download/' . $data['image']); ?>" class="btn btn-sm btn-primary" style="margin-left: 10px; padding: 2px 12px;">
                            <i class="fa fa-download"></i> Télécharger
                        </a>
                    </td>
                </tr>-->
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>