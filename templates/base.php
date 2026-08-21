<!DOCTYPE html>
<?php if (empty($hideSiteHeader)): ?>
    <html lang="fr-FR" data-bs-theme="dark">
<?php endif; ?>
<?php if (!empty($hideSiteHeader)): ?>
    <html lang="fr-FR" data-bs-theme="<?php echo ($themeChoice === 'light') ? 'light' : 'dark'; ?>">
<?php endif; ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Meteastro - Votre portail d'astronomie et de météorologie.">
    <title>Meteastro | Astronomie & Météorologie</title>

    <link rel="icon" type="image/png" href="/assets/images/logo.png">

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#007bff">

    <!-- <link rel="stylesheet" href="/assets/css/divers.css"> -->

    <?php if (!empty($hideSiteHeader)): ?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="/assets/css/connexion.css" />
    <?php endif; ?>

    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Space+Mono&display=swap"
        rel="stylesheet">

    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Inter:wght@300;400;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />

    <style>
        <?php if (!empty($hideSiteHeader)): ?>
            .login-page .info-bar,
            .login-page .header-top,
            .login-page .pwa-app,
            .login-page .navbar,
            .login-page .footer-glass {
                display: none !important;
            }

        <?php endif; ?>
        <?php if (empty($hideSiteHeader)): ?>
            :root {
                --glass: rgba(15, 23, 42, 0.7);
                --border: rgba(255, 255, 255, 0.1);
                --accent: #3b82f6;
            }

            body {
                background-color: #05070a;
                color: #e2e8f0;
                font-family: 'Outfit', sans-serif;
                overflow-x: hidden;
            }

            /* Background étoilé */
            #star-field {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: -1;
                overflow: hidden;
                pointer-events: none;
                background: radial-gradient(circle at center, #0a0f1a 0%, #05070a 100%);
            }

            /* Style Glassmorphism Bootstrap */
            .glass-card {
                background: var(--glass) !important;
                backdrop-filter: blur(12px);
                border: 1px solid var(--border) !important;
                border-radius: 1.5rem !important;
                transition: all 0.4s ease;
            }

            .glass-card:hover {
                border-color: var(--accent) !important;
                transform: translateY(-5px);
            }

            /* Boites de catégories */
            .category-box {
                position: relative;
                height: 350px;
                overflow: hidden;
                border-radius: 1.5rem;
                display: block;
                text-decoration: none;
            }

            .category-box img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.7s ease;
                filter: brightness(0.5);
            }

            .category-box:hover img {
                transform: scale(1.1);
                filter: brightness(0.7);
            }

            /* Onglets personnalisés */
            .nav-tabs {
                border: none;
            }

            .nav-link {
                color: #94a3b8 !important;
                border: none !important;
                font-family: 'Space Mono', monospace;
                text-transform: uppercase;
                letter-spacing: 1px;
                font-size: 0.8rem;
                padding: 1rem 1.5rem;
            }

            .nav-link.active {
                background: none !important;
                color: var(--accent) !important;
                border-bottom: 2px solid var(--accent) !important;
            }

            /* Inputs de formulaire */
            .form-control {
                background: rgba(255, 255, 255, 0.05) !important;
                border: 1px solid var(--border) !important;
                color: white !important;
                border-radius: 0.75rem;
                padding: 0.8rem 1.2rem;
            }

            .form-control:focus {
                border-color: var(--accent) !important;
                box-shadow: 0 0 0 0.25 margin-top: 10px;
                rem rgba(59, 130, 246, 0.25);
            }

            .btn-astro {
                border: none;
                border-radius: 0.75rem;
                padding: 1rem;
                font-weight: bold;
                text-transform: uppercase;
                letter-spacing: 1px;
                transition: 0.3s;
            }

            .btn-astro:hover {
                background: #2563eb;
                box-shadow: 0 0 20px rgba(59, 130, 246, 0.4);
            }

            @keyframes twinkle {

                0%,
                100% {
                    opacity: 0.3;
                }

                50% {
                    opacity: 1;
                }
            }

            p {
                color: white;
            }

            :root {
                --glass-bg: rgba(15, 23, 42, 0.8);
                --glass-border: rgba(255, 255, 255, 0.1);
                --accent-color: #3b82f6;
            }

            body {
                font-family: 'Outfit', sans-serif;
                background-color: #05070a;
                color: #e2e8f0;
                overflow-x: hidden;
            }

            /* SLIDE PARAMETRES */
            .edit-full-page {
                position: fixed;
                top: 0;
                left: 100%;
                width: 100%;
                height: 100%;
                background: #020617;
                z-index: 10000;
                overflow-y: auto;
                visibility: hidden;
                transition: left 0.5s cubic-bezier(0.77, 0, 0.175, 1), visibility 0.5s;
            }

            .edit-full-page.active {
                left: 0;
                visibility: visible;
            }

            .glass-container {
                background: rgba(15, 23, 42, 0.7);
                backdrop-filter: blur(12px);
                border: 1px solid var(--glass-border);
                border-radius: 24px;
                padding: 2.5rem;
            }

            .input-glass {
                width: 100%;
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid var(--glass-border);
                color: white;
                padding: 12px;
                border-radius: 12px;
                margin-top: 5px;
                outline: none;
            }

            .input-glass:focus {
                border-color: var(--accent-color);
                background: rgba(255, 255, 255, 0.1);
            }

            .btn-cosmic-glass {
                background: rgba(59, 130, 246, 0.2);
                border: 1px solid var(--accent-color);
                color: white;
                padding: 15px;
                border-radius: 12px;
                font-weight: 600;
                transition: all 0.3s;
                width: 100%;
                cursor: pointer;
            }

            .btn-cosmic-glass:hover {
                background: var(--accent-color);
                transform: translateY(-2px);
            }

            /* Header & Logo */
            .header-top {
                background: linear-gradient(to right, #0f172a, #020617);
                padding: 1rem 0;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }

            .logo-text {
                font-weight: 700;
                letter-spacing: 2px;
                color: white;
                text-decoration: none;
                font-size: 1.5rem;
            }

            /* Marquee moderne */
            .info-bar {
                background: rgba(59, 130, 246, 0.1);
                color: var(--accent-color);
                padding: 5px 0;
                font-size: 0.85rem;
                border-bottom: 1px solid rgba(59, 130, 246, 0.2);
            }

            /* Navbar Custom */
            .dropdown-menu {
                background: #1e293b;
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            .dropdown-item:hover {
                background: var(--accent-color);
                color: white;
            }

            /* Protection Images */
            img {
                user-select: none;
                -webkit-user-drag: none;
            }

            /* Modal Custom */
            .modal-content {
                background: #0f172a;
                border: 1px solid var(--glass-border);
                border-radius: 20px;
            }

            /* ==========================================================================
       1. VARIABLES DE THÈME
       ========================================================================== */
            :root {
                --pwa-bg-header: rgba(243, 243, 244, 0.90);
                --pwa-border-color: rgba(0, 0, 0, 0.07);
                --pwa-text-main: #1f1f1f;
                --pwa-text-inactive: #5f6368;
                --pwa-text-active: #0b57d0;
                --pwa-accent-2: #7c3aed;
                --pwa-shadow-nav: 0 8px 30px rgba(0, 0, 0, 0.12);
                --pwa-card-bg: #ffffff;
                --pwa-overlay-bg: rgba(15, 15, 20, 0.55);
            }

            @media (prefers-color-scheme: dark) {
                :root {
                    --pwa-bg-header: rgba(31, 31, 31, 0.90);
                    --pwa-border-color: rgba(255, 255, 255, 0.08);
                    --pwa-text-main: #e8e8e8;
                    --pwa-text-inactive: #9aa0a6;
                    --pwa-text-active: #8ab4f8;
                    --pwa-accent-2: #c084fc;
                    --pwa-shadow-nav: 0 8px 32px rgba(0, 0, 0, 0.55);
                    --pwa-card-bg: #1a1a1e;
                    --pwa-overlay-bg: rgba(0, 0, 0, 0.72);
                }
            }

            /* ==========================================================================
       2. BOUTON FLOTTANT (FAB) — verre dépoli + halo pulsé
       ========================================================================== */
            .pwa-fab-trigger {
                position: relative;
                background: color-mix(in srgb, var(--pwa-card-bg) 65%, transparent);
                backdrop-filter: blur(14px) saturate(160%);
                -webkit-backdrop-filter: blur(14px) saturate(160%);
                color: var(--pwa-text-active);
                border: 1px solid var(--pwa-border-color);
                width: 42px;
                height: 42px;
                border-radius: 50%;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: visible;
                transition: transform 0.35s cubic-bezier(0.34, 1.56, 0.64, 1),
                    background-color 0.25s ease,
                    box-shadow 0.25s ease;
            }

            .pwa-fab-trigger:hover {
                background: color-mix(in srgb, var(--pwa-card-bg) 82%, transparent);
                box-shadow: 0 4px 16px rgba(11, 87, 208, 0.18);
                transform: translateY(-2px);
            }

            .pwa-fab-trigger:active {
                transform: scale(0.90);
            }

            .pwa-fab-trigger svg {
                width: 20px;
                height: 20px;
                fill: currentColor;
                position: relative;
                z-index: 2;
            }

            /* Halo pulsé discret, en fond du bouton */
            .pwa-fab-pulse {
                position: absolute;
                inset: 0;
                border-radius: 50%;
                background: radial-gradient(circle, var(--pwa-text-active) 0%, transparent 70%);
                opacity: 0.35;
                animation: pwa-pulse 2.6s ease-in-out infinite;
                z-index: 1;
            }

            @keyframes pwa-pulse {

                0%,
                100% {
                    transform: scale(0.85);
                    opacity: 0.25;
                }

                50% {
                    transform: scale(1.15);
                    opacity: 0.5;
                }
            }

            /* ==========================================================================
       3. OVERLAY & MODAL
       ========================================================================== */
            .pwa-modal-overlay {
                position: fixed;
                inset: 0;
                background: var(--pwa-overlay-bg);
                backdrop-filter: blur(6px);
                -webkit-backdrop-filter: blur(6px);
                z-index: 1000;
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.35s ease;
                padding: 16px;
                box-sizing: border-box;
            }

            .pwa-modal-overlay.is-open {
                opacity: 1;
                pointer-events: auto;
            }

            .pwa-modal-container {
                width: 100%;
                max-width: 380px;
                transform: translateY(24px) scale(0.96);
                opacity: 0;
                transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
            }

            .pwa-modal-overlay.is-open .pwa-modal-container {
                transform: translateY(0) scale(1);
                opacity: 1;
            }

            .pwa-card {
                background: var(--pwa-card-bg);
                border: 1px solid var(--pwa-border-color);
                border-radius: 32px;
                padding: 32px 28px 28px;
                text-align: center;
                box-shadow: var(--pwa-shadow-nav);
                position: relative;
                overflow: hidden;
            }

            /* Mesh de couleur discret en arrière-plan du card */
            .pwa-card::before {
                content: "";
                position: absolute;
                top: -60%;
                left: -20%;
                width: 160%;
                height: 160%;
                background:
                    radial-gradient(circle at 20% 20%, color-mix(in srgb, var(--pwa-text-active) 18%, transparent) 0%, transparent 45%),
                    radial-gradient(circle at 80% 10%, color-mix(in srgb, var(--pwa-accent-2) 14%, transparent) 0%, transparent 45%);
                pointer-events: none;
                z-index: 0;
            }

            .pwa-card>* {
                position: relative;
                z-index: 1;
            }

            .pwa-btn-close {
                position: absolute;
                top: 18px;
                right: 18px;
                background: color-mix(in srgb, var(--pwa-text-main) 6%, transparent);
                border: none;
                color: var(--pwa-text-inactive);
                width: 30px;
                height: 30px;
                border-radius: 50%;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: background 0.2s, color 0.2s, transform 0.2s;
            }

            .pwa-btn-close:hover {
                background: color-mix(in srgb, var(--pwa-text-main) 12%, transparent);
                color: var(--pwa-text-main);
                transform: rotate(90deg);
            }

            /* ==========================================================================
       4. LOGO
       ========================================================================== */
            .pwa-logo-wrap {
                position: relative;
                width: 72px;
                height: 72px;
                margin: 0 auto 18px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .pwa-logo-glow {
                position: absolute;
                inset: -10px;
                border-radius: 24px;
                background: conic-gradient(from 0deg, var(--pwa-text-active), var(--pwa-accent-2), var(--pwa-text-active));
                filter: blur(16px);
                opacity: 0.45;
                animation: pwa-spin 6s linear infinite;
            }

            @keyframes pwa-spin {
                to {
                    transform: rotate(360deg);
                }
            }

            .pwa-logo {
                position: relative;
                width: 72px;
                height: 72px;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 6px 16px rgba(0, 0, 0, 0.18);
            }

            .pwa-logo img {
                width: 100%;
                height: 100%;
                object-fit: contain;
                display: block;
            }

            /* ==========================================================================
       5. TEXTES
       ========================================================================== */
            .pwa-rainbow-title {
                margin: 0 0 8px 0;
                font-size: 21px;
                font-weight: 800;
                letter-spacing: -0.02em;
                background: linear-gradient(100deg,
                        hsl(210, 90%, 55%), hsl(260, 85%, 60%), hsl(320, 80%, 58%),
                        hsl(20, 90%, 58%), hsl(210, 90%, 55%));
                background-size: 300% 100%;
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                animation: pwa-rainbow-fluid 12s ease-in-out infinite;
            }

            @keyframes pwa-rainbow-fluid {

                0%,
                100% {
                    background-position: 0% 50%;
                }

                50% {
                    background-position: 100% 50%;
                }
            }

            .pwa-subtitle {
                color: var(--pwa-text-inactive);
                margin: 0 0 18px 0;
                font-size: 14px;
                line-height: 1.5;
            }

            .pwa-badge-status {
                font-size: 12.5px;
                font-weight: 600;
                color: var(--pwa-text-inactive);
                background: color-mix(in srgb, var(--pwa-text-main) 5%, transparent);
                border: 1px solid var(--pwa-border-color);
                padding: 7px 14px;
                border-radius: 100px;
                display: inline-flex;
                align-items: center;
                gap: 7px;
                margin-bottom: 20px;
            }

            .pwa-badge-dot {
                width: 7px;
                height: 7px;
                border-radius: 50%;
                background: var(--pwa-text-active);
                animation: pwa-blink 1.6s ease-in-out infinite;
            }

            @keyframes pwa-blink {

                0%,
                100% {
                    opacity: 1;
                }

                50% {
                    opacity: 0.3;
                }
            }

            /* ==========================================================================
       6. INSTRUCTIONS
       ========================================================================== */
            .pwa-list-instructions {
                text-align: left;
                list-style: none;
                padding: 0;
                margin: 0 0 20px 0;
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .pwa-list-instructions li {
                display: flex;
                align-items: flex-start;
                gap: 12px;
                font-size: 14px;
                line-height: 1.5;
                color: var(--pwa-text-main);
            }

            .pwa-step-num {
                flex-shrink: 0;
                width: 22px;
                height: 22px;
                border-radius: 50%;
                background: color-mix(in srgb, var(--pwa-text-active) 14%, transparent);
                color: var(--pwa-text-active);
                font-size: 12px;
                font-weight: 700;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .pwa-inline-icon {
                font-size: 15px;
                opacity: 0.8;
            }

            /* ==========================================================================
       7. BOUTON D'INSTALLATION
       ========================================================================== */
            .pwa-btn-install {
                display: none;
                width: 100%;
                padding: 15px;
                font-size: 15px;
                border-radius: 16px;
                background: linear-gradient(135deg, var(--pwa-text-active), var(--pwa-accent-2));
                color: #fff;
                font-weight: 700;
                border: none;
                cursor: pointer;
                align-items: center;
                justify-content: center;
                gap: 8px;
                box-shadow: 0 6px 18px color-mix(in srgb, var(--pwa-text-active) 35%, transparent);
                transition: transform 0.15s ease, box-shadow 0.25s ease;
            }

            .pwa-btn-install:hover {
                box-shadow: 0 8px 22px color-mix(in srgb, var(--pwa-text-active) 45%, transparent);
            }

            .pwa-btn-install:active {
                transform: scale(0.97);
            }

            /* ==========================================================================
       8. AFFICHAGE MODE APPLI INSTALLÉE
       ========================================================================== */
            .pwa-dashboard-view {
                display: none !important;
            }

            @media (display-mode: standalone),
            (display-mode: fullscreen),
            (display-mode: minimal-ui) {
                .pwa-dashboard-view {
                    display: block !important;
                }

                .pwa-browser-view,
                .pwa-fab-trigger {
                    display: none !important;
                }
            }

            /* ==========================================================================
       1. VARIABLES DE THÈME (STYLE FLOTTANT ANDROID MATERIAL YOU)
       ========================================================================== */
            :root {
                /* --- Mode Clair (Couleurs de surface Android) --- */
                --bg-header: rgba(243, 243, 244, 0.90);
                /* Opacité 90% teintée Android 14 */
                --bg-nav: rgba(243, 243, 244, 0.88);
                /* Transparence équilibrée native */
                --border-color: rgba(0, 0, 0, 0.06);
                /* Délimitation subtile */
                --text-main: #1f1f1f;
                --text-inactive: #444746;
                /* Teinte "On-Surface Variant" Android */
                --text-active: #0b57d0;
                /* Bleu primaire standard Material 3 */
                --shadow-header: 0 1px 2px rgba(0, 0, 0, 0.05);
                --shadow-nav: 0 2px 6px rgba(0, 0, 0, 0.08);
            }

            @media (prefers-color-scheme: dark) {
                :root {
                    /* --- Mode Sombre (Couleurs de surface Dark Android) --- */
                    --bg-header: rgba(31, 31, 31, 0.90);
                    --bg-nav: rgba(31, 31, 31, 0.88);
                    --border-color: rgba(255, 255, 255, 0.07);
                    --text-main: #e3e3e3;
                    --text-inactive: #c4c7c5;
                    --text-active: #a8c7fa;
                    /* Bleu clair adaptatif écran sombre */
                    --shadow-header: 0 1px 2px rgba(0, 0, 0, 0.2);
                    --shadow-nav: 0 4px 12px rgba(0, 0, 0, 0.3);
                }
            }

            /* ==========================================================================
       2. EN-TÊTE DE L'APPLICATION
       ========================================================================== */
            .app-header {
                position: sticky;
                top: 0;
                height: 60px;
                background: var(--bg-header);
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0 20px;
                box-shadow: var(--shadow-header);
                border-bottom: 1px solid var(--border-color);
                z-index: 900;
                transition: background 0.25s ease, border-color 0.25s ease;
            }

            .header-icon-btn {
                background: none;
                border: none;
                font-size: 24px;
                color: var(--text-main);
                padding: 5px;
                width: auto;
                cursor: pointer;
                transition: color 0.25s ease;
            }

            .app-title {
                font-weight: 700;
                font-size: 18px;
                letter-spacing: -0.5px;
                color: var(--text-main);
                transition: color 0.25s ease;
            }

            .header-install-btn {
                width: auto;
                padding: 8px 14px;
                font-size: 13px;
                border-radius: 12px;
                background-color: var(--text-active);
                color: white;
                border: none;
                cursor: pointer;
                transition: background-color 0.25s ease;
            }

            /* ==========================================================================
       3. GESTION PAR DÉFAUT (NAVIGATEURS WEB CLASSIQUES)
       ========================================================================== */
            .bottom-nav {
                display: none !important;
                /* Masqué sur navigateur de bureau */
            }

            .container-mobile {
                padding-bottom: 30px;
                /* Espace standard de fin de page */
                transition: padding 0.25s ease;
            }

            /* --- CONTRÔLE D'AFFICHAGE DE L'INTERFACE DE L'APPLICATION --- */

            /* 1. Caché par défaut sur tous les navigateurs web classiques */
            .pwa-app-dashboard {
                display: none !important;
            }

            .pwa-app {
                display: none;
            }

            /* 2. Affiché uniquement si l'application est lancée en mode Standalone (PWA installée) */
            @media (display-mode: standalone),
            (display-mode: fullscreen),
            (display-mode: minimal-ui) {
                .pwa-app-dashboard {
                    display: flex !important;
                    flex-direction: column;
                    gap: 14px;
                    width: 100%;
                    max-width: 420px;
                    margin: 0 auto 24px auto;
                    font-family: 'Outfit', sans-serif;
                    box-sizing: border-box;
                    padding: 0 8px;
                }
            }

            /* Compatibilité additionnelle spécifique aux anciens systèmes iOS (Safari PWAs) */
            @media (navigator-standalone: true) {
                .pwa-app-dashboard {
                    display: flex !important;
                    flex-direction: column;
                }
            }

            /* ==========================================================================
       4. COMPORTEMENT EXCLUSIF : MODE APP INSTALLÉE (STANDALONE)
       ========================================================================== */
            @media (display-mode: standalone) {

                .bottom-nav {
                    display: grid !important;
                    grid-template-columns: repeat(5, 1fr);
                    position: fixed;
                    bottom: 16px;
                    left: 12px;
                    right: 12px;
                    height: 60px;

                    /* Intégration du rendu Android sans flou */
                    background: var(--bg-nav);
                    border: 1px solid var(--border-color);
                    box-shadow: var(--shadow-nav);
                    border-radius: 24px;
                    /* Format "pilule flottante" Material You */

                    align-items: center;
                    z-index: 1000;
                    padding: 0 4px;
                    box-sizing: border-box;
                    pointer-events: auto;
                    transition: background 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
                }

                .nav-item {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    text-decoration: none;
                    color: var(--text-inactive);
                    font-size: 10px;
                    font-weight: 500;
                    /* Medium font-weight natif Android */
                    height: 100%;
                    width: 100%;
                    padding: 4px 0;
                    box-sizing: border-box;
                    transition: color 0.15s ease, font-weight 0.15s ease;
                }

                .nav-icon {
                    font-size: 20px;
                    margin-bottom: 3px;
                    line-height: 1;
                    transition: transform 0.2s cubic-bezier(0.2, 0, 0, 1);
                    /* Courbe haptique Android */
                }

                .nav-label {
                    display: block;
                    font-size: 10px;
                    letter-spacing: 0.1px;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    max-width: 92%;
                }

                /* --- ÉTATS & RETOURS EFFETS TACTILES --- */
                .nav-item:active .nav-icon {
                    transform: scale(0.9);
                    /* Feedback haptique visuel doux Android */
                }

                .nav-item.active {
                    color: var(--text-active);
                    font-weight: 700;
                    /* Met en valeur l'élément actif */
                }

                /* --- ADAPTATION STRUCTURELLE DE L'APPLICATION --- */
                .container-mobile {
                    padding-bottom: 100px !important;
                    box-sizing: border-box;
                }

                .navbar,
                #playerContainer {
                    display: none !important;
                }

                /* ==========================================================================
       SOUS-MENU CONFIGURATION COMPTE (STYLE FLOTTANT ANDROID MATERIAL YOU)
       ========================================================================== */

                /* Positionnement du bouton parent */
                .nav-item-has-submenu {
                    position: relative;
                }

                /* Le Sous-Menu (Format carte arrondie, sans débordement d'écran) */
                .nav-submenu {
                    position: absolute;
                    bottom: 72px;
                    /* Placé idéalement juste au-dessus de la bottom-nav */

                    /* CORRECTION ANGLE : Aligné sur la droite de l'onglet avec un retrait de sécurité */
                    right: 0;
                    margin-right: 8px;
                    width: 180px;

                    /* Design & Couleurs adaptatives */
                    background: var(--bg-nav);
                    border: 1px solid var(--border-color);
                    box-shadow: var(--shadow-nav);
                    border-radius: 20px;
                    /* Coins généreusement arrondis style Material 3 */
                    padding: 6px;
                    z-index: 1001;
                    box-sizing: border-box;

                    /* GESTION DE L'ANIMATION (Sans display: none pour préserver la fluidité) */
                    visibility: hidden;
                    opacity: 0;
                    transform: translateY(12px) scale(0.96);
                    /* Effet d'apparition du bas vers le haut */
                    transition: transform 0.28s cubic-bezier(0.2, 0, 0, 1),
                        opacity 0.22s ease,
                        visibility 0.22s ease;
                }

                /* ÉTAT ACTIF : Classe injectée par le JavaScript à l'ouverture */
                .nav-submenu.open {
                    visibility: visible;
                    opacity: 1;
                    transform: translateY(0) scale(1);
                    /* Rendu stable et centré sur sa zone */
                }

                /* Rotation fluide de la flèche (caret) */
                .submenu-caret {
                    font-size: 8px;
                    color: inherit;
                    transition: transform 0.25s cubic-bezier(0.2, 0, 0, 1);
                    display: inline-block;
                }

                .nav-item-has-submenu.open .submenu-caret {
                    transform: rotate(180deg);
                }

                @media (prefers-color-scheme: dark) {
                    .nav-submenu {
                        background: #2d2d2d;
                        border: 1px solid rgba(255, 255, 255, 0.08);
                        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
                    }
                }

                /* Éléments internes du sous-menu */
                .submenu-item {
                    display: flex;
                    align-items: center;
                    padding: 10px 16px;
                    text-decoration: none;
                    color: #1f1f1f;
                    font-size: 14px;
                    font-family: system-ui, sans-serif;
                    transition: background 0.15s;
                }

                @media (prefers-color-scheme: dark) {
                    .submenu-item {
                        color: #e3e3e3;
                    }
                }

                .submenu-item:hover,
                .submenu-item.active {
                    background: rgba(0, 0, 0, 0.05);
                }

                @media (prefers-color-scheme: dark) {

                    .submenu-item:hover,
                    .submenu-item.active {
                        background: rgba(255, 255, 255, 0.08);
                    }
                }

                .submenu-icon {
                    margin-right: 10px;
                    font-size: 16px;
                }

                .submenu-divider {
                    border: 0;
                    border-top: 1px solid rgba(0, 0, 0, 0.08);
                    margin: 6px 0;
                }

                @media (prefers-color-scheme: dark) {
                    .submenu-divider {
                        border-top-color: rgba(255, 255, 255, 0.1);
                    }
                }

                .logout-item {
                    color: #dc3545;
                }

                /* Rouge pour la déconnexion */

                /* Conteneur pour aligner le texte et la flèche sur la même ligne */
                .nav-label-wrapper {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 4px;
                    /* Espace entre le texte et la flèche */
                }

                /* Style de la petite flèche */
                .submenu-caret {
                    font-size: 8px;
                    /* Plus discret que le texte */
                    color: #757575;
                    /* Couleur grise par défaut */
                    transition: transform 0.2s ease;
                    /* Animation fluide de rotation */
                    display: inline-block;
                }

                @media (prefers-color-scheme: dark) {
                    .submenu-caret {
                        color: #aaa;
                    }
                }

                /* Changement de couleur au survol/actif de l'onglet parent */
                .nav-item-has-submenu:hover .submenu-caret {
                    color: #0b57d0;
                    /* S'adapte à la couleur active de votre thème */
                }

                /* ROTATION AUTOMATIQUE : Quand le sous-menu a la classe 'open' */
                .nav-item-has-submenu:has(.nav-submenu.open) .submenu-caret {
                    transform: rotate(180deg);
                    color: #0b57d0;
                }

                /* ==========================================================================
       INTERFACES COMPOSANTS STANDALONE (MATERIAL YOU)
       ========================================================================== */

                .pwa-app-dashboard {
                    width: 100%;
                    max-width: 420px;
                    margin: 0 auto 24px auto;
                    display: flex;
                    flex-direction: column;
                    gap: 14px;
                    font-family: 'Outfit', sans-serif;
                    box-sizing: border-box;
                    padding: 0 8px;
                }

                /* --- Barre d'état satellite --- */
                .pwa-status-bar {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    background: rgba(11, 87, 208, 0.06);
                    border: 1px solid rgba(11, 87, 208, 0.15);
                    padding: 8px 14px;
                    border-radius: 12px;
                }

                .pwa-status-bar p {
                    margin: 0;
                    font-size: 12px;
                    font-weight: 600;
                    color: var(--text-active);
                    letter-spacing: 0.2px;
                }

                .pulse-indicator {
                    width: 7px;
                    height: 7px;
                    background-color: #198754;
                    border-radius: 50%;
                    box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7);
                    animation: pwaPulse 1.8s infinite;
                }

                @keyframes pwaPulse {
                    0% {
                        transform: scale(0.95);
                        box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7);
                    }

                    70% {
                        transform: scale(1);
                        box-shadow: 0 0 0 6px rgba(25, 135, 84, 0);
                    }

                    100% {
                        transform: scale(0.95);
                        box-shadow: 0 0 0 0 rgba(25, 135, 84, 0);
                    }
                }

                /* --- Header Top --- */
                .pwa-header-top {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 6px 4px;
                }

                .pwa-brand {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                }

                .pwa-logo {
                    width: 42px;
                    height: 42px;
                    object-fit: contain;
                }

                .pwa-brand-text h1 {
                    margin: 0;
                    font-size: 18px;
                    font-weight: 800;
                    letter-spacing: 1px;
                    color: var(--text-main);
                }

                .pwa-sub-brand {
                    margin: 0;
                    font-size: 11px;
                    color: var(--text-inactive);
                    font-weight: 500;
                    text-transform: uppercase;
                }

                /* Badge Utilisateur connecté */
                .pwa-user-badge {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    background: rgba(255, 255, 255, 0.07);
                    border: 1px solid var(--border-color);
                    padding: 6px 14px;
                    border-radius: 100px;
                }

                .lightmode .pwa-user-badge {
                    background: rgba(0, 0, 0, 0.04);
                }

                .pwa-user-badge i {
                    font-size: 13px;
                    color: var(--text-active);
                }

                .pwa-user-badge span {
                    font-size: 13px;
                    font-weight: 600;
                    color: var(--text-main);
                }

                /* --- Grille d'informations (Dashboard) --- */
                .pwa-info-grid {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 10px;
                    width: 100%;
                }

                .pwa-info-card {
                    background: var(--bg-nav);
                    border: 1px solid var(--border-color);
                    box-shadow: var(--shadow-header);
                    border-radius: 16px;
                    padding: 12px;
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    box-sizing: border-box;
                }

                .pwa-card-icon {
                    font-size: 16px;
                    color: var(--text-inactive);
                    background: rgba(255, 255, 255, 0.05);
                    width: 32px;
                    height: 32px;
                    border-radius: 10px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .lightmode .pwa-card-icon {
                    background: rgba(0, 0, 0, 0.03);
                }

                .pwa-card-meta {
                    display: flex;
                    flex-direction: column;
                }

                .pwa-meta-label {
                    font-size: 10px;
                    font-weight: 600;
                    color: var(--text-inactive);
                    text-transform: uppercase;
                    letter-spacing: 0.3px;
                }

                .pwa-meta-value {
                    font-size: 12px;
                    font-weight: 700;
                    color: var(--text-main);
                    margin-top: 1px;
                }

                /* --- BOUTON RETOUR FLÉCHÉ (STYLE NATIF ANDROID) --- */
                .pwa-back-button {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    color: var(--text-main);
                    text-decoration: none;
                    font-size: 16px;
                    transition: background-color 0.2s ease, transform 0.1s ease;
                    margin-right: 4px;
                    /* Espace léger avant le logo */
                }

                /* Effet de retour tactile au clic / survol */
                .pwa-back-button:hover,
                .pwa-back-button:active {
                    background-color: rgba(255, 255, 255, 0.1);
                }

                .lightmode .pwa-back-button:hover,
                .lightmode .pwa-back-button:active {
                    background-color: rgba(0, 0, 0, 0.06);
                }

                /* Léger effet d'enfoncement lors de l'appui sur écran tactile */
                .pwa-back-button:active {
                    transform: scale(0.92);
                }

                /* --- COMPOSANTS APPLICATIFS SUPPLEMENTAIRES --- */

                /* Mise en avant légère de la carte des visiteurs */
                .pwa-info-card.pwa-card-highlight {
                    border-color: rgba(11, 87, 208, 0.3);
                    background: linear-gradient(145deg, var(--bg-nav), rgba(11, 87, 208, 0.03));
                }

                .pwa-info-card.pwa-card-highlight .pwa-card-icon i {
                    color: var(--text-active);
                }

                /* Titre de Section des Réglages */
                .pwa-settings-section {
                    margin-top: 6px;
                }

                .pwa-section-title {
                    font-size: 11px;
                    font-weight: 700;
                    text-transform: uppercase;
                    color: var(--text-inactive);
                    letter-spacing: 0.6px;
                    margin: 0 0 8px 4px;
                }

                /* Conteneur de la liste style iOS/Android moderne */
                .pwa-settings-list {
                    background: var(--bg-nav);
                    border: 1px solid var(--border-color);
                    box-shadow: var(--shadow-header);
                    border-radius: 16px;
                    overflow: hidden;
                    display: flex;
                    flex-direction: column;
                }

                /* Éléments de liste */
                .pwa-settings-item {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 14px 16px;
                    color: var(--text-main);
                    text-decoration: none;
                    font-size: 13px;
                    font-weight: 600;
                    border-bottom: 1px solid var(--border-color);
                    transition: background-color 0.15s ease;
                    box-sizing: border-box;
                    cursor: pointer;
                    -webkit-tap-highlight-color: transparent;
                }

                .pwa-settings-item:last-child {
                    border-bottom: none;
                }

                .pwa-settings-item:hover,
                .pwa-settings-item:active {
                    background-color: rgba(255, 255, 255, 0.03);
                }

                .lightmode .pwa-settings-item:hover,
                .lightmode .pwa-settings-item:active {
                    background-color: rgba(0, 0, 0, 0.02);
                }

                .pwa-item-main {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                }

                .pwa-item-main i {
                    font-size: 16px;
                    color: var(--text-inactive);
                    width: 20px;
                    text-align: center;
                }

                /* Icône de flèche pour les liens sortants */
                .pwa-list-chevron {
                    font-size: 11px;
                    color: var(--text-inactive);
                    opacity: 0.4;
                }

                /* Track UI du Switch de Thème */
                .pwa-switch-ui-track {
                    width: 44px;
                    height: 22px;
                    background: rgba(255, 255, 255, 0.07);
                    border: 1px solid var(--border-color);
                    border-radius: 100px;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 0 5px;
                    box-sizing: border-box;
                }

                .lightmode .pwa-switch-ui-track {
                    background: rgba(0, 0, 0, 0.04);
                }

                .pwa-switch-ui-track i {
                    font-size: 9px;
                }

                .sun-icon {
                    color: #ffb703;
                }

                .moon-icon {
                    color: #a2d2ff;
                }

                /* Mentions Légales de fin d'application */
                .pwa-app-legal {
                    text-align: center;
                    margin-top: 14px;
                    padding: 0 8px;
                }

                .pwa-legal-tagline {
                    margin: 0 0 4px 0;
                    font-size: 10px;
                    color: var(--text-inactive);
                    font-weight: 500;
                }

                .pwa-legal-credits {
                    margin: 0;
                    font-size: 9px;
                    color: var(--text-inactive);
                    opacity: 0.6;
                }

                .pwa-legal-credits strong {
                    color: var(--text-main);
                    font-weight: 600;
                }

                /* --- BOUTON DE DECLENCHEMENT "À PROPOS" --- */
                /* Zone qui englobe le bouton pour lui donner un point d'ancrage */
                .pwa-sticky-trigger-zone {
                    display: flex;
                    justify-content: center;
                    width: 100%;
                    margin-top: 20px;
                    /* Permet de définir à quel moment le bouton s'active par rapport au bas de la page */
                    height: 200px;
                }

                /* Le bouton devient flottant UNIQUEMENT quand on approche du bas */
                .pwa-about-trigger {
                    position: sticky;
                    bottom: 20px;
                    /* Distance par rapport au bas de l'écran une fois "bloqué" */

                    /* Design de ton bouton circulaire */
                    width: 48px;
                    height: 48px;
                    background: var(--bg-nav);
                    border: 1px solid var(--border-color);
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
                    border-radius: 50%;
                    color: var(--text-inactive);

                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    z-index: 99;
                    transition: transform 0.2s ease, color 0.2s ease, background-color 0.2s ease;
                    -webkit-tap-highlight-color: transparent;
                }

                /* Effets classiques */
                .pwa-about-trigger:hover {
                    color: var(--text-active);
                }

                .pwa-about-trigger:active {
                    transform: scale(0.92);
                    background-color: rgba(255, 255, 255, 0.05);
                }

                .lightmode .pwa-about-trigger:active {
                    background-color: rgba(0, 0, 0, 0.03);
                }

                /* --- LA FENÊTRE DIALOGUE APPLICATION --- */
                .pwa-about-dialog {
                    border: none;
                    padding: 0;
                    background: transparent;
                    max-width: 420px;
                    width: 100%;
                    overflow: visible;
                }

                /* Arrière-plan flouté d'application mobile */
                .pwa-about-dialog::backdrop {
                    background: rgba(0, 0, 0, 0.6);
                    backdrop-filter: blur(5px);
                    -webkit-backdrop-filter: blur(5px);
                }

                /* Adaptation du container à l'intérieur de la modale */
                .pwa-about-dialog .pwa-app-dashboard {
                    background: #0d1117;
                    /* Mettre la couleur de fond sombre par défaut de votre app */
                    border: 1px solid var(--border-color);
                    border-radius: 24px;
                    padding: 16px 12px;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
                    margin: 0 auto;
                }

                .lightmode .pwa-about-dialog .pwa-app-dashboard {
                    background: #ffffff;
                    /* Mettre la couleur de fond claire par défaut de votre app */
                }

                /* Bouton Fermer (Croix en haut à droite de la barre d'état) */
                .pwa-status-bar {
                    position: relative;
                }

                .pwa-close-modal {
                    position: absolute;
                    right: 12px;
                    width: 22px;
                    height: 22px;
                    background: rgba(255, 255, 255, 0.1);
                    border: none;
                    border-radius: 50%;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: var(--text-main);
                }

                .lightmode .pwa-close-modal {
                    background: rgba(0, 0, 0, 0.06);
                }

                .pwa-close-modal::before {
                    content: '✕';
                    font-size: 10px;
                    font-weight: bold;
                }

                .info-bar {
                    display: none !important;
                }

                .header-top {
                    display: none !important;
                }

                .footer-glass {
                    display: none !important;
                }

                .pwa-app {
                    display: block;
                }

                /* --- RESPONSIVE MOBILE ÉCRANS COMPACTS (ex: iPhone SE, anciens terminaux) --- */
                @media (max-width: 350px) {
                    .nav-label {
                        font-size: 9px;
                    }

                    .nav-icon {
                        font-size: 18px;
                    }
                }
            }

            @keyframes pulseButton {
                0% {
                    transform: scale(1);
                    box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.5);
                }

                70% {
                    transform: scale(1.03);
                    box-shadow: 0 0 0 8px rgba(239, 68, 68, 0);
                }

                100% {
                    transform: scale(1);
                    box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
                }
            }

            .pulse-live-btn {
                animation: pulseButton 2s infinite;
            }

            :root {
                --player-accent: #3b82f6;
                --player-bg: rgba(15, 23, 42, 0.85);
            }

            .astro-player {
                position: fixed;
                bottom: 30px;
                right: 30px;
                z-index: 2000;
                display: flex;
                align-items: center;
                background: var(--player-bg) !important;
                backdrop-filter: blur(15px);
                border-radius: 50px !important;
                border: 1px solid rgba(59, 130, 246, 0.3) !important;
                padding: 6px;
                transition: all 0.4s ease;
            }

            .player-main {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 0 5px;
                z-index: 2;
            }

            .info-slide {
                width: 0;
                overflow: hidden;
                white-space: nowrap;
                transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .info-slide.open {
                width: 220px;
            }

            .info-content {
                padding: 0 15px;
                font-size: 0.7rem;
                color: #e2e8f0;
                font-family: 'Outfit', sans-serif;
            }

            .info-content a {
                color: var(--player-accent);
                text-decoration: none;
            }

            .play-btn,
            .info-btn {
                background: rgba(59, 130, 246, 0.1);
                border: 1px solid rgba(59, 130, 246, 0.5);
                color: white;
                cursor: pointer;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: 0.3s;
            }

            .play-btn {
                width: 35px;
                height: 35px;
            }

            .info-btn {
                width: 25px;
                height: 25px;
                font-family: 'Space Mono', monospace;
                font-size: 0.7rem;
                font-weight: bold;
            }

            .play-btn:hover,
            .info-btn:hover {
                background: var(--player-accent);
                box-shadow: 0 0 10px rgba(59, 130, 246, 0.5);
            }

            .sound-bars {
                display: flex;
                align-items: flex-end;
                gap: 2px;
                height: 12px;
                margin-left: 8px;
            }

            .bar {
                width: 2px;
                height: 3px;
                background: var(--player-accent);
                border-radius: 1px;
                transition: 0.2s;
            }

            .playing .bar {
                animation: bounce 0.8s ease infinite alternate;
            }

            .playing .bar:nth-child(2) {
                animation-delay: 0.2s;
            }

            .playing .bar:nth-child(3) {
                animation-delay: 0.4s;
            }

            @keyframes bounce {
                from {
                    height: 2px;
                }

                to {
                    height: 12px;
                }
            }

            .btn-icon {
                width: 0;
                height: 0;
                border-top: 5px solid transparent;
                border-bottom: 5px solid transparent;
                border-left: 8px solid white;
                margin-left: 2px;
                transition: 0.2s;
            }

            .playing .btn-icon {
                width: 8px;
                height: 8px;
                border: none;
                border-left: 2px solid white;
                border-right: 2px solid white;
                margin-left: 0;
            }

            /* ==========================================================================
       1. VARIABLES DE THÈME (STYLE FLOTTANT ANDROID MATERIAL YOU)
       ========================================================================== */
            :root {
                /* --- Mode Clair (Couleurs de surface Android) --- */
                --bg-header: rgba(243, 243, 244, 0.90);
                /* Opacité 90% teintée Android 14 */
                --bg-nav: rgba(243, 243, 244, 0.88);
                /* Transparence équilibrée native */
                --border-color: rgba(0, 0, 0, 0.06);
                /* Délimitation subtile */
                --text-inactive: #444746;
                /* Teinte "On-Surface Variant" Android */
                --text-active: #0b57d0;
                /* Bleu primaire standard Material 3 */
                --shadow-header: 0 1px 2px rgba(0, 0, 0, 0.05);
                --shadow-nav: 0 2px 6px rgba(0, 0, 0, 0.08);
            }

            @media (prefers-color-scheme: dark) {
                :root {
                    /* --- Mode Sombre (Couleurs de surface Dark Android) --- */
                    --bg-header: rgba(31, 31, 31, 0.90);
                    --bg-nav: rgba(31, 31, 31, 0.88);
                    --border-color: rgba(255, 255, 255, 0.07);
                    --text-inactive: #c4c7c5;
                    --text-active: #a8c7fa;
                    /* Bleu clair adaptatif écran sombre */
                    --shadow-header: 0 1px 2px rgba(0, 0, 0, 0.2);
                    --shadow-nav: 0 4px 12px rgba(0, 0, 0, 0.3);
                }
            }

            /* ==========================================================================
       2. EN-TÊTE DE L'APPLICATION
       ========================================================================== */
            .app-header {
                position: sticky;
                top: 0;
                height: 60px;
                background: var(--bg-header);
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0 20px;
                box-shadow: var(--shadow-header);
                border-bottom: 1px solid var(--border-color);
                z-index: 900;
                transition: background 0.25s ease, border-color 0.25s ease;
            }

            .header-icon-btn {
                background: none;
                border: none;
                font-size: 24px;
                color: var(--text-main);
                padding: 5px;
                width: auto;
                cursor: pointer;
                transition: color 0.25s ease;
            }

            .app-title {
                font-weight: 700;
                font-size: 18px;
                letter-spacing: -0.5px;
                color: var(--text-main);
                transition: color 0.25s ease;
            }

            .header-install-btn {
                width: auto;
                padding: 8px 14px;
                font-size: 13px;
                border-radius: 12px;
                background-color: var(--text-active);
                color: white;
                border: none;
                cursor: pointer;
                transition: background-color 0.25s ease;
            }

            /* ==========================================================================
       3. GESTION PAR DÉFAUT (NAVIGATEURS WEB CLASSIQUES)
       ========================================================================== */
            .bottom-nav {
                display: none !important;
                /* Masqué sur navigateur de bureau */
            }

            .container-mobile {
                padding-bottom: 30px;
                /* Espace standard de fin de page */
                transition: padding 0.25s ease;
            }

            /* --- CONTRÔLE D'AFFICHAGE DE L'INTERFACE DE L'APPLICATION --- */

            /* 1. Caché par défaut sur tous les navigateurs web classiques */
            .pwa-app-dashboard {
                display: none !important;
            }

            /* 2. Affiché uniquement si l'application est lancée en mode Standalone (PWA installée) */
            @media (display-mode: standalone),
            (display-mode: fullscreen),
            (display-mode: minimal-ui) {
                .pwa-app-dashboard {
                    display: flex !important;
                    flex-direction: column;
                    gap: 14px;
                    width: 100%;
                    max-width: 420px;
                    margin: 0 auto 24px auto;
                    font-family: 'Outfit', sans-serif;
                    box-sizing: border-box;
                    padding: 0 8px;
                }
            }

            /* Compatibilité additionnelle spécifique aux anciens systèmes iOS (Safari PWAs) */
            @media (navigator-standalone: true) {
                .pwa-app-dashboard {
                    display: flex !important;
                    flex-direction: column;
                }
            }

            /* ==========================================================================
       4. COMPORTEMENT EXCLUSIF : MODE APP INSTALLÉE (STANDALONE)
       ========================================================================== */
            @media (display-mode: standalone) {

                .bottom-nav {
                    display: grid !important;
                    grid-template-columns: repeat(5, 1fr);
                    position: fixed;
                    bottom: 16px;
                    left: 12px;
                    right: 12px;
                    height: 60px;

                    /* Intégration du rendu Android sans flou */
                    background: var(--bg-nav);
                    border: 1px solid var(--border-color);
                    box-shadow: var(--shadow-nav);
                    border-radius: 24px;
                    /* Format "pilule flottante" Material You */

                    align-items: center;
                    z-index: 1000;
                    padding: 0 4px;
                    box-sizing: border-box;
                    pointer-events: auto;
                    transition: background 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
                }

                .nav-item {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    text-decoration: none;
                    color: var(--text-inactive);
                    font-size: 10px;
                    font-weight: 500;
                    /* Medium font-weight natif Android */
                    height: 100%;
                    width: 100%;
                    padding: 4px 0;
                    box-sizing: border-box;
                    transition: color 0.15s ease, font-weight 0.15s ease;
                }

                .nav-icon {
                    font-size: 20px;
                    margin-bottom: 3px;
                    line-height: 1;
                    transition: transform 0.2s cubic-bezier(0.2, 0, 0, 1);
                    /* Courbe haptique Android */
                }

                .nav-label {
                    display: block;
                    font-size: 10px;
                    letter-spacing: 0.1px;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    max-width: 92%;
                }

                /* --- ÉTATS & RETOURS EFFETS TACTILES --- */
                .nav-item:active .nav-icon {
                    transform: scale(0.9);
                    /* Feedback haptique visuel doux Android */
                }

                .nav-item.active {
                    color: var(--text-active);
                    font-weight: 700;
                    /* Met en valeur l'élément actif */
                }

                /* --- ADAPTATION STRUCTURELLE DE L'APPLICATION --- */
                .container-mobile {
                    padding-bottom: 100px !important;
                    box-sizing: border-box;
                }

                .navbar,
                #playerContainer {
                    display: none !important;
                }

                /* ==========================================================================
       SOUS-MENU CONFIGURATION COMPTE (STYLE FLOTTANT ANDROID MATERIAL YOU)
       ========================================================================== */

                /* Positionnement du bouton parent */
                .nav-item-has-submenu {
                    position: relative;
                }

                /* Le Sous-Menu (Format carte arrondie, sans débordement d'écran) */
                .nav-submenu {
                    position: absolute;
                    bottom: 72px;
                    /* Placé idéalement juste au-dessus de la bottom-nav */

                    /* CORRECTION ANGLE : Aligné sur la droite de l'onglet avec un retrait de sécurité */
                    right: 0;
                    margin-right: 8px;
                    width: 180px;

                    /* Design & Couleurs adaptatives */
                    background: var(--bg-nav);
                    border: 1px solid var(--border-color);
                    box-shadow: var(--shadow-nav);
                    border-radius: 20px;
                    /* Coins généreusement arrondis style Material 3 */
                    padding: 6px;
                    z-index: 1001;
                    box-sizing: border-box;

                    /* GESTION DE L'ANIMATION (Sans display: none pour préserver la fluidité) */
                    visibility: hidden;
                    opacity: 0;
                    transform: translateY(12px) scale(0.96);
                    /* Effet d'apparition du bas vers le haut */
                    transition: transform 0.28s cubic-bezier(0.2, 0, 0, 1),
                        opacity 0.22s ease,
                        visibility 0.22s ease;
                }

                /* ÉTAT ACTIF : Classe injectée par le JavaScript à l'ouverture */
                .nav-submenu.open {
                    visibility: visible;
                    opacity: 1;
                    transform: translateY(0) scale(1);
                    /* Rendu stable et centré sur sa zone */
                }

                /* Rotation fluide de la flèche (caret) */
                .submenu-caret {
                    font-size: 8px;
                    color: inherit;
                    transition: transform 0.25s cubic-bezier(0.2, 0, 0, 1);
                    display: inline-block;
                }

                .nav-item-has-submenu.open .submenu-caret {
                    transform: rotate(180deg);
                }

                @media (prefers-color-scheme: dark) {
                    .nav-submenu {
                        background: #2d2d2d;
                        border: 1px solid rgba(255, 255, 255, 0.08);
                        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.4);
                    }
                }

                /* Éléments internes du sous-menu */
                .submenu-item {
                    display: flex;
                    align-items: center;
                    padding: 10px 16px;
                    text-decoration: none;
                    color: #1f1f1f;
                    font-size: 14px;
                    font-family: system-ui, sans-serif;
                    transition: background 0.15s;
                }

                @media (prefers-color-scheme: dark) {
                    .submenu-item {
                        color: #e3e3e3;
                    }
                }

                .submenu-item:hover,
                .submenu-item.active {
                    background: rgba(0, 0, 0, 0.05);
                }

                @media (prefers-color-scheme: dark) {

                    .submenu-item:hover,
                    .submenu-item.active {
                        background: rgba(255, 255, 255, 0.08);
                    }
                }

                .submenu-icon {
                    margin-right: 10px;
                    font-size: 16px;
                }

                .submenu-divider {
                    border: 0;
                    border-top: 1px solid rgba(0, 0, 0, 0.08);
                    margin: 6px 0;
                }

                @media (prefers-color-scheme: dark) {
                    .submenu-divider {
                        border-top-color: rgba(255, 255, 255, 0.1);
                    }
                }

                .logout-item {
                    color: #dc3545;
                }

                /* Rouge pour la déconnexion */

                /* Conteneur pour aligner le texte et la flèche sur la même ligne */
                .nav-label-wrapper {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 4px;
                    /* Espace entre le texte et la flèche */
                }

                /* Style de la petite flèche */
                .submenu-caret {
                    font-size: 8px;
                    /* Plus discret que le texte */
                    color: #757575;
                    /* Couleur grise par défaut */
                    transition: transform 0.2s ease;
                    /* Animation fluide de rotation */
                    display: inline-block;
                }

                @media (prefers-color-scheme: dark) {
                    .submenu-caret {
                        color: #aaa;
                    }
                }

                /* Changement de couleur au survol/actif de l'onglet parent */
                .nav-item-has-submenu:hover .submenu-caret {
                    color: #0b57d0;
                    /* S'adapte à la couleur active de votre thème */
                }

                /* ROTATION AUTOMATIQUE : Quand le sous-menu a la classe 'open' */
                .nav-item-has-submenu:has(.nav-submenu.open) .submenu-caret {
                    transform: rotate(180deg);
                    color: #0b57d0;
                }

                /* ==========================================================================
       INTERFACES COMPOSANTS STANDALONE (MATERIAL YOU)
       ========================================================================== */

                .pwa-app-dashboard {
                    width: 100%;
                    max-width: 420px;
                    margin: 0 auto 24px auto;
                    display: flex;
                    flex-direction: column;
                    gap: 14px;
                    font-family: 'Outfit', sans-serif;
                    box-sizing: border-box;
                    padding: 0 8px;
                }

                /* --- Barre d'état satellite --- */
                .pwa-status-bar {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    background: rgba(11, 87, 208, 0.06);
                    border: 1px solid rgba(11, 87, 208, 0.15);
                    padding: 8px 14px;
                    border-radius: 12px;
                }

                .pwa-status-bar p {
                    margin: 0;
                    font-size: 12px;
                    font-weight: 600;
                    color: var(--text-active);
                    letter-spacing: 0.2px;
                }

                .pulse-indicator {
                    width: 7px;
                    height: 7px;
                    background-color: #198754;
                    border-radius: 50%;
                    box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7);
                    animation: pwaPulse 1.8s infinite;
                }

                @keyframes pwaPulse {
                    0% {
                        transform: scale(0.95);
                        box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.7);
                    }

                    70% {
                        transform: scale(1);
                        box-shadow: 0 0 0 6px rgba(25, 135, 84, 0);
                    }

                    100% {
                        transform: scale(0.95);
                        box-shadow: 0 0 0 0 rgba(25, 135, 84, 0);
                    }
                }

                /* --- Header Top --- */
                .pwa-header-top {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 6px 4px;
                }

                .pwa-brand {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                }

                .pwa-logo {
                    width: 42px;
                    height: 42px;
                    object-fit: contain;
                }

                .pwa-brand-text h1 {
                    margin: 0;
                    font-size: 18px;
                    font-weight: 800;
                    letter-spacing: 1px;
                    color: var(--text-main);
                }

                .pwa-sub-brand {
                    margin: 0;
                    font-size: 11px;
                    color: var(--text-inactive);
                    font-weight: 500;
                    text-transform: uppercase;
                }

                /* Badge Utilisateur connecté */
                .pwa-user-badge {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    background: rgba(255, 255, 255, 0.07);
                    border: 1px solid var(--border-color);
                    padding: 6px 14px;
                    border-radius: 100px;
                }

                .lightmode .pwa-user-badge {
                    background: rgba(0, 0, 0, 0.04);
                }

                .pwa-user-badge i {
                    font-size: 13px;
                    color: var(--text-active);
                }

                .pwa-user-badge span {
                    font-size: 13px;
                    font-weight: 600;
                    color: var(--text-main);
                }

                /* --- Grille d'informations (Dashboard) --- */
                .pwa-info-grid {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 10px;
                    width: 100%;
                }

                .pwa-info-card {
                    background: var(--bg-nav);
                    border: 1px solid var(--border-color);
                    box-shadow: var(--shadow-header);
                    border-radius: 16px;
                    padding: 12px;
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    box-sizing: border-box;
                }

                .pwa-card-icon {
                    font-size: 16px;
                    color: var(--text-inactive);
                    background: rgba(255, 255, 255, 0.05);
                    width: 32px;
                    height: 32px;
                    border-radius: 10px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .lightmode .pwa-card-icon {
                    background: rgba(0, 0, 0, 0.03);
                }

                .pwa-card-meta {
                    display: flex;
                    flex-direction: column;
                }

                .pwa-meta-label {
                    font-size: 10px;
                    font-weight: 600;
                    color: var(--text-inactive);
                    text-transform: uppercase;
                    letter-spacing: 0.3px;
                }

                .pwa-meta-value {
                    font-size: 12px;
                    font-weight: 700;
                    color: var(--text-main);
                    margin-top: 1px;
                }

                /* --- BOUTON RETOUR FLÉCHÉ (STYLE NATIF ANDROID) --- */
                .pwa-back-button {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    color: var(--text-main);
                    text-decoration: none;
                    font-size: 16px;
                    transition: background-color 0.2s ease, transform 0.1s ease;
                    margin-right: 4px;
                    /* Espace léger avant le logo */
                }

                /* Effet de retour tactile au clic / survol */
                .pwa-back-button:hover,
                .pwa-back-button:active {
                    background-color: rgba(255, 255, 255, 0.1);
                }

                .lightmode .pwa-back-button:hover,
                .lightmode .pwa-back-button:active {
                    background-color: rgba(0, 0, 0, 0.06);
                }

                /* Léger effet d'enfoncement lors de l'appui sur écran tactile */
                .pwa-back-button:active {
                    transform: scale(0.92);
                }

                /* --- COMPOSANTS APPLICATIFS SUPPLEMENTAIRES --- */

                /* Mise en avant légère de la carte des visiteurs */
                .pwa-info-card.pwa-card-highlight {
                    border-color: rgba(11, 87, 208, 0.3);
                    background: linear-gradient(145deg, var(--bg-nav), rgba(11, 87, 208, 0.03));
                }

                .pwa-info-card.pwa-card-highlight .pwa-card-icon i {
                    color: var(--text-active);
                }

                /* Titre de Section des Réglages */
                .pwa-settings-section {
                    margin-top: 6px;
                }

                .pwa-section-title {
                    font-size: 11px;
                    font-weight: 700;
                    text-transform: uppercase;
                    color: var(--text-inactive);
                    letter-spacing: 0.6px;
                    margin: 0 0 8px 4px;
                }

                /* Conteneur de la liste style iOS/Android moderne */
                .pwa-settings-list {
                    background: var(--bg-nav);
                    border: 1px solid var(--border-color);
                    box-shadow: var(--shadow-header);
                    border-radius: 16px;
                    overflow: hidden;
                    display: flex;
                    flex-direction: column;
                }

                /* Éléments de liste */
                .pwa-settings-item {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 14px 16px;
                    color: var(--text-main);
                    text-decoration: none;
                    font-size: 13px;
                    font-weight: 600;
                    border-bottom: 1px solid var(--border-color);
                    transition: background-color 0.15s ease;
                    box-sizing: border-box;
                    cursor: pointer;
                    -webkit-tap-highlight-color: transparent;
                }

                .pwa-settings-item:last-child {
                    border-bottom: none;
                }

                .pwa-settings-item:hover,
                .pwa-settings-item:active {
                    background-color: rgba(255, 255, 255, 0.03);
                }

                .lightmode .pwa-settings-item:hover,
                .lightmode .pwa-settings-item:active {
                    background-color: rgba(0, 0, 0, 0.02);
                }

                .pwa-item-main {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                }

                .pwa-item-main i {
                    font-size: 16px;
                    color: var(--text-inactive);
                    width: 20px;
                    text-align: center;
                }

                /* Icône de flèche pour les liens sortants */
                .pwa-list-chevron {
                    font-size: 11px;
                    color: var(--text-inactive);
                    opacity: 0.4;
                }

                /* Track UI du Switch de Thème */
                .pwa-switch-ui-track {
                    width: 44px;
                    height: 22px;
                    background: rgba(255, 255, 255, 0.07);
                    border: 1px solid var(--border-color);
                    border-radius: 100px;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: 0 5px;
                    box-sizing: border-box;
                }

                .lightmode .pwa-switch-ui-track {
                    background: rgba(0, 0, 0, 0.04);
                }

                .pwa-switch-ui-track i {
                    font-size: 9px;
                }

                .sun-icon {
                    color: #ffb703;
                }

                .moon-icon {
                    color: #a2d2ff;
                }

                /* Mentions Légales de fin d'application */
                .pwa-app-legal {
                    text-align: center;
                    margin-top: 14px;
                    padding: 0 8px;
                }

                .pwa-legal-tagline {
                    margin: 0 0 4px 0;
                    font-size: 10px;
                    color: var(--text-inactive);
                    font-weight: 500;
                }

                .pwa-legal-credits {
                    margin: 0;
                    font-size: 9px;
                    color: var(--text-inactive);
                    opacity: 0.6;
                }

                .pwa-legal-credits strong {
                    color: var(--text-main);
                    font-weight: 600;
                }

                /* --- BOUTON DE DECLENCHEMENT "À PROPOS" --- */
                /* Zone qui englobe le bouton pour lui donner un point d'ancrage */
                .pwa-sticky-trigger-zone {
                    display: flex;
                    justify-content: center;
                    width: 100%;
                    margin-top: 20px;
                    /* Permet de définir à quel moment le bouton s'active par rapport au bas de la page */
                    height: 200px;
                }

                /* Le bouton devient flottant UNIQUEMENT quand on approche du bas */
                .pwa-about-trigger {
                    position: sticky;
                    bottom: 20px;
                    /* Distance par rapport au bas de l'écran une fois "bloqué" */

                    /* Design de ton bouton circulaire */
                    width: 48px;
                    height: 48px;
                    background: var(--bg-nav);
                    border: 1px solid var(--border-color);
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
                    border-radius: 50%;
                    color: var(--text-inactive);

                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    z-index: 99;
                    transition: transform 0.2s ease, color 0.2s ease, background-color 0.2s ease;
                    -webkit-tap-highlight-color: transparent;
                }

                /* Effets classiques */
                .pwa-about-trigger:hover {
                    color: var(--text-active);
                }

                .pwa-about-trigger:active {
                    transform: scale(0.92);
                    background-color: rgba(255, 255, 255, 0.05);
                }

                .lightmode .pwa-about-trigger:active {
                    background-color: rgba(0, 0, 0, 0.03);
                }

                /* --- LA FENÊTRE DIALOGUE APPLICATION --- */
                .pwa-about-dialog {
                    border: none;
                    padding: 0;
                    background: transparent;
                    max-width: 420px;
                    width: 100%;
                    overflow: visible;
                }

                /* Arrière-plan flouté d'application mobile */
                .pwa-about-dialog::backdrop {
                    background: rgba(0, 0, 0, 0.6);
                    backdrop-filter: blur(5px);
                    -webkit-backdrop-filter: blur(5px);
                }

                /* Adaptation du container à l'intérieur de la modale */
                .pwa-about-dialog .pwa-app-dashboard {
                    background: #0d1117;
                    /* Mettre la couleur de fond sombre par défaut de votre app */
                    border: 1px solid var(--border-color);
                    border-radius: 24px;
                    padding: 16px 12px;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
                    margin: 0 auto;
                }

                .lightmode .pwa-about-dialog .pwa-app-dashboard {
                    background: #ffffff;
                    /* Mettre la couleur de fond claire par défaut de votre app */
                }

                /* Bouton Fermer (Croix en haut à droite de la barre d'état) */
                .pwa-status-bar {
                    position: relative;
                }

                .pwa-close-modal {
                    position: absolute;
                    right: 12px;
                    width: 22px;
                    height: 22px;
                    background: rgba(255, 255, 255, 0.1);
                    border: none;
                    border-radius: 50%;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: var(--text-main);
                }

                .lightmode .pwa-close-modal {
                    background: rgba(0, 0, 0, 0.06);
                }

                .pwa-close-modal::before {
                    content: '✕';
                    font-size: 10px;
                    font-weight: bold;
                }

                .info-bar {
                    display: none !important;
                }

                .header-top {
                    display: none !important;
                }

                .footer-glass {
                    display: none !important;
                }

                /* --- RESPONSIVE MOBILE ÉCRANS COMPACTS (ex: iPhone SE, anciens terminaux) --- */
                @media (max-width: 350px) {
                    .nav-label {
                        font-size: 9px;
                    }

                    .nav-icon {
                        font-size: 18px;
                    }
                }
            }

        <?php endif; ?>
    </style>
    <?php if (empty($hideSiteHeader) || (!empty($hideSiteHeader) && !empty($newsletter))): ?>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
        <link rel="stylesheet" href="/assets/css/cookie.css">
        <link rel="stylesheet" href="/assets/css/style.css">
        <link rel="stylesheet" href="/assets/css/bootstrap.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js">
        </script>
        <link rel='stylesheet' href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css'>
    <?php endif; ?>
