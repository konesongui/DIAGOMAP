<table class="table table-striped">

    <tr>
        <th>Documents</th>
        <td colspan="3">
            <?php if (!empty($data['image'])): ?>
                <iframe
                        src="<?= base_url('uploads/front_office/files/' . $data['image']) ?>"
                        width="100%"
                        height="500px"
                        frameborder="0"
                        style="border: 1px solid #ccc; border-radius: 8px;">
                </iframe>
            <?php else: ?>
                Aucun fichier attaché
            <?php endif; ?>
        </td>
    </tr>
</table>

<style>
    iframe {
        max-width: 100%;
    }
</style>