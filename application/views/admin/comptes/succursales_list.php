<div class="content-wrapper" style="min-height: 946px; background: #f5f7fb;">
    <section class="content-header">
        <h1>
            <i class="fa fa-sitemap"></i> Succursales
            <?php if (!empty($head_office)): ?>
                <small><?php echo html_escape($head_office->nom); ?></small>
            <?php endif; ?>
        </h1>
    </section>

    <section class="content">
        <?php if ($this->session->flashdata('msg')): ?>
            <div class="alert alert-info"><?php echo $this->session->flashdata('msg'); ?></div>
        <?php endif; ?>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Liste des succursales rattachées</h3>
                <div class="box-tools pull-right">
                    <a href="<?php echo site_url('admin/comptes/create_succursale'); ?>" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus"></i> Nouvelle succursale
                    </a>
                </div>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Code</th>
                            <th>Contact</th>
                            <th>Forfait</th>
                            <th>Statut</th>
                            <th>Héritage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($branches)): ?>
                            <?php foreach ($branches as $branch): ?>
                                <tr>
                                    <td><?php echo html_escape($branch['nom']); ?></td>
                                    <td><?php echo !empty($branch['code_succursale']) ? html_escape($branch['code_succursale']) : '-'; ?></td>
                                    <td>
                                        <?php echo !empty($branch['email']) ? html_escape($branch['email']) : '-'; ?><br>
                                        <small><?php echo !empty($branch['telephone']) ? html_escape($branch['telephone']) : ''; ?></small>
                                    </td>
                                    <td><?php echo !empty($branch['forfait']) ? html_escape($branch['forfait']) : '-'; ?></td>
                                    <td>
                                        <span class="label label-<?php echo (!empty($branch['statut']) && $branch['statut'] === 'actif') ? 'success' : 'warning'; ?>">
                                            <?php echo !empty($branch['statut']) ? html_escape($branch['statut']) : '-'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $inherit = array();
                                        if (!empty($branch['inherit_settings'])) {
                                            $inherit[] = 'Paramètres';
                                        }
                                        if (!empty($branch['inherit_roles'])) {
                                            $inherit[] = 'Permissions';
                                        }
                                        if (!empty($branch['inherit_ohada'])) {
                                            $inherit[] = 'OHADA';
                                        }
                                        echo !empty($inherit) ? html_escape(implode(', ', $inherit)) : '-';
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Aucune succursale enregistrée pour ce siège.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
