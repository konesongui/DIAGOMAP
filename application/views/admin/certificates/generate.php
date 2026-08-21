<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-print"></i> Générer un certificat
                        </h3>
                    </div>

                    <form action="<?php echo base_url('admin/certificate_generator/generate'); ?>"
                          method="post"
                          id="generateForm">
                        <div class="box-body">
                            <?php echo $this->customlib->getCSRF(); ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Modèle de certificat <span class="text-danger">*</span></label>
                                        <select name="certificate_id" id="certificate_id" class="form-control" required>
                                            <option value="">-- Sélectionner un modèle --</option>
                                            <?php foreach ($certificates as $cert): ?>
                                                <option value="<?php echo $cert->id; ?>" data-type="<?php echo $cert->template_type; ?>">
                                                    <?php echo $cert->title; ?> (<?php echo $cert->template_type; ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Personnel <span class="text-danger">*</span></label>
                                        <select name="staff_id" class="form-control" required>
                                            <option value="">-- Sélectionner un employé --</option>
                                            <?php foreach ($staff_list as $staff): ?>
                                                <option value="<?php echo $staff['id']; ?>">
                                                    <?php echo $staff['name']; ?> - <?php echo $staff['designation']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Champs dynamiques selon le type de certificat -->
                            <div id="dynamicFields"></div>
                        </div>

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-download"></i> Générer et télécharger
                            </button>
                            <button type="button" class="btn btn-info" id="previewBtn">
                                <i class="fa fa-eye"></i> Aperçu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#certificate_id').change(function() {
            var type = $(this).find(':selected').data('type');
            var $dynamicFields = $('#dynamicFields');

            $dynamicFields.empty();

            if (type === 'training') {
                $dynamicFields.html(`
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nom de la formation <span class="text-danger">*</span></label>
                            <input type="text" name="training_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Durée <span class="text-danger">*</span></label>
                            <input type="text" name="duration" class="form-control" placeholder="Ex: 40 heures" required>
                        </div>
                    </div>
                </div>
            `);
            } else if (type === 'internship') {
                $dynamicFields.html(`
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Date de début <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Date de fin <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                </div>
            `);
            }
        });

        $('#previewBtn').click(function() {
            // Logique pour l'aperçu
            alert('Fonctionnalité d\'aperçu à implémenter');
        });
    });
</script>