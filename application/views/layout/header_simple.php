<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : 'Accès Caisse'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }
        .navbar-simple {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 0;
            margin-bottom: 20px;
        }
        .navbar-simple a {
            color: white;
            text-decoration: none;
        }
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 15px;
        }
    </style>
</head>
<body>
<div class="navbar-simple">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h4><i class="fas fa-briefcase"></i> Gestion des Caisses</h4>
            </div>
            <div class="col-md-6 text-right">
                <?php if ($this->session->userdata('caisse_access_name')): ?>
                    <span class="mr-3">
                            <i class="fas fa-user-lock"></i> <?php echo htmlspecialchars($this->session->userdata('caisse_access_name')); ?>
                        </span>
                    <a href="<?php echo base_url('admin/income/logout_caisse_access'); ?>" class="btn btn-sm btn-light">
                        <i class="fas fa-sign-out-alt"></i> Quitter
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="container">