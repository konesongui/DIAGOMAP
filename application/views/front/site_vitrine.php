<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo isset($school['name']) ? htmlspecialchars($school['name']) : 'Diagoma'; ?> | Site vitrine</title>
    <style>
        :root {
            --ink: #0d1b2a;
            --muted: #415a77;
            --paper: #f7f9fb;
            --card: #ffffff;
            --accent: #e76f51;
            --accent-2: #2a9d8f;
            --line: #dbe4ea;
            --shadow: 0 20px 60px rgba(13, 27, 42, 0.12);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Trebuchet MS", "Segoe UI", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(1200px 500px at -10% -10%, rgba(42, 157, 143, 0.18), transparent 60%),
                radial-gradient(800px 400px at 100% 10%, rgba(231, 111, 81, 0.18), transparent 60%),
                linear-gradient(180deg, #ffffff 0%, var(--paper) 100%);
            min-height: 100vh;
        }

        .container {
            width: min(1120px, 92vw);
            margin: 0 auto;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 0;
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(247, 249, 251, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid transparent;
        }

        .logo {
            font-size: 1.3rem;
            font-weight: 700;
            letter-spacing: 0.4px;
        }

        .logo span {
            color: var(--accent);
        }

        .top-actions a {
            text-decoration: none;
            color: var(--ink);
            padding: 10px 16px;
            border-radius: 999px;
            border: 1px solid var(--line);
            transition: all .2s ease;
            margin-left: 8px;
            display: inline-block;
            background: rgba(255, 255, 255, 0.8);
        }

        .top-actions a:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(13, 27, 42, 0.08);
        }

        .top-actions a.primary {
            background: var(--ink);
            border-color: var(--ink);
            color: #fff;
        }

        .hero {
            padding: 36px 0 30px;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 28px;
            align-items: stretch;
        }

        .hero-copy {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 26px;
            padding: 38px;
            box-shadow: var(--shadow);
            animation: rise .65s ease-out;
        }

        .chip {
            display: inline-block;
            background: rgba(42, 157, 143, 0.12);
            color: #1e7f75;
            border: 1px solid rgba(42, 157, 143, 0.35);
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 16px;
        }

        h1 {
            margin: 0;
            font-size: clamp(2rem, 4vw, 3.4rem);
            line-height: 1.1;
        }

        .lead {
            color: var(--muted);
            font-size: 1.06rem;
            margin-top: 16px;
            max-width: 54ch;
        }

        .hero-cta {
            margin-top: 22px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            border: none;
            border-radius: 12px;
            padding: 12px 18px;
            text-decoration: none;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-main {
            background: var(--accent);
            color: #fff;
        }

        .btn-ghost {
            background: #fff;
            color: var(--ink);
            border: 1px solid var(--line);
        }

        .hero-panel {
            background: linear-gradient(150deg, #0d1b2a 0%, #16324f 45%, #1f4061 100%);
            color: #fff;
            border-radius: 26px;
            padding: 28px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow);
            animation: rise .8s ease-out;
        }

        .hero-panel:before,
        .hero-panel:after {
            content: "";
            position: absolute;
            border-radius: 999px;
            filter: blur(2px);
        }

        .hero-panel:before {
            width: 180px;
            height: 180px;
            right: -45px;
            top: -45px;
            background: rgba(231, 111, 81, 0.45);
        }

        .hero-panel:after {
            width: 210px;
            height: 210px;
            left: -80px;
            bottom: -70px;
            background: rgba(42, 157, 143, 0.35);
        }

        .metric {
            position: relative;
            z-index: 1;
            margin-bottom: 14px;
            padding: 12px 14px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(2px);
        }

        .metric strong {
            display: block;
            font-size: 1.6rem;
            line-height: 1;
        }

        .services {
            padding: 22px 0 64px;
        }

        .services h2 {
            margin-bottom: 16px;
            font-size: clamp(1.4rem, 2.8vw, 2rem);
        }

        .modules {
            padding: 8px 0 64px;
        }

        .forms-section {
            padding: 6px 0 58px;
        }

        .forms-wrap {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 16px;
        }

        .form-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 18px;
            box-shadow: 0 8px 20px rgba(13, 27, 42, 0.06);
            padding: 20px;
        }

        .form-card h3 {
            margin: 0 0 6px;
        }

        .form-card p {
            margin: 0 0 12px;
            color: var(--muted);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .field {
            margin-bottom: 10px;
        }

        .field.full {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            font-size: 0.87rem;
            margin-bottom: 4px;
            color: #20304b;
            font-weight: 600;
        }

        input,
        textarea {
            width: 100%;
            border: 1px solid #cfdbe6;
            border-radius: 10px;
            padding: 11px 12px;
            font: inherit;
            color: var(--ink);
            background: #fff;
        }

        input:focus,
        textarea:focus {
            border-color: #2a9d8f;
            outline: none;
            box-shadow: 0 0 0 3px rgba(42, 157, 143, 0.15);
        }

        .btn-submit {
            background: var(--ink);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 11px 16px;
            font-weight: 700;
            cursor: pointer;
        }

        .flash-message {
            margin: 0 0 14px;
            border-radius: 10px;
            padding: 10px 12px;
            font-weight: 600;
        }

        .flash-success {
            background: #e7f8ef;
            border: 1px solid #9be0bb;
            color: #1d7a49;
        }

        .flash-error {
            background: #fff1f1;
            border: 1px solid #ffc9c9;
            color: #9c2929;
        }

        .modules h2 {
            margin-bottom: 8px;
            font-size: clamp(1.4rem, 2.8vw, 2rem);
        }

        .modules .intro {
            margin: 0 0 18px;
            color: var(--muted);
            max-width: 78ch;
        }

        .module-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .module-card {
            background: linear-gradient(170deg, #ffffff 0%, #f9fcff 100%);
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 8px 20px rgba(13, 27, 42, 0.06);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .module-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 30px rgba(13, 27, 42, 0.12);
        }

        .module-badge {
            display: inline-block;
            margin-bottom: 8px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: .2px;
            color: #123;
            background: rgba(42, 157, 143, 0.14);
            border: 1px solid rgba(42, 157, 143, 0.28);
        }

        .module-card h3 {
            margin: 0 0 6px;
            font-size: 1.02rem;
        }

        .module-card p {
            margin: 0;
            color: var(--muted);
            line-height: 1.5;
            font-size: 0.93rem;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 20px;
            box-shadow: 0 8px 20px rgba(13, 27, 42, 0.06);
            transform: translateY(14px);
            opacity: 0;
            animation: reveal .6s ease forwards;
        }

        .card:nth-child(2) { animation-delay: .12s; }
        .card:nth-child(3) { animation-delay: .24s; }

        .card h3 {
            margin: 0 0 8px;
        }

        .card p {
            margin: 0;
            color: var(--muted);
            line-height: 1.55;
        }

        html {
            scroll-behavior: smooth;
        }

        .nav {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .nav a {
            color: var(--muted);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .nav a:hover {
            color: var(--ink);
        }

        .trust-bar {
            padding: 16px 0 10px;
        }

        .trust-strip {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px 18px;
            padding: 18px 22px;
            border: 1px solid var(--line);
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.7);
            box-shadow: 0 10px 25px rgba(13, 27, 42, 0.04);
            color: var(--muted);
            font-size: 0.9rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .feature-band {
            padding: 18px 0 60px;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }

        .benefit-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 20px;
            box-shadow: 0 8px 20px rgba(13, 27, 42, 0.05);
        }

        .benefit-icon {
            width: 48px;
            height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: linear-gradient(135deg, rgba(42, 157, 143, 0.15), rgba(231, 111, 81, 0.18));
            font-size: 1.4rem;
            margin-bottom: 14px;
        }

        .benefit-card h3 {
            margin: 0 0 8px;
            font-size: 1.08rem;
        }

        .benefit-card p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
        }

        .steps {
            padding: 20px 0 60px;
        }

        .step-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-top: 16px;
        }

        .step-card {
            position: relative;
            background: linear-gradient(180deg, #fff, #f6fafb);
            border: 1px solid var(--line);
            border-radius: 18px;
            padding: 20px 18px 18px;
        }

        .step-number {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background: var(--ink);
            color: #fff;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .step-card h3 {
            margin: 0 0 8px;
            font-size: 1.04rem;
        }

        .step-card p {
            margin: 0;
            color: var(--muted);
            line-height: 1.55;
            font-size: 0.95rem;
        }

        .pricing {
            padding: 22px 0 60px;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
            margin-top: 18px;
        }

        .plan {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 20px;
            padding: 24px 20px;
            box-shadow: 0 8px 22px rgba(13, 27, 42, 0.04);
            position: relative;
        }

        .plan.featured {
            border-color: rgba(231, 111, 81, 0.45);
            box-shadow: 0 16px 30px rgba(231, 111, 81, 0.12);
            transform: translateY(-4px);
        }

        .plan-badge {
            position: absolute;
            top: 16px;
            right: 16px;
            background: rgba(231, 111, 81, 0.12);
            color: var(--accent);
            border: 1px solid rgba(231, 111, 81, 0.2);
            border-radius: 999px;
            padding: 5px 10px;
            font-size: 0.74rem;
            font-weight: 700;
        }

        .plan h3 {
            margin: 0 0 8px;
            font-size: 1.2rem;
        }

        .plan .subtitle {
            margin: 0 0 18px;
            color: var(--muted);
            min-height: 50px;
        }

        .price {
            display: flex;
            align-items: baseline;
            gap: 6px;
            margin: 0 0 12px;
        }

        .price strong {
            font-size: 2.2rem;
            letter-spacing: -0.06em;
        }

        .price span {
            color: var(--muted);
            font-weight: 600;
        }

        .plan ul {
            list-style: none;
            padding: 0;
            margin: 0 0 18px;
        }

        .plan li {
            position: relative;
            padding-left: 28px;
            margin-bottom: 10px;
            color: #2c3d52;
            line-height: 1.45;
        }

        .plan li:before {
            content: "✓";
            position: absolute;
            left: 0;
            top: 0;
            color: var(--accent-2);
            font-weight: 900;
        }

        .cta-panel {
            margin-top: 26px;
            padding: 24px 22px;
            border-radius: 20px;
            background: linear-gradient(135deg, #0d1b2a 0%, #16324f 55%, #224b78 100%);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            box-shadow: var(--shadow);
        }

        .cta-panel h3 {
            margin: 0 0 6px;
            font-size: clamp(1.4rem, 2vw, 2rem);
        }

        .cta-panel p {
            margin: 0;
            color: rgba(255, 255, 255, 0.8);
        }

        .media-showcase {
            padding: 8px 0 52px;
        }

        .media-grid {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 18px;
            margin-top: 16px;
        }

        .media-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(13, 27, 42, 0.05);
        }

        .media-card .media-head {
            padding: 14px 16px 0;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .media-card img,
        .media-card video {
            width: 100%;
            display: block;
            border: 0;
            background: #edf3f7;
            object-fit: cover;
        }

        .media-card img {
            height: 260px;
        }

        .media-card video {
            height: 260px;
            background: linear-gradient(135deg, #0d1b2a, #1f4061);
        }

        .billing-controls {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 18px;
            margin-bottom: 16px;
        }

        .currency-switch,
        .period-switch {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 999px;
            padding: 6px;
            box-shadow: 0 8px 18px rgba(13, 27, 42, 0.04);
        }

        .currency-btn,
        .period-btn {
            border: 0;
            background: transparent;
            color: var(--muted);
            font: inherit;
            font-weight: 700;
            border-radius: 999px;
            padding: 9px 15px;
            cursor: pointer;
            transition: all .2s ease;
        }

        .currency-btn.active,
        .period-btn.active {
            background: var(--ink);
            color: #fff;
        }

        .save-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(42, 157, 143, 0.12);
            color: #0b7f7a;
            border: 1px solid rgba(42, 157, 143, 0.25);
            border-radius: 999px;
            padding: 8px 12px;
            font-size: 0.86rem;
            font-weight: 700;
        }

        .faq {
            padding: 0 0 60px;
        }

        .faq-list {
            display: grid;
            gap: 12px;
            margin-top: 18px;
        }

        .faq-item {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 16px;
            padding: 18px 20px;
        }

        .faq-item strong {
            display: block;
            margin-bottom: 6px;
            font-size: 1rem;
        }

        .faq-item span {
            color: var(--muted);
            line-height: 1.6;
        }

        .footer {
            padding: 26px 0 40px;
            color: var(--muted);
            font-size: 0.92rem;
            text-align: center;
        }

        @keyframes rise {
            from {
                opacity: 0;
                transform: translateY(18px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes reveal {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 920px) {
            .hero {
                grid-template-columns: 1fr;
            }
            .grid {
                grid-template-columns: 1fr 1fr;
            }
            .feature-grid,
            .step-grid,
            .pricing-grid,
            .module-grid {
                grid-template-columns: 1fr 1fr;
            }
            .forms-wrap {
                grid-template-columns: 1fr;
            }
            .cta-panel {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 620px) {
            .topbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            .nav {
                width: 100%;
                justify-content: flex-start;
            }
            .hero-copy,
            .hero-panel {
                padding: 24px;
                border-radius: 18px;
            }
            .grid,
            .feature-grid,
            .step-grid,
            .pricing-grid,
            .module-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="container topbar">
        <div class="logo">
            <?php echo isset($school['name']) && $school['name'] !== '' ? htmlspecialchars($school['name']) : 'Diagoma'; ?> <span>Site</span>
        </div>
        <nav class="nav" aria-label="Navigation principale">
            <a href="#services">Services</a>
            <a href="#modules">Modules</a>
            <a href="#benefits">Avantages</a>
            <a href="#pricing">Tarifs</a>
            <a href="#faq">FAQ</a>
        </nav>
        <div class="top-actions">
            <a href="<?php echo site_url('login'); ?>">Connexion staff</a>
        </div>
    </header>

    <main class="container hero">
        <section class="hero-copy">
            <span class="chip">Plateforme de gestion moderne</span>
            <h1>Un systeme unique pour piloter votre organisation</h1>
            <p class="lead">
                Centralisez les operations administratives, RH et financieres avec une interface claire,
                rapide et orientee resultats. Ce site vitrine presente l'essentiel de vos services.
            </p>
            <div class="hero-cta">
                <a class="btn btn-main" href="<?php echo site_url('login'); ?>">Demarrer maintenant</a>
                <a class="btn btn-ghost" href="#modules">Voir les modules</a>
            </div>
        </section>

        <aside class="hero-panel" aria-label="Indicateurs">
            <div class="metric">
                <strong>+95%</strong>
                <span>de taches admin automatisees</span>
            </div>
            <div class="metric">
                <strong>24/7</strong>
                <span>acces securise aux donnees</span>
            </div>
            <div class="metric">
                <strong>1 plateforme</strong>
                <span>RH, finance, et suivi global</span>
            </div>
        </aside>
    </main>

    <section class="container services" id="services">
        <h2>Ce que vous pouvez gerer facilement</h2>
        <div class="grid">
            <article class="card">
                <h3>Ressources humaines</h3>
                <p>Fiches employes, presences via QR code, badges, suivi de carriere et organisation des equipes.</p>
            </article>
            <article class="card">
                <h3>Finance integree</h3>
                <p>Suivi des revenus et depenses, tableaux de bord lisibles, export et controle des operations.</p>
            </article>
            <article class="card">
                <h3>Pilotage global</h3>
                <p>Des workflows unifies pour gagner du temps et prendre de meilleures decisions au quotidien.</p>
            </article>
        </div>
    </section>

    <section class="container modules" id="modules">
        <h2>Grands modules du logiciel</h2>
        <p class="intro">
            Une architecture modulaire qui couvre les operations essentielles de votre organisation, depuis la gestion des equipes jusqu'au pilotage financier et aux rapports strategiques.
        </p>
        <div class="module-grid">
            <article class="module-card">
                <span class="module-badge">RH</span>
                <h3>Ressources Humaines</h3>
                <p>Creation staff, affectations, presences QR, badges, documents et suivi administratif.</p>
            </article>
            <article class="module-card">
                <span class="module-badge">Finance</span>
                <h3>Comptabilite et Tresorerie</h3>
                <p>Recettes, depenses, etats financiers, imputations et historique des transactions.</p>
            </article>
            <article class="module-card">
                <span class="module-badge">Relations</span>
                <h3>Visiteurs et Courriers</h3>
                <p>Gestion des visiteurs, dispatch, documents entrants/sortants et rendez-vous.</p>
            </article>
            <article class="module-card">
                <span class="module-badge">Membres</span>
                <h3>Vie Communautaire</h3>
                <p>Membres, groupes, evenements, offrandes, baptemes, mariages et funerailles.</p>
            </article>
            <article class="module-card">
                <span class="module-badge">Patrimoine</span>
                <h3>Immobilisations</h3>
                <p>Suivi des actifs, amortissements, cessions et controle de la valeur patrimoniale.</p>
            </article>
            <article class="module-card">
                <span class="module-badge">Reporting</span>
                <h3>Rapports et Exports</h3>
                <p>Tableaux de bord, statistiques, exports PDF/Excel et consolidation periodique.</p>
            </article>
            <article class="module-card">
                <span class="module-badge">Securite</span>
                <h3>Acces et Authentification</h3>
                <p>Connexion securisee, profils staff/eleve/parent et journal des actions.</p>
            </article>
        </div>
    </section>

    <section class="container trust-bar" aria-label="Confiance">
        <div class="trust-strip">
            <span>Suivi RH</span>
            <span>Finance claire</span>
            <span>Rapports en temps réel</span>
            <span>Gestion centralisée</span>
            <span>Accompagnement dédié</span>
        </div>
    </section>

    <section class="container feature-band" id="benefits">
        <h2>Pourquoi les organisations nous choisissent</h2>
        <div class="feature-grid">
            <article class="benefit-card">
                <div class="benefit-icon">📊</div>
                <h3>Tableaux de bord utiles</h3>
                <p>Visualisez rapidement les indicateurs critiques de votre activité et prenez des decisions fondees sur des donnees fiables.</p>
            </article>
            <article class="benefit-card">
                <div class="benefit-icon">⚡</div>
                <h3>Automatisation des tâches</h3>
                <p>Reducez le temps de traitement des operations manuelles et eliminez les erreurs via des workflows automatises.</p>
            </article>
            <article class="benefit-card">
                <div class="benefit-icon">🔒</div>
                <h3>Securite et traçabilite</h3>
                <p>Protegez vos donnees avec des acces controles, une authentification robuste et un historique complet des actions.</p>
            </article>
        </div>
    </section>

    <section class="container steps" id="process">
        <h2>Une mise en route rapide et claire</h2>
        <div class="step-grid">
            <article class="step-card">
                <div class="step-number">1</div>
                <h3>Diagnostic</h3>
                <p>Nous analysons vos besoins, vos enjeux et vos processus pour definir la meilleure configuration.</p>
            </article>
            <article class="step-card">
                <div class="step-number">2</div>
                <h3>Parametrage</h3>
                <p>Nous configurons les modules, roles, donnees et flux d'entree pour correspondre a votre organisation.</p>
            </article>
            <article class="step-card">
                <div class="step-number">3</div>
                <h3>Formation</h3>
                <p>Votre equipe apprend l'outil et les bonnes pratiques pour une adoption rapide et durable.</p>
            </article>
            <article class="step-card">
                <div class="step-number">4</div>
                <h3>Suivi</h3>
                <p>Nous restons a vos cotes pour ajuster les modules et accompagner votre croissance.</p>
            </article>
        </div>
    </section>

    <section class="container media-showcase" aria-label="Captures et vidéos">
        <h2>Découvrez le logiciel en action</h2>
        <div class="media-grid">
            <article class="media-card">
                <div class="media-head">Capture d'écran</div>
                <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?auto=format&fit=crop&w=1200&q=80" alt="Capture du tableau de bord de gestion" />
            </article>
            <article class="media-card">
                <div class="media-head">Vidéo de démonstration</div>
                <video controls poster="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=1200&q=80">
                    <source src="https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4" type="video/mp4">
                    Votre navigateur ne prend pas en charge la lecture de vidéos HTML5.
                </video>
            </article>
        </div>
    </section>

    <section class="container pricing" id="pricing">
        <h2>Des tarifs simples et transparents</h2>
        <p class="intro">Choisissez le plan qui correspond à la taille de votre entreprise. Changez à tout moment.</p>

        <div class="billing-controls">
            <div class="currency-switch" aria-label="Devise">
                <button class="currency-btn active" type="button" data-currency="EUR">EUR (€)</button>
                <button class="currency-btn" type="button" data-currency="XOF">XOF (FCFA)</button>
                <button class="currency-btn" type="button" data-currency="USD">USD ($)</button>
            </div>
            <div class="period-switch" aria-label="Période de facturation">
                <button class="period-btn active" type="button" data-period="monthly">Mensuel</button>
                <button class="period-btn" type="button" data-period="yearly">Annuel</button>
            </div>
            <!--<span class="save-badge">💰 Économisez 2 mois</span>-->
        </div>

        <div class="pricing-grid">
            <article class="plan">
                <h3>Essentiel</h3>
                <p class="subtitle">Pour les organisations qui veulent commencer proprement.</p>
                <div class="price"><strong data-price="plan1">39</strong><span data-unit="plan1">€/mois</span></div>
                <ul>
                    <li>Gestion de base RH</li>
                    <li>Suivi des presences</li>
                    <li>Acces staff et admin</li>
                    <li>Rapports standards</li>
                </ul>
                <a class="btn btn-ghost" href="#demo">Demander un devis</a>
            </article>

            <article class="plan featured">
                <span class="plan-badge">Populaire</span>
                <h3>Performance</h3>
                <p class="subtitle">Le meilleur compromis pour une gestion complete et fluide.</p>
                <div class="price"><strong data-price="plan2">89</strong><span data-unit="plan2">€/mois</span></div>
                <ul>
                    <li>Tous les modules principaux</li>
                    <li>Finance, et reporting</li>
                    <li>Automatisations et exports</li>
                    <li>Support prioritaire</li>
                </ul>
                <a class="btn btn-main" href="#demo">Essayer gratuitement</a>
            </article>

            <article class="plan">
                <h3>Entreprise</h3>
                <p class="subtitle">Pour les structures qui veulent un accompagnement premium.</p>
                <div class="price"><strong data-price="plan3">Sur devis</strong><span data-unit="plan3"></span></div>
                <ul>
                    <li>Customisation avancee</li>
                    <li>Integrations et migration</li>
                    <li>Accompagnement sur mesure</li>
                    <li>Assistance 24/7</li>
                </ul>
                <a class="btn btn-ghost" href="#demo">Parler a un expert</a>
            </article>
        </div>

        <div class="cta-panel">
            <div>
                <h3>Besoin d'une solution sur mesure ?</h3>
                <p>Nous vous accompagnons pour adapter le logiciel a vos processus et a votre croissance.</p>
            </div>
            <a class="btn btn-main" href="#demo">Planifier une demo</a>
        </div>
    </section>

    <section class="container faq" id="faq">
        <h2>Questions frequentes</h2>
        <div class="faq-list">
            <div class="faq-item">
                <strong>Le logiciel convient-il a une structure de taille moyenne ?</strong>
                <span>Oui, il est concu pour evoluer avec votre organisation, du pilotage simple au fonctionnement multi-services.</span>
            </div>
            <div class="faq-item">
                <strong>Est-il possible d'utiliser plusieurs modules ?</strong>
                <span>Oui. Vous pouvez activer les modules utiles selon vos besoins et les ajouter au fil du temps.</span>
            </div>
            <div class="faq-item">
                <strong>Quelle est la duree de mise en place ?</strong>
                <span>Selon la complexite, la mise en route prend souvent quelques jours a quelques semaines avec un accompagnement concret.</span>
            </div>
        </div>
    </section>

    <section class="container forms-section" id="demo">
        <div class="forms-wrap">
            <article class="form-card">
                <h3>Demande de demo</h3>
                <p>Planifiez une demonstration pour decouvrir le logiciel adapte a votre organisation.</p>
                <?php if ($this->session->flashdata('demo_success')) { ?>
                    <div class="flash-message flash-success"><?php echo $this->session->flashdata('demo_success'); ?></div>
                <?php } ?>
                <?php if ($this->session->flashdata('demo_error')) { ?>
                    <div class="flash-message flash-error"><?php echo $this->session->flashdata('demo_error'); ?></div>
                <?php } ?>
                <form method="post" action="<?php echo site_url('site/submit_demo_request'); ?>">
                    <?php echo $this->customlib->getCSRF(); ?>
                    <div class="form-grid">
                        <div class="field">
                            <label for="demo_full_name">Nom complet</label>
                            <input id="demo_full_name" type="text" name="full_name" required>
                        </div>
                        <div class="field">
                            <label for="demo_email">Email</label>
                            <input id="demo_email" type="email" name="email" required>
                        </div>
                        <div class="field">
                            <label for="demo_phone">Telephone</label>
                            <input id="demo_phone" type="text" name="phone" required>
                        </div>
                        <div class="field">
                            <label for="demo_company">Organisation</label>
                            <input id="demo_company" type="text" name="company" required>
                        </div>
                        <div class="field full">
                            <label for="demo_message">Besoin principal</label>
                            <textarea id="demo_message" name="message" rows="4" placeholder="Exemple: RH + Finance + rapports" required></textarea>
                        </div>
                    </div>
                    <button class="btn-submit" type="submit">Envoyer la demande</button>
                </form>
            </article>

            <article class="form-card" id="newsletter">
                <h3>Newsletter</h3>
                <p>Recevez les nouveautes produit, mises a jour et conseils d'utilisation.</p>
                <?php if ($this->session->flashdata('newsletter_success')) { ?>
                    <div class="flash-message flash-success"><?php echo $this->session->flashdata('newsletter_success'); ?></div>
                <?php } ?>
                <?php if ($this->session->flashdata('newsletter_error')) { ?>
                    <div class="flash-message flash-error"><?php echo $this->session->flashdata('newsletter_error'); ?></div>
                <?php } ?>
                <form method="post" action="<?php echo site_url('site/subscribe_newsletter'); ?>">
                    <?php echo $this->customlib->getCSRF(); ?>
                    <div class="field">
                        <label for="newsletter_email">Votre email</label>
                        <input id="newsletter_email" type="email" name="email" required placeholder="nom@organisation.com">
                    </div>
                    <button class="btn-submit" type="submit">Je m'abonne</button>
                </form>
            </article>
        </div>
    </section>

    <footer class="container footer">
        <?php echo isset($school['name']) && $school['name'] !== '' ? htmlspecialchars($school['name']) : 'Diagoma'; ?> - Site vitrine officiel
    </footer>

    <script>
        (function () {
            const prices = {
                EUR: {
                    monthly: { plan1: 39, plan2: 89, plan3: 'Sur devis' },
                    yearly: { plan1: 390, plan2: 890, plan3: 'Sur devis' }
                },
                XOF: {
                    monthly: { plan1: 24000, plan2: 54000, plan3: 'Sur devis' },
                    yearly: { plan1: 240000, plan2: 540000, plan3: 'Sur devis' }
                },
                USD: {
                    monthly: { plan1: 45, plan2: 99, plan3: 'Sur devis' },
                    yearly: { plan1: 450, plan2: 990, plan3: 'Sur devis' }
                }
            };

            const currencyButtons = document.querySelectorAll('.currency-btn');
            const periodButtons = document.querySelectorAll('.period-btn');
            const formatCurrency = function (value, currency) {
                if (value === 'Sur devis') {
                    return 'Sur devis';
                }
                if (currency === 'XOF') {
                    return new Intl.NumberFormat('fr-FR').format(value);
                }
                return new Intl.NumberFormat('fr-FR', { maximumFractionDigits: 0 }).format(value);
            };

            const setPricing = function (currency, period) {
                const selected = prices[currency][period];
                const currencySuffix = {
                    EUR: '€/mois',
                    XOF: 'FCFA/mois',
                    USD: '$/mois'
                };

                const planOne = document.querySelector('[data-price="plan1"]');
                const planTwo = document.querySelector('[data-price="plan2"]');
                const unitOne = document.querySelector('[data-unit="plan1"]');
                const unitTwo = document.querySelector('[data-unit="plan2"]');

                if (planOne) {
                    planOne.textContent = formatCurrency(selected.plan1, currency);
                }
                if (planTwo) {
                    planTwo.textContent = formatCurrency(selected.plan2, currency);
                }
                if (unitOne) {
                    unitOne.textContent = currency === 'XOF' ? (period === 'yearly' ? 'FCFA/an' : 'FCFA/mois') : (period === 'yearly' ? currencySuffix[currency].replace('/mois', '/an') : currencySuffix[currency]);
                }
                if (unitTwo) {
                    unitTwo.textContent = currency === 'XOF' ? (period === 'yearly' ? 'FCFA/an' : 'FCFA/mois') : (period === 'yearly' ? currencySuffix[currency].replace('/mois', '/an') : currencySuffix[currency]);
                }

                const planThree = document.querySelector('[data-price="plan3"]');
                const unitThree = document.querySelector('[data-unit="plan3"]');
                if (planThree) {
                    planThree.textContent = 'Sur devis';
                }
                if (unitThree) {
                    unitThree.textContent = '';
                }
            };

            currencyButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    currencyButtons.forEach(function (item) { item.classList.toggle('active', item === button); });
                    const activePeriod = document.querySelector('.period-btn.active')?.dataset.period || 'monthly';
                    setPricing(button.dataset.currency, activePeriod);
                });
            });

            periodButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    periodButtons.forEach(function (item) { item.classList.toggle('active', item === button); });
                    const activeCurrency = document.querySelector('.currency-btn.active')?.dataset.currency || 'EUR';
                    setPricing(activeCurrency, button.dataset.period);
                });
            });

            setPricing('EUR', 'monthly');
        })();
    </script>
</body>
</html>
