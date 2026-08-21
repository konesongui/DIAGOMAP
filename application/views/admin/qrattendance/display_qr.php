<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pointage par QR Code</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ===== STYLES GÉNÉRAUX ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            min-height: 100vh;
        }

        .qr-container {
            max-width: 600px;
            width: 100%;
            background: #ffffff;
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        /* ===== EN-TÊTE ===== */
        .qr-header {
            background: linear-gradient(135deg, #1a2a6c 0%, #28669e 50%, #3b8fc2 100%);
            padding: 25px 25px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .qr-header .badge-entreprise {
            display: inline-block;
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 2px;
            padding: 3px 16px;
            border-radius: 20px;
            margin-bottom: 8px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .qr-header h1 {
            color: #ffffff;
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 3px;
        }

        .qr-header h1 i {
            margin-right: 10px;
            color: #ffd700;
        }

        .qr-header .subtitle {
            color: rgba(255, 255, 255, 0.85);
            font-size: 13px;
            font-weight: 300;
        }

        .qr-header .subtitle i {
            margin: 0 5px;
            font-size: 11px;
        }

        /* ===== CORPS ===== */
        .qr-body {
            padding: 30px 30px 25px;
            background: #ffffff;
            text-align: center;
        }

        /* ===== QR CODE ===== */
        .qr-code-wrapper {
            background: #f8faff;
            border-radius: 20px;
            padding: 25px;
            text-align: center;
            border: 2px dashed #e8edf5;
            position: relative;
        }

        .qr-code-wrapper .scan-me {
            position: absolute;
            top: -12px;
            right: 20px;
            background: linear-gradient(135deg, #28669e, #1a2a6c);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 5px 16px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(40, 102, 158, 0.3);
        }

        .qr-code-wrapper img {
            max-width: 350px;
            width: 100%;
            height: auto;
            border-radius: 12px;
            background: #ffffff;
            padding: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        .qr-code-wrapper .qr-label {
            margin-top: 12px;
            font-size: 13px;
            color: #6b7a8f;
            font-weight: 500;
        }

        .qr-code-wrapper .qr-label i {
            color: #28669e;
            margin-right: 6px;
        }

        /* ===== PIED DE PAGE ===== */
        .qr-footer {
            background: #f8faff;
            padding: 12px 25px;
            border-top: 1px solid #eef2f7;
            text-align: center;
        }

        .qr-footer .token-info {
            font-size: 11px;
            color: #8a9aa8;
            font-family: 'Courier New', monospace;
        }

        .qr-footer .token-info i {
            color: #28669e;
            margin-right: 6px;
        }

        /* ===== BOUTON D'IMPRESSION ===== */
        .no-print {
            text-align: center;
            padding: 15px 0 20px;
            background: #fff;
        }

        .no-print button {
            background: linear-gradient(135deg, #28669e, #1a2a6c);
            color: #fff;
            border: none;
            padding: 12px 40px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(40, 102, 158, 0.3);
            transition: all 0.3s ease;
        }

        .no-print button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(40, 102, 158, 0.4);
        }

        .no-print small {
            color: #999;
            font-size: 12px;
            display: block;
            margin-top: 8px;
        }

        /* ============================================================ */
        /* ===== STYLES D'IMPRESSION - QR CODE PLEIN ÉCRAN ===== */
        /* ============================================================ */
        @media print {
            /* Réinitialisation complète */
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            html, body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                min-height: 100vh !important;
                height: 100% !important;
                width: 100% !important;
                overflow: hidden !important;
            }

            /* Conteneur - occupe toute la page */
            .qr-container {
                max-width: 100% !important;
                width: 100% !important;
                height: 100vh !important;
                margin: 0 !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: center !important;
                align-items: center !important;
                padding: 15px !important;
                background: #ffffff !important;
            }

            /* ===== EN-TÊTE - RÉDUIT ===== */
            .qr-header {
                background: #1a2a6c !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
                padding: 15px 30px 12px !important;
                width: 100% !important;
                max-width: 800px !important;
                border-radius: 12px 12px 0 0 !important;
            }

            .qr-header .badge-entreprise {
                background: rgba(255, 255, 255, 0.15) !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
                border: 1px solid rgba(255, 255, 255, 0.2) !important;
                font-size: 11px !important;
                padding: 3px 16px !important;
                margin-bottom: 6px !important;
            }

            .qr-header h1 {
                color: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                font-size: 24px !important;
                margin-bottom: 2px !important;
            }

            .qr-header h1 i {
                color: #ffd700 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                font-size: 22px !important;
            }

            .qr-header .subtitle {
                color: rgba(255, 255, 255, 0.9) !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                font-size: 13px !important;
            }

            /* ===== CORPS - PLEIN ÉCRAN POUR LE QR CODE ===== */
            .qr-body {
                padding: 10px 30px 10px !important;
                background: #ffffff !important;
                width: 100% !important;
                max-width: 800px !important;
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                flex: 1 !important;
                min-height: 400px !important;
            }

            /* ===== QR CODE - TRÈS GRAND ===== */
            .qr-code-wrapper {
                border: 4px solid #28669e !important;
                background: #ffffff !important;
                padding: 20px !important;
                width: 100% !important;
                max-width: 550px !important;
                margin: 0 auto !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
                page-break-inside: avoid !important;
                border-radius: 16px !important;
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                justify-content: center !important;
                min-height: 400px !important;
            }

            .qr-code-wrapper .scan-me {
                background: #1a2a6c !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
                color: #ffffff !important;
                font-size: 14px !important;
                padding: 6px 22px !important;
                top: -14px !important;
                right: 25px !important;
                border-radius: 14px !important;
            }

            .qr-code-wrapper img {
                max-width: 450px !important;
                width: 90% !important;
                max-height: 450px !important;
                height: auto !important;
                padding: 20px !important;
                box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1) !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .qr-code-wrapper .qr-label {
                color: #6b7a8f !important;
                font-size: 14px !important;
                margin-top: 12px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .qr-code-wrapper .qr-label i {
                color: #28669e !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* ===== PIED DE PAGE - RÉDUIT ===== */
            .qr-footer {
                background: #f8faff !important;
                border-top: 2px solid #eef2f7 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
                padding: 10px 30px !important;
                width: 100% !important;
                max-width: 800px !important;
                border-radius: 0 0 12px 12px !important;
            }

            .qr-footer .token-info {
                font-size: 12px !important;
                color: #8a9aa8 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .qr-footer .token-info i {
                color: #28669e !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* ===== CACHER LE BOUTON D'IMPRESSION ===== */
            .no-print {
                display: none !important;
            }

            /* Éviter les coupures de page */
            .qr-header,
            .qr-body,
            .qr-code-wrapper,
            .qr-footer {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 500px) {
            .qr-header {
                padding: 20px 15px 15px;
            }

            .qr-header h1 {
                font-size: 20px;
            }

            .qr-body {
                padding: 20px 15px;
            }

            .qr-code-wrapper img {
                max-width: 220px;
                padding: 12px;
            }

            .qr-code-wrapper {
                padding: 18px;
            }
        }

        @media (max-width: 380px) {
            .qr-code-wrapper img {
                max-width: 180px;
                padding: 10px;
            }

            .qr-header h1 {
                font-size: 17px;
            }

            .qr-header .subtitle {
                font-size: 11px;
            }
        }
    </style>
</head>
<body>

    <div class="qr-container" id="qrContainer">

        <!-- ===== EN-TÊTE ===== -->
        <div class="qr-header">
            <span class="badge-entreprise">
                <i class="fas fa-building"></i> <?php echo $this->session->userdata('admin')['name'] ?? 'Entreprise'; ?>
            </span>
            <h1>
                <i class="fas fa-qrcode"></i> Pointage QR
            </h1>
            <p class="subtitle">
                <i class="fas fa-phone-alt"></i> Scannez avec votre mobile
                <i class="fas fa-arrow-right"></i>
                <i class="fas fa-check-circle" style="color: #ffd700;"></i> Validez votre présence
            </p>
        </div>

        <!-- ===== CORPS ===== -->
        <div class="qr-body">

            <!-- QR Code -->
            <div class="qr-code-wrapper">
                <span class="scan-me"><i class="fas fa-camera"></i> SCANNEZ-MOI</span>
                <?php
                $qr_size = 500;
                $qr_api_url = 'https://api.qrserver.com/v1/create-qr-code/?size=' . $qr_size . 'x' . $qr_size . '&data=' . urlencode($qr_url) . '&margin=20';
                ?>
                <img 
                    src="<?php echo $qr_api_url; ?>" 
                    alt="QR Code Présence" 
                    id="qrImage"
                    loading="lazy"
                >
                <div class="qr-label">
                    <i class="fas fa-sync-alt"></i> Code valable jusqu'à 23h59
                </div>
            </div>

        </div>

        <!-- ===== PIED DE PAGE ===== -->
        <div class="qr-footer">
            <span class="token-info">
                <i class="fas fa-key"></i> Token: <?php echo substr($token, 0, 8); ?>...<?php echo substr($token, -8); ?>
            </span>
        </div>

        <!-- ===== BOUTON D'IMPRESSION ===== -->
        <div class="no-print">
            <button onclick="window.print();">
                <i class="fas fa-print"></i> Imprimer le QR Code
            </button>
            <small>
                <i class="fas fa-info-circle"></i> Idéal pour afficher sur le mur de l'entreprise
            </small>
        </div>

    </div>

    <script>
        // ===== ACTUALISATION DU QR CODE TOUTES LES 5 MINUTES =====
        setInterval(function() {
            const img = document.getElementById('qrImage');
            if (img) {
                const timestamp = new Date().getTime();
                img.src = img.src.split('?')[0] + '?t=' + timestamp;
            }
        }, 300000);
    </script>

</body>
</html>