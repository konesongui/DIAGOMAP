<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-bullhorn"></i> Demandes de démonstration</h1>
    </section>

    <section class="content">
        <!-- Messages flash -->
        <?php if ($this->session->flashdata('msg')): ?>
            <div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?php echo $this->session->flashdata('msg'); ?>
            </div>
        <?php endif; ?>

        <!-- Cartes de statistiques -->
        <div class="row" style="margin-bottom:30px;">
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="small-box" style="background:#fff; border-left:4px solid #1b4f80; border-radius:4px; box-shadow:0 2px 10px rgba(0,0,0,0.08); padding:15px; position:relative;">
                    <div class="inner">
                        <h3 style="margin:0; color:#1b4f80;"><?php echo (int)($stats['total'] ?? 0); ?></h3>
                        <p style="margin:5px 0 0; color:#6c757d; font-size:14px;">Total demandes</p>
                    </div>
                    <div style="position:absolute; right:15px; top:50%; transform:translateY(-50%); color:#1b4f80; opacity:0.5; font-size:32px;">
                        <i class="fa fa-bullhorn"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="small-box" style="background:#fff; border-left:4px solid #28a745; border-radius:4px; box-shadow:0 2px 10px rgba(0,0,0,0.08); padding:15px; position:relative;">
                    <div class="inner">
                        <h3 style="margin:0; color:#28a745;"><?php echo (int)($stats['acceptée'] ?? 0); ?></h3>
                        <p style="margin:5px 0 0; color:#6c757d; font-size:14px;">Demandes acceptées</p>
                    </div>
                    <div style="position:absolute; right:15px; top:50%; transform:translateY(-50%); color:#28a745; opacity:0.5; font-size:32px;">
                        <i class="fa fa-check-circle"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="small-box" style="background:#fff; border-left:4px solid #ffc107; border-radius:4px; box-shadow:0 2px 10px rgba(0,0,0,0.08); padding:15px; position:relative;">
                    <div class="inner">
                        <h3 style="margin:0; color:#ffc107;"><?php echo (int)($stats['nouvelle'] ?? 0); ?></h3>
                        <p style="margin:5px 0 0; color:#6c757d; font-size:14px;">Demandes en attente</p>
                    </div>
                    <div style="position:absolute; right:15px; top:50%; transform:translateY(-50%); color:#ffc107; opacity:0.5; font-size:32px;">
                        <i class="fa fa-clock-o"></i>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div class="small-box" style="background:#fff; border-left:4px solid #dc3545; border-radius:4px; box-shadow:0 2px 10px rgba(0,0,0,0.08); padding:15px; position:relative;">
                    <div class="inner">
                        <h3 style="margin:0; color:#dc3545;"><?php echo (int)($stats['refusée'] ?? 0); ?></h3>
                        <p style="margin:5px 0 0; color:#6c757d; font-size:14px;">Demandes refusées</p>
                    </div>
                    <div style="position:absolute; right:15px; top:50%; transform:translateY(-50%); color:#dc3545; opacity:0.5; font-size:32px;">
                        <i class="fa fa-times-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des demandes -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-list"></i> Liste des demandes</h3>
                <div class="box-tools pull-right">
                    <span class="badge bg-primary"><?php echo count($requests ?? []); ?></span>
                </div>
            </div>
            
            <div class="box-body table-responsive">
                <table class="table table-striped table-bordered table-hover" id="demoRequestsTable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Nom</th>
                            <th>Organisation</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Message</th>
                            <th>Statut</th>
                            <th style="min-width:180px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($requests)): ?>
                            <?php foreach ($requests as $row): ?>
                                <tr>
                                    <td><?php echo (int)$row['id']; ?></td>
                                    <td><?php echo !empty($row['created_at']) ? date('d/m/Y H:i', strtotime($row['created_at'])) : '-'; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($row['full_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['company'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <a href="mailto:<?php echo htmlspecialchars($row['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                            <i class="fa fa-envelope-o"></i> 
                                            <?php echo htmlspecialchars($row['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <i class="fa fa-phone"></i> 
                                        <?php echo htmlspecialchars($row['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    <td>
                                        <div style="max-width:200px; max-height:60px; overflow:auto;">
                                            <?php echo nl2br(htmlspecialchars($row['message'] ?? '', ENT_QUOTES, 'UTF-8')); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        $status = $row['status'] ?? 'nouvelle';
                                        $statusColors = [
                                            'nouvelle' => 'label-info',
                                            'acceptée' => 'label-success',
                                            'refusée' => 'label-danger'
                                        ];
                                        $statusIcons = [
                                            'nouvelle' => 'fa-clock-o',
                                            'acceptée' => 'fa-check-circle',
                                            'refusée' => 'fa-times-circle'
                                        ];
                                        $color = $statusColors[$status] ?? 'label-default';
                                        $icon = $statusIcons[$status] ?? 'fa-circle';
                                        ?>
                                        <span class="label <?php echo $color; ?>">
                                            <i class="fa <?php echo $icon; ?>"></i> 
                                            <?php echo ucfirst(htmlspecialchars($status, ENT_QUOTES, 'UTF-8')); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <!-- Boutons d'action -->
                                        <div style="display:flex; flex-wrap:wrap; gap:5px; align-items:center;">
                                            <button type="button" 
                                                    class="btn btn-success btn-xs" 
                                                    onclick="toggleReplyForm(<?php echo (int)$row['id']; ?>)">
                                                <i class="fa fa-reply"></i> Répondre
                                            </button>
                                            
                                            <a href="<?php echo site_url('admin/demorequests/delete/' . (int)$row['id']); ?>"
                                               class="btn btn-danger btn-xs"
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette demande ?');">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </div>
                                        
                                        <!-- Formulaire de réponse (caché par défaut) -->
                                        <div id="reply-form-<?php echo (int)$row['id']; ?>" 
                                             class="reply-form" 
                                             style="display:none; margin-top:12px;">
                                            <form action="<?php echo site_url('admin/demorequests/reply/' . (int)$row['id']); ?>" 
                                                  method="post" 
                                                  enctype="multipart/form-data" 
                                                  class="well well-sm" 
                                                  style="margin:0; background:#f9fbff; border:1px solid #dfe7f3; border-radius:8px; padding:15px;">
                                                
                                                <?php echo $this->customlib->getCSRF(); ?>
                                                
                                                <div class="form-group">
                                                    <label for="reply_subject_<?php echo (int)$row['id']; ?>">
                                                        <i class="fa fa-tag"></i> Objet
                                                    </label>
                                                    <input type="text" 
                                                           name="reply_subject" 
                                                           id="reply_subject_<?php echo (int)$row['id']; ?>" 
                                                           class="form-control input-sm" 
                                                           value="Réponse à votre demande de démonstration" 
                                                           required>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="status_<?php echo (int)$row['id']; ?>">
                                                        <i class="fa fa-flag"></i> Statut
                                                    </label>
                                                    <select name="status" 
                                                            id="status_<?php echo (int)$row['id']; ?>" 
                                                            class="form-control input-sm" 
                                                            required>
                                                        <option value="acceptée">✅ Acceptée</option>
                                                        <option value="refusée">❌ Refusée</option>
                                                        <option value="nouvelle">⏳ En attente</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label for="reply_message_<?php echo (int)$row['id']; ?>">
                                                        <i class="fa fa-envelope"></i> Message
                                                    </label>
                                                    <?php 
                                                    $defaultReply = "Bonjour " . htmlspecialchars($row['full_name'] ?? '', ENT_QUOTES, 'UTF-8') . ",\n\n";
                                                    $defaultReply .= "Nous avons bien reçu votre demande de démonstration transmise depuis notre site.\n";
                                                    $defaultReply .= "Merci pour l'intérêt porté à DIAGO. Nous vous répondrons dans les meilleurs délais au sujet de votre demande.\n\n";
                                                    $defaultReply .= "Cordialement,\nL'équipe DIAGO";
                                                    ?>
                                                    <textarea name="reply_message" 
                                                              id="reply_message_<?php echo (int)$row['id']; ?>" 
                                                              class="form-control input-sm" 
                                                              rows="5" 
                                                              required><?php echo $defaultReply; ?></textarea>
                                                </div>
                                                
                                                <div class="form-group" hidden>
                                                    <label for="attachment_<?php echo (int)$row['id']; ?>">
                                                        <i class="fa fa-paperclip"></i> Pièce jointe (optionnel)
                                                    </label>
                                                    <input type="file" 
                                                           name="attachment" 
                                                           id="attachment_<?php echo (int)$row['id']; ?>" 
                                                           class="form-control input-sm" 
                                                           accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.xlsx,.xls,.txt">
                                                    <small class="text-muted">Formats acceptés: PDF, DOC, DOCX, JPG, PNG, XLSX, TXT</small>
                                                </div>
                                                
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="fa fa-send"></i> Envoyer par email
                                                </button>
                                                
                                                <button type="button" 
                                                        class="btn btn-default btn-sm" 
                                                        onclick="toggleReplyForm(<?php echo (int)$row['id']; ?>)">
                                                    <i class="fa fa-times"></i> Annuler
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">
                                    <i class="fa fa-inbox" style="font-size:24px; display:block; margin-bottom:10px;"></i>
                                    Aucune demande de démonstration enregistrée.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (!empty($requests)): ?>
            <div class="box-footer clearfix">
                <div class="pull-right">
                    <small class="text-muted">
                        <i class="fa fa-file-text-o"></i> Total: <?php echo count($requests); ?> demande(s)
                    </small>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<!-- JavaScript -->
<script>
(function() {
    'use strict';
    
    // Auto-fermeture des messages flash après 5 secondes
    var alerts = document.querySelectorAll('.alert');
    if (alerts.length > 0) {
        setTimeout(function() {
            alerts.forEach(function(alert) {
                if (alert && alert.parentNode) {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        if (alert && alert.parentNode) {
                            alert.parentNode.removeChild(alert);
                        }
                    }, 500);
                }
            });
        }, 5000);
    }
    
    // Fonction pour basculer l'affichage du formulaire de réponse
    window.toggleReplyForm = function(id) {
        var form = document.getElementById('reply-form-' + id);
        if (!form) return;
        
        // Cacher tous les autres formulaires
        var allForms = document.querySelectorAll('.reply-form');
        allForms.forEach(function(f) {
            if (f.id !== 'reply-form-' + id) {
                f.style.display = 'none';
            }
        });
        
        // Basculer l'affichage du formulaire actuel
        if (form.style.display === 'none' || form.style.display === '') {
            form.style.display = 'block';
            // Scroll vers le formulaire
            setTimeout(function() {
                form.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100);
        } else {
            form.style.display = 'none';
        }
    };
    
    // Confirmation avant suppression
    document.querySelectorAll('.btn-danger').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            if (!confirm('Êtes-vous sûr de vouloir supprimer cette demande ? Cette action est irréversible.')) {
                e.preventDefault();
                return false;
            }
        });
    });
    
    // Validation du formulaire avant soumission
    document.querySelectorAll('form[action*="reply"]').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            var subject = this.querySelector('[name="reply_subject"]');
            var message = this.querySelector('[name="reply_message"]');
            
            if (subject && subject.value.trim() === '') {
                alert('Veuillez saisir un objet.');
                e.preventDefault();
                return false;
            }
            
            if (message && message.value.trim() === '') {
                alert('Veuillez saisir un message.');
                e.preventDefault();
                return false;
            }
            
            // Confirmation d'envoi
            return confirm('Confirmez-vous l\'envoi de cet email ?');
        });
    });
})();
</script>

<!-- CSS additionnel -->
<style>
/* Amélioration du tableau */
.table-striped > tbody > tr:nth-of-type(odd) {
    background-color: #fafbfc;
}

.table-hover > tbody > tr:hover {
    background-color: #f0f4ff;
}

/* Amélioration des cartes statistiques */
.small-box {
    position: relative;
    transition: all 0.3s ease;
    overflow: hidden;
}

.small-box:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.12) !important;
}

/* Style pour les labels de statut */
.label {
    padding: 5px 10px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.label i {
    margin-right: 3px;
}

/* Animation pour les formulaires */
.reply-form {
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Amélioration des icônes */
.fa {
    margin-right: 4px;
}

/* Responsive */
@media (max-width: 768px) {
    .small-box {
        margin-bottom: 15px;
    }
    
    .table-responsive {
        overflow-x: auto;
    }
    
    .reply-form {
        min-width: auto;
    }
}
</style>