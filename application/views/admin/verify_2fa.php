<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>Vérification 2FA - DIAGO</title>
    <link href="<?php echo base_url(); ?>backend/usertemplate/asset/plugins/global/plugins.bundle.css" rel="stylesheet" />
</head>
<body class="d-flex align-items-center justify-content-center bg-light">
<div class="card w-400px">
    <div class="card-body text-center">
        <h3>Vérification en deux étapes</h3>
        <p>Un code à 6 chiffres vous a été envoyé par email.</p>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="text" name="code" class="form-control form-control-lg text-center mb-3" placeholder="000000" maxlength="6" autofocus>
            <button type="submit" class="btn btn-primary w-100">Vérifier</button>
        </form>
        <a href="<?php echo site_url('site/login'); ?>" class="btn btn-link mt-3">Annuler</a>
    </div>
</div>
</body>
</html>