<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accès par code caisse</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .access-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            margin: 20px;
        }
        .access-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .access-header i {
            font-size: 70px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .code-input {
            text-align: center;
            font-family: monospace;
            font-size: 18px;
            letter-spacing: 2px;
        }
        .btn-access {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: bold;
        }
        .alert {
            margin-top: 15px;
        }
    </style>
</head>
<body>
<div class="access-card">
    <div class="access-header">
        <i class="fas fa-key"></i>
        <h2>Accès à votre caisse</h2>
        <p>Entrez le code d'accès qui vous a été fourni</p>
    </div>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i> <?php echo $this->session->flashdata('error'); ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo base_url('admin/income/access_by_code'); ?>" method="get">
        <div class="form-group">
            <label for="code">Code d'accès</label>
            <input type="text"
                   class="form-control code-input"
                   id="code"
                   name="code"
                   placeholder="CAISSE-XXX-YYYYMMDD-XXXXXX"
                   required
                   autofocus>
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-access">
            <i class="fas fa-unlock-alt"></i> Accéder à la caisse
        </button>
    </form>

    <div class="text-center mt-3">
        <small class="text-muted">
            <i class="fas fa-info-circle"></i>
            Contactez l'administrateur pour obtenir votre code
        </small>
    </div>
</div>
</body>
</html>