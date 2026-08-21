<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Pointage QR - Scan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a2a6c 0%, #28669e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .scan-container {
            max-width: 450px;
            width: 100%;
            background: #ffffff;
            border-radius: 24px;
            padding: 35px 30px 30px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        .scan-header {
            text-align: center;
            margin-bottom: 25px;
        }
        
        .scan-header .icon {
            font-size: 50px;
            color: #28669e;
            margin-bottom: 8px;
        }
        
        .scan-header h1 {
            color: #1a2a6c;
            font-size: 22px;
            font-weight: 700;
        }
        
        .scan-header p {
            color: #6b7a8f;
            font-size: 13px;
            margin-top: 4px;
        }
        
        .form-group {
            margin-bottom: 18px;
        }
        
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #1a2a6c;
            margin-bottom: 6px;
        }
        
        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e8edf5;
            border-radius: 12px;
            font-size: 16px;
            transition: border 0.3s ease;
            background: #f8faff;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #28669e;
            background: #ffffff;
        }
        
        /* ===== PHOTO SECTION ===== */
        .photo-section {
            margin-bottom: 20px;
        }
        
        .photo-section label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #1a2a6c;
            margin-bottom: 8px;
        }
        
        .photo-wrapper {
            position: relative;
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
            border-radius: 16px;
            overflow: hidden;
            border: 2px dashed #e8edf5;
            background: #f8faff;
            aspect-ratio: 4/3;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .photo-wrapper:hover {
            border-color: #28669e;
            background: #f0f5ff;
        }
        
        .photo-wrapper video,
        .photo-wrapper canvas {
            display: none;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .photo-wrapper video.active,
        .photo-wrapper canvas.active {
            display: block;
        }
        
        .photo-wrapper .placeholder {
            text-align: center;
            color: #8a9aa8;
            padding: 20px;
        }
        
        .photo-wrapper .placeholder i {
            font-size: 40px;
            color: #28669e;
            display: block;
            margin-bottom: 10px;
        }
        
        .photo-wrapper .placeholder span {
            font-size: 14px;
        }
        
        .photo-actions {
            display: flex;
            gap: 10px;
            margin-top: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn-photo {
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-photo i {
            font-size: 16px;
        }
        
        .btn-camera {
            background: #28669e;
            color: #fff;
        }
        
        .btn-camera:hover {
            background: #1a2a6c;
            transform: translateY(-2px);
        }
        
        .btn-capture {
            background: #28a745;
            color: #fff;
        }
        
        .btn-capture:hover {
            background: #1e7e34;
            transform: translateY(-2px);
        }
        
        .btn-retake {
            background: #ffc107;
            color: #1a2a6c;
        }
        
        .btn-retake:hover {
            background: #e0a800;
            transform: translateY(-2px);
        }
        
        .btn-photo:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }
        
        .photo-status {
            text-align: center;
            font-size: 12px;
            color: #8a9aa8;
            margin-top: 6px;
        }
        
        .photo-status i {
            color: #28a745;
        }
        
        .btn-submit {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #28669e, #1a2a6c);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(40, 102, 158, 0.3);
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(40, 102, 158, 0.4);
        }
        
        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        #message {
            margin-top: 20px;
            padding: 14px;
            border-radius: 12px;
            text-align: center;
            display: none;
            font-weight: 500;
        }
        
        #message.success {
            display: block;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        #message.error {
            display: block;
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 10px;
        }
        
        .loading.active {
            display: block;
        }
        
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #28669e;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .footer-note {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #8a9aa8;
        }
        
        .footer-note i {
            color: #28669e;
        }
        
        /* Message caméra non disponible */
        .camera-error {
            display: none;
            background: #fff3cd;
            color: #856404;
            padding: 12px;
            border-radius: 10px;
            text-align: center;
            font-size: 13px;
            margin-top: 10px;
            border: 1px solid #ffc107;
        }
        
        .camera-error.active {
            display: block;
        }
    </style>
