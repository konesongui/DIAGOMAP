<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($document_title, ENT_QUOTES, 'UTF-8'); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 24px;
            background: linear-gradient(180deg, #eef4ff 0%, #f5f7fb 100%);
            color: #1b2430;
        }

        .popup-shell {
            max-width: 980px;
            margin: 0 auto;
        }

        .popup-toolbar {
            background: linear-gradient(135deg, #1b4f80 0%, #2d6ea8 100%);
            color: #fff;
            border-radius: 14px 14px 0 0;
            padding: 14px 18px;
            box-shadow: 0 12px 28px rgba(27, 79, 128, 0.18);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .popup-toolbar .title {
            font-weight: 700;
            letter-spacing: 0.5px;
            font-size: 15px;
        }

        .toolbar-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            display: inline-block;
            padding: 8px 18px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            font-size: 13px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #fff;
            color: #1b4f80;
        }

        .btn-primary:hover {
            background: #f0f0f0;
            transform: translateY(-1px);
        }

        .btn-default {
            background: rgba(255,255,255,0.12);
            color: #fff;
            border: 1px solid rgba(255,255,255,0.25);
        }

        .btn-default:hover {
            background: rgba(255,255,255,0.2);
        }

        .doc {
            max-width: 850px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #dfe7f3;
            border-radius: 0 0 16px 16px;
            box-shadow: 0 18px 42px rgba(15, 23, 42, 0.08);
            padding: 38px 42px;
        }

        .header {
            border-bottom: 2px solid #1b4f80;
            padding-bottom: 14px;
            margin-bottom: 28px;
        }

        .company {
            font-size: 12px;
            color: #56708d;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .doc-code {
            margin-top: 12px;
            background: #eef4ff;
            border: 1px solid #dfe7f3;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 14px;
            color: #1b4f80;
            display: inline-block;
        }

        h1 {
            margin: 8px 0 0;
            color: #1b4f80;
            font-size: 28px;
        }

        .content {
            line-height: 2;
            font-size: 15px;
        }

        .signature {
            margin-top: 40px;
            padding-top: 18px;
            border-top: 1px solid #dfe7f3;
            font-size: 14px;
        }

        .signature .name {
            margin-top: 8px;
            font-weight: bold;
            color: #1b4f80;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .popup-toolbar {
                display: none !important;
            }

            .doc {
                box-shadow: none;
                border: none;
                border-radius: 0;
                max-width: 100%;
                padding: 30px;
                margin: 0;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 12px;
            }
            
            .doc {
                padding: 20px;
            }
            
            .popup-toolbar {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }
            
            .toolbar-actions {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="popup-shell">
        <div class="popup-toolbar">
            <div class="title">
                <i class="fa fa-file-text-o"></i> <?php echo htmlspecialchars($document_title, ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <div class="toolbar-actions">
                <button class="btn btn-primary" onclick="window.print();">
                    <i class="fa fa-print"></i> Imprimer
                </button>
                <button class="btn btn-default" onclick="window.close();">
                    <i class="fa fa-times"></i> Fermer
                </button>
            </div>
        </div>

        <div class="doc">
            <?php echo $document_html; ?>
        </div>
    </div>

    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <script>
        // Fonction pour fermer la fenêtre avec confirmation
        function closeWindow() {
            if (confirm('Voulez-vous vraiment fermer cette fenêtre ?')) {
                window.close();
            }
        }   
        
        // Raccourci clavier pour imprimer
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                // Laisser le comportement par défaut
                return true;
            }
        });
    </script>
</body>
</html>