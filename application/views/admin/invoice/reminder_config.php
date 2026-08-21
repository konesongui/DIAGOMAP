<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-bell"></i> Configuration des relances automatiques
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Paramètres des relances par email</h3>
                    </div>

                    <?php echo form_open('admin/invoiceitem/configure_reminders', ['class' => 'form-horizontal']); ?>
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Activer les relances automatiques</label>
                            <div class="col-sm-9">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="reminder_enabled" value="1" <?php echo set_checkbox('reminder_enabled', '1', $settings[0]['reminder_enabled'] ?? false); ?>>
                                        Oui, activer le système de relances
                                    </label>
                                </div>
                                <p class="help-block">Les relances seront envoyées automatiquement chaque jour selon le planning configuré</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Rappel avant échéance</label>
                            <div class="col-sm-3">
                                <select name="reminder_before_days" class="form-control">
                                    <option value="0">Désactivé</option>
                                    <option value="1" <?php echo set_select('reminder_before_days', '1', ($settings[0]['reminder_before_days'] ?? 0) == 1); ?>>1 jour avant</option>
                                    <option value="2" <?php echo set_select('reminder_before_days', '2', ($settings[0]['reminder_before_days'] ?? 0) == 2); ?>>2 jours avant</option>
                                    <option value="3" <?php echo set_select('reminder_before_days', '3', ($settings[0]['reminder_before_days'] ?? 0) == 3); ?>>3 jours avant</option>
                                    <option value="5" <?php echo set_select('reminder_before_days', '5', ($settings[0]['reminder_before_days'] ?? 0) == 5); ?>>5 jours avant</option>
                                    <option value="7" <?php echo set_select('reminder_before_days', '7', ($settings[0]['reminder_before_days'] ?? 0) == 7); ?>>7 jours avant</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Rappel le jour de l'échéance</label>
                            <div class="col-sm-9">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="reminder_on_due_date" value="1" <?php echo set_checkbox('reminder_on_due_date', '1', $settings[0]['reminder_on_due_date'] ?? false); ?>>
                                        Envoyer un rappel le jour même de l'échéance
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Première relance après échéance</label>
                            <div class="col-sm-3">
                                <select name="reminder_after_days_1" class="form-control">
                                    <option value="0">Désactivé</option>
                                    <option value="3" <?php echo set_select('reminder_after_days_1', '3', ($settings[0]['reminder_after_days_1'] ?? 3) == 3); ?>>3 jours après</option>
                                    <option value="5" <?php echo set_select('reminder_after_days_1', '5', ($settings[0]['reminder_after_days_1'] ?? 3) == 5); ?>>5 jours après</option>
                                    <option value="7" <?php echo set_select('reminder_after_days_1', '7', ($settings[0]['reminder_after_days_1'] ?? 3) == 7); ?>>7 jours après</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Deuxième relance après échéance</label>
                            <div class="col-sm-3">
                                <select name="reminder_after_days_2" class="form-control">
                                    <option value="0">Désactivé</option>
                                    <option value="7" <?php echo set_select('reminder_after_days_2', '7', ($settings[0]['reminder_after_days_2'] ?? 7) == 7); ?>>7 jours après</option>
                                    <option value="10" <?php echo set_select('reminder_after_days_2', '10', ($settings[0]['reminder_after_days_2'] ?? 7) == 10); ?>>10 jours après</option>
                                    <option value="15" <?php echo set_select('reminder_after_days_2', '15', ($settings[0]['reminder_after_days_2'] ?? 7) == 15); ?>>15 jours après</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Dernière relance après échéance</label>
                            <div class="col-sm-3">
                                <select name="reminder_after_days_3" class="form-control">
                                    <option value="0">Désactivé</option>
                                    <option value="15" <?php echo set_select('reminder_after_days_3', '15', ($settings[0]['reminder_after_days_3'] ?? 15) == 15); ?>>15 jours après</option>
                                    <option value="21" <?php echo set_select('reminder_after_days_3', '21', ($settings[0]['reminder_after_days_3'] ?? 15) == 21); ?>>21 jours après</option>
                                    <option value="30" <?php echo set_select('reminder_after_days_3', '30', ($settings[0]['reminder_after_days_3'] ?? 15) == 30); ?>>30 jours après</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Email expéditeur</label>
                            <div class="col-sm-6">
                                <input type="email" name="reminder_sender_email" class="form-control" value="<?php echo $settings[0]['reminder_sender_email'] ?? $settings[0]['email'] ?? ''; ?>">
                                <p class="help-block">L'adresse email qui apparaîtra comme expéditeur des relances</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label">Nom expéditeur</label>
                            <div class="col-sm-6">
                                <input type="text" name="reminder_sender_name" class="form-control" value="<?php echo $settings[0]['reminder_sender_name'] ?? $settings[0]['name'] ?? ''; ?>">
                                <p class="help-block">Le nom qui apparaîtra comme expéditeur des relances</p>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <div class="pull-right">
                            <a href="<?php echo base_url('admin/invoiceitem/reminder_history'); ?>" class="btn btn-info">
                                <i class="fa fa-history"></i> Voir l'historique
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Enregistrer
                            </button>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                </div>

                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Configuration CRON</h3>
                    </div>
                    <div class="box-body">
                        <p>Pour que les relances automatiques fonctionnent, vous devez configurer une tâche CRON sur votre serveur :</p>

                        <div class="alert alert-info">
                            <strong>Commande à exécuter quotidiennement :</strong><br>
                            <code>wget -q -O /dev/null <?php echo base_url('admin/invoiceitem/process_reminders'); ?></code>
                        </div>

                        <p>Ou en ligne de commande PHP :</p>
                        <div class="alert alert-info">
                            <code>php <?php echo FCPATH; ?>index.php admin/invoiceitem/process_reminders</code>
                        </div>

                        <p><strong>Fréquence recommandée :</strong> Une fois par jour, de préférence tôt le matin (ex: 2h00)</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    $(document).ready(function() {
        // Test d'envoi de relance
        $(document).on('click', '.test-reminder', function(e) {
            e.preventDefault();
            var invoiceId = $(this).data('id');

            Swal.fire({
                title: 'Test de relance',
                text: "Voulez-vous envoyer une relance de test pour cette facture ?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Oui, envoyer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?php echo base_url("admin/invoiceitem/test_reminder/"); ?>' + invoiceId,
                        success: function(response) {
                            Swal.fire('Succès', response, 'success');
                        },
                        error: function() {
                            Swal.fire('Erreur', 'Échec de l\'envoi', 'error');
                        }
                    });
                }
            });
        });
    });
</script>