</head>
<body>

    <div class="scan-container">
        <div class="scan-header">
            <div class="icon"><i class="fas fa-qrcode"></i></div>
            <h1>Pointage QR</h1>
            <p>Matricule + Photo pour valider votre présence</p>
        </div>

        <form id="scanForm" onsubmit="return false;">
            <div class="form-group">
                <label for="employee_id"><i class="fas fa-id-card"></i> Matricule / Identifiant</label>
                <input 
                    type="text" 
                    id="employee_id" 
                    name="employee_id" 
                    placeholder="Ex: 9000 ou EMP001"
                    autocomplete="off"
                    required
                    autofocus
                >
            </div>
            
            <!-- SECTION PHOTO -->
            <div class="photo-section">
                <label><i class="fas fa-camera"></i> Photo de vérification</label>
                <div class="photo-wrapper" id="photoWrapper">
                    <video id="video" autoplay playsinline class="active"></video>
                    <canvas id="canvas"></canvas>
                    <div class="placeholder" id="placeholder">
                        <i class="fas fa-camera"></i>
                        <span>Cliquez pour activer la caméra</span>
                    </div>
                </div>
                
                <div class="photo-actions">
                    <button type="button" class="btn-photo btn-camera" id="btnCamera">
                        <i class="fas fa-video"></i> Caméra
                    </button>
                    <button type="button" class="btn-photo btn-capture" id="btnCapture" disabled>
                        <i class="fas fa-camera"></i> Prendre
                    </button>
                    <button type="button" class="btn-photo btn-retake" id="btnRetake" style="display:none;">
                        <i class="fas fa-redo"></i> Refaire
                    </button>
                </div>
                
                <div class="photo-status" id="photoStatus">
                    <i class="fas fa-info-circle"></i> Activez la caméra pour prendre une photo
                </div>
                
                <div class="camera-error" id="cameraError">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Caméra non accessible. Vérifiez les permissions.
                </div>
            </div>
            
            <input type="hidden" id="token" name="token" value="<?php echo html_escape($token); ?>">
            <input type="hidden" id="photo_data" name="photo_data" value="">
            
            <button type="submit" class="btn-submit" id="submitBtn" disabled>
                <i class="fas fa-check-circle"></i> Valider ma présence
            </button>
            
            <div class="loading" id="loading">
                <div class="spinner"></div>
                <p style="margin-top: 10px; color: #6b7a8f;">Enregistrement en cours...</p>
            </div>
        </form>

        <div id="message"></div>

        <div class="footer-note">
            <i class="fas fa-shield-alt"></i> 
            <?php echo date('d/m/Y H:i'); ?> - Photo obligatoire
        </div>
    </div>

    <script>
        // ===== GESTION DE LA CAMÉRA =====
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const placeholder = document.getElementById('placeholder');
        const photoWrapper = document.getElementById('photoWrapper');
        const btnCamera = document.getElementById('btnCamera');
        const btnCapture = document.getElementById('btnCapture');
        const btnRetake = document.getElementById('btnRetake');
        const photoStatus = document.getElementById('photoStatus');
        const cameraError = document.getElementById('cameraError');
        const photoDataInput = document.getElementById('photo_data');
        const submitBtn = document.getElementById('submitBtn');
        const employeeIdInput = document.getElementById('employee_id');

        let stream = null;
        let photoTaken = false;
        let cameraActive = false;

        // Activer la caméra
        async function startCamera() {
            try {
                // Arrêter la caméra si déjà active
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }
                
                stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'user',
                        width: { ideal: 640 },
                        height: { ideal: 480 }
                    },
                    audio: false
                });
                
                video.srcObject = stream;
                await video.play();
                
                cameraActive = true;
                photoTaken = false;
                btnCapture.disabled = false;
                btnCamera.innerHTML = '<i class="fas fa-video"></i> Caméra ON';
                btnCamera.style.background = '#28a745';
                photoStatus.innerHTML = '<i class="fas fa-check-circle" style="color: #28a745;"></i> Caméra active - Prêt à prendre la photo';
                cameraError.classList.remove('active');
                placeholder.style.display = 'none';
                video.classList.add('active');
                canvas.classList.remove('active');
                btnRetake.style.display = 'none';
                photoDataInput.value = '';
                submitBtn.disabled = true;
                
            } catch (error) {
                console.error('Erreur caméra:', error);
                cameraActive = false;
                btnCapture.disabled = true;
                btnCamera.innerHTML = '<i class="fas fa-video"></i> Réessayer';
                btnCamera.style.background = '#28669e';
                cameraError.classList.add('active');
                photoStatus.innerHTML = '<i class="fas fa-exclamation-circle" style="color: #dc3545;"></i> Caméra non disponible';
                placeholder.style.display = 'block';
                video.classList.remove('active');
            }
        }

        // Prendre la photo
        function takePhoto() {
            if (!cameraActive || !stream) {
                photoStatus.innerHTML = '<i class="fas fa-exclamation-circle" style="color: #dc3545;"></i> Activez d\'abord la caméra';
                return;
            }

            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            // Convertir en base64
            const photoData = canvas.toDataURL('image/jpeg', 0.9);
            photoDataInput.value = photoData;
            photoTaken = true;
            
            // Afficher la photo
            video.classList.remove('active');
            canvas.classList.add('active');
            btnCapture.disabled = true;
            btnRetake.style.display = 'inline-flex';
            btnCamera.disabled = true;
            btnCamera.innerHTML = '<i class="fas fa-check"></i> Photo prise';
            btnCamera.style.background = '#28a745';
            photoStatus.innerHTML = '<i class="fas fa-check-circle" style="color: #28a745;"></i> Photo prise avec succès !';
            submitBtn.disabled = false;
            
            // Arrêter la caméra pour économiser les ressources
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
        }

        // Refaire la photo
        function retakePhoto() {
            photoTaken = false;
            photoDataInput.value = '';
            canvas.classList.remove('active');
            video.classList.remove('active');
            btnRetake.style.display = 'none';
            btnCapture.disabled = true;
            btnCamera.disabled = false;
            btnCamera.innerHTML = '<i class="fas fa-video"></i> Caméra';
            btnCamera.style.background = '#28669e';
            submitBtn.disabled = true;
            photoStatus.innerHTML = '<i class="fas fa-info-circle"></i> Cliquez sur "Caméra" pour recommencer';
            placeholder.style.display = 'block';
            cameraActive = false;
        }

        // Événements
        btnCamera.addEventListener('click', startCamera);
        btnCapture.addEventListener('click', takePhoto);
        btnRetake.addEventListener('click', retakePhoto);

        // Cliquer sur le wrapper pour activer la caméra
        photoWrapper.addEventListener('click', function() {
            if (!cameraActive && !photoTaken) {
                startCamera();
            }
        });

        // Vérifier si la caméra est supportée
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            cameraError.classList.add('active');
            cameraError.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Votre navigateur ne supporte pas la caméra. Utilisez Chrome ou Safari.';
            btnCamera.disabled = true;
            btnCapture.disabled = true;
        }

        // ===== SOUMISSION DU FORMULAIRE =====
        document.getElementById('scanForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const employeeId = employeeIdInput.value.trim();
            const token = document.getElementById('token').value;
            const photoData = photoDataInput.value;
            const messageDiv = document.getElementById('message');
            const loadingDiv = document.getElementById('loading');
            
            if (!employeeId) {
                messageDiv.className = 'error';
                messageDiv.textContent = '⚠️ Veuillez entrer votre matricule.';
                return;
            }
            
            if (!photoData) {
                messageDiv.className = 'error';
                messageDiv.textContent = '⚠️ Veuillez prendre une photo.';
                return;
            }
            
            // Désactiver le bouton
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> En cours...';
            loadingDiv.classList.add('active');
            messageDiv.className = '';
            messageDiv.textContent = '';
            
            // Envoyer la requête
            fetch('<?php echo base_url("admin/qrattendance/process_scan"); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'employee_id=' + encodeURIComponent(employeeId) + 
                      '&token=' + encodeURIComponent(token) +
                      '&photo_data=' + encodeURIComponent(photoData)
            })
            .then(response => response.json())
            .then(data => {
                loadingDiv.classList.remove('active');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> Valider ma présence';
                
                if (data.success) {
                    messageDiv.className = 'success';
                    messageDiv.innerHTML = '✅ ' + data.message;
                    // Réinitialiser
                    employeeIdInput.value = '';
                    employeeIdInput.focus();
                    retakePhoto();
                } else {
                    messageDiv.className = 'error';
                    messageDiv.textContent = '❌ ' + data.message;
                    // Réactiver la caméra pour un nouvel essai
                    if (data.message.includes('photo')) {
                        setTimeout(() => {
                            startCamera();
                        }, 1500);
                    }
                }
            })
            .catch(error => {
                loadingDiv.classList.remove('active');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> Valider ma présence';
                messageDiv.className = 'error';
                messageDiv.textContent = '❌ Erreur de connexion. Veuillez réessayer.';
                console.error('Erreur:', error);
            });
        });

        // Soumettre avec Entrée
        employeeIdInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !submitBtn.disabled) {
                document.getElementById('scanForm').dispatchEvent(new Event('submit'));
            }
        });

        // Focus automatique
        employeeIdInput.focus();

        // Démarrer la caméra automatiquement au chargement
        setTimeout(startCamera, 500);
    </script>

</body>
</html>