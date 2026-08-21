<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-cogs"></i> Test de l'API FNE
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Diagnostic de connexion</h3>
                    </div>
                    <div class="box-body">
                        <table class="table table-bordered">
                            <tr>
                                <th>Test</th>
                                <th>Résultat</th>
                                <th>Détails</th>
                            </tr>
                            <tr>
                                <td>Version PHP</td>
                                <td>
                                    <?php if (version_compare(PHP_VERSION, '7.0', '>=')): ?>
                                        <span class="label label-success">OK</span>
                                    <?php else: ?>
                                        <span class="label label-danger">Attention</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= PHP_VERSION ?></td>
                            </tr>
                            <tr>
                                <td>Extension cURL</td>
                                <td>
                                    <?php if ($curl_enabled): ?>
                                        <span class="label label-success">Activé</span>
                                    <?php else: ?>
                                        <span class="label label-danger">Désactivé</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $curl_enabled ? $curl_version['version'] : 'Non installé' ?></td>
                            </tr>
                            <tr>
                                <td>Connexion au serveur FNE</td>
                                <td>
                                    <?php if ($fne_server_reachable): ?>
                                        <span class="label label-success">Accessible</span>
                                    <?php else: ?>
                                        <span class="label label-danger">Inaccessible</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $fne_server_error ?: 'Connexion réussie' ?></td>
                            </tr>
                            <tr>
                                <td>Configuration FNE</td>
                                <td>
                                    <?php if (!empty($fne_config['api_key'])): ?>
                                        <span class="label label-success">Configuré</span>
                                    <?php else: ?>
                                        <span class="label label-warning">Non configuré</span>
                                    <?php endif; ?>
                                </td>
                                <td>URL: <?= $fne_config['test_url'] ?><br>API Key: <?= substr($fne_config['api_key'], 0, 5) ?>...<?= substr($fne_config['api_key'], -5) ?></td>
                            </tr>
                        </table>

                        <div class="alert alert-info">
                            <h4><i class="fa fa-info-circle"></i> Comment tester ?</h4>
                            <p>Utilisez l'URL : <code><?= base_url('admin/invoiceitem/test_fne_format/ID_FACTURE') ?></code></p>
                            <p>Remplacez ID_FACTURE par l'ID d'une facture existante.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>