<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-upload"></i> Importation des entrées de stock
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Importation par fichier CSV</h3>
                    </div>

                    <?php if ($this->session->flashdata('msg')) { ?>
                        <div class="box-body">
                            <?php echo $this->session->flashdata('msg'); ?>
                        </div>
                    <?php } ?>

                    <form action="<?php echo site_url('admin/stockentry/do_import'); ?>" method="post" enctype="multipart/form-data" id="importForm">
                        <div class="box-body">
                            <?php echo $this->customlib->getCSRF(); ?>

                            <div class="row">
                                <!-- Remplacer la section des instructions par : -->
                                <div class="alert alert-info">
                                    <h4><i class="fa fa-info-circle"></i> Instructions d'importation</h4>
                                    <p><strong>Fonctionnalités automatiques :</strong></p>
                                    <ul>
                                        <li>Les <strong>nouvelles catégories</strong> seront créées automatiquement</li>
                                        <li>Les <strong>nouveaux articles</strong> seront créés automatiquement</li>
                                        <li>Les <strong>articles existants</strong> seront utilisés si trouvés</li>
                                        <li>Les <strong>entrées de stock</strong> seront créées avec leur référence unique</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="file">Sélectionner un fichier CSV <span class="text-danger">*</span></label>
                                        <input type="file" name="file" id="file" class="form-control" required accept=".csv">
                                        <p class="help-block">Format accepté: .csv (maximum 2MB)</p>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Actions</label><br>
                                        <button type="submit" class="btn btn-primary" id="submitBtn">
                                            <i class="fa fa-upload"></i> Importer
                                        </button>
                                        <a href="<?php echo site_url('admin/stockentry/exportformat'); ?>" class="btn btn-success">
                                            <i class="fa fa-download"></i> Télécharger le modèle
                                        </a>
                                        <a href="<?php echo site_url('admin/stockentry'); ?>" class="btn btn-default">
                                            <i class="fa fa-arrow-left"></i> Retour à la liste
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="well">
                                        <h4><i class="fa fa-table"></i> Structure du fichier CSV</h4>
                                        <p>Votre fichier CSV doit contenir les colonnes suivantes (dans cet ordre) :</p>

                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <?php foreach ($field as $key => $value) { ?>
                                                    <th><?php echo $value; ?></th>
                                                <?php } ?>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <?php foreach ($field as $key => $value) { ?>
                                                    <td>
                                                        <?php if (in_array($key, ['reference', 'designation', 'issue_date', 'item_name', 'category_name', 'quantity'])) { ?>
                                                            <span class="text-danger">*</span>
                                                        <?php } ?>
                                                        <?php echo $key; ?>
                                                    </td>
                                                <?php } ?>
                                            </tr>
                                            </tbody>
                                        </table>

                                        <div class="alert alert-warning">
                                            <h5><i class="fa fa-exclamation-triangle"></i> Notes importantes :</h5>
                                            <ul>
                                                <li>Les champs marqués d'une <span class="text-danger">*</span> sont obligatoires</li>
                                                <li>La <strong>référence</strong> doit être unique pour chaque entrée de stock</li>
                                                <li>Le <strong>item_code</strong> est optionnel - s'il n'est pas fourni, le système utilisera le nom</li>
                                                <li>Si un article existe déjà avec le même nom et catégorie, il sera réutilisé</li>
                                                <li>Si un article existe avec le même code mais nom différent, il sera mis à jour</li>
                                                <li>Les nouvelles catégories sont créées avec le statut "actif"</li>
                                                <li>Format de date recommandé : <strong>JJ/MM/AAAA</strong> (ex: 25/12/2024)</li>
                                                <li>Tous les articles avec la même référence seront groupés dans la même entrée</li>
                                            </ul>
                                        </div>

                                        <h5>Exemple de données :</h5>
                                        <pre class="bg-light p-3 border rounded">
reference,designation,issue_date,item_code,item_name,category_name,quantity,unit,unit_price,total_price
ES-202412-0001,Approvisionnement Décembre,15/12/2024,ART001,Ordinateur Portable,Informatique,5,Unité,1200.00,6000.00
ES-202412-0001,Approvisionnement Décembre,15/12/2024,ART002,Souris Sans Fil,Informatique,10,Pièce,25.50,255.00
ES-202412-0002,Matériel Bureau,20/12/2024,ART003,Chaise de Bureau,Mobilier,2,Unité,150.00,300.00
                                        </pre>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Validation du fichier
        $('#importForm').submit(function(e) {
            var fileInput = $('#file');
            var file = fileInput[0].files[0];

            if (!file) {
                alert('Veuillez sélectionner un fichier CSV');
                return false;
            }

            // Vérifier l'extension
            var fileName = file.name;
            var fileExt = fileName.split('.').pop().toLowerCase();

            if (fileExt !== 'csv') {
                alert('Seuls les fichiers CSV sont autorisés');
                return false;
            }

            // Vérifier la taille (max 2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Le fichier est trop volumineux. Maximum 2MB.');
                return false;
            }

            // Afficher un indicateur de chargement
            $('#submitBtn').html('<i class="fa fa-spinner fa-spin"></i> Importation en cours...');
            $('#submitBtn').prop('disabled', true);

            return true;
        });

        // Réactiver le bouton si le formulaire n'est pas soumis
        $('#importForm').on('reset', function() {
            $('#submitBtn').html('<i class="fa fa-upload"></i> Importer');
            $('#submitBtn').prop('disabled', false);
        });
    });
</script>

<style type="text/css">
    .well {
        background: #f8f9fa;
        border: 1px solid #e3e6f0;
        border-radius: 0.35rem;
    }
    .table th {
        background: #4e73df;
        color: white;
    }
    .text-danger {
        color: #e74a3b !important;
        font-weight: bold;
    }
</style>