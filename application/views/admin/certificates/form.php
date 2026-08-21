<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <?php echo isset($certificate) ? 'Modifier' : 'Créer'; ?> un Modèle de Certificat
                        </h3>
                    </div>

                    <form action="<?php echo isset($certificate) ? base_url('admin/certificate_manager/edit/'.$certificate->id) : base_url('admin/certificate_manager/create'); ?>"
                          method="post"
                          enctype="multipart/form-data">
                        <div class="box-body">
                            <?php echo $this->customlib->getCSRF(); ?>

                            <?php if ($this->session->flashdata('msg')): ?>
                                <?php echo $this->session->flashdata('msg'); ?>
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Type de certificat <span class="text-danger">*</span></label>
                                        <select name="template_type" class="form-control" id="template_type" <?php echo isset($certificate) ? 'disabled' : ''; ?>>
                                            <option value="">-- Sélectionner --</option>
                                            <?php foreach ($types as $key => $type): ?>
                                                <option value="<?php echo $key; ?>"
                                                    <?php echo isset($certificate) && $certificate->template_type == $key ? 'selected' : ''; ?>>
                                                    <?php echo $type['name']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php echo form_error('template_type'); ?>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Titre du certificat <span class="text-danger">*</span></label>
                                        <input type="text" name="title" class="form-control"
                                               value="<?php echo isset($certificate) ? $certificate->title : set_value('title'); ?>">
                                        <?php echo form_error('title'); ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Contenu du certificat <span class="text-danger">*</span></label>
                                        <textarea name="content_body" class="form-control" rows="5" id="content_body"><?php
                                            echo isset($certificate) ? $certificate->content_body : set_value('content_body');
                                            ?></textarea>
                                        <?php echo form_error('content_body'); ?>

                                        <?php if (!isset($certificate)): ?>
                                            <small class="help-block">
                                                <strong>Variables disponibles :</strong>
                                                <?php
                                                if ($this->input->post('template_type') && isset($types[$this->input->post('template_type')]['variables'])) {
                                                    echo implode(', ', $types[$this->input->post('template_type')]['variables']);
                                                }
                                                ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Texte de signature</label>
                                        <input type="text" name="signature_text" class="form-control"
                                               value="<?php echo isset($certificate) ? $certificate->signature_text : set_value('signature_text'); ?>"
                                               placeholder="Ex: Le Directeur Général">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Couleur d'en-tête</label>
                                        <input type="text" name="header_color" class="form-control my-colorpicker1"
                                               value="<?php echo isset($certificate) ? $certificate->header_color : '#453278'; ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Logo</label>
                                        <input type="file" name="logo" class="form-control">
                                        <?php if (isset($certificate) && $certificate->logo_path): ?>
                                            <input type="hidden" name="old_logo" value="<?php echo $certificate->logo_path; ?>">
                                            <div class="margin-top-5">
                                                <img src="<?php echo base_url($certificate->logo_path); ?>" width="50" height="50">
                                                <small class="text-muted">Logo actuel</small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Signature (image)</label>
                                        <input type="file" name="signature" class="form-control">
                                        <?php if (isset($certificate) && $certificate->signature_path): ?>
                                            <input type="hidden" name="old_signature" value="<?php echo $certificate->signature_path; ?>">
                                            <div class="margin-top-5">
                                                <img src="<?php echo base_url($certificate->signature_path); ?>" width="100" height="40">
                                                <small class="text-muted">Signature actuelle</small>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <?php if (!isset($certificate)): ?>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Code unique</label>
                                            <input type="text" name="generated_code" class="form-control" readonly
                                                   value="<?php echo $generated_code; ?>">
                                            <small class="help-block">Ce code sera utilisé pour identifier ce modèle de certificat</small>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary pull-right">
                                <i class="fa fa-save"></i> <?php echo $this->lang->line('save'); ?>
                            </button>
                            <a href="<?php echo base_url('admin/certificate_manager'); ?>" class="btn btn-default">
                                <i class="fa fa-times"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Color picker
        $('.my-colorpicker1').colorpicker();

        // Mettre à jour les variables disponibles selon le type
        $('#template_type').change(function() {
            var type = $(this).val();
            if (type) {
                $.ajax({
                    url: '<?php echo base_url("admin/certificate_manager/get_variables"); ?>',
                    method: 'post',
                    data: {type: type},
                    success: function(data) {
                        if (data.variables) {
                            $('.help-block').html('<strong>Variables disponibles :</strong> ' + data.variables.join(', '));
                        }
                    }
                });
            }
        });
    });
</script>