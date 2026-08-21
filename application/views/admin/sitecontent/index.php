<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Gestion du site</h1>
        <?php if ($this->session->flashdata('msg')) { ?>
            <div class="alert alert-success"><?php echo $this->session->flashdata('msg'); ?></div>
        <?php } ?>
        <?php if ($this->session->flashdata('error')) { ?>
            <div class="alert alert-danger"><?php echo $this->session->flashdata('error'); ?></div>
        <?php } ?>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Menus principaux</h3>
                    </div>
                    <div class="box-body">
                        <form id="siteContentForm" action="<?php echo site_url('admin/sitecontent/save'); ?>" method="post" enctype="multipart/form-data">
                            <?php echo $this->customlib->getCSRF(); ?>

                            <div id="menusContainer">
                                <?php $menus = $content['menus'] ?? [];
                                if (empty($menus)) $menus = [];
                                foreach ($menus as $idx => $m) { ?>
                                    <div class="menu-row mb-2 p-2" style="border:1px solid #eee;border-radius:6px;">
                                        <div class="form-group">
                                            <label>Libellé</label>
                                            <input type="text" name="menu[<?php echo $idx; ?>][title]" class="form-control" value="<?php echo html_escape($m['title']); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>URL (page_url ou externe)</label>
                                            <input type="text" name="menu[<?php echo $idx; ?>][url]" class="form-control" value="<?php echo html_escape($m['page_url'] ?? $m['url'] ?? ''); ?>">
                                        </div>
                                        <div class="form-check">
                                            <label><input type="checkbox" name="menu[<?php echo $idx; ?>][ext]" <?php echo (!empty($m['ext_url']) || !empty($m['ext'])) ? 'checked' : ''; ?>> URL externe</label>
                                            &nbsp;&nbsp;
                                            <label><input type="checkbox" name="menu[<?php echo $idx; ?>][new_tab]" <?php echo !empty($m['open_new_tab']) ? 'checked' : ''; ?>> Ouvrir dans un nouvel onglet</label>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <button type="button" id="addMenuBtn" class="btn btn-sm btn-secondary">Ajouter un menu</button>

                            <hr>

                            <h4>Blocs (images / vidéos / contenu)</h4>
                            <div id="blocksContainer">
                                <?php $blocks = $content['blocks'] ?? [];
                                if (empty($blocks)) $blocks = [];
                                foreach ($blocks as $i => $b) { ?>
                                    <div class="block-row mb-3 p-2" style="border:1px solid #eee;border-radius:6px;">
                                        <div class="form-group">
                                            <label>Titre</label>
                                            <input type="text" name="block_title[]" class="form-control" value="<?php echo html_escape($b['title']); ?>">
                                        </div>
                                        <div class="form-group">
                                            <label>Contenu</label>
                                            <textarea name="block_content[]" class="form-control" rows="4"><?php echo html_escape($b['content']); ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Image (optionnel)</label>
                                            <input type="file" name="image_<?php echo $i; ?>" class="form-control">
                                            <?php if (!empty($b['image'])) { ?><div class="mt-2"><img src="<?php echo base_url($b['image']); ?>" style="max-width:200px;"></div><?php } ?>
                                        </div>
                                        <div class="form-group">
                                            <label>Vidéo (optionnel)</label>
                                            <input type="file" name="video_<?php echo $i; ?>" class="form-control">
                                            <?php if (!empty($b['video'])) { ?><div class="mt-2">Fichier vidéo: <?php echo basename($b['video']); ?></div><?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <button type="button" id="addBlockBtn" class="btn btn-sm btn-secondary">Ajouter un bloc</button>

                            <hr>

                            <button id="saveSiteContentBtn" class="btn btn-primary" type="submit">Enregistrer</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Aide rapide</h3>
                    </div>
                    <div class="box-body">
                        <p>Créez des menus pour la barre de navigation publique et des blocs de contenu (image/vidéo + texte) qui pourront être affichés sur vos pages.</p>
                        <p>Les images et vidéos sont stockées dans <code>uploads/site_content/</code>.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    (function(){
        var menuCount = <?php echo count($menus); ?>;
        document.getElementById('addMenuBtn').addEventListener('click', function(){
            var container = document.getElementById('menusContainer');
            var idx = menuCount++;
            var div = document.createElement('div');
            div.className = 'menu-row mb-2 p-2';
            div.style = 'border:1px solid #eee;border-radius:6px;';
            div.innerHTML = '\n                <div class="form-group">\n                    <label>Libellé</label>\n                    <input type="text" name="menu['+idx+'][title]" class="form-control">\n                </div>\n                <div class="form-group">\n                    <label>URL (page_url ou externe)</label>\n                    <input type="text" name="menu['+idx+'][url]" class="form-control">\n                </div>\n                <div class="form-check">\n                    <label><input type="checkbox" name="menu['+idx+'][ext]"> URL externe</label>\n                    &nbsp;&nbsp;\n                    <label><input type="checkbox" name="menu['+idx+'][new_tab]"> Ouvrir dans un nouvel onglet</label>\n                </div>';
            container.appendChild(div);
        });

        var blockCount = <?php echo max(1, count($blocks)); ?>;
        document.getElementById('addBlockBtn').addEventListener('click', function(){
            var container = document.getElementById('blocksContainer');
            var i = blockCount++;
            var div = document.createElement('div');
            div.className = 'block-row mb-3 p-2';
            div.style = 'border:1px solid #eee;border-radius:6px;';
            div.innerHTML = '\n                <div class="form-group">\n                    <label>Titre</label>\n                    <input type="text" name="block_title[]" class="form-control">\n                </div>\n                <div class="form-group">\n                    <label>Contenu</label>\n                    <textarea name="block_content[]" class="form-control" rows="4"></textarea>\n                </div>\n                <div class="form-group">\n                    <label>Image (optionnel)</label>\n                    <input type="file" name="image_'+i+'" class="form-control">\n                </div>\n                <div class="form-group">\n                    <label>Vidéo (optionnel)</label>\n                    <input type="file" name="video_'+i+'" class="form-control">\n                </div>';
            container.appendChild(div);
        });
    })();
</script>

<script>
    (function(){
        $(document).ready(function(){
            var IMG_MAX = 2 * 1024 * 1024; // 2 MB
            var VID_MAX = 50 * 1024 * 1024; // 50 MB
            var ALLOWED_IMG = ['image/jpeg','image/png','image/gif'];
            var ALLOWED_VID = ['video/mp4','video/webm','video/ogg'];

            function isImageType(mime) { return ALLOWED_IMG.indexOf(mime) !== -1 || (mime && mime.indexOf('image/') === 0); }
            function isVideoType(mime) { return ALLOWED_VID.indexOf(mime) !== -1 || (mime && mime.indexOf('video/') === 0); }

            function validateFile(file) {
                if (!file) return {ok:true};
                var t = file.type;
                var s = file.size || 0;
                if (isImageType(t)) {
                    if (s > IMG_MAX) return {ok:false, message: 'Image trop volumineuse (max 2MB)'};
                    return {ok:true, type: 'image'};
                }
                if (isVideoType(t)) {
                    if (s > VID_MAX) return {ok:false, message: 'Vidéo trop volumineuse (max 50MB)'};
                    return {ok:true, type: 'video'};
                }
                // Fallback by extension
                var name = file.name.toLowerCase();
                if (name.match(/\.(jpg|jpeg|png|gif)$/)) {
                    if (s > IMG_MAX) return {ok:false, message: 'Image trop volumineuse (max 2MB)'};
                    return {ok:true, type: 'image'};
                }
                if (name.match(/\.(mp4|webm|ogg)$/)) {
                    if (s > VID_MAX) return {ok:false, message: 'Vidéo trop volumineuse (max 50MB)'};
                    return {ok:true, type: 'video'};
                }
                return {ok:false, message: 'Type de fichier non supporté'};
            }

            // Delegate change on file inputs inside blocksContainer
            $('#blocksContainer').on('change', 'input[type=file]', function(e){
                var input = this;
                var file = input.files && input.files[0];
                // remove any existing preview right after this input
                $(input).nextAll('.file-preview').remove();
                if (!file) return;
                var v = validateFile(file);
                if (!v.ok) {
                    alert(v.message);
                    $(input).val('');
                    return;
                }
                var $wrap = $('<div class="file-preview mt-2"></div>');
                if (v.type === 'image') {
                    var url = URL.createObjectURL(file);
                    var $img = $('<img>').attr('src', url).css({'max-width':'200px','display':'block'});
                    $wrap.append($img);
                } else if (v.type === 'video') {
                    var url = URL.createObjectURL(file);
                    var $vid = $('<video controls></video>').attr('src', url).css({'max-width':'320px','display':'block'});
                    $wrap.append($vid);
                }
                $(input).after($wrap);
            });

            // Validate all file inputs before sending
            function validateAllFiles() {
                var ok = true;
                $('#blocksContainer input[type=file]').each(function(){
                    var file = this.files && this.files[0];
                    if (!file) return true; // continue
                    var v = validateFile(file);
                    if (!v.ok) {
                        alert('Fichier invalide: ' + v.message);
                        ok = false;
                        return false; // break
                    }
                });
                return ok;
            }

            $('#siteContentForm').on('submit', function(e){
                e.preventDefault();
                if (!validateAllFiles()) return;
                var $btn = $('#saveSiteContentBtn');
                $btn.prop('disabled', true).text('Enregistrement...');
                var fd = new FormData(this);
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: fd,
                    processData: false,
                    contentType: false,
                    success: function(resp){
                        try {
                            var j = (typeof resp === 'object') ? resp : JSON.parse(resp);
                            if (j.status && j.status == 'success') {
                                location.reload();
                                return;
                            }
                            alert(j.message || 'Enregistré');
                            location.reload();
                        } catch(e) {
                            location.reload();
                        }
                    },
                    error: function(xhr){
                        var msg = 'Erreur serveur';
                        try { var j = JSON.parse(xhr.responseText); msg = j.message || msg; } catch(e){}
                        alert(msg);
                    },
                    complete: function(){
                        $btn.prop('disabled', false).text('Enregistrer');
                    }
                });
            });

        });
    })();
</script>