</head>

<body
    class="astro-body <?= !empty($hideSiteHeader) ? 'login-page  ' : '' ?> <?php echo $bodyClass; ?> <?= $isAurora ? 'aurora-theme' : ($isMatrixGrid ? 'matrix-theme' : ($isStarfield ? 'starfield-theme' : ($isDeepSpace ? 'deep-space-theme' : ($isSupernova ? 'supernova-theme' : ($isBlueprint ? 'blueprint-theme' : ''))))) ?>">

    <?php if (empty($hideSiteHeader)): ?>
        <div id="notification-onboarding"
            style="display: none; position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); width: 90%; max-width: 400px; background: #fff; box-shadow: 0 4px 20px rgba(0,0,0,0.15); padding: 20px; border-radius: 12px; z-index: 999999; font-family: sans-serif;">
            <h3 style="margin-top: 0; color: #333;">Activer les notifications ? 🚀</h3>
            <p style="color: #666; font-size: 14px;">Restez informé en temps réel des nouveautés en astronomie et
                météorologie directement sur votre appareil.</p>
            <div style="text-align: right; margin-top: 15px;">
                <button id="btn-refuse-notif"
                    style="background: none; border: none; color: #999; margin-right: 15px; cursor: pointer; font-weight: bold;">Plus
                    tard</button>
                <button id="btn-accept-notif"
                    style="background: #007bff; border: none; color: #fff; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: bold;">Autoriser</button>
            </div>
        </div>

        <div id="star-field"></div>
        <div class="sun"></div>
        <div class="lens-flare"></div>
        <?php if ($isConnected): ?>
            <div id="settings-overlay" class="edit-full-page">
                <div class="container py-5">
                    <div class="d-flex justify-content-between align-items-center mb-5">
                        <a href="#" class="btn btn-outline-light rounded-pill px-4 shadow-sm">
                            <i class="fa-solid fa-arrow-left me-2"></i> Retour
                        </a>
                        <h2 class="text-white mb-0 fw-600">Paramètres du Compte</h2>
                    </div>

                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="glass-container text-start shadow-lg">
                                <form id="settingsForm" method="POST">
                                    <div class="mb-4">
                                        <label class="text-white-50 small fw-bold">NOM D'UTILISATEUR</label>
                                        <input type="text" name="name" class="input-glass"
                                            value="<?= htmlspecialchars($userName) ?>" required>
                                    </div>
                                    <div class="mb-4">
                                        <label class="text-white-50 small fw-bold">ADRESSE EMAIL</label>
                                        <input type="email" name="email" class="input-glass"
                                            value="<?= htmlspecialchars($userEmail) ?>" required>
                                    </div>
                                    <div class="mb-4">
                                        <label class="text-white-50 small fw-bold">NOUVEAU MOT DE PASSE</label>
                                        <input type="password" name="password" class="input-glass" placeholder="••••••••">
                                        <small class="text-white-50">Laissez vide pour conserver l'actuel.</small>
                                    </div>

                                    <button type="button" class="btn-cosmic-glass" data-bs-toggle="modal"
                                        data-bs-target="#confirmModal">
                                        <i class="fa-solid fa-rotate me-2"></i> Synchroniser les données
                                    </button>

                                    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content shadow-lg">
                                                <div class="modal-body text-center p-5">
                                                    <i class="fa-solid fa-triangle-exclamation text-warning mb-4"
                                                        style="font-size: 3rem;"></i>
                                                    <h4 class="text-white mb-3">Attention !</h4>
                                                    <p class="text-white-50 mb-4">La modification de vos informations système
                                                        entraînera une
                                                        <b>déconnexion immédiate</b> pour synchroniser votre terminal.
                                                    </p>
                                                    <div class="d-grid gap-2">
                                                        <button type="submit" name="confirm_update"
                                                            class="btn btn-primary rounded-pill py-2 fw-bold">Confirmer et se
                                                            déconnecter</button>
                                                        <button type="button" class="btn btn-link text-white-50"
                                                            data-bs-dismiss="modal">Annuler</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="info-bar text-center">
            <div class="container">
                <i class="fas fa-satellite me-2"></i> Bienvenue sur Meteastro ! Système opérationnel.
            </div>
        </div>

        <header class="header-top">
            <div class="container d-flex justify-content-center align-items-center">
                <a href="/" class="logo-text d-flex justify-content-between align-items-center">
                    <img src="/assets/images/logo.png" alt="logo" width="40" class="me-3">
                    METEASTRO
                    <?php echo $isConnected ? '<span class="text-primary ms-2 fs-6">| ' . htmlspecialchars($userName) . '</span>' : ''; ?>
                </a>
            </div>
        </header>

        <div class="pwa-app">
            <div class="pwa-status-bar">
                <span class="pulse-indicator"></span>
                <p><i class="fas fa-satellite"></i> Liaison Meteastro : Système opérationnel</p>
            </div>

            <header class="pwa-header-top">
                <div class="pwa-brand">
                    <a href="/" class="pwa-back-button" aria-label="Retour à l'accueil">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <img src="/assets/images/logo.png" alt="Meteastro Logo" class="pwa-logo">
                    <div class="pwa-brand-text">
                        <h1>METEASTRO</h1>
                        <p class="pwa-sub-brand">Station d'Exploration</p>
                    </div>
                </div>

                <?php if ($isConnected): ?>
                    <div class="pwa-user-badge">
                        <i class="fa-solid fa-user-astronaut"></i>
                        <span><?php echo htmlspecialchars($_SESSION['name'] ?? 'Équipage'); ?></span>
                    </div>
                <?php endif; ?>
            </header>
        </div>

        <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
            <div class="container">
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                    aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                        <?= $menuHtml ?>
                    </ul>
                </div>

                <button class="pwa-fab-trigger pwa-browser-view" id="pwa-action-open" title="Installer l'application">
                    <svg viewBox="0 0 24 24">
                        <path d="M5 20h14v-2H5v2zm7-2l5-5h-4V4h-2v7H7l5 5z" />
                    </svg>
                </button>
            </div>
        </nav>

        <div class="pwa-modal-overlay pwa-browser-view" id="pwa-component-modal">
            <main class="pwa-modal-container">
                <div class="pwa-card">
                    <button class="pwa-btn-close" id="pwa-action-close" aria-label="Fermer">
                        <svg viewBox="0 0 24 24" width="16" height="16">
                            <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"
                                fill="none" />
                        </svg>
                    </button>

                    <div class="pwa-logo-wrap">
                        <div class="pwa-logo"><img src="/assets/images/logo.png" alt="Logo"></div>
                    </div>

                    <h2 class="pwa-rainbow-title">Installer Meteastro</h2>

                    <p class="pwa-subtitle">
                        Ajoutez l'application à votre écran d'accueil pour profiter d'une expérience plein écran fluide.
                    </p>

                    <div class="pwa-badge-status" id="pwa-text-status">
                        <span class="pwa-badge-dot"></span>
                        Analyse du système...
                    </div>

                    <div id="pwa-guide-ios" class="pwa-guide" style="display: none;">
                        <ol class="pwa-list-instructions">
                            <li><span class="pwa-step-num">1</span>Ouvrez le menu de partage de Safari <span
                                    class="pwa-inline-icon">⎋</span></li>
                            <li><span class="pwa-step-num">2</span>Faites défiler et sélectionnez <strong>Sur l'écran
                                    d'accueil</strong>
                            </li>
                            <li><span class="pwa-step-num">3</span>Validez en cliquant sur <strong>Ajouter</strong></li>
                        </ol>
                    </div>

                    <div id="pwa-guide-generic" class="pwa-guide" style="display: none;">
                        <ol class="pwa-list-instructions" id="pwa-text-instructions">
                            <li><span class="pwa-step-num">1</span>Ouvrez les options de votre navigateur <span
                                    class="pwa-inline-icon">⋮</span></li>
                            <li><span class="pwa-step-num">2</span>Choisissez <strong>Installer l'application</strong> ou
                                <i>Ajouter à
                                    l'écran d'accueil</i>
                            </li>
                        </ol>
                        <button id="pwa-action-install" class="pwa-btn-install">
                            <span>Installer maintenant</span>
                            <svg viewBox="0 0 24 24" width="18" height="18">
                                <path d="M5 20h14v-2H5v2zm7-2l5-5h-4V4h-2v7H7l5 5z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </main>
        </div>

        <main class="container my-5">
        </main>

        <div class="container-mobile">
        </div>

        <nav class="bottom-nav">
            <a href="/divers/astronomie/astronomie.php"
                class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'astronomie.php') ? 'active' : ''; ?>">
                <span class="nav-icon">🪐</span>
                <span class="nav-label">Astro</span>
            </a>

            <a href="/divers/meteorologie/meteorologie.php"
                class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'meteorologie.php') ? 'active' : ''; ?>">
                <span class="nav-icon">⛈️</span>
                <span class="nav-label">Météo</span>
            </a>

            <a href="/#contacts" data-anchor="contacts"
                class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php' && (strpos($_SERVER['REQUEST_URI'], '#contacts') !== false || isset($_GET['contact']))) ? 'active' : ''; ?>">
                <span class="nav-icon">✉️</span>
                <span class="nav-label">Contact</span>
            </a>

            <?php if (isset($isConnected) && $isConnected === true): ?>
                <a href="/redirect.php"
                    class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'redirect.php') ? 'active' : ''; ?>">
                    <span class="nav-icon">📝</span>
                    <span class="nav-label">Contenus</span>
                </a>

                <div class="nav-item nav-item-has-submenu" id="account-menu-trigger">
                    <span class="nav-icon">👤</span>
                    <div class="nav-label-wrapper">
                        <span class="nav-label">Mon Compte</span>
                        <span class="submenu-caret">▼</span>
                    </div>

                    <div class="nav-submenu" id="account-submenu">
                        <a href="#parametres/"
                            class="submenu-item <?php echo (basename($_SERVER['PHP_SELF']) == '#parametres/') ? 'active' : ''; ?>">
                            <span class="submenu-icon">⚙️</span> Paramètres
                        </a>
                        <hr class="submenu-divider">
                        <a href="/connexion/logout.php" class="submenu-item logout-item">
                            <span class="submenu-icon">🚪</span> Déconnexion
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <a href="/connexion/login.php"
                    class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'login.php') ? 'active' : ''; ?>">
                    <span class="nav-icon">👤</span>
                    <span class="nav-label">Connexion</span>
                </a>

                <a href="/connexion/signup.php"
                    class="nav-item <?php echo (basename($_SERVER['PHP_SELF']) == 'signup.php') ? 'active' : ''; ?>">
                    <span class="nav-icon">👤➕</span>
                    <span class="nav-label">S'inscrire</span>
                </a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
    <?php if (empty($hideSiteHeader)): ?>
        <main class="container-fluid">
            <?= $content ?>
        </main>
    <?php endif; ?>

    <?php if (!empty($hideSiteHeader)): ?>
        <main class="container container-mobile">
            <?= $content ?>
        </main>
    <?php endif; ?>

    <?php if (empty($hideSiteHeader)): ?>
        <script>
            // Protection globale des images
            document.addEventListener('contextmenu', e => {
                if (e.target.tagName === 'IMG') e.preventDefault();
            });
            document.addEventListener('dragstart', e => {
                if (e.target.tagName === 'IMG') e.preventDefault();
            });

            // Gestion du Slide
            function handleNavigation() {
                const overlay = document.getElementById('settings-overlay');
                if (window.location.hash === '#parametres/') {
                    overlay.classList.add('active');
                    document.body.style.overflow = 'hidden';
                } else {
                    overlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            }
            window.addEventListener('hashchange', handleNavigation);
            window.addEventListener('load', handleNavigation);

            // Protection images
            document.addEventListener('contextmenu', e => { if (e.target.tagName === 'IMG') e.preventDefault(); });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const menuTrigger = document.getElementById('account-menu-trigger');
                const submenu = document.getElementById('account-submenu');

                if (menuTrigger && submenu) {
                    // Gestionnaire d'ouverture/fermeture tactile
                    menuTrigger.addEventListener('click', (e) => {
                        // IMPORTANT : Si on clique à l'intérieur du sous-menu, on laisse le comportement normal (le lien fonctionne)
                        if (submenu.contains(e.target)) {
                            return;
                        }

                        // Sinon, c'est qu'on a cliqué sur le bouton principal "Mon Compte", on bascule l'affichage
                        e.stopPropagation();
                        const isOpen = submenu.classList.toggle('open');

                        if (isOpen) {
                            menuTrigger.classList.add('open');
                        } else {
                            menuTrigger.classList.remove('open');
                        }
                    });

                    // Fermeture si clic n'importe où en dehors du composant complet
                    document.addEventListener('click', (e) => {
                        if (!menuTrigger.contains(e.target)) {
                            submenu.classList.remove('open');
                            menuTrigger.classList.remove('open');
                        }
                    });
                }
            });
        </script>
        <div class="astro-player glass-card shadow-lg" id="playerContainer">
            <div class="player-main">
                <div class="sound-bars">
                    <span class="bar"></span>
                    <span class="bar"></span>
                    <span class="bar"></span>
                </div>

                <button id="audioToggle" class="play-btn" title="Play/Pause">
                    <div class="btn-icon" id="audioIcon"></div>
                </button>

                <button id="infoToggle" class="info-btn">i</button>
            </div>

            <div id="extraInfo" class="info-slide">
                <div class="info-content">
                    <strong>Expedition</strong> by Alex-Productions
                    <p class="small mb-1">Promoted by <a href="https://www.chosic.com/free-music/all/"
                            target="_blank">Chosic</a></p>
                    <span class="badge-cc">CC BY 3.0</span>
                </div>
            </div>

            <audio id="bgMusic">
                <source src="/assets/musiques/Expedition-Long-Version-chosic.com_.mp3" type="audio/mpeg">
            </audio>
        </div>

        <footer class="mt-5 py-5 footer-glass">
            <div class="container">
                <div class="row gy-4 align-items-center">

                    <div class="col-md-4 text-center text-md-start">
                        <div class="footer-brand mb-3">
                            <span class="fs-4 fw-bold text-gradient">METEASTRO</span>
                        </div>
                        <p class="text-muted small mb-0">
                            <i class='bx bx-code-alt me-2'></i>Créé par <strong>Adrien Bruyère</strong><br>
                            <i class='bx bx-copyright me-2'></i>Tous droits réservés © <?= date('Y') ?>
                        </p>
                    </div>

                    <?php
                    $version = require 'version.php';
                    $siteVersion = $version['siteVersion'];
                    $appVersion = $siteVersion . '.' . $version['appBuild'];
                    $updated = $version['updated'];
                    $dateAffichee = "{$updated['day']} {$updated['num']} {$updated['month']} {$updated['year']}";
                    ?>
                    <div class="col-md-4 text-center">
                        <div class="stats-card p-3 rounded-4">
                            <p class="small text-muted mb-2">
                                <i class='bx bx-history me-1'></i> Version <?= $siteVersion ?> (<?= $dateAffichee ?>)<br>
                                <i class='bx bx-calendar me-1'></i> Lancé le 8 Avril 2022
                            </p>
                            <div class="visitor-counter py-1 px-3 bg-primary bg-opacity-10 rounded-pill d-inline-block">
                                <span class="small fw-bold text-primary">
                                    <i class='bx bx-group me-1'></i>
                                    <?= $nombreVisite ?> visiteurs
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 text-center text-md-end">
                        <div class="d-flex flex-column align-items-md-end align-items-center gap-3">
                            <div class="darkmode-control" id="switch">
                                <span class="text-muted-extra small">Thème</span>
                                <div class="switch-ui">
                                    <i class="fas fa-sun sun-icon"></i>
                                    <i class="fas fa-moon moon-icon"></i>
                                </div>
                            </div>

                            <div class="social-links h4 mb-0">
                                <a href="https://github.com/adrienb39/meteastro" class="text-muted me-3" title="GitHub"><i
                                        class='bx bxl-github'></i></a>
                                <a href="/#contacts" class="text-muted" title="Contact"><i class='bx bx-envelope'></i></a>
                            </div>
                        </div>
                    </div>

                </div>

                <hr class="my-4 opacity-10">

                <div class="text-center">
                    <p class="small text-muted-extra">
                        Station spatiale Meteastro — Astronomie & Météorologie en temps réel.
                    </p>
                </div>
            </div>
        </footer>
        <div class="pwa-app">
            <div class="pwa-sticky-trigger-zone">
                <button class="pwa-about-trigger" onclick="document.getElementById('pwa-about-modal').showModal()"
                    aria-label="Ouvrir les informations de la station">
                    <div class="pwa-item-main">
                        <i class="fa-solid fa-circle-info"></i>
                    </div>
                </button>
            </div>

            <dialog id="pwa-about-modal" class="pwa-about-dialog">
                <div class="pwa-app-dashboard">

                    <div class="pwa-status-bar">
                        <span class="pulse-indicator"></span>
                        <p><i class="fas fa-satellite"></i> Liaison Meteastro : Système opérationnel</p>
                        <button class="pwa-close-modal" onclick="document.getElementById('pwa-about-modal').close()"
                            aria-label="Fermer"></button>
                    </div>

                    <header class="pwa-header-top">
                        <div class="pwa-brand">
                            <img src="/assets/images/logo.png" alt="Meteastro Logo" class="pwa-logo">
                            <div class="pwa-brand-text">
                                <h1>METEASTRO</h1>
                                <p class="pwa-sub-brand">Station d'Exploration</p>
                            </div>
                        </div>

                        <?php if ($isConnected): ?>
                            <div class="pwa-user-badge">
                                <i class="fa-solid fa-user-astronaut"></i>
                                <span><?php echo htmlspecialchars($_SESSION['name'] ?? 'Équipage'); ?></span>
                            </div>
                        <?php endif; ?>
                    </header>

                    <div class="pwa-info-grid">
                        <div class="pwa-info-card">
                            <div class="pwa-card-icon"><i class="fa-solid fa-code-branch"></i></div>
                            <div class="pwa-card-meta">
                                <span class="pwa-meta-label">Version App</span>
                                <span class="pwa-meta-value" id="pwa-version">Chargement...</span>
                            </div>
                        </div>

                        <div class="pwa-info-card">
                            <div class="pwa-card-icon"><i class="fa-solid fa-server"></i></div>
                            <div class="pwa-card-meta">
                                <span class="pwa-meta-label">Service Worker</span>
                                <span class="pwa-meta-value" id="pwa-sw">Vérification...</span>
                            </div>
                        </div>

                        <div class="pwa-info-card">
                            <div class="pwa-card-icon"><i class="fa-solid fa-database"></i></div>
                            <div class="pwa-card-meta">
                                <span class="pwa-meta-label">Stockage Cache</span>
                                <span class="pwa-meta-value" id="pwa-cache">Calcul...</span>
                            </div>
                        </div>

                        <div class="pwa-info-card">
                            <div class="fa-solid fa-rocket pwa-card-icon"></div>
                            <div class="pwa-card-meta">
                                <span class="pwa-meta-label">Lancement</span>
                                <span class="pwa-meta-value">8 Avril 2022</span>
                            </div>
                        </div>

                        <div class="pwa-info-card pwa-card-highlight">
                            <div class="pwa-card-icon"><i class="fa-solid fa-users"></i></div>
                            <div class="pwa-card-meta">
                                <span class="pwa-meta-label">Fréquentation</span>
                                <span class="pwa-meta-value"><?= $nombreVisite ?></span>
                            </div>
                        </div>

                        <div class="pwa-info-card">
                            <div class="pwa-card-icon"><i class="fa-solid fa-mobile-screen-button"></i></div>
                            <div class="pwa-card-meta">
                                <span class="pwa-meta-label">Environnement</span>
                                <span class="pwa-meta-value">Cockpit App</span>
                            </div>
                        </div>
                    </div>

                    <div class="pwa-settings-section">
                        <h2 class="pwa-section-title">Configuration & Liens</h2>

                        <div class="pwa-settings-list">
                            <div class="pwa-settings-item" id="switch" role="button" aria-label="Changer de thème">
                                <div class="pwa-item-main">
                                    <i class="fa-solid fa-circle-half-stroke"></i>
                                    <span>Thème de l'interface</span>
                                </div>
                                <div class="pwa-switch-ui-track">
                                    <i class="fas fa-sun sun-icon"></i>
                                    <i class="fas fa-moon moon-icon"></i>
                                </div>
                            </div>

                            <a href="https://github.com/adrienb39/meteastro" target="_blank" rel="noopener"
                                class="pwa-settings-item">
                                <div class="pwa-item-main">
                                    <i class="fa-brands fa-github"></i>
                                    <span>Code Source du Projet</span>
                                </div>
                                <i class="fa-solid fa-chevron-right pwa-list-chevron"></i>
                            </a>

                            <a href="/#contacts" class="pwa-settings-item">
                                <div class="pwa-item-main">
                                    <i class="fa-solid fa-envelope"></i>
                                    <span>Contacter le support</span>
                                </div>
                                <i class="fa-solid fa-chevron-right pwa-list-chevron"></i>
                            </a>
                        </div>
                    </div>

                    <footer class="pwa-app-legal">
                        <p class="pwa-legal-tagline">Station spatiale Meteastro — Astronomie & Météorologie en temps réel.
                        </p>
                        <p class="pwa-legal-credits">Conçu par <strong>Adrien Bruyère</strong> • Tous droits réservés ©
                            <?= date('Y') ?>
                        </p>
                    </footer>

                </div>
            </dialog>
        </div>

        <div id="termsModal" class="modal-overlay">
            <div class="modal-content glass-card animate-in">
                <div class="modal-header">
                    <h2><i class="fa-solid fa-file-contract"></i> Termes et Conditions</h2>
                    <button id="closeTerms" class="close-btn">&times;</button>
                </div>
                <div class="modal-body">
                    <h3>1. Acceptation des termes</h3>
                    <p>En accédant à ce site, vous acceptez d'être lié par ces termes et conditions et toutes les lois et
                        réglementations applicables.</p>

                    <h3>2. Utilisation du site</h3>
                    <p>Vous pouvez utiliser notre site uniquement à des fins légales et d'une manière qui ne porte pas
                        atteinte aux droits des autres utilisateurs.</p>

                    <h3>3. Propriété intellectuelle</h3>
                    <p>Tous les contenus présents sur ce site (textes, graphiques, logos) sont la propriété de Meteastro.
                    </p>

                    <h3>4. Limitation de responsabilité</h3>
                    <p>Meteastro ne peut être tenu responsable des dommages résultant de l'utilisation de ce site.</p>

                    <h3>5. Modifications</h3>
                    <p>Meteastro se réserve le droit de modifier ces termes à tout moment.</p>
                </div>
                <div class="modal-footer">
                    <button id="acceptTermsBtn" class="box-button btn-glow">J'AI COMPRIS</button>
                </div>
            </div>
        </div>
        <div class="cookie-wrapper">

            <aside id="cookie-banner" class="cookie-banner" role="status" aria-labelledby="banner-title"
                style="display: none;">
                <div class="cookie-container">
                    <header>
                        <div class="cookie-icon" aria-hidden="true">🍪</div>
                        <h2 id="banner-title">Gestion des cookies</h2>
                    </header>

                    <div class="cookie-content">
                        <p>
                            Meteastro utilise des cookies pour optimiser votre navigation stellaire.
                            Certains sont nécessaires au fonctionnement du vaisseau, d'autres nous aident à cartographier
                            votre expérience.
                            <a href="#" id="openTerms" class="cookie-link">Politique de confidentialité</a>.
                        </p>
                    </div>

                    <div class="cookie-actions">
                        <button id="accept-all" class="cookie-btn btn-accept">Accepter tout</button>
                        <button id="reject-all" class="cookie-btn btn-reject">Refuser</button>
                        <button id="manage-btn" class="cookie-btn btn-manage">Paramétrer</button>
                    </div>
                </div>
            </aside>

        </div>

        <div id="cookie-modal" class="cookie-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-title"
            style="display: none;">
            <div class="cookie-modal-content">
                <button id="close-modal" class="btn-close-modal" aria-label="Fermer la fenêtre">&times;</button>

                <header class="modal-header">
                    <h3 id="modal-title">Préférences spatiales</h3>
                    <p class="modal-subtitle">Choisissez les données que vous souhaitez partager avec la station.</p>
                </header>

                <div class="cookie-settings-list">
                    <div class="setting-item">
                        <div class="setting-info">
                            <span class="setting-name">Cookies Techniques</span>
                            <span class="setting-desc">Indispensables pour la connexion et la sécurité.</span>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="check-essential" checked
                                disabled>
                        </div>
                    </div>

                    <div class="setting-item">
                        <div class="setting-info">
                            <span class="setting-name">Mesures d'Audience</span>
                            <span class="setting-desc">Nous permettent d'améliorer les instruments de bord.</span>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="check-analytics">
                        </div>
                    </div>

                    <div class="setting-item">
                        <div class="setting-info">
                            <span class="setting-name">Personnalisation</span>
                            <span class="setting-desc">Contenu adapté à vos précédentes explorations.</span>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="check-ads">
                        </div>
                    </div>
                </div>

                <footer class="modal-footer">
                    <button id="save-settings" class="cookie-btn btn-save">Enregistrer la configuration</button>
                </footer>
            </div>
        </div>
        <?php if (empty($hideSiteHeader)): ?>
            <script src="/assets/js/bootstrap.bundle.min.js"></script>
        <?php endif; ?>
        <script src="/assets/js/footer.js"></script>
        <script src="/assets/js/app.js"></script>
        <script src="/assets/js/astronomie.js"></script>
        <script src="/assets/js/cookie.js"></script>
        <script src="/assets/js/divers.js"></script>
        <?php if (!empty($hideSiteHeader)): ?>
            <script src="/assets/js/login.js"></script>
        <?php endif; ?>
        <script src="/assets/js/main.js"></script>
        <script src="/assets/js/menu.js"></script>
        <script src="/assets/js/meteorologie.js"></script>
        <script src="/assets/js/popup.js"></script>
        <script src="/assets/js/updatemenu.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const music = document.getElementById('bgMusic');
                const btn = document.getElementById('audioToggle');
                const infoBtn = document.getElementById('infoToggle');
                const infoSlide = document.getElementById('extraInfo');
                const player = document.getElementById('playerContainer');
                const titleDisplay = document.getElementById('currentTrackTitle');

                // CONFIGURATION
                const defaultMusic = "/assets/musiques/Expedition-Long-Version-chosic.com_.mp3";
                // On s'assure que userPlaylist est un tableau même si PHP renvoie null
                const userPlaylist = <?php echo !empty($jsonPlaylist) ? $jsonPlaylist : '[]'; ?>;
                let currentTrackIndex = -1;

                music.volume = 0.25;

                function updateTrackInfo(source) {
                    if (!titleDisplay) return;
                    const fileName = source.split('/').pop().replace(/_/g, ' ').replace('.mp3', '');
                    if (source.includes('Expedition')) {
                        titleDisplay.innerHTML = "<strong>Expedition</strong> by Alex-Productions";
                    } else {
                        titleDisplay.innerHTML = `<strong>En lecture :</strong> ${fileName}`;
                    }
                }

                function updateUI(playing) {
                    playing ? player.classList.add('playing') : player.classList.remove('playing');
                }

                // LOGIQUE DE BOUCLE INFINIE
                music.addEventListener('ended', () => {
                    if (userPlaylist.length > 0) {
                        currentTrackIndex++;

                        // Si on dépasse la fin de la playlist, on revient à -1 (Musique MeteAstro)
                        if (currentTrackIndex >= userPlaylist.length) {
                            currentTrackIndex = -1;
                            music.src = defaultMusic;
                        } else {
                            // Sinon on passe à la musique suivante dans uploads
                            music.src = userPlaylist[currentTrackIndex];
                        }

                        updateTrackInfo(music.getAttribute('src'));
                        music.play().then(() => updateUI(true));
                    } else {
                        // S'il n'y a pas d'uploads, on relance juste la musique par défaut
                        music.currentTime = 0;
                        music.play().then(() => updateUI(true));
                    }
                });

                // Gestion du Play/Pause
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (music.paused) {
                        music.play().then(() => {
                            updateUI(true);
                            localStorage.setItem('musicPlaying', 'true');
                        });
                    } else {
                        music.pause();
                        updateUI(false);
                        localStorage.setItem('musicPlaying', 'false');
                    }
                });

                // Persistance au chargement
                const wasPlaying = localStorage.getItem('musicPlaying') === 'true';
                if (wasPlaying) {
                    // On essaie de relancer la lecture (souvent bloqué par le navigateur sans clic)
                    music.play()
                        .then(() => updateUI(true))
                        .catch(() => {
                            console.log("Lecture auto bloquée : en attente d'une interaction utilisateur.");
                            localStorage.setItem('musicPlaying', 'false');
                            updateUI(false);
                        });
                }

                infoBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    infoSlide.classList.toggle('open');
                });
            });
        </script>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // 1. Gestion de la version de l'application
                const appVersion = "<?= $appVersion ?>"; // Calqué sur le CACHE_NAME du SW
                const versionField = document.getElementById('pwa-version');
                if (versionField) {
                    versionField.textContent = `v${appVersion}`;
                }

                // 2. Détection de l'état du Service Worker de la PWA
                const swField = document.getElementById('pwa-sw');
                if ('serviceWorker' in navigator) {
                    navigator.serviceWorker.ready.then((registration) => {
                        if (swField) {
                            if (registration.active) {
                                swField.textContent = "Actif";
                                swField.style.color = "#198754"; // Vert de confirmation système Android
                            } else {
                                swField.textContent = "En attente";
                            }
                        }
                    }).catch(() => {
                        if (swField) swField.textContent = "Erreur SW";
                    });
                } else {
                    if (swField) swField.textContent = "Indisponible";
                }

                // 3. Calcul de l'espace de stockage alloué au cache de l'application
                const cacheField = document.getElementById('pwa-cache');
                if ('storage' in navigator && 'estimate' in navigator.storage) {
                    navigator.storage.estimate().then((estimate) => {
                        if (cacheField) {
                            // Conversion précise en Mégaoctets (Mo)
                            const usedSize = (estimate.usage / (1024 * 1024)).toFixed(2);
                            cacheField.textContent = `${usedSize} Mo`;
                        }
                    }).catch(() => {
                        if (cacheField) cacheField.textContent = "Erreur";
                    });
                } else {
                    if (cacheField) cacheField.textContent = "Non géré";
                }
            });
        </script>
        <script>
            document.getElementById('contactForm')?.addEventListener('submit', async function (e) {
                e.preventDefault();

                const form = e.currentTarget;
                const btn = document.getElementById('btnSubmit');
                const originalText = btn.innerHTML;

                // Helper pour gérer l'état du bouton d'envoi
                const setLoading = (isLoading) => {
                    btn.disabled = isLoading;
                    btn.style.opacity = isLoading ? '0.7' : '1';
                    btn.innerHTML = isLoading
                        ? '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>TRANSMISSION EN COURS...'
                        : originalText;
                };

                setLoading(true);

                try {
                    const formData = new FormData(form);
                    formData.append('send_signal', '1');

                    // 1. Envoi de la requête au contrôleur
                    const response = await fetch('/', {
                        method: 'POST',
                        body: formData,
                        cache: 'no-cache',
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    // 2. Contrôle du statut HTTP
                    if (!response.ok) {
                        throw new Error(`Erreur réseau HTTP (${response.status})`);
                    }

                    // 3. Extraction et validation du JSON
                    const rawText = await response.text();
                    let result;

                    try {
                        result = JSON.parse(rawText);
                    } catch (jsonError) {
                        console.error('Réponse brute non-JSON reçue du serveur :', rawText);
                        throw new Error('La réponse du serveur est corrompue (Format JSON invalide).');
                    }

                    // 4. Traitement du retour métier PHP
                    if (result.status === 'success') {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Signal reçu !',
                            text: result.message || 'Transmission réussie.',
                            background: '#0f172a',
                            color: '#f1f5f9',
                            confirmButtonColor: '#38bdf8',
                            timer: 4000,
                            timerProgressBar: true
                        });

                        form.reset();
                    } else {
                        throw new Error(result.message || 'Le centre de contrôle a rejeté le signal.');
                    }

                } catch (error) {
                    // 5. Capture et affichage des erreurs (Réseau, JSON ou PHP)
                    console.error('Échec de transmission :', error);

                    Swal.fire({
                        icon: 'error',
                        title: 'Interférence détectée',
                        text: error.message,
                        background: '#0f172a',
                        color: '#f1f5f9',
                        confirmButtonColor: '#ef4444'
                    });

                } finally {
                    // 6. Restauration systématique du bouton
                    setLoading(false);
                }
            });
        </script>

        <script>
            /**
     * Générateur d'ambiance Meteastro
     * Gère les étoiles (nuit) et les particules solaires (jour)
     */
            function initSpaceAmbience() {
                const field = document.getElementById('star-field');
                if (!field) return;

                // Nettoyage si le script est relancé
                field.innerHTML = '';

                // Détection du mode via la classe sur le body
                const isLightMode = document.body.classList.contains('lightmode');

                // Configuration selon le mode
                const config = {
                    count: isLightMode ? 80 : 150, // Moins de particules le jour pour la clarté
                    color: isLightMode ? '#FFD700' : '#FFFFFF', // Or le jour, blanc la nuit
                    blur: isLightMode ? '1px' : '0px' // Effet de halo pour la poussière solaire
                };

                const fragment = document.createDocumentFragment();

                for (let i = 0; i < config.count; i++) {
                    const particle = document.createElement('div');
                    const size = (Math.random() * (isLightMode ? 4 : 2) + 1) + 'px';

                    // Styles de base
                    Object.assign(particle.style, {
                        position: 'absolute',
                        width: size,
                        height: size,
                        backgroundColor: config.color,
                        left: Math.random() * 100 + '%',
                        top: Math.random() * 100 + '%',
                        borderRadius: '50%',
                        opacity: Math.random() * (isLightMode ? 0.5 : 1),
                        filter: `blur(${config.blur})`,
                        pointerEvents: 'none'
                    });

                    // Animation spécifique
                    if (isLightMode) {
                        // Poussière qui flotte doucement
                        particle.style.animation = `floatSolar ${Math.random() * 15 + 10}s infinite ease-in-out`;
                    } else {
                        // Étoiles qui scintillent
                        particle.style.animation = `twinkle ${Math.random() * 3 + 2}s infinite ease-in-out`;
                    }

                    // Décalage aléatoire pour éviter l'effet "bloc"
                    particle.style.animationDelay = Math.random() * 5 + 's';

                    fragment.appendChild(particle);
                }

                field.appendChild(fragment);
            }

            // Lancement au chargement
            document.addEventListener('DOMContentLoaded', initSpaceAmbience);
        </script>
        <script>
            (() => {
                // 1. Configuration & Sélection des éléments du DOM
                const DOM = {
                    modal: document.getElementById('pwa-component-modal'),
                    btnOpen: document.getElementById('pwa-action-open'),
                    btnClose: document.getElementById('pwa-action-close'),
                    btnInstall: document.getElementById('pwa-action-install'),
                    txtStatus: document.getElementById('pwa-text-status'),
                    guideIos: document.getElementById('pwa-guide-ios'),
                    guideGeneric: document.getElementById('pwa-guide-generic'),
                    txtInstructions: document.getElementById('pwa-text-instructions')
                };

                let pwaDeferredPrompt = null;
                const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

                // 2. Initialisation de l'affichage selon l'OS
                const initPlatformUX = () => {
                    if (isIOS) {
                        DOM.txtStatus.textContent = "📱 Configuration : Apple iOS";
                        DOM.guideIos.style.display = "block";
                        DOM.guideGeneric.style.display = "none";
                    } else {
                        DOM.txtStatus.textContent = "🤖 Configuration : Android / Standard";
                        DOM.guideIos.style.display = "none";
                        DOM.guideGeneric.style.display = "block";
                        if (DOM.btnInstall) DOM.btnInstall.style.display = 'none';
                    }
                };

                // 3. Gestionnaires d'événements pour la Modale
                const toggleModal = (isOpen) => {
                    if (!DOM.modal) return;
                    DOM.modal.classList.toggle('is-open', isOpen);
                };

                const initModalEvents = () => {
                    if (!DOM.btnOpen || !DOM.btnClose) return;

                    DOM.btnOpen.addEventListener('click', () => toggleModal(true));
                    DOM.btnClose.addEventListener('click', () => toggleModal(false));
                    DOM.modal.addEventListener('click', (e) => {
                        if (e.target === DOM.modal) toggleModal(false);
                    });
                };

                // 4. Gestion intelligente du défilement (Bouton discret au scroll)
                const initScrollBehavior = () => {
                    if (!DOM.btnOpen) return;

                    let lastScrollTop = 0;
                    window.addEventListener('scroll', () => {
                        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

                        // Si l'utilisateur descend de plus de 100px, le bouton s'estompe
                        if (scrollTop > lastScrollTop && scrollTop > 100) {
                            DOM.btnOpen.classList.add('pwa-discret');
                        } else {
                            DOM.btnOpen.classList.remove('pwa-discret');
                        }
                        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
                    }, { passive: true });
                };

                // 5. Logique PWA (Interception et Déclenchement de l'installation)
                const initPwaEngine = () => {
                    window.addEventListener('beforeinstallprompt', (e) => {
                        e.preventDefault();
                        pwaDeferredPrompt = e;

                        // Si on n'est pas sur iOS, on propose le bouton natif à la place des étapes manuelles
                        if (!isIOS && DOM.btnInstall && DOM.txtInstructions) {
                            DOM.btnInstall.style.display = 'block';
                            DOM.txtInstructions.style.display = 'none';
                        }
                    });

                    if (DOM.btnInstall) {
                        DOM.btnInstall.addEventListener('click', async () => {
                            if (!pwaDeferredPrompt) return;

                            await pwaDeferredPrompt.prompt();
                            const { outcome } = await pwaDeferredPrompt.userChoice;

                            if (outcome === 'accepted') {
                                cleanUpPwaUX();
                            }
                            pwaDeferredPrompt = null;
                        });
                    }

                    window.addEventListener('appinstalled', () => {
                        cleanUpPwaUX();
                    });
                };

                // Nettoyage de l'interface une fois installée
                const cleanUpPwaUX = () => {
                    pwaDeferredPrompt = null;
                    toggleModal(false);
                    if (DOM.btnOpen) DOM.btnOpen.style.display = 'none';
                };

                // Lancement global du script
                const init = () => {
                    initPlatformUX();
                    initModalEvents();
                    initScrollBehavior();
                    initPwaEngine();
                };

                document.addEventListener('DOMContentLoaded', init);
            })();
        </script>
    <?php endif; ?>
</body>

</html>