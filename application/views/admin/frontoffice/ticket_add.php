<section class="content-header"><h1>Nouveau ticket <small>Contacter le support</small></h1></section>
<section class="content"><div class="box box-primary"><form id="ticketCreateForm" method="post" action="<?php echo site_url('admin/tickets/add_ajax'); ?>">
    <div class="box-body">
        <div class="form-group"><label for="titre">Titre</label><input id="titre" name="titre" class="form-control" required maxlength="200"></div>
        <div class="form-group"><label for="description">Description</label><textarea id="description" name="description" class="form-control" rows="6" required></textarea></div>
        <div class="row">
            <div class="col-md-4"><label>Catégorie</label><select name="categorie_id" class="form-control" required><option value="">Sélectionner</option><?php foreach ($categories as $category) { ?><option value="<?php echo (int) $category['id']; ?>"><?php echo html_escape($category['nom']); ?></option><?php } ?></select></div>
            <div class="col-md-4"><label>Priorité</label><select name="priorite_id" class="form-control" required><option value="">Sélectionner</option><?php foreach ($priorites as $priority) { ?><option value="<?php echo (int) $priority['id']; ?>"><?php echo html_escape($priority['nom']); ?></option><?php } ?></select></div>
            <div class="col-md-4"><label>Attribuer à</label><select name="assigned_to" class="form-control"><option value="">Non attribué</option><?php foreach ($staff as $member) { ?><option value="<?php echo (int) $member['id']; ?>"><?php echo html_escape($member['nom']); ?></option><?php } ?></select></div>
        </div>
        <div class="form-group" style="margin-top:15px"><label for="notes">Notes</label><textarea id="notes" name="notes" class="form-control" rows="3"></textarea></div>
    </div>
    <div class="box-footer"><a href="<?php echo site_url('admin/tickets'); ?>" class="btn btn-default">Annuler</a> <button type="submit" class="btn btn-primary"><i class="fa fa-send"></i> Envoyer</button></div>
</form></div></section>
<script>
$(function () {
    $('#ticketCreateForm').on('submit', function (event) {
        event.preventDefault();
        var form = $(this);
        $.post(form.attr('action'), form.serialize(), function (response) {
            if (response.success) {
                successMsg(response.message);
                window.location.href = '<?php echo site_url('admin/tickets'); ?>';
            } else {
                errorMsg(response.message || 'Impossible de créer le ticket.');
            }
        }, 'json').fail(function () {
            errorMsg('Une erreur est survenue lors de l’envoi du ticket.');
        });
    });
});
</script>