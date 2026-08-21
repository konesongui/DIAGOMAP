<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <title>DIAGO - Connexion</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="shortcut icon" href="<?php echo base_url(); ?>backend/usertemplate/assets/images/favicon.png" />
    <!-- Liens CSS -->
    <link href="<?php echo base_url(); ?>backend/usertemplate/asset/plugins/global/plugins.bundle.css" rel="stylesheet" />
    <link href="<?php echo base_url(); ?>backend/usertemplate/asset/css/style.bundle.css" rel="stylesheet" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        /* Reset & fond */
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Poppins', 'Inter', system-ui, sans-serif;
            overflow-x: hidden;
        }
        #bg-canvas {
            position: fixed;
            top:0; left:0;
            width:100%; height:100%;
            z-index:-2;
            background: linear-gradient(135deg, white 0%, white 50%, white 100%);
        }
        .overlay {
            position: fixed;
            top:0; left:0;
            width:100%; height:100%;
            background: radial-gradient(circle at 20% 30%, rgba(255,215,0,0.15), rgba(0,0,0,0.2));
            z-index:-1;
        }
        .login-container {
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:1.5rem;
        }
        .card-glass {
            background-color:#273772;
            backdrop-filter:blur(16px);
            border-radius:2rem;
            border:1px solid rgba(255,255,255,0.3);
            box-shadow:0 25px 50px -12px rgba(0,0,0,0.5);
            width:100%;
            max-width:434px;
            padding:2rem 2rem 2.5rem;
            transition:transform 0.3s ease;
        }
        .card-glass:hover { transform:translateY(-5px); }
        .logo-area { text-align:center; margin-bottom:1.5rem; }
        .logo-img { max-width:120px; height:auto; filter:drop-shadow(0 4px 8px rgba(0,0,0,0.2)); }
        .title {
            font-size:1.8rem;
            font-weight:700;
            text-align:center;
            color:#FFE6A7;
            letter-spacing:-0.5px;
            margin-bottom:0.5rem;
        }
        .subtitle {
            text-align:center;
            color:rgba(255,250,225,0.85);
            font-size:0.9rem;
            margin-bottom:2rem;
            border-bottom:1px dashed rgba(255,215,0,0.4);
            display:inline-block;
            width:auto;
            margin-left:auto;
            margin-right:auto;
            padding-bottom:0.5rem;
        }
        .input-group { margin-bottom:1.5rem; }
        .input-icon { position:relative; }
        .input-icon input {
            width:147%;
            padding:1rem 1rem 1rem 3rem;
            background:rgba(255,255,255,0.9);
            border:none;
            border-radius:12px;
            font-size:1rem;
            color:#1a2e2a;
            margin-left:23px;
            transition:all 0.2s;
            outline:none;
            font-weight:500;
        }
        .input-icon input:focus {
            background:white;
            box-shadow:0 0 0 3px rgba(212,175,55,0.5);
        }
        .input-icon i {
            position:absolute;
            left:1rem;
            top:50%;
            transform:translateY(-50%);
            font-size:1.2rem;
            margin-left:24px;
            color:#D4AF37;
        }
        .toggle-pwd {
            position:absolute;
            right:-8rem;
            top:50%;
            transform:translateY(-50%);
            cursor:pointer;
            font-size:1.2rem;
            user-select:none;
            opacity:0.7;
            transition:0.2s;
        }
        .toggle-pwd:hover { opacity:1; }
        .btn-login {
            background:linear-gradient(95deg, #D4AF37, #F9E79F);
            border:none;
            width:92%;
            padding:1rem;
            border-radius:12px;
            font-weight:700;
            font-size:1.1rem;
            color:#0B2F26;
            margin-left: 20px;
            cursor:pointer;
            transition:all 0.3s;
            box-shadow:0 8px 20px rgba(0,0,0,0.2);
            display:flex;
            align-items:center;
            justify-content:center;
            gap:10px;
        }
        .btn-login:hover {
            transform:scale(1.02);
            background:linear-gradient(95deg, #F9E79F, #D4AF37);
            box-shadow:0 10px 25px rgba(212,175,55,0.4);
        }
        .btn-login:disabled {
            opacity:0.7;
            cursor:not-allowed;
        }
        .help-text {
            text-align:center;
            margin-top:1.8rem;
            font-size:0.8rem;
            color:rgba(255,245,200,0.9);
        }
        .help-text a {
            color:#FFD966;
            text-decoration:none;
            font-weight:600;
        }
        .help-text a:hover { text-decoration:underline; }
        @keyframes shake {
            0% { transform:translateX(0); }
            25% { transform:translateX(-6px); }
            75% { transform:translateX(6px); }
            100% { transform:translateX(0); }
        }
        .shake { animation:shake 0.35s ease-in-out; }

        /* ----- TOAST NOTIFICATION ----- */
        #toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 380px;
            width: 100%;
            pointer-events: none;
        }
        .toast {
            background: #1e293b;
            color: #fff;
            padding: 16px 20px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateX(120%);
            opacity: 0;
            transition: transform 0.4s ease, opacity 0.4s ease;
            pointer-events: auto;
            border-left: 6px solid #4caf50;
        }
        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }
        .toast.success { border-left-color: #4caf50; }
        .toast.error   { border-left-color: #e63946; }
        .toast .toast-icon {
            font-size: 1.6rem;
            flex-shrink: 0;
        }
        .toast .toast-message {
            flex: 1;
            font-weight: 500;
            font-size: 0.95rem;
        }
        .toast .toast-close {
            background: none;
            border: none;
            color: rgba(255,255,255,0.6);
            font-size: 1.2rem;
            cursor: pointer;
            transition: color 0.2s;
            padding: 0 4px;
        }
        .toast .toast-close:hover { color: #fff; }

        @media (max-width: 500px) {
            .card-glass { padding:1.5rem; }
            .title { font-size:1.5rem; }
            #toast-container { right:10px; left:10px; max-width:100%; }
        }
    </style>
</head>
<body>
<canvas id="bg-canvas"></canvas>
<div class="overlay"></div>

<!-- Conteneur des toasts -->
<div id="toast-container"></div>

<div class="login-container">
    <div class="card-glass">
        <div class="logo-area">
            <img src="<?php echo base_url(); ?>uploads/front_office/logo/logos.png" class="logo-img" alt="Logo Eglise" />
        </div>
        <h1 class="title">Bienvenue</h1>
        <div style="text-align:center;">
            <span class="subtitle">Espace de gestion DIAGO</span>
        </div>

        <!-- Formulaire avec méthode POST, intercepté en AJAX -->
        <form id="loginForm" method="post" action="<?php echo site_url('site/login') ?>">
            <div class="input-group">
                <div class="input-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="text" name="username" placeholder="Email d'utilisateur" required autocomplete="username" />
                </div>
            </div>
            <div class="input-group">
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="password" placeholder="Mot de passe" required autocomplete="current-password" />
                    <span class="toggle-pwd" onclick="togglePassword()">👁️</span>
                </div>
            </div>
            <button type="submit" class="btn-login" id="btnLogin">
                <span>Se connecter</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>
        <div class="help-text">
            🔐 Accès sécurisé — <a href="<?php echo site_url('site/forgotpassword') ?>">Mot de passe oublié ?</a>
        </div>
    </div>
</div>

<!-- ==================== SCRIPTS ==================== -->

<!-- Particules -->
<script>
    const canvas = document.getElementById("bg-canvas");
    const ctx = canvas.getContext("2d");
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
    let particlesArray = [];
    class Particle {
        constructor(){
            this.x = Math.random() * canvas.width;
            this.y = Math.random() * canvas.height;
            this.size = Math.random() * 3 + 1;
            this.speedX = (Math.random() * 0.8) - 0.4;
            this.speedY = (Math.random() * 0.8) - 0.4;
        }
        update(){
            this.x += this.speedX;
            this.y += this.speedY;
            if(this.x < 0 || this.x > canvas.width) this.speedX *= -1;
            if(this.y < 0 || this.y > canvas.height) this.speedY *= -1;
        }
        draw() {
            const gradient = ctx.createLinearGradient(
                this.x - this.size, this.y,
                this.x + this.size, this.y
            );
            gradient.addColorStop(0, "rgb(39,55,114)");
            gradient.addColorStop(1, "rgb(255,193,7)");
            ctx.fillStyle = gradient;
            ctx.beginPath();
            ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
            ctx.fill();
        }
    }
    function connectLines(){
        for(let a=0; a<particlesArray.length; a++){
            for(let b=a+1; b<particlesArray.length; b++){
                let dx = particlesArray[a].x - particlesArray[b].x;
                let dy = particlesArray[a].y - particlesArray[b].y;
                let dist = dx*dx + dy*dy;
                let limit = (canvas.width/12) * (canvas.height/12);
                if(dist < limit){
                    ctx.strokeStyle = "rgba(212,175,55,0.15)";
                    ctx.lineWidth = 0.8;
                    ctx.beginPath();
                    ctx.moveTo(particlesArray[a].x, particlesArray[a].y);
                    ctx.lineTo(particlesArray[b].x, particlesArray[b].y);
                    ctx.stroke();
                }
            }
        }
    }
    function initParticles(){
        particlesArray = [];
        for(let i=0; i<90; i++){ particlesArray.push(new Particle()); }
    }
    function animateParticles(){
        ctx.clearRect(0,0,canvas.width,canvas.height);
        for(let i=0; i<particlesArray.length; i++){
            particlesArray[i].update();
            particlesArray[i].draw();
        }
        connectLines();
        requestAnimationFrame(animateParticles);
    }
    initParticles();
    animateParticles();
    window.addEventListener("resize", ()=>{
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        initParticles();
    });
</script>

<!-- Toggle mot de passe -->
<script>
    function togglePassword() {
        const pwd = document.getElementById("password");
        const type = pwd.getAttribute("type") === "password" ? "text" : "password";
        pwd.setAttribute("type", type);
    }
</script>

<!-- TOAST -->
<script>
    function showToast(message, type = 'success', duration = 4000) {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;

        const iconMap = {
            success: '✅',
            error: '❌',
            info: 'ℹ️'
        };
        const icon = iconMap[type] || 'ℹ️';

        toast.innerHTML = `
            <span class="toast-icon">${icon}</span>
            <span class="toast-message">${message}</span>
            <button class="toast-close">&times;</button>
        `;

        container.appendChild(toast);

        requestAnimationFrame(() => {
            toast.classList.add('show');
        });

        toast.querySelector('.toast-close').addEventListener('click', () => {
            closeToast(toast);
        });

        const timer = setTimeout(() => {
            closeToast(toast);
        }, duration);

        toast._timer = timer;
    }

    function closeToast(toast) {
        if (!toast) return;
        toast.classList.remove('show');
        clearTimeout(toast._timer);
        setTimeout(() => {
            if (toast.parentNode) toast.parentNode.removeChild(toast);
        }, 400);
    }
</script>

<!-- Gestion AJAX du formulaire (sans son) -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('loginForm');
        const btn = document.getElementById('btnLogin');

        <?php if (!empty($error_message)): ?>
        showToast('<?php echo addslashes($error_message); ?>', 'error', 5000);
        btn.disabled = false;
        btn.innerHTML = '<span>Se connecter</span> <i class="fas fa-arrow-right"></i>';
        <?php endif; ?>

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            btn.disabled = true;
            btn.innerHTML = '<span>⏳ Connexion...</span> <i class="fas fa-spinner fa-pulse"></i>';

            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });

                let data;
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    data = await response.json();
                } else {
                    if (response.redirected) {
                        data = { success: true, redirect: response.url };
                    } else {
                        const text = await response.text();
                        throw new Error('Réponse non JSON. Statut: ' + response.status);
                    }
                }

                if (data.success) {
                    // Succès
                    btn.innerHTML = '<span>✅ Connecté</span> <i class="fas fa-check-circle"></i>';
                    btn.style.background = 'linear-gradient(95deg, #2b7a4b, #4caf50)';
                    btn.style.boxShadow = '0 0 20px #4caf50';

                    showToast('Connexion réussie avec succès !', 'success', 4000);

                    setTimeout(() => {
                        window.location.href = data.redirect || '<?php echo site_url('dashboard'); ?>';
                    }, 2000);
                } else {
                    // Échec
                    btn.innerHTML = '<span>❌ Échec</span> <i class="fas fa-times-circle"></i>';
                    btn.style.background = 'linear-gradient(95deg, #b91c1c, #e63946)';
                    btn.style.boxShadow = '0 0 20px #e63946';
                    btn.classList.add('shake');
                    setTimeout(() => btn.classList.remove('shake'), 400);

                    const msg = data.message || 'Échec de l\'authentification. Vérifiez vos accès.';
                    showToast(msg, 'error', 5000);
                    btn.disabled = false;
                    btn.innerHTML = '<span>Se connecter</span> <i class="fas fa-arrow-right"></i>';
                }

            } catch (error) {
                console.error('Erreur:', error);
                btn.innerHTML = '<span>❌ Échec</span> <i class="fas fa-times-circle"></i>';
                btn.style.background = 'linear-gradient(95deg, #b91c1c, #e63946)';
                btn.style.boxShadow = '0 0 20px #e63946';
                btn.classList.add('shake');
                setTimeout(() => btn.classList.remove('shake'), 400);

                showToast('Erreur réseau ou serveur. Veuillez réessayer.', 'error', 5000);
                btn.disabled = false;
                btn.innerHTML = '<span>Se connecter</span> <i class="fas fa-arrow-right"></i>';
            }
        });
    });
</script>

</body>
</html>