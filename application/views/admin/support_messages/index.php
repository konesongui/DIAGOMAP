<section class="content-header"><h1>Messages de support <small>Information affichée à la connexion</small></h1></section>
<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"><?php echo $editing ? 'Modifier le message' : 'Créer un message'; ?></h3></div>
        <form method="post" action="<?php echo site_url('admin/support_messages/save'); ?>">
            <div class="box-body">
            <?php if ($editing) { ?><input type="hidden" name="id" value="<?php echo (int) $editing['id']; ?>"><?php } ?>
            <div class="form-group"><label for="title">Titre</label><input id="title" name="title" class="form-control" required maxlength="200" placeholder="Mise à jour en cours" value="<?php echo $editing ? html_escape($editing['title']) : ''; ?>"></div>
            <div class="form-group"><label for="message">Message</label><textarea id="message" name="message" class="form-control" rows="5" required placeholder="Le service sera momentanément indisponible..."><?php echo $editing ? html_escape($editing['message']) : ''; ?></textarea></div>
            <div class="row"><div class="col-md-4"><label><input type="checkbox" name="active" value="1" <?php echo (!$editing || (int) $editing['active'] === 1) ? 'checked' : ''; ?>> Actif</label></div><div class="col-md-4"><label>Début <input type="datetime-local" name="start_at" class="form-control" value="<?php echo ($editing && !empty($editing['start_at'])) ? date('Y-m-d\\TH:i', strtotime($editing['start_at'])) : ''; ?>"></label></div><div class="col-md-4"><label>Fin <input type="datetime-local" name="end_at" class="form-control" value="<?php echo ($editing && !empty($editing['end_at'])) ? date('Y-m-d\\TH:i', strtotime($editing['end_at'])) : ''; ?>"></label></div></div>
            </div>
            <div class="box-footer"><button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Enregistrer</button></div>
        </form>
    </div>
    <div class="box box-default"><div class="box-header with-border"><h3 class="box-title">Messages enregistrés</h3></div><div class="box-body table-responsive">
        <table class="table table-bordered table-striped"><thead><tr><th>Titre</th><th>Message</th><th>État</th><th>Actions</th></tr></thead><tbody>
        <?php foreach ($messages as $item) { ?><tr><td><?php echo html_escape($item['title']); ?></td><td><?php echo nl2br(html_escape($item['message'])); ?></td><td><?php echo ((int) $item['active'] === 1) ? '<span class="label label-success">Actif</span>' : '<span class="label label-default">Inactif</span>'; ?></td><td><a class="btn btn-default btn-xs" href="<?php echo site_url('admin/support_messages/edit/' . (int) $item['id']); ?>"><i class="fa fa-pencil"></i></a> <a class="btn btn-danger btn-xs" href="<?php echo site_url('admin/support_messages/delete/' . (int) $item['id']); ?>" onclick="return confirm('Supprimer ce message ?');"><i class="fa fa-trash"></i></a></td></tr><?php } ?>
        <?php if (empty($messages)) { ?><tr><td colspan="4" class="text-center">Aucun message enregistré.</td></tr><?php } ?></tbody></table>
    </div></div>
</section